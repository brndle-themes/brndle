{{--
  Hero immersive single post layout — inspired by Apple Newsroom.
  Full-viewport hero with gradient overlay and content rising above.
--}}

@includeWhen($singleShowProgressBar, 'partials.components.reading-progress')

<article @php(post_class('h-entry'))>

  @if(has_post_thumbnail())
    {{-- Full-viewport hero with featured image --}}
    <header class="relative min-h-[80vh] flex items-end overflow-hidden">
      {{-- Background image --}}
      <div class="absolute inset-0">
        {!! get_the_post_thumbnail(get_the_ID(), 'brndle-hero', [
          'class' => 'w-full h-full object-cover',
          'loading' => 'eager',
          'decoding' => 'async',
          'fetchpriority' => 'high',
        ]) !!}
      </div>

      {{-- Gradient overlay --}}
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>

      {{-- Hero content overlaid at bottom --}}
      <div class="relative z-10 w-full max-w-4xl mx-auto px-6 pb-20 pt-40">
        @if($category = get_the_category())
          <a
            href="{{ get_category_link($category[0]->term_id) }}"
            class="inline-block text-xs font-semibold uppercase tracking-wider text-white/80 hover:text-white transition-colors"
          >
            {{ $category[0]->name }}
          </a>
        @endif

        <h1 class="mt-4 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-[1.1] text-white [text-wrap:balance] p-name">
          {{ $title }}
        </h1>

        <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-white/70">
          <div class="flex items-center gap-2">
            @php($avatar = get_avatar_url(get_the_author_meta('ID'), ['size' => 40]))
            <img
              src="{{ $avatar }}"
              alt="{{ get_the_author() }}"
              class="w-8 h-8 rounded-full ring-2 ring-white/20"
              loading="lazy"
              decoding="async"
            >
            <a
              href="{{ get_author_posts_url(get_the_author_meta('ID')) }}"
              class="font-medium text-white hover:text-white/80 transition-colors p-author h-card"
            >
              {{ get_the_author() }}
            </a>
          </div>
          <span class="text-white/40">&middot;</span>
          <time class="dt-published" datetime="{{ get_post_time('c', true) }}">
            {{ get_the_date() }}
          </time>
          @if($singleShowReadingTime)
            <span class="text-white/40">&middot;</span>
            <span>{{ $readingTime }}</span>
          @endif
        </div>
      </div>
    </header>

    {{-- Content section rises above hero --}}
    <div class="relative z-10 bg-surface-primary -mt-20 rounded-t-3xl pt-16">
  @else
    {{-- Fallback: dark gradient header when no featured image --}}
    <header class="relative bg-surface-inverse py-20">
      <div class="max-w-4xl mx-auto px-6">
        @if($category = get_the_category())
          <a
            href="{{ get_category_link($category[0]->term_id) }}"
            class="inline-block text-xs font-semibold uppercase tracking-wider text-text-inverse/70 hover:text-text-inverse transition-colors"
          >
            {{ $category[0]->name }}
          </a>
        @endif

        <h1 class="mt-4 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-[1.1] text-text-inverse [text-wrap:balance] p-name">
          {{ $title }}
        </h1>

        <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-text-inverse/60">
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
              class="font-medium text-text-inverse hover:opacity-80 transition-opacity p-author h-card"
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

    <div class="bg-surface-primary pt-16">
  @endif

    {{-- Content --}}
    <div class="max-w-[700px] mx-auto px-6 e-content">
      <div class="prose prose-lg max-w-none
        prose-headings:font-bold prose-headings:tracking-tight
        prose-a:text-accent prose-a:no-underline hover:prose-a:underline
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

  </div>{{-- close the content wrapper opened in the hero section --}}

</article>
