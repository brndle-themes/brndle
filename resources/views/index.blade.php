@extends('layouts.app')

@section('content')
  <div class="max-w-7xl mx-auto px-6 pt-32 pb-16">
    @include('partials.page-header')

    @if($archiveShowCategoryFilter)
      @include('partials.components.category-filter')
    @endif

    @if(! have_posts())
      <div class="py-12 text-center">
        <p class="text-lg text-text-secondary">{{ __('No posts found.', 'brndle') }}</p>
        {!! get_search_form(false) !!}
      </div>
    @else
      @php
        $allowedArchive = ['grid', 'list', 'magazine', 'editorial', 'minimal'];
        $layout = in_array($archiveLayout, $allowedArchive) ? $archiveLayout : 'grid';
      @endphp
      @include('partials.archive.' . $layout)
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
