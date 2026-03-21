<?php

namespace Brndle\Compatibility;

class WooCommerce
{
    public static function boot(): void
    {
        add_action('after_setup_theme', function () {
            if (! class_exists('WooCommerce')) {
                return;
            }

            add_theme_support('woocommerce');
            add_theme_support('wc-product-gallery-zoom');
            add_theme_support('wc-product-gallery-lightbox');
            add_theme_support('wc-product-gallery-slider');
        });

        // Wrap WooCommerce content in Brndle layout
        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper');
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end');

        add_action('woocommerce_before_main_content', function () {
            echo '<div class="max-w-7xl mx-auto px-6 py-16">';
        });

        add_action('woocommerce_after_main_content', function () {
            echo '</div>';
        });
    }
}
