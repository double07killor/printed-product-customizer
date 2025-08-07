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
        add_action('woocommerce_cart_calculate_fees', [$this, 'apply_filament_fee']);
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

    /**
     * Apply filament change fees while honoring exemptions.
     */
    public function apply_filament_fee($cart) {
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        $unique = [];
        $exempt_slugs = [];
        $surcharges = [];
        $inventory = get_option('fpc_filament_inventory', []);
        $margin    = floatval(get_option('fpc_exotic_margin', 0));

        foreach ($cart->get_cart() as $cart_item) {
            $product_id = $cart_item['product_id'] ?? 0;
            $groups = FPC_Product_Config::get_filament_groups($product_id);
            $subgroups = FPC_Product_Config::get_subgroups($product_id);
            $selected = $cart_item['fpc_filaments'] ?? [];
            if (!is_array($groups)) {
                continue;
            }

            $group_base = [];
            if (is_array($subgroups)) {
                foreach ($subgroups as $sg) {
                    $allowed = $sg['allowed'] ?? [];
                    $base    = floatval($sg['base_grams'] ?? 0);
                    foreach ($allowed as $gkey) {
                        if (!isset($group_base[$gkey])) {
                            $group_base[$gkey] = 0;
                        }
                        $group_base[$gkey] += $base;
                    }
                }
            }

            foreach ($groups as $group) {
                $exempt_slugs = array_merge($exempt_slugs, $group['exempt_filaments'] ?? []);
                if (!empty($group['exempt_all_filaments'])) {
                    continue;
                }
                $key = $group['key'] ?? '';
                if (!$key) {
                    continue;
                }
                $slug = $selected[$key] ?? '';
                if ($slug) {
                    $unique[$slug] = true;
                }

                if ($slug && !empty($group['apply_exotic_fee'])) {
                    $base_g  = $group_base[$key] ?? 0;
                    $waste_g = floatval($group['waste_grams'] ?? 0);
                    $total_g = $base_g + $waste_g;
                    $max_price = floatval($group['max_price_per_kg'] ?? $group['max_price'] ?? 0);
                    if ($total_g > 0 && $max_price > 0) {
                        $price_per_kg = floatval($inventory[$slug]['price_per_kg'] ?? 0);
                        $delta = $price_per_kg - $max_price;
                        if ($delta > 0) {
                            $surcharge = ceil(($total_g / 1000) * $delta * (1 + $margin));
                            if ($surcharge > 0) {
                                $qty = $cart_item['quantity'] ?? 1;
                                if (!isset($surcharges[$slug])) {
                                    $surcharges[$slug] = 0;
                                }
                                $surcharges[$slug] += $surcharge * $qty;
                            }
                        }
                    }
                }
            }
        }

        $unique = array_diff_key($unique, array_flip($exempt_slugs));
        $count = count($unique);
        $fee_per_change = floatval(get_option('fpc_filament_change_fee', 0));
        if ($fee_per_change > 0 && $count > 1) {
            $cart->add_fee(__('Filament Change Fee', 'printed-product-customizer'), ($count - 1) * $fee_per_change);
        }

        foreach ($surcharges as $slug => $fee) {
            if ($fee > 0) {
                $cart->add_fee(sprintf(__('Exotic Filament Surcharge (%s)', 'printed-product-customizer'), $slug), $fee);
            }
        }
    }
}

new FPC_Variation_Pricing();
