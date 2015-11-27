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

// rus_build_kupivkredit dbazhenov

use Tygh\Http;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (!empty($_REQUEST['order_id'])) {
    $order_id = (int) $_REQUEST['order_id'];
} else {
    $order_id = 0;
}

$kvk_url = (!empty($_REQUEST['test']) == 'Y') ? KVK_API_TEST_URL . '/api' : KVK_API_URL;

$order_info = fn_get_order_info($order_id);
$kvk_order_id = !empty($order_info['payment_info']['kvk_order_id']) ? $order_info['payment_info']['kvk_order_id'] : 0;

if ($mode == 'kvk_cancel') {
    $suffix = 'cancel_order';
    $reason = __('kupivkredit_cancel_order_status');

$post = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<request>
    <partnerId>$_REQUEST[partner_id]</partnerId>
    <apiKey>$_REQUEST[api_key]</apiKey>
    <params>
        <PartnerOrderId>$kvk_order_id</PartnerOrderId>
        <Reason>$reason</Reason>
    </params>
</request>";
    $message = fn_rus_kupivkredit_hash_message($post, $_REQUEST['sig']);
    $response = Http::post("https://$kvk_url/$suffix", $message, array(
        'headers' => array(
            'Content-type: text/xml'
        )
    ));
    preg_match('/<status>(.*)<\/status>/', $response, $status);
    preg_match('/<result>(.*)<\/result>/', $response, $result);

    if (isset($status[1]) && strtoupper($status[1]) == 'OK') {
        $pp_response['order_status'] = 'I';
        $pp_response['reason_text'] = 'ok';
    } else {
        $pp_response['reason_text'] = $result[1];
    }
    fn_update_order_payment_info($order_id, $pp_response);

    return array(CONTROLLER_STATUS_REDIRECT, "orders.details?order_id=$order_id");

} elseif ($mode == 'kvk_complete') {
    $suffix = 'order_completed';

$post = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<request>
    <partnerId>$_REQUEST[partner_id]</partnerId>
    <apiKey>$_REQUEST[api_key]</apiKey>
    <params>
        <PartnerOrderId>$kvk_order_id</PartnerOrderId>
    </params>
</request>";
    $message = fn_rus_kupivkredit_hash_message($post, $_REQUEST['sig']);
    $response = Http::post("https://$kvk_url/$suffix", $message, array(
        'headers' => array(
            'Content-type: text/xml'
        )
    ));
    preg_match('/<status>(.*)<\/status>/', $response, $status);
    preg_match('/<result>(.*)<\/result>/', $response, $result);

    if (isset($status[1]) && strtoupper($status[1]) == 'OK') {
        $pp_response['order_status'] = 'C';
        $pp_response['reason_text'] = 'ok';
    } else {
        $pp_response['reason_text'] = $result[1];
    }
    fn_update_order_payment_info($order_id, $pp_response);

    return array(CONTROLLER_STATUS_REDIRECT, "orders.details?order_id=$order_id");

} elseif ($mode == 'kvk_confirm') {
    $suffix = 'confirm_order';
$post = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<request>
    <partnerId>$_REQUEST[partner_id]</partnerId>
    <apiKey>$_REQUEST[api_key]</apiKey>
    <params>
        <PartnerOrderId>$kvk_order_id</PartnerOrderId>
    </params>
</request>";

    $message = fn_rus_kupivkredit_hash_message($post, $_REQUEST['sig']);
    $response = Http::post("https://$kvk_url/$suffix", $message, array(
        'headers' => array(
            'Content-type: text/xml'
        )
    ));

    preg_match('/<status>(.*)<\/status>/', $response, $status);
    preg_match('/<result>(.*)<\/result>/', $response, $result);

    if (isset($status[1]) && strtoupper($status[1]) == 'OK') {
        $pp_response['reason_text'] = __('kupivkredit_order_confirmed');
    } else {
        $pp_response['reason_text'] = $result[1];
    }
    fn_update_order_payment_info($order_id, $pp_response);

    return array(CONTROLLER_STATUS_REDIRECT, "orders.details?order_id=$order_id");
}

function fn_rus_kupivkredit_hash_message($post, $secret)
{
    $base = base64_encode($post);
    $sig = fn_rus_kupivkredit_hash_order($base, $secret);

return "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<envelope>
    <Base64EncodedMessage>$base</Base64EncodedMessage>
    <RequestSignature>$sig</RequestSignature>
</envelope>";
}
