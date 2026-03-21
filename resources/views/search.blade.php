@extends('layouts.app')

@section('content')
  <div class="max-w-7xl mx-auto px-6 py-16">
    @include('partials.page-header')

    @if(! have_posts())
      <div class="py-12 text-center">
        <p class="text-lg text-[var(--color-text-secondary)]">{{ __('No results found.', 'brndle') }}</p>
        {!! get_search_form(false) !!}
      </div>
    @else
      @include('partials.archive.list')
    @endif

    <div class="mt-16">
      {!! get_the_posts_pagination([
        'mid_size' => 2,
        'prev_text' => __('&larr; Previous', 'brndle'),
        'next_text' => __('Next &rarr;', 'brndle'),
      ]) !!}
    </div>
  </div>
@endsection
