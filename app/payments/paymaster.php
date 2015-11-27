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

if (defined('PAYMENT_NOTIFICATION')) {

    $order_id = !empty($_REQUEST['order_id']) ? $_REQUEST['order_id'] : 0;

    if ($mode == 'notify') {

        $payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $order_id);
        $processor_data = fn_get_payment_method_data($payment_id);

        $secure_string = $_REQUEST['LMI_MERCHANT_ID'].';'.$_REQUEST['order_id'].';'.$_REQUEST['LMI_SYS_PAYMENT_ID'].';'.$_REQUEST['LMI_SYS_PAYMENT_DATE'].';'.$_REQUEST['LMI_PAYMENT_AMOUNT'].';'.$_REQUEST['LMI_CURRENCY'].';'.$_REQUEST['LMI_PAID_AMOUNT'].';'.$_REQUEST['LMI_PAID_CURRENCY'].';'.$_REQUEST['LMI_PAYMENT_SYSTEM'].';'.$_REQUEST['LMI_SIM_MODE'].';'.$processor_data['processor_params']['paymaster_key'];

        $secret_hash = '';
        if (empty($processor_data['processor_params']['sing_algo']) || $processor_data['processor_params']['sing_algo'] == 'md5') {
            $secret_hash = base64_encode(md5($secure_string, true));
        } elseif ($processor_data['processor_params']['sing_algo'] == 'sha256') {
            $secret_hash = base64_encode(hash('sha256', $secure_string, true));
        }

        if ($_REQUEST['LMI_HASH'] == $secret_hash) {
            $pp_response = array(
                'order_status' => 'P'
            );

            $pp_response["transaction_id"] = $_REQUEST['LMI_SYS_PAYMENT_ID'];

            if (fn_check_payment_script('paymaster.php', $order_id)) {
                fn_finish_payment($order_id, $pp_response);
            }

        } else {
            $order_id = $_REQUEST['order_id'];

            $pp_response['order_status'] = 'N';
            $pp_response["reason_text"] = __('text_transaction_cancelled');

            if (fn_check_payment_script('paymaster.php', $order_id)) {
                fn_finish_payment($order_id, $pp_response, false);
            }
        }

    } elseif ($mode == 'return') {

        if (fn_check_payment_script('paymaster.php', $order_id)) {
            fn_order_placement_routines('route', $order_id);
        }

    } elseif ($mode == 'invoice') {

        echo "YES";

    } elseif ($mode == 'error') {

        $pp_response['order_status'] = 'N';
        $pp_response["reason_text"] = __('text_transaction_cancelled');

        if (fn_check_payment_script('paymaster.php', $order_id)) {
            fn_finish_payment($order_id, $pp_response, false);
        }

        fn_order_placement_routines('route', $order_id);
    }

} else {

    if (!defined('BOOTSTRAP')) { die('Access denied'); }

    $post_address = "https://paymaster.ru/Payment/Init";

    $payment_desc = '';
    if (is_array($order_info['products'])) {
        foreach ($order_info['products'] as $k => $v) {
            $payment_desc .= $order_info['products'][$k]['product'] . ' / ';
        }
    }

    $payment_desc = base64_encode ($payment_desc);

    $customer_phone = '';
    if (!empty($order_info['phone'])) {
        $customer_phone = $order_info['phone'];

    } elseif (!empty($order_info['b_phone'])) {
        $customer_phone = $order_info['b_phone'];

    } elseif (!empty($order_info['s_phone'])) {
        $customer_phone = $order_info['s_phone'];
    }

    if (empty($processor_data['processor_params']['currency'])) {
        $processor_data['processor_params']['currency'] = 'RUB';
    }

    $post_data = array(
        'LMI_MERCHANT_ID' => $processor_data['processor_params']['merchant_id'],
        'LMI_PAYMENT_AMOUNT' => fn_paymaster_get_sum($order_info, $processor_data),

        'LMI_CURRENCY' => $processor_data['processor_params']['currency'],

        'LMI_PAYMENT_NO' => $order_info['order_id'],
        'LMI_PAYMENT_DESC_BASE64' => $payment_desc,

        'LMI_INVOICE_CONFIRMATION_URL' => fn_url("payment_notification.invoice?payment=paymaster&order_id=$order_id", AREA),
        'LMI_PAYMENT_NOTIFICATION_URL' => fn_url("payment_notification.notify?payment=paymaster&order_id=$order_id", AREA),

        'LMI_SUCCESS_URL' => fn_url("payment_notification.return?payment=paymaster&order_id=$order_id", AREA),
        'LMI_FAILURE_URL' => fn_url("payment_notification.error?payment=paymaster&order_id=$order_id", AREA),

        'LMI_PAYER_PHONE_NUMBER' => $customer_phone,
        'LMI_PAYER_EMAIL' => $order_info['email'],
    );

    if (!empty($processor_data['processor_params']['payment_method'])) {
        $post_data['LMI_PAYMENT_METHOD'] = $processor_data['processor_params']['payment_method'];
    }

    fn_create_payment_form($post_address, $post_data, 'Paymaster', false);
}

function fn_paymaster_get_sum($order_info, $processor_data)
{
    $price = $order_info['total'];

    if (CART_PRIMARY_CURRENCY != $processor_data['processor_params']['currency']) {
        $currencies = Registry::get('currencies');
        $currency = $currencies[$processor_data['processor_params']['currency']];
        $price = fn_format_rate_value($price, 'F', $currency['decimals'], '.', '', $currency['coefficient']);
    }

    return sprintf('%.2f', $price);
}

exit;
