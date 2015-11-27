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

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if ($mode == 'place_order' || $mode == 'subscribe_unisender_customer') {

        $user_data = $_SESSION['cart']['user_data'];
        $subscriber_id = fn_unisender_get_subscriber_id($user_data['email']);

        if (!empty($_REQUEST['unisender_lists']) && !fn_is_empty($_REQUEST['unisender_lists'])) {
            if (empty($subscriber_id)) {
                $subscriber_id = fn_unisender_add_subscriber($user_data['email']);
            }
            fn_unisender_subscribe($user_data, reset($_REQUEST['unisender_lists']), true);

        } else {
            if (!empty($subscriber_id)) {
                fn_unisender_unsubscribe($subscriber_id);
            }
        }

        if ($mode == 'subscribe_unisender_customer') {
            return array(CONTROLLER_STATUS_REDIRECT, 'checkout.checkout');
        }
    }
}

if ($mode == 'checkout' || $mode == 'customer_info') {

    $email = db_get_field("SELECT email FROM ?:users WHERE user_id = ?i", $_SESSION['auth']['user_id']);

    if ((empty($email) || $_SESSION['auth']['user_id'] == 0) && !empty($_SESSION['cart']['user_data']['email'])) {
        $email = $_SESSION['cart']['user_data']['email'];
    }

    $mailing_lists = fn_unisender_get_user_lists($email);
    Tygh::$app['view']->assign('unisender_user_mailing_lists', $mailing_lists);
    Tygh::$app['view']->assign('unisender_page_mailing_lists', fn_unisender_get_enabled_lists());
}
