{{--
  Sidebar single post layout — inspired by Intercom blog.
  Content + sticky TOC sidebar on desktop.
--}}

@includeWhen($singleShowProgressBar, 'partials.components.reading-progress')

<article @php(post_class('h-entry'))>

  {{-- Header: full width above the grid --}}
  <header class="max-w-6xl mx-auto px-6 pt-16 pb-8">
    @if($category = get_the_category())
      <a
        href="{{ get_category_link($category[0]->term_id) }}"
        class="inline-block text-xs font-semibold uppercase tracking-wider text-accent hover:opacity-80 transition-opacity"
      >
        {!! $category[0]->name !!}
      </a>
    @endif

    <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-tight leading-[1.1] text-text-primary [text-wrap:balance] p-name">
      {{ $title }}
    </h1>

    <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-text-tertiary">
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

  {{-- Mobile TOC (collapsible) --}}
  @if($singleShowToc)
    <div class="lg:hidden max-w-[700px] mx-auto px-6 mb-8">
      <details class="rounded-xl border border-surface-tertiary bg-surface-secondary">
        <summary class="px-4 py-3 text-sm font-semibold text-text-primary cursor-pointer select-none">
          {{ __('Table of Contents', 'brndle') }}
        </summary>
        <div class="px-4 pb-4">
          @include('partials.components.table-of-contents')
        </div>
      </details>
    </div>
  @endif

  {{-- Two-column grid: content + sidebar --}}
  <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-[1fr_280px] gap-12">

    {{-- Left column: content --}}
    <div class="min-w-0">
      <div class="max-w-[700px] e-content">
        <div class="prose prose-lg max-w-none
          prose-headings:font-bold prose-headings:tracking-tight
          prose-a:text-accent prose-a:no-underline hover:prose-a:underline
          prose-img:rounded-xl">
          @php(the_content())
        </div>
      </div>

      {{-- Pagination --}}
      @if($pagination)
        <div class="max-w-[700px] mt-8">
          <nav class="page-nav" aria-label="Page">
            {!! $pagination !!}
          </nav>
        </div>
      @endif

      {{-- Tags --}}
      @if($tags = get_the_tags())
        <div class="max-w-[700px] mt-12 pt-8 border-t border-surface-tertiary">
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
    </div>

    {{-- Right column: sidebar (desktop only) --}}
    <aside class="hidden lg:block">
      <div class="lg:sticky lg:top-24 lg:self-start space-y-8">
        {{-- TOC --}}
        @includeWhen($singleShowToc, 'partials.components.table-of-contents')

        {{-- Author box --}}
        <div class="max-w-[700px] mx-auto px-6 mt-10">
    @includeWhen($singleShowAuthorBox, 'partials.components.author-box')
  </div>
      </div>
    </aside>

  {{-- Author box for mobile (shown below content on small screens) --}}
  @if($singleShowAuthorBox)
    <div class="lg:hidden">
      @include('partials.components.author-box')
    </div>
  @endif

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
