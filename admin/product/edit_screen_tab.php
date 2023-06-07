<?php

defined('ABSPATH') ?: exit();

/**
 * Add product data tab for 3 per page line sheet data
 */
add_filter('woocommerce_product_data_tabs', function ($tabs) {

    $tabs['sbwc_line_sheet'] = [
        "label"  => __('Line Sheet Data', LSGEN_TDOM),
        "target" => "sbwc_line_sheet_data",
        "class"  => ["show_if_simple", "show_if_variable"]
    ];

    return $tabs;
});

/**
 * Render data tab
 */
add_action('woocommerce_product_data_panels', function () {

    global $post;

    // get previously save line sheet attributes
    $ls_attribs = get_post_meta($post->ID, '_ls_attribs', true);

    // extract attribute names and values
    $ls_names  = array_keys($ls_attribs);
    $ls_values = array_values($ls_attribs);

?>

    <style>
        .forminp-text {
            font-size: 12px;
        }
    </style>

    <div id="sbwc_line_sheet_data" class="panel woocommerce_options_panel">
        <div class="inline notice woocommerce-message show_if_simple show_if_variable" style="margin: 10px; padding: 10px;">
            <p class="description"><b><?php _e('<u>INSTRUCTIONS:</u><br> Add up to 5 additional (optional) line sheet attributes below to be included in your line sheet.<br> Note that attributes defined under the Attributes panel will be pulled into the line sheet automatically, so keep this in mind while adding additional parameters below. <br>The line sheet has a limitation of 8 parameter lines. Defaults are product name, SKU and a concatenated list of product attributes, with one set of attributes taking up a line, so the more default (variation) attributes a product has, the less space you will have available for custom attributes added below.', LSGEN_TDOM); ?></b></p>
        </div>
        <div class="options_group">
            <p class="form-field">
                <label for="attribute_name[]"><?php _e('Attribute name', LSGEN_TDOM); ?></label>
                <input type="text" class="forminp-text" name="attribute_name" placeholder="<?php _e('eg: Material', LSGEN_TDOM); ?>" value="<?php echo isset($ls_names[0]) ? $ls_names[0] : null; ?>">
            </p>
            <p class="form-field">
                <label for="attribute_value[]"><?php _e('Attribute value', LSGEN_TDOM); ?></label>
                <input type="text" class="forminp-text" name="attribute_value" placeholder="<?php _e('eg: Cotton', LSGEN_TDOM); ?>" value="<?php echo isset($ls_values[0]) ? $ls_values[0] : null; ?>">
            </p>
        </div>
        <div class="options_group">
            <p class="form-field">
                <label for="attribute_name[]"><?php _e('Attribute name', LSGEN_TDOM); ?></label>
                <input type="text" class="forminp-text" name="attribute_name" placeholder="<?php _e('eg: Weight', LSGEN_TDOM); ?>" value="<?php echo isset($ls_names[1]) ? $ls_names[1] : null; ?>">
            </p>
            <p class="form-field">
                <label for="attribute_value[]"><?php _e('Attribute value', LSGEN_TDOM); ?></label>
                <input type="text" class="forminp-text" name="attribute_value" placeholder="<?php _e('eg: 1.2kg', LSGEN_TDOM); ?>" value="<?php echo isset($ls_values[1]) ? $ls_values[1] : null; ?>">
            </p>
        </div>
        <div class="options_group">
            <p class="form-field">
                <label for="attribute_name[]"><?php _e('Attribute name', LSGEN_TDOM); ?></label>
                <input type="text" class="forminp-text" name="attribute_name" placeholder="<?php _e('eg: Volume', LSGEN_TDOM); ?>" value="<?php echo isset($ls_names[2]) ? $ls_names[2] : null; ?>">
            </p>
            <p class="form-field">
                <label for="attribute_value[]"><?php _e('Attribute value', LSGEN_TDOM); ?></label>
                <input type="text" class="forminp-text" name="attribute_value" placeholder="<?php _e('eg: 18 Liters', LSGEN_TDOM); ?>" value="<?php echo isset($ls_values[2]) ? $ls_values[2] : null; ?>">
            </p>
        </div>
        <div class="options_group">
            <p class="form-field">
                <label for="attribute_name[]"><?php _e('Attribute name', LSGEN_TDOM); ?></label>
                <input type="text" class="forminp-text" name="attribute_name" placeholder="<?php _e('eg: Dimensions', LSGEN_TDOM); ?>" value="<?php echo isset($ls_names[3]) ? $ls_names[3] : null; ?>">
            </p>
            <p class="form-field">
                <label for="attribute_value[]"><?php _e('Attribute value', LSGEN_TDOM); ?></label>
                <input type="text" class="forminp-text" name="attribute_value" placeholder="<?php _e('eg: 31*15*45 cm, 12.2 * 5.9 * 17.7 in', LSGEN_TDOM); ?>" value="<?php echo isset($ls_values[3]) ? $ls_values[3] : null; ?>">
            </p>
        </div>
        <div class="options_group">
            <p class="form-field">
                <label for="attribute_name[]"><?php _e('Attribute name', LSGEN_TDOM); ?></label>
                <input type="text" class="forminp-text" name="attribute_name" placeholder="<?php _e('eg: Laptop Size', LSGEN_TDOM); ?>" value="<?php echo isset($ls_names[4]) ? $ls_names[4] : null; ?>">
            </p>
            <p class="form-field">
                <label for="attribute_value[]"><?php _e('Attribute value', LSGEN_TDOM); ?></label>
                <input type="text" class="forminp-text" name="attribute_value" placeholder="<?php _e('eg: 15.6 inch', LSGEN_TDOM); ?>" value="<?php echo isset($ls_values[4]) ? $ls_values[4] : null; ?>">
            </p>
        </div>
        <div class="options_group">
            <p class="form-field">
                <label for="save_ls_attribs"><?php _e('Save attributes', LSGEN_TDOM); ?></label>
                <input type="submit" class="button button-primary button-small" onclick="lsSaveAttributes(event)" value="<?php _e('Save Line Sheet Attributes', LSGEN_TDOM); ?>">
            </p>

        </div>
    </div>

    <script>
        // save attributes
        function lsSaveAttributes(event) {

            $ = jQuery;

            event.preventDefault();

            var att_names = [],
                att_vals = [],
                btn = $(event.target);

            btn.text('<?php _e('Working...', LSGEN_TDOM) ?>');

            $('input[name="attribute_name"]').each(function(index, element) {
                att_names.push($(this).val() !== '' ? $(this).val() : '');
            });

            $('input[name="attribute_value"]').each(function(index, element) {
                att_vals.push($(this).val() !== '' ? $(this).val() : '');
            });

            var data = {
                '_ajax_nonce': '<?php echo wp_create_nonce('save ls custom attributes') ?>',
                'action': 'ls_save_custom_attribs',
                'attrib_names': att_names,
                'attrib_vals': att_vals,
                'prod_id': '<?php echo $post->ID; ?>'
            }

            $.post(ajaxurl, data, function(response) {
                alert(response);
                btn.text('<?php _e('Save Line Sheet Attributes', LSGEN_TDOM) ?>');
                // console.log(response);
            })

        }
    </script>

<?php });

/**
 * Save custom attribs
 */
add_action('wp_ajax_nopriv_ls_save_custom_attribs', 'ls_save_custom_attribs');
add_action('wp_ajax_ls_save_custom_attribs', 'ls_save_custom_attribs');

function ls_save_custom_attribs() {

    check_ajax_referer('save ls custom attributes');

    // wp_send_json($_POST);

    // vars
    $pId         = $_POST['prod_id'];
    $attribNames = $_POST['attrib_names'];
    $attribVals  = $_POST['attrib_vals'];

    // filter arrays
    $attribNamesFiltered = array_filter($attribNames, function ($value) {
        return $value !== '';
    });

    $attribValsFiltered  = array_filter($attribVals, function ($value) {
        return $value !== '';
    });

    // if no attrib name or attrib vals
    if (empty($attribNamesFiltered) || empty($attribValsFiltered)) :
        wp_send_json(__('Please provide valid attribute name and value sets!', LSGEN_TDOM));
    endif;

    // truncate unequal arrays so we can still combine them into a key => value array
    $length = min(count($attribNames), count($attribVals));
    $keys   = array_slice($attribNames, 0, $length);
    $vals   = array_slice($attribVals, 0, $length);

    // combine into attrib name => attrib val array
    $attribs = array_combine($keys, $vals);

    // save to product meta
    $saved = update_post_meta($pId, '_ls_attribs', $attribs);

    if ($saved || is_int($saved)) :
        wp_send_json(__('Additional line sheet attributes successfully saved!', LSGEN_TDOM));
    else :
        wp_send_json(__('Additional line sheet attributes could not be saved, probably because the data is identical to what is already present for this product. Please make sure you enter value line sheet attribute data before attempting to save.', LSGEN_TDOM));
    endif;
}
