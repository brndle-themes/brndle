{{--
  Cinematic single post layout — inspired by Figma blog.
  Visual storytelling with ultra-wide hero and full-width imagery.
--}}

@includeWhen($singleShowProgressBar, 'partials.components.reading-progress')

<article @php(post_class('h-entry'))>

  {{-- Ultra-wide hero image --}}
  @if(has_post_thumbnail())
    <div class="w-full overflow-hidden">
      <div class="aspect-[21/9] w-full">
        {!! get_the_post_thumbnail(get_the_ID(), 'brndle-hero', [
          'class' => 'w-full h-full object-cover',
          'loading' => 'eager',
          'decoding' => 'async',
          'fetchpriority' => 'high',
        ]) !!}
      </div>
    </div>
  @endif

  {{-- Title section --}}
  <header class="max-w-4xl mx-auto px-6 py-16 text-center">
    @if($category = get_the_category())
      <a
        href="{{ get_category_link($category[0]->term_id) }}"
        class="inline-block text-xs font-semibold uppercase tracking-wider text-accent hover:opacity-80 transition-opacity"
      >
        {!! $category[0]->name !!}
      </a>
    @endif

    <h1 class="mt-4 text-5xl sm:text-6xl font-bold tracking-tight leading-[1.1] text-text-primary [text-wrap:balance] p-name">
      {{ $title }}
    </h1>

    <div class="mt-8 flex flex-wrap items-center justify-center gap-4 text-sm text-text-tertiary">
      <div class="flex items-center gap-2">
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
          class="font-medium text-text-primary hover:text-accent transition-colors p-author h-card"
        >
          {{ get_the_author() }}
        </a>
      </div>
      <span>&middot;</span>
      <time class="dt-published" datetime="{{ get_post_time('c', true) }}">
        {{ get_the_date() }}
      </time>
      @include('partials.components.updated-date', ['withSeparator' => true])
      @if($singleShowReadingTime)
        <span>&middot;</span>
        <span>{{ $readingTime }}</span>
      @endif
    </div>
  </header>

  {{-- Content with cinematic styling --}}
  <div class="cinematic-content e-content">
    <div class="max-w-[700px] mx-auto px-6">
      <div class="prose prose-lg max-w-none
        prose-headings:font-bold prose-headings:tracking-tight prose-headings:scroll-mt-24 prose-headings:pt-16
        prose-a:text-accent prose-a:no-underline hover:prose-a:underline
        prose-img:rounded-xl prose-img:my-0">
        @php(the_content())
      </div>
    </div>
  {{-- Cinematic styles: full-width images, section spacing --}}
  <style>
    /* Full-width images breaking out of content column */
    .cinematic-content .wp-block-image,
    .cinematic-content figure.alignwide,
    .cinematic-content figure.alignfull {
      max-width: 100vw;
      margin-left: 50%;
      transform: translateX(-50%);
      width: 100vw;
      padding: 2rem 0;
    }

    .cinematic-content .wp-block-image img,
    .cinematic-content figure.alignwide img,
    .cinematic-content figure.alignfull img {
      width: 100%;
      border-radius: 0;
    }

    /* Generous spacing between major sections (h2 boundaries) */
    .cinematic-content .prose h2 {
      padding-top: 4rem;
      scroll-margin-top: 6rem;
    }

    /* Alternating subtle backgrounds on section groups */
    .cinematic-content .prose h2:nth-of-type(even) {
      position: relative;
    }

    .cinematic-content .prose h2:nth-of-type(even)::before {
      content: '';
      position: absolute;
      left: -50vw;
      right: -50vw;
      top: 0;
      bottom: -2rem;
      background: var(--color-surface-secondary);
      z-index: -1;
    }
  </style>

  {{-- Pagination --}}
  @if($pagination)
    <div class="max-w-[700px] mx-auto px-6 mt-8">
      <nav class="page-nav" aria-label="Page">
        {!! $pagination !!}
      </nav>
    </div>
  @endif

  {{-- Tags --}}
  @if($tags = get_the_tags())
    <div class="max-w-[700px] mx-auto px-6 mt-12 pt-8 border-t border-surface-tertiary">
      <div class="flex flex-wrap gap-2">
        @foreach($tags as $tag)
          <a
            href="{{ get_tag_link($tag->term_id) }}"
            class="px-3 py-1 text-xs font-medium rounded-full bg-surface-secondary text-text-secondary hover:bg-accent-subtle hover:text-accent transition-colors"
          >
            {{ $tag->name }}
          </a>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Social share --}}
  <div class="max-w-[700px] mx-auto px-6 mt-8">
    @includeWhen($singleShowSocialShare, 'partials.components.social-share')
  </div>

  {{-- Author box --}}
  <div class="max-w-[700px] mx-auto px-6 mt-10">
    @includeWhen($singleShowAuthorBox, 'partials.components.author-box')
  </div>

  {{-- Post navigation --}}
  <div class="max-w-[700px] mx-auto px-6 mt-10">
    @includeWhen($singleShowPostNav, 'partials.components.post-navigation')
  </div>

  <div class="max-w-5xl mx-auto px-6 mt-16 mb-16">
  {{-- Related posts --}}
  @includeWhen($singleShowRelatedPosts, 'partials.components.related-posts')
  {{-- Comments --}}
  @php(comments_template())

</article>
