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
    $max_groups = get_post_meta($post->ID, '_fpc_filament_groups_max', true);
    if (!$max_groups) {
        $max_groups = max(1, count($groups));
    }
    $additional_rules = get_post_meta($post->ID, '_fpc_additional_group_rules', true);
    if (!is_array($additional_rules)) {
        $additional_rules = [];
    }
    $sync      = new FPC_Filament_Sync();
    $inventory = $sync->get_inventory();
    $materials_list = [];
    foreach ($inventory as $item) {
        if (!empty($item['material'])) {
            $materials_list[$item['material']] = true;
        }
    }
    $materials_list = array_keys($materials_list);
    sort($materials_list);
    ?>
    <div id="fpc_filament_groups_panel" class="panel woocommerce_options_panel hidden">
        <div class="fpc-repeatable-wrapper">
            <div class="fpc-repeatable-container">
                <div class="fpc-repeatable-row fpc-template" data-default-title="<?php esc_attr_e('Group', 'printed-product-customizer'); ?>" style="display:none;">
                    <h4 class="fpc-group-toggle"><span class="fpc-group-title"><?php _e('Group', 'printed-product-customizer'); ?></span></h4>
                    <div class="fpc-group-fields">
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
                            <label><?php _e('Allowed Materials', 'printed-product-customizer'); ?></label>
                            <select class="fpc-materials wc-enhanced-select" multiple="multiple" style="width:100%;" name="fpc_filament_groups[__INDEX__][materials][]">
                                <?php foreach ($materials_list as $mat) : ?>
                                    <option value="<?php echo esc_attr($mat); ?>" <?php selected($mat, 'PETG'); ?>><?php echo esc_html($mat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                        <p class="form-field">
                            <label><?php _e('Default Filament', 'printed-product-customizer'); ?></label>
                            <select class="fpc-default-filament wc-enhanced-select" style="width:100%;" name="fpc_filament_groups[__INDEX__][default_filament]">
                                <option value="psm-m-bk-petg" selected></option>
                            </select>
                        </p>
                        <p class="form-field">
                            <label><?php _e('Filament Whitelist', 'printed-product-customizer'); ?></label>
                            <select class="fpc-filament-whitelist wc-enhanced-select" multiple="multiple" style="width:100%;" name="fpc_filament_groups[__INDEX__][filament_whitelist][]"></select>
                            <span class="description"><?php _e('Leave empty to allow all', 'printed-product-customizer'); ?></span>
                        </p>
                        <p class="form-field">
                            <label><?php _e('Allow Override', 'printed-product-customizer'); ?></label>
                            <input type="checkbox" name="fpc_filament_groups[__INDEX__][allow_override]" value="1" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Override Message', 'printed-product-customizer'); ?></label>
                            <input type="text" class="short" name="fpc_filament_groups[__INDEX__][override_message]" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Override Surcharge', 'printed-product-customizer'); ?></label>
                            <input type="number" step="any" class="short" name="fpc_filament_groups[__INDEX__][override_surcharge]" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Filament Blacklist', 'printed-product-customizer'); ?></label>
                            <select class="fpc-filament-blacklist wc-enhanced-select" multiple="multiple" style="width:100%;" name="fpc_filament_groups[__INDEX__][filament_blacklist][]"></select>
                        </p>
                        <p class="form-field">
                            <label><?php _e('Exempt all Filaments', 'printed-product-customizer'); ?></label>
                            <input type="checkbox" class="fpc-exempt-all" name="fpc_filament_groups[__INDEX__][exempt_all_filaments]" value="1" />
                        </p>
                        <p class="form-field fpc-exempt-filaments-field">
                            <label><?php _e('Exempt Filaments', 'printed-product-customizer'); ?></label>
                            <select class="fpc-exempt-filaments wc-enhanced-select" multiple="multiple" style="width:100%;" name="fpc_filament_groups[__INDEX__][exempt_filaments][]"></select>
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
                        <p class="form-field fpc-additional-fee-field" style="display:none;">
                            <label><?php _e('Additional Group Fee', 'printed-product-customizer'); ?></label>
                            <input type="number" step="any" class="short" name="fpc_filament_groups[__INDEX__][additional_group_fee]" />
                        </p>
                        <p><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></p>
                    </div>
                </div>
                <?php foreach ($groups as $index => $group) : ?>
                    <div class="fpc-repeatable-row" data-default-title="<?php esc_attr_e('Group', 'printed-product-customizer'); ?>">
                        <h4 class="fpc-group-toggle"><span class="fpc-group-title"><?php echo esc_html($group['label'] ?? __('Group', 'printed-product-customizer')); ?></span></h4>
                        <div class="fpc-group-fields">
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
                            <?php
                            $materials  = $group['materials'] ?? [];
                            $blacklist  = $group['filament_blacklist'] ?? [];
                            $whitelist  = $group['filament_whitelist'] ?? [];
                            $filtered   = array_filter($inventory, function($item) use ($materials) {
                                return empty($materials) || in_array($item['material'], $materials, true);
                            });
                            $filtered_no_blacklist = array_filter($filtered, function($item, $slug) use ($blacklist) {
                                return !in_array($slug, $blacklist, true);
                            }, ARRAY_FILTER_USE_BOTH);
                            $allowed_options = empty($whitelist) ? $filtered_no_blacklist : array_filter($filtered_no_blacklist, function($item, $slug) use ($whitelist) {
                                return in_array($slug, $whitelist, true);
                            }, ARRAY_FILTER_USE_BOTH);
                            ?>
                            <p class="form-field">
                                <label><?php _e('Allowed Materials', 'printed-product-customizer'); ?></label>
                                <select class="fpc-materials wc-enhanced-select" multiple="multiple" style="width:100%;" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][materials][]">
                                    <?php foreach ($materials_list as $mat) : ?>
                                        <option value="<?php echo esc_attr($mat); ?>" <?php selected(in_array($mat, $materials, true)); ?>><?php echo esc_html($mat); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                            <p class="form-field">
                                <label><?php _e('Default Filament', 'printed-product-customizer'); ?></label>
                                <select class="fpc-default-filament wc-enhanced-select" style="width:100%;" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][default_filament]">
                                    <option value=""></option>
                                    <?php foreach ($filtered_no_blacklist as $slug => $item) : ?>
                                        <option value="<?php echo esc_attr($slug); ?>" <?php selected($group['default_filament'] ?? '', $slug); ?>><?php echo esc_html($slug); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                            <p class="form-field">
                                <label><?php _e('Filament Whitelist', 'printed-product-customizer'); ?></label>
                                <select class="fpc-filament-whitelist wc-enhanced-select" multiple="multiple" style="width:100%;" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][filament_whitelist][]">
                                    <?php foreach ($filtered_no_blacklist as $slug => $item) : ?>
                                        <option value="<?php echo esc_attr($slug); ?>" <?php selected(in_array($slug, $whitelist, true)); ?>><?php echo esc_html($slug); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="description"><?php _e('Leave empty to allow all', 'printed-product-customizer'); ?></span>
                            </p>
                            <p class="form-field">
                                <label><?php _e('Allow Override', 'printed-product-customizer'); ?></label>
                                <input type="checkbox" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][allow_override]" value="1" <?php checked(!empty($group['allow_override'])); ?> />
                            </p>
                            <p class="form-field">
                                <label><?php _e('Override Message', 'printed-product-customizer'); ?></label>
                                <input type="text" class="short" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][override_message]" value="<?php echo esc_attr($group['override_message'] ?? ''); ?>" />
                            </p>
                            <p class="form-field">
                                <label><?php _e('Override Surcharge', 'printed-product-customizer'); ?></label>
                                <input type="number" step="any" class="short" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][override_surcharge]" value="<?php echo esc_attr($group['override_surcharge'] ?? ''); ?>" />
                            </p>
                            <p class="form-field">
                                <label><?php _e('Filament Blacklist', 'printed-product-customizer'); ?></label>
                                <select class="fpc-filament-blacklist wc-enhanced-select" multiple="multiple" style="width:100%;" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][filament_blacklist][]">
                                    <?php foreach ($filtered as $slug => $item) : ?>
                                        <option value="<?php echo esc_attr($slug); ?>" <?php selected(in_array($slug, $blacklist, true)); ?>><?php echo esc_html($slug); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                            <p class="form-field">
                                <label><?php _e('Exempt all Filaments', 'printed-product-customizer'); ?></label>
                                <input type="checkbox" class="fpc-exempt-all" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][exempt_all_filaments]" value="1" <?php checked(!empty($group['exempt_all_filaments'])); ?> />
                            </p>
                            <p class="form-field fpc-exempt-filaments-field">
                                <label><?php _e('Exempt Filaments', 'printed-product-customizer'); ?></label>
                                <select class="fpc-exempt-filaments wc-enhanced-select" multiple="multiple" style="width:100%;" name="fpc_filament_groups[<?php echo esc_attr($index); ?>][exempt_filaments][]">
                                    <?php $exempt = $group['exempt_filaments'] ?? []; foreach ($allowed_options as $slug => $item) : ?>
                                        <option value="<?php echo esc_attr($slug); ?>" <?php selected(in_array($slug, $exempt, true)); ?>><?php echo esc_html($slug); ?></option>
                                    <?php endforeach; ?>
                                </select>
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
                            <p><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <p><button type="button" class="button fpc-repeatable-add"><?php _e('Add Filament Group', 'printed-product-customizer'); ?></button></p>
            <p class="form-field">
                <label><?php _e('Max Groups', 'printed-product-customizer'); ?></label>
                <select name="fpc_filament_groups_max" class="short">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <option value="<?php echo esc_attr($i); ?>" <?php selected($max_groups, $i); ?>><?php echo esc_html($i); ?></option>
                    <?php endfor; ?>
                </select>
            </p>
            <p><button type="button" class="button fpc-additional-rules-toggle"><?php _e('Additional Group Rules', 'printed-product-customizer'); ?></button></p>
            <div id="fpc-additional-rules" data-label="<?php esc_attr_e('Additional Group Rules', 'printed-product-customizer'); ?>" style="display:none;"></div>
        </div>
    </div>
    <script type="text/javascript">
        window.fpcFilamentInventory = <?php echo wp_json_encode($inventory); ?>;
        window.fpcAdditionalGroupRules = <?php echo wp_json_encode($additional_rules); ?>;
    </script>
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
                'default_filament'=> sanitize_text_field($group['default_filament'] ?? ''),
                'materials'       => array_map('sanitize_text_field', $group['materials'] ?? []),
                'filament_whitelist' => array_map('sanitize_text_field', $group['filament_whitelist'] ?? []),
                'filament_blacklist' => array_map('sanitize_text_field', $group['filament_blacklist'] ?? []),
                'exempt_all_filaments' => !empty($group['exempt_all_filaments']) ? 1 : 0,
                'exempt_filaments' => array_map('sanitize_text_field', $group['exempt_filaments'] ?? []),
                'allow_override'  => !empty($group['allow_override']) ? 1 : 0,
                'override_message'=> sanitize_text_field($group['override_message'] ?? ''),
                'override_surcharge' => floatval($group['override_surcharge'] ?? 0),
                'base_grams'      => floatval($group['base_grams'] ?? 0),
                'waste_grams'     => floatval($group['waste_grams'] ?? 0),
                'max_price'       => floatval($group['max_price'] ?? 0),
            ];
        }
        update_post_meta($post_id, '_fpc_filament_groups', $groups);
    } else {
        delete_post_meta($post_id, '_fpc_filament_groups');
    }

    if (isset($_POST['fpc_filament_groups_max'])) {
        update_post_meta($post_id, '_fpc_filament_groups_max', intval($_POST['fpc_filament_groups_max']));
    } else {
        delete_post_meta($post_id, '_fpc_filament_groups_max');
    }

    if (isset($_POST['fpc_additional_group_rules']) && is_array($_POST['fpc_additional_group_rules'])) {
        $group = $_POST['fpc_additional_group_rules'];
        $rules = [
            'label'           => sanitize_text_field($group['label'] ?? ''),
            'key'             => sanitize_title($group['key'] ?? ''),
            'required'        => !empty($group['required']) ? 1 : 0,
            'default_filament'=> sanitize_text_field($group['default_filament'] ?? ''),
            'materials'       => array_map('sanitize_text_field', $group['materials'] ?? []),
            'filament_whitelist' => array_map('sanitize_text_field', $group['filament_whitelist'] ?? []),
            'filament_blacklist' => array_map('sanitize_text_field', $group['filament_blacklist'] ?? []),
            'exempt_all_filaments' => !empty($group['exempt_all_filaments']) ? 1 : 0,
            'exempt_filaments' => array_map('sanitize_text_field', $group['exempt_filaments'] ?? []),
            'allow_override'  => !empty($group['allow_override']) ? 1 : 0,
            'override_message'=> sanitize_text_field($group['override_message'] ?? ''),
            'override_surcharge' => floatval($group['override_surcharge'] ?? 0),
            'base_grams'      => floatval($group['base_grams'] ?? 0),
            'waste_grams'     => floatval($group['waste_grams'] ?? 0),
            'max_price'       => floatval($group['max_price'] ?? 0),
            'additional_group_fee' => floatval($group['additional_group_fee'] ?? 0),
        ];
        update_post_meta($post_id, '_fpc_additional_group_rules', $rules);
    } else {
        delete_post_meta($post_id, '_fpc_additional_group_rules');
    }
}
