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

class Qiwi
{
    protected $_url = '';
    protected $_lifetime = 0;
    protected $_currency;
    protected $_login;
    protected $_password;

    protected $_response;
    protected $_error_code = 0;
    protected $_error_text = '';

    public function __construct($processor_data)
    {
        $this->_login = $processor_data['processor_params']['login'];
        $this->_password = $processor_data['processor_params']['passwd'];
        $this->_currency = $processor_data['processor_params']['currency'];

        $shop_id = !empty($processor_data['processor_params']['shop_id']) ? $processor_data['processor_params']['shop_id'] : 0;
        $this->_url = "https://w.qiwi.com/api/v2/prv/$shop_id/bills/";

        $this->_lifetime = date('Y-m-d\TH:i:s', time() + ($processor_data['processor_params']['lifetime'] * 60));

        $this->_headers = array(
            "Accept: text/json",
            "Content-Type: application/x-www-form-urlencoded; charset=utf-8"
        );
    }

    public function createBill($order_transaction, $order_info)
    {
        $order_total = fn_format_rate_value($order_info['total'], 'F', 2, '.', '', '');
        $user = str_replace(array('+', ' ', '(', ')', '-'), '', $order_info['payment_info']['phone']);

        $data = array(
            'user' => 'tel:+' . $user,
            'amount' => $order_total,
            'comment' => (!empty($order_info['notice']) ? $order_info['notice'] : ''),
        );

        $url = $this->_url . $order_transaction;

        $data['ccy'] = $this->_currency;
        $data['lifetime'] = $this->_lifetime;

        $extra = array(
            'headers' => $this->_headers,
            'basic_auth' => array(
                $this->_login,
                $this->_password
            ),
        );

        $this->_response = Http::put($url, $data, $extra);
        $this->_response = json_decode($this->_response, true);

        if (!empty($this->_response['response']['result_code'])) {
            $this->_error_code = $this->_response['response']['result_code'];
            $this->_error_text = $this->_response['response']['description'];
        }

        return $this->_response;
    }

    public function getBill($order_transaction)
    {
        $url = $this->_url . $order_transaction;

        $extra = array(
            'headers' => $this->_headers,
            'basic_auth' => array(
                $this->_login,
                $this->_password
            ),
        );

        $this->_response = Http::get($url, array(), $extra);
        $this->_response = json_decode($this->_response, true);

        if (!empty($this->_response['response']['result_code'])) {
            $this->_error_code = $this->_response['response']['result_code'];
            $this->_error_text = $this->_response['response']['description'];
        }

        return $this->_response['response'];
    }

    public function formBill($order_transaction, $order_info, $processor_data)
    {
        $order_total = fn_format_rate_value($order_info['total'], 'F', 2, '.', '', '');
        $user = str_replace(array('+', ' ', '(', ')', '-'), '', $order_info['payment_info']['phone']);

        $data = array(
            'txn_id' => $order_transaction,
            'from' => $processor_data['processor_params']['shop_id'],
            'to' => '+' . $user,
            'summ' => $order_total,
            'currency' => $this->_currency,
            'successUrl' => fn_url("payment_notification.return?payment=qiwi_rest"),
            'failUrl' => fn_url("payment_notification.return?payment=qiwi_rest"),
            'lifetime' => $processor_data['processor_params']['lifetime'] * 60,
            'comm' => (!empty($order_info['notice']) ? $order_info['notice'] : '')
        );

        return $data;
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

    public function getStatusBill()
    {
        return $this->_response['response']['bill']['status'];
    }

    public static function writeLog($data, $file = 'qiwi.log')
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
