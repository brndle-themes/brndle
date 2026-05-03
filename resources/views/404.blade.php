@extends('layouts.app')

@php
  $recent = get_posts([
    'numberposts' => 4,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => true,
    'suppress_filters' => false,
  ]);
@endphp

@section('content')
  <div class="brndle-404 mx-auto max-w-5xl px-6 py-16 sm:py-24">
    {{-- Hero --}}
    <div class="text-center">
      <p class="text-sm font-semibold uppercase tracking-[0.25em] text-accent">404</p>
      <h1 class="mt-3 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-text-primary [text-wrap:balance]">
        {{ __('We can\'t find that page', 'brndle') }}
      </h1>
      <p class="mx-auto mt-4 max-w-xl text-base sm:text-lg text-text-secondary [text-wrap:pretty]">
        {{ __('The link may be broken or the page moved. Try a search or browse the latest articles below.', 'brndle') }}
      </p>

      {{-- Search form --}}
      <form role="search" method="get" action="{{ esc_url(home_url('/')) }}" class="mx-auto mt-8 flex max-w-xl flex-col sm:flex-row items-stretch gap-2">
        <label for="brndle-404-search" class="sr-only">{{ __('Search', 'brndle') }}</label>
        <div class="relative flex-1">
          <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-text-tertiary">
            <x-icon name="search" class="h-4 w-4" />
          </span>
          <input
            id="brndle-404-search"
            type="search"
            name="s"
            value="{{ get_search_query() }}"
            placeholder="{{ __('Search articles…', 'brndle') }}"
            class="w-full rounded-md border border-border-subtle bg-surface-primary py-3 pl-10 pr-4 text-text-primary placeholder:text-text-tertiary focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 transition-colors"
          />
        </div>
        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-accent px-5 py-3 text-sm font-semibold text-white hover:opacity-90 transition-opacity">
          {{ __('Search', 'brndle') }}
        </button>
      </form>

      {{-- Primary CTA --}}
      <div class="mt-6">
        <a href="{{ esc_url(home_url('/')) }}" class="inline-flex items-center gap-2 text-sm font-medium text-text-secondary hover:text-accent transition-colors">
          <x-icon name="home" class="h-4 w-4" />
          {{ __('Back to home', 'brndle') }}
        </a>
      </div>
    </div>

    {{-- Recent posts --}}
    @if(! empty($recent))
      <div class="mt-16 sm:mt-20">
        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-text-tertiary text-center mb-6">
          {{ __('Latest articles', 'brndle') }}
        </h2>
        <ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          @foreach($recent as $post)
            @php(setup_postdata($post))
            <li>
              <a href="{{ esc_url(get_permalink($post)) }}" class="group block h-full rounded-xl border border-surface-tertiary bg-surface-primary overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                @if(has_post_thumbnail($post))
                  <div class="aspect-[4/3] overflow-hidden bg-surface-secondary">
                    {!! get_the_post_thumbnail($post, 'medium', ['class' => 'h-full w-full object-cover transition-transform duration-500 group-hover:scale-105', 'loading' => 'lazy']) !!}
                  </div>
                @endif
                <div class="p-4">
                  @php($cats = get_the_category($post->ID))
                  @if(! empty($cats))
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-accent">{!! esc_html($cats[0]->name) !!}</span>
                  @endif
                  <h3 class="mt-1 text-sm sm:text-base font-bold text-text-primary leading-snug group-hover:text-accent transition-colors line-clamp-3">
                    {!! get_the_title($post) !!}
                  </h3>
                  <time datetime="{{ get_post_time('c', true, $post) }}" class="mt-2 block text-xs text-text-tertiary">
                    {{ get_the_date('', $post) }}
                  </time>
                </div>
              </a>
            </li>
          @endforeach
        </ul>
        @php(wp_reset_postdata())
      </div>
    @endif
  </div>
@endsection
