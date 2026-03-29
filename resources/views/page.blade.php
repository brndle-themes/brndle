@extends('layouts.app')

@section('content')
  @while(have_posts())
    @php(the_post())
    <article @php(post_class())>
      <div class="max-w-4xl mx-auto px-6 pt-20 pb-24">
        <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-text-primary [text-wrap:balance]">
          {{ get_the_title() }}
        </h1>

        <div class="mt-10 prose prose-lg max-w-none
          prose-headings:font-bold prose-headings:tracking-tight
          prose-a:text-accent prose-a:no-underline hover:prose-a:underline
          prose-img:rounded-xl
          prose-p:text-text-secondary prose-p:leading-relaxed">
          @php(the_content())
        </div>
      </div>
    </article>
  @endwhile
@endsection
