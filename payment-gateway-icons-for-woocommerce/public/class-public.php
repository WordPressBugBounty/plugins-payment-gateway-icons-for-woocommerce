<?php

if (!defined('ABSPATH')) exit;

class FCPGIFW_Public {

    public function __construct() {
        add_filter('woocommerce_gateway_icon', [$this, 'modify_icon'], 20, 2);

        add_filter(
            'woocommerce_cart_shipping_method_full_label',
            [$this, 'add_shipping_icon'],
            10,
            2
        );
    }

    /**
     * Payment icon override
     */
    public function modify_icon($icon = '', $gateway_id = '') {

        if (empty($gateway_id) || !function_exists('WC')) {
            return $icon;
        }

        $gateways = WC()->payment_gateways()->payment_gateways();

        if (!isset($gateways[$gateway_id])) {
            return $icon;
        }

        $custom_icon = FCPGIFW_Icons::get_payment_icon($gateway_id);

        if (empty($custom_icon)) {
            return $icon;
        }

        return sprintf(
            '<img src="%s" alt="%s" style="max-height:24px; width:auto;" />',
            esc_url($custom_icon),
            esc_attr($gateways[$gateway_id]->get_title())
        );
    }

    /**
     * Shipping icon (TYPE-BASED)
     */
    public function add_shipping_icon($label, $method) {

        if (empty($method) || !is_object($method)) {
            return $label;
        }

        $icon = FCPGIFW_Icons::get_shipping_icon($method);

        if (empty($icon)) {
            return $label;
        }

        $icon_html = sprintf(
            '<img src="%s" class="fcpgifw-shipping-icon" alt="" />',
            esc_url($icon)
        );

        return $icon_html . ' ' . $label;
    }
}