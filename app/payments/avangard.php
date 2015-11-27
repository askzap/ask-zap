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

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {

    $order_id = 0;
    $processor_data = array();

    if (!empty($_REQUEST['xml'])) {

        fn_trusted_vars('xml');
        libxml_use_internal_errors(true);

        $xml_data = simplexml_load_string($_REQUEST['xml']);

        if ($xml_data) {
            $data = array(
                'id' => strval($xml_data->id),
                'status_code' => strval($xml_data->status_code),
                'status_desc' => strval($xml_data->status_desc),
                'order_id' => strval($xml_data->order_number),
                'signature' => strval($xml_data->signature),
                'credit_card' => strval($xml_data->card_num),
                'exp_mm' => strval($xml_data->exp_mm),
                'exp_yy' => strval($xml_data->exp_yy),
                'method_name' => strval($xml_data->method_name),
                'shop_id' => strval($xml_data->shop_id),
                'ticket' => strval($xml_data->ticket),
            );

            $order_id = strval($xml_data->order_number);
        } else {

            header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
            foreach (libxml_get_errors() as $error) {
                echo $error;
            }

            libxml_clear_errors();

            exit;
        }

    } elseif (!empty($_REQUEST['signature'])) {
        $data = array(
            'id' => $_REQUEST['id'],
            'status_code' => $_REQUEST['status_code'],
            'status_desc' => $_REQUEST['status_desc'],
            'order_id' => $_REQUEST['order_number'],
            'signature' => $_REQUEST['signature'],
            'credit_card' => $_REQUEST['card_num'],
            'exp_mm' => $_REQUEST['exp_mm'],
            'exp_yy' => $_REQUEST['exp_yy'],
            'method_name' => $_REQUEST['method_name'],
            'shop_id' => $_REQUEST['shop_id'],
            'ticket' => $_REQUEST['ticket']
        );

        $order_id = $data['order_id'];

    } elseif (!empty($_REQUEST['order_id'])) {
        $order_id = $_REQUEST['order_id'];
    }

    if (!empty($order_id)) {
        if (!fn_check_payment_script('avangard.php', $order_id, $processor_data)) {

            header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
            echo "Bad Request";

            exit;
        }

        if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
            fn_avangard_log_write($_REQUEST, 'avangard_log_notify.txt');
        }
    }

    if ($mode == 'notify') {

        $order_info = fn_get_order_info($order_id);

        $price = fn_avangard_convert_price($order_info['total'], 'RUB');

        $signature = strtoupper(md5($processor_data['processor_params']['av_sign']) . md5($data['shop_id'] . $order_id . ($price * 100)));
        $signature = strtoupper(md5($signature));

        if ($signature != $data['signature']) {
            header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
            echo "Bad Request";

            exit;
        }

        $pp_response = array(
            'reason_text' => $data['status_desc'],
            'transaction_id' => $data['id'],
            'avangard_ticket' => $data['ticket']
        );

        if ($data['status_code'] == '1') {
            fn_change_order_status($order_id, 'O');

        } elseif ($data['status_code'] == '3') {

            if (!empty($processor_data['processor_params']['paid_order_status'])) {
                $pp_response['order_status'] = $processor_data['processor_params']['paid_order_status'];
            } else {
                $pp_response['order_status'] = 'P';
            }

            $pp_response['credit_card'] = $data['credit_card'];
            $pp_response['expiry_date'] = $data['exp_mm'] . '/' . $data['exp_yy'];
            $pp_response['method'] = $data['method_name'];

            fn_finish_payment($data['order_id'], $pp_response);

        } elseif ($data['status_code'] == '6') {
            fn_change_order_status($data['order_id'], 'I');

        } else {
            $pp_response['order_status'] = 'F';
            $pp_response['reason_text'] = __('addons.rus_payments.avangard_fail_payment');

            fn_finish_payment($data['order_id'], $pp_response);
        }

        header($_SERVER["SERVER_PROTOCOL"] . " 202 Accepted");
        echo "Accepted";

    } elseif ($mode == 'ok' && !empty($order_id)) {

        $valid_id = db_get_field("SELECT order_id FROM ?:order_data WHERE order_id = ?i AND type = 'S'", $order_id);
        if (!empty($valid_id)) {
            fn_change_order_status($order_id, 'O');
        }

        fn_set_notification('N', '', __('rus_payments.avangard_payment_successfully'));

        fn_order_placement_routines('route', $order_id);

    } elseif ($mode == 'return' && !empty($order_id)) {

        $pp_response = array();
        $pp_response['reason_text'] = __('addons.rus_payments.avangard_fail_payment');

        $order_info = fn_get_order_info($order_id);

        if (!empty($processor_data['processor_params']['failed_order_status']) && empty($order_info['repaid'])) {
            $pp_response['order_status'] = $processor_data['processor_params']['failed_order_status'];

        } else {
            $pp_response['order_status'] = 'F';
        }

        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('route', $order_id);
    }

    exit;

} else {

    if (empty($processor_data)) {
        $processor_data = fn_get_processor_data($order_info['payment_id']);
    }

    if (!empty($order_id)) {
        $registration_data = fn_avangard_registration($order_id, $order_info, $processor_data);

        if ($registration_data['response_code'] == 0) {
            $form_url = 'https://www.avangard.ru/iacq/pay';
            $ticket['ticket'] = $registration_data['ticket'];
            fn_create_payment_form($form_url, $ticket, 'Avangard');
        } else {
            $pp_response['reason_text'] = $registration_data['response_message'];
            $pp_response['order_status'] = 'F';
            $pp_response['transaction_id'] = $registration_data['id'];
        }
    }
}

function fn_avangard_registration($number_order, $order_info, $processor_data)
{
    if (empty($order_info['notes'])) {
        $order_info['notes'] = 'Оплата заказа';
    }

    $back_url = Registry::get('config.current_location') . "/?dispatch=payment_notification.return&amp;payment=avangard&amp;order_id=$number_order";
    $back_url_ok = Registry::get('config.current_location') . "/?dispatch=payment_notification.ok&amp;payment=avangard&amp;order_id=$number_order";
    $back_url_fail = Registry::get('config.current_location') . "/?dispatch=payment_notification.return&amp;payment=avangard&amp;order_id=$number_order";

    $phone = '';
    if (!empty($order_info['phone'])) {
        $phone = $order_info['phone'];

    } elseif (!empty($order_info['b_phone'])) {
        $phone = $order_info['b_phone'];

    } elseif (!empty($order_info['s_phone'])) {
        $phone = $order_info['s_phone'];
    }

    $price = fn_avangard_convert_price($order_info['total'], 'RUB');

    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->formatOutput = true;
    $order = $dom->createElement('new_order');
    $order->appendChild($dom->createElement('shop_id', $processor_data['processor_params']['shop_id']));
    $order->appendChild($dom->createElement('shop_passwd', $processor_data['processor_params']['password']));
    $order->appendChild($dom->createElement('amount', $price * 100));
    $order->appendChild($dom->createElement('order_number', $number_order));
    $order->appendChild($dom->createElement('order_description', $order_info['notes']));
    $order->appendChild($dom->createElement('language', 'RU'));
    $order->appendChild($dom->createElement('back_url', $back_url));
    $order->appendChild($dom->createElement('back_url_ok', $back_url_ok));
    $order->appendChild($dom->createElement('back_url_fail', $back_url_fail));
    $order->appendChild($dom->createElement('client_name', $order_info['lastname'] . ' ' . $order_info['firstname']));
    $order->appendChild($dom->createElement('client_address', $order_info['b_address'].' '.$order_info['b_address_2']));
    $order->appendChild($dom->createElement('client_phone', $phone));
    $order->appendChild($dom->createElement('client_email', $order_info['email']));
    $order->appendChild($dom->createElement('client_ip', $_SERVER['REMOTE_ADDR']));
    $dom->appendChild($order);

    $url = 'https://www.avangard.ru/iacq/h2h/reg';

    $extra = array(
        'headers' => array(
            'Content-type: application/x-www-form-urlencoded;charset=utf-8',
            'Expect:'
        )
    );

    if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
        fn_avangard_log_write($dom->saveXML(), 'avangard_reg_data.txt');
    }

    $result_xml = Http::post($url, array('xml' => $dom->saveXML()), $extra);

    if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
        fn_avangard_log_write($result_xml, 'avangard_reg_result.txt');
    }

    $xml_string = @simplexml_load_string($result_xml);
    $result['id'] = strval($xml_string->id);
    $result['ticket'] = strval($xml_string->ticket);
    $result['ok_code'] = strval($xml_string->ok_code);
    $result['failure_code'] = strval($xml_string->failure_code);
    $result['response_code'] = strval($xml_string->response_code);
    $result['response_message'] = strval($xml_string->response_message);

    return $result;
}

function fn_avangard_log_write($data, $file)
{
    $path = fn_get_files_dir_path();
    fn_mkdir($path);
    $file = fopen($path . $file, 'a');

    if (!empty($file)) {
        fputs($file, 'TIME: ' . date('Y-m-d H:i:s', TIME) . "\n");
        fputs($file, fn_array2code_string($data) . "\n\n");
        fclose($file);
    }
}

function fn_avangard_convert_price($price, $currency_to)
{
    if (CART_PRIMARY_CURRENCY != $currency_to) {
        $currencies = Registry::get('currencies');

        if (empty($currencies[$currency_to])) {
            return sprintf('%.2f', $price);
        }

        $currency = $currencies[$currency_to];
        $price = fn_format_rate_value($price, 'F', $currency['decimals'], '.', '', $currency['coefficient']);
    }

    return sprintf('%.2f', $price);
}
