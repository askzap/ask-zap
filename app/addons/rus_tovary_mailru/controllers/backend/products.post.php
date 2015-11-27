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

// rus_build_mailru dbazhenov

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return array(CONTROLLER_STATUS_OK);
}

if ($mode == 'manage') {

    $selected_fields = Tygh::$app['view']->getTemplateVars('selected_fields');

    $selected_fields[] = array('name' => '[data][mailru_brand]', 'text' => __('mailru_brand'));
    $selected_fields[] = array('name' => '[data][mailru_model]', 'text' => __('mailru_model')); //value
    $selected_fields[] = array('name' => '[data][mailru_type_prefix]', 'text' => __('mailru_type_prefix'));
    if (Registry::get('addons.rus_tovary_mailru.local_delivery_cost') == 'Y') {
        $selected_fields[] = array('name' => '[data][mailru_cost]', 'text' => __('mailru_cost'));
    }
    $selected_fields[] = array('name' => '[data][mailru_delivery]', 'text' => __('mailru_delivery'));
    $selected_fields[] = array('name' => '[data][mailru_pickup]', 'text' => __('mailru_pickup'));
    $selected_fields[] = array('name' => '[data][mailru_mcp]', 'text' => __('mailru_mcp'));
    $selected_fields[] = array('name' => '[data][mailru_export]', 'text' => __('mailru_export'));

    Tygh::$app['view']->assign('selected_fields', $selected_fields);

} elseif ($mode == 'm_update') {

    $selected_fields = $_SESSION['selected_fields'];

    $field_groups = Tygh::$app['view']->getTemplateVars('field_groups');
    $filled_groups = Tygh::$app['view']->getTemplateVars('filled_groups');
    $field_names = Tygh::$app['view']->getTemplateVars('field_names');

    if (!empty($selected_fields['data']['mailru_brand'])) {
        $field_groups['A']['mailru_brand'] = 'products_data';
        $filled_groups['A']['mailru_brand'] = __('mailru_brand');
        unset($field_names['mailru_brand']);
    }

    if (!empty($selected_fields['data']['mailru_model'])) {
        $field_groups['A']['mailru_model'] = 'products_data';
        $filled_groups['A']['mailru_model'] = __('mailru_model');
        unset($field_names['mailru_model']);
    }

    if (!empty($selected_fields['data']['mailru_type_prefix'])) {
        $field_groups['A']['mailru_type_prefix'] = 'products_data';
        $filled_groups['A']['mailru_type_prefix'] = __('mailru_type_prefix');
        unset($field_names['mailru_type_prefix']);
    }

    if (!empty($selected_fields['data']['mailru_cost'])) {
        $field_groups['A']['mailru_cost'] = 'products_data';
        $filled_groups['A']['mailru_cost'] = __('mailru_cost');
        unset($field_names['mailru_cost']);
    }

    if (!empty($selected_fields['data']['mailru_delivery'])) {
        $field_groups['S']['mailru_delivery']['name'] = 'products_data';
        $field_groups['S']['mailru_delivery']['variants'] = array(
            'Y' => 'yes',
            'N' => 'no',
        );
        $filled_groups['S']['mailru_delivery'] = __('mailru_delivery');
        unset($field_names['mailru_delivery']);
    }

    if (!empty($selected_fields['data']['mailru_pickup'])) {
        $field_groups['S']['mailru_pickup']['name'] = 'products_data';
        $field_groups['S']['mailru_pickup']['variants'] = array(
            'Y' => 'yes',
            'N' => 'no',
        );
        $filled_groups['S']['mailru_pickup'] = __('mailru_pickup');
        unset($field_names['mailru_pickup']);
    }

    if (!empty($selected_fields['data']['mailru_mcp'])) {
        $field_groups['A']['mailru_mcp'] = 'products_data';
        $filled_groups['A']['mailru_mcp'] = __('mailru_mcp');
        unset($field_names['mailru_mcp']);
    }

    if (!empty($selected_fields['data']['mailru_export'])) {
        $field_groups['S']['mailru_export']['name'] = 'products_data';
        $field_groups['S']['mailru_export']['variants'] = array(
            'Y' => 'yes',
            'N' => 'no',
        );
        $filled_groups['S']['mailru_export'] = __('mailru_export');
        unset($field_names['mailru_export']);
    }

    Tygh::$app['view']->assign('field_groups', $field_groups);
    Tygh::$app['view']->assign('filled_groups', $filled_groups);
    Tygh::$app['view']->assign('field_names', $field_names);

}
