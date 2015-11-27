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

fn_trusted_vars("processor_params", "payment_data");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update') {
        $payment_id = fn_attach_image_pairs('path_stamp', 'stamp', $_REQUEST['payment_id'], DESCR_SL);
    }

    return array(CONTROLLER_STATUS_OK, "payments.manage");
}

if ($mode == 'update' || $mode == 'manage') {

    $processors = Tygh::$app['view']->getTemplateVars('payment_processors');

    if (!empty($processors)) {
        $rus_payments = array();
        foreach (fn_get_schema('rus_payments', 'processors') as $rus_payment) {
            $rus_payments[$rus_payment['processor']] = $rus_payment;
        }

        foreach ($processors as &$processor) {
            $processor['position'] = 'default_' . $processor['processor'];
            if (!empty($rus_payments[$processor['processor']])) {
                $processor['russian'] = 'Y';
                $processor['type'] = 'R';
                if (isset($rus_payments[$processor['processor']]['position'])) {
                    $processor['position'] = 'a_' . $rus_payments[$processor['processor']]['position'];
                }
            }
        }
        $processors = fn_sort_array_by_key($processors, 'position');

        Tygh::$app['view']->assign('payment_processors', $processors);
    }

} elseif ($mode == 'yandex_get_md5_password') {

    $md5 = md5(TIME . $_REQUEST['md5_shoppassword']);
    $md5 = substr($md5, 0, 20);

    Tygh::$app['view']->assign('ya_md5', $md5);
    Tygh::$app['view']->display('views/payments/components/cc_processors/yandex_money.tpl');

    exit;

} elseif ($mode == 'processor') {

    $payment_image = array();

    if (!empty($_REQUEST['payment_id'])) {
        $processor_script = db_get_field("SELECT processor_script FROM ?:payments INNER JOIN ?:payment_processors USING (processor_id) WHERE payment_id = ?i", $_REQUEST['payment_id']);
        if ($processor_script == 'qiwi.php' && class_exists('SoapClient')) {
            Tygh::$app['view']->assign('soap_exist', true);
        } elseif ($processor_script == 'account.php') {
            $payment_image = $_REQUEST;
            $payment_image['path_stamp'] = fn_get_image_pairs($_REQUEST['payment_id'], 'stamp', 'M', true, true, DESCR_SL);

            Tygh::$app['view']->assign('payment_image', $payment_image);
        }
    }

    if (!empty($_REQUEST['processor_id'])) {
        $processor_script = db_get_field("SELECT processor_script FROM ?:payment_processors WHERE processor_id = ?i", $_REQUEST['processor_id']);
    }

    if (!empty($processor_script) && ($processor_script == 'account.php')) {
        $profile_fields = fn_get_profile_fields('ALL', array(), CART_LANGUAGE);
        Tygh::$app['view']->assign('profile_fields', $profile_fields);
        Tygh::$app['view']->assign('account_fields', fn_get_schema('rus_payments', 'account_fields'));
    }

}
