{{--
  Print-only "Source:" line.

  Hidden on screen via Tailwind's `print:` variant. The complementary
  `@media print` block in app.css forces it to display when the page is
  printed and styles it as a footer line under the article.
--}}
@php
  $printSource = '';
  if (is_singular()) {
      $printSource = (string) get_permalink();
  } elseif (function_exists('home_url')) {
      $printSource = (string) home_url(add_query_arg(null, null));
  }
@endphp

@if($printSource !== '')
  <p class="brndle-print-source hidden print:block">
    {{ __('Source:', 'brndle') }} <span>{{ $printSource }}</span>
  </p>
@endif
