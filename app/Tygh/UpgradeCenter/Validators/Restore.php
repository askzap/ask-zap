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

namespace Tygh\UpgradeCenter\Validators;

use Tygh\Registry;

/**
 * Upgrade validators: Check restore.php writable permissions
 */
class Restore implements IValidator
{
    /**
     * Global App config
     *
     * @var array $config
     */
    protected $config = array();

    /**
     * Validator identifier
     *
     * @var array $name ID
     */
    protected $name = 'restore';

    /**
     * Connection status identifier
     *
     * @var bool $ftp_connection_status. Default null.
     */
    protected $ftp_connection_status = null;

    /**
     * Validate specified data by schema
     *
     * @param  array $schema  Incoming validator schema
     * @param  array $request Request data
     * @return array Validation result and Data to be displayed
     */
    public function check($schema, $request)
    {
        $result = true;
        $data = array();

        $restore_path = $this->config['dir']['root'] . '/var/upgrade/restore.php';

        if (!file_exists($restore_path)) {
            $result = false;
            $data = __('error_exim_file_doesnt_exist') . ': ' . $restore_path;

        } elseif (!is_writable($restore_path)) {
            $result = $this->correctPermissions($restore_path, true);
            if (!$result) {
                $data[] = $restore_path;
            }
        }

        return array($result, $data);
    }

    /**
     * Sets writable permissions to the specified file/dir
     *
     * @param  string $path               Path to file/dir
     * @param  bool   $show_notifications Show FTP connection error notifications
     * @return bool   true if permissions were succesfully changed, false otherwise
     */
    public function correctPermissions($path, $show_notifications)
    {
        $ftp_link = Registry::get('ftp_connection');

        if (empty($ftp_link) && is_null($this->ftp_connection_status)) {
            if (fn_ftp_connect(Registry::get('settings.Upgrade_center'), $show_notifications)) {
                $this->ftp_connection_status = true;
            } else {
                $this->ftp_connection_status = false;
            }
        }

        $correction_result = true;

        if (is_file($path) || is_dir($path)) {
            if (!is_writable($path)) {
                @chmod($path, 0777);
            }
            if (!is_writable($path) && !is_null($this->ftp_connection_status)) {
                fn_ftp_chmod_file($path, 0777);
            }

            if (!is_writable($path)) {
                $correction_result = false;
            }
        }

        return $correction_result;
    }

    /**
     * Gets validator name (ID)
     *
     * @return string Name
     */
    public function getName()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->config = Registry::get('config');
    }
}
