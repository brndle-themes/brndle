@php
  $related = get_transient('brndle_related_' . get_the_ID());
  if (!$related) {
    $cats = wp_get_post_categories(get_the_ID());
    if (empty($cats) || is_wp_error($cats)) {
      $related = [];
      set_transient('brndle_related_' . get_the_ID(), $related, HOUR_IN_SECONDS);
    }
    if (!empty($cats) && !is_wp_error($cats)) {
      $candidates = get_posts([
        'category__in' => $cats,
        'post__not_in' => [get_the_ID()],
        'posts_per_page' => 10,
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
      ]);
      if (count($candidates) > 3) {
        $keys = array_rand($candidates, 3);
        $related = array_map(fn ($k) => $candidates[$k], (array) $keys);
      } else {
        $related = $candidates;
      }
      set_transient('brndle_related_' . get_the_ID(), $related, HOUR_IN_SECONDS);
    }
  }
@endphp

@if(!empty($related))
  <section class="mt-16">
    <h2 class="text-2xl font-bold text-text-primary mb-8">{{ __('Related Posts', 'brndle') }}</h2>

    <div class="grid md:grid-cols-3 gap-6">
      @foreach($related as $post)
        @php(setup_postdata($post))
        <a href="{{ get_permalink($post) }}" class="group block rounded-2xl border border-surface-tertiary bg-surface-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
          <div class="aspect-video overflow-hidden rounded-t-2xl bg-surface-secondary">
            @if(has_post_thumbnail($post))
              {!! get_the_post_thumbnail($post, 'medium_large', [
                'class' => 'w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500',
                'loading' => 'lazy',
                'decoding' => 'async',
              ]) !!}
            @else
              <div class="w-full h-full bg-gradient-to-br from-surface-tertiary to-surface-secondary flex items-center justify-center">
                @if($cat = get_the_category($post->ID))
                  <span class="text-sm font-bold text-accent/30 uppercase tracking-wider">{!! $cat[0]->name !!}</span>
                @endif
              </div>
            @endif
          </div>
          <div class="p-5">
            <h3 class="font-semibold text-text-primary group-hover:text-accent transition-colors leading-snug line-clamp-2">
              {!! get_the_title($post) !!}
            </h3>
            <time class="block mt-2 text-xs text-text-tertiary" datetime="{{ get_the_date('c', $post) }}">
              {{ get_the_date('', $post) }}
            </time>
          </div>
        </a>
      @endforeach
      @php(wp_reset_postdata())
    </div>
  </section>
@endif
