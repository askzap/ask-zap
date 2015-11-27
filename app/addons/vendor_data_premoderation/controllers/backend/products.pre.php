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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (Registry::get('runtime.company_id')) {
        $company_data = Registry::get('runtime.company_data');
        $products_prior_approval = Registry::get('addons.vendor_data_premoderation.products_prior_approval');
        $products_updates_approval = Registry::get('addons.vendor_data_premoderation.products_updates_approval');

        if ($mode == 'update') {
            // We update existing product
            if (!empty($_REQUEST['product_id'])) {
                // Pre-moderation is required for current vendor
                if ($products_updates_approval == 'all'
                    || ($products_updates_approval == 'custom' && $company_data['pre_moderation_edit'] == 'Y')
                ) {
                    // Set approvement flag to "Pending"
                    $_REQUEST['product_data']['approved'] = $_POST['product_data']['approved'] = 'P';
                } else {
                    unset($_REQUEST['product_data']['approved'], $_POST['product_data']['approved']);
                }
            }
            // We create a new product
            else {
                // Pre-moderation is required for current vendor
                if ($products_prior_approval == 'all'
                    || ($products_prior_approval == 'custom' && $company_data['pre_moderation'] == 'Y')
                ) {
                    // Set approvement flag to "Pending"
                    $_REQUEST['product_data']['approved'] = $_POST['product_data']['approved'] = 'P';
                }
            }
        } elseif ($mode == 'm_update' && !empty($_REQUEST['products_data'])) {
            if ($products_updates_approval == 'all'
                || ($products_updates_approval == 'custom' && $company_data['pre_moderation_edit'] == 'Y')
            ) {
                foreach ($_REQUEST['products_data'] as $key => $data) {
                    $_REQUEST['products_data'][$key]['approved'] = $_POST['products_data'][$key]['approved'] = 'P';
                }
            } else {
                foreach ($_REQUEST['products_data'] as $key => $data) {
                    unset($_REQUEST['products_data'][$key]['approved'], $_POST['products_data'][$key]['approved']);
                }
            }
        } elseif ($mode == 'm_add' && !empty($_REQUEST['products_data'])) {
            if ($products_prior_approval == 'all'
                || ($products_prior_approval == 'custom' && $company_data['pre_moderation'] == 'Y')
            ) {
                foreach ($_REQUEST['products_data'] as $key => $data) {
                    $_REQUEST['products_data'][$key]['approved'] = $_POST['products_data'][$key]['approved'] = 'P';
                }
            }
        } elseif ($mode == 'update_file' && !empty($_REQUEST['product_id'])) {
            if ($products_updates_approval == 'all'
                || ($products_updates_approval == 'custom' && $company_data['pre_moderation_edit'] == 'Y')
            ) {
                fn_change_approval_status($_REQUEST['product_id'], 'P');
            }
        }
    }
}
