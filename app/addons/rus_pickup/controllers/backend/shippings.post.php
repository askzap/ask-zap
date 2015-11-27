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

if ($mode == 'configure') {

    if (!empty($_REQUEST['shipping_id'])) {

        $module = !empty($_REQUEST['module']) ? $_REQUEST['module'] : '';
        if ($module == 'pickup') {

            $shipping = Tygh::$app['view']->getTemplateVars('shipping');

            $params = array();
            list($locations, $params) = fn_get_store_locations($params);

            $active_stores = array();
            if (!empty($shipping['service_params']['active_stores']) && is_array($shipping['service_params']['active_stores'])) {
                $_active_stores = $shipping['service_params']['active_stores'];

                foreach($_active_stores as $store_location_id) {
                    $active_stores[$store_location_id] = $locations[$store_location_id]['city'] . ' (' . $locations[$store_location_id]['name'] .')'; $locations[$store_location_id]['name'];
                }
            }

            if (!empty($locations)) {
                $stores = $all_stores = $select_stores = array();

                foreach ($locations as $location) {
                    if ($location['pickup_avail'] == 'Y') {
                        $result = array_search($location['store_location_id'], $active_stores);
                        if ($result === false) {
                            $select_stores[$location['store_location_id']] = $location['city'] . ' (' . $location['name'] .')';
                        }
                        $all_stores[$location['store_location_id']] = $location['city'] . ' (' . $location['name'] .')';
                    }
                }

                asort($select_stores);
                asort($active_stores);

                Tygh::$app['view']->assign('all_stores', $all_stores);
                Tygh::$app['view']->assign('select_stores', $select_stores);
                Tygh::$app['view']->assign('active_stores', $active_stores);
            }
        }
    }
}
