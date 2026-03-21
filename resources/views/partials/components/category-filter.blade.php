@php
  $categories = get_categories(['hide_empty' => true]);
  $currentCat = is_category() ? get_queried_object_id() : 0;
  $blogUrl = get_post_type_archive_link('post') ?: home_url('/');
@endphp

@if($categories)
  <nav aria-label="{{ __('Filter posts by category', 'brndle') }}" class="mb-8">
    <ul class="flex flex-wrap gap-2 list-none p-0 m-0">
      {{-- "All" pill --}}
      <li>
        <a
          href="{{ $blogUrl }}"
          class="inline-block px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200
            {{ ! $currentCat
              ? 'bg-[var(--color-accent)] text-white'
              : 'bg-[var(--color-surface-secondary)] text-[var(--color-text-secondary)] hover:bg-[var(--color-surface-tertiary)]'
            }}"
        >
          {{ __('All', 'brndle') }}
        </a>
      </li>

      {{-- Category pills --}}
      @foreach($categories as $cat)
        <li>
          <a
            href="{{ get_category_link($cat->term_id) }}"
            class="inline-block px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200
              {{ $currentCat === $cat->term_id
                ? 'bg-[var(--color-accent)] text-white'
                : 'bg-[var(--color-surface-secondary)] text-[var(--color-text-secondary)] hover:bg-[var(--color-surface-tertiary)]'
              }}"
          >
            {{ $cat->name }}
          </a>
        </li>
      @endforeach
    </ul>
  </nav>
@endif
