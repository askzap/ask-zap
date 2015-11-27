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
use Tygh\Http;
use Tygh\Languages\Languages;

if ( !defined('AREA') ) { die('Access denied'); }

function fn_rus_sdek_install()
{
    $service = array(
        'status' => 'A',
        'module' => 'sdek',
        'code' => '1',
        'sp_file' => '',
        'description' => 'СДЭК',
    );

    $service['service_id'] = db_query('INSERT INTO ?:shipping_services ?e', $service);

    foreach (Languages::getAll() as $service['lang_code'] => $lang_data) {
        db_query('INSERT INTO ?:shipping_service_descriptions ?e', $service);
    }
}

function fn_rus_sdek_uninstall()
{
    $service_ids = db_get_fields('SELECT service_id FROM ?:shipping_services WHERE module = ?s', 'sdek');
    db_query('DELETE FROM ?:shipping_services WHERE service_id IN (?a)', $service_ids);
    db_query('DELETE FROM ?:shipping_service_descriptions WHERE service_id IN (?a)', $service_ids);
    
    db_query('DROP TABLE IF EXISTS ?:rus_cities_sdek');
    db_query('DROP TABLE IF EXISTS ?:rus_city_sdek_descriptions');
    db_query('DROP TABLE IF EXISTS ?:rus_sdek_products');
    db_query('DROP TABLE IF EXISTS ?:rus_sdek_register');
    db_query('DROP TABLE IF EXISTS ?:rus_sdek_status');
}

function fn_rus_sdek_update_cart_by_data_post(&$cart, $new_cart_data, $auth)
{
    if (!empty($new_cart_data['select_office'])) {
        $cart['select_office'] = $new_cart_data['select_office'];
    }
}

function fn_rus_sdek_calculate_cart_taxes_pre(&$cart, $cart_products, &$product_groups)
{

    if (!empty($cart['shippings_extra']['data'])) {
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

                        if($shipping['module'] != 'sdek') {
                            continue;
                        }

                        if (!empty($cart['shippings_extra']['data'][$group_key][$shipping_id])) {
                            $shippings_extra = $cart['shippings_extra']['data'][$group_key][$shipping_id];
                            $product_groups[$group_key]['chosen_shippings'][$shipping_key]['data'] = $shippings_extra;
                            if (!empty($select_office[$group_key][$shipping_id])) {
                                $office_id = $select_office[$group_key][$shipping_id];
                                $product_groups[$group_key]['chosen_shippings'][$shipping_key]['office_id'] = $office_id;

                                if (!empty($shippings_extra['offices'][$office_id])) {
                                    $office_data = $shippings_extra['offices'][$office_id];
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
                foreach ($shippings as $shipping_id => $shippings_extra) {
                    if (!empty($product_groups[$group_key]['shippings'][$shipping_id]['module'])) {
                        $module = $product_groups[$group_key]['shippings'][$shipping_id]['module'];

                        if ($module == 'sdek' && !empty($shippings_extra)) {
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

                    if ($module == 'sdek' && !empty($cart['shippings_extra']['data'][$group_key][$shipping_id])) {
                        $shipping_extra = $cart['shippings_extra']['data'][$group_key][$shipping_id];
                        $product_groups[$group_key]['chosen_shippings'][$shipping_key]['data'] = $shipping_extra;
                    }
                }
            }
        }
    }
}

function fn_sdek_calculate_cost_by_shipment($order_info, $shipping_info, $shipment_info, $rec_city_code) 
{

        $total = $weight =  0;
        $goods = array();
        $length = $width = $height = 20;

        foreach ($shipment_info['products'] as $item_id => $amount) {
            $product = $order_info['products'][$item_id];

            $total += $product['subtotal'];

            $product_extra = db_get_row("SELECT shipping_params, weight FROM ?:products WHERE product_id = ?i", $product['product_id']);

            if (!empty($product_extra['weight']) && $product_extra['weight'] != 0) {
                $product_weight = $product_extra['weight'];
            } else {
                $product_weight = 0.01;
            }

            $p_ship_params = unserialize($product_extra['shipping_params']);

            $package_length = empty($p_ship_params['box_length']) ? $length : $p_ship_params['box_length'];
            $package_width = empty($p_ship_params['box_width']) ? $width : $p_ship_params['box_width'];
            $package_height = empty($p_ship_params['box_height']) ? $height : $p_ship_params['box_height'];
            $weight_ar = fn_expand_weight($product_weight);
            $weight = round($weight_ar['plain'] * Registry::get('settings.General.weight_symbol_grams') / 1000, 3);

            $good['weight'] = $weight;
            $good['length'] = $package_length;
            $good['width'] = $package_width;
            $good['height'] = $package_height;

            for ($x = 1; $x <= $amount; $x++) {
                $goods[] = $good;
            }
            
        }

        $url = 'http://api.edostavka.ru/calculator/calculate_price_by_json.php';
        $post['version'] = '1.0';       
        $post['dateExecute'] = date('Y-m-d');

        if (!empty($shipping_info['service_params']['dateexecute'])) {
            $timestamp = TIME + $shipping_info['service_params']['dateexecute'] * SECONDS_IN_DAY;
            $dateexecute = date('Y-m-d', $timestamp);
        } else {
            $dateexecute = date('Y-m-d');
        }

        $post['dateExecute'] = $dateexecute;

        if (!empty($shipping_settings['authlogin'])) {
            $post['authLogin'] = $shipping_info['service_params']['authlogin'];
            $post['secure'] = !empty($shipping_info['service_params']['authpassword']) ? md5($post['dateExecute']."&".$shipping_info['service_params']['authpassword']): '';
        }

        $post['authLogin'] = $shipping_info['service_params']['authlogin'];
        $post['secure'] = md5($post['dateExecute']."&".$shipping_info['service_params']['authpassword']);

        $post['senderCityId'] = $shipping_info['service_params']['from_city_id'];
        $post['receiverCityId'] = $rec_city_code;
        $post['tariffId'] = $shipping_info['service_params']['tariffid'];
        $post['goods'] = $goods;

        $post = json_encode($post);

        $key = md5($post);
        $sdek_data = fn_get_session_data($key);
        $content = json_encode($post);
        if (empty($sdek_data)) {
            $response = Http::post($url, $post, array('Content-Type: application/json',  'Content-Length: '.strlen($content)));
            fn_set_session_data($key, $response);
        } else {
            $response = $sdek_data;
        }

        $result = json_decode($response, true);

        if (!empty($result['result']['price'])) {
            $result = $result['result']['price'];
        } else {
            $result = false;
        }

        return $result;
}
