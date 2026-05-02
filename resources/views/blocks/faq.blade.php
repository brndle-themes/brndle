@php
  $a = $attributes;
  $isDark = ($a['variant'] ?? 'light') === 'dark';
  $faqItems = array_values(array_filter(
    $a['items'] ?? [],
    fn($item) => is_array($item) && !empty($item['question']) && !empty($item['answer'])
  ));
  $schema = null;
  if (!empty($faqItems)) {
    $schema = [
      '@context' => 'https://schema.org',
      '@type'    => 'FAQPage',
      'mainEntity' => array_map(function ($item) {
        return [
          '@type' => 'Question',
          'name'  => wp_strip_all_tags($item['question']),
          'acceptedAnswer' => [
            '@type' => 'Answer',
            'text'  => wp_strip_all_tags($item['answer']),
          ],
        ];
      }, $faqItems),
    ];
  }
@endphp

@if($schema)
  <script type="application/ld+json">{!! wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif

<section class="py-24 md:py-32 {{ $isDark ? 'brndle-section-dark' : 'bg-surface-primary' }}">
  <div class="max-w-3xl mx-auto px-6">
    @if($a['title'])
      <div class="text-center mb-16 reveal">
        <h2 class="text-4xl font-bold tracking-tight {{ $isDark ? '' : 'text-text-primary' }}">{!! wp_kses_post($a['title']) !!}</h2>
      </div>
    @endif

    <div class="space-y-4 reveal">
      @foreach(($a['items'] ?? []) as $i => $item)
        <details class="group rounded-2xl border {{ $isDark ? 'border-white/[0.08] bg-white/[0.02]' : 'border-surface-tertiary bg-surface-primary' }} overflow-hidden hover:border-text-tertiary transition-colors motion-reduce:transition-none" id="faq-{{ $i }}">
          <summary class="flex items-center justify-between cursor-pointer px-6 py-5 text-left focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent" aria-controls="faq-answer-{{ $i }}">
            <span class="text-[15px] font-semibold {{ $isDark ? '' : 'text-text-primary' }} pr-8">{{ $item['question'] ?? '' }}</span>
            <x-icon name="plus" class="w-5 h-5 {{ $isDark ? 'text-white/40' : 'text-text-tertiary' }} shrink-0 transition-transform duration-300 group-open:rotate-45 motion-reduce:transition-none" />
          </summary>
          <div class="px-6 pt-2 pb-5 {{ $isDark ? 'text-white/70' : 'text-text-secondary' }} leading-relaxed" id="faq-answer-{{ $i }}">
            {!! wp_kses_post($item['answer'] ?? '') !!}
          </div>
        </details>
      @endforeach
    </div>
  </div>
</section>
