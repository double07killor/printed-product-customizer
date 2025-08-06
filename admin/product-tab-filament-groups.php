<?php
if (!defined('ABSPATH')) {
    exit;
}

// Add Filament Groups tab to product data tabs
add_filter('woocommerce_product_data_tabs', 'fpc_filament_groups_product_data_tab');
function fpc_filament_groups_product_data_tab($tabs) {
    $tabs['fpc_filament_groups'] = [
        'label'    => __('Filament Groups', 'printed-product-customizer'),
        'target'   => 'fpc_filament_groups_panel',
        'class'    => ['show_if_simple', 'show_if_variable'],
        'priority' => 25,
    ];
    return $tabs;
}

// Panel content
add_action('woocommerce_product_data_panels', 'fpc_filament_groups_product_data_panel');
function fpc_filament_groups_product_data_panel() {
    global $post;
    $value = get_post_meta($post->ID, '_fpc_filament_groups', true);
    ?>
    <div id="fpc_filament_groups_panel" class="panel woocommerce_options_panel hidden">
        <div class="options_group">
            <?php
            woocommerce_wp_textarea_input([
                'id'          => 'fpc_filament_groups',
                'label'       => __('Filament group definitions (JSON)', 'printed-product-customizer'),
                'value'       => $value,
                'description' => __('Placeholder textarea for filament group configuration.', 'printed-product-customizer'),
                'desc_tip'    => true,
            ]);
            ?>
        </div>
    </div>
    <?php
}

// Save meta
add_action('woocommerce_process_product_meta', 'fpc_filament_groups_save');
function fpc_filament_groups_save($post_id) {
    if (isset($_POST['fpc_filament_groups'])) {
        update_post_meta($post_id, '_fpc_filament_groups', wp_kses_post(stripslashes($_POST['fpc_filament_groups'])));
    }
}
