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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Registry;
use Tygh\Ym\Api;
use Tygh\Ym\OrderStatus;
use Tygh\Ym\Yml;

/**
 * Hooks
 */

function fn_yandex_market_get_rewrite_rules(&$rewrite_rules, &$prefix, &$extension)
{
    // Yandex Market Pricelist
    $rewrite_rules['!^\/yandex_market([0-9]*)\.yml$!'] = '$customer_index?dispatch=yandex_market.view&page=$matches[1]';
    $rewrite_rules['!^' . $prefix . '\/yandex_market([0-9]*)\.yml$!'] = '$customer_index?dispatch=yandex_market.view&page=$matches[2]';
}

function fn_yandex_market_tools_change_status(&$params, &$result)
{
    if (
        !empty($params['table'])
        && in_array($params['table'], array('products', 'categories'))
        && $result
    ) {
        Yml::clearCaches();
    }
}

function fn_yandex_market_update_product_post(&$product_data, &$product_id, &$lang_code, &$create)
{
    Yml::clearCaches();
}

function fn_yandex_market_delete_product_post(&$product_id, &$product_deleted)
{
    if ($product_deleted) {
        Yml::clearCaches();
    }
}

function fn_yandex_market_update_category_post(&$category_data, &$category_id, &$lang_code)
{
    $company_id = isset($category_data['company_id']) ? $category_data['company_id'] : 0;
    Yml::clearCaches($company_id);
}

function fn_yandex_market_delete_category_post(&$category_id, &$recurse, &$category_ids)
{
    Yml::clearCaches();
}

function fn_yandex_market_change_order_status(&$status_to, &$status_from, &$order_info, &$force_notification, &$order_statuses, &$place_order)
{
    Yml::clearCaches($order_info['company_id']);

    if (!empty($order_info['yandex_market'])) {
        if ($place_order) {
            $force_notification = array('C' => false); // We don't know real email yet
        } else {
            $status_obj = new OrderStatus($order_info);
            $res = $status_obj->change($status_to, $status_from);
            if ($res === false) {
                $status_to = $status_from; // Prevent
            }
        }
    }
}

function fn_yandex_market_api_handle_request(&$api, &$authorized)
{
    $api_namespace = 'ym/';

    $request = $api->getRequest();
    $resource = $request->getResource();

    if (strpos($resource, $api_namespace) === 0) {
        // Prepare resource
        $resource = substr($resource, strlen($api_namespace));
        $resource = trim($resource, '/');

        $method = $request->getMethod();
        $data = $request->getData();
        $accept_type = $request->getAcceptType();

        $ym_api = new Api($resource, $method, $data, $accept_type);
        $ym_api->handleRequest();
    }
}

function fn_yandex_market_shippings_get_shippings_list_conditions(&$group, &$shippings, &$fields, &$join, &$condition, &$order_by)
{
    $fields[] = '?:shippings.yml_shipping_type';
    $fields[] = '?:shippings.yml_outlet_ids';
}

function fn_yandex_market_place_order(&$order_id, &$action, &$order_status, &$cart, &$auth)
{
    if (!empty($cart['yandex_market'])) {
        fn_yandex_market_update_order_ym_data($order_id, $cart['yandex_market']);
    }
}

function fn_yandex_market_get_order_info(&$order, &$additional_data)
{
    if (!empty($additional_data['Y'])) {
        $order['yandex_market'] = unserialize($additional_data['Y']);
    }
}

/**
 * \Hooks
 */


/**
 * Handlers
 */

function fn_yandex_market_clear_url_info()
{
    $storefront_url = Registry::get('config.http_location');
    if (fn_allowed_for('ULTIMATE')) {
        if (Registry::get('runtime.company_id') || Registry::get('runtime.simple_ultimate')) {
            $company = Registry::get('runtime.company_data');
            $storefront_url = 'http://' . $company['storefront'];
        } else {
            $storefront_url = '';
        }
    }

    if (!empty($storefront_url)) {
        $yml_available_in_customer = __('yml_available_in_customer', array(
            '[http_location]' => $storefront_url,
            '[yml_url]' => fn_url('yandex_market.view', 'C', 'http'),
        ));
    } else {
        $yml_available_in_customer = '';
    }

    return __('yml_clear_cache_info', array(
        '[clear_cache_url]' =>  fn_url('addons.update?addon=yandex_market?cc'),
        '[yml_available_in_customer]' => $yml_available_in_customer
    ));

}

function fn_yandex_market_purchase_get_info()
{
    return __('yml_purchase_info');
}

function fn_settings_variants_addons_yandex_market_feature_for_brand()
{
    $brands = db_get_hash_single_array(
        "SELECT a.feature_id, b.description"
        . " FROM ?:product_features as a"
        . " LEFT JOIN ?:product_features_descriptions as b ON a.feature_id = b.feature_id"
        . " WHERE a.feature_type = ?s AND b.lang_code = ?s",
        array('description', 'description'), 'E', DESCR_SL
    );

    return $brands;
}

function fn_settings_variants_addons_yandex_market_feature_for_vendor_code()
{
    $brands = db_get_hash_single_array(
        "SELECT a.feature_id, b.description"
        . " FROM ?:product_features as a"
        . " LEFT JOIN ?:product_features_descriptions as b ON a.feature_id = b.feature_id"
        . " WHERE b.lang_code = ?s",
        array('description', 'description'), DESCR_SL
    );

    return array_merge(array('' => ' -- '), $brands);
}

function fn_yandex_market_get_order_statuses_for_setting()
{
    static $data;

    if (empty($data)) {
        $data = array(
            '' => ' -- '
        );

        foreach (fn_get_statuses(STATUSES_ORDER) as $status) {
            $data[$status['status']] = $status['description'];
        }
    }

    return $data;
}

function fn_settings_variants_addons_yandex_market_order_status_unpaid()
{
    return fn_yandex_market_get_order_statuses_for_setting();
}

function fn_settings_variants_addons_yandex_market_order_status_processing()
{
    return fn_yandex_market_get_order_statuses_for_setting();
}

function fn_settings_variants_addons_yandex_market_order_status_canceled()
{
    return fn_yandex_market_get_order_statuses_for_setting();
}

function fn_settings_variants_addons_yandex_market_order_status_delivery()
{
    return fn_yandex_market_get_order_statuses_for_setting();
}

function fn_settings_variants_addons_yandex_market_order_status_pickup()
{
    return fn_yandex_market_get_order_statuses_for_setting();
}

function fn_settings_variants_addons_yandex_market_order_status_delivered()
{
    return fn_yandex_market_get_order_statuses_for_setting();
}

function fn_yandex_market_oauth_info()
{
    if (
        !fn_string_not_empty(Registry::get('addons.yandex_market.ym_application_id'))
        || !fn_string_not_empty(Registry::get('addons.yandex_market.ym_application_password'))
    ) {
        return __('yml_aouth_info_part1', array(
            '[callback_uri]' => fn_url('ym_tools.oauth')
        ));
    } else {
        $client_id = Registry::get('addons.yandex_market.ym_application_id');

        return __('yml_aouth_info_part2', array(
            '[auth_uri]' => "https://oauth.yandex.ru/authorize?response_type=code&client_id=" . $client_id,
            '[edit_app_uri]' => "https://oauth.yandex.ru/client/edit/" . $client_id,
        ));
    }
}

/**
 * \Handlers
 */


/**
 * Functions
 */

function fn_yandex_market_addon_install()
{
    // Order statuses
    $statuses = array(
        array(
            'status' => 'X',
            'is_default' => 'N',
            'description' => __('yml_status_pickup'),
            'email_subj' => __('yml_status_pickup'),
            'email_header' => __('yml_status_pickup'),
            'params' => array(
                'color' => '#6aa84f',
                'notify' => 'Y',
                'notify_department' => 'N',
                'repay' => 'N',
                'inventory' => 'D',
            ),
        ),
        array(
            'status' => 'W',
            'is_default' => 'N',
            'description' => __('yml_status_delivered'),
            'email_subj' => __('yml_status_delivered'),
            'email_header' => __('yml_status_delivered'),
            'params' => array(
                'color' => '#76a5af',
                'notify' => 'N',
                'notify_department' => 'N',
                'repay' => 'N',
                'inventory' => 'D',
            ),
        ),
    );

    foreach ($statuses as $status) {
        $exists = db_get_field(
            "SELECT status_id FROM ?:statuses WHERE status = ?s AND type = ?s",
            $status['status'], STATUSES_ORDER
        );
        if (!$exists) {
            fn_update_status('', $status, STATUSES_ORDER);
        }
    }
}

function fn_get_market_categories()
{
    return fn_get_schema('yandex_market', 'categories');
}

function fn_yandex_market_get_shipping_types($with_lang = false)
{
    $types = array(
        'delivery',
        'pickup',
        'post',
    );

    if ($with_lang) {
        $data = array();
        foreach ($types as $type) {
            $data[$type] = __('yml_shipping_type_' . $type);
        }

        return $data;
    }

    return $types;
}

function fn_yandex_market_update_order_ym_data($order_id, $data)
{
    db_query("REPLACE INTO ?:order_data ?e", array(
        'order_id' => $order_id,
        'type' => 'Y', // Yandex market
        'data' => serialize($data),
    ));
}

function fn_yandex_market_array_to_yml($data, $level = 0)
{
    if (!is_array($data)) {
        return fn_html_escape($data);
    }

    $return = '';
    foreach ($data as $key => $value) {
        $attr = '';
        if (is_array($value) && is_numeric(key($value))) {
            foreach ($value as $k => $v) {
                $arr = array(
                    $key => $v
                );
                $return .= fn_array_to_xml($arr);
                unset($value[$k]);
            }
            unset($data[$key]);
            continue;
        }

        if (strpos($key, '@') !== false) {
            $data = explode('@', $key);
            $key = $data[0];
            unset($data[0]);

            if (count($data) > 0) {
                foreach ($data as $prop) {
                    if (strpos($prop, '=') !== false) {
                        $prop = explode('=', $prop);
                        $attr .= ' ' . $prop[0] . '="' . $prop[1] . '"';
                    } else {
                        $attr .= ' ' . $prop . '=""';
                    }
                }
            }
        }

        if (strpos($key, '+') !== false) {
            list($key) = explode('+', $key, 2);
        }

        $tab = str_repeat('    ', $level);

        if (empty($value)) {
            if ($key == 'local_delivery_cost') {
                $return .= $tab . "<" . $key . $attr . ">" . fn_yandex_market_array_to_yml($value, $level + 1) . '</' . $key . ">\n";
            } else {
                $return .= $tab . "<" . $key . $attr . "/>\n";
            }

        } elseif (is_array($value)) {
            $return .= $tab . "<" . $key . $attr . ">\n" . fn_yandex_market_array_to_yml($value, $level + 1) . '</' . $key . ">\n";

        } else {
            $return .= $tab . "<" . $key . $attr . '>' . fn_yandex_market_array_to_yml($value, $level + 1) . '</' . $key . ">\n";
        }

    }

    return $return;
}

function fn_yandex_market_c_encode($s)
{
    $rep = array(
        ' ' => '%20',
        'а' => '%D0%B0', 'А' => '%D0%90',
        'б' => '%D0%B1', 'Б' => '%D0%91',
        'в' => '%D0%B2', 'В' => '%D0%92',
        'г' => '%D0%B3', 'Г' => '%D0%93',
        'д' => '%D0%B4', 'Д' => '%D0%94',
        'е' => '%D0%B5', 'Е' => '%D0%95',
        'ё' => '%D1%91', 'Ё' => '%D0%81',
        'ж' => '%D0%B6', 'Ж' => '%D0%96',
        'з' => '%D0%B7', 'З' => '%D0%97',
        'и' => '%D0%B8', 'И' => '%D0%98',
        'й' => '%D0%B9', 'Й' => '%D0%99',
        'к' => '%D0%BA', 'К' => '%D0%9A',
        'л' => '%D0%BB', 'Л' => '%D0%9B',
        'м' => '%D0%BC', 'М' => '%D0%9C',
        'н' => '%D0%BD', 'Н' => '%D0%9D',
        'о' => '%D0%BE', 'О' => '%D0%9E',
        'п' => '%D0%BF', 'П' => '%D0%9F',
        'р' => '%D1%80', 'Р' => '%D0%A0',
        'с' => '%D1%81', 'С' => '%D0%A1',
        'т' => '%D1%82', 'Т' => '%D0%A2',
        'у' => '%D1%83', 'У' => '%D0%A3',
        'ф' => '%D1%84', 'Ф' => '%D0%A4',
        'х' => '%D1%85', 'Х' => '%D0%A5',
        'ц' => '%D1%86', 'Ц' => '%D0%A6',
        'ч' => '%D1%87', 'Ч' => '%D0%A7',
        'ш' => '%D1%88', 'Ш' => '%D0%A8',
        'щ' => '%D1%89', 'Щ' => '%D0%A9',
        'ъ' => '%D1%8A', 'Ъ' => '%D0%AA',
        'ы' => '%D1%8B', 'Ы' => '%D0%AB',
        'ь' => '%D1%8C', 'Ь' => '%D0%AC',
        'э' => '%D1%8D', 'Э' => '%D0%AD',
        'ю' => '%D1%8E', 'Ю' => '%D0%AE',
        'я' => '%D1%8F', 'Я' => '%D0%AF'
    );

    $s = strtr($s, $rep);

    return $s;
}

function fn_yandex_market_check_country($country)
{
    $countries = fn_get_schema('yandex_market', 'countries');

    return isset($countries[$country]);
}

function fn_yandex_auth_error($msg)
{
    header('WWW-Authenticate: Basic realm="Authorization required"');
    header('HTTP/1.0 401 Unauthorized');
    fn_echo($msg);
    exit;
}

function fn_yandex_auth()
{
    if (!empty($_SERVER['PHP_AUTH_USER'])) {

        $_data = array(
            'user_login' => $_SERVER['PHP_AUTH_USER'],
            'password' => $_SERVER['PHP_AUTH_PW'],
        );

        $_auth = array();
        list($status, $user_data, $user_login, $password, $salt) = fn_auth_routines($_data, $_auth);

        if (
            !empty($user_data)
            && $user_data['status'] == 'A'
            && in_array($user_data['user_type'], array('A', 'V'))
            && $user_data['password'] == fn_generate_salted_password($_SERVER['PHP_AUTH_PW'], $salt)
        ) {
            return $user_data;
        }

    }

    fn_yandex_auth_error(__("error"));
}

/**
 * \Functions
 */
