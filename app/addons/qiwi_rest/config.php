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

fn_define('QIWI_BILL_STATUS_WAITING', 'waiting');
fn_define('QIWI_BILL_STATUS_PAID', 'paid');
fn_define('QIWI_BILL_STATUS_REJECTED', 'rejected');
fn_define('QIWI_BILL_STATUS_UNPAID', 'unpaid');
fn_define('QIWI_BILL_STATUS_EXPIRED', 'expired');

fn_define('QIWI_NOTIFY_OK', 0);
fn_define('QIWI_NOTIFY_ERROR_PARAMS', 5);
fn_define('QIWI_NOTIFY_ERROR_DB', 13);
fn_define('QIWI_NOTIFY_ERROR_PASSWORD', 150);
fn_define('QIWI_NOTIFY_ERROR_SIGN', 151);
fn_define('QIWI_NOTIFY_ERROR_SERVER', 300);
