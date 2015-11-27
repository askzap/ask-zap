<?php

use Phinx\Migration\AbstractMigration;
use Tygh\Registry; if (!defined('AREA')){define('AREA','A');} require_once dirname(dirname(__FILE__)).'/init.php';
class LangVarsUpdater extends AbstractMigration
{
    public function up()
    {
        fn_print_r('Hello world!');


    	die();
    }

    public function down()
    {


    }
}