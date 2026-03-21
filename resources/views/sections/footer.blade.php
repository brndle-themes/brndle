<footer class="bg-surface-inverse border-t border-white/[0.05]">
  <div class="max-w-7xl mx-auto px-6 py-16">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
      {{-- Brand --}}
      <a href="{{ home_url('/') }}" class="flex items-center gap-2">
        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center">
          <span class="text-white text-xs font-black">B</span>
        </div>
        <span class="text-sm font-bold text-white">{!! $siteName !!}</span>
      </a>

      {{-- Copyright --}}
      <p class="text-sm text-slate-500">
        &copy; {{ date('Y') }} {!! $siteName !!}. {{ __('All rights reserved.', 'brndle') }}
      </p>

      {{-- Footer nav --}}
      @if(has_nav_menu('footer_navigation'))
        <nav aria-label="{{ wp_get_nav_menu_name('footer_navigation') }}">
          {!! wp_nav_menu([
            'theme_location' => 'footer_navigation',
            'menu_class' => 'flex items-center gap-6',
            'container' => false,
            'echo' => false,
            'link_before' => '<span class="text-sm text-slate-500 hover:text-slate-300 transition-colors">',
            'link_after' => '</span>',
          ]) !!}
        </nav>
      @endif
    </div>
  </div>
</footer>
