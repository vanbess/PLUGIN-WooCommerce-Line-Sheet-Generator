<?php

/**
 * Plugin Name:       SBWC Line Sheet Generator
 * Description:       Allows the generation of WooCommerce product line sheets and saving said line sheets to PDF for distribution
 * Version:           1.0.1
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            WC Bessinger
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ls-gen
 */

defined('ABSPATH') || exit();

add_action('plugins_loaded', function () {

    // constants
    define('LSGEN_PATH', plugin_dir_path(__FILE__));
    define('LSGEN_URI', plugin_dir_url(__FILE__));
    define('LSGEN_TDOM', 'ls-gen');

    // composer (DomPDF and FPDI)
    require_once LSGEN_PATH . 'vendor/autoload.php';

    // admin
    require_once LSGEN_PATH . 'admin/admin.php';
    require_once LSGEN_PATH . 'admin/ajax/fetch_preview.php';
    require_once LSGEN_PATH . 'admin/ajax/gen_line_sheet.php';
    require_once LSGEN_PATH . 'admin/ajax/delete_all.php';
});
