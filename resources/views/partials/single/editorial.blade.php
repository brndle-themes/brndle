{{--
  Editorial single post layout — inspired by NYT long reads.
  Magazine quality with drop cap, breakout images, generous line height.
--}}

@includeWhen($singleShowProgressBar, 'partials.components.reading-progress')

<article @php(post_class('h-entry'))>

  {{-- Header --}}
  <header class="max-w-4xl mx-auto px-6 pt-16 pb-10">
    {{-- Category + date header --}}
    <div class="flex flex-wrap items-center gap-3 text-sm text-text-tertiary">
      @if($category = get_the_category())
        <a
          href="{{ get_category_link($category[0]->term_id) }}"
          class="font-semibold uppercase tracking-wider text-accent hover:opacity-80 transition-opacity text-xs"
        >
          {{ $category[0]->name }}
        </a>
        <span>&middot;</span>
      @endif
      <time class="dt-published" datetime="{{ get_post_time('c', true) }}">
        {{ get_the_date() }}
      </time>
    </div>

    <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-heading font-bold tracking-tight leading-[1.1] text-text-primary [text-wrap:balance] p-name">
      {!! $title !!}
    </h1>

    {{-- Meta --}}
    <div class="mt-8 flex flex-wrap items-center gap-4 text-sm text-text-tertiary">
      <div class="flex items-center gap-3">
        @php($avatar = get_avatar_url(get_the_author_meta('ID'), ['size' => 48]))
        <img
          src="{{ $avatar }}"
          alt="{{ get_the_author() }}"
          class="w-10 h-10 rounded-full"
          loading="lazy"
          decoding="async"
        >
        <div>
          <a
            href="{{ get_author_posts_url(get_the_author_meta('ID')) }}"
            class="block font-medium text-text-primary hover:text-accent transition-colors p-author h-card"
          >
            {{ get_the_author() }}
          </a>
          @if($singleShowReadingTime)
            <span class="text-xs text-text-tertiary">{{ $readingTime }}</span>
          @endif
        </div>
      </div>
    </div>

    {{-- Divider --}}
    <div class="mt-8 border-t border-surface-tertiary"></div>
  </header>

  {{-- Featured image: breakout width --}}
  @if(has_post_thumbnail())
    <div class="max-w-5xl mx-auto px-6 mb-14">
      <figure>
        {!! get_the_post_thumbnail(get_the_ID(), 'brndle-hero', [
          'class' => 'w-full h-auto',
          'loading' => 'eager',
          'decoding' => 'async',
          'fetchpriority' => 'high',
        ]) !!}
        @if($caption = get_the_post_thumbnail_caption())
          <figcaption class="mt-3 text-center text-sm text-text-tertiary italic">
            {{ $caption }}
          </figcaption>
        @endif
      </figure>
    </div>
  @endif

  {{-- Content with drop cap and breakout images --}}
  <div class="max-w-[700px] mx-auto px-6 e-content">
    <div class="editorial-content prose prose-lg max-w-none leading-[1.8]
      prose-headings:font-heading prose-headings:font-bold prose-headings:tracking-tight
      prose-a:text-accent prose-a:no-underline hover:prose-a:underline
      prose-img:rounded-none prose-img:my-12
      prose-blockquote:text-xl prose-blockquote:leading-relaxed prose-blockquote:italic
      prose-blockquote:border-l-4 prose-blockquote:border-accent
      prose-blockquote:pl-6 prose-blockquote:text-text-secondary
      first-of-type:prose-p:first-letter:text-7xl first-of-type:prose-p:first-letter:font-bold
      first-of-type:prose-p:first-letter:float-left first-of-type:prose-p:first-letter:mr-3
      first-of-type:prose-p:first-letter:mt-1 first-of-type:prose-p:first-letter:leading-none
      first-of-type:prose-p:first-letter:text-accent">
      @php(the_content())
    </div>
  </div>

  {{-- Drop cap and breakout image styles --}}
  <style>
    .editorial-content > p:first-of-type::first-letter {
      font-size: 4.5rem;
      font-weight: 700;
      float: left;
      margin-right: 0.75rem;
      margin-top: 0.25rem;
      line-height: 1;
      color: var(--color-accent);
    }

    /* Breakout images beyond the 700px content column */
    .editorial-content img,
    .editorial-content figure {
      max-width: 64rem;
      margin-left: 50%;
      transform: translateX(-50%);
      width: calc(100% + 4rem);
    }

    @media (min-width: 1024px) {
      .editorial-content img,
      .editorial-content figure {
        width: calc(100% + 16rem);
      }
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
  @includeWhen($singleShowSocialShare, 'partials.components.social-share')

  {{-- Author box --}}
  @includeWhen($singleShowAuthorBox, 'partials.components.author-box')

  {{-- Post navigation --}}
  @includeWhen($singleShowPostNav, 'partials.components.post-navigation')

  {{-- Related posts --}}
  @includeWhen($singleShowRelatedPosts, 'partials.components.related-posts')

  {{-- Comments --}}
  @php(comments_template())

</article>
