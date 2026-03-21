@php($a = $attributes)

<section class="py-16 {{ ($a['variant'] ?? 'light') === 'dark' ? 'bg-[var(--color-surface-inverse)] text-white' : 'bg-[var(--color-surface-primary)] border-y border-[var(--color-surface-tertiary)]' }}">
  <div class="max-w-7xl mx-auto px-6">
    @php($items = $a['items'] ?? [])
    <div class="grid grid-cols-2 md:grid-cols-{{ min(count($items), 4) }} gap-8 md:gap-12">
      @foreach($items as $stat)
        <div class="text-center reveal">
          <div class="text-4xl md:text-5xl font-bold tracking-tight">{{ $stat['value'] ?? '' }}</div>
          <div class="mt-1 text-sm {{ ($a['variant'] ?? 'light') === 'dark' ? 'text-[var(--color-text-secondary)]' : 'text-[var(--color-text-secondary)]' }}">{{ $stat['label'] ?? '' }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>
