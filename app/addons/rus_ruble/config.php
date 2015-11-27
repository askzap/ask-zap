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

fn_define('CRON_IMPORT_KEY_LENGTH', 16);
fn_define('SYMBOL_RUBL', '<span class="ty-rub">Р</span>');
fn_define('SYMBOL_RUBL_TEXT', ' Руб.');
fn_define('CURRENCY_RUB', 'RUB');

fn_define('SYNC_OK', 0);
fn_define('SYNC_MAGIC_KEY_EMPTY', 1);
fn_define('SYNC_MAGIC_KEY_INCORRECT', 2);
fn_define('SYNC_ERROR', 3);
fn_define('SYNC_NOT_SET_RUB', 4);
