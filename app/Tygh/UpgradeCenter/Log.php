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

class Log
{
    /**
     * Instance of Log
     *
     * @var App $instance
     */
    private static $instance;

    /**
     * Current Package identifier
     *
     * @var string $package_id
     */
    private $package_id = '';

    /**
     * Global config
     *
     * @var array $config
     */
    private $config = array();

    public function add($message, $append = true)
    {
        $message = date('Y-m-d H:i:s', TIME) . ': ' . $message;

        $flags = $append ? FILE_APPEND : 0;
        file_put_contents($this->getLogFilePath(), $message . "\n", $flags);

    }

    private function getLogFilePath()
    {
        return $this->config['dir']['root'] . '/var/upgrade/' . $this->package_id . '_log.txt';
    }

    /**
     * Returns instance of Log
     *
     * @return App
     */
    public static function instance($package_id)
    {
        if (empty(self::$instance)) {
            self::$instance = new self($package_id);
        }

        return self::$instance;
    }

    private function __construct($package_id, $config = array())
    {
        $this->package_id = $package_id;

        if (class_exists('\Tygh\Registry')) {
            $this->config = \Tygh\Registry::get('config');
        }

        $this->config = array_merge($this->config, $config);

    }
}
