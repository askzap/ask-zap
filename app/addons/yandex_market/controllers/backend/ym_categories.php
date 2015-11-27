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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'picker') {

    $categories_tree = fn_get_market_categories();
    Tygh::$app['view']->assign('categories_tree', $categories_tree);

    if (!empty($_REQUEST['obj_id'])) {
        Tygh::$app['view']->assign('obj_id', $_REQUEST['obj_id']);
    }

    Tygh::$app['view']->display('addons/yandex_market/views/ym_categories/picker.tpl');
    exit;

} elseif ($mode == 'autocomplete') {

    if (!empty($_REQUEST['q'])) {

        $result = array();
        $count = 0;

        foreach (fn_get_market_categories() as $value) {
            $res = mb_stripos($value, $_REQUEST['q']);

            if ($res === false) {
                continue;
            } else {
                $result[] = array(
                    'value' => $value,
                    'label' => $value,
                );
                $count ++;
            }

            if ($count >= YM_CATEGORIES_MAX_COUNT) {
                break;
            }
        }

        Registry::get('ajax')->assign('autocomplete', $result);
    }

    exit;

}
