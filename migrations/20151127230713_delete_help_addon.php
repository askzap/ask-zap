<?php

use Phinx\Migration\AbstractMigration;
use Tygh\Registry; if (!defined('AREA')){define('AREA','A');} require_once dirname(dirname(__FILE__)).'/init.php';
class DeleteHelpAddon extends AbstractMigration
{
    public function up()
    {
        fn_uninstall_addon("help_tutorial", false);
        fn_uninstall_addon("rss_feed", false);
        fn_uninstall_addon("customers_also_bought", false);
        fn_uninstall_addon("blog", false);
        fn_uninstall_addon("reward_points", false);

        $addons = array(
            'hidpi' => 'A',
            'watermarks' => 'A',
        );
        foreach ($addons as $addon => $status) {
            db_query("UPDATE ?:addons SET status = ?s WHERE addon = ?s", $status, $addon);
        }
    }

    public function down()
    {


    }
}