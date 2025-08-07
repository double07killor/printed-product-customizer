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
    $filament_keys_json = esc_attr(wp_json_encode(array_keys($filament_opts)));
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
                        <input type="text" class="fpc-tag-input" data-options="<?php echo $filament_keys_json; ?>" name="fpc_subgroups[__INDEX__][allowed]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Allow Additional Groups', 'printed-product-customizer'); ?></label>
                        <input type="checkbox" name="fpc_subgroups[__INDEX__][allow_additional]" value="1" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Base Grams', 'printed-product-customizer'); ?></label>
                        <input type="number" step="any" class="short" name="fpc_subgroups[__INDEX__][base_grams]" />
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
                            <?php $allowed = isset($sg['allowed']) && is_array($sg['allowed']) ? implode(', ', $sg['allowed']) : ''; ?>
                            <input type="text" class="fpc-tag-input" data-options="<?php echo $filament_keys_json; ?>" name="fpc_subgroups[<?php echo esc_attr($index); ?>][allowed]" value="<?php echo esc_attr($allowed); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Allow Additional Groups', 'printed-product-customizer'); ?></label>
                            <input type="checkbox" name="fpc_subgroups[<?php echo esc_attr($index); ?>][allow_additional]" value="1" <?php checked(!empty($sg['allow_additional'])); ?> />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Base Grams', 'printed-product-customizer'); ?></label>
                            <input type="number" step="any" class="short" name="fpc_subgroups[<?php echo esc_attr($index); ?>][base_grams]" value="<?php echo esc_attr($sg['base_grams'] ?? ''); ?>" />
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
            $allowed_raw = $sg['allowed'] ?? '';
            $allowed = array_filter(array_map('sanitize_text_field', array_map('trim', explode(',', $allowed_raw))));
            $label = sanitize_text_field($sg['label'] ?? '');
            $key = sanitize_title($sg['key'] ?? '');
            $allow_additional = !empty($sg['allow_additional']) ? 1 : 0;
            $base_grams = floatval($sg['base_grams'] ?? 0);

            if ($label === '' && $key === '' && empty($allowed) && !$allow_additional && $base_grams === 0.0) {
                continue;
            }

            $subgroups[] = [
                'label'           => $label,
                'key'             => $key,
                'allowed'         => $allowed,
                'allow_additional'=> $allow_additional,
                'base_grams'      => $base_grams,
            ];
        }

        if (!empty($subgroups)) {
            update_post_meta($post_id, '_fpc_subgroups', $subgroups);
        } else {
            delete_post_meta($post_id, '_fpc_subgroups');
        }
    } else {
        delete_post_meta($post_id, '_fpc_subgroups');
    }
}
