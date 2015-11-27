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

namespace Tygh\UpgradeCenter\Migrations;

use Phinx\Console\PhinxApplication;
use Phinx\Db\Adapter\AdapterFactory;
use Tygh\Registry;

/**
 * Migration class that allows to use Phinx migrations not via console
 */
class Migration
{
    /**
     * Instance of Migrations app
     *
     * @var App $instance
     */
    private static $instance;

    /**
     * Migrations rules
     *
     * @var array $config
     */
    protected $config = array();

    /**
     * Returns instance of Migration class
     *
     * @return Migration app
     */
    public static function instance($params = array())
    {
        if (empty(self::$instance)) {
            self::$instance = new self($params);
        }

        return self::$instance;
    }

    public function __construct($config)
    {
        $this->config = $config;

        Registry::set('config.dir.migrations', $config['migration_dir']);
    }

    public function __call($name, $arguments)
    {
        $config_path = Registry::get('config.dir.root') . '/app/Tygh/UpgradeCenter/Migrations/config.migrations.php';

        switch ($name) {
            case 'migrate':
                $_SERVER['argv'] = array(
                    'phinx',
                    'migrate',
                    '-c' . $config_path,
                    '-edevelopment',
                );
                break;

            case 'rollback':
                $_SERVER['argv'] = array(
                    'phinx',
                    'rollback',
                    '-c' . $config_path,
                    '-edevelopment',
                );
                break;

            default:
                return false;
        }

        $output = new Output;
        $output->setConfig($this->config);

        $app = new PhinxApplication('0.4.3');
        AdapterFactory::instance()->registerAdapter('mysqli', '\Tygh\UpgradeCenter\Phinx\MysqliAdapter');
        $app->setAutoExit(false);
        $app->run(null, $output);
    }
}
