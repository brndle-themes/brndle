<?php

namespace Brndle\Compatibility;

class WPML
{
    public static function boot(): void
    {
        // Add hreflang tags if WPML or Polylang is active
        add_action('wp_head', function () {
            if (! function_exists('icl_get_languages') && ! function_exists('pll_the_languages')) {
                return;
            }

            if (function_exists('icl_get_languages')) {
                $languages = icl_get_languages('skip_missing=0');
                if ($languages) {
                    foreach ($languages as $lang) {
                        printf(
                            '<link rel="alternate" hreflang="%s" href="%s" />' . "\n",
                            esc_attr($lang['language_code']),
                            esc_url($lang['url'])
                        );
                    }
                }
            }
        }, 1);
    }
}
