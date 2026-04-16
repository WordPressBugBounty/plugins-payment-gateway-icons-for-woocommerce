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

        update_option(FCPGIFW_OPTION, array_map('esc_url_raw', $_POST['fcpgifw_icons']));

        add_action('admin_notices', function () {
            echo '<div class="updated"><p>Gateway icons saved.</p></div>';
        });
    }

    public function render_page() {

        if (!function_exists('WC')) return;

        $icons = get_option(FCPGIFW_OPTION, []);
        $gateways = WC()->payment_gateways()->payment_gateways();

        ?>
        <div class="wrap">
            <h1>Payment Gateway Icons</h1>

            <form method="post">
                <?php wp_nonce_field('fcpgifw_save_icons'); ?>

                <table class="form-table">
                    <tbody>

                    <?php foreach ($gateways as $id => $gateway): ?>
                        <tr>
                            <th><?php echo esc_html($gateway->get_title()); ?></th>
                            <td>

                                <div class="fcpgifw-field">

                                    <input type="text"
                                           class="fcpgifw-icon-url"
                                           name="fcpgifw_icons[<?php echo esc_attr($id); ?>]"
                                           value="<?php echo esc_attr($icons[$id] ?? ''); ?>"
                                           style="width:350px;" />

                                    <button type="button" class="button fcpgifw-upload-btn">
                                        Upload
                                    </button>

                                    <?php if (!empty($icons[$id])): ?>
                                        <div>
                                            <img src="<?php echo esc_url($icons[$id]); ?>" style="height:24px;" />
                                        </div>
                                    <?php endif; ?>

                                </div>

                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>

                <?php submit_button('Save Icons'); ?>
            </form>
        </div>
        <?php
    }
}