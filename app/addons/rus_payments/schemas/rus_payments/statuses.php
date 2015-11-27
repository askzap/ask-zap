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

$schema = array(
    'yandex_money_postponed_order_status' => array(
        'type' => 'O', // order
        'description' => __('addons.rus_payments.on_hold_status'),
        'email_subj' => __('addons.rus_payments.on_hold_status_email_subject'),
        'email_header' => __('addons.rus_payments.on_hold_status_email_header'),
        'params' => array(
            'color' => '#49afcd',
            'notify' => 'Y',
            'notify_department' => 'N',
            'inventory' => 'D',
            'remove_cc_info' => 'N',
            'repay' => 'N',
            'appearance_type' => 'D',
        )
    ),
    'yandex_money_refunded_order_status' => array(
        'type' => 'O', // order
        'description' => __('addons.rus_payments.refunded_status'),
        'email_subj' => __('addons.rus_payments.refunded_status_email_subject'),
        'email_header' => __('addons.rus_payments.refunded_status_email_header'),
        'params' => array(
            'color' => '#ea9999',
            'notify' => 'Y',
            'notify_department' => 'N',
            'inventory' => 'I',
            'remove_cc_info' => 'N',
            'repay' => 'N',
            'appearance_type' => 'D',
        )
    )
);

return $schema;
