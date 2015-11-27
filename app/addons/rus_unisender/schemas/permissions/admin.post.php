<?php
/***************************************************************************
 * *
 * (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev *
 * *
 * This is commercial software, only users who have purchased a valid *
 * license and accept to the terms of the License Agreement can install *
 * and use this program. *
 * *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT OF THE SOFTWARE LICENSE AGREEMENT IN THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE. *
 ****************************************************************************/

$schema['unisender'] = array(
    'modes' => array (
        'send_sms' => array (
            'permissions' => 'send_sms_unisender'
        ),
    ),
    'permissions' => array ('GET' => 'view_unisender', 'POST' => 'manage_unisender')
);

return $schema;
