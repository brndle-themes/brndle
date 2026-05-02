{{--
  Template Name: Transparent Header
  Description: Header overlays the content with transparent background. Perfect for pages with dark hero sections.
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
<html <?php language_attributes(); ?> class="scroll-smooth" data-theme="{{ $initialTheme }}">
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

    <div id="app">
      <a class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-2 focus:bg-accent focus:text-white focus:rounded-lg focus:text-sm focus:font-semibold" href="#main">
        {{ __('Skip to content', 'brndle') }}
      </a>

      {{-- Transparent header overlay --}}
      <header id="brndle-header" class="fixed top-0 inset-x-0 z-50 transition-all duration-300" aria-label="{{ esc_attr__('Site header', 'brndle') }}">
        <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
          {{-- Logo --}}
          <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0">
            @if(!empty($siteLogo))
              <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto dark:hidden">
              <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto hidden dark:block">
            @else
              <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center shadow-lg shadow-accent/20">
                <span class="text-on-accent text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
              </div>
              <span class="text-lg font-bold tracking-tight text-text-primary">{{ $siteName }}</span>
            @endif
          </a>

          {{-- Nav links --}}
          @if(has_nav_menu('primary_navigation'))
            <div class="hidden md:flex items-center gap-8">
              {!! wp_nav_menu([
                'theme_location' => 'primary_navigation',
                'menu_class' => 'flex items-center gap-8',
                'container' => false,
                'echo' => false,
                'link_before' => '<span class="text-[13px] font-medium text-text-secondary hover:text-text-primary transition-colors">',
                'link_after' => '</span>',
              ]) !!}
            </div>
          @endif

          {{-- CTA --}}
          <div class="hidden md:flex items-center gap-3">
            @if(!empty($headerCtaText))
              <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
                 class="px-5 py-2 text-[13px] font-semibold rounded-lg bg-accent text-on-accent hover:opacity-90 transition-opacity">
                {{ $headerCtaText }}
              </a>
            @endif
          </div>

          {{-- Mobile toggle --}}
          <button id="brndle-menu-btn" class="md:hidden text-text-secondary p-2" aria-label="{{ esc_attr__('Toggle menu', 'brndle') }}" aria-expanded="false">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
          </button>
        </nav>

        {{-- Mobile menu --}}
        <div id="brndle-mobile-menu" class="md:hidden max-h-0 overflow-hidden transition-all duration-300 bg-surface-primary/95 backdrop-blur-xl border-t border-surface-tertiary/50">
          <div class="max-w-7xl mx-auto px-6 py-6 space-y-1">
            @if(has_nav_menu('primary_navigation'))
              {!! wp_nav_menu([
                'theme_location' => 'primary_navigation',
                'menu_class' => 'space-y-1',
                'container' => false,
                'echo' => false,
                'link_before' => '<span class="block text-sm font-medium text-text-secondary hover:text-text-primary py-2.5 transition-colors">',
                'link_after' => '</span>',
              ]) !!}
            @endif
          </div>
        </div>

        {{-- Scroll effect: adds bg on scroll, driven by theme-aware surface color --}}
        <script>
        (function(){
          var h=document.getElementById('brndle-header');
          if(!h)return;
          window.addEventListener('scroll',function(){
            var s=window.scrollY>50;
            h.classList.toggle('bg-surface-primary/85',s);
            h.classList.toggle('backdrop-blur-xl',s);
            h.classList.toggle('border-b',s);
            h.classList.toggle('border-surface-tertiary/50',s);
          },{passive:true});
        })();
        </script>

        {{-- Mobile menu toggle --}}
        <script>
        (function(){var b=document.getElementById('brndle-menu-btn'),m=document.getElementById('brndle-mobile-menu');if(!b||!m)return;b.addEventListener('click',function(){var o=m.classList.toggle('max-h-[400px]');m.classList.toggle('max-h-0');b.setAttribute('aria-expanded',o)})})();
        </script>
      </header>

      <main id="main">
        @while(have_posts())
          @php(the_post())
          @php(the_content())
        @endwhile
      </main>

      @include('sections.footer')
    </div>

    {{-- Transparent template renders its own inline header (above). When the user
         picks 'header' for the toggle position, that inline header does not include
         the partial itself, so we always render the floating button here — but fall
         back to a sensible corner when 'header' is selected. --}}
    @if ($toggleDriven)
      @include('partials.components.dark-mode-toggle', ['position' => ($darkModeTogglePosition === 'header' ? 'bottom-right' : $darkModeTogglePosition)])
    @endif

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
