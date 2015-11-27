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

$schema['central']['orders']['items']['shipping.spsr.spsr_menu'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'spsr_invoice.manage',
    'position' => 400,
    'subitems' => array(
        'shippings.spsr.invoices_title' => array(
            'href' => 'spsr_invoice.manage',
            'position' => 403
        ),
        'shipping.spsr.spsr_address' => array(
            'href' => 'spsr_addr.manage',
            'position' => 401
        ),
        'shippings.spsr.couriers_title' => array(
            'href' => 'spsr_courier.manage',
            'position' => 402
        ),
    )
);

return $schema;
