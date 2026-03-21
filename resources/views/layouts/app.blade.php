<!doctype html>
<html @php(language_attributes()) class="scroll-smooth" data-theme="{{ $darkModeDefault }}">
  <head>
    <meta charset="utf-8">
    <script>
    (function(){var t=localStorage.getItem('brndle-theme');if(t==='dark'||t==='light'){document.documentElement.setAttribute('data-theme',t)}else{document.documentElement.setAttribute('data-theme',window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light')}})();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body @php(body_class('font-sans antialiased bg-surface-primary text-text-primary'))>
    @php(wp_body_open())

    <div id="app">
      <a class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-2 focus:bg-white focus:text-slate-900 focus:rounded-lg focus:shadow-lg" href="#main">
        {{ __('Skip to content', 'brndle') }}
      </a>

      @include('sections.header')

      <main id="main" class="main">
        @yield('content')
      </main>

      @hasSection('sidebar')
        <aside class="sidebar">
          @yield('sidebar')
        </aside>
      @endif

      @include('sections.footer')
    </div>

    @if($showDarkModeToggle)
      @include('partials.components.dark-mode-toggle')
    @endif

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
