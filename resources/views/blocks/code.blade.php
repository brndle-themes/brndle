@php
    use Brndle\Blocks\Helpers as BlockHelpers;

    $a = $attributes;
    $code = (string) ($a['code'] ?? '');
    if ($code === '') {
        return;
    }

    $language = (string) ($a['language'] ?? 'plain');
    $allowedLanguages = ['plain','bash','css','diff','dockerfile','html','js','json','jsx','markdown','nginx','php','python','scss','shell','sql','ts','tsx','yaml'];
    if (! in_array($language, $allowedLanguages, true)) {
        $language = 'plain';
    }
    // hljs has no class for "plain" — drop the language- prefix in that case
    // so the highlighter doesn't try to parse the snippet.
    $hljsClass = $language === 'plain' ? '' : 'language-' . $language;
    $showLineNumbers = ! empty($a['showLineNumbers']);
    $showCopy = ! isset($a['showCopy']) || $a['showCopy'] !== false;
    $theme = (string) ($a['theme'] ?? 'auto');
    if (! in_array($theme, ['auto', 'light', 'dark'], true)) {
        $theme = 'auto';
    }
    $caption = trim((string) ($a['caption'] ?? ''));
    $align = (string) ($a['align'] ?? '');
    $anchor = (string) ($a['anchor'] ?? '');
    $uniqueId = (string) ($a['uniqueId'] ?? '');

    $extra = ['data-brndle-code' => '', 'data-language' => $language, 'data-theme' => $theme];
@endphp

<x-block-wrapper :block="'code'" :unique-id="$uniqueId" :align="$align" :anchor="$anchor" :attrs="$extra">
    <figure class="brndle-code__figure">
        <div class="brndle-code__frame{{ $showLineNumbers ? ' has-line-numbers' : '' }}">
            @if($showCopy)
                <button
                    type="button"
                    class="brndle-code__copy"
                    data-brndle-code-copy
                    aria-label="{{ esc_attr__('Copy code to clipboard', 'brndle') }}"
                >
                    <span class="brndle-code__copy-default" aria-hidden="true">
                        <x-icon name="copy" class="brndle-code__copy-icon" />
                        <span class="brndle-code__copy-label">{{ __('Copy', 'brndle') }}</span>
                    </span>
                    <span class="brndle-code__copy-success" aria-hidden="true" hidden>
                        <x-icon name="check" class="brndle-code__copy-icon" />
                        <span class="brndle-code__copy-label">{{ __('Copied', 'brndle') }}</span>
                    </span>
                </button>
            @endif
            @php
                $lineCount = $showLineNumbers ? max(1, substr_count($code, "\n") + 1) : 0;
            @endphp
            <div class="brndle-code__shell">
                @if($showLineNumbers)
                    <aside class="brndle-code__line-numbers" aria-hidden="true">
                        @for($n = 1; $n <= $lineCount; $n++)
                            <span class="brndle-code__ln">{{ $n }}</span>
                        @endfor
                    </aside>
                @endif
                <pre
                    class="brndle-code__pre"
                    tabindex="0"
                    role="region"
                    aria-label="{{ esc_attr(sprintf(
                        /* translators: %s: programming language name */
                        __('Code block: %s', 'brndle'),
                        $language === 'plain' ? __('plain text', 'brndle') : $language
                    )) }}"
                ><code class="brndle-code__code hljs {{ $hljsClass }}">{{ $code }}</code></pre>
            </div>
        </div>
        @if($caption !== '')
            <figcaption class="brndle-code__caption">{!! wp_kses($caption, ['a' => ['href' => true, 'rel' => true, 'target' => true], 'code' => [], 'strong' => [], 'em' => []]) !!}</figcaption>
        @endif
    </figure>
</x-block-wrapper>
