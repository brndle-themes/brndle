@php($a = $attributes)

<section class="py-24 md:py-32 {{ ($a['variant'] ?? 'light') === 'dark' ? 'bg-surface-inverse text-white' : 'bg-surface-primary' }}">
  <div class="max-w-7xl mx-auto px-6">
    @if($a['title'])
      <div class="max-w-3xl mx-auto text-center mb-20 reveal">
        @if($a['eyebrow'])
          <p class="text-sm font-semibold text-accent uppercase tracking-[0.15em] mb-3">{{ $a['eyebrow'] }}</p>
        @endif
        <h2 class="text-4xl sm:text-5xl font-bold tracking-tight">{!! $a['title'] !!}</h2>
        @if($a['subtitle'])
          <p class="mt-4 text-lg {{ ($a['variant'] ?? 'light') === 'dark' ? 'text-text-secondary' : 'text-text-secondary' }}">{{ $a['subtitle'] }}</p>
        @endif
      </div>
    @endif

    <div class="space-y-24">
      @foreach(($a['features'] ?? []) as $i => $feature)
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center reveal {{ $i % 2 !== 0 ? 'lg:[direction:rtl] lg:[&>*]:[direction:ltr]' : '' }}">
          <div>
            @if(isset($feature['icon']))
              <div class="w-12 h-12 rounded-xl bg-accent-subtle flex items-center justify-center mb-6">
                <span class="text-accent text-2xl">{!! $feature['icon'] !!}</span>
              </div>
            @endif
            <h3 class="text-2xl sm:text-3xl font-bold tracking-tight">{{ $feature['title'] ?? '' }}</h3>
            <p class="mt-4 text-lg {{ ($a['variant'] ?? 'light') === 'dark' ? 'text-text-secondary' : 'text-text-secondary' }} leading-relaxed">{{ $feature['description'] ?? '' }}</p>

            @if(isset($feature['bullets']) && is_array($feature['bullets']))
              <ul class="mt-6 space-y-3">
                @foreach($feature['bullets'] as $bullet)
                  <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"/></svg>
                    <span class="{{ ($a['variant'] ?? 'light') === 'dark' ? 'text-text-secondary' : 'text-text-secondary' }}">{{ $bullet }}</span>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>

          @if(isset($feature['image']))
            <div class="rounded-2xl {{ ($a['variant'] ?? 'light') === 'dark' ? 'border border-white/10' : 'bg-surface-secondary border border-surface-tertiary shadow-lg' }} overflow-hidden">
              <img src="{{ esc_url($feature['image']) }}" alt="{{ esc_attr($feature['title'] ?? '') }}" class="w-full" loading="lazy" decoding="async">
            </div>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</section>
