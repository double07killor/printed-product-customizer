<?php
if (!defined('ABSPATH')) {
    exit;
}

add_filter('woocommerce_product_data_tabs', 'fpc_logo_zones_product_data_tab');
function fpc_logo_zones_product_data_tab($tabs) {
    $tabs['fpc_logo_zones'] = [
        'label'    => __('Logo Zones', 'printed-product-customizer'),
        'target'   => 'fpc_logo_zones_panel',
        'class'    => ['show_if_simple', 'show_if_variable'],
        'priority' => 29,
    ];
    return $tabs;
}

add_action('woocommerce_product_data_panels', 'fpc_logo_zones_product_data_panel');
function fpc_logo_zones_product_data_panel() {
    global $post;
    $zones = get_post_meta($post->ID, '_fpc_logo_zones', true);
    $zones = is_array($zones) ? $zones : [];
    $assignments = get_post_meta($post->ID, '_fpc_body_assignments', true);
    $assignments = is_array($assignments) ? $assignments : [];
    $bodies = [];
    foreach ($assignments as $as) {
        if (!empty($as['body'])) {
            $bodies[] = $as['body'];
        }
    }
    $subgroups = get_post_meta($post->ID, '_fpc_subgroups', true);
    $subgroups = is_array($subgroups) ? $subgroups : [];
    $filament_groups = get_post_meta($post->ID, '_fpc_filament_groups', true);
    $filament_groups = is_array($filament_groups) ? $filament_groups : [];
    $available_logos = apply_filters('fpc_available_logos', [
        'logo_a' => __('Logo A', 'printed-product-customizer'),
        'logo_b' => __('Logo B', 'printed-product-customizer'),
    ]);
    ?>
    <div id="fpc_logo_zones_panel" class="panel woocommerce_options_panel hidden">
        <div class="fpc-repeatable-wrapper">
            <div class="fpc-repeatable-container">
                <div class="fpc-repeatable-row fpc-template" style="display:none;">
                    <p class="form-field"><label><?php _e('Label', 'printed-product-customizer'); ?></label><input type="text" class="short fpc-generate-key" name="fpc_logo_zones[__INDEX__][label]" /></p>
                    <p class="form-field"><label><?php _e('Key', 'printed-product-customizer'); ?></label><input type="text" class="short fpc-key-field" name="fpc_logo_zones[__INDEX__][key]" /></p>
                    <p class="form-field"><label><?php _e('Target Body', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[__INDEX__][body]"><option value=""><?php _e('Select body', 'printed-product-customizer'); ?></option><?php foreach ($bodies as $b) : ?><option value="<?php echo esc_attr($b); ?>"><?php echo esc_html($b); ?></option><?php endforeach; ?></select></p>
                    <p class="form-field"><label><?php _e('Plane Origin', 'printed-product-customizer'); ?></label> x<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][origin][x]" /> y<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][origin][y]" /> z<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][origin][z]" /></p>
                    <p class="form-field"><label><?php _e('Normal Vector', 'printed-product-customizer'); ?></label> x<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][normal][x]" /> y<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][normal][y]" /> z<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][normal][z]" /></p>
                    <p class="form-field"><label><?php _e('Up Vector', 'printed-product-customizer'); ?></label> x<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][up][x]" /> y<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][up][y]" /> z<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][up][z]" /></p>
                    <p class="form-field"><label><?php _e('BBox Width/Height', 'printed-product-customizer'); ?></label> w<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][bbox][w]" /> h<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][bbox][h]" /></p>
                    <p class="form-field"><label><?php _e('Alignment', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[__INDEX__][align]"><option value="left">left</option><option value="center">center</option><option value="right">right</option></select></p>
                    <p class="form-field"><label><?php _e('Allowed Logos', 'printed-product-customizer'); ?></label><select multiple="multiple" name="fpc_logo_zones[__INDEX__][allowed_logos][]"><?php foreach ($available_logos as $k => $l) : ?><option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($l); ?></option><?php endforeach; ?></select></p>
                    <p class="form-field"><label><?php _e('Default Logo', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[__INDEX__][default_logo]"><option value=""><?php _e('None', 'printed-product-customizer'); ?></option><?php foreach ($available_logos as $k => $l) : ?><option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($l); ?></option><?php endforeach; ?></select></p>
                    <p class="form-field"><label><?php _e('Allow Removal?', 'printed-product-customizer'); ?></label><input type="checkbox" name="fpc_logo_zones[__INDEX__][allow_removal]" value="1" /></p>
                    <p class="form-field"><label><?php _e('Allow Swap?', 'printed-product-customizer'); ?></label><input type="checkbox" name="fpc_logo_zones[__INDEX__][allow_swap]" value="1" /></p>
                    <div class="form-field">
                        <label><?php _e('Price Adjustments', 'printed-product-customizer'); ?></label>
                        <table class="widefat">
                            <tbody class="fpc-price-adjust-container">
                                <tr class="fpc-price-row fpc-template" style="display:none;">
                                    <td><select name="fpc_logo_zones[__INDEX__][price_adjust][__PRICE_INDEX__][logo]"><option value=""></option><?php foreach ($available_logos as $k => $l) : ?><option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($l); ?></option><?php endforeach; ?></select></td>
                                    <td><input type="number" step="any" name="fpc_logo_zones[__INDEX__][price_adjust][__PRICE_INDEX__][price]" /></td>
                                    <td><button type="button" class="button fpc-price-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <p><button type="button" class="button fpc-price-add"><?php _e('Add Adjustment', 'printed-product-customizer'); ?></button></p>
                    </div>
                    <p class="form-field"><label><?php _e('Default Style', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[__INDEX__][default_style]"><option value="recessed">recessed</option><option value="smooth">smooth</option><option value="embossed">embossed</option></select></p>
                    <p class="form-field"><label><?php _e('Depth Offsets', 'printed-product-customizer'); ?></label> recessed<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][depth][recessed]" /> smooth<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][depth][smooth]" /> embossed<input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][depth][embossed]" /></p>
                    <p class="form-field"><label><?php _e('Color Inlay Depth', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][color_inlay]" /></p>
                    <p class="form-field"><label><?php _e('Color Mode', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[__INDEX__][color_mode]"><option value="single">single</option><option value="dual">dual</option></select></p>
                    <p class="form-field"><label><?php _e('Primary Subgroup', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[__INDEX__][primary][subgroup]"><option value=""><?php _e('Select subgroup', 'printed-product-customizer'); ?></option><?php foreach ($subgroups as $sg) : if (!empty($sg['key'])) : ?><option value="<?php echo esc_attr($sg['key']); ?>"><?php echo esc_html($sg['label']); ?></option><?php endif; endforeach; ?></select></p>
                    <p class="form-field"><label><?php _e('Primary Allowed Filament Groups', 'printed-product-customizer'); ?></label><select multiple="multiple" name="fpc_logo_zones[__INDEX__][primary][allowed][]"><?php foreach ($filament_groups as $fg) : if (!empty($fg['key'])) : ?><option value="<?php echo esc_attr($fg['key']); ?>"><?php echo esc_html($fg['label']); ?></option><?php endif; endforeach; ?></select></p>
                    <p class="form-field"><label><?php _e('Primary Default Filament Group', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[__INDEX__][primary][default]"><option value=""><?php _e('None', 'printed-product-customizer'); ?></option><?php foreach ($filament_groups as $fg) : if (!empty($fg['key'])) : ?><option value="<?php echo esc_attr($fg['key']); ?>"><?php echo esc_html($fg['label']); ?></option><?php endif; endforeach; ?></select></p>
                    <p class="form-field"><label><?php _e('Primary Changeover Fee', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][primary][changeover_fee]" /></p>
                    <p class="form-field"><label><?php _e('Secondary Subgroup', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[__INDEX__][secondary][subgroup]"><option value=""><?php _e('Select subgroup', 'printed-product-customizer'); ?></option><?php foreach ($subgroups as $sg) : if (!empty($sg['key'])) : ?><option value="<?php echo esc_attr($sg['key']); ?>"><?php echo esc_html($sg['label']); ?></option><?php endif; endforeach; ?></select></p>
                    <p class="form-field"><label><?php _e('Secondary Allowed Filament Groups', 'printed-product-customizer'); ?></label><select multiple="multiple" name="fpc_logo_zones[__INDEX__][secondary][allowed][]"><?php foreach ($filament_groups as $fg) : if (!empty($fg['key'])) : ?><option value="<?php echo esc_attr($fg['key']); ?>"><?php echo esc_html($fg['label']); ?></option><?php endif; endforeach; ?></select></p>
                    <p class="form-field"><label><?php _e('Secondary Default Filament Group', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[__INDEX__][secondary][default]"><option value=""><?php _e('None', 'printed-product-customizer'); ?></option><?php foreach ($filament_groups as $fg) : if (!empty($fg['key'])) : ?><option value="<?php echo esc_attr($fg['key']); ?>"><?php echo esc_html($fg['label']); ?></option><?php endif; endforeach; ?></select></p>
                    <p class="form-field"><label><?php _e('Secondary Changeover Fee', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_logo_zones[__INDEX__][secondary][changeover_fee]" /></p>
                    <p><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></p>
                </div>
                <?php foreach ($zones as $index => $zone) : ?>
                    <div class="fpc-repeatable-row">
                        <p class="form-field"><label><?php _e('Label', 'printed-product-customizer'); ?></label><input type="text" class="short fpc-generate-key" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][label]" value="<?php echo esc_attr($zone['label'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Key', 'printed-product-customizer'); ?></label><input type="text" class="short fpc-key-field" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][key]" value="<?php echo esc_attr($zone['key'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Target Body', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[<?php echo esc_attr($index); ?>][body]"><option value=""><?php _e('Select body', 'printed-product-customizer'); ?></option><?php foreach ($bodies as $b) : ?><option value="<?php echo esc_attr($b); ?>" <?php selected(isset($zone['body']) && $zone['body'] === $b); ?>><?php echo esc_html($b); ?></option><?php endforeach; ?></select></p>
                        <p class="form-field"><label><?php _e('Plane Origin', 'printed-product-customizer'); ?></label> x<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][origin][x]" value="<?php echo esc_attr($zone['origin']['x'] ?? ''); ?>" /> y<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][origin][y]" value="<?php echo esc_attr($zone['origin']['y'] ?? ''); ?>" /> z<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][origin][z]" value="<?php echo esc_attr($zone['origin']['z'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Normal Vector', 'printed-product-customizer'); ?></label> x<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][normal][x]" value="<?php echo esc_attr($zone['normal']['x'] ?? ''); ?>" /> y<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][normal][y]" value="<?php echo esc_attr($zone['normal']['y'] ?? ''); ?>" /> z<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][normal][z]" value="<?php echo esc_attr($zone['normal']['z'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Up Vector', 'printed-product-customizer'); ?></label> x<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][up][x]" value="<?php echo esc_attr($zone['up']['x'] ?? ''); ?>" /> y<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][up][y]" value="<?php echo esc_attr($zone['up']['y'] ?? ''); ?>" /> z<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][up][z]" value="<?php echo esc_attr($zone['up']['z'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('BBox Width/Height', 'printed-product-customizer'); ?></label> w<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][bbox][w]" value="<?php echo esc_attr($zone['bbox']['w'] ?? ''); ?>" /> h<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][bbox][h]" value="<?php echo esc_attr($zone['bbox']['h'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Alignment', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[<?php echo esc_attr($index); ?>][align]"><option value="left" <?php selected(($zone['align'] ?? '') === 'left'); ?>>left</option><option value="center" <?php selected(($zone['align'] ?? '') === 'center'); ?>>center</option><option value="right" <?php selected(($zone['align'] ?? '') === 'right'); ?>>right</option></select></p>
                        <p class="form-field"><label><?php _e('Allowed Logos', 'printed-product-customizer'); ?></label><select multiple="multiple" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][allowed_logos][]"><?php foreach ($available_logos as $k => $l) : ?><option value="<?php echo esc_attr($k); ?>" <?php selected(in_array($k, $zone['allowed_logos'] ?? [], true)); ?>><?php echo esc_html($l); ?></option><?php endforeach; ?></select></p>
                        <p class="form-field"><label><?php _e('Default Logo', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[<?php echo esc_attr($index); ?>][default_logo]"><option value=""><?php _e('None', 'printed-product-customizer'); ?></option><?php foreach ($available_logos as $k => $l) : ?><option value="<?php echo esc_attr($k); ?>" <?php selected(isset($zone['default_logo']) && $zone['default_logo'] === $k); ?>><?php echo esc_html($l); ?></option><?php endforeach; ?></select></p>
                        <p class="form-field"><label><?php _e('Allow Removal?', 'printed-product-customizer'); ?></label><input type="checkbox" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][allow_removal]" value="1" <?php checked(!empty($zone['allow_removal'])); ?> /></p>
                        <p class="form-field"><label><?php _e('Allow Swap?', 'printed-product-customizer'); ?></label><input type="checkbox" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][allow_swap]" value="1" <?php checked(!empty($zone['allow_swap'])); ?> /></p>
                        <div class="form-field">
                            <label><?php _e('Price Adjustments', 'printed-product-customizer'); ?></label>
                            <table class="widefat">
                                <tbody class="fpc-price-adjust-container">
                                    <tr class="fpc-price-row fpc-template" style="display:none;">
                                        <td><select name="fpc_logo_zones[<?php echo esc_attr($index); ?>][price_adjust][__PRICE_INDEX__][logo]"><option value=""></option><?php foreach ($available_logos as $k => $l) : ?><option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($l); ?></option><?php endforeach; ?></select></td>
                                        <td><input type="number" step="any" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][price_adjust][__PRICE_INDEX__][price]" /></td>
                                        <td><button type="button" class="button fpc-price-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></td>
                                    </tr>
                                    <?php if (!empty($zone['price_adjust'])) : foreach ($zone['price_adjust'] as $pi => $pa) : ?>
                                    <tr class="fpc-price-row">
                                        <td><select name="fpc_logo_zones[<?php echo esc_attr($index); ?>][price_adjust][<?php echo esc_attr($pi); ?>][logo]"><option value=""></option><?php foreach ($available_logos as $k => $l) : ?><option value="<?php echo esc_attr($k); ?>" <?php selected(isset($pa['logo']) && $pa['logo'] === $k); ?>><?php echo esc_html($l); ?></option><?php endforeach; ?></select></td>
                                        <td><input type="number" step="any" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][price_adjust][<?php echo esc_attr($pi); ?>][price]" value="<?php echo esc_attr($pa['price'] ?? ''); ?>" /></td>
                                        <td><button type="button" class="button fpc-price-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></td>
                                    </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                            <p><button type="button" class="button fpc-price-add"><?php _e('Add Adjustment', 'printed-product-customizer'); ?></button></p>
                        </div>
                        <p class="form-field"><label><?php _e('Default Style', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[<?php echo esc_attr($index); ?>][default_style]"><option value="recessed" <?php selected(($zone['default_style'] ?? '') === 'recessed'); ?>>recessed</option><option value="smooth" <?php selected(($zone['default_style'] ?? '') === 'smooth'); ?>>smooth</option><option value="embossed" <?php selected(($zone['default_style'] ?? '') === 'embossed'); ?>>embossed</option></select></p>
                        <p class="form-field"><label><?php _e('Depth Offsets', 'printed-product-customizer'); ?></label> recessed<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][depth][recessed]" value="<?php echo esc_attr($zone['depth']['recessed'] ?? ''); ?>" /> smooth<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][depth][smooth]" value="<?php echo esc_attr($zone['depth']['smooth'] ?? ''); ?>" /> embossed<input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][depth][embossed]" value="<?php echo esc_attr($zone['depth']['embossed'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Color Inlay Depth', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][color_inlay]" value="<?php echo esc_attr($zone['color_inlay'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Color Mode', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[<?php echo esc_attr($index); ?>][color_mode]"><option value="single" <?php selected(($zone['color_mode'] ?? '') === 'single'); ?>>single</option><option value="dual" <?php selected(($zone['color_mode'] ?? '') === 'dual'); ?>>dual</option></select></p>
                        <p class="form-field"><label><?php _e('Primary Subgroup', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[<?php echo esc_attr($index); ?>][primary][subgroup]"><option value=""><?php _e('Select subgroup', 'printed-product-customizer'); ?></option><?php foreach ($subgroups as $sg) : if (!empty($sg['key'])) : ?><option value="<?php echo esc_attr($sg['key']); ?>" <?php selected(isset($zone['primary']['subgroup']) && $zone['primary']['subgroup'] === $sg['key']); ?>><?php echo esc_html($sg['label']); ?></option><?php endif; endforeach; ?></select></p>
                        <p class="form-field"><label><?php _e('Primary Allowed Filament Groups', 'printed-product-customizer'); ?></label><select multiple="multiple" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][primary][allowed][]"><?php $pa = $zone['primary']['allowed'] ?? []; foreach ($filament_groups as $fg) : if (!empty($fg['key'])) : ?><option value="<?php echo esc_attr($fg['key']); ?>" <?php selected(in_array($fg['key'], $pa, true)); ?>><?php echo esc_html($fg['label']); ?></option><?php endif; endforeach; ?></select></p>
                        <p class="form-field"><label><?php _e('Primary Default Filament Group', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[<?php echo esc_attr($index); ?>][primary][default]"><option value=""><?php _e('None', 'printed-product-customizer'); ?></option><?php foreach ($filament_groups as $fg) : if (!empty($fg['key'])) : ?><option value="<?php echo esc_attr($fg['key']); ?>" <?php selected(isset($zone['primary']['default']) && $zone['primary']['default'] === $fg['key']); ?>><?php echo esc_html($fg['label']); ?></option><?php endif; endforeach; ?></select></p>
                        <p class="form-field"><label><?php _e('Primary Changeover Fee', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][primary][changeover_fee]" value="<?php echo esc_attr($zone['primary']['changeover_fee'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Secondary Subgroup', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[<?php echo esc_attr($index); ?>][secondary][subgroup]"><option value=""><?php _e('Select subgroup', 'printed-product-customizer'); ?></option><?php foreach ($subgroups as $sg) : if (!empty($sg['key'])) : ?><option value="<?php echo esc_attr($sg['key']); ?>" <?php selected(isset($zone['secondary']['subgroup']) && $zone['secondary']['subgroup'] === $sg['key']); ?>><?php echo esc_html($sg['label']); ?></option><?php endif; endforeach; ?></select></p>
                        <p class="form-field"><label><?php _e('Secondary Allowed Filament Groups', 'printed-product-customizer'); ?></label><select multiple="multiple" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][secondary][allowed][]"><?php $sa = $zone['secondary']['allowed'] ?? []; foreach ($filament_groups as $fg) : if (!empty($fg['key'])) : ?><option value="<?php echo esc_attr($fg['key']); ?>" <?php selected(in_array($fg['key'], $sa, true)); ?>><?php echo esc_html($fg['label']); ?></option><?php endif; endforeach; ?></select></p>
                        <p class="form-field"><label><?php _e('Secondary Default Filament Group', 'printed-product-customizer'); ?></label><select name="fpc_logo_zones[<?php echo esc_attr($index); ?>][secondary][default]"><option value=""><?php _e('None', 'printed-product-customizer'); ?></option><?php foreach ($filament_groups as $fg) : if (!empty($fg['key'])) : ?><option value="<?php echo esc_attr($fg['key']); ?>" <?php selected(isset($zone['secondary']['default']) && $zone['secondary']['default'] === $fg['key']); ?>><?php echo esc_html($fg['label']); ?></option><?php endif; endforeach; ?></select></p>
                        <p class="form-field"><label><?php _e('Secondary Changeover Fee', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_logo_zones[<?php echo esc_attr($index); ?>][secondary][changeover_fee]" value="<?php echo esc_attr($zone['secondary']['changeover_fee'] ?? ''); ?>" /></p>
                        <p><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <p><button type="button" class="button fpc-repeatable-add"><?php _e('Add Logo Zone', 'printed-product-customizer'); ?></button></p>
        </div>
    </div>
    <?php
}

add_action('woocommerce_process_product_meta', 'fpc_logo_zones_save');
function fpc_logo_zones_save($post_id) {
    if (isset($_POST['fpc_logo_zones']) && is_array($_POST['fpc_logo_zones'])) {
        $zones = [];
        foreach ($_POST['fpc_logo_zones'] as $zone) {
            $price_adjust = [];
            if (!empty($zone['price_adjust']) && is_array($zone['price_adjust'])) {
                foreach ($zone['price_adjust'] as $pa) {
                    $price_adjust[] = [
                        'logo'  => sanitize_text_field($pa['logo'] ?? ''),
                        'price' => floatval($pa['price'] ?? 0),
                    ];
                }
            }
            $zones[] = [
                'label'         => sanitize_text_field($zone['label'] ?? ''),
                'key'           => sanitize_title($zone['key'] ?? ''),
                'body'          => sanitize_text_field($zone['body'] ?? ''),
                'origin'        => [
                    'x' => floatval($zone['origin']['x'] ?? 0),
                    'y' => floatval($zone['origin']['y'] ?? 0),
                    'z' => floatval($zone['origin']['z'] ?? 0),
                ],
                'normal'        => [
                    'x' => floatval($zone['normal']['x'] ?? 0),
                    'y' => floatval($zone['normal']['y'] ?? 0),
                    'z' => floatval($zone['normal']['z'] ?? 0),
                ],
                'up'            => [
                    'x' => floatval($zone['up']['x'] ?? 0),
                    'y' => floatval($zone['up']['y'] ?? 0),
                    'z' => floatval($zone['up']['z'] ?? 0),
                ],
                'bbox'          => [
                    'w' => floatval($zone['bbox']['w'] ?? 0),
                    'h' => floatval($zone['bbox']['h'] ?? 0),
                ],
                'align'         => sanitize_text_field($zone['align'] ?? ''),
                'allowed_logos' => array_map('sanitize_text_field', $zone['allowed_logos'] ?? []),
                'default_logo'  => sanitize_text_field($zone['default_logo'] ?? ''),
                'allow_removal' => !empty($zone['allow_removal']) ? 1 : 0,
                'allow_swap'    => !empty($zone['allow_swap']) ? 1 : 0,
                'price_adjust'  => $price_adjust,
                'default_style' => sanitize_text_field($zone['default_style'] ?? ''),
                'depth'         => [
                    'recessed' => floatval($zone['depth']['recessed'] ?? 0),
                    'smooth'   => floatval($zone['depth']['smooth'] ?? 0),
                    'embossed' => floatval($zone['depth']['embossed'] ?? 0),
                ],
                'color_inlay'   => floatval($zone['color_inlay'] ?? 0),
                'color_mode'    => sanitize_text_field($zone['color_mode'] ?? 'single'),
                'primary'       => [
                    'subgroup'      => sanitize_text_field($zone['primary']['subgroup'] ?? ''),
                    'allowed'       => array_map('sanitize_text_field', $zone['primary']['allowed'] ?? []),
                    'default'       => sanitize_text_field($zone['primary']['default'] ?? ''),
                    'changeover_fee'=> floatval($zone['primary']['changeover_fee'] ?? 0),
                ],
                'secondary'     => [
                    'subgroup'      => sanitize_text_field($zone['secondary']['subgroup'] ?? ''),
                    'allowed'       => array_map('sanitize_text_field', $zone['secondary']['allowed'] ?? []),
                    'default'       => sanitize_text_field($zone['secondary']['default'] ?? ''),
                    'changeover_fee'=> floatval($zone['secondary']['changeover_fee'] ?? 0),
                ],
            ];
        }
        update_post_meta($post_id, '_fpc_logo_zones', $zones);
    } else {
        delete_post_meta($post_id, '_fpc_logo_zones');
    }
}
