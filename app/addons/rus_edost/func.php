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

if ( !defined('AREA') ) { die('Access denied'); }

function fn_rus_edost_install()
{
    $services = fn_get_schema('edost', 'services', 'php', true);

    foreach ($services as $service) {
        $service_id = db_query('INSERT INTO ?:shipping_services ?e', $service);
        $service['service_id'] = $service_id;

        foreach (Languages::getAll() as $service['lang_code'] => $lang_data) {
            db_query('INSERT INTO ?:shipping_service_descriptions ?e', $service);
        }
    }
}

function fn_rus_edost_uninstall()
{
    $service_ids = db_get_fields('SELECT service_id FROM ?:shipping_services WHERE module = ?s', 'edost');

    if (!empty($service_ids)) {
        db_query('DELETE FROM ?:shipping_services WHERE service_id IN (?a)', $service_ids);
        db_query('DELETE FROM ?:shipping_service_descriptions WHERE service_id IN (?a)', $service_ids);
    }
}

function fn_rus_edost_update_cart_by_data_post(&$cart, $new_cart_data, $auth)
{

    if (!empty($new_cart_data['select_office'])) {
        $cart['select_office'] = $new_cart_data['select_office'];
    }

}

function fn_rus_edost_calculate_cart_taxes_pre(&$cart, $cart_products, &$product_groups)
{

    if (!empty($cart['shippings_extra'])) {

        if (!empty($cart['select_office'])) {
            $select_office = $cart['select_office'];
        } elseif (!empty($_REQUEST['select_office'])) {
            $select_office = $cart['select_office'] = $_REQUEST['select_office'];
        }

        if (!empty($select_office)) {
            foreach ($product_groups as $group_key => $group) {
                if (!empty($group['chosen_shippings'])) {
                    foreach ($group['chosen_shippings'] as $shipping_key => $shipping) {
                        $shipping_id = $shipping['shipping_id'];

                        if($shipping['module'] != 'edost') {
                            continue;
                        }

                        if (!empty($cart['shippings_extra']['data'][$group_key][$shipping_id])) {
                            $shippings_extra = $cart['shippings_extra']['data'][$group_key][$shipping_id];

                            $product_groups[$group_key]['chosen_shippings'][$shipping_key]['data'] = $shippings_extra;

                            if (!empty($select_office[$group_key][$shipping_id])) {
                                $office_id = $select_office[$group_key][$shipping_id];
                                $product_groups[$group_key]['chosen_shippings'][$shipping_key]['office_id'] = $office_id;
                                if (!empty($shippings_extra['office'][$office_id])) {
                                    $office_data = $shippings_extra['office'][$office_id];
                                    $product_groups[$group_key]['chosen_shippings'][$shipping_key]['office_data'] = $office_data;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!empty($cart['shippings_extra']['data'])) {
            foreach ($cart['shippings_extra']['data'] as $group_key => $shippings) {
                foreach ($shippings as $shipping_id => $shipping_data) {

                    if (!empty($product_groups[$group_key]['shippings'][$shipping_id]['module'])) 
                    {                    
                        $module = $product_groups[$group_key]['shippings'][$shipping_id]['module'];
                        if (!empty($shipping_data) && $module == 'edost') {
                            $product_groups[$group_key]['shippings'][$shipping_id]['data'] = $shipping_data;
                        }
                    }

                }
            }
        }

        if (!empty($cart['shippings_extra']['rates'])) {
            foreach ($cart['shippings_extra']['rates'] as $group_key => $shippings) {
                foreach ($shippings as $shipping_id => $shipping) {
                    if (!empty($shipping['day']) && !empty($product_groups[$group_key]['shippings'][$shipping_id])) {
                        $product_groups[$group_key]['shippings'][$shipping_id]['delivery_time'] = $shipping['day'];
                    }
                }
            }
        }
    }

    if (!empty($cart['payment_id'])) {
        $payment_info = fn_get_payment_method_data($cart['payment_id']);

        if (strpos($payment_info['template'], 'edost_cod.tpl')) {

            $cart['shippings_extra']['sum'] = array(
                'pricediff' => 0,
                'transfer' => 0,
                'total' => 0
            );

            foreach ($product_groups as $group_key => $group) {

                foreach ($group['shippings'] as $shipping_id => $shipping) {

                    if (!empty($cart['shippings_extra']['rates'][$group_key][$shipping_id]['pricecash'])) {
                        $cart['product_groups'][$group_key]['shippings'][$shipping_id]['rate'] = $cart['shippings_extra']['rates'][$group_key][$shipping_id]['pricecash'];
                        $product_groups[$group_key]['shippings'][$shipping_id]['rate'] = $cart['shippings_extra']['rates'][$group_key][$shipping_id]['pricecash'];
                    }

                    if (!empty($cart['shipping'][$shipping_id])) {
                        $cart['shipping'][$shipping_id]['rate'] = $cart['shippings_extra']['rates'][$group_key][$shipping_id]['pricecash'];
                        $cart['shipping'][$shipping_id]['rates'] = 1;
                    }
                }

                if (!empty($group['chosen_shippings'])) {

                    foreach ($group['chosen_shippings'] as $shipping_key => $shipping) {

                        $shipping_id = $shipping['shipping_id'];

                        if (!empty($cart['shippings_extra']['rates'][$group_key][$shipping_id]['pricecash'])) {
                            $cart['product_groups'][$group_key]['shippings'][$shipping_id]['rate'] = $cart['shippings_extra']['rates'][$group_key][$shipping_id]['pricecash'];
                            $cart['shippings_extra']['sum']['pricediff'] += $cart['shippings_extra']['rates'][$group_key][$shipping_id]['pricediff'];
                        }

                        $cart['shippings_extra']['sum']['transfer'] += $cart['shippings_extra']['rates'][$group_key][$shipping_id]['transfer'];

                        if (!empty($cart['shippings_extra']['rates'][$group_key][$shipping['shipping_id']]['pricecash'])) {
                            $product_groups[$group_key]['chosen_shippings'][$shipping_key]['rate'] = $cart['shippings_extra']['rates'][$group_key][$shipping['shipping_id']]['pricecash'];
                            $cart['shipping_cost'] = $cart['shippings_extra']['rates'][$group_key][$shipping['shipping_id']]['pricecash'];
                            $cart['display_shipping_cost'] = $cart['shipping_cost'];
                        }
                    }

                    $cart['shippings_extra']['sum']['total'] = $cart['shippings_extra']['sum']['transfer'] + $cart['shippings_extra']['sum']['pricediff'];
                }

            }

        }

        $_SESSION['shipping_hash'] = fn_get_shipping_hash($cart['product_groups']);
    }

}
