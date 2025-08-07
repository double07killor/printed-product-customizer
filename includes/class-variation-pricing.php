<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Basic scaffold for variation pricing adjustments based on quantity.
 */
class FPC_Variation_Pricing {
    public function __construct() {
        add_action('woocommerce_before_calculate_totals', [$this, 'apply_pricing']);
    }

    /**
     * Adjust pricing logic. Currently applies a simple discount for qty > 1.
     */
    public function apply_pricing($cart) {
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        foreach ($cart->get_cart() as $cart_item) {
            $price = $cart_item['data']->get_price();

            // Apply fees for any unique filaments beyond the default group count.
            $product_id = $cart_item['product_id'];
            $rules = get_post_meta($product_id, '_fpc_additional_group_rules', true);
            $fee   = isset($rules['additional_group_fee']) ? (float) $rules['additional_group_fee'] : 0;
            if ($fee > 0) {
                $defined_groups = get_post_meta($product_id, '_fpc_filament_groups', true);
                $base_count     = is_array($defined_groups) ? count($defined_groups) : 0;

                $defaults   = array_filter(array_map('sanitize_text_field', (array) ($cart_item['fpc_filaments'] ?? [])));
                $additional = array_filter(array_map('sanitize_text_field', (array) ($cart_item['fpc_additional_groups'] ?? [])));
                $unique     = count(array_unique(array_merge($defaults, $additional)));

                $extra = max(0, $unique - $base_count);
                if ($extra > 0) {
                    $price += $fee * $extra;
                }
            }

            if ($cart_item['quantity'] > 1) {
                $price *= 0.9; // 10% discount as placeholder
            }

            $cart_item['data']->set_price($price);
        }
    }
}

new FPC_Variation_Pricing();
