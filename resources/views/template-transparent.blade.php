{{--
  Template Name: Transparent Header
  Description: Header overlays the content with transparent background. Perfect for pages with dark hero sections.
--}}

@php($hasDarkMode = $showDarkModeToggle || $darkModeDefault !== 'light')
<!doctype html>
<html @php(language_attributes()) class="scroll-smooth" data-theme="{{ $darkModeDefault }}">
  <head>
    <meta charset="utf-8">
    @if($hasDarkMode)
    <script>
    (function(){var t=localStorage.getItem('brndle-theme');if(t==='dark'||t==='light'){document.documentElement.setAttribute('data-theme',t)}else{document.documentElement.setAttribute('data-theme',window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light')}})();
    </script>
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())
    @vite(['resources/css/app.css'])
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
              <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto">
            @else
              <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
              </div>
              <span class="text-lg font-bold tracking-tight text-white">{!! $siteName !!}</span>
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
                'link_before' => '<span class="text-[13px] font-medium text-white/70 hover:text-white transition-colors">',
                'link_after' => '</span>',
              ]) !!}
            </div>
          @endif

          {{-- CTA --}}
          <div class="hidden md:flex items-center gap-3">
            @if(!empty($headerCtaText))
              <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
                 class="px-5 py-2 text-[13px] font-semibold rounded-lg bg-white/10 text-white border border-white/20 hover:bg-white/20 transition-all">
                {{ $headerCtaText }}
              </a>
            @endif
          </div>

          {{-- Mobile toggle --}}
          <button id="brndle-menu-btn" class="md:hidden text-white p-2" aria-label="{{ esc_attr__('Toggle menu', 'brndle') }}" aria-expanded="false">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
          </button>
        </nav>

        {{-- Mobile menu --}}
        <div id="brndle-mobile-menu" class="md:hidden max-h-0 overflow-hidden transition-all duration-300 bg-surface-inverse/95 backdrop-blur-xl">
          <div class="max-w-7xl mx-auto px-6 py-6 space-y-1">
            @if(has_nav_menu('primary_navigation'))
              {!! wp_nav_menu([
                'theme_location' => 'primary_navigation',
                'menu_class' => 'space-y-1',
                'container' => false,
                'echo' => false,
                'link_before' => '<span class="block text-sm font-medium text-white/70 hover:text-white py-2.5 transition-colors">',
                'link_after' => '</span>',
              ]) !!}
            @endif
          </div>
        </div>

        {{-- Scroll effect: adds bg on scroll --}}
        <script>
        (function(){var h=document.getElementById('brndle-header');if(!h)return;window.addEventListener('scroll',function(){if(window.scrollY>50){h.style.backgroundColor='rgba(8,11,22,0.85)';h.style.backdropFilter='blur(20px)';h.style.boxShadow='0 1px 0 rgba(255,255,255,0.05)'}else{h.style.backgroundColor='transparent';h.style.backdropFilter='none';h.style.boxShadow='none'}},{passive:true})})();
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

    @if($hasDarkMode)
      @include('partials.components.dark-mode-toggle')
    @endif

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
