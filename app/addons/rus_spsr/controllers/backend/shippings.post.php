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
use Tygh\RusSpsr;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'configure') {

    if ($_REQUEST['module'] == 'spsr') {
        $type_products = array();

        $login = RusSpsr::WALogin();
        if ($login) {
            $type_products = RusSpsr::WAGetEncloseType();

            if ($_REQUEST['module'] == 'spsr') {
                $location['country'] = Registry::get('settings.General.default_country');
                $location['city'] = Registry::get('settings.General.default_city');
                $data = RusSpsr::WAGetCities($location);

                Tygh::$app['view']->assign('from_city_id', $data['City_ID']);
                Tygh::$app['view']->assign('from_city_owner_id', $data['City_owner_ID']);
                Tygh::$app['view']->assign('city', $location['city']);
            }
            RusSpsr::WALogout();

            Tygh::$app['view']->assign('type_products', $type_products);
        } else {
            fn_set_notification('E', __('notice'), RusSpsr::$last_error);
        }
    }
}

if ($mode == 'spsr_get_city_data') {
    $params = $_REQUEST;

    if (defined('AJAX_REQUEST')) {
        $location['country'] = Registry::get('settings.General.default_country');
        $location['city'] = $params['var_city'];

        RusSpsr::WALogin();
        $data = RusSpsr::WAGetCities($location);
        RusSpsr::WALogout();

        $city_data = array(
            'from_city_id' => $data['City_ID'],
            'from_city_owner_id' => $data['City_owner_ID'],
        );

        Tygh::$app['view']->assign('spsr_new_city_data', $city_data);
        Tygh::$app['view']->display('addons/rus_spsr/views/shippings/components/services/spsr.tpl');
        exit;
    }
}
