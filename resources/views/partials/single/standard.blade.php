{{--
  Standard single post layout — inspired by Vercel blog.
  Clean, modern, generous whitespace, 700px content column.
--}}

@includeWhen($singleShowProgressBar, 'partials.components.reading-progress')

<article @php(post_class('h-entry'))>

  {{-- Header --}}
  <header class="max-w-4xl mx-auto px-6 pt-20 pb-8">
    @if($category = get_the_category())
      <a href="{{ get_category_link($category[0]->term_id) }}"
         class="inline-block text-xs font-semibold uppercase tracking-wider text-accent hover:opacity-80 transition-opacity">
        {!! esc_html($category[0]->name) !!}
      </a>
    @endif

    <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-tight leading-[1.1] text-text-primary [text-wrap:balance] p-name">
      {{ $title }}
    </h1>

    <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-text-tertiary">
      <div class="flex items-center gap-2">
        @php($avatar = get_avatar_url(get_the_author_meta('ID'), ['size' => 40]))
        <img src="{{ $avatar }}" alt="{{ get_the_author() }}" class="w-8 h-8 rounded-full" loading="lazy" decoding="async">
        <a href="{{ get_author_posts_url(get_the_author_meta('ID')) }}"
           class="font-medium text-text-primary hover:text-accent transition-colors p-author h-card">
          {{ get_the_author() }}
        </a>
      </div>
      <span>&middot;</span>
      <time class="dt-published" datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
      @if($singleShowReadingTime)
        <span>&middot;</span>
        <span>{{ $readingTime }}</span>
      @endif
    </div>
  </header>

  {{-- Featured image --}}
  @if(has_post_thumbnail())
    <div class="max-w-5xl mx-auto px-6 mb-12">
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
      prose-a:text-accent prose-a:no-underline hover:prose-a:underline
      prose-img:rounded-xl
      prose-p:leading-relaxed
      prose-pre:bg-surface-inverse prose-pre:text-white/80
      prose-blockquote:border-l-[var(--color-accent)] prose-blockquote:text-text-secondary
      prose-code:text-accent
      prose-strong:text-text-primary">
      @php(the_content())
    </div>
  </div>

  {{-- Tags --}}
  @if($tags = get_the_tags())
    <div class="max-w-[700px] mx-auto px-6 mt-12 pt-8 border-t border-surface-tertiary">
      <div class="flex flex-wrap gap-2">
        @foreach($tags as $tag)
          <a href="{{ get_tag_link($tag->term_id) }}"
             class="px-3 py-1 text-xs font-medium rounded-full bg-surface-secondary text-text-secondary hover:bg-accent-subtle hover:text-accent transition-colors">
            {{ $tag->name }}
          </a>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Social share --}}
  @if($singleShowSocialShare)
    <div class="max-w-[700px] mx-auto px-6 mt-8">
      @include('partials.components.social-share')
    </div>
  @endif

  {{-- Author box --}}
  @if($singleShowAuthorBox)
    <div class="max-w-[700px] mx-auto px-6 mt-10">
      @include('partials.components.author-box')
    </div>
  @endif

  {{-- Post navigation --}}
  @if($singleShowPostNav)
    <div class="max-w-[700px] mx-auto px-6 mt-10">
      @include('partials.components.post-navigation')
    </div>
  @endif

  {{-- Related posts --}}
  @if($singleShowRelatedPosts)
    <div class="max-w-5xl mx-auto px-6 mt-16 mb-16">
      @include('partials.components.related-posts')
    </div>
  @endif

  {{-- Comments --}}
  <div class="max-w-[700px] mx-auto px-6 mb-16">
    @php(comments_template())
  </div>

</article>
