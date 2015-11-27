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

class Pickup implements IService
{
    /**
     * Abailability multithreading in this module
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

    /**
     * Current Company id environment
     *
     * @var int $company_id
     */
    public $company_id = 0;

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
     * @param string $response
     * @internal param string $resonse Reponse from Shipping service server
     * @return string Text of error or false if no errors
     */
    public function processErrors($response)
    {

        if (!empty($this->_error_stack)) {
            $error = '';
            foreach ($this->_error_stack as $_error) {
                $error .= '; ' . $_error;
            }
        }

        return $error;
    }

    /**
     * Sets data to internal class variable
     *
     * @param  array      $shipping_info
     * @return array|void
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
        $request_data = array();

        return $request_data;
    }

    /**
     * Process simple request to shipping service server
     *
     * @return string Server response
     */
    public function getSimpleRates()
    {
        $response = $this->getRequestData();

        return $response;
    }

    /**
     * Gets shipping cost and information about possible errors
     *
     * @param string $response
     * @internal param string $resonse Reponse from Shipping service server
     * @return array Shipping cost and errors
     */
    public function processResponse($response)
    {
        $return = array(
            'cost' => false,
            'error' => false,
            'delivery_time' => false,
        );

        $location = $this->_shipping_info['package_info']['location'];
        $service_params = $this->_shipping_info['service_params'];

        $destination_id = fn_get_available_destination($location);

        if (!empty($destination_id)) {
            $condition = db_quote(" AND a.status = ?s AND b.lang_code = ?s", 'A', DESCR_SL);

            $condition .= ' AND (' . fn_find_array_in_set(array($destination_id), 'a.pickup_destinations_ids', true) . ')' ;

            $join = db_quote(" LEFT JOIN ?:store_location_descriptions as b ON a.store_location_id = b.store_location_id");

            $fields = array(
                'a.*',
                'b.*',
            );

            $fields = implode(', ',$fields);

            $_stores = db_get_hash_array("SELECT $fields FROM ?:store_locations as a $join WHERE 1 $condition", 'store_location_id');

            if (!empty($_stores)) {

                $stores = array();

                if (!empty($service_params['active_stores'])) {
                    foreach ($service_params['active_stores'] as $key => $id) {
                        if (isset($_stores[$id])) {
                            $stores[$id] = $_stores[$id];
                            $stores[$id]['shipping_position'] = $key;
                        }
                    }

                    if (!empty($service_params['sorting'])) {
                        $sorting = $service_params['sorting'];
                    } else {
                        $sorting = 'shipping_position';
                    }

                    $stores = fn_sort_array_by_key($stores, $sorting);
                } else {
                    $stores = $_stores;
                }

                if (!empty($stores)) {
                    $this->_fillSessionData($stores);
                } else {
                    $this->_internalError(__('stores_sort_nothing_found'));
                }

            } else {
                $this->_internalError(__('stores_nothing_found'));
            }

            if (empty($this->_error_stack)) {

                $pickup_surcharge = $this->_checkStoreCost($stores);
                $return['cost'] = $pickup_surcharge;
            } else {
                $return['error'] = $this->processErrors($response);
            }
        } else {
            $this->_internalError(__('destination_nothing_found'));
            $return['error'] = $this->processErrors($response);
        }

        return $return;
    }

    private function _fillSessionData($stores)
    {
        $group_key = $this->_shipping_info['keys']['group_key'];
        $shipping_id = $this->_shipping_info['keys']['shipping_id'];

        $_SESSION['cart']['shippings_extra']['data'][$group_key][$shipping_id]['stores'] = $stores;

        return true;
    }

    private function _checkStoreCost($stores)
    {

        $pickup_surcharge = 0;
        $check = false;
        $group_key = $this->_shipping_info['keys']['group_key'];
        $shipping_id = $this->_shipping_info['keys']['shipping_id'];

        if (!empty($_SESSION['cart']['select_store'])) {
            $select_store = $_SESSION['cart']['select_store'];

            if (!empty($select_store[$group_key][$shipping_id])) {
                $store_id = $select_store[$group_key][$shipping_id];
                if (!empty($stores[$store_id]['pickup_surcharge'])) {
                    $pickup_surcharge = $stores[$store_id]['pickup_surcharge'];
                } else {
                    $check = true;
                }

            } elseif (!empty($stores)) {
                $check = true;
            }
        } else {
            $check = true;
        }

        if ($check) {
            $stores = fn_sort_array_by_key($stores, 'pickup_surcharge');
            $first = array_shift($stores);
            $pickup_surcharge = $first['pickup_surcharge'];
            $_SESSION['cart']['select_store'][$group_key][$shipping_id] = $first['store_location_id'];
            $_SESSION['cart']['pickup_surcharge'][$group_key][$shipping_id] = $pickup_surcharge;
        }

        return $pickup_surcharge;
    }
}
