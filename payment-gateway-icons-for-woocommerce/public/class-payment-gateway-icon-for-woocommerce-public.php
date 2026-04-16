<?php

/**
 * Public-facing functionality (icon override)
 */

if (!defined('ABSPATH')) exit;

class FCPGIFW_Public {

    public function __construct() {
        add_filter('woocommerce_gateway_icon', [$this, 'modify_icon'], 20, 2);
    }

    /**
     * Override gateway icon globally
     */
    public function modify_icon($icon = '', $gateway_id = '') {

        if (empty($gateway_id)) {
            return $icon;
        }

        // Get all stored icons (single source of truth)
        $icons = get_option(FCPGIFW_OPTION, []);

        if (!is_array($icons) || empty($icons[$gateway_id])) {
            return $icon;
        }

        $custom_icon = esc_url($icons[$gateway_id]);

        // Optional retina support (if stored)
        $custom_icon_2x = !empty($icons[$gateway_id . '_2x'])
            ? esc_url($icons[$gateway_id . '_2x'])
            : '';

        // Get gateway title safely
        $title = $gateway_id;

        if (function_exists('WC') && WC()->payment_gateways()) {
            $gateways = WC()->payment_gateways()->payment_gateways();

            if (isset($gateways[$gateway_id])) {
                $title = $gateways[$gateway_id]->get_title();
            }
        }

        $img = '<img src="' . $custom_icon . '" alt="' . esc_attr($title) . '" style="max-height:24px;" />';

        // Add retina support if available
        if ($custom_icon_2x) {
            $img = '<img 
                src="' . $custom_icon . '" 
                srcset="' . $custom_icon . ' 1x, ' . $custom_icon_2x . ' 2x"
                alt="' . esc_attr($title) . '" 
                style="max-height:24px;" />';
        }

        return $img;
    }
}