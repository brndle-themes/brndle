@php
  $style = $headerStyle ?? 'sticky';
@endphp

{{-- ============================================================
     STYLE: MINIMAL — Floating hamburger + fullscreen overlay
     ============================================================ --}}
@if($style === 'minimal')
<header id="brndle-header" class="fixed top-0 inset-x-0 z-50" aria-label="Site header">
  <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="Main navigation">
    {{-- Logo (hidden on minimal — shows only in overlay) --}}
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0 opacity-0 pointer-events-none">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto hidden dark:block">
      @else
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20">
          <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
      @endif
    </a>

    {{-- Floating hamburger button --}}
    <button id="brndle-minimal-btn" type="button"
            class="fixed top-5 right-6 z-[60] w-11 h-11 rounded-full bg-surface-primary shadow-lg border border-surface-tertiary/50 flex items-center justify-center text-text-secondary hover:text-text-primary transition-colors"
            aria-expanded="false" aria-controls="brndle-minimal-overlay" aria-label="Open menu">
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
        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20">
          <span class="text-white text-base font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
        <span class="text-xl font-bold tracking-tight text-text-primary">{!! $siteName !!}</span>
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
         class="mt-10 px-8 py-3 text-sm font-semibold rounded-lg bg-accent text-white hover:opacity-90 transition-opacity">
        {{ $headerCtaText }}
      </a>
    @endif
    @if(($showDarkModeToggle ?? false) && ($darkModeTogglePosition ?? '') === 'header')
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
<header id="brndle-header" class="relative z-50 bg-surface-primary border-b border-surface-tertiary/50" aria-label="Site header">
  {{-- Top row: logo centered --}}
  <div class="max-w-7xl mx-auto px-6 pt-6 pb-3 flex items-center justify-center">
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-9 w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-9 w-auto hidden dark:block">
      @else
        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20">
          <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
        <span class="text-lg font-bold tracking-tight text-text-primary">{!! $siteName !!}</span>
      @endif
    </a>
  </div>

  {{-- Bottom row: nav pills --}}
  <nav class="max-w-7xl mx-auto px-6 pb-4 flex items-center justify-center gap-3" aria-label="Main navigation">
    {{-- Desktop nav --}}
    @if(has_nav_menu('primary_navigation'))
      <div class="hidden md:flex items-center gap-1">
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'flex items-center gap-1',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="px-4 py-1.5 text-[13px] font-medium text-text-secondary hover:text-text-primary hover:bg-surface-secondary rounded-full transition-all">',
          'link_after' => '</span>',
        ]) !!}
      </div>
    @endif

    {{-- Dark mode toggle --}}
    @if(($showDarkModeToggle ?? false) && ($darkModeTogglePosition ?? '') === 'header')
      <div class="hidden md:block">
        @include('partials.components.dark-mode-toggle')
      </div>
    @endif

    {{-- CTA --}}
    @if(!empty($headerCtaText))
      <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
         class="hidden md:inline-flex px-5 py-1.5 text-[13px] font-semibold rounded-full bg-accent text-white hover:opacity-90 transition-opacity">
        {{ $headerCtaText }}
      </a>
    @endif

    {{-- Mobile hamburger --}}
    <button id="brndle-menu-btn" type="button"
            class="md:hidden flex items-center justify-center w-10 h-10 text-text-secondary"
            aria-expanded="false" aria-controls="brndle-mobile-menu" aria-label="Toggle menu">
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
          'link_before' => '<span class="block text-sm font-medium text-text-secondary hover:text-accent py-2.5 transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      @endif
      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block mt-4 text-center text-sm font-semibold px-5 py-2.5 rounded-lg bg-accent text-white">
          {{ $headerCtaText }}
        </a>
      @endif
      @if(($showDarkModeToggle ?? false) && ($darkModeTogglePosition ?? '') === 'header')
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
<header id="brndle-header" class="fixed top-0 inset-x-0 z-50 bg-transparent transition-all" aria-label="Site header">
  <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="Main navigation">
    {{-- Logo --}}
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto hidden dark:block">
      @else
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20">
          <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
        <span class="text-lg font-bold tracking-tight text-white">{!! $siteName !!}</span>
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
          'link_before' => '<span class="text-[13px] font-medium text-white/80 hover:text-white transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      </div>
    @endif

    {{-- Right side --}}
    <div class="hidden md:flex items-center gap-3">
      @if(($showDarkModeToggle ?? false) && ($darkModeTogglePosition ?? '') === 'header')
        @include('partials.components.dark-mode-toggle')
      @endif

      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
           class="px-5 py-2 text-[13px] font-semibold rounded-lg border border-white/30 text-white hover:bg-white/10 transition-all">
          {{ $headerCtaText }}
        </a>
      @endif
    </div>

    {{-- Mobile toggle --}}
    <button id="brndle-menu-btn" type="button"
            class="md:hidden flex items-center justify-center w-10 h-10 text-white"
            aria-expanded="false" aria-controls="brndle-mobile-menu" aria-label="Toggle menu">
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
          'link_before' => '<span class="block text-sm font-medium text-text-secondary hover:text-accent py-2.5 transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      @endif
      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block mt-4 text-center text-sm font-semibold px-5 py-2.5 rounded-lg bg-accent text-white">
          {{ $headerCtaText }}
        </a>
      @endif
      @if(($showDarkModeToggle ?? false) && ($darkModeTogglePosition ?? '') === 'header')
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
    h.classList.toggle('bg-surface-primary',window.scrollY>50);
    h.classList.toggle('backdrop-blur-xl',window.scrollY>50);
    h.classList.toggle('shadow-sm',window.scrollY>50);
    h.classList.toggle('[&_a]:text-text-primary',window.scrollY>50);
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
<header id="brndle-header" class="relative z-50 bg-surface-primary border-b-2 border-surface-tertiary" aria-label="Site header">
  <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="Main navigation">
    {{-- Logo --}}
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto hidden dark:block">
      @else
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20">
          <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
        <span class="text-lg font-bold tracking-tight text-text-primary">{!! $siteName !!}</span>
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
          'link_before' => '<span class="text-[13px] font-medium text-text-secondary hover:text-text-primary transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      </div>
    @endif

    {{-- Right side --}}
    <div class="hidden md:flex items-center gap-3">
      @if(($showDarkModeToggle ?? false) && ($darkModeTogglePosition ?? '') === 'header')
        @include('partials.components.dark-mode-toggle')
      @endif

      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
           class="px-5 py-2 text-[13px] font-semibold rounded-lg bg-accent text-white hover:opacity-90 transition-opacity">
          {{ $headerCtaText }}
        </a>
      @endif
    </div>

    {{-- Mobile toggle --}}
    <button id="brndle-menu-btn" type="button"
            class="md:hidden flex items-center justify-center w-10 h-10 text-text-secondary"
            aria-expanded="false" aria-controls="brndle-mobile-menu" aria-label="Toggle menu">
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
          'link_before' => '<span class="block text-sm font-medium text-text-secondary hover:text-accent py-2.5 transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      @endif
      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block mt-4 text-center text-sm font-semibold px-5 py-2.5 rounded-lg bg-accent text-white">
          {{ $headerCtaText }}
        </a>
      @endif
      @if(($showDarkModeToggle ?? false) && ($darkModeTogglePosition ?? '') === 'header')
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
     STYLE: STICKY (default) — Glassmorphism sticky header
     ============================================================ --}}
@else
<header id="brndle-header" class="sticky top-0 z-50 bg-surface-primary/80 backdrop-blur-xl border-b border-surface-tertiary/50 transition-all" aria-label="Site header">
  <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="Main navigation">
    {{-- Logo --}}
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0">
      @if(!empty($siteLogo))
        <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto dark:hidden">
        <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto hidden dark:block">
      @else
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20">
          <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
        </div>
        <span class="text-lg font-bold tracking-tight text-text-primary">{!! $siteName !!}</span>
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
          'link_before' => '<span class="text-[13px] font-medium text-text-secondary hover:text-text-primary transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      </div>
    @endif

    {{-- Right side --}}
    <div class="hidden md:flex items-center gap-3">
      @if(($showDarkModeToggle ?? false) && ($darkModeTogglePosition ?? '') === 'header')
        @include('partials.components.dark-mode-toggle')
      @endif

      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}"
           class="px-5 py-2 text-[13px] font-semibold rounded-lg bg-accent text-white hover:opacity-90 transition-opacity">
          {{ $headerCtaText }}
        </a>
      @endif
    </div>

    {{-- Mobile toggle --}}
    <button id="brndle-menu-btn" type="button"
            class="md:hidden flex items-center justify-center w-10 h-10 text-text-secondary"
            aria-expanded="false" aria-controls="brndle-mobile-menu" aria-label="Toggle menu">
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
          'link_before' => '<span class="block text-sm font-medium text-text-secondary hover:text-accent py-2.5 transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      @endif
      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block mt-4 text-center text-sm font-semibold px-5 py-2.5 rounded-lg bg-accent text-white">
          {{ $headerCtaText }}
        </a>
      @endif
      @if(($showDarkModeToggle ?? false) && ($darkModeTogglePosition ?? '') === 'header')
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
