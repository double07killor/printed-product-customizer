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
    $groups = get_post_meta($post->ID, '_fpc_filament_groups', true);
    if (!is_array($groups)) {
        $groups = [];
    }
    ?>
    <div id="fpc_filament_groups_panel" class="panel woocommerce_options_panel hidden">
        <div class="fpc-repeatable-wrapper">
            <div class="fpc-repeatable-container">
                <div class="fpc-repeatable-row fpc-template" style="display:none;">
                    <p class="form-field">
                        <label><?php _e('Group Label', 'printed-product-customizer'); ?></label>
                        <input type="text" class="short fpc-generate-key" name="fpc_filament_groups[__INDEX__][label]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Group Key', 'printed-product-customizer'); ?></label>
                        <input type="text" class="short fpc-key-field" name="fpc_filament_groups[__INDEX__][key]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Required', 'printed-product-customizer'); ?></label>
                        <input type="checkbox" name="fpc_filament_groups[__INDEX__][required]" value="1" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Additional Group', 'printed-product-customizer'); ?></label>
                        <input type="checkbox" name="fpc_filament_groups[__INDEX__][additional]" value="1" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Default Filament', 'printed-product-customizer'); ?></label>
                        <input type="text" class="short" name="fpc_filament_groups[__INDEX__][default_filament]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Allowed Materials', 'printed-product-customizer'); ?></label>
                        <label><input type="checkbox" name="fpc_filament_groups[__INDEX__][materials][]" value="PETG" /> PETG</label>
                        <label><input type="checkbox" name="fpc_filament_groups[__INDEX__][materials][]" value="TPU" /> TPU</label>
                        <label><input type="checkbox" name="fpc_filament_groups[__INDEX__][materials][]" value="PLA" /> PLA</label>
                    </p>
                    <p class="form-field">
                        <label><?php _e('Color Whitelist (slugs)', 'printed-product-customizer'); ?></label>
                        <input type="text" class="short" name="fpc_filament_groups[__INDEX__][color_whitelist]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Color Blacklist (slugs)', 'printed-product-customizer'); ?></label>
                        <input type="text" class="short" name="fpc_filament_groups[__INDEX__][color_blacklist]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Base Grams', 'printed-product-customizer'); ?></label>
                        <input type="number" step="any" class="short" name="fpc_filament_groups[__INDEX__][base_grams]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Waste Grams', 'printed-product-customizer'); ?></label>
                        <input type="number" step="any" class="short" name="fpc_filament_groups[__INDEX__][waste_grams]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Max Price/kg before surcharge', 'printed-product-customizer'); ?></label>
                        <input type="number" step="any" class="short" name="fpc_filament_groups[__INDEX__][max_price]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Changeover Fee', 'printed-product-customizer'); ?></label>
                        <input type="number" step="any" class="short" name="fpc_filament_groups[__INDEX__][changeover_fee]" />
                    </p>
                    <p><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></p>
                </div>
                <?php foreach ($groups as $index => $group) : ?>
                    <div class="fpc-repeatable-row">
                        <p class="form-field">
                            <label><?php _e('Group Label', 'printed-product-customizer'); ?></label>
                            <input type="text" class="short fpc-generate-key" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][label]" value="<?php echo esc_attr($group['label'] ?? ''); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Group Key', 'printed-product-customizer'); ?></label>
                            <input type="text" class="short fpc-key-field" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][key]" value="<?php echo esc_attr($group['key'] ?? ''); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Required', 'printed-product-customizer'); ?></label>
                            <input type="checkbox" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][required]" value="1" <?php checked(!empty($group['required'])); ?> />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Additional Group', 'printed-product-customizer'); ?></label>
                            <input type="checkbox" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][additional]" value="1" <?php checked(!empty($group['additional'])); ?> />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Default Filament', 'printed-product-customizer'); ?></label>
                            <input type="text" class="short" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][default_filament]" value="<?php echo esc_attr($group['default_filament'] ?? ''); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Allowed Materials', 'printed-product-customizer'); ?></label>
                            <?php $materials = $group['materials'] ?? []; ?>
                            <label><input type="checkbox" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][materials][]" value="PETG" <?php checked(in_array('PETG', $materials, true)); ?> /> PETG</label>
                            <label><input type="checkbox" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][materials][]" value="TPU" <?php checked(in_array('TPU', $materials, true)); ?> /> TPU</label>
                            <label><input type="checkbox" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][materials][]" value="PLA" <?php checked(in_array('PLA', $materials, true)); ?> /> PLA</label>
                        </p>
                        <p class="form-field">
                            <label><?php _e('Color Whitelist (slugs)', 'printed-product-customizer'); ?></label>
                            <input type="text" class="short" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][color_whitelist]" value="<?php echo esc_attr($group['color_whitelist'] ?? ''); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Color Blacklist (slugs)', 'printed-product-customizer'); ?></label>
                            <input type="text" class="short" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][color_blacklist]" value="<?php echo esc_attr($group['color_blacklist'] ?? ''); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Base Grams', 'printed-product-customizer'); ?></label>
                            <input type="number" step="any" class="short" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][base_grams]" value="<?php echo esc_attr($group['base_grams'] ?? ''); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Waste Grams', 'printed-product-customizer'); ?></label>
                            <input type="number" step="any" class="short" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][waste_grams]" value="<?php echo esc_attr($group['waste_grams'] ?? ''); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Max Price/kg before surcharge', 'printed-product-customizer'); ?></label>
                            <input type="number" step="any" class="short" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][max_price]" value="<?php echo esc_attr($group['max_price'] ?? ''); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Changeover Fee', 'printed-product-customizer'); ?></label>
                            <input type="number" step="any" class="short" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][changeover_fee]" value="<?php echo esc_attr($group['changeover_fee'] ?? ''); ?>" />
                        </p>
                        <p><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <p><button type="button" class="button fpc-repeatable-add"><?php _e('Add Filament Group', 'printed-product-customizer'); ?></button></p>
        </div>
    </div>
    <?php
}

// Save meta
add_action('woocommerce_process_product_meta', 'fpc_filament_groups_save');
function fpc_filament_groups_save($post_id) {
    if (isset($_POST['fpc_filament_groups']) && is_array($_POST['fpc_filament_groups'])) {
        $groups = [];
        foreach ($_POST['fpc_filament_groups'] as $group) {
            $groups[] = [
                'label'           => sanitize_text_field($group['label'] ?? ''),
                'key'             => sanitize_title($group['key'] ?? ''),
                'required'        => !empty($group['required']) ? 1 : 0,
                'additional'      => !empty($group['additional']) ? 1 : 0,
                'default_filament'=> sanitize_text_field($group['default_filament'] ?? ''),
                'materials'       => array_map('sanitize_text_field', $group['materials'] ?? []),
                'color_whitelist' => sanitize_text_field($group['color_whitelist'] ?? ''),
                'color_blacklist' => sanitize_text_field($group['color_blacklist'] ?? ''),
                'base_grams'      => floatval($group['base_grams'] ?? 0),
                'waste_grams'     => floatval($group['waste_grams'] ?? 0),
                'max_price'       => floatval($group['max_price'] ?? 0),
                'changeover_fee'  => floatval($group['changeover_fee'] ?? 0),
            ];
        }
        update_post_meta($post_id, '_fpc_filament_groups', $groups);
    } else {
        delete_post_meta($post_id, '_fpc_filament_groups');
    }
}
