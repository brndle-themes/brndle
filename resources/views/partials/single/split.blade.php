{{--
  Split single post layout — inspired by Stripe blog.
  Two-column desktop: sticky info panel left, scrollable content right.
--}}

@includeWhen($singleShowProgressBar, 'partials.components.reading-progress')

<article @php(post_class('h-entry'))>

  {{-- Desktop: two-column split layout --}}
  <div class="lg:grid lg:grid-cols-2 lg:min-h-screen">

    {{-- Left panel: sticky info --}}
    <div class="lg:sticky lg:top-0 lg:h-screen flex flex-col justify-center bg-surface-secondary border-r border-accent/10">
      <div class="px-8 lg:px-12 py-16 lg:py-0">
        {{-- Category --}}
        @if($category = get_the_category())
          <a
            href="{{ get_category_link($category[0]->term_id) }}"
            class="inline-block text-xs font-semibold uppercase tracking-wider text-accent hover:opacity-80 transition-opacity"
          >
            {!! esc_html($category[0]->name) !!}
          </a>
        @endif

        <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight leading-[1.1] text-text-primary [text-wrap:balance] p-name">
          {{ $title }}
        </h1>

        {{-- Meta --}}
        <div class="mt-8 space-y-4">
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
              <div class="flex items-center gap-2 text-xs text-text-tertiary">
                <time class="dt-published" datetime="{{ get_post_time('c', true) }}">
                  {{ get_the_date() }}
                </time>
                @if($singleShowReadingTime)
                  <span>&middot;</span>
                  <span>{{ $readingTime }}</span>
                @endif
              </div>
            </div>
          </div>
        </div>

        {{-- Tags on left panel --}}
        @if($tags = get_the_tags())
          <div class="mt-8 flex flex-wrap gap-2">
            @foreach($tags as $tag)
              <a
                href="{{ get_tag_link($tag->term_id) }}"
                class="px-3 py-1 text-xs font-medium rounded-full bg-surface-primary text-text-secondary hover:bg-accent-subtle hover:text-accent transition-colors"
              >
                {{ $tag->name }}
              </a>
            @endforeach
          </div>
        @endif

        {{-- Featured image on left panel (desktop) --}}
        @if(has_post_thumbnail())
          <div class="hidden lg:block mt-10 rounded-xl overflow-hidden">
            {!! get_the_post_thumbnail(get_the_ID(), 'medium_large', [
              'class' => 'w-full h-auto',
              'loading' => 'eager',
              'decoding' => 'async',
              'fetchpriority' => 'high',
            ]) !!}
          </div>
        @endif
      </div>
    </div>

    {{-- Right panel: scrollable content --}}
    <div class="bg-surface-primary">
      {{-- Featured image for mobile (above content) --}}
      @if(has_post_thumbnail())
        <div class="lg:hidden px-6 pt-8">
          <div class="rounded-xl overflow-hidden">
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
      <div class="max-w-[700px] px-8 py-16 e-content">
        <div class="prose prose-lg max-w-none
          prose-headings:font-bold prose-headings:tracking-tight
          prose-a:text-accent prose-a:no-underline hover:prose-a:underline
          prose-img:rounded-xl">
          @php(the_content())
        </div>

        {{-- Pagination --}}
        @if($pagination)
          <div class="mt-8">
            <nav class="page-nav" aria-label="Page">
              {!! $pagination !!}
            </nav>
          </div>
        @endif
      </div>

      {{-- Post-content sections inside right column --}}
      <div class="px-8 pb-16 space-y-0">
        {{-- Social share --}}
        <div class="max-w-[700px] mx-auto px-6 mt-8">
    @includeWhen($singleShowSocialShare, 'partials.components.social-share')
  </div>

        {{-- Author box --}}
        <div class="max-w-[700px] mx-auto px-6 mt-10">
    @includeWhen($singleShowAuthorBox, 'partials.components.author-box')
  </div>
      </div>
    </div>

  {{-- Full-width sections below the split --}}
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
