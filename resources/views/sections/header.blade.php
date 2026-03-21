@php
  $style = $headerStyle ?? 'sticky';
  $headerClasses = match($style) {
    'solid' => 'relative z-50 bg-[var(--color-surface-primary)] border-b border-[var(--color-surface-tertiary)]',
    'transparent' => 'fixed top-0 inset-x-0 z-50 bg-transparent',
    default => 'sticky top-0 z-50 bg-[var(--color-surface-primary)]/80 backdrop-blur-xl border-b border-[var(--color-surface-tertiary)]/50',
  };
  $textClasses = ($style === 'transparent')
    ? 'text-white'
    : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]';
  $logoTextClass = ($style === 'transparent')
    ? 'text-white'
    : 'text-[var(--color-text-primary)]';
@endphp

<header class="{{ $headerClasses }} transition-all">
  <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
    {{-- Logo --}}
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 group shrink-0">
      @if($siteLogo)
        {{-- Light logo (hidden in dark mode if dark logo exists) --}}
        <img
          src="{{ $siteLogo }}"
          alt="{{ $siteName }}"
          class="h-8 {{ $siteLogoDark ? '[data-theme=dark]_&:hidden' : '' }}"
        >
        {{-- Dark logo (shown only in dark mode) --}}
        @if($siteLogoDark)
          <img
            src="{{ $siteLogoDark }}"
            alt="{{ $siteName }}"
            class="h-8 hidden [data-theme=dark]_&:block"
          >
        @endif
      @else
        {{-- Default Brndle SVG logo --}}
        <img
          src="{{ get_theme_file_uri('resources/images/logo-light.svg') }}"
          alt="{{ $siteName }}"
          class="h-8 dark:hidden [data-theme=dark]:hidden"
        >
        <img
          src="{{ get_theme_file_uri('resources/images/logo-dark.svg') }}"
          alt="{{ $siteName }}"
          class="h-8 hidden dark:block [data-theme=dark]:block"
        >
      @endif
    </a>

    {{-- Desktop Navigation --}}
    @if(has_nav_menu('primary_navigation'))
      <nav class="hidden md:flex items-center gap-8" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'flex items-center gap-8',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="text-[13px] font-medium ' . $textClasses . ' transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      </nav>
    @endif

    {{-- Right side actions --}}
    <div class="hidden md:flex items-center gap-4">
      {{-- Dark mode toggle (header position) --}}
      @if(($showDarkModeToggle ?? false) && ($darkModeTogglePosition ?? '') === 'header')
        @include('partials.components.dark-mode-toggle')
      @endif

      {{-- CTA Button --}}
      @if(!empty($headerCtaText))
        <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="px-5 py-2 text-sm font-semibold rounded-lg bg-[var(--color-accent)] text-white hover:opacity-90 transition-opacity">
          {{ $headerCtaText }}
        </a>
      @endif
    </div>

    {{-- Mobile menu toggle (CSS-only) --}}
    <div class="md:hidden">
      <input type="checkbox" id="brndle-mobile-menu" class="peer sr-only" aria-label="{{ __('Toggle menu', 'brndle') }}">
      <label for="brndle-mobile-menu" class="flex items-center justify-center w-10 h-10 rounded-lg cursor-pointer text-[var(--color-text-secondary)] hover:bg-[var(--color-surface-secondary)] transition-colors">
        {{-- Hamburger icon --}}
        <svg class="w-5 h-5 peer-checked:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        {{-- Close icon --}}
        <svg class="w-5 h-5 hidden peer-checked:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </label>

      {{-- Mobile menu panel --}}
      <div class="hidden peer-checked:block absolute top-16 inset-x-0 bg-[var(--color-surface-primary)] border-b border-[var(--color-surface-tertiary)] shadow-lg z-50">
        <div class="max-w-7xl mx-auto px-6 py-6 space-y-4">
          @if(has_nav_menu('primary_navigation'))
            {!! wp_nav_menu([
              'theme_location' => 'primary_navigation',
              'menu_class' => 'space-y-3',
              'container' => false,
              'echo' => false,
              'link_before' => '<span class="block text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-accent)] transition-colors py-1">',
              'link_after' => '</span>',
            ]) !!}
          @endif

          @if(!empty($headerCtaText))
            <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block w-full text-center px-5 py-2.5 text-sm font-semibold rounded-lg bg-[var(--color-accent)] text-white hover:opacity-90 transition-opacity">
              {{ $headerCtaText }}
            </a>
          @endif
        </div>
      </div>
    </div>
  </nav>
</header>
