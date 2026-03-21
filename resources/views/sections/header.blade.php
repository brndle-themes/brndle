@php
  $style = $headerStyle ?? 'sticky';
  $headerClasses = match($style) {
    'solid' => 'relative z-50 bg-[var(--color-surface-primary)] border-b border-[var(--color-surface-tertiary)]',
    'transparent' => 'fixed top-0 inset-x-0 z-50 bg-transparent',
    default => 'sticky top-0 z-50 bg-[var(--color-surface-primary)]/80 backdrop-blur-xl border-b border-[var(--color-surface-tertiary)]/50',
  };
@endphp

<header class="{{ $headerClasses }} transition-all">
  <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
    {{-- Logo --}}
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 shrink-0">
      <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20">
        <span class="text-white text-sm font-black">B</span>
      </div>
      <span class="text-lg font-bold tracking-tight text-[var(--color-text-primary)]">{!! $siteName !!}</span>
    </a>

    {{-- Desktop Navigation --}}
    @if(has_nav_menu('primary_navigation'))
      <div class="hidden md:flex items-center gap-8">
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'flex items-center gap-8',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="text-[13px] font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition-colors">',
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
           class="px-5 py-2 text-[13px] font-semibold rounded-lg bg-[var(--color-accent)] text-white hover:opacity-90 transition-opacity">
          {{ $headerCtaText }}
        </a>
      @endif
    </div>

    {{-- Mobile toggle --}}
    <label for="brndle-mobile-menu" class="md:hidden flex items-center justify-center w-10 h-10 cursor-pointer text-[var(--color-text-secondary)]">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
      </svg>
    </label>
    <input type="checkbox" id="brndle-mobile-menu" class="peer sr-only">

    {{-- Mobile menu --}}
    <div class="hidden peer-checked:block fixed inset-x-0 top-16 bg-[var(--color-surface-primary)] border-b border-[var(--color-surface-tertiary)] shadow-xl z-50 md:hidden">
      <div class="max-w-7xl mx-auto px-6 py-6 space-y-1">
        @if(has_nav_menu('primary_navigation'))
          {!! wp_nav_menu([
            'theme_location' => 'primary_navigation',
            'menu_class' => 'space-y-1',
            'container' => false,
            'echo' => false,
            'link_before' => '<span class="block text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-accent)] py-2.5 transition-colors">',
            'link_after' => '</span>',
          ]) !!}
        @endif
        @if(!empty($headerCtaText))
          <a href="{{ esc_url($headerCtaUrl ?? '#') }}" class="block mt-4 text-center text-sm font-semibold px-5 py-2.5 rounded-lg bg-[var(--color-accent)] text-white">
            {{ $headerCtaText }}
          </a>
        @endif
      </div>
    </div>
  </nav>
</header>
