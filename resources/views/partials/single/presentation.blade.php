{{--
  Presentation single post layout — inspired by Linear.
  Each section feels like a visual slide. Numbered markers, scroll snapping.
--}}

@includeWhen($singleShowProgressBar, 'partials.components.reading-progress')

<article @php(post_class('h-entry'))>

  {{-- Title slide --}}
  <header class="min-h-[60vh] flex items-center justify-center text-center px-6 py-20">
    <div class="max-w-4xl mx-auto">
      @if($category = get_the_category())
        <a
          href="{{ get_category_link($category[0]->term_id) }}"
          class="inline-block text-xs font-semibold uppercase tracking-wider text-accent hover:opacity-80 transition-opacity"
        >
          {!! $category[0]->name !!}
        </a>
      @endif

      <h1 class="mt-4 text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight leading-[1.05] text-text-primary [text-wrap:balance] p-name">
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
        @if($singleShowReadingTime)
          <span>&middot;</span>
          <span>{{ $readingTime }}</span>
        @endif
      </div>
    </div>
  </header>

  {{-- Featured image --}}
  @if(has_post_thumbnail())
    <div class="max-w-6xl mx-auto px-6 mb-16">
      <div class="rounded-2xl overflow-hidden">
        {!! get_the_post_thumbnail(get_the_ID(), 'brndle-hero', [
          'class' => 'w-full h-auto',
          'loading' => 'eager',
          'decoding' => 'async',
          'fetchpriority' => 'high',
        ]) !!}
      </div>
    </div>
  @endif

  {{-- Content with scroll snap and section counters --}}
  <div class="presentation-wrapper" style="scroll-snap-type: y proximity;">
    <div class="max-w-[700px] mx-auto px-6 e-content">
      <div class="presentation-content prose prose-lg max-w-none
        prose-headings:font-bold prose-headings:tracking-tight
        prose-a:text-accent prose-a:no-underline hover:prose-a:underline
        prose-img:rounded-xl">
        @php(the_content())
      </div>
    </div>
  {{-- Presentation styles: numbered sections, scroll snap --}}
  <style>
    /* Section counter on h2 headings */
    .presentation-content {
      counter-reset: section;
    }

    .presentation-content h2 {
      counter-increment: section;
      position: relative;
      padding-top: 6rem;
      scroll-margin-top: 6rem;
      scroll-snap-align: start;
    }

    .presentation-content h2::before {
      content: counter(section, decimal-leading-zero);
      display: block;
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--color-accent);
      margin-bottom: 0.75rem;
      font-variant-numeric: tabular-nums;
    }

    /* Left marker line on desktop */
    @media (min-width: 1024px) {
      .presentation-content h2 {
        padding-left: 3rem;
      }

      .presentation-content h2::before {
        position: absolute;
        left: 0;
        top: 6rem;
      }

      .presentation-content h2::after {
        content: '';
        position: absolute;
        left: 2.25rem;
        top: 6rem;
        bottom: 0;
        width: 1px;
        background: var(--color-surface-tertiary);
      }
    }

    /* First heading doesn't need as much top padding */
    .presentation-content h2:first-of-type {
      padding-top: 2rem;
    }

    .presentation-content h2:first-of-type::after {
      top: 2rem;
    }

    @media (min-width: 1024px) {
      .presentation-content h2:first-of-type::before {
        top: 2rem;
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
