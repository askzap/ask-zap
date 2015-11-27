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

class Exception extends \Exception
{

    protected $tech_message;

    public function __construct($message = '', $code = 0, $tech_message = '')
    {
        parent::__construct($message, (int) $code);

        $this->tech_message = $tech_message;
    }

    public function getTechMessage()
    {
        return $this->tech_message;
    }
}
