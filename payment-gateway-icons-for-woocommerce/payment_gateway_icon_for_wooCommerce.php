<?php

/**
 * Plugin Name:       Payment Gateway Icons For WooCommerce
 * Plugin URI:        https://petruthit.com
 * Description:       Add or change WooCommerce payment gateway icons globally (Stripe compatible).
 * Version:           2.1.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Nastin Mfena
 * Text Domain:       payment-gateway-icons-for-woocommerce
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Plugin version
 */
define('PAYMENT_GATEWAY_ICON_FOR_WOOCOMMERCE_VERSION', '2.0.0');

/**
 * Plugin paths (NEW)
 */
define('FCPGIFW_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('FCPGIFW_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FCPGIFW_OPTION', 'fcpgifw_icons');

/**
 * Load core classes (NEW STRUCTURE)
 */
require_once FCPGIFW_PLUGIN_PATH . 'includes/class-icons.php';
require_once FCPGIFW_PLUGIN_PATH . 'admin/class-admin.php';
require_once FCPGIFW_PLUGIN_PATH . 'public/class-public.php';

/**
 * Initialize plugin
 */
function run_payment_gateway_icon_for_woocommerce() {

    // Admin (settings UI)
    if (is_admin()) {
        new FCPGIFW_Admin();
    }

    // Frontend (icon override)
    new FCPGIFW_Public();
}

/**
 * Run after plugins loaded (IMPORTANT for WooCommerce)
 */
add_action('plugins_loaded', 'run_payment_gateway_icon_for_woocommerce');