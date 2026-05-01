@php
  $a = $attributes;
  $variant = $a['variant'] ?? 'dark';
  $isDark = $variant === 'dark';
  $isGradient = $variant === 'gradient';
  $sectionClass = $isDark
    ? 'brndle-section-dark'
    : ($isGradient ? 'brndle-section-gradient text-white' : 'bg-surface-primary text-text-primary');
  $isInverse = $isDark || $isGradient;
@endphp

<section class="relative min-h-screen flex items-center overflow-hidden {{ $sectionClass }} noise">
  {{-- Ambient --}}
  <div class="absolute inset-0 overflow-hidden">
    <div class="absolute inset-0 ambient-grid bg-[size:64px_64px]"></div>
    <div class="absolute top-[-20%] left-[-10%] w-[40rem] h-[40rem] rounded-full bg-accent/[0.12] blur-[128px]"></div>
    <div class="absolute bottom-[-15%] right-[-5%] w-[35rem] h-[35rem] rounded-full bg-accent/[0.08] blur-[128px]"></div>
  </div>

  <div class="relative z-10 w-full max-w-7xl mx-auto px-6 pt-32 pb-20">
    <div class="{{ $a['image'] ? 'grid md:grid-cols-2 gap-12 items-center' : '' }}">
      <div class="{{ $a['image'] ? '' : 'max-w-4xl' }}">
        @if($a['eyebrow'])
          <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full border border-white/[0.08] bg-white/[0.03] backdrop-blur-sm mb-8">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping motion-reduce:animate-none absolute h-full w-full rounded-full bg-accent opacity-75" aria-hidden="true"></span>
              <span class="relative rounded-full h-2 w-2 bg-accent" aria-hidden="true"></span>
            </span>
            <span class="text-sm font-medium {{ $isInverse ? 'text-white/70' : 'text-text-secondary' }}">{{ $a['eyebrow'] }}</span>
          </div>
        @endif

        <h1 class="{{ $a['image'] ? 'text-[clamp(2.5rem,5vw,4rem)]' : 'text-[clamp(3rem,7vw,5rem)]' }} font-bold leading-[1.06] tracking-[-0.03em]">
          {!! wp_kses_post($a['title']) !!}
        </h1>

        @if($a['subtitle'])
          <p class="mt-6 text-[clamp(1.05rem,1.8vw,1.25rem)] leading-relaxed {{ $isInverse ? 'text-white/70' : 'text-text-secondary' }} {{ $a['image'] ? '' : 'max-w-2xl' }}">
            {{ $a['subtitle'] }}
          </p>
        @endif

        <div class="mt-10 flex flex-wrap items-center gap-4">
          @if($a['cta_primary'])
            <a href="{{ esc_url($a['cta_primary_url']) }}" class="group inline-flex items-center gap-2 px-7 py-3.5 text-[0.925rem] font-semibold rounded-xl focus:outline-2 focus:outline-offset-2 focus:outline-accent {{ $isInverse ? 'brndle-cta-inverse bg-white hover:shadow-[0_0_40px_rgba(255,255,255,0.12)]' : 'bg-surface-inverse text-white hover:opacity-90' }} transition-all duration-300 hover:-translate-y-0.5 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
              {{ $a['cta_primary'] }}
              <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1 motion-reduce:transition-none motion-reduce:group-hover:translate-x-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
          @endif

          @if($a['cta_secondary'])
            <a href="{{ esc_url($a['cta_secondary_url']) }}" class="inline-flex items-center gap-2 px-7 py-3.5 text-[0.925rem] font-medium rounded-xl border focus:outline-2 focus:outline-offset-2 focus:outline-accent {{ $isInverse ? 'border-white/20 text-white/80 hover:bg-white/5 hover:border-white/30' : 'border-surface-tertiary text-text-secondary hover:bg-surface-secondary' }} transition-all duration-300 motion-reduce:transition-none">
              {{ $a['cta_secondary'] }}
            </a>
          @endif
        </div>
      </div>

      @if($a['image'])
        <div class="relative">
          <div class="absolute -inset-4 bg-gradient-to-r from-accent/20 via-accent/10 to-accent/20 rounded-2xl blur-2xl opacity-60"></div>
          <div class="relative rounded-2xl border {{ $isInverse ? 'border-white/[0.08] bg-white/[0.02]' : 'border-surface-tertiary' }} overflow-hidden shadow-2xl">
            <img src="{{ esc_url($a['image']) }}" alt="{{ wp_strip_all_tags($a['title']) }}" class="w-full" loading="eager" decoding="async" fetchpriority="high">
          </div>
        </div>
      @endif
    </div>

    @if(!empty($a['logos']))
      <div class="mt-20 pt-10 border-t {{ $isInverse ? 'border-white/[0.06]' : 'border-surface-tertiary' }}">
        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] {{ $isInverse ? 'text-white/50' : 'text-text-tertiary' }} mb-8">{{ __('Works with', 'brndle') }}</p>
        <div class="flex flex-wrap items-center gap-x-12 gap-y-6">
          @foreach($a['logos'] as $logo)
            @if(is_array($logo) && isset($logo['url']))
              <img src="{{ esc_url($logo['url']) }}" alt="{{ esc_attr($logo['name'] ?? '') }}" class="h-[60px] grayscale invert opacity-60 hover:grayscale-0 hover:invert-0 hover:opacity-100 transition-all duration-300 motion-reduce:transition-none" loading="lazy" decoding="async">
            @elseif(is_string($logo))
              <span class="text-lg font-bold {{ $isInverse ? 'text-white/50' : 'text-text-tertiary' }} tracking-tight">{{ $logo }}</span>
            @endif
          @endforeach
        </div>
      </div>
    @endif
  </div>
</section>
