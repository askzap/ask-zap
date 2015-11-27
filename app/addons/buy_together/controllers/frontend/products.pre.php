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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'options') {
    if (!empty($_REQUEST['appearance']['bt_chain'])) {
        $products = array();

        foreach ($_REQUEST['product_data'] as $ids => $options) {
            if (strpos($ids, '_') !== false) {
                list($product_id, $bt_chain_id) = explode('_', $ids);
                $products[$product_id]['selected_options'] = $_REQUEST['product_data'][$ids]['product_options'];
                $products[$product_id]['changed_option'] = $_REQUEST['changed_option'][$ids];

                unset($products[$product_id]['selected_options']['AOC']);
            }
        }

        $params = array(
            'chain_id' => $_REQUEST['appearance']['bt_chain'],
            'status' => 'A',
            'full_info' => true,
            'date' => true,
            'selected_options' => $products,
        );

        $chains = fn_buy_together_get_chains($params, $auth);

        if (!empty($chains)) {
            Registry::get('view')->assign('chains', $chains);
            Registry::get('view')->display('addons/buy_together/blocks/product_tabs/buy_together.tpl');

            exit();
        }
    }

}
