{{--
  Minimal dark single post layout — inspired by Mercury/Linear.
  Forced dark mode, monospace accents, stark elegance.
--}}

@includeWhen($singleShowProgressBar, 'partials.components.reading-progress')

<div data-theme="dark">
  <article @php(post_class('h-entry bg-[var(--color-surface-primary)] text-[var(--color-text-primary)]'))>

    {{-- Header --}}
    <header class="max-w-[640px] mx-auto px-6 pt-32 pb-12 space-y-8">
      {{-- Category + date in monospace --}}
      <div class="flex flex-wrap items-center gap-3 font-mono text-xs tracking-wider uppercase text-[var(--color-text-tertiary)]">
        @if($category = get_the_category())
          <a
            href="{{ get_category_link($category[0]->term_id) }}"
            class="text-[var(--color-accent)] hover:opacity-80 transition-opacity"
          >
            {{ $category[0]->name }}
          </a>
          <span>/</span>
        @endif
        <time class="dt-published" datetime="{{ get_post_time('c', true) }}">
          {{ get_the_date() }}
        </time>
        @if($singleShowReadingTime)
          <span>/</span>
          <span>{{ $readingTime }}</span>
        @endif
      </div>

      <h1 class="text-4xl sm:text-5xl font-bold tracking-tight leading-[1.1] text-[var(--color-text-primary)] [text-wrap:balance] p-name">
        {!! $title !!}
      </h1>

      {{-- Author --}}
      <div class="flex items-center gap-3">
        @php($avatar = get_avatar_url(get_the_author_meta('ID'), ['size' => 40]))
        <img
          src="{{ $avatar }}"
          alt="{{ get_the_author() }}"
          class="w-8 h-8 rounded-full"
          loading="lazy"
          decoding="async"
        >
        <a
          href="{{ get_author_posts_url(get_the_author_meta('ID')) }}"
          class="font-mono text-sm text-[var(--color-text-secondary)] hover:text-[var(--color-accent)] transition-colors p-author h-card"
        >
          {{ get_the_author() }}
        </a>
      </div>

      {{-- Thin divider --}}
      <div class="border-t border-[var(--color-surface-tertiary)]"></div>
    </header>

    {{-- Featured image: no rounded corners, no shadows --}}
    @if(has_post_thumbnail())
      <div class="max-w-[640px] mx-auto px-6 mb-16">
        {!! get_the_post_thumbnail(get_the_ID(), 'brndle-hero', [
          'class' => 'w-full h-auto',
          'loading' => 'eager',
          'decoding' => 'async',
          'fetchpriority' => 'high',
        ]) !!}
      </div>
    @endif

    {{-- Content --}}
    <div class="max-w-[640px] mx-auto px-6 e-content">
      <div class="minimal-dark-content prose prose-lg prose-invert max-w-none
        prose-headings:font-bold prose-headings:tracking-tight
        prose-a:text-[var(--color-accent)] prose-a:no-underline hover:prose-a:underline
        prose-img:rounded-none
        prose-pre:bg-transparent prose-pre:border prose-pre:border-[var(--color-surface-tertiary)]
        prose-code:text-[var(--color-text-primary)]
        prose-blockquote:border-[var(--color-surface-tertiary)]
        prose-blockquote:text-[var(--color-text-secondary)]">
        @php(the_content())
      </div>
    </div>

    {{-- Styles for minimal dark specifics --}}
    <style>
      [data-theme="dark"] .minimal-dark-content pre {
        background: transparent;
        border: 1px solid var(--color-surface-tertiary);
      }

      [data-theme="dark"] .minimal-dark-content img {
        border-radius: 0;
        box-shadow: none;
      }

      [data-theme="dark"] .minimal-dark-content hr {
        border-color: var(--color-surface-tertiary);
      }
    </style>

    {{-- Pagination --}}
    @if($pagination)
      <div class="max-w-[640px] mx-auto px-6 mt-8">
        <nav class="page-nav" aria-label="Page">
          {!! $pagination !!}
        </nav>
      </div>
    @endif

    {{-- Tags --}}
    @if($tags = get_the_tags())
      <div class="max-w-[640px] mx-auto px-6 mt-16 pt-8 border-t border-[var(--color-surface-tertiary)]">
        <div class="flex flex-wrap gap-2">
          @foreach($tags as $tag)
            <a
              href="{{ get_tag_link($tag->term_id) }}"
              class="px-3 py-1 font-mono text-xs tracking-wider rounded-none border border-[var(--color-surface-tertiary)] text-[var(--color-text-secondary)] hover:border-[var(--color-accent)] hover:text-[var(--color-accent)] transition-colors"
            >
              {{ $tag->name }}
            </a>
          @endforeach
        </div>
      </div>
    @endif

    {{-- Social share --}}
    @includeWhen($singleShowSocialShare, 'partials.components.social-share')

    {{-- Author box --}}
    @includeWhen($singleShowAuthorBox, 'partials.components.author-box')

    {{-- Post navigation --}}
    @includeWhen($singleShowPostNav, 'partials.components.post-navigation')

    {{-- Related posts --}}
    @includeWhen($singleShowRelatedPosts, 'partials.components.related-posts')

    {{-- Comments --}}
    <div class="pb-16">
      @php(comments_template())
    </div>

  </article>
</div>
