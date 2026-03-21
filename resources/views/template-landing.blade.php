{{--
  Template Name: Landing Page
  Description: Full-width landing page with AI-generated sections. Zero JavaScript.
--}}

@extends('layouts.landing')

@section('content')
  @while(have_posts())
    @php the_post() @endphp
    {!! the_content() !!}
  @endwhile
@endsection
