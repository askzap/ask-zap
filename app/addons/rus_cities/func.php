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

if ( !defined('AREA') ) { die('Access denied'); }

function fn_rus_cities_uninstall()
{
    db_query ("DROP TABLE IF EXISTS `?:rus_cities`");
    db_query ("DROP TABLE IF EXISTS `?:rus_city_descriptions`");
}

function fn_get_cities($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    // Set default values to input params
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);

    $fields = array(
        'c.city_id',
        'c.country_code',
        'c.state_code',
        'c.city_code',
        'c.status',
        'cd.city',
    );

    $condition = '';
    if (!empty($params['only_avail'])) {
        $condition .= db_quote(" AND c.status = ?s", 'A');
    }

    if (!empty($params['q'])) {
        $condition .= db_quote(" AND cd.city LIKE ?l", '%' . $params['q'] . '%');
    }

    if (!empty($params['state_code'])) {
        $condition .= db_quote(" AND c.state_code = ?s", $params['state_code']);
    }

    if (!empty($params['country_code'])) {
        $condition .= db_quote(" AND c.country_code = ?s", $params['country_code']);
    }

    $join = "LEFT JOIN ?:rus_city_descriptions as cd ON cd.city_id = c.city_id AND cd.lang_code = ?s ";

    $limit = '';

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT count(*) FROM ?:rus_cities as c $join WHERE 1 ?p", $lang_code,  $condition);
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }

    $cities = db_get_array(
        "SELECT " . implode(', ', $fields) . " FROM ?:rus_cities as c $join WHERE 1 ?p ORDER BY cd.city $limit",
    $lang_code, $condition);

    foreach ($cities as &$city) {
        if (empty($city['city'])) {
            $city['city'] = db_get_field("SELECT city FROM ?:rus_city_descriptions WHERE city_id = ?i AND lang_code = 'ru'", $city['city_id']);
        }
    }

    return array($cities, $params);
}

function fn_get_all_cities($avail_only = true, $lang_code = CART_LANGUAGE)
{
    $avail_cond = ($avail_only == true) ? " WHERE a.status = 'A' AND b.status = 'A'" : '';

    //return db_get_hash_multi_array("SELECT a.country_code, a.code, b.state FROM ?:states as a LEFT JOIN ?:state_descriptions as b ON b.state_id = a.state_id AND b.lang_code = ?s $avail_cond ORDER BY a.country_code, b.state", array('country_code'), $lang_code);

    $countries = db_get_hash_multi_array("SELECT a.country_code, a.code as state_id, b.code, c.city, b.city_id FROM ?:states as a " .
        "LEFT JOIN ?:rus_cities as b ON b.state_id = a.state_id " .
        "LEFT JOIN ?:rus_city_descriptions as c ON c.city_id = b.city_id AND c.lang_code = ?s " .
        "$avail_cond ORDER BY a.country_code, b.code, c.city", array('country_code'), $lang_code);

    $rus_countries = db_get_hash_array("SELECT city_id, city FROM ?:rus_city_descriptions WHERE lang_code = ?s", 'city_id', 'ru');

    $cities = array();

    foreach ($countries as $c_code => $states) {
        foreach ($states as $city) {
            if (!empty($city['city_id'])) {
                $cities[$c_code][$city['state_id']][] = array(
                    'code' => $city['code'],
                    'city' => empty($city['city']) ? $rus_countries[$city['city_id']]['city'] : $city['city']
                );
            }
        }
    }

    return $cities;
}
