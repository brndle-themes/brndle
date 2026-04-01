@props(['size' => 'brndle-card', 'class' => 'w-full h-full object-cover', 'loading' => 'lazy', 'priority' => false])

@if(has_post_thumbnail())
  {!! get_the_post_thumbnail(get_the_ID(), $size, [
    'class' => $class,
    'loading' => $priority ? 'eager' : $loading,
    'decoding' => 'async',
    'fetchpriority' => $priority ? 'high' : null,
  ]) !!}
@else
  <img src="{{ get_theme_file_uri('public/placeholder.webp') }}" alt="" class="{{ $class }}" loading="{{ $priority ? 'eager' : $loading }}" decoding="async" width="600" height="400">
@endif
