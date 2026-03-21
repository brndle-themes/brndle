@php($a = $attributes)

<section class="py-12 {{ ($a['variant'] ?? 'light') === 'dark' ? 'bg-surface-inverse' : 'bg-surface-secondary border-y border-surface-tertiary' }}">
  <div class="max-w-7xl mx-auto px-6">
    @if($a['title'])
      <p class="text-[11px] font-semibold uppercase tracking-[0.2em] {{ ($a['variant'] ?? 'light') === 'dark' ? 'text-text-tertiary' : 'text-text-tertiary' }} text-center mb-8">{{ $a['title'] }}</p>
    @endif
    <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-6">
      @foreach(($a['companies'] ?? []) as $company)
        @if(is_array($company) && isset($company['url']))
          <img src="{{ esc_url($company['url']) }}" alt="{{ esc_attr($company['name'] ?? '') }}" class="h-7 opacity-40 grayscale hover:opacity-70 hover:grayscale-0 transition-all duration-300" loading="lazy" decoding="async">
        @elseif(is_string($company))
          <span class="text-lg font-bold {{ ($a['variant'] ?? 'light') === 'dark' ? 'text-text-tertiary' : 'text-text-tertiary' }} tracking-tight">{{ $company }}</span>
        @endif
      @endforeach
    </div>
  </div>
</section>
