<?php

if (!defined('ABSPATH')) exit;

class FCPGIFW_Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'handle_form']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function add_menu() {
        add_submenu_page(
            'woocommerce',
            'Gateway Icons',
            'Gateway Icons',
            'manage_woocommerce',
            'fcpgifw-icons',
            [$this, 'render_page']
        );
    }

    public function enqueue_assets($hook) {

        if ($hook !== 'woocommerce_page_fcpgifw-icons') return;

        wp_enqueue_media();

        wp_enqueue_script(
            'fcpgifw-media-uploader',
            FCPGIFW_PLUGIN_URL . 'admin/media-uploader.js',
            ['jquery'],
            PAYMENT_GATEWAY_ICON_FOR_WOOCOMMERCE_VERSION,
            true
        );
    }

    public function handle_form() {

        if (!isset($_POST['fcpgifw_icons'])) return;
        if (!current_user_can('manage_woocommerce')) return;

        check_admin_referer('fcpgifw_save_icons');

        FCPGIFW_Icons::save_icons($_POST['fcpgifw_icons']);

        add_action('admin_notices', function () {
            echo '<div class="updated"><p>Icons saved.</p></div>';
        });
    }

    public function render_page() {

    if (!function_exists('WC')) return;

    $icons = FCPGIFW_Icons::get_icons();
    $gateways = WC()->payment_gateways()->payment_gateways();
    
    // Get all shipping methods using the improved method
    $shipping_methods = FCPGIFW_Icons::get_available_shipping_methods();

    ?>
    <div class="wrap">
        <h1>Payment & Shipping Gateway Icons</h1>
        
        <?php if (empty($shipping_methods)): ?>
            <div class="notice notice-warning">
                <p>No shipping methods found. Please add shipping methods in WooCommerce Settings.</p>
            </div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field('fcpgifw_save_icons'); ?>

            <h2>Payment Methods</h2>
            <p class="description">Upload the desired icons under each method below.</p>
            <table class="form-table">
                <tbody>
                <?php foreach ($gateways as $id => $gateway): ?>
                    <tr>
                        <th><?php echo esc_html($gateway->get_title()); ?></th>
                        <td>
                            <input type="text"
                                   class="fcpgifw-icon-url"
                                   name="fcpgifw_icons[payment][<?php echo esc_attr($id); ?>]"
                                   value="<?php echo esc_attr($icons['payment'][$id] ?? ''); ?>"
                                   style="width:350px;" />
                            <button type="button" class="button fcpgifw-upload-btn">Upload</button>
                            <?php if (!empty($icons['payment'][$id])): ?>
                                <div>
                                    <img src="<?php echo esc_url($icons['payment'][$id]); ?>" style="height:24px;" />
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Shipping Methods</h2>
            <p class="description">Upload the desired icons under each method below.</p>
            <table class="form-table">
                <tbody>
                <?php if (!empty($shipping_methods)): ?>
                    <?php foreach ($shipping_methods as $method_id => $title): ?>
                        <tr>
                            <th><?php echo esc_html($title . ' (' . $method_id . ')'); ?></th>
                            <td>
                                <input type="text"
                                       class="fcpgifw-icon-url"
                                       name="fcpgifw_icons[shipping][<?php echo esc_attr($method_id); ?>]"
                                       value="<?php echo esc_attr($icons['shipping'][$method_id] ?? ''); ?>"
                                       style="width:350px;" />
                                <button type="button" class="button fcpgifw-upload-btn">Upload</button>
                                <?php if (!empty($icons['shipping'][$method_id])): ?>
                                    <div>
                                        <img src="<?php echo esc_url($icons['shipping'][$method_id]); ?>" style="height:24px;" />
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">
                            <div class="notice notice-info">
                                <p>No shipping methods found. <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=shipping'); ?>">Add shipping methods</a> first.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <?php submit_button('Save Icons'); ?>
        </form>
    </div>
    <?php
}
    /**
     * Get ALL shipping methods including third-party plugins
     */
    private function get_all_shipping_methods() {
        $methods = [];
        
        // 1. Get native WooCommerce shipping methods
        $methods = $this->get_native_shipping_methods();
        
        // 2. Get methods from all shipping zones
        $zone_methods = $this->get_zone_shipping_methods();
        $methods = array_merge($methods, $zone_methods);
        
        // 3. Get methods from third-party shipping plugins via filter
        $third_party_methods = apply_filters('fcpgifw_register_shipping_methods', []);
        if (!empty($third_party_methods) && is_array($third_party_methods)) {
            $methods = array_merge($methods, $third_party_methods);
        }
        
        // 4. Allow customization through action
        $methods = apply_filters('fcpgifw_shipping_methods_list', $methods);
        
        // Remove duplicates (keep first occurrence)
        $unique_methods = [];
        foreach ($methods as $key => $value) {
            if (!isset($unique_methods[$key])) {
                $unique_methods[$key] = $value;
            }
        }
        
        // Sort alphabetically by title
        asort($unique_methods);
        
        return $unique_methods;
    }
    
    /**
     * Get native WooCommerce shipping method types
     */
    private function get_native_shipping_methods() {
        $shipping_methods = WC()->shipping()->get_shipping_methods();
        $methods = [];
        
        foreach ($shipping_methods as $method) {
            $method_id = $method->id;
            $method_title = !empty($method->method_title) ? $method->method_title : $method->title;
            $methods[$method_id] = $method_title;
        }
        
        return $methods;
    }
    
    /**
     * Get methods from all shipping zones (including configured instances)
     */
    private function get_zone_shipping_methods() {
        $methods = [];
        
        // Get all shipping zones
        $zones = WC_Shipping_Zones::get_zones();
        
        // Add default zone (zone 0)
        $zones[] = ['zone_id' => 0];
        
        foreach ($zones as $zone_data) {
            $zone_id = isset($zone_data['zone_id']) ? $zone_data['zone_id'] : 0;
            $zone = new WC_Shipping_Zone($zone_id);
            $zone_methods = $zone->get_shipping_methods();
            
            foreach ($zone_methods as $method) {
                // Get the base method ID (without instance ID)
                if (method_exists($method, 'get_method_id')) {
                    $base_id = $method->get_method_id();
                } elseif (method_exists($method, 'get_id')) {
                    $base_id = $method->get_id();
                    if (strpos($base_id, ':') !== false) {
                        $base_id = explode(':', $base_id)[0];
                    }
                } elseif (isset($method->id)) {
                    $base_id = $method->id;
                    if (strpos($base_id, ':') !== false) {
                        $base_id = explode(':', $base_id)[0];
                    }
                } else {
                    continue;
                }
                
                // Get title
                if (method_exists($method, 'get_method_title')) {
                    $title = $method->get_method_title();
                } elseif (method_exists($method, 'get_title')) {
                    $title = $method->get_title();
                } elseif (isset($method->method_title)) {
                    $title = $method->method_title;
                } else {
                    $title = $base_id;
                }
                
                // Store with base ID (this allows all instances of same method type to share icon)
                if (!isset($methods[$base_id])) {
                    $methods[$base_id] = $title;
                }
            }
        }
        
        return $methods;
    }

    /**
     * Safe shipping type detection (kept for compatibility)
     */
    private function get_shipping_type_id($method) {

        $type_id = '';

        if (method_exists($method, 'get_method_id')) {
            $type_id = $method->get_method_id();
        } elseif (isset($method->method_id)) {
            $type_id = $method->method_id;
        } elseif (isset($method->id) && strpos($method->id, ':') !== false) {
            $type_id = explode(':', $method->id)[0];
        }

        return $type_id;
    }

    /**
     * Safe title getter (kept for compatibility)
     */
    private function get_shipping_title($method) {

        if (method_exists($method, 'get_method_title')) {
            return $method->get_method_title();
        }

        if (isset($method->method_title)) {
            return $method->method_title;
        }

        if (method_exists($method, 'get_title')) {
            return $method->get_title();
        }

        return 'Shipping Method';
    }
}