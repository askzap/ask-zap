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

if (empty($_REQUEST['country_code'])) {
    $_REQUEST['country_code'] = Registry::get('settings.General.default_country');
}

$params = $_REQUEST;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //
    // Create/update city
    //
    if ($mode == 'update') {

        if (empty($params['city_data']['state_code'])) {
            fn_set_notification("E",__("error"),__("not_selected_state"));

            return array(CONTROLLER_STATUS_REDIRECT, "cities.manage?state_id=&country_code={$params['city_data']['country_code']}");
        }

        fn_update_city($params['city_data'], $params['city_id'], DESCR_SL);

        return array(CONTROLLER_STATUS_OK, "cities.manage?state_code={$params['city_data']['state_code']}&country_code={$params['city_data']['country_code']}");
    }

    //
    // Updating existing cities
    //
    if ($mode == 'm_update') {

        foreach ($params['cities'] as $key => $_data) {
            if (!empty($_data)) {
                fn_update_city($_data, $key, DESCR_SL);
            }
        }
    }

    //
    // Delete selected cities
    //
    if ($mode == 'm_delete') {

        if (!empty($params['city_ids'])) {
            foreach ($params['city_ids'] as $v) {
                db_query("DELETE FROM ?:rus_cities WHERE city_id = ?i", $v);
                db_query("DELETE FROM ?:rus_city_descriptions WHERE city_id = ?i", $v);
            }
        }
    }

    if ($mode == 'delete') {

        if (Registry::get('runtime.company_id')) {
            return array(CONTROLLER_STATUS_REDIRECT, "cities.manage");
        }

        if (!empty($_REQUEST['city_id'])) {
            db_query("DELETE FROM ?:rus_cities WHERE city_id = ?i", $params['city_id']);
            db_query("DELETE FROM ?:rus_city_descriptions WHERE city_id = ?i", $params['city_id']);
        }
    }

    $redirect_url = "cities.manage?country_code={$params['country_code']}";

    if (!empty($params['state_code'])) {
        $redirect_url .= "&state_code={$params['state_code']}";
    }

    return array(CONTROLLER_STATUS_OK, $redirect_url);
}

if ($mode == 'manage') {

    if (empty($_REQUEST['country_code'])) {
        $params['country_code'] = Registry::get('settings.General.default_country');
    }

    list($cities, $search) = fn_get_cities($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

    $states = fn_get_all_states();
    $countries = fn_get_simple_countries(false, DESCR_SL);
    if (!empty($cities)) {
        foreach ($cities as $key => $city) {
            $cities[$key]['state_name'] = fn_get_state_name($city['state_code'], $city['country_code'], DESCR_SL);
            $cities[$key]['country_name'] = $countries[$city['country_code']];
        }
    }

    Tygh::$app['view']->assign('countries', $countries);
    Tygh::$app['view']->assign('states', $states);
    Tygh::$app['view']->assign('cities', $cities);
    Tygh::$app['view']->assign('search', $search);

}

function fn_update_city($city_data, $city_id = 0, $lang_code = DESCR_SL)
{

    if (empty($city_id)) {

        if (!empty($city_data['city']) && !empty($city_data['city_code'])) {
            $city_data['city_id'] = $city_id = db_query("REPLACE INTO ?:rus_cities ?e", $city_data);

            foreach (fn_get_translation_languages() as $city_data['lang_code'] => $_v) {
                db_query('REPLACE INTO ?:rus_city_descriptions ?e', $city_data);
            }
        }
    } else {
        if (!empty($city_data['city']) && !empty($city_data['city_code'])) {

            db_query("UPDATE ?:rus_cities SET ?u WHERE city_id = ?i", $city_data, $city_id);

            $exist = db_get_field("SELECT city_id FROM ?:rus_city_descriptions WHERE city_id = ?i AND lang_code = ?s", $city_id, $lang_code);

            if ($exist)
                db_query("UPDATE ?:rus_city_descriptions SET ?u WHERE city_id = ?i AND lang_code = ?s", $city_data, $city_id, $lang_code);
            else {
                $city_data['city_id'] = $city_id;
                $city_data['lang_code'] = $lang_code;
                db_query("INSERT INTO ?:rus_city_descriptions ?e", $city_data);
            }
        }
    }

    return $city_id;
}
