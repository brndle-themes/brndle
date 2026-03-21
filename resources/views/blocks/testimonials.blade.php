@php($a = $attributes)

<section class="py-24 md:py-32 bg-[var(--color-surface-primary)]">
  <div class="max-w-7xl mx-auto px-6">
    @if($a['title'])
      <div class="max-w-3xl mx-auto text-center mb-16 reveal">
        @if($a['eyebrow'])
          <p class="text-sm font-semibold text-[var(--color-accent)] uppercase tracking-[0.15em] mb-3">{{ $a['eyebrow'] }}</p>
        @endif
        <h2 class="text-4xl sm:text-5xl font-bold tracking-tight text-[var(--color-text-primary)]">{!! $a['title'] !!}</h2>
      </div>
    @endif

    <div class="grid md:grid-cols-{{ min(count($a['items'] ?? []), 3) }} gap-6">
      @foreach(($a['items'] ?? []) as $item)
        <div class="p-8 rounded-2xl bg-[var(--color-surface-secondary)] border border-[var(--color-surface-tertiary)] hover:border-[var(--color-text-tertiary)] hover:shadow-lg transition-all duration-300 reveal">
          {{-- Stars --}}
          <div class="flex items-center gap-1 mb-4">
            @for($i = 0; $i < ($item['stars'] ?? 5); $i++)
              <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            @endfor
          </div>

          <p class="text-[var(--color-text-secondary)] leading-relaxed mb-6">"{{ $item['quote'] ?? '' }}"</p>

          <div class="flex items-center gap-3">
            @if(isset($item['avatar']))
              <img src="{{ esc_url($item['avatar']) }}" alt="{{ esc_attr($item['name'] ?? '') }}" class="w-10 h-10 rounded-full" loading="lazy" decoding="async">
            @else
              @php($colors = ['from-indigo-400 to-purple-500', 'from-emerald-400 to-cyan-500', 'from-amber-400 to-orange-500', 'from-pink-400 to-rose-500'])
              <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $colors[$loop->index % count($colors)] }} flex items-center justify-center text-white text-sm font-bold">
                {{ strtoupper(substr($item['name'] ?? 'A', 0, 1)) }}
              </div>
            @endif
            <div>
              <div class="text-sm font-semibold text-[var(--color-text-primary)]">{{ $item['name'] ?? '' }}</div>
              <div class="text-xs text-[var(--color-text-tertiary)]">{{ $item['role'] ?? '' }}</div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
