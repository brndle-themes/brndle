{{-- Grid Layout: 3-column card grid (Stripe/Figma pattern) --}}
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
  @while(have_posts()) @php(the_post())
    <article @php(post_class('group'))>
      <a href="{{ get_permalink() }}" class="block rounded-2xl overflow-hidden border border-[var(--color-surface-tertiary)] bg-[var(--color-surface-primary)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
        <div class="aspect-[3/2] overflow-hidden">
          @include('partials.components.post-thumbnail', [
            'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
          ])
        </div>

        <div class="p-6">
          {{-- Category --}}
          @if($category = get_the_category())
            <span class="text-xs font-semibold uppercase tracking-wider text-[var(--color-accent)]">{{ $category[0]->name }}</span>
          @endif

          <h2 class="mt-2 text-lg font-bold text-[var(--color-text-primary)] leading-snug group-hover:text-[var(--color-accent)] transition-colors">
            {!! get_the_title() !!}
          </h2>

          <p class="mt-2 text-sm text-[var(--color-text-secondary)] leading-relaxed line-clamp-2">
            {!! get_the_excerpt() !!}
          </p>

          <div class="mt-4 flex items-center gap-3 text-xs text-[var(--color-text-tertiary)]">
            <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
            <span>&middot;</span>
            <span>{{ $readingTime ?? '' }}</span>
          </div>
        </div>
      </a>
    </article>
  @endwhile
</div>
