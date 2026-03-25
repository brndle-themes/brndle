@php
  $a = $attributes;
  $members = $a['members'] ?? [];
  $isDark = ($a['variant'] ?? 'light') === 'dark';
  $cols = $a['columns'] ?? '3';
  $gridClass = ['2' => 'sm:grid-cols-2', '3' => 'sm:grid-cols-2 lg:grid-cols-3', '4' => 'sm:grid-cols-2 lg:grid-cols-4'][$cols] ?? 'sm:grid-cols-2 lg:grid-cols-3';
  $avatarColors = ['from-indigo-400 to-purple-500', 'from-emerald-400 to-cyan-500', 'from-amber-400 to-orange-500', 'from-pink-400 to-rose-500', 'from-blue-400 to-indigo-500', 'from-teal-400 to-emerald-500'];
@endphp

<section class="py-24 md:py-32 {{ $isDark ? 'brndle-section-dark' : 'bg-surface-primary' }}">
  <div class="max-w-7xl mx-auto px-6">
    @if($a['title'])
      <div class="max-w-2xl mx-auto text-center mb-16 reveal">
        @if($a['eyebrow'])
          <p class="text-sm font-semibold text-accent uppercase tracking-[0.15em] mb-3">{{ $a['eyebrow'] }}</p>
        @endif
        <h2 class="text-4xl sm:text-5xl font-bold tracking-tight">{!! wp_kses_post($a['title']) !!}</h2>
        @if($a['subtitle'])
          <p class="mt-4 text-lg {{ $isDark ? 'text-white/70' : 'text-text-secondary' }}">{{ $a['subtitle'] }}</p>
        @endif
      </div>
    @endif

    @if(!empty($members))
    <div class="grid grid-cols-1 {{ $gridClass }} gap-8">
      @foreach($members as $i => $member)
        <div class="reveal group">
          <div class="aspect-square rounded-2xl overflow-hidden mb-5 {{ $isDark ? 'bg-white/[0.05]' : 'bg-surface-secondary' }}">
            @if(!empty($member['photo']))
              <img src="{{ esc_url($member['photo']) }}" alt="{{ esc_attr($member['name'] ?? '') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
            @else
              <div class="w-full h-full bg-gradient-to-br {{ $avatarColors[$i % count($avatarColors)] }} flex items-center justify-center text-white text-4xl font-bold">
                {{ strtoupper(substr($member['name'] ?? 'T', 0, 1)) }}
              </div>
            @endif
          </div>
          <h3 class="text-lg font-bold">{{ $member['name'] ?? '' }}</h3>
          @if(!empty($member['role']))
            <p class="text-sm text-accent font-medium mt-0.5">{{ $member['role'] }}</p>
          @endif
          @if(!empty($member['bio']))
            <p class="mt-3 text-sm {{ $isDark ? 'text-white/60' : 'text-text-secondary' }} leading-relaxed">{{ $member['bio'] }}</p>
          @endif
          @if(!empty($member['linkedin']) || !empty($member['twitter']))
            <div class="mt-4 flex items-center gap-3">
              @if(!empty($member['linkedin']))
                <a href="{{ esc_url($member['linkedin']) }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn profile of {{ esc_attr($member['name'] ?? '') }}" class="{{ $isDark ? 'text-white/40 hover:text-white/80' : 'text-text-tertiary hover:text-text-primary' }} transition-colors">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </a>
              @endif
              @if(!empty($member['twitter']))
                <a href="{{ esc_url($member['twitter']) }}" target="_blank" rel="noopener noreferrer" aria-label="X/Twitter profile of {{ esc_attr($member['name'] ?? '') }}" class="{{ $isDark ? 'text-white/40 hover:text-white/80' : 'text-text-tertiary hover:text-text-primary' }} transition-colors">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.259 5.625zM17.05 20.1h1.833L6.992 4.125H5.029z"/></svg>
                </a>
              @endif
            </div>
          @endif
        </div>
      @endforeach
    </div>
    @endif
  </div>
</section>
