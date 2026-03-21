@php
  $authorId = get_the_author_meta('ID');
  $authorName = get_the_author();
  $authorBio = get_the_author_meta('description');
  $authorUrl = get_author_posts_url($authorId);
  $authorAvatar = get_avatar_url($authorId, ['size' => 112]);
@endphp

<div class="p-6 rounded-2xl bg-[var(--color-surface-secondary)] border border-[var(--color-surface-tertiary)]">
  <div class="flex items-start gap-4">
    <img
      src="{{ $authorAvatar }}"
      alt="{{ esc_attr($authorName) }}"
      class="w-14 h-14 rounded-full shrink-0"
      loading="lazy"
      decoding="async"
    >
    <div>
      <a href="{{ $authorUrl }}" class="font-bold text-[var(--color-text-primary)] hover:text-[var(--color-accent)] transition-colors">
        {{ $authorName }}
      </a>
      @if($authorBio)
        <p class="mt-1 text-sm text-[var(--color-text-secondary)] leading-relaxed">
          {{ $authorBio }}
        </p>
      @endif
    </div>
  </div>
</div>
