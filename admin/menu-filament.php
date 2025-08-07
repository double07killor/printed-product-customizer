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
        $sheet_url = sanitize_text_field($_POST['fpc_google_sheet_url'] ?? '');

        update_option('fpc_google_sheet_url', $sheet_url);

        $parts = $sheet_url ? fpc_parse_sheet_url($sheet_url) : false;
        if ($parts) {
            update_option('fpc_google_sheet_id', $parts[0]);
            update_option('fpc_google_sheet_tab', $parts[1]);
        } else {
            update_option('fpc_google_sheet_id', '');
            update_option('fpc_google_sheet_tab', '');
        }

        if (!empty($_FILES['fpc_google_json']['tmp_name'])) {
            $uploads = wp_upload_dir();
            $dir     = trailingslashit($uploads['basedir']) . 'fpc-filament-sync';
            wp_mkdir_p($dir);
            $dest = $dir . '/service-account.json';
            move_uploaded_file($_FILES['fpc_google_json']['tmp_name'], $dest);
            @chmod($dest, 0600);
            update_option('fpc_google_json_path', $dest);
        }

        if ($sheet_url && !$parts) {
            echo '<div class="error"><p>' . esc_html__('Invalid sheet URL format. Please use the full Google Sheets link.', 'printed-product-customizer') . '</p></div>';
        } else {
            echo '<div class="updated"><p>' . esc_html__('Settings saved.', 'printed-product-customizer') . '</p></div>';
        }
    }

    // Handle manual sync
    if (isset($_POST['fpc_sync_filament']) && check_admin_referer('fpc_sync_filament')) {
        $sync   = new FPC_Filament_Sync();
        $result = $sync->sync();
        if (is_wp_error($result)) {
            $message = $result->get_error_message();
            switch ($result->get_error_code()) {
                case 'missing_key':
                    $message .= ' ' . __('Upload your Google API JSON key and try again.', 'printed-product-customizer');
                    break;
                case 'invalid_key':
                    $message .= ' ' . __('Verify that the JSON credentials are correct.', 'printed-product-customizer');
                    break;
                case 'no_token':
                    $message .= ' ' . __('Authentication with Google failed.', 'printed-product-customizer');
                    break;
                case 'fpc_missing_setup':
                case 'missing_setup':
                    $message .= ' ' . __('Check that the sheet URL and tab settings are saved correctly.', 'printed-product-customizer');
                    break;
                case 'no_data':
                    $message .= ' ' . __('Ensure the sheet has data and try again.', 'printed-product-customizer');
                    break;
                case 'api_error':
                    $message .= ' ' . __('Please confirm the sheet ID and service account permissions.', 'printed-product-customizer');
                    break;
            }
            echo '<div class="error"><p>' . esc_html($message) . '</p></div>';
        } else {
            echo '<div class="updated"><p>' . esc_html__('Inventory synced.', 'printed-product-customizer') . '</p></div>';
        }
    }

    $sheet_url   = get_option('fpc_google_sheet_url', '');
    $inventory   = get_option('fpc_filament_inventory', []);
    $json_path   = get_option('fpc_google_json_path');
    $service_acc = '';
    if ($json_path && file_exists($json_path)) {
        $config = json_decode(file_get_contents($json_path), true);
        if (!empty($config['client_email'])) {
            $service_acc = $config['client_email'];
        }
    }

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Filament Inventory', 'printed-product-customizer') . '</h1>';

    // Settings form
    echo '<form method="post" enctype="multipart/form-data" style="margin-bottom:20px;">';
    wp_nonce_field('fpc_save_filament_settings');
    echo '<h2>' . esc_html__('Google Sheets Settings', 'printed-product-customizer') . '</h2>';
    echo '<table class="form-table"><tbody>';
    echo '<tr><th><label for="fpc_google_sheet_url">' . esc_html__('Sheet URL', 'printed-product-customizer') . '</label></th>';
    echo '<td><input type="text" class="regular-text" id="fpc_google_sheet_url" name="fpc_google_sheet_url" value="' . esc_attr($sheet_url) . '"></td></tr>';
    echo '<tr><th><label for="fpc_google_json">' . esc_html__('Google API JSON Key', 'printed-product-customizer') . '</label></th>';
    echo '<td><input type="file" id="fpc_google_json" name="fpc_google_json" accept="application/json"></td></tr>';
    if ($service_acc) {
        echo '<tr><th>' . esc_html__('Service Account Email', 'printed-product-customizer') . '</th>';
        echo '<td><input type="text" class="regular-text" readonly value="' . esc_attr($service_acc) . '"></td></tr>';
    }
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
