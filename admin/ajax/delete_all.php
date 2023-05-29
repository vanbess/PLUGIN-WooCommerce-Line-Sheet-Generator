<?php

defined('ABSPATH') ?: exit();

/**
 * Delete all previously generated PDFs
 */

add_action('wp_ajax_nopriv_ls_delete_all', 'ls_delete_all');
add_action('wp_ajax_ls_delete_all', 'ls_delete_all');

function ls_delete_all() {

    check_ajax_referer('ls delete all');

    $folderPath = LSGEN_PATH . 'admin/pdfs/';

    // Get the list of files in the folder
    $files = scandir($folderPath);

    // Exclude . and .. directories from the list
    $files = array_diff($files, ['.', '..']);

    // Loop through the files and unlink (delete) each one
    if (!empty($files)) :
        foreach ($files as $file) :
            $filePath = $folderPath . $file;
            if (is_file($filePath)) :
                unlink($filePath);
            endif;
        endforeach;

        wp_send_json_success(__('All PDFs successfully deleted. The page will now reload.', LSGEN_TDOM));

    else :
        wp_send_json_error(__('There are no PDFs to delete at this stage. The page will now reload.', LSGEN_TDOM));
    endif;
}
