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

use Tygh\Http;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {
    if (!empty($_REQUEST['pd_order_id'])) {
        $order_id = (int) $_REQUEST['pd_order_id'];
    } else {
        die('Order id not found.');
    }

    if ($mode != 'process') {
        $pp_response = array(
            'order_status' => 'F',
            'reason_text' => (($mode == 'decline') ? __('pd_declined') : __('pd_reverse'))
        );
    } else {
        $order_info = fn_get_order_info($order_id);
        $status_url = ($order_info['payment_method']['processor_params']['test'] == 'Y') ? 'https://pg-test.platidoma.ru/status.php' : 'https://pg.platidoma.ru/status.php';
        $pd_shop_id = $order_info['payment_method']['processor_params']['pd_shop_id'];
        $pd_login = $order_info['payment_method']['processor_params']['pd_login'];
        $pd_gate_password = $order_info['payment_method']['processor_params']['pd_gate_password'];

        $return = fn_get_pd_status($_REQUEST, $pd_shop_id, $pd_login, $pd_gate_password, $status_url);

        preg_match('/<type>(.*)<\/type>/', $return, $type);
        preg_match('/<code>(.*)<\/code>/', $return, $code);
        preg_match('/<description>(.*)<\/description>/', $return, $description);
        preg_match('/<pd_amount>(.*)<\/pd_amount>/', $return, $amount);
        preg_match('/<pd_status>(.*)<\/pd_status>/', $return, $status);
        preg_match('/<pd_order_id>(.*)<\/pd_order_id>/', $return, $pd_order_id);
        preg_match('/<pd_trans_id>(.*)<\/pd_trans_id>/', $return, $pd_trans_id);
        preg_match('/<pd_rnd>(.*)<\/pd_rnd>/', $return, $pd_rnd);
        preg_match('/<pd_sign>(.*)<\/pd_sign>/', $return, $pd_sign);

        $pp_response['reason_text'] = __("pd_$status[1]");
        if ($type && $type[1] == 'error') {
            $pp_response['order_status'] = 'F';
        } else {
            $sign = md5($pd_shop_id . ':' . $pd_login . ':' . $pd_gate_password . ':' . $pd_rnd[1] . ':' . $pd_trans_id[1] . ':' . $_REQUEST['pd_order_id'] . ':' . $amount[1]);

            if ($status[1] == 'paid' && $order_id == (int) $pd_order_id[1] && $sign == $pd_sign[1]) {
                $pp_response['order_status'] = 'P';
                $pp_response['transaction_id'] = $pd_trans_id[1];
            } else {
                $pp_response['order_status'] = 'F';
            }
        }
    }

    if (fn_check_payment_script('pay_at_home.php', $order_id)) {
        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('route', $order_id, false);
    }

    exit;

} else {
    $processor_url = ($processor_data['processor_params']['test'] == 'Y') ? 'https://pg-test.platidoma.ru/payment.php' : 'https://pg.platidoma.ru/payment.php';
    $order_total = fn_format_rate_value($order_info['total'], 'F', 2, '.', '');

    $pd_shop_id = $processor_data['processor_params']['pd_shop_id'];
    $pd_login = $processor_data['processor_params']['pd_login'];
    $pd_gate_password = $processor_data['processor_params']['pd_gate_password'];
    $pd_rnd = time();

    $order_id = $order_info['order_id'];
    $_order_id = $order_info['repaid'] ? ($order_id . '_' . $order_info['repaid']) : $order_id;

    $post = array();
    $post['pd_shop_id'] = $pd_shop_id;
    $post['pd_login'] = $pd_login;
    $post['pd_amount'] = $order_total;
    $post['pd_order_id'] = $_order_id;
    $post['pd_rnd'] = $pd_rnd;
    $post['pd_sign'] = md5($pd_shop_id . ':' . $pd_login . ':' . $pd_gate_password . ':' . $pd_rnd . ':' . $order_total);

    fn_create_payment_form($processor_url, $post, __('pay_at_home'));
}

function fn_get_pd_status($pd, $pd_shop_id, $pd_login, $pd_gate_password, $status_url)
{
    $pd['pd_sign'] = md5($pd_shop_id . ':' . $pd_login . ':' . $pd_gate_password . ':' . $pd['pd_rnd'] . ':' . $pd['pd_trans_id'] . ':' . $pd['pd_order_id'] . ':' . $pd['pd_amount']);
    $post = array();
    $post['pd_shop_id'] = $pd_shop_id;
    $post['pd_trans_id'] = $pd['pd_trans_id'];
    $post['pd_rnd'] = $pd['pd_rnd'];
    $post['pd_sign'] = $pd['pd_sign'];

    return Http::post($status_url, $post);
}
