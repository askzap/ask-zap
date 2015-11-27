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

    if ($mode == 'update') {

        if (!empty($_REQUEST['store_location_id'])) {

            if (!empty($_REQUEST['pickup_destinations_ids'])) {
                $data['pickup_destinations_ids'] = implode(',',$_REQUEST['pickup_destinations_ids']);
             } else {
                $data['pickup_destinations_ids'] = '0';
             }

            db_query('UPDATE ?:store_locations SET ?u WHERE store_location_id = ?i', $data, $_REQUEST['store_location_id']);
        }
    }

}

if ($mode == 'add' || $mode == 'update') {

    // [Page sections]
    $tabs = Registry::get('navigation.tabs');

    $tabs['rus_pickup'] = array (
        'title' => __('rus_pickup.pickup'),
        'js' => true
    );

    Registry::set('navigation.tabs', $tabs);
    // [/Page sections]

    if ($mode == 'update') {

        $store_location = Tygh::$app['view']->getTemplateVars('store_location');

        if (!empty($store_location['pickup_destinations_ids'])) {
            $destinations_ids = explode(',', $store_location['pickup_destinations_ids']);
            Tygh::$app['view']->assign('d_ids', $destinations_ids);
        }

        $destinations = fn_get_destinations(DESCR_SL);

        Tygh::$app['view']->assign('destinations', $destinations);

    }

}
