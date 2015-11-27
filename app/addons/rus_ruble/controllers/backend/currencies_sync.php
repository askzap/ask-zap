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
use Tygh\RusCurrency;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'sync') {

        if (isset($_REQUEST['magic_key'])) {
            $sync_status = fn_rus_ruble_sync($_REQUEST['magic_key']);
        } else {
            $sync_status = SYNC_MAGIC_KEY_EMPTY;
        }

        if ($sync_status == SYNC_OK) {
            fn_set_notification('N', __('notice'), __('rus_ruble.sbrf_currencies_successfully_sync'));

        } elseif ($sync_status == SYNC_ERROR) {
            fn_set_notification('W', __('warning'), __('rus_ruble.sbrf_currencies_unsuccessfully_sync'));

        } elseif ($sync_status == SYNC_MAGIC_KEY_EMPTY) {
            fn_set_notification('W', __('warning'), __('rus_ruble.magic_key_empty'));

        } elseif ($sync_status == SYNC_MAGIC_KEY_INCORRECT) {
            fn_set_notification('W', __('warning'), __('rus_ruble.magic_key_incorrect'));

        } elseif ($sync_status == SYNC_NOT_SET_RUB) {
            fn_set_notification('W', __('warning'), __('rus_ruble.not_set_ruble'));
        }

        return array(CONTROLLER_STATUS_REDIRECT, 'addons.update?addon=rus_ruble');

    } elseif ($mode == 'symbol_update') {

        $currencies = Registry::get('currencies');

        if (!empty($currencies[CURRENCY_RUB])) {
            RusCurrency::symbol_update();

        } else {
            fn_set_notification('E', __('error'), __('rus_ruble.symbol_no_currency_rub'));
        }

        return array (CONTROLLER_STATUS_REDIRECT, 'addons.update?addon=rus_ruble');

    } elseif ($mode == 'symbol_install') {

        $currencies = Registry::get('currencies');

        if (empty($currencies[CURRENCY_RUB])) {
            $symbol = RusCurrency::rub_create();
        }

        fn_rus_ruble_gen_magic_key();

        return array (CONTROLLER_STATUS_REDIRECT, 'addons.update?addon=rus_ruble');

    } elseif ($mode == 'keygen') {

        fn_rus_ruble_gen_magic_key();

        return array (CONTROLLER_STATUS_REDIRECT, 'addons.update?addon=rus_ruble');
    }
}

if ($mode == 'sync_cron') {

    if (isset($_REQUEST['magic_key'])) {
        $sync_status = fn_rus_ruble_sync($_REQUEST['magic_key']);
    } else {
        $sync_status = SYNC_MAGIC_KEY_EMPTY;
    }

    if ($sync_status == SYNC_OK) {
        fn_echo(__('rus_ruble.sbrf_currencies_successfully_sync'));
    } else {
        fn_echo(__('rus_ruble.sbrf_currencies_unsuccessfully_sync'));
    }

    exit;
}
