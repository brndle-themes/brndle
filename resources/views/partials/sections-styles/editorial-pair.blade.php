{{--
  Style: editorial-pair
  Wide 2-column grid of large cards. With count=2 it reads as a
  newspaper-style pair; with count=4 it becomes a 2x2 spread; with
  count=6 it stacks 3 rows of 2. Hardcoded slice(0,2) used to cap
  this at 2 posts even when the admin asked for more. Mobile
  collapses to 1-col.
--}}
<section class="brndle-section-editorial-pair">
  @include('partials.sections-styles._header')

  <div class="grid gap-6 lg:gap-8 md:grid-cols-2">
    @foreach ($sectionPosts as $sectionPost)
      @php(setup_postdata($GLOBALS['post'] = $sectionPost))
      <article @php(post_class('group rounded-2xl overflow-hidden border border-surface-tertiary bg-surface-primary'))>
        <a href="{{ get_permalink() }}" class="block h-full">
          <div class="aspect-[16/10] overflow-hidden bg-surface-secondary">
            @include('partials.components.post-thumbnail', [
              'size'  => 'brndle-hero',
              'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
            ])
          </div>
          <div class="p-6 lg:p-8">
            @if ($cat = get_the_category())
              <span class="text-xs font-semibold uppercase tracking-wider text-accent">{!! esc_html($cat[0]->name) !!}</span>
            @endif
            <h3 class="mt-3 text-xl lg:text-2xl font-bold leading-tight text-text-primary group-hover:text-accent transition-colors">
              {!! get_the_title() !!}
            </h3>
            <p class="mt-3 text-text-secondary leading-relaxed line-clamp-3">
              {{ wp_strip_all_tags(get_the_excerpt()) }}
            </p>
            <div class="mt-4 flex items-center gap-3 text-xs text-text-tertiary">
              <span class="font-medium text-text-secondary">{{ get_the_author() }}</span>
              <span aria-hidden="true">&middot;</span>
              <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
            </div>
          </div>
        </a>
      </article>
      @php(wp_reset_postdata())
    @endforeach
  </div>
</section>
