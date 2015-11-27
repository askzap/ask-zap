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
use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update' && $_REQUEST['addon'] == 'maps_provider' && !empty($_REQUEST['mp_settings'])) {
        $mp_settings = $_REQUEST['mp_settings'];

        fn_mp_update_settings($mp_settings);
        fn_update_addon($_REQUEST['addon_data']);

        return array(CONTROLLER_STATUS_REDIRECT, "addons.manage");
    }
}

if ($mode == 'update') {

    if ($_REQUEST['addon'] == 'maps_provider') {
        Tygh::$app['view']->assign('mp_provider_templates', fn_mp_get_setting_templates());
        Tygh::$app['view']->assign('mp_settings', fn_mp_get_settings());
    }

}

function fn_mp_get_setting_templates()
{
    $templates = array();

    $skin_path = fn_get_theme_path('[themes]/[theme]', 'A');
    $relative_directory_path = 'addons/maps_provider/settings/';
    $template_path =  $skin_path . '/templates/' . $relative_directory_path;
    $_templates = fn_get_dir_contents($template_path, false, true, '.tpl');

    if (!empty($_templates)) {
        $needles = array('settings_', '.tpl');
        $replacements = array('', '');

        foreach ($_templates as $template) {
            if (preg_match('/^settings_/', $template, $m)) {
                $_template = str_replace($needles, $replacements, $template); // Get the provider name
                $templates[$_template] = $relative_directory_path . $template;
            }
        }
    }

    return $templates;
}

function fn_mp_update_settings($mp_settings, $company_id = null)
{
    if (!$setting_id = Settings::instance()->getId('maps_provider_', '')) {
        $setting_id = Settings::instance()->update(array(
            'name' =>           'maps_provider_',
            'section_id' =>     0,
            'section_tab_id' => 0,
            'type' =>           'A', // any not existing type
            'position' =>       0,
            'is_global' =>      'N',
            'handler' =>        ''
        ));
    }

    Settings::instance()->updateValueById($setting_id, serialize($mp_settings), $company_id);
}
