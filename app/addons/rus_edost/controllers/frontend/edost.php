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

if ($mode == 'services') {
    $params = $_REQUEST;

    $data = array(
        'num' => $params['tracknumbers'],
        'n' => $params['tracknumbers'],
    );

    $trackings = fn_get_schema('edost', 'trackings', 'php', true);

    $join = "";
    if (!empty($params['shipment_id'])) {
        $join = db_quote(" AND shipment_id = ?i ", $params['shipment_id']);
    }

    $shipping = db_get_row("SELECT shipping_id FROM ?:shipments WHERE tracking_number = ?s AND carrier = ?s ?p", $params['tracknumbers'], $params['carrier'], $join);

    if (!empty($shipping['shipping_id'])) {
        $data_shipment = fn_get_shipping_info($shipping['shipping_id'], DESCR_SL);

        $code_tracking = db_get_field('SELECT code FROM ?:shipping_services WHERE service_id = ?i', $data_shipment['service_id']);

        if (!empty($code_tracking) && !empty($trackings[$code_tracking])) {
            $data['c'] = $trackings[$code_tracking];
        }
    }

    if (!empty($data['c'])) {
        fn_echo("<form action='http://www.edost.ru/tracking.php' method='post' name='frm' accept-charset='windows-1251'>");
    } else {
        fn_echo("<form action='http://www.edost.ru/tracking.php' method='get' name='frm' accept-charset='windows-1251'>");
    }

    foreach ($data as $name => $value) {
        fn_echo("<input type='hidden' name='" . htmlentities($name) . "' value='" . $value . "'>");
    }
    fn_echo("</form>");
    fn_echo('<script language="JavaScript">');
    fn_echo('document.frm.submit();');
    fn_echo('</script>');
}
