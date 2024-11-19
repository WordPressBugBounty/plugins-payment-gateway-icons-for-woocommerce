<?php

/**
 * Plugin Name
 *
 * @package           Payment_Gateway_Icon_For_WooCommerce 
 * @author            Nastin Mfena
 * @copyright         2022 Petruth IT Solutions
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Payment Gateway Icons For Woocommerce
 * Plugin URI:        https://petruthit.com
 * Description:       Add or change the default payment gateway icons that appear on the WooCommerce checkout page to anything you like or brand that suits your business. You can sizes for desktop and mobile view.
 * Version:           1.2.1
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Nastin Mfena
 * Text Domain:       payment-gateway-icons-for-woocommerce
 * License:           GPL-2.0+
 * License URI:      http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PAYMENT_GATEWAY_ICON_FOR_WOOCOMMERCE_VERSION', '1.2.1' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-payment-gateway-icon-for-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_payment_gateway_icon_for_woocommerce() {

	$plugin = new Payment_Gateway_Icon_For_WooCommerce();
	$plugin->run();

}
run_payment_gateway_icon_for_woocommerce();