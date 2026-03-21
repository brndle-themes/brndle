@php
  $related = get_transient('brndle_related_' . get_the_ID());
  if (!$related) {
    $cats = wp_get_post_categories(get_the_ID());
    $related = get_posts([
      'category__in' => $cats,
      'post__not_in' => [get_the_ID()],
      'posts_per_page' => 3,
      'orderby' => 'rand',
    ]);
    set_transient('brndle_related_' . get_the_ID(), $related, HOUR_IN_SECONDS);
  }
@endphp

@if(!empty($related))
  <section class="mt-16">
    <h2 class="text-2xl font-bold text-[var(--color-text-primary)] mb-8">{{ __('Related Posts', 'brndle') }}</h2>

    <div class="grid md:grid-cols-3 gap-6">
      @foreach($related as $post)
        @php setup_postdata($post); @endphp
        <a href="{{ get_permalink($post) }}" class="group block rounded-2xl overflow-hidden border border-[var(--color-surface-tertiary)] bg-[var(--color-surface-primary)] hover:shadow-lg transition-all duration-300">
          <div class="aspect-[3/2] overflow-hidden">
            @if(has_post_thumbnail($post))
              {!! get_the_post_thumbnail($post, 'brndle-card', [
                'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
                'loading' => 'lazy',
                'decoding' => 'async',
              ]) !!}
            @else
              <div class="w-full h-full bg-gradient-to-br from-[var(--color-surface-tertiary)] to-[var(--color-surface-secondary)] flex items-center justify-center">
                @if($cat = get_the_category($post->ID))
                  <span class="text-lg font-bold text-[var(--color-accent)] opacity-30">{{ $cat[0]->name }}</span>
                @endif
              </div>
            @endif
          </div>
          <div class="p-4">
            <h3 class="font-semibold text-[var(--color-text-primary)] group-hover:text-[var(--color-accent)] transition-colors line-clamp-2">
              {{ get_the_title($post) }}
            </h3>
            <time class="block mt-2 text-sm text-[var(--color-text-tertiary)]" datetime="{{ get_the_date('c', $post) }}">
              {{ get_the_date('', $post) }}
            </time>
          </div>
        </a>
      @endforeach
      @php wp_reset_postdata(); @endphp
    </div>
  </section>
@endif
