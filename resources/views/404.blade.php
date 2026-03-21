@extends('layouts.app')

@section('content')
  <div class="max-w-2xl mx-auto px-6 py-32 text-center">
    <p class="text-sm font-semibold text-accent uppercase tracking-widest">404</p>
    <h1 class="mt-4 text-5xl font-bold tracking-tight text-text-primary">Page not found</h1>
    <p class="mt-4 text-lg text-text-secondary">{{ __("Sorry, we couldn't find the page you're looking for.", 'brndle') }}</p>
    <a href="{{ home_url('/') }}" class="mt-8 inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition-colors">
      {{ __('Go home', 'brndle') }}
    </a>
  </div>
@endsection
