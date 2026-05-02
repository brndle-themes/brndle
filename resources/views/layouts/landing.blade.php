@php
  $toggleDriven = (bool) ($showDarkModeToggle ?? false);
  $initialTheme = in_array($darkModeDefault, ['light', 'dark', 'system'], true) ? $darkModeDefault : 'light';
  $viteEntries = ['resources/css/app.css'];
  if ($toggleDriven) {
    $viteEntries[] = 'resources/js/dark-mode.js';
  }
  if (\Brndle\Settings\Settings::get('perf_view_transitions', false)) {
    $viteEntries[] = 'resources/js/view-transitions.js';
  }
@endphp
<!doctype html>
<html @php(language_attributes()) class="scroll-smooth" data-theme="{{ $initialTheme }}">
  <head>
    <meta charset="utf-8">
    @if ($toggleDriven)
      <script>(function(){try{var s=localStorage.getItem('brndle-theme');if(s==='dark'||s==='light'||s==='system'){document.documentElement.setAttribute('data-theme',s)}}catch(e){}})();</script>
    @else
      <script>try{localStorage.removeItem('brndle-theme')}catch(e){}</script>
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())

    @vite($viteEntries)
    {{-- Landing layout stays lean: only ships dark-mode JS when the feature is enabled. --}}
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

      <main id="main">
        @yield('content')
      </main>

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
