@php($a = $attributes)

<section class="py-12 {{ ($a['variant'] ?? 'light') === 'dark' ? 'brndle-section-dark' : 'bg-surface-secondary border-y border-surface-tertiary' }}">
  <div class="max-w-7xl mx-auto px-6">
    @if($a['title'])
      <p class="text-[11px] font-semibold uppercase tracking-[0.2em] {{ ($a['variant'] ?? 'light') === 'dark' ? 'text-white/50' : 'text-text-tertiary' }} text-center mb-8">{{ $a['title'] }}</p>
    @endif
    <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-6">
      @foreach(($a['companies'] ?? []) as $company)
        @if(is_array($company) && isset($company['url']))
          <x-img :src="$company['url']" :alt="$company['alt'] ?? ($company['name'] ?? '')" class="h-[60px] grayscale invert opacity-60 hover:grayscale-0 hover:invert-0 hover:opacity-100 transition-all duration-300" />
        @elseif(is_string($company))
          <span class="text-lg font-bold {{ ($a['variant'] ?? 'light') === 'dark' ? 'text-white/50' : 'text-text-tertiary' }} tracking-tight">{{ $company }}</span>
        @endif
      @endforeach
    </div>
  </div>
</section>
