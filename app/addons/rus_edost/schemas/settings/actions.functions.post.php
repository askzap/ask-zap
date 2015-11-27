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

// rus_build_edost dbazhenov

use Tygh\Registry;

/**
 * Check if RUB currency is active and used as a primary.
 *
 * @param mixed $new_value New values of shipping_edost_enabled setting
 * @param mixed $old_value Old values of shipping_edost_enabled setting
 */
function fn_settings_actions_shippings_edost_enabled(&$new_value, $old_value)
{
    $currencies = Registry::get('currencies');
    if ($new_value == 'Y' && (empty($currencies[CURRENCY_RUB]) || $currencies[CURRENCY_RUB]['is_primary'] == 'N')) {
        fn_delete_notification('changes_saved');
        fn_set_notification('E', __('warning'), __('edost_activation_error'));
        $new_value = 'N';
    }
}
