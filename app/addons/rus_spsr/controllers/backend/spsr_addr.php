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

use Tygh\RusSpsr;
use Tygh\Registry;
use Tygh\Navigation\LastView;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $params = $_REQUEST;

    if ($mode == 'm_delete') {
        if (!empty($params['addr_ids'])) {
            foreach ($_REQUEST['addr_ids'] as $a) {
                $a = explode('||', $a);
                $id = $a[0];
                $owner_id = $a[1];
                fn_spsr_delete_addr($id , $owner_id);
            }
        }

        $suffix = ".manage";

    } elseif ($mode == 'update') {
        if (!empty($params['create_address'])) {
            RusSpsr::WALogin();

            $address = $params['create_address'];
            $city_data = RusSpsr::WAGetCities(array('city' => $address['city_name']));
            $address['city_id'] = $city_data['City_ID'];
            $address['city_owner_id'] = $city_data['City_owner_ID'];

            $result = RusSpsr::WAAddAddress($address , 8);
            if ($result) {
                fn_set_notification('N', __('notice'), __('shippings.spsr.address_add'));
                if (!empty(RusSpsr::$last_error)) {
                    fn_set_notification('N', __('notice'), RusSpsr::$last_error);
                }
            } else {
                fn_set_notification('E', __('notice'), __('shippings.spsr.not_address_add') . ' : ' . RusSpsr::$last_error);
            }

            RusSpsr::WALogout();
            $suffix = ".manage";
        }

    } elseif ($mode == 'delete') {
        if (!empty($_REQUEST['addr_id']) && !empty($_REQUEST['addr_owner_id'])) {
            fn_spsr_delete_addr($_REQUEST['addr_id'], $_REQUEST['addr_owner_id']);
        }

        $suffix = ".manage";
    }

    return array(CONTROLLER_STATUS_OK, "spsr_addr$suffix");
}

if ($mode == 'manage') {
    $spsr_login = RusSpsr::WALogin();
    $addr_list = RusSpsr::WAGetAddrList(8);

    list($addr_list, $search) = fn_get_spsr_address($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'), $addr_list);

    RusSpsr::WALogout();
    Tygh::$app['view']->assign('addr_list', $addr_list);
    Tygh::$app['view']->assign('search', $search);

}

function fn_spsr_delete_addr($addr_id , $addr_owner_id) {
    RusSpsr::WALogin();

    $result = RusSpsr::WADelAddress($addr_id , $addr_owner_id, 8);
    if ($result) {
        fn_set_notification('N', __('notice'), __('shippings.spsr.address_delete'));
    } else {
        fn_set_notification('E', __('notice'), __('shippings.spsr.not_address_delete') . ' : ' . RusSpsr::$last_error);
    }

    RusSpsr::WALogout();

    return $result;
}

function fn_get_spsr_address($params, $items_per_page, $addr_list)
{
    $params = LastView::instance()->update('address', $params);

    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );
    $params = array_merge($default_params, $params);

    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = count($addr_list);
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $limit = str_replace("LIMIT ", "", $limit);
    $offset = explode(",", $limit);

    if (!empty($addr_list)) {
        $addr_list = array_slice($addr_list, (int) $offset[0], (int) $offset[1]);
    }

    return array($addr_list, $params);
}
