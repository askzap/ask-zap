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

namespace Tygh\UpgradeCenter;

use Tygh\UpgradeCenter\Migrations\Migration;
use Tygh\Http;
use Tygh\Registry;
use Tygh\Addons\SchemesManager;
use Tygh\Languages\Languages;
use Tygh\UpgradeCenter\Log;
use Tygh\UpgradeCenter\Output;
use Tygh\Themes\Themes;
use Tygh\DataKeeper;
use Tygh\Mailer;
use Tygh\Settings;

class App
{
    /**
     * Instance of App
     *
     * @var App $instance
     */
    private static $instance;

    /**
     * Available upgrade connectors
     *
     * @var array $_connectors List of connectors
     */
    protected $connectors = array();

    /**
     * Global App config
     *
     * @var array $config
     */
    protected $config = array();

    /**
     * Init params
     *
     * @var array $params
     */
    protected $params = array();

    /**
     * Console mode flag
     *
     * @var bool $is_console
     */
    private $is_console = null;

    /**
     * Gets list of available upgrade packages
     *
     * @return array List of packages
     */
    public function getPackagesList()
    {
        $packages = array();

        $pack_path = $this->getPackagesDir();
        $packages_dirs = fn_get_dir_contents($pack_path);

        if (!empty($packages_dirs)) {
            foreach ($packages_dirs as $package_id) {
                $schema = $this->getSchema($package_id);
                $schema['id'] = $package_id;

                if (!$this->validateSchema($schema)) {
                    continue;
                }

                if (is_file($pack_path . $package_id . '/' . $schema['file'])) {
                    $schema['ready_to_install'] = true;
                } else {
                    $schema['ready_to_install'] = false;
                }

                $packages[$schema['type']][$package_id] = $schema;
            }
        }

        return $packages;
    }

    /**
     * Sets notification to customer
     *
     * @param  string $type    Notification type (E - error, W - warning, N - notice)
     * @param  string $title   Notification title
     * @param  string $message Text of the notification
     * @return bool   true if notification was added to stack or displayed
     */
    public function setNotification($type, $title, $message)
    {
        if ($this->isConsole()) {
            echo "($type) $title: $message" . PHP_EOL;
            $result = true;
        } else {
            $result = fn_set_notification($type, $title, $message);
        }

        return $result;
    }

    /**
     * Checks and download upgrade schemas if available. Shows notification about new upgrades.
     * Uses data from the Upgrade Connectors.
     *
     * @param bool $show_upgrade_notice Flag that determines whether or not the message about new upgrades
     */
    public function checkUpgrades($show_upgrade_notice = true)
    {
        $connectors = $this->getConnectors();

        if (!empty($connectors)) {
            foreach ($connectors as $_id => $connector) {
                $data = $connector->getConnectionData();

                $headers = empty($data['headers']) ? array() : $data['headers'];
                if ($data['method'] == 'post') {
                    Http::mpost($data['url'], $data['data'], array(
                        'callback' => array(array(), $_id, $show_upgrade_notice),
                        'headers' => $headers));
                } else {
                    Http::mget($data['url'], $data['data'], array(
                        'callback' => array(array($this, 'processResponses'), $_id, $show_upgrade_notice),
                        'headers' => $headers));
                }
            }

            Http::processMultiRequest();
        }
    }

    /**
     * Deletes all downloaded packages
     *
     * @return bool true if deleted
     */
    public function clearDownloadedPackages()
    {
        fn_rm($this->getPackagesDir());
        $created = fn_mkdir($this->getPackagesDir());

        return $created;
    }

    /**
     * Processes Upgrade Connectors responses.
     *
     * @param  string $response            Response text from specified upgrade server
     * @param  int    $connector_id        Connector ID from the connectors list
     * @param  bool   $show_upgrade_notice Flag that determines whether or not the message about new upgrades
     * @return mixed  Processing result from the Connector
     */
    public function processResponses($response, $connector_id, $show_upgrade_notice)
    {
        $schema = $this->connectors[$connector_id]->processServerResponse($response, $show_upgrade_notice);

        if (!empty($schema)) {
            $schema['id'] = $connector_id;
            $schema['type'] = $connector_id == 'core' ? 'core' : 'addon';

            if (!$this->validateSchema($schema)) {
                $this->setNotification('E', __('error'), __('uc_broken_upgrade_connector', array('[connector_id]' => $connector_id)));

                return false;
            }

            $pack_path = $this->getPackagesDir() . $connector_id;

            fn_mkdir($pack_path);
            fn_put_contents($pack_path . '/schema.json', json_encode($schema));
        }

        return $schema;
    }

    /**
     * Downloads upgrade package from the Upgade server
     *
     * @param  string $connector_id Connector identifier (core, addon_name, seo, some_addon)
     * @return bool   True if upgrade package was successfully downloaded, false otherwise
     */
    public function downloadPackage($connector_id)
    {
        $connectors = $this->getConnectors();

        if (isset($connectors[$connector_id])) {
            $logger = Log::instance($connector_id);
            $logger->add(str_replace('[package_id]', $connector_id, 'Download \"[package_id]\" upgrade package'));

            $schema = $this->getSchema($connector_id);
            $pack_dir = $this->getPackagesDir() . $connector_id . '/';
            $pack_path = $pack_dir . $schema['file'];

            list($result, $message) = $connectors[$connector_id]->downloadPackage($schema, $pack_path);

            if (!empty($message)) {
                $logger->add($message);
                $this->setNotification('W', __('warning'), $message);
            }

            if ($result) {
                fn_mkdir($pack_dir . 'content');
                fn_decompress_files($pack_path, $pack_dir . 'content/');

                list($result, $message) = $this->checkPackagePermissions($connector_id);

                if ($result) {
                    $logger->add('Upgrade package has been downloaded and ready to install');

                    $this->setNotification('N', __('notice'), __('uc_downloaded_and_ready'));
                } else {
                    fn_rm($pack_dir . 'content');
                    fn_rm($pack_path);

                    $this->setNotification('E', __('error'), $message);

                    $logger->add($message);
                }
            }

            return $result;

        } else {
            $this->setNotification('E', __('error'), __('uc_connector_not_found'));

            return false;
        }
    }

    /**
     * Gets extra validators from Upgrade package
     *
     * @param  string $package_id Package id like "core", "access_restrictions"
     * @param  array  $schema     Package schema
     * @return array  Instances of the extra validators
     */
    public function getPackageValidators($package_id, $schema)
    {
        $validators = array();

        if (!empty($schema['validators'])) {
            $validators_path = $this->getPackagesDir() . $package_id . '/content/validators/';

            foreach ($schema['validators'] as $validator_name) {
                if (file_exists($validators_path . $validator_name . '.php')) {
                    include_once $validators_path . $validator_name . '.php';

                    $class_name = "\\Tygh\\UpgradeCenter\\Validators\\" . $validator_name;
                    if (class_exists($class_name)) {
                        $validators[] = new $class_name();
                    }
                }
            }
        }

        return $validators;
    }

    /**
     * Gets list of the files to be updated with the hash checking statuses
     * @param  string $package_id Package id like "core", "access_restrictions"
     * @return array  List of files
     */
    public function getPackageContent($package_id)
    {
        $schema = $this->getSchema($package_id, true);

        if (!empty($schema['files'])) {
            foreach ($schema['files'] as $path => $file_data) {
                $original_path = $this->config['dir']['root'] . '/' . $path;

                switch ($file_data['status']) {
                    case 'changed':
                        if (!file_exists($original_path) || (file_exists($original_path) && md5_file($original_path) != $file_data['hash'])) {
                            $schema['files'][$path]['collision'] = true;
                        }

                        break;

                    case 'deleted':
                        if (file_exists($original_path) && md5_file($original_path) != $file_data['hash']) {
                            $schema['files'][$path]['collision'] = true;
                        }
                        break;

                    case 'new':
                        if (file_exists($original_path)) {
                            $schema['files'][$path]['collision'] = true;
                        }
                        break;
                }
            }
        }

        return $schema;
    }

    /**
     * Validates and installs package
     *
     * @todo Implement language installer
     * @todo Additional migrations validation
     *
     * @param string $package_id Package id like "core", "access_restrictions", etc
     * @return array($result, $data) Installation result
     */
    public function install($package_id, $request)
    {
        $result = true;

        $logger = Log::instance($package_id);

        $logger->add('');
        $logger->add(str_replace('[package_id]', $package_id, 'Start installation of the "[package_id]" upgrade package'));
        $logger->add('================================================');

        $logger->add('Get all available validators');

        Output::steps(5); // Validators, Backups (database/files), Copying Files, Migrations, Languages
        Output::display(__('uc_title_validators'), __('uc_upgrade_progress'), false);

        $validators = $this->getValidators();
        $schema = $this->getSchema($package_id, true);
        $information_schema = $this->getSchema($package_id, false);

        $package_validators = $this->getPackageValidators($package_id, $schema);
        if (!empty($package_validators)) {
            $validators = array_merge($package_validators, $validators);
        }

        foreach ($validators as $validator) {
            $logger->add(str_replace('[validator]', $validator->getName(), 'Execute "[validator]" validator'));
            Output::display(__('uc_execute_validator', array('[validator]' => $validator->getName())), '', false);

            list($result, $data) = $validator->check($schema, $request);
            if (!$result) {
                break;
            }
        }

        if (!$result) {
            $logger->add('Upgrade stopped: Awaiting resolving validation errors: ' . $validator->getName());

            return array($result, array($validator->getName() => $data));
        } else {
            $backup_filename
                = "upg_{$package_id}_{$information_schema['from_version']}-{$information_schema['to_version']}_" .
                  date('dMY_His', TIME);
            // Prepare restore.php file. Paste necessary data and access information
            $restore_key = $this->prepareRestore($backup_filename . '.zip');
            if (empty($restore_key)) {
                $logger->add('Upgrade stopped: Unable to prepare restore file. restore.php was locally modified/removed or renamed.');

                return array(false, array(__('restore') => __('upgrade_center.unable_to_prepare_restore')));
            }

            $content_path = $this->getPackagesDir() . $package_id . '/content/';

            // Run pre script
            if (!empty($schema['scripts']['pre'])) {
                include_once($content_path . 'scripts/' . $schema['scripts']['pre']);
            }

            $this->closeStore();

            $logger->add('Backup files and Database');
            Output::display(__('backup_data'), '', true);

            $backup_file = DataKeeper::backup(array(
                'pack_name' => $backup_filename,
                'compress' => 'zip',
                'set_comet_steps' => false,
                'move_progress' => false,
            ));
            if (empty($backup_file) || !file_exists($backup_file)) {
                $logger->add('Upgrade stopped: Failed to backup DB/Files');

                return array(false, array(__('backup') => __('text_uc_failed_to_backup_tables')));
            }

            // Send mail to admin e-mail with information about backup
            Mailer::sendMail(array(
                'to' => 'company_site_administrator',
                'from' => 'default_company_site_administrator',
                'data' => array(
                    'backup_file' => $backup_file,
                    'settings_section_url' => fn_url('settings.manage'),
                    'restore_link' => Registry::get('config.http_location') . '/var/upgrade/restore.php?uak=' . $restore_key,
                ),
                'tpl' => 'upgrade/backup_info.tpl',
            ), 'A', Registry::get('settings.Appearance.backend_default_language'));

            $logger->add('Copy package files');
            Output::display(__('uc_copy_files'), '', true);

            // Move files from package
            $this->applyPackageFiles($content_path . 'package', $this->config['dir']['root']);
            $this->cleanupOldFiles($schema, $this->config['dir']['root']);

            // Copy files from themes_repository to design folder
            $this->processThemesFiles($schema);

            Output::display(__('uc_run_migrations'), '', true);
            // Run migrations
            if (!empty($schema['migrations'])) {
                $logger->add('Run migrations');

                $minimal_date = 0;

                foreach ($schema['migrations'] as $migration) {
                    preg_match('/^[0-9]+/', $migration, $matches);

                    if (!empty($matches[0])) {
                        $date = $matches[0];
                        if ($date < $minimal_date || empty($minimal_date)) {
                            $minimal_date = $date;
                        }
                    }
                }

                $config = array(
                    'migration_dir' => $content_path . 'migrations/',
                    'package_id' => $package_id,
                );

                Migration::instance($config)->migrate($minimal_date);
            }

            // Install languages
            Output::display(__('uc_install_languages'), '', true);

            if (!empty($schema['languages'])) {
                $logger->add('Install langauges from the upgrade package');

                $avail_languages = Languages::getAvailable('A', true);

                foreach ($avail_languages as $lang_code => $language) {
                    if (in_array($lang_code, $schema['languages'])) {
                        $logger->add(str_replace('[lang_code]', $lang_code, 'Install the \"[lang_code]\" language'));
                        Output::display(__('install') . ': ' . $lang_code, '', false);

                        Languages::installCrowdinPack($content_path . 'languages/' . $lang_code, array(
                            'install_newly_added' => true,
                            'validate_lang_code' => true,
                            'reinstall' => true,
                        ));
                    } else {
                        $pack_code = '';
                        if (in_array(CART_LANGUAGE, $schema['languages'])) {
                            $pack_code = CART_LANGUAGE;
                        } elseif (in_array('en', $schema['languages'])) {
                            $pack_code = 'en';
                        }

                        if (file_exists($content_path . 'languages/' . $pack_code)) {
                            // Fill the unknown language by the Default/EN language variables
                            Languages::installCrowdinPack($content_path . 'languages/' . $pack_code, array(
                                'reinstall' => true,
                                'force_lang_code' => $lang_code,
                                'install_newly_added' => true
                            ));
                        }
                    }
                }
            }
        }

        // Run post script
        if (!empty($schema['scripts']['post'])) {
            include_once($content_path . 'scripts/' . $schema['scripts']['post']);
        }

        Output::display(__('text_uc_upgrade_completed'), '', true);
        $logger->add('Upgrade completed');

        $this->deletePackage($package_id);

        // Clear obsolete files
        fn_clear_cache();
        fn_rm(Registry::get('config.dir.cache_templates'));

        return array(true, array());
    }

    /**
     * Deletes schema and package content of the upgrade package
     *
     * @param  string $package_id Package identifier
     * @return bool   true if deleted
     */
    public function deletePackage($package_id)
    {
        $pack_dir = $this->getPackagesDir() . $package_id . '/';

        return fn_rm($pack_dir);
    }

    /**
     * Unpacks and checks the uploaded upgrade pack
     *
     * @param  string $path Path to the zip/tgz archive with the upgrade
     * @return true   if upgrade pack is ready to use, false otherwise
     */
    public function uploadUpgradePack($pack_info)
    {
        // Extract the add-on pack and check the permissions
        $extract_path = fn_get_cache_path(false) . 'tmp/upgrade_pack/';
        $destination = $this->getPackagesDir();

        // Re-create source folder
        fn_rm($extract_path);
        fn_mkdir($extract_path);

        fn_copy($pack_info['path'], $extract_path . $pack_info['name']);

        if (fn_decompress_files($extract_path . $pack_info['name'], $extract_path)) {
            if (file_exists($extract_path . 'schema.json')) {
                $schema = json_decode(fn_get_contents($extract_path . 'schema.json'), true);

                if ($this->validateSchema($schema)) {
                    $package_id = preg_replace('/\.(zip|tgz|gz)$/i', '', $pack_info['name']);

                    $this->deletePackage($package_id);
                    fn_mkdir($destination . $package_id);

                    fn_copy($extract_path, $destination . $package_id);
                    list($result, $message) = $this->checkPackagePermissions($package_id);

                    if ($result) {
                        $this->setNotification('N', __('notice'), __('uc_downloaded_and_ready'));
                    } else {
                        $this->setNotification('E', __('error'), $message);
                        $this->deletePackage($package_id);
                    }

                } else {
                    $this->setNotification('E', __('error'), __('uc_broken_upgrade_connector', array('[connector_id]' => $pack_info['name'])));
                }
            } else {
                $this->setNotification('E', __('error'), __('uc_unable_to_read_schema'));
            }
        }

        // Clear obsolete unpacked data
        fn_rm($extract_path);

        return false;
    }

    /**
     * Prepares restore.php file.
     *
     * @return bool if all necessary information was added to restore.php
     */
    protected function prepareRestore($backup_filename)
    {
        $restore_path = $this->config['dir']['root'] . '/var/upgrade/restore.php';

        $content = fn_get_contents($restore_path);

        $uc_settings = Settings::instance()->getValues('Upgrade_center');

        $data = "\$uc_settings = " . var_export($uc_settings, true) . ";\n\n";
        $data .= "\$config = " . var_export(Registry::get('config'), true) . ";\n\n";
        $data .= "\$backup_filename = '" . $backup_filename . "';\n\n";
        $restore_key = md5(uniqid()) . md5(uniqid('', true));
        $data .= "\$uak = '" . $restore_key . "';";

        $replaced = 0;
        $content = preg_replace('#\/\/\[params\].*?\/\/\[\/params\]#ims', "//[params]\n" . $data . "\n\n//[/params]", $content, -1, $replaced);

        if (!$replaced || !fn_put_contents($restore_path, $content)) {
            return false;
        }

        // Check if restore is available through the HTTP
        $result = Http::get(Registry::get('config.http_location') . '/var/upgrade/restore.php');
        if ($result != 'Access denied') {
            return false;
        }

        return $restore_key;
    }

    /**
     * Gets list of the available Upgrade Validators
     * @todo Extends by add-ons
     *
     * @return array List of validator objects
     */
    protected function getValidators()
    {
        $validators = array();
        $validator_names = fn_get_dir_contents($this->config['dir']['root'] . '/app/Tygh/UpgradeCenter/Validators/', false, true);

        foreach ($validator_names as $validator) {
            $validator_class = "\\Tygh\\UpgradeCenter\\Validators\\" . fn_camelize(basename($validator, '.php'));

            if (class_exists($validator_class)) {
                $validators[] = new $validator_class;
            }
        }

        return $validators;
    }

    /**
     * Gets list of the available Upgrade Connectors
     *
     * @return array List of connector objects
     */
    protected function getConnectors()
    {
        if (empty($this->connectors)) {
            $connector = new Connectors\Core\Connector();
            $this->connectors['core'] = $connector;

            // Extend connectors by addons
            $addons = Registry::get('addons');

            foreach ($addons as $addon_name => $settings) {
                $class_name =  "\\Tygh\\UpgradeCenter\\Connectors\\" . fn_camelize($addon_name) . "\\Connector";
                $connector = class_exists($class_name) ? new $class_name() : null;

                if (!is_null($connector)) {
                    $this->connectors[$addon_name] = $connector;
                }
            }
        }

        return $this->connectors;
    }

    /**
     * Gets JSON schema of upgrade package as array
     *
     * @param  string $package_id Package id like "core", "access_restrictions"
     * @return array  Schema data. Empty if schema is not available
     */
    protected function getSchema($package_id, $for_content = false)
    {
        $schema = array();
        if ($for_content) {
            $schema_path = 'content/package.json';
        } else {
            $schema_path = 'schema.json';
        }

        $pack_path = $this->getPackagesDir() . $package_id . '/' . $schema_path;

        if (file_exists($pack_path)) {
            $schema = json_decode(fn_get_contents($pack_path), true);
            $schema['type'] = empty($schema['type']) ? 'hotfix' : $schema['type'];
        }

        return $schema;
    }

    /**
     * Checks if package has rights to update files and if all files were mentioned in the package.json schema
     * @todo Bad codestyle: Multi returns.
     *
     * @param  string $package_id Package id like "core", "access_restrictions", etc
     * @return bool   true if package is correct, false otherwise
     */
    protected function checkPackagePermissions($package_id)
    {
        $content_path = $this->getPackagesDir() . $package_id . '/content/';
        $schema = $this->getSchema($package_id);

        if (empty($schema)) {
            return array(false, __('uc_unable_to_read_schema'));
        }

        if (!file_exists($content_path .'package.json')) {
            return array(false, __('uc_package_schema_not_found'));
        }

        $package_schema = $this->getSchema($package_id, true);

        if (empty($package_schema)) {
            return array(false, __('uc_package_schema_is_not_json'));
        }

        if ($schema['type'] == 'addon') {
            $valid_paths = array(
                'app/addons/' . $package_id,
                'js/addons/' . $package_id,
                'images/',

                'design/backend/css/addons/' . $package_id,
                'design/backend/mail/templates/addons/' . $package_id,
                'design/backend/media/fonts/addons/' . $package_id,
                'design/backend/media/images/addons/' . $package_id,
                'design/backend/templates/addons/' . $package_id,

                'var/themes_repository/[^/]+/css/addons/' . $package_id,
                'var/themes_repository/[^/]+/mail/media/',
                'var/themes_repository/[^/]+/mail/templates/addons/' . $package_id,
                'var/themes_repository/[^/]+/media/fonts/',
                'var/themes_repository/[^/]+/media/images/addons/' . $package_id,
                'var/themes_repository/[^/]+/media/images/addons/' . $package_id,
                'var/themes_repository/[^/]+/styles/data/',
                'var/themes_repository/[^/]+/templates/addons/' . $package_id,

                'var/langs/',
            );

            if (!empty($package_schema['files'])) {
                foreach ($package_schema['files'] as $path => $data) {
                    $valid = false;

                    foreach ($valid_paths as $valid_path) {
                        if (preg_match('#^' . $valid_path . '#', $path)) {
                            $valid = true;
                            break;
                        }
                    }

                    if (!$valid) {
                        return array(false, __('uc_addon_package_forbidden_path', array('[path]' => $path)));
                    }
                }
            }
        }

        // Check migrations
        $migrations = fn_get_dir_contents($content_path . 'migrations/', false, true, '' , '', true);
        $schema_migrations = empty($package_schema['migrations']) ? array() : $package_schema['migrations'];

        if (count($migrations) != count($schema_migrations) || array_diff($migrations, $schema_migrations)) {
            return array(false, __('uc_addon_package_migrations_forbidden'));
        }

        // Check languages
        $languages = fn_get_dir_contents($content_path . 'languages/', true);
        $schema_languages = empty($package_schema['languages']) ? array() : $package_schema['languages'];

        if (count($languages) != count($schema_languages) || array_diff($languages, $schema_languages)) {
            return array(false, __('uc_addon_package_languages_forbidden'));
        }

        // Check files
        $files = array_flip(fn_get_dir_contents($content_path . 'package/', false, true, '' , '', true));
        $schema_files = empty($package_schema['files']) ? array() : $package_schema['files'];

        $diff = array_diff_key($schema_files, $files);
        foreach ($diff as $file) {
            if (!empty($file['status']) && $file['status'] == 'deleted') {
                continue;
            } else {
                return array(false, __('uc_addon_package_files_do_not_match_schema'));
            }
        }

        // Check pre/post scripts
        if (!empty($package_schema['scripts'])) {
            $scripts = fn_get_dir_contents($content_path . 'scripts/', false, true);
            $schema_scripts = array();
            if (!empty($package_schema['scripts']['pre'])) {
                $schema_scripts[] = $package_schema['scripts']['pre'];
            }
            if (!empty($package_schema['scripts']['post'])) {
                $schema_scripts[] = $package_schema['scripts']['post'];
            }

            if (count($scripts) != count($schema_scripts) || array_diff($scripts, $schema_scripts)) {
                return array(false, __('uc_addon_package_pre_post_scripts_mismatch'));
            }
        }

        return array(true, '');
    }

    /**
     * Validates schema to check if upgrade pack can be applied
     *
     * @param  array $schema Pack schema data
     * @return bool  true if valid, false otherwise
     */
    protected function validateSchema($schema)
    {
        $is_valid = true;

        $required_fields = array(
            'file',
            'name',
            'description',
            'from_version',
            'to_version',
            'timestamp',
            'size',
            'type'
        );

        foreach ($required_fields as $field) {
            if (empty($schema[$field])) {
                $is_valid = false;
            }
        }

        if ($is_valid) {
            switch ($schema['type']) {
                case 'core':
                case 'hotfix':
                    if ($schema['from_version'] != PRODUCT_VERSION) {
                        $is_valid = false;
                    }
                    break;

                case 'addon':
                    $addon_scheme = SchemesManager::getScheme($schema['id']);

                    if (!empty($addon_scheme)) {
                        $addon_version = $addon_scheme->getVersion();
                    } else {
                        $is_valid = false;
                        break;
                    }

                    if ($schema['from_version'] != $addon_version) {
                        $is_valid = false;
                    }
                    break;
            }
        }

        return $is_valid;
    }

    /**
     * Copies package files to the core
     * @todo Make console coping
     *
     * @param  string $from Source direcory with files
     * @param  string $to   Destination directory
     * @return bool   true if copied, false otherwise
     */
    protected function applyPackageFiles($from, $to)
    {
        if (is_dir($from)) {
            $result = fn_copy($from, $to);
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Cleanups old files mentioned in upgrade schema
     *
     * @param array $schema Upgrade package schema
     */
    protected function cleanupOldFiles($schema)
    {
        foreach ($schema['files'] as $file_path => $file) {
            if ($file['status'] == 'deleted') {
                fn_rm($this->config['dir']['root'] . '/' . $file_path);
            }
        }
    }

    /**
     * Copies theme files from the theme_repository to design folder
     *
     * @param  array $schema UC package schema
     * @return array List of processed files
     */
    protected function processThemesFiles($schema)
    {
        if (empty($schema['files'])) {
            return array();
        }

        $repo_files = array();
        $repo_path = str_replace($this->config['dir']['root'] . '/', '', $this->config['dir']['themes_repository']);

        // Process themes_repository
        foreach ($schema['files'] as $file_path => $file_data) {
            if (strpos($file_path, $repo_path) !== false) {
                $path = str_replace($repo_path, '', $file_path);
                $path = explode('/', $path);

                $theme_name = array_shift($path);

                $repo_files[$theme_name][] = implode('/', $path);
            }
        }

        $themes = fn_get_dir_contents($this->config['dir']['root'] . '/design/themes/');
        foreach ($themes as $theme_name) {
            $manifest = Themes::factory($theme_name)->getManifest();
            $parent_theme = empty($manifest['parent_theme']) ? 'basic' : $manifest['parent_theme'];

            if (!empty($repo_files[$parent_theme])) {
                foreach ($repo_files[$parent_theme] as $file_path) {
                    // Check if we need to create folders path before copying
                    $dir_path = dirname($this->config['dir']['root'] . '/design/themes/' . $theme_name . '/' . $file_path);
                    fn_mkdir($dir_path);

                    fn_copy($this->config['dir']['themes_repository'] . $parent_theme . '/' . $file_path, $this->config['dir']['root'] . '/design/themes/' . $theme_name . '/' . $file_path);
                }
            }
        }

        return $repo_files;
    }

    /**
     * Gets full path to the packages dir
     * @return string /full/path/to/packages/dir
     */
    protected function getPackagesDir()
    {
        return $this->config['dir']['upgrade'] . 'packages/';
    }

    /**
     * Closes storefront
     */
    protected function closeStore()
    {
        fn_set_store_mode('closed');
        if (fn_allowed_for('ULTIMATE')) {
            $company_ids = fn_get_all_companies_ids();
            foreach ($company_ids as $company_id) {
                fn_set_store_mode('closed', $company_id);
            }
        }
    }

    /**
     * Checks if script run from the console
     *
     * @return bool true if run from console
     */
    protected function isConsole()
    {
        if (is_null($this->is_console)) {
            if (defined('CONSOLE')) {
                $this->is_console = true;
            } else {
                $this->is_console = false;
            }
        }

        return $this->is_console;
    }

    /**
     * Returns instance of App
     *
     * @return App
     */
    public static function instance($params = array())
    {
        if (empty(self::$instance)) {
            self::$instance = new self($params);
        }

        return self::$instance;
    }

    public function __construct($params)
    {
        $this->config = Registry::get('config');
        $this->params = $params;
    }
}
