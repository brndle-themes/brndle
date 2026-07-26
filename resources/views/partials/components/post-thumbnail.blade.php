{{--
  Featured image for a post card.

  Defaults to `object-contain`, not `object-cover`. Featured images are 1200x630
  OG cards with text baked into the graphic, so cropping cuts words off. Pair
  this with an `aspect-card` wrapper and the image fills it edge to edge with no
  letterboxing; an off-ratio upload letterboxes instead of losing content.

  Defaults to `medium_large`, NOT `brndle-card`. Until 2.2.0 `brndle-card` was a
  600x400 hard crop, so on any site that ran an earlier version that filename
  still points at an already-destroyed file sitting on disk. Asking for it would
  make the card look cropped no matter how correct the CSS is, until someone
  remembers to run `wp media regenerate`. `medium_large` is a core soft resize
  that has always preserved the source ratio, so it is right on old and new
  installs alike and needs no regeneration.
--}}
@props(['size' => 'medium_large', 'class' => 'w-full h-full object-contain', 'loading' => 'lazy', 'priority' => false])

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
