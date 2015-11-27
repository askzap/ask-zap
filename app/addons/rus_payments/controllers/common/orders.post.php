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
use Tygh\Pdf;
use Tygh\Mailer;
use Tygh\Payments\RusInvoicePayment;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'print_sbrf_receipt' || $mode == "send_sbrf_receipt") {

    $order_info = fn_get_order_info($_REQUEST['order_id']);

    $currencies = Registry::get('currencies');

    if (isset($currencies['RUB'])) {
        $currency = $currencies['RUB'];

        $for_rub = fn_format_rate_value($order_info['total'], 'F', $currency['decimals'], $currency['decimals_separator'], $currency['thousands_separator'], $currency['coefficient']);

        if ($currency['decimals'] != 0) {
            $length_for_rub = $currency['decimals']+1;
            $rub = substr($for_rub , 0 , -$length_for_rub );

            $length_for_kop = $currency['decimals'];

            $kop = substr($for_rub , -$length_for_kop );

            $total_print = '<strong>' . $rub . '</strong>&nbsp;' . __("sbrf_rub") . '&nbsp;<strong>' . $kop . '</strong>&nbsp;' . __("sbrf_kop");
        } else {
            $total_print = '<strong>' . $for_rub . '</strong>&nbsp;' . __("sbrf_rub");
        }
    } else {
        $total_print = fn_format_price_by_currency($order_info['total']);
    }

    $view = Tygh::$app['view'];

    $view->assign('total_print', $total_print);
    $view->assign('order_info', $order_info);
    $view->assign('fonts_path', fn_get_theme_path('[relative]/[theme]/media/fonts'));

    $temp_dir = Registry::get('config.dir.cache_misc') . 'tmp/';
    fn_mkdir($temp_dir);

    $path = fn_qr_generate($order_info, '|', $temp_dir);
    $url_qr_code = Registry::get('config.http_location') . '/' . fn_get_rel_dir($path);
    $view->assign('url_qr_code', $url_qr_code);

    if ($mode == "send_sbrf_receipt") {

        if (!empty($order_info['email'])) {
            fn_disable_live_editor_mode();

            $html = array(
                $view->displayMail('addons/rus_payments/print_sbrf_receipt.tpl', false, 'C')
            );

            Pdf::render($html, fn_get_files_dir_path() . 'sberbank_receipt.pdf', 'save');

            $data = array(
                'order_info' => $order_info,
                'total_print' => $total_print,
                'fonts_path' => fn_get_theme_path('[relative]/[theme]/media/fonts'),
                'url_qr_code' => $url_qr_code,
                'email_subj' => __("sbrf_receipt_for_payment", array('[order_id]' => $order_info['order_id']))
            );

            Mailer::sendMail(array(
                'to' => $order_info['email'],
                'from' => 'default_company_orders_department',
                'data' => $data,
                'attachments' => array(fn_get_files_dir_path() . 'sberbank_receipt.pdf'),
                'tpl' => 'addons/rus_payments/print_sbrf_receipt.tpl',
                'is_html' => true
            ), 'A');

            fn_set_notification('N', __('notice'), __('text_email_sent'));
        }

    } else {
        $view->assign('show_print_button', true);
        $view->displayMail('addons/rus_payments/print_sbrf_receipt.tpl', true, 'C');
    }

    exit;

}  elseif ($mode == 'print_invoice_payment' || $mode == 'send_account_payment') {

    $month = array(
        1 => 'Января',
        2 => 'Февраля',
        3 => 'Марта',
        4 => 'Апреля',
        5 => 'Мая',
        6 => 'Июня',
        7 => 'Июля',
        8 => 'Августа',
        9 => 'Сентября',
        10 => 'Октября',
        11 => 'Ноября',
        12 => 'Декабря'
    );

    $order_info = fn_get_order_info($_REQUEST['order_id']);
    $currencies = Registry::get('currencies');

    if (CART_SECONDARY_CURRENCY == 'RUB' && isset($currencies['RUB'])) {
        $currency = $currencies['RUB'];

        $for_rub = fn_format_rate_value($order_info['total'], 'F', $currency['decimals'], $currency['decimals_separator'], $currency['thousands_separator'], $currency['coefficient']);

        if ($currency['decimals'] != 0) {
            $length_for_rub = $currency['decimals'] + 1;
            $rub = substr($for_rub, 0, -$length_for_rub);

            $length_for_kop = $currency['decimals'];

            $kop = substr($for_rub, -$length_for_kop);

            $total_print = '<strong>' . $rub . '</strong>&nbsp;' . __("sbrf_rub") . '&nbsp;<strong>' . $kop . '</strong>&nbsp;' . __("sbrf_kop");
        } else {
            $total_print = '<strong>' . $for_rub . '</strong>&nbsp;' . __("sbrf_rub");
        }
    } else {
        $total_print = fn_format_price_by_currency($order_info['total']);
    }

    $order_info['sum_tax'] = 0;
    if (!empty($order_info['taxes'])) {
        foreach ($order_info['taxes'] as $data_tax) {
            $order_info['sum_tax'] = $order_info['sum_tax'] + $data_tax['tax_subtotal'];
        }
    }

    $order_info['info_customer'] = "";
    if (!empty($order_info['payment_info'])) {
        if (!empty($order_info['payment_info']['organization_customer'])) {
            $order_info['info_customer'] .= $order_info['payment_info']['organization_customer'] . ' ';
        }

        if (!empty($order_info['payment_info']['inn_customer'])) {
            $order_info['info_customer'] .= __("inn_customer") . ': ' . $order_info['payment_info']['inn_customer'] . ' ';
        }

        if (!empty($order_info['payment_info']['phone'])) {
            $order_info['info_customer'] .= __("phone") . ': ' . $order_info['payment_info']['phone'] . ' ';
        }

        if (!empty($order_info['payment_info']['zip_postal_code'])) {
            $order_info['info_customer'] .= __("zip_postal_code") . ': ' . $order_info['payment_info']['zip_postal_code'] . ' ';
        }

        if (!empty($order_info['payment_info']['address'])) {
            $order_info['info_customer'] .= __("address") . ': ' . $order_info['payment_info']['address'] . ' ';
        }

        if (!empty($order_info['payment_info']['bank_details'])) {
            $order_info['info_customer'] .= __("addons.rus_payments.bank_details") . ': ' . $order_info['payment_info']['bank_details'];
        }
    }

    $order_info['info_supplier'] = "";
    $data_payment = $order_info['payment_method']['processor_params'];
    if (!empty($data_payment)) {
        if (!empty($data_payment['account_recepient_name'])) {
            $order_info['info_supplier'] .= $data_payment['account_recepient_name'] . ' ';
        }

        if (!empty($data_payment['account_inn'])) {
            $order_info['info_supplier'] .= __("inn_customer") . ': ' . $data_payment['account_inn'] . ' ';
        }

        if (!empty($data_payment['account_kpp'])) {
            $order_info['info_supplier'] .= __("addons.rus_payments.account_kpp") . ': ' . $data_payment['account_kpp'] . ' ';
        }

        if (!empty($data_payment['account_address'])) {
            $order_info['info_supplier'] .= __("address") . ': ' . $data_payment['account_address'] . ' ';
        }

        if (!empty($data_payment['account_phone'])) {
            $order_info['info_supplier'] .= __("phone") . ': ' . $data_payment['account_phone'] . ' ';
        }
    }

    $str_date = "";
    $n_month = (int)date("m", $order_info['timestamp']);
    $invoice_date = date("d", $order_info['timestamp']) . ' ' . $month[$n_month] . ' ' . date("Y", $order_info['timestamp']);

    $order_info['str_total'] = (CART_SECONDARY_CURRENCY == 'RUB') ? RusInvoicePayment::clearDoit($order_info['total']) : "";
    $order_info['path_stamp'] = fn_get_image_pairs($order_info['payment_id'], 'stamp', 'M', true, true, DESCR_SL);
    $order_info['text_invoice_payment'] = __("addons.rus_payments.text_invoice_payment", array('[number_account]' => $order_info['order_id'], '[invoice_data]' => $invoice_date));

    $view = Tygh::$app['view'];
    $view->assign('total_print', $total_print);
    $view->assign('order_info', $order_info);
    $view->assign('fonts_path', fn_get_theme_path('[relative]/[theme]/media/fonts'));
    if ($order_info['shipping_cost'] != 0) {
        $view->assign('shipping_cost', true);
    }

    if ($mode == "send_account_payment") {
        if (!empty($order_info['email'])) {
            fn_disable_live_editor_mode();

            $html = array(
                $view->displayMail('addons/rus_payments/print_invoice_payment.tpl', false, 'C')
            );

            Pdf::render($html, fn_get_files_dir_path() . 'account_payment.pdf', 'save');

            $data = array(
                'order_info' => $order_info,
                'total_print' => $total_print,
                'fonts_path' => fn_get_theme_path('[relative]/[theme]/media/fonts'),
            );

            Mailer::sendMail(array(
                'to' => $order_info['email'],
                'from' => 'default_company_orders_department',
                'data' => $data,
                'attachments' => array(fn_get_files_dir_path() . 'account_payment.pdf'),
                'tpl' => 'addons/rus_payments/print_invoice_payment.tpl',
                'is_html' => true
            ), 'A');

            fn_set_notification('N', __('notice'), __('text_email_sent'));
        }
    } else {
        $view->assign('show_print_button', true);
        $view->displayMail('addons/rus_payments/print_invoice_payment.tpl', true, 'C');
    }

    exit;

} elseif ($mode == 'get_stamp') {

    Header("Content-Type: image/png");
    Header("Content-Type: image/jpg");
    Header("Content-Type: image/jpeg");
    Header("Content-Type: image/gif");

    $path_stamp = fn_get_image_pairs($_REQUEST['payment_id'], 'stamp', 'M', true, true, DESCR_SL);

    $image = fn_get_contents($path_stamp['icon']['absolute_path']);

    fn_echo($image);

    exit;

}
