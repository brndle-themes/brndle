{{--
  Style: featured-hero
  1 large feature on the left + 2 stacked smaller cards on the right.
  Best for the top section of the homepage. Renders 3 posts (or all
  posts if fewer were returned). Stacks vertically on mobile.
--}}
<section class="brndle-section-featured-hero">
  @include('partials.sections-styles._header')

  @php
    // Honor the admin "Posts to show" count: 1 hero + remainder as
    // sidebar cards. Was hardcoded to 2 sidebar items, silently truncating
    // any count > 3. The sidebar column scales vertically to match the
    // hero card's height; layouts beyond ~5 sidebar entries get visually
    // tight, so the editor can use a smaller count when picking this style.
    $hero = $sectionPosts[0] ?? null;
    $sidebar = array_slice($sectionPosts, 1);
  @endphp

  <div class="grid gap-6 lg:gap-8 lg:grid-cols-2">
    {{-- Large feature --}}
    @if ($hero)
      @php(setup_postdata($GLOBALS['post'] = $hero))
      <article @php(post_class('group relative rounded-2xl overflow-hidden border border-surface-tertiary bg-surface-primary'))>
        <a href="{{ get_permalink() }}" class="block h-full">
          <div class="aspect-[16/10] overflow-hidden bg-surface-secondary">
            @include('partials.components.post-thumbnail', [
              'size'     => 'brndle-hero',
              'priority' => true,
              'class'    => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
            ])
          </div>
          <div class="p-6 lg:p-8">
            @if ($cat = get_the_category())
              <span class="text-xs font-semibold uppercase tracking-wider text-accent">{!! esc_html($cat[0]->name) !!}</span>
            @endif
            <h3 class="mt-3 text-2xl lg:text-3xl font-bold leading-tight text-text-primary group-hover:text-accent transition-colors">
              {!! get_the_title() !!}
            </h3>
            <p class="mt-3 text-text-secondary leading-relaxed line-clamp-2">
              {{ wp_strip_all_tags(get_the_excerpt()) }}
            </p>
            <div class="mt-4 flex items-center gap-3 text-xs text-text-tertiary">
              <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
            </div>
          </div>
        </a>
      </article>
      @php(wp_reset_postdata())
    @endif

    {{-- Sidebar (2 stacked) --}}
    <div class="grid gap-6 content-start">
      @foreach ($sidebar as $sidebarPost)
        @php(setup_postdata($GLOBALS['post'] = $sidebarPost))
        <article @php(post_class('group rounded-2xl overflow-hidden border border-surface-tertiary bg-surface-primary'))>
          <a href="{{ get_permalink() }}" class="grid grid-cols-[120px_1fr] sm:grid-cols-[160px_1fr] gap-4 items-stretch p-3">
            <div class="aspect-square sm:aspect-[4/3] overflow-hidden rounded-xl bg-surface-secondary">
              @include('partials.components.post-thumbnail', [
                'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
              ])
            </div>
            <div class="py-1 pr-1 flex flex-col justify-center">
              @if ($cat = get_the_category())
                <span class="text-[10px] font-semibold uppercase tracking-wider text-accent">{!! esc_html($cat[0]->name) !!}</span>
              @endif
              <h3 class="mt-1 text-base sm:text-lg font-bold leading-snug text-text-primary group-hover:text-accent transition-colors line-clamp-3">
                {!! get_the_title() !!}
              </h3>
              <time class="mt-2 text-xs text-text-tertiary" datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
            </div>
          </a>
        </article>
        @php(wp_reset_postdata())
      @endforeach
    </div>
  </div>
</section>
