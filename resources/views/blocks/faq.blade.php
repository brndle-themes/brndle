@php
  $a = $attributes;
  $isDark = ($a['variant'] ?? 'light') === 'dark';
@endphp

<section class="py-24 md:py-32 {{ $isDark ? 'brndle-section-dark' : 'bg-surface-primary' }}">
  <div class="max-w-3xl mx-auto px-6">
    @if($a['title'])
      <div class="text-center mb-16 reveal">
        <h2 class="text-4xl font-bold tracking-tight {{ $isDark ? '' : 'text-text-primary' }}">{!! wp_kses_post($a['title']) !!}</h2>
      </div>
    @endif

    <div class="space-y-4 reveal">
      @foreach(($a['items'] ?? []) as $i => $item)
        <details class="group rounded-2xl border {{ $isDark ? 'border-white/[0.08] bg-white/[0.02]' : 'border-surface-tertiary bg-surface-primary' }} overflow-hidden hover:border-text-tertiary transition-colors" id="faq-{{ $i }}">
          <summary class="flex items-center justify-between cursor-pointer px-6 py-5 text-left focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent" aria-controls="faq-answer-{{ $i }}">
            <span class="text-[15px] font-semibold {{ $isDark ? '' : 'text-text-primary' }} pr-8">{{ $item['question'] ?? '' }}</span>
            <svg class="w-5 h-5 {{ $isDark ? 'text-white/40' : 'text-text-tertiary' }} shrink-0 transition-transform duration-300 group-open:rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
          </summary>
          <div class="px-6 pt-2 pb-5 {{ $isDark ? 'text-white/70' : 'text-text-secondary' }} leading-relaxed" id="faq-answer-{{ $i }}">
            {!! wp_kses_post($item['answer'] ?? '') !!}
          </div>
        </details>
      @endforeach
    </div>
  </div>
</section>
