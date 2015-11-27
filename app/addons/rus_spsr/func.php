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

use Tygh\Languages\Languages;

if ( !defined('AREA') ) { die('Access denied'); }

function fn_rus_spsr_install()
{
    $service = array(
        'status' => 'A',
        'module' => 'spsr',
        'code' => 'spsr',
        'sp_file' => '',
        'description' => 'СПСР'
    );

    $service['service_id'] = db_query('INSERT INTO ?:shipping_services ?e', $service);

    foreach (Languages::getAll() as $service['lang_code'] => $lang_data) {
        db_query('INSERT INTO ?:shipping_service_descriptions ?e', $service);
    }
}

function fn_rus_spsr_uninstall()
{
    $service_ids = db_get_fields('SELECT service_id FROM ?:shipping_services WHERE module = ?s', 'spsr');
    db_query('DELETE FROM ?:shipping_services WHERE service_id IN (?a)', $service_ids);
    db_query('DELETE FROM ?:shipping_service_descriptions WHERE service_id IN (?a)', $service_ids);

    db_query('DROP TABLE IF EXISTS ?:rus_spsr_invoices');
    db_query('DROP TABLE IF EXISTS ?:rus_spsr_register');
    db_query('DROP TABLE IF EXISTS ?:rus_spsr_invoices_items');
    db_query('ALTER TABLE ?:products DROP spsr_product_type');
}

function fn_rus_spsr_calculate_cart_taxes_pre(&$cart, $cart_products, &$product_groups)
{
    if (!empty($cart['shippings_extra']['rates'])) {
        foreach($cart['shippings_extra']['rates'] as $group_key => $shippings) {
            foreach($shippings as $shipping_id => $shipping) {
                if (!empty($shipping['day'])) {
                    $product_groups[$group_key]['shippings'][$shipping_id]['delivery_time'] = $shipping['day'];
                }
            }
        }
    }
}

function fn_rus_spsr_calculate_cart_items(&$cart, &$cart_products, $auth)
{
    if(!empty($cart['products'])) {
        foreach($cart['products'] as $key => $product) {
            $spsr_product_type = db_get_field("SELECT spsr_product_type FROM ?:products WHERE product_id = ?i", $product['product_id']);
            $cart['products'][$key]['spsr_product_type'] = $cart_products[$key]['spsr_product_type'] = $spsr_product_type;
        }
    }
}

function fn_rus_spsr_barcode_number($number)
{
    $sum_nech = $sum_ch = 0;
    $prefix = 20;
    $number = (string) $number;
    $count = strlen($number);

    if($count < 9) {
        $number = str_pad($number, 9, "0", STR_PAD_LEFT);
        $number = $prefix . $number;

        for ($i = 0; $i < strlen($number); $i++) { 
            $a = $number[$i];
            $b = ($i + 1) % 2;

            if ($b === 1) {
                $sum_nech += $a;
            } else {
                $sum_ch += $a;
            }
        }

        $check = $sum_ch * 3 + $sum_nech;
        $check_big = ceil($check / 10);
        $check_big = $check_big * 10; 
        $contr_sum = $check_big - $check;
        $barcode = $number . $contr_sum;

        return $barcode;

    } else {
        fn_set_notification('E', __('notice'), __('shippings.spsr.barcode_error_len') . ' : ' . $count);

        return false;
    }
}
