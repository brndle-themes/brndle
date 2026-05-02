{{--
  <x-img> — responsive image component.

  Emits a <picture> with AVIF + WebP <source> elements whenever sibling
  files exist on disk for a local upload, then falls through to a plain
  <img> for the original format. External URLs (CDNs, raw URLs pasted
  into the admin) skip the picture wrapper because we can't enumerate
  variants for them.

  Props:
    - src       (string, required) — image URL
    - alt       (string)            — alt text; defaults to ''
    - width     (int|null)          — explicit pixel width (CLS prevention)
    - height    (int|null)          — explicit pixel height
    - class     (string)            — class names on the <img>
    - sizes     (string)            — sizes attribute, only used when srcset is later added
    - priority  (bool)              — true → fetchpriority="high" + loading="eager"
    - eager     (bool)              — alias for priority

  Filter `brndle/img/picture_sources` returns false to skip the picture
  wrapper entirely (for sites behind a smart CDN that auto-negotiates the
  format from the original URL).
--}}
@php
  $src      = $src      ?? '';
  $alt      = $alt      ?? '';
  $width    = $width    ?? null;
  $height   = $height   ?? null;
  $class    = $class    ?? '';
  $sizes    = $sizes    ?? null;
  $priority = (bool) ($priority ?? $eager ?? false);

  $isLocal  = false;
  $diskPath = null;
  if (is_string($src) && $src !== '') {
      $uploads  = wp_get_upload_dir();
      $baseUrl  = $uploads['baseurl'] ?? '';
      $basePath = $uploads['basedir'] ?? '';
      if ($baseUrl && $basePath && str_starts_with($src, $baseUrl)) {
          $relative = ltrim(substr($src, strlen($baseUrl)), '/');
          $diskPath = trailingslashit($basePath) . $relative;
          $isLocal  = is_string($diskPath) && file_exists($diskPath);
      }
  }

  // Discover sibling AVIF/WebP next to the original (e.g. hero.jpg →
  // hero.jpg.avif or hero.avif). Plugins like EWWW / WebP Express place
  // .webp variants alongside originals; we accept either pattern.
  $variants = [];
  if ($isLocal && apply_filters('brndle/img/picture_sources', true, $src)) {
      foreach (['avif' => 'image/avif', 'webp' => 'image/webp'] as $ext => $type) {
          $candidates = [
              $diskPath . '.' . $ext,                                       // hero.jpg.avif
              preg_replace('/\.[a-z0-9]+$/i', '.' . $ext, $diskPath),       // hero.avif
          ];
          foreach (array_unique(array_filter($candidates)) as $candidate) {
              if ($candidate !== $diskPath && file_exists($candidate)) {
                  $variantUrl = $baseUrl . '/' . ltrim(substr($candidate, strlen($basePath)), '/');
                  $variants[$type] = $variantUrl;
                  break;
              }
          }
      }
  }

  $imgAttrs = [];
  $imgAttrs['src']     = esc_url($src);
  $imgAttrs['alt']     = esc_attr($alt);
  if ($class !== '') {
      $imgAttrs['class'] = $class;
  }
  if ($width)  { $imgAttrs['width']  = (int) $width; }
  if ($height) { $imgAttrs['height'] = (int) $height; }
  if ($sizes && ! empty($srcset ?? null)) {
      $imgAttrs['sizes'] = $sizes;
  }
  $imgAttrs['decoding']      = 'async';
  $imgAttrs['loading']       = $priority ? 'eager' : 'lazy';
  $imgAttrs['fetchpriority'] = $priority ? 'high' : 'auto';

  $renderAttrs = function (array $attrs): string {
      $out = '';
      foreach ($attrs as $k => $v) {
          if ($v === null || $v === '') {
              continue;
          }
          $out .= ' ' . $k . '="' . (is_int($v) ? $v : esc_attr((string) $v)) . '"';
      }
      return $out;
  };
@endphp

@if(!empty($variants))
  <picture>
    @foreach($variants as $type => $url)
      <source type="{{ $type }}" srcset="{{ esc_url($url) }}">
    @endforeach
    <img{!! $renderAttrs($imgAttrs) !!}>
  </picture>
@else
  <img{!! $renderAttrs($imgAttrs) !!}>
@endif
