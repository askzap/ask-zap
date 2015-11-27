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

use Tygh\Payments\Processors\Qiwi;

if (defined('PAYMENT_NOTIFICATION')) {

    $order_id = 0;
    if (!empty($_REQUEST['bill_id'])) {

        $transaction_id = $_REQUEST['bill_id'];
        if (strpos($transaction_id, '_TEST_') !== false) {
            $order_id = substr($transaction_id, 6 /* length _TEST_ */);
            $test_payment = true;

        } else {
            $order_id = substr($transaction_id, 0, -4 /* length minutes . seconds */);
        }

    } else if (!empty($_REQUEST['order'])) {
        $transaction_id = $_REQUEST['order'];
        $order_id = substr($_REQUEST['order'], 0, -4);
    }

    if (empty($processor_data)) {
        $order_info = fn_get_order_info($order_id);
        $processor_data = fn_get_processor_data($order_info['payment_id']);
    }

    if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
        Qiwi::writeLog($_REQUEST, 'qiwi_request.txt');
    }

    if ($mode == 'notify') {

        if (fn_qiwi_rest_check_params($_REQUEST)) {

            $notify_status = QIWI_NOTIFY_OK;

            $qiwi = new Qiwi($processor_data);
            $response = $qiwi->getBill($transaction_id);

            if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
                Qiwi::writeLog($_REQUEST, 'qiwi_get_bill.txt');
            }

            $order_status = 'F';
            if (!empty($response['bill'])) {

                $status_request = $_REQUEST['status'];
                $status = $response['bill']['status'];

                if ($status_request != $status) {
                    $pp_response['ip'] = $_SERVER['REMOTE_ADDR'];
                    $pp_response['reason_text'] = __('addons.qiwi_rest.error_check_status_bill');

                } elseif (isset($processor_data['processor_params']['statuses'][$status])) {
                    $order_status = $processor_data['processor_params']['statuses'][$status];
                }
            }

            $pp_response['order_status'] = $order_status;
            $pp_response['transaction_id'] = $transaction_id;

            if (!empty($status)) {
                $pp_response['reason_text'] = fn_qiwi_rest_get_bill_status($status);
            }

            if (fn_check_payment_script('qiwi_rest.php', $order_id)) {

                if ($processor_data['processor_params']['invoice_type'] == 'create') {
                    fn_update_order_payment_info($order_id, $pp_response);
                    fn_change_order_status($order_id, $pp_response['order_status']);
                } else {
                    fn_finish_payment($order_id, $pp_response);
                }

            } else {
                $notify_status = QIWI_NOTIFY_ERROR_PARAMS;
            }

        } else {
            $notify_status = QIWI_NOTIFY_ERROR_PARAMS;
        }

        header("Content-type: text/xml");

        $dom = new DOMDocument('1.0', 'utf-8');
        $result = $dom->createElement('result');
        $result_code = $dom->createElement('result_code', $notify_status);

        $result->appendChild($result_code);
        $dom->appendChild($result);

        if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
            Qiwi::writeLog($dom->saveXML(), 'qiwi_xml.txt');
        }

        fn_echo($dom->saveXML());

    } elseif ($mode == 'return') {

        if ($processor_data['processor_params']['invoice_type'] == 'external') {
            $valid_id = db_get_field("SELECT order_id FROM ?:order_data WHERE order_id = ?i AND type = 'S'", $order_id);
            if (!empty($valid_id)) {
                fn_change_order_status($order_id, 'O');
            }
        }

        if (!empty($order_id)) {
            fn_order_placement_routines('route', $order_id, false);
        }
    }

    exit;

} else {

    $order_transaction = $order_id . date('is');
    $qiwi = new Qiwi($processor_data);

    if ($processor_data['processor_params']['invoice_type'] == 'create') {
        $response = $qiwi->createBill($order_transaction, $order_info);

        if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
            Qiwi::writeLog($response, 'qiwi_create_bill.txt');
        }

        $pp_response = '';
        if (!$qiwi->isError()) {

            if ($qiwi->getStatusBill() == QIWI_BILL_STATUS_WAITING) {
                $pp_response['order_status'] = 'O';

            } elseif ($qiwi->getStatusBill() == QIWI_BILL_STATUS_PAID) {
                $pp_response['order_status'] = 'P';

            } else {
                $pp_response['order_status'] = 'F';
            }

            $pp_response['reason_text'] = fn_qiwi_rest_get_bill_status($response['response']['bill']['status']);
            $pp_response['transaction_id'] = $order_transaction;

        } else {
            $pp_response['order_status'] = 'F';
            $pp_response['reason_text'] = $qiwi->getErrorText();
        }

        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('route', $order_id, false);

    } else {
        $data = $qiwi->formBill($order_transaction, $order_info, $processor_data);
        $url = 'https://w.qiwi.com/order/external/create.action';

        if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
            Qiwi::writeLog(array($url, $data), 'qiwi_page.txt');
        }

        fn_create_payment_form($url, $data, 'Qiwi', false, 'GET');
    }

}

function fn_qiwi_rest_check_params($params)
{
    return !(empty($params['bill_id']) || empty($params['status']) || empty($params['amount']) || empty($params['user']));
}
