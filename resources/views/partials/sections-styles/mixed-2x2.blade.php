{{--
  Style: mixed-2x2
  1 hero card on top + 3 small cards below in a 3-col grid. Best for
  "this week" sections where one story dominates and the rest are
  recency picks. Stacks fully on mobile.
--}}
<section class="brndle-section-mixed-2x2">
  @include('partials.sections-styles._header')

  @php
    // Honor the admin "Posts to show" count: 1 hero on top + remainder
    // in the responsive 3-col grid below. Was hardcoded to 3 small cards,
    // silently truncating count > 4. The grid wraps naturally to multiple
    // rows on counts like 7 (1 hero + 2 rows of 3).
    $hero = $sectionPosts[0] ?? null;
    $rest = array_slice($sectionPosts, 1);
  @endphp

  <div class="grid gap-6 lg:gap-8">
    {{-- Hero --}}
    @if ($hero)
      @php(setup_postdata($GLOBALS['post'] = $hero))
      <article @php(post_class('group rounded-2xl overflow-hidden border border-surface-tertiary bg-surface-primary'))>
        <a href="{{ get_permalink() }}" class="grid lg:grid-cols-[5fr_4fr]">
          <div class="aspect-[16/10] lg:aspect-auto lg:min-h-[320px] overflow-hidden bg-surface-secondary">
            @include('partials.components.post-thumbnail', [
              'size'  => 'brndle-hero',
              'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
            ])
          </div>
          <div class="p-6 lg:p-10 flex flex-col justify-center">
            @if ($cat = get_the_category())
              <span class="text-xs font-semibold uppercase tracking-wider text-accent">{!! esc_html($cat[0]->name) !!}</span>
            @endif
            <h3 class="mt-3 text-2xl lg:text-3xl font-bold leading-tight text-text-primary group-hover:text-accent transition-colors">
              {!! get_the_title() !!}
            </h3>
            <p class="mt-3 text-text-secondary leading-relaxed line-clamp-3">
              {{ wp_strip_all_tags(get_the_excerpt()) }}
            </p>
            <time class="mt-4 text-xs text-text-tertiary" datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
          </div>
        </a>
      </article>
      @php(wp_reset_postdata())
    @endif

    {{-- 3 small below --}}
    @if (! empty($rest))
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($rest as $restPost)
          @php(setup_postdata($GLOBALS['post'] = $restPost))
          <article @php(post_class('group rounded-2xl overflow-hidden border border-surface-tertiary bg-surface-primary'))>
            <a href="{{ get_permalink() }}" class="block h-full">
              <div class="aspect-video overflow-hidden bg-surface-secondary">
                @include('partials.components.post-thumbnail', [
                  'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
                ])
              </div>
              <div class="p-4">
                <h4 class="text-base font-bold leading-snug text-text-primary group-hover:text-accent transition-colors line-clamp-2">
                  {!! get_the_title() !!}
                </h4>
                <time class="mt-2 block text-xs text-text-tertiary" datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
              </div>
            </a>
          </article>
          @php(wp_reset_postdata())
        @endforeach
      </div>
    @endif
  </div>
</section>
