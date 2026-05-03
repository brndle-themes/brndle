{{--
  Author bio box (v1.9.1)

  Rendered at the foot of single posts when `single_show_author_box` is on.
  Works with the LocalAvatar feature: when an author has uploaded a local
  avatar, it's used; otherwise falls back to gravatar (or to a transparent
  pixel if `disable_gravatar_fallback` is set in theme settings).

  Reads per-user meta keys set in the user profile screen
  (Brndle\Avatars\LocalAvatar::SOCIAL_KEYS):
    _brndle_role        — short subtitle under the name
    _brndle_twitter     — X / Twitter URL
    _brndle_linkedin    — LinkedIn URL
    _brndle_github      — GitHub URL
    _brndle_website     — personal website URL
--}}
@php
  $authorId = (int) get_the_author_meta('ID');
  $authorName = get_the_author();
  $authorBio = get_the_author_meta('description');
  $authorRole = (string) get_user_meta($authorId, '_brndle_role', true);
  $authorPostsUrl = get_author_posts_url($authorId);
  $postCount = (int) count_user_posts($authorId, 'post', true);

  $socials = array_filter([
    'twitter' => (string) get_user_meta($authorId, '_brndle_twitter', true),
    'linkedin' => (string) get_user_meta($authorId, '_brndle_linkedin', true),
    'github' => (string) get_user_meta($authorId, '_brndle_github', true),
    'website' => (string) get_user_meta($authorId, '_brndle_website', true),
  ]);
@endphp

<aside class="brndle-author-box mt-12 p-6 sm:p-8 rounded-2xl bg-surface-secondary border border-surface-tertiary">
  <div class="flex items-start gap-5">
    {{-- Avatar — picks up the local avatar via the LocalAvatar filter on
         get_avatar / pre_get_avatar_data. Explicit width / height attrs
         prevent CLS while the image loads. --}}
    <div class="shrink-0">
      {!! get_avatar($authorId, 96, '', $authorName, ['class' => 'rounded-2xl ring-1 ring-surface-tertiary', 'extra_attr' => 'width="96" height="96" loading="lazy" decoding="async"']) !!}
    </div>

    <div class="min-w-0 flex-1">
      <div class="flex items-baseline gap-2 flex-wrap">
        <a href="{{ esc_url($authorPostsUrl) }}" class="text-lg font-bold text-text-primary hover:text-accent transition-colors">
          {{ $authorName }}
        </a>
        @if($authorRole)
          <span class="text-sm text-text-tertiary">&middot; {{ $authorRole }}</span>
        @endif
      </div>

      @if($authorBio)
        {{-- WP user `description` meta intentionally allows HTML (links,
             strong, em). Render via wp_kses_post so authors can keep
             rich-text bios without exposing XSS. The wpautop converts
             double newlines into paragraph breaks for legacy bios. --}}
        <div class="brndle-author-bio mt-2 text-sm text-text-secondary leading-relaxed">
          {!! wp_kses_post(wpautop($authorBio)) !!}
        </div>
      @endif

      <div class="mt-4 flex items-center gap-4 flex-wrap">
        @if($postCount > 0)
          <a href="{{ esc_url($authorPostsUrl) }}" class="text-xs font-semibold text-accent hover:text-accent/80 inline-flex items-center gap-1">
            {{ sprintf(_n('%d post', '%d posts', $postCount, 'brndle'), $postCount) }}
            <span aria-hidden="true">&rarr;</span>
          </a>
        @endif

        @if(!empty($socials))
          <ul class="flex items-center gap-2 list-none m-0 p-0">
            @foreach($socials as $key => $url)
              <li>
                <a href="{{ esc_url($url) }}"
                   target="_blank"
                   rel="noopener me"
                   aria-label="{{ esc_attr($authorName . ' on ' . ucfirst($key)) }}"
                   class="inline-flex items-center justify-center w-8 h-8 rounded-md text-text-tertiary hover:text-accent hover:bg-surface-tertiary transition-colors">
                  @switch($key)
                    @case('twitter')
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                      @break
                    @case('linkedin')
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                      @break
                    @case('github')
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                      @break
                    @case('website')
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                      @break
                  @endswitch
                </a>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
</aside>
