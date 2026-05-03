@php
    $a = $attributes;
    $quote = trim((string) ($a['quote'] ?? ''));
    if ($quote === '') {
        return;
    }

    $cite = trim((string) ($a['cite'] ?? ''));
    $citeUrl = trim((string) ($a['citeUrl'] ?? ''));
    $variant = (string) ($a['variant'] ?? 'bordered-left');
    if (! in_array($variant, ['bordered-left', 'centered-large', 'outset'], true)) {
        $variant = 'bordered-left';
    }
    $accentColor = (string) ($a['accentColor'] ?? 'accent');
    if (! in_array($accentColor, ['accent', 'text-primary', 'text-tertiary'], true)) {
        $accentColor = 'accent';
    }
    $align = (string) ($a['align'] ?? '');
    $anchor = (string) ($a['anchor'] ?? '');
    $uniqueId = (string) ($a['uniqueId'] ?? '');

    $extraClass = 'is-' . $variant . ' is-accent-' . $accentColor;

    $allowedQuote = ['strong' => [], 'em' => [], 'b' => [], 'i' => [], 'br' => [], 'a' => ['href' => true, 'target' => true, 'rel' => true]];
    $allowedCite = ['strong' => [], 'em' => [], 'b' => [], 'i' => []];
@endphp

<x-block-wrapper :block="'pull-quote'" :unique-id="$uniqueId" :align="$align" :anchor="$anchor" :extra-class="$extraClass">
    <figure class="brndle-pull-quote__figure">
        @if($variant === 'centered-large')
            <span class="brndle-pull-quote__glyph" aria-hidden="true">
                <x-icon name="quote" class="brndle-pull-quote__glyph-icon" />
            </span>
        @endif
        <blockquote class="brndle-pull-quote__body">
            <p class="brndle-pull-quote__text">{!! wp_kses($quote, $allowedQuote) !!}</p>
        </blockquote>
        @if($cite !== '')
            <figcaption class="brndle-pull-quote__cite">
                @if($citeUrl !== '')
                    <a class="brndle-pull-quote__cite-link" href="{{ esc_url($citeUrl) }}" rel="nofollow noopener">{!! wp_kses($cite, $allowedCite) !!}</a>
                @else
                    {!! wp_kses($cite, $allowedCite) !!}
                @endif
            </figcaption>
        @endif
    </figure>
</x-block-wrapper>
