<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle syncing filament inventory from Google Sheets.
 */
class FPC_Filament_Sync {
    /** Option names used for settings and storage */
    const OPTION_API_KEY    = 'fpc_google_api_key';
    const OPTION_SHEET_ID   = 'fpc_google_sheet_id';
    const OPTION_SHEET_RANGE = 'fpc_google_sheet_range';
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
        $api_key  = get_option(self::OPTION_API_KEY);
        $range    = get_option(self::OPTION_SHEET_RANGE, 'Sheet1');

        if (empty($sheet_id) || empty($api_key)) {
            return new WP_Error(
                'fpc_missing_credentials',
                __('Google Sheets credentials not configured.', 'printed-product-customizer')
            );
        }

        $url      = sprintf(
            'https://sheets.googleapis.com/v4/spreadsheets/%s/values/%s?key=%s',
            rawurlencode($sheet_id),
            rawurlencode($range),
            rawurlencode($api_key)
        );
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (empty($data['values'])) {
            return new WP_Error(
                'fpc_no_data',
                __('No data returned from Google Sheets.', 'printed-product-customizer')
            );
        }

        $rows    = $data['values'];
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
