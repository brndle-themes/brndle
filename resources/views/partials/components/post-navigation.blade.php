@php
  $prevPost = get_previous_post();
  $nextPost = get_next_post();
@endphp

@if($prevPost || $nextPost)
  <nav class="grid grid-cols-2 gap-4" aria-label="{{ __('Post navigation', 'brndle') }}">
    @if($prevPost)
      <a href="{{ get_permalink($prevPost) }}" class="group border border-surface-tertiary rounded-xl p-4 hover:border-accent hover:shadow-md transition-all duration-300">
        <span class="text-sm text-text-tertiary group-hover:text-accent transition-colors">
          &larr; {{ __('Previous', 'brndle') }}
        </span>
        <span class="block mt-1 font-semibold text-text-primary group-hover:text-accent transition-colors line-clamp-2">
          {!! get_the_title($prevPost) !!}
        </span>
      </a>
    @else
      <div></div>
    @endif

    @if($nextPost)
      <a href="{{ get_permalink($nextPost) }}" class="group border border-surface-tertiary rounded-xl p-4 text-right hover:border-accent hover:shadow-md transition-all duration-300">
        <span class="text-sm text-text-tertiary group-hover:text-accent transition-colors">
          {{ __('Next', 'brndle') }} &rarr;
        </span>
        <span class="block mt-1 font-semibold text-text-primary group-hover:text-accent transition-colors line-clamp-2">
          {!! get_the_title($nextPost) !!}
        </span>
      </a>
    @else
      <div></div>
    @endif
  </nav>
@endif
