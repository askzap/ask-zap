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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {

    if ($mode == 'rbx_get_currencies') {
        if (!empty($_REQUEST['merchantid'])) {

            if (!empty($_REQUEST['payment_id'])) {
                $processor_data = fn_get_processor_data((int) $_REQUEST['payment_id']);
                $url = ($processor_data['processor_params']['mode'] == 'live') ? 'https://merchant.roboxchange.com/' : 'http://test.robokassa.ru/';

                Registry::get('view')->assign('processor_params', $processor_data['processor_params']);
            } else {
                $url = 'http://test.robokassa.ru/';
            }

            $url = $url . 'WebService/Service.asmx/GetCurrencies?MerchantLogin=' . $_REQUEST['merchantid'] . '&Language=' . CART_LANGUAGE;
            $data_currencies = Http::get($url);

            $xml = @simplexml_load_string($data_currencies);
            $result = array();

            if (isset($xml->Groups->Group)) {
                foreach ($xml->Groups->Group as $group) {
                    $key = strval($group->attributes()->Description);
                    foreach ($group->Items->Currency as $currency) {
                        $sub_key = strval($currency->attributes()->Label);
                        $cur_name = strval($currency->attributes()->Name);
                        $result[$key][$sub_key] = $cur_name;
                    }
                }
            }

            Registry::get('view')->assign('rbx_currencies', $result);
            Registry::get('view')->display('views/payments/components/cc_processors/robokassa_cur_selectbox.tpl');
        }

        exit;
    }

    if (empty($_REQUEST['InvId']) || empty($_REQUEST['OutSum']) || (empty($_REQUEST['SignatureValue']) && $mode != 'cancel')) {
        die('Access denied');
    }

    $order_id = (int) $_REQUEST['InvId'];
    if ($mode == 'result') {
        $order_info = fn_get_order_info($order_id);
        $processor_data = $order_info['payment_method'];
        $crc = strtoupper(md5($_REQUEST['OutSum'] . ':' . $_REQUEST['InvId'] . ':' . $processor_data['processor_params']['password2']));
        if (strtoupper($_REQUEST['SignatureValue']) == $crc) {
            $pp_response['order_status'] = 'P';
            $pp_response['reason_text'] = __('approved');
        } else {
            $pp_response['order_status'] = 'F';
            $pp_response['reason_text'] = __('control_summ_wrong');
        }
        fn_finish_payment($order_id, $pp_response);
        die('OK' . $order_id);

    } elseif ($mode == 'return') {
        $order_info = fn_get_order_info($order_id);
        if ($order_info['status'] == 'O') {
            $pp_response = array();
            $pp_response['order_status'] = 'F';
            $pp_response['reason_text'] = __('merchant_response_was_not_received');
            $pp_response['transaction_id'] = '';
            fn_finish_payment($order_id, $pp_response);
        }
        fn_order_placement_routines('route', $order_id, false);

    } elseif ($mode == 'cancel') {
        $pp_response['order_status'] = 'N';
        $pp_response['reason_text'] = __('text_transaction_cancelled');
        fn_finish_payment($order_id, $pp_response, false);
        fn_order_placement_routines('route', $order_id);
    }

} else {
    $total = fn_rus_pay_format_price($order_info['total'], $processor_data['processor_params']['currency']);

    $crc = strtoupper(md5($processor_data['processor_params']['merchantid'] . ':' . $total. ':' . $order_id . ':' . $processor_data['processor_params']['password1']));
    $url = ($processor_data['processor_params']['mode'] == 'live') ? 'https://merchant.roboxchange.com/Index.aspx' : 'http://test.robokassa.ru/Index.aspx';

    $post_data = array(
        'MrchLogin' => $processor_data['processor_params']['merchantid'],
        'OutSum' => $total,
        'InvId' => $order_id,
        'Desc' => $processor_data['processor_params']['details'],
        'SignatureValue' => $crc,
        'Culture' => CART_LANGUAGE,
        'IncCurrLabel' => $processor_data['processor_params']['payment_method']
    );

    fn_create_payment_form($url, $post_data, 'Robokassa server');
}

exit;



