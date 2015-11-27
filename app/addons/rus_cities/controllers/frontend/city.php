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

if ($mode == 'autocomplete_city') {

    $params = $_REQUEST;

    if (defined('AJAX_REQUEST') && $params['q']) {

        $select = array();
        $prefix = array('гор.','г.' ,'г ', 'гор ','город ');

        if (preg_match('/^[a-zA-Z]+$/',$params['q'])) {
            $lang_code = 'en';
        } else {
            $lang_code = 'ru';
        }

        $params['q'] = str_replace($prefix,'',$params['q']);

        if (Registry::get('addons.rus_spsr.status') == 'A') {
            $server = 'spsr';
        } elseif (Registry::get('addons.rus_sdek.status') == 'A') {
            $server = 'sdek';
        } else {
            $server = 'edost';
        }

        if ($server == 'spsr') {

            $location['city'] = $params['q'];
            $cities = rus_spsr_get_cities($location);

            if (!empty($cities)) {
                $cities = array_slice($cities , 0, 15);
                foreach ($cities as $city) {
                    $select[] = array(
                        'code' => $city['City_ID'],
                        'value' => $city['CityName'],
                        'label' => $city['CityName'] . ' ('. $city['RegionName'] .')',
                    );
                }
            }

        } else {

            if ($server == 'sdek') {
                $table = '?:rus_cities_sdek';
                $table_description = '?:rus_city_sdek_descriptions';
            } elseif ($server == 'edost') {
                $table = '?:rus_cities';
                $table_description = '?:rus_city_descriptions';
            }

            $search = trim($params['q'])."%";

            $join = db_quote("LEFT JOIN $table as c ON c.city_id = d.city_id");

            $condition = db_quote(" AND c.status = ?s", 'A');

            if (!empty($params['check_country']) && $params['check_country'] != 'undefined') {
                $condition .= db_quote(" AND c.country_code = ?s", $params['check_country']);

                if (!empty($params['check_state']) && $params['check_state'] != 'undefined') {
                    $condition .= db_quote(" AND c.state_code = ?s", $params['check_state']);
                }
            }

            $cities = db_get_array("SELECT d.city, c.city_code FROM $table_description as d ?p WHERE city LIKE ?l AND lang_code = ?s  ?p  LIMIT ?i", $join , $search , $lang_code, $condition, 10);

            if (!empty($cities)) {
                foreach ($cities as $city) {
                    $select[] = array(
                        'code' => $city['city_code'],
                        'value' => $city['city'],
                        'label' => $city['city'],
                    );
                }
            }
        }

        Registry::get('ajax')->assign('autocomplete', $select);
        exit();
    }

}
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

        if (empty($params['check_city']) && !empty($_SESSION['customer_loc_rus_city'])) {
            $params['check_city'] = $_SESSION['customer_loc_rus_city'];
        }

        if (!empty($params['check_city'])) {
            $check = false;
            foreach ($cities as $key => $city) {
                if ($city['city'] == $params['check_city']) {
                    $check = true;
                    $cities[$key]['active'] = 'Y';
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

function rus_spsr_get_cities($location)
{
    $city_name = $location['city'];

    $url = 'http://api.spsr.ru/waExec/WAExec';

    $extra = array (
        'headers' => array('Content-Type: application/xml'),
    );

    if (!empty($location['country'])) {
        $country_name = fn_get_country_name($location['country'], 'ru');
    } else {
        $country_name = '';
    }

    $data=<<<EOT
<root xmlns="http://spsr.ru/webapi/Info/GetCities/1.0">
<p:Params Name="WAGetCities" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0" />
<GetCities CityName="{$city_name}" CountryName="{$country_name}"/>
</root>
EOT;

    $response = Http::post($url, $data, $extra);

    $xml = @simplexml_load_string($response);

    $status_code = (string) $xml->Result['RC'];

    if ($status_code != 0) {
        return false;
    } else {

        if (isset($xml->City->Cities) && !empty($xml->City)) {

            $result = array();

            foreach ($xml->City->Cities as $key => $city) {
                $city_data = array();
                foreach ($city->attributes() as $a_key => $value) {
                    $city_data[$a_key] = (string) $value;
                }
                $result[] = $city_data;
            }
        }
    }

    if (empty($result)) {
        return false;
    }

    return $result;
}
