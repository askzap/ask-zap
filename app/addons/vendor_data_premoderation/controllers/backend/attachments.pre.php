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

        if ($mode == 'add') {
            $products_prior_approval = Registry::get('addons.vendor_data_premoderation.products_prior_approval');
            if (($products_prior_approval == 'all' || ($products_prior_approval == 'custom' && $company_data['pre_moderation'] == 'Y')) && !empty($_REQUEST['object_id']) && $_REQUEST['object_type'] == 'product') {
                fn_change_approval_status($_REQUEST['object_id'], 'P');
            }
        }

        if ($mode == 'update') {
            $products_updates_approval = Registry::get('addons.vendor_data_premoderation.products_updates_approval');
            if (($products_updates_approval == 'all' || ($products_updates_approval == 'custom' && $company_data['pre_moderation_edit'] == 'Y')) && !empty($_REQUEST['object_id']) && $_REQUEST['object_type'] == 'product') {
                fn_change_approval_status($_REQUEST['object_id'], 'P');
            }
        }
    }
}
