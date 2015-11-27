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
use Tygh\Http;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'shipping_estimation_city') {

    $params = $_REQUEST;
    if (isset($_SESSION['customer_loc'])) {
        $customer_loc = $_SESSION['customer_loc'];
        if (empty($params['check_city']) && (!empty($customer_loc['city']))) {
            $params['check_city'] = $customer_loc['city'];
            $_SESSION['customer_loc_rus_city'] = $customer_loc['city'];
        }
    }

    if (defined('AJAX_REQUEST')) {
        $cities = array();
        if (Registry::get('addons.rus_cities.status') == 'A') {
            $lang_code = DESCR_SL;

            $join = db_quote("LEFT JOIN ?:rus_cities as c ON c.city_id = d.city_id");

            $condition = db_quote(" AND c.status = ?s", 'A');

            if (!empty($params['check_country']) && $params['check_country'] != 'undefined') {
                $condition .= db_quote(" AND c.country_code = ?s", $params['check_country']);

                if (!empty($params['check_state']) && $params['check_state'] != 'undefined') {
                    $condition .= db_quote(" AND c.state_code = ?s", $params['check_state']);
                }
            }

            $cities = db_get_array("SELECT d.city, c.city_code FROM ?:rus_city_descriptions as d ?p WHERE d.lang_code = ?s ?p", $join , $lang_code, $condition);
        }

        if (empty($params['check_city']) && !empty($_SESSION['customer_loc_rus_city'])) {
            $params['check_city'] = $_SESSION['customer_loc_rus_city'];
        }

        if (!empty($params['check_city'])) {
            $check = false;
            if (!empty($cities)) {
                foreach ($cities as $key => $city) {
                    if ($city['city'] == $params['check_city']) {
                        $check = true;
                        $cities[$key]['active'] = 'Y';
                    }
                }
            }

            if (!$check) {
                Tygh::$app['view']->assign('client_city', $params['check_city']);
            }
        }

        if (!empty($customer_loc['state']) && $customer_loc['state'] != $params['check_state']) {

            if (!empty($_SESSION['customer_loc_rus_city'])) {
                unset($_SESSION['customer_loc_rus_city']);
            }

            Tygh::$app['view']->assign('client_city', '');
        }
        Tygh::$app['view']->assign('cities', $cities);
        Tygh::$app['view']->display('views/checkout/components/shipping_estimation.tpl');
        exit;
    }
}
