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

use Tygh\RusSpsr;
use Tygh\Registry;
use Tygh\Pdf;
use Tygh\Navigation\LastView;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'manage') {
        $params = $_REQUEST;

        $post_fix = '';
        if (!empty($params['period'])) {
            $post_fix .= '&period=' . $params['period'];
        }
        if (!empty($params['time_from'])) {
            $post_fix .= '&time_from=' . $params['time_from'];
        }
        if (!empty($params['time_to'])) {
            $post_fix .= '&time_to=' . $params['time_to'];
        }
        if (!empty($params['status'])) {
            $post_fix .= '&status=' . $params['status'];
        }

        $suffix = ".manage" . $post_fix;
    }

    return array(CONTROLLER_STATUS_OK, "spsr_invoice$suffix");
}

if ($mode == 'manage') {
    $params = $_REQUEST;
    $data = array (
        'period' => empty($params['period']) ? 'W' : $params['period'],
    );

    if (!empty($data['period']) && $data['period'] != 'A') {
        if (!empty($params['time_from'])) {
            $data['time_from'] = strtotime($params['time_from']);
        }
        if (!empty($params['time_to'])) {
            $data['time_to'] = strtotime($params['time_to']);
        }

        list($data['time_from'], $data['time_to']) = fn_create_periods($data);
    } else {
        $data['time_from'] = $data['time_to'] = 0;
    }

    $data['status'] = 'A';
    $status = -1;

    if (!empty($params['status'])) {
        if ($params['status'] == 'D') {
            $data['status'] = 'D';
            $status = 1;

        } elseif ($params['status'] == 'P') {
            $data['status'] = 'P';
            $status = 0;
        }
    }

    if (RusSpsr::WALogin()) {
        $invoices = RusSpsr::WAGetExtMon($data['time_from'], $data['time_to'], $status);

        if (!empty($invoices)) {
            foreach ($invoices as $key => $invoice) {
                $info = db_get_row("SELECT order_id , courier_key FROM ?:rus_spsr_invoices WHERE invoice_number = ?s", $invoice['invoice']['InvoiceNumber']);
                if (!empty($info)) {
                    $invoices[$key]['invoice']['order_id'] = $info['order_id'];
                    $invoices[$key]['invoice']['courier_key'] = $info['courier_key'];
                }

                $invoices[$key]['invoice']['Receipt_Date'] = str_replace('T', ' ' ,$invoice['invoice']['Receipt_Date']);
                $invoices[$key]['invoice']['DeliveryDateWaitFor'] = str_replace('T', ' ' ,$invoice['invoice']['DeliveryDateWaitFor']);
            }
        }

        RusSpsr::WALogout();

        list($invoices, $search) = fn_get_spsr_invoices_list($params, Registry::get('settings.Appearance.admin_elements_per_page'), $invoices);

        Tygh::$app['view']->assign('search', $search);
        Tygh::$app['view']->assign('invoices', $invoices);

    } else {
        fn_set_notification('E', __('notice'), RusSpsr::$last_error);
    }

    $period = $data;
    Tygh::$app['view']->assign('period', $period);

    $url_invoice = RusSpsr::urlInvoice();
    Tygh::$app['view']->assign('url_invoice', $url_invoice);

} elseif ($mode == 'invoice_info') {
    if (RusSpsr::WALogin() && !empty($_REQUEST['invoice_id'])) {
        list($invoice_info, $pieces) = fn_get_spsr_invoice_info($_REQUEST['invoice_id']);

        $spsr_settings = Registry::get('addons.rus_spsr');
        $info_barcode = array(
            'width' => $spsr_settings['width'],
            'height' => $spsr_settings['height'],
            'type' => 'C128B',
        );

        Tygh::$app['view']->assign('info_barcode', $info_barcode);
        Tygh::$app['view']->assign('invoice', $invoice_info);
        Tygh::$app['view']->assign('pieces', $pieces);

        RusSpsr::WALogout();

    } else {
        return array(CONTROLLER_STATUS_OK, "spsr_invoice.manage");
    }

} elseif ($mode == 'print_invoice') {
    if (!empty($_REQUEST['invoice_id'])) {
        if(RusSpsr::WALogin()) {
            fn_print_spsr_invoice($_REQUEST['invoice_id'], !empty($_REQUEST['format']) && $_REQUEST['format'] == 'pdf');
            RusSpsr::WALogout();
        } else {
            return array(CONTROLLER_STATUS_OK, "spsr_invoice.manage");
        } 
    }

    exit;
}

function fn_print_spsr_invoice($invoice_ids, $pdf = false, $lang_code = CART_LANGUAGE)
{
    $view = Tygh::$app['view'];
    $html = array();

    if (!is_array($invoice_ids)) {
        $invoice_ids = array($invoice_ids);
    }

    $spsr_settings = Registry::get('addons.rus_spsr');
    $info_barcode = array(
        'width' => $spsr_settings['width'],
        'height' => $spsr_settings['height'],
        'type' => 'C128B',
    );
    $view->assign('info_barcode', $info_barcode);

    foreach ($invoice_ids as $invoice_id) {
        list($invoice_info, $pieces) = fn_get_spsr_invoice_info($invoice_id);

        $order_info = fn_get_order_info($invoice_info['order_id'], false, true, false, true);

        if (empty($invoice_info) && empty($order_info)) {
            continue;
        }

        $view->assign('order_info', $order_info);
        $view->assign('invoice_info', $invoice_info);
        $view->assign('pieces', $pieces);

        if ($pdf == true) {
            fn_disable_translation_mode();
            $html[] = $view->displayMail('orders/print_packing_slip.tpl', false, 'A', $invoice_info['company_id'], $lang_code);
        } else {
            $view->displayMail('addons/rus_spsr/print_invoice.tpl', true, 'A', $invoice_info['company_id'], $lang_code);
        }

        if ($invoice_id != end($invoice_ids)) {
            echo("<div style='page-break-before: always;'>&nbsp;</div>");
        }
    }

    if ($pdf == true) {
        Pdf::render($html, __('packing_slip') . '-' . implode('-', $order_ids));
    }

    return true;
}

function fn_get_spsr_invoice_info($invoice_id) {
    $invoice_info_db = db_get_row("SELECT * FROM ?:rus_spsr_invoices WHERE invoice_number = ?i", $invoice_id);

    if (!empty($invoice_info_db)) {
        $add_about_order = db_get_row("SELECT company_id, timestamp FROM ?:orders WHERE order_id = ?i", $invoice_info_db['order_id']);

        $invoice_info_db['timestamp'] = $add_about_order['timestamp'];
        $invoice_info_db['company_id'] = $add_about_order['company_id'];

        $invoice_info = RusSpsr::WAGetInvoiceInfo(array($invoice_id));

        $invoice_info = array_merge($invoice_info_db , $invoice_info[$invoice_id]);

        $pieces = db_get_array("SELECT * FROM ?:rus_spsr_invoices_items WHERE ship_ref_num = ?s", $invoice_info_db['ship_ref_num']);

        $invoice_info['products_amount'] = 0;
        $invoice_info['pieces_amount'] = count($pieces);

        foreach ($pieces as $key => $piece) {
            $pieces[$key]['data'] = unserialize($piece['data']);
            $pieces[$key]['products_amount'] = 0;
            foreach ($pieces[$key]['data']['products'] as $product) {
                if (isset($product['product_id'])) {
                    $invoice_info['products_amount'] += $product['amount'];
                    $pieces[$key]['products_amount'] += $product['amount'];
                }
            }
        }

        return array($invoice_info, $pieces);
    }
}

function fn_get_spsr_invoices_list($params, $items_per_page, $invoices)
{
    $params = LastView::instance()->update('invoices', $params);

    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );
    $params = array_merge($default_params, $params);

    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = count($invoices);
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $limit = str_replace("LIMIT ", "", $limit);
    $offset = explode(",", $limit);

    if (!empty($invoices)) {
        $invoices = array_slice($invoices, (int) $offset[0], (int) $offset[1]);
    }

    return array($invoices, $params);
}
