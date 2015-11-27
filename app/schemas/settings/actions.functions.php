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

use Tygh\Helpdesk;
use Tygh\Http;
use Tygh\Registry;
use Tygh\Settings;

/**
 * Check if secure connection is available
 */
function fn_settings_actions_security_secure_storefront(&$new_value, $old_value)
{
    if ($new_value !== 'none') {
        $company_id = fn_get_runtime_company_id();

        if (!fn_allowed_for('ULTIMATE') || (fn_allowed_for('ULTIMATE') && $company_id)) {

            $suffix = '';
            if (fn_allowed_for('ULTIMATE')) {
                $suffix = '&company_id=' . $company_id;
            }

            $storefront_url = fn_url('index.index?check_https=Y' . $suffix, 'C', 'https');

            $content = Http::get($storefront_url);
            if (empty($content) || $content != 'OK') {
                // Disable https
                Settings::instance()->updateValue('secure_storefront', 'none', 'Security');
                $new_value = 'none';

                fn_set_notification('W', __('warning'), __('warning_https_disabled'));
            }
        }
    }
}

/**
 * Check if secure connection is available
 */
function fn_settings_actions_security_secure_admin(&$new_value, $old_value)
{
    if ($new_value !== 'N') {
        $suffix = '';
        if (fn_allowed_for('ULTIMATE')) {
            $suffix = '&company_id=' . Registry::get('runtime.company_id');
        }

        $admin_url = fn_url('index.index?check_https=Y' . $suffix, 'A', 'https');

        $content = Http::get($admin_url);

        if (empty($content) || $content != 'OK') {
            // Disable https
            Settings::instance()->updateValue('secure_admin', 'N', 'Security');
            $new_value = 'N';

            fn_set_notification('W', __('warning'), __('warning_https_disabled'));
        }
    }
}

/**
 * Alter order initial ID
 */
function fn_settings_actions_general_order_start_id(&$new_value, $old_value)
{
    if (intval($new_value)) {
        db_query("ALTER TABLE ?:orders AUTO_INCREMENT = ?i", $new_value);
    }
}

/**
 * Save empty value if has no checked check boxes
 */
function fn_settings_actions_general_search_objects(&$new_value, $old_value)
{
    if ($new_value == 'N') {
        $new_value = '';
    }
}

function fn_settings_actions_upgrade_center_license_number(&$new_value, &$old_value)
{
    if (empty($new_value)) {
        $new_value = $old_value;

        fn_set_notification('E', __('error'), __('license_number_cannot_be_empty'));

        return false;
    }

    $mode = fn_get_storage_data('store_mode');

    $data = Helpdesk::getLicenseInformation($new_value);
    list($license_status, $updates, $messages) = Helpdesk::parseLicenseInformation($data, $_SESSION['auth'], true);

    if ($license_status == 'ACTIVE' && ($mode != 'full' || empty($old_value))) {
        fn_set_storage_data('store_mode', 'full');
        $_SESSION['mode_recheck'] = true;
    } else {
        if ($license_status != 'ACTIVE') {
            $new_value = $old_value;
        }
    }
}

function fn_settings_actions_appearance_backend_default_language(&$new_value, &$old_value)
{
    if (fn_allowed_for('ULTIMATE')) {
        db_query("UPDATE ?:companies SET lang_code = ?s", $new_value);
    }
}

if (fn_allowed_for('ULTIMATE')) {
    function fn_settings_actions_stores_share_users(&$new_value, $old_value)
    {
        $emails = fn_get_double_user_emails();
        if (!empty($emails)) {
            fn_delete_notification('changes_saved');
            fn_set_notification('E', __('error'), __('ult_share_users_setting_disabled'));
            $new_value = $old_value;
        }
    }
}

function fn_settings_actions_appearance_notice_displaying_time(&$new_value, $old_value)
{
    $new_value = fn_convert_to_numeric($new_value);
}
