{{--
  Back-to-top floating button.

  Rendered from layouts/app.blade.php when the
  `brndle/back_to_top_enabled` filter returns true (default). The
  controller in resources/js/back-to-top.js handles visibility +
  smooth-scroll + reduced-motion.
--}}
<button
  type="button"
  data-brndle-back-to-top
  aria-label="{{ __('Back to top', 'brndle') }}"
  class="brndle-back-to-top">
  <x-icon name="arrow-up" class="h-5 w-5" />
</button>
