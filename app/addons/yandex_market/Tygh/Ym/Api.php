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
use Tygh\Api\Response;

class Api
{

    protected $resource;
    protected $method;
    protected $data;
    protected $accept_type;

    public function __construct($resource, $method, $data, $accept_type, $options = array())
    {
        $this->resource    = $resource;
        $this->method      = strtolower($method);
        $this->data        = $data;
        $this->accept_type = $accept_type;

        if (!empty($options)) {
            $this->options = $options;
        } else {
            $this->options = Registry::get('addons.yandex_market');
        }
    }

    public function handleRequest()
    {
        $data = array();
        $status = Response::STATUS_BAD_REQUEST;

        $result = array();

        if (!$this->authenticate()) {
            $result['status'] = Response::STATUS_UNAUTHORIZED;
        } else {
            if ($this->method == 'post') {
                if ($this->resource == 'cart') {
                    $result = $this->cart($this->data);
                } elseif ($this->resource == 'order/accept') {
                    $result = $this->orderAccept($this->data);
                } elseif ($this->resource == 'order/status') {
                    $result = $this->orderStatus($this->data);
                }
            }
        }

        if (!empty($result['status'])) {
            $data = isset($result['data']) ? $result['data'] : '';
            $response = new Response($result['status'], $data, $this->accept_type);
        } else {
            $response = new Response(Response::STATUS_INTERNAL_SERVER_ERROR);
        }

        $response->send();
    }

    public function cart($params)
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = array();

        if (!empty($params['cart'])) {

            $purchase = new Purchase($params['cart']['currency']);
            if ($res = $purchase->cart($params['cart']['items'], $params['cart']['delivery'])) {
                $data = $res;
                $status = Response::STATUS_OK;
            }

        }

        return array(
            'status' => $status,
            'data' => $data,
        );
    }

    public function orderAccept($params)
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = array();

        if (!empty($params['order'])) {

            $order = $params['order'];
            $order_payment = array(
                'type'   => !empty($order['paymentType']) ? $order['paymentType'] : '',
                'method' => !empty($order['paymentMethod']) ? $order['paymentMethod'] : '',
            );
            $order_notes = !empty($order['notes']) ? $order['notes'] : '';

            $purchase = new Purchase($order['currency']);
            if ($res = $purchase->order($order['items'], $order['delivery'], $order['id'], $order_payment, $order_notes)) {
                $data = $res;
                $status = Response::STATUS_OK;
            }

        }

        return array(
            'status' => $status,
            'data' => $data,
        );
    }

    public function orderStatus($params)
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = array();

        if (!empty($params['order'])) {

            $order = $params['order'];
            $buyer = !empty($order['buyer']) ? $order['buyer'] : false;
            $delivery = !empty($order['delivery']) ? $order['delivery'] : false;
            $substatus = !empty($order['substatus']) ? $order['substatus'] : '';

            $purchase = new Purchase($order['currency']);
            if ($res = $purchase->orderStatus($order['id'], $buyer, $delivery, $order['status'], $substatus)) {
                $data = $res;
                $status = Response::STATUS_OK;
            }

        }

        return array(
            'status' => $status,
            'data' => $data,
        );
    }

    protected function authenticate()
    {
        $auth_token = '';

        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth_token = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_GET['auth-token'])) {
            $auth_token = $_GET['auth-token'];
        }

        if (!empty($auth_token) && $auth_token == $this->options['auth_token']) {
            return true;
        }
        
        return false;
    }

}
