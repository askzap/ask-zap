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

// rus_build_pack dbazhenov

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {
    if ($mode == 'notify') {
        if (isset($_REQUEST['hash']) && isset($_REQUEST['type'])) {
            $processor_data = fn_get_processor_data_by_name('vsevcredit.php');
            $payment_data = fn_get_payment_by_processor($processor_data['processor_id']);
            $payment_data = reset($payment_data);
            $payment_params = fn_get_payment_method_data($payment_data['payment_id']);
            $secret_word = $payment_params['processor_params']['vvc_secret'];

            if ($_REQUEST['type'] == 'user') {
                if (md5($_REQUEST['user_code'] . $_REQUEST['email'] . $secret_word) === $_REQUEST['hash']) {
                    //For the anonymous checkout
                    die(0);
                }
            } elseif ($_REQUEST['type'] == 'order_status') {
                if (isset($_REQUEST['order_id']) && isset($_REQUEST['type']) && $_REQUEST['type'] != 'user') {
                    $order_id = (int) $_REQUEST['order_id'];
                    $order_info = fn_get_order_info($order_id);
                } else {
                    die('Access denied');
                }

                if (md5($_REQUEST['user_id'] . $order_id . $_REQUEST['vvc_order_id'] . $_REQUEST['vvc_status'] . $secret_word) === $_REQUEST['hash'] && $order_info['payment_info']['awaiting_callback'] == true) {
                    if ($_REQUEST['vvc_status'] == 'OK') {
                        $pp_response['order_status'] = 'P';
                    } elseif ($_REQUEST['vvc_status'] == 'CANCEL') {
                        $pp_response['order_status'] = 'F';
                    }

                    if (fn_check_payment_script('vsevcredit.php', $order_id)) {
                        fn_finish_payment($order_id, $pp_response);
                        fn_update_order_payment_info($order_id, array('awaiting_callback' => false));
                    }

                    die('OK');
                }
            }
        }
    } elseif ($mode == 'complete') {
        if (isset($_REQUEST['order_id'])) {
            $order_id = (int) $_REQUEST['order_id'];
            $order_info = fn_get_order_info($order_id);
        } else {
            die('Access denied');
        }

        if (fn_check_payment_script('vsevcredit.php', $order_id)) {
            fn_change_order_status($order_id, 'O', $order_info['status'], false);
            fn_order_placement_routines('route', $_REQUEST['order_id']);
        }
    } else {
        die('CANCEL');
    }
} else {
    $shop_id = (!empty($processor_data['processor_params']['vvc_shop_id'])) ? $processor_data['processor_params']['vvc_shop_id'] : 0;
    $user_id = (!empty($order_info['user_id'])) ? $order_info['user_id'] : 0;
    $order_id = (!empty($order_id)) ? $order_id : 0;
    $url = ($processor_data['processor_params']['test_mode']) ? 'http://test.vsevcredit.ru/js/widget.js' : 'http://vsevcredit.ru/js/widget.js';

    if (!empty($order_id)) {
        fn_update_order_payment_info($order_id, array('awaiting_callback' => true));
    }

echo <<<EOT
<html>
<head>
<script type="text/javascript">
    var VVC_SETTINGS = {
        shop_id  : {$shop_id},
        user_id  : {$user_id},
        order_id : {$order_id},
        css      : 'red',
        response : function(result){}
    };
</script>
<script type="text/javascript" src="{$url}"></script>
</head>
<body>
EOT;

    $msg = __('text_cc_processor_connection', array(
        '[processor]' => 'Vsevcredit server'
    ));

    $order_items = $order_info['products'];
    $products_info = "[";
    foreach ($order_items as $order_item) {
        $products_info .="{ id:'" . $order_item['product_id'] . "', title:'" . $order_item['product'] . "', amount:'" . $order_item['price'] . "', count:'" . $order_item['amount'] . "', info:''    },";
    }
    if (isset($order_info['shipping_cost'])) {
        $shipping_info = '';
        foreach ($order_info['shipping'] as $shipping) {
            $shipping_info .= $shipping['shipping'] . ', ';
        }
        $products_info .="{ id:'', title:'" . __('shipping') . "', amount:'" . $order_info['shipping_cost'] . "', count:'" . 1 . "', info:'" . $shipping_info . "'    },";
    }
    if (isset($order_info['tax_exempt']) && $order_info['tax_exempt'] == 'N') {
        $tax_total = 0;
        $tax_info = '';
        if (isset($order_info['taxes'])) {
            foreach ($order_info['taxes'] as $tax) {
                if ($tax['price_includes_tax'] == 'N') {
                    $tax_total += $tax['tax_subtotal'];
                    $tax_info .= $tax['description'] . ', ';
                }
            }
        }
        if (!empty($tax_total)) {
            $products_info .="{ id:'', title:'" . __('tax') . "', amount:'" . $tax_total . "', count:'" . 1 . "', info:'" . $tax_info . "'    },";
        }
    }
    $products_info .="], 5";

echo <<<EOT
<p><div align=center>{$msg}</div></p>
</body>
<script type="text/javascript">
VVC.onBuy($products_info);
</script>
</html>
EOT;

    exit;
}
