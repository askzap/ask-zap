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

use Tygh\Development;
use Tygh\Registry;
use Tygh\Settings;
use Tygh\UpgradeCenter\App as UpgradeCenter;
use Tygh\UpgradeCenter\Log;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (in_array($mode, array('upgrade', 'revert'))) {
    // temporary set development mode, for full error displaying
    Development::enable('compile_check');
}

$custom_theme_files = array(
);

$skip_files = array(
    'manifest.json'
);

$backend_files = array(
    'admin_index' => 'admin.php',
    'vendor_index' => 'vendor.php',
);

$uc_settings = Settings::instance()->getValues('Upgrade_center');

// If we're performing the update, check if upgrade center override controller is exist in the package
if (!empty($_SESSION['uc_package']) && file_exists(Registry::get('config.dir.upgrade') . $_SESSION['uc_package'] . '/uc_override.php')) {
    return include(Registry::get('config.dir.upgrade') . $_SESSION['uc_package'] . '/uc_override.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update_settings') {
        if (!empty($_REQUEST['settings_data'])) {
            foreach ($_REQUEST['settings_data'] as $setting_name => $setting_value) {
                Settings::instance()->updateValue($setting_name, $setting_value, 'Upgrade_center');
            }
        }
    }

    if ($mode == 'download') {
        $app = UpgradeCenter::instance();
        $app->downloadPackage($_REQUEST['id']);

        return array(CONTROLLER_STATUS_REDIRECT, 'upgrade_center.manage');

    }

    if ($mode == 'upload') {
        $upgrade_pack = fn_filter_uploaded_data('upgrade_pack', Registry::get('config.allowed_pack_exts'));

        if (empty($upgrade_pack[0])) {
            fn_set_notification('E', __('error'), __('text_allowed_to_upload_file_extension', array('[ext]' => implode(',', Registry::get('config.allowed_pack_exts')))));
        } else {
            $upgrade_pack = $upgrade_pack[0];
            UpgradeCenter::instance()->uploadUpgradePack($upgrade_pack);
        }

        return array(CONTROLLER_STATUS_REDIRECT, 'upgrade_center.manage');

    }

    if ($mode == 'install') {
        if (!empty($_REQUEST['change_ftp_settings'])) {
            Log::instance($_REQUEST['id'])->add('Update FTP connection settings');

            foreach ($_REQUEST['change_ftp_settings'] as $setting_name => $value) {
                Settings::instance()->updateValue($setting_name, $value, '', true);
                Registry::set('settings.Upgrade_center.' . $setting_name, $value);
            }
        }

        list($result, $data) = UpgradeCenter::instance()->install($_REQUEST['id'], $_REQUEST);

        if (!$result) {
            $view = Tygh::$app['view'];

            $view->assign('validation_result', $result);
            $view->assign('validation_data', $data);
            $view->assign('id', str_replace('.', '_', $_REQUEST['id']));
            $view->assign('type', $_REQUEST['type']);
            $view->assign('caption', __('continue'));

            if (defined('AJAX_REQUEST')) {
                Tygh::$app['ajax']->updateRequest();
            }

            $view->display('views/upgrade_center/components/notices.tpl');
            $view->display('views/upgrade_center/components/install_button.tpl');
            exit;
        } else {
            fn_set_notification('N', __('successful'), __('text_uc_upgrade_completed'), 'K');

            if (defined('AJAX_REQUEST')) {
                Tygh::$app['ajax']->assign('non_ajax_notifications', true);
                Tygh::$app['ajax']->assign('force_redirection', fn_url('upgrade_center.manage'));
            }

            return array(CONTROLLER_STATUS_REDIRECT, 'upgrade_center.manage');

        }
    }

    return array(CONTROLLER_STATUS_REDIRECT);
}

if ($mode == 'refresh') {
    $app = UpgradeCenter::instance();

    $app->clearDownloadedPackages();
    $app->checkUpgrades(false, true);

    $upgrade_packages = $app->getPackagesList();
    if (empty($upgrade_packages)) {
        fn_set_notification('N', __('notice'), __('text_no_upgrades_available'));
    }

    return array(CONTROLLER_STATUS_OK, !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "upgrade_center.manage");

} elseif ($mode == 'manage') {
    $app = UpgradeCenter::instance();

    $app->checkUpgrades(false, true);
    $upgrade_packages = $app->getPackagesList();

    Tygh::$app['view']->assign('upgrade_packages', $upgrade_packages);

} elseif ($mode == 'package_content' && !empty($_REQUEST['package_id'])) {
    $package_id = $_REQUEST['package_id'];
    $content = UpgradeCenter::instance()->getPackageContent($package_id);

    Tygh::$app['view']->assign('package_id', $package_id);
    Tygh::$app['view']->assign('content', $content);

} elseif ($mode == 'ftp_settings') {
    Tygh::$app['view']->assign('id', $_REQUEST['package_id']);
    Tygh::$app['view']->assign('type', $_REQUEST['package_type']);
    Tygh::$app['view']->assign('uc_settings', Settings::instance()->getValues('Upgrade_center'));
}
