{{--
  Standard single post layout — inspired by Vercel blog.
  Clean, modern, generous whitespace.
--}}

@includeWhen($singleShowProgressBar, 'partials.components.reading-progress')

<article @php(post_class('h-entry'))>

  {{-- Header --}}
  <header class="max-w-4xl mx-auto px-6 pt-16 pb-8">
    {{-- Category --}}
    @if($category = get_the_category())
      <a
        href="{{ get_category_link($category[0]->term_id) }}"
        class="inline-block text-xs font-semibold uppercase tracking-wider text-[var(--color-accent)] hover:opacity-80 transition-opacity"
      >
        {{ $category[0]->name }}
      </a>
    @endif

    <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-tight leading-[1.1] text-[var(--color-text-primary)] [text-wrap:balance] p-name">
      {!! $title !!}
    </h1>

    {{-- Meta --}}
    <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-[var(--color-text-tertiary)]">
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
          class="font-medium text-[var(--color-text-primary)] hover:text-[var(--color-accent)] transition-colors p-author h-card"
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
  </header>

  {{-- Featured image --}}
  @if(has_post_thumbnail())
    <div class="max-w-6xl mx-auto px-6 mb-12">
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

  {{-- Content --}}
  <div class="max-w-[700px] mx-auto px-6 e-content">
    <div class="prose prose-lg max-w-none
      prose-headings:font-bold prose-headings:tracking-tight
      prose-a:text-[var(--color-accent)] prose-a:no-underline hover:prose-a:underline
      prose-img:rounded-xl">
      @php(the_content())
    </div>
  </div>

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
    <div class="max-w-[700px] mx-auto px-6 mt-12 pt-8 border-t border-[var(--color-surface-tertiary)]">
      <div class="flex flex-wrap gap-2">
        @foreach($tags as $tag)
          <a
            href="{{ get_tag_link($tag->term_id) }}"
            class="px-3 py-1 text-xs font-medium rounded-full bg-[var(--color-surface-secondary)] text-[var(--color-text-secondary)] hover:bg-[var(--color-accent-subtle)] hover:text-[var(--color-accent)] transition-colors"
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
