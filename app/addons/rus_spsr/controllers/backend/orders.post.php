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
use Tygh\RusSpsr;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $params = $_REQUEST;
    $selected_section = (!empty($params['selected_section'])) ? $params['selected_section'] : '';

    if ($mode == 'spsr_create_packages') {
        $invoice_selected_products = $params['add_product_piece'];
        $invoice_shipments_code = $params['add_invoice_product_code'];
        $shipments_cost = $params['add_invoice_ship_cost'];

        if (empty($params['order_id'])) {
            return array(CONTROLLER_STATUS_REDIRECT, "orders.manage");
        }

        $login = RusSpsr::WALogin();

        list($order_info, $invoices, $shipments, $shipping_data) = fn_spsr_pre_check_invoice_create($params['order_id'], $selected_section, $invoice_selected_products);

        $old_piece_id = db_get_field("SELECT MAX(item_id) FROM ?:rus_spsr_invoices_items");

        if (!empty($invoices) && ($login)) {
            $invoices_packages = array();
            $total_weight = 0;

            foreach ($invoices as $invoice_key => $invoice) {
                if (empty($old_piece_id) || $old_piece_id == '0' || (!empty($shipping_data[$invoice_key]['service_params']['old_piece_id']) && $old_piece_id < $shipping_data[$invoice_key]['service_params']['old_piece_id'])) {
                    $old_piece_id = $shipping_data[$invoice_key]['service_params']['piece_barcodes'];
                }

                $shipment_id = $invoice['shipment']['shipment_id'];
                $service_params = $invoice['service_params'];

                $ship_ref_num = $order_info['order_id'] . 'I' . $shipment_id;

                $products = fn_array_merge($invoice['products'], $invoice_selected_products[$invoice_key]);

                $bag_size = $service_params['service_params']['bag_size'];

                $pieces = array();
                foreach ($products as $_key => $_product) {
                    if ($_product['product_id'] != $_product['id']) {
                        fn_set_notification('E', __('notice'), __('shippings.spsr.not_invoice_create_package_error'));
                        return array(CONTROLLER_STATUS_REDIRECT, "orders.details?order_id={$_REQUEST['order_id']}&selected_section={$selected_section}");
                    }

                    $piece_key = $_product['piece'];
                    $pieces[$piece_key]['description'] = $_product['product_type'];
                    if ($_product['bag'] != 'x'){
                        $pieces[$piece_key]['length'] = $bag_size[$_product['bag']]['length'];
                        $pieces[$piece_key]['width'] = $bag_size[$_product['bag']]['width'];
                        $pieces[$piece_key]['height'] = $bag_size[$_product['bag']]['height'];          
                    } else {
                        $pieces[$piece_key]['length'] = $_product['length'];
                        $pieces[$piece_key]['width'] = $_product['width'];
                        $pieces[$piece_key]['height'] = $_product['height'];        
                    }

                    if (!isset($pieces[$piece_key]['weight'])) {
                        $pieces[$piece_key]['weight'] = 0;
                    }

                    $pieces[$piece_key]['weight'] += $_product['weight'];
                    $pieces[$piece_key]['products'][] = $_product;

                    $total_weight += $_product['weight'];
                }

                $_pieces = array();
                foreach ($pieces as $pie_key => $piece) {
                    $old_piece_id = $old_piece_id + 1;
                    $barcode = fn_rus_spsr_barcode_number($old_piece_id);

                    $_pieces[$old_piece_id] = $piece;
                    $_pieces[$old_piece_id]['piece_id'] = $old_piece_id;
                    $_pieces[$old_piece_id]['barcode'] = $barcode;
                    $_pieces[$old_piece_id]['shipment_id'] = $shipment_id;
                    $_pieces[$old_piece_id]['invoice_key'] = $invoice_key;
                    $_pieces[$old_piece_id]['ShipRefNum'] = $ship_ref_num;
                }

                $pieces = $_pieces;
                ksort($pieces);
                if (!empty($old_piece_id) && !empty($pieces)) {
                    $ship['product'] = __("shipping");

                    if ($invoice['invoice_shipping_cost'] != $shipments_cost[$shipment_id]) {
                        $ship['price'] = $shipments_cost[$shipment_id];
                    } else {
                        $ship['price'] = $invoice['invoice_shipping_cost'];
                    }
                    $pieces[$old_piece_id]['products'][] = $ship;
                }

                $invoice_data = array(
                    'order_id' => $_REQUEST['order_id'],
                    'ship_ref_num' => $ship_ref_num,
                    'shipment_id' => $shipment_id,
                    'timestamp' => TIME,
                    'shipping_cost' => $ship['price'],
                    'tariff_code' => $invoice_shipments_code[$shipment_id],
                );
                db_query('INSERT INTO ?:rus_spsr_invoices ?e', $invoice_data);

                foreach ($pieces as $item_id => $piece) {
                    $piece_data = array(
                        'item_id' => $item_id,
                        'order_id' => $params['order_id'],
                        'data' => serialize($piece),
                        'ship_ref_num' => $ship_ref_num,
                        'shipment_id' => $shipment_id,
                        'barcode' => $piece['barcode'],
                    );
                    db_query('INSERT INTO ?:rus_spsr_invoices_items ?e', $piece_data);
                }
            }
        }
        RusSpsr::WALogout();

    } elseif ($mode == 'spsr_clear_invoice') {
        $barcodes = $params['spsr_invoice']['barcodes'];

        if (!empty($params['order_id']) && !empty($barcodes)) {
            foreach ($barcodes as $shipment_id => $barcode) {
                db_query("DELETE FROM ?:rus_spsr_invoices WHERE order_id = ?i AND shipment_id = ?i", $params['order_id'], $shipment_id);
                db_query("DELETE FROM ?:rus_spsr_invoices_items WHERE order_id = ?i AND shipment_id = ?i", $params['order_id'], $shipment_id);
            }
        }

    } elseif ($mode == 'spsr_create_invoice') {
        $spsr_invoice = $params['spsr_invoice'];

        if (!empty($spsr_invoice)) {
            $packages = db_get_array("SELECT * FROM ?:rus_spsr_invoices_items WHERE order_id = ?i", $params['order_id']);

            if (!empty($packages)) {
                $login = RusSpsr::WALogin();

                if (!empty($spsr_invoice['barcodes'])) {
                    foreach ($spsr_invoice['barcodes'] as $value) {
                        foreach ($value as $item => $code) {
                            if ($code == 0) {
                                fn_set_notification('N', __('notice'), __('shippings.spsr.error_barcode') . ' ' . $item);
                            }
                        }
                    }
                }

                list($order_info, $invoices, $shipments, $shipping_data) = fn_spsr_pre_check_invoice_create($params['order_id'], $selected_section);

                foreach ($packages as $key => $value) {
                    if (isset($spsr_invoice['barcodes'][$value['shipment_id']]) && !empty($spsr_invoice['barcodes'][$value['shipment_id']])) {
                        $value['data'] = unserialize($value['data']);
                        $invoices[$value['shipment_id']]['packages'][$value['item_id']] = $value;
                        $invoices[$value['shipment_id']]['packages'][$value['item_id']]['data']['barcode'] = $spsr_invoice['barcodes'][$value['shipment_id']][$value['item_id']];
                        $invoices[$value['shipment_id']]['packages'][$value['item_id']]['barcode'] = $spsr_invoice['barcodes'][$value['shipment_id']][$value['item_id']];
                    }
                }

                if (!empty($invoices) && ($login)) {
                    $total_weight = 0;
                    $total_pieces_count = 0;
                    $total_invoices_cost = 0;
                    $total_invoices_cost_ins = 0;
                    $total_invoices_cost_val = 0;
                    $total_invoices_cost_cod = 0;

                    $invoices_xml = array();
                    $save_data = array(
                        'packages' => array(),
                    );
                    $sipper_data = array();

                    $address = explode('|', $spsr_invoice['sbor_addr']);
                    $sboraddr_id = $address[0];
                    $sboraddr_owner_id = $address[1];

                    $addr_list = RusSpsr::WAGetAddrList(8);
                    foreach ($addr_list as $addr) {
                        if ($addr['SborAddr_ID'] == $sboraddr_id && $addr['SborAddr_Owner_ID'] == $sboraddr_owner_id ) {
                            $sipper_data = $addr;
                        }
                    }

                    if (empty($sipper_data)) {
                        return array(CONTROLLER_STATUS_REDIRECT, "spsr_addr.manage");
                    }

                    $_sipper_data = RusSpsr::WAGetCities(array('city' => $sipper_data['CityName1']));
                    $shipper_for_xml = array(
                        'PostCode' => $sipper_data['PostCode'],
                        'Region' => $_sipper_data['RegionName'],
                        'City' => $sipper_data['CityName1'],
                        'Address' => $sipper_data['Address'],
                        'CompanyName' => $sipper_data['Organization'],
                        'ContactName' => $sipper_data['FIO'],
                    );
                    $shipper_xml = RusSpsr::arraySimpleXml('Shipper', $shipper_for_xml);

                    $city_data = RusSpsr::WAGetCities(array('city' => $order_info['s_city'], 'country' => $order_info['s_country']));
                    $country_name = fn_get_country_name($order_info['s_country'], 'ru');

                    foreach ($invoices as $invoice_key => $invoice) {
                        if (isset($spsr_invoice['barcodes'][$invoice_key]) && !empty($spsr_invoice['barcodes'][$invoice_key])) {
                            $total_weight += $invoice['weight'];
                            $shipment_id = $invoice['shipment']['shipment_id'];
                            $service_params = $invoice['service_params'];
                            $additional_params = $service_params['service_params'];

                            $receiver_for_xml = array(
                                'PostCode' => $order_info['s_zipcode'],
                                'Country' => $country_name,
                                'Region' => $city_data['RegionName'],
                                'City' => $city_data['CityName'] ,
                                'Address' => $order_info['s_address'] ,
                                'CompanyName' => $order_info['company'], 
                                'ContactName' => $order_info['s_firstname'] . ' ' . $order_info['s_lastname'],
                                'Phone' => $order_info['s_phone'],
                                'Comment' => $invoice['shipment']['comments'],
                                'Email' => $order_info['email'],
                                'ConsigneeCollect' => $shipping_data[$invoice_key]['service_params']['to_be_called_for'],
                            );
                            $receiver_xml = RusSpsr::arraySimpleXml('Receiver', $receiver_for_xml);

                            $additional_services_for_xml = array(
                                'COD' => $additional_params['cod'],
                                'PartDelivery' => $additional_params['part_delivery'],
                                'CheckContents' => $additional_params['check_contents'],
                                'Verify' => $additional_params['verify'],
                                'ReturnDoc' => $additional_params['return_doc'],
                                'TryOn' => $additional_params['try_on'],
                                'ByHand' => $additional_params['by_hand'],
                                'PaidByReceiver' => $additional_params['paid_by_receiver'],
                                'AgreedDelivery' => $additional_params['agreed_delivery'],
                                'IDC' => $additional_params['idc'],
                            );
                            $additional_services_xml = RusSpsr::arraySimpleXml('AdditionalServices', $additional_services_for_xml);

                            if ($additional_params['sms_to_shipper'] == 1 && !empty($sipper_data['Phone'])) {         
                                $sms_for_xml['SMStoShipper'] = $additional_params['sms_to_shipper'];
                                $sms_for_xml['SMSNumberShipper'] = trim($sipper_data['Phone']);
                            }

                            if ($additional_params['sms_to_receiver'] == 1 && !empty($order_info['s_phone'])) {
                                $sms_for_xml['SMStoReceiver'] = $additional_params['sms_to_receiver'];
                                $sms_for_xml['SMSNumberReceiver'] = trim($order_info['s_phone']);           
                            }

                            if (isset($sms_for_xml) && !empty($sms_for_xml)) {
                                $sms_xml = RusSpsr::arraySimpleXml('SMS', $sms_for_xml);
                            }

                            $packages = $invoice['packages'];
                            asort($packages);
                            $save_data['packages'] = fn_array_merge($save_data['packages'], $packages);

                            list($pieces_xml, $invoice_full_desc) = RusSpsr::piecesXml($packages);

                            $total_pieces_count = $total_pieces_count + count($packages);

                            if ($additional_params['dues_order'] == '0') {
                                $spsr_invoice['pick_up_type'] = 'W';      
                            } else {
                                $spsr_invoice['pick_up_type'] = 'C';      
                            }

                            if ($additional_params['insurance_type'] == 'INS') {
                                $total_invoices_cost_ins += $invoice['cost'];
                            }

                            if ($additional_params['insurance_type'] == 'VAL') {
                                $total_invoices_cost_val += $invoice['cost'];
                            }

                            if (!empty($spsr_invoice['delivery_date'])) {
                                $spsr_invoice['delivery_date'] = date('Y-m-d' , fn_parse_date($spsr_invoice['delivery_date'])) . 'T00:00:00.000';   
                            }

                            $invoice_for_xml = array(
                                'Action' => "N",
                                'ShipRefNum' => $invoice['ship_ref_num'],
                                'PickUpType' => $spsr_invoice['pick_up_type'],
                                'ProductCode' => $spsr_invoice['invoice_product_code'],
                                'FullDescription' => fn_html_escape($invoice_full_desc),
                                'PiecesCount' => count($packages),
                                'DeliveryDate' => $spsr_invoice['delivery_date'],
                                'DeliveryTime' => $spsr_invoice['delivery_time'],
                                'InsuranceType' => $additional_params['insurance_type'],
                                'InsuranceSum' => $invoice['cost'],
                            );

                            if ($additional_params['cod'] == 1){
                                $invoice_for_xml['CODGoodsSum'] = $invoice['cost'] + $invoice['invoice_shipping_cost']; 
                                $invoice_for_xml['CODDeliverySum'] = $invoice['invoice_shipping_cost'];
                                $total_invoices_cost_cod += $invoice_for_xml['CODGoodsSum']; 
                            }
                            $invoice_xml = RusSpsr::invoiceXml($invoice_for_xml , $pieces_xml , $shipper_xml , $receiver_xml , $additional_services_xml , $sms_xml);

                            $save_data['invoices'][$shipment_id] = $invoice;
                            $save_data['invoices'][$shipment_id]['for_xml'] = $invoice_for_xml;
                            $save_data['invoices'][$shipment_id]['shipper'] = $shipper_for_xml;
                            $save_data['invoices'][$shipment_id]['receiver_xml'] = $receiver_for_xml;
                            $save_data['invoices'][$shipment_id]['invoice_for_xml'] = $invoice_for_xml;

                            $invoices_xml = array_merge($invoices_xml, $invoice_xml);
                        } else {
                            unset($invoices[$invoice_key]);
                        }
                    }
                }

                if (!empty($invoices)) {
                    $general_for_xml = array(
                        'ContractNumber' => RusSpsr::$icn,
                        'TotalShipments' => count($invoices),
                        'TotalInsurance' => $total_invoices_cost_ins,
                        'TotalDeclared' => $total_invoices_cost_val,
                        'TotalPieces' => $total_pieces_count,
                        'TotalWeight' => $total_weight,
                    );

                    if ($additional_params['cod'] == 1) {
                        $general_for_xml['TotalCOD'] = $total_invoices_cost_cod; 
                    }

                    $save_data['order_info'] = array(
                        'total' => $order_info['total'],
                        'subtotal' => $order_info['subtotal'],
                        'discount' => $order_info['discount'],
                        'subtotal_discount' => $order_info['subtotal_discount'],
                        'shipping_ids' => $order_info['shipping_ids'],
                        'shipping_cost' => $order_info['shipping_cost'],
                        'timestamp' => $order_info['timestamp'],
                    );
                    $save_data['general'] = $general_for_xml;
                    $save_data['service_params'] = $service_params;
                    $save_data['params'] = $spsr_invoice;

                    $general_xml = RusSpsr::generalXml($general_for_xml, $invoices_xml);
                    list($session_info, $g_xml) = RusSpsr::WANewInvoicesByFile($general_xml);

                    if (!empty($session_info)) {
                        $register_id = RusSpsr::saveSesInvoices($params['order_id'], $session_info, $save_data, $g_xml);
                    } else {
                        fn_set_notification('E', __('error'),  RusSpsr::$last_error);
                    }

                    if (!empty($register_id)) {
                        fn_set_notification('N', __('notice'), __('shippings.spsr.register_save'));
                    } else {
                        fn_set_notification('E', __('notice'), __('shippings.spsr.not_register_save'));
                    }
                }

                $error_logout = RusSpsr::WALogout();
                if (!$error_logout) {
                    fn_set_notification('E', __('notice'), RusSpsr::$last_error);
                }
            }
        }

    } elseif ($mode == 'spsr_check_session') {
        $spsr_check_sessions = $params['spsr_check_session'];
        RusSpsr::WALogin();

        foreach ($spsr_check_sessions as $spsr_check_session) {
            $session_info = array(
                'session_id' => $spsr_check_session['session_id'],
                'session_owner_id' => $spsr_check_session['session_owner_id'],
            );

            $result = RusSpsr::WAInvSessionInfo($session_info);

            if (!empty($result['invoices'])) {
                foreach ($result['invoices'] as $invoice) {
                    $data = array(
                        'register_id' => $spsr_check_session['register_id'],
                        'order_id' => $params['order_id'],
                        'ship_ref_num' => $invoice['GCNumber'],
                        'invoice_number' => $invoice['InvoiceNumber'],
                        'timestamp' => TIME,
                        'barcodes' => $invoice['Barcodes'],
                        'client_barcodes' => $invoice['ClientBarcodes'],
                    );

                    db_query('UPDATE ?:rus_spsr_invoices SET ?u WHERE order_id = ?i AND ship_ref_num = ?s ', $data, $params['order_id'], $invoice['GCNumber']);

                    $shipment_data = array(
                        'tracking_number' => $invoice['InvoiceNumber'],
                    );
                    $a = explode('I', $invoice['GCNumber']);
                    $shipment_id = (!empty($a[1])) ? $a[1] : 0;
                    db_query('UPDATE ?:shipments SET ?u WHERE shipment_id = ?i', $shipment_data, $shipment_id);
                }

                $save_data = array(
                    'status' => 'I',
                );
                db_query('UPDATE ?:rus_spsr_register SET ?u WHERE register_id = ?i', $save_data, $spsr_check_session['register_id']);

                if (!empty($result['message'])) {
                    fn_set_notification('E', __('notice'), $result['message']);
                }

            } else {
                fn_set_notification('E', __('notice'), $result['message']);
            }

            if (!empty($result['code_message']) && $result['code_message'] == 'EMP') {
                $data_register = db_get_row("SELECT * FROM ?:rus_spsr_register WHERE order_id = ?i AND session_id = ?i AND session_owner_id = ?i", $params['order_id'], $spsr_check_session['session_id'], $spsr_check_session['session_owner_id']);
                $data_xml = str_replace('Action="N"', 'Action="U"', $data_register['data_xml']);

                list($session, $g_xml) = RusSpsr::WANewInvoicesByFile($data_xml);
                if (!empty($session)) {
                    db_query('UPDATE ?:rus_spsr_register SET session_id = ?i, session_owner_id = ?i WHERE order_id = ?i AND register_id = ?i', $session['session_id'], $session['session_owner_id'], $data_register['order_id'], $data_register['register_id']);
                } else {
                    fn_set_notification('E', __('error'),  RusSpsr::$last_error);
                }
            }
        }

        RusSpsr::WALogout();

    } elseif ($mode == 'bind_order_to_invoice') {
        $spsr_bind = $_REQUEST['spsr_bind'];
        $array = explode('||', $spsr_bind['active_courier']);
        $courier = array(
            'order_id' => $array[0],
            'order_owner_id' => $array[1],
            'courier_key' => $array[2],
        );

        $login = RusSpsr::WALogin();
        if (!empty($spsr_bind['invoices']) && $login) {
            foreach ($spsr_bind['invoices'] as $key => $invoice) {
                $result = RusSpsr::WABindOrderToInvoice($invoice, $courier);

                if ($result) {
                    $save = array(
                        'courier_key' => $courier['courier_key'], 
                        'courier_id' => $courier['order_id'], 
                        'courier_owner_id' => $courier['order_owner_id'], 
                    );
                    db_query('UPDATE ?:rus_spsr_invoices SET ?u WHERE invoice_number = ?i',$save, $invoice);
                } else {
                    fn_set_notification('E', __('notice'), RusSpsr::$last_error);
                }
            }
        }
        RusSpsr::WALogout();
    }

    if (!empty($selected_section)) {
        return array(CONTROLLER_STATUS_REDIRECT, "orders.details?order_id={$_REQUEST['order_id']}&selected_section={$selected_section}");
    }
}

if ($mode == 'details') {
    $params = $_REQUEST;
    $spsr_settings = Registry::get('addons.rus_spsr');
    $order_info = Tygh::$app['view']->getTemplateVars('order_info');
    $new_invoice = array();
    $f_spsr = 0;
    $spsr_register = 0;

    list($all_shipments) = fn_get_shipments_info(array('order_id' => $params['order_id'], 'advanced_info' => true));
    if (!empty($all_shipments)) {
        $spsr_shipments = $shipping_data = array();

        foreach ($all_shipments as $key => $_shipment) {
            if ($_shipment['carrier'] == 'spsr') {
                $spsr_shipments[$_shipment['shipment_id']] = $_shipment;
            }
        }

        $login = RusSpsr::WALogin();
        if (!empty($spsr_shipments) && ($login)) {
            $total = array(
                'packages_count' => 0,
                'shipping_cost' => 0,
                'cost' => 0,
                'amount' => 0,
            );

            $spsr_invoice_info = array();
            $register_status = 'N';
            $registers = db_get_array("SELECT register_id, order_id, session_id, session_owner_id, timestamp, status FROM ?:rus_spsr_register WHERE order_id = ?i ", $order_info['order_id']);
            if (!empty($registers)) {
                foreach ($registers as $register_id => $register) {
                    $spsr_invoices = db_get_array("SELECT * FROM ?:rus_spsr_invoices WHERE order_id = ?i AND register_id = ?i ", $order_info['order_id'], $register['register_id']);
                    if (!empty($spsr_invoices)) {
                        $_invoices = array();

                        foreach ($spsr_invoices as $invoice) {
                            $_invoices[] = $invoice['invoice_number'];

                            if (!empty($invoice['invoice_number'])) {
                                $spsr_invoice_info[$invoice['invoice_number']] = $invoice;
                            }
                            unset($spsr_shipments[$invoice['shipment_id']]);
                        }

                        $spsr_couriers = RusSpsr::WAGetActiveOrders();
                        Tygh::$app['view']->assign('spsr_couriers', $spsr_couriers);

                        $invoice_info = RusSpsr::WAGetInvoiceInfo($_invoices);
                        $spsr_invoice_info = fn_array_merge($invoice_info, $spsr_invoice_info);
                        if (!empty($invoice_info)) {
                            $spsr_register = 1;
                        }
                    }

                    if ($register['status'] == 'S') {
                        $register_status = 'Y';
                    }
                }

                Tygh::$app['view']->assign('spsr_invoice_info', $spsr_invoice_info);
            }
            Tygh::$app['view']->assign('spsr_register_status', $register_status);
            Tygh::$app['view']->assign('spsr_register', $spsr_register);

            foreach ($spsr_shipments as $shipment) {
                $shipping_data = fn_get_shipping_info($shipment['shipping_id'], DESCR_SL);
                $shipping_data['rate'] = $order_info['shipping_cost'];

                $invoices[$shipment['shipment_id']] = RusSpsr::preInvoiceByShipments($shipment, $order_info, $shipping_data);

                if (!empty($invoices[$shipment['shipment_id']]['spsr_tariffs'])) {
                    $packages = db_get_array("SELECT * FROM ?:rus_spsr_invoices_items WHERE order_id = ?i AND shipment_id = ?i ", $order_info['order_id'], $shipment['shipment_id']);

                    if (!empty($packages)) {
                        foreach ($packages as $value) {
                            if (empty($invoices[$value['shipment_id']]['packages_count'])) {
                                $invoices[$value['shipment_id']]['packages_count'] = 0;
                            }

                            $value['data'] = unserialize($value['data']);
                            $invoices[$value['shipment_id']]['packages'][$value['item_id']] = $value;
                            $invoices[$value['shipment_id']]['packages_count'] ++; 
                        }

                        if (!empty($invoices[$shipment['shipment_id']]['packages'])) {
                            asort($invoices[$shipment['shipment_id']]['packages']);
                        }

                        $total['packages_count'] = (!empty($invoices[$shipment['shipment_id']]['packages_count'])) ? $total['packages_count'] + $invoices[$shipment['shipment_id']]['packages_count'] : 0;
                        $total['shipping_cost'] = $total['shipping_cost'] + $invoices[$shipment['shipment_id']]['invoice_shipping_cost'];
                        $total['cost'] = $total['cost'] + $invoices[$shipment['shipment_id']]['cost'];
                        $total['amount'] = $total['amount'] + $invoices[$shipment['shipment_id']]['amount'];
                    } else {
                        $new_invoice[$shipment['shipment_id']] = $invoices[$shipment['shipment_id']];
                        unset($invoices[$shipment['shipment_id']]);
                    }
                } else {
                    unset($invoices[$shipment['shipment_id']]);
                }
            }

            $addr_list = RusSpsr::WAGetAddrList(8);

            if (!empty($total)) {
                Tygh::$app['view']->assign('spsr_total', $total);
            }

            if (!empty($invoices)) {
                Tygh::$app['view']->assign('spsr_packages', 'Y');
                Tygh::$app['view']->assign('spsr_invoices', $invoices);
                $f_spsr = 1;
            }

            if (!empty($new_invoice)) {
                Tygh::$app['view']->assign('spsr_new_invoice', 'Y');
                Tygh::$app['view']->assign('spsr_data_invoice', $new_invoice);
                $f_spsr = 1;
            }

            if (!empty($registers)) {
                Tygh::$app['view']->assign('registers', $registers);
                $f_spsr = 1;
            }

            Tygh::$app['view']->assign('addr_list', $addr_list);

            $url_invoice = RusSpsr::urlInvoice();
            Tygh::$app['view']->assign('url_invoice', $url_invoice);

            $info_barcode = array(
                'width' => $spsr_settings['width'],
                'height' => $spsr_settings['height'],
                'type' => 'C128B',
            );
            Tygh::$app['view']->assign('info_barcode', $info_barcode);

            if ($f_spsr) {
                Registry::set('navigation.tabs.rus_spsr_invoice', array (
                    'title' => __('shippings.spsr.tab_invoice'),
                    'js' => true
                ));
            }
        }
        if (!$login) {
            fn_set_notification('E', __('notice'), __('shippings.spsr.login_error') . ' : ' . RusSpsr::$last_error);
        }

        $error_logout = RusSpsr::WALogout();
        if (!$error_logout) {
            fn_set_notification('E', __('notice'), __('shippings.spsr.logout_error') . ' : ' . RusSpsr::$last_error);
        }
    }
}

if ($mode == 'spsr_barcode') {
    require(Registry::get('config.dir.addons') . 'barcode/lib/barcodegenerator/barcode.php');

    $style = BCS_ALIGN_CENTER;
    if (Registry::get('addons.barcode.text') == 'Y') {
        $style = $style + BCS_DRAW_TEXT;
    }
    if (Registry::get('addons.barcode.output') == 'png') {
        $style = $style + BCS_IMAGE_PNG;
    }
    if (Registry::get('addons.barcode.output') == 'jpeg') {
        $style = $style + BCS_IMAGE_JPEG;
    }

    $width = (!empty($_REQUEST['width'])) ? $_REQUEST['width'] : BCD_DEFAULT_WIDTH;
    $height = (!empty($_REQUEST['height'])) ? $_REQUEST['height'] : BCD_DEFAULT_HEIGHT;
    $id = (!empty($_REQUEST['id'])) ? $_REQUEST['id'] : '';
    $type = (!empty($_REQUEST['type'])) ? $_REQUEST['type'] : '';
    $xres = 1;
    $font = 3;
    $prefix = 'spsr';

    $objects = array (
        'I25' => 'I25Object',
        'C39' => 'C39Object',
        'C128A' => 'C128AObject',
        'C128B' => 'C128BObject',
        'C128C' => 'C128CObject',
    );

    $numeric_objects = array (
        'I25' => true,
        'C128C' => true,
    );

    if (!empty($objects[$type])) {
        if (!empty($numeric_objects[$type]) && !is_numeric($prefix)) {
            $prefix = '';
        }

        $code = $prefix . $id;
        require(Registry::get('config.dir.addons') . 'barcode/lib/barcodegenerator/' . fn_strtolower($objects[$type]) . '.php');

        $obj = new $objects[$type]($width, $height, $style, $code);
        if ($obj) {
            $obj->SetFont($font);
            $obj->DrawObject($xres);
            $obj->FlushObject();
            $obj->DestroyObject();
            unset($obj);
        }

    } else {
        __DEBUG__("Need bar code type ex. C39");
    }

    exit;
}

function fn_spsr_pre_check_invoice_create($order_id, $section, $spsr_shipments = array()) {
    $shipping = array();

    if (empty($order_id)) {
        return array(CONTROLLER_STATUS_REDIRECT, "orders.manage");
    }

    $order_info = fn_get_order_info($order_id, false, true, true, true);

    if (!empty($order_info)) {
        list($_shipments) = fn_get_shipments_info(array('order_id' => $order_id, 'advanced_info' => true));

        if (!empty($_shipments)) {
            $shipments = array();
            foreach ($_shipments as $shipment){
                if ($shipment['carrier'] == 'spsr') {
                    if (empty($spsr_shipments) || !empty($spsr_shipments[$shipment['shipment_id']])) {
                        $shipments[$shipment['shipment_id']] = $shipment;
                    }
                }
            }
        }

        if (!empty($shipments)) {
            foreach ($shipments as $shipment_id => $shipment) {
                $shipping = fn_get_shipping_info($shipment['shipping_id'], DESCR_SL);
                $shipping['rate'] = $order_info['shipping_cost'];
                $invoices[$shipment_id] = RusSpsr::preInvoiceByShipments($shipment, $order_info, $shipping);
                $shipping_data[$shipment_id] = $shipping;
            }
        } else {
            return array(CONTROLLER_STATUS_REDIRECT, "orders.details?order_id={$order_id}&selected_section={$section}");
        }
    }

    return array($order_info, $invoices, $shipments, $shipping_data);
}
