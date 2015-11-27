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

use Tygh\Registry;
use Tygh\Storage;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_exim_1c_import($import_data, $user_data, $company_id, $lang_code)
{
    $cml = fn_get_cml_tag_names();
    if (Registry::get('addons.rus_exim_1c.exim_1c_allow_import_categories') == 'Y') {
        fn_exim_1c_import_categories($import_data -> $cml['classifier'], 0, $user_data['user_type'], $company_id, $lang_code, $cml);
    }
    if (Registry::get('addons.rus_exim_1c.exim_1c_allow_import_features') == 'Y') {
        fn_exim_1c_import_features($import_data -> $cml['classifier'], $company_id, $cml);
    }
    fn_exim_1c_import_products($import_data -> $cml['catalog'], $user_data, $company_id, $lang_code, $cml);
}

function fn_exim_1c_import_products($catalog, $user_data, $company_id, $lang_code, $cml)
{
    if (isset($catalog -> $cml['products'])) {   
        $images = $products = array();
        
        list($dir_1c, $dir_1c_url, $dir_1c_images) = fn_rus_exim_1c_get_dir_1c();
        
        $_categories = Registry::get('rus_exim_1c.categories_1c');
        $settings_1c = Registry::get('addons.rus_exim_1c');
        $only_import_offers = $settings_1c['exim_1c_only_import_offers'];
        $import_product_name = $settings_1c['exim_1c_import_product_name'];
        $import_product_code = $settings_1c['exim_1c_import_product_code'];
        $allow_import_categories = $settings_1c['exim_1c_allow_import_categories'];
        $allow_import_features = $settings_1c['exim_1c_allow_import_features'];
        $import_full_description = $settings_1c['exim_1c_import_full_description'];
        $import_short_description = $settings_1c['exim_1c_import_short_description'];
        $import_page_title = $settings_1c['exim_1c_page_title'];
        $property_for_promo_text = trim($settings_1c['exim_1c_property_product']);
        $category_link_type = $settings_1c['exim_1c_category_link_type'];
        $all_images_is_additional = $settings_1c['exim_1c_all_images_is_additional'];
        $add_tax = $settings_1c['exim_1c_add_tax'];
        $hide_product = $settings_1c['exim_1c_add_out_of_stock'];
        $schema_version = $settings_1c['exim_1c_schema_version'];
        $type_option = $settings_1c['exim_1c_type_option'];
        $standart_option_name = $settings_1c['exim_1c_import_option_name'];
        
        $features_1c = array();
        if (Registry::isExist('rus_exim_1c.features_1c')) {
            $features_1c = Registry::get('rus_exim_1c.features_1c');
        }

        foreach ($catalog -> $cml['products'] -> $cml['product'] as $_product) {
            if (empty($_product -> $cml['name']) || empty($_product -> $cml['groups'] -> $cml['id'])) {
                continue;
            }

            $ids = fn_explode('#', $_product -> $cml['id']);
            $guid_product = array_shift($ids);
            $product_id = db_get_field("SELECT product_id FROM ?:products WHERE external_id = ?s", $guid_product);
            $product_id = (empty($product_id)) ? 0 : $product_id;
            if (($product_id != 0) && ($only_import_offers == 'Y')) {
                continue;
            }
            
            $product = array();
            $product['external_id'] = $guid_product;
            $full_name = $product_code = $html_description = ''; 
            foreach ($_product -> $cml['value_fields'] -> $cml['value_field'] as $reckvizit) {
                if (strval($reckvizit -> $cml['name']) == $cml['full_name']) {
                    $full_name = strval($reckvizit -> $cml['value']);
                }
                if (strval($reckvizit -> $cml['name']) == $cml['code']) {
                    $product_code = strval($reckvizit -> $cml['value']);
                }
                if (strval($reckvizit -> $cml['name']) == $cml['html_description']) {
                    $html_description = strval($reckvizit -> $cml['value']);
                }
            }

            if ($schema_version == '2.07' && !empty($_product -> $cml['image'])) {
                foreach ($_product -> $cml['image'] as $file_description) {
                    $filename = fn_basename(strval($file_description));
                    if(fn_exim_1c_file_is_file($filename)){
                        $html_description = file_get_contents($dir_1c . $filename);
                    }
                }
            }

            // Import product name            
            $product['product'] = strval($_product -> $cml['name']);
            if (($import_product_name == 'full_name') && (!empty($full_name))) {
                $product['product'] = $full_name;
            }
            // Import product code
            $article = strval($_product -> $cml['article']);
            $product['product_code'] = !empty($article) ? $article : '';
            if ($import_product_code == 'code') {
                $product['product_code'] = $product_code;
            }
            if ($import_product_code == 'bar') {
                $product['product_code'] = strval($_product -> $cml['bar']);
            }
            // Import product full description
            if ($import_full_description != 'not_import') {
                $product['full_description'] = $_product -> $cml['description'];
            }
            if ($import_full_description == 'html_description') {
                $product['full_description'] = $html_description;
            } elseif ($import_full_description == 'full_name') {
                $product['full_description'] = $full_name;
            }
            // Import product short description
            if ($import_short_description != 'not_import') {
                $product['short_description'] = $_product -> $cml['description'];
            }
            if ($import_short_description == 'html_description') {
                $product['short_description'] = $html_description;
            } elseif ($import_short_description == 'full_name') {
                $product['short_description'] = $full_name;
            }

            // Import page title
            if ($import_page_title == 'name') {
                $product['page_title'] = trim($_product -> $cml['name'], " -");
            } elseif ($import_page_title == 'full_name') {
                $product['page_title'] = trim($full_name, " -");
            }

            // Import promo text
            if (!empty($property_for_promo_text)) {
                $product['promo_text'] = fn_exim_1c_get_promo_text($_product, $property_for_promo_text, $cml);
            }

            $product['company_id'] = ($user_data['user_type'] == 'V') ? $user_data['company_id'] : $company_id;
            if ($allow_import_categories == 'Y') {
                $product['category_id'] = !empty($_categories[strval($_product -> $cml['groups'] -> $cml['id'])]) ? $_categories[strval($_product -> $cml['groups'] -> $cml['id'])] : array('0');
            }
            if ($product_id == 0) {
                $product['price'] = '0.00';
                $product['list_price'] = '0.00';
                $product['timestamp'] = time();
                $product['lower_limit'] = 1;
                $product['details_layout'] = 'default';
                $product['lang_code'] = $lang_code;
                if ($hide_product == 'Y') {
                    $product['status'] = 'H';
                }
                if ($allow_import_categories != 'Y') { 
                    $product['category_id'] = fn_exim_1c_get_default_category($company_id, $settings_1c);
                }
                $product_id = fn_exim_1c_create_product($product);
            } else {
                $product['updated_timestamp'] = time();
                if ($allow_import_categories == 'Y') {
                    $eid = strval($_product -> $cml['groups'] -> $cml['id']);
                    $product['category_id'] = !empty($_categories[$eid]) ? $_categories[$eid] : 0;
                } 
                fn_exim_1c_update_product($product, $product_id, array('allow_import_categories' => $allow_import_categories, 'category_link_type' => $category_link_type), $lang_code, $company_id);
            }
            // Import product features
            if ((isset($_product -> $cml['properties_values'] -> $cml['property_values'])) && ($allow_import_features == 'Y') && (!empty($features_1c))) {
                fn_exim_1c_import_product_features($_product, $product_id, $features_1c, $_categories, $lang_code, $cml);
            }
            // Import taxes
            if (isset($_product -> $cml['taxes_rates']) && ($add_tax == 'Y')) {
                fn_exim_1c_add_tax($_product -> $cml['taxes_rates'], $product_id, $lang_code, $cml);
            }
            // Import images
            $image_main = true;
            foreach ($_product -> $cml['image'] as $image) {
                $filename = fn_basename(strval($image));
                fn_exim_1c_image_search($filename, $image_main, $all_images_is_additional, $dir_1c_images, $product_id, $lang_code);
                $image_main = false;
            }
            // Import combinations
            if ((isset($_product -> $cml['product_features'])) && ($schema_version == '2.07')) {
                fn_exim_1c_import_product_combinations($_product -> $cml['product_features'], $product_id, $lang_code, $cml, $standart_option_name, $company_id, $type_option);
            }

            fn_echo(' ');
        }
        $category_ids = array_unique(db_get_fields("SELECT category_id FROM ?:products_categories ORDER BY category_id"));
        foreach ($category_ids as $category_id) {
            $product_count = db_get_field("SELECT COUNT(*) FROM ?:products_categories WHERE category_id = ?i", $category_id);
            db_query("UPDATE ?:categories SET product_count = ?i WHERE category_id = ?i", $product_count, $category_id);
        }
    }
}

function fn_exim_1c_import_product_combinations($combinations, $product_id, $lang_code, $cml, $standart_option_name, $company_id, $type_option)
{
    $option_data = fn_exim_1c_create_option_structure($product_id, $standart_option_name, $company_id, $type_option);
    foreach ($combinations -> $cml['product_feature'] as $_combination) {
        $option_id = db_get_field("SELECT option_id FROM ?:product_options WHERE product_id = ?i", $product_id);
        $variant_id = db_get_field("SELECT variants.variant_id FROM ?:product_option_variants AS variants "
            . "LEFT JOIN ?:product_option_variants_descriptions AS variants_descriptions ON variants.variant_id = variants_descriptions.variant_id "
            . "WHERE variants.option_id = ?i AND variants_descriptions.lang_code = ?s AND variants_descriptions.variant_name = ?s", $option_id, $lang_code, strval($_combination -> $cml['name']));
        $variant_id = (empty($variant_id)) ? 0 : $variant_id;
        $option_data['variants'][] = array(
            'variant_name' => strval($_combination -> $cml['name']),
            'variant_id' => $variant_id,
            'external_id' => strval($_combination -> $cml['id']),
        );
    }
    $option_id = fn_update_product_option($option_data, $option_id, $lang_code);
}

function fn_exim_1c_image_search($filename, $image_main, $all_images_is_additional, $dir_1c_images, $product_id, $lang_code)
{
    if (fn_exim_1c_file_is_image($filename)) {
        if ($image_main && ($all_images_is_additional != 'Y')) {
            fn_exim_1c_add_product_image($filename, $dir_1c_images, $product_id, 'M', $lang_code);
            $image_main = false;
        } else {
            fn_exim_1c_add_product_image($filename, $dir_1c_images, $product_id, 'A', $lang_code);
        }
    }
}

function fn_exim_1c_add_product_image($filename, $dir_1c_images, $product_id, $type, $lang_code)
{
    if (file_exists($dir_1c_images . $filename)) {
        $detail_file = fn_explode('.', $filename);
        $type_file = array_shift($detail_file);
        $condition = db_quote(" AND images.image_path LIKE ?s", "%" . $type_file . "%");
        $images = db_get_array(
            "SELECT images.image_id, images_links.pair_id"
            . " FROM ?:images AS images"
            . " LEFT JOIN ?:images_links AS images_links ON images.image_id = images_links.detailed_id"
            . " WHERE images_links.object_id = ?i $condition", $product_id);
            
        foreach ($images as $image) {
            fn_delete_image_pair($image['pair_id'], 'product');
        }
        $image_data[] = array(
            'name' => $filename,
            'path' => $dir_1c_images . $filename,
            'size' => filesize($dir_1c_images . $filename),
        );
        $pair_data[] = array(
            'pair_id' => '',
            'type' => $type,
            'object_id' => 0,
            'image_alt' => '',
            'detailed_alt' => '',
        );
        $pair_ids = fn_update_image_pairs(array(), $image_data, $pair_data, $product_id, 'product', array(), 1, $lang_code);
    }
}

function fn_exim_1c_add_tax($tax, $product_id, $lang_code, $cml)
{
    $product = array();
    $tax_id = db_get_field("SELECT tax_id FROM ?:rus_exim_1c_taxes WHERE tax_1c = ?s", strval($tax -> $cml['tax_rate'] -> $cml['rate_t']));
    $_tax_ids = db_get_field("SELECT tax_ids FROM ?:products WHERE product_id = ?i", $product_id);
    $tax_ids = fn_explode(',', $_tax_ids);
    if (!in_array($tax_id, $tax_ids)) {
        $tax_ids[] = $tax_id; 
    }
    foreach ($tax_ids as $key => &$value) {
        if (empty($value)) {
            unset($tax_ids[$key]);
        }
    }
    db_query("UPDATE ?:products SET tax_ids = ?s WHERE product_id = ?i", implode(',', $tax_ids), $product_id);
}

function fn_exim_1c_create_product($product_data)
{
    $product_id = db_query("INSERT INTO ?:products ?e", $product_data);
    $product_data['product_id'] = $product_id;
    db_query("INSERT INTO ?:product_descriptions ?e", $product_data);
    db_query("INSERT INTO ?:product_prices ?e", $product_data);
    db_query("INSERT INTO ?:products_categories ?e", $product_data);
    
    return $product_id;
}

function fn_exim_1c_update_product($product, $product_id, $category_params, $lang_code, $company_id)
{
    // Update categories link
    if ($category_params['allow_import_categories'] == 'Y') {
        $categories = array();
        $_categories = db_get_fields("SELECT category_id FROM ?:products_categories WHERE product_id = ?i ORDER BY category_id", $product_id);
        if (empty($_categories)) {
            $categories = fn_exim_1c_add_category(array(), $product_id, $product['category_id'], 'M');
        } elseif (in_array($product['category_id'], $_categories)) {
            if ($category_params['category_link_type'] == 'main') {
                foreach ($_categories as $category_id) {
                    $_link_type = ($category_id == $product['category_id']) ? 'M' : 'A';
                    $categories = fn_exim_1c_add_category($categories, $product_id, $category_id, $_link_type);
                }
            } else {
                $categories = db_get_array("SELECT * FROM ?:products_categories WHERE product_id = ?i ORDER BY category_id", $product_id);
            }
        } else {
            if ($category_params['category_link_type'] == 'main') {
                foreach ($_categories as $category_id) {
                    $categories = fn_exim_1c_add_category($categories, $product_id, $category_id, 'A');
                }
                $categories = fn_exim_1c_add_category($categories, $product_id, $product['category_id'], 'M');
            } else {
                $categories = db_get_array("SELECT * FROM ?:products_categories WHERE product_id = ?i ORDER BY category_id", $product_id);
                $categories = fn_exim_1c_add_category($categories, $product_id, $product['category_id'], 'A');
            }
        }
        db_query("DELETE FROM ?:products_categories WHERE product_id = ?i", $product_id);
        foreach ($categories as $category_data) {
            db_query("INSERT INTO ?:products_categories ?e", $category_data);
        }
    }
    
    db_query("UPDATE ?:products SET ?u WHERE product_id = ?i", $product, $product_id);
    db_query("UPDATE ?:product_descriptions SET ?u WHERE product_id = ?i AND lang_code = ?s", $product, $product_id, $lang_code);
}

function fn_exim_1c_add_category($categories, $product_id, $category_id, $link_type)
{
    $categories[] = array(
        'product_id' => $product_id,
        'category_id' => $category_id,
        'position' => 0,
        'link_type' => $link_type
    );

    return $categories;
}

function fn_exim_1c_get_promo_text($product, $property_name = '', $cml)
{
    $features_1c = array();
    if (Registry::isExist('rus_exim_1c.features_1c')) {
        $features_1c = Registry::get('rus_exim_1c.features_1c');
    } else {
        return '';
    }
    if (isset($product -> $cml['properties_values'] -> $cml['property_values'])) {
        foreach ($product -> $cml['properties_values'] -> $cml['property_values'] as $_feature) {
            if (!empty($features_1c[strval($_feature -> $cml['id'])])) {
                $feature_name = trim($features_1c[strval($_feature -> $cml['id'])]['name'], " ");
                if ($property_name == $feature_name) {
                    return $_feature -> $cml['value'];
                }
            }
        }
    }

    return '';
}

function fn_exim_1c_get_default_category($company_id, $settings_1c)
{
    $default_category = $settings_1c['exim_1c_default_category'];
    $default_category = db_get_field("SELECT category_id FROM ?:categories WHERE category_id = ?i", $default_category);
    if (!empty($default_category)) {
        return $default_category;
    } else {
        if (!Registry::isExist('rus_exim_1c.default_category')) {
            $category_data = array(
                'category' => 'Default category',
                'status' => 'D',
                'parent_id' => 0,
                'company_id' => $company_id
            );
            Registry::set('rus_exim_1c.default_category', fn_update_category($category_data, 0));
        }
        return Registry::get('rus_exim_1c.default_category');
    }
}

function fn_exim_1c_import_product_features($product, $product_id, $features_1c, $categories, $lang_code, $cml)
{
    $is_id = false;
    $shipping_params = fn_exim_1c_get_shipping_params();
    $category_id = !empty($categories[strval($product -> $cml['groups'] -> $cml['id'])]) ? $categories[strval($product -> $cml['groups'] -> $cml['id'])] : '0';
    $variants_data['product_id'] = $product_id;
    $variants_data['lang_code'] = $lang_code;
    foreach ($product -> $cml['properties_values'] -> $cml['property_values'] as $_feature) {
        if ((!isset($features_1c[strval($_feature -> $cml['id'])]))) {
            continue;
        }

        $variants_data['feature_id'] = $features_1c[strval($_feature -> $cml['id'])]['id'];
        $_variants = '';
        if (!empty($features_1c[strval($_feature -> $cml['id'])]['variants'])) {
            $_variants = $features_1c[strval($_feature -> $cml['id'])]['variants'];
        }
        $value_data['feature_id'] = $features_1c[strval($_feature -> $cml['id'])]['id'];
        $value_data['product_id'] = $product_id;
        $value_data['lang_code'] = $lang_code;
        $variant = array(
            'variant' => '',
        );
        if (empty($_variants)) {   
            $variant['variant'] = strval($_feature -> $cml['value']);
            if (strlen($variant['variant']) > 255) {
                $variant['variant'] = substr($variant['variant'],0,255);
            }
        } else {            
            foreach ($_variants as $_variant) {
                if (strval($_feature -> $cml['value']) == $_variant['id']) {
                    $variant['variant'] = $_variant['value'];
                    $is_id = true;
                    break;
                }
            }
            if (!$is_id) {
                $variant['variant'] = strval($_feature -> $cml['value']);
            }
        } 
        fn_exim_1c_add_shipping_param($product_id, $shipping_params, $_feature, $features_1c[strval($_feature -> $cml['id'])], $cml);
        if ($variants_data['feature_id'] == 0) {
            continue;
        }
        if (!empty($category_id)) {
            $feature_categories = fn_explode(',', db_get_field("SELECT categories_path FROM ?:product_features WHERE feature_id = ?i", $variants_data['feature_id']));
            if (!in_array($category_id, $feature_categories)) {
                $feature_categories[] = $category_id;
                $feature_categories = array_diff($feature_categories, array(''));
                db_query("UPDATE ?:product_features SET categories_path = ?s WHERE feature_id = ?i", implode(',', $feature_categories), $variants_data['feature_id']);
            }
        }
        // Check if current variant already exist
        list($check, $variant_id) = fn_exim_1c_check_feature_variant($variants_data['feature_id'], $variant['variant'], $lang_code);
        if ($check) {
            $variants_data['variant_id'] = fn_add_feature_variant($variants_data['feature_id'], $variant);
        } else {
            $variants_data['variant_id'] = $variant_id;
        }
        fn_exim_1c_add_features_values($variants_data);
    }
}

function fn_exim_1c_add_shipping_param($product_id, $shipping_params, $product_feature, $feature_1c, $cml)
{
    foreach ($shipping_params as $shipping_param) {
        if (in_array($feature_1c['name'], $shipping_param['fields'])) {
            $value = strval($product_feature -> $cml['value']);
            if ($shipping_param['name'] == 'weight_property') {
                $_value = preg_replace('/,/i','.',$value);
                db_query("UPDATE ?:products SET weight = ?i WHERE product_id = ?i", (float) $_value, $product_id);
            }
            if ($shipping_param['name'] == 'free_shipping') {
                if ($value == $cml['yes']) {
                    db_query("UPDATE ?:products SET free_shipping = 'Y' WHERE product_id = ?i", $product_id);
                } else {
                    db_query("UPDATE ?:products SET free_shipping = '' WHERE product_id = ?i", $product_id);
                }
            }
            if ($shipping_param['name'] == 'shipping_cost') {
                $_value = preg_replace('/,/i','.',$value);
                db_query("UPDATE ?:products SET shipping_freight = ?i WHERE product_id = ?i", (float) $_value, $product_id);
            }
            if ($shipping_param['name'] == 'number_of_items') {
                fn_exim_1c_add_box_param($product_id, 'min_items_in_box', (int) $value);
                fn_exim_1c_add_box_param($product_id, 'max_items_in_box', (int) $value);
            }
            if ($shipping_param['name'] == 'box_length') {
                fn_exim_1c_add_box_param($product_id, 'box_length', (int) $value);
            }
            if ($shipping_param['name'] == 'box_width') {
                fn_exim_1c_add_box_param($product_id, 'box_width', (int) $value);
            }
            if ($shipping_param['name'] == 'box_height') {
                fn_exim_1c_add_box_param($product_id, 'box_height', (int) $value);
            }
        }    
    }
}

function fn_exim_1c_add_box_param($product_id, $param, $value)
{
    $shipping_param = db_get_field("SELECT shipping_params FROM ?:products WHERE product_id = ?i", $product_id);
    if (empty($shipping_param)) {
        db_query("UPDATE ?:products SET shipping_params = ?s WHERE product_id = ?i", serialize(array($param => $value)), $product_id);
    } else {
        $_shipping_param = unserialize($shipping_param);
        $_shipping_param[$param] = $value;
        db_query("UPDATE ?:products SET shipping_params = ?s WHERE product_id = ?i", serialize($_shipping_param), $product_id);
    }
    
}

function fn_exim_1c_add_features_values($variants_data) 
{
    db_query("DELETE FROM ?:product_features_values WHERE feature_id = ?i AND product_id = ?i", $variants_data['feature_id'], $variants_data['product_id'], $variants_data['variant_id']);
    db_query("INSERT INTO ?:product_features_values ?e", $variants_data);
}

function fn_exim_1c_check_feature_variant($feature_id, $variant, $lang_code)
{
    $variant_exists = db_get_field(
        "SELECT ?:product_feature_variant_descriptions.variant_id"
        . " FROM ?:product_feature_variant_descriptions"
        . " LEFT JOIN ?:product_feature_variants ON ?:product_feature_variant_descriptions.variant_id = ?:product_feature_variants.variant_id"
        . " WHERE ?:product_feature_variants.feature_id = ?i AND ?:product_feature_variant_descriptions.variant = ?s AND ?:product_feature_variant_descriptions.lang_code = ?s",
        $feature_id, $variant, $lang_code
    );
    $result = (!empty($variant_exists)) ? false : true;

    return array($result, $variant_exists);
}

function fn_exim_1c_import_categories($classifier_data, $parent_id = 0, $user_type, $company_id, $lang_code, $cml)
{
    $categories_1c = array();
    if (isset($classifier_data -> $cml['groups'])) {
        $default_category = Registry::get('addons.rus_exim_1c.exim_1c_default_category');
        foreach ($classifier_data -> $cml['groups'] -> $cml['group'] as $_group) {
            $category_id = db_get_field("SELECT category_id FROM ?:categories WHERE external_id = ?s", strval($_group -> $cml['id']));
            $category_id = (!empty($category_id)) ? $category_id : 0;
            $category_data = array(
                'category' => strval($_group -> $cml['name']),
                'lang_code' => $lang_code,
                'status' => 'A',
                'parent_id' => (int) $parent_id,
                'timestamp' => time(),
                'company_id' => $company_id,
                'external_id' => strval($_group -> $cml['id'])
            );
            if ($user_type != 'V') {
                $category_id = fn_exim_1c_update_category($category_data, $category_id, $lang_code);
            } else {
                $category_id = $default_category;
                $id = db_get_field("SELECT category_id FROM ?:category_descriptions WHERE lang_code = ?s AND category = ?s", $lang_code, strval($_group -> $cml['name']));  
                if (!empty($id)) {
                    $category_id = $id;
                }
            }
            $categories_1c[strval($_group -> $cml['id'])] = $category_id;
            if (isset($_group -> $cml['groups'] -> $cml['group'])) {
                fn_exim_1c_import_categories($_group, $category_id, $user_type, $company_id, $lang_code, $cml);
            }
        }
        if (Registry::isExist('rus_exim_1c.categories_1c')) {
            $_categories_1c = Registry::get('rus_exim_1c.categories_1c');
            $categories_1c = fn_array_merge ($_categories_1c, $categories_1c);
            Registry::set('rus_exim_1c.categories_1c', $categories_1c);    
        } else {
            Registry::set('rus_exim_1c.categories_1c', $categories_1c);    
        }
    }
}

function fn_exim_1c_update_category($category_data, $category_id, $lang_code)
{
    $_data = $category_data;
    unset($_data['parent_id']);
    $_data['timestamp'] = fn_parse_date($category_data['timestamp']);
    
    if (empty($category_id)) {
        $create = true;

        $category_id = db_query("INSERT INTO ?:categories ?e", $_data);
        $_data['category_id'] = $category_id;
        foreach (fn_get_translation_languages() as $_data['lang_code'] => $v) {
            db_query("INSERT INTO ?:category_descriptions ?e", $_data);
        }
        $category_data['parent_id'] = !empty($category_data['parent_id']) ? $category_data['parent_id'] : 0;
    } else {
        db_query("UPDATE ?:categories SET ?u WHERE category_id = ?i", $_data, $category_id);
        db_query("UPDATE ?:category_descriptions SET ?u WHERE category_id = ?i AND lang_code = ?s", $_data, $category_id, $lang_code);
    }
    if ($category_id) {
        if (isset($category_data['parent_id'])) {
            fn_change_category_parent($category_id, intval($category_data['parent_id']));
        }
    }
    
    return $category_id;
}

function fn_exim_1c_import_features($classifier_data, $company_id, $cml)
{
    $settings_1c = Registry::get('addons.rus_exim_1c');
    if (isset($classifier_data -> $cml['properties'] -> $cml['property'])) {
        $features_1c = array();

        $promo_text = trim($settings_1c['exim_1c_property_product']);

        $shipping_params = fn_exim_1c_get_shipping_params();
        $features_list = fn_explode("\n", $settings_1c['exim_1c_features_list']);
        $deny_or_allow_list = $settings_1c['exim_1c_deny_or_allow'];

        foreach ($classifier_data -> $cml['properties'] -> $cml['property'] as $_feature) {
            $_variants = array();
            if ($deny_or_allow_list != 'not_used') {
                if ($deny_or_allow_list == 'do_not_import') {
                    if (in_array(strval($_feature -> $cml['name']), $features_list)) {
                        continue;
                    }
                } else {
                    if (!in_array(strval($_feature -> $cml['name']), $features_list)) {
                        continue;
                    }
                }
            }

            $feature_data = array();
            
            $id = db_get_field("SELECT feature_id FROM ?:product_features WHERE external_id = ?s", strval($_feature -> $cml['id']));
            $new_feature = false;
            $feature_id = $id;
            if (empty($id)) {
                $new_feature = true;
                $feature_id = 0;
            }
            $feature_id = (!empty($id)) ? $id : 0;   
            $feature_data = fn_exim_1c_get_feature(strval($_feature -> $cml['name']), $feature_id, strval($_feature -> $cml['id']), $company_id, $settings_1c);

            if (fn_exim_1c_feature_display_shipping_param(strval($_feature -> $cml['name']), $shipping_params)) {
                if ($promo_text != strval($_feature -> $cml['name'])) {
                    $feature_id = fn_update_product_feature($feature_data, $feature_id);
                    if ($new_feature) {
                        db_query("INSERT INTO ?:ult_objects_sharing VALUES ($company_id, $feature_id, 'product_features')");
                    }
                } else {
                    fn_delete_feature($feature_id);
                }
            }
            
            $count = 0;
            
            if (!empty($_feature -> $cml['variants_values'])) {

                $_feature_data = $_feature -> $cml['variants_values'] -> $cml['directory'];
                foreach ($_feature_data as $_variant) {
                    $_variants[$count]['id'] = strval($_variant -> $cml['id_value']);
                    $_variants[$count]['value'] = strval($_variant -> $cml['value']);
                    $count++;
                }
            }

            $features_1c[strval($_feature -> $cml['id'])]['id'] = $feature_id;
            $features_1c[strval($_feature -> $cml['id'])]['name'] = strval($_feature -> $cml['name']);
            if (!empty($_variants)) {
                $features_1c[strval($_feature -> $cml['id'])]['variants'] = $_variants;
            }
        }

        if (Registry::isExist('rus_exim_1c.features_1c')) {
            $_features_1c = Registry::get('rus_exim_1c.features_1c');
            $features_1c = fn_array_merge ($_features_1c, $features_1c);
            Registry::set('rus_exim_1c.features_1c', $features_1c);    
        } else {
            Registry::set('rus_exim_1c.features_1c', $features_1c);    
        }
    }
}

function fn_exim_1c_feature_display_shipping_param($feature_name, $shipping_params)
{
    foreach ($shipping_params as $shipping_param) {
        if (in_array($feature_name, $shipping_param['fields'])) {
            if ($shipping_param['display'] == 'Y') {
                return true;
            } else {
                return false;
            }
        }
    }
    
    return true;
}

function fn_exim_1c_get_feature($feature_name, $feature_id, $external_id, $company_id, $settings_1c)
{
    $feature_type = 'S';
    $property_for_manufacturer = trim($settings_1c['exim_1c_property_for_manufacturer']);
    if (!empty($property_for_manufacturer) && ($property_for_manufacturer == $feature_name)) {
        $feature_type = 'E';
    }
    $data = array(
        'variants' => array(),
        'description' => $feature_name,
        'position' => 0,
        'feature_type' => $feature_type,
        'parent_id' => 0,
        'prefix' => '',
        'suffix' => '',
        'company_id' => $company_id,
        'external_id' => $external_id
    );

    if (empty($feature_id)) {
        $data['display_on_catalog'] = "Y";
        $data['display_on_product'] = "Y";
    }

    return $data;
}

function fn_exim_1c_offers($xml, $company_id, $lang_code)
{
    $cml = fn_get_cml_tag_names();
    $create_prices = Registry::get('addons.rus_exim_1c.exim_1c_create_prices');
    $type_option = Registry::get('addons.rus_exim_1c.exim_1c_type_option');
    $hide_product = Registry::get('addons.rus_exim_1c.exim_1c_add_out_of_stock');
    $schema_version = Registry::get('addons.rus_exim_1c.exim_1c_schema_version');
    if ((isset($xml -> $cml['packages'] -> $cml['prices_types'])) && ($create_prices == 'Y')) {
        $prices_1c = array();
        $_prices_1c = db_get_array("SELECT price_1c, type, usergroup_id FROM ?:rus_exim_1c_prices");
        foreach ($xml -> $cml['packages'] -> $cml['prices_types'] -> $cml['price_type'] as $_price) {
            foreach ($_prices_1c as $_price_1c) {
                if ($_price_1c['price_1c'] == strval($_price -> $cml['name'])) {
                    $_price_1c['external_id'] = strval($_price -> $cml['id']);
                    $prices_1c[] = $_price_1c;
                }
            }
        }
    }
    if (isset($xml -> $cml['packages'] -> $cml['offers'])) {
        $standart_option_name = Registry::get('addons.rus_exim_1c.exim_1c_standart_option_name');
        $import_mode = Registry::get('addons.rus_exim_1c.exim_1c_import_mode_offers');
        $standart_option_name = Registry::get('addons.rus_exim_1c.exim_1c_import_option_name'); 
        
        $options_data = $global_options_data = array();
        foreach ($xml -> $cml['packages'] -> $cml['offers'] -> $cml['offer'] as $offer) {
            $product = array();
            $ids = fn_explode('#', strval($offer -> $cml['id']));
            $product_guid = array_shift($ids);
            $combination_guid = (!empty($ids)) ? array_shift($ids) : '';
            $product_id = db_get_field("SELECT product_id FROM ?:products WHERE external_id = ?s", $product_guid);
            if (empty($product_id)) {
                continue;
            }

            $amount = 0;
            if (isset($offer -> $cml['store'])) {
                foreach ($offer -> $cml['store'] as $store) {
                    $amount += strval($store[$cml['in_stock']]);
                }
            }
            if (isset($offer -> $cml['amount'])) {
                $amount = strval($offer -> $cml['amount']);
            }
            
            $prices = array();
            if (isset($offer -> $cml['prices'])) {
                if ($create_prices == 'Y') {
                    foreach ($offer -> $cml['prices'] -> $cml['price'] as $_price_data) {
                        foreach ($prices_1c as $price_1c) {
                            if (strval($_price_data -> $cml['price_id']) == $price_1c['external_id']) {
                                                   
                                if ($price_1c['type'] == 'base') {
                                    $prices['base_price'] = strval($_price_data -> $cml['price_per_item']);
                                }
                                if ($price_1c['type'] == 'list') {
                                    $prices['list_price'] = strval($_price_data -> $cml['price_per_item']);
                                }
                                if ($price_1c['usergroup_id'] > 0) {
                                    $prices['qty_prices'][] = array(
                                        'usergroup_id' => $price_1c['usergroup_id'],
                                        'price' => strval($_price_data -> $cml['price_per_item']),
                                    );
                                }
                            }
                        }
                    }
                } else {
                    $prices['base_price'] = strval($offer -> $cml['prices'] -> $cml['price'] -> $cml['price_per_item']);
                }
            }

            if (empty($prices)) {
                $prices['base_price'] = 0;
            }

            if (empty($combination_guid)) {
                db_query("UPDATE ?:products SET ?u WHERE product_id = ?i", array('amount' => $amount), $product_id);
                // To hide products if they are out of stock
                if ($hide_product == 'Y') {
                    if ($amount == 0) {
                        db_query("UPDATE ?:products SET status = 'H' WHERE product_id = ?i", $product_id);
                    }
                }
                fn_exim_1c_add_price($prices, $product_id);
            } else {
                db_query("UPDATE ?:products SET ?u WHERE product_id = ?i", array('tracking' => 'O'), $product_id);
                if ($schema_version == '2.07') {
                    if (!empty($prices['base_price'])) {
                        fn_exim_1c_add_price(array('base_price' => 0), $product_id);
                    }

                    $option_id = db_get_field("SELECT option_id FROM ?:product_options WHERE product_id = ?i", $product_id);
                    $variant_id = db_get_field("SELECT variant_id FROM ?:product_option_variants WHERE external_id = ?s AND option_id = ?i", $combination_guid, $option_id);
                    db_query("UPDATE ?:product_option_variants SET modifier = ?i WHERE variant_id = ?i", $prices['base_price'], $variant_id);
                    
                    $old_combination_hash = db_get_field("SELECT combination_hash FROM ?:product_options_inventory WHERE external_id = ?s", $combination_guid);
                    $image_pair_id = db_get_field("SELECT pair_id FROM ?:images_links WHERE object_id = ?i", $old_combination_hash);
                    db_query("DELETE FROM ?:product_options_inventory WHERE external_id = ?s AND product_id = ?i", $combination_guid, $product_id);
                    $combination_data = array(
                        'product_id' => $product_id,
                        'combination_hash' => fn_generate_cart_id($product_id, array('product_options' => array($option_id => $variant_id))),
                        'combination' => fn_get_options_combination(array($option_id => $variant_id)),
                        'amount' => $amount,
                        'external_id' => $combination_guid
                    );
                    $variant_combination = db_get_field("SELECT combination_hash FROM ?:product_options_inventory WHERE combination_hash = ?i", $combination_data['combination_hash']);
                    if (empty($variant_combination)) {
                        db_query("INSERT INTO ?:product_options_inventory ?e", $combination_data);
                    }
                    if (!empty($image_pair_id)) {
                        db_query("UPDATE ?:images_links SET object_id = ?i WHERE pair_id = ?i", $combination_data['combination_hash'], $image_pair_id);
                    }

                    continue;
                }

                if ($import_mode == 'standart') {
                    $variant_name = '';
                    if (!empty($offer -> $cml['product_features'] -> $cml['product_feature'])) {
                        foreach($offer -> $cml['product_features'] -> $cml['product_feature'] as $feature_data) {
                            $variant_name .= strval($feature_data -> $cml['name']) . ':' . strval($feature_data -> $cml['value']) . '; ';
                        }
                    }
                    $options_data[$product_id][] = array(
                        'variant_name' => $variant_name,
                        'amount' => $amount,
                        'combination_guid' => $combination_guid,
                        'price' => $prices['base_price'],
                    );

                    if (!empty($prices['base_price'])) {
                        fn_exim_1c_add_price(array('base_price' => 0), $product_id);
                    }
                } elseif ($import_mode == 'global_option') {
                    $combination = array();
                    foreach($offer -> $cml['product_features'] -> $cml['product_feature'] as $feature_data) {
                        if (isset($global_options_data[strval($feature_data -> $cml['name'])])) {
                            if (!in_array(strval($feature_data -> $cml['value']), $global_options_data[strval($feature_data -> $cml['name'])]['variants'])) { 
                                $global_options_data[strval($feature_data -> $cml['name'])]['variants'][] = strval($feature_data -> $cml['value']);
                            }
                            if (!in_array($product_id, $global_options_data[strval($feature_data -> $cml['name'])]['product_ids'])) {
                                $global_options_data[strval($feature_data -> $cml['name'])]['product_ids'][] = $product_id;
                            }
                        } else {
                            $global_options_data[strval($feature_data -> $cml['name'])]['variants'][] = strval($feature_data -> $cml['value']);
                            $global_options_data[strval($feature_data -> $cml['name'])]['product_ids'][] = $product_id;
                        }
                        
                        
                        $combination[] = array(
                            'option_name' => strval($feature_data -> $cml['name']),
                            'variant_name' => strval($feature_data -> $cml['value']),
                        );
                    }
                    $options_data[$product_id][] = array(
                        'combination' => $combination,
                        'amount' => $amount,
                        'combination_guid' => $combination_guid,
                    );
                } elseif ($import_mode == 'individual_option') {
                    $combination = array();
                    foreach($offer -> $cml['product_features'] -> $cml['product_feature'] as $feature_data) {
                        $combination[] = array(
                            'option_name' => strval($feature_data -> $cml['name']),
                            'variant_name' => strval($feature_data -> $cml['value']),
                        );
                    }
                    $options_data[$product_id][] = array(
                        'combination' => $combination,
                        'amount' => $amount,
                        'combination_guid' => $combination_guid,
                    );
                }
            }
        }

        if ($schema_version == '2.07') {
            return;
        }

        if ($import_mode == 'standart') {
            foreach ($options_data as $pid => $variants_data) {
                $option_id = db_get_field("SELECT option_id FROM ?:product_options WHERE product_id = ?i", $pid);
                $option_id = (empty($option_id)) ? 0 : $option_id;
                $option_data = fn_exim_1c_create_option_structure($pid, $standart_option_name, $company_id, $type_option);
                foreach ($variants_data as $variant_data) {
                    $variant_id = db_get_field("SELECT variants.variant_id FROM ?:product_option_variants AS variants "
                    . "LEFT JOIN ?:product_option_variants_descriptions AS variants_descriptions ON variants.variant_id = variants_descriptions.variant_id "
                    . "WHERE variants.option_id = ?i AND variants_descriptions.lang_code = ?s AND variants_descriptions.variant_name = ?s", $option_id, $lang_code, $variant_data['variant_name']);
                    $variant_id = (empty($variant_id)) ? 0 : $variant_id;
                    $option_data['variants'][] = array(
                        'variant_name' => $variant_data['variant_name'],
                        'variant_id' => $variant_id,
                        'modifier_type' => 'A',
                        'modifier' => $variant_data['price'],
                        'weight_modifier' => 0,
                        'weight_modifier_type' => 'A',
                        'external_id' => $variant_data['combination_guid'],
                    );
                }
                $option_id = fn_update_product_option($option_data, $option_id, $lang_code);
                
                $empty_stock = true;
                foreach ($variants_data as $variant_data) {
                    $variant_id = db_get_field("SELECT variant_id FROM ?:product_option_variants WHERE external_id = ?s AND option_id = ?i", $variant_data['combination_guid'], $option_id);
                    $old_combination_hash = db_get_field("SELECT combination_hash FROM ?:product_options_inventory WHERE external_id = ?s", $variant_data['combination_guid']);
                    $image_pair_id = db_get_field("SELECT pair_id FROM ?:images_links WHERE object_id = ?i", $old_combination_hash);
                    db_query("DELETE FROM ?:product_options_inventory WHERE external_id = ?s AND product_id = ?i", $variant_data['combination_guid'], $pid);
                    $combination_data = array(
                        'product_id' => $pid,
                        'combination_hash' => fn_generate_cart_id($pid, array('product_options' => array($option_id => $variant_id))),
                        'combination' => fn_get_options_combination(array($option_id => $variant_id)),
                        'amount' => $variant_data['amount'],
                        'external_id' => $variant_data['combination_guid']
                    );
                    $variant_combination = db_get_field("SELECT combination_hash FROM ?:product_options_inventory WHERE combination_hash = ?i", $combination_data['combination_hash']);
                    if (empty($variant_combination)) {
                        db_query("INSERT INTO ?:product_options_inventory ?e", $combination_data);
                    }
                    if (!empty($image_pair_id)) {
                        db_query("UPDATE ?:images_links SET object_id = ?i WHERE pair_id = ?i", $combination_data['combination_hash'], $image_pair_id);
                    }
                    if ($variant_data['amount'] > 0) {
                        $empty_stock = false;
                    }
                } 
                // To hide products if they are out of stock
                if ($hide_product == 'Y') {
                    if ($empty_stock) {
                        db_query("UPDATE ?:products SET status = 'H' WHERE product_id = ?i", $pid);
                    }
                }
            }
        } elseif ($import_mode == 'global_option') {
            foreach ($global_options_data as $option => &$global_option_data) {
                $option_id = db_get_field("SELECT options.option_id FROM ?:product_options AS options "
                . "LEFT JOIN ?:product_options_descriptions AS options_descriptions ON options.option_id = options_descriptions.option_id " 
                . "WHERE options.product_id = 0 AND options_descriptions.option_name = ?s", $option);
                $option_id = (empty($option_id))? 0: $option_id;                
                $option_data = fn_exim_1c_create_option_structure(0, $option, $company_id, $type_option);
                if ($option_id != 0) {
                    $old_variants = db_get_fields("SELECT variants_descriptions.variant_name FROM ?:product_option_variants AS variants "
                    . "LEFT JOIN ?:product_option_variants_descriptions AS variants_descriptions ON variants.variant_id = variants_descriptions.variant_id "
                    . "WHERE variants.option_id = ?i AND variants_descriptions.lang_code = ?s", $option_id, $lang_code);
                    $old_variants = array_diff($old_variants, $global_option_data['variants']);
                    $global_option_data['variants'] = fn_array_merge($global_option_data['variants'], $old_variants);
                }
                foreach ($global_option_data['variants'] as $variant) {
                    $variant_id = db_get_field("SELECT variants.variant_id FROM ?:product_option_variants AS variants "
                    . "LEFT JOIN ?:product_option_variants_descriptions AS variants_descriptions ON variants.variant_id = variants_descriptions.variant_id "
                    . "WHERE variants.option_id = ?i AND variants_descriptions.lang_code = ?s AND variants_descriptions.variant_name = ?s", $option_id, $lang_code, $variant);
                    $variant_id = (empty($variant_id)) ? 0 : $variant_id;
                    $option_data['variants'][] = array(
                        'variant_name' => $variant,
                        'variant_id' => $variant_id,
                        'modifier_type' => 'A',
                        'modifier' => 0,
                        'weight_modifier' => 0,
                        'weight_modifier_type' => 'A'
                    );
                }
                $option_id = fn_update_product_option($option_data, $option_id, $lang_code);
                $global_option_data['option_id'] = $option_id;
                foreach ($global_option_data['product_ids'] as $product_id) {
                    db_query("REPLACE INTO ?:product_global_option_links ?e", array(
                        'option_id' => $option_id,
                        'product_id' => $product_id
                    ));
                }
            } 
            foreach ($options_data as $pid => $combinations) {
                foreach ($combinations as $_combination_data) {
                    $add_options_combination = array();
                    foreach ($_combination_data['combination'] as $combination) {
                        $option_id = $global_options_data[$combination['option_name']]['option_id'];
                        $variant_id = db_get_field("SELECT variants_descriptions.variant_id FROM ?:product_option_variants AS variants "
                        . "LEFT JOIN ?:product_option_variants_descriptions AS variants_descriptions ON variants.variant_id = variants_descriptions.variant_id "
                        . "WHERE lang_code = ?s AND option_id = ?i AND variant_name = ?s", $lang_code, $option_id, $combination['variant_name']);
                        $add_options_combination[$option_id] = $variant_id;
                    }
                    $old_combination_hash = db_get_field("SELECT combination_hash FROM ?:product_options_inventory WHERE external_id = ?s", $_combination_data['combination_guid']);
                    $image_pair_id = db_get_field("SELECT pair_id FROM ?:images_links WHERE object_id = ?i", $old_combination_hash);
                    $empty_stock = true;
                    db_query("DELETE FROM ?:product_options_inventory WHERE external_id = ?s AND product_id = ?i", $_combination_data['combination_guid'], $pid);               
                    $combination_data = array(
                        'product_id' => $pid,
                        'combination_hash' => fn_generate_cart_id($pid, array('product_options' => $add_options_combination)),
                        'combination' => fn_get_options_combination($add_options_combination),
                        'amount' => $_combination_data['amount'],
                        'external_id' => $_combination_data['combination_guid']
                    );
                    db_query("INSERT INTO ?:product_options_inventory ?e", $combination_data);
                    if (!empty($image_pair_id)) {
                        db_query("UPDATE ?:images_links SET object_id = ?i WHERE pair_id = ?i", $combination_data['combination_hash'], $image_pair_id);
                    }
                    if ($combination_data['amount'] > 0) {
                        $empty_stock = false;
                    }
                }
                // To hide products if they are out of stock
                if ($hide_product == 'Y') {
                    if ($empty_stock) {
                        db_query("UPDATE ?:products SET status = 'H' WHERE product_id = ?i", $pid);
                    }
                }
            }           
        } elseif ($import_mode == 'individual_option') {
            foreach ($options_data as $pid => $combinations) {
                foreach ($combinations as $_combination_data) {
                    $add_options_combination = array();
                    foreach ($_combination_data['combination'] as $combination) {
                        $option_id = db_get_field("SELECT options.option_id FROM ?:product_options AS options "
                        . "LEFT JOIN ?:product_options_descriptions AS options_descriptions ON options.option_id = options_descriptions.option_id "
                        . "WHERE options_descriptions.lang_code = ?s AND options_descriptions.option_name = ?s AND options.product_id = ?i", $lang_code, $combination['option_name'], $pid);
                        $option_id = (empty($option_id)) ? 0 : $option_id;
                        
                        $option_data = fn_exim_1c_create_option_structure($pid, $combination['option_name'], $company_id, $type_option);
                        $option_id = fn_update_product_option($option_data, $option_id, $lang_code);

                        $variant_id = db_get_field("SELECT variants_descriptions.variant_id FROM ?:product_option_variants AS variants "
                        . "LEFT JOIN ?:product_option_variants_descriptions AS variants_descriptions ON variants.variant_id = variants_descriptions.variant_id "
                        . "WHERE variants_descriptions.lang_code = ?s AND variants.option_id = ?i AND variants_descriptions.variant_name = ?s", $lang_code, $option_id, $combination['variant_name']);
                        if (empty($variant_id)) {                            
                            $variant = array(
                                'option_id' => $option_id,
                                'modifier_type' => 'A',
                                'modifier' => 0,
                                'weight_modifier' => 0,
                                'weight_modifier_type' => 'A'
                            );
                            $variant_id = db_query("INSERT INTO ?:product_option_variants ?e", $variant);
                            $variant = array(
                                'variant_id' => $variant_id,
                                'variant_name' => $combination['variant_name'],
                                'lang_code' => $lang_code,
                            );
                            db_query("INSERT INTO ?:product_option_variants_descriptions ?e", $variant);    
                        }
                        $add_options_combination[$option_id] = $variant_id;
                    }
                    $old_combination_hash = db_get_field("SELECT combination_hash FROM ?:product_options_inventory WHERE external_id = ?s", $_combination_data['combination_guid']);
                    $image_pair_id = db_get_field("SELECT pair_id FROM ?:images_links WHERE object_id = ?i", $old_combination_hash);
                    $empty_stock = true;
                    db_query("DELETE FROM ?:product_options_inventory WHERE external_id = ?s AND product_id = ?i", $_combination_data['combination_guid'], $pid);               
                    $combination_data = array(
                        'product_id' => $pid,
                        'combination_hash' => fn_generate_cart_id($pid, array('product_options' => $add_options_combination)),
                        'combination' => fn_get_options_combination($add_options_combination),
                        'amount' => $_combination_data['amount'],
                        'external_id' => $_combination_data['combination_guid']
                    );
                    db_query("INSERT INTO ?:product_options_inventory ?e", $combination_data);
                    if (!empty($image_pair_id)) {
                        db_query("UPDATE ?:images_links SET object_id = ?i WHERE pair_id = ?i", $combination_data['combination_hash'], $image_pair_id);
                    }
                    if ($combination_data['amount'] > 0) {
                        $empty_stock = false;
                    }
                }
                // To hide products if they are out of stock
                if ($hide_product == 'Y') {
                    if ($empty_stock) {
                        db_query("UPDATE ?:products SET status = 'H' WHERE product_id = ?i", $pid);
                    }
                }
            }
        }
    }
}

function fn_exim_1c_create_option_structure($product_id, $option_name, $company_id, $type_option)
{
    return array(
        'product_id' => $product_id,
        'option_name' => $option_name,
        'company_id' => $company_id,
        'option_type' => $type_option,
        'required' => 'N',
        'inventory' => 'Y',
        'multiupload' => 'M',
    );
}

function fn_exim_1c_add_price($prices, $product_id)
{
    if (!empty($prices)) {
        if (isset($prices['base_price'])) {
            $price = array(
                'product_id' => $product_id,
                'price' => $prices['base_price'],
                'lower_limit' => 1,
            );   
            db_query("DELETE FROM ?:product_prices WHERE lower_limit = 1 AND usergroup_id = 0 AND product_id = ?i", $product_id);
            db_query("INSERT INTO ?:product_prices ?e", $price); 
        }
        if (isset($prices['list_price'])) {
            db_query("UPDATE ?:products SET list_price = ?i WHERE product_id = ?i", $prices['list_price'], $product_id);
        }
        if (isset($prices['qty_prices'])) {
            foreach ($prices['qty_prices'] as $_price) {
                $price = array(
                    'product_id' => $product_id,
                    'price' => $_price['price'],
                    'lower_limit' => 1,
                    'usergroup_id' => $_price['usergroup_id'],
                );
                db_query("DELETE FROM ?:product_prices WHERE lower_limit = 1 AND usergroup_id = ?i AND product_id = ?i", $_price['usergroup_id'], $product_id);
                db_query("INSERT INTO ?:product_prices ?e", $price);
            }
        }
        
    }
}

function fn_settings_variants_addons_rus_exim_1c_exim_1c_order_statuses()
{
    $order_statuses = array();
    $statuses = db_get_array("SELECT status, description FROM ?:status_descriptions WHERE type = 'O' AND lang_code = ?s", CART_LANGUAGE);
    foreach ($statuses as $key => $val) {
        $order_statuses[$val['status']] = $val['description'];
    }
    
    return $order_statuses;
}

function fn_settings_variants_addons_rus_exim_1c_exim_1c_default_category()
{
    $categories_tree = array();
    $categories = fn_get_plain_categories_tree(0, false);       
    foreach ($categories as $key => $category_data) {
        if (isset($category_data['level'])) {
            $indent = '';
            for($i = 0; $i < $category_data['level']; $i++) {
                $indent = $indent . "¦__";
            }
            $categories_tree[$category_data['category_id']] = $indent.$category_data['category'];
        }
    }
    
    return $categories_tree;
}

function fn_exim_1c_check_prices($xml)
{
    $cml = fn_get_cml_tag_names();
    $t_prices = db_get_array("SELECT price_1c, usergroup_id, type FROM ?:rus_exim_1c_prices");
    $t_prices_1c = array();
    if (isset($xml->$cml['offers']->$cml['offer'])) {
        if (isset($xml->$cml['prices_types']->$cml['price_type'])) {
            foreach ($xml->$cml['prices_types']->$cml['price_type'] as $_prices) { 
                $t_prices_1c[] = array(
                    'price_1c' => trim(strval($_prices->$cml['name'])),
                    'external_id' => trim(strval($_prices->$cml['id']))
                );
            }
        }
    }    
    if (!empty($t_prices_1c)) {
        foreach ($t_prices as $k => &$t_price) {
            $valid = false;
            foreach ($t_prices_1c as $kk => $t_price_1c) {
                if ($t_price['price_1c'] == $t_price_1c['price_1c']) {
                    $valid = true;
                    break;
                }
            }
            $t_price['valid'] = $valid;
        }
    }
    
    return $t_prices;
}

function fn_settings_variants_addons_rus_exim_1c_exim_1c_lang()
{
    $langs = array();
    $_langs = db_get_array("SELECT lang_code, name FROM ?:languages");       
    foreach ($_langs as $_lang) {
        $langs[$_lang['lang_code']] = $_lang['name'];
    }

    return $langs;
}

function fn_rus_exim_1c_get_information()
{
    $storefront_url = Registry::get('config.http_location');
    if (fn_allowed_for('ULTIMATE')) {
        if (Registry::get('runtime.company_id') || Registry::get('runtime.simple_ultimate')) {
            $company = Registry::get('runtime.company_data');
            $storefront_url = 'http://' . $company['storefront'];
        } else {
            $storefront_url = '';
        }
    }
    
    if (!empty($storefront_url)) {
        $exim_1c_info = __('exim_1c_information', array(
            '[http_location]' => $storefront_url . '/' . 'exim_1c',
        ));
    } else {
        $exim_1c_info = '';
    }

    return $exim_1c_info;
}

function fn_rus_exim_1c_get_information_shipping_features()
{
    $exim_1c_info_features = __('exim_1c_information_shipping_features');

    return $exim_1c_info_features;
}

function fn_exim_1c_export_orders($company_id, $lang_code)
{
    $cml = fn_get_cml_tag_names();
    $params = array(
        'company_name' => true,
        'place' => 'exim_1c',
        'company_id' => $company_id,
    );

    $statuses = Registry::get('addons.rus_exim_1c.exim_1c_order_statuses');
    if (!empty($statuses)) {
        foreach($statuses as $key => $status) {
            if (!empty($status)) {
                $params['status'][] = $key;
            }
        }
    }
  
    list($orders, $search) = fn_get_orders($params);
    header("Content-type: text/xml; charset=utf-8");
    fn_echo("\xEF\xBB\xBF");    
    $xml = new XMLWriter();
    $xml -> openMemory();
    $xml -> startDocument();
    $xml -> startElement($cml['commerce_information']);    
    foreach ($orders as $k => $data) {
        $order_data = fn_get_order_info($data['order_id']);
        $xml = fn_exim_1c_echo_order_xml($xml, $order_data, $lang_code);
    }
    $xml -> endElement();
    fn_echo($xml -> outputMemory());
}

function fn_exim_1c_echo_order_xml($xml, $order_data, $lang_code)
{
    $currency = (!empty($order_data['secondary_currency'])) ? $order_data['secondary_currency'] : CART_PRIMARY_CURRENCY;
    $payment = (!empty($order_data['payment_method']['payment'])) ? $order_data['payment_method']['payment'] : "-";
    $cml = fn_get_cml_tag_names();
    $xml -> startElement($cml['document']);
        $xml -> writeElement($cml['id'], $order_data['order_id']);
        $xml -> writeElement($cml['number'], $order_data['order_id']);
        $xml -> writeElement($cml['date'], date('Y-m-d', $order_data['timestamp']));
        $xml -> writeElement($cml['operation'], $cml['order']);
        $xml -> writeElement($cml['role'], $cml['seller']);
        $xml -> writeElement($cml['currency'], $currency);
        $xml -> writeElement($cml['total'], $order_data['total']);
        $xml = fn_exim_1c_build_customer_info($xml, $order_data);
        $xml -> writeElement($cml['time'], date('H:i:s', $order_data['timestamp']));
        $xml -> writeElement($cml['notes'], $order_data['notes']);
        $xml = fn_exim_1c_build_order_products($xml, $order_data);
        $status = db_get_field("SELECT description FROM ?:status_descriptions WHERE type = 'O' AND status = ?s AND lang_code = ?s", $order_data['status'], $lang_code);
        $xml -> startElement($cml['value_fields']);
            $xml -> startElement($cml['value_field']);
                $xml -> writeElement($cml['name'], $cml['status_order']);
                $xml -> writeElement($cml['value'], $status);
            $xml -> endElement();
            $xml -> startElement($cml['value_field']);
                $xml -> writeElement($cml['name'], $cml['payment']);
                $xml -> writeElement($cml['value'], $payment);
            $xml -> endElement();
        $xml -> endElement();
    $xml -> endElement();
    
    return $xml;
    
}

function fn_exim_1c_build_customer_info($xml, $order_data)
{
    $zipcode = (!empty($order_data['b_zipcode'])) ? $order_data['b_zipcode'] : "-";
    $country = (!empty($order_data['b_country_descr'])) ? $order_data['b_country_descr'] : "-";
    $b_city = (!empty($order_data['b_city'])) ? trim($order_data['b_city']) : "-";
    $city = (!empty($b_city)) ? $b_city : "-";
    $address1 = (!empty($order_data['b_address'])) ? $order_data['b_address'] : "-";
    $address2 = (!empty($order_data['b_address_2'])) ? $order_data['b_address_2'] : "-";
    $cml = fn_get_cml_tag_names();
    $xml -> startElement($cml['contractors']);
        $xml -> startElement($cml['contractor']);
            $xml -> writeElement($cml['id'], $order_data['user_id']);
            $xml -> writeElement($cml['unregistered'], ($order_data['user_id'] == 0) ? $cml['yes'] : $cml['no']);
            $xml -> writeElement($cml['name'], (!empty($order_data['company'])) ? $order_data['company'] : $order_data['lastname'] . ' ' . $order_data['firstname']);
            $xml -> writeElement($cml['role'], $cml['seller']);
            $xml -> writeElement($cml['full_name_contractor'], $order_data['lastname'] . ' ' . $order_data['firstname']);
            $xml -> writeElement($cml['lastname'], $order_data['lastname']);
            $xml -> writeElement($cml['firstname'], $order_data['firstname']);
            $xml -> startElement($cml['address']);
                $xml -> writeElement($cml['presentation'], "$zipcode, $country, $city, $address1 $address2");
                $xml -> startElement($cml['address_field']);
                    $xml -> writeElement($cml['type'], $cml['post_code']);
                    $xml -> writeElement($cml['value'], $zipcode);
                $xml -> endElement();
                $xml -> startElement($cml['address_field']);
                    $xml -> writeElement($cml['type'], $cml['country']);
                    $xml -> writeElement($cml['value'], $country);
                $xml -> endElement();
                $xml -> startElement($cml['address_field']);
                    $xml -> writeElement($cml['type'], $cml['city']);
                    $xml -> writeElement($cml['value'], $city);
                $xml -> endElement();
                $xml -> startElement($cml['address_field']);
                    $xml -> writeElement($cml['type'], $cml['address']);
                    $xml -> writeElement($cml['value'], "$address1 $address2");
                $xml -> endElement();
            $xml -> endElement();
            $xml -> startElement($cml['contacts']);
                $xml -> startElement($cml['contact']);
                    $xml -> writeElement($cml['type'], $cml['mail']);
                    $xml -> writeElement($cml['value'], $order_data['email']);
                $xml -> endElement();
                $xml -> startElement($cml['contact']);
                    $xml -> writeElement($cml['type'], $cml['work_phone']);
                    $xml -> writeElement($cml['value'], (empty($order_data['phone'])) ? '-' : $order_data['phone']);
                $xml -> endElement();
            $xml -> endElement();
        $xml -> endElement();
    $xml -> endElement();
    
    return $xml;
}

function fn_exim_1c_build_order_products($xml, $order_data)
{
    $cml = fn_get_cml_tag_names();
    $xml -> startElement($cml['products']);
    $schema_version = Registry::get('addons.rus_exim_1c.exim_1c_schema_version');
    if (Registry::get('addons.rus_exim_1c.exim_1c_order_shipping') == 'Y' && $order_data['shipping_cost'] > 0) {
        $xml -> startElement($cml['product']);
            $xml -> writeElement($cml['id'], 'ORDER_DELIVERY');
            $xml -> writeElement($cml['name'], $cml['delivery_order']);
            $xml -> writeElement($cml['price_per_item'], $order_data['shipping_cost']);
            $xml -> writeElement($cml['amount'], 1);
            $xml -> writeElement($cml['total'], $order_data['shipping_cost']);
            $xml -> startElement($cml['value_fields']);
                $xml -> startElement($cml['value_field']);
                    $xml -> writeElement($cml['name'], $cml['spec_nomenclature']);
                    $xml -> writeElement($cml['value'], $cml['service']);
                $xml -> endElement();
                $xml -> startElement($cml['value_field']);
                    $xml -> writeElement($cml['name'], $cml['type_nomenclature']);
                    $xml -> writeElement($cml['value'], $cml['service']);
                $xml -> endElement();
            $xml -> endElement();
        $xml -> endElement();
    }
    foreach ($order_data['products'] as $product) {
        $external_id = db_get_field("SELECT external_id FROM ?:products WHERE product_id = ?i", $product['product_id']);
        $xml -> startElement($cml['product']);
            $xml -> writeElement($cml['id'], $external_id);
            $xml -> writeElement($cml['code'], $product['product_id']);
            $xml -> writeElement($cml['article'], $product['product_code']);
            $xml -> writeElement($cml['name'], $product['product']);
            if ($schema_version == '2.07') {
                $xml -> startElement($cml['base_unit']);
                    $xml->writeAttribute($cml['code'], '796');
                    $xml->writeAttribute($cml['full_name_unit'], $cml['item']);
                    $xml->text($cml['item']);
                $xml -> endElement();
            } else {
                $xml -> writeElement($cml['base_unit'], $cml['item']);
            }
            $xml -> startElement($cml['discounts']);
                $xml -> startElement($cml['discount']);
                    $xml -> writeElement($cml['name'], $cml['product_discount']);
                    $xml -> writeElement($cml['total'], $product['discount']);
                    $xml -> writeElement($cml['in_total'], 'true');
                $xml -> endElement();
            $xml -> endElement();
            $xml -> writeElement($cml['price_per_item'], $product['base_price']);
            $xml -> writeElement($cml['amount'], $product['amount']);
            $xml -> writeElement($cml['total'], $product['subtotal']);
            $xml -> startElement($cml['value_fields']);
                $xml -> startElement($cml['value_field']);
                    $xml -> writeElement($cml['name'], $cml['spec_nomenclature']);
                    $xml -> writeElement($cml['value'], $cml['product']);
                $xml -> endElement();
                $xml -> startElement($cml['value_field']);
                    $xml -> writeElement($cml['name'], $cml['type_nomenclature']);
                    $xml -> writeElement($cml['value'], $cml['product']);
                $xml -> endElement();
            $xml -> endElement();
        $xml -> endElement();
    }
    $xml -> endElement();
    
    return $xml;
}

function fn_exim_1c_auth_error($msg)
{
    header('WWW-Authenticate: Basic realm="Authorization required"');
    header('HTTP/1.0 401 Unauthorized');
    fn_echo($msg);
}

function fn_exim_1c_checkauth()
{
    fn_echo("success\n");
    fn_echo(COOKIE_1C . "\n");
    fn_echo(uniqid());
}

function fn_exim_1c_init()
{
    fn_echo("zip=no\n");
    fn_echo('file_limit=' . FILE_LIMIT . "\n");
}

function fn_exim_1c_get_external_file($filename)
{
    list($dir_1c, $dir_1c_url, $dir_1c_images) = fn_rus_exim_1c_get_dir_1c();
    if (!is_dir($dir_1c)) {
        fn_mkdir($dir_1c);
    }
    $file_path = $dir_1c . $filename;

    if (Registry::get('addons.rus_exim_1c.exim_1c_schema_version') == '2.07') {
        if (file_exists($file_path) &&  extension_loaded('XMLReader')) {
            $xml = new XMLReader();
            $xml->open($file_path);
            $xml->setParserProperty(XMLReader::VALIDATE, true);
            if (!$xml->isValid()) {
                @unlink($file_path);
            }
        }
    }

    if (fn_exim_1c_file_is_image($filename)) {
        if (!is_dir($dir_1c_images)) {
            fn_mkdir($dir_1c_images);
        }
        $file_path = $dir_1c_images . $filename;    
    }

    $file = @fopen($file_path, 'w');
    if (!$file) {
        return false;
    }
    fwrite($file, fn_get_contents('php://input'));
    fclose($file);
    
    return true;
}

function fn_exim_1c_file_is_image($filename)
{
    $file_array = fn_explode('.', $filename);
    if (is_array($file_array)) {
        $type = array_pop($file_array);
        if (in_array($type, array('jpg', 'jpeg', 'png', 'gif'))) {
            return true;
        }
    }

    return false;
}

function fn_exim_1c_file_is_file($filename)
{
    $file_array = fn_explode('.', $filename);
    if (is_array($file_array)) {
        $type = array_pop($file_array);
        if (in_array($type, array('txt', 'html'))) {
            return true;
        }
    }

    return false;
}

function fn_rus_exim_1c_get_dir_1c()
{
    $dir_1c = fn_get_files_dir_path() . 'exim/1C_' . date('dmY') . '/';
    $dir_1c_url = Registry::get('config.http_location') . '/' . fn_get_rel_dir($dir_1c);
    $dir_1c_images = Storage::instance('images')->getAbsolutePath('from_1c/');
    
    return array($dir_1c, $dir_1c_url, $dir_1c_images);
}

function fn_exim_1c_clear_1c_dir()
{
    list($dir_1c, $dir_1c_url, $dir_1c_images) = fn_rus_exim_1c_get_dir_1c();
    if(is_dir($dir_1c)) {
        $dirHandle = opendir($dir_1c);
        while (false !== ($file = readdir($dirHandle))) {
            if ($file!='.' && $file!='..') {
                $tmp_path=$dir_1c.'/'.$file;
                chmod($tmp_path, 0777);
                if (is_dir($tmp_path)) {
                    fn_exim_1c_clear_1c_dir($tmp_path);
                } else { 
                    if(file_exists($tmp_path)) {
                        unlink($tmp_path);
                    }
                }
            }
        }
        closedir($dirHandle);
    }
    
    return true;
}

function fn_exim_1c_get_xml($filename)
{
    list($dir_1c, $dir_1c_url, $dir_1c_images) = fn_rus_exim_1c_get_dir_1c();
    
    return @simplexml_load_file($dir_1c . $filename);
}

function fn_exim_1c_xml_to_array($xml)
{
    return json_decode(json_encode($xml), true);
}

/**
* Gets tag name by its identity key in the scheme. The tag names are written in Russian.
*/ 
function fn_get_cml_tag_names()
{
    return fn_get_schema('cml_fields', 'fields_names');
}

function fn_exim_1c_get_shipping_params()
{
    return array(
        array(
            'name' => 'weight_property',
            'fields' => fn_explode("\n", Registry::get('addons.rus_exim_1c.exim_1c_weight_property')),
            'display' => Registry::get('addons.rus_exim_1c.exim_1c_display_weight'),
        ),
        array(
            'name' => 'free_shipping',
            'fields' => fn_explode("\n", Registry::get('addons.rus_exim_1c.exim_1c_free_shipping')),
            'display' => Registry::get('addons.rus_exim_1c.exim_1c_display_free_shipping'),
        ),
        array(
            'name' => 'shipping_cost',
            'fields' => fn_explode("\n", Registry::get('addons.rus_exim_1c.exim_1c_shipping_cost')),
            'display' => '',
        ),
        array(
            'name' => 'number_of_items',
            'fields' => fn_explode("\n", Registry::get('addons.rus_exim_1c.exim_1c_number_of_items')),
            'display' => '',
        ),
        array(
            'name' => 'box_length',
            'fields' => fn_explode("\n", Registry::get('addons.rus_exim_1c.exim_1c_box_length')),
            'display' => '',
        ),
        array(
            'name' => 'box_width',
            'fields' => fn_explode("\n", Registry::get('addons.rus_exim_1c.exim_1c_box_width')),
            'display' => '',
        ),
        array(
            'name' => 'box_height',
            'fields' => fn_explode("\n", Registry::get('addons.rus_exim_1c.exim_1c_box_height')),
            'display' => '',
        ),
    );
}

//
// hooks
//

function fn_rus_exim_1c_get_orders($params, $fields, $sortings, &$condition, $join, $group)
{
    $number_for_orders = trim(Registry::get('addons.rus_exim_1c.exim_1c_from_order_id'));
    if (isset($params['place'])) {
        if (!empty($number_for_orders)) {
            $order_id = Registry::get('addons.rus_exim_1c.exim_1c_from_order_id');
            if (!empty($order_id)) {
                $condition .= db_quote(" AND ?:orders.order_id >= ?i", $order_id);
            }
        }
    }
}

function fn_rus_exim_1c_allowed_access($user_data)
{
    if (empty($user_data['usergroups'])) {
        return true;
    }

    foreach ($user_data['usergroups'] as $usergroup) {
        $privilege = db_get_field("SELECT privilege FROM ?:usergroup_privileges WHERE usergroup_id = ?i AND privilege = 'exim_1c'", $usergroup['usergroup_id']);

        if ((!empty($privilege)) && ($usergroup['status'] == 'A')) {
            return true;
        }
    }
    
    return false;
}
