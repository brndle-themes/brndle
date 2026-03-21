<!doctype html>
<html @php(language_attributes()) class="scroll-smooth">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())

    @vite(['resources/css/app.css'])
    {{-- No JS loaded for landing pages — zero JS --}}
  </head>

  <body @php(body_class('font-sans antialiased'))>
    @php(wp_body_open())

    <div id="app">
      <a class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-2 focus:bg-white focus:text-slate-900 focus:rounded-lg focus:shadow-lg" href="#main">
        {{ __('Skip to content', 'brndle') }}
      </a>

      <main id="main">
        @yield('content')
      </main>
    </div>

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
