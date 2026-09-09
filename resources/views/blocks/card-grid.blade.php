{{--
  Card Grid (2.3.0)

  For short, parallel items - service lanes, capability tiles, audience splits.
  The Features block is a SPOTLIGHT: each item takes a full-width alternating
  row and needs its own image. Four short items rendered through it produce a
  very tall page of near-empty cards, which is what this block exists to avoid.

  `featureFirst` promotes item one across the full row, so the grid has a
  focal point instead of reading as N identical tiles.
--}}
@php
  $a = $attributes;
  $isDark = ($a['variant'] ?? 'light') === 'dark';

  $items = array_values(array_filter(
    $a['items'] ?? [],
    fn($item) => is_array($item) && ! empty($item['title'])
  ));

  $columns = (int) ($a['columns'] ?? 2);
  $columns = in_array($columns, [2, 3], true) ? $columns : 2;
  $colClass = $columns === 3 ? 'md:grid-cols-3' : 'md:grid-cols-2';
  $featureFirst = ! empty($a['featureFirst']) && count($items) > 1;
@endphp

@if (! empty($items))
  <section class="py-20 md:py-28 {{ $isDark ? 'brndle-section-dark bg-surface-inverse text-white' : 'bg-surface-primary' }}">
    <div class="max-w-7xl mx-auto px-6">

      @if (! empty($a['eyebrow']) || ! empty($a['title']) || ! empty($a['subtitle']))
        <div class="max-w-3xl mb-12 md:mb-16">
          @if (! empty($a['eyebrow']))
            <p class="text-sm font-medium tracking-wide text-accent mb-3">{{ $a['eyebrow'] }}</p>
          @endif
          @if (! empty($a['title']))
            <h2 class="text-[clamp(1.75rem,3.4vw,2.5rem)] font-semibold tracking-tight text-balance">{{ $a['title'] }}</h2>
          @endif
          @if (! empty($a['subtitle']))
            <p class="mt-4 text-[1.0625rem] leading-relaxed {{ $isDark ? 'text-white/70' : 'text-text-secondary' }}">{{ $a['subtitle'] }}</p>
          @endif
        </div>
      @endif

      <div class="grid grid-cols-1 {{ $colClass }} gap-5">
        @foreach ($items as $i => $item)
          @php
            $href = ! empty($item['link_url']) ? $item['link_url'] : '';
            $tag = $href ? 'a' : 'div';
            $span = ($featureFirst && $i === 0) ? ($columns === 3 ? 'md:col-span-3' : 'md:col-span-2') : '';
            $lift = ($featureFirst && $i === 0)
              ? ($isDark ? 'bg-white/[0.06]' : 'bg-accent-subtle')
              : ($isDark ? 'bg-white/[0.03] border border-white/10' : 'bg-surface-primary border border-surface-tertiary');
          @endphp
          <{{ $tag }}
            @if($href) href="{{ esc_url($href) }}" @endif
            class="group flex flex-col gap-3 rounded-2xl p-7 md:p-8 {{ $span }} {{ $lift }} transition-colors duration-300 motion-reduce:transition-none {{ $href ? 'hover:border-accent focus:outline-2 focus:outline-offset-2 focus:outline-accent' : '' }}"
          >
            <h3 class="text-[1.2rem] font-semibold tracking-tight">{{ $item['title'] }}</h3>

            @if (! empty($item['description']))
              <p class="text-[0.9375rem] leading-relaxed max-w-[56ch] {{ $isDark ? 'text-white/70' : 'text-text-secondary' }}">{{ $item['description'] }}</p>
            @endif

            @if (! empty($item['link_text']))
              <span class="mt-1 text-sm font-semibold text-accent">
                {{ $item['link_text'] }}
                <span aria-hidden="true" class="inline-block transition-transform duration-300 group-hover:translate-x-1 motion-reduce:transition-none">&rarr;</span>
              </span>
            @endif
          </{{ $tag }}>
        @endforeach
      </div>
    </div>
  </section>
@endif
