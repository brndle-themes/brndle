@php
    $a = $attributes;
    $title = trim((string) ($a['title'] ?? ''));
    $items = array_values(array_filter(
        (array) ($a['items'] ?? []),
        fn ($item) => is_array($item) && (! empty($item['label']) || ! empty($item['content']))
    ));
    if (empty($items)) {
        return;
    }

    $displayMode = (string) ($a['displayMode'] ?? 'tabs');
    if (! in_array($displayMode, ['tabs', 'accordion'], true)) {
        $displayMode = 'tabs';
    }
    $tabsAlignment = (string) ($a['tabsAlignment'] ?? 'start');
    if (! in_array($tabsAlignment, ['start', 'center', 'end'], true)) {
        $tabsAlignment = 'start';
    }
    $accordionMode = (string) ($a['accordionMode'] ?? 'single');
    if (! in_array($accordionMode, ['single', 'multiple'], true)) {
        $accordionMode = 'single';
    }
    $accordionDefault = (string) ($a['accordionDefault'] ?? 'closed');
    if (! in_array($accordionDefault, ['closed', 'first', 'all'], true)) {
        $accordionDefault = 'closed';
    }

    $align = (string) ($a['align'] ?? '');
    $anchor = (string) ($a['anchor'] ?? '');
    $uniqueId = (string) ($a['uniqueId'] ?? '');
    $idBase = $uniqueId !== '' ? $uniqueId : substr(md5((string) microtime(true)), 0, 8);

    $extraClass = "is-mode-{$displayMode}";
    if ($displayMode === 'tabs') {
        $extraClass .= " is-align-{$tabsAlignment}";
    } else {
        $extraClass .= " is-acc-{$accordionMode} is-default-{$accordionDefault}";
    }

    $allowedContent = ['strong' => [], 'em' => [], 'b' => [], 'i' => [], 'a' => ['href' => true, 'target' => true, 'rel' => true], 'br' => [], 'code' => []];

    $extraAttrs = [];
    if ($displayMode === 'accordion') {
        $extraAttrs['data-mode'] = $accordionMode;
        $extraAttrs['data-default'] = $accordionDefault;
    }
@endphp

<x-block-wrapper :block="'tabs-accordion'" :unique-id="$uniqueId" :align="$align" :anchor="$anchor" :extra-class="$extraClass" :attrs="$extraAttrs">
    @if($title !== '')
        <h2 class="brndle-tabs-accordion__heading">{{ $title }}</h2>
    @endif

    @if($displayMode === 'tabs')
        @if(count($items) > 1)
            <div role="tablist" aria-orientation="horizontal" class="brndle-tabs-accordion__tablist">
                @foreach($items as $i => $item)
                    @php
                        $tabId = "brndle-ta-{$idBase}-tab-{$i}";
                        $panelId = "brndle-ta-{$idBase}-panel-{$i}";
                        $active = $i === 0;
                    @endphp
                    <button
                        type="button"
                        role="tab"
                        id="{{ $tabId }}"
                        aria-controls="{{ $panelId }}"
                        aria-selected="{{ $active ? 'true' : 'false' }}"
                        tabindex="{{ $active ? '0' : '-1' }}"
                        class="brndle-tabs-accordion__tab"
                    >{{ $item['label'] ?? '' }}</button>
                @endforeach
            </div>
        @endif
        <div class="brndle-tabs-accordion__panels">
            @foreach($items as $i => $item)
                @php
                    $tabId = "brndle-ta-{$idBase}-tab-{$i}";
                    $panelId = "brndle-ta-{$idBase}-panel-{$i}";
                    $active = $i === 0;
                @endphp
                <div
                    role="tabpanel"
                    id="{{ $panelId }}"
                    aria-labelledby="{{ $tabId }}"
                    tabindex="0"
                    class="brndle-tabs-accordion__panel"
                    @if(! $active) hidden @endif
                >
                    @if(! empty($item['content']))
                        <div class="brndle-tabs-accordion__content">
                            {!! wp_kses(wpautop((string) $item['content']), array_merge($allowedContent, ['p' => []])) !!}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="brndle-tabs-accordion__accordion">
            @foreach($items as $i => $item)
                @php
                    $btnId = "brndle-ta-{$idBase}-acc-btn-{$i}";
                    $panelId = "brndle-ta-{$idBase}-acc-panel-{$i}";
                    $expanded = ($accordionDefault === 'all') || ($accordionDefault === 'first' && $i === 0);
                @endphp
                <div class="brndle-tabs-accordion__acc-item{{ $expanded ? ' is-open' : '' }}">
                    <h3 class="brndle-tabs-accordion__acc-heading">
                        <button
                            type="button"
                            id="{{ $btnId }}"
                            aria-controls="{{ $panelId }}"
                            aria-expanded="{{ $expanded ? 'true' : 'false' }}"
                            class="brndle-tabs-accordion__acc-trigger"
                        >
                            <span class="brndle-tabs-accordion__acc-label">{{ $item['label'] ?? '' }}</span>
                            <span class="brndle-tabs-accordion__acc-icon" aria-hidden="true">
                                <x-icon name="chevron-down" class="brndle-tabs-accordion__acc-chevron" />
                            </span>
                        </button>
                    </h3>
                    <div
                        id="{{ $panelId }}"
                        role="region"
                        aria-labelledby="{{ $btnId }}"
                        class="brndle-tabs-accordion__acc-panel"
                        @if(! $expanded) hidden @endif
                    >
                        @if(! empty($item['content']))
                            <div class="brndle-tabs-accordion__content">
                                {!! wp_kses(wpautop((string) $item['content']), array_merge($allowedContent, ['p' => []])) !!}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-block-wrapper>
