@php($a = $attributes)

<section class="py-24 md:py-32 {{ ($a['variant'] ?? 'light') === 'dark' ? 'bg-surface-inverse text-white' : 'bg-surface-secondary' }}">
  <div class="max-w-7xl mx-auto px-6">
    <div class="max-w-3xl mx-auto text-center mb-16 reveal">
      @if($a['eyebrow'])
        <p class="text-sm font-semibold text-accent uppercase tracking-[0.15em] mb-3">{{ $a['eyebrow'] }}</p>
      @endif
      @if($a['title'])
        <h2 class="text-4xl sm:text-5xl font-bold tracking-tight">{!! $a['title'] !!}</h2>
      @endif
      @if($a['subtitle'])
        <p class="mt-5 text-lg text-text-secondary">{{ $a['subtitle'] }}</p>
      @endif
    </div>

    @php($plans = $a['plans'] ?? [])
    <div class="grid md:grid-cols-{{ min(count($plans), 3) }} gap-6 max-w-5xl mx-auto">
      @foreach($plans as $plan)
        @php($featured = $plan['featured'] ?? false)
        <div class="p-8 rounded-2xl reveal transition-all duration-300 hover:shadow-lg {{ $featured ? 'bg-slate-900 text-white border-2 border-accent relative' : 'bg-white border border-slate-200 hover:border-slate-300' }}">
          @if($featured)
            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-accent text-white text-xs font-bold">{{ $plan['badge'] ?? __('Most Popular', 'brndle') }}</div>
          @endif

          <h3 class="text-lg font-bold">{{ $plan['name'] ?? '' }}</h3>
          @if(isset($plan['description']))
            <p class="text-sm {{ $featured ? 'text-slate-400' : 'text-text-secondary' }} mt-1">{{ $plan['description'] }}</p>
          @endif

          <div class="mt-6 mb-8">
            <span class="text-5xl font-bold">{{ $plan['price'] ?? '' }}</span>
            @if(isset($plan['period']))
              <span class="{{ $featured ? 'text-slate-400' : 'text-text-tertiary' }} ml-1">{{ $plan['period'] }}</span>
            @endif
          </div>

          @if(isset($plan['features']) && is_array($plan['features']))
            <ul class="space-y-3 mb-8">
              @foreach($plan['features'] as $feature)
                <li class="flex items-center gap-3 text-sm {{ $featured ? 'text-slate-300' : 'text-text-secondary' }}">
                  <svg class="w-5 h-5 {{ $featured ? 'text-emerald-400' : 'text-emerald-500' }} shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"/></svg>
                  {{ $feature }}
                </li>
              @endforeach
            </ul>
          @endif

          <a href="{{ esc_url($plan['cta_url'] ?? '#') }}" class="block w-full text-center px-6 py-3 text-sm font-semibold rounded-xl transition-all {{ $featured ? 'bg-accent hover:bg-accent-dark text-white hover:-translate-y-px hover:shadow-lg hover:shadow-accent/25' : 'border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300' }}">
            {{ $plan['cta_text'] ?? __('Get Started', 'brndle') }}
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>
