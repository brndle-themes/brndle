@extends('layouts.app')

@section('content')
  @php
    // News-portal sections layout (1.5.0). Activates when:
    //   1. The blog index IS the front page (not a static page).
    //   2. The admin opt-in toggle is on.
    // The empty-sections case is handled by auto-defaults inside the
    // sections partial (1.5.2), so we don't gate on `! empty()` anymore.
    // Falls through to the regular archive layout otherwise.
    $useHomepageSections = is_home()
      && (int) get_option('page_for_posts') === 0
      && (bool) \Brndle\Settings\Settings::get('homepage_sections_enabled', false);

    $allowedArchive = ['grid', 'list', 'magazine', 'editorial', 'minimal'];
    $layout = in_array($archiveLayout, $allowedArchive) ? $archiveLayout : 'grid';
  @endphp

  <div class="max-w-7xl mx-auto px-6 pt-32 pb-16">
    {{-- Hide the "Latest Posts" header + category filter when sections
         layout is active. Each section renders its own category heading,
         and a duplicate "Latest Posts" title above the magazine flow
         reads as visual noise. --}}
    @unless ($useHomepageSections)
      @include('partials.page-header')

      @if($archiveShowCategoryFilter)
        @include('partials.components.category-filter')
      @endif
    @endunless

    @if(! have_posts())
      <div class="py-12 text-center">
        <p class="text-lg text-text-secondary">{{ __('No posts found.', 'brndle') }}</p>
        {!! get_search_form(false) !!}
      </div>
    @else
      @if ($useHomepageSections)
        @include('partials.archive.sections')
      @else
        @include('partials.archive.' . $layout)
      @endif
    @endif

    {{-- Pagination only makes sense for the regular archive view; the
         sections layout shows curated post counts per category and routes
         pagination to the per-category archives via "View all" links. --}}
    @unless ($useHomepageSections)
      <div class="mt-16">
        {!! get_the_posts_pagination([
          'mid_size' => 2,
          'prev_text' => __('&larr; Previous', 'brndle'),
          'next_text' => __('Next &rarr;', 'brndle'),
        ]) !!}
      </div>
    @endunless
  </div>
@endsection
