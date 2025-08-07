<?php
if (!defined('ABSPATH')) {
    exit;
}

// Register submenu for predefined logos
add_action('admin_menu', 'fpc_register_logo_menu');
function fpc_register_logo_menu() {
    add_submenu_page(
        'woocommerce',
        __('Predefined Logos', 'printed-product-customizer'),
        __('Predefined Logos', 'printed-product-customizer'),
        'manage_woocommerce',
        'fpc_predefined_logos',
        'fpc_render_logo_page'
    );
}

function fpc_render_logo_page() {
    if (!current_user_can('manage_woocommerce')) {
        return;
    }

    if (isset($_POST['fpc_upload_logo']) && check_admin_referer('fpc_upload_logo')) {
        if (!empty($_FILES['fpc_logo_file']['tmp_name'])) {
            $raw = file_get_contents($_FILES['fpc_logo_file']['tmp_name']);
            $sanitized = FPC_SVG_Logo::sanitize_svg($raw);
            $layers = FPC_SVG_Logo::parse_svg_layers($sanitized);

            $label = sanitize_text_field($_POST['fpc_logo_label'] ?? '');
            $price = floatval($_POST['fpc_logo_price'] ?? 0);
            $slug  = sanitize_title(pathinfo($_FILES['fpc_logo_file']['name'], PATHINFO_FILENAME));

            $upload_dir = wp_upload_dir();
            $dir = trailingslashit($upload_dir['basedir']) . 'fpc-logos';
            if (!file_exists($dir)) {
                wp_mkdir_p($dir);
            }
            $file_path = trailingslashit($dir) . $slug . '.svg';
            file_put_contents($file_path, $sanitized);

            $logos = get_option('fpc_predefined_logos', []);
            $logos[$slug] = [
                'label'           => $label ?: $slug,
                'priceAdjustment' => $price,
                'layers'          => $layers,
            ];
            update_option('fpc_predefined_logos', $logos);

            echo '<div class="updated"><p>' . esc_html__('Logo uploaded.', 'printed-product-customizer') . '</p></div>';
        }
    }

    $logos = get_option('fpc_predefined_logos', []);

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Predefined Logos', 'printed-product-customizer') . '</h1>';

    echo '<form method="post" enctype="multipart/form-data" style="margin-bottom:20px;">';
    wp_nonce_field('fpc_upload_logo');
    echo '<table class="form-table"><tbody>';
    echo '<tr><th><label for="fpc_logo_label">' . esc_html__('Label', 'printed-product-customizer') . '</label></th>';
    echo '<td><input type="text" id="fpc_logo_label" name="fpc_logo_label" class="regular-text" /></td></tr>';
    echo '<tr><th><label for="fpc_logo_file">' . esc_html__('SVG File', 'printed-product-customizer') . '</label></th>';
    echo '<td><input type="file" id="fpc_logo_file" name="fpc_logo_file" accept=".svg" /></td></tr>';
    echo '<tr><th><label for="fpc_logo_price">' . esc_html__('Price Adjustment', 'printed-product-customizer') . '</label></th>';
    echo '<td><input type="number" step="any" id="fpc_logo_price" name="fpc_logo_price" /></td></tr>';
    echo '</tbody></table>';
    echo '<p><input type="submit" name="fpc_upload_logo" class="button button-primary" value="' . esc_attr__('Upload', 'printed-product-customizer') . '" /></p>';
    echo '</form>';

    if (!empty($logos)) {
        echo '<table class="widefat"><thead><tr>';
        echo '<th>' . esc_html__('Slug', 'printed-product-customizer') . '</th>';
        echo '<th>' . esc_html__('Label', 'printed-product-customizer') . '</th>';
        echo '<th>' . esc_html__('Layers', 'printed-product-customizer') . '</th>';
        echo '<th>' . esc_html__('Price Adj.', 'printed-product-customizer') . '</th>';
        echo '</tr></thead><tbody>';
        foreach ($logos as $slug => $data) {
            $count = count($data['layers']);
            echo '<tr>';
            echo '<td>' . esc_html($slug) . '</td>';
            echo '<td>' . esc_html($data['label'] ?? '') . '</td>';
            echo '<td>' . esc_html($count) . '</td>';
            echo '<td>' . esc_html($data['priceAdjustment'] ?? 0) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }

    echo '</div>';
}
