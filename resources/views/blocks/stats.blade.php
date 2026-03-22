@php($a = $attributes)

<section class="py-16 {{ ($a['variant'] ?? 'light') === 'dark' ? 'brndle-section-dark' : 'bg-surface-primary border-y border-surface-tertiary' }}">
  <div class="max-w-7xl mx-auto px-6">
    @php($items = $a['items'] ?? [])
    @php($statCols = ['md:grid-cols-1', 'md:grid-cols-2', 'md:grid-cols-3', 'md:grid-cols-4'])
    <div class="grid grid-cols-2 {{ $statCols[min(count($items), 4) - 1] ?? 'md:grid-cols-4' }} gap-8 md:gap-12">
      @foreach($items as $stat)
        <div class="text-center reveal">
          <div class="text-4xl md:text-5xl font-bold tracking-tight" aria-label="{{ ($stat['value'] ?? '') . ' ' . ($stat['label'] ?? '') }}">{{ $stat['value'] ?? '' }}</div>
          <div class="mt-1 text-sm {{ ($a['variant'] ?? 'light') === 'dark' ? 'text-white/70' : 'text-text-secondary' }}" aria-hidden="true">{{ $stat['label'] ?? '' }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>
