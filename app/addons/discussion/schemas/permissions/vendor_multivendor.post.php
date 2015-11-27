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

$schema['controllers']['discussion'] = array (
    'modes' => array(
        'add' => array(
            'permissions' => true
        ),
        'update' => array(
            'permissions' => false
        ),
        'delete' => array(
            'permissions' => false
        ),
        'm_delete' => array(
            'permissions' => false
        ),
    ),
    'permissions' => false,
);

$schema['controllers']['discussion_manager'] = array (
    'modes' => array(
        'manage' => array(
            'permissions' => false
        ),
    ),
    'permissions' => true,
    //'permissions' => false,
);

return $schema;
