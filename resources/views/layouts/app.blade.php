@php
  // Toggle-driven mode: user can switch → read localStorage + ship JS controller.
  // Fixed mode (toggle off): theme is whatever the admin picked — no JS, no localStorage read, always wipe stale prefs.
  $toggleDriven = (bool) ($showDarkModeToggle ?? false);
  $initialTheme = in_array($darkModeDefault, ['light', 'dark', 'system'], true) ? $darkModeDefault : 'light';
  $viteEntries = ['resources/css/app.css'];
  if ($toggleDriven) {
    $viteEntries[] = 'resources/js/dark-mode.js';
  }
@endphp
<!doctype html>
<html @php(language_attributes()) class="scroll-smooth" data-theme="{{ $initialTheme }}">
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
