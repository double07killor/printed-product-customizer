<?php
/**
 * Plugin Name: Printed Product Customizer
 * Description: A WooCommerce extension to enable customizable 3D printed products with filament-aware configuration, text input zones, and 3MF integration.
 * Version: 0.1.10
 * Author: Fortney Engineering
 * Text Domain: printed-product-customizer
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Plugin constants
define('FPC_VERSION', '0.1.10');
define('FPC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FPC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FPC_PLUGIN_FILE', __FILE__);

// Autoload classes (optional — or use Composer)
function fpc_autoload_classes($class) {
    if (strpos($class, 'FPC_') === 0) {
        $file = FPC_PLUGIN_DIR . 'includes/class-' . strtolower(str_replace('_', '-', $class)) . '.php';
        if (file_exists($file)) {
            include_once $file;
        }
    }
}
spl_autoload_register('fpc_autoload_classes');

// Plugin activation
register_activation_hook(__FILE__, 'fpc_activate');
function fpc_activate() {
    // Set default options, create storage, etc.
}

// Plugin deactivation
register_deactivation_hook(__FILE__, 'fpc_deactivate');
function fpc_deactivate() {
    // Cleanup if needed
}

// Load plugin
add_action('plugins_loaded', 'fpc_init');
function fpc_init() {
    // Load localization
    load_plugin_textdomain('printed-product-customizer', false, dirname(plugin_basename(__FILE__)) . '/languages');

    // Core helpers
    require_once FPC_PLUGIN_DIR . 'includes/helpers.php';

    // Load admin menus
    if (is_admin()) {
        require_once FPC_PLUGIN_DIR . 'admin/menu-filament.php';
        require_once FPC_PLUGIN_DIR . 'admin/menu-logos.php';
        require_once FPC_PLUGIN_DIR . 'admin/product-tab-filament-groups.php';
        require_once FPC_PLUGIN_DIR . 'admin/product-tab-subgroups.php';
        require_once FPC_PLUGIN_DIR . 'admin/product-tab-3mf-mapping.php';
        require_once FPC_PLUGIN_DIR . 'admin/product-tab-design-zones.php';
    }

    // Core logic
    require_once FPC_PLUGIN_DIR . 'includes/class-filament-sync.php';
    require_once FPC_PLUGIN_DIR . 'includes/class-product-config.php';
    require_once FPC_PLUGIN_DIR . 'includes/class-variation-pricing.php';
    require_once FPC_PLUGIN_DIR . 'includes/class-svg-logo.php';
}

// Enqueue admin scripts
add_action('admin_enqueue_scripts', 'fpc_admin_scripts');
function fpc_admin_scripts($hook) {
    if (strpos($hook, 'woocommerce') === false && !in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }
    wp_enqueue_style('fpc-admin', FPC_PLUGIN_URL . 'admin/assets/admin.css', [], FPC_VERSION);
    wp_enqueue_script('fpc-admin', FPC_PLUGIN_URL . 'admin/assets/admin.js', ['jquery', 'jquery-ui-autocomplete'], FPC_VERSION, true);
}

// Allow SVG uploads for logo management
add_filter('upload_mimes', 'fpc_allow_svg_upload');
function fpc_allow_svg_upload($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}

// Prevent Jetpack from firing unnecessary API requests that clutter the console.
add_filter('jetpack_just_in_time_msgs', '__return_false');
