@php($a = $attributes)

<section class="relative py-24 md:py-32 overflow-hidden text-white noise" style="background-color: {{ ($a['variant'] ?? 'dark') === 'dark' ? '#080B16' : 'var(--color-accent)' }}">
  <div class="absolute inset-0">
    <div class="absolute inset-0 bg-[linear-gradient(rgba(99,102,241,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.04)_1px,transparent_1px)] bg-[size:48px_48px]"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[40rem] h-[40rem] rounded-full bg-accent/10 blur-[128px]"></div>
  </div>

  <div class="relative z-10 max-w-3xl mx-auto px-6 text-center reveal">
    <h2 class="text-4xl sm:text-5xl font-bold tracking-tight">{!! $a['title'] !!}</h2>

    @if($a['subtitle'])
      <p class="mt-5 text-lg text-white/70 max-w-xl mx-auto">{{ $a['subtitle'] }}</p>
    @endif

    <div class="mt-10 flex flex-wrap justify-center gap-4">
      @if($a['cta_primary'])
        <a href="{{ esc_url($a['cta_primary_url']) }}" class="group inline-flex items-center gap-2 px-8 py-4 text-[0.95rem] font-semibold rounded-xl bg-white text-slate-900 transition-all duration-300 hover:shadow-[0_0_50px_rgba(255,255,255,0.12)] hover:-translate-y-0.5">
          {{ $a['cta_primary'] }}
          <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
      @endif

      @if($a['cta_secondary'])
        <a href="{{ esc_url($a['cta_secondary_url']) }}" class="inline-flex items-center gap-2 px-8 py-4 text-[0.95rem] font-medium rounded-xl border border-white/15 text-white/80 hover:bg-white/5 hover:border-white/25 transition-all duration-300">
          {{ $a['cta_secondary'] }}
        </a>
      @endif
    </div>
  </div>
</section>
