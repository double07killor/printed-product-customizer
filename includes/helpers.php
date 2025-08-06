<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Retrieve post meta as an array.
 */
function fpc_get_meta_array($post_id, $key) {
    $value = get_post_meta($post_id, $key, true);
    return is_array($value) ? $value : [];
}
