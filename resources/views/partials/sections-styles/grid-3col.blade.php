{{--
  Style: grid-3col
  Uniform 3-column grid. Mobile: 1-col, tablet: 2-col, desktop: 3-col.
  Workhorse layout for evergreen content sections.
--}}
<section class="brndle-section-grid-3col">
  @include('partials.sections-styles._header')

  <div class="grid gap-6 lg:gap-8 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($sectionPosts as $sectionPost)
      @php(setup_postdata($GLOBALS['post'] = $sectionPost))
      <article @php(post_class('group rounded-2xl overflow-hidden border border-surface-tertiary bg-surface-primary'))>
        <a href="{{ get_permalink() }}" class="block h-full">
          <div class="aspect-video overflow-hidden bg-surface-secondary">
            @include('partials.components.post-thumbnail', [
              'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
            ])
          </div>
          <div class="p-5">
            @if ($cat = get_the_category())
              <span class="text-[10px] font-semibold uppercase tracking-wider text-accent">{!! esc_html($cat[0]->name) !!}</span>
            @endif
            <h3 class="mt-2 text-lg font-bold leading-snug text-text-primary group-hover:text-accent transition-colors line-clamp-2">
              {!! get_the_title() !!}
            </h3>
            <p class="mt-2 text-sm text-text-secondary leading-relaxed line-clamp-2">
              {{ wp_strip_all_tags(get_the_excerpt()) }}
            </p>
            <time class="mt-3 block text-xs text-text-tertiary" datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
          </div>
        </a>
      </article>
      @php(wp_reset_postdata())
    @endforeach
  </div>
</section>
