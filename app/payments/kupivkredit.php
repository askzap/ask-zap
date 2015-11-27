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
    $pp_response = array();
    $order_id = (!empty($_REQUEST['order_id'])) ? $_REQUEST['order_id'] : '';

    if ($mode == 'close' || $mode == 'decision') {
        $order_info = fn_get_order_info($order_id);
        $decision = $_REQUEST['decision'];

        if (empty($decision) || $decision == 'null' || $decision == 'undefined') {
            $pp_response['order_status'] = 'N';
            $pp_response['reason_text'] = __('kupivkredit_widget_incomplete');
        } elseif ($decision == 'closed') {
            $pp_response['order_status'] = 'I';
            $pp_response['reason_text'] = __('kupivkredit_widget_closed');
        } elseif ($decision == 'rej') {
            $pp_response['order_status'] = 'D';
            $pp_response['reason_text'] = __('kupivkredit_widget_bank_reject');
        } elseif ($decision == 'ver') {
            $pp_response['order_status'] = 'O';
            $pp_response['reason_text'] = __('kupivkredit_widget_open');
        } elseif ($decision == 'agr') {
            $pp_response['order_status'] = 'P';
            $pp_response['reason_text'] = __('kupivkredit_widget_processed');
        }
    }

    if (fn_check_payment_script('kupivkredit.php', $order_id)) {
        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('route', $order_id, false);
    }

} else {
    $url = ($processor_data['processor_params']['test'] == 'Y') ? 'https://' . KVK_WIDGET_TEST_URL : 'https://' . KVK_WIDGET_URL;
    $kvk_order_id = (($order_info['repaid']) ? ($order_info['order_id']  . '_' . $order_info['repaid']) : $order_info['order_id']) . '_' . fn_date_format(time(), '%H_%M_%S');
    //We should save this iformation for the actions such as 'confirm order', 'cancel order' in the admin area.
    fn_update_order_payment_info($order_id, array('kvk_order_id' => $kvk_order_id));
    $order = array();

    foreach ($order_info['products'] as $k => $item) {
        $price = fn_format_price(($item['subtotal'] - fn_external_discounts($item)) / $item['amount']);
        $order['items'][] = array (
            'title' => $item['product'],
            'category' => db_get_field("SELECT ?:category_descriptions.category FROM ?:category_descriptions LEFT JOIN ?:products_categories ON ?:category_descriptions.category_id = ?:products_categories.category_id WHERE ?:products_categories.product_id = ?i AND ?:products_categories.link_type = ?s AND ?:category_descriptions.lang_code = ?s", $item['product_id'], 'M', $order_info['lang_code']),
            'qty' => $item['amount'],
            'price' => fn_format_rate_value($price, 'F', 0, '.', '', '')
        );
    }

    if (!empty($order_info['shipping_cost'])) {
        $order['items'][] = array(
            'title' => __('shipping_cost'),
            'category' => '',
            'qty' => 1,
            'price' => fn_format_rate_value($order_info['shipping_cost'], 'F', 0, '.', '', '')
        );
    }

    if (!empty($order_info['taxes'])) {
        foreach ($order_info['taxes'] as $tax) {
            if ($tax['price_includes_tax'] == 'N') {
                $order['items'][] = array(
                    'title' => __('tax'),
                    'category' => '',
                    'qty' => 1,
                    'price' => fn_format_rate_value($tax['tax_subtotal'], 'F', 0, '.', '', '')
                );
            }
        }
    }

    $surcharge = isset($order_info['payment_surcharge']) ? intval($order_info['payment_surcharge']) : 0;
    if ($surcharge != 0) {
        $order['items'][] = array(
            'title' => __('payment_surcharge'),
            'category' => '',
            'qty' => 1,
            'price' => fn_format_rate_value($order_info['payment_surcharge'], 'F', 0, '.', '', '')
        );
    }

    $order['details'] = array(
        'firstname' => $order_info['b_firstname'],
        'lastname' => $order_info['b_lastname'],
        'middlename' => '',
        'email' => $order_info['email'],
        'cellphone' => $order_info['b_phone']
    );

    $order['partnerId'] = $processor_data['processor_params']['kvk_shop_id'];
    $order['partnerName'] = Registry::get('settings.Company.company_name');
    $order['partnerOrderId'] = $kvk_order_id;
    $order['deliveryType'] = '';

    $base = base64_encode(json_encode($order));
    $sig = fn_rus_kupivkredit_hash_order($base, $processor_data['processor_params']['kvk_secret']);
    $order_total = fn_format_rate_value($order_info['total'], 'F', 2, '.', '', '');

    $view = Registry::get('view');
    $view->assign('order_id', $order_info['order_id']);
    $view->assign('data', $order);
    $view->assign('base', $base);
    $view->assign('sig', $sig);
    $view->assign('url', $url);
    $view->assign('order_total', $order_total);
    $view->assign('url_return', fn_url("payment_notification.close?payment=kupivkredit&order_id=$order_id", AREA, 'current'));
    $view->assign('url_decision', fn_url("payment_notification.decision?payment=kupivkredit&order_id=$order_id", AREA, 'current'));

    if (AREA == 'A') {
        $view->display('views/orders/components/kupivkredit.tpl');
    } else {
        $view->display('views/orders/processors/kupivkredit.tpl');
    }

    exit;
}
