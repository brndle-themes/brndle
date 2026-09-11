{{--
  <x-block-wrapper> — shared outer wrapper for v2.1 editorial blocks.

  Purpose: standardize the per-instance scope id, alignment, anchor, and
  base class on every editorial block so:
    - per-instance CSS scoping (`#brndle-{block}-{uniqueId}`) just works
    - print / view-transitions selectors stay simple
    - the markup shape is identical across the suite

  Props (all optional):
    - block         Block slug, e.g. 'code'. Becomes part of the class
                    name (`brndle-{block}`).
    - uniqueId      8-char client-id slice from useUniqueId(). Used for
                    the wrapper id when present.
    - align         WP alignment ('wide' | 'full' | '' …). Mapped to
                    'alignwide' / 'alignfull' so theme.json layout
                    constraints take over.
    - anchor        Block anchor (from supports.anchor). Wins over the
                    auto-generated id when both are set.
    - extraClass    Free-form additional classes.
    - attrs         Extra HTML attributes (associative array).
--}}
@php
    $blockSlug   = $block ?? 'block';
    $uid         = (string) ($uniqueId ?? '');
    $align       = (string) ($align ?? '');
    $anchor      = (string) ($anchor ?? '');
    $extraClass  = (string) ($extraClass ?? '');
    $attrs       = (array)  ($attrs ?? []);

    // px-6 matches the gutter every section-based block carries. Without it
    // these four blocks (code, pull-quote, tabs-accordion, timeline) render
    // flush to the viewport edge on a phone, because the wrapper only ever
    // set margin-block - a max-width alone does nothing once the screen is
    // narrower than the max.
    $classes = ['brndle-' . $blockSlug, 'px-6'];
    if ($align === 'wide') {
        $classes[] = 'alignwide';
    } elseif ($align === 'full') {
        $classes[] = 'alignfull';
    }
    if ($extraClass !== '') {
        $classes[] = $extraClass;
    }

    $autoId = $uid !== '' ? "brndle-{$blockSlug}-{$uid}" : '';
    $finalId = $anchor !== '' ? $anchor : $autoId;

    $attrString = '';
    foreach ($attrs as $k => $v) {
        $attrString .= ' ' . esc_attr($k) . '="' . esc_attr($v) . '"';
    }
@endphp
<div class="{{ implode(' ', $classes) }}"@if($finalId !== '') id="{{ $finalId }}"@endif{!! $attrString !!}>
    {{ $slot }}
</div>
