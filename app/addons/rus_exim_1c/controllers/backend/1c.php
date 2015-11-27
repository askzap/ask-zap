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

use Tygh\Registry;
use Tygh\BlockManager\SchemesManager;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $suffix = '';
    if ($mode == 'save_offers_data') {
        if (Registry::get('addons.rus_exim_1c.exim_1c_create_prices') == 'Y') {
            $prices = $_REQUEST['prices_1c'];
            if (!empty($_REQUEST['list_price_1c'])) {
                $_list_prices = fn_explode(',', $_REQUEST['list_price_1c']);
                $list_prices = array();
                foreach($_list_prices as $_list_price) {
                    $list_prices[] = array(
                        'price_1c' => trim($_list_price),
                        'usergroup_id' => 0,
                        'type' => 'list',
                    );
                }        
                $prices = fn_array_merge($list_prices, $prices, false);
            }
            if (!empty($_REQUEST['base_price_1c'])) {
                $_base_prices = fn_explode(',', $_REQUEST['base_price_1c']);
                $base_prices = array();
                foreach($_base_prices as $_base_price) {
                    $base_prices[] = array(
                        'price_1c' => trim($_base_price),
                        'usergroup_id' => 0,
                        'type' => 'base',
                    );
                }
                $prices = fn_array_merge($base_prices, $prices, false);
                db_query("DELETE FROM ?:rus_exim_1c_prices");
                foreach ($prices as $price) {
                    if (!empty($price['price_1c'])) {
                        db_query("INSERT INTO ?:rus_exim_1c_prices ?e", $price);
                    }
                }
            } else {
                fn_set_notification('W', __('warning'), __('base_price_empty'));
            }
        }
        if (Registry::get('addons.rus_exim_1c.exim_1c_add_tax') == 'Y') {
            $taxes_1c = $_REQUEST['taxes_1c'];
            db_query("DELETE FROM ?:rus_exim_1c_taxes");
            foreach ($taxes_1c as $tax_1c) {
                if (!empty($tax_1c['tax_1c'])) {
                    db_query("INSERT INTO ?:rus_exim_1c_taxes ?e", $tax_1c);
                }
            }
        }
        
        $suffix = '.offers';
    }
    
    return array(CONTROLLER_STATUS_OK, "1c$suffix");
}

if ($mode == 'offers') {
    $cml = fn_get_cml_tag_names();
    if (Registry::get('addons.rus_exim_1c.exim_1c_create_prices') == 'Y') {
        $prices_data = db_get_array("SELECT * FROM ?:rus_exim_1c_prices");
        $prices = array();
        $list_price_1c = $base_price_1c = '';
        foreach ($prices_data as $price) {
            if ($price['type'] == 'base') {
                $base_price_1c.= $price['price_1c'].',';
            } elseif ($price['type'] == 'list') {
                $list_price_1c.= $price['price_1c'].',';
            } else {
                $prices[] = $price;
            }
        }

        Tygh::$app['view']->assign('list_price_1c', trim($list_price_1c, ','));
        Tygh::$app['view']->assign('base_price_1c', trim($base_price_1c, ','));
        Tygh::$app['view']->assign('prices_data', $prices);
        
        if (Registry::get('addons.rus_exim_1c.exim_1c_check_prices') == 'Y') {  
            list($dir_1c, $dir_1c_url, $dir_1c_images) = fn_rus_exim_1c_get_dir_1c();        
            $result = array();
            $file_offers = glob($dir_1c . "offers*");
            if (!empty($file_offers)) {
                $filename = fn_basename($file_offers[0]);
                $xml = @simplexml_load_file($dir_1c . $filename);
                if (isset($xml->$cml['packages'])) {
                    $result = fn_exim_1c_check_prices($xml->$cml['packages']);
                }
            } else {
                fn_set_notification('W', __('warning'), __('offers_not_found'));
            } 
            
            Tygh::$app['view']->assign('resul_test', $result);
        }
    }
    
    if (Registry::get('addons.rus_exim_1c.exim_1c_add_tax') == 'Y') {
        $taxes = fn_get_taxes();
        $taxes_data = db_get_array("SELECT * FROM ?:rus_exim_1c_taxes");
        
        Tygh::$app['view']->assign('taxes_data', $taxes_data);
        Tygh::$app['view']->assign('taxes', $taxes);
    }
}