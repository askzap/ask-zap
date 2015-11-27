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
use Tygh\Shippings\RusSdek;

/**
 * Sdek shipping service
 */
class Sdek implements IService
{
    /**
     * Abailability multithreading in this module
     *
     * @var array $_allow_multithreading
     */
    private $_allow_multithreading = false;

    private $version = "1.0";

    /**
     * Stack for errors occured during the preparing rates process
     *
     * @var array $_error_stack
     */
    private $_error_stack = array();

    protected static $_error_descriptions = array(
        '0' => 'Внутренняя ошибка на сервере. Обратитесь к программистам компании СДЭК для исправления',
        '1' => 'Указанная вами версия API не поддерживается',
        '2' => 'Ошибка авторизации',
        '3' => 'Невозможно осуществить доставку по этому направлению при заданных условиях',
        '4' => 'Ошибка при указании параметров места ',
        '5' => 'Не задано ни одного места для отправления',
        '6' => 'Не задан тариф или список тарифов',
        '7' => 'Не задан город-отправитель',
        '8' => 'Не задан город-получатель',
        '9' => 'При авторизации не задана дата планируемой отправки',
        '10' => 'Ошибка задания режима доставки',
        '11' => 'Неправильно задан формат данных',
        '12' => 'Ошибка декодирования данных. Ожидается <json или jsop>',
        '13' => 'Почтовый индекс города-отправителя отсутствует в базе СДЭК',
        '14' => 'Невозможно однозначно идентифицировать город-отправитель по почтовому индексу',
        '15' => 'Почтовый индекс города-получателя отсутствует в базе СДЭК',
        '16' => 'Невозможно однозначно идентифицировать город-получатель по почтовому индексу',
    );

    /**
     * Current Company id environment
     *
     * @var int $company_id
     */
    public $company_id = 0;

    public $city_id;

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
     * Sets data to internal class variable
     *
     * @param array $shipping_info
     */
    public function prepareData($shipping_info)
    {
        $this->_shipping_info = $shipping_info;
        $this->company_id = Registry::get('runtime.company_id');
    }

    /**
     * Prepare request information
     *
     * @return array Prepared data
     */
    public function getRequestData()
    {
        static $request_data = NULL;

        $weight_data = fn_expand_weight($this->_shipping_info['package_info']['W']);
        $shipping_settings = $this->_shipping_info['service_params'];
        $origination = $this->_shipping_info['package_info']['origination'];
        $location = $this->_shipping_info['package_info']['location'];
        $ruble = Registry::get('currencies.RUB');

        if ($origination['country'] != 'RU') {
            $this->_internalError(__('shippings.sdek.country_error'));
        }

        if (empty($ruble) || $ruble['is_primary'] == 'N') {
            $this->_internalError(__('shippings.sdek.activation_error'));
        }

        $this->city_id = $_code = RusSdek::cityId($location);
        $_code_sender = $shipping_settings['from_city_id'];

        $url = 'http://api.edostavka.ru/calculator/calculate_price_by_json.php';
        isset($this->version) ? $post['version'] = $this->version : '';
        if (!empty($shipping_settings['dateexecute'])) {
            $timestamp = TIME + $shipping_settings['dateexecute'] * SECONDS_IN_DAY;
            $dateexecute = date('Y-m-d', $timestamp);
        } else {
            $dateexecute = date('Y-m-d');
        }

        $post['dateExecute'] = $dateexecute;

        if (!empty($shipping_settings['authlogin'])) {
            $post['authLogin'] = $shipping_settings['authlogin'];
            $post['secure'] = !empty($shipping_settings['authpassword']) ? md5($post['dateExecute']."&".$shipping_settings['authpassword']): '';
        }

        $post['senderCityId'] = (int) $_code_sender;
        $post['receiverCityId'] = (int) $_code;
        $post['tariffId'] = $shipping_settings['tariffid'];

        $weight = round($weight_data['plain'] * Registry::get('settings.General.weight_symbol_grams') / 1000, 3);
        $length = !empty($shipping_settings['length']) ? $shipping_settings['length'] : 20;
        $width = !empty($shipping_settings['width']) ? $shipping_settings['width'] : 20;
        $height = !empty($shipping_settings['height']) ? $shipping_settings['height'] : 20;

        $goods = array();
        if (!empty($this->_shipping_info['package_info']['packages'])) {
            $packages = $this->_shipping_info['package_info']['packages'];
            $packages_count = count($packages);
            if ($packages_count > 0) {
                foreach ($packages as $id => $package) {
                    $package_length = empty($package['shipping_params']['box_length']) ? $length : $package['shipping_params']['box_length'];
                    $package_width = empty($package['shipping_params']['box_width']) ? $width : $package['shipping_params']['box_width'];
                    $package_height = empty($package['shipping_params']['box_height']) ? $height : $package['shipping_params']['box_height'];
                    $weight_ar = fn_expand_weight($package['weight']);
                    $weight = round($weight_ar['plain'] * Registry::get('settings.General.weight_symbol_grams') / 1000, 3);

                    $goods[$id]['weight'] = $weight;
                    $goods[$id]['length'] = $package_length;
                    $goods[$id]['width'] = $package_width;
                    $goods[$id]['height'] = $package_height;
                }
            } else {
               $goods['weight'] = $weight;
               $goods['length'] = $length;
               $goods['width'] = $width;
               $goods['height'] = $height;
               $goods = array ($goods);
            }
        } else {
            $weight = round($weight_data['plain'] * Registry::get('settings.General.weight_symbol_grams') / 1000, 3);
            $goods['weight'] = $weight;
            $goods['length'] = $length;
            $goods['width'] = $width;
            $goods['height'] = $height;
            $goods = array ($goods);
        }
        $post['goods'] = $goods;

        $request_data = array(
            'method' => 'post',
            'url' => $url,
            'data' => json_encode($post),
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
        $key = md5($data['data']);
        $sdek_data = fn_get_session_data($key);
        $data_string = json_encode($data['data']);

        if (empty($sdek_data)) {
            $response = Http::post($data['url'], $data['data'], array('Content-Type: application/json',  'Content-Length: '.strlen($data_string)));
            fn_set_session_data($key, $response);
        } else {
            $response = $sdek_data;
        }

        return $response;
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

        $result = json_decode($response);
        $result_array = json_decode(json_encode($result), true);

        if (empty($this->_error_stack) && !empty($result_array['result'])) {

            $rates = $this->_getRates($result_array);

            $this->_fillSessionData($rates);

            if (empty($this->_error_stack) && !empty($rates['price'])) {
                $return['cost'] = $rates['price'];
            } else {
                $this->_internalError(__('xml_error'));
                $return['error'] = $this->processErrors($result_array);
            }

        } else {
            $return['error'] = $this->processErrors($result_array);
        }

        return $return;
    }

    private function _getRates($response)
    {

        $rates = array();
        if (!empty($response['result']['price'])) {
            $rates['price'] = $response['result']['price'];
            if (!empty($response['result']['deliveryPeriodMin']) && !empty($response['result']['deliveryPeriodMax'])) {
                $plus = $this->_shipping_info['service_params']['dateexecute'];
                $min_time = $plus + $response['result']['deliveryPeriodMin'];
                $max_time = $plus + $response['result']['deliveryPeriodMax'];
                $date = $min_time . '-' . $max_time . ' (' . __('days') . ')';
                if (!empty($date)) {
                    $rates['date'] = $date;
                }
            }

            $rec_city_code = $this->city_id;
            $tarif_id = $this->_shipping_info['service_params']['tariffid'];
            if (!empty($rec_city_code) && $tarif_id == SDEK_STOCKROOM && $tarif_id == $response['result']['tariffId']) {
                $params = array(
                    'cityid' => $rec_city_code
                );
                $offices = RusSdek::pvzOffices($params);
                if (!empty($offices)) {
                    $rates['offices'] = $offices;
                } else {
                    $rates['clear'] = true;
                }
            }
        }

        return $rates;
    }

    private function _fillSessionData($rates = array())
    {
        $shipping_info = $this->_shipping_info;

        if (isset($shipping_info['keys']['group_key']) && !empty($shipping_info['keys']['shipping_id'])) {
            $group_key = $shipping_info['keys']['group_key'];
            $shipping_id = $shipping_info['keys']['shipping_id'];

            if (!empty($rates['offices'])) {
                $_SESSION['cart']['shippings_extra']['data'][$group_key][$shipping_id]['offices'] = $rates['offices'];
            }

            if (!empty($rates['date'])) {
                $_SESSION['cart']['shippings_extra']['data'][$group_key][$shipping_id]['delivery_time'] = $rates['date'];
            }

            if (!empty($rates['clear'])) {
                unset($_SESSION['cart']['shippings_extra']['data'][$group_key][$shipping_id]['offices']);
            }
        }

        return true;
    }

    /**
     * Gets error message from shipping service server
     *
     * @param  string $resonse Reponse from Shipping service server
     * @return string Text of error or false if no errors
     */
    public function processErrors($result_array)
    {
        // Parse JSON message returned by the sdek post server.
        $return = false;

        if (!empty($result_array['error'])) {
            if (!empty($result_array['error'][0]['code'])) {
                $status_code = $result_array['error'][0]['code'];
                if (empty($result_array['error'][0]['text'])) {
                    $return = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : 'Ошибка расчета';
                } else {
                    $return = $result_array['error'][0]['text'];
                }
            }
        }

        if (!empty($this->_error_stack)) {
            foreach ($this->_error_stack as $error) {
                $return .= '; ' . $error;
            }
        }

        return $return;
    }
}
