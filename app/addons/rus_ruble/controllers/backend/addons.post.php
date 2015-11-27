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

if ($mode == 'update') {

    if ($_REQUEST['addon'] == 'rus_ruble') {

        $exist_currency_rub = true;
        $currencies = Registry::get('currencies');

        if (empty($currencies[CURRENCY_RUB])) {
            $exist_currency_rub = false;
        }

        Tygh::$app['view']->assign('exist_currency_rub', $exist_currency_rub);

    }
}
