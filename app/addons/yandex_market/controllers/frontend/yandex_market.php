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
use Tygh\Ym\Yml;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'view') {

    if (Registry::get('addons.yandex_market.enable_authorization') == 'Y') {
        $user_data = fn_yandex_auth();
    }

    $company_id = Registry::get('runtime.company_id');

    if (fn_allowed_for('MULTIVENDOR')) {
        $company_id = 0;
        if (!empty($user_data) && ($user_data['user_type'] == 'V')) {
            $company_id = $user_data['company_id'];
        }
    }

    $options = Registry::get('addons.yandex_market');
    $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 0;

    $lang_code = DESCR_SL;
    if (Registry::isExist('languages.ru')) {
        $lang_code = 'ru';
    }

    $yml = new Yml($company_id, $options, $lang_code, $page, isset($_REQUEST['debug']));

    $yml->get();

    exit;

}
