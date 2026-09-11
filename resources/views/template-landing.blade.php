{{--
  Template Name: Landing Page
  Description: Full-width landing page with AI-generated sections. Zero JavaScript.
--}}

@extends('layouts.landing')

@section('content')
  @while(have_posts())
    @php(the_post())
    {{--
      Loose text needs a column; blocks do not.

      This template renders the_content() for pages built from full-width
      section blocks, each of which brings its own container. Anything that is
      NOT such a block - a plain paragraph, a heading, a list, the whole body
      of a legal page - arrived with no container at all: flush to the
      viewport edge, no gutter, no rhythm, headings sitting on the paragraph
      above. The styles below give those elements a reading column and leave
      sections, figures and aligned blocks full width.

      Written as element rules inside :where() rather than utility classes
      because a class added here would not exist in the compiled stylesheet
      until the theme is rebuilt, and at zero specificity any block's own
      styles still win.
    --}}
    <style id="brndle-landing-flow">
      .brndle-landing-flow > :where(p, h1, h2, h3, h4, h5, h6, ul, ol, dl, blockquote, pre, table, hr) {
        max-width: 48rem;
        margin-inline: auto;
        padding-inline: 1.5rem;
      }
      .brndle-landing-flow > :where(p, ul, ol, dl, blockquote, pre, table) {
        margin-block: 1rem;
        line-height: 1.7;
      }
      .brndle-landing-flow > :where(h2) {
        margin-block: 2.5rem 0.75rem;
        font-size: 1.5rem;
        line-height: 1.3;
      }
      .brndle-landing-flow > :where(h3) {
        margin-block: 2rem 0.5rem;
        font-size: 1.25rem;
        line-height: 1.35;
      }
      .brndle-landing-flow > :where(h4, h5, h6) {
        margin-block: 1.5rem 0.5rem;
      }
      .brndle-landing-flow > :where(ul, ol) {
        padding-inline-start: 2.75rem;
        list-style-position: outside;
      }
      .brndle-landing-flow > :where(ul) { list-style-type: disc; }
      .brndle-landing-flow > :where(ol) { list-style-type: decimal; }
      .brndle-landing-flow > :where(li) { margin-block: 0.25rem; }
      .brndle-landing-flow > :where(hr) {
        margin-block: 2.5rem;
        border: 0;
        border-top: 1px solid color-mix(in srgb, currentColor 15%, transparent);
      }
      /* The first element should not collide with whatever section precedes it. */
      .brndle-landing-flow > :where(p, h2, h3):first-child { margin-top: 2.5rem; }
      /* And the last should not sit on the footer. */
      .brndle-landing-flow > :where(p, ul, ol, table):last-child { margin-bottom: 3rem; }
    </style>

    <div class="brndle-landing-flow">
      {!! the_content() !!}
    </div>
  @endwhile
@endsection
