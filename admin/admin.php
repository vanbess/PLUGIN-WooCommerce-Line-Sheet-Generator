<?php

/**
 * Admin page
 */

//  add menu page
add_action('admin_menu', function () {
    add_menu_page(
        __('Line Sheet Generator Settings', LSGEN_TDOM),
        __('Line Sheet Generator', LSGEN_TDOM),
        'manage_options',
        'ls-generator-admin',
        'ls_generator_admin',
        'dashicons-media-document',
        21
    );
});

// render menu page
function ls_generator_admin() {

    global $wpdb;

    // query and return products
    $query = "SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_type = 'product' AND post_status = 'publish'";
    $prod_data = $wpdb->get_results($query);

    global $title; ?>

    <!-- preview cont reset for accuracy -->
    <style id="preview-reset">
        #ls-preview-cont *,
        #ls-preview-cont *::before,
        #ls-preview-cont *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
        }

        #ls-preview-cont html,
        #ls-preview-cont body,
        #ls-preview-cont div,
        #ls-preview-cont span,
        #ls-preview-cont applet,
        #ls-preview-cont object,
        #ls-preview-cont h1,
        #ls-preview-cont h2,
        #ls-preview-cont h3,
        #ls-preview-cont h4,
        #ls-preview-cont h5,
        #ls-preview-cont h6,
        #ls-preview-cont p,
        #ls-preview-cont blockquote,
        #ls-preview-cont pre,
        #ls-preview-cont a,
        #ls-preview-cont abbr,
        #ls-preview-cont acronym,
        #ls-preview-cont address,
        #ls-preview-cont big,
        #ls-preview-cont cite,
        #ls-preview-cont code,
        #ls-preview-cont del,
        #ls-preview-cont dfn,
        #ls-preview-cont em,
        #ls-preview-cont img,
        #ls-preview-cont ins,
        #ls-preview-cont kbd,
        #ls-preview-cont q,
        #ls-preview-cont s,
        #ls-preview-cont samp,
        #ls-preview-cont small,
        #ls-preview-cont strike,
        #ls-preview-cont strong,
        #ls-preview-cont sub,
        #ls-preview-cont sup,
        #ls-preview-cont tt,
        #ls-preview-cont var,
        #ls-preview-cont b,
        #ls-preview-cont u,
        #ls-preview-cont i,
        #ls-preview-cont center,
        #ls-preview-cont dl,
        #ls-preview-cont dt,
        #ls-preview-cont dd,
        #ls-preview-cont ol,
        #ls-preview-cont ul,
        #ls-preview-cont li,
        #ls-preview-cont fieldset,
        #ls-preview-cont form,
        #ls-preview-cont label,
        #ls-preview-cont legend,
        #ls-preview-cont table,
        #ls-preview-cont caption,
        #ls-preview-cont tbody,
        #ls-preview-cont tfoot,
        #ls-preview-cont thead,
        #ls-preview-cont tr,
        #ls-preview-cont th,
        #ls-preview-cont td,
        #ls-preview-cont article,
        #ls-preview-cont aside,
        #ls-preview-cont canvas,
        #ls-preview-cont details,
        #ls-preview-cont embed,
        #ls-preview-cont figure,
        #ls-preview-cont figcaption,
        #ls-preview-cont footer,
        #ls-preview-cont header,
        #ls-preview-cont hgroup,
        #ls-preview-cont menu,
        #ls-preview-cont nav,
        #ls-preview-cont output,
        #ls-preview-cont ruby,
        #ls-preview-cont section,
        #ls-preview-cont summary,
        #ls-preview-cont time,
        #ls-preview-cont mark,
        #ls-preview-cont audio,
        #ls-preview-cont video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
        }

        #ls-preview-cont input[type="checkbox"],
        #ls-preview-cont input[type="radio"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0;
        }

        #ls-preview-cont ul,
        #ls-preview-cont ol {
            list-style: none;
        }

        #ls-preview-cont body {
            margin: 0;
        }

        #ls-preview-cont body {
            font-family: Arial, sans-serif;
        }

        #ls-preview-cont body {
            font-size: 16px;
        }
    </style>

    <!-- select2 -->
    <script id="select2js" src="<?php echo LSGEN_URI . 'admin/select2.min.js'; ?>"></script>
    <link id="select2css" rel="stylesheet" href="<?php echo LSGEN_URI . 'admin/select2.min.css'; ?>">

    <!-- admin main -->
    <div id="ls-generator-admin">

        <h2>
            <?php echo $title; ?>
            <button id="clear_generation_cache" class="button button-secondary" onclick="clearLSCache(event)" title="<?php _e('Click to clear previously generated line sheet HTML file cache', LSGEN_TDOM); ?>">
                <?php _e('Clear Preview Cache', LSGEN_TDOM); ?>
            </button>
            <button id="view_generated" class="button button-secondary" onclick="showPreviouslyGenerated(event)" title="<?php _e('Click to view a list of previously generated line sheet PDFs.', LSGEN_TDOM); ?>">
                <?php _e('View Previously Generated Line Sheets', LSGEN_TDOM); ?>
            </button>
        </h2>

        <!-- show previously generated modal -->
        <script>
            $ = jQuery;

            function showPreviouslyGenerated(event) {
                $('#ls-previously-generated-overlay, #ls-previously-generated').show();
            }
        </script>

        <!-- ls settings cont -->
        <div id="ls-settings-cont">

            <!-- inputs cont -->
            <div id="ls-generator-form-cont">

                <div style="width: 400px; padding: 0 30px;">

                    <!-- product ids -->
                    <p style="margin-bottom: 5px;">
                        <label for="prod_ids">
                            <i><b><?php _e('Select product IDs:*', LSGEN_TDOM); ?></b></i>
                        </label>
                    </p>
                    <p style="margin-top: 0;">
                        <select name="prod_ids" id="prod_ids" multiple class="regular-text">
                            <?php foreach ($prod_data as $obj) : ?>
                                <option value="<?php echo $obj->ID; ?>"><?php echo $obj->post_title; ?> (ID: <?php echo $obj->ID; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <!-- per page -->
                    <p style="margin-bottom: 5px;">
                        <label for="page_layout">
                            <i><b><?php _e('Select line sheet layout type:*', LSGEN_TDOM); ?></b></i>
                        </label>
                    </p>
                    <p style="margin-top: 0;">
                        <select name="page_layout" id="page_layout" class="regular-text">
                            <option value="3" selected><?php _e('3 Product Detailed Wholesale Layout', LSGEN_TDOM); ?></option>
                            <option value="4"><?php _e('Standard 4 Product Layout', LSGEN_TDOM); ?></option>
                            <option value="6"><?php _e('Standard 6 Product Layout', LSGEN_TDOM); ?></option>
                        </select>
                    </p>

                    <!-- allow links -->
                    <p style="margin-bottom: 5px;">
                        <label for="allow_links">
                            <i><b><?php _e('Link products in line sheet to individual product pages?*', LSGEN_TDOM); ?></b></i>
                        </label>
                    </p>
                    <p style="margin-top: 0;">
                        <select name="allow_links" id="allow_links" class="regular-text" onchange="showTrackingInput(event)">
                            <option value="yes"><?php _e('Yes', LSGEN_TDOM); ?></option>
                            <option value="no" selected><?php _e('No', LSGEN_TDOM); ?></option>
                        </select>
                    </p>

                    <script>
                        $ = jQuery;

                        /* Show/hide tracking variable input */
                        function showTrackingInput(event) {

                            var target = $(event.target),
                                selected = target.val();

                            if (selected === 'yes') {
                                $('.tracking_input').show();
                            } else {
                                $('.tracking_input').hide();
                            }

                        }
                    </script>

                    <!--  tracking variables input -->
                    <p class="tracking_input" style="margin-bottom: 5px; display: none;">
                        <label for="tracking_vars">
                            <i><b><?php _e('Add tracking variables below to track link clicks from line sheet (optional):', LSGEN_TDOM); ?></b></i>
                        </label>
                    </p>
                    <p class="tracking_input" style="margin-top: 0; display: none;">
                        <input type="text" name="tracking_vars" id="tracking_vars" class="regular-text" placeholder="<?php _e('example: ?var_1=abc&var_2=xyz ', LSGEN_TDOM); ?>">
                    </p>

                    <!-- currency -->
                    <?php
                    if (function_exists('alg_get_enabled_currencies')) :

                        // retrieve ALG enabled currency list
                        $enabled_currencies = alg_get_enabled_currencies(true);

                        if (is_array($enabled_currencies) && !empty($enabled_currencies)) : ?>
                            <p style="margin-bottom: 5px;">
                                <label for="currency">
                                    <i><b><?php _e('Select Currency:*', LSGEN_TDOM); ?></b></i>
                                </label>
                            </p>
                            <p style="margin-top: 0;">
                                <select name="currency" id="currency" class="regular-text">
                                    <?php foreach ($enabled_currencies as $currency) : ?>
                                        <option value="<?php echo $currency; ?>"><?php echo $currency; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                    <?php endif;

                    endif;
                    ?>

                    <!-- header text -->
                    <p style="margin-bottom: 5px;">
                        <label for="header_text">
                            <i><b><?php _e('Specify page header text:*', LSGEN_TDOM); ?></b></i>
                        </label>
                    </p>
                    <p style="margin-top: 0;">
                        <input type="text" name="header_text" id="header_text" maxlength="50" class="regular-text" placeholder="<?php _e('example: Spring Collection ' . date('Y'), LSGEN_TDOM); ?>">
                    </p>

                    <!-- contact email -->
                    <p style="margin-bottom: 5px;">
                        <label for="email">
                            <i><b><?php _e('Specify footer contact email:*', LSGEN_TDOM); ?></b></i>
                        </label>
                    </p>
                    <p style="margin-top: 0;">
                        <input type="email" name="email" id="email" class="regular-text">
                    </p>
                </div>

                <div style="width: 400px; padding: 0 30px;">

                    <!-- line sheet intro -->
                    <p style="margin-bottom: 5px;">
                        <label for="intro">
                            <i><b><?php _e('Add line sheet intro text here (optional, 250 characters max):', LSGEN_TDOM); ?></b></i>
                        </label>
                    </p>
                    <p style="margin-top: 0;">
                        <textarea name="intro" id="intro" cols="30" rows="10" maxlength="250" class="regular-text" placeholder="<?php _e('NOTE: displays on 4 and 6 product layouts only', 'default'); ?>"></textarea>
                        <span id="characterCount" style="text-align: right; display: block;"><b>0/250</b></span>
                    </p>

                    <!-- generate preview -->
                    <p>
                        <button class="button button-secondary regular-text" style="width: 100%" title="<?php _e('Click to generate your line sheet preview. The preview will be displayed in the line sheet preview box below.', LSGEN_TDOM); ?>" onclick="generatePreview(event)">
                            <?php _e('Generate Preview', LSGEN_TDOM); ?>
                        </button>
                    </p>

                    <hr>

                    <!-- line sheet name -->
                    <p style="margin-bottom: 5px;">
                        <label for="ls_name">
                            <i><b><?php _e('Line sheet save name:*', LSGEN_TDOM); ?></b></i>
                        </label>
                    </p>
                    <p style="margin-top: 0;">
                        <input type="text" name="ls_name" id="ls_name" class="regular-text" placeholder="<?php _e('example: shoes line sheet', LSGEN_TDOM); ?>">
                    </p>

                    <!-- generate linesheet -->
                    <p>
                        <button id="generate_ls" class="button button-primary regular-text" title="<?php _e('Click to generate your line sheet. NOTE: You need to first generate your line sheet preview before generating the actual line sheet PDF.', LSGEN_TDOM); ?>" style="width: 100%" onclick="generateLinesheet(event)">
                            <?php _e('Generate Line Sheet', LSGEN_TDOM); ?>
                        </button>
                    </p>

                </div>

            </div>

            <!-- previously generated modal overlay -->
            <div id="ls-previously-generated-overlay" style="display:none;"></div>

            <!-- previously generate line sheets modal -->
            <div id="ls-previously-generated" style="display:none;">

                <!-- close -->
                <a href="#" class="ls-close-pu" onclick="closeModal(event)">x</a>

                <script>
                    $ = jQuery;

                    function closeModal(event) {
                        $('#ls-previously-generated-overlay, #ls-previously-generated').hide();
                    }
                </script>

                <label for="ls-previously-generated-cont">

                    <i><b><?php _e('Previously generated line sheet PDFs (click on a link to download):', LSGEN_TDOM); ?></b></i>

                    <!-- delete all previously generated -->
                    <button class="button button-small del-all-pdfs" title="<?php _e('Click to delete all previously generated PDFs.', LSGEN_TDOM); ?>" onclick="delAllPDFs(event)"><?php _e('Delete All', LSGEN_TDOM); ?></button>

                </label>

                <div id="ls-previously-generated-cont">

                    <?php
                    // Fetch previously generated PDFs and present for download

                    // folder path
                    $folderPath = LSGEN_PATH . 'admin/pdfs/';

                    // Get list of PDF files
                    $files = scandir($folderPath);

                    // Exclude . and .. directories from the list
                    $files = array_diff($files, ['.', '..']);

                    // Generate links from files if found
                    if (!empty($files)) :
                        foreach ($files as $file) :

                            $filePathUri = LSGEN_URI . 'admin/pdfs/';
                            $fileUri = $filePathUri . $file; ?>

                            <p>
                                <a class="ls-pdf-link" href="<?php echo $fileUri; ?>" onclick="downloadLineSheetPDF(event)" title="<?php _e('click to download', LSGEN_TDOM); ?>"><?php echo $file; ?></a>
                            </p>

                        <?php endforeach;

                    // If no files found
                    else : ?>

                        <span id="ls-pdf-no-links">
                            <?php _e('There are currently no previously generated PDFs.', LSGEN_TDOM); ?>
                        </span>

                    <?php endif; ?>

                </div>

            </div>

        </div>


        <!-- linesheet preview cont -->
        <div id="ls-preview-cont">

            <style>
                div#ls-preview-cont h1 {
                    font-size: 22px;
                }
            </style>

            <h1><?php _e('Line sheet preview will appear here when you click Generate Preview', LSGEN_TDOM); ?></h1>

        </div>

    </div>

    <!-- script -->
    <script>
        $ = jQuery;

        /* ******* */
        /* Select2 */
        /* ******* */
        $('#prod_ids').select2({
            'placeholder': '<?php _e('Start typing to search...', LSGEN_TDOM); ?>'
        });

        /* ************************ */
        /* Textarea character count */
        /* ************************ */
        const textarea = document.getElementById('intro');
        const characterCount = document.getElementById('characterCount');

        textarea.addEventListener('input', function() {
            const input = this.value;
            const count = input.length;

            characterCount.textContent = count + '/250';
        });

        /* **************** */
        /* Generate preview */
        /* **************** */
        function generatePreview(event) {

            // vars
            var btn = $(event.target),
                prod_ids = $('#prod_ids').val(),
                page_layout = $('#page_layout').val(),
                email = $('#email').val(),
                intro = $('#intro').val(),
                header_text = $('#header_text').val(),
                allow_links = $('#allow_links').val(),
                tracking_vars = $('#tracking_vars').val(),
                currency = $('#currency').length ? $('#currency').val() : null;

            btn.text('<?php _e('Working...', LSGEN_TDOM) ?>');

            // error check
            if (!prod_ids || !page_layout || !email || !header_text) {
                alert('<?php _e('Please supply all required parameters!', LSGEN_TDOM) ?>');
                btn.text('<?php _e('Generate Preview', LSGEN_TDOM); ?>');
                return;
            }

            // setup and send ajax request
            data = {
                '_ajax_nonce': '<?php echo wp_create_nonce('ls generate preview') ?>',
                'action': 'ls_generate_preview',
                'prod_ids': prod_ids,
                'page_layout': page_layout,
                'email': email,
                'intro': intro,
                'header_text': header_text,
                'currency': currency,
                'allow_links': allow_links,
                'tracking_vars': tracking_vars
            }

            $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                $('#ls-preview-cont').empty().html(response.html);
                $('#generate_ls').attr('last-file-src', response.fileSrc);
                btn.text('<?php _e('Generate Preview', LSGEN_TDOM); ?>');
            })

        }

        /* ****************** */
        /* Generate linesheet */
        /* ****************** */
        function generateLinesheet(event) {

            var btn = $(event.target),
                fileName = $('#ls_name').val(),
                layout = $('#page_layout').val();

            btn.text('<?php _e('Working...', LSGEN_TDOM) ?>');

            // if no file name
            if (!fileName) {
                alert('<?php _e('Please specify file name first!', LSGEN_TDOM); ?>');
                btn.text('<?php _e('Generate Line Sheet', LSGEN_TDOM); ?>');
                return;
            }

            // setup and send ajax request
            data = {
                '_ajax_nonce': '<?php echo wp_create_nonce('ls generate line sheet') ?>',
                'action': 'ls_generate_line_sheet',
                'fileSrc': btn.attr('last-file-src'),
                'fileName': fileName,
                'layout': layout
            }

            $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                alert(response.data);
                location.reload();
                // console.log(response);
            })
        }

        /* ********************************* */
        /* Function: download line sheet PDF */
        /* ********************************* */
        function downloadLineSheetPDF(event) {

            event.preventDefault();

            var link = $(event.target);
            var href = link.attr('href');
            window.open(href, '_blank');

        }

        /* ********************************************** */
        /* Function: delete all generated line sheet PDFs */
        /* ********************************************** */
        function delAllPDFs(event) {

            event.preventDefault();

            var btn = $(event.target).text('<?php _e('Working...', LSGEN_TDOM) ?>');

            data = {
                '_ajax_nonce': '<?php echo wp_create_nonce('ls delete all') ?>',
                'action': 'ls_delete_all',
            }

            alert('<?php _e('Are you sure you want to permanently delete all PDF line sheets?', LSGEN_TDOM) ?>');

            $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                alert(response.data);
                location.reload();
            })

        }

        /******************************************************
         * Function: clear previously generated HTML previews
         ******************************************************/
        function clearLSCache(event) {

            event.preventDefault();

            var btn = $(event.target);

            btn.text('<?php _e('Working...', LSGEN_TDOM); ?>');

            data = {
                '_ajax_nonce': '<?php echo wp_create_nonce('delete ls cache') ?>',
                'action': 'ls_delete_cache',
            }

            $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                alert(response);
                location.reload();
            })

        }
    </script>

    <!-- styles -->
    <style>
        div#ls-generator-admin>h2 {
            background: white;
            padding: 1em 1.5em;
            margin-top: 0;
            margin-left: -19px;
            box-shadow: 0px 2px 4px lightgray;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        button#clear_generation_cache {
            margin-left: 30px;
            border-color: #d63638;
            color: #d63638;
            margin-right: 30px;
        }

        .regular-text {
            width: 100%;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            width: 400px !important;
        }

        div#ls-preview-cont {
            padding: 30px;
            border: 2px solid #ccc;
            border-radius: 5px;
            box-shadow: 0px 2px 4px lightgrey;
            background: white;
            width: 95%;
            display: flex;
            flex-wrap: wrap;
        }

        .select2-container .select2-selection--multiple {
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            min-height: 70px;
        }

        div#ls-preview-cont>h1 {
            text-transform: uppercase;
            color: #999;
            text-shadow: 0px 1px 2px lightgray;
            margin-left: auto;
            margin-right: auto;
        }

        div#ls-generator-form-cont {
            margin-bottom: 4em;
        }

        .line-sheet-page-table-cont:nth-child(even) {
            position: relative;
            left: 12mm;
        }

        .line-sheet-page-table-cont.is-3-page:nth-child(even) {
            left: 0;
        }

        .line-sheet-page-table-cont {
            padding: 5mm 10mm 5mm 10mm !important;
            border: 0.5mm solid #ddd !important;
            border-radius: 1mm;
            box-shadow: 0mm 0mm 1.5mm lightgray;
            margin-bottom: 13mm !important;
        }

        ul#ls-instructions {
            list-style: number;
            padding-left: 15px;
        }

        div#ls-generator-form-cont {
            display: flex;
            flex-basis: min-content;
            align-items: center;
            justify-content: left;
        }

        div#ls-settings-cont {
            display: flex;
        }

        div#ls-previously-generated-overlay {
            position: fixed;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: #0000008c;
            z-index: 999;
        }

        div#ls-previously-generated {
            min-width: 360px;
            width: 34vw;
            padding: 15px;
            position: absolute;
            left: 26vw;
            background: #f0f0f1;
            z-index: 1000;
            border-radius: 1mm;
            min-height: 720px;
        }

        div#ls-previously-generated-cont {
            background: white;
            overflow-y: auto;
            border-radius: 5px;
            border: 1px solid #aaa;
            margin-top: 15px;
            padding: 15px;
            min-height: 655px;
        }

        div#ls-previously-generated>label {
            position: relative;
            display: block;
        }

        button.button.button-small.del-all-pdfs {
            position: absolute;
            right: 5px;
            bottom: -4px;
            background: #d63638;
            border-color: #d63638;
            color: white;
            box-shadow: 0px 2px 2px darkgray;
        }

        div#ls-previously-generated-cont>p {
            margin-top: 0;
        }

        a.ls-close-pu {
            width: 25px;
            height: 25px;
            background: #d63638;
            display: block;
            text-align: center;
            color: white;
            text-decoration: none;
            line-height: 1.7;
            border-radius: 50%;
            box-shadow: 0px 2px 4px black;
            position: absolute;
            right: -11px;
            top: -12px;
        }

        .line-sheet-page-table-cont.is-3-page {
            height: 213mm !important;
        }
    </style>

<?php }

/**
 * Clear HTML template cache via AJAX
 */

add_action('wp_ajax_ls_delete_cache', 'ls_delete_cache');

function ls_delete_cache() {

    check_ajax_referer('delete ls cache');

    $folderPath = LSGEN_PATH . 'admin/generated/';

    // Get all files in the folder
    $files = glob($folderPath . '*');

    // Loop through each file and delete it
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    wp_send_json(__('File cache successfully cleared.', LSGEN_TDOM));
}
