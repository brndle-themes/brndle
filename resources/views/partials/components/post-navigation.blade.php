@php
  $prevPost = get_previous_post();
  $nextPost = get_next_post();
@endphp

@if($prevPost || $nextPost)
  <nav class="grid grid-cols-2 gap-4" aria-label="{{ __('Post navigation', 'brndle') }}">
    @if($prevPost)
      <a href="{{ get_permalink($prevPost) }}" class="group border border-[var(--color-surface-tertiary)] rounded-xl p-4 hover:border-[var(--color-accent)] hover:shadow-md transition-all duration-300">
        <span class="text-sm text-[var(--color-text-tertiary)] group-hover:text-[var(--color-accent)] transition-colors">
          &larr; {{ __('Previous', 'brndle') }}
        </span>
        <span class="block mt-1 font-semibold text-[var(--color-text-primary)] group-hover:text-[var(--color-accent)] transition-colors line-clamp-2">
          {{ get_the_title($prevPost) }}
        </span>
      </a>
    @else
      <div></div>
    @endif

    @if($nextPost)
      <a href="{{ get_permalink($nextPost) }}" class="group border border-[var(--color-surface-tertiary)] rounded-xl p-4 text-right hover:border-[var(--color-accent)] hover:shadow-md transition-all duration-300">
        <span class="text-sm text-[var(--color-text-tertiary)] group-hover:text-[var(--color-accent)] transition-colors">
          {{ __('Next', 'brndle') }} &rarr;
        </span>
        <span class="block mt-1 font-semibold text-[var(--color-text-primary)] group-hover:text-[var(--color-accent)] transition-colors line-clamp-2">
          {{ get_the_title($nextPost) }}
        </span>
      </a>
    @else
      <div></div>
    @endif
  </nav>
@endif
