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


/**
 * RussianPost shipping service
 */
class RussianPost implements IService
{
    private $_module = NULL;

    public function prepareData($shipping_info)
    {
        $this->_shipping_info = $shipping_info;

        $module = fn_camelize($shipping_info['service_code']);

        if ($module == 'RussianPost') {
            $module = 'RussianPostOfficial';
        }

        $module = 'Tygh\\Shippings\\Services\\' . $module;

        if (class_exists($module)) {
            $module_obj = new $module;

            $module_obj->prepareData($shipping_info);

            $this->_module = $module_obj;
        }
    }

    public function processResponse($response)
    {
        return $this->_module->processResponse($response);
    }

    public function processErrors($response)
    {
        return $this->_module->processErrors($response);
    }

    public function allowMultithreading()
    {
        return $this->_module->allowMultithreading();
    }

    public function getRequestData()
    {
        return $this->_module->getRequestData();
    }

    public function getSimpleRates()
    {
        return $this->_module->getSimpleRates();
    }
}
