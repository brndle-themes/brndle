@extends('layouts.app')

@php
  global $wp_query;
  $totalResults = (int) ($wp_query->found_posts ?? 0);
  $query = trim((string) get_search_query());
  $hasResults = have_posts();
@endphp

@section('content')
  <div class="brndle-search mx-auto max-w-7xl px-6 py-12 sm:py-16">
    {{-- Header --}}
    <header class="mb-10">
      <p class="text-xs font-semibold uppercase tracking-[0.2em] text-text-tertiary">
        {{ __('Search', 'brndle') }}
      </p>
      <h1 class="mt-2 text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-text-primary [text-wrap:balance]">
        @if($query !== '')
          {!! sprintf(
            /* translators: %s: search query, wrapped in <span> for accent styling */
            wp_kses(__('Results for %s', 'brndle'), ['span' => ['class' => true]]),
            '<span class="text-accent">&ldquo;' . esc_html($query) . '&rdquo;</span>'
          ) !!}
        @else
          {{ __('Search', 'brndle') }}
        @endif
      </h1>

      @if($hasResults && $query !== '')
        <p class="mt-3 text-sm text-text-tertiary">
          {{ sprintf(_n('%s result found', '%s results found', $totalResults, 'brndle'), number_format_i18n($totalResults)) }}
        </p>
      @endif

      {{-- Refined search form --}}
      <form role="search" method="get" action="{{ esc_url(home_url('/')) }}" class="mt-6 flex max-w-2xl flex-col sm:flex-row items-stretch gap-2">
        <label for="brndle-search-input" class="sr-only">{{ __('Search', 'brndle') }}</label>
        <div class="relative flex-1">
          <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-text-tertiary">
            <x-icon name="search" class="h-4 w-4" />
          </span>
          <input
            id="brndle-search-input"
            type="search"
            name="s"
            value="{{ esc_attr($query) }}"
            placeholder="{{ __('Search articles…', 'brndle') }}"
            class="w-full rounded-md border border-border-subtle bg-surface-primary py-2.5 pl-10 pr-4 text-text-primary placeholder:text-text-tertiary focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 transition-colors"
          />
        </div>
        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-accent px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90 transition-opacity">
          {{ __('Refine', 'brndle') }}
        </button>
      </form>
    </header>

    @if($hasResults)
      @include('partials.archive.list')

      <div class="mt-12">
        {!! get_the_posts_pagination([
          'mid_size' => 2,
          'prev_text' => __('&larr; Previous', 'brndle'),
          'next_text' => __('Next &rarr;', 'brndle'),
        ]) !!}
      </div>
    @else
      <div class="mx-auto max-w-2xl py-12 sm:py-16">
        <div class="rounded-xl border border-border-subtle bg-surface-secondary px-6 py-10 text-center">
          <p class="text-base sm:text-lg font-semibold text-text-primary">
            @if($query !== '')
              {!! sprintf(
                /* translators: %s: search query */
                wp_kses(__('No matches for %s', 'brndle'), ['span' => ['class' => true]]),
                '<span class="text-accent">&ldquo;' . esc_html($query) . '&rdquo;</span>'
              ) !!}
            @else
              {{ __('Try a different search', 'brndle') }}
            @endif
          </p>
          <p class="mt-2 text-sm text-text-secondary">
            {{ __('Check the spelling or try fewer / more general words.', 'brndle') }}
          </p>

          @php
            $popularCats = get_terms([
              'taxonomy' => 'category',
              'orderby' => 'count',
              'order' => 'DESC',
              'number' => 6,
              'hide_empty' => true,
            ]);
          @endphp
          @if(! is_wp_error($popularCats) && ! empty($popularCats))
            <div class="mt-8">
              <p class="text-xs font-semibold uppercase tracking-wider text-text-tertiary mb-3">
                {{ __('Or browse by topic', 'brndle') }}
              </p>
              <div class="flex flex-wrap items-center justify-center gap-2">
                @foreach($popularCats as $cat)
                  <a href="{{ esc_url(get_term_link($cat)) }}" class="inline-flex items-center px-3 py-1.5 rounded-full border border-border-subtle text-xs font-medium text-text-secondary hover:border-accent hover:text-accent transition-colors">
                    {{ esc_html($cat->name) }}
                  </a>
                @endforeach
              </div>
            </div>
          @endif

          <div class="mt-8">
            <a href="{{ esc_url(home_url('/')) }}" class="inline-flex items-center gap-2 text-sm font-medium text-text-secondary hover:text-accent transition-colors">
              <x-icon name="home" class="h-4 w-4" />
              {{ __('Back to home', 'brndle') }}
            </a>
          </div>
        </div>
      </div>
    @endif
  </div>
@endsection
