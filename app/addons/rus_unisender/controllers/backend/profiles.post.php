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
    return;
}

//
// View page details
//
if ($mode == 'update') {
    if (!empty($_REQUEST['user_type']) && ($_REQUEST['user_type'] == 'C')) {

        if (fn_check_permissions('unisender', 'send_sms', 'admin', 'GET')) {
            Tygh::$app['view']->assign('show_tab_send_sms', true);
            Registry::set('navigation.tabs.message', array(
                'title' => __('addons.rus_unisender.sms_message'),
                'js' => true
            ));
        }
    }

} elseif ($mode == 'manage') {

    if (fn_allowed_for('MULTIVENDOR') || Registry::get('runtime.company_id') || Registry::get('runtime.simple_ultimate')) {
        Tygh::$app['view']->assign('show_unisender_tool', true);
    }
}
