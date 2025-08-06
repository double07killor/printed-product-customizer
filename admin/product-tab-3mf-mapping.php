<?php
if (!defined('ABSPATH')) {
    exit;
}

add_filter('woocommerce_product_data_tabs', 'fpc_3mf_mapping_product_data_tab');
function fpc_3mf_mapping_product_data_tab($tabs) {
    $tabs['fpc_3mf_mapping'] = [
        'label'    => __('3MF Mapping', 'printed-product-customizer'),
        'target'   => 'fpc_3mf_mapping_panel',
        'class'    => ['show_if_simple', 'show_if_variable'],
        'priority' => 27,
    ];
    return $tabs;
}

add_action('woocommerce_product_data_panels', 'fpc_3mf_mapping_product_data_panel');
function fpc_3mf_mapping_product_data_panel() {
    global $post;
    $value = get_post_meta($post->ID, '_fpc_body_assignments', true);
    ?>
    <div id="fpc_3mf_mapping_panel" class="panel woocommerce_options_panel hidden">
        <div class="options_group">
            <?php
            woocommerce_wp_textarea_input([
                'id'          => 'fpc_body_assignments',
                'label'       => __('Body assignments (JSON)', 'printed-product-customizer'),
                'value'       => $value,
                'description' => __('Placeholder textarea for mapping 3MF bodies to subgroups.', 'printed-product-customizer'),
                'desc_tip'    => true,
            ]);
            ?>
        </div>
    </div>
    <?php
}

add_action('woocommerce_process_product_meta', 'fpc_3mf_mapping_save');
function fpc_3mf_mapping_save($post_id) {
    if (isset($_POST['fpc_body_assignments'])) {
        update_post_meta($post_id, '_fpc_body_assignments', wp_kses_post(stripslashes($_POST['fpc_body_assignments'])));
    }
}
