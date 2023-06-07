<?php

defined('ABSPATH') ?: exit();

/**
 * Generate line sheet and save to PDF, then combine into PDF document and save
 */
add_action('wp_ajax_nopriv_ls_generate_line_sheet', 'ls_generate_line_sheet');
add_action('wp_ajax_ls_generate_line_sheet', 'ls_generate_line_sheet');

function ls_generate_line_sheet() {

    check_ajax_referer('ls generate line sheet');

    // Get the linesheet path
    $fileSrc = get_option('ls_last_linesheet_path');

    // get file name
    $fileName = sanitize_text_field($_POST['fileName']);
    $fileName = str_replace(' ', '_', $fileName);

    // get layout
    $layout = $_POST['layout'];

    try {

        // get html content
        $html = file_get_contents($fileSrc);

        // if $html is empty, return error
        if (empty($html)) :
            wp_send_json_error(__('No line sheet HTML found. Did you generate a line sheet preview before attempting to generate a line sheet PDF?', LSGEN_TDOM));
        endif;

        // init MPDF
        $mpdf = new \Mpdf\Mpdf(['auto_link' => true]);

        // fix our margins

        // if wholesale layout
        if ($layout == '3') :
            $mpdf->__construct([
                'margin_bottom'     => 0,
                'margin_top'        => 6,
                'margin_left'       => 10,
                'margin_right'      => 10,
                'margin_header'     => 0,
                'margin_footer'     => 0,
                'orientation'       => 'L',
                'default_font_size' => '10',
                'default_font'      => 'Helvetica',
            ]);


        // if default layout
        else :
            $mpdf->__construct([
                'margin_bottom' => 0,
                'margin_top'    => 5,
                'margin_left'   => 10,
                'margin_right'  => 0,
            ]);
        endif;

        // write html
        $mpdf->WriteHTML($html);

        try {
            //code...
            $mpdf->Output(LSGEN_PATH . 'admin/pdfs/' . time() . '_' . $fileName . '.pdf', 'F');

            wp_send_json_success(__('PDF successfully saved.'));
        } catch (\Throwable $th) {
            //throw $th;
            wp_send_json_success(__('PDF not successfully saved. Error: ') . $th->getMessage());
        }

        //code...
    } catch (\Throwable $th) {
        wp_send_json_error(__('Use of mPDF failed with the following error: ') . $th->getMessage());
    }
}
