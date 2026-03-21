{{-- Blog archive card --}}
<article @php(post_class('group'))>
  <a href="{{ get_permalink() }}" class="block rounded-2xl overflow-hidden border border-slate-200 bg-white hover:shadow-lg hover:border-slate-300 hover:-translate-y-1 transition-all duration-300">
    @if(has_post_thumbnail())
      <div class="aspect-[3/2] overflow-hidden">
        {!! get_the_post_thumbnail(get_the_ID(), 'brndle-card', [
          'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
          'loading' => 'lazy',
          'decoding' => 'async',
        ]) !!}
      </div>
    @else
      <div class="aspect-[3/2] bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
        <svg class="w-12 h-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V5.25a1.5 1.5 0 00-1.5-1.5H3.75a1.5 1.5 0 00-1.5 1.5v14.25a1.5 1.5 0 001.5 1.5z"/>
        </svg>
      </div>
    @endif

    <div class="p-6">
      {{-- Category --}}
      @if($category = get_the_category())
        <span class="text-xs font-semibold uppercase tracking-wider text-accent">{{ $category[0]->name }}</span>
      @endif

      <h2 class="mt-2 text-lg font-bold text-text-primary leading-snug group-hover:text-accent transition-colors">
        {!! $title !!}
      </h2>

      <p class="mt-2 text-sm text-text-secondary leading-relaxed line-clamp-2">
        @php(the_excerpt())
      </p>

      <div class="mt-4 flex items-center gap-3 text-xs text-text-tertiary">
        <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
        <span>&middot;</span>
        <span>{{ $readingTime }}</span>
      </div>
    </div>
  </a>
</article>
