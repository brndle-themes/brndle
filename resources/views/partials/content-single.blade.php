{{-- Single blog post --}}
<article @php(post_class('h-entry'))>
  {{-- Hero area --}}
  <div class="max-w-4xl mx-auto px-6 pt-16 pb-8">
    {{-- Category --}}
    @if($category = get_the_category())
      <a href="{{ get_category_link($category[0]->term_id) }}" class="inline-block text-xs font-semibold uppercase tracking-wider text-accent hover:text-accent-dark transition-colors">
        {{ $category[0]->name }}
      </a>
    @endif

    <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-tight text-text-primary leading-[1.1] p-name">
      {!! $title !!}
    </h1>

    {{-- Meta --}}
    <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-text-tertiary">
      <div class="flex items-center gap-2">
        @php($avatar = get_avatar_url(get_the_author_meta('ID'), ['size' => 40]))
        <img src="{{ $avatar }}" alt="{{ get_the_author() }}" class="w-8 h-8 rounded-full" loading="lazy" decoding="async">
        <a href="{{ get_author_posts_url(get_the_author_meta('ID')) }}" class="font-medium text-text-primary hover:text-accent transition-colors p-author h-card">
          {{ get_the_author() }}
        </a>
      </div>
      <span>&middot;</span>
      <time class="dt-published" datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
      <span>&middot;</span>
      <span>{{ $readingTime }}</span>
    </div>
  </div>

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
  <div class="max-w-3xl mx-auto px-6 e-content">
    <div class="prose prose-lg prose-slate max-w-none
      prose-headings:font-bold prose-headings:tracking-tight
      prose-a:text-accent prose-a:no-underline hover:prose-a:underline
      prose-img:rounded-xl
      prose-pre:bg-slate-950 prose-pre:text-slate-300
      prose-blockquote:border-l-accent prose-blockquote:text-text-secondary">
      @php(the_content())
    </div>
  </div>

  {{-- Pagination --}}
  @if($pagination())
    <div class="max-w-3xl mx-auto px-6 mt-8">
      <nav class="page-nav" aria-label="Page">
        {!! $pagination !!}
      </nav>
    </div>
  @endif

  {{-- Tags --}}
  @if($tags = get_the_tags())
    <div class="max-w-3xl mx-auto px-6 mt-12 pt-8 border-t border-slate-200">
      <div class="flex flex-wrap gap-2">
        @foreach($tags as $tag)
          <a href="{{ get_tag_link($tag->term_id) }}" class="px-3 py-1 text-xs font-medium rounded-full bg-surface-secondary text-text-secondary hover:bg-accent-subtle hover:text-accent transition-colors">
            {{ $tag->name }}
          </a>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Author box --}}
  <div class="max-w-3xl mx-auto px-6 mt-12 mb-16">
    <div class="p-6 rounded-2xl bg-surface-secondary border border-slate-200">
      <div class="flex items-start gap-4">
        <img src="{{ get_avatar_url(get_the_author_meta('ID'), ['size' => 64]) }}" alt="{{ get_the_author() }}" class="w-14 h-14 rounded-full shrink-0" loading="lazy" decoding="async">
        <div>
          <a href="{{ get_author_posts_url(get_the_author_meta('ID')) }}" class="text-base font-bold text-text-primary hover:text-accent transition-colors">
            {{ get_the_author() }}
          </a>
          @if($bio = get_the_author_meta('description'))
            <p class="mt-1 text-sm text-text-secondary leading-relaxed">{{ $bio }}</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Comments --}}
  @php(comments_template())
</article>
