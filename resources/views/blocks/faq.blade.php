@php($a = $attributes)

<section class="py-24 md:py-32 bg-surface-primary">
  <div class="max-w-3xl mx-auto px-6">
    @if($a['title'])
      <div class="text-center mb-16 reveal">
        <h2 class="text-4xl font-bold tracking-tight text-text-primary">{!! $a['title'] !!}</h2>
      </div>
    @endif

    <div class="space-y-4 reveal">
      @foreach(($a['items'] ?? []) as $item)
        <details class="group rounded-2xl border border-surface-tertiary bg-surface-primary overflow-hidden hover:border-text-tertiary transition-colors">
          <summary class="flex items-center justify-between cursor-pointer px-6 py-5 text-left">
            <span class="text-[15px] font-semibold text-text-primary pr-8">{{ $item['question'] ?? '' }}</span>
            <svg class="w-5 h-5 text-text-tertiary shrink-0 transition-transform duration-300 group-open:rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
          </summary>
          <div class="px-6 pb-5 text-text-secondary leading-relaxed">
            {{ $item['answer'] ?? '' }}
          </div>
        </details>
      @endforeach
    </div>
  </div>
</section>
