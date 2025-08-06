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
            if ($cart_item['quantity'] > 1) {
                $price = $cart_item['data']->get_price();
                $cart_item['data']->set_price($price * 0.9); // 10% discount as placeholder
            }
        }
    }
}

new FPC_Variation_Pricing();
