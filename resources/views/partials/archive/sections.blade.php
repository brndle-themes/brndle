{{--
  Blog Homepage Sections (1.5.0)

  Iterates the admin-configured `homepage_sections` array. Each row
  selects a top-level category, a visual style, and a post count. The
  matching style partial renders a self-contained <section>.

  1.5.2: when the toggle is ON but no sections are configured yet, we
  auto-pick the top 6 most-populated top-level categories and assign
  varied styles in a magazine-flow order (hero -> grid -> strip -> ...).
  This means "enable toggle" delivers a working magazine homepage with
  zero further admin work.

  Each rendered section is also wrapped in `.brndle-homepage-section`
  with an index-based modifier (`--alt`) so the styles can alternate
  background tone, kicker treatment, and divider weight without each
  partial having to re-implement the same logic.

  Empty / unconfigured sections are skipped so a misconfigured row
  does not produce dead whitespace.

  Plan: plans/2026-05-02-blog-homepage-sections.md
--}}
@php
  $sections = (array) \Brndle\Settings\Settings::get('homepage_sections', []);
  $allowedStyles = \Brndle\Settings\Defaults::homepageSectionStyles();

  // Auto-defaults: when no sections are configured yet, pick the top 5
  // most-populated TOP-LEVEL (parent=0) categories and compose them as a
  // real magazine homepage. The flow is deliberately picked to read like a
  // newsstand front page rather than a uniform grid:
  //   01 lead story        → featured-hero  (3 posts: 1 big + 2 stacked)
  //   02 recent recap      → grid-3col      (6 posts in a uniform grid)
  //   03 long read + list  → magazine-strip (1 feature + 4 numbered)
  //   04 editorial spread  → editorial-pair (2 large, side by side)
  //   05 trending ticker   → ticker         (8 posts, horizontal snap)
  //
  // Performance: `get_categories()` with `orderby=count` + `hide_empty=true`
  // joins through wp_term_relationships, which is slow on 7k+ post sites.
  // Cached for 1h via transient and invalidated on term edits (see the
  // hooks registered in app/Compatibility/HomepageSectionsCache.php).
  if (empty($sections)) {
    $autoCatIds = get_transient('brndle_homepage_auto_cats');
    if ($autoCatIds === false) {
      $autoCats = get_categories([
        'parent'     => 0,
        'orderby'    => 'count',
        'order'      => 'DESC',
        'hide_empty' => true,
        'number'     => 5,
      ]);
      $autoCatIds = array_map(static fn($c) => (int) $c->term_id, $autoCats);
      set_transient('brndle_homepage_auto_cats', $autoCatIds, HOUR_IN_SECONDS);
    }

    $styleFlow = [
      'featured-hero',     // 01 lead
      'grid-3col',         // 02 recap
      'magazine-strip',    // 03 long-read + numbered list
      'editorial-pair',    // 04 spread
      'ticker',            // 05 trending strip
    ];
    $countFlow = [3, 6, 5, 2, 8];

    foreach ($autoCatIds as $i => $catId) {
      $sections[] = [
        'category_id'   => $catId,
        'style'         => $styleFlow[$i] ?? 'grid-3col',
        'count'         => $countFlow[$i] ?? 4,
        'show_title'    => true,
        'show_view_all' => true,
      ];
    }
  }

  // DoS guard: a misconfigured 100-section list would issue 100 SELECT
  // queries per pageview. Cap rendered sections at 12 (matches Sanitizer
  // hard limit). Anything past 12 is silently dropped here too in case
  // legacy data slips through with the cap raised.
  if (count($sections) > 12) {
    $sections = array_slice($sections, 0, 12);
  }

  $renderedIndex = 0;
@endphp

<div class="brndle-homepage-sections">
  @foreach ($sections as $config)
    @php
      $categoryId = (int) ($config['category_id'] ?? 0);
      if ($categoryId <= 0) continue;

      $category = get_category($categoryId);
      if (! $category || is_wp_error($category)) continue;

      $count = max(1, min(10, (int) ($config['count'] ?? 4)));
      $sectionPosts = get_posts([
        'category'            => $categoryId,
        'posts_per_page'      => $count,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
      ]);
      if (empty($sectionPosts)) continue;

      // Prime thumbnail attachment caches for the whole section in one query
      // instead of paying N+1 inside the per-post `get_the_post_thumbnail()`
      // calls. Critical on 7k+ post sites where the attachments table is
      // large and missing this cost ~30-80ms per section without object cache.
      $thumbIds = array_filter(array_map(
        static fn($p) => (int) get_post_meta($p->ID, '_thumbnail_id', true),
        $sectionPosts
      ));
      if (! empty($thumbIds)) {
        _prime_post_caches($thumbIds, true, true);
      }

      $style = (string) ($config['style'] ?? 'grid-3col');
      if (! in_array($style, $allowedStyles, true)) {
        $style = 'grid-3col';
      }

      $showTitle = ! empty($config['show_title']);
      $showViewAll = ! empty($config['show_view_all']);

      // The number kicker (01, 02, ...) gives each block a distinct
      // magazine-style hierarchy mark. The first section gets a small
      // top-padding tweak via `.is-first`; everything else inherits the
      // shared wrapper with hairline divider + kicker.
      $wrapperClass = $renderedIndex === 0
        ? 'brndle-homepage-section is-first'
        : 'brndle-homepage-section';
      $sectionNumber = str_pad((string) ($renderedIndex + 1), 2, '0', STR_PAD_LEFT);
      $renderedIndex++;
    @endphp

    <div class="{{ $wrapperClass }}" data-section-style="{{ $style }}">
      @include('partials.sections-styles.' . $style, [
        'sectionCategory'    => $category,
        'sectionPosts'       => $sectionPosts,
        'sectionShowTitle'   => $showTitle,
        'sectionShowViewAll' => $showViewAll,
        'sectionNumber'      => $sectionNumber,
      ])
    </div>
  @endforeach
</div>

{{-- Ticker controller (1.5.6).
     Attaches prev/next arrow buttons to every `.brndle-ticker` on the
     page. Idempotent: a guard flag prevents double-binding if soft-nav
     re-runs the script. Skipped on touch-primary devices via the
     CSS @media (hover:none) rule that hides the buttons (we still
     attach the listeners — cheaper than feature-detecting in JS — but
     they have no visible target).
     Runs once at the end of the sections render, so it ships only when
     sections layout is active.
--}}
<script>
(function () {
  if (window.brndleTickerInit) return;
  window.brndleTickerInit = true;

  function init() {
    document.querySelectorAll('.brndle-ticker').forEach(function (root) {
      var track = root.querySelector('[data-brndle-ticker-track]');
      var prev = root.querySelector('[data-brndle-ticker-prev]');
      var next = root.querySelector('[data-brndle-ticker-next]');
      if (!track || !prev || !next) return;

      function update() {
        var canScroll = track.scrollWidth > track.clientWidth + 1;
        if (!canScroll) {
          prev.hidden = true;
          next.hidden = true;
          return;
        }
        // Use the first/last card's position rather than scrollLeft directly.
        // The track has horizontal padding (`px-8`) so scrollLeft is non-zero
        // at "start" — bounding-rect math is padding-agnostic and matches what
        // the eye sees: prev is hidden iff the first card is fully visible.
        var firstCard = track.querySelector('article');
        var lastCard = track.lastElementChild && track.lastElementChild.tagName === 'ARTICLE'
          ? track.lastElementChild
          : track.querySelectorAll('article');
        if (lastCard && lastCard.length) lastCard = lastCard[lastCard.length - 1];

        var trackRect = track.getBoundingClientRect();
        var atStart = firstCard
          ? firstCard.getBoundingClientRect().left >= trackRect.left - 1
          : track.scrollLeft <= 1;
        var atEnd = lastCard
          ? lastCard.getBoundingClientRect().right <= trackRect.right + 1
          : track.scrollLeft + track.clientWidth >= track.scrollWidth - 1;
        prev.hidden = atStart;
        next.hidden = atEnd;
      }

      function scrollByStep(direction) {
        // Step is roughly one card width — find the first visible card and use its width.
        var firstCard = track.querySelector('article');
        var step = firstCard ? firstCard.offsetWidth + 20 : track.clientWidth * 0.8;
        track.scrollBy({ left: direction * step, behavior: 'smooth' });
      }

      prev.addEventListener('click', function () { scrollByStep(-1); });
      next.addEventListener('click', function () { scrollByStep(1); });
      track.addEventListener('scroll', update, { passive: true });
      window.addEventListener('resize', update, { passive: true });

      // First measurement runs immediately; a second pass after a tick
      // catches images/fonts that adjust card width once they load. This
      // is what flips the prev arrow to hidden when initially at scroll=0.
      update();
      requestAnimationFrame(update);
      setTimeout(update, 250);

      // If any image inside the track loads later, re-measure once so
      // overflow detection stays correct when card heights settle.
      track.querySelectorAll('img').forEach(function (img) {
        if (!img.complete) {
          img.addEventListener('load', update, { once: true });
          img.addEventListener('error', update, { once: true });
        }
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
  // Re-run on view-transitions soft-nav so a navigation back to the
  // homepage rebinds without a full page reload.
  document.addEventListener('brndle:soft-nav', init);
})();
</script>
