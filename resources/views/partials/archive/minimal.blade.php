{{-- Minimal Layout: Author-forward, clean typography (Medium pattern) --}}
<div class="max-w-3xl mx-auto space-y-10">
  @while(have_posts())
    @php(the_post())
    <article @php(post_class('group pb-10 border-b border-surface-tertiary last:border-b-0'))>
      <a href="{{ get_permalink() }}" class="block">
        {{-- Author row --}}
        <div class="flex items-center gap-3 mb-3">
          @if($avatar = get_avatar_url(get_the_author_meta('ID'), ['size' => 32]))
            <img src="{{ $avatar }}" alt="{{ get_the_author() }}" class="w-8 h-8 rounded-full" loading="lazy" decoding="async">
          @endif
          <span class="text-sm font-medium text-text-primary">{{ get_the_author() }}</span>
        </div>

        {{-- Title + content --}}
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="flex-1 min-w-0">
            <h2 class="text-xl font-bold text-text-primary leading-snug group-hover:text-accent transition-colors">
              {!! get_the_title() !!}
            </h2>

            <p class="mt-2 text-base text-text-secondary leading-relaxed line-clamp-3">
              {!! get_the_excerpt() !!}
            </p>

            {{-- Meta --}}
            <div class="mt-4 flex flex-wrap items-center gap-2 text-sm text-text-tertiary">
              <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
              <span>&middot;</span>
              <span>{{ $readingTime ?? '' }}</span>
              @if($category = get_the_category())
                <span>&middot;</span>
                <span class="px-2 py-0.5 rounded-full bg-surface-secondary text-xs text-text-secondary">{{ $category[0]->name }}</span>
              @endif
            </div>
          </div>

          {{-- Optional small inline thumbnail --}}
          @if(has_post_thumbnail())
            <div class="w-28 h-28 shrink-0 rounded-lg overflow-hidden hidden sm:block">
              @include('partials.components.post-thumbnail', [
                'size' => 'thumbnail',
                'class' => 'w-full h-full object-cover',
              ])
            </div>
          @endif
        </div>
      </a>
    </article>
  @endwhile
</div>
