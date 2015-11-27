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
use Tygh\Shippings\RusSdek;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $params = $_REQUEST;
    $order_info = fn_get_order_info($params['order_id'], false, true, true, true);

    if ($mode == 'sdek_order_delivery') {

        if (empty($params['add_sdek_info'])) {
            return false;
        }

        foreach ($params['add_sdek_info'] as $shipment_id => $sdek_info) {

            list($_shipments, $search) = fn_get_shipments_info(array('order_id' => $params['order_id'], 'advanced_info' => true, 'shipment_id' => $shipment_id));

            $shipment = reset($_shipments);

            $params_shipping = array(
                'shipping_id' => $shipment['shipping_id'],
                'Date' => date("Y-m-d", $shipment['shipment_timestamp'])
            );

            $data_auth = RusSdek::dataAuth($params_shipping);

            if (empty($data_auth)) {
                continue;
            } 

            $order_for_sdek = $sdek_info['Order'];

            $lastname = "";
            if (!empty($order_info['lastname'])) {
                $lastname = $order_info['lastname'];

            } elseif (!empty($order_info['s_lastname'])) {
                $lastname = $order_info['s_lastname'];

            } elseif (!empty($order_info['b_lastname'])) {
                $lastname = $order_info['b_lastname'];
            }
            $firstname = "";
            if (!empty($order_info['firstname'])) {
                $firstname = $order_info['firstname'];

            } elseif (!empty($order_info['s_firstname'])) {
                $firstname = $order_info['s_firstname'];

            } elseif (!empty($order_info['b_firstname'])) {
                $firstname = $order_info['b_firstname'];
            }

            $order_for_sdek['RecipientName'] = $lastname . ' ' . $firstname;

            if (!empty($order_info['phone'])) {
                $order_for_sdek['Phone'] = $order_info['phone'];

            } elseif (!empty($order_info['s_phone'])) {
                $order_for_sdek['Phone'] = $order_info['s_phone'];

            } elseif (!empty($order_info['b_phone'])) {
                $order_for_sdek['Phone'] = $order_info['b_phone'];
            }

            $sdek_products = array();
            $weight = 0;

            foreach ($shipment['products'] as $item_id => $amount) {
                $data_product = $order_info['products'][$item_id];

                $product_weight = db_get_field("SELECT weight FROM ?:products WHERE product_id = ?i", $data_product['product_id']);

                if (!empty($product_weight) && $product_weight != 0) {
                    $product_weight = $product_weight;
                } else {
                    $product_weight = 0.01;
                }

                $sdek_products[] = array(
                    'ware_key' => $data_product['product_id'],
                    'product' => $data_product['product'],
                    'price' => $data_product['price'],
                    'amount' => $amount,
                    'total' => $data_product['price'] * $amount,
                    'weight' => $amount * $product_weight,
                    'order_id' => $params['order_id'],
                    'shipment_id' => $shipment_id,
                );
                $weight = $weight + ($amount * $product_weight);
            }

            $order_for_sdek['SellerName'] = Registry::get('runtime.company_data.company');

            $data_auth['Number'] = $params['order_id'] . '_' . $shipment_id;

            $data_auth['OrderCount'] = "1";

            $xml = '            ' . RusSdek::arraySimpleXml('DeliveryRequest', $data_auth, 'open');

            $order_for_sdek['Number'] = $params['order_id'] . '_' . $shipment_id;
            $order_for_sdek['DateInvoice'] = date("Y-m-d", $shipment['shipment_timestamp']);
            $order_for_sdek['RecipientEmail'] = $order_info['email'];
            $order_for_sdek['DeliveryRecipientCost'] = $order_for_sdek['DeliveryRecipientCost'];

            $xml .= '            ' . RusSdek::arraySimpleXml('Order', $order_for_sdek, 'open');

            if (!empty($sdek_info['Address'])) {
                $xml .= '            ' . RusSdek::arraySimpleXml('Address', $sdek_info['Address']);
            }

            $package_for_xml = array (
                'Number' => $shipment_id,
                'BarCode' => $shipment_id,
                'Weight' => $weight
            );
            $xml .= '            ' . RusSdek::arraySimpleXml('Package', $package_for_xml, 'open');

            foreach ($sdek_products as $k => $product) {
                $product_for_xml = array (
                    'WareKey' => $product['ware_key'],
                    'Cost' => $product['price'],
                    'Payment' => $product['price'],
                    'Weight' => $product['weight'],
                    'Amount' => $product['amount'],
                    'Comment' => $product['product'],
                );

                $xml .= '            ' . RusSdek::arraySimpleXml('Item', $product_for_xml);
            }

            $xml .= '            ' . '</Package>';
            $xml .= '            ' . '</Order>';
            $xml .= '            ' . '</DeliveryRequest>';

            $response = RusSdek::xmlRequest('http://gw.edostavka.ru:11443/new_orders.php', $xml, $data_auth);
            
            $result = RusSdek::resultXml($response);

            if (empty($result['error'])) {

                $register_data = array(
                    'order_id' => $params['order_id'],
                    'shipment_id' => $shipment_id,
                    'dispatch_number' => $result['number'],
                    'data' => date("Y-m-d", $shipment['shipment_timestamp']),
                    'data_xml' => $xml,
                    'timestamp' => TIME,
                    'status' => 'S',
                    'tariff' => $sdek_info['Order']['TariffTypeCode'],
                    'file_sdek' => $shipment_id . '/' . $params['order_id'] . '.pdf',
                    'notes' => $sdek_info['Order']['Comment'],
                );

                if (!empty($result['number'])) {
                    db_query('UPDATE ?:shipments SET tracking_number = ?s WHERE shipment_id = ?i', $result['number'], $shipment_id);
                }

                if ($sdek_info['Order']['TariffTypeCode'] != SDEK_STOCKROOM) {
                    $register_data['address'] = $sdek_info['Address']['Street'];
                } else {
                    $register_data['address_pvz'] = "{$sdek_info['Address']['PvzCode']}";
                }

                $register_id = db_query('INSERT INTO ?:rus_sdek_register ?e', $register_data);

                foreach ($sdek_products as $sdek_product) {
                    $sdek_product['register_id'] = $register_id;
                    db_query('INSERT INTO ?:rus_sdek_products ?e', $sdek_product);
                }

                fn_sdek_get_ticket_order($data_auth, $params['order_id'], $shipment_id);
            }

            $date_status = RusSdek::orderStatusXml($data_auth, $params['order_id'], $shipment_id);

            RusSdek::addStatusOrders($date_status);
        }

    } elseif ($mode == 'sdek_order_delete') {
        foreach ($params['add_sdek_info'] as $shipment_id => $sdek_info) {
            list($_shipments) = fn_get_shipments_info(array('order_id' => $params['order_id'], 'advanced_info' => true, 'shipment_id' => $shipment_id));
            $shipment = reset($_shipments);
            $params_shipping = array(
                'shipping_id' => $shipment['shipping_id'],
                'Date' => date("Y-m-d", $shipment['shipment_timestamp']),
            );
            $data_auth = RusSdek::dataAuth($params_shipping);
            if (empty($data_auth)) {
                continue;
            }

            $data_auth['Number'] = $params['order_id'] . '_' . $shipment_id;
            $data_auth['OrderCount'] = "1";
            $xml = '            ' . RusSdek::arraySimpleXml('DeleteRequest', $data_auth, 'open');
            $order_sdek = array (
                'Number' => $params['order_id'] . '_' . $shipment_id
            );
            $xml .= '            ' . RusSdek::arraySimpleXml('Order', $order_sdek);
            $xml .= '            ' . '</DeleteRequest>';

            $response = RusSdek::xmlRequest('http://gw.edostavka.ru:11443/delete_orders.php', $xml, $data_auth);
            $result = RusSdek::resultXml($response);
            if (empty($result['error'])) {
                db_query('DELETE FROM ?:rus_sdek_products WHERE order_id = ?i and shipment_id = ?i ', $params['order_id'], $shipment_id);
                db_query('DELETE FROM ?:rus_sdek_register WHERE order_id = ?i and shipment_id = ?i ', $params['order_id'], $shipment_id);
                db_query('DELETE FROM ?:rus_sdek_status WHERE order_id = ?i and shipment_id = ?i ', $params['order_id'], $shipment_id);
            }
        }

    } elseif ($mode == 'sdek_order_status') {
        foreach ($params['add_sdek_info'] as $shipment_id => $sdek_info) {
            list($_shipments) = fn_get_shipments_info(array('order_id' => $params['order_id'], 'advanced_info' => true, 'shipment_id' => $shipment_id));
            $shipment = reset($_shipments);
            $params_shipping = array(
                'shipping_id' => $shipment['shipping_id'],
                'Date' => date("Y-m-d", $shipment['shipment_timestamp']),
            );
            $data_auth = RusSdek::dataAuth($params_shipping);
            if (empty($data_auth)) {
                continue;
            }
            $date_status = RusSdek::orderStatusXml($data_auth, $params['order_id'], $shipment_id);
            RusSdek::addStatusOrders($date_status);
        }
    }

    $url = fn_url("orders.details&order_id=" . $params['order_id'], 'A', 'current');
    if (defined('AJAX_REQUEST') && !empty($url)) {
        Registry::get('ajax')->assign('force_redirection', $url);
        exit;
    }

    return array(CONTROLLER_STATUS_OK, $url);
}

if ($mode == 'details') {
    $params = $_REQUEST;
    $order_info = Tygh::$app['view']->getTemplateVars('order_info');

    $sdek_info = $sdek_pvz = false; 
    if (!empty($order_info['shipping'])) {
        foreach ($order_info['shipping'] as $shipping) {
            if (($shipping['module'] == 'sdek') && !empty($shipping['office_id'])) {
                $sdek_pvz = $shipping['office_id'];                
            }
        }        
    }

    list($all_shipments) = fn_get_shipments_info(array('order_id' => $params['order_id'], 'advanced_info' => true));

    if (!empty($all_shipments)) {

        $sdek_shipments = $data_shipments = array();

        foreach ($all_shipments as $key => $_shipment) {
            if ($_shipment['carrier'] == 'sdek') {
                $sdek_shipments[] = $_shipment;
            }
        }

        if (!empty($sdek_shipments)) {

            $offices = array();
            $rec_city = (!empty($order_info['s_city'])) ? $order_info['s_city'] : $order_info['b_city'];
            $rec_city_code = db_get_field("SELECT city_code FROM ?:rus_city_sdek_descriptions as a LEFT JOIN ?:rus_cities_sdek as b ON a.city_id=b.city_id WHERE city = ?s", $rec_city);


            if (!empty($rec_city_code)) {
                $offices = RusSdek::pvzOffices(array('cityid' => $rec_city_code));
            }

            foreach ($sdek_shipments as $key => $shipment) {

                    $data_sdek = db_get_row("SELECT register_id, order_id, timestamp, status, tariff, address_pvz, address, file_sdek, notes FROM ?:rus_sdek_register WHERE order_id = ?i and shipment_id = ?i", $shipment['order_id'], $shipment['shipment_id']);

                    if (!empty($data_sdek)) {
                        $data_shipments[$shipment['shipment_id']] = $data_sdek;
                        $data_shipments[$shipment['shipment_id']]['shipping'] = $shipment['shipping'];
                        if ($data_shipments[$shipment['shipment_id']]['tariff'] == SDEK_STOCKROOM) {
                            $data_shipments[$shipment['shipment_id']]['address'] = $offices[$data_sdek['address_pvz']]['Address'];
                        }

                        $data_status = db_get_array("SELECT * FROM ?:rus_sdek_status WHERE order_id = ?i AND shipment_id = ?i ", $params['order_id'], $shipment['shipment_id']);
                        if (!empty($data_status)) {
                            foreach ($data_status as $k => $status) {
                                $status['city'] = db_get_field("SELECT city FROM ?:rus_city_sdek_descriptions as a LEFT JOIN ?:rus_cities_sdek as b ON a.city_id=b.city_id WHERE b.city_code = ?s", $status['city_code']);
                                $status['date'] = date("d-m-Y  H:i:s", $status['timestamp']);
                                $data_shipments[$shipment['shipment_id']]['sdek_status'][$status['id']] = array(
                                    'id' => $status['id'],
                                    'date' => $status['date'],
                                    'status' => $status['status'],
                                    'city' => $status['city'],
                                );
                            }
                        }

                    } else {

                        $data_shipping = fn_get_shipping_info($shipment['shipping_id'], DESCR_SL);

                        $cost = fn_sdek_calculate_cost_by_shipment($order_info, $data_shipping, $shipment, $rec_city_code);

                        $data_shipments[$shipment['shipment_id']] = array(
                            'order_id' => $shipment['order_id'],
                            'shipping' => $shipment['shipping'],
                            'comments' => $shipment['comments'],
                            'delivery_cost' => $cost,
                            'tariff_id' => $data_shipping['service_params']['tariffid'],
                            'send_city_code' => $data_shipping['service_params']['from_city_id'],
                        );

                        if ($data_shipping['service_params']['tariffid'] == SDEK_STOCKROOM) {
                            $data_shipments[$shipment['shipment_id']]['offices'] = $offices;
                        } else {
                            $data_shipments[$shipment['shipment_id']]['rec_address'] = (!empty($order_info['s_address'])) ? $order_info['s_address'] : $order_info['b_address'];
                        }
                    }
            }

            if (!empty($data_shipments)) {

                Registry::set('navigation.tabs.sdek_orders', array (
                    'title' => __('shippings.sdek.sdek_orders'),
                    'js' => true
                ));

                Tygh::$app['view']->assign('data_shipments', $data_shipments);
                Tygh::$app['view']->assign('sdek_pvz', $sdek_pvz);
                Tygh::$app['view']->assign('rec_city_code', $rec_city_code);
                Tygh::$app['view']->assign('order_id', $params['order_id']);

            }
        }
    }

} elseif ($mode == 'sdek_get_ticket') {

    $params = $_REQUEST;

    $file = $params['order_id'] . '.pdf';

    $path = fn_get_files_dir_path() . 'sdek/' . $params['shipment_id'] . '/';

    fn_get_file($path . $file);

    if (defined('AJAX_REQUEST') && !empty($url)) {
        Registry::get('ajax')->assign('force_redirection', $url);
        exit;
    }

    return array(CONTROLLER_STATUS_OK);
}

function fn_sdek_get_ticket_order($data_auth, $order_id, $chek_id)
{
    unset($data_auth['Number']);
    $xml = '            ' . RusSdek::arraySimpleXml('OrdersPrint', $data_auth, 'open');
    $order_sdek = array (
        'Number' => $order_id . '_' . $chek_id,
        'Date' => $data_auth['Date']
    );
    $xml .= '            ' . RusSdek::arraySimpleXml('Order', $order_sdek);
    $xml .= '            ' . '</OrdersPrint>';

    $response = RusSdek::xmlRequest('http://gw.edostavka.ru:11443/orders_print.php', $xml, $data_auth);

    $download_file_dir = fn_get_files_dir_path() . '/sdek' . '/' . $chek_id . '/';

    fn_rm($download_file_dir);
    fn_mkdir($download_file_dir);

    $name = $order_id . '.pdf';

    $download_file_path = $download_file_dir . $name;
    if (!fn_is_empty($response)) {
        fn_put_contents($download_file_path, $response);
    }
}