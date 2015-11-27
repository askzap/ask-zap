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
class Ems implements IService
{
    /**
     * Availability multithreading in this module
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
     * Timeout requests to Russian Post
     *
     * @var integer $_timeout
     */
    private $_timeout = 5;

    private function _internalError($error)
    {
        $this->_error_stack[] = $error;
    }

    /**
     * Gets representative region name from State code
     * 	Example: 'ULY' => 'region--uljanovskaja-oblast',
     *
     * @param  string $state 2-3 letters State code
     * @return string representative region code
     */
    private function _convertState($state)
    {
        $convert = array (
            'ALT' => 'region--altajskij-kraj',
            'AMU' => 'region--amurskaja-oblast',
            'ARK' => 'region--arhangelskaja-oblast',
            'AST' => 'region--astrahanskaja-oblast',
            'BEL' => 'region--belgorodskaja-oblast',
            'BRY' => 'region--brjanskaja-oblast',
            'CE' => 'region--chechenskaja-respublika',
            'CHE' => 'region--cheljabinskaja-oblast',
            'CHU' => 'region--chukotskij-ao',
            'CU' => 'region--chuvashskaja-respublika',
            'YEV' => 'region--evrejskaja-ao',
            'KHA' => 'region--khabarovskij-kraj',
            'KHM' => 'region--khanty-mansijskij-ao',
            'IRK' => 'region--irkutskaja-oblast',
            'IVA' => 'region--ivanovskaja-oblast',
            'YAN' => 'region--yamalo-neneckij-ao',
            'YAR' => 'region--yaroslavskaja-oblast',
            'KB' => 'region--kabardino-balkarskaja-respublika',
            'KGD' => 'region--kaliningradskaja-oblast',
            'KLU' => 'region--kaluzhskaja-oblast',
            'KAM' => 'region--kamchatskij-kraj',
            'KC' => 'region--karachaevo-cherkesskaja-respublika',
            'KEM' => 'region--kemerovskaja-oblast',
            'KIR' => 'region--kirovskaja-oblast',
            'KOS' => 'region--kostromskaja-oblast',
            'KDA' => 'region--krasnodarskij-kraj',
            'KIA' => 'region--krasnojarskij-kraj',
            'KGN' => 'region--kurganskaja-oblast',
            'KRS' => 'region--kurskaja-oblast',
            'LEN' => 'region--leningradskaja-oblast',
            'LIP' => 'region--lipeckaja-oblast',
            'MAG' => 'region--magadanskaja-oblast',
            'MOS' => 'region--moskovskaja-oblast',
            'MOW' => 'region--moskovskaja-oblast',//the same as for moskovskaja oblast because ems does not provide a different code for moscow
            'MUR' => 'region--murmanskaja-oblast',
            'NEN' => 'region--neneckij-ao',
            'NIZ' => 'region--nizhegorodskaja-oblast',
            'NGR' => 'region--novgorodskaja-oblast',
            'NVS' => 'region--novosibirskaja-oblast',
            'OMS' => 'region--omskaja-oblast',
            'ORE' => 'region--orenburgskaja-oblast',
            'ORL' => 'region--orlovskaja-oblast',
            'PNZ' => 'region--penzenskaja-oblast',
            'PER' => 'region--permskij-kraj',
            'PRI' => 'region--primorskij-kraj',
            'PSK' => 'region--pskovskaja-oblast',
            'AD' => 'region--respublika-adygeja',
            'AL' => 'region--respublika-altaj',
            'BA' => 'region--respublika-bashkortostan',
            'BU' => 'region--respublika-burjatija',
            'DA' => 'region--respublika-dagestan',
            'KK' => 'region--respublika-khakasija',
            'IN' => 'region--respublika-ingushetija',
            'KL' => 'region--respublika-kalmykija',
            'KR' => 'region--respublika-karelija',
            'KO' => 'region--respublika-komi',
            'ME' => 'region--respublika-marij-el',
            'MO' => 'region--respublika-mordovija',
            'SA' => 'region--respublika-saha-yakutija',
            'SE' => 'region--respublika-sev.osetija-alanija',
            'TA' => 'region--respublika-tatarstan',
            'TY' => 'region--respublika-tyva',
            'RYA' => 'region--rjazanskaja-oblast',
            'ROS' => 'region--rostovskaja-oblast',
            'SAK' => 'region--sahalinskaja-oblast',
            'SAM' => 'region--samarskaja-oblast',
            'SPE' => 'region--leningradskaja-oblast',//the same as for leningradskaya oblast because ems does not provide a different code for st. petersburg
            'SAR' => 'region--saratovskaja-oblast',
            'SMO' => 'region--smolenskaja-oblast',
            'STA' => 'region--stavropolskij-kraj',
            'SVE' => 'region--sverdlovskaja-oblast',
            'TAM' => 'region--tambovskaja-oblast',
            'TYU' => 'region--tjumenskaja-oblast',
            'TOM' => 'region--tomskaja-oblast',
            'TUL' => 'region--tulskaja-oblast',
            'TVE' => 'region--tverskaja-oblast',
            'UD' => 'region--udmurtskaja-respublika',
            'ULY' => 'region--uljanovskaja-oblast',
            'VLA' => 'region--vladimirskaja-oblast',
            'VGG' => 'region--volgogradskaja-oblast',
            'VLG' => 'region--vologodskaja-oblast',
            'VOR' => 'region--voronezhskaja-oblast',
            'ZAB' => 'region--zabajkalskij-kraj',
        );

        return !empty($convert[$state]) ? $convert[$state] : '';
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
            'delivery_time' => false
        );

        $cost = json_decode($response, true);

        if (empty($this->_error_stack) && !empty($cost['rsp']['price'])) {
            $result = $cost['rsp']['price'];
            if (CART_PRIMARY_CURRENCY != 'RUB') {
                $result = fn_rus_russianpost_format_price_down($result, 'RUB');
            }

            if (!empty($cost['rsp']['term']) && !empty($cost['rsp']['term']['min']) && !empty($cost['rsp']['term']['max'])) {

                if (!empty($this->_shipping_info['service_params']['delivery_time_plus'])) {
                    $plus_day = $this->_shipping_info['service_params']['delivery_time_plus'];
                } else {
                    $plus_day = 0;
                }

                $min_time = $cost['rsp']['term']['min'] + $plus_day;
                $max_time = $cost['rsp']['term']['max'] + $plus_day;

                $return['delivery_time'] = $min_time . '-' . $max_time . ' ' . __('days');
            }

            $return['cost'] = $result;

        } else {
            $return['error'] = $this->processErrors($cost);
        }

        return $return;
    }

    /**
     * Gets error message from shipping service server
     *
     * @param  string $resonse Reponse from Shipping service server
     * @return string Text of error or false if no errors
     */
    public function processErrors($json_response)
    {
        if (!empty($json_response['rsp']['err'])) {
            $error = $json_response['rsp']['err']['code'] . ': ' . $json_response['rsp']['err']['msg'];
        } else {
            $error = __('service_not_available');
        }

        if (!empty($this->_error_stack)) {
            foreach ($this->_error_stack as $_error) {
                $error .= '; ' . $_error;
            }
        }

        return $error;
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

        if ($origination['country'] != 'RU') {
            $this->_internalError(__('ems_country_error'));
        }

        $weight = $weight_data['plain'] * Registry::get('settings.General.weight_symbol_grams') / 1000;

        $origination_point = '';
        $destination_point = '';

        if (!isset($shipping_settings['mode']) || $shipping_settings['mode'] == 'regions') {
            $origination_point = $this->_convertState($origination['state']);
            $destination_point = $this->_convertState($location['state']);
        } else {

            $cities = $this->getEmsLocations();

            if (!empty($cities)) {
                foreach ($cities as $i => $loc_data) {

                    if (fn_strtolower($loc_data['name']) == fn_strtolower($origination['city']) || fn_strtolower(str_replace('city--', '', $loc_data['value'])) == fn_strtolower($origination['city'])) {
                        $origination_point = $loc_data['value'];
                    }

                    if (!empty($location['city']) && $location['country'] == 'RU') {
                        if (fn_strtolower($loc_data['name']) == fn_strtolower($location['city']) || fn_strtolower(str_replace('city--', '', $loc_data['value'])) == fn_strtolower($location['city'])) {
                            $destination_point = $loc_data['value'];
                        }
                    }

                    if (!empty($destination_point) && !empty($origination_point)) {
                        break;
                    }
                }

            }

            if (empty($destination_point)) {

                if (empty($location['state'])) {
                    $general = Registry::get('settings.General');
                    $location['state'] = $general['default_state'];
                }

                if ($location['country'] == 'RU') {
                        $destination_point = $this->_convertState($location['state']);

                } else {
                    $countries = $this->getEmsLocations('countries');

                    if (!empty($countries)) {
                        foreach ($countries as $i => $loc_data) {
                            if ($loc_data['value'] == $location['country']) {
                                $destination_point = $location['country'];
                                break;
                            }
                        }
                    }
                }

            }
        }

        $url = 'http://www.emspost.ru/api/rest';
        $data = array();

        if (!empty($destination_point) && !empty($origination_point)) {
            $data = array (
                'method' => 'ems.calculate',
                'from' => $origination_point,
                'to' => $destination_point,
                'weight' => $weight,
                'type' => 'att'
            );
        }

        $request_data = array(
            'method' => 'get',
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
        $response = Http::get($data['url'], $data['data']);

        return $response;
    }

    /**
     * Process get EMS shipping destinations
     *
     * @param  string cities, regions or countries
     * @return array Server response
     */
    public function getEmsLocations($type = 'cities')
    {

        $url = 'http://www.emspost.ru/api/rest';
        $request = array (
            'method' => 'ems.get.locations',
            'type' => $type,
            'plain' => 'true'
        );
        $result = Http::get($url, $request, array('timeout' => $this->_timeout));
        $result = json_decode($result, true);

        $response = false;
        if (!empty($result['rsp'])) {
            if ($result['rsp']['stat'] == 'ok' && !empty($result['rsp']['locations'])) {
                $response = $result['rsp']['locations'];

            } elseif ($result['rsp']['stat'] == 'fail') {
                $this->_internalError($result['rsp']['err']['msg']);
            }
        }

        return $response;
    }
}
