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

use Tygh\Shippings\IService;
use Tygh\Registry;
use Tygh\Http;

/**
 * UPS shipping service
 */
class RussianPostOfficial implements IService
{
    /**
     * Availability multithreading in this module
     *
     * @var array $_allow_multithreading
     */
    private $_allow_multithreading = false;

    /**
     * Maximum allowed requests to Russian Post server
     *
     * @var integer $_max_num_requests
     */
    private $_max_num_requests = 2;


    /**
     * Timeout requests to Russian Post server
     *
     * @var integer $_timeout
     */
    private $_timeout = 3;
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
     */
    public function prepareData($shipping_info)
    {
        $this->_shipping_info = $shipping_info;
    }

    /**
     * Gets shipping cost and information about possible errors
     *
     * @param  string $resonse Reponse from Shipping service server
     * @return array  Shipping cost and errors
     */
    public function processResponse($response)
    {
        $return = array(
            'cost' => false,
            'error' => false,
        );

        $matches = array();
        preg_match('/<span id=\"TarifValue\">([0-9]*[,|][0-9]*)<\/span>/i', $response, $matches);

        $shipping_cost = !empty($matches[1]) ? preg_replace('/,/', '.', $matches[1]) : '';

        if (CART_PRIMARY_CURRENCY != 'RUB') {
            $shipping_cost = fn_rus_russianpost_format_price_down($shipping_cost, 'RUB');
        }

        if (empty($this->_error_stack) && $shipping_cost) {
            $return['cost'] = $shipping_cost;

        } else {
            $return['error'] = $this->processErrors($response);
        }

        return $return;
    }

    /**
     * Gets error message from shipping service server
     *
     * @param  string $resonse Reponse from Shipping service server
     * @return string Text of error or false if no errors
     */
    public function processErrors($response)
    {
        preg_match('/<span id=\"lblErrStr\">(.*)<\/span>/i', $response, $matches);

        $error = !empty($matches[1]) ? $matches[1] : __('error_occurred');

        if (!empty($this->_error_stack)) {
            foreach ($this->_error_stack as $_error) {
                $error .= '; ' . $_error;
            }
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
        $shipping_settings = $this->_shipping_info['service_params'];
        $origination = $this->_shipping_info['package_info']['origination'];
        $location = $this->_shipping_info['package_info']['location'];

        $country_code = db_get_field("SELECT code_N3 FROM ?:countries WHERE code = ?s", $location['country']);

        if (empty($location['zipcode'])) {
            $this->_internalError(__('russian_post_empty_zipcode'));
            $location['zipcode'] = false;
        }

        $ruble = Registry::get('currencies.RUB');

        if (empty($ruble) || $ruble['is_primary'] == 'N') {
            $this->_internalError(__('russian_post_activation_error'));
        }

        $russian_post_request_settings = array(
            'package_type' => array(
                'zak_band' => 23,
                'zak_pis' => 13,
                'cen_band' => 26,
                'cen_pos' => 36,
                'cen_pis' => 16,
                'zak_kart' => 18,
                'ob_pos' => 33,
            ),
            'shipping_type' => array(
                'ground' => 1,
                'air' => 2,
            )
        );

        $shipping_settings['shipping_type'] = !empty($shipping_settings['shipping_type']) ? $shipping_settings['shipping_type'] : 'ground';
        $shipping_settings['package_type'] = !empty($shipping_settings['package_type']) ? $shipping_settings['package_type'] : 'zak_band';

        if ($shipping_settings['shipping_type'] == 'air' && ($shipping_settings['package_type'] == 'cen_band' || $shipping_settings['package_type'] == 'cen_pos')) {
            $this->_internalError(__('service_not_available'));
        }

        $shipping_type = $russian_post_request_settings['shipping_type'][$shipping_settings['shipping_type']];
        $package_type = $russian_post_request_settings['package_type'][$shipping_settings['package_type']];
        $weight = (int) round($weight_data['plain'] * Registry::get('settings.General.weight_symbol_grams')); // in grams
        $total_cost = $this->_shipping_info['package_info']['C'];

        if (CART_PRIMARY_CURRENCY != 'RUB') {
            $total_cost = fn_rus_russianpost_format_price($total_cost, 'RUB');
        }

        $url = 'http://www.russianpost.ru/autotarif/Autotarif.aspx';
        $request = array(
            'viewPost' => $package_type,
            'countryCode' => $country_code,
            'typePost' => $shipping_type,
            'weight' => $weight,
            'value1' => ceil($total_cost),
            'postOfficeId' => $location['zipcode'],
        );

        $request_data = array(
            'method' => 'get',
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

        $response = false;

        if (empty($this->_error_stack)) {
            $data = $this->getRequestData();

            // Russian post server works very unstably, that is why we cannot use multithreading and should use cycle.
            $retry = 0;
            do {
                $retry++;
                $response = Http::get($data['url'], $data['data'], array('timeout' => $this->_timeout));
            } while (strpos($response, 'Результаты расчёта') == 0 && $retry <= $this->_max_num_requests);

            if ($retry == $this->_max_num_requests) {
                $this->_internalError(__('error_occurred'));
            }
        }

        return $response;
    }
}
