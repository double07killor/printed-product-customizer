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

    // Handle manual sync
    if (isset($_POST['fpc_sync_filament']) && check_admin_referer('fpc_sync_filament')) {
        $sync = new FPC_Filament_Sync();
        $sync->sync();
        echo '<div class="updated"><p>' . esc_html__('Inventory synced.', 'printed-product-customizer') . '</p></div>';
    }

    $inventory = get_option('fpc_filament_inventory', []);

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Filament Inventory', 'printed-product-customizer') . '</h1>';
    echo '<form method="post">';
    wp_nonce_field('fpc_sync_filament');
    echo '<p><input type="submit" name="fpc_sync_filament" class="button button-primary" value="' . esc_attr__('Sync with Google Sheets', 'printed-product-customizer') . '"></p>';
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
