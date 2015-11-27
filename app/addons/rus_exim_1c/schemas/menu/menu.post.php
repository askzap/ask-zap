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

use \Tygh\Registry;

if ((Registry::get('addons.rus_exim_1c.exim_1c_add_tax') == 'Y') || (Registry::get('addons.rus_exim_1c.exim_1c_create_prices') == 'Y')) {
    $schema['top']['addons']['items']['1c'] = array(
        'position' => 310,
        'href' => '1c.offers',
        'subitems' => array(
            '1c_prices' => array(
                'href' => '1c.offers',
                'position' => 100
            )
        ),
    );
}

return $schema;
