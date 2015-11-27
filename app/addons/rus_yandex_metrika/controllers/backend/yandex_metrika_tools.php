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
use Tygh\RestClient;
use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'oauth') {

    if (!empty($_REQUEST['code'])) {
        $client = new RestClient(
            'https://oauth.yandex.ru/',
            Registry::get('addons.rus_yandex_metrika.application_id'),
            Registry::get('addons.rus_yandex_metrika.application_password'),
            'basic',
            array(),
            ''
        );
        $res = $client->post('token', array(
            'grant_type' => 'authorization_code',
            'code' => $_REQUEST['code'],
        ));
        $result = json_decode($res, true);
        if (!empty($result['access_token'])) {
            Settings::instance()->updateValue('auth_token', $result['access_token'], 'rus_yandex_metrika');
        }
    }

    return array(CONTROLLER_STATUS_REDIRECT, 'addons.update&addon=rus_yandex_metrika');

}
