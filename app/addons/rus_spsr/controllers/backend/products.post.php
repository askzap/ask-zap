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

use Tygh\Registry;
use Tygh\RusSpsr;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return array(CONTROLLER_STATUS_OK);
}

if ($mode == 'manage') {

    $selected_fields = Tygh::$app['view']->getTemplateVars('selected_fields');
    $selected_fields[] = array('name' => '[data][spsr_product_type]', 'text' => __('shippings.spsr.product_type'));

    Tygh::$app['view']->assign('selected_fields', $selected_fields);

} elseif ($mode == 'm_update') {

    $selected_fields = $_SESSION['selected_fields'];

    $field_groups = Tygh::$app['view']->getTemplateVars('field_groups');
    $filled_groups = Tygh::$app['view']->getTemplateVars('filled_groups');
    $field_names = Tygh::$app['view']->getTemplateVars('field_names');

    if (!empty($selected_fields['data']['spsr_product_type'])) {
        $type_products = array();

        $login = RusSpsr::WALogin();
        if ($login) {
            $type_products = RusSpsr::WAGetEncloseType();
        }
        RusSpsr::WALogout();

        $field_groups['S']['spsr_product_type']['name'] = 'products_data';
        foreach ($type_products as $type) {
            $field_groups['S']['spsr_product_type']['variants'][$type['Value']] = 'shippings.spsr.type' . $type['Value'];
        }

        $filled_groups['S']['spsr_product_type'] = __('shippings.spsr.product_type');
        unset($field_names['spsr_product_type']);
    }

    Tygh::$app['view']->assign('field_groups', $field_groups);
    Tygh::$app['view']->assign('filled_groups', $filled_groups);
    Tygh::$app['view']->assign('field_names', $field_names);

} elseif ($mode == 'update') {
    $type_products = array();

    $login = RusSpsr::WALogin();
    if ($login) {
        $type_products = RusSpsr::WAGetEncloseType();
    }
    RusSpsr::WALogout();

    Tygh::$app['view']->assign('type_products', $type_products);
}
