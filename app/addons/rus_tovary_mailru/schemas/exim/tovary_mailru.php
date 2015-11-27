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

include_once(Registry::get('config.dir.addons') . 'rus_tovary_mailru/schemas/exim/tovary_mailru.functions.php');

$schema = array(
    'section' => 'products',
    'name' => __('mailru'),
    'pattern_id' => 'tovary_mailru',
    'key' => array('product_id'),
    'table' => 'products',
    'export_only' => true,
    'filename' => __('mailru_products_filename') . '_' . date('mdY') . '.xml',
    'func_save_content_to_file' => 'fn_mailru_prepare_offer',
    'condition' => array(
        'use_company_condition' => true,
        'conditions' => array(
            'mailru_export' => 'Y',
            'status' => 'A',
        ),
    ),
    'pre_processing' => array(
        'mailru_put_header' => array(
            'function' => 'fn_mailru_put_header',
            'args' => array('@filename'),
        ),
    ),
    'post_processing' => array(
        'mailru_put_bottom' => array(
            'function' => 'fn_mailru_put_bottom',
            'args' =>  array('@filename'),
        ),
    ),
    'references' => array (
        'product_descriptions' => array (
            'reference_fields' => array ('product_id' => '#key', 'lang_code' => '#lang_code'),
            'join_type' => 'LEFT'
        ),
        'product_prices' => array (
            'reference_fields' => array ('product_id' => '#key', 'lower_limit' => 1, 'usergroup_id' => 0),
            'join_type' => 'LEFT'
        ),
        'images_links' => array (
            'reference_fields' => array('object_id' => '#key', 'object_type' => 'product', 'type' => 'M'),
            'join_type' => 'LEFT'
        ),
		'products_categories' => array (
            'reference_fields' => array ('product_id' => '#key', 'link_type' => 'M'),
            'join_type' => 'LEFT'
        ),
		'category_descriptions' => array (
            'reference_fields' => array ('category_id' => '#products_categories.category_id', 'lang_code' => '#lang_code'),
            'join_type' => 'LEFT'
        ),
    ),
    'range_options' => array (
        'selector_url' => 'products.manage',
        'object_name' => __('products'),
    ),
    'options' => array (
        'lang_code' => array (
            'title' => 'language',
            'type' => 'languages',
            'default_value' => array(DEFAULT_LANGUAGE),
        ),
        'price_dec_sign_delimiter' => array (
            'title' => 'price_dec_sign_delimiter',
            'description' => 'text_price_dec_sign_delimiter',
            'type' => 'input',
            'default_value' => '.'
        ),
    ),
    'override_options' => array (
        'delimiter' => 'T',
    ),
    'export_fields' => array (
        'product_id' => array (
            'db_field' => 'product_id',
            'required' => true,
        ),
        'product_code' => array (
            'db_field' => 'product_code',
            'alt_key' => true,
            'required' => true,
        ),
        'Language' => array(
            'table' => 'product_descriptions',
            'db_field' => 'lang_code',
            'type' => 'languages',
            'required' => true,
            'multilang' => true
        ),
        'product_name' => array (
            'table' => 'product_descriptions',
            'db_field' => 'product',
            'required' => true,
            'multilang' => true
        ),
        'category' => array (
            'process_get' => array ('fn_exim_mailru_get_product_category', '#key', 'M', '#lang_code'),
            'linked' => false, // this field is not linked during import-export
            'required' => true,
            'multilang' => true
        ),
		'category_descriptions' => array (
            'table' => 'category_descriptions',
            'db_field' => 'category',
            'required' => true,
            'multilang' => true
        ),
        'price' => array (
            'table' => 'product_prices',
            'db_field' => 'price',
            'process_get' => array ('fn_exim_mailru_export_price', '#this', '@price_dec_sign_delimiter'),
            'required' => true
        ),
        'status' => array (
            'db_field' => 'status',
            'required' => true
        ),
        'amount' => array (
            'db_field' => 'amount', // count product
            'required' => true
        ),
        'shipping_freight' => array (
            'db_field' => 'shipping_freight',
            'process_get' => array ('fn_exim_mailru_export_price', '#this', '@price_dec_sign_delimiter'),
            'required' => true
        ),
        'free_shipping' => array (
            'db_field' => 'free_shipping',
            'required' => true
        ),
        'product' => array (
            'db_field' => 'product',
            'table' => 'product_descriptions',
            'process_get' => array ('fn_exim_mailru_get_product_info', '#this'),
            'required' => true,
            'multilang' => true,
        ),
        'full_description' => array (
            'db_field' => 'full_description',
            'table' => 'product_descriptions',
            'process_get' => array ('fn_exim_mailru_get_product_info', '#this'),
            'required' => true,
            'multilang' => true,
        ),
        'product_features' => array (
            'process_get' => array ('fn_exim_mailru_get_product_features', '#key', '#lang_code'), // param product
            'linked' => false, // this field is not linked during import-export
            'required' => true,
            'multilang' => true
        ),
        'product_url' => array (
            'process_get' => array ('fn_exim_get_product_url', '#key', '#lang_code'),
            'linked' => false,
            'export_only' => true,
            'required' => true
        ),
        'image_url' => array (
            'process_get' => array ('fn_exim_mailru_get_image_url', '#key', 'product', 'M', false, true, '#lang_code'),
            'db_field' => 'image_id',
            'table' => 'images_links',
            'export_only' => true,
            'required' => true
        ),
        'is_edp' => array(
            'db_field' => 'is_edp',
            'required' => true
        ),
    ),
);
if (fn_allowed_for('ULTIMATE')) {
    $schema['references']['companies'] = array ('reference_fields' => array ('company_id' => '&company_id'), 'join_type' => 'LEFT', 'import_skip_db_processing' => true);
    $schema['export_fields']['company_id'] = array (
        'db_field' => 'company_id',
        'required' => true
    );
}

return $schema;
