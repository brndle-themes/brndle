@props(['size' => 'brndle-card', 'class' => 'w-full h-full object-cover', 'loading' => 'lazy', 'priority' => false])

@if(has_post_thumbnail())
  {!! get_the_post_thumbnail(get_the_ID(), $size, [
    'class' => $class,
    'loading' => $priority ? 'eager' : $loading,
    'decoding' => 'async',
    'fetchpriority' => $priority ? 'high' : null,
  ]) !!}
@else
  <div class="w-full h-full bg-gradient-to-br from-[var(--color-surface-tertiary)] to-[var(--color-surface-secondary)] flex items-center justify-center">
    @if($category = get_the_category())
      <span class="text-lg font-bold text-accent opacity-30">{{ $category[0]->name }}</span>
    @endif
  </div>
@endif
