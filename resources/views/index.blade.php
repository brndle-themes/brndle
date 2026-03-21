@extends('layouts.app')

@section('content')
  <div class="max-w-7xl mx-auto px-6 py-16">
    @include('partials.page-header')

    @if(! have_posts())
      <div class="py-12 text-center">
        <p class="text-lg text-text-secondary">{{ __('No posts found.', 'brndle') }}</p>
        {!! get_search_form(false) !!}
      </div>
    @endif

    <div class="mt-12 grid md:grid-cols-2 lg:grid-cols-3 gap-8">
      @while(have_posts()) @php(the_post())
        @includeFirst(['partials.content-' . get_post_type(), 'partials.content'])
      @endwhile
    </div>

    <div class="mt-16">
      {!! get_the_posts_navigation() !!}
    </div>
  </div>
@endsection
