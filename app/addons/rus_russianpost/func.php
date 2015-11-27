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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Registry;
use Tygh\Languages\Languages;

function fn_rus_russianpost_install()
{
    $objects = fn_rus_russianpost_schema();

    foreach ($objects as $object) {

        $service = array(
            'status' => $object['status'],
            'module' => $object['module'],
            'code' => $object['code'],
            'sp_file' => $object['sp_file'],
            'description' => $object['description'],
        );

        $service_id = db_get_field('SELECT service_id FROM ?:shipping_services WHERE module = ?s AND code = ?s', $object['module'], $object['code']);
        if (empty($service_id)) {

            $service['service_id'] = db_query('INSERT INTO ?:shipping_services ?e', $service);

            foreach (Languages::getAll() as $service['lang_code'] => $lang_data) {
                db_query('INSERT INTO ?:shipping_service_descriptions ?e', $service);
            }
        }
    }
}

function fn_rus_russianpost_uninstall()
{
    $objects = fn_rus_russianpost_schema();

    foreach ($objects as $object) {
        $service_ids = db_get_fields('SELECT service_id FROM ?:shipping_services WHERE module = ?s', $object['module']);

        if (!empty($service_ids)) {
            db_query('DELETE FROM ?:shipping_services WHERE service_id IN (?a)', $service_ids);
            db_query('DELETE FROM ?:shipping_service_descriptions WHERE service_id IN (?a)', $service_ids);
        }
    }
}

function fn_rus_russianpost_schema()
{
    return array(
        'ems' => array(
            'status' => 'A',
            'module' => 'russian_post',
            'code' => 'ems',
            'sp_file' => '',
            'description' => 'EMS (Почта России)'
        ),
        'post' => array(
            'status' => 'A',
            'module' => 'russian_post',
            'code' => 'russian_post',
            'sp_file' => '',
            'description' => 'Почта России'
        ),
        'postcalc' => array(
            'status' => 'A',
            'module' => 'russian_post',
            'code' => 'russian_post_calc',
            'sp_file' => '',
            'description' => 'Калькулятор Почты России'
        )
    );
}

function fn_rus_russianpost_format_price_down($price, $payment_currency)
{
    $currencies = Registry::get('currencies');

    if (array_key_exists($payment_currency, $currencies)) {
        $price = fn_format_price($price * $currencies[$payment_currency]['coefficient']);
    } else {
        return false;
    }

    return $price;
}

function fn_rus_russianpost_format_price($price, $payment_currency)
{
    $currencies = Registry::get('currencies');

    if (array_key_exists($payment_currency, $currencies)) {
        if ($currencies[$payment_currency]['is_primary'] != 'Y') {
            $price = fn_format_price($price / $currencies[$payment_currency]['coefficient']);
        }
    } else {
        return false;
    }

    return $price;
}

function fn_rus_russianpost_get_shipping_service($module)
{
    $service = db_get_row('SELECT * FROM ?:shipping_services WHERE `module` = ?s', $module);

    return $service;
}

function fn_rus_russianpost_calculate_cart_taxes_pre(&$cart, $cart_products, &$product_groups)
{

    if (!empty($cart['shippings_extra']['data'])) {

        if (!empty($cart['shippings_extra']['data'])) {
            foreach($cart['shippings_extra']['data'] as $group_key => $shippings) {
                foreach($shippings as $shipping_id => $shippings_extra) {

                    if (!empty($product_groups[$group_key]['shippings'][$shipping_id]['module'])) {
                        $module = $product_groups[$group_key]['shippings'][$shipping_id]['module'];
                        if ($module == 'ems' && !empty($shippings_extra)) {
                            $product_groups[$group_key]['shippings'][$shipping_id]['data'] = $shippings_extra;

                            if (!empty($shippings_extra['delivery_time'])) {
                                $product_groups[$group_key]['shippings'][$shipping_id]['delivery_time'] = $shippings_extra['delivery_time'];
                            }
                        }
                    }
                }
            }
        }

        foreach ($product_groups as $group_key => $group) {
            if (!empty($group['chosen_shippings'])) {
                foreach ($group['chosen_shippings'] as $shipping_key => $shipping) {
                    $shipping_id = $shipping['shipping_id'];
                    $module = $shipping['module'];
                    if ($module == 'ems' && !empty($cart['shippings_extra']['data'][$group_key][$shipping_id])) {
                        $shipping_extra = $cart['shippings_extra']['data'][$group_key][$shipping_id];
                        $product_groups[$group_key]['chosen_shippings'][$shipping_key]['data'] = $shipping_extra;
                    }
                }
            }
        }

    }
}

function fn_rus_postblank_clear_text_cen($string)
{

    $array = array();
    $string_array = explode(' ',$string);
    foreach($string_array as $key => $value) {
        if (is_numeric($value)) {
            $array[] = $value;
        }
    }

    $count = count($array);

    if ($count == 1) {

        $total = $array[0] . '.00';
        $rub = $array[0];
        $kop = '00';
    } elseif ($count == 2) {
        $total = implode('.',$array);
        $rub = $array[0];
        $kop = $array[1];
    } elseif ($count > 2) {
        $total = $array[0] . '.' . $array[1];
        $rub = $array[0];
        $kop = $array[1];
    } else {
        $total = $rub = $kop = '';
    }

    return array($total, $rub, $kop);
}
