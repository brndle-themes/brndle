{{--
  Style: list-with-thumb
  Full-width rows, thumbnail left + content right. Editorial / news
  feed feel. Stacks the thumb on top on mobile.
--}}
<section class="brndle-section-list-with-thumb">
  @include('partials.sections-styles._header')

  <ol class="divide-y divide-surface-tertiary">
    @foreach ($sectionPosts as $sectionPost)
      @php(setup_postdata($GLOBALS['post'] = $sectionPost))
      <li class="py-5 first:pt-0 last:pb-0">
        <article @php(post_class('group'))>
          <a href="{{ get_permalink() }}" class="grid gap-4 sm:gap-6 sm:grid-cols-[200px_1fr] md:grid-cols-[260px_1fr] items-start">
            <div class="aspect-[16/10] sm:aspect-[4/3] overflow-hidden rounded-xl bg-surface-secondary">
              @include('partials.components.post-thumbnail', [
                'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
              ])
            </div>
            <div class="min-w-0">
              @if ($cat = get_the_category())
                <span class="text-[11px] font-semibold uppercase tracking-wider text-accent">{!! esc_html($cat[0]->name) !!}</span>
              @endif
              <h3 class="mt-2 text-xl md:text-2xl font-bold leading-snug text-text-primary group-hover:text-accent transition-colors">
                {!! get_the_title() !!}
              </h3>
              <p class="mt-2 text-text-secondary leading-relaxed line-clamp-2">
                {{ wp_strip_all_tags(get_the_excerpt()) }}
              </p>
              <div class="mt-3 flex items-center gap-3 text-xs text-text-tertiary">
                <span class="font-medium text-text-secondary">{{ get_the_author() }}</span>
                <span aria-hidden="true">&middot;</span>
                <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
              </div>
            </div>
          </a>
        </article>
      </li>
      @php(wp_reset_postdata())
    @endforeach
  </ol>
</section>
