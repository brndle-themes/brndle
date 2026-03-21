{{-- Magazine Layout: Featured hero + grid (Linear/Apple pattern) --}}
@php $postIndex = 0; @endphp

@while(have_posts()) @php(the_post())
  @if($postIndex === 0)
    {{-- Hero / Featured Post --}}
    <article @php(post_class('group'))>
      <a href="{{ get_permalink() }}" class="block rounded-2xl overflow-hidden border border-[var(--color-surface-tertiary)] bg-[var(--color-surface-primary)] hover:shadow-lg transition-all duration-300">
        <div class="grid lg:grid-cols-2 gap-0">
          {{-- Image --}}
          <div class="aspect-[4/3] overflow-hidden">
            @include('partials.components.post-thumbnail', [
              'size' => 'brndle-hero',
              'priority' => true,
              'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
            ])
          </div>

          {{-- Content --}}
          <div class="p-8 lg:p-10 flex flex-col justify-center">
            @if($category = get_the_category())
              <span class="text-xs font-semibold uppercase tracking-wider text-[var(--color-accent)]">{{ $category[0]->name }}</span>
            @endif

            <h2 class="mt-3 text-3xl font-bold text-[var(--color-text-primary)] leading-tight group-hover:text-[var(--color-accent)] transition-colors">
              {!! get_the_title() !!}
            </h2>

            <p class="mt-4 text-[var(--color-text-secondary)] leading-relaxed">
              {!! get_the_excerpt() !!}
            </p>

            <div class="mt-6 flex items-center gap-3 text-sm text-[var(--color-text-tertiary)]">
              @if($avatar = get_avatar_url(get_the_author_meta('ID'), ['size' => 40]))
                <img src="{{ $avatar }}" alt="{{ get_the_author() }}" class="w-8 h-8 rounded-full" loading="lazy" decoding="async">
              @endif
              <span class="font-medium text-[var(--color-text-primary)]">{{ get_the_author() }}</span>
              <span>&middot;</span>
              <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
              <span>&middot;</span>
              <span>{{ $readingTime ?? '' }}</span>
            </div>
          </div>
        </div>
      </a>
    </article>

    {{-- Spacer before grid --}}
    <div class="mt-12 grid md:grid-cols-2 lg:grid-cols-3 gap-8">
  @else
    {{-- Remaining posts: grid cards --}}
    <article @php(post_class('group'))>
      <a href="{{ get_permalink() }}" class="block rounded-2xl overflow-hidden border border-[var(--color-surface-tertiary)] bg-[var(--color-surface-primary)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
        <div class="aspect-[3/2] overflow-hidden">
          @include('partials.components.post-thumbnail', [
            'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500',
          ])
        </div>

        <div class="p-6">
          @if($category = get_the_category())
            <span class="text-xs font-semibold uppercase tracking-wider text-[var(--color-accent)]">{{ $category[0]->name }}</span>
          @endif

          <h2 class="mt-2 text-lg font-bold text-[var(--color-text-primary)] leading-snug group-hover:text-[var(--color-accent)] transition-colors">
            {!! get_the_title() !!}
          </h2>

          <p class="mt-2 text-sm text-[var(--color-text-secondary)] leading-relaxed line-clamp-2">
            {!! get_the_excerpt() !!}
          </p>

          <div class="mt-4 flex items-center gap-3 text-xs text-[var(--color-text-tertiary)]">
            <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
            <span>&middot;</span>
            <span>{{ $readingTime ?? '' }}</span>
          </div>
        </div>
      </a>
    </article>
  @endif

  @php $postIndex++; @endphp
@endwhile

@if($postIndex > 1)
  </div>
@endif
