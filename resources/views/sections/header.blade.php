<header class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-200/50 transition-all">
  <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
    {{-- Logo --}}
    <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 group">
      @if($siteLogo)
        <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-8">
      @else
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20">
          <span class="text-white text-sm font-black">B</span>
        </div>
        <span class="text-lg font-bold tracking-tight text-slate-900">{!! $siteName !!}</span>
      @endif
    </a>

    {{-- Navigation --}}
    @if(has_nav_menu('primary_navigation'))
      <nav class="hidden md:block" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'flex items-center gap-8',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="text-[13px] font-medium text-slate-600 hover:text-slate-900 transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      </nav>
    @endif
  </nav>
</header>
