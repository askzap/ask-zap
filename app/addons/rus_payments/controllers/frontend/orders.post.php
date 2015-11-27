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

if ($mode == 'details') {

    $order_info = fn_get_order_info($_REQUEST['order_id']);

    if (isset($order_info['payment_method']['processor_params']['sbrf_enabled']) && $order_info['payment_method']['processor_params']['sbrf_enabled'] == "Y" ) {
        $navigation_tabs = Registry::get('navigation.tabs');
        $navigation_tabs['payment_information'] = array(
            'title' => __('payment_information'),
            'js' => true,
            'href' => 'orders.details?order_id=' . $_REQUEST['order_id'] . '&selected_section=payment_information'
        );

        $temp_dir = Registry::get('config.dir.cache_misc') . 'tmp/';
        fn_mkdir($temp_dir);

        $path = fn_qr_generate($order_info, '|', $temp_dir);
        $url_qr_code = Registry::get('config.current_location') . '/' . fn_get_rel_dir($path);
        Tygh::$app['view']->assign('url_qr_code', $url_qr_code);

        Registry::set('navigation.tabs', $navigation_tabs);
    }

    if (isset($order_info['payment_method']['processor_params']['account_enabled']) && $order_info['payment_method']['processor_params']['account_enabled'] == "Y" ) {
        $navigation_tabs = Registry::get('navigation.tabs');
        $navigation_tabs['payment_information'] = array(
            'title' => __('payment_information'),
            'js' => true,
            'href' => 'orders.details?order_id=' . $_REQUEST['order_id'] . '&selected_section=payment_information'
        );
        Registry::set('navigation.tabs', $navigation_tabs);

        if (!empty($order_info['payment_id']) && !empty($order_info['payment_info'])) {
            Tygh::$app['view']->assign('account_params', $order_info['payment_info']);
        }
    }

    if (!empty($_REQUEST['payment_id'])) {
        $payment_info = fn_get_payment_method_data($_REQUEST['payment_id']);
        Tygh::$app['view']->assign('payment_info', $payment_info);
    }

}
