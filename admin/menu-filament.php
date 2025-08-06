<?php
if (!defined('ABSPATH')) {
    exit;
}

// Register Filament Inventory submenu under WooCommerce
add_action('admin_menu', 'fpc_register_filament_menu');
function fpc_register_filament_menu() {
    add_submenu_page(
        'woocommerce',
        __('Filament Inventory', 'printed-product-customizer'),
        __('Filament Inventory', 'printed-product-customizer'),
        'manage_woocommerce',
        'fpc_filament_inventory',
        'fpc_render_filament_inventory_page'
    );
}

/**
 * Render the Filament Inventory admin page.
 */
function fpc_render_filament_inventory_page() {
    if (!current_user_can('manage_woocommerce')) {
        return;
    }

    // Save settings
    if (isset($_POST['fpc_save_filament_settings']) && check_admin_referer('fpc_save_filament_settings')) {
        update_option('fpc_google_api_key', sanitize_text_field($_POST['fpc_google_api_key'] ?? ''));
        update_option('fpc_google_sheet_id', sanitize_text_field($_POST['fpc_google_sheet_id'] ?? ''));
        update_option('fpc_google_sheet_range', sanitize_text_field($_POST['fpc_google_sheet_range'] ?? ''));
        echo '<div class="updated"><p>' . esc_html__('Settings saved.', 'printed-product-customizer') . '</p></div>';
    }

    // Handle manual sync
    if (isset($_POST['fpc_sync_filament']) && check_admin_referer('fpc_sync_filament')) {
        $sync   = new FPC_Filament_Sync();
        $result = $sync->sync();
        if (is_wp_error($result)) {
            echo '<div class="error"><p>' . esc_html($result->get_error_message()) . '</p></div>';
        } else {
            echo '<div class="updated"><p>' . esc_html__('Inventory synced.', 'printed-product-customizer') . '</p></div>';
        }
    }

    $api_key = get_option('fpc_google_api_key', '');
    $sheet_id = get_option('fpc_google_sheet_id', '');
    $range = get_option('fpc_google_sheet_range', 'Sheet1');
    $inventory = get_option('fpc_filament_inventory', []);

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Filament Inventory', 'printed-product-customizer') . '</h1>';

    // Settings form
    echo '<form method="post" style="margin-bottom:20px;">';
    wp_nonce_field('fpc_save_filament_settings');
    echo '<h2>' . esc_html__('Google Sheets Settings', 'printed-product-customizer') . '</h2>';
    echo '<table class="form-table"><tbody>';
    echo '<tr><th><label for="fpc_google_api_key">' . esc_html__('API Key', 'printed-product-customizer') . '</label></th>';
    echo '<td><input type="text" class="regular-text" id="fpc_google_api_key" name="fpc_google_api_key" value="' . esc_attr($api_key) . '"></td></tr>';
    echo '<tr><th><label for="fpc_google_sheet_id">' . esc_html__('Sheet ID', 'printed-product-customizer') . '</label></th>';
    echo '<td><input type="text" class="regular-text" id="fpc_google_sheet_id" name="fpc_google_sheet_id" value="' . esc_attr($sheet_id) . '"></td></tr>';
    echo '<tr><th><label for="fpc_google_sheet_range">' . esc_html__('Range or Tab', 'printed-product-customizer') . '</label></th>';
    echo '<td><input type="text" class="regular-text" id="fpc_google_sheet_range" name="fpc_google_sheet_range" value="' . esc_attr($range) . '"></td></tr>';
    echo '</tbody></table>';
    echo '<p><input type="submit" name="fpc_save_filament_settings" class="button button-secondary" value="' . esc_attr__('Save Settings', 'printed-product-customizer') . '"></p>';
    echo '</form>';

    // Sync form
    echo '<form method="post">';
    wp_nonce_field('fpc_sync_filament');
    echo '<p><input type="submit" name="fpc_sync_filament" class="button button-primary" value="' . esc_attr__('Sync Now', 'printed-product-customizer') . '"></p>';
    echo '</form>';

    if (!empty($inventory)) {
        echo '<table class="widefat">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__('Slug', 'printed-product-customizer') . '</th>';
        echo '<th>' . esc_html__('Material', 'printed-product-customizer') . '</th>';
        echo '<th>' . esc_html__('Price/kg', 'printed-product-customizer') . '</th>';
        echo '<th>' . esc_html__('Stock (g)', 'printed-product-customizer') . '</th>';
        echo '<th>' . esc_html__('Color', 'printed-product-customizer') . '</th>';
        echo '<th>' . esc_html__('Texture', 'printed-product-customizer') . '</th>';
        echo '</tr></thead><tbody>';

        foreach ($inventory as $slug => $data) {
            echo '<tr>';
            echo '<td>' . esc_html($slug) . '</td>';
            echo '<td>' . esc_html($data['material'] ?? '') . '</td>';
            echo '<td>' . esc_html($data['price_per_kg'] ?? '') . '</td>';
            echo '<td>' . esc_html($data['stock_grams'] ?? '') . '</td>';
            $color = $data['color'] ?? '';
            echo '<td><span style="display:inline-block;width:20px;height:20px;background:' . esc_attr($color) . ';"></span> ' . esc_html($color) . '</td>';
            echo '<td>' . esc_html($data['texture'] ?? '') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }

    echo '</div>';
}
