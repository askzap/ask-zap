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
    if ($mode == 'manage') {
        $params = $_REQUEST;

        $post_fix = '';
        if(!empty($params['period'])) {
            $post_fix .= '&period=' . $params['period'];
        }
        if(!empty($params['time_from'])) {
            $post_fix .= '&time_from=' . $params['time_from'];
        }
        if(!empty($params['time_to'])) {
            $post_fix .= '&time_to=' . $params['time_to'];
        }

        $suffix = ".manage" . $post_fix;

    } elseif ($mode == 'm_delete') {
        $params = $_REQUEST['courier_ids'];

        if(!empty($params)) {
            foreach ($params as $c) {
                $c = explode('||', $c);
                $id = $c[0];
                $owner_id = $c[1];
                fn_spsr_delete_courier($id , $owner_id);
            }
        }

        $suffix = ".manage";

    } elseif ($mode == 'update') {
        $params = $_REQUEST['spsr_courier'];

        if(!empty($params)) {
            RusSpsr::WALogin();

            $date = date('Y-m-d' , fn_parse_date($params['necesserydate'])) . 'T00:00:00.000';
            $service = $params['service'];
            $placescount = 1;

            $address = explode('||', $params['sbor_addr']);
            $sboraddr_id = $address[0];
            $sboraddr_owner_id = $address[1];
            $fio = $address[2];
            $city = $address[3];
            $receiver_city = RusSpsr::WAGetCities(array('city' => $city));

            $data = array(
                'date' => $date,
                'time' => $params['necesserytime'],
                'receiver_city_id' => $receiver_city['City_ID'],
                'receiver_city_owner_id' => $receiver_city['City_owner_ID'],
                'mode' => $service,     
                'fio' => $fio,
                'sboraddr_id' => $sboraddr_id,
                'sboraddr_owner_id' => $sboraddr_owner_id,
                'order_type' => 0,
                'placescount' => $params['placescount'],
                'weight' => $params['weight'],
                'length' => $params['length'],
                'width' => $params['width'],
                'depth' => $params['depth'],
                'description' => $params['description'],
            );

            $order_id = RusSpsr::WACreateOrder($data);

            if(!empty($order_id)) {
                fn_set_notification('N', __('notice'), __('shippings.spsr.order_add'). ' : ' . $order_id);
            } else {
                fn_set_notification('E', __('notice'), __('shippings.spsr.not_order_add') . ' : ' . RusSpsr::$last_error);
            }

            RusSpsr::WALogout();

            $suffix = ".manage";
        }

    } elseif ($mode == 'delete') {
        if (!empty($_REQUEST['courier_id']) && !empty($_REQUEST['courier_owner_id'])) {
            fn_spsr_delete_courier($_REQUEST['courier_id'], $_REQUEST['courier_owner_id']);
        }

        $suffix = ".manage";
    }

    return array(CONTROLLER_STATUS_OK, "spsr_courier$suffix");
}

if ($mode == 'manage') {
    $params = $_REQUEST;
    $data = array (
        'period' => empty($params['period']) ? 'A' : $params['period'],
    );

    if (!empty($data['period']) && $data['period'] != 'A') {
        if (!empty($params['time_from'])) {
            $data['time_from'] = strtotime($params['time_from']);
        }
        if (!empty($params['time_to'])) {
            $data['time_to'] = strtotime($params['time_to']);
        }
        list($data['time_from'], $data['time_to']) = fn_create_periods($data);
    } else {
        $data['time_from'] = $data['time_to'] = 0;
    }

    $spsr_login = RusSpsr::WALogin();

    if ($spsr_login) {
        if (!empty($data['period']) && $data['period'] != 'A') {
            $couriers = RusSpsr::WAGetOrders($data['time_from'], $data['time_to']);
        } else {
            $couriers = RusSpsr::WAGetActiveOrders();
        }

        list($couriers, $search) = fn_get_spsr_couriers($params, Registry::get('settings.Appearance.admin_elements_per_page'), $couriers);

        Tygh::$app['view']->assign('couriers', $couriers);
        Tygh::$app['view']->assign('search', $search);

        $addr_list = RusSpsr::WAGetAddrList(8);
        Tygh::$app['view']->assign('addr_list', $addr_list);

        $spsr_services = RusSpsr::WAGetServices();
        Tygh::$app['view']->assign('spsr_services', $spsr_services);

        RusSpsr::WALogout();
    } else {
        fn_set_notification('E', __('notice'), RusSpsr::$last_error);
    }

    $period = $data;

    Tygh::$app['view']->assign('period', $period);

}

function fn_spsr_delete_courier($id , $owner_id) {
    RusSpsr::WALogin();

    $result = RusSpsr::WACancelOrder($id , $owner_id);

    $save = array(
        'courier_key' => '', 
        'courier_id' => '', 
        'courier_owner_id' => '', 
    );

    db_query('UPDATE ?:rus_spsr_invoices SET ?u WHERE courier_id = ?i AND courier_owner_id = ?i', $save, $id, $owner_id);

    if($result) {
        fn_set_notification('N', __('notice'), __('shippings.spsr.order_delete') . ' : ' . $result['order'] . ' ' . $result['order_state']);
    } else {
        fn_set_notification('E', __('notice'), __('shippings.spsr.not_order_delete') . ' : ' . RusSpsr::$last_error);
    }

    RusSpsr::WALogout();
}

function fn_get_spsr_couriers($params, $items_per_page, $couriers)
{
    $params = LastView::instance()->update('couriers', $params);

    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );
    $params = array_merge($default_params, $params);

    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = count($couriers);
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $limit = str_replace("LIMIT ", "", $limit);
    $offset = explode(",", $limit);

    if (!empty($couriers)) {
        $couriers = array_slice($couriers, (int) $offset[0], (int) $offset[1]);
    }

    return array($couriers, $params);
}
