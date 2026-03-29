@php
  $a = $attributes;
  $isDark = ($a['variant'] ?? 'dark') === 'dark';
  $videoUrl = $a['video_url'] ?? '';
  $videoType = $a['video_type'] ?? 'youtube';
  $autoplay = ($a['autoplay'] ?? false) ? 1 : 0;
  $controls = ($a['show_controls'] ?? true) ? 1 : 0;

  $embedUrl = '';
  if ($videoType === 'youtube' && $videoUrl) {
    preg_match('/(?:youtu\.be\/|(?:m\.)?youtube\.com\/(?:embed\/|v\/|watch\?v=|shorts\/|live\/))([^&?\s]+)/', $videoUrl, $m);
    $vid = $m[1] ?? '';
    if ($vid) {
      $embedUrl = "https://www.youtube-nocookie.com/embed/{$vid}?rel=0&modestbranding=1&autoplay={$autoplay}&controls={$controls}";
      if ($autoplay) {
        $embedUrl .= '&mute=1';
      }
    }
  } elseif ($videoType === 'vimeo' && $videoUrl) {
    preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $videoUrl, $m);
    $vid = $m[1] ?? '';
    if ($vid) {
      $embedUrl = "https://player.vimeo.com/video/{$vid}?autoplay={$autoplay}";
      if ($autoplay) {
        $embedUrl .= '&muted=1';
      }
    }
  }

  $maxWidthClass = ['full' => 'max-w-7xl', 'medium' => 'max-w-4xl', 'narrow' => 'max-w-2xl'][$a['max_width'] ?? 'full'] ?? 'max-w-7xl';
  $aspectClass = ['16/9' => 'aspect-video', '4/3' => 'aspect-[4/3]', '1/1' => 'aspect-square', '21/9' => 'aspect-[21/9]'][$a['aspect_ratio'] ?? '16/9'] ?? 'aspect-video';
@endphp

<section class="py-24 md:py-32 {{ $isDark ? 'brndle-section-dark' : 'bg-surface-primary' }}">
  <div class="{{ $maxWidthClass }} mx-auto px-6">
    @if($a['title'])
      <div class="max-w-2xl mx-auto text-center mb-12 reveal">
        @if($a['eyebrow'])
          <p class="text-sm font-semibold text-accent uppercase tracking-[0.15em] mb-3">{{ $a['eyebrow'] }}</p>
        @endif
        <h2 class="text-4xl sm:text-5xl font-bold tracking-tight">{!! wp_kses_post($a['title']) !!}</h2>
        @if($a['subtitle'])
          <p class="mt-4 text-lg {{ $isDark ? 'text-white/70' : 'text-text-secondary' }}">{{ $a['subtitle'] }}</p>
        @endif
      </div>
    @endif

    <div class="reveal">
      @if($embedUrl)
        <div class="relative {{ $aspectClass }} rounded-2xl overflow-hidden {{ $isDark ? 'shadow-accent-glow border border-white/[0.08]' : 'shadow-2xl border border-surface-tertiary' }}">
          <iframe
            src="{{ esc_url($embedUrl) }}"
            class="absolute inset-0 w-full h-full"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
            loading="lazy"
            title="{{ wp_strip_all_tags($a['title'] ?? __('Video', 'brndle')) }}"
          ></iframe>
        </div>
      @elseif($videoType === 'self' && $videoUrl)
        <div class="relative {{ $aspectClass }} rounded-2xl overflow-hidden {{ $isDark ? 'shadow-accent-glow border border-white/[0.08]' : 'shadow-2xl border border-surface-tertiary' }}">
          <video
            class="absolute inset-0 w-full h-full object-cover"
            {{ $controls ? 'controls' : '' }}
            {{ $autoplay ? 'autoplay muted playsinline' : '' }}
            @if(!empty($a['poster'])) poster="{{ esc_url($a['poster']) }}" @endif
            preload="metadata"
            aria-label="{{ wp_strip_all_tags($a['title'] ?? __('Video', 'brndle')) }}"
          >
            <source src="{{ esc_url($videoUrl) }}" type="video/mp4">
          </video>
        </div>
      @else
        <div class="{{ $aspectClass }} rounded-2xl {{ $isDark ? 'bg-white/[0.04] border border-white/10' : 'bg-surface-secondary border border-surface-tertiary' }} flex items-center justify-center">
          <p class="{{ $isDark ? 'text-white/40' : 'text-text-tertiary' }} text-sm">{{ __('Add a video URL in the block settings', 'brndle') }}</p>
        </div>
      @endif
    </div>
  </div>
</section>
