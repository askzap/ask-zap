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

$schema['cities'] = array(
    'modes' => array (
        'delete' => array (
            'permissions' => 'manage_cities'
        )
    ),
    'permissions' => array ('GET' => 'view_cities', 'POST' => 'manage_cities'),
);

$schema['tools']['modes']['update_status']['param_permissions']['table']['rus_cities'] = 'manage_cities';

return $schema;
