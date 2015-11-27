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

use Tygh\Languages\Languages;
use Tygh\Languages\Values;

if ( !defined('AREA') ) { die('Access denied'); }

function fn_rus_pickup_install()
{
    $service = array(
        'status' => 'A',
        'module' => 'pickup',
        'code' => 'pickup',
        'sp_file' => '',
        'description' => 'Pickup',
    );

    $service['service_id'] = db_get_field('SELECT service_id FROM ?:shipping_services WHERE module = ?s AND code = ?s', $service['module'], $service['code']);

    if (empty($service['service_id'])) {
        $service['service_id'] = db_query('INSERT INTO ?:shipping_services ?e', $service);
    }

    $languages = Languages::getAll();
    foreach ($languages as $lang_code => $lang_data) {

        if ($lang_code == 'ru') {
            $service['description'] = "Самовывоз";
        } else {
            $service['description'] = "Pickup";
        }

        $service['lang_code'] = $lang_code;

        db_query('INSERT INTO ?:shipping_service_descriptions ?e', $service);
    }
}

function fn_rus_pickup_uninstall()
{
    $service_ids = db_get_fields('SELECT service_id FROM ?:shipping_services WHERE module = ?s', 'pickup');
    if (!empty($service_ids)) {
        db_query('DELETE FROM ?:shipping_services WHERE service_id IN (?a)', $service_ids);
        db_query('DELETE FROM ?:shipping_service_descriptions WHERE service_id IN (?a)', $service_ids);
    }
}

function fn_rus_pickup_update_cart_by_data_post(&$cart, $new_cart_data, $auth)
{

    if (!empty($new_cart_data['select_store'])) {
        $cart['select_store'] = $new_cart_data['select_store'];
    }

}

function fn_rus_pickup_calculate_cart_taxes_pre(&$cart, $cart_products, &$product_groups)
{

    if (!empty($cart['shippings_extra']['data'])) {

        if (!empty($cart['select_store'])) {
            $select_store = $cart['select_store'];
        } elseif (!empty($_REQUEST['select_store'])) {
            $select_store = $cart['select_store'] = $_REQUEST['select_store'];
        }

        if (!empty($select_store)) {

            $tmp_surcharge_array = array();
            foreach ($select_store as $g_key => $g) {
                foreach ($g as $s_id => $s) {
                    $tmp_surcharge = $cart['shippings_extra']['data'][$g_key][$s_id]['stores'][$s]['pickup_surcharge'];

                    if (!empty($product_groups[$g_key]['shippings'][$s_id]['rate'])) {
                        $tmp_rate = $product_groups[$g_key]['shippings'][$s_id]['rate'];
                        $tmp_surcharge_array[$g_key][$s_id] = $tmp_rate - $tmp_surcharge;
                    }
                }
            }

            foreach ($product_groups as $group_key => $group) {
                if (!empty($group['chosen_shippings'])) {
                    foreach ($group['chosen_shippings'] as $shipping_key => $shipping) {

                        if ($shipping['module'] != 'pickup') {
                            continue;
                        }

                        $shipping_id = $shipping['shipping_id'];

                        if (!empty($cart['shippings_extra']['data'][$group_key][$shipping_id])) {
                            $shippings_extra = $cart['shippings_extra']['data'][$group_key][$shipping_id];

                            if (!empty($tmp_surcharge_array[$group_key][$shipping_id])) {
                                foreach ($shippings_extra['stores'] as $_key => $_store) {
                                    $shippings_extra['stores'][$_key]['pickup_rate'] = $shippings_extra['stores'][$_key]['pickup_surcharge'] + $tmp_surcharge_array[$group_key][$shipping_id];
                                }
                            }

                            $product_groups[$group_key]['chosen_shippings'][$shipping_key]['data'] = $shippings_extra;

                            if (!empty($select_store[$group_key][$shipping_id])) {
                                $store_id = $select_store[$group_key][$shipping_id];
                                $product_groups[$group_key]['chosen_shippings'][$shipping_key]['store_location_id'] = $store_id;
                                if (!empty($shippings_extra['stores'][$store_id])) {
                                    $store_data = $shippings_extra['stores'][$store_id];
                                    $product_groups[$group_key]['chosen_shippings'][$shipping_key]['store_data'] = $store_data;
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($cart['shippings_extra']['data'] as $group_key => $shippings) {
            foreach ($shippings as $shipping_id => $shippings_extra) {

                if (!empty($product_groups[$group_key]['shippings'][$shipping_id]['module'])) {
                    $module = $product_groups[$group_key]['shippings'][$shipping_id]['module'];

                    if ($module == 'pickup' && !empty($shippings_extra)) {

                        if (!empty($tmp_surcharge_array[$group_key][$shipping_id])) {
                            foreach ($shippings_extra['stores'] as $_key => $_store) {
                                $shippings_extra['stores'][$_key]['pickup_rate'] = $shippings_extra['stores'][$_key]['pickup_surcharge'] + $tmp_surcharge_array[$group_key][$shipping_id];
                            }                          
                        }

                        $product_groups[$group_key]['shippings'][$shipping_id]['data'] = $shippings_extra;
                    }
                }

            }
        }

        foreach ($product_groups as $group_key => $group) {
            if (!empty($group['chosen_shippings'])) {
                foreach ($group['chosen_shippings'] as $shipping_key => $shipping) {
                    $shipping_id = $shipping['shipping_id'];
                    $module = $shipping['module'];

                    if ($module == 'pickup' && !empty($cart['shippings_extra']['data'][$group_key][$shipping_id])) {
                        $shippings_extra = $cart['shippings_extra']['data'][$group_key][$shipping_id];

                        if (!empty($tmp_surcharge_array[$group_key][$shipping_id])) {
                            foreach ($shippings_extra['stores'] as $_key => $_store) {
                                $shippings_extra['stores'][$_key]['pickup_rate'] = $shippings_extra['stores'][$_key]['pickup_surcharge'] + $tmp_surcharge_array[$group_key][$shipping_id];
                            }
                        }
                        $product_groups[$group_key]['chosen_shippings'][$shipping_key]['data'] = $shippings_extra;

                    }

                }
            }
        }
    }

}
