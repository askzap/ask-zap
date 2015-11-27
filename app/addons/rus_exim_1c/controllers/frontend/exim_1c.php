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

if (Registry::get('addons.rus_exim_1c.status') != 'A') {
    fn_echo('ADDON DISABLED');
    exit;
}

if (!empty($_SERVER['PHP_AUTH_USER'])) {
    $_data['user_login'] = $_SERVER['PHP_AUTH_USER'];
} else {
    fn_exim_1c_auth_error('EMPTY_USER_1C');
    exit;
}

list($status, $user_data, $user_login, $password, $salt) = fn_auth_routines($_data, array());

if (empty($_SERVER['PHP_AUTH_USER']) || !($user_login == $_SERVER['PHP_AUTH_USER'] && $user_data['password'] == fn_generate_salted_password($_SERVER['PHP_AUTH_PW'], $salt))) {
    fn_exim_1c_auth_error('WRONG_KEY_1C');
    exit;
}

if (!fn_rus_exim_1c_allowed_access($user_data)) {
    fn_echo('ACCESS DENIED');
    exit;
}

$company_id = 0;
if (PRODUCT_EDITION == 'ULTIMATE') {
    if (Registry::get('runtime.simple_ultimate')) {
        $company_id = Registry::get('runtime.forced_company_id');
    } else {
        if ($user_data['company_id'] == 0) {
            fn_echo('SHOP IS NOT SIMPLE');
            exit;
        } else {
            $company_id = $user_data['company_id'];
            Registry::set('runtime.company_id', $company_id);
        }
    }
} elseif ($user_data['user_type'] == 'V') {
    if ($user_data['company_id'] == 0) {
        fn_echo('SHOP IS NOT SIMPLE');
        exit;
    } else {
        $company_id = $user_data['company_id'];
        Registry::set('runtime.company_id', $company_id);
    }
} else {
    Registry::set('runtime.company_id', $company_id);
}

$type = $mode = '';
if (isset($_REQUEST['type'])) {
    $type = $_REQUEST['type'];
}
if (isset($_REQUEST['mode'])) {
    $mode = $_REQUEST['mode'];
}
$filename = (!empty($_REQUEST['filename'])) ? fn_basename($_REQUEST['filename']) : '';

$lang_code = Registry::get('addons.rus_export_1c.exim_1c_lang');
if (empty($lang_code)) {
    $lang_code = CART_LANGUAGE;
}
if ($type == 'catalog') {
    if ($mode == 'checkauth') {
        fn_exim_1c_checkauth();
    } elseif ($mode == 'init') {
        fn_exim_1c_init();
        if (Registry::get('addons.rus_exim_1c.exim_1c_schema_version') == '2.05') {
            fn_exim_1c_clear_1c_dir();
        }
    } elseif ($mode == 'file') {
        if (fn_exim_1c_get_external_file($filename) === false) {
            fn_echo("failure");
            exit;
        }
        fn_echo("success\n");
    } elseif ($mode == 'import') {
        $fileinfo = pathinfo($filename);
        $xml = fn_exim_1c_get_xml($filename);
        if ($xml === false) {
            fn_echo("failure");
            exit;
        }
        if (strpos($fileinfo['filename'], 'import') == 0) {
            fn_exim_1c_import($xml, $user_data, $company_id, $lang_code);
        }
        if (strpos($fileinfo['filename'], 'offers') == 0) {
            fn_exim_1c_offers($xml, $company_id, $lang_code);
        }
        fn_echo("success\n");
    }
} elseif (($type == 'sale') && ($user_data['user_type'] != 'V') && (Registry::get('addons.rus_exim_1c.exim_1c_check_prices') != 'Y')) {
    if ($mode == 'checkauth') {
        fn_exim_1c_checkauth();
    } elseif ($mode == 'init') {
        fn_exim_1c_init();
    //export 1C orders to CS-Cart
    } elseif ($mode == 'file') {
        fn_echo("success\n");
    //export CS-Cart orders to 1C
    } elseif ($mode == 'query') {
        fn_exim_1c_export_orders($company_id, $lang_code);
    } elseif ($mode == 'success') {
        fn_echo("success");
    }
}

exit;
