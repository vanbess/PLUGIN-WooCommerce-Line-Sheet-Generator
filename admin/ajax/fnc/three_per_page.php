<?php

defined('ABSPATH') ?: exit();

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
 * @param bool $is_linked - whether or not to link products to product pages
 * @param string $tracking_vars - if linked to product pages, tracking vars to add
 * @return void
 */
function ls_generate_line_sheet_preview_html_three_per_page($prod_ids, $page_layout, $currency, $canvas_size, $header_text, $intro, $email, $is_linked, $tracking_vars) {

    // override standard canvas orientation
    $canvas_size = 'width: 277mm; height: 200mm;';

    // grab ls logo src
    global $ls_logo_url;

    // Determine the total number of pages needed
    $totalProducts = count($prod_ids);
    $totalPages    = ceil($totalProducts / $page_layout);

    // get number of products on the last page
    $numProductsLastPage = $totalProducts % $page_layout;

    ob_start();

    // Generate the HTML layout for each page
    for ($page = 1; $page <= $totalPages; $page++) :

        $startIndex     = ($page - 1) * $page_layout;
        $productIdsPage = array_slice($prod_ids, $startIndex, $page_layout);

?>

        <div class="line-sheet-page-table-cont is-3-page" data-last-page-prods="<?php echo $numProductsLastPage; ?>" data-page="<?php echo $page; ?>" totalpages="<?php echo $totalPages; ?>" style="<?php echo $canvas_size ?> font-size: 10pt;">

            <table class="line-sheet-page page-<?php echo $page ?>" style="width: 100%;border-collapse:collapse;">

                <!-- header -->
                <tr class="ls-header">

                    <!-- logo -->
                    <td colspan="1" style="border-bottom: 1mm solid black; padding-bottom: 3mm; width: 85mm; vertical-align: middle;">
                        <img style="height: 8mm;" src="<?php echo LSGEN_URI . 'admin/logo/logo.jpg'; ?>" alt="<?php echo get_bloginfo('name'); ?>">
                    </td>

                    <!-- title -->
                    <td colspan="1" style="border-bottom: 1mm solid black; padding-bottom: 3mm; font-weight: bold; width: 85mm; text-align: center; vertical-align: middle;">
                        <?php echo trim($header_text); ?>
                    </td>

                    <!-- date -->
                    <td colspan="1" style="border-bottom: 1mm solid black; padding-bottom: 3mm; font-weight: bold; width: 85mm; text-align: right; vertical-align: middle;">
                        <?php echo date('M Y'); ?>
                    </td>

                </tr>

                <!-- product container -->
                <tr class="ls-prod-cont-outer">

                    <td colspan="3">
                        <table class="ls-prod-table" style="width: 100%; border-collapse: collapse;">
                            <?php
                            // loop to generate page data
                            $productCounter = 0;

                            foreach ($productIdsPage as $productId) :
                                $productCounter++;

                                // retrieve relevant product data
                                $prod       = wc_get_product($productId);
                                $pImgId     = $prod->get_image_id();
                                $pImgUrl    = wp_get_attachment_url($pImgId);
                                $pTitle     = trim($prod->get_title());
                                $pSku       = trim($prod->get_sku());
                                $pLink      = $prod->get_permalink();
                                $pAttribs   = $prod->get_attributes();
                                $lsAttribs  = get_post_meta($productId, '_ls_attribs', true);
                                $currSymbol = get_woocommerce_currency_symbol($currency);

                                /**
                                 * Get correct pricing based on subbed currency
                                 */

                                // if has children
                                if ($prod->has_child()) :

                                    $children = $prod->get_children();

                                    // default price
                                    $pPrice = get_post_meta($children[0], '_price', true) ? get_post_meta($children[0], '_price', true) : get_post_meta($children[0], '_regular_price', true);

                                    // case ALG is installed and currencies set up
                                    if (function_exists('alg_wc_cs_get_exchange_rate')) :

                                        $conv_rate = alg_wc_cs_get_exchange_rate('USD', $currency);
                                        $pid       = array_rand($children, 1);

                                        $alg_price = get_post_meta($pid, "_alg_currency_switcher_per_product_regular_price_{$currency}_{$pid}");

                                        if ($alg_price) :
                                            $pPrice = $alg_price;
                                        else :
                                            $pPrice = round($pPrice * $conv_rate, ALG_WC_CS_EXCHANGE_RATES_PRECISION);
                                        endif;

                                    endif;

                                // if simple product
                                else :

                                    // if ($currency == $defaultCurrency) :
                                    $pPrice = $prod->get_price();

                                    // case ALG is installed and currencies set up
                                    if (function_exists('alg_wc_cs_get_exchange_rate')) :

                                        $conv_rate = alg_wc_cs_get_exchange_rate('USD', $currency);

                                        $alg_price = get_post_meta($productId, "_alg_currency_switcher_per_product_regular_price_{$currency}");

                                        if ($alg_price) :
                                            $pPrice = $alg_price;
                                        else :
                                            $pPrice = round($pPrice * $conv_rate, ALG_WC_CS_EXCHANGE_RATES_PRECISION);
                                        endif;

                                    endif;

                                endif; ?>

                                <!-- product container -->
                                <tr class="ls-prod">

                                    <!-- product image -->
                                    <td class="ls-prod-img" style="vertical-align: middle; padding-top: 4mm; <?php echo $productCounter % 3 !== 0 ? 'border-bottom: 1mm solid #f3f3f3;' : null; ?> <?php echo $productCounter % 3 === 0 ? 'padding-bottom: 5mm;' : 'padding-bottom: 4mm;' ?><?php echo $productCounter % 3 === 2 && $page == $totalPages ? 'padding-bottom: 64mm;border-bottom: none;' : null; ?> <?php echo $productCounter % 3 === 1 && $page == $totalPages ? 'padding-bottom: 122mm;border-bottom: none;' : null; ?>">

                                        <?php
                                        // linked to product page or not?
                                        switch ($is_linked) {
                                            case true: ?>
                                                <a href="<?php echo $pLink; ?><?php echo !empty($tracking_vars) ? $tracking_vars : '' ?>">
                                                    <img src="<?php echo $pImgUrl ?>" style="width:50mm;" />
                                                </a>
                                            <?php break;

                                            default: ?>
                                                <!-- product cont -->
                                                <img src="<?php echo $pImgUrl ?>" style="width:50mm;" />
                                        <?php break;
                                        } ?>

                                    </td>

                                    <!-- product attributes -->
                                    <td class="ls-prod-attribs" style="vertical-align: top; padding-top: 4mm; width: 100mm; <?php echo $productCounter % 3 !== 0 ? 'border-bottom: 1mm solid #f3f3f3;' : null; ?> <?php echo $productCounter % 3 === 0 ? 'padding-bottom: 5mm;' : 'padding-bottom: 4mm;'; ?>">

                                        <table class="ls-prod-attribs-table" style="width: 100%; border-collapse: collapse;">

                                            <?php
                                            // build attribs array so that we can stripe their table rows
                                            $all_attribs = [
                                                'Name:' => $pTitle,
                                                'SKU:' => $pSku,
                                            ];

                                            // custom line sheet attribs
                                            if ($lsAttribs && is_array($lsAttribs) && !empty($lsAttribs)) :
                                                foreach ($lsAttribs as $attribName => $attribVal) :
                                                    $all_attribs[$attribName . ':'] = $attribVal;
                                                endforeach;
                                            endif;

                                            // default product attribs
                                            foreach ($pAttribs as $attribute) :
                                                $all_attribs[wc_attribute_label($attribute['name']) . 's:'] = implode(' / ',  wc_get_product_terms($productId, $attribute['name'], array('fields' => 'names')));
                                            endforeach;

                                            // setup attrib counter for striping
                                            $attrib_counter = 0;

                                            // loop through and render each attrib as table element
                                            foreach ($all_attribs as $attr_name => $attr_val) : $attrib_counter++; ?>
                                                <tr style="<?php echo $attrib_counter % 2 === 0 ? null : 'background-color: #f3f3f3;'; ?>">
                                                    <td style="font-weight: bold; width: 30mm; padding-left: 2mm;"><?php echo $attr_name; ?></td>
                                                    <td><?php echo $attr_val; ?></td>
                                                </tr>
                                            <?php endforeach; ?>

                                        </table>

                                    </td>

                                    <!-- wholesale pricing -->
                                    <td class="ls-prod-pricing" style="vertical-align: top; padding-top: 4mm; width: 100mm; <?php echo $productCounter % 3 !== 0 ? 'border-bottom: 1mm solid #f3f3f3;' : null; ?> <?php echo $productCounter % 3 === 0 ? 'padding-bottom: 5mm;' : 'padding-bottom: 4mm;'; ?>">

                                        <table class="ls-prod-pricing-table" style="width: 100%; border-collapse: collapse;">

                                            <tr style="background-color: #f3f3f3; border-left: 4mm solid white;">
                                                <td style="padding-left: 2mm;"></td>
                                                <td style="font-weight: bold; text-align: center;">Price</td>
                                                <td style="font-weight: bold; text-align: center;">Currency</td>
                                                <td style="font-weight: bold; text-align: center;">Discount</td>
                                            </tr>

                                            <tr style="border-left: 4mm solid white;">
                                                <td style="font-weight: bold;padding-left: 2mm;">Retail Price:</td>
                                                <td style="text-align: center;"><?php echo $currSymbol; ?><?php echo number_format($pPrice, 2, '.', ''); ?></td>
                                                <td style="text-align: center;"><?php echo $currency; ?></td>
                                                <td style="text-align: center;">-</td>
                                            </tr>

                                            <tr style="background-color: #f3f3f3; border-left: 4mm solid white;">
                                                <td colspan="4" style="font-weight: bold;padding-left: 2mm;">Wholesale Price:</td>
                                            </tr>

                                            <!-- wholesale discounts -->
                                            <!-- 30% -->
                                            <tr style=" border-left: 4mm solid white;">
                                                <td style="padding-left: 2mm;">10 - 24 Pieces</td>
                                                <td style="text-align: center;">
                                                    <?php
                                                    $discAmnt  = $pPrice * (30 / 100);
                                                    $discPrice = $pPrice - $discAmnt;
                                                    $rounded   = floor(($discPrice * 100) / 100) + .99;
                                                    $rounded   = number_format($rounded, 2, '.', '');

                                                    echo $currSymbol . $rounded;
                                                    ?>
                                                </td>
                                                <td style="text-align: center;"><?php echo $currency; ?></td>
                                                <td style="text-align: center;">30%</td>
                                            </tr>

                                            <!-- 35% -->
                                            <tr style="background-color: #f3f3f3; border-left: 4mm solid white;">
                                                <td style="padding-left: 2mm;">25 - 49 Pieces</td>
                                                <td style="text-align: center;">
                                                    <?php
                                                    $discAmnt  = $pPrice * (35 / 100);
                                                    $discPrice = $pPrice - $discAmnt;
                                                    $rounded   = floor(($discPrice * 100) / 100) + .99;
                                                    $rounded   = number_format($rounded, 2, '.', '');

                                                    echo $currSymbol . $rounded;
                                                    ?>
                                                </td>
                                                <td style="text-align: center;"><?php echo $currency; ?></td>
                                                <td style="text-align: center;">35%</td>
                                            </tr>

                                            <!-- 40% -->
                                            <tr style=" border-left: 4mm solid white;">
                                                <td style="padding-left: 2mm;">50 - 99 Pieces</td>
                                                <td style="text-align: center;">
                                                    <?php
                                                    $discAmnt  = $pPrice * (40 / 100);
                                                    $discPrice = $pPrice - $discAmnt;
                                                    $rounded   = floor(($discPrice * 100) / 100) + .99;
                                                    $rounded   = number_format($rounded, 2, '.', '');

                                                    echo $currSymbol . $rounded;
                                                    ?>
                                                </td>
                                                <td style="text-align: center;"><?php echo $currency; ?></td>
                                                <td style="text-align: center;">40%</td>
                                            </tr>

                                            <!-- 45% -->
                                            <tr style="background-color: #f3f3f3; border-left: 4mm solid white;">
                                                <td style="padding-left: 2mm;">100 - 249 Pieces</td>
                                                <td style="text-align: center;">
                                                    <?php
                                                    $discAmnt  = $pPrice * (45 / 100);
                                                    $discPrice = $pPrice - $discAmnt;
                                                    $rounded   = floor(($discPrice * 100) / 100) + .99;
                                                    $rounded   = number_format($rounded, 2, '.', '');

                                                    echo $currSymbol . $rounded;
                                                    ?>
                                                </td>
                                                <td style="text-align: center;"><?php echo $currency; ?></td>
                                                <td style="text-align: center;">45%</td>
                                            </tr>

                                            <!-- 50% -->
                                            <tr style=" border-left: 4mm solid white;">
                                                <td style="padding-left: 2mm;">250 - 499 Pieces</td>
                                                <td style="text-align: center;">
                                                    <?php
                                                    $discAmnt  = $pPrice * (50 / 100);
                                                    $discPrice = $pPrice - $discAmnt;
                                                    $rounded   = floor(($discPrice * 100) / 100) + .99;
                                                    $rounded   = number_format($rounded, 2, '.', '');

                                                    echo $currSymbol . $rounded;
                                                    ?>
                                                </td>
                                                <td style="text-align: center;"><?php echo $currency; ?></td>
                                                <td style="text-align: center;">50%</td>
                                            </tr>

                                            <!-- 55% -->
                                            <tr style="background-color: #f3f3f3; border-left: 4mm solid white;">
                                                <td style="padding-left: 2mm;">500 - 1000 Pieces</td>
                                                <td style="text-align: center;">
                                                    <?php
                                                    $discAmnt  = $pPrice * (55 / 100);
                                                    $discPrice = $pPrice - $discAmnt;
                                                    $rounded   = floor(($discPrice * 100) / 100) + .99;
                                                    $rounded   = number_format($rounded, 2, '.', '');

                                                    echo $currSymbol . $rounded;
                                                    ?>
                                                </td>
                                                <td style="text-align: center;"><?php echo $currency; ?></td>
                                                <td style="text-align: center;">55%</td>
                                            </tr>

                                            <!-- 58% -->
                                            <tr style=" border-left: 4mm solid white;">
                                                <td style="padding-left: 2mm;">1000+ Pieces</td>
                                                <td style="text-align: center;">
                                                    <?php
                                                    $discAmnt  = $pPrice * (58 / 100);
                                                    $discPrice = $pPrice - $discAmnt;
                                                    $rounded   = floor(($discPrice * 100) / 100) + .99;
                                                    $rounded   = number_format($rounded, 2, '.', '');

                                                    echo $currSymbol . $rounded;
                                                    ?>
                                                </td>
                                                <td style="text-align: center;"><?php echo $currency; ?></td>
                                                <td style="text-align: center;">58%</td>
                                            </tr>
                                        </table>

                                    </td>

                                </tr>
                            <?php
                            endforeach;
                            ?>
                        </table>
                    </td>
                </tr>

                <!-- footer -->
                <tr class="ls-footer">

                    <!-- footer left - contact email -->
                    <td class="ls-footer-left" style="width: 85mm; text-align: left; font-weight: bold; border-top: 1mm solid black; padding-top: 3mm; margin-top: 3mm; vertical-align: middle;">
                        <a class="ls-mailto" href="mailto:<?php echo trim($email) ?>" style="text-decoration: none; color: black;">
                            <?php echo trim($email) ?>
                        </a>
                    </td>

                    <!-- footer center - page number -->
                    <td class="ls-footer-center" style="width: 85mm; text-align: center; font-weight: bold; border-top: 1mm solid black; padding-top: 3mm; vertical-align: middle;">
                        <?php echo trim($page) ?>
                    </td>

                    <!-- footer right - website linke -->
                    <td class="ls-footer-right" style="width: 85mm; text-align: right; font-weight: bold; border-top: 1mm solid black; padding-top: 3mm; vertical-align: middle;">
                        <a href="<?php echo get_site_url() ?>" style="text-decoration: none; color: black;">
                            <?php echo trim(str_replace(['http://', 'https://'], '', get_site_url())) ?>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
        <?php
        // add page break comment to all pages but last page so that we know where to split the html into separate pages in gen_line_sheet.php
        if ($page < $totalPages) : ?>
            <!--mpdf <pagebreak sheet-size="A4-L" /> mpdf-->
<?php endif;
    endfor;

    $html = ob_get_clean();
    return $html;
}

?>