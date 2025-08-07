<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle syncing filament inventory from Google Sheets.
 */
class FPC_Filament_Sync {
    /** Option names used for settings and storage */
    const OPTION_SHEET_ID   = 'fpc_google_sheet_id';
    const OPTION_SHEET_TAB  = 'fpc_google_sheet_tab';
    const OPTION_INVENTORY  = 'fpc_filament_inventory';

    /**
     * Perform a sync from Google Sheets and store the result in a site option.
     *
     * Expects the sheet to contain a header row with the following columns:
     * slug, material, price_per_kg, stock_grams, color, texture
     *
     * @return array|WP_Error Parsed inventory array on success or WP_Error on failure.
     */
    public function sync() {
        $sheet_id = get_option(self::OPTION_SHEET_ID);
        $tab      = get_option(self::OPTION_SHEET_TAB);

        if (empty($sheet_id) || empty($tab)) {
            return new WP_Error(
                'fpc_missing_setup',
                __('Google Sheets settings not configured.', 'printed-product-customizer')
            );
        }

        $rows = fpc_google_get_values($sheet_id, $tab);
        if (is_wp_error($rows)) {
            return $rows;
        }
        $headers = array_map('sanitize_key', array_shift($rows));
        $inventory = [];

        foreach ($rows as $row) {
            $row      = array_map('sanitize_text_field', $row);
            $row_data = array_pad($row, count($headers), '');
            $item     = array_combine($headers, $row_data);

            if (empty($item['slug'])) {
                continue;
            }

            $inventory[$item['slug']] = [
                'material'     => $item['material'] ?? '',
                'price_per_kg' => (float) ($item['price_per_kg'] ?? 0),
                'stock_grams'  => (float) ($item['stock_grams'] ?? 0),
                'color'        => $item['color'] ?? '',
                'texture'      => $item['texture'] ?? '',
            ];
        }

        update_option(self::OPTION_INVENTORY, $inventory);
        return $inventory;
    }

    /**
     * Retrieve the current filament inventory.
     */
    public function get_inventory() {
        return get_option(self::OPTION_INVENTORY, []);
    }
}
