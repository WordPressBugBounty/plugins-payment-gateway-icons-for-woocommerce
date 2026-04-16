<?php

if (!defined('ABSPATH')) exit;

class FCPGIFW_Public {

    public function __construct() {
        add_filter('woocommerce_gateway_icon', [$this, 'modify_icon'], 20, 2);
    }

    /**
     * Replace WooCommerce payment gateway icon with custom icon
     */
    public function modify_icon($icon = '', $gateway_id = '') {

        if (empty($gateway_id)) {
            return $icon;
        }

        if (!function_exists('WC')) {
            return $icon;
        }

        $gateways = WC()->payment_gateways()->payment_gateways();

        if (!isset($gateways[$gateway_id])) {
            return $icon;
        }

        $icons = get_option(FCPGIFW_OPTION, []);
        $custom_icon = $icons[$gateway_id] ?? '';

        if (empty($custom_icon)) {
            return $icon;
        }

        $custom_icon = esc_url($custom_icon);
        $alt = esc_attr($gateways[$gateway_id]->get_title());

        return sprintf(
            '<img src="%s" alt="%s" style="max-height:24px; width:auto;" />',
            $custom_icon,
            $alt
        );
    }
}