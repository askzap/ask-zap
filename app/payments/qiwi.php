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

// rus_build_pack

use Tygh\Registry;

if (defined('PAYMENT_NOTIFICATION')) {
    die('Access denied');
}

if (!defined('BOOTSTRAP')) {
    if (!empty($_REQUEST['parameter']) && $_REQUEST['parameter'] == 'update') {
        require './init_payment.php';

        class Response
        {
            public $updateBillResult;
        }
        class Param
        {
            public $login;
            public $password;
            public $txn;
            public $status;
        }

        class UpdateServer
        {
            function updateBill($param)
            {
                if (!is_object($param)) {
                    return false;
                }

                $order_info = fn_get_order_info($param->txn, false, true, true, true);
                $temp = '';
                if (!empty($order_info['payment_method']['processor_params']['passwd']) && !empty($order_info['payment_method']['processor_params']['login'])) {
                    $txn = fn_convert_encoding('utf-8', 'windows-1251', $param->txn);
                    $password = fn_convert_encoding('utf-8', 'windows-1251', $order_info['payment_method']['processor_params']['passwd']);
                    $crc = strtoupper(md5($txn . strtoupper(md5($password))));

                    if ($param->login == $order_info['payment_method']['processor_params']['login'] && $param->password == $crc) {
                        $pp_response = array();
                        $status = 'qiwi_order_status_' . $param->status;
                        if ($param->status == 60) {
                            $pp_response['order_status'] = 'P';
                        } elseif ($param->status >= 50 && $param->status < 60) {
                            $pp_response['order_status'] = 'O';
                        } else {
                            $pp_response['order_status'] = 'F';
                        }

                        $pp_response['reason_text'] = __($status);
                        fn_finish_payment($param->txn, $pp_response);

                        $temp = new Response();
                        $temp->updateBillResult = 0;
                    }
                }

                return $temp;
            }
        }
        $server = new SoapServer('./qiwi_files/IShopClientWS.wsdl', array('classmap' => array('tns:updateBill' => 'Param', 'tns:updateBillResponse' => 'Response')));
        $server->setClass('UpdateServer');
        $server->handle();
    } else {
        die('Access denied');
    }
} else {
    include(Registry::get('config.dir.payments') . 'qiwi_files/IShopServerWSService.php');
    define('TRACE', 0);
    $_location = (!empty($processor_data['processor_params']['location'])) ? $processor_data['processor_params']['location'] : 'http://ishop.qiwi.ru/services/ishop';

    $dame_format = ($_location == "http://ishop.qiwi.ru/services/ishop") ? 'd.m.Y H:i:s' : 'Y.m.d H:i:s';

    $service = new IShopServerWSService(Registry::get('config.dir.payments') . 'qiwi_files/IShopServerWS.wsdl', array('location' => $_location, 'trace' => TRACE));

    include(Registry::get('config.dir.payments') . 'qiwi_files/qiwi_func.php');

    $_order_id = $order_info['repaid'] ? ($order_info['order_id'] . '_' . $order_info['repaid']) : $order_info['order_id'];
    $_order_total = fn_format_rate_value($order_info['total'], 'F', 2, '.', '', '');
    $_lifetime = date($dame_format, time() + ($processor_data['processor_params']['lifetime'] * 60));

    $data = array(
        'login' => $processor_data['processor_params']['login'],
        'password' => $processor_data['processor_params']['passwd'],
        'phone' => fn_qiwi_convert_phone($order_info['payment_info']['phone']),
        'amount' => $_order_total,
        'txn_id' => $_order_id,
        'comment' => (!empty($order_info['notice']) ? $order_info['notice'] : ''),
        'lifetime' => $_lifetime,
        'alarm' => $processor_data['processor_params']['alarm'],
        'create' => 1
    );

    $result = createBill($data, $service);

    $status = 'qiwi_result_status_' . $result;

    if ($result == 0) {
        $pp_response['order_status'] = 'O';
    } else {
        $pp_response['order_status'] = 'F';
    }

    $pp_response['reason_text'] = __($status);
    fn_finish_payment($order_id, $pp_response);
    $idata = array (
        'order_id' => $_order_id,
        'type' => 'S',
        'data' => TIME
    );
    db_query("REPLACE INTO ?:order_data ?e", $idata);
    fn_order_placement_routines('route', $order_id, false);
}

function fn_qiwi_convert_phone($phone)
{
    $phone = str_replace(array('+', ' ', '(', ')', '-'), '', $phone);

    if (strlen($phone) > 10) {
        $phone = substr($phone, -10);
    }

    return $phone;
}
