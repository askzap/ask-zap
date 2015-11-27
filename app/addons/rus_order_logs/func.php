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

function fn_save_order_log($order_id, $user_id, $action, $description = '', $timestamp = TIME)
{
    $data = db_query("INSERT INTO ?:order_logs SET ?u", array(
        'order_id' => $order_id,
        'user_id' => $user_id,
        'action' => $action,
        'description' => $description,
        'timestamp' => $timestamp
    ));
    
    return $data;
}

function fn_get_order_logs($order_id)
{
    $logs = db_get_array("SELECT logs.*, users.firstname, users.lastname FROM ?:order_logs as logs "
        . " LEFT JOIN ?:users as users USING(user_id) WHERE logs.order_id = ?i ORDER BY logs.log_id ASC", $order_id
    );

    return $logs;
}

/**
 * Hooks
 */

function fn_rus_order_logs_change_order_status($status_to, $status_from, $order_info, $force_notification, $order_statuses, $place_order)
{
    if ($status_to != $status_from) {
        $user_id = 0;
        if (AREA == 'A' && $place_order != true) {
            $user_id = $_SESSION['auth']['user_id'];
        }
        $description = $order_statuses[$status_from]['description'] . ' &rarr; ' . $order_statuses[$status_to]['description'];
        if (!$place_order && $status_to != 'N') {
            fn_save_order_log($order_info['order_id'], $user_id, 'rus_order_logs_status_changed', $description, TIME);
        }
    }
}

function fn_rus_order_logs_place_order($order_id, $action, $order_status, $cart, $auth)
{
    if ($order_status == 'N') {
        $action_status = 'rus_order_logs_order_created';
    } else {
        $action_status = 'rus_order_logs_order_changed';
    }
    fn_save_order_log($order_id, $_SESSION['auth']['user_id'], $action_status, '', TIME);
}

function fn_rus_order_logs_delete_order($order_id)
{
    fn_save_order_log($order_id, $_SESSION['auth']['user_id'], 'rus_order_logs_order_deleted', '', TIME);
}

/**
 * \ Hooks
 */