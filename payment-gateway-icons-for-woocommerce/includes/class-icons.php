<?php

if (!defined('ABSPATH')) exit;

class FCPGIFW_Icons {

    /**
     * Normalize options (backward compatible)
     */
    public static function get_icons() {

        $icons = get_option(FCPGIFW_OPTION, []);

        // OLD STRUCTURE → convert
        if (!isset($icons['payment']) && !isset($icons['shipping'])) {
            return [
                'payment' => is_array($icons) ? $icons : [],
                'shipping' => []
            ];
        }

        return [
            'payment' => isset($icons['payment']) ? $icons['payment'] : [],
            'shipping' => isset($icons['shipping']) ? $icons['shipping'] : [],
        ];
    }

    public static function get_payment_icon($gateway_id) {
        $icons = self::get_icons();
        return $icons['payment'][$gateway_id] ?? '';
    }

    /**
     * Get shipping icon by TYPE with fallback to instance
     */
    public static function get_shipping_icon($method) {

        if (empty($method) || !is_object($method)) {
            return '';
        }

        $icons = self::get_icons();

        $type_id     = method_exists($method, 'get_method_id') ? $method->get_method_id() : '';
        $instance_id = isset($method->id) ? $method->id : '';

        // 1. Match by type (NEW)
        if ($type_id && isset($icons['shipping'][$type_id])) {
            return $icons['shipping'][$type_id];
        }

        // 2. Fallback to instance (OLD compatibility)
        if ($instance_id && isset($icons['shipping'][$instance_id])) {
            return $icons['shipping'][$instance_id];
        }

        return '';
    }

    public static function save_icons($icons) {

        $normalized = self::get_icons();

        if (isset($icons['payment'])) {
            $normalized['payment'] = array_map('esc_url_raw', $icons['payment']);
        }

        if (isset($icons['shipping'])) {
            $normalized['shipping'] = array_map('esc_url_raw', $icons['shipping']);
        }

        update_option(FCPGIFW_OPTION, $normalized);
    }
}