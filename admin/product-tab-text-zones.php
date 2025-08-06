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
    $zones = get_post_meta($post->ID, '_fpc_text_zones', true);
    $zones = is_array($zones) ? $zones : [];
    $assignments = get_post_meta($post->ID, '_fpc_body_assignments', true);
    $assignments = is_array($assignments) ? $assignments : [];
    $bodies = [];
    foreach ($assignments as $as) {
        if (!empty($as['body'])) {
            $bodies[] = $as['body'];
        }
    }
    $filament_groups = get_post_meta($post->ID, '_fpc_filament_groups', true);
    $filament_groups = is_array($filament_groups) ? $filament_groups : [];
    ?>
    <div id="fpc_text_zones_panel" class="panel woocommerce_options_panel hidden">
        <div class="fpc-repeatable-wrapper">
            <div class="fpc-repeatable-container">
                <div class="fpc-repeatable-row fpc-template" style="display:none;">
                    <p class="form-field">
                        <label><?php _e('Label', 'printed-product-customizer'); ?></label>
                        <input type="text" class="short fpc-generate-key" name="fpc_text_zones[__INDEX__][label]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Key', 'printed-product-customizer'); ?></label>
                        <input type="text" class="short fpc-key-field" name="fpc_text_zones[__INDEX__][key]" />
                    </p>
                    <p class="form-field">
                        <label><?php _e('Target Body', 'printed-product-customizer'); ?></label>
                        <select name="fpc_text_zones[__INDEX__][body]">
                            <option value=""><?php _e('Select body', 'printed-product-customizer'); ?></option>
                            <?php foreach ($bodies as $b) : ?>
                                <option value="<?php echo esc_attr($b); ?>"><?php echo esc_html($b); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p class="form-field"><label><?php _e('Plane Origin', 'printed-product-customizer'); ?></label> x<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][origin][x]" /> y<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][origin][y]" /> z<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][origin][z]" /></p>
                    <p class="form-field"><label><?php _e('Normal Vector', 'printed-product-customizer'); ?></label> x<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][normal][x]" /> y<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][normal][y]" /> z<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][normal][z]" /></p>
                    <p class="form-field"><label><?php _e('Up Vector', 'printed-product-customizer'); ?></label> x<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][up][x]" /> y<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][up][y]" /> z<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][up][z]" /></p>
                    <p class="form-field"><label><?php _e('BBox Width/Height', 'printed-product-customizer'); ?></label> w<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][bbox][w]" /> h<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][bbox][h]" /></p>
                    <p class="form-field">
                        <label><?php _e('Text Alignment', 'printed-product-customizer'); ?></label>
                        <select name="fpc_text_zones[__INDEX__][align]">
                            <option value="left">left</option>
                            <option value="center">center</option>
                            <option value="right">right</option>
                        </select>
                    </p>
                    <p class="form-field"><label><?php _e('Max Length', 'printed-product-customizer'); ?></label><input type="number" class="short" name="fpc_text_zones[__INDEX__][max_length]" /></p>
                    <p class="form-field"><label><?php _e('Font Whitelist', 'printed-product-customizer'); ?></label><input type="text" class="short" name="fpc_text_zones[__INDEX__][fonts]" /></p>
                    <p class="form-field"><label><?php _e('Min Font Size', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][min_font]" /> <label><?php _e('Max Font Size', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][max_font]" /></p>
                    <p class="form-field"><label><?php _e('Allow User to Select Style?', 'printed-product-customizer'); ?></label><input type="checkbox" name="fpc_text_zones[__INDEX__][allow_style]" value="1" /></p>
                    <p class="form-field"><label><?php _e('Default Style', 'printed-product-customizer'); ?></label><select name="fpc_text_zones[__INDEX__][default_style]"><option value="recessed">recessed</option><option value="smooth">smooth</option><option value="embossed">embossed</option></select></p>
                    <p class="form-field"><label><?php _e('Depth Offsets', 'printed-product-customizer'); ?></label> recessed<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][depth][recessed]" /> smooth<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][depth][smooth]" /> embossed<input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][depth][embossed]" /></p>
                    <p class="form-field"><label><?php _e('Color Inlay Depth', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][color_inlay]" /></p>
                    <p class="form-field">
                        <label><?php _e('Allowed Filament Groups', 'printed-product-customizer'); ?></label>
                        <select multiple="multiple" name="fpc_text_zones[__INDEX__][allowed_filaments][]">
                            <?php foreach ($filament_groups as $fg) : if (!empty($fg['key'])) : ?>
                                <option value="<?php echo esc_attr($fg['key']); ?>"><?php echo esc_html($fg['label']); ?></option>
                            <?php endif; endforeach; ?>
                        </select>
                    </p>
                    <p class="form-field"><label><?php _e('Changeover Fee', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_text_zones[__INDEX__][changeover_fee]" /></p>
                    <p><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></p>
                </div>
                <?php foreach ($zones as $index => $zone) : ?>
                    <div class="fpc-repeatable-row">
                        <p class="form-field">
                            <label><?php _e('Label', 'printed-product-customizer'); ?></label>
                            <input type="text" class="short fpc-generate-key" name="fpc_text_zones[<?php echo esc_attr($index); ?>][label]" value="<?php echo esc_attr($zone['label'] ?? ''); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Key', 'printed-product-customizer'); ?></label>
                            <input type="text" class="short fpc-key-field" name="fpc_text_zones[<?php echo esc_attr($index); ?>][key]" value="<?php echo esc_attr($zone['key'] ?? ''); ?>" />
                        </p>
                        <p class="form-field">
                            <label><?php _e('Target Body', 'printed-product-customizer'); ?></label>
                            <select name="fpc_text_zones[<?php echo esc_attr($index); ?>][body]">
                                <option value=""><?php _e('Select body', 'printed-product-customizer'); ?></option>
                                <?php foreach ($bodies as $b) : ?>
                                    <option value="<?php echo esc_attr($b); ?>" <?php selected(isset($zone['body']) && $zone['body'] === $b); ?>><?php echo esc_html($b); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                        <p class="form-field"><label><?php _e('Plane Origin', 'printed-product-customizer'); ?></label> x<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][origin][x]" value="<?php echo esc_attr($zone['origin']['x'] ?? ''); ?>" /> y<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][origin][y]" value="<?php echo esc_attr($zone['origin']['y'] ?? ''); ?>" /> z<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][origin][z]" value="<?php echo esc_attr($zone['origin']['z'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Normal Vector', 'printed-product-customizer'); ?></label> x<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][normal][x]" value="<?php echo esc_attr($zone['normal']['x'] ?? ''); ?>" /> y<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][normal][y]" value="<?php echo esc_attr($zone['normal']['y'] ?? ''); ?>" /> z<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][normal][z]" value="<?php echo esc_attr($zone['normal']['z'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Up Vector', 'printed-product-customizer'); ?></label> x<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][up][x]" value="<?php echo esc_attr($zone['up']['x'] ?? ''); ?>" /> y<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][up][y]" value="<?php echo esc_attr($zone['up']['y'] ?? ''); ?>" /> z<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][up][z]" value="<?php echo esc_attr($zone['up']['z'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('BBox Width/Height', 'printed-product-customizer'); ?></label> w<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][bbox][w]" value="<?php echo esc_attr($zone['bbox']['w'] ?? ''); ?>" /> h<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][bbox][h]" value="<?php echo esc_attr($zone['bbox']['h'] ?? ''); ?>" /></p>
                        <p class="form-field">
                            <label><?php _e('Text Alignment', 'printed-product-customizer'); ?></label>
                            <select name="fpc_text_zones[<?php echo esc_attr($index); ?>][align]">
                                <option value="left" <?php selected(($zone['align'] ?? '') === 'left'); ?>>left</option>
                                <option value="center" <?php selected(($zone['align'] ?? '') === 'center'); ?>>center</option>
                                <option value="right" <?php selected(($zone['align'] ?? '') === 'right'); ?>>right</option>
                            </select>
                        </p>
                        <p class="form-field"><label><?php _e('Max Length', 'printed-product-customizer'); ?></label><input type="number" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][max_length]" value="<?php echo esc_attr($zone['max_length'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Font Whitelist', 'printed-product-customizer'); ?></label><input type="text" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][fonts]" value="<?php echo esc_attr($zone['fonts'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Min Font Size', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][min_font]" value="<?php echo esc_attr($zone['min_font'] ?? ''); ?>" /> <label><?php _e('Max Font Size', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][max_font]" value="<?php echo esc_attr($zone['max_font'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Allow User to Select Style?', 'printed-product-customizer'); ?></label><input type="checkbox" name="fpc_text_zones[<?php echo esc_attr($index); ?>][allow_style]" value="1" <?php checked(!empty($zone['allow_style'])); ?> /></p>
                        <p class="form-field"><label><?php _e('Default Style', 'printed-product-customizer'); ?></label><select name="fpc_text_zones[<?php echo esc_attr($index); ?>][default_style]"><option value="recessed" <?php selected(($zone['default_style'] ?? '') === 'recessed'); ?>>recessed</option><option value="smooth" <?php selected(($zone['default_style'] ?? '') === 'smooth'); ?>>smooth</option><option value="embossed" <?php selected(($zone['default_style'] ?? '') === 'embossed'); ?>>embossed</option></select></p>
                        <p class="form-field"><label><?php _e('Depth Offsets', 'printed-product-customizer'); ?></label> recessed<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][depth][recessed]" value="<?php echo esc_attr($zone['depth']['recessed'] ?? ''); ?>" /> smooth<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][depth][smooth]" value="<?php echo esc_attr($zone['depth']['smooth'] ?? ''); ?>" /> embossed<input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][depth][embossed]" value="<?php echo esc_attr($zone['depth']['embossed'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Color Inlay Depth', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][color_inlay]" value="<?php echo esc_attr($zone['color_inlay'] ?? ''); ?>" /></p>
                        <p class="form-field">
                            <label><?php _e('Allowed Filament Groups', 'printed-product-customizer'); ?></label>
                            <select multiple="multiple" name="fpc_text_zones[<?php echo esc_attr($index); ?>][allowed_filaments][]">
                                <?php $allowedf = $zone['allowed_filaments'] ?? []; foreach ($filament_groups as $fg) : if (!empty($fg['key'])) : ?>
                                    <option value="<?php echo esc_attr($fg['key']); ?>" <?php selected(in_array($fg['key'], $allowedf, true)); ?>><?php echo esc_html($fg['label']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </p>
                        <p class="form-field"><label><?php _e('Changeover Fee', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_text_zones[<?php echo esc_attr($index); ?>][changeover_fee]" value="<?php echo esc_attr($zone['changeover_fee'] ?? ''); ?>" /></p>
                        <p><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <p><button type="button" class="button fpc-repeatable-add"><?php _e('Add Text Zone', 'printed-product-customizer'); ?></button></p>
        </div>
    </div>
    <?php
}

add_action('woocommerce_process_product_meta', 'fpc_text_zones_save');
function fpc_text_zones_save($post_id) {
    if (isset($_POST['fpc_text_zones']) && is_array($_POST['fpc_text_zones'])) {
        $zones = [];
        foreach ($_POST['fpc_text_zones'] as $zone) {
            $zones[] = [
                'label'           => sanitize_text_field($zone['label'] ?? ''),
                'key'             => sanitize_title($zone['key'] ?? ''),
                'body'            => sanitize_text_field($zone['body'] ?? ''),
                'origin'          => [
                    'x' => floatval($zone['origin']['x'] ?? 0),
                    'y' => floatval($zone['origin']['y'] ?? 0),
                    'z' => floatval($zone['origin']['z'] ?? 0),
                ],
                'normal'          => [
                    'x' => floatval($zone['normal']['x'] ?? 0),
                    'y' => floatval($zone['normal']['y'] ?? 0),
                    'z' => floatval($zone['normal']['z'] ?? 0),
                ],
                'up'              => [
                    'x' => floatval($zone['up']['x'] ?? 0),
                    'y' => floatval($zone['up']['y'] ?? 0),
                    'z' => floatval($zone['up']['z'] ?? 0),
                ],
                'bbox'            => [
                    'w' => floatval($zone['bbox']['w'] ?? 0),
                    'h' => floatval($zone['bbox']['h'] ?? 0),
                ],
                'align'           => sanitize_text_field($zone['align'] ?? ''),
                'max_length'      => intval($zone['max_length'] ?? 0),
                'fonts'           => sanitize_text_field($zone['fonts'] ?? ''),
                'min_font'        => floatval($zone['min_font'] ?? 0),
                'max_font'        => floatval($zone['max_font'] ?? 0),
                'allow_style'     => !empty($zone['allow_style']) ? 1 : 0,
                'default_style'   => sanitize_text_field($zone['default_style'] ?? ''),
                'depth'           => [
                    'recessed' => floatval($zone['depth']['recessed'] ?? 0),
                    'smooth'   => floatval($zone['depth']['smooth'] ?? 0),
                    'embossed' => floatval($zone['depth']['embossed'] ?? 0),
                ],
                'color_inlay'     => floatval($zone['color_inlay'] ?? 0),
                'allowed_filaments'=> array_map('sanitize_text_field', $zone['allowed_filaments'] ?? []),
                'changeover_fee'  => floatval($zone['changeover_fee'] ?? 0),
            ];
        }
        update_post_meta($post_id, '_fpc_text_zones', $zones);
    } else {
        delete_post_meta($post_id, '_fpc_text_zones');
    }
}
