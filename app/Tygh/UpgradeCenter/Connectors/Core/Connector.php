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

namespace Tygh\UpgradeCenter\Connectors\Core;

use Tygh\Registry;
use Tygh\Settings;
use Tygh\UpgradeCenter\Connectors\IConnector as UCInterface;

/**
 * Core upgrade connector interface
 */
class Connector implements UCInterface
{
    /**
     * Upgrade server URL
     *
     * @var string $updates_server
     */
    protected $updates_server = '';

    /**
     * Upgrade center settings
     *
     * @var array $uc_settings
     */
    protected $uc_settings = array();

    /**
     * Prepares request data for request to Upgrade server (Check for the new upgrades)
     *
     * @return array Prepared request information
     */
    public function getConnectionData()
    {
        $request_data = array(
            'method' => 'get',
            'url' => $this->updates_server . '/index.php',
            'data' => array(
                'dispatch' => 'product_updates.get_available',
                'ver' => PRODUCT_VERSION,
                'edition' => PRODUCT_EDITION,
                'build' => PRODUCT_BUILD,
                'license_number' => $this->uc_settings['license_number'],
            ),
            'headers' => array(
                'Content-type: text/xml'
            )
        );

        return $request_data;
    }

    /**
     * Processes the response from the Upgrade server.
     *
     * @param  string $response            server response
     * @param  bool   $show_upgrade_notice internal flag, that allows/disallows Connector displays upgrade notice (A new version of [product] available)
     * @return array  Upgrade package information or empty array if upgrade is not available
     */
    public function processServerResponse($response, $show_upgrade_notice)
    {
        $parsed_data = array();
        $data = simplexml_load_string($response);

        if ($data->packages->item) {
            $parsed_data = array(
                'file' => (string) $data->packages->item->file,
                'name' => (string) $data->packages->item->name,
                'description' => (string) $data->packages->item->description,
                'from_version' => (string) $data->packages->item->from_version,
                'to_version' => (string) $data->packages->item->to_version,
                'timestamp' => (int) $data->packages->item->timestamp,
                'size' => (int) $data->packages->item->size,
                'package_id' => (string) $data->packages->item['id'],
                'md5' => (string) $data->packages->item->file['md5'],
            );

            if ($show_upgrade_notice) {
                fn_set_notification('W', __('notice'), __('text_upgrade_available', array(
                    '[product]' => PRODUCT_NAME,
                    '[link]' => fn_url('upgrade_center.manage')
                )), 'S', 'upgrade_center');
            }
        }

        return $parsed_data;
    }

    /**
     * Downloads upgrade package from the Upgade server
     *
     * @param  array  $schema       Package schema
     * @param  string $package_path Path where the upgrade pack must be saved
     * @return bool   True if upgrade package was successfully downloaded, false otherwise
     */
    public function downloadPackage($schema, $package_path)
    {
        $data = fn_get_contents(Registry::get('config.resources.updates_server') . '/index.php?dispatch=product_updates.get_package&package_id=' . $schema['package_id'] . '&edition=' . PRODUCT_EDITION . '&license_number=' . $this->uc_settings['license_number']);

        if (!empty($data)) {
            fn_put_contents($package_path, $data);

            if (md5_file($package_path) == $schema['md5']) {
                $result = array(true, '');
            } else {
                fn_rm($package_path);

                $result = array(false, __('text_uc_broken_package'));
            }
        } else {
            $result = array(false, __('text_uc_cant_download_package'));
        }

        return $result;
    }

    public function __construct()
    {
        $this->updates_server = Registry::isExist('config.resources.updates_server') ? Registry::get('config.resources.updates_server') : Registry::get('config.updates_server');

        $this->uc_settings = Settings::instance()->getValues('Upgrade_center');
    }
}
