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

$cart = & $_SESSION['cart'];

if ($mode == 'update_payment' || $mode == 'select_customer' || $mode == 'add') {
    if (!empty($cart['payment_id'])) {
        $processor_script = db_get_field("SELECT processor_script FROM ?:payments INNER JOIN ?:payment_processors USING (processor_id) WHERE payment_id = ?i", $cart['payment_id']);
        $payment_info = fn_get_payment_method_data($cart['payment_id']);

        if ($processor_script == 'account.php') {
            $account_params = fn_rus_payments_account_fields($payment_info['processor_params']['fields_account'], $cart['user_data']);

            if (empty($cart['payment_info']) && !empty($account_params)) {
                $_SESSION['cart']['payment_info'] = $account_params;
            }
        }
    }
}

if ($mode == 'update') {
    $phone_normalize = "";
    if (!empty($cart['user_data']['phone'])) {
        $phone_normalize =  fn_rus_payments_normalize_phone($cart['user_data']['phone']);
    }
    Tygh::$app['view']->assign('phone_normalize', $phone_normalize);
}
