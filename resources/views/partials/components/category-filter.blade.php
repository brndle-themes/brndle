@php
  // WP's fallback category is never a useful public filter - it says nothing
  // about the posts in it, and on a site with uncategorised content it can
  // rank high enough by count to take a slot from a real topic.
  $excluded = array_values(array_filter(array_map('intval',
      (array) apply_filters('brndle_category_filter_exclude', [(int) get_option('default_category')])
  )));

  $cacheKey = 'brndle_top_categories_' . md5(implode(',', $excluded));
  $categories = get_transient($cacheKey);
  if ($categories === false) {
      $categories = get_categories([
          'hide_empty' => true,
          'orderby'    => 'count',
          'order'      => 'DESC',
          'number'     => 8,
          'exclude'    => $excluded,
      ]);
      set_transient($cacheKey, $categories, HOUR_IN_SECONDS);
  }
  $currentCat = is_category() ? get_queried_object_id() : 0;
  $blogUrl = get_post_type_archive_link('post') ?: home_url('/');
@endphp

@if($categories)
  <nav aria-label="{{ __('Filter posts by category', 'brndle') }}" class="mb-8">
    <ul class="flex flex-wrap gap-2 list-none p-0 m-0">
      {{-- "All" pill --}}
      <li>
        <a
          href="{{ $blogUrl }}"
          class="inline-block px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200
            {{ ! $currentCat
              ? 'bg-accent text-on-accent'
              : 'bg-surface-secondary text-text-secondary hover:bg-surface-tertiary'
            }}"
        >
          {{ __('All', 'brndle') }}
        </a>
      </li>

      {{-- Category pills --}}
      @foreach($categories as $cat)
        <li>
          <a
            href="{{ get_category_link($cat->term_id) }}"
            class="inline-block px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200
              {{ $currentCat === $cat->term_id
                ? 'bg-accent text-on-accent'
                : 'bg-surface-secondary text-text-secondary hover:bg-surface-tertiary'
              }}"
          >
            {!! $cat->name !!}
          </a>
        </li>
      @endforeach
    </ul>
  </nav>
@endif
