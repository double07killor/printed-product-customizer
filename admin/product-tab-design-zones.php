<?php
if (!defined('ABSPATH')) {
    exit;
}

add_filter('woocommerce_product_data_tabs', 'fpc_design_zones_product_data_tab');
function fpc_design_zones_product_data_tab($tabs) {
    $tabs['fpc_design_zones'] = [
        'label'    => __('Design Zones', 'printed-product-customizer'),
        'target'   => 'fpc_design_zones_panel',
        'class'    => ['show_if_simple', 'show_if_variable'],
        'priority' => 28,
    ];
    return $tabs;
}

add_action('woocommerce_product_data_panels', 'fpc_design_zones_product_data_panel');
function fpc_design_zones_product_data_panel() {
    global $post;
    $zones = get_post_meta($post->ID, '_fpc_design_zones', true);
    $zones = is_array($zones) ? $zones : [];
    $assignments = get_post_meta($post->ID, '_fpc_body_assignments', true);
    $assignments = is_array($assignments) ? $assignments : [];
    $bodies = [];
    foreach ($assignments as $as) {
        if (!empty($as['body'])) {
            $bodies[] = $as['body'];
        }
    }
    ?>
    <div id="fpc_design_zones_panel" class="panel woocommerce_options_panel hidden">
        <div class="fpc-repeatable-wrapper">
            <div class="fpc-repeatable-container">
                <div class="fpc-repeatable-row fpc-template" data-default-title="<?php esc_attr_e('Design Zone', 'printed-product-customizer'); ?>" style="display:none;">
                    <h4 class="fpc-group-toggle"><span class="fpc-group-title"><?php _e('Design Zone', 'printed-product-customizer'); ?></span></h4>
                    <div class="fpc-group-fields">
                    <p class="form-field"><label><?php _e('Label', 'printed-product-customizer'); ?></label><input type="text" class="short fpc-generate-key" name="fpc_design_zones[__INDEX__][label]" /></p>
                    <p class="form-field"><label><?php _e('Key', 'printed-product-customizer'); ?></label><input type="text" class="short fpc-key-field" name="fpc_design_zones[__INDEX__][key]" /></p>
                    <p class="form-field"><label><?php _e('Target Body', 'printed-product-customizer'); ?></label><select name="fpc_design_zones[__INDEX__][body]"><option value=""><?php _e('Select body', 'printed-product-customizer'); ?></option><?php foreach ($bodies as $b) : ?><option value="<?php echo esc_attr($b); ?>"><?php echo esc_html($b); ?></option><?php endforeach; ?></select></p>
                    <p class="form-field"><label><?php _e('BBox Width/Height', 'printed-product-customizer'); ?></label> w<input type="number" step="any" class="short" name="fpc_design_zones[__INDEX__][bbox][w]" /> h<input type="number" step="any" class="short" name="fpc_design_zones[__INDEX__][bbox][h]" /></p>
                    <p class="form-field"><label><?php _e('Depth', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_design_zones[__INDEX__][depth]" /></p>
                    <div class="form-field">
                        <label><?php _e('Designs', 'printed-product-customizer'); ?></label>
                        <div class="fpc-design-wrapper">
                            <div class="fpc-design-container">
                                <div class="fpc-design-row fpc-template fpc-repeatable-row" data-default-title="<?php esc_attr_e('Design', 'printed-product-customizer'); ?>" style="display:none;">
                                    <h4 class="fpc-group-toggle"><span class="fpc-group-title"><?php _e('Design', 'printed-product-customizer'); ?></span></h4>
                                    <div class="fpc-group-fields">
                                        <select name="fpc_design_zones[__INDEX__][designs][__DESIGN_INDEX__][type]">
                                            <option value="predefined">predefined</option>
                                            <option value="custom_svg">custom_svg</option>
                                            <option value="custom_text">custom_text</option>
        <option value="blank">blank</option>
                                        </select>
                                        <input type="text" class="fpc-generate-key" name="fpc_design_zones[__INDEX__][designs][__DESIGN_INDEX__][name]" placeholder="<?php esc_attr_e('Name', 'printed-product-customizer'); ?>" />
                                        <input type="number" step="any" name="fpc_design_zones[__INDEX__][designs][__DESIGN_INDEX__][price]" placeholder="<?php esc_attr_e('Price', 'printed-product-customizer'); ?>" />
                                        <input type="text" name="fpc_design_zones[__INDEX__][designs][__DESIGN_INDEX__][text]" placeholder="<?php esc_attr_e('Text', 'printed-product-customizer'); ?>" />
                                        <input type="text" name="fpc_design_zones[__INDEX__][designs][__DESIGN_INDEX__][file]" placeholder="<?php esc_attr_e('SVG File', 'printed-product-customizer'); ?>" />
                                        <input type="number" step="any" name="fpc_design_zones[__INDEX__][designs][__DESIGN_INDEX__][rotation]" placeholder="<?php esc_attr_e('Rotation', 'printed-product-customizer'); ?>" />
                                        <input type="number" step="any" name="fpc_design_zones[__INDEX__][designs][__DESIGN_INDEX__][scale]" placeholder="<?php esc_attr_e('Scale', 'printed-product-customizer'); ?>" />
                                        <input type="number" step="any" name="fpc_design_zones[__INDEX__][designs][__DESIGN_INDEX__][x]" placeholder="<?php esc_attr_e('X', 'printed-product-customizer'); ?>" />
                                        <input type="number" step="any" name="fpc_design_zones[__INDEX__][designs][__DESIGN_INDEX__][y]" placeholder="<?php esc_attr_e('Y', 'printed-product-customizer'); ?>" />
                                        <button type="button" class="button fpc-design-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button>
                                    </div>
                                </div>
                            </div>
                            <p><button type="button" class="button fpc-design-add"><?php _e('Add Design', 'printed-product-customizer'); ?></button></p>
                        </div>
                    </div>
                    <p><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></p>
                    </div>
                </div>
                <?php foreach ($zones as $index => $zone) : ?>
                    <div class="fpc-repeatable-row" data-default-title="<?php esc_attr_e('Design Zone', 'printed-product-customizer'); ?>">
                        <h4 class="fpc-group-toggle"><span class="fpc-group-title"><?php echo esc_html($zone['label'] ?? __('Design Zone', 'printed-product-customizer')); ?></span></h4>
                        <div class="fpc-group-fields">
                        <p class="form-field"><label><?php _e('Label', 'printed-product-customizer'); ?></label><input type="text" class="short fpc-generate-key" name="fpc_design_zones[<?php echo esc_attr($index); ?>][label]" value="<?php echo esc_attr($zone['label'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Key', 'printed-product-customizer'); ?></label><input type="text" class="short fpc-key-field" name="fpc_design_zones[<?php echo esc_attr($index); ?>][key]" value="<?php echo esc_attr($zone['key'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Target Body', 'printed-product-customizer'); ?></label><select name="fpc_design_zones[<?php echo esc_attr($index); ?>][body]"><option value=""><?php _e('Select body', 'printed-product-customizer'); ?></option><?php foreach ($bodies as $b) : ?><option value="<?php echo esc_attr($b); ?>" <?php selected(($zone['body'] ?? '') === $b); ?>><?php echo esc_html($b); ?></option><?php endforeach; ?></select></p>
                        <p class="form-field"><label><?php _e('BBox Width/Height', 'printed-product-customizer'); ?></label> w<input type="number" step="any" class="short" name="fpc_design_zones[<?php echo esc_attr($index); ?>][bbox][w]" value="<?php echo esc_attr($zone['bbox']['w'] ?? ''); ?>" /> h<input type="number" step="any" class="short" name="fpc_design_zones[<?php echo esc_attr($index); ?>][bbox][h]" value="<?php echo esc_attr($zone['bbox']['h'] ?? ''); ?>" /></p>
                        <p class="form-field"><label><?php _e('Depth', 'printed-product-customizer'); ?></label><input type="number" step="any" class="short" name="fpc_design_zones[<?php echo esc_attr($index); ?>][depth]" value="<?php echo esc_attr($zone['depth'] ?? ''); ?>" /></p>
                        <div class="form-field">
                            <label><?php _e('Designs', 'printed-product-customizer'); ?></label>
                            <div class="fpc-design-wrapper">
                                <div class="fpc-design-container">
                                  <div class="fpc-design-row fpc-template fpc-repeatable-row" data-default-title="<?php esc_attr_e('Design', 'printed-product-customizer'); ?>" style="display:none;">
                                      <h4 class="fpc-group-toggle"><span class="fpc-group-title"><?php _e('Design', 'printed-product-customizer'); ?></span></h4>
                                      <div class="fpc-group-fields">
                                          <select name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][__DESIGN_INDEX__][type]">
                                              <option value="predefined">predefined</option>
                                              <option value="custom_svg">custom_svg</option>
                                              <option value="custom_text">custom_text</option>
                                              <option value="blank">blank</option>
                                          </select>
                                          <input type="text" class="fpc-generate-key" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][__DESIGN_INDEX__][name]" placeholder="<?php esc_attr_e('Name', 'printed-product-customizer'); ?>" />
                                          <input type="number" step="any" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][__DESIGN_INDEX__][price]" placeholder="<?php esc_attr_e('Price', 'printed-product-customizer'); ?>" />
                                          <input type="text" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][__DESIGN_INDEX__][text]" placeholder="<?php esc_attr_e('Text', 'printed-product-customizer'); ?>" />
                                          <input type="text" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][__DESIGN_INDEX__][file]" placeholder="<?php esc_attr_e('SVG File', 'printed-product-customizer'); ?>" />
                                          <input type="number" step="any" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][__DESIGN_INDEX__][rotation]" placeholder="<?php esc_attr_e('Rotation', 'printed-product-customizer'); ?>" />
                                          <input type="number" step="any" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][__DESIGN_INDEX__][scale]" placeholder="<?php esc_attr_e('Scale', 'printed-product-customizer'); ?>" />
                                          <input type="number" step="any" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][__DESIGN_INDEX__][x]" placeholder="<?php esc_attr_e('X', 'printed-product-customizer'); ?>" />
                                          <input type="number" step="any" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][__DESIGN_INDEX__][y]" placeholder="<?php esc_attr_e('Y', 'printed-product-customizer'); ?>" />
                                          <button type="button" class="button fpc-design-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button>
                                      </div>
                                  </div>
                                      <?php if (!empty($zone['designs'])) : foreach ($zone['designs'] as $di => $design) : ?>
                                        <div class="fpc-design-row fpc-repeatable-row" data-default-title="<?php esc_attr_e('Design', 'printed-product-customizer'); ?>">
                                            <h4 class="fpc-group-toggle"><span class="fpc-group-title"><?php echo esc_html($design['name'] ?? __('Design', 'printed-product-customizer')); ?></span></h4>
                                            <div class="fpc-group-fields">
                                                <select name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][<?php echo esc_attr($di); ?>][type]">
                                                    <option value="predefined" <?php selected(($design['type'] ?? '') === 'predefined'); ?>>predefined</option>
                                                    <option value="custom_svg" <?php selected(($design['type'] ?? '') === 'custom_svg'); ?>>custom_svg</option>
                                                    <option value="custom_text" <?php selected(($design['type'] ?? '') === 'custom_text'); ?>>custom_text</option>
                                                    <option value="blank" <?php selected(($design['type'] ?? '') === 'blank'); ?>>blank</option>
                                                </select>
                                                <input type="text" class="fpc-generate-key" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][<?php echo esc_attr($di); ?>][name]" value="<?php echo esc_attr($design['name'] ?? ''); ?>" placeholder="<?php esc_attr_e('Name', 'printed-product-customizer'); ?>" />
                                                <input type="number" step="any" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][<?php echo esc_attr($di); ?>][price]" value="<?php echo esc_attr($design['price'] ?? ''); ?>" placeholder="<?php esc_attr_e('Price', 'printed-product-customizer'); ?>" />
                                                <input type="text" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][<?php echo esc_attr($di); ?>][text]" value="<?php echo esc_attr($design['text'] ?? ''); ?>" placeholder="<?php esc_attr_e('Text', 'printed-product-customizer'); ?>" />
                                                <input type="text" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][<?php echo esc_attr($di); ?>][file]" value="<?php echo esc_attr($design['file'] ?? ''); ?>" placeholder="<?php esc_attr_e('SVG File', 'printed-product-customizer'); ?>" />
                                                <input type="number" step="any" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][<?php echo esc_attr($di); ?>][rotation]" value="<?php echo esc_attr($design['rotation'] ?? ''); ?>" placeholder="<?php esc_attr_e('Rotation', 'printed-product-customizer'); ?>" />
                                                <input type="number" step="any" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][<?php echo esc_attr($di); ?>][scale]" value="<?php echo esc_attr($design['scale'] ?? ''); ?>" placeholder="<?php esc_attr_e('Scale', 'printed-product-customizer'); ?>" />
                                                <input type="number" step="any" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][<?php echo esc_attr($di); ?>][x]" value="<?php echo esc_attr($design['x'] ?? ''); ?>" placeholder="<?php esc_attr_e('X', 'printed-product-customizer'); ?>" />
                                                <input type="number" step="any" name="fpc_design_zones[<?php echo esc_attr($index); ?>][designs][<?php echo esc_attr($di); ?>][y]" value="<?php echo esc_attr($design['y'] ?? ''); ?>" placeholder="<?php esc_attr_e('Y', 'printed-product-customizer'); ?>" />
                                                <button type="button" class="button fpc-design-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button>
                                            </div>
                                        </div>
                                    <?php endforeach; endif; ?>
                                </div>
                                <p><button type="button" class="button fpc-design-add"><?php _e('Add Design', 'printed-product-customizer'); ?></button></p>
                            </div>
                        </div>
                        <p><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <p><button type="button" class="button fpc-repeatable-add"><?php _e('Add Design Zone', 'printed-product-customizer'); ?></button></p>
        </div>
    </div>
    <?php
}

add_action('woocommerce_process_product_meta', 'fpc_design_zones_save');
function fpc_design_zones_save($post_id) {
    if (isset($_POST['fpc_design_zones']) && is_array($_POST['fpc_design_zones'])) {
        $zones = [];
        foreach ($_POST['fpc_design_zones'] as $zone) {
            if (empty($zone['label']) && empty($zone['key']) && empty($zone['body'])) {
                continue;
            }
            $designs = [];
            if (!empty($zone['designs']) && is_array($zone['designs'])) {
                foreach ($zone['designs'] as $design) {
                    if (empty($design['type'])) {
                        continue;
                    }
                    $designs[] = [
                        'type'     => sanitize_text_field($design['type'] ?? ''),
                        'name'     => sanitize_text_field($design['name'] ?? ''),
                        'price'    => floatval($design['price'] ?? 0),
                        'text'     => sanitize_text_field($design['text'] ?? ''),
                        'file'     => sanitize_text_field($design['file'] ?? ''),
                        'rotation' => floatval($design['rotation'] ?? 0),
                        'scale'    => floatval($design['scale'] ?? 100),
                        'x'        => floatval($design['x'] ?? 0),
                        'y'        => floatval($design['y'] ?? 0),
                    ];
                }
            }
            $zones[] = [
                'label'  => sanitize_text_field($zone['label'] ?? ''),
                'key'    => sanitize_title($zone['key'] ?? ''),
                'body'   => sanitize_text_field($zone['body'] ?? ''),
                'bbox'   => [
                    'w' => floatval($zone['bbox']['w'] ?? 0),
                    'h' => floatval($zone['bbox']['h'] ?? 0),
                ],
                'depth'  => floatval($zone['depth'] ?? 0),
                'designs'=> $designs,
            ];
        }
        if (!empty($zones)) {
            update_post_meta($post_id, '_fpc_design_zones', $zones);
        } else {
            delete_post_meta($post_id, '_fpc_design_zones');
        }
    } else {
        delete_post_meta($post_id, '_fpc_design_zones');
    }
}
