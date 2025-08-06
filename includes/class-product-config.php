<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Utility class for retrieving product configuration stored in post meta.
 */
class FPC_Product_Config {
    public static function get_filament_groups($product_id) {
        return get_post_meta($product_id, '_fpc_filament_groups', true);
    }

    public static function get_subgroups($product_id) {
        return get_post_meta($product_id, '_fpc_subgroups', true);
    }

    public static function get_text_zones($product_id) {
        return get_post_meta($product_id, '_fpc_text_zones', true);
    }

    public static function get_body_assignments($product_id) {
        return get_post_meta($product_id, '_fpc_body_assignments', true);
    }

    public static function get_instock_combos($product_id) {
        return get_post_meta($product_id, '_fpc_instock_combos', true);
    }
}
