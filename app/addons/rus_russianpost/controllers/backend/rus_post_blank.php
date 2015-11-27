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

// rus_build_loginza vmalyshev

use Tygh\Registry;
use Tygh\Pdf;
use RusPostBlank\RusPostBlank;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'print') {

        $view = Tygh::$app['view'];

        fn_save_post_data('blank_data');

        $lang_code = 'ru';

        $params = $_REQUEST['blank_data'];

        if (!empty($_REQUEST['order_id'])) {

            $order_id = $_REQUEST['order_id'];

            $order_info = fn_get_order_info($order_id, false, true, false, true);

            if (empty($order_info)) {
                exit;
            }

            list($params['clear_total_cen'],,) = fn_rus_postblank_clear_text_cen($params['total_cen']);
            list($params['clear_total_cod'],$params['total_rub'],$params['total_kop']) = fn_rus_postblank_clear_text_cen($params['total_cod']);

            $params['text1'] = preg_split('//u',$params['text1'],-1,PREG_SPLIT_NO_EMPTY);
            $params['text2'] = preg_split('//u',$params['text2'],-1,PREG_SPLIT_NO_EMPTY);

            $view->assign('data', $params);
            $view->assign('order_info', $order_info);

            if ($action == '112') {

                if ($params['print_pdf'] == 'Y') {
                    $pdf_params = array(
                        'page_width' => '200mm',
                        'page_height' => '285mm',
                        'margin_left' => '0mm',
                        'margin_right' => '0mm',
                        'margin_top' => '0mm',
                        'margin_bottom' => '0mm',
                    );

                    $blanks = array(1);
                    $html[] = $view->displayMail('addons/rus_russianpost/112_pdf.tpl', false, AREA, $order_info['company_id'], $lang_code);
                } else {
                    $view->displayMail('addons/rus_russianpost/112.tpl', true, AREA, $order_info['company_id'], $lang_code);
                }
            }

            if ($action == '113') {

                if ($params['print_pdf'] == 'Y') {
                    $pdf_params = array(
                        'page_width' => '195mm',
                        'page_height' => '190mm',
                        'margin_left' => '0mm',
                        'margin_right' => '0mm',
                        'margin_top' => '0mm',
                        'margin_bottom' => '0mm',
                    );
                    $blanks = array(1,2);
                    $html[] = $view->displayMail('addons/rus_russianpost/113_1_pdf.tpl', false, AREA, $order_info['company_id'], $lang_code);
                    $html[] = $view->displayMail('addons/rus_russianpost/113_2_pdf.tpl', false, AREA, $order_info['company_id'], $lang_code);
                } else {
                    $view->displayMail('addons/rus_russianpost/113_1.tpl', true, AREA, $order_info['company_id'], $lang_code);
                    echo("<div style='page-break-before: always;'></div>");
                    $view->displayMail('addons/rus_russianpost/113_2.tpl', true, AREA, $order_info['company_id'], $lang_code);
                }

            }

            if ($action == '116') {

                if ($params['print_pdf'] == 'Y') {
                    $pdf_params = array(
                        'page_width' => '143mm',
                        'page_height' => '208mm',
                        'margin_left' => '0mm',
                        'margin_right' => '0mm',
                        'margin_top' => '0mm',
                        'margin_bottom' => '0mm',
                    );

                    $blanks = array(1,2);
                    $html[] = $view->displayMail('addons/rus_russianpost/116_1_pdf.tpl', false, AREA, $order_info['company_id'], $lang_code);
                    $html[] = $view->displayMail('addons/rus_russianpost/116_2_pdf.tpl', false, AREA, $order_info['company_id'], $lang_code);
                } else {
                    $view->displayMail('addons/rus_russianpost/116_1.tpl', true, AREA, $order_info['company_id'], $lang_code);
                    echo("<div style='page-break-before: always;'></div>");
                    $view->displayMail('addons/rus_russianpost/116_2.tpl', true, AREA, $order_info['company_id'], $lang_code);
                }
            }

            if ($action == '7p') {

                if ($params['print_pdf'] == 'Y') {
                    $pdf_params = array(
                        'page_width' => '148mm',
                        'page_height' => '105mm',
                        'margin_left' => '0mm',
                        'margin_right' => '0mm',
                        'margin_top' => '0mm',
                        'margin_bottom' => '0mm',
                    );

                    $blanks = array(1);
                    $html[] = $view->displayMail('addons/rus_russianpost/7-p_pdf.tpl', false, AREA, $order_info['company_id'], $lang_code);
                } else {
                    $view->displayMail('addons/rus_russianpost/7-p.tpl', true, AREA, $order_info['company_id'], $lang_code);
                }
            }
            if ($action == '7b') {

                if ($params['print_pdf'] == 'Y') {
                    $pdf_params = array(
                        'page_width' => '148mm',
                        'page_height' => '105mm',
                        'margin_left' => '0mm',
                        'margin_right' => '0mm',
                        'margin_top' => '0mm',
                        'margin_bottom' => '0mm',
                    );

                    $blanks = array(1);
                    $html[] = $view->displayMail('addons/rus_russianpost/7-b_pdf.tpl', false, AREA, $order_info['company_id'], $lang_code);
                } else {
                    $view->displayMail('addons/rus_russianpost/7-b.tpl', true, AREA, $order_info['company_id'], $lang_code);
                }
            }
        }

        if ($params['print_pdf'] == 'Y') {
            Pdf::render($html, __("rus_post_blank.{$action}") . ' #' . $order_info['order_id'] . '-' . implode('-', $blanks), false, $pdf_params);
        }

        exit;
    }
}

if ($mode == 'edit') {
    // [Page sections]
    $tabs = array (
        'settings' => array (
            'title' => __('settings'),
            'js' => true
        ),
        'recipient' => array (
            'title' => __('recipient'),
            'js' => true
        ),
        'sender' => array (
            'title' => __('sender'),
            'js' => true
        ),
    );

    Registry::set('navigation.tabs', $tabs);
    // [/Page sections]

    $order_id = $_REQUEST['order_id'];

    $order_info = fn_get_order_info($order_id, false, true, false, true);

    if (CART_PRIMARY_CURRENCY != 'RUB') {
        $currencies = Registry::get('currencies');
        if (!empty($currencies['RUB'])) {
            $currency = $currencies['RUB'];
            if (!empty($currency)) {
                $order_info['total'] = fn_format_rate_value($order_info['total'], 'F', $currency['decimals'], $currency['decimals_separator'], '', $currency['coefficient']);
                $order_info['total'] = fn_format_price($order_info['total'], 'RUB', 2);
            }
        }
    }

    $rp['clear'] = RusPostBlank::clearDoit($order_info['total']);
    $rp['summ'] = RusPostBlank::doit($order_info['total'],false,false);

    $total_array = explode('.', $order_info['total']);

    $total['113'] = $rp['clear'];
    $total['116'] = $total_array[0] . ' (' . $rp['summ'] . ') руб. ' . $total_array[1] . ' коп.';

    $firstname = '';
    $lastname = '';

    if (!empty($order_info['lastname'])) {
        $lastname = $order_info['lastname'];

    } elseif (!empty($order_info['b_lastname'])) {
        $lastname = $order_info['b_lastname'];

    } elseif (!empty($order_info['s_lastname'])) {
        $lastname = $order_info['s_lastname'];
    }

    if (!empty($order_info['firstname'])) {
        $firstname = $order_info['firstname'];

    } elseif (!empty($order_info['b_firstname'])) {
        $firstname = $order_info['b_firstname'];

    } elseif (!empty($order_info['s_firstname'])) {
        $firstname = $order_info['s_firstname'];
    }

    $order_info['fio'] = $lastname . ' ' . $firstname;

    $order_info['state_name'] = fn_get_state_name($order_info['s_state'], $order_info['s_country'], DESCR_SL);
    $order_info['country_name'] = fn_get_country_name($order_info['s_country'], DESCR_SL);

    $order_info['address_line_2'] = $order_info['country_name'] . ', ' . $order_info['state_name'] . ', ' . $order_info['s_city'];

    if (!empty($order_info['phone'])) {
        $order_info['recipient_phone'] = fn_rus_russianpost_normalize_phone($order_info['phone']);

    } elseif (!empty($order_info['b_phone'])) {
        $order_info['recipient_phone'] = fn_rus_russianpost_normalize_phone($order_info['b_phone']);

    } elseif (!empty($order_info['s_phone'])) {
        $order_info['recipient_phone'] = fn_rus_russianpost_normalize_phone($order_info['s_phone']);
    }

    Tygh::$app['view']->assign('pre_total', $total);
    Tygh::$app['view']->assign('order_info', $order_info);

    $pre_data = Registry::get('addons.rus_russianpost');
    $pre_data['company_phone'] = fn_rus_russianpost_normalize_phone($pre_data['company_phone']);

    Tygh::$app['view']->assign('pre_data', $pre_data);
}

function fn_rus_russianpost_normalize_phone($data_phone)
{
    $array_search = array('+', '7', '8');

    $data_phone = str_replace('-', '', $data_phone);
    $data_phone = str_replace($array_search, '', substr($data_phone, 0, 2)) . substr($data_phone, 2);

    return $data_phone;
}
