@php
  $position = $darkModeTogglePosition ?? 'bottom-right';
  $isInline = ($position === 'header');
  $positionClasses = match($position) {
    'bottom-left' => 'fixed bottom-6 left-6 z-50',
    'header' => '',
    default => 'fixed bottom-6 right-6 z-50',
  };
@endphp

<button
  type="button"
  id="brndle-dark-toggle"
  class="{{ $positionClasses }} w-10 h-10 rounded-full bg-surface-secondary border border-surface-tertiary shadow-md flex items-center justify-center cursor-pointer hover:border-accent transition-colors"
  role="switch"
  aria-pressed="false"
  aria-label="{{ __('Toggle dark mode', 'brndle') }}"
>
  {{-- Sun icon (shown in dark mode, click to go light) --}}
  <svg id="brndle-icon-sun" class="w-5 h-5 text-text-secondary hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
  </svg>
  {{-- Moon icon (shown in light mode, click to go dark) --}}
  <svg id="brndle-icon-moon" class="w-5 h-5 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
  </svg>
</button>

<script>
(function(){
  var btn=document.getElementById('brndle-dark-toggle');
  var sun=document.getElementById('brndle-icon-sun');
  var moon=document.getElementById('brndle-icon-moon');
  if(!btn||!sun||!moon)return;
  function update(){
    var isDark=document.documentElement.getAttribute('data-theme')==='dark';
    btn.setAttribute('aria-pressed',isDark?'true':'false');
    sun.classList.toggle('hidden',!isDark);
    moon.classList.toggle('hidden',isDark);
  }
  update();
  btn.addEventListener('click',function(){
    var isDark=document.documentElement.getAttribute('data-theme')==='dark';
    var next=isDark?'light':'dark';
    document.documentElement.setAttribute('data-theme',next);
    localStorage.setItem('brndle-theme',next);
    update();
  });
})();
</script>
