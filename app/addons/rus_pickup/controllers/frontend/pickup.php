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

if ($mode == 'view') {
    fn_add_breadcrumb(__('rus_pickup.pick_up_points'));

    list($store_locations, $search) = fn_get_store_locations($_REQUEST);

    $stores_by_city = array();

    foreach ($store_locations as $value) {
        if ($value['pickup_avail'] == 'Y') {
            $stores_by_city[$value['city']][] = $value;
        }
    }

    if (!empty($_REQUEST['sort_by_city'])) {
        ksort($stores_by_city);

    }

    Tygh::$app['view']->assign('sl_settings', fn_get_store_locator_settings());
    Tygh::$app['view']->assign('store_locations', $stores_by_city);
    Tygh::$app['view']->assign('store_locator_search', $search);
}
