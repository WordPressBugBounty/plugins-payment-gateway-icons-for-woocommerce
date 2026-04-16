<?php

/**
 * Core plugin class
 */

if (!defined('ABSPATH')) exit;

class Payment_Gateway_Icon_For_WooCommerce {

    protected $plugin_name;
    protected $version;

    /**
     * Initialize plugin
     */
    public function __construct() {

        $this->plugin_name = 'payment-gateway-icons-for-woocommerce';
        $this->version     = PAYMENT_GATEWAY_ICON_FOR_WOOCOMMERCE_VERSION;

        $this->load_dependencies();
        $this->init_components();
    }

    /**
     * Load required files
     */
    private function load_dependencies() {

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-icons.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-public.php';
    }

    /**
     * Initialize plugin components
     */
    private function init_components() {

        // Admin only (WooCommerce backend)
        if (is_admin()) {
            new FCPGIFW_Admin();
        }

        // Frontend
        new FCPGIFW_Public();
    }

    /**
     * Plugin name
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Version
     */
    public function get_version() {
        return $this->version;
    }
}