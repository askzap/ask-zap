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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_qiwi_rest_install()
{
    fn_qiwi_rest_uninstall();

    $_data = array(
        'processor' => 'Qiwi REST',
        'processor_script' => 'qiwi_rest.php',
        'processor_template' => 'addons/qiwi_rest/views/orders/components/payments/qiwi_rest.tpl',
        'admin_template' => 'qiwi_rest.tpl',
        'callback' => 'Y',
        'type' => 'P',
        'addon' => 'qiwi_rest'
    );

    db_query("INSERT INTO ?:payment_processors ?e", $_data);
}

function fn_qiwi_rest_uninstall()
{
    db_query("DELETE FROM ?:payment_processors WHERE processor_script = ?s", "qiwi_rest.php");
}

function fn_qiwi_rest_normalize_phone($phone)
{
    $phone_normalize = '';

    if (!empty($phone)) {
        if (strpos('+', $phone) === false && $phone[0] == '8') {
            $phone[0] = '7';
        }

        $phone_normalize = str_replace(array(' ', '(', ')', '-'), '', $phone);
    }

    return $phone_normalize;
}

function fn_qiwi_rest_get_bill_status($status)
{
    $statuses = array(
        'waiting' => __('addons.qiwi_rest.bill_status_waiting'),
        'paid' => __('addons.qiwi_rest.bill_status_paid'),
        'rejected' => __('addons.qiwi_rest.bill_status_rejected'),
        'unpaid' => __('addons.qiwi_rest.bill_status_unpaid'),
        'expired' => __('addons.qiwi_rest.bill_status_expired')
    );

    return isset($statuses[$status]) ? $statuses[$status] : __('addons.qiwi_rest.bill_status_unknown');
}
