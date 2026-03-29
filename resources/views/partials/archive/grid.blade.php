{{-- Grid Layout: 3-column card grid (Stripe/Figma pattern) --}}
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
  @while(have_posts())
    @php(the_post())
    <article @php(post_class('group'))>
      <a href="{{ get_permalink() }}" class="block rounded-2xl border border-surface-tertiary bg-surface-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
        <div class="aspect-video overflow-hidden rounded-t-2xl bg-surface-secondary">
          @include('partials.components.post-thumbnail', [
            'size' => 'medium_large',
            'class' => 'w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500',
          ])
        </div>

        <div class="p-6">
          {{-- Category --}}
          @if($category = get_the_category())
            <span class="text-xs font-semibold uppercase tracking-wider text-accent">{!! esc_html($category[0]->name) !!}</span>
          @endif

          <h2 class="mt-2 text-lg font-bold text-text-primary leading-snug group-hover:text-accent transition-colors">
            {{ get_the_title() }}
          </h2>

          <p class="mt-2 text-sm text-text-secondary leading-relaxed line-clamp-2">
            {{ get_the_excerpt() }}
          </p>

          <div class="mt-4 flex items-center gap-3 text-xs text-text-tertiary">
            <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
            <span>&middot;</span>
            <span>{{ $readingTime ?? '' }}</span>
          </div>
        </div>
      </a>
    </article>
  @endwhile
</div>
