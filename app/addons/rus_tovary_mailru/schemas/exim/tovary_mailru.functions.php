<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

// rus_build_mailru dbazhenov

use Tygh\Registry;

//
// Export product categories
// Parameters:
// @product_id - product ID
// @link_type - M - main category, A - additional
// @delimter - path delimiter
// @lang_code - language code
function fn_exim_mailru_get_product_category($product_id, $link_type, $lang_code = '')
{
    $category_id = db_get_field("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = ?s", $product_id, $link_type);

    return $category_id;
}

//
// Export product features
// Parameters:
// @product_id - product ID
// @lang_code - language code
function fn_exim_mailru_get_product_features($product_id, $lang_code = CART_LANGUAGE)
{
    static $features;

    if (!isset($features[$lang_code])) {
        list($features[$lang_code]) = fn_get_product_features(array('plain' => true), 0, $lang_code);
    }

    //params
    $main_category = db_get_field('SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = ?s', $product_id, 'M');

    $product = array(
        'product_id' => $product_id,
        'main_category' => $main_category
    );

    $product_features = fn_get_product_features_list($product, 'A', $lang_code);

    $result = array();
    if (!empty($product_features)) {
        foreach ($product_features as $f) {
            $display_on_catalog = $features[$lang_code][$f['feature_id']]['display_on_catalog'];
            $display_on_product = $features[$lang_code][$f['feature_id']]['display_on_product'];

            if ($display_on_catalog == "Y" || $display_on_product == "Y") {
                if ($f['feature_type'] == "C") {
                    $result[] = array(
                        'description' => fn_exim_mailru_get_product_info($f['description']),
                        'value' => ($f['value'] == "Y") ? __("yes") : __ ("no")
                    );
                } elseif ($f['feature_type'] == "S" && !empty($f['variant'])) {
                    $result[] = array(
                        'description' => fn_exim_mailru_get_product_info($f['description']),
                        'value' => fn_exim_mailru_get_product_info($f['variant'])
                    );
                } elseif ($f['feature_type'] == "T" && !empty($f['value'])) {
                    $result[] = array(
                        'description' => fn_exim_mailru_get_product_info($f['description']),
                        'value' => fn_exim_mailru_get_product_info($f['value'])
                    );
                } elseif ($f['feature_type'] == "M") {
                    if (!empty($f['variants'])) {
                        $_value = '';
                        $counter = count($f['variants']);
                        foreach ($f['variants'] as $_variant) {
                            if ($counter > 1) {
                                $_value .= $_variant['variant'] . ', ';
                            } else {
                                $_value = $_variant['variant'];
                            }
                        }
                        $_value = ($counter > 1) ? substr($_value, 0, -2) : $_value;
                        $result[] = array(
                            'description' => fn_exim_mailru_get_product_info($f['description']),
                            'value' => fn_exim_mailru_get_product_info($_value)
                        );
                    }
                } elseif ($f['feature_type'] == "N") {
                    $result[] = array(
                        'description' => fn_exim_mailru_get_product_info($f['description']),
                        'value' => fn_exim_mailru_get_product_info($f['variant'])
                    );
                } elseif ($f['feature_type'] == "O") {
                    $result[] = array(
                        'description' => fn_exim_mailru_get_product_info($f['description']),
                        'value' => fn_exim_mailru_get_product_info($f['value_int'])
                    );
                }
            } elseif ($f['feature_type'] == "E") {
                    $result[] = array(
                        'description' => fn_exim_mailru_get_product_info($f['description']),
                        'value' => fn_exim_mailru_get_product_info($f['variant'])
                    );
            }
        }
    }

    return !empty($result) ? $result : '';
}

function fn_exim_mailru_get_image_url($product_id, $object_type, $pair_type, $get_icon, $get_detailed, $lang_code)
{
    $image_pair = fn_get_image_pairs($product_id, $object_type, $pair_type, $get_icon, $get_detailed, $lang_code);

    $image_data = fn_image_to_display($image_pair, Registry::get('settings.Thumbnails.product_details_thumbnail_width'), Registry::get('settings.Thumbnails.product_details_thumbnail_height'));

    if (strpos($image_data['image_path'],'.php')) {
            $image_data['image_path'] = fn_generate_thumbnail($image_data['detailed_image_path'], $image_data['width'], $image_data['height']);
    }

    if (!empty($image_data['image_path'])) {
        $url = $image_data['image_path'];
    } else {
        $url = '';
    }

    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}

function fn_exim_mailru_export_price($price, $decimals_separator)
{
    return fn_format_rate_value($price,'F',2,$decimals_separator,'','');
}

function fn_exim_mailru_get_product_info($data)
{
    return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
}
