{{--
  Template Name: Full Canvas
  Description: No header, no footer, no margins. Pure full-screen content canvas for creative pages.
--}}

@php
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
      <script>(function(){try{var s=localStorage.getItem('brndle-theme');if(s==='dark'||s==='light'||s==='system'){document.documentElement.setAttribute('data-theme',s)}}catch(e){}})();</script>
    @else
      <script>try{localStorage.removeItem('brndle-theme')}catch(e){}</script>
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())
    @vite($viteEntries)
  </head>

  <body @php(body_class('font-sans antialiased bg-surface-primary text-text-primary'))>
    @php(wp_body_open())

    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-2 focus:bg-accent focus:text-white focus:rounded-lg focus:text-sm focus:font-semibold">{{ __('Skip to content', 'brndle') }}</a>

    <main id="main">
      @while(have_posts())
        @php(the_post())
        @php(the_content())
      @endwhile
    </main>

    {{-- Canvas has no header to host the inline toggle, so render the floating button
         whenever dark mode is enabled — even if position is set to 'header'. --}}
    @if ($toggleDriven)
      @include('partials.components.dark-mode-toggle', ['position' => ($darkModeTogglePosition === 'header' ? 'bottom-right' : $darkModeTogglePosition)])
    @endif

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
