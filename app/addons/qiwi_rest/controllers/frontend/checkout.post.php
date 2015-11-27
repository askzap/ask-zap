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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Registry;

if ($mode == 'complete') {

    if (!empty($_REQUEST['order_id'])) {

        $order_info = fn_get_order_info($_REQUEST['order_id']);

        $payment_id = $order_info['payment_id'];
        $processor_data = fn_get_processor_data($payment_id);

        if (!empty($processor_data['processor_params']['invoice_type']) && $processor_data['processor_params']['invoice_type'] == 'create') {

            $shop_id = $processor_data['processor_params']['shop_id'];

            $payment_redirect = '';
            if (!empty($order_info['payment_info']['transaction_id'])) {
                $order_transaction = $order_info['payment_info']['transaction_id'];
                $payment_redirect = fn_url("https://w.qiwi.com/order/external/main.action?shop={$shop_id}&transaction={$order_transaction}");
            }

            $qiwi_page_text = __("addons.qiwi_rest.qiwi_page_link", array('[url]' => $payment_redirect));
            Tygh::$app['view']->assign('qiwi_page_text', $qiwi_page_text);
        }
    }

}