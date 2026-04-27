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
     * Enhanced to support third-party plugins via filter
     */
    public static function get_shipping_icon($method) {

        if (empty($method) || !is_object($method)) {
            return '';
        }

        $icons = self::get_icons();

        /**
         * SAFE TYPE DETECTION (handles all plugins)
         */
        $type_id = '';
        $instance_id = '';

        if (method_exists($method, 'get_method_id')) {
            $type_id = $method->get_method_id();
            $instance_id = method_exists($method, 'get_id') ? $method->get_id() : '';
        } elseif (isset($method->method_id)) {
            $type_id = $method->method_id;
            $instance_id = $method->id ?? '';
        } elseif (isset($method->id)) {
            $instance_id = $method->id;
            if (strpos($method->id, ':') !== false) {
                $type_id = explode(':', $method->id)[0];
            } else {
                $type_id = $method->id;
            }
        }

        // 1. Match by TYPE (preferred)
        if ($type_id && isset($icons['shipping'][$type_id])) {
            return apply_filters('fcpgifw_shipping_icon_url', $icons['shipping'][$type_id], $type_id, $instance_id, $method);
        }

        // 2. Fallback to INSTANCE (backward compatibility)
        if ($instance_id && isset($icons['shipping'][$instance_id])) {
            return apply_filters('fcpgifw_shipping_icon_url', $icons['shipping'][$instance_id], $type_id, $instance_id, $method);
        }

        // 3. Allow third-party plugins to provide dynamic icons
        $dynamic_icon = apply_filters('fcpgifw_dynamic_shipping_icon', '', $method, $type_id, $instance_id);
        if (!empty($dynamic_icon)) {
            return $dynamic_icon;
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
    
    /**
     * Get all available shipping methods (including third-party)
     * Used by admin to display methods
     */
    public static function get_available_shipping_methods() {
        $methods = [];
        
        // Get native WooCommerce shipping methods
        $shipping_methods = WC()->shipping()->get_shipping_methods();
        foreach ($shipping_methods as $method) {
            $methods[$method->id] = $method->method_title;
        }
        
        // Get methods from shipping zones
        $zones = WC_Shipping_Zones::get_zones();
        $zones[] = ['zone_id' => 0]; // Add default zone
        
        foreach ($zones as $zone_data) {
            $zone_id = isset($zone_data['zone_id']) ? $zone_data['zone_id'] : 0;
            $zone = new WC_Shipping_Zone($zone_id);
            $zone_methods = $zone->get_shipping_methods();
            
            foreach ($zone_methods as $method) {
                // Get base method ID
                if (method_exists($method, 'get_method_id')) {
                    $base_id = $method->get_method_id();
                } elseif (isset($method->method_id)) {
                    $base_id = $method->method_id;
                } elseif (isset($method->id) && strpos($method->id, ':') !== false) {
                    $base_id = explode(':', $method->id)[0];
                } elseif (isset($method->id)) {
                    $base_id = $method->id;
                } else {
                    continue;
                }
                
                // Get title
                $title = '';
                if (method_exists($method, 'get_method_title')) {
                    $title = $method->get_method_title();
                } elseif (method_exists($method, 'get_title')) {
                    $title = $method->get_title();
                } elseif (isset($method->method_title)) {
                    $title = $method->method_title;
                } elseif (isset($method->title)) {
                    $title = $method->title;
                } else {
                    $title = $base_id;
                }
                
                if (!isset($methods[$base_id])) {
                    $methods[$base_id] = $title;
                }
            }
        }
        
        // Allow third-party plugins to register their methods
        $methods = apply_filters('fcpgifw_register_shipping_methods', $methods);
        
        // Sort by title
        asort($methods);
        
        return $methods;
    }
}