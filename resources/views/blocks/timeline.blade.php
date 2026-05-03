@php
    $a = $attributes;
    $title = trim((string) ($a['title'] ?? ''));
    $items = array_values(array_filter(
        (array) ($a['items'] ?? []),
        fn ($item) => is_array($item) && (
            ! empty($item['date']) || ! empty($item['title']) || ! empty($item['description'])
        )
    ));
    if (empty($items)) {
        return;
    }

    $iconStyle = (string) ($a['iconStyle'] ?? 'dot');
    if (! in_array($iconStyle, ['dot', 'numbered', 'lucide'], true)) {
        $iconStyle = 'dot';
    }
    $connector = (string) ($a['connector'] ?? 'solid');
    if (! in_array($connector, ['solid', 'dashed', 'none'], true)) {
        $connector = 'solid';
    }
    $density = (string) ($a['density'] ?? 'comfortable');
    if (! in_array($density, ['comfortable', 'compact'], true)) {
        $density = 'comfortable';
    }
    $align = (string) ($a['align'] ?? '');
    $anchor = (string) ($a['anchor'] ?? '');
    $uniqueId = (string) ($a['uniqueId'] ?? '');

    $extraClass = "is-icon-{$iconStyle} is-connector-{$connector} is-density-{$density}";
@endphp

<x-block-wrapper :block="'timeline'" :unique-id="$uniqueId" :align="$align" :anchor="$anchor" :extra-class="$extraClass">
    @if($title !== '')
        <h2 class="brndle-timeline__heading">{{ $title }}</h2>
    @endif
    <ol class="brndle-timeline__list">
        @foreach($items as $i => $item)
            <li class="brndle-timeline__item" data-index="{{ $i + 1 }}">
                <div class="brndle-timeline__node" aria-hidden="true">
                    @if($iconStyle === 'numbered')
                        <span class="brndle-timeline__number">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    @elseif($iconStyle === 'lucide' && ! empty($item['icon']))
                        <span class="brndle-timeline__icon-wrap">
                            <x-icon :name="$item['icon']" class="brndle-timeline__icon" />
                        </span>
                    @else
                        <span class="brndle-timeline__dot"></span>
                    @endif
                </div>
                <div class="brndle-timeline__content">
                    @if(! empty($item['date']))
                        <span class="brndle-timeline__date">{{ $item['date'] }}</span>
                    @endif
                    @if(! empty($item['title']))
                        <h3 class="brndle-timeline__title">{{ $item['title'] }}</h3>
                    @endif
                    @if(! empty($item['description']))
                        <p class="brndle-timeline__description">{{ $item['description'] }}</p>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</x-block-wrapper>
