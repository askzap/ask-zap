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

if ($mode == 'details') {
    $params = $_REQUEST;
    if(!empty($params['order_id'])) {
        $data_status = array();
        list($_shipments) = fn_get_shipments_info(array('order_id' => $params['order_id'], 'advanced_info' => true));
        if (!empty($_shipments)) {
            foreach ($_shipments as $key => $shipment) {
                if ($shipment['carrier'] == 'sdek') {
                    $join = db_quote(" LEFT JOIN ?:rus_cities_sdek as b ON a.city_code = b.city_code ");
                    $join .= db_quote(" LEFT JOIN ?:rus_city_sdek_descriptions as c ON b.city_id = c.city_id ");
                    $d_status = db_get_array("SELECT a.*, c.city FROM ?:rus_sdek_status as a ?p WHERE a.order_id = ?i AND a.shipment_id = ?i ", $join, $params['order_id'], $shipment['shipment_id']);

                    if (!empty($d_status)) {
                        $data_status[$key] = $d_status;
                    }
                }
            }

            if (!empty($data_status)) {
                Tygh::$app['view']->assign('data_status', $data_status);
                $navigation_tabs = Registry::get('navigation.tabs');
                $navigation_tabs['sdek_information'] = array(
                    'title' => __('shipping_information'),
                    'js' => true,
                    'href' => 'orders.details?order_id=' . $params['order_id'] . '&selected_section=sdek_information'
                );
                Registry::set('navigation.tabs', $navigation_tabs);
            }
        }
    }
}

