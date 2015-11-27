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
use Tygh\RusSpsr;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'details') {
    $params = $_REQUEST;

    if(!empty($params['order_id'])) {
        $order_info = Tygh::$app['view']->getTemplateVars('order_info');

        list($all_shipments) = fn_get_shipments_info(array('order_id' => $params['order_id'], 'advanced_info' => true));
        if (!empty($all_shipments)) {
            $spsr_shipments = $shipping_data = array();

            foreach ($all_shipments as $key => $_shipment) {
                if ($_shipment['carrier'] == 'spsr') {
                    $spsr_shipments[$_shipment['shipment_id']] = $_shipment;
                }
            }

            $login = RusSpsr::WALogin();
            if (!empty($spsr_shipments)) {
                if ($login) {
                    $invoices = db_get_fields("SELECT invoice_number FROM ?:rus_spsr_invoices WHERE order_id = ?i ", $params['order_id']);
                    if (!empty($invoices)) {
                        $invoice_info = RusSpsr::WAGetInvoiceInfo($invoices);
                        Tygh::$app['view']->assign('spsr_info', $invoice_info);
                    }

                    RusSpsr::WALogout();
                }

                $navigation_tabs = Registry::get('navigation.tabs');
                $navigation_tabs['spsr_information'] = array(
                    'title' => __('shippings.spsr.shipping_information'),
                    'js' => true,
                    'href' => 'orders.details?order_id=' . $params['order_id'] . '&selected_section=spsr_information',
                );
                Registry::set('navigation.tabs', $navigation_tabs);
            }
        }
    }
}
