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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_vendor_data_premoderation_get_products(&$params, &$fields, &$sortings, &$condition, &$join, &$sorting, &$group_by, &$lang_code)
{
    $sortings['approval'] = 'products.approved';

    // at admin area we allow filtering by approval status
    if (AREA == 'A') {
        if (!empty($params['approval_status']) && $params['approval_status'] != 'all') {
            $condition .= db_quote(' AND products.approved = ?s', $params['approval_status']);
        }
    }
    // at customer area we require product to be approved
    elseif (AREA == 'C') {
        $condition .= db_quote(' AND products.approved = ?s', 'Y');
    }
}

function fn_vendor_data_premoderation_get_preview_url_post(&$uri, &$object_data, &$user_id, &$preview_url)
{
    if ($object_data['status'] == 'A' && fn_allowed_for('MULTIVENDOR') && isset($object_data['approved']) && $object_data['approved'] == 'Y' ) {
        $preview_url = fn_url($uri, 'C', 'http', DESCR_SL);
    }
}

function fn_vendor_data_premoderation_get_product_data(&$product_id, &$field_list, &$join)
{
    if (AREA == 'A') {
        $field_list .= ', companies.pre_moderation as company_pre_moderation';
        $field_list .= ', companies.pre_moderation_edit as company_pre_moderation_edit';
        if (strpos($join, '?:companies') === false) {
            $join .= ' LEFT JOIN ?:companies as companies ON companies.company_id = ?:products.company_id';
        }
    }
}

function fn_vendor_data_premoderation_get_product_data_post(&$product_data, &$auth, &$preview)
{
    if (AREA == 'C' && !$preview && isset($product_data['approved']) && $product_data['approved'] != 'Y') {
        $product_data = array();
    }
}

function fn_vendor_data_premoderation_import_pre_moderation(&$import_data, &$pattern)
{
    if (Registry::get('runtime.company_id') && !empty($import_data)) {
        $company_data = Registry::get('runtime.company_data');
        $products_prior_approval = Registry::get('addons.vendor_data_premoderation.products_prior_approval');
        if ($products_prior_approval == 'all' || ($products_prior_approval == 'custom' && $company_data['pre_moderation'] == 'Y')) {
            foreach ($import_data as $id => &$data) {
                $data['approved'] = 'P';
            }
        }
    }
}

function fn_vendor_data_premoderation_update_company_pre(&$company_data, &$company_id, &$lang_code)
{
    if (fn_allowed_for('MULTIVENDOR') && Registry::get('runtime.company_id')) {

        $orig_company_data = fn_get_company_data($company_id, $lang_code);
        $vendor_profile_updates_approval = Registry::get('addons.vendor_data_premoderation.vendor_profile_updates_approval');

        if ($orig_company_data['status'] == 'A' && ($vendor_profile_updates_approval == 'all' || ($vendor_profile_updates_approval == 'custom' && !empty($orig_company_data['pre_moderation_edit_vendors']) && $orig_company_data['pre_moderation_edit_vendors'] == 'Y'))) {

            $logotypes = fn_filter_uploaded_data('logotypes_image_icon'); // FIXME: dirty comparison

            // check that some data is changed
            if (array_diff_assoc($company_data, $orig_company_data) || !empty($logotypes)) {
                $company_data['status'] = 'P';
            }
        }
    }
}

function fn_vendor_data_premoderation_set_admin_notification(&$auth)
{
    if ($auth['company_id'] == 0 && fn_check_permissions('premoderation', 'products_approval', 'admin')) {
        $count = db_get_field('SELECT COUNT(*) FROM ?:products WHERE approved = ?s', 'P');

        if ($count > 0) {
            fn_set_notification('W', __('notice'), __('text_not_approved_products', array(
                '[link]' => fn_url('premoderation.products_approval?approval_status=P')
            )), 'K');
        }
    }
}

function fn_vendor_data_premoderation_get_filters_products_count_query_params(&$values_fields, &$join, &$sliders_join, &$feature_ids, &$where, &$sliders_where, &$filter_vq, &$filter_rq)
{
    $where .= db_quote(" AND ?:products.approved = ?s", 'Y');
}

function fn_change_approval_status($p_ids, $status)
{
    if (is_array($p_ids)) {
        db_query('UPDATE ?:products SET approved = ?s WHERE product_id IN (?a)', $status, $p_ids);
    } else {
        db_query('UPDATE ?:products SET approved = ?s WHERE product_id = ?i', $status, $p_ids);
    }

    return true;
}

function fn_vendor_data_premoderation_clone_product_post(&$product_id, &$pid, &$orig_name, &$new_name)
{
    if (!empty($pid) && Registry::get('runtime.company_id')) {
        $company_data = Registry::get('runtime.company_data');
        $products_prior_approval = Registry::get('addons.vendor_data_premoderation.products_prior_approval');
        if ($products_prior_approval == 'all' || ($products_prior_approval == 'custom' && $company_data['pre_moderation'] == 'Y')) {
            fn_change_approval_status($pid, 'P');
        }
    }
}
