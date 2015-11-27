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

namespace Tygh\Ym;

use Tygh\Registry;

class OrderStatus
{

    protected $order;

    protected $statuses;
    protected $default_status = 'O';

    protected static $from_api = false;

    public function __construct($order_data)
    {
        $this->order = $order_data;

        $this->statuses = array(
            'unpaid'     => Registry::get('addons.yandex_market.order_status_unpaid'),
            'processing' => Registry::get('addons.yandex_market.order_status_processing'),
            'canceled'   => Registry::get('addons.yandex_market.order_status_canceled'),
            'delivery'   => Registry::get('addons.yandex_market.order_status_delivery'),
            'pickup'     => Registry::get('addons.yandex_market.order_status_pickup'),
            'delivered'  => Registry::get('addons.yandex_market.order_status_delivered'),
        );
    }

    public function change($status_to, $status_from)
    {
        if (self::$from_api) {
            return;
        }

        if (!empty($this->order['yandex_market']['status'])) {
            if (in_array($this->order['yandex_market']['status'], array('PROCESSING', 'DELIVERY', 'PICKUP'))) {
                $map = $this->getBackMap();
                $status = '';
                $substatus = '';
                if (isset($map[$status_to])) {
                    $status = $map[$status_to];
                } elseif (in_array($status_to, array('O', 'F', 'D', 'I'))) {
                    $status = 'CANCELLED';
                }

                if ($status == 'CANCELLED') {
                    if ($status_to == 'I') {
                        $substatus = 'USER_CHANGED_MIND';
                    } else {
                        $substatus = 'SHOP_FAILED';
                    }
                }

                if (!empty($status)) {
                    $data = array_filter(array(
                        'status'    => $status,
                        'substatus' => $substatus,
                    ));

                    $client = new ApiClient;
                    return $client->orderStatusUpdate($this->order['yandex_market']['order_id'], $data);
                }
            }
        }
    }

    public function update($status)
    {
        $map = $this->getMap();

        $new_status = $this->default_status;
        if (isset($map[$status])) {
            $new_status = $map[$status];
        }
        
        self::$from_api = true;
        $result = fn_change_order_status($this->order['order_id'], $new_status);
        self::$from_api = false;

        return $result;
    }

    protected function getMap()
    {
        $statuses_map = array(
            'UNPAID'     => $this->statuses['unpaid'],
            'PROCESSING' => $this->statuses['processing'],
            'CANCELLED'  => $this->statuses['canceled'],
            'DELIVERY'   => $this->statuses['delivery'],
            'PICKUP'     => $this->statuses['pickup'],
            'DELIVERED'  => $this->statuses['delivered'],
        );

        return $statuses_map;
    }

    protected function getBackMap()
    {
        return array_flip($this->getMap());
    }

}
