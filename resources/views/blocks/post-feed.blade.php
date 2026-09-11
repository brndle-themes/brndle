{{--
  Post Feed (2.3.0)

  Wraps the homepage section-style partials so a category rail can be placed
  inside any page, not only on the blog-as-front-page path. The partials in
  partials/sections-styles/ are the same ones partials/archive/sections.blade.php
  renders, so a rail looks identical wherever it is used.

  Empty categories render nothing rather than an empty heading.
--}}
@php
  $a = $attributes;
  $anchor = (string) ($a['anchor'] ?? '');

  $categoryId = (int) ($a['categoryId'] ?? 0);
  $category   = $categoryId > 0 ? get_category($categoryId) : null;
  if (! $category || is_wp_error($category)) {
    $category = null;
  }

  $count = max(1, min(10, (int) ($a['count'] ?? 3)));

  $feedPosts = $category ? get_posts([
    'category'            => $category->term_id,
    'posts_per_page'      => $count,
    'post_status'         => 'publish',
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
  ]) : [];

  // Same N+1 guard the archive sections use: prime every thumbnail in one
  // query instead of paying a lookup inside each get_the_post_thumbnail().
  if (! empty($feedPosts)) {
    $thumbIds = array_filter(array_map(
      static fn($p) => (int) get_post_meta($p->ID, '_thumbnail_id', true),
      $feedPosts
    ));
    if (! empty($thumbIds)) {
      _prime_post_caches($thumbIds, true, true);
    }
  }

  $allowedStyles = \Brndle\Settings\Defaults::homepageSectionStyles();
  $style = (string) ($a['style'] ?? 'grid-3col');
  if (! in_array($style, $allowedStyles, true)) {
    $style = 'grid-3col';
  }

  // A block author can override the heading without renaming the category.
  $heading = trim((string) ($a['heading'] ?? ''));
  if ($heading !== '' && $category) {
    $category = clone $category;
    $category->name = $heading;
  }

  // The section-style partials render <x-img> components, and Blade reserves
  // $attributes for a component's own attribute bag. The block render callback
  // passes an ARRAY under that exact name, which then shadows the bag and makes
  // the component call ->all() on an array. Every value we need is already in
  // $a, so drop it before including.
  unset($attributes);
@endphp

@if (! empty($feedPosts))
  {{-- On the blog front page the parent .brndle-homepage-sections wrapper supplies
       the container. Placed inside a page there is no parent, so the rail rendered
       flush against the viewport edge while every other section sat in max-w-7xl. --}}
  <div class="brndle-post-feed-outer max-w-7xl mx-auto px-6 py-16 md:py-20"@if($anchor !== '') id="{{ $anchor }}"@endif>
    <div class="brndle-homepage-section brndle-post-feed" data-section-style="{{ $style }}">
    @include('partials.sections-styles.' . $style, [
      'sectionCategory'    => $category,
      'sectionPosts'       => $feedPosts,
      'sectionShowTitle'   => ! empty($a['showTitle']),
      'sectionShowViewAll' => ! empty($a['showViewAll']),
      'sectionNumber'      => '',
    ])
    </div>
  </div>
@endif
