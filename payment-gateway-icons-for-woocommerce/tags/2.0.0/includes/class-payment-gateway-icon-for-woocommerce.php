<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://petruthit.com
 * @since      1.1.0
 *
 * @package    Payment_Gateway_Icon_For_WooCommerce
 * @subpackage Payment_Gateway_Icon_For_WooCommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.1.0
 * @package    Payment_Gateway_Icon_For_WooCommerce
 * @subpackage Payment_Gateway_Icon_For_WooCommerce/includes
 * @author     Nastin Mfena <nastinmfena@gmail.com>
 */
class Payment_Gateway_Icon_For_WooCommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      Payment_Gateway_Icon_For_WooCommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.1.0
	 */
	public function __construct() {

		$this->plugin_name  = 'payment-gateway-icon-for-woocommerce';
                $this->version      = PAYMENT_GATEWAY_ICON_FOR_WOOCOMMERCE_VERSION;

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Payment_Gateway_Icon_For_WooCommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Payment_Gateway_Icon_For_WooCommerce_i18n. Defines internationalization functionality.
	 * - Payment_Gateway_Icon_For_WooCommerce_Admin. Defines all hooks for the admin area.
	 * - Payment_Gateway_Icon_For_WooCommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-payment-gateway-icon-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-payment-gateway-icon-for-woocommerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-payment-gateway-icon-for-woocommerce-public.php';

		$this->loader = new Payment_Gateway_Icon_For_WooCommerce_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		if (is_admin()) {
			$plugin_admin = new Payment_Gateway_Icon_For_WooCommerce_Admin( $this->get_plugin_name(), $this->get_version() );
			
			// woocommerce-gateway-amazon-payments-advanced:1.9.0 wait until init 10 to hook
			// to `woocommerce_payment_gateways` filter.
			//
			// So, priority 10 will be to early and we'll not be able to add our custom setting
			// field.
			$this->loader->add_action('init', $plugin_admin, 'hook_form_fields_modifier', 20);
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		if (!is_admin()) {
			$plugin_public = new Payment_Gateway_Icon_For_WooCommerce_Public( $this->get_plugin_name(), $this->get_version() );
					
			$this->loader->add_filter('woocommerce_gateway_icon', $plugin_public, 'modify_icon', 20, 2);
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.1.0
	 * @return    Payment_Gateway_Icon_For_WooCommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}