<?php

defined('ABSPATH') ?: exit();

require_once __DIR__ . '/fnc/four_per_page.php';
require_once __DIR__ . '/fnc/six_per_page.php';
require_once __DIR__ . '/fnc/three_per_page.php';

/**
 * Generate linesheet preview and return generated HTML
 */
add_action('wp_ajax_nopriv_ls_generate_preview', 'ls_generate_preview');
add_action('wp_ajax_ls_generate_preview', 'ls_generate_preview');

function ls_generate_preview() {

    check_ajax_referer('ls generate preview');

    // vars
    $email         = $_POST['email'];
    $header_text   = $_POST['header_text'];
    $intro         = $_POST['intro'];
    $page_layout   = $_POST['page_layout'];
    $prod_ids      = $_POST['prod_ids'];
    $currency      = $_POST['currency'];
    $is_linked     = $_POST['allow_links'];
    $tracking_vars = $_POST['tracking_vars'];

    // setup is linked check
    $is_linked = $is_linked === 'no' ? false : true;

    // setup canvas size (minus 20mm to accommodate 10mm page margin all round)
    $canvas_size = 'width: 210mm; height: 297mm;';

    switch ($page_layout) {
        case '3':
            // generate preview HTML (3 per page => "wholesale layout")
            $return_html = ls_generate_line_sheet_preview_html_three_per_page($prod_ids, $page_layout, $currency, $canvas_size, $header_text, $intro, $email, $is_linked, $tracking_vars);
            break;
        case '4':
            // generate preview HTML (4 per page)
            $return_html = ls_generate_line_sheet_preview_html_four_per_page($prod_ids, $page_layout, $currency, $canvas_size, $header_text, $intro, $email, $is_linked, $tracking_vars);
            break;
        case '6':
            // generate preview HTML (6 per page)
            $return_html = ls_generate_line_sheet_preview_html_six_per_page($prod_ids, $page_layout, $currency, $canvas_size, $header_text, $intro, $email, $is_linked, $tracking_vars);
            break;
        default:
            // default four per page HTML
            $return_html = ls_generate_line_sheet_preview_html_four_per_page($prod_ids, $page_layout, $currency, $canvas_size, $header_text, $intro, $email, $is_linked, $tracking_vars);
            break;
    }

    // save return html
    $fileName = LSGEN_PATH . 'admin/generated/generated_templ_' . time() . '.html';
    $saved = file_put_contents($fileName, $return_html);

    if ($saved) :
        update_option('ls_last_linesheet_path', $fileName);
    else :
        update_option('ls_last_linesheet_path', null);
    endif;

    // echo generated HTML
    // echo $return_html;

    wp_send_json([
        'html'    => $return_html,
        'fileSrc' => $fileName
    ]);
}
