{{--
  Featured image for a post card.

  Defaults to `object-contain`, not `object-cover`. Featured images are 1200x630
  OG cards with text baked into the graphic, so cropping cuts words off. Pair
  this with an `aspect-card` wrapper and the image fills it edge to edge with no
  letterboxing; an off-ratio upload letterboxes instead of losing content.
--}}
@props(['size' => 'brndle-card', 'class' => 'w-full h-full object-contain', 'loading' => 'lazy', 'priority' => false])

@if(has_post_thumbnail())
  {!! get_the_post_thumbnail(get_the_ID(), $size, [
    'class' => $class,
    'loading' => $priority ? 'eager' : $loading,
    'decoding' => 'async',
    'fetchpriority' => $priority ? 'high' : null,
  ]) !!}
@else
  <img src="{{ get_theme_file_uri('public/placeholder.webp') }}" alt="" class="{{ $class }}" loading="{{ $priority ? 'eager' : $loading }}" decoding="async" width="1200" height="630">
@endif
