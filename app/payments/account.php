<?php
/****************************************************************************
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

if (defined('PAYMENT_NOTIFICATION')) {

} else {

    if (!empty($processor_data['processor_params']['account_order_status'])) {
        $pp_response = array(
            'order_status' => $processor_data['processor_params']['account_order_status']
        );
    } else {
        $pp_response = array(
            'order_status' => 'O'
        );
    }
}
