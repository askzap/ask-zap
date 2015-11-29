<?php
use Phinx\Migration\AbstractMigration;
use Tygh\Registry; if (!defined('AREA')){define('AREA','A');} require_once dirname(dirname(__FILE__)).'/init.php';
class LangVarsUpdater extends AbstractMigration
{
    public function up()
    {
        fn_print_r('Hello world!');
		$addons = array('ak_union_web');
        $updater = array();

        foreach (fn_get_translation_languages() as $lang_code => $_data) {
            foreach ($addons as $addon) {
            	$addon_scheme = @simplexml_load_file('app/addons/'.$addon.'/addon.xml', '\\Tygh\\ExSimpleXmlElement', LIBXML_NOCDATA);
                $current_langvars = $addon_scheme->xpath("//language_variables/item[@lang='$lang_code']");
                foreach ($current_langvars as $key => $value) {
                    if(!empty($value) && !empty($value['id']) && !empty($value['lang'])) {
                        $updater[] = array(
                            'value'        =>  (string)$value,
                            'name'         =>  (string)$value['id'],
                            'lang_code'    =>  (string)$value['lang'],
                        );

                        fn_print_r((string)$value['lang'].' --> '.(string)$value['id'].' --> '.(string)$value);
                    }
                }
            }
        }
        
        db_query('REPLACE INTO ?:language_values ?m', $updater);

    	die();
    }

    public function down()
    {


    }
}