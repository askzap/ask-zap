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

$scheme = array(
    'engaged_visitor' => array(
        'name' => __('yandex_metrika_engaged_visitor_text'),
        'type' => 'number',
        'class' => 1,
        'depth' => 5,
        'conditions' => array(),
        'flag' => '',
    ),
    'basket' => array(
        'name' => __('yandex_metrika_basket_text'),
        'type' => 'action',
        'class' => 1,
        'flag' => 'basket',
        'depth' => 0,
        'conditions' => array(
            array(
                'url' => 'basket',
                'type' => 'exact',
            )
        ),
    ),
    'order' => array(
        'name' => __('yandex_metrika_order_text'),
        'type' => 'action',
        'class' => 1,
        'flag' => 'order',
        'depth' => 0,
        'conditions' => array(
            array(
                'url' => 'order',
                'type' => 'exact',
            )
        ),
        'controller' => 'checkout',
        'mode' => 'complete',
    ),
    'wishlist' => array(
        'name' => __('yandex_metrika_wishlist_text'),
        'type' => 'action',
        'class' => 1,
        'flag' => '',
        'depth' => 0,
        'conditions' => array(
            array(
                'url' => 'wishlist',
                'type' => 'exact',
            )
        ),
    ),
    'buy_with_one_click_form_opened' => array(
        'name' => __('yandex_metrika_buy_with_one_click_form_opened_text'),
        'type' => 'action',
        'class' => 1,
        'flag' => '',
        'depth' => 0,
        'conditions' => array(
            array(
                'url' => 'buy_with_one_click_form_opened',
                'type' => 'exact',
            )
        ),
    ),
    'call_request' => array(
        'name' => __('yandex_metrika_call_request_text'),
        'type' => 'action',
        'class' => 1,
        'flag' => '',
        'depth' => 0,
        'conditions' => array(
            array(
                'url' => 'call_request',
                'type' => 'exact',
            )
        ),
    ),
);

return $scheme;
