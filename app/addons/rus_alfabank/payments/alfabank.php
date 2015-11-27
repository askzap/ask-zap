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

use Tygh\Payments\Processors\Alfabank;

if (defined('PAYMENT_NOTIFICATION')) {

    $order_id = 0;
    if (!empty($_REQUEST['ordernumber'])) {
        $order_id = $_REQUEST['ordernumber'];
    }

    $order_info = fn_get_order_info($order_id);
    if (empty($processor_data) && !empty($order_info)) {
        $processor_data = fn_get_processor_data($order_info['payment_id']);
    }

    if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
        Alfabank::writeLog($_REQUEST, 'alfabank_request.log');
    }

    if (!empty($order_info) && ($mode == 'return' || $mode == 'error')) {

        $pp_response = array(
            'order_status' => 'F'
        );

        if ($order_info['payment_info']['transaction_id'] != $_REQUEST['orderId']) {
            $pp_response['reason_text'] = __("addons.rus_alfabank.wrong_transaction_id");

        } else {
            $sberbank = new Alfabank($processor_data);
            $response = $sberbank->getOrderExtended($order_info['payment_info']['transaction_id']);

            if ($sberbank->isError()) {
                $pp_response = array(
                    'order_status' => 'F',
                    'reason_text' => $response['errorMessage']
                );

            } elseif ($response['orderStatus'] == 2) {

                if ($response['amount'] == ($order_info['total'] * 100)) {
                    $pp_response = array(
                        'order_status' => 'P',
                        'card_number' => $response['cardAuthInfo']['pan'],
                        'cardholder_name' => $response['cardAuthInfo']['cardholderName'],
                        'expiry_month' => substr($response['cardAuthInfo']['expiration'], 0, 4),
                        'expiry_year' => substr($response['cardAuthInfo']['expiration'], 0, -2),
                        'bank' => $response['bankInfo']['bankName'],
                        'ip_address' => $response['ip'],
                    );
                } else {
                    $pp_response['reason_text'] = __("addons.rus_alfabank.wrong_amount");
                }

            } else {
                $pp_response = array(
                    'order_status' => 'F',
                    'reason_text' => $response['actionCodeDescription'],
                    'ip_address' => $response['ip'],
                );
            }
        }

        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('route', $order_id, false);
    }

    exit;

} else {
    $alfabank = new Alfabank($processor_data);

    $response = $alfabank->register($order_info);

    if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
        Alfabank::writeLog($response, 'alfabank.log');
    }

    if (!$alfabank->isError()) {

        $pp_response = array(
            'transaction_id' => $response['orderId']
        );

        fn_update_order_payment_info($order_id, $pp_response);
        fn_create_payment_form($response['formUrl'], array(), 'Alfabank', true, 'GET');

    } else {
        $pp_response['order_status'] = 'F';
        $pp_response['reason_text'] = $alfabank->getErrorText();

        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('route', $order_id, false);
    }

}

