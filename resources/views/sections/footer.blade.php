@php
  $style = $footerStyle ?? 'dark';
  $links = is_callable($socialLinks) ? $socialLinks() : (is_array($socialLinks) ? $socialLinks : []);
@endphp

{{-- ============================================================
     STYLE: MINIMAL — Thin top border, centered copyright only
     ============================================================ --}}
@if($style === 'minimal')
<footer class="border-t border-surface-tertiary/50" aria-label="Site footer">
  <div class="max-w-7xl mx-auto px-6 py-8">
    <p class="text-sm text-text-tertiary text-center">
      {!! $footerCopyright !!}
    </p>
  </div>
</footer>

{{-- ============================================================
     STYLE: COLUMNS — Multi-column with menus
     ============================================================ --}}
@elseif($style === 'columns')
<footer class="bg-[#080B16] text-white" aria-label="Site footer">
  <div class="max-w-7xl mx-auto px-6 pt-16 pb-8">
    {{-- Columns grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-white/10">
      {{-- Column 1: Brand + tagline --}}
      <div>
        <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 mb-4">
          @if(!empty($siteLogo))
            <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto">
          @else
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center">
              <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
            </div>
            <span class="text-base font-bold text-white">{!! $siteName !!}</span>
          @endif
        </a>
        <p class="text-sm text-white/60 leading-relaxed">{{ get_bloginfo('description', 'display') }}</p>
      </div>

      {{-- Column 2: Footer Col 1 --}}
      @if(has_nav_menu('footer_col_1'))
        <div>
          @php($col1_obj = wp_get_nav_menu_object(get_nav_menu_locations()['footer_col_1'] ?? 0))
          @if($col1_obj)
            <h4 class="text-sm font-semibold text-white mb-4">{{ $col1_obj->name }}</h4>
          @endif
          {!! wp_nav_menu([
            'theme_location' => 'footer_col_1',
            'menu_class' => 'space-y-3',
            'container' => false,
            'echo' => false,
            'link_before' => '<span class="text-sm text-white/60 hover:text-white transition-colors">',
            'link_after' => '</span>',
          ]) !!}
        </div>
      @endif

      {{-- Column 3: Footer Col 2 --}}
      @if(has_nav_menu('footer_col_2'))
        <div>
          @php($col2_obj = wp_get_nav_menu_object(get_nav_menu_locations()['footer_col_2'] ?? 0))
          @if($col2_obj)
            <h4 class="text-sm font-semibold text-white mb-4">{{ $col2_obj->name }}</h4>
          @endif
          {!! wp_nav_menu([
            'theme_location' => 'footer_col_2',
            'menu_class' => 'space-y-3',
            'container' => false,
            'echo' => false,
            'link_before' => '<span class="text-sm text-white/60 hover:text-white transition-colors">',
            'link_after' => '</span>',
          ]) !!}
        </div>
      @endif

      {{-- Column 4: Footer Col 3 --}}
      @if(has_nav_menu('footer_col_3'))
        <div>
          @php($col3_obj = wp_get_nav_menu_object(get_nav_menu_locations()['footer_col_3'] ?? 0))
          @if($col3_obj)
            <h4 class="text-sm font-semibold text-white mb-4">{{ $col3_obj->name }}</h4>
          @endif
          {!! wp_nav_menu([
            'theme_location' => 'footer_col_3',
            'menu_class' => 'space-y-3',
            'container' => false,
            'echo' => false,
            'link_before' => '<span class="text-sm text-white/60 hover:text-white transition-colors">',
            'link_after' => '</span>',
          ]) !!}
        </div>
      @endif
    </div>

    {{-- Bottom bar --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-8">
      <p class="text-sm text-white/60">
        {!! $footerCopyright !!}
      </p>

      @if($footerShowSocial && !empty($links))
        <div class="flex items-center gap-5">
          @if(!empty($links['twitter']))
            <a href="{{ esc_url($links['twitter']) }}" target="_blank" rel="noopener noreferrer"
               class="text-white/60 hover:text-white transition-colors" aria-label="X / Twitter">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
          @endif
          @if(!empty($links['github']))
            <a href="{{ esc_url($links['github']) }}" target="_blank" rel="noopener noreferrer"
               class="text-white/60 hover:text-white transition-colors" aria-label="GitHub">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
            </a>
          @endif
          @if(!empty($links['linkedin']))
            <a href="{{ esc_url($links['linkedin']) }}" target="_blank" rel="noopener noreferrer"
               class="text-white/60 hover:text-white transition-colors" aria-label="LinkedIn">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            </a>
          @endif
          @if(!empty($links['instagram']))
            <a href="{{ esc_url($links['instagram']) }}" target="_blank" rel="noopener noreferrer"
               class="text-white/60 hover:text-white transition-colors" aria-label="Instagram">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
            </a>
          @endif
        </div>
      @endif
    </div>
  </div>
</footer>

{{-- ============================================================
     STYLE: LIGHT — Light bg, dark text
     ============================================================ --}}
@elseif($style === 'light')
<footer class="bg-surface-secondary text-text-primary" aria-label="Site footer">
  <div class="max-w-7xl mx-auto px-6 py-12">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
      {{-- Brand --}}
      <a href="{{ home_url('/') }}" class="flex items-center gap-2.5">
        @if(!empty($siteLogo))
          <img src="{{ esc_url($siteLogo) }}" alt="{{ $siteName }}" class="h-7 w-auto dark:hidden">
          <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-7 w-auto hidden dark:block">
        @else
          <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center">
            <span class="text-white text-xs font-black">{{ mb_substr($siteName, 0, 1) }}</span>
          </div>
          <span class="text-sm font-bold">{!! $siteName !!}</span>
        @endif
      </a>

      {{-- Copyright --}}
      <p class="text-sm text-text-tertiary">
        {!! $footerCopyright !!}
      </p>

      {{-- Social icons --}}
      @if($footerShowSocial && !empty($links))
        <div class="flex items-center gap-5">
          @if(!empty($links['twitter']))
            <a href="{{ esc_url($links['twitter']) }}" target="_blank" rel="noopener noreferrer"
               class="text-text-tertiary hover:text-text-primary transition-colors" aria-label="X / Twitter">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
          @endif
          @if(!empty($links['github']))
            <a href="{{ esc_url($links['github']) }}" target="_blank" rel="noopener noreferrer"
               class="text-text-tertiary hover:text-text-primary transition-colors" aria-label="GitHub">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
            </a>
          @endif
          @if(!empty($links['linkedin']))
            <a href="{{ esc_url($links['linkedin']) }}" target="_blank" rel="noopener noreferrer"
               class="text-text-tertiary hover:text-text-primary transition-colors" aria-label="LinkedIn">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            </a>
          @endif
          @if(!empty($links['instagram']))
            <a href="{{ esc_url($links['instagram']) }}" target="_blank" rel="noopener noreferrer"
               class="text-text-tertiary hover:text-text-primary transition-colors" aria-label="Instagram">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
            </a>
          @endif
        </div>
      @endif
    </div>
  </div>
</footer>

{{-- ============================================================
     STYLE: BIG — Enterprise mega footer (newsletter + columns)
     ============================================================ --}}
@elseif($style === 'big')
<footer class="text-white" aria-label="Site footer">
  {{-- Newsletter section --}}
  <div class="bg-accent">
    <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col sm:flex-row items-center justify-between gap-6">
      <div class="text-center sm:text-left">
        <h3 class="text-lg font-bold text-white">Stay up to date</h3>
        <p class="text-sm text-white/70 mt-1">Get the latest news and updates delivered to your inbox.</p>
      </div>
      <form class="flex w-full sm:w-auto gap-2" onsubmit="return false;" aria-label="Newsletter signup">
        <label for="brndle-footer-email" class="sr-only">Email address</label>
        <input id="brndle-footer-email" type="email" placeholder="Enter your email"
               class="flex-1 sm:w-72 px-4 py-2.5 text-sm rounded-lg bg-white/20 text-white placeholder-white/50 border border-white/20 focus:outline-none focus:ring-2 focus:ring-white/40">
        <button type="submit" class="px-6 py-2.5 text-sm font-semibold rounded-lg bg-white text-accent hover:bg-white/90 transition-colors">
          Subscribe
        </button>
      </form>
    </div>
  </div>

  {{-- Main columns --}}
  <div class="bg-[#080B16]">
    <div class="max-w-7xl mx-auto px-6 pt-16 pb-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-white/10">
        {{-- Column 1: Brand + tagline --}}
        <div>
          <a href="{{ home_url('/') }}" class="flex items-center gap-2.5 mb-4">
            @if(!empty($siteLogo))
              <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto">
            @else
              <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center">
                <span class="text-white text-sm font-black">{{ mb_substr($siteName, 0, 1) }}</span>
              </div>
              <span class="text-base font-bold text-white">{!! $siteName !!}</span>
            @endif
          </a>
          <p class="text-sm text-white/60 leading-relaxed">{{ get_bloginfo('description', 'display') }}</p>
        </div>

        {{-- Column 2: Footer Col 1 --}}
        @if(has_nav_menu('footer_col_1'))
          <div>
            @php($col1_obj = wp_get_nav_menu_object(get_nav_menu_locations()['footer_col_1'] ?? 0))
            @if($col1_obj)
              <h4 class="text-sm font-semibold text-white mb-4">{{ $col1_obj->name }}</h4>
            @endif
            {!! wp_nav_menu([
              'theme_location' => 'footer_col_1',
              'menu_class' => 'space-y-3',
              'container' => false,
              'echo' => false,
              'link_before' => '<span class="text-sm text-white/60 hover:text-white transition-colors">',
              'link_after' => '</span>',
            ]) !!}
          </div>
        @endif

        {{-- Column 3: Footer Col 2 --}}
        @if(has_nav_menu('footer_col_2'))
          <div>
            @php($col2_obj = wp_get_nav_menu_object(get_nav_menu_locations()['footer_col_2'] ?? 0))
            @if($col2_obj)
              <h4 class="text-sm font-semibold text-white mb-4">{{ $col2_obj->name }}</h4>
            @endif
            {!! wp_nav_menu([
              'theme_location' => 'footer_col_2',
              'menu_class' => 'space-y-3',
              'container' => false,
              'echo' => false,
              'link_before' => '<span class="text-sm text-white/60 hover:text-white transition-colors">',
              'link_after' => '</span>',
            ]) !!}
          </div>
        @endif

        {{-- Column 4: Footer Col 3 --}}
        @if(has_nav_menu('footer_col_3'))
          <div>
            @php($col3_obj = wp_get_nav_menu_object(get_nav_menu_locations()['footer_col_3'] ?? 0))
            @if($col3_obj)
              <h4 class="text-sm font-semibold text-white mb-4">{{ $col3_obj->name }}</h4>
            @endif
            {!! wp_nav_menu([
              'theme_location' => 'footer_col_3',
              'menu_class' => 'space-y-3',
              'container' => false,
              'echo' => false,
              'link_before' => '<span class="text-sm text-white/60 hover:text-white transition-colors">',
              'link_after' => '</span>',
            ]) !!}
          </div>
        @endif
      </div>

      {{-- Bottom bar --}}
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-8">
        <p class="text-sm text-white/60">
          {!! $footerCopyright !!}
        </p>

        <div class="flex items-center gap-6">
          @if($footerShowSocial && !empty($links))
            <div class="flex items-center gap-5">
              @if(!empty($links['twitter']))
                <a href="{{ esc_url($links['twitter']) }}" target="_blank" rel="noopener noreferrer"
                   class="text-white/60 hover:text-white transition-colors" aria-label="X / Twitter">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
              @endif
              @if(!empty($links['github']))
                <a href="{{ esc_url($links['github']) }}" target="_blank" rel="noopener noreferrer"
                   class="text-white/60 hover:text-white transition-colors" aria-label="GitHub">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                </a>
              @endif
              @if(!empty($links['linkedin']))
                <a href="{{ esc_url($links['linkedin']) }}" target="_blank" rel="noopener noreferrer"
                   class="text-white/60 hover:text-white transition-colors" aria-label="LinkedIn">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </a>
              @endif
              @if(!empty($links['instagram']))
                <a href="{{ esc_url($links['instagram']) }}" target="_blank" rel="noopener noreferrer"
                   class="text-white/60 hover:text-white transition-colors" aria-label="Instagram">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
              @endif
            </div>
          @endif

          <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;"
             class="text-sm text-white/60 hover:text-white transition-colors flex items-center gap-1" aria-label="Back to top">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
            </svg>
            Top
          </a>
        </div>
      </div>
    </div>
  </div>
</footer>

{{-- ============================================================
     STYLE: STACKED — Centered stacked footer
     ============================================================ --}}
@elseif($style === 'stacked')
<footer class="bg-surface-primary border-t border-surface-tertiary/50" aria-label="Site footer">
  <div class="max-w-7xl mx-auto px-6 py-16 flex flex-col items-center text-center gap-6">
    {{-- Logo --}}
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

    {{-- Site description --}}
    @php($siteDesc = get_bloginfo('description', 'display'))
    @if(!empty($siteDesc))
      <p class="text-sm text-text-tertiary max-w-md leading-relaxed">{{ $siteDesc }}</p>
    @endif

    {{-- Navigation links --}}
    @if(has_nav_menu('footer_navigation'))
      <nav aria-label="Footer navigation">
        {!! wp_nav_menu([
          'theme_location' => 'footer_navigation',
          'menu_class' => 'flex flex-wrap items-center justify-center gap-x-6 gap-y-2',
          'container' => false,
          'echo' => false,
          'link_before' => '<span class="text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">',
          'link_after' => '</span>',
        ]) !!}
      </nav>
    @endif

    {{-- Social icons --}}
    @if($footerShowSocial && !empty($links))
      <div class="flex items-center gap-5">
        @if(!empty($links['twitter']))
          <a href="{{ esc_url($links['twitter']) }}" target="_blank" rel="noopener noreferrer"
             class="text-text-tertiary hover:text-text-primary transition-colors" aria-label="X / Twitter">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          </a>
        @endif
        @if(!empty($links['github']))
          <a href="{{ esc_url($links['github']) }}" target="_blank" rel="noopener noreferrer"
             class="text-text-tertiary hover:text-text-primary transition-colors" aria-label="GitHub">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
          </a>
        @endif
        @if(!empty($links['linkedin']))
          <a href="{{ esc_url($links['linkedin']) }}" target="_blank" rel="noopener noreferrer"
             class="text-text-tertiary hover:text-text-primary transition-colors" aria-label="LinkedIn">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
        @endif
        @if(!empty($links['instagram']))
          <a href="{{ esc_url($links['instagram']) }}" target="_blank" rel="noopener noreferrer"
             class="text-text-tertiary hover:text-text-primary transition-colors" aria-label="Instagram">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
          </a>
        @endif
      </div>
    @endif

    {{-- Copyright --}}
    <p class="text-sm text-text-tertiary">
      {!! $footerCopyright !!}
    </p>
  </div>
</footer>

{{-- ============================================================
     STYLE: DARK (default) — Dark bg, white text, single row
     ============================================================ --}}
@else
<footer class="bg-[#080B16] text-white" aria-label="Site footer">
  <div class="max-w-7xl mx-auto px-6 py-12">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
      {{-- Brand --}}
      <a href="{{ home_url('/') }}" class="flex items-center gap-2.5">
        @if(!empty($siteLogo))
          <img src="{{ esc_url($siteLogoDark ?: $siteLogo) }}" alt="{{ $siteName }}" class="h-7 w-auto">
        @else
          <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 flex items-center justify-center">
            <span class="text-white text-xs font-black">{{ mb_substr($siteName, 0, 1) }}</span>
          </div>
          <span class="text-sm font-bold text-white">{!! $siteName !!}</span>
        @endif
      </a>

      {{-- Copyright --}}
      <p class="text-sm text-white/60">
        {!! $footerCopyright !!}
      </p>

      {{-- Social icons --}}
      @if($footerShowSocial && !empty($links))
        <div class="flex items-center gap-5">
          @if(!empty($links['twitter']))
            <a href="{{ esc_url($links['twitter']) }}" target="_blank" rel="noopener noreferrer"
               class="text-white/60 hover:text-white transition-colors" aria-label="X / Twitter">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
          @endif
          @if(!empty($links['github']))
            <a href="{{ esc_url($links['github']) }}" target="_blank" rel="noopener noreferrer"
               class="text-white/60 hover:text-white transition-colors" aria-label="GitHub">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
            </a>
          @endif
          @if(!empty($links['linkedin']))
            <a href="{{ esc_url($links['linkedin']) }}" target="_blank" rel="noopener noreferrer"
               class="text-white/60 hover:text-white transition-colors" aria-label="LinkedIn">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            </a>
          @endif
          @if(!empty($links['instagram']))
            <a href="{{ esc_url($links['instagram']) }}" target="_blank" rel="noopener noreferrer"
               class="text-white/60 hover:text-white transition-colors" aria-label="Instagram">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
            </a>
          @endif
        </div>
      @endif
    </div>
  </div>
</footer>
@endif
