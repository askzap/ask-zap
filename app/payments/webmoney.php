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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {

    if ($mode == 'result') {

        if (isset($_REQUEST['LMI_PREREQUEST']) && ($_REQUEST['LMI_PREREQUEST'] == 1)) {

            $order_id = $_REQUEST['LMI_PAYMENT_NO'];

            $order_info = fn_get_order_info($order_id);
            $processor_data = fn_get_payment_method_data($order_info['payment_id']);

            $payment_amount = fn_webmoney_get_price_by_payee_purse_type($order_info['total'], $processor_data['processor_params']['lmi_payee_purse']);

            $prerequest_success = true;
            $reason_text = '';

            if (!$payment_amount) {
                $prerequest_success = false;
                $reason_text .= __('text_unsupported_currency');
            } elseif ($_REQUEST['LMI_PAYMENT_AMOUNT'] != $payment_amount) {
                $prerequest_success = false;
                $reason_text .= __('wm_rt_differ_payment_amount_in_prerequest');
            }

            if ($_REQUEST['LMI_PAYEE_PURSE'] != $processor_data['processor_params']['lmi_payee_purse']) {
                $prerequest_success = false;
                $reason_text .= __('wm_rt_differ_payee_purse_in_prerequest');
            }
            if ($_REQUEST['LMI_MODE'] != $processor_data['processor_params']['lmi_mode']) {
                $prerequest_success = false;
                $reason_text .= __('wm_rt_differ_mode_in_prerequest');
            }

            $pp_response = array();
            if ($prerequest_success) {
                $pp_response['order_status'] = 'O';
                $pp_response['lmi_payer_wm'] = $_REQUEST['LMI_PAYER_WM'];
                $pp_response['lmi_payer_purse'] = $_REQUEST['LMI_PAYER_PURSE'];
                $pp_response['reason_text'] = '';
                echo 'YES';
            } else {
                $pp_response['order_status'] = 'F';
                $pp_response['reason_text'] = $reason_text;
            }
            fn_update_order_payment_info($order_id, $pp_response);
            exit;

        } else {

            $order_id = $_REQUEST['LMI_PAYMENT_NO'];

            $order_info = fn_get_order_info($order_id);
            $processor_data = fn_get_payment_method_data($order_info['payment_id']);

            if (!empty($order_info['payment_info']['order_status']) && ($order_info['payment_info']['order_status'] == 'F')) {
                exit;
            }

            $payment_amount = fn_webmoney_get_price_by_payee_purse_type($order_info['total'], $processor_data['processor_params']['lmi_payee_purse']);

            $hash_str = $processor_data['processor_params']['lmi_payee_purse'].$payment_amount.$order_id.$processor_data['processor_params']['lmi_mode'].$_REQUEST['LMI_SYS_INVS_NO'].$_REQUEST['LMI_SYS_TRANS_NO'].$_REQUEST['LMI_SYS_TRANS_DATE'].$processor_data['processor_params']['lmi_secret_key'].$_REQUEST['LMI_PAYER_PURSE'].$_REQUEST['LMI_PAYER_WM'];

            if (empty($processor_data['processor_params']['sing_algo']) || $processor_data['processor_params']['sing_algo'] == 'sha256') {
                $hash = strtoupper(hash('sha256', $hash_str));
            } elseif ($processor_data['processor_params']['sing_algo'] == 'md5') {
                $hash = strtoupper(md5($hash_str));
            }

            $notification_of_payment_success = true;
            $reason_text = '';

            if (!$payment_amount) {
                $prerequest_success = false;
                $reason_text .= __('text_unsupported_currency');

            } elseif ($_REQUEST['LMI_HASH'] != $hash) {
                $notification_of_payment_success = false;
                $reason_text .= __('wm_rt_differ_hash_in_notification_request');

            } elseif ($_REQUEST['LMI_PAYMENT_AMOUNT'] != $payment_amount) {
                $notification_of_payment_success = false;
                $reason_text .= __('wm_rt_differ_payment_amount_in_notification_request');

            } elseif ($_REQUEST['LMI_PAYEE_PURSE'] != $processor_data['processor_params']['lmi_payee_purse']) {
                $notification_of_payment_success = false;
                $reason_text .= __('wm_rt_differ_payee_purse_in_notification_request');

            } elseif ($_REQUEST['LMI_MODE'] != $processor_data['processor_params']['lmi_mode']) {
                $notification_of_payment_success = false;
                $reason_text .= __('wm_rt_differ_mode_in_notification_request');
            }

            $pp_response = array(
                'lmi_sys_invs_no' => $_REQUEST['LMI_SYS_INVS_NO'],
                'lmi_sys_trans_no' => $_REQUEST['LMI_SYS_TRANS_NO'],
                'lmi_sys_trans_date' => $_REQUEST['LMI_SYS_TRANS_DATE']
            );

            if ($notification_of_payment_success) {
                $pp_response['order_status'] = 'P';
                $pp_response['paid_amount'] = $payment_amount;

            } else {
                $pp_response['order_status'] = 'F';
                $pp_response['reason_text'] = $reason_text;
            }

            fn_finish_payment($order_id, $pp_response);
            exit;
        }

    } elseif ($mode == 'success' || $mode == 'fail') {

        $order_id = $_REQUEST['LMI_PAYMENT_NO'];
        fn_order_placement_routines('route', $order_id);
    }

} else {

    $url = 'https://merchant.webmoney.ru/lmi/payment.asp';
    $payment_amount = fn_webmoney_get_price_by_payee_purse_type($order_info['total'], $processor_data['processor_params']['lmi_payee_purse']);

    if ($payment_amount == false) {
        $pp_response = array();
        $pp_response['order_status'] = 'F';
        $pp_response['reason_text'] = __('text_unsupported_currency');

        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('route', $order_id);
    }

    $payment_desc = $processor_data['processor_params']['lmi_payment_desc'] . $order_id . ($order_info['repaid'] ? "_{$order_info['repaid']}" : '');

    $post_data = array(
        'LMI_PAYMENT_AMOUNT' => $payment_amount,
        'LMI_PAYMENT_DESC' => $payment_desc,
        'LMI_PAYMENT_NO' => $order_id,
        'LMI_PAYEE_PURSE' => $processor_data['processor_params']['lmi_payee_purse'],
        'LMI_RESULT_URL' => fn_url("payment_notification.result?payment=webmoney", AREA, 'current'),
        'LMI_SUCCESS_URL' => fn_url("payment_notification.success?payment=webmoney", AREA, 'current'),
        'LMI_SUCCESS_METHOD' => 1,
        'LMI_FAIL_URL' => fn_url("payment_notification.fail?payment=webmoney", AREA, 'current'),
        'LMI_FAIL_METHOD' => 1
    );

    if ($processor_data['processor_params']['lmi_mode'] == 1) {
        $post_data['LMI_SIM_MODE'] = $processor_data['processor_params']['lmi_sim_mode'];
    }

    fn_create_payment_form($url, $post_data, 'WebMoney server', false);
}

function fn_webmoney_get_price_by_payee_purse_type ($price, $purse)
{
    $currencies = Registry::get('currencies');

    $purse_type = substr($purse, 0, 1);

    if ($purse_type == 'R') {
        $currency = 'RUB';
    } elseif ($purse_type == 'Z') {
        $currency = 'USD';
    } elseif ($purse_type == 'E') {
        $currency = 'EUR';
    } elseif ($purse_type == 'U') {
        $currency = 'UAH';
    } elseif ($purse_type == 'B') {
        $currency = 'BYR';
    } elseif ($purse_type == 'Y') {
        $currency = 'UZS';
    } else {
        return false;
    }

    if (empty($currencies[$currency])) {
        return false;
    }

    return fn_format_price($price / $currencies[$currency]['coefficient'], CART_PRIMARY_CURRENCY, 2, false);
}
