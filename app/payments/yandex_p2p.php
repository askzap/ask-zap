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

use Tygh\Payments\Processors\YandexMoney\Client;

if (defined('PAYMENT_NOTIFICATION')) {

    if ($mode == 'process' && !empty($_REQUEST['order_id'])) {

        $order_info = fn_get_order_info($_REQUEST['order_id']);

        $pp_response = array(
            'order_status' => 'F'
        );

        if (!empty($_REQUEST['code']) && fn_check_payment_script('yandex_p2p.php', $_REQUEST['order_id'], $processor_data)) {

            $client_id = $processor_data['processor_params']['client_id'];
            $result_url = fn_url("payment_notification.result?payment=yandex_p2p&order_id=" . $order_info['order_id']);

            $file_log = null;
            if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
                $path = fn_get_files_dir_path();
                fn_mkdir($path);

                $file_log = $path . 'yandex_money_p2p.log';
            }

            $ym = new Client($client_id, $file_log);
            $receiveTokenResp = $ym->receiveOAuthToken($_REQUEST['code'], $result_url, $processor_data['processor_params']['secret_key']);

            if ($receiveTokenResp->isSuccess()) {
                $token = $receiveTokenResp->getAccessToken();

                $params = array(
                    'to' => $processor_data['processor_params']['payee_id'],
                    'amount' => $order_info['total'],
                    'comment' => __("rus_payments.yandex_money_payment_order", array('[order_id]' => $_REQUEST['order_id'])),
                    'message' => null,
                    'label' => $_REQUEST['order_id'],
                );

                if (fn_yandex_money_is_test_mode($processor_data)) {
                    $params['test_payment'] = "true";
                    $params['test_result'] = $processor_data['processor_params']['test_code'];
                }

                $request = $ym->requestPaymentP2P($token, $params);

                if ($request->isSuccess()) {

                    $requestId = $request->getRequestId();
                    $process = $ym->processPaymentByWallet($token, $requestId, fn_yandex_money_is_test_mode($processor_data));

                    if ($process->isSuccess()) {
                        $pp_response['order_status'] = 'P';
                        $pp_response['transaction_id'] = $process->getPaymentId();
                        $pp_response['reason_text'] = __("rus_payments.yandex_money_payer") . ": ". $process->getPayer() . "; " . __("rus_payments.yandex_money_payee") . ": " . $process->getPayee();

                    } else {
                        $pp_response['reason_text'] = __("rus_payments.yandex_money_" . $process->getError());
                    }

                } else {

                    if ($request->getError() == 'ext_action_required') {
                        $pp_response['reason_text'] = __("rus_payments.yandex_money_" . $request->getError(), array("[ext_action_uri]" => $request->getExtActionUri()));
                    } else {
                        $pp_response['reason_text'] = __("rus_payments.yandex_money_" . $request->getError());
                    }
                }

            } else {
                $pp_response['reason_text'] = __("rus_payments.yandex_money_" . $receiveTokenResp->getError());
            }

            fn_finish_payment($order_info['order_id'], $pp_response);
        }

        fn_order_placement_routines('route', $order_info['order_id'], false);
    }

} else {

    $scope = "payment.to-account(\"". $processor_data['processor_params']['payee_id'] ."\",\"account\").limit(," . $order_info['total'] . ")";
    $redirect_url = fn_url("payment_notification.process?payment=yandex_p2p&order_id=" . $order_info['order_id']);
    $authUri = Client::authorizeUri($processor_data['processor_params']['client_id'], $redirect_url, $scope);

    fn_create_payment_form($authUri, array(), 'Yandex.P2P', false);
}

function fn_yandex_money_is_test_mode($processor_data)
{
    return !empty($processor_data['processor_params']['test_mode']) && $processor_data['processor_params']['test_mode'] == 'Y';
}

exit;
