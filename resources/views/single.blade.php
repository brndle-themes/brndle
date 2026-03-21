@extends('layouts.app')

@section('content')
  @while(have_posts())
    @php(the_post())
    @php
      $allowedSingle = ['standard', 'hero-immersive', 'sidebar', 'editorial', 'cinematic', 'presentation', 'split', 'minimal-dark'];
      $layout = in_array($singleLayout, $allowedSingle) ? $singleLayout : 'standard';
    @endphp
    @include('partials.single.' . $layout)
  @endwhile
@endsection
