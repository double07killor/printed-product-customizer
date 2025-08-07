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
    $files = get_post_meta($post->ID, '_fpc_3mf_files', true);
    $files = is_array($files) ? $files : [];
    $assignments = get_post_meta($post->ID, '_fpc_body_assignments', true);
    $assignments = is_array($assignments) ? $assignments : [];
    $subgroups = get_post_meta($post->ID, '_fpc_subgroups', true);
    $subgroups = is_array($subgroups) ? $subgroups : [];
    ?>
    <div id="fpc_3mf_mapping_panel" class="panel woocommerce_options_panel hidden">
        <div class="options_group">
            <p class="form-field">
                <label><?php _e('Upload 3MF Files', 'printed-product-customizer'); ?></label>
                <input type="file" name="fpc_3mf_files[]" multiple="multiple" accept=".3mf" />
            </p>
            <?php if (!empty($files)) : ?>
                <ul>
                    <?php foreach ($files as $id) : $url = wp_get_attachment_url($id); ?>
                        <li><?php echo esc_html(basename($url)); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <p><button type="button" class="button" id="fpc-save-3mf"><?php _e('Save 3MF Files', 'printed-product-customizer'); ?></button></p>
            <div class="fpc-save-notice" style="display:none;"></div>
        </div>
        <div class="options_group fpc-repeatable-wrapper">
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e('Body Name', 'printed-product-customizer'); ?></th>
                        <th><?php _e('Subgroup', 'printed-product-customizer'); ?></th>
                        <th><?php _e('No Visualization', 'printed-product-customizer'); ?></th>
                        <th><?php _e('Non Exported', 'printed-product-customizer'); ?></th>
                        <th><?php _e('Finish', 'printed-product-customizer'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="fpc-repeatable-container">
                    <tr class="fpc-repeatable-row fpc-template" style="display:none;">
                        <td><input type="text" class="fpc-body-field" name="fpc_body_assignments[__INDEX__][body]" /></td>
                        <td>
                            <select class="fpc-subgroup-field" name="fpc_body_assignments[__INDEX__][subgroup]">
                                <option value=""><?php _e('None', 'printed-product-customizer'); ?></option>
                                <?php foreach ($subgroups as $sg) : if (!empty($sg['key'])) : ?>
                                    <option value="<?php echo esc_attr($sg['key']); ?>"><?php echo esc_html($sg['label']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </td>
                        <td style="text-align:center;"><input type="checkbox" class="fpc-no-visualization-field" name="fpc_body_assignments[__INDEX__][no_visualization]" value="1" /></td>
                        <td style="text-align:center;"><input type="checkbox" class="fpc-non-exported-field" name="fpc_body_assignments[__INDEX__][non_exported]" value="1" /></td>
                        <td>
                            <select class="fpc-finish-field" name="fpc_body_assignments[__INDEX__][finish]">
                                <option value=""></option>
                                <option value="brass"><?php _e('Brass', 'printed-product-customizer'); ?></option>
                                <option value="stainless"><?php _e('Stainless', 'printed-product-customizer'); ?></option>
                                <option value="black_oxide"><?php _e('Black Oxide', 'printed-product-customizer'); ?></option>
                            </select>
                        </td>
                        <td><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></td>
                    </tr>
                    <?php foreach ($assignments as $index => $as) : ?>
                        <tr class="fpc-repeatable-row">
                            <td><input type="text" class="fpc-body-field" name="fpc_body_assignments[<?php echo esc_attr($index); ?>][body]" value="<?php echo esc_attr($as['body'] ?? ''); ?>" /></td>
                            <td>
                                <select class="fpc-subgroup-field" name="fpc_body_assignments[<?php echo esc_attr($index); ?>][subgroup]">
                                    <option value=""><?php _e('None', 'printed-product-customizer'); ?></option>
                                    <?php foreach ($subgroups as $sg) : if (!empty($sg['key'])) : ?>
                                        <option value="<?php echo esc_attr($sg['key']); ?>" <?php selected(isset($as['subgroup']) && $as['subgroup'] === $sg['key']); ?>><?php echo esc_html($sg['label']); ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                            </td>
                            <td style="text-align:center;"><input type="checkbox" class="fpc-no-visualization-field" name="fpc_body_assignments[<?php echo esc_attr($index); ?>][no_visualization]" value="1" <?php checked(!empty($as['no_visualization'])); ?> /></td>
                            <td style="text-align:center;"><input type="checkbox" class="fpc-non-exported-field" name="fpc_body_assignments[<?php echo esc_attr($index); ?>][non_exported]" value="1" <?php checked(!empty($as['non_exported'])); ?> /></td>
                            <td>
                                <select class="fpc-finish-field" name="fpc_body_assignments[<?php echo esc_attr($index); ?>][finish]">
                                    <option value=""></option>
                                    <option value="brass" <?php selected(isset($as['finish']) && $as['finish'] === 'brass'); ?>><?php _e('Brass', 'printed-product-customizer'); ?></option>
                                    <option value="stainless" <?php selected(isset($as['finish']) && $as['finish'] === 'stainless'); ?>><?php _e('Stainless', 'printed-product-customizer'); ?></option>
                                    <option value="black_oxide" <?php selected(isset($as['finish']) && $as['finish'] === 'black_oxide'); ?>><?php _e('Black Oxide', 'printed-product-customizer'); ?></option>
                                </select>
                            </td>
                            <td><button type="button" class="button fpc-repeatable-remove"><?php _e('Remove', 'printed-product-customizer'); ?></button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><button type="button" class="button fpc-repeatable-add"><?php _e('Add Body', 'printed-product-customizer'); ?></button></p>
        </div>
    </div>
    <?php
}

add_action('woocommerce_process_product_meta', 'fpc_3mf_mapping_save');
function fpc_3mf_mapping_save($post_id) {
    $existing_files = get_post_meta($post_id, '_fpc_3mf_files', true);
    $existing_files = is_array($existing_files) ? $existing_files : [];

    if (!empty($_FILES['fpc_3mf_files']['name'][0])) {
        $uploaded = [];
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $files = $_FILES['fpc_3mf_files'];
        foreach ($files['name'] as $i => $name) {
            if ($files['name'][$i]) {
                $file = [
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];
                $overrides = ['test_form' => false];
                $movefile = wp_handle_upload($file, $overrides);
                if (!isset($movefile['error'])) {
                    $attachment = [
                        'post_mime_type' => $movefile['type'],
                        'post_title'     => sanitize_file_name($name),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    ];
                    $attach_id = wp_insert_attachment($attachment, $movefile['file'], $post_id);
                    wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $movefile['file']));
                    $uploaded[] = $attach_id;
                }
            }
        }
        if ($uploaded) {
            $existing_files = array_merge($existing_files, $uploaded);
            update_post_meta($post_id, '_fpc_3mf_files', $existing_files);
        }
    }

    // Parse all attached 3MF files to extract body names.
    $bodies = fpc_3mf_collect_bodies($existing_files);

    $assignments = [];
    if (isset($_POST['fpc_body_assignments']) && is_array($_POST['fpc_body_assignments'])) {
        foreach ($_POST['fpc_body_assignments'] as $as) {
            if (empty($as['body'])) {
                continue;
            }
            $assignments[] = [
                'body'            => sanitize_text_field($as['body']),
                'subgroup'        => sanitize_text_field($as['subgroup'] ?? ''),
                'no_visualization'=> !empty($as['no_visualization']) ? '1' : '',
                'non_exported'    => !empty($as['non_exported']) ? '1' : '',
                'finish'          => sanitize_text_field($as['finish'] ?? ''),
            ];
        }
    }

    // Ensure all bodies from the 3MF files exist in assignments.
    foreach ($bodies as $body_name) {
        $found = false;
        foreach ($assignments as $as) {
            if ($as['body'] === $body_name) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $assignments[] = [
                'body'            => $body_name,
                'subgroup'        => '',
                'no_visualization'=> '',
                'non_exported'    => '',
                'finish'          => '',
            ];
        }
    }

    if (!empty($assignments)) {
        update_post_meta($post_id, '_fpc_body_assignments', $assignments);
    } else {
        delete_post_meta($post_id, '_fpc_body_assignments');
    }
}

add_action('wp_ajax_fpc_save_3mf_files', 'fpc_3mf_ajax_save_files');
function fpc_3mf_ajax_save_files() {
    if (!current_user_can('edit_products')) {
        wp_send_json_error(['message' => __('Permission denied', 'printed-product-customizer')]);
    }

    $post_id = intval($_POST['post_id']);

    $existing_files = get_post_meta($post_id, '_fpc_3mf_files', true);
    $existing_files = is_array($existing_files) ? $existing_files : [];

    $uploaded = [];
    if (!empty($_FILES['fpc_3mf_files']['name'][0])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $files = $_FILES['fpc_3mf_files'];
        foreach ($files['name'] as $i => $name) {
            if ($files['name'][$i]) {
                $file = [
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];
                $overrides = ['test_form' => false];
                $movefile = wp_handle_upload($file, $overrides);
                if (!isset($movefile['error'])) {
                    $attachment = [
                        'post_mime_type' => $movefile['type'],
                        'post_title'     => sanitize_file_name($name),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    ];
                    $attach_id = wp_insert_attachment($attachment, $movefile['file'], $post_id);
                    wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $movefile['file']));
                    $uploaded[] = $attach_id;
                }
            }
        }
    }

    if ($uploaded) {
        $existing_files = array_merge($existing_files, $uploaded);
        update_post_meta($post_id, '_fpc_3mf_files', $existing_files);
    }

    $bodies = fpc_3mf_collect_bodies($existing_files);

    $assignments = get_post_meta($post_id, '_fpc_body_assignments', true);
    $assignments = is_array($assignments) ? $assignments : [];

    foreach ($bodies as $body_name) {
        $found = false;
        foreach ($assignments as $as) {
            if ($as['body'] === $body_name) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $assignments[] = [
                'body'            => $body_name,
                'subgroup'        => '',
                'no_visualization'=> '',
                'non_exported'    => '',
                'finish'          => '',
            ];
        }
    }

    update_post_meta($post_id, '_fpc_body_assignments', $assignments);

    wp_send_json_success([
        'message'     => __('3MF files saved', 'printed-product-customizer'),
        'assignments' => $assignments,
    ]);
}

function fpc_3mf_collect_bodies($attachment_ids) {
    $bodies = [];
    foreach ($attachment_ids as $id) {
        $file = get_attached_file($id);
        if ($file && file_exists($file)) {
            $bodies = array_merge($bodies, fpc_3mf_parse_file($file));
        }
    }
    return array_values(array_unique($bodies));
}

function fpc_3mf_parse_file($file_path) {
    $result = [];
    $zip = new ZipArchive();
    if ($zip->open($file_path) === true) {
        $xml = $zip->getFromName('3D/3dmodel.model');
        if ($xml !== false) {
            $model = simplexml_load_string($xml);
            if ($model !== false) {
                // Register default namespace for XPath queries.
                $model->registerXPathNamespace('m', 'http://schemas.microsoft.com/3dmanufacturing/core/2015/02');
                $objects = $model->xpath('//m:object');
                if (is_array($objects)) {
                    foreach ($objects as $obj) {
                        $name = (string)$obj['name'];
                        if ($name !== '') {
                            $result[] = $name;
                        }
                    }
                }
            }
        }
        $zip->close();
    }
    return $result;
}
