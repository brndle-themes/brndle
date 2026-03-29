@php
  $style = $headerStyle ?? 'sticky';
  $toggleInHeader = ($showDarkModeToggle ?? false) && ($darkModeTogglePosition ?? '') === 'header';
@endphp

{{-- ============================================================
     STYLE: MINIMAL — Floating hamburger + fullscreen overlay
     ============================================================ --}}
@if($style === 'minimal')
<header id="brndle-header" class="fixed top-0 inset-x-0 z-50" aria-label="{{ esc_attr__('Site header', 'brndle') }}">
  <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="{{ esc_attr__('Main navigation', 'brndle') }}">
    {{-- Logo (hidden on minimal — shows only in overlay) --}}
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0 opacity-0 pointer-events-none">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto hidden dark:block">
      @else
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center shadow-lg shadow-accent/20">
          <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
      @endif
    </a>

    {{-- Floating hamburger button --}}
    <button id="brndle-minimal-btn" type="button"
            class="fixed top-5 right-6 z-[60] w-11 h-11 rounded-full bg-surface-primary shadow-lg border border-surface-tertiary/50 flex items-center justify-center text-text-secondary hover:text-text-primary transition-colors"
            aria-expanded="false" aria-controls="brndle-minimal-overlay" aria-label="{{ esc_attr__('Open menu', 'brndle') }}">
      <svg id="brndle-minimal-open" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
      </svg>
      <svg id="brndle-minimal-close" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </nav>

  {{-- Fullscreen overlay --}}
  <div id="brndle-minimal-overlay"
       class="fixed inset-0 z-50 bg-surface-primary flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
    <a href="{{ home_url('/') }}" class="mb-12 flex items-center gap-3">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-10 w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-10 w-auto hidden dark:block">
      @else
        <div class="w-10 h-10 rounded-lg bg-accent flex items-center justify-center shadow-lg shadow-accent/20">
          <span class="text-white text-base font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
        <span class="text-xl font-bold tracking-tight text-text-primary">{{ $siteName }}</span>
      @endif
    </a>
    @if(has_nav_menu('primary_navigation'))
      {!! wp_nav_menu([
        'theme_location' => 'primary_navigation',
        'menu_class' => 'flex flex-col items-center gap-6',
        'container' => false,
        'echo' => false,
        'link_before' => '<span class="text-2xl font-light text-text-secondary hover:text-text-primary transition-colors">',
        'link_after' => '</span>',
      ]) !!}
    @endif
    @if(!empty($headerCtaText))
      <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
         class="mt-10 px-8 py-3 text-base font-semibold rounded-lg bg-accent text-on-accent hover:opacity-90 transition-opacity">
        {{ $headerCtaText }}
      </a>
    @endif
    @if($toggleInHeader)
      <div class="mt-8">
        @include('partials.components.dark-mode-toggle')
      </div>
    @endif
  </div>
</header>

<script>
(function(){
  var btn=document.getElementById('brndle-minimal-btn');
  var overlay=document.getElementById('brndle-minimal-overlay');
  var iconOpen=document.getElementById('brndle-minimal-open');
  var iconClose=document.getElementById('brndle-minimal-close');
  if(!btn||!overlay)return;
  btn.addEventListener('click',function(){
    var isOpen=overlay.classList.toggle('opacity-100');
    overlay.classList.toggle('opacity-0');
    overlay.classList.toggle('pointer-events-none');
    overlay.classList.toggle('pointer-events-auto');
    iconOpen.classList.toggle('hidden',isOpen);
    iconClose.classList.toggle('hidden',!isOpen);
    btn.setAttribute('aria-expanded',isOpen);
    document.body.style.overflow=isOpen?'hidden':'';
  });
})();
</script>

{{-- ============================================================
     STYLE: CENTERED — Logo top, nav pills below
     ============================================================ --}}
@elseif($style === 'centered')
<header id="brndle-header" class="relative z-50 bg-surface-primary border-b border-surface-tertiary/50" aria-label="{{ esc_attr__('Site header', 'brndle') }}">
  {{-- Top row: logo centered --}}
  <div class="max-w-7xl mx-auto px-6 pt-6 pb-3 flex items-center justify-center">
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-9 w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-9 w-auto hidden dark:block">
      @else
        <div class="w-9 h-9 rounded-lg bg-accent flex items-center justify-center shadow-lg shadow-accent/20">
          <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
        <span class="text-lg font-bold tracking-tight text-text-primary">{{ $siteName }}</span>
      @endif
    </a>
  </div>

  {{-- Bottom row: nav pills --}}
  <nav class="max-w-7xl mx-auto px-6 pb-4 flex items-center justify-center gap-3" aria-label="{{ esc_attr__('Main navigation', 'brndle') }}">
    {{-- Desktop nav --}}
    @if(has_nav_menu('primary_navigation'))
      <div class="hidden md:flex items-center gap-1">
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'flex items-center gap-1',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="px-4 py-1.5 text-base font-medium text-text-secondary hover:text-text-primary hover:bg-surface-secondary rounded-full transition-all">',
          'link_after' => '</span>',
        ]) !!}
      </div>
    @endif

    {{-- Dark mode toggle --}}
    @if($toggleInHeader)
      <div class="hidden md:block">
        @include('partials.components.dark-mode-toggle')
      </div>
    @endif

    {{-- CTA --}}
    @if(!empty($headerCtaText))
      <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
         class="hidden md:inline-flex px-5 py-1.5 text-base font-semibold rounded-full bg-accent text-on-accent hover:opacity-90 transition-opacity">
        {{ $headerCtaText }}
      </a>
    @endif

    {{-- Mobile hamburger --}}
    <button id="brndle-menu-btn" type="button"
            class="md:hidden flex items-center justify-center w-10 h-10 text-text-secondary"
            aria-expanded="false" aria-controls="brndle-mobile-menu" aria-label="{{ esc_attr__('Toggle menu', 'brndle') }}">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
      </svg>
    </button>
  </nav>

  {{-- Mobile menu --}}
  <div id="brndle-mobile-menu" class="overflow-hidden transition-all duration-300 max-h-0 md:hidden bg-surface-primary border-t border-surface-tertiary/50">
    <div class="max-w-7xl mx-auto px-6 py-4 space-y-1">
      @if(has_nav_menu('primary_navigation'))
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'space-y-1',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="block text-base font-medium text-text-secondary hover:text-accent py-2.5 transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      @endif
      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block mt-4 text-center text-base font-semibold px-5 py-2.5 rounded-lg bg-accent text-on-accent">
          {{ $headerCtaText }}
        </a>
      @endif
      @if($toggleInHeader)
        <div class="pt-2">
          @include('partials.components.dark-mode-toggle')
        </div>
      @endif
    </div>
  </div>
</header>

<script>
(function(){
  var btn=document.getElementById('brndle-menu-btn');
  var menu=document.getElementById('brndle-mobile-menu');
  if(!btn||!menu)return;
  btn.addEventListener('click',function(){
    var open=menu.classList.toggle('max-h-[400px]');
    menu.classList.toggle('max-h-0');
    btn.setAttribute('aria-expanded',open);
  });
})();
</script>

{{-- ============================================================
     STYLE: TRANSPARENT — Fixed, becomes solid on scroll
     ============================================================ --}}
@elseif($style === 'transparent')
<header id="brndle-header" class="fixed top-0 inset-x-0 z-50 bg-transparent transition-all" aria-label="{{ esc_attr__('Site header', 'brndle') }}">
  <nav class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between" aria-label="{{ esc_attr__('Main navigation', 'brndle') }}">
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-[52px] w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-[52px] w-auto hidden dark:block">
      @else
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center shadow-lg shadow-accent/20">
          <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
        <span class="text-lg font-bold tracking-tight text-text-primary">{{ $siteName }}</span>
      @endif
    </a>

    {{-- Desktop Navigation --}}
    @if(has_nav_menu('primary_navigation'))
      <div class="hidden md:flex items-center gap-8">
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'flex items-center gap-8',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="text-base font-medium text-text-secondary hover:text-text-primary transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      </div>
    @endif

    {{-- Right side --}}
    <div class="hidden md:flex items-center gap-3">
      @if($toggleInHeader)
        @include('partials.components.dark-mode-toggle')
      @endif

      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
           class="px-5 py-2 text-base font-semibold rounded-lg bg-accent text-on-accent hover:opacity-90 transition-opacity">
          {{ $headerCtaText }}
        </a>
      @endif
    </div>

    {{-- Mobile toggle --}}
    <button id="brndle-menu-btn" type="button"
            class="md:hidden flex items-center justify-center w-10 h-10 text-text-secondary"
            aria-expanded="false" aria-controls="brndle-mobile-menu" aria-label="{{ esc_attr__('Toggle menu', 'brndle') }}">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
      </svg>
    </button>
  </nav>

  {{-- Mobile menu --}}
  <div id="brndle-mobile-menu" class="overflow-hidden transition-all duration-300 max-h-0 md:hidden bg-surface-primary">
    <div class="max-w-7xl mx-auto px-6 py-4 space-y-1">
      @if(has_nav_menu('primary_navigation'))
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'space-y-1',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="block text-base font-medium text-text-secondary hover:text-accent py-2.5 transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      @endif
      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block mt-4 text-center text-base font-semibold px-5 py-2.5 rounded-lg bg-accent text-on-accent">
          {{ $headerCtaText }}
        </a>
      @endif
      @if($toggleInHeader)
        <div class="pt-2">
          @include('partials.components.dark-mode-toggle')
        </div>
      @endif
    </div>
  </div>
</header>

<script>
(function(){
  var h=document.getElementById('brndle-header');
  if(!h)return;
  window.addEventListener('scroll',function(){
    var s=window.scrollY>50;
    h.classList.toggle('bg-surface-primary/80',s);
    h.classList.toggle('backdrop-blur-xl',s);
    h.classList.toggle('shadow-sm',s);
    h.classList.toggle('border-b',s);
    h.classList.toggle('border-surface-tertiary/50',s);
  },{passive:true});
})();
</script>
<script>
(function(){
  var btn=document.getElementById('brndle-menu-btn');
  var menu=document.getElementById('brndle-mobile-menu');
  if(!btn||!menu)return;
  btn.addEventListener('click',function(){
    var open=menu.classList.toggle('max-h-[400px]');
    menu.classList.toggle('max-h-0');
    btn.setAttribute('aria-expanded',open);
  });
})();
</script>

{{-- ============================================================
     STYLE: SOLID — Static, solid bg, thicker border
     ============================================================ --}}
@elseif($style === 'solid')
<header id="brndle-header" class="relative z-50 bg-surface-primary border-b-2 border-surface-tertiary" aria-label="{{ esc_attr__('Site header', 'brndle') }}">
  <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="{{ esc_attr__('Main navigation', 'brndle') }}">
    {{-- Logo --}}
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto hidden dark:block">
      @else
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center shadow-lg shadow-accent/20">
          <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
        <span class="text-lg font-bold tracking-tight text-text-primary">{{ $siteName }}</span>
      @endif
    </a>

    {{-- Desktop Navigation --}}
    @if(has_nav_menu('primary_navigation'))
      <div class="hidden md:flex items-center gap-8">
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'flex items-center gap-8',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="text-base font-medium text-text-secondary hover:text-text-primary transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      </div>
    @endif

    {{-- Right side --}}
    <div class="hidden md:flex items-center gap-3">
      @if($toggleInHeader)
        @include('partials.components.dark-mode-toggle')
      @endif

      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
           class="px-5 py-2 text-base font-semibold rounded-lg bg-accent text-on-accent hover:opacity-90 transition-opacity">
          {{ $headerCtaText }}
        </a>
      @endif
    </div>

    {{-- Mobile toggle --}}
    <button id="brndle-menu-btn" type="button"
            class="md:hidden flex items-center justify-center w-10 h-10 text-text-secondary"
            aria-expanded="false" aria-controls="brndle-mobile-menu" aria-label="{{ esc_attr__('Toggle menu', 'brndle') }}">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
      </svg>
    </button>
  </nav>

  {{-- Mobile menu --}}
  <div id="brndle-mobile-menu" class="overflow-hidden transition-all duration-300 max-h-0 md:hidden bg-surface-primary border-t border-surface-tertiary">
    <div class="max-w-7xl mx-auto px-6 py-4 space-y-1">
      @if(has_nav_menu('primary_navigation'))
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'space-y-1',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="block text-base font-medium text-text-secondary hover:text-accent py-2.5 transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      @endif
      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block mt-4 text-center text-base font-semibold px-5 py-2.5 rounded-lg bg-accent text-on-accent">
          {{ $headerCtaText }}
        </a>
      @endif
      @if($toggleInHeader)
        <div class="pt-2">
          @include('partials.components.dark-mode-toggle')
        </div>
      @endif
    </div>
  </div>
</header>

<script>
(function(){
  var btn=document.getElementById('brndle-menu-btn');
  var menu=document.getElementById('brndle-mobile-menu');
  if(!btn||!menu)return;
  btn.addEventListener('click',function(){
    var open=menu.classList.toggle('max-h-[400px]');
    menu.classList.toggle('max-h-0');
    btn.setAttribute('aria-expanded',open);
  });
})();
</script>

{{-- ============================================================
     STYLE: SPLIT — Two-row header (logo + nav separated)
     ============================================================ --}}
@elseif($style === 'split')
<header id="brndle-header" class="relative z-50 bg-surface-primary" aria-label="{{ esc_attr__('Site header', 'brndle') }}">
  {{-- Top row: logo + search + CTA --}}
  <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
    {{-- Logo --}}
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto hidden dark:block">
      @else
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center shadow-lg shadow-accent/20">
          <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
        <span class="text-lg font-bold tracking-tight text-text-primary">{{ $siteName }}</span>
      @endif
    </a>

    {{-- Right side: search icon + dark mode + CTA --}}
    <div class="flex items-center gap-3">
      {{-- Search icon --}}
      <button type="button" class="hidden md:flex items-center justify-center w-10 h-10 text-text-secondary hover:text-text-primary transition-colors" aria-label="{{ esc_attr__('Search', 'brndle') }}" onclick="document.dispatchEvent(new CustomEvent('brndle:search-open'))">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
        </svg>
      </button>

      @if($toggleInHeader)
        <div class="hidden md:block">
          @include('partials.components.dark-mode-toggle')
        </div>
      @endif

      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
           class="hidden md:inline-flex px-5 py-2 text-base font-semibold rounded-lg bg-accent text-on-accent hover:opacity-90 transition-opacity">
          {{ $headerCtaText }}
        </a>
      @endif

      {{-- Mobile toggle --}}
      <button id="brndle-menu-btn" type="button"
              class="md:hidden flex items-center justify-center w-10 h-10 text-text-secondary"
              aria-expanded="false" aria-controls="brndle-mobile-menu" aria-label="{{ esc_attr__('Toggle menu', 'brndle') }}">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
        </svg>
      </button>
    </div>
  </div>

  {{-- Bottom row: centered nav --}}
  <div class="hidden md:block border-t border-surface-tertiary/50">
    <nav class="max-w-7xl mx-auto px-6 h-12 flex items-center justify-center" aria-label="{{ esc_attr__('Main navigation', 'brndle') }}">
      @if(has_nav_menu('primary_navigation'))
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'flex items-center gap-8',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="text-base font-medium text-text-secondary hover:text-text-primary transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      @endif
    </nav>
  </div>

  {{-- Mobile menu --}}
  <div id="brndle-mobile-menu" class="overflow-hidden transition-all duration-300 max-h-0 md:hidden bg-surface-primary border-t border-surface-tertiary/50">
    <div class="max-w-7xl mx-auto px-6 py-4 space-y-1">
      @if(has_nav_menu('primary_navigation'))
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'space-y-1',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="block text-base font-medium text-text-secondary hover:text-accent py-2.5 transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      @endif
      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block mt-4 text-center text-base font-semibold px-5 py-2.5 rounded-lg bg-accent text-on-accent">
          {{ $headerCtaText }}
        </a>
      @endif
      @if($toggleInHeader)
        <div class="pt-2">
          @include('partials.components.dark-mode-toggle')
        </div>
      @endif
    </div>
  </div>
</header>

<script>
(function(){
  var btn=document.getElementById('brndle-menu-btn');
  var menu=document.getElementById('brndle-mobile-menu');
  if(!btn||!menu)return;
  btn.addEventListener('click',function(){
    var open=menu.classList.toggle('max-h-[400px]');
    menu.classList.toggle('max-h-0');
    btn.setAttribute('aria-expanded',open);
  });
})();
</script>

{{-- ============================================================
     STYLE: BANNER — Announcement bar + sticky header
     ============================================================ --}}
@elseif($style === 'banner')
@php
  $bannerText = $headerBannerText ?? 'Free shipping on all orders';
@endphp
<div id="brndle-banner" class="relative z-50 bg-accent text-on-accent text-center text-sm py-2 px-6 transition-all" role="banner" aria-label="{{ esc_attr__('Announcement', 'brndle') }}">
  <div class="max-w-7xl mx-auto flex items-center justify-center gap-4">
    <span class="font-medium">{{ $bannerText }}</span>
    <button id="brndle-banner-close" type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-colors" aria-label="{{ esc_attr__('Dismiss announcement', 'brndle') }}">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>
</div>

<header id="brndle-header" class="sticky top-0 z-50 bg-surface-primary/80 backdrop-blur-xl border-b border-surface-tertiary/50 transition-all" aria-label="{{ esc_attr__('Site header', 'brndle') }}">
  <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="{{ esc_attr__('Main navigation', 'brndle') }}">
    {{-- Logo --}}
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto hidden dark:block">
      @else
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center shadow-lg shadow-accent/20">
          <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
        <span class="text-lg font-bold tracking-tight text-text-primary">{{ $siteName }}</span>
      @endif
    </a>

    {{-- Desktop Navigation --}}
    @if(has_nav_menu('primary_navigation'))
      <div class="hidden md:flex items-center gap-8">
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'flex items-center gap-8',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="text-base font-medium text-text-secondary hover:text-text-primary transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      </div>
    @endif

    {{-- Right side --}}
    <div class="hidden md:flex items-center gap-3">
      @if($toggleInHeader)
        @include('partials.components.dark-mode-toggle')
      @endif

      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
           class="px-5 py-2 text-base font-semibold rounded-lg bg-accent text-on-accent hover:opacity-90 transition-opacity">
          {{ $headerCtaText }}
        </a>
      @endif
    </div>

    {{-- Mobile toggle --}}
    <button id="brndle-menu-btn" type="button"
            class="md:hidden flex items-center justify-center w-10 h-10 text-text-secondary"
            aria-expanded="false" aria-controls="brndle-mobile-menu" aria-label="{{ esc_attr__('Toggle menu', 'brndle') }}">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
      </svg>
    </button>
  </nav>

  {{-- Mobile menu --}}
  <div id="brndle-mobile-menu" class="overflow-hidden transition-all duration-300 max-h-0 md:hidden bg-surface-primary border-t border-surface-tertiary/50">
    <div class="max-w-7xl mx-auto px-6 py-4 space-y-1">
      @if(has_nav_menu('primary_navigation'))
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'space-y-1',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="block text-base font-medium text-text-secondary hover:text-accent py-2.5 transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      @endif
      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block mt-4 text-center text-base font-semibold px-5 py-2.5 rounded-lg bg-accent text-on-accent">
          {{ $headerCtaText }}
        </a>
      @endif
      @if($toggleInHeader)
        <div class="pt-2">
          @include('partials.components.dark-mode-toggle')
        </div>
      @endif
    </div>
  </div>
</header>

<script>
(function(){
  var banner=document.getElementById('brndle-banner');
  var closeBtn=document.getElementById('brndle-banner-close');
  if(!banner||!closeBtn)return;
  if(localStorage.getItem('brndle_banner_hidden')==='1'){banner.style.display='none';return;}
  closeBtn.addEventListener('click',function(){
    banner.style.display='none';
    localStorage.setItem('brndle_banner_hidden','1');
  });
})();
(function(){
  var btn=document.getElementById('brndle-menu-btn');
  var menu=document.getElementById('brndle-mobile-menu');
  if(!btn||!menu)return;
  btn.addEventListener('click',function(){
    var open=menu.classList.toggle('max-h-[400px]');
    menu.classList.toggle('max-h-0');
    btn.setAttribute('aria-expanded',open);
  });
})();
</script>

{{-- ============================================================
     STYLE: GLASS — Glassmorphism fixed header for dark heroes
     ============================================================ --}}
@elseif($style === 'glass')
<header id="brndle-header" class="fixed top-0 inset-x-0 z-50 transition-all" aria-label="{{ esc_attr__('Site header', 'brndle') }}">
  {{-- Gradient top border --}}
  <div class="h-px bg-gradient-to-r from-accent via-accent/50 to-transparent"></div>

  <div class="bg-surface-primary/80 backdrop-blur-2xl backdrop-saturate-150 border-b border-surface-tertiary/50" id="brndle-glass-bg">
    <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="{{ esc_attr__('Main navigation', 'brndle') }}">
      {{-- Logo --}}
      <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0">
        @if(!empty($siteLogo))
          <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto">
        @else
          <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center shadow-lg shadow-accent/20">
            <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
          </div>
          <span class="text-lg font-bold tracking-tight text-white">{{ $siteName }}</span>
        @endif
      </a>

      {{-- Desktop Navigation --}}
      @if(has_nav_menu('primary_navigation'))
        <div class="hidden md:flex items-center gap-8">
          {!! wp_nav_menu([
            'theme_location' => 'primary_navigation',
            'menu_class' => 'flex items-center gap-8',
            'container' => false,
            'echo' => false,
            'link_before' => '<span class="text-base font-medium text-text-secondary hover:text-text-primary transition-colors">',
            'link_after' => '</span>',
          ]) !!}
        </div>
      @endif

      {{-- Right side --}}
      <div class="hidden md:flex items-center gap-3">
        @if($toggleInHeader)
          @include('partials.components.dark-mode-toggle')
        @endif

        @if(!empty($headerCtaText))
          <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
             class="px-5 py-2 text-base font-semibold rounded-lg bg-accent text-on-accent hover:opacity-90 transition-opacity">
            {{ $headerCtaText }}
          </a>
        @endif
      </div>

      {{-- Mobile toggle --}}
      <button id="brndle-menu-btn" type="button"
              class="md:hidden flex items-center justify-center w-10 h-10 text-text-secondary"
              aria-expanded="false" aria-controls="brndle-mobile-menu" aria-label="{{ esc_attr__('Toggle menu', 'brndle') }}">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
        </svg>
      </button>
    </nav>

    {{-- Mobile menu --}}
    <div id="brndle-mobile-menu" class="overflow-hidden transition-all duration-300 max-h-0 md:hidden border-t border-white/10">
      <div class="max-w-7xl mx-auto px-6 py-4 space-y-1">
        @if(has_nav_menu('primary_navigation'))
          {!! wp_nav_menu([
            'theme_location' => 'primary_navigation',
            'menu_class' => 'space-y-1',
            'container' => false,
            'echo' => false,
            'link_before' => '<span class="block text-base font-medium text-white/60 hover:text-white py-2.5 transition-colors">',
            'link_after' => '</span>',
          ]) !!}
        @endif
        @if(!empty($headerCtaText))
          <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block mt-4 text-center text-base font-semibold px-5 py-2.5 rounded-lg bg-accent text-on-accent">
            {{ $headerCtaText }}
          </a>
        @endif
        @if($toggleInHeader)
          <div class="pt-2">
            @include('partials.components.dark-mode-toggle')
          </div>
        @endif
      </div>
    </div>
  </div>
</header>

<script>
(function(){
  var bg=document.getElementById('brndle-glass-bg');
  if(!bg)return;
  window.addEventListener('scroll',function(){
    var o=Math.min(window.scrollY/200,0.85);
    bg.style.backgroundColor='rgba(0,0,0,'+o+')';
  },{passive:true});
})();
(function(){
  var btn=document.getElementById('brndle-menu-btn');
  var menu=document.getElementById('brndle-mobile-menu');
  if(!btn||!menu)return;
  btn.addEventListener('click',function(){
    var open=menu.classList.toggle('max-h-[400px]');
    menu.classList.toggle('max-h-0');
    btn.setAttribute('aria-expanded',open);
  });
})();
</script>

{{-- ============================================================
     STYLE: STICKY (default) — Glassmorphism sticky header
     ============================================================ --}}
@else
<header id="brndle-header" class="sticky top-0 z-50 bg-surface-primary/80 backdrop-blur-xl border-b border-surface-tertiary/50 transition-all" aria-label="{{ esc_attr__('Site header', 'brndle') }}">
  <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="{{ esc_attr__('Main navigation', 'brndle') }}">
    {{-- Logo --}}
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto hidden dark:block">
      @else
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center shadow-lg shadow-accent/20">
          <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
        <span class="text-lg font-bold tracking-tight text-text-primary">{{ $siteName }}</span>
      @endif
    </a>

    {{-- Desktop Navigation --}}
    @if(has_nav_menu('primary_navigation'))
      <div class="hidden md:flex items-center gap-8">
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'flex items-center gap-8',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="text-base font-medium text-text-secondary hover:text-text-primary transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      </div>
    @endif

    {{-- Right side --}}
    <div class="hidden md:flex items-center gap-3">
      @if($toggleInHeader)
        @include('partials.components.dark-mode-toggle')
      @endif

      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
           class="px-5 py-2 text-base font-semibold rounded-lg bg-accent text-on-accent hover:opacity-90 transition-opacity">
          {{ $headerCtaText }}
        </a>
      @endif
    </div>

    {{-- Mobile toggle --}}
    <button id="brndle-menu-btn" type="button"
            class="md:hidden flex items-center justify-center w-10 h-10 text-text-secondary"
            aria-expanded="false" aria-controls="brndle-mobile-menu" aria-label="{{ esc_attr__('Toggle menu', 'brndle') }}">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
      </svg>
    </button>
  </nav>

  {{-- Mobile menu --}}
  <div id="brndle-mobile-menu" class="overflow-hidden transition-all duration-300 max-h-0 md:hidden bg-surface-primary border-t border-surface-tertiary/50">
    <div class="max-w-7xl mx-auto px-6 py-4 space-y-1">
      @if(has_nav_menu('primary_navigation'))
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'space-y-1',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="block text-base font-medium text-text-secondary hover:text-accent py-2.5 transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      @endif
      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block mt-4 text-center text-base font-semibold px-5 py-2.5 rounded-lg bg-accent text-on-accent">
          {{ $headerCtaText }}
        </a>
      @endif
      @if($toggleInHeader)
        <div class="pt-2">
          @include('partials.components.dark-mode-toggle')
        </div>
      @endif
    </div>
  </div>
</header>

<script>
(function(){
  var btn=document.getElementById('brndle-menu-btn');
  var menu=document.getElementById('brndle-mobile-menu');
  if(!btn||!menu)return;
  btn.addEventListener('click',function(){
    var open=menu.classList.toggle('max-h-[400px]');
    menu.classList.toggle('max-h-0');
    btn.setAttribute('aria-expanded',open);
  });
})();
</script>
@endif
