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

$cart = $_SESSION['cart'];

if ($mode == 'checkout') {
    $phone_normalize =  fn_rus_payments_normalize_phone($cart['user_data']['phone']);
    $payment_id = (!empty($cart['payment_method_data']['payment_id'])) ? $cart['payment_method_data']['payment_id'] : 0;
    $processor_script = db_get_field("SELECT processor_script FROM ?:payments INNER JOIN ?:payment_processors USING (processor_id) WHERE payment_id = ?i", $payment_id);

    if (($processor_script == 'account.php') && !empty($cart['payment_method_data']['processor_params']['fields_account'])) {
        $account_params = fn_rus_payments_account_fields($cart['payment_method_data']['processor_params']['fields_account'], $cart['user_data']);

        if (!empty($account_params)) {
            Tygh::$app['view']->assign('account_params', $account_params);
        }
    }
    Tygh::$app['view']->assign('phone_normalize', $phone_normalize);
}
