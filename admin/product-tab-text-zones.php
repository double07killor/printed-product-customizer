<?php
if (!defined('ABSPATH')) {
    exit;
}

add_filter('woocommerce_product_data_tabs', 'fpc_text_zones_product_data_tab');
function fpc_text_zones_product_data_tab($tabs) {
    $tabs['fpc_text_zones'] = [
        'label'    => __('Text Zones', 'printed-product-customizer'),
        'target'   => 'fpc_text_zones_panel',
        'class'    => ['show_if_simple', 'show_if_variable'],
        'priority' => 28,
    ];
    return $tabs;
}

add_action('woocommerce_product_data_panels', 'fpc_text_zones_product_data_panel');
function fpc_text_zones_product_data_panel() {
    global $post;
    $value = get_post_meta($post->ID, '_fpc_text_zones', true);
    ?>
    <div id="fpc_text_zones_panel" class="panel woocommerce_options_panel hidden">
        <div class="options_group">
            <?php
            woocommerce_wp_textarea_input([
                'id'          => 'fpc_text_zones',
                'label'       => __('Text zone definitions (JSON)', 'printed-product-customizer'),
                'value'       => $value,
                'description' => __('Placeholder textarea for text zone configuration.', 'printed-product-customizer'),
                'desc_tip'    => true,
            ]);
            ?>
        </div>
    </div>
    <?php
}

add_action('woocommerce_process_product_meta', 'fpc_text_zones_save');
function fpc_text_zones_save($post_id) {
    if (isset($_POST['fpc_text_zones'])) {
        update_post_meta($post_id, '_fpc_text_zones', wp_kses_post(stripslashes($_POST['fpc_text_zones'])));
    }
}
