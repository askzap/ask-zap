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

use Tygh\Ym\ApiClient;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$api = new ApiClient;

if ($mode == 'oauth') {

    if (!empty($_REQUEST['code'])) {
        $api->auth($_REQUEST['code']);
    }

    return array(CONTROLLER_STATUS_REDIRECT, 'addons.update&addon=yandex_market&selected_section=yandex_market_purchase');

}

if ($mode == 'api_test') {

    $res = $api->test();
    fn_print_die($res);

}
