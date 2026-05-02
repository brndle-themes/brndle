{{--
  Style: ticker
  Horizontal scrolling row with snap. Cards are uniform width and
  the row scrolls horizontally on overflow. Native scrollbar is
  hidden (it read as visual noise on the magazine homepage); two
  overlay arrow buttons handle desktop scrolling. Touch / trackpad
  swipe still works as before. The arrows hide automatically when
  the row fits (no overflow) and disable at the start / end of the
  scroll range. Vanilla JS, lazy-attached after DOM ready.
--}}
@php($tickerId = 'brndle-ticker-' . $sectionCategory->term_id . '-' . wp_generate_uuid4())
<section class="brndle-section-ticker brndle-ticker" data-brndle-ticker="{{ $tickerId }}">
  @include('partials.sections-styles._header')

  <div class="brndle-ticker__viewport relative -mx-4 sm:-mx-6 lg:-mx-8">
    <div role="list"
         tabindex="0"
         aria-label="{{ esc_attr(sprintf(__('Latest in %s', 'brndle'), $sectionCategory->name)) }}"
         data-brndle-ticker-track
         class="brndle-ticker__track flex gap-4 sm:gap-5 px-4 sm:px-6 lg:px-8 overflow-x-auto snap-x snap-mandatory pb-1 scroll-smooth focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 rounded-md">
      @foreach ($sectionPosts as $sectionPost)
        @php(setup_postdata($GLOBALS['post'] = $sectionPost))
        <article role="listitem"
                 @php(post_class('group shrink-0 snap-start w-72 sm:w-80 rounded-2xl overflow-hidden border border-surface-tertiary bg-surface-primary'))>
          <a href="{{ get_permalink() }}" class="block h-full">
            <div class="aspect-video overflow-hidden bg-surface-secondary">
              @include('partials.components.post-thumbnail', [
                'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
              ])
            </div>
            <div class="p-4">
              @if ($cat = get_the_category())
                <span class="text-[10px] font-semibold uppercase tracking-wider text-accent">{!! esc_html($cat[0]->name) !!}</span>
              @endif
              <h3 class="mt-2 text-base font-bold leading-snug text-text-primary group-hover:text-accent transition-colors line-clamp-2">
                {!! get_the_title() !!}
              </h3>
              <time class="mt-2 block text-xs text-text-tertiary" datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
            </div>
          </a>
        </article>
        @php(wp_reset_postdata())
      @endforeach
    </div>

    {{-- Overlay arrow buttons. Hidden on touch devices (the JS controller
         removes them when matchMedia('(hover: none)') is true) and when
         the track does not actually overflow. --}}
    <button type="button"
            data-brndle-ticker-prev
            aria-label="{{ esc_attr__('Scroll left', 'brndle') }}"
            class="brndle-ticker__btn brndle-ticker__btn--prev"
            hidden>
      <span aria-hidden="true">&lsaquo;</span>
    </button>
    <button type="button"
            data-brndle-ticker-next
            aria-label="{{ esc_attr__('Scroll right', 'brndle') }}"
            class="brndle-ticker__btn brndle-ticker__btn--next"
            hidden>
      <span aria-hidden="true">&rsaquo;</span>
    </button>
  </div>
</section>
