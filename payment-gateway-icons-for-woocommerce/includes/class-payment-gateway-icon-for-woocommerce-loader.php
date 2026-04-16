<?php

/**
 * Loader for Payment Gateway Icons for WooCommerce
 */

if (!defined('ABSPATH')) exit;

class Payment_Gateway_Icon_For_WooCommerce_Loader {

    /**
     * Register hooks directly
     */
    public function run() {

        /**
         * Admin (settings UI)
         */
        if (is_admin()) {
            $admin = new FCPGIFW_Admin();

            // Admin hooks are self-registered in constructor
        }

        /**
         * Frontend (icon override)
         */
        $public = new FCPGIFW_Public();

        // Public hooks are self-registered in constructor
    }
}