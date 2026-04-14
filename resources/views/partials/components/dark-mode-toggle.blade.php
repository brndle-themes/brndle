{{--
  Dark-mode toggle button (dumb partial).

  Safe to include any number of times on the same page — no IDs, no inline
  <script>. All behaviour lives in `resources/js/dark-mode.js`, which binds
  to every `[data-brndle-dark-toggle]` on the page via a single controller.

  Renders three icons (sun/moon/system); the controller shows exactly one at
  a time based on the current state. Initial `aria-label` and icon visibility
  are overwritten by JS on boot — the `data-brndle-state="__"` sentinel
  guarantees correct paint even if CSS loads before JS.

  Callers may pass `$position` explicitly (e.g. from the header). Otherwise
  it falls back to the globally-injected `$darkModeTogglePosition`.
--}}
@php
  $position = $position ?? ($darkModeTogglePosition ?? 'bottom-right');
  $positionClasses = match ($position) {
      'bottom-left'  => 'fixed bottom-6 left-6 z-50',
      'bottom-right' => 'fixed bottom-6 right-6 z-50',
      default        => '',
  };
@endphp

@if ($showDarkModeToggle ?? false)
  <button
    type="button"
    data-brndle-dark-toggle
    data-brndle-state="__"
    class="{{ $positionClasses }} w-10 h-10 rounded-full bg-surface-secondary border border-surface-tertiary shadow-md flex items-center justify-center cursor-pointer hover:border-accent transition-colors"
    aria-label="{{ __('Toggle color theme', 'brndle') }}"
  >
    {{-- Sun (shown when state = light) --}}
    <svg data-brndle-icon="light" class="w-5 h-5 text-text-secondary hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
    {{-- Moon (shown when state = dark) --}}
    <svg data-brndle-icon="dark" class="w-5 h-5 text-text-secondary hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
      <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
    </svg>
    {{-- System / half-circle (shown when state = system) --}}
    <svg data-brndle-icon="system" class="w-5 h-5 text-text-secondary hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
      <circle cx="12" cy="12" r="8.25"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5" fill="currentColor"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12H12" opacity="0"/>
      <path d="M12 3.75a8.25 8.25 0 010 16.5z" fill="currentColor"/>
    </svg>
  </button>
@endif
