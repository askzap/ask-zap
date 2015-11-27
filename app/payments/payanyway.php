<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {
    if ($mode == 'notify') {
        if (!empty($_REQUEST['MNT_TRANSACTION_ID'])) {

            $order_id = (int) $_REQUEST['MNT_TRANSACTION_ID'];
            $order_info = fn_get_order_info($order_id);
            $processor_data = $order_info['payment_method'];

            if (isset($_REQUEST['MNT_ID']) && isset($_REQUEST['MNT_TRANSACTION_ID']) && isset($_REQUEST['MNT_OPERATION_ID'])
                && isset($_REQUEST['MNT_AMOUNT']) && isset($_REQUEST['MNT_CURRENCY_CODE']) && isset($_REQUEST['MNT_TEST_MODE'])
                && isset($_REQUEST['MNT_SIGNATURE']))
            {
                $signature = md5("{$_REQUEST['MNT_ID']}{$_REQUEST['MNT_TRANSACTION_ID']}{$_REQUEST['MNT_OPERATION_ID']}{$_REQUEST['MNT_AMOUNT']}{$_REQUEST['MNT_CURRENCY_CODE']}{$_REQUEST['MNT_TEST_MODE']}{$processor_data['processor_params']['mnt_dataintegrity_code']}");
                if ($_REQUEST['MNT_SIGNATURE'] == $signature) {
                    fn_change_order_status($order_id, 'P', $order_info['status'], false);
                    die('SUCCESS');
                } else {
                    die('FAIL');
                }
            } else {
                die('FAIL');
            }

        } else {
            die('FAIL');
        }

    } elseif ($mode == 'success') {

        $order_id = $_REQUEST['MNT_TRANSACTION_ID'];
        $order_info = fn_get_order_info($order_id);

        $pp_response = array();

        if ($order_info['status'] == 'N') {
            $pp_response['order_status'] = 'O';
        }
        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('route', $order_id);

    } elseif ($mode == 'fail') {
        if (isset($_REQUEST['MNT_TRANSACTION_ID'])) {
            $order_id = $_REQUEST['MNT_TRANSACTION_ID'];
            $order_info = fn_get_order_info($order_id);

            $pp_response = array();
            $pp_response['order_status'] = 'D';

            fn_finish_payment($order_id, $pp_response);
            fn_order_placement_routines('route', $order_id);
        } else {
            fn_redirect('checkout.' . (Registry::get('settings.General.checkout_style') != 'multi_page' ? 'checkout' : 'summary'));
        }
    } elseif ($mode == 'invoice') {

        $order_id = $_REQUEST['MNT_TRANSACTION_ID'];
        $order_info = fn_get_order_info($order_id);
        if ($order_info) {
            $processor_data = fn_get_payment_method_data($order_info['payment_id']);

            include_once (Registry::get('config.dir.payments') . 'payanyway_files/MonetaAPI/MonetaWebService.php');

            switch ($processor_data['processor_params']['mnt_payment_server']) {
                case "demo.moneta.ru":
                    $service = new MonetaWebService("https://demo.moneta.ru/services.wsdl", $processor_data['processor_params']['payanyway_login'], $processor_data['processor_params']['payanyway_password']);
                    break;
                case "www.payanyway.ru":
                    $service = new MonetaWebService("https://www.moneta.ru/services.wsdl", $processor_data['processor_params']['payanyway_login'], $processor_data['processor_params']['payanyway_password']);
                    break;
            }

            try {
                $transactionRequestType = new MonetaForecastTransactionRequest();
                if (isset($_REQUEST['paymentSystem_accountId']))
                    $transactionRequestType->payer = $_REQUEST['paymentSystem_accountId'];
                $transactionRequestType->payee = $_REQUEST['MNT_ID'];
                $transactionRequestType->amount = $_REQUEST['MNT_AMOUNT'];
                $transactionRequestType->clientTransaction = $_REQUEST['MNT_TRANSACTION_ID'];
                $forecast = $service->ForecastTransaction($transactionRequestType);

                $request = new MonetaInvoiceRequest();
                if (isset($_REQUEST['paymentSystem_accountId']))
                    $request->payer = $_REQUEST['paymentSystem_accountId'];
                $request->payee = $_REQUEST['MNT_ID'];
                $request->amount = $_REQUEST['MNT_AMOUNT'];
                $request->clientTransaction = $_REQUEST['MNT_TRANSACTION_ID'];
                if ($processor_data['processor_params']['payment_system'] == 'post') {
                    $operationInfo = new MonetaOperationInfo();
                    $a1 = new MonetaKeyValueAttribute();
                    $a1->key = 'mailofrussiaindex';
                    $a1->value = $_REQUEST['additionalParameters_mailofrussiaSenderIndex'];
                    $operationInfo->addAttribute($a1);
                    $a2 = new MonetaKeyValueAttribute();
                    $a2->key = 'mailofrussiaaddress';
                    $a2->value = $_REQUEST['additionalParameters_mailofrussiaSenderAddress'];
                    $operationInfo->addAttribute($a2);
                    $a3 = new MonetaKeyValueAttribute();
                    $a3->key = 'mailofrussianame';
                    $a3->value = $_REQUEST['additionalParameters_mailofrussiaSenderName'];
                    $operationInfo->addAttribute($a3);
                    $request->operationInfo = $operationInfo;
                } elseif ($processor_data['processor_params']['payment_system'] == 'euroset') {
                    $operationInfo = new MonetaOperationInfo();
                    $a1 = new MonetaKeyValueAttribute();
                    $a1->key = 'rapidamphone';
                    $a1->value = $_REQUEST['additionalParameters_rapidaPhone'];
                    $operationInfo->addAttribute($a1);
                    $request->operationInfo = $operationInfo;
                }

                $response = $service->Invoice($request);
                if ($processor_data['processor_params']['payment_system'] == 'euroset') {
                    $response1 = $service->GetOperationDetailsById($response->transaction);
                    foreach ($response1->operation->attribute as $attr) {
                        if ($attr->key == 'rapidatid') {
                            $transaction_id = $attr->value;
                        }
                    }
                } else {
                    $transaction_id = $response->transaction;
                }

                $invoice['status_title'] = fn_get_lang_var('text_payanyway_invoice_created');
                $invoice['status'] = $response->status;
                $invoice['system'] = $processor_data['processor_params']['payment_system'];
                $invoice['transaction'] = str_pad($transaction_id, 10, "0", STR_PAD_LEFT);
                $invoice['amount'] = number_format($forecast->payerAmount,2,'.','')." ".$_REQUEST['MNT_CURRENCY_CODE'];
                $invoice['fee'] = number_format($forecast->payerFee,2,'.','')." ".$_REQUEST['MNT_CURRENCY_CODE'];

                $pp_response['order_status'] = 'O';
                fn_finish_payment($order_id, $pp_response);
                fn_clear_cart($_SESSION['cart']);

            } catch (Exception $e) {
                $invoice['status_title'] = fn_get_lang_var('text_payanyway_invoice_failed');
                $invoice['status'] = 'FAILED';
                $invoice['error_message'] = $e->getMessage();

                $pp_response['order_status'] = 'F';
                fn_finish_payment($order_id, $pp_response);
            }

            fn_add_breadcrumb(__('orders'));
            fn_add_breadcrumb(__('invoice'));

            Registry::get('view')->assign('invoice', $invoice);
        }
    }

} else {

    $payment_system = $processor_data['processor_params']['payment_system'];
    $post_data = array();
    if (isset($processor_data['processor_params'][$payment_system]) && $processor_data['processor_params'][$payment_system]['invoice']) {
        $action = fn_url("");
        $post_data["dispatch"] = "payment_notification.invoice";
        $post_data["payment"] = "payanyway";
    } else {
        $action = "https://{$processor_data['processor_params']['mnt_payment_server']}/assistant.htm";
    }

    $currencies = Registry::get('currencies');
    $mnt_currency = $processor_data['processor_params']['currency'];
    if (!isset($currencies[$mnt_currency]))
        $mnt_currency = CART_PRIMARY_CURRENCY;

    $mnt_amount = number_format($order_info['total'] / $currencies[$mnt_currency]['coefficient'], 2, '.', '');
    $mnt_signature = md5($processor_data['processor_params']['mnt_id'].$order_id.$mnt_amount.$mnt_currency.$processor_data['processor_params']['mnt_test_mode'].$processor_data['processor_params']['mnt_dataintegrity_code']);

    $post_data["MNT_ID"] = $processor_data['processor_params']['mnt_id'];
    $post_data["MNT_TRANSACTION_ID"] = $order_id;
    $post_data["MNT_AMOUNT"] = $mnt_amount;
    $post_data["MNT_CURRENCY_CODE"] = $mnt_currency;
    $post_data["MNT_TEST_MODE"] = $processor_data['processor_params']['mnt_test_mode'];
    $post_data["MNT_SIGNATURE"] = $mnt_signature;
    $post_data["MNT_SUCCESS_URL"] = fn_url("payment_notification.success&payment=payanyway", AREA, 'current');
    $post_data["MNT_FAIL_URL"] = fn_url("payment_notification.fail&payment=payanyway", AREA, 'current');

    if ($payment_system !== "payanyway") {
        $post_data["followup"] = "true";
        $post_data["javascriptEnabled"] = "true";
    }
    if (isset($processor_data['processor_params'][$payment_system]) && !empty($processor_data['processor_params'][$payment_system]['unitId'])) {
        $post_data["paymentSystem.unitId"] = $processor_data['processor_params'][$payment_system]['unitId'];
    }
    if (isset($processor_data['processor_params'][$payment_system]) && !empty($processor_data['processor_params'][$payment_system]['accountId'])) {
        $post_data["paymentSystem.accountId"] = $processor_data['processor_params'][$payment_system]['accountId'];
    }

    switch ($payment_system) {
        case 'post':
            $post_data["additionalParameters.mailofrussiaSenderIndex"] = $order_info['payment_info']['mailofrussiaSenderIndex'];
            $post_data["additionalParameters.mailofrussiaSenderAddress"] = $order_info['payment_info']['mailofrussiaSenderAddress'];
            $post_data["additionalParameters.mailofrussiaSenderName"] = $order_info['payment_info']['mailofrussiaSenderName'];
            break;
        case 'moneymail':
            $post_data["additionalParameters.buyerEmail"] = $order_info['payment_info']['buyerEmail'];
            break;
        case 'euroset':
            $post_data["additionalParameters.rapidaPhone"] = $order_info['payment_info']['rapidaPhone'];
            break;
        case 'webmoney':
            $post_data["paymentSystem.accountId"] = $order_info['payment_info']['accountId'];
            break;
        default:
            break;
    }

    fn_create_payment_form($action, $post_data, 'PayAnyWay server', true, "get");

    exit;
}

function fn_payanyway_format_price ($price, $currency)
{
    $currencies = Registry::get('currencies');
    if (isset($currencies[$currency]))
        return number_format($price / $currencies[$currency]['coefficient'], 2, '.', '');
    else
        return number_format($price, 2, '.', '');
}
