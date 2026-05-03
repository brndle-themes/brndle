@php
  // Toggle-driven mode: user can switch → read localStorage + ship JS controller.
  // Fixed mode (toggle off): theme is whatever the admin picked — no JS, no localStorage read, always wipe stale prefs.
  $toggleDriven = (bool) ($showDarkModeToggle ?? false);
  $initialTheme = in_array($darkModeDefault, ['light', 'dark', 'system'], true) ? $darkModeDefault : 'light';

  // Critical CSS path. Three states matter:
  //   1. Toggle off → standard @vite-driven render-blocking app.css.
  //   2. Toggle on AND `resources/css/critical.css` readable → inline
  //      critical + preload-and-swap deferred app.css.
  //   3. Toggle on BUT critical.css missing (release didn't ship it,
  //      file got deleted, perms wrong) → silently fall back to (1).
  //      Any other path leaves the page render-blocked-on-nothing — see
  //      attowp.com 1.4.0 incident — and that's the worst possible
  //      outcome.
  $criticalCssEnabled = (bool) \Brndle\Settings\Settings::get('perf_critical_css', false);
  $criticalCss = '';
  if ($criticalCssEnabled) {
    $criticalCssPath = get_theme_file_path('resources/css/critical.css');
    $criticalCss = is_readable($criticalCssPath) ? (string) file_get_contents($criticalCssPath) : '';
    if ($criticalCss === '') {
      // Asset missing on this install — degrade to render-blocking
      // app.css instead of dropping CSS entirely.
      $criticalCssEnabled = false;
    }
  }

  // When critical CSS is on (and the file is readable) we emit
  // `<link rel=preload>` for app.css ourselves and swap to
  // `rel=stylesheet` after load — so we drop it from the @vite call to
  // avoid a duplicate render-blocking <link>.
  $viteEntries = $criticalCssEnabled ? [] : ['resources/css/app.css'];
  if ($toggleDriven) {
    $viteEntries[] = 'resources/js/dark-mode.js';
  }
  if (\Brndle\Settings\Settings::get('perf_view_transitions', false)) {
    $viteEntries[] = 'resources/js/view-transitions.js';
  }
  // Mega menu controller (M1.C) — only needed when the primary nav exists.
  // The script is idempotent + early-exits if no `[data-brndle-has-submenu]`
  // elements are found, but skipping the request entirely on pages without
  // a registered nav is cheaper.
  if (has_nav_menu('primary_navigation')) {
    $viteEntries[] = 'resources/js/mega-menu.js';
  }
@endphp
<!doctype html>
<html <?php language_attributes(); ?> class="scroll-smooth" data-theme="{{ $initialTheme }}">
  <head>
    <meta charset="utf-8">
    @if ($toggleDriven)
      {{-- Toggle mode: applies stored preference synchronously before <body> paints to avoid FOUC. --}}
      <script>(function(){try{var s=localStorage.getItem('brndle-theme');if(s==='dark'||s==='light'||s==='system'){document.documentElement.setAttribute('data-theme',s)}}catch(e){}})();</script>
    @else
      {{-- Fixed mode: admin chose a single theme. Wipe any stale preference a user set before the toggle was disabled. --}}
      <script>try{localStorage.removeItem('brndle-theme')}catch(e){}</script>
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())

    @if ($criticalCssEnabled && $criticalCss !== '')
      {{-- Inline critical CSS so the first paint isn't render-blocked. --}}
      <style id="brndle-critical">{!! $criticalCss !!}</style>
      {{-- Preload the full app.css and swap to a stylesheet once it's
           loaded. The noscript fallback covers users with JS disabled. --}}
      <link rel="preload" as="style" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/app.css') }}" onload="this.onload=null;this.rel='stylesheet'">
      <noscript><link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/app.css') }}"></noscript>
    @endif

    @vite($viteEntries)
  </head>

  <body @php(body_class('font-sans antialiased bg-surface-primary text-text-primary'))>
    @php(wp_body_open())

    <div id="app">
      <a class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-2 focus:bg-accent focus:text-white focus:rounded-lg focus:text-sm focus:font-semibold" href="#main">
        {{ __('Skip to content', 'brndle') }}
      </a>

      @unless($hideHeader ?? false)
        @include('sections.header')
      @endunless

      <main id="main" class="main">
        @yield('content')
      </main>

      @hasSection('sidebar')
        <aside class="sidebar">
          @yield('sidebar')
        </aside>
      @endif

      @unless($hideFooter ?? false)
        @include('sections.footer')
      @endunless
    </div>

    @if ($toggleDriven && ($darkModeTogglePosition ?? '') !== 'header')
      @include('partials.components.dark-mode-toggle')
    @endif

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
