<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle syncing filament inventory from Google Sheets.
 * This is a simplified placeholder implementation.
 */
class FPC_Filament_Sync {
    /**
     * Perform a sync from Google Sheets and store the result in a site option.
     * In this scaffold the method stores a hard-coded sample dataset.
     */
    public function sync() {
        $sample = [
            'black_petg' => [
                'material'     => 'PETG',
                'price_per_kg' => 24,
                'stock_grams'  => 342,
                'color'        => '#222222',
                'texture'      => 'matte',
            ],
        ];

        update_option('fpc_filament_inventory', $sample);
        return $sample;
    }

    /**
     * Retrieve the current filament inventory.
     */
    public function get_inventory() {
        return get_option('fpc_filament_inventory', []);
    }
}
