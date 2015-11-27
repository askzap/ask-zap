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
use Tygh\Shippings\RusSdek;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'autocomplete_city') {

    $params = $_REQUEST;

    if (defined('AJAX_REQUEST') && $params['q']) {

        $select = array();
        $prefix = array('гор.','г.' ,'г ', 'гор ','город ');

        $params['q'] = str_replace($prefix,'',$params['q']);
        $search = trim($params['q'])."%";
        
        if (preg_match('/^[a-zA-Z]+$/',$params['q'])) {
            if ((Registry::get('addons.rus_cities.status') == 'A')) {
                $city = db_get_field("SELECT a.city FROM ?:rus_city_descriptions as a LEFT JOIN ?:rus_city_descriptions as b ON a.city_id = b.city_id WHERE b.city LIKE ?l AND a.lang_code = ?s AND b.lang_code = ?s  ", $search , 'ru', 'en');
                if (!empty($city)) {
                    $search = trim($city)."%";
                } else {
                    fn_set_notification('E', __('notice'), __('shippings.sdek.lang_error'));
                    exit();
                }
            }
        }

        $join = db_quote("LEFT JOIN ?:rus_cities_sdek as c ON c.city_id = d.city_id");

        $condition = db_quote(" AND c.status = ?s", 'A');

        if (!empty($params['check_country']) && $params['check_country'] != 'undefined') {
            $condition .= db_quote(" AND c.country_code = ?s", $params['check_country']);

            if (!empty($params['check_state']) && $params['check_state'] != 'undefined') {
                $condition .= db_quote(" AND c.state_code = ?s", $params['check_state']);
            }
        }

        $cities = db_get_array("SELECT d.city, c.city_code FROM ?:rus_city_sdek_descriptions as d ?p WHERE city LIKE ?l AND lang_code = ?s  ?p  LIMIT ?i", $join , $search , 'ru', $condition, 10);

        if (!empty($cities)) {
            foreach ($cities as $city) {
                $select[] = array(
                    'code' => $city['city_code'],
                    'value' => $city['city'],
                    'label' => $city['city'],
                );
            }
        }

        Registry::get('ajax')->assign('autocomplete', $select);
        exit();
    }

} elseif ($mode == 'sdek_get_city_data') {
    $params = $_REQUEST;

    if (defined('AJAX_REQUEST')) {

        $location['country'] = 'RU';
        $location['city'] = $params['var_city'];

        $data = RusSdek::cityId($location);

        $city_data = array(
            'from_city_id' => $data,
        );

        Tygh::$app['view']->assign('sdek_new_city_data', $city_data);
        Tygh::$app['view']->display('addons/rus_sdek/views/shippings/components/services/sdek.tpl');
        exit;
    }
}
