{{-- Editorial Layout: Text-only, chronological (Vercel blog pattern) --}}
<div class="space-y-0">
  @while(have_posts())
    @php(the_post())
    <article @php(post_class('py-6 border-b border-surface-tertiary last:border-b-0'))>
      <a href="{{ get_permalink() }}" class="block group">
        <h2 class="text-xl font-semibold text-text-primary group-hover:text-accent transition-colors">
          {!! get_the_title() !!}
        </h2>

        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-text-tertiary">
          <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
          @if($category = get_the_category())
            <span>&middot;</span>
            <span class="text-accent">{!! $category[0]->name !!}</span>
          @endif
        </div>

        <p class="mt-3 text-text-secondary leading-relaxed">
          {{ wp_strip_all_tags(get_the_excerpt()) }}
        </p>
      </a>
    </article>
  @endwhile
</div>
