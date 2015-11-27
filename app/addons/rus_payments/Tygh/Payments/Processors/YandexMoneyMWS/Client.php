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

namespace Tygh\Payments\Processors\YandexMoneyMWS;

use Tygh\Http;
use Tygh\Payments\Processors\YandexMoneyMWS\Exception as ExceptionMWS;

class Client
{
    protected $options = array(
        'protocol' => 'https',
        'is_test_mode' => false,
        'live_host' => 'penelope.yamoney.ru',
        'test_host' => 'penelope-demo.yamoney.ru',
        'test_port' => 8083,
        'url' => ':protocol://:host::port/webservice/mws/api/:path',
        'shop_id' => null,
        'sslcert' => null,
        'sslkey' => null,
    );

    public function __construct(array $options = array())
    {
        $this->authenticate($options);
    }

    public function authenticate(array $options)
    {
        if (!empty($options['pkcs12_file'])) {
            list($options['sslcert'], $options['sslkey']) = self::extractPKCS12($options['pkcs12_file'], $options['pass']);
        }

        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }

    public function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    public function confirmPayment($order_id, $amount, $currency = 'RUB')
    {
        $params = array(
            'orderId' => $order_id,
            'amount' => $amount,
            'currency' => $currency,
            'requestDT' => date('c'),
        );

        $this->request('/confirmPayment', $params);
    }

    public function cancelPayment($order_id)
    {
        $params = array(
            'orderId' => $order_id,
        );

        $this->request('/cancelPayment', $params);
    }

    public function returnPayment($client_order_id, $invoice_id, $amount, $cause = '#', $currency = '643')
    {
        $xml_request = '<?xml version="1.0" encoding="UTF-8"?><returnPaymentRequest'
            . ' clientOrderId="' . $client_order_id . '"'
            . ' invoiceId="' . $invoice_id . '"'
            . ' amount="' . $amount . '"'
            . ' currency="' . $currency . '"'
            . ' cause="' . $cause . '"'
            . ' shopId="' . $this->options['shop_id'] . '"'
            . ' requestDT="' . date('c') . '"'
            . '/>';

        $params = self::encryptPKCS7($this->options['sslcert'], $this->options['sslkey'], $xml_request);

        $this->request('/returnPayment', $params);
    }

    public function getOrders($params = array())
    {
        $params_map = array(
            'order_number'  => 'orderNumber',
            'created_from'  => 'orderCreatedDatetimeGreaterOrEqual',
            'created_to'    => 'orderCreatedDatetimeLessOrEqual',
            'paid_from'     => 'paymentDatetimeGreaterOrEqual',
            'paid_to'       => 'paymentDatetimeLessOrEqual',
            'paid'          => 'paid',
        );

        $parsed_params = array(
            'shopId' => $this->options['shop_id'],
            'requestDT' => date('c'),
        );

        foreach ($params as $k => $v) {
            if (!empty($params_map[$k])) {
                if (in_array($k, array('created_from', 'created_to', 'paid_from', 'paid_to'))) {
                    $parsed_params[$params_map[$k]] = date('c', $v);
                } else {
                    $parsed_params[$params_map[$k]] = $v;
                }
            }
        }

        $orders = $this->request('/listOrders', $parsed_params);

        return $orders;
    }

    public function request($path, $params = array())
    {
        $options = $this->options;

        $url = strtr($options['url'], array(
            ':protocol'     => $options['protocol'],
            ':host'         => $options['is_test_mode'] ? $options['test_host'] : $options['live_host'],
            '::port'        => $options['is_test_mode'] ? ':' . $options['test_port'] : '',
            ':path'         => trim($path, '/'),
        ));

        fn_yandex_money_log_write(array('REQUEST', $url, $params, $options), 'ym_mws_requests.log');

        $response = Http::post($url, $params, array(
            'ssl_cert' => $options['sslcert'],
            'ssl_key' => $options['sslkey'],
        ));

        fn_yandex_money_log_write(array('RESPONSE', $response), 'ym_mws_requests.log');

        $response = $this->decodeResponse($response);

        if (empty($response) || !empty($response['error']) || !empty($response['status'])) {
            throw new ExceptionMWS('Error occured!', $response['error'], $response['techMessage']);
        }

        return $response;
    }

    public static function extractPKCS12($pkcs12_file, $pass = null)
    {
        $certs = array();
        $cert_filename = '';
        $pkey_filename = '';

        $res = openssl_pkcs12_read(file_get_contents($pkcs12_file), $certs, $pass);

        $pathinfo = pathinfo($pkcs12_file);

        if (!empty($certs['cert'])) {
            $cert_filename = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.crt';
            file_put_contents($cert_filename, $certs['cert']);
        }

        if (!empty($certs['pkey'])) {
            $pkey_filename = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.key';
            file_put_contents($pkey_filename, $certs['pkey']);
        }

        return array($cert_filename, $pkey_filename);
    }

    public static function encryptPKCS7($sslcert, $sslkey, $request_body)
    {
        $descriptorspec = array(
            0 => array("pipe", "r"), // stdin is a pipe that the child will read from
            1 => array("pipe", "w"), // stdout is a pipe that the child will write to
            2 => array("pipe", "w")
        ); // stderr is a file to write to

        $process = proc_open(
            'openssl smime -sign -signer ' . $sslcert .
            ' -inkey ' . $sslkey .
            ' -nochain -nocerts -outform PEM -nodetach',
            $descriptorspec, $pipes
        );

        fwrite($pipes[0], $request_body);
        fclose($pipes[0]);

        $data = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        return $data;
    }

    protected function decodeResponse($response)
    {
        return simplexml_load_string($response);
    }
}
