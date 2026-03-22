{{-- List Layout: Horizontal cards, single column (Notion/Intercom pattern) --}}
<div class="space-y-8">
  @while(have_posts())
    @php(the_post())
    <article @php(post_class('group'))>
      <a href="{{ get_permalink() }}" class="flex flex-col md:flex-row gap-6 pb-8 border-b border-surface-tertiary last:border-b-0">
        {{-- Thumbnail --}}
        <div class="w-full md:w-72 shrink-0 aspect-[16/9] rounded-xl overflow-hidden">
          @include('partials.components.post-thumbnail', [
            'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
          ])
        </div>

        {{-- Content --}}
        <div class="flex flex-col justify-center min-w-0">
          {{-- Category --}}
          @if($category = get_the_category())
            <span class="text-xs font-semibold uppercase tracking-wider text-accent">{!! esc_html($category[0]->name) !!}</span>
          @endif

          <h2 class="mt-2 text-xl font-bold text-text-primary leading-snug group-hover:text-accent transition-colors">
            {!! get_the_title() !!}
          </h2>

          <p class="mt-2 text-sm text-text-secondary leading-relaxed line-clamp-3">
            {!! get_the_excerpt() !!}
          </p>

          <div class="mt-4 flex items-center gap-3 text-xs text-text-tertiary">
            @if($avatar = get_avatar_url(get_the_author_meta('ID'), ['size' => 32]))
              <img src="{{ $avatar }}" alt="{{ get_the_author() }}" class="w-6 h-6 rounded-full" loading="lazy" decoding="async">
            @endif
            <span class="font-medium text-text-primary">{{ get_the_author() }}</span>
            <span>&middot;</span>
            <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
            <span>&middot;</span>
            <span>{{ $readingTime ?? '' }}</span>
          </div>
        </div>
      </a>
    </article>
  @endwhile
</div>
