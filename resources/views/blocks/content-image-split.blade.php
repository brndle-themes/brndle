@php($a = $attributes)

<section class="py-24 md:py-32 {{ ($a['variant'] ?? 'light') === 'dark' ? 'brndle-section-dark' : 'bg-surface-primary' }}">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center {{ ($a['image_position'] ?? 'right') === 'left' ? 'lg:[direction:rtl] lg:[&>*]:[direction:ltr]' : '' }}">
      <div class="reveal">
        @if($a['eyebrow'])
          <p class="text-sm font-semibold text-accent uppercase tracking-[0.15em] mb-3">{{ $a['eyebrow'] }}</p>
        @endif
        @if($a['title'])
          <h2 class="text-3xl sm:text-4xl font-bold tracking-tight">{!! wp_kses_post($a['title']) !!}</h2>
        @endif
        @if($a['description'])
          <p class="mt-4 text-lg {{ ($a['variant'] ?? 'light') === 'dark' ? 'text-white/70' : 'text-text-secondary' }} leading-relaxed">{{ $a['description'] }}</p>
        @endif
        @if(!empty($a['bullets']))
          <ul class="mt-6 space-y-3">
            @foreach($a['bullets'] as $bullet)
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 text-accent shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"/></svg>
                <span class="{{ ($a['variant'] ?? 'light') === 'dark' ? 'text-white/70' : 'text-text-secondary' }}">{{ $bullet }}</span>
              </li>
            @endforeach
          </ul>
        @endif
        @if($a['cta_text'])
          <div class="mt-8">
            <a href="{{ esc_url($a['cta_url']) }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold rounded-xl bg-accent text-on-accent hover:opacity-90 transition-all focus:outline-2 focus:outline-offset-2 focus:outline-accent">
              {{ $a['cta_text'] }}
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
          </div>
        @endif
      </div>
      <div class="reveal">
        @if($a['image'])
          <div class="rounded-2xl {{ ($a['variant'] ?? 'light') === 'dark' ? 'border border-white/10' : 'bg-surface-secondary border border-surface-tertiary shadow-lg' }} overflow-hidden">
            <img src="{{ esc_url($a['image']) }}" alt="{{ esc_attr($a['image_alt'] ?? '') }}" class="w-full" loading="lazy" decoding="async">
          </div>
        @else
          <div class="aspect-[4/3] rounded-2xl {{ ($a['variant'] ?? 'light') === 'dark' ? 'bg-white/5 border border-white/10' : 'bg-surface-secondary border border-surface-tertiary' }} flex items-center justify-center">
            <span class="text-text-tertiary text-sm">{{ __('Add an image URL', 'brndle') }}</span>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>
