@php
  $a = $attributes;
  $isDark = ($a['variant'] ?? 'dark') === 'dark';
@endphp

<section class="relative min-h-screen flex items-center overflow-hidden {{ $isDark ? 'bg-[#080B16] text-white' : 'bg-white text-slate-900' }} noise">
  {{-- Ambient --}}
  <div class="absolute inset-0 overflow-hidden">
    <div class="absolute inset-0 bg-[linear-gradient(rgba(99,102,241,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.03)_1px,transparent_1px)] bg-[size:64px_64px]"></div>
    <div class="absolute top-[-20%] left-[-10%] w-[40rem] h-[40rem] rounded-full bg-indigo-600/[0.12] blur-[128px]"></div>
    <div class="absolute bottom-[-15%] right-[-5%] w-[35rem] h-[35rem] rounded-full bg-purple-600/[0.08] blur-[128px]"></div>
  </div>

  <div class="relative z-10 w-full max-w-7xl mx-auto px-6 pt-32 pb-20">
    <div class="max-w-4xl">
      @if($a['eyebrow'])
        <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full border border-white/[0.08] bg-white/[0.03] backdrop-blur-sm mb-8">
          <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative rounded-full h-2 w-2 bg-emerald-400"></span>
          </span>
          <span class="text-sm font-medium {{ $isDark ? 'text-slate-400' : 'text-slate-600' }}">{{ $a['eyebrow'] }}</span>
        </div>
      @endif

      <h1 class="text-[clamp(2.5rem,6vw,5rem)] font-bold leading-[1.06] tracking-[-0.03em]">
        {!! $a['title'] !!}
      </h1>

      @if($a['subtitle'])
        <p class="mt-6 text-[clamp(1.05rem,1.8vw,1.25rem)] leading-relaxed {{ $isDark ? 'text-slate-400' : 'text-slate-600' }} max-w-2xl">
          {{ $a['subtitle'] }}
        </p>
      @endif

      <div class="mt-10 flex flex-wrap items-center gap-4">
        @if($a['cta_primary'])
          <a href="{{ esc_url($a['cta_primary_url']) }}" class="group inline-flex items-center gap-2 px-7 py-3.5 text-[0.925rem] font-semibold rounded-xl {{ $isDark ? 'bg-white text-slate-900 hover:shadow-[0_0_40px_rgba(255,255,255,0.12)]' : 'bg-slate-900 text-white hover:bg-slate-800' }} transition-all duration-300 hover:-translate-y-0.5">
            {{ $a['cta_primary'] }}
            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </a>
        @endif

        @if($a['cta_secondary'])
          <a href="{{ esc_url($a['cta_secondary_url']) }}" class="inline-flex items-center gap-2 px-7 py-3.5 text-[0.925rem] font-medium rounded-xl border {{ $isDark ? 'border-white/[0.1] text-slate-300 hover:bg-white/[0.04]' : 'border-slate-200 text-slate-700 hover:bg-slate-50' }} transition-all duration-300">
            {{ $a['cta_secondary'] }}
          </a>
        @endif
      </div>
    </div>

    @if($a['image'])
      <div class="mt-20 relative">
        <div class="absolute -inset-4 bg-gradient-to-r from-indigo-500/20 via-purple-500/15 to-cyan-500/20 rounded-2xl blur-2xl opacity-60"></div>
        <div class="relative rounded-2xl border {{ $isDark ? 'border-white/[0.08] bg-white/[0.02]' : 'border-slate-200' }} overflow-hidden shadow-2xl">
          <img src="{{ esc_url($a['image']) }}" alt="{{ wp_strip_all_tags($a['title']) }}" class="w-full" loading="eager" decoding="async" fetchpriority="high">
        </div>
      </div>
    @endif

    @if(!empty($a['logos']))
      <div class="mt-20 pt-10 border-t {{ $isDark ? 'border-white/[0.06]' : 'border-slate-200' }}">
        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] {{ $isDark ? 'text-slate-500' : 'text-slate-400' }} mb-8">{{ __('Trusted by industry leaders', 'brndle') }}</p>
        <div class="flex flex-wrap items-center gap-x-12 gap-y-6">
          @foreach($a['logos'] as $logo)
            @if(is_array($logo) && isset($logo['url']))
              <img src="{{ esc_url($logo['url']) }}" alt="{{ esc_attr($logo['name'] ?? '') }}" class="h-7 opacity-40 grayscale hover:opacity-70 hover:grayscale-0 transition-all duration-300" loading="lazy" decoding="async">
            @elseif(is_string($logo))
              <span class="text-lg font-bold {{ $isDark ? 'text-slate-600' : 'text-slate-400' }} tracking-tight">{{ $logo }}</span>
            @endif
          @endforeach
        </div>
      </div>
    @endif
  </div>
</section>
