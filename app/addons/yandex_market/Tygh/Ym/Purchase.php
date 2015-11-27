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

class Purchase
{

    const FAKE_YM_NAME = 'YANDEX';

    protected $auth;
    protected $cart;
    protected $currency;

    public function __construct($currency_code = '')
    {
        $this->auth = & $_SESSION['auth'];

        $this->cart = & $_SESSION['cart'];
        $this->cart['products'] = array();

        $this->currency = $currency_code;
    }

    public function cart($items, $delivery)
    {
        $this->addProductsToCart($items, $delivery);

        $group = reset($this->cart['product_groups']);

        // Products
        $products = array();
        foreach ($items as $item) {
            foreach ($group['products'] as $product) {
                if ($product['product_id'] == $item['offerId']) {
                    $products[] = array(
                        'feedId'   => $item['feedId'],
                        'offerId'  => $item['offerId'],
                        'price'    => $product['price'],
                        'count'    => $product['amount'],
                        'delivery' => true,
                    );
                }
            }
        }
        
        // Shippings
        $shippings = array();
        foreach ($group['shippings'] as $shipping) {
            $_shipping = array(
                'id'          => $shipping['shipping_id'],
                'type'        => strtoupper($shipping['yml_shipping_type']),
                'serviceName' => $shipping['shipping'],
                'price'       => $shipping['rate'],
                'dates'       => array(
                    'fromDate' => date('d-m-Y'), // FIXME
                ),
            );

            if ($shipping['yml_shipping_type'] == 'pickup') {
                $_shipping['outlets'] = array();
                $outlets = explode(',', $shipping['yml_outlet_ids']);
                foreach ($outlets as $outlet) {
                    $outlet = trim($outlet);
                    if (!empty($outlet)) {
                        $_shipping['outlets'][] = array(
                            'id' => intval($outlet)
                        );
                    }
                }
            }

            $shippings[] = $_shipping;
        }

        // Payments
        $payments = array();
        $_pay_settings = array_merge(
            Registry::get('addons.yandex_market.purchase_prepayments'),
            Registry::get('addons.yandex_market.purchase_postpayments')
        );
        foreach ($_pay_settings as $_payment_name => $_v) {
            if ($_v == 'Y') {
                $payments[] = strtoupper($_payment_name);
            }
        }

        $result = array(
            'cart' => array(
                'items' => $products,
                'deliveryOptions' => $shippings,
                'paymentMethods' => $payments,
            ),
        );

        return $result;
    }

    public function order($items, $delivery, $ym_order_id, $payment_data, $notes = '')
    {
        list(,, $address) = $this->addProductsToCart($items, $delivery);

        if ($order_id = $this->placeOrder($ym_order_id, $payment_data, $address, $notes)) {
            $result = array(
                'accepted' => true,
                'id'       => (string) $order_id,
            );
        } else {
            $result = array(
                'accepted' => false,
                'reason'   => 'OUT_OF_DATE',
            );
        }
        
        return array(
            'order' => $result
        );
    }

    protected function addProductsToCart($items, $delivery)
    {
        $products = array();

        foreach ($items as $item) {
            $products[$item['offerId']] = array(
                'product_id' => $item['offerId'],
                'amount'     => $item['count'],
            );
        }

        fn_add_product_to_cart($products, $this->cart, $this->auth);

        $addr = $this->parseDelivery($delivery);

        $this->cart['user_data'] = array(
            'lastname' => self::FAKE_YM_NAME,
            'b_firstname' => self::FAKE_YM_NAME,
            's_firstname' => self::FAKE_YM_NAME,
            'firstname' => self::FAKE_YM_NAME,
            'b_lastname' => self::FAKE_YM_NAME,
            's_lasttname' => self::FAKE_YM_NAME,
            'b_address' => $addr['address'],
            's_address' => $addr['address'],
            'b_city' => $addr['city'],
            's_city' => $addr['city'],
            'b_country' => $addr['country_code'],
            's_country' => $addr['country_code'],
            'b_state' => $_state = (!empty($addr['state_code']) ? $addr['state_code'] : $addr['subject_federation']),
            's_state' => $_state,
        );

        if (!empty($delivery['type']) && !empty($delivery['id'])) {
            fn_checkout_update_shipping($this->cart, array(
                0 => $delivery['id']
            ));
        }

        $this->cart['calculate_shipping'] = true;

        list($cart_products, $product_groups) = fn_calculate_cart_content($this->cart, $this->auth, 'A', true, 'F', true);

        return array($cart_products, $product_groups, $addr);
    }

    protected function placeOrder($ym_order_id, $payment_data, $address, $notes)
    {
        $this->cart['yandex_market'] = array(
            'order_id'       => $ym_order_id,
            'payment_type'   => $payment_data['type'],
            'payment_method' => $payment_data['method'],
            'address'        => $address,
        );
        $this->cart['yml_order_id'] = $ym_order_id; // need for search
        $this->cart['notes'] = $notes;
        $this->cart['payment_id'] = 0; // skip payment
        
        if ($res = fn_place_order($this->cart, $this->auth)) {
            list($order_id) = $res;
            return $order_id;
        }

        return false;
    }

    public function orderStatus($ym_order_id, $buyer, $delivery, $status, $substatus = '')
    {
        $order_id = db_get_field("SELECT order_id FROM ?:orders WHERE yml_order_id = ?i", $ym_order_id);
        $order_data = fn_get_order_info($order_id);

        $this->updateOrderData($order_data, $buyer, $delivery, $status, $substatus);

        $order_obj = new OrderStatus($order_data);
        $result = $order_obj->update($status);
        
        return true;
    }

    protected function updateOrderData($order_data, $buyer, $delivery, $status, $substatus = '')
    {
        $ym_data = $order_data['yandex_market'];

        $new_data = array();

        // Buyer
        if (!empty($buyer['email'])) {
            $new_data['email'] = $buyer['email'];
        }

        if (!empty($buyer['firstName'])) {
            $new_data['firstname'] = $new_data['b_firstname'] = $new_data['s_firstname'] = $buyer['firstName'];
        }

        if (!empty($buyer['lastName'])) {
            $new_data['lastname'] = $new_data['b_lastname'] = $new_data['s_lastname'] = $buyer['lastName'];
        }

        if (!empty($buyer['phone'])) {
            $new_data['phone'] = $new_data['b_phone'] = $new_data['s_phone'] = $buyer['phone'];
        }

        // Delivery
        $addr = $this->parseDelivery($delivery);

        if (!empty($addr['recipient'])) {
            @list($firstname, $lastname) = explode(' ', $addr['recipient'], 2);
            $new_data['s_firstname'] = $firstname;
            $new_data['s_lastname'] = $lastname;
        }

        // Update order data
        $ym_data['address'] = $addr;
        if (!empty($status)) {
            $ym_data['status'] = $status;
        }
        if (!empty($substatus)) {
            $ym_data['substatus'] = $substatus;
        }
        
        fn_yandex_market_update_order_ym_data($order_data['order_id'], $ym_data);

        fn_update_order_customer_info($new_data, $order_data['order_id']);
    }

    protected function parseDelivery($delivery)
    {
        $data = array(
            // YM region
            'country' => '',
            'region' => '',
            'country_district' => '',
            'subject_federation' => '',
            'subject_federation_district' => '',
            'city' => '',
            'village' => '',
            'city_district' => '',
            'subway_station' => '',
            'other' => '',
            // YM address
            'street' => '',
            'house' => '',
            'block' => '',
            'floor' => '',
            // Additional
            'country_code' => '',
            'state_code' => '',
            'address' => '',
        );

        // Address
        if (isset($delivery['address'])) {
            $data = array_merge($data, $delivery['address']);
        }

        // Region
        $link = & $delivery['region'];
        while ($link) {
            $key = strtolower($link['type']);
            if (empty($data[$key])) {
                $data[$key] = $link['name'];
            }
            $link = & $link['parent'];
        }

        // Post-processing
        
        $countries_map = fn_get_schema('yandex_market', 'countries');
        if (isset($countries_map[$data['country']])) {
            $data['country_code'] = $countries_map[$data['country']];
        }

        $states_map = fn_get_schema('yandex_market', 'states');
        if (isset($states_map[$data['country_code']][$data['subject_federation']])) {
            $data['state_code'] = $states_map[$data['country_code']][$data['subject_federation']];
        }

        $addr_fields = array($data['street'], $data['house'], $data['block'], $data['floor']);
        $data['address'] = implode(', ', array_filter($addr_fields));

        return $data;
    }

}
