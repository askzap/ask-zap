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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update_details') {
        fn_save_order_log($_REQUEST['order_id'], $_SESSION['auth']['user_id'], 'rus_order_logs_order_changed', '', TIME);
    }
}

if ($mode == 'details') {
    $logs = fn_get_order_logs($_REQUEST['order_id']);
    Tygh::$app['view']->assign('logs', $logs);
    Registry::set('navigation.tabs.logs', array(
        'title' => __('logs'),
        'js' => true
    ));
}

if ($mode == 'update_order_logs') {
    if (defined('AJAX_REQUEST')) {
        $logs = fn_get_order_logs($_REQUEST['order_id']);
        Tygh::$app['view']->assign('logs', $logs);
        Tygh::$app['view']->display('addons/rus_order_logs/views/orders/components/order_logs.tpl');
        exit;
    }
}