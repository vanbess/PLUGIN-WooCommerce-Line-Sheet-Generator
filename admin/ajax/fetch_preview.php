<?php

defined('ABSPATH') ?: exit();

/**
 * Generate linesheet preview and return generated HTML
 */
add_action('wp_ajax_nopriv_ls_generate_preview', 'ls_generate_preview');
add_action('wp_ajax_ls_generate_preview', 'ls_generate_preview');

function ls_generate_preview() {

    check_ajax_referer('ls generate preview');

    // vars
    $email       = $_POST['email'];
    $header_text = $_POST['header_text'];
    $intro       = $_POST['intro'];
    $layout      = $_POST['layout'];
    $per_page    = $_POST['per_page'];
    $prod_ids    = $_POST['prod_ids'];
    $currency    = $_POST['currency'];

    // setup canvas size (minus 20mm to accommodate 10mm page margin all round)
    $canvas_size = $layout === 'a4' ? 'width: 210mm; height: 297mm;' : 'width: 216mm; height: 279mm;';

    // generate preview HTML
    $return_html = ls_generate_line_sheet_preview_html($prod_ids, $per_page, $currency, $canvas_size, $header_text, $intro, $email, $layout);

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

/**
 * Generates and returns line sheet preview HTML
 *
 * @param array $prod_ids - array of product IDs to use
 * @param int $per_page - the amount of products per page
 * @param string $canvas_size - the width and height of a page in pixels, determined by the layout size selected (A4 or US Letter)
 * @param string $currency - the currency to fetch product pricing for; depends on Currency Switcher for WooCommerce Pro being installed; defualts to base currency if not installed
 * @param string $header_text - header text to be added to the top of each page in the line sheet
 * @param string $intro - the line sheet intro to be displayed on the first page of the line sheet document
 * @param string $email - email to which enquiries can be sent
 * @param string $layout - the layout chosen, i.e. A4 or US letter
 * @return void
 */
function ls_generate_line_sheet_preview_html($prod_ids, $per_page, $currency, $canvas_size, $header_text, $intro, $email, $layout) {

    // Determine the total number of pages needed
    $totalProducts = count($prod_ids);
    $totalPages    = ceil($totalProducts / $per_page);

    // Get default currency
    $defaultCurrency =  get_woocommerce_currency();

    ob_start();

    // Generate the HTML layout for each page
    for ($page = 1; $page <= $totalPages; $page++) :

        $startIndex     = ($page - 1) * $per_page;
        $productIdsPage = array_slice($prod_ids, $startIndex, $per_page);

        $isNotFrontPage = $page !== 1 ? ' not-front-page' : null;
?>
        <div class="line-sheet-page-table-cont" style="padding: 0; margin: 0; <?php echo $canvas_size ?>">

            <table class="line-sheet-page page-<?php echo $page ?> layout-<?php echo $layout ?>" style="border-collapse: collapse; font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell,'Helvetica Neue',sans-serif;">

                <!-- header -->
                <tr class="ls-header">
                    <td colspan="2" style="text-align: left; font-weight:bold; font-size: 4mm; border-bottom: 0.5mm solid black; padding-top:3mm; padding-bottom: 3mm;">
                        <?php echo trim(get_bloginfo('name')); ?>
                    </td>
                    <td colspan="1" style="text-align: right; font-weight: bold; font-size: 4mm; border-bottom: 0.5mm solid black; padding-top:3mm; padding-bottom: 3mm;">
                        <?php echo trim($header_text); ?>
                    </td>
                </tr>

                <?php
                // setup first page intro text
                if ($page === 1) :
                ?>
                    <tr>
                        <td colspan="3" style="text-align: left;line-height:6mm;font-size: 4mm;padding-top: 3mm;padding-bottom: 2mm; min-height:13.5mm;">
                            <?php echo trim($intro) ?>
                        </td>
                    </tr>

                <?php else : ?>
                    <tr>
                        <td colspan="3" style="text-align: left;line-height:6mm;font-size: 4mm;padding-top: 3mm;padding-bottom: 2mm; min-height: 13.5mm;">
                        </td>
                    </tr>
                <?php endif; ?>

                <!-- product container -->
                <tr class="ls-prod-cont-outer">

                    <td colspan="3">
                        <table class="ls-prod-table">
                            <?php
                            // loop to generate page data
                            $productCounter = 0;

                            foreach ($productIdsPage as $productId) :
                                $productCounter++;

                                // retrieve relevant product data
                                $prod    = wc_get_product($productId);
                                $pImgId  = $prod->get_image_id();
                                $pImgUrl = wp_get_attachment_url($pImgId);
                                $pTitle  = trim($prod->get_title());
                                $pSku    = trim($prod->get_sku());
                                $pLink   = $prod->get_permalink();

                                // Setup correct pricing based on subbed currency
                                $pPrice = $prod->get_price();

                                if ($currency == $defaultCurrency) :
                                    $pPrice = $prod->get_price();
                                else :
                                    $pPrice = get_post_meta($productId, "_alg_currency_switcher_per_product_regular_price_$currency", true) ?: $pPrice;
                                endif;

                                // setup prod pair container
                                if ($productCounter % 2 === 1) : ?>
                                    <tr class="ls-prod-pair">
                                    <?php endif; ?>

                                    <?php if ($productCounter >= 3 && $page === 1 && empty($intro)) : ?>
                                        <!-- product cont -->
                                        <td class="ls-prod-cont" style="text-align:center; padding: 3mm; line-height: 6.5mm; padding-bottom: 20mm;">
                                            <a href="<?php echo $pLink; ?>" style="text-decoration: none; color: black;">
                                                <img style="width:88mm; height: 88mm; border: 0.5mm solid #ddd; box-sizing: border-box; margin-bottom: 5mm;" src="<?php echo $pImgUrl ?>" />
                                                <div class="ls-prod-title" style="font-weight: bold; font-size: 3.5mm;"><?php echo $pTitle ?></div>
                                                <div class="ls-prod-price"><?php echo $currency . ' ' . number_format($pPrice, 2, '.', '') ?></div>
                                                <div class="ls-prod-sku"><?php echo 'SKU: ' . $pSku ?></div>
                                            </a>
                                        </td>

                                    <?php elseif ($productCounter >= 3 && $page > 1) : ?>

                                        <!-- product cont -->
                                        <td class="ls-prod-cont" style="text-align:center; padding: 3mm; line-height: 6.5mm; padding-bottom: 20mm;">
                                            <a href="<?php echo $pLink; ?>" style="text-decoration: none; color: black;">
                                                <img style="width:88mm; height: 88mm; border: 0.5mm solid #ddd; box-sizing: border-box; margin-bottom: 5mm;" src="<?php echo $pImgUrl ?>" />
                                                <div class="ls-prod-title" style="font-weight: bold; font-size: 3.5mm;"><?php echo $pTitle ?></div>
                                                <div class="ls-prod-price"><?php echo $currency . ' ' . number_format($pPrice, 2, '.', '') ?></div>
                                                <div class="ls-prod-sku"><?php echo 'SKU: ' . $pSku ?></div>
                                            </a>
                                        </td>

                                    <?php elseif ($productCounter >= 3 && $page === 1 && !empty($intro)) : ?>

                                        <!-- product cont -->
                                        <td class="ls-prod-cont" style="text-align:center; padding: 3mm; line-height: 6.5mm;">
                                            <a href="<?php echo $pLink; ?>" style="text-decoration: none; color: black;">
                                                <img style="width:88mm; height: 88mm; border: 0.5mm solid #ddd; box-sizing: border-box; margin-bottom: 5mm;" src="<?php echo $pImgUrl ?>" />
                                                <div class="ls-prod-title" style="font-weight: bold; font-size: 3.5mm;"><?php echo $pTitle ?></div>
                                                <div class="ls-prod-price"><?php echo $currency . ' ' . number_format($pPrice, 2, '.', '') ?></div>
                                                <div class="ls-prod-sku"><?php echo 'SKU: ' . $pSku ?></div>
                                            </a>
                                        </td>
                                        
                                    <?php else : ?>
                                        <!-- product cont -->
                                        <td class="ls-prod-cont" style="text-align:center; padding: 3mm; line-height: 6.5mm;">
                                            <a href="<?php echo $pLink; ?>" style="text-decoration: none; color: black;">
                                                <img style="width:88mm; height: 88mm; border: 0.5mm solid #ddd; box-sizing: border-box; margin-bottom: 5mm;" src="<?php echo $pImgUrl ?>" />
                                                <div class="ls-prod-title" style="font-weight: bold; font-size: 3.5mm;"><?php echo $pTitle ?></div>
                                                <div class="ls-prod-price"><?php echo $currency . ' ' . number_format($pPrice, 2, '.', '') ?></div>
                                                <div class="ls-prod-sku"><?php echo 'SKU: ' . $pSku ?></div>
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                    <?php if ($productCounter % 2 === 0 || $productCounter === count($productIdsPage)) : ?>
                                    </tr>
                            <?php
                                    endif;
                                endforeach;
                            ?>
                        </table>
                    </td>
                </tr>

                <!-- footer -->
                <tr class="ls-footer" style="padding-top: 30mm;">

                    <!-- footer left - contact email -->
                    <td class="ls-footer-left" style="width: 63mm; text-align: left; font-weight:bold; font-size: 4mm; border-top: 0.5mm solid black; padding-top: 4mm; margin-bottom: -10mm;">
                        <a style="text-decoration:none; color: black;" class="ls-mailto" href="mailto:<?php echo trim($email) ?>">
                            <?php echo trim($email) ?>
                        </a>
                    </td>

                    <!-- footer center - page number -->
                    <td class="ls-footer-center" style="width: 63mm; text-align: center; font-weight:bold; font-size: 4mm; border-top: 0.5mm solid black; padding-top: 4mm;">
                        <?php echo trim($page) ?>
                    </td>

                    <!-- footer right - website linke -->
                    <td class="ls-footer-right" style="width: 63mm; text-align: right; font-weight:bold; font-size: 4mm; border-top: 0.5mm solid black; padding-top: 4mm;">
                        <a style="text-decoration: none; color: black;" href="<?php echo get_site_url() ?>">
                            <?php echo trim(str_replace(['http://', 'https://'], '', get_site_url())) ?>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
        <?php
        // add page break comment to all pages but last page so that we know where to split the html into separate pages in gen_line_sheet.php
        if ($page < $totalPages) : ?>
            <!--mpdf <pagebreak sheet-size="A4-P" /> mpdf-->
<?php endif;
    endfor;

    $html = ob_get_clean();
    return $html;
}
