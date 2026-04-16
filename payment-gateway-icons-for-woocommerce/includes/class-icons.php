<?php

if (!defined('ABSPATH')) exit;

class FCPGIFW_Icons {

    public static function get_icons() {
        $icons = get_option(FCPGIFW_OPTION, []);
        return is_array($icons) ? $icons : [];
    }

    public static function get_icon($gateway_id) {
        $icons = self::get_icons();
        return $icons[$gateway_id] ?? '';
    }

    public static function save_icons($icons) {
        update_option(FCPGIFW_OPTION, $icons);
    }
}