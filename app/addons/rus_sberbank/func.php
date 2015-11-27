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

function fn_rus_sberbank_install()
{
    fn_rus_sberbank_uninstall();

    $_data = array(
        'processor' => 'Sberbank Online',
        'processor_script' => 'sberbank.php',
        'processor_template' => 'views/orders/components/payments/cc_outside.tpl',
        'admin_template' => 'sberbank.tpl',
        'callback' => 'Y',
        'type' => 'P',
        'addon' => 'rus_sberbank'
    );

    db_query("INSERT INTO ?:payment_processors ?e", $_data);
}

function fn_rus_sberbank_uninstall()
{
    db_query("DELETE FROM ?:payment_processors WHERE processor_script = ?s", "sberbank.php");
}

function fn_rus_sberbank_normalize_phone($phone)
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

