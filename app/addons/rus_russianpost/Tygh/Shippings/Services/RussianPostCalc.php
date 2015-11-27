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

// rus_build_pack dbazhenov

namespace Tygh\Shippings\Services;

use Tygh\Registry;
use Tygh\Shippings\IService;
use Tygh\Http;

/**
 * UPS shipping service
 */
class RussianPostCalc implements IService
{
    /**
     * Availability multithreading in this module
     *
     * @var array $_allow_multithreading
     */
    private $_allow_multithreading = false;

    /**
     * Stack for errors occured during the preparing rates process
     *
     * @var array $_error_stack
     */
    private $_error_stack = array();

    private function _internalError($error)
    {
        $this->_error_stack[] = $error;
    }

    /**
     * Sets data to internal class variable
     *
     * @param array $shipping_info
     * @return array|void
     */
    public function prepareData($shipping_info)
    {
        $this->_shipping_info = $shipping_info;
    }

    /**
     * Gets shipping cost and information about possible errors
     *
     * @param string $response
     * @return array Shipping cost and errors
     * @internal param string $resonse Reponse from Shipping service server
     */
    public function processResponse($response)
    {
        $return = array(
            'cost' => false,
            'error' => false,
            'delivery_time' => false,
        );

        $shipping_type = $this->_shipping_info['service_params']['shipping_type'];
        $response = json_decode($response, true);

        if ($response['msg']['type'] == 'done') {

            foreach ($response['calc'] as $calc) {
                if ($calc['type'] == $shipping_type) {
                    if ($calc['cost'] != 0) {
                        $cost = $calc['cost'];
                        if (CART_PRIMARY_CURRENCY != 'RUB') {
                            $cost = fn_rus_russianpost_format_price_down($cost, 'RUB');
                        }

                        if (!empty($this->_shipping_info['delivery_time'])) {
                            $plus_day = (int) $this->_shipping_info['delivery_time'];
                        } else {
                            $plus_day = 0;
                        }

                        $return['cost'] = $cost;
                        if (!empty($plus_day)) {
                            $return['delivery_time'] = $calc['days'] . '-' . ($calc['days'] + $plus_day) . ' ' . __('days');
                        } else {
                            $return['delivery_time'] = $calc['days'] . ' ' . __('days');
                        }

                        break;
                    } else {
                        $this->_internalError(__('error_occurred'));
                    }
                }
            }

        } else {
            $return['error'] = $this->processErrors($response);
        }

        return $return;
    }

    /**
     * Gets error message from shipping service server
     *
     * @param string $response
     * @return string Text of error or false if no errors
     * @internal param string $resonse Reponse from Shipping service server
     */
    public function processErrors($response)
    {
        $error = __('error_occurred');
        if ($response['msg']['type'] == 'error') {
            $error = $response['msg']['text'];
        }

        return $error;
    }

    /**
     * Checks if shipping service allows to use multithreading
     *
     * @return bool true if allow
     */
    public function allowMultithreading()
    {
        return $this->_allow_multithreading;
    }

    /**
     * Prepare request information
     *
     * @return array Prepared data
     */
    public function getRequestData()
    {
        $weight_data = fn_expand_weight($this->_shipping_info['package_info']['W']);
        $origination = $this->_shipping_info['package_info']['origination'];
        $location = $this->_shipping_info['package_info']['location'];
        $cost = $this->_shipping_info['package_info']['C'];

        if (CART_PRIMARY_CURRENCY != 'RUB') {
            $cost = fn_rus_russianpost_format_price($cost, 'RUB');
        }

        $weight_data['plain'] = $weight_data['plain'] * Registry::get('settings.General.weight_symbol_grams') / 1000;

        $url = 'http://russianpostcalc.ru/api_v1.php';
        $request = array(
            'apikey' => !empty($this->_shipping_info['service_params']['user_key']) ? $this->_shipping_info['service_params']['user_key'] : '',
            'method' => 'calc',
            'from_index' => !empty($origination['zipcode']) ? $origination['zipcode'] : '',
            'to_index' => !empty($location['zipcode']) ? $location['zipcode'] : '',
            'weight' => $weight_data['plain'],
            'ob_cennost_rub' => $cost,
        );

        $all_to_md5 = $request;
        $all_to_md5[] = !empty($this->_shipping_info['service_params']['user_key_password']) ? $this->_shipping_info['service_params']['user_key_password'] : '';
        $request['hash'] = md5(implode("|", $all_to_md5));

        $request_data = array(
            'method' => 'post',
            'url' => $url,
            'data' => $request,
        );

        return $request_data;
    }

    /**
     * Process simple request to shipping service server
     *
     * @return string Server response
     */
    public function getSimpleRates()
    {
        $data = $this->getRequestData();

        // Russian post server works very unstably, that is why we cannot use multithreading.
        $key = md5(serialize($data['data']));
        $response = fn_get_session_data($key);

        if (empty($response)) {
            $response = Http::get($data['url'], $data['data']);
            fn_set_session_data($key, $response);
        }

        return $response;
    }
}
