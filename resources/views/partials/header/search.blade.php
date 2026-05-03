{{--
  Header search slot (M4.B)

  Floating search trigger + popover. Rendered once at the layout level
  rather than inside each header style's markup so it works consistently
  across all 8 styles without per-style edits. The trigger is fixed-
  position at the top-right (offset to clear the WP admin bar when
  visible). The popover sits below the header (top: 80px) and is keyboard
  + click-outside dismissible (handled by header-behaviors.js).

  Search form rendered via WP's `get_search_form()` which respects any
  active `searchform.php` template + filter chain. When a plugin
  customizes search (Yoast, RankMath, search-by-algolia), this picks
  up that customization automatically.
--}}
@if ((bool) \Brndle\Settings\Settings::get('header_search_enabled', false))
  <button type="button"
          data-brndle-search-trigger
          aria-expanded="false"
          aria-controls="brndle-search-popover"
          aria-label="{{ esc_attr__('Toggle search', 'brndle') }}"
          class="brndle-search-trigger fixed top-3 right-20 z-[57] md:top-4 md:right-24"
          style="--admin-bar-offset: var(--wp-admin-bar-height, 0px); top: calc(0.75rem + var(--admin-bar-offset));">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <circle cx="11" cy="11" r="8"></circle>
      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
    </svg>
  </button>

  <div id="brndle-search-popover" hidden role="dialog" aria-modal="false" aria-label="{{ esc_attr__('Search', 'brndle') }}">
    {!! get_search_form(['echo' => false]) !!}
  </div>
@endif
