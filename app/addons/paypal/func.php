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
use Tygh\Settings;
use Tygh\Http;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

require_once dirname(__FILE__) . "/paypal_express.functions.php";

function fn_paypal_delete_payment_processors()
{
    db_query("DELETE FROM ?:payment_descriptions WHERE payment_id IN (SELECT payment_id FROM ?:payments WHERE processor_id IN (SELECT processor_id FROM ?:payment_processors WHERE processor_script IN ('paypal.php', 'paypal_pro.php', 'payflow_pro.php', 'paypal_express.php', 'paypal_advanced.php')))");
    db_query("DELETE FROM ?:payments WHERE processor_id IN (SELECT processor_id FROM ?:payment_processors WHERE processor_script IN ('paypal.php', 'paypal_pro.php', 'payflow_pro.php', 'paypal_express.php', 'paypal_advanced.php'))");
    db_query("DELETE FROM ?:payment_processors WHERE processor_script IN ('paypal.php', 'paypal_pro.php', 'payflow_pro.php', 'paypal_express.php', 'paypal_advanced.php')");
}

function fn_paypal_get_checkout_payment_buttons(&$cart, &$cart_products, &$auth, &$checkout_buttons, &$checkout_payments, &$payment_id)
{
    $processor_data = fn_get_processor_data($payment_id);
    if (!empty($processor_data) && empty($checkout_buttons[$payment_id]) && Registry::get('runtime.mode') == 'cart') {
        $checkout_buttons[$payment_id] = '
            <form name="pp_express" action="'. fn_payment_url('current', 'paypal_express.php') . '" method="post">
            <input name="payment_id" value="' . $payment_id . '" type="hidden" />
            <input src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-small.png" type="image" />
            <input name="mode" value="express" type="hidden" />
            </form>';
    }
}

function fn_paypal_payment_url(&$method, &$script, &$url, &$payment_dir)
{
    if (strpos($script, 'paypal_express.php') !== false) {
        $payment_dir = '/app/addons/paypal/payments/';
    }
}

function fn_update_paypal_settings($settings)
{
    if (isset($settings['pp_statuses'])) {
        $settings['pp_statuses'] = serialize($settings['pp_statuses']);
    }

    foreach ($settings as $setting_name => $setting_value) {
        Settings::instance()->updateValue($setting_name, $setting_value);
    }

    //Get company_ids for which we should update logos. If root admin click 'update for all', get all company_ids
    if (isset($settings['pp_logo_update_all_vendors']) && $settings['pp_logo_update_all_vendors'] == 'Y') {
        $company_ids = db_get_fields('SELECT company_id FROM ?:companies');
        $company_id = array_shift($company_ids);
    } elseif (!Registry::get('runtime.simple_ultimate')) {
        $company_id = Registry::get('runtime.company_id');
    } else {
        $company_id = 1;
    }
    //Use company_id as pair_id
    fn_attach_image_pairs('paypal_logo', 'paypal_logo', $company_id);
    if (isset($company_ids)) {
        foreach ($company_ids as $logo_id) {
            fn_clone_image_pairs($logo_id, $company_id, 'paypal_logo');
        }
    }
}

function fn_get_paypal_settings($lang_code = DESCR_SL)
{
    $pp_settings = Settings::instance()->getValues('paypal', 'ADDON');
    if (!empty($pp_settings['general']['pp_statuses'])) {
        $pp_settings['general']['pp_statuses'] = unserialize($pp_settings['general']['pp_statuses']);
    }

    $pp_settings['general']['main_pair'] = fn_get_image_pairs(fn_paypal_get_logo_id(), 'paypal_logo', 'M', false, true, $lang_code);

    return $pp_settings['general'];
}

function fn_paypal_get_logo_id()
{
    if (Registry::get('runtime.simple_ultimate')) {
        $logo_id = 1;
    } elseif (Registry::get('runtime.company_id')) {
        $logo_id = Registry::get('runtime.company_id');
    } else {
        $logo_id = 0;
    }

    return $logo_id;
}

function fn_paypal_update_payment_pre(&$payment_data, &$payment_id, &$lang_code, &$certificate_file, &$certificates_dir)
{
    if (!empty($payment_data['processor_id']) && db_get_field("SELECT processor_id FROM ?:payment_processors WHERE processor_id = ?i AND processor_script IN ('paypal.php', 'paypal_pro.php', 'payflow_pro.php', 'paypal_express.php', 'paypal_advanced.php')", $payment_data['processor_id'])) {
        $p_surcharge = floatval($payment_data['p_surcharge']);
        $a_surcharge = floatval($payment_data['a_surcharge']);
        if (!empty($p_surcharge) || !empty($a_surcharge)) {
            $payment_data['p_surcharge'] = 0;
            $payment_data['a_surcharge'] = 0;
            fn_set_notification('E', __('error'), __('text_paypal_surcharge'));
        }
    }
}

function fn_paypal_rma_update_details_post(&$data, &$show_confirmation_page, &$show_confirmation, &$is_refund, &$_data, &$confirmed)
{
    $change_return_status = $data['change_return_status'];
    if (($show_confirmation == false || ($show_confirmation == true && $confirmed == 'Y')) && $is_refund == 'Y') {
        $order_info = fn_get_order_info($change_return_status['order_id']);
        $amount = 0;
        $st_inv = fn_get_statuses(STATUSES_RETURN);
        if ($change_return_status['status_to'] != $change_return_status['status_from'] && $st_inv[$change_return_status['status_to']]['params']['inventory'] != 'D') {
            if (!empty($order_info['payment_method']) && !empty($order_info['payment_method']['processor_params']) && !empty($order_info['payment_info']) && !empty($order_info['payment_info']['transaction_id'])) {
                if (!empty($order_info['payment_method']['processor_params']['username']) && !empty($order_info['payment_method']['processor_params']['password'])) {
                    $request_data = array(
                        'METHOD' => 'RefundTransaction',
                        'VERSION' => '94',
                        'TRANSACTIONID' => $order_info['payment_info']['transaction_id']
                    );
                    if (!empty($order_info['returned_products'])) {
                        foreach ($order_info['returned_products'] as $product) {
                            $amount += $product['subtotal'];
                        }
                    } elseif (!empty($order_info['products'])) {
                        foreach ($order_info['products'] as $product) {
                            if (isset($product['extra']['returns'])) {
                                foreach ($product['extra']['returns'] as $return_id => $return_data)  {
                                    $amount += $return_data['amount'] * $product['subtotal'];
                                }
                            }
                        }
                    }

                    if ($amount != $order_info['subtotal'] || fn_allowed_for('MULTIVENDOR')) {
                        $request_data['REFUNDTYPE'] = 'Partial';
                        $request_data['AMT'] = $amount;
                        $request_data['CURRENCYCODE'] = isset($order_info['payment_method']['processor_params']['currency']) ? $order_info['payment_method']['processor_params']['currency'] : 'USD';
                        $request_data['NOTE'] = !empty($_REQUEST['comment']) ? $_REQUEST['comment'] : '';
                    } else {
                        $request_data['REFUNDTYPE'] = 'Full';
                    }
                    fn_paypal_build_request($order_info['payment_method'], $request_data, $post_url, $cert_file);
                    $result = fn_paypal_request($request_data, $post_url, $cert_file);
                }
            }
        }
    }
}

function fn_validate_paypal_order_info($data, $order_info)
{
    if (empty($data) || empty($order_info)) {
        return false;
    }
    $errors = array();
    if (!isset($data['num_cart_items']) || count($order_info['products']) != $data['num_cart_items']) {
        if (isset($order_info['payment_method']) && isset($order_info['payment_method']['processor_id']) && 'paypal.php' == db_get_field("SELECT processor_script FROM ?:payment_processors WHERE processor_id = ?i", $order_info['payment_method']['processor_id'])) {
            list(, $count) = fn_pp_standart_prepare_products($order_info);

            if ($count != $data['num_cart_items']) {
                $errors[] = __('pp_product_count_is_incorrect');
            }
        }
    }
    if (!isset($order_info['payment_method']['processor_params']) || !isset($order_info['payment_method']['processor_params']['currency']) || !isset($data['mc_currency']) || $data['mc_currency'] != $order_info['payment_method']['processor_params']['currency']) {
        //if cureency defined in paypal settings do not match currency in IPN
        $errors[] = __('pp_currency_is_incorrect');
    } elseif (!isset($data['mc_gross']) || !isset($order_info['total']) || (float)$data['mc_gross'] != (float)$order_info['total']) {
        //if currency is ok, check totals
        $errors[] = __('pp_total_is_incorrect');
    }

    if (!empty($errors)) {
        $pp_response['ipn_errors'] = implode('; ', $errors);
        fn_update_order_payment_info($order_info['order_id'], $pp_response);
        return false;
    }
    return true;
}

function fn_paypal_get_customer_info($data)
{
    $user_data = array();
    if (!empty($data['address_street'])) {
        $user_data['b_address'] = $user_data['s_address'] = $data['address_street'];
    }
    if (!empty($data['address_city'])) {
        $user_data['b_city'] = $user_data['s_city'] = $data['address_city'];
    }
    if (!empty($data['address_state'])) {
        $user_data['b_state'] = $user_data['s_state'] = $data['address_state'];
    }
    if (!empty($data['address_country'])) {
        $user_data['b_country'] = $user_data['s_country'] = $data['address_country'];
    }
    if (!empty($data['address_zip'])) {
        $user_data['b_zipcode'] = $user_data['s_zipcode'] = $data['address_zip'];
    }
    if (!empty($data['contact_phone'])) {
        $user_data['b_phone'] = $user_data['s_phone'] = $data['contact_phone'];
    }
    if (!empty($data['address_country_code'])) {
        $user_data['b_country'] = $user_data['s_country'] = $data['address_country_code'];
    }
    if (!empty($data['first_name'])) {
        $user_data['firstname'] = $data['first_name'];
    }
    if (!empty($data['last_name'])) {
        $user_data['lastname'] = $data['last_name'];
    }
    if (!empty($data['address_name'])) {
        //When customer set a shipping name we should use it
        $_address_name = explode(' ', $data['address_name']);
        $user_data['s_firstname'] = $_address_name[0];
        $user_data['s_lastname'] = $_address_name[1];
    }
    if (!empty($data['payer_business_name'])) {
        $user_data['company'] = $data['payer_business_name'];
    }
    if (!empty($data['payer_email'])) {
        $user_data['email'] = $data['payer_email'];
    }

    return $user_data;
}

function fn_process_paypal_ipn($order_id, $data)
{
    $order_info = fn_get_order_info($order_id);
    if (!empty($order_info) && !empty($data['txn_id']) && (empty($order_info['payment_info']['txn_id']) || $data['payment_status'] != 'Completed' || ($data['payment_status'] == 'Completed' && $order_info['payment_info']['txn_id'] !== $data['txn_id']))) {
        //Can't check refund transactions.
        if (isset($data['txn_type']) && !fn_validate_paypal_order_info($data, $order_info)) {
            return false;
        }
        $pp_settings = fn_get_paypal_settings();
        fn_clear_cart($cart, true);
        $customer_auth = fn_fill_auth(array(), array(), false, 'C');
        fn_form_cart($order_id, $cart, $customer_auth);

        if ($pp_settings['override_customer_info'] == 'Y') {
            $cart['user_data'] = fn_paypal_get_customer_info($data);
        }

        $cart['order_id'] = $order_id;
        $cart['payment_info'] = $order_info['payment_info'];
        $cart['payment_info']['protection_eligibility'] = !empty($data['protection_eligibility']) ? $data['protection_eligibility'] : '';
        $cart['payment_id'] = $order_info['payment_id'];
        if (!empty($data['memo'])) {
            //Save customer notes
            $cart['notes'] = $data['memo'];
        }
        if ($data['payment_status'] == 'Completed') {
            //save uniq ipn id to avoid double ipn processing
            $cart['payment_info']['txn_id'] = $data['txn_id'];
        }
        if (!empty($data['payer_email'])) {
            $cart['payment_info']['customer_email'] = $data['payer_email'];
        }
        if (!empty($data['payer_id'])) {
            $cart['payment_info']['client_id'] = $data['payer_id']; 
        }
        //Sometimes, for some reasons cart_id in product products calculated incorrectle, so we need recalculate it.
        $cart['change_cart_products'] = true;
        fn_calculate_cart_content($cart, $customer_auth);
        $cart['payment_info']['order_status'] = $pp_settings['pp_statuses'][strtolower($data['payment_status'])];
        list($order_id, ) = fn_update_order($cart, $order_id);

        if ($order_id) {
            fn_change_order_status($order_id, $pp_settings['pp_statuses'][strtolower($data['payment_status'])]);
            if (fn_allowed_for('MULTIVENDOR')) {
                $child_order_ids = db_get_fields("SELECT order_id FROM ?:orders WHERE parent_order_id = ?i", $order_id);
                if (!empty($child_order_ids)) {
                    foreach ($child_order_ids as $child_order_id) {
                        fn_update_order_payment_info($child_order_id, $cart['payment_info']);
                    }
                }
            }
        }

        return true;
    }
}

function fn_pp_get_ipn_order_ids($data)
{
    $order_ids = (array)(int)$data['custom'];
    fn_set_hook('paypal_get_ipn_order_ids', $data, $order_ids);

    return $order_ids;
}

function fn_paypal_prepare_checkout_payment_methods(&$cart, &$auth, &$payment_groups)
{
    if (isset($cart['payment_id'])) {
        foreach ($payment_groups as $tab => $payments) {
            foreach ($payments as $payment_id => $payment_data) {
                if (isset($_SESSION['pp_express_details'])) {
                    if ($payment_id != $cart['payment_id']) {
                        unset($payment_groups[$tab][$payment_id]);
                    } else {
                        $_tab = $tab;
                    }
                }
            }
        }
        if (isset($_tab)) {
            $_payment_groups = $payment_groups[$_tab];
            $payment_groups = array();
            $payment_groups[$_tab] = $_payment_groups;
        }
    }
}

function fn_pp_standart_prepare_products($order_info, $paypal_currency = '', $max_pp_products = MAX_PAYPAL_PRODUCTS)
{
    $post_data = array();
    $product_count = 1;

    if (empty($paypal_currency)) {
        $paypal_currency = !empty($order_info['payment_method']['processor_params']['currency']) ? $order_info['payment_method']['processor_params']['currency'] : CART_PRIMARY_CURRENCY;
    }

    $paypal_shipping = fn_order_shipping_cost($order_info);
    $paypal_total = fn_format_price($order_info['total'] - $paypal_shipping, $paypal_currency);

    if (empty($order_info['use_gift_certificates']) && !floatval($order_info['subtotal_discount']) && empty($order_info['points_info']['in_use']) && count($order_info['products']) < MAX_PAYPAL_PRODUCTS) {
        $i = 1;
        if (!empty($order_info['products'])) {
            foreach ($order_info['products'] as $k => $v) {
                $suffix = '_'.($i++);
                $v['product'] = htmlspecialchars(strip_tags($v['product']));
                $v['price'] = fn_format_price(($v['subtotal'] - fn_external_discounts($v)) / $v['amount'], $paypal_currency);
                $post_data["item_name$suffix"] = $v['product'];
                $post_data["amount$suffix"] = $v['price'];
                $post_data["quantity$suffix"] = $v['amount'];
                if (!empty($v['product_options'])) {
                    foreach ($v['product_options'] as $_k => $_v) {
                        $_v['option_name'] = htmlspecialchars(strip_tags($_v['option_name']));
                        $_v['variant_name'] = htmlspecialchars(strip_tags($_v['variant_name']));
                        $post_data["on$_k$suffix"] = $_v['option_name'];
                        $post_data["os$_k$suffix"] = $_v['variant_name'];
                    }
                }
            }
        }

        if (!empty($order_info['taxes']) && Registry::get('settings.General.tax_calculation') == 'subtotal') {
            foreach ($order_info['taxes'] as $tax_id => $tax) {
                if ($tax['price_includes_tax'] == 'Y') {
                    continue;
                }
                $suffix = '_' . ($i++);
                $item_name = htmlspecialchars(strip_tags($tax['description']));
                $item_price = fn_format_price($tax['tax_subtotal'], $paypal_currency);
                $post_data["item_name$suffix"] = $item_name;
                $post_data["amount$suffix"] = $item_price;
                $post_data["quantity$suffix"] = '1';
            }
        }

        // Gift Certificates
        if (!empty($order_info['gift_certificates'])) {
            foreach ($order_info['gift_certificates'] as $k => $v) {
                $suffix = '_' . ($i++);
                $v['gift_cert_code'] = htmlspecialchars($v['gift_cert_code']);
                $v['amount'] = (!empty($v['extra']['exclude_from_calculate'])) ? 0 : fn_format_price($v['amount'], $paypal_currency);
                $post_data["item_name$suffix"] = $v['gift_cert_code'];
                $post_data["amount$suffix"] = $v['amount'];
                $post_data["quantity$suffix"] = '1';
            }
        }

        if (fn_allowed_for('MULTIVENDOR') && fn_take_payment_surcharge_from_vendor('')) {
            $take_surcharge = false;
        } else {
            $take_surcharge = true;
        }

        // Payment surcharge
        if ($take_surcharge && floatval($order_info['payment_surcharge'])) {
            $suffix = '_' . ($i++);
            $name = __('surcharge');
            $payment_surcharge_amount = fn_format_price($order_info['payment_surcharge'], $paypal_currency);
            $post_data["item_name$suffix"] = $name;
            $post_data["amount$suffix"] = $payment_surcharge_amount;
            $post_data["quantity$suffix"] = '1';
        }
        $product_count = $i - 1;
    } elseif ($paypal_total <= 0) {
        $post_data['item_name_1'] = __('total_product_cost');;
        $post_data['amount_1'] = fn_format_price($order_info['total'], $paypal_currency);
        $post_data['quantity_1'] = '1';
        $post_data['amount'] = fn_format_price($order_info['total'], $paypal_currency);;
        $post_data['shipping_1'] = 0;
    } else {
        $post_data['item_name_1'] = __('total_product_cost');;
        $post_data['amount_1'] = $paypal_total;
        $post_data['quantity_1'] = '1';
    }

    return array($post_data, $product_count);
}

function fn_pp_save_mode($order_info)
{
    $data['pp_mode'] = 'test';
    if (!empty($order_info['payment_method']) && !empty($order_info['payment_method']['processor_params']) && !empty($order_info['payment_method']['processor_params']['mode'])) {
        $data['pp_mode'] = $order_info['payment_method']['processor_params']['mode'];
    }
    fn_update_order_payment_info($order_info['order_id'], $data);

    return true;
}

function fn_pp_get_mode($order_id)
{
    $result = 'test';
    $payment_info = db_get_field("SELECT data FROM ?:order_data WHERE order_id = ?i AND type = 'P'", $order_id);
    if (!empty($payment_info)) {
        $payment_info = unserialize(fn_decrypt_text($payment_info));
        if (!empty($payment_info['pp_mode'])) {
            $result = $payment_info['pp_mode'];
        }
    }

    return $result;
}
