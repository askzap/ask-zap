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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $return_url = !empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : '';

    if ($mode == 'request') {

        if (fn_image_verification('call_request', $_REQUEST) == false) {
            fn_save_post_data('call_data');

        } elseif (!empty($_REQUEST['call_data'])) {

            $product_data = !empty($_REQUEST['product_data']) ? $_REQUEST['product_data'] : array();

            if ($res = fn_do_call_request($_REQUEST['call_data'], $product_data, $_SESSION['cart'], $_SESSION['auth'])) {
                if (!empty($res['error'])) {
                    fn_set_notification('E', __('error'), $res['error']);
                } elseif (!empty($res['notice'])) {
                    fn_set_notification('N', __('notice'), $res['notice']);
                }
            }

        }

    }

    return array(CONTROLLER_STATUS_OK, $return_url);
}
