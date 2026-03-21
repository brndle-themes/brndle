@extends('layouts.app')

@section('content')
  @while(have_posts())
    @php the_post() @endphp
    <div class="max-w-4xl mx-auto px-6 py-16">
      @include('partials.page-header')
      @includeFirst(['partials.content-page', 'partials.content'])
    </div>
  @endwhile
@endsection
