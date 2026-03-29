@php
  $a = $attributes;
  $steps = $a['steps'] ?? [];
  $isDark = ($a['variant'] ?? 'light') === 'dark';
  $isVertical = ($a['layout'] ?? 'horizontal') === 'vertical';
  $stepCols = ['md:grid-cols-1', 'md:grid-cols-2', 'md:grid-cols-3', 'md:grid-cols-4'];
@endphp

<section class="py-24 md:py-32 {{ $isDark ? 'brndle-section-dark' : 'bg-surface-secondary' }}">
  <div class="max-w-7xl mx-auto px-6">
    @if($a['title'])
      <div class="max-w-3xl mx-auto text-center mb-16 reveal">
        @if($a['eyebrow'])
          <p class="text-sm font-semibold text-accent uppercase tracking-[0.15em] mb-3">{{ $a['eyebrow'] }}</p>
        @endif
        <h2 class="text-4xl sm:text-5xl font-bold tracking-tight">{!! wp_kses_post($a['title']) !!}</h2>
        @if($a['subtitle'])
          <p class="mt-4 text-lg {{ $isDark ? 'text-white/70' : 'text-text-secondary' }}">{{ $a['subtitle'] }}</p>
        @endif
      </div>
    @endif

    @if(!empty($steps))
    @if($isVertical)
      <div class="max-w-2xl mx-auto">
        @foreach($steps as $i => $step)
          <div class="relative flex gap-6 {{ $i < count($steps) - 1 ? 'pb-12' : '' }} reveal">
            @if($i < count($steps) - 1)
              <div class="absolute left-5 top-12 w-px h-[calc(100%-48px)] {{ $isDark ? 'bg-white/10' : 'bg-surface-tertiary' }}"></div>
            @endif
            <div class="relative z-10 flex-shrink-0 w-10 h-10 rounded-full bg-accent text-on-accent flex items-center justify-center text-sm font-bold">
              @if(!empty($step['icon']))
                {{ $step['icon'] }}
              @else
                {{ $i + 1 }}
              @endif
            </div>
            <div class="pt-1">
              <h3 class="text-lg font-bold">{{ $step['title'] ?? '' }}</h3>
              @if(!empty($step['description']))
                <p class="mt-2 {{ $isDark ? 'text-white/70' : 'text-text-secondary' }} leading-relaxed">{{ $step['description'] }}</p>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="grid {{ $stepCols[max(min(count($steps), 4) - 1, 0)] ?? 'md:grid-cols-3' }} gap-8">
        @foreach($steps as $i => $step)
          <div class="relative text-center reveal">
            @if($i < count($steps) - 1)
              <div class="hidden md:block absolute top-5 h-px {{ $isDark ? 'bg-white/10' : 'bg-surface-tertiary' }}" style="left: calc(50% + 28px); width: calc(100% - 56px);" aria-hidden="true"></div>
            @endif
            <div class="relative z-10 w-12 h-12 rounded-full bg-accent text-on-accent flex items-center justify-center text-lg font-bold mx-auto mb-4">
              @if(!empty($step['icon']))
                {{ $step['icon'] }}
              @else
                {{ $i + 1 }}
              @endif
            </div>
            <h3 class="text-lg font-bold mb-2">{{ $step['title'] ?? '' }}</h3>
            @if(!empty($step['description']))
              <p class="{{ $isDark ? 'text-white/70' : 'text-text-secondary' }} leading-relaxed">{{ $step['description'] }}</p>
            @endif
          </div>
        @endforeach
      </div>
    @endif
    @endif
  </div>
</section>
