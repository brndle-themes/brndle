{{--
  Template Name: Full Canvas
  Description: No header, no footer, no margins. Pure full-screen content canvas for creative pages.
--}}

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
    @vite(['resources/css/app.css'])
  </head>

  <body @php(body_class('font-sans antialiased bg-surface-primary text-text-primary'))>
    @php(wp_body_open())

    <main id="main">
      @while(have_posts())
        @php(the_post())
        @php(the_content())
      @endwhile
    </main>

    @if($showDarkModeToggle)
      @include('partials.components.dark-mode-toggle')
    @endif

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
