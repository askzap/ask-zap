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

// rus_build_mailru dbazhenov

$schema['export_fields']['TM Brand'] = array (
    'db_field' => 'mailru_brand',
);
$schema['export_fields']['TM Model'] = array (
    'db_field' => 'mailru_model', //model
);
$schema['export_fields']['TM typePrefix'] = array (
    'db_field' => 'mailru_type_prefix', //grupa
);
$schema['export_fields']['TM Allow local delivery cost'] = array (
    'db_field' => 'mailru_cost', //price
);
$schema['export_fields']['TM Allow delivery'] = array (
    'db_field' => 'mailru_delivery', //delivery
);
$schema['export_fields']['TM Allow booking and self delivery'] = array (
    'db_field' => 'mailru_pickup', //pickup
);
$schema['export_fields']['TM MCP'] = array (
    'db_field' => 'mailru_mcp',
);
$schema['export_fields']['TM Export Yes'] = array (
    'db_field' => 'mailru_export',
);

return $schema;
