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
use Tygh\Registry;
use Tygh\Payments\Processors\YandexMoneyMWS\Client as MWSClient;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/* HOOKS */

function fn_rus_payments_change_order_status(&$status_to, &$status_from, &$order_info, &$force_notification, &$order_statuses, &$place_order)
{
    $processor_data = fn_get_processor_data($order_info['payment_id']);
    $payment_info = $order_info['payment_info'];

    if (!empty($processor_data['processor']) && $processor_data['processor'] == 'Yandex.Money' && !empty($payment_info['yandex_postponed_payment'])) {

        try {

            $cert = $processor_data['processor_params']['certificate_filename'];

            $mws_client = new MWSClient();
            $mws_client->authenticate(array(
                'pkcs12_file' => Registry::get('config.dir.certificates') . $cert,
                'pass' => $processor_data['processor_params']['p12_password'],
                'is_test_mode' => $processor_data['processor_params']['mode'] == 'test',
            ));

            if ($status_to == $processor_data['processor_params']['confirmed_order_status']) {

                $mws_client->confirmPayment($payment_info['yandex_invoice_id'], $order_info['total']);

                $payment_info['yandex_confirmed_time'] = date('c');
                $payment_info['yandex_postponed_payment'] = false;

            } elseif ($status_to == $processor_data['processor_params']['canceled_order_status']) {

                $mws_client->cancelPayment($payment_info['yandex_invoice_id']);

                $payment_info['yandex_canceled_time'] = date('c');
                $payment_info['yandex_postponed_payment'] = false;
            }

            $payment_info['order_status'] = $status_to;

            fn_update_order_payment_info($order_info['order_id'], $payment_info);

            $order_info['payment_info'] = $payment_info;

        } catch (\Exception $e) {
            fn_set_notification('E', __('error'), __('addons.rus_payments.yandex_money_mws_operation_error'));
            return $status_to = $status_from;
        }
    }
}

/* \HOOKS */

function fn_rus_payments_install()
{
    $payments = fn_get_schema('rus_payments', 'processors', 'php', true);

    if (!empty($payments)) {
        foreach ($payments as $payment) {

            $processor_id = db_get_field("SELECT processor_id FROM ?:payment_processors WHERE admin_template = ?s", $payment['admin_template']);

            if (empty($processor_id)) {
                db_query('INSERT INTO ?:payment_processors ?e', $payment);
            } else {
                db_query("UPDATE ?:payment_processors SET ?u WHERE processor_id = ?i", $payment, $processor_id);
            }
        }
    }

    $statuses = fn_get_schema('rus_payments', 'statuses', 'php', true);

    if (!empty($statuses)) {
        foreach ($statuses as $status_name => $status_data) {
            $status = fn_update_status('', $status_data, $status_data['type']);
            fn_set_storage_data($status_name, $status);
        }
    }
}

function fn_rus_payments_uninstall()
{
    $payments = fn_get_schema('rus_payments', 'processors');
    fn_rus_payments_disable_payments($payments, true);

    foreach ($payments as $payment) {
        db_query("DELETE FROM ?:payment_processors WHERE admin_template = ?s", $payment['admin_template']);
    }

    $statuses = fn_get_schema('rus_payments', 'statuses', 'php', true);
    if (!empty($statuses)) {
        foreach ($statuses as $status_name => $status_data) {
            fn_delete_status(fn_get_storage_data($status_name), 'O');
        }
    }

}

function fn_rus_payments_disable_payments($payments, $drop_processor_id = false)
{
    $fields = '';
    if ($drop_processor_id) {
        $fields = 'processor_id = 0,';
    }

    foreach ($payments as $payment) {
        $processor_id = db_get_field("SELECT processor_id FROM ?:payment_processors WHERE admin_template = ?s", $payment['admin_template']);

        if (!empty($processor_id)) {
            db_query("UPDATE ?:payments SET $fields status = 'D' WHERE processor_id = ?i", $processor_id);
        }
    }
}

function fn_rus_pay_format_price($price, $payment_currency)
{
    $currencies = Registry::get('currencies');

    if (array_key_exists($payment_currency, $currencies)) {
        if ($currencies[$payment_currency]['is_primary'] != 'Y') {
            $price = fn_format_price($price / $currencies[$payment_currency]['coefficient']);
        }
    } else {
        return false;
    }

    return $price;
}

function fn_rus_pay_format_price_down($price, $payment_currency)
{
    $currencies = Registry::get('currencies');

    if (array_key_exists($payment_currency, $currencies)) {
          $price = fn_format_price($price * $currencies[$payment_currency]['coefficient']);
    } else {
        return false;
    }

    return $price;
}

function fn_rus_payments_normalize_phone($phone)
{
    $phone_normalize = '';

    if (!empty($phone)) {
        if (strpos('+', $phone) === false && $phone[0] == '8') {
            $phone[0] = '7';
        }

        $phone_normalize = str_replace(array(' ', '(', ')', '-'), '', $phone);
    }

    return $phone_normalize;
}

function fn_qr_generate($order_info, $delimenter = '|', $dir = "")
{
    $processor_params = $order_info['payment_method']['processor_params'];

    $format_block = 'ST' . '0001' . '2' . $delimenter;

    $required_block = array(
        'Name' => $processor_params['sbrf_recepient_name'],
        'PersonalAcc' => $processor_params['sbrf_settlement_account'],
        'BankName' => $processor_params['sbrf_bank'],
        'BIC' => $processor_params['sbrf_bik'],
        'CorrespAcc' => $processor_params['sbrf_cor_account'],
    );

    $required_block = fn_qr_array2string($required_block, $delimenter);

    $additional_block = array(
        'PayeeINN' => $processor_params['sbrf_inn'],
        'Sum' => $order_info['total'] * 100,
        'Purpose' => __('sbrf_order_payment') . ' №' . $order_info['order_id'],
        'LastName' => $order_info['b_lastname'],
        'FirstName' => $order_info['b_firstname'],
        'PayerAddress' => $order_info['b_city'],
        'Phone' => $order_info['b_phone'],
    );

    $additional_block = fn_qr_array2string($additional_block, $delimenter);

    $string = $format_block . $required_block . $additional_block;

    $string = substr($string, 0, -1);

    $resolution = $processor_params['sbrf_qr_resolution'];

    $data = array(
        'cht' => 'qr',
        'choe' => 'UTF-8',
        'chl' => $string,
        'chs' => $resolution . 'x' . $resolution,
        'chld' => 'M|4'
    );

    $url = 'https://chart.googleapis.com/chart';

    $response = Http::get($url, $data);

    if (!strpos($response, 'Error')) {

        fn_put_contents($dir . 'qr_code_' . $order_info['order_id'] . '.png', $response);
        $path = $dir . 'qr_code_' . $order_info['order_id'] . '.png';

    } else {
        $path = fn_get_contents(DIR_ROOT. '/images/no_image.png');
    }

    return $path;
}

function fn_qr_array2string($array, $del = '|', $eq = '=')
{
    if (is_array($array)) {

        $string = '';

        foreach ($array as $key => $value) {
            if (!empty($value)) {
                $string .= $key . $eq . $value . $del ;
            }
        }
    }

    return $string;
}

function fn_yandex_money_log_write($data, $file)
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

function fn_rus_payments_get_order_info(&$order, $additional_data)
{
    if (!empty($order['payment_info']) && isset($order['payment_info']['yandex_payment_type'])) {

        if ($order['payment_info']['yandex_payment_type'] == 'pc') {
            $payment_type = 'yandex';

        } elseif ($order['payment_info']['yandex_payment_type'] == 'ac') {
            $payment_type = 'card';

        } elseif ($order['payment_info']['yandex_payment_type'] == 'gp') {
            $payment_type = 'terminal';

        } elseif ($order['payment_info']['yandex_payment_type'] == 'mc') {
            $payment_type = 'phone';

        } elseif ($order['payment_info']['yandex_payment_type'] == 'wm') {
            $payment_type = 'webmoney';

        } elseif ($order['payment_info']['yandex_payment_type'] == 'ab') {
            $payment_type = 'alfabank';

        } elseif ($order['payment_info']['yandex_payment_type'] == 'sb') {
            $payment_type = 'sberbank';

        } elseif ($order['payment_info']['yandex_payment_type'] == 'ma') {
            $payment_type = 'masterpass';

        } elseif ($order['payment_info']['yandex_payment_type'] == 'pb') {
            $payment_type = 'psbank';

        } else {
            $payment_type = $order['payment_info']['yandex_payment_type'];
        }

        $order['payment_info']['yandex_payment_type'] = __('yandex_payment_' . $payment_type);
    }
}

function fn_rus_payments_account_fields($fields_account, $user_data)
{
    $account_params = array();
    $profile_fields = db_get_hash_array("SELECT field_id, field_name, field_type FROM ?:profile_fields", "field_id");

    foreach ($fields_account as $name_account => $field_account) {
        if (!empty($profile_fields[$field_account]['field_name'])) {
            $account_params[$name_account] = !empty($user_data[$profile_fields[$field_account]['field_name']]) ? $user_data[$profile_fields[$field_account]['field_name']] : '';

        } elseif (!empty($user_data['fields'][$field_account])) {
            $account_params[$name_account] = !empty($user_data['fields'][$field_account]) ? $user_data['fields'][$field_account] : '';

        } else {
            $account_params[$name_account] = '';
        }
    }

    return $account_params;
}
