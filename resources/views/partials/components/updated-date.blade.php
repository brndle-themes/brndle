{{--
  Last-updated date pill.

  Renders inline next to the publish date on single posts when:
  - the `single_show_updated_date` setting is on (default true), and
  - the modified time is at least 24h after the publish time.

  The 24h threshold filters out trivial saves immediately after
  publishing. Sites that want a tighter or looser threshold can override
  via the `brndle/updated_date_threshold_seconds` filter.

  This partial only writes the badge — it does not emit `dateModified`
  in JSON-LD; Yoast / RankMath own the schema.
--}}
@php
  $showUpdated = (bool) ($singleShowUpdatedDate ?? true);
  $threshold = (int) apply_filters('brndle/updated_date_threshold_seconds', DAY_IN_SECONDS);
  $publishedAt = (int) get_the_time('U');
  $modifiedAt = (int) get_the_modified_time('U');
  $shouldRender = $showUpdated && $publishedAt > 0 && ($modifiedAt - $publishedAt) > $threshold;
@endphp

@if($shouldRender)
  @if(! empty($withSeparator))
    <span aria-hidden="true">&middot;</span>
  @endif
  <span class="brndle-updated-date inline-flex items-center gap-1 text-xs text-text-tertiary {{ $extraClass ?? '' }}">
    <x-icon name="clock" class="h-3 w-3" />
    <span class="font-medium">{{ __('Updated', 'brndle') }}</span>
    <time class="dt-updated" datetime="{{ get_the_modified_date('c') }}">
      {{ get_the_modified_date() }}
    </time>
  </span>
@endif
