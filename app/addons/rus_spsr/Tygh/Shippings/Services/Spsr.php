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

namespace Tygh\Shippings\Services;

use Tygh\Shippings\IService;
use Tygh\Registry;
use Tygh\Http;
use Tygh\RusSpsr;

/**
 * Edost shipping service
 */
class Spsr implements IService
{
    /**
     * Abailability multithreading in this module
     *
     * @var array $_allow_multithreading
     */
    private $_allow_multithreading = true;

    /**
     * Stack for errors occured during the preparing rates process
     *
     * @var array $_error_stack
     */
    private $_error_stack = array();

    /**
     * Current Company id environment
     *
     * @var int $company_id
     */
    public $company_id = 0;

    public $sid;
    /**
     * Collects errors during preparing and processing request
     *
     * @param string $error
     */
    private function _internalError($error)
    {
        $this->_error_stack[] = $error;
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
     * Gets error message from shipping service server
     *
     * @param  string $resonse Reponse from Shipping service server
     * @return string Text of error or false if no errors
     */
    public function processErrors($response)
    {
        return $return;
    }

    /**
     * Sets data to internal class variable
     *
     * @param array $shipping_info
     */
    public function prepareData($shipping_info)
    {
        $this->_shipping_info = $shipping_info;
        $this->company_id = Registry::get('runtime.company_id');

        if(!RusSpsr::WALogin()) {
            $this->_internalError(RusSpsr::$last_error);
        }
    }

    /**
     * Prepare request information
     *
     * @return array Prepared data
     */
    public function getRequestData()
    {
        $data = array();
        $login = RusSpsr::WALogin();
        if ($login) {
            $weight_data = fn_expand_weight($this->_shipping_info['package_info']['W']);
            $shipping_settings = $this->_shipping_info['service_params'];
            $location = !empty($this->_shipping_info['package_info']['location']) ? $this->_shipping_info['package_info']['location'] : '';
            $packages = !empty($this->_shipping_info['package_info']['packages']) ? $this->_shipping_info['package_info']['packages'] : array();
            $code = $this->_shipping_info['service_code'];
            $ruble = Registry::get('currencies.RUB');

            if (!empty($shipping_settings)) {
                $from_city = "";
                $to_city = "";
                $nature = $shipping_settings['default_product_type'];

                if (!empty($_SESSION['cart'])) {
                    $cart = $_SESSION['cart'];
                    $products = $uniq_types = array();
                    foreach($packages as $package) {
                        foreach($package['products'] as $key => $product) {
                            $products[$key] = $cart['products'][$key]['spsr_product_type'];
                            $uniq_types[] = $cart['products'][$key]['spsr_product_type'];
                        }
                    }

                    $uniq_types = array_unique($uniq_types);
                    if(count($uniq_types) == 1) {
                        $nature = $uniq_types[0];
                    } elseif (count($uniq_types) > 1) {
                        if(array_search(18, $uniq_types)) {
                            $amount_check = 1;
                            $nature = 18;
                        }
                    }
                }

                if (!empty($shipping_settings['from_city_id'])){
                    $from_city = $shipping_settings['from_city_id'].'|'.$shipping_settings['from_city_owner_id'];
                } else {
                    $this->_internalError(__("shipping.spsr.not_setting_city"));
                }

                if (isset($shipping_settings['insurance_type']) && !empty($shipping_settings['insurance_type'])) {
                    if($shipping_settings['insurance_type'] == "INS") {
                        $amount_check = 1;
                    } elseif ($shipping_settings['insurance_type'] == "VAL") {
                        $amount_check = 0;
                    }
                }

                $city = RusSpsr::WAGetCities($location);

                if (!empty($city)){
                    $to_city = $city['City_ID'].'|'.$city['City_owner_ID'];
                } else {
                    $this->_internalError(RusSpsr::$last_error);
                }

                $weight = $weight_data['plain'];
                $amount = $this->_shipping_info['package_info']['C'];
                $sid = RusSpsr::$sid;

                $data = array(
                    'ToCity' => $to_city,
                    'FromCity' => $from_city,
                    'Weight' => $weight,
                    'Nature' => $nature,
                    'Amount' => $amount,
                    'AmountCheck' => $amount_check,
                    'SMS' => $shipping_settings['sms_to_shipper'],
                    'SMS_Recv' => $shipping_settings['sms_to_receiver'],
                    'PlatType' => $shipping_settings['plat_type'],
                    'DuesOrder' => $shipping_settings['dues_order'], 
                    'ToBeCalledFor' => $shipping_settings['to_be_called_for'],
                    'ByHand' => $shipping_settings['by_hand'],
                    'icd' => $shipping_settings['idc'],
                    'SID' => $sid,
                );
            }
        } else {
            fn_set_notification('E', __('notice'), RusSpsr::$last_error);
        }

        $url = 'http://www.cpcr.ru/cgi-bin/postxml.pl?TARIFFCOMPUTE_2';
        $request_data = array(
            'method' => 'post',
            'url' => $url,
            'data' => $data,
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
        $response = Http::post($data['url'], $data['data']);

        return $response;
    }

    private function _fillSessionData($shipping_info, $day)
    {
        if (!empty($shipping_info['keys']['group_key']) && !empty($shipping_info['keys']['shipping_id'])) {
            $group_key = $shipping_info['keys']['group_key'];
            $shipping_id = $shipping_info['keys']['shipping_id'];

            /* Bad code: We should not use Global variables in the Class methods */
            if (!isset($_SESSION['cart']['shippings_extra'][$group_key])) {
                $_SESSION['cart']['shippings_extra'][$group_key] = array();
            }

            $_SESSION['cart']['shippings_extra']['rates'][$group_key][$shipping_id]['day'] = $day . ' ' . __('days');
        }

        return true;
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

        $xml = simplexml_load_string($response);
        if (isset($xml->Error)){
            $this->_internalError((string)$xml->Error);

        } elseif (isset($xml->Tariff)) {
            $_result = array();
            foreach ($xml->Tariff as $shipment) {
                $_result[] = array(
                    'TariffType' => (string) $shipment->TariffType,
                    'Total_Dost' => (string) $shipment->Total_Dost,
                    'Total_DopUsl' => (string) $shipment->Total_DopUsl,
                    'Insurance' => (string) $shipment->id,
                    'worth' => (string) $shipment->Insurance,
                    'DP' => (string) $shipment->DP,
                );
            }
        }

        if(!empty($_result)) {
            foreach($_result as $ship) {
                if ($ship['TariffType'] == 'Услуги по доставке "Пеликан-стандарт"') {
                    $return['cost'] = $ship['Total_Dost'];
                    self::_fillSessionData($this->_shipping_info, $ship['DP']);
                }
            }
        } 

        if(empty($return['cost'])) {
            $return['error'] = __('shippings.spsr.error_get_cost');
        }

        if (!empty($this->_error_stack)) {
            foreach ($this->_error_stack as $error) {
                $return['error'] .= '; ' . $error;
            }
        }

        RusSpsr::WALogout();

        return $return;
    }
}
