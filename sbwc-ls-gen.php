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
    require_once LSGEN_PATH . 'admin/product/edit_screen_tab.php';

    
    /**
     * CONVERT PNG SITE LOGOS WITH TRANSPARENT BACKGROUNDS TO JPG
     * FOR USE WITH MPDF - MPDF DOES NOT SUPPORT PNG IMAGES
     */

    // get current theme
    $curr_theme = get_option('current_theme');

    // get logo image based on current theme (either Flatsome or Riode in this particular case)
    if ($curr_theme === 'Flatsome' || $curr_theme === 'Flatsome Child') :
        $logo_src = get_theme_mod('site_logo');
    else :
        $logo_src = get_theme_mod('custom_logo');
    endif;

    // check if logo is png and convert to jpg if true
    $image_info = getimagesize($logo_src);

    // if image info extracted and jpeg logo file does not already exist, convert to jpg and save 
    if ($image_info && $image_info['mime'] === 'image/png' && !file_exists(LSGEN_PATH . 'admin/logo/logo.jpg')) :

        $pngPath = $logo_src; // Replace with the actual path to your PNG image
        $jpgPath = LSGEN_PATH . 'admin/logo/logo.jpg'; // Replace with the desired path to save the converted JPEG image
        
        // Create a new image from the PNG file
        $pngImage = imagecreatefrompng($pngPath);
        
        // Get the dimensions of the PNG image
        $width  = imagesx($pngImage);
        $height = imagesy($pngImage);
        
        // Create a new true color image with a white background
        $jpgImage = imagecreatetruecolor($width, $height);
        $whiteColor = imagecolorallocate($jpgImage, 255, 255, 255); // RGB values for white
        imagefilledrectangle($jpgImage, 0, 0, $width, $height, $whiteColor);
        
        // Copy the PNG image onto the JPEG image with the white background
        imagecopy($jpgImage, $pngImage, 0, 0, 0, 0, $width, $height);
        
        // Apply white background using imagefill()
        imagefill($jpgImage, 0, 0, $whiteColor);
        
        // Save the JPEG image
        imagejpeg($jpgImage, $jpgPath, 100);
        
        // Free up memory
        imagedestroy($pngImage);
        imagedestroy($jpgImage);

    endif;
});
