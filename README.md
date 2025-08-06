# Printed Product Customizer

Initial scaffold for a WordPress/WooCommerce plugin that enables configuration of 3D printed products.

## Features

- Filament inventory admin menu with Google Sheets sync.
- WooCommerce product data tabs for:
  - Filament Groups
  - Subgroups
  - 3MF Body Mapping
  - Text Zones
- Basic variation pricing adjustment scaffold.

## Google Sheets Sync

1. Navigate to **WooCommerce → Filament Inventory** in the WordPress admin.
2. Enter your Google API key, sheet ID, and range (or tab name) and save settings.
3. Click **Sync Now** to fetch filament data.
4. The sheet must have a header row with columns: `slug`, `material`, `price_per_kg`, `stock_grams`, `color`, `texture`.

This repository currently contains minimal placeholders intended for further development.
