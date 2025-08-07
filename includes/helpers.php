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

/**
 * Log debug messages when WP_DEBUG is enabled.
 */
function fpc_debug($message, $data = null) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        if (null !== $data) {
            $message .= ' ' . wp_json_encode($data);
        }
        error_log('[FPC] ' . $message);
    }
}

/**
 * URL safe base64 encoding.
 */
function fpc_base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Parse a Google Sheets URL and return sheet ID and gid.
 */
function fpc_parse_sheet_url($url) {
    $parts = wp_parse_url($url);
    if (empty($parts['path'])) {
        return false;
    }

    if (!preg_match('#/d/([^/]+)#', $parts['path'], $m)) {
        return false;
    }
    $sheet = $m[1];

    $gid   = '';
    $query = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
        if (isset($query['gid'])) {
            $gid = $query['gid'];
        }
    }
    if (!$gid && !empty($parts['fragment']) && preg_match('/gid=(\d+)/', $parts['fragment'], $m)) {
        $gid = $m[1];
    }

    if (!$gid) {
        return false;
    }

    return [$sheet, $gid];
}

/**
 * Retrieve an access token using a service account JSON file.
 */
function fpc_google_get_access_token() {
    $path = get_option('fpc_google_json_path');
    if (!$path || !file_exists($path)) {
        fpc_debug('Service account key missing', $path);
        return new WP_Error('missing_key', __('Service account key not found.', 'printed-product-customizer'));
    }

    $config = json_decode(file_get_contents($path), true);
    if (empty($config['client_email']) || empty($config['private_key'])) {
        fpc_debug('Invalid service account config');
        return new WP_Error('invalid_key', __('Invalid service account key.', 'printed-product-customizer'));
    }

    $cached = get_transient('fpc_google_token');
    if ($cached) {
        fpc_debug('Using cached Google token');
        return $cached;
    }

    $now    = time();
    $header = fpc_base64url_encode(wp_json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $claim  = fpc_base64url_encode(wp_json_encode([
        'iss'   => $config['client_email'],
        'scope' => 'https://www.googleapis.com/auth/spreadsheets https://www.googleapis.com/auth/drive.metadata.readonly',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'exp'   => $now + 3600,
        'iat'   => $now,
    ]));
    $to_sign = $header . '.' . $claim;
    openssl_sign($to_sign, $signature, $config['private_key'], 'sha256');
    $jwt = $to_sign . '.' . fpc_base64url_encode($signature);

    fpc_debug('Requesting new Google token');
    $response = wp_remote_post('https://oauth2.googleapis.com/token', [
        'body' => [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ],
    ]);

    if (is_wp_error($response)) {
        fpc_debug('Token request failed', $response->get_error_message());
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($body['access_token'])) {
        fpc_debug('Token request missing access_token', $body);
        return new WP_Error('no_token', __('Unable to obtain access token.', 'printed-product-customizer'));
    }

    set_transient('fpc_google_token', $body['access_token'], (int) $body['expires_in']);
    fpc_debug('Google token acquired');
    return $body['access_token'];
}

/**
 * Get the sheet (tab) title from a sheet ID and gid.
 */
function fpc_google_get_sheet_title($sheet_id, $gid) {
    $token = fpc_google_get_access_token();
    if (is_wp_error($token)) {
        return $token;
    }

    $url      = sprintf('https://sheets.googleapis.com/v4/spreadsheets/%s?fields=sheets.properties', rawurlencode($sheet_id));
    fpc_debug('GET', $url);
    $response = wp_remote_get($url, [
        'headers' => ['Authorization' => 'Bearer ' . $token],
    ]);

    if (is_wp_error($response)) {
        return new WP_Error('api_error', $response->get_error_message());
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    foreach ($data['sheets'] ?? [] as $sheet) {
        $props = $sheet['properties'] ?? [];
        if ((string) ($props['sheetId'] ?? '') === (string) $gid) {
            return $props['title'] ?? '';
        }
    }

    return new WP_Error('api_error', __('Sheet tab not found.', 'printed-product-customizer'));
}

/**
 * Retrieve values from a Google Sheet.
 */
function fpc_google_get_values($sheet_id, $range) {
    if (preg_match('/^(\d+)(!.*)?$/', $range, $m)) {
        $title = fpc_google_get_sheet_title($sheet_id, $m[1]);
        if (is_wp_error($title)) {
            return $title;
        }
        $range = $title . ($m[2] ?? '');
    }

    $token = fpc_google_get_access_token();
    if (is_wp_error($token)) {
        fpc_debug('Access token error', $token->get_error_message());
        return $token;
    }

    $url = sprintf('https://sheets.googleapis.com/v4/spreadsheets/%s/values/%s', rawurlencode($sheet_id), rawurlencode($range));
    fpc_debug('GET', $url);
    $response = wp_remote_get($url, [
        'headers' => ['Authorization' => 'Bearer ' . $token],
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($body['values'])) {
        return new WP_Error('no_data', __('No data returned from Google Sheets.', 'printed-product-customizer'));
    }

    return $body['values'];
}
