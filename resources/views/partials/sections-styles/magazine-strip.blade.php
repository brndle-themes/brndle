{{--
  Style: magazine-strip
  1 large left feature + up to 4 stacked compact items on the right.
  Best for "long read + recent" presentations. Stacks fully on mobile.
--}}
<section class="brndle-section-magazine-strip">
  @include('partials.sections-styles._header')

  @php
    // Honor the admin "Posts to show" count: 1 feature + remainder as the
    // numbered strip. Hardcoded 1 + 4 used to silently truncate count=7
    // down to 5 posts. The numbered strip scales naturally on the right
    // column up to the slider max (10).
    $feature = $sectionPosts[0] ?? null;
    $strip = array_slice($sectionPosts, 1);
  @endphp

  <div class="grid gap-6 lg:gap-8 lg:grid-cols-[3fr_2fr]">
    {{-- Feature --}}
    @if ($feature)
      @php(setup_postdata($GLOBALS['post'] = $feature))
      <article @php(post_class('group rounded-2xl overflow-hidden border border-surface-tertiary bg-surface-primary'))>
        <a href="{{ get_permalink() }}" class="block h-full">
          <div class="aspect-[16/10] lg:aspect-[4/3] overflow-hidden bg-surface-secondary">
            @include('partials.components.post-thumbnail', [
              'size'  => 'brndle-hero',
              'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
            ])
          </div>
          <div class="p-6 lg:p-8">
            @if ($cat = get_the_category())
              <span class="text-xs font-semibold uppercase tracking-wider text-accent">{!! esc_html($cat[0]->name) !!}</span>
            @endif
            <h3 class="mt-3 text-2xl lg:text-3xl font-bold leading-tight text-text-primary group-hover:text-accent transition-colors">
              {!! get_the_title() !!}
            </h3>
            <p class="mt-3 text-text-secondary leading-relaxed line-clamp-3">
              {{ wp_strip_all_tags(get_the_excerpt()) }}
            </p>
          </div>
        </a>
      </article>
      @php(wp_reset_postdata())
    @endif

    {{-- Strip (4 stacked) --}}
    <ol class="grid gap-3 content-start lg:gap-4">
      @foreach ($strip as $i => $stripPost)
        @php(setup_postdata($GLOBALS['post'] = $stripPost))
        <li>
          <article @php(post_class('group flex items-stretch gap-4 rounded-xl border border-surface-tertiary bg-surface-primary p-3 hover:border-accent/40 transition-colors'))>
            <a href="{{ get_permalink() }}" class="flex items-stretch gap-4 w-full">
              <span aria-hidden="true" class="shrink-0 w-8 text-center self-center font-bold text-2xl text-text-tertiary tabular-nums">
                {{ str_pad((string) ($i + 2), 2, '0', STR_PAD_LEFT) }}
              </span>
              <div class="shrink-0 w-20 h-20 sm:w-24 sm:h-24 overflow-hidden rounded-lg bg-surface-secondary">
                @include('partials.components.post-thumbnail', [
                  'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
                ])
              </div>
              <div class="min-w-0 flex flex-col justify-center">
                <h4 class="text-sm sm:text-base font-bold leading-snug text-text-primary group-hover:text-accent transition-colors line-clamp-3">
                  {!! get_the_title() !!}
                </h4>
                <time class="mt-1 text-xs text-text-tertiary" datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
              </div>
            </a>
          </article>
        </li>
        @php(wp_reset_postdata())
      @endforeach
    </ol>
  </div>
</section>
