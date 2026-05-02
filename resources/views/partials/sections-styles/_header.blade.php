{{--
  Shared section header (category title + "view all" link).
  Used by every sections-styles/*.blade.php partial.

  Inputs:
    - $sectionCategory      WP_Term
    - $sectionShowTitle     bool
    - $sectionShowViewAll   bool
    - $sectionNumber        string|null  Two-digit kicker like "02"

  The kicker number gives each section a magazine-style hierarchy
  mark. Falls back to no kicker when the wrapper does not pass one
  (e.g. when a partial is included from a non-homepage context).
--}}
@if ($sectionShowTitle || $sectionShowViewAll)
  <header class="brndle-section-header flex items-end justify-between gap-4 mb-6 lg:mb-8 pt-2">
    @if ($sectionShowTitle)
      <div class="flex items-baseline gap-3 lg:gap-4 min-w-0">
        @if (! empty($sectionNumber ?? null))
          <span aria-hidden="true"
                class="brndle-section-kicker shrink-0 text-2xl lg:text-3xl font-black tabular-nums tracking-tight text-text-tertiary leading-none">
            {{ $sectionNumber }}
          </span>
          <span aria-hidden="true" class="shrink-0 h-px w-8 lg:w-12 bg-text-tertiary/30 self-center"></span>
        @endif
        <h2 class="text-2xl lg:text-3xl font-bold text-text-primary tracking-tight truncate">
          {!! esc_html($sectionCategory->name) !!}
        </h2>
      </div>
    @endif

    @if ($sectionShowViewAll)
      <a href="{{ esc_url(get_category_link($sectionCategory)) }}"
         class="shrink-0 text-sm font-semibold text-accent hover:text-accent/80 transition-colors inline-flex items-center gap-1.5">
        {{ __('View all', 'brndle') }}
        <span aria-hidden="true">&rarr;</span>
      </a>
    @endif
  </header>
@endif
