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
    $subgroups = get_post_meta($post->ID, '_fpc_subgroups', true);
    if (!is_array($subgroups)) {
        $subgroups = [];
    }
    $filament_groups = get_post_meta($post->ID, '_fpc_filament_groups', true);
    $filament_groups = is_array($filament_groups) ? $filament_groups : [];
    $filament_opts = [];
    foreach ($filament_groups as $fg) {
        if (!empty($fg['key'])) {
            $filament_opts[$fg['key']] = $fg['label'];
        }
    }
    ?>
    <div id="fpc_subgroups_panel" class="panel woocommerce_options_panel hidden">
        <div class="fpc-repeatable-wrapper">
            <div class="fpc-repeatable-container">
                <div class="fpc-repeatable-row fpc-template" style="display:none;">
                    <p class="form-field">
                        <label><?php _e('Label', 'printed-product-customizer'); ?></label>
                        <input type="text" class="short fpc-generate-key" name="fpc_subgroups[__INDEX__][label]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Subgroup Key', 'printed-product-customizer'); ?></label>
                        <input type="text" class="short fpc-key-field" name="fpc_subgroups[__INDEX__][key]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Allowed Filament Groups', 'printed-product-customizer'); ?></label>
                        <select multiple="multiple" name="fpc_subgroups[__INDEX__][allowed][]">
                            <?php foreach ($filament_opts as $k => $l) : ?>
                                <option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($l); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p class="form-field">
                        <label><?php _e('Allow Additional Groups', 'printed-product-customizer'); ?></label>
                        <input type="checkbox" name="fpc_subgroups[__INDEX__][allow_additional]" value="1" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Forced Filament Group', 'printed-product-customizer'); ?></label>
                        <select name="fpc_subgroups[__INDEX__][forced]"><option value=""><?php _e('None', 'printed-product-customizer'); ?></option><?php foreach ($filament_opts as $k => $l) : ?><option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($l); ?></option><?php endforeach; ?></select>
                    </p>
                    <p><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></p>
                </div>
                <?php foreach ($subgroups as $index => $sg) : ?>
                    <div class="fpc-repeatable-row">
                        <p class="form-field">
                            <label><?php _e('Label', 'printed-product-customizer'); ?></label>
                            <input type="text" class="short fpc-generate-key" name="fpc_subgroups[<?php echo esc_attr($index); ?>][label]" value="<?php echo esc_attr($sg['label'] ?? ''); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Subgroup Key', 'printed-product-customizer'); ?></label>
                            <input type="text" class="short fpc-key-field" name="fpc_subgroups[<?php echo esc_attr($index); ?>][key]" value="<?php echo esc_attr($sg['key'] ?? ''); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Allowed Filament Groups', 'printed-product-customizer'); ?></label>
                            <select multiple="multiple" name="fpc_subgroups[<?php echo esc_attr($index); ?>][allowed][]">
                                <?php $allowed = $sg['allowed'] ?? []; foreach ($filament_opts as $k => $l) : ?>
                                    <option value="<?php echo esc_attr($k); ?>" <?php selected(in_array($k, $allowed, true)); ?>><?php echo esc_html($l); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                        <p class="form-field">
                            <label><?php _e('Allow Additional Groups', 'printed-product-customizer'); ?></label>
                            <input type="checkbox" name="fpc_subgroups[<?php echo esc_attr($index); ?>][allow_additional]" value="1" <?php checked(!empty($sg['allow_additional'])); ?> />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Forced Filament Group', 'printed-product-customizer'); ?></label>
                            <select name="fpc_subgroups[<?php echo esc_attr($index); ?>][forced]">
                                <option value=""><?php _e('None', 'printed-product-customizer'); ?></option>
                                <?php foreach ($filament_opts as $k => $l) : ?>
                                    <option value="<?php echo esc_attr($k); ?>" <?php selected(isset($sg['forced']) && $sg['forced'] === $k); ?>><?php echo esc_html($l); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                        <p><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <p><button type="button" class="button fpc-repeatable-add"><?php _e('Add Subgroup', 'printed-product-customizer'); ?></button></p>
        </div>
    </div>
    <?php
}

add_action('woocommerce_process_product_meta', 'fpc_subgroups_save');
function fpc_subgroups_save($post_id) {
    if (isset($_POST['fpc_subgroups']) && is_array($_POST['fpc_subgroups'])) {
        $subgroups = [];
        foreach ($_POST['fpc_subgroups'] as $sg) {
            $subgroups[] = [
                'label'           => sanitize_text_field($sg['label'] ?? ''),
                'key'             => sanitize_title($sg['key'] ?? ''),
                'allowed'         => array_map('sanitize_text_field', $sg['allowed'] ?? []),
                'allow_additional'=> !empty($sg['allow_additional']) ? 1 : 0,
                'forced'          => sanitize_text_field($sg['forced'] ?? ''),
            ];
        }
        update_post_meta($post_id, '_fpc_subgroups', $subgroups);
    } else {
        delete_post_meta($post_id, '_fpc_subgroups');
    }
}
