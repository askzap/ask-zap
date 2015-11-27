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

namespace Tygh\Payments\Processors;

use Tygh\Http;

class Alfabank
{
    protected $_url = '';
    protected $_currency;
    protected $_login;
    protected $_password;

    protected $_response;
    protected $_error_code = 0;
    protected $_error_text = '';

    public function __construct($processor_data)
    {
        $this->_login = $processor_data['processor_params']['login'];
        $this->_password = $processor_data['processor_params']['password'];

        if ($processor_data['processor_params']['mode'] == 'test') {
            $this->_url = "https://test.paymentgate.ru/testpayment/rest/";
        } else {
            $this->_url = "https://here.need.insert.url.for.live.payment/";
        }
    }

    public function register($order_info, $protocol = 'current')
    {
        $order_total = fn_format_rate_value($order_info['total'], 'F', 2, '.', '', '');
        $order_id = $order_info['order_id'];
        $orderNumber = $order_id . '_' . substr(md5($order_id . TIME), 0, 3);

        $data = array(
            'userName' => $this->_login,
            'password' => $this->_password,
            'orderNumber' => $orderNumber,
            'amount' => $order_total * 100,
            //'currency' => $this->_currency,
            'returnUrl' => fn_url("payment_notification.return?payment=alfabank&ordernumber=$order_id", AREA, $protocol),
            'failUrl' => fn_url("payment_notification.error?payment=alfabank&ordernumber=$order_id", AREA, $protocol),
        );

        $this->_response = Http::post($this->_url . 'register.do', $data);
        $this->_response = json_decode($this->_response, true);

        if (!empty($this->_response['errorCode'])) {
            $this->_error_code = $this->_response['errorCode'];
            $this->_error_text = $this->_response['errorMessage'];
        }

        return $this->_response;
    }

    public function getOrder($transaction_id)
    {
        $data = array(
            'userName' => $this->_login,
            'password' => $this->_password,
            'orderId' => $transaction_id
        );

        $this->_response = Http::post($this->_url . 'getOrderStatus.do', $data);

        $this->_response = json_decode($this->_response, true);

        if (!empty($this->_response['errorCode'])) {
            $this->_error_code = $this->_response['errorCode'];
            $this->_error_text = $this->_response['errorMessage'];
        }

        return $this->_response;
    }

    public function getOrderExtended($transaction_id)
    {
        $data = array(
            'userName' => $this->_login,
            'password' => $this->_password,
            'orderId' => $transaction_id
        );

        $this->_response = Http::post($this->_url . 'getOrderStatusExtended.do', $data);

        $this->_response = json_decode($this->_response, true);

        if (!empty($this->_response['errorCode'])) {
            $this->_error_code = $this->_response['errorCode'];
            $this->_error_text = $this->_response['errorMessage'];
        }

        return $this->_response;
    }

    public function getErrorCode()
    {
        return $this->_error_code;
    }

    public function getErrorText()
    {
        return $this->_error_text;
    }

    public function isError()
    {
        return !empty($this->_error_code);
    }

    public static function writeLog($data, $file = 'sberbank.log')
    {
        $path = fn_get_files_dir_path();
        fn_mkdir($path);
        $file = fopen($path . $file, 'a');

        if (!empty($file)) {
            fputs($file, 'TIME: ' . date('Y-m-d H:i:s', TIME) . "\n");
            fputs($file, fn_array2code_string($data) . "\n\n");
            fclose($file);
        }
    }
}
