<?php
if (!defined('ABSPATH')) {
    exit;
}

add_filter('woocommerce_product_data_tabs', 'fpc_subgroups_product_data_tab');
function fpc_subgroups_product_data_tab($tabs) {
    $tabs['fpc_subgroups'] = [
        'label'    => __('Subgroups', 'printed-product-customizer'),
        'target'   => 'fpc_subgroups_panel',
        'class'    => ['show_if_simple', 'show_if_variable'],
        'priority' => 26,
    ];
    return $tabs;
}

add_action('woocommerce_product_data_panels', 'fpc_subgroups_product_data_panel');
function fpc_subgroups_product_data_panel() {
    global $post;
    $value = get_post_meta($post->ID, '_fpc_subgroups', true);
    ?>
    <div id="fpc_subgroups_panel" class="panel woocommerce_options_panel hidden">
        <div class="options_group">
            <?php
            woocommerce_wp_textarea_input([
                'id'          => 'fpc_subgroups',
                'label'       => __('Subgroup definitions (JSON)', 'printed-product-customizer'),
                'value'       => $value,
                'description' => __('Placeholder textarea for subgroup configuration.', 'printed-product-customizer'),
                'desc_tip'    => true,
            ]);
            ?>
        </div>
    </div>
    <?php
}

add_action('woocommerce_process_product_meta', 'fpc_subgroups_save');
function fpc_subgroups_save($post_id) {
    if (isset($_POST['fpc_subgroups'])) {
        update_post_meta($post_id, '_fpc_subgroups', wp_kses_post(stripslashes($_POST['fpc_subgroups'])));
    }
}
