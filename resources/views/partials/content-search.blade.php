<article @php(post_class('group'))>
  <a href="{{ get_permalink() }}" class="block p-6 rounded-2xl border border-slate-200 bg-white hover:shadow-md hover:border-slate-300 transition-all">
    <h2 class="text-lg font-bold text-text-primary group-hover:text-accent transition-colors">
      {!! $title !!}
    </h2>

    <p class="mt-2 text-sm text-text-secondary leading-relaxed line-clamp-2">
      @php(the_excerpt())
    </p>

    <div class="mt-3 flex items-center gap-2 text-xs text-text-tertiary">
      @includeWhen(get_post_type() === 'post', 'partials.entry-meta')
    </div>
  </a>
</article>
