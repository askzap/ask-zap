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

$schema['companies'] = array (
    'modes' => array (
        'manage' => array (
            'permissions' => array ('GET' => 'view_vendors', 'POST' => 'manage_vendors'),
        ),
        'add' => array (
            'permissions' => 'manage_vendors',
        ),
        'update' => array (
            'permissions' => array ('GET' => 'view_vendors', 'POST' => 'manage_vendors'),
        ),
        'get_companies_list' => array (
            'permissions' => 'view_vendors',
        ),
        'payouts_m_delete' => array (
            'permissions' => 'manage_payouts',
        ),
        'payouts_add' => array (
            'permissions' => 'manage_payouts',
        ),
        'payout_delete' => array (
            'permissions' => 'manage_payouts',
        ),
        'balance' => array (
            'permissions' => 'view_payouts',
        ),
    ),
    'permissions' => 'manage_vendors',
);

return $schema;
