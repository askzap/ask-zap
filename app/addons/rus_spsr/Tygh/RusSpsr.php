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

namespace Tygh;

use Tygh\Http;
use Tygh\Settings;
use Tygh\Registry;

class RusSpsr
{
    public static $sid;
    public static $login;
    public static $icn;
    public static $url;
    public static $last_error;
    public static $url_invoice;

    public static $extra = array (
        'headers' => array('Content-Type: application/xml'),
    );

    public static function WALogin()
    {
        $spsr_info = Registry::get('addons.rus_spsr');

        self::$icn = $spsr_info['icn'];
        $login = self::$login = $spsr_info['login'];
        $password = $spsr_info['password'];
        $company = $spsr_info['shop_name'];

        if ($spsr_info['secure_protocol']) {
            if ($spsr_info['server'] == 'test') {
                $url = self::$url = 'https://api.spsr.ru/test';
            } else {
                $url = self::$url = 'https://api.spsr.ru';
            }
        } else {
            if ($spsr_info['server'] == 'test') {
                $url = self::$url = 'http://api.spsr.ru:8020/waExec/WAExec';
            } else {
                $url = self::$url = 'http://api.spsr.ru/waExec/WAExec';
            }            
        }

        $data=<<<EOT
        <root xmlns="http://spsr.ru/webapi/usermanagment/login/1.0">
            <p:Params Name="WALogin" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0" />
            <Login Login="{$login}" Pass="{$password}" UserAgent="{$company}"/>
        </root>
EOT;

        $response = Http::post($url, $data, self::$extra);
        $xml = simplexml_load_string($response);

        $status_code = (string) $xml->Result['RC'];
        if ($status_code != 0) {
            self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : __("shippings.spsr.error_city");
            $return = false;
        } else {
            self::$sid = (string) $xml->Login['SID'];
            $return = true;
        }

        return $return;
    }

    public static function WALogout()
    {
        $sid = self::$sid;
        $login = self::$login;
        $url = self::$url;
        $extra = self::$extra;

        $data=<<<EOT
        <root xmlns="http://spsr.ru/webapi/usermanagment/logout/1.0" >
            <p:Params Name="WALogout" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0" />
            <Logout Login="{$login}" SID="{$sid}" />
        </root>
EOT;

        $return = false;
        $response = Http::post($url, $data, $extra);
        $xml = simplexml_load_string($response);

        $status_code = (string) $xml->Result['RC'];
        if ($status_code == 0) {
            $return = true;
        }

        return $return;
    }

    public static function WAGetCities($location)
    {
        $city_name = $location['city'];
        $url = self::$url;

        if (!empty($location['country'])) {
            $country_name = fn_get_country_name($location['country'], 'ru');
        } else {
            $country_name = '';
        }

        $data=<<<EOT
        <root xmlns="http://spsr.ru/webapi/Info/GetCities/1.0">
            <p:Params Name="WAGetCities" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0" />
            <GetCities CityName="{$city_name}" CountryName="{$country_name}"/>
        </root>
EOT;

        $response = Http::post($url, $data, self::$extra);
        $xml = simplexml_load_string($response);

        $return = false;
        $status_code = (string) $xml->Result['RC'];
        if ($status_code != 0) {
            self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : __("shippings.spsr.error_city");
        } else {
            if (isset($xml->City->Cities) && !empty($xml->City)) {
                $return = array();
                $city_name = fn_strtolower($city_name);

                foreach ($xml->City->Cities as $city) {  
                    $spsr_city = fn_strtolower((string) $city['CityName']);

                    if ($spsr_city == $city_name) {
                        $return = self::attributesToArray($city);
                    }
                }
            }
        } 

        if (empty($return)) {
            self::$last_error = __("shipping.sdek.not_city");
        }

        return $return;
    }

    public static function WAGetEncloseType()
    {
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;

        $data=<<<EOT
        <root xmlns="http://spsr.ru/webapi/Info/Info/1.0">
            <p:Params Name="WAGetEncloseType" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0" />
            <Login SID="{$sid}"/>
        </root>
EOT;

        $response = Http::post($url, $data, $extra);
        $xml = simplexml_load_string($response);

        $return = false;
        $status_code = (string) $xml->Result['RC'];
        if ($status_code == 0) {
            if (isset($xml->EncloseTypes) && !empty($xml->EncloseTypes)) {
                $enclose_type = array();

                foreach ($xml->EncloseTypes->EType as $type) {
                    $enclose_type[] = self::attributesToArray($type); 
                }
            }

            if (!empty($enclose_type)) {
                $return = $enclose_type;
            }
        }

        return $return;
    }

    public static function WAGetAddrList($addr_type)
    {
        $login = self::$login;
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;
        $icn = self::$icn;
        $list = array();

        $data = <<<EOT
        <root xmlns="http://spsr.ru/webapi/DataEditManagment/GetAddress/1.0" >
            <p:Params Name="WAGetAddress" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0" />
            <Login SID="{$sid}"/>
            <AddrList ICN="{$icn}" Login="{$login}" AddressType="{$addr_type}"/>
        </root>
EOT;

        $response = Http::post($url, $data, $extra);
        $xml = simplexml_load_string($response);

        $status_code = (string) $xml->Result['RC'];
        if ($status_code == 0) {
            if (isset($xml->AddrList) && !empty($xml->AddrList)) {
                foreach ($xml->AddrList->Address as $addres) {
                    $list[] = self::attributesToArray($addres); 
                }
            }
        }

        if (!empty($list)) {
            return $list;
        } else {
            return false;
        }
    }

    public static function WANewInvoicesByFile($content)
    {
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;
        $icn = self::$icn;
        $login = self::$login;
        $xml_content = '';

        if (is_array($content)) {
            foreach ($content as $str) {
                $xml_content .= $str;
            }
        } else {
            $xml_content = $content;
        }

        $data=<<<EOT
        <root xmlns="http://spsr.ru/webapi/xmlconverter/1.3">
            <Params Name="WANewInvoicesByFile" Ver="1.0" xmlns="http://spsr.ru/webapi/WA/1.0"/>
            <Login SID="{$sid}"/>
                <XmlConverter>
                    {$xml_content}
                </XmlConverter>
        </root>
EOT;

        $response = Http::post($url, $data, $extra);
        fn_set_storage_data('spsr_session', $response);
        $xml = simplexml_load_string($response);

        $status_code = (string) $xml->Result['RC'];
        if ($status_code != 0) {
            $error = (string) $xml->error['ErrorNumber'];
            self::$last_error = !empty($error) ? $error : __("shippings.spsr.error_registry");
            return false;
        } else {
            $result = array(
                'session_id' => (string) $xml->Session['Session_ID'],
                'session_owner_id' => (string) $xml->Session['Session_Owner_ID'],
                'dt_create_session' => (string) $xml->Session['dtCreateSession'],
            );

            return array($result, $xml_content);
        }
    }

    public static function WAGetActiveOrders()
    {
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;
        $icn = self::$icn;
        $login = self::$login;

        $xml=<<<EOT
        <root xmlns="http://spsr.ru/webapi/DataEditManagment/GetActiveOrders/1.0" >
            <p:Params Name="WAGetActiveOrders" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0"
            />
            <Login SID="{$sid}"/>
            <ActiveOrders ICN="{$icn}" Login="{$login}" />
        </root>
EOT;

        $response = Http::post($url, $xml, $extra);
        $xml = simplexml_load_string($response);

        $status_code = (string) $xml->Result['RC'];
        if ($status_code != 0) {
            self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : __("shippings.spsr.error_data");
            $return = false;
        } else {
            if (isset($xml->Orders->Order)) {
                $orders = array();
                foreach ($xml->Orders->Order as $order) {
                    $key = (string) $order['OrderNumber'];
                    $orders[$key] = self::attributesToArray($order);
                }
            }
            if (!empty($orders)) {
                $return = $orders;
            } else {
                self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : __("shippings.spsr.error_order");
                $return = false;
            }
        }

        return $return;
    }

    public static function WAGetInvoiceInfo($invoices)
    {
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;
        $icn = self::$icn;
        $login = self::$login;
        $xml_content = '';

        foreach ($invoices as $invoice) {
            $xml_content .= '<InvoiceInfo InvoiceNumber="' . $invoice . '"/>';
        }

        $data=<<<EOT
        <root xmlns="http://spsr.ru/webapi/DataEditManagment/GetInvoiceInfo/1.1">
            <p:Params Name="WAGetInvoiceInfo" xmlns:p="http://spsr.ru/webapi/WA/1.0" Ver="1.1"/>
            <Login SID="{$sid}" Login="{$login}" ICN="{$icn}"/>
            {$xml_content}
        </root>
EOT;

        $response = Http::post($url, $data, $extra);
        fn_set_storage_data('spsr_invoice_info', $response);
        $xml = simplexml_load_string($response);

        $return = false;
        $status_code = (string) $xml->Result['RC'];
        if ($status_code != 0) {
            self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : __("shippings.spsr.error_data");
        } else {
            $invoices = array();
            foreach ($xml->GetInvoiceInfo->Invoice as $invoice) {
                $i_key = (string) $invoice['ShipmentNumber'];
                $i_data = self::attributesToArray($invoice);
                $s_data = self::attributesToArray($invoice->Shipper);
                $r_data = self::attributesToArray($invoice->Receiver);
                $invoices[$i_key] = array(
                    'invoice_info' => $i_data,
                    'shipper' => $s_data,
                    'receiver' => $r_data
                );
            }

            $return = $invoices;
        }

        return $return;
    }

    public static function WAInvSessionInfo($session_info)
    {
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;

        $data = <<<EOT
        <root xmlns="http://spsr.ru/webapi/DataEditManagment/InvSessionInfo/1.0">
            <Params Name="WAInvSessionInfo" Ver="1.0" xmlns="http://spsr.ru/webapi/WA/1.0" />
            <Login SID="{$sid}" />
            <InvSessionInfo Session_ID="{$session_info['session_id']}" Session_Owner_ID="{$session_info['session_owner_id']}" />
        </root>
EOT;

        $response = Http::post($url, $data, $extra);
        fn_set_storage_data('spsr_session_result',$response);
        $xml = simplexml_load_string($response);

        $code = 0;
        $status_code = (string) $xml->InvSessionInfo['SessionState'];
        if ($status_code != 3) {
            $result['message'] = (string) $xml->InvSessionInfo['Description'];
            $result['message'] = $result['message'] . ' ' . (string) $xml->error['ErrorMessageRU'];
            self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : (string) $xml->InvSessionInfo['Description'];
        } else {
            $_response = $xml->InvSessionInfo->Trace->root;
            $_result = json_decode(json_encode((array) $_response), true);
            $response_code = (string) $_response->Result['RC'];

            if ($response_code != 0) {
                self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : __("shippings.spsr.error_data");
                $return = false;
            } else {
                $invoices = array();
                $messages = "";

                if (!empty($_result['Invoice'])) {
                    foreach ($_response->Invoice as $invoice) {
                        $invoices[] = self::attributesToArray($invoice);
                    }

                    foreach ($_response->Invoice->Message as $message){
                        $messages .= (string) $message['Text'] . ', ';
                        $code = (!empty($message['MessageCode'])) ? (string) $message['MessageCode'] : 0;
                    }
                } else {
                    foreach ($_response->Message as $message){
                        $messages .= (string) $message['Text'] . ', ';
                        $code = (!empty($message['MessageCode'])) ? (string) $message['MessageCode'] : 0;
                    }
                }

                $result = array(
                    'invoices' => $invoices,
                    'message' => $messages,
                    'code_message' => $code,
                );
            }
        }

        return $result;
    }

    public static function WAAddAddress($data, $addr_type)
    {
        $login = self::$login;
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;
        $icn = self::$icn;

        $data=<<<EOT
        <root xmlns="http://spsr.ru/webapi/DataEditManagment/AddAddress/1.0" >
            <p:Params Name="WAAddAddress" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0" />
            <Login SID="{$sid}"/>
            <AddAddr
                ICN="{$icn}"
                Login="{$login}"
                Address="{$data['address']}"
                FIO="{$data['fio']}"
                Organization="{$data['organization']}"
                Phone="{$data['phone']}"
                AddPhone="{$data['addphone']}"
                PostCode="{$data['postcode']}"
                City_ID="{$data['city_id']}"
                City_Owner_ID="{$data['city_owner_id']}"
                AddressType="{$addr_type}" 
            />
        </root>
EOT;

        $response = Http::post($url, $data, $extra);
        $xml = simplexml_load_string($response);

        $result = array();
        $status_code = (string) $xml->Result['RC'];
        if ($status_code != 0) {
            if (isset($xml->AddInfo) && $status_code == 1020) {
                foreach ($xml->AddInfo as $addres){
                    foreach ($addres as $results){
                        $result = array(
                            'SborAddr_ID' => (string) $results['SborAddr_ID'],
                            'SborAddr_Owner_ID' => (string) $results['SborAddr_Owner_ID'],
                        );
                    }
                }
            }
        } else {
            if (isset($xml->AddAddr)) {
                $result = array(
                    'SborAddr_ID' => (string) $xml->AddAddr['SborAddr_ID'],
                    'SborAddr_Owner_ID' => (string) $xml->AddAddr['SborAddr_Owner_ID'],
                );
            }
        }

        if (!empty($result)) {
            return $result;
        } else {
            self::$last_error = self::$_error_descriptions[$status_code];
            return false;
        }
    }

    public static function WADelAddress($sboraddr_id, $sboraddr_owner_id , $addr_type)
    {
        $login = self::$login;
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;
        $icn = self::$icn;

        $data=<<<EOT
        <root xmlns="http://spsr.ru/webapi/DataEditManagment/DelAddress/1.0">
            <p:Params Name="WADelAddress" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0" />
            <Login SID="{$sid}" />
            <DelAddr ICN="{$icn}" Login="{$login}" SborAddr_ID="{$sboraddr_id}" SborAddr_Owner_ID="{$sboraddr_owner_id}"
            AddressType="{$addr_type}" />
        </root>
EOT;

        $response = Http::post($url, $data, $extra);
        $xml = simplexml_load_string($response);

        $status_code = (string) $xml->Result['RC'];
        if ($status_code != 0) {
            self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : __("shippings.spsr.error_city");
            $return = false;
        } else {
            $return = true;
        }

        return $return;
    }

    public static function WAGetServices()
    {
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;

        $data=<<<EOT
        <root xmlns="http://spsr.ru/webapi/Info/Info/1.0">
            <p:Params Name="WAGetServices" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0" />
            <Login SID="{$sid}"/>
        </root>
EOT;

        $response = Http::post($url, $data, $extra);
        $xml = simplexml_load_string($response);

        $return = false;
        $status_code = (string) $xml->Result['RC'];
        if ($status_code == 0) {
            if (isset($xml->MainServices) && !empty($xml->MainServices)) {
                $services = array();

                foreach ($xml->MainServices->Service as $service) {
                    $services[] = self::attributesToArray($service); 
                }
            }

            if (!empty($services)) {
                $return = $services;
            }
        }

        return $return;
    }

    public static function WACreateOrder($data)
    {
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;
        $icn = self::$icn;
        $login = self::$login;

        $xml=<<<EOT
        <root xmlns="http://spsr.ru/webapi/DataEditManagment/CreateOrder/1.0">
            <p:Params Name="WACreateOrder" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0" />
            <Login SID="{$sid}" />
            <AddOrder
            ICN="{$icn}"
            Login="{$login}"
            NecesseryDate="{$data['date']}"
            NecesseryTime="{$data['time']}"
            DeliveryMode="{$data['mode']}"
            FIO="{$data['fio']}"
            SborAddr_ID="{$data['sboraddr_id']}"
            SborAddr_Owner_ID="{$data['sboraddr_owner_id']}"
            ReceiverCity_ID="{$data['receiver_city_id']}"
            ReceiverCity_Owner_ID="{$data['receiver_city_owner_id']}"
            PlacesCount="{$data['placescount']}"
            Weight="{$data['weight']}"
            Description="{$data['description']}"
            OrderType="{$data['order_type']}"
            Length="{$data['length']}"
            Width="{$data['width']}"
            Depth="{$data['depth']}"
            />
        </root>
EOT;

        $response = Http::post($url, $xml, $extra);
        fn_set_storage_data('spsr_create_order', $response);
        $xml = simplexml_load_string($response);

        $status_code = (string) $xml->Result['RC'];
        if ($status_code != 0) {
            self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : __("shippings.spsr.error_data");
            $return = false;
        } else {
            if (isset($xml->AddOrder)) {
                $return = (string) $xml->AddOrder['OrderNum'];
            }
        }

        return $return;
    }

    public static function WACancelOrder($order_id, $order_owner_id)
    {
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;
        $icn = self::$icn;
        $login = self::$login;

        $data=<<<EOT
        <root xmlns="http://spsr.ru/webapi/DataEditManagment/CancelOrder/1.0" >
            <p:Params Name="WACancelOrder" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0" />
            <Login SID="{$sid}"/>
            <CancelOrder ICN="{$icn}" Login="{$login}" Order_ID="{$order_id}"
            Order_Owner_ID="{$order_owner_id}"/>
        </root>
EOT;

        $response = Http::post($url, $data, $extra);
        $xml = simplexml_load_string($response);

        $status_code = (string) $xml->Result['RC'];
        if ($status_code != 0) {
            self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : __("shippings.spsr.error_data");
            $return = false;
        } else {
            $result = array(
                'order' => (string) $xml->CancelOrder['OrderNumber'],
                'order_state' => (string) $xml->CancelOrder['OrderState'],
            );
            $return = $result;
        }

        return $return;
    }

    public static function WAGetExtMon($from, $to, $status = '-1')
    {
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;
        $icn = self::$icn;
        $login = self::$login;

        $from = date('Y-m-d' , fn_parse_date($from)) . 'T00:00:00.803';
        $to = date('Y-m-d' , fn_parse_date($to)) . 'T23:59:59.803';

        $data=<<<EOT
        <root xmlns="http://spsr.ru/webapi/DataEditManagment/GetExtMon/1.0"><p:Params Name="WAGetExtMon" xmlns:p="http://spsr.ru/webapi/WA/1.0" Ver="1.0"/>
            <Login
            SID="{$sid}" />
            <GetInvoicesInfo
            ICN="{$icn}"
            Login="{$login}"
            FromDT="{$from}"
            ToDT="{$to}"
            DeliveryStatus="{$status}" />
        </root>
EOT;

        $response = Http::post($url, $data, $extra);
        fn_set_storage_data('spsr_invoice_info', $response);
        $xml = simplexml_load_string($response);

        $status_code = (string) $xml->Result['RC'];
        if ($status_code != 0) {
            self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : __("shippings.spsr.error_data");
            return false;
        } else {
            if (!empty($xml->Invoices->Invoice)) {
                $invoices = array();
                foreach ($xml->Invoices->Invoice as $invoice) {
                    $result_invoice = $receiver = array();
                    foreach ($invoice->attributes() as $_key => $_value){
                        $result_invoice[$_key] = (string) $_value;
                    }
                    foreach ($invoice->Receiver->attributes() as $_key => $_value){
                        $receiver[$_key] = (string) $_value;
                    }
                    foreach ($invoice->Shipper->attributes() as $_key => $_value){
                        $shippier[$_key] = (string) $_value;
                    }    

                    $invoices[] = array(
                        'invoice' => $result_invoice,
                        'receiver' => $receiver,
                        'shippier' => $shippier,
                    );              
                }
            }
            if (!empty($invoices)) {
                return $invoices;               
            }
        }
    }

    public static function WABindOrderToInvoice($invoice, $courier)
    {
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;
        $icn = self::$icn;
        $login = self::$login;

        $xml=<<<EOT
        <root xmlns="http://spsr.ru/webapi/DataEditManagment/BindOrderToInvoice/1.0">
            <Params Name="WABindOrderToInvoice" Ver="1.0" xmlns="http://spsr.ru/webapi/WA/1.0"/>
            <Login SID="{$sid}"/>
            <Invoice ICN="{$icn}"
            Login="{$login}"
            InvoiceNumber="{$invoice}"
            Order_ID="{$courier['order_id']}"
            Order_Owner_ID="{$courier['order_owner_id']}">
            </Invoice>
        </root>
EOT;

        $response = Http::post($url, $xml, $extra);
        fn_set_storage_data('spsr_bind_order', $response);
        $xml = simplexml_load_string($response);

        $status_code = (string) $xml->Result['RC'];
        if ($status_code != 0) {
            self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : 'Ошибка получения города';
            return false;
        } else {
            return true;
        }
    }

    public static function WAGetOrders($from, $to)
    {
        $sid = self::$sid;
        $url = self::$url;
        $extra = self::$extra;
        $icn = self::$icn;
        $login = self::$login;

        $from = date('Y-m-d' , fn_parse_date($from)) . 'T00:00:00.000';
        $to = date('Y-m-d' , fn_parse_date($to)) . 'T00:00:00.000';

        $xml=<<<EOT
        <root xmlns="http://spsr.ru/webapi/DataEditManagment/GetOrders/1.0" >
            <p:Params Name="WAGetOrders" Ver="1.0" xmlns:p="http://spsr.ru/webapi/WA/1.0" />
            <Login SID="{$sid}"/>
            <GetOrders ICN="{$icn}" Login="{$login}" FromDT="{$from}" ToDT="{$to}" />
        </root>
EOT;

        $response = Http::post($url, $xml, $extra);
        $xml = simplexml_load_string($response);

        $return = false;
        $status_code = (string) $xml->Result['RC'];
        if ($status_code != 0) {
            self::$last_error = !empty(self::$_error_descriptions[$status_code]) ? self::$_error_descriptions[$status_code] : 'Ошибка получения города';
        } else {
            if (isset($xml->Orders->OrderInfo)) {
                $orders = array();
                foreach ($xml->Orders->OrderInfo as $order) {
                    $key = (string) $order['OrderNum'];
                    $orders[$key] = array(
                        'OrderNumber' => (string) $order['OrderNum'],
                        'OrderState' => (string) $order['OrderState'],
                        'DateOfCreate' => (string) $order['CreateDT'],
                        'PlanningDT_From' => (string) $order['CourierArrivalDT'],
                        'PlanningDT_To' => (string) $order['PlanningDT_to'],
                        'FIO' => (string) $order['ContactFIO'],
                        'OperatorFIO' => (string) $order['OperatorFIO'],
                        'ContactPhone' => (string) $order['ContactPhone'],
                        'Address' => (string) $order['Address'],
                    );
                }
                $return = $orders;
            }
        }

        return $return;
    }

    public static function saveSesInvoices($order_id, $session_info, $save_data, $xml)
    {
        $register_data = array(
            'session_id' => $session_info['session_id'],
            'session_owner_id' => $session_info['session_owner_id'], 
            'order_id' => $order_id, 
            'data' => serialize($save_data),
            'data_xml' => $xml,
            'timestamp' => TIME,
            'status' => 'S',
        );

        $register_id = db_query('INSERT INTO ?:rus_spsr_register ?e', $register_data);

        foreach($save_data['packages'] as $key => $piece) {
            db_query('UPDATE ?:rus_spsr_invoices SET register_id = ?i WHERE order_id = ?i AND shipment_id = ?i', $register_id, $piece['order_id'], $piece['shipment_id']);
            db_query('UPDATE ?:rus_spsr_invoices_items SET register_id = ?i, barcode = ?i, data = ?s WHERE order_id = ?i AND invoice_item_id = ?i AND shipment_id = ?i', $register_id, $piece['barcode'], serialize($piece['data']), $piece['order_id'], $piece['invoice_item_id'], $piece['shipment_id']);
        }

        return $register_id;
    }

    public static function attributesToArray($xml)
    {
        $result = array();
        foreach ($xml->attributes() as $key => $value){
            $result[$key] = (string) $value;
        }

        return $result;
    }

    protected static $_error_descriptions = array(
        '0' => 'OK',
        '1' => 'Неизвестная ошибка',
        '2' => 'Указанная версия процедуры не существует',
        '3' => 'Версия процедуры не указана',
        '4' => 'Неверная структура xml',
        '5' => 'Ошибка валидации xml',
        '6' => 'Указанная процедура не существует',
        '7' => 'Ошибка синтаксиса процедуры',
        '8' => 'Процедура отключена',
        '9' => 'Передан некороректный xml',
        '1010' => 'Переданный ИКН не найден',
        '1011' => 'Договор расторгнут',
        '1004' => 'Неверно указан Логин или Пароль',
        '1005' => 'Клиент заблокирован',
        '1006' => 'Сессия истекла или не существует',
        '1007' => 'Указанный Логин не имеет доступа к переданному ИКН',
        '1012' => 'Накладная не принадлежит переданному ИКН',
        '1014' => 'Передан диапазон дат более 90 дней',
        '1015' => 'Пользователь отключен',
        '1016' => ' Не переданы обязательные параметры (номер счета или диапазон дат)',
        '1017' => 'Объект не существует',
        '1019' => 'Адрес сбора не принадлежит данному ИКН',
        '1062' => 'Переданный логин не соответствует идентификатору сессии',
        '1021' => 'Нарушена консистентность данных',
        '1022' => 'Доступ разрешен только администратору',
        '1048' => 'Клиент не определен по переданным данным',
        '1049' => 'Контракт не определен по переданным данным',
        '1058' => 'Не указан один или несколько аргументов',
        '1009' => 'Город не найден',
        '1013' => 'Передан пустой город',
        '1047' => 'Город не обслуживается',
        '1018' => 'Заказ не может быть отменен',
        '1020' => 'Адрес уже существует',
        '1023' => 'Передан адрес без города',
        '1024' => 'Переданы некорректные идентификаторы адреса сбора',
        '1025' => 'Дата сбора не может быть меньше текущей',
        '1026' => 'Вызов курьера возможен только на следующий день',
        '1027' => 'Проблема при определении оператора по логину',
        '1028' => 'Не заполнено количество мест',
        '1029' => 'Не заполнено ФИО',
        '1030' => 'Ошибка определения департамента',
        '1031' => 'Передан некорректный ИКН',
        '1032' => 'Передан некорректный адрес сбора',
        '1033' => 'Не заполнен вес',
        '1034' => 'Задолженность, оформление заказа невозможно',
        '1035' => 'Найдено несколько улиц по условиям поиска',
        '1036' => 'Улица не найдена',
        '1037' => 'По указанному адресу нет свободных квот',
        '1038' => 'Найдено несколько регионов по условиям поиска',
        '1039' => 'Регион не найден',
        '1040' => 'Найдено несколько городов по условиям поиска',
        '1041' => 'Город не найден',
        '1042' => 'Оператор вам перезвонит',
        '1043' => 'Номер дома не определен',
        '1044' => 'Номер дома слишком длинный',
        '1045' => 'Изменение квоты запрещено',
        '1046' => 'Доступные квоты отсутствуют',
        '1050' => 'Сервис недоступен в указанное время',
        '1051' => 'Накалданая с указанным номером присвойки уже существует',
        '1052' => 'Переданная сессия не соответствует сессии получения квоты ',
        '1053' => 'Переданная дата не найдена в списке квотируемых',
        '1054' => 'Отсутствуют свободные квоты на указанную дату',
        '1055' => 'Накладная уже выдана в доставку',
        '1056' => 'Бронирование квоты не найдено',
        '1057' => 'Данные для поиска не переданы',
        '1059' => 'Накладная не найдена по указанному номеру',
    );

    public static function preInvoiceByShipments($shipment, $order_info, $shipping_data)
    {
        $total_weight = 0;
        $invoice = array();

        if (!empty($shipment)) {
            $settings = $shipping_data['service_params'];
            if (!empty($settings['default_product_type'])) {
                $products = $order_info['products'];
                $shipment_id = $shipment['shipment_id'];
                $invoices_db = db_get_row("SELECT * FROM ?:rus_spsr_invoices WHERE order_id = ?i AND shipment_id = ?i ", $order_info['order_id'], $shipment_id);

                $subtotal_discount_drop = !empty($order_info['subtotal_discount']) ? $order_info['subtotal_discount'] : 0;
                $total = !empty($order_info['subtotal']) ? $order_info['subtotal'] : 1;

                $invoice['order_id'] = $order_info['order_id'];
                $invoice['ship_ref_num'] = $order_info['order_id'] . 'I' . $shipment['shipment_id'];
                $invoice['shipment'] = $shipment;

                $invoice['amount'] = 0;
                $invoice['weight'] = 0;
                $cost = 0;
                foreach ($shipment['products'] as $key => $amount) {
                    $product = $products[$key];

                    $ship_data = db_get_row("SELECT spsr_product_type, shipping_params, weight FROM ?:products WHERE product_id = ?i", $product['product_id']);
                    $ship_data['shipping_params'] = unserialize($ship_data['shipping_params']);
                    $product = array_merge($product, $ship_data);

                    if ($settings['part_delivery']) {
                        $_amount = 1;
                        $i = 0;
                        while ($i < $amount) {
                            $cost = $cost + $product['price'];
                            $invoice['amount']++;
                            $invoice['weight'] = $invoice['weight'] + $product['weight'];
                            $i++;
                        }
                    } else {
                        $invoice['amount'] = $_amount = $amount;
                        $cost = $product['price'] * $_amount;
                        $invoice['weight'] = $product['weight'] * $_amount;
                    }  

                    $product_description = $product['product'];
                    if (!empty($product['extra']['product_options_value'])) {
                        foreach ($product['extra']['product_options_value'] as $option) {
                            $product_description .= ', ' . $option['variant_name'];
                        }
                    }

                    $data = array(
                        'item_id' => $key,
                        'product_type' => $product['spsr_product_type'],
                        'product_id' => $product['product_id'],
                        'amount' => $_amount,
                        'product_code' => $product['product_code'],
                        'product' => $product_description,
                        'price' => $product['price'],
                        'length' => $product['shipping_params']['box_length'],
                        'width' => $product['shipping_params']['box_width'],
                        'height' => $product['shipping_params']['box_height'],
                        'weight' => $product['weight'],
                    );

                    if ($settings['part_delivery']) {
                        $i = 0;
                        while ($i < $amount) {
                            $invoice['products'][] = $data;
                            $i++;
                        }
                    } else {
                        $invoice['products'][] = $data;
                    }    
                }

                if (!empty($cost)) {
                   $cost_before_disc = $cost;
                   $cost = $cost - $cost*($subtotal_discount_drop/$total);
                }

                $invoice['cost'] = $cost;
                $invoice['cost_before_disc'] = !empty($cost_before_disc) ? $cost_before_disc : 0;
                $invoice['cost_discount'] = !empty($subtotal_discount_drop) ? $subtotal_discount_drop : 0;

                $invoice['spsr_tariffs'] = self::deliveryTariffs($invoice, $order_info, $shipping_data);

                if (!empty($invoices_db['shipping_cost'])) {
                    $invoice['invoice_shipping_cost'] = $invoices_db['shipping_cost'];
                    $invoice['spsr_tariff'] = (!empty($invoice['spsr_tariffs'])) ? $invoice['spsr_tariffs'][$invoices_db['tariff_code']] : 0;
                } else {
                    $invoice['invoice_shipping_cost'] = (!empty($invoice['spsr_tariffs'])) ? $invoice['spsr_tariffs']['PelSt']['Total_Dost'] : 0;
                }

                $invoice['service_params'] = $shipping_data;
                $invoice['package_info'] = $settings;
            }
        }

        return $invoice;
    }

    public static function deliveryTariffs($invoice, $order_info, $shipping_data)
    {
        $code_tariff = array(
            'Калибри-документ' => 'Dox',
            'Гепард-экспресс 13' => 'Gep13',
            'Гепард-экспресс 18' => 'Gep18',
            'Гепард-экспресс' => 'GepEx',
            'Пеликан-стандарт' => 'PelSt',
            'Пеликан-эконом' => 'PelEc',
            'Бизон-карго' => 'BisCa',
            'Бизон-авиа' => 'BisAv',
            'Фрахт' => 'Freig',
            'Пеликан-онлайн' => 'PelOn',
        );

        if($shipping_data['service_params']['insurance_type'] == "INS") {
            $amount_check = 1;
        } elseif ($shipping_data['service_params']['insurance_type'] == "VAL") {
            $amount_check = 0;
        }

        $product_type = array();
        foreach ($invoice['products'] as $product) {
            $product_type[$product['product_type']] = $product['product_type'];
            $nature = $product['product_type'];
        }

        if (count($product_type) > 1) {
            if(array_search(18, $product_type)) {
                $amount_check = 1;
                $nature = 18;
            } else {
                $nature = $shipping_data['service_params']['default_product_type'];
            }
        }

        $data_city = array();
        $data_city['city'] = (!empty($order_info['s_city'])) ? $order_info['s_city'] : $order_info['b_city'];
        $data_city['country'] = (!empty($order_info['s_country'])) ? $order_info['s_country'] : $order_info['b_country'];
        $city = self::WAGetCities($data_city);

        $weight = $invoice['weight'];
        if ($weight == 0) {
            $weight = 0.01 * $invoice['amount'];
        }

        $data = array(
            'ToCity' => $city['City_ID'] . '|' . $city['City_owner_ID'],
            'FromCity' => $shipping_data['service_params']['from_city_id'] . '|' . $shipping_data['service_params']['from_city_owner_id'],
            'Weight' => $weight,
            'Nature' => $nature,
            'Amount' => $invoice['cost'],
            'AmountCheck' => $amount_check,
            'SMS' => $shipping_data['service_params']['sms_to_shipper'],
            'SMS_Recv' => $shipping_data['service_params']['sms_to_receiver'],
            'PlatType' => $shipping_data['service_params']['plat_type'],
            'DuesOrder' => $shipping_data['service_params']['dues_order'],
            'ToBeCalledFor' => $shipping_data['service_params']['to_be_called_for'],
            'ByHand' => $shipping_data['service_params']['by_hand'],
            'icd' => $shipping_data['service_params']['idc'],
            'SID' => self::$sid,
        );

        $_result = array();
        $response = Http::post('http://www.cpcr.ru/cgi-bin/postxml.pl?TARIFFCOMPUTE_2', $data);
        $xml = simplexml_load_string($response);

        if (isset($xml->Error)){
            fn_set_notification('E', __('notice'), __('shippings.spsr.error_tariff'));

        } elseif (isset($xml->Tariff)) {
            foreach ($xml->Tariff as $shipment) {
                if ($shipment->Total_Dost == 'Error') {
                    fn_set_notification('E', __('notice'), (string) $shipment->TariffType);
                } else {
                    $tariff_name = str_replace("Услуги по доставке ", "", (string) $shipment->TariffType);
                    $tariff_name = str_replace('"', "", $tariff_name);
                    $_result[$code_tariff[$tariff_name]] = array(
                        'Code' => $code_tariff[$tariff_name],
                        'TariffType' => $tariff_name,
                        'Total_Dost' => (string) $shipment->Total_Dost,
                        'Total_DopUsl' => (string) $shipment->Total_DopUsl,
                        'Insurance' => (string) $shipment->id,
                        'worth' => (string) $shipment->Insurance,
                        'DP' => (string) $shipment->DP,
                    );
                }
            }
        }

        return $_result;
    }

    public static function arraySimpleXml($name, $data, $type = 'simple')
    {
        $xml = '<'.$name.' ';
        foreach($data as $key => $value) {
            if (!empty($value)) {
                $value = fn_html_escape($value);
                $xml .= $key .'="' . $value .'" ';
            }
        }

        if ($type == 'open') {
            $xml .= '>';            
        } else {
            $xml .= '/>'; 
        }
       
        return $xml;
    }

    public static function piecesXml($pieces)
    {
        $xml = array();

        if (!empty($pieces)) {
            $invoice_full_desc = '';

            $xml[] = '    <Pieces>';
            foreach ($pieces as $piece_key => $piece) {
                $piece_for_xml = array(
                    'PieceID' => $piece['barcode'],
                    'Description' => $piece['data']['description'],
                    'ClientBarcode' => $piece['data']['barcode'],
                    'Weight' => $piece['data']['weight'],
                    'Length' => $piece['data']['length'],
                    'Width' => $piece['data']['width'],
                    'Depth' => $piece['data']['height'],
                );

                $xml[] = '        ' . self::arraySimpleXml('Piece', $piece_for_xml , 'open');
                foreach ($piece['data']['products'] as $subpice) {
                    if ($subpice['product'] != 'Доставка') {
                        $subpice_for_xml = array(
                            'Description' => fn_html_escape($subpice['product']),
                            'Cost' => $subpice['price'],
                            'ProductCode' => $subpice['product_code'],
                            'Quantity' => $subpice['amount'],
                        );
                        $xml[] = '            ' . self::arraySimpleXml('SubPiece', $subpice_for_xml);
                        $invoice_full_desc .= $subpice['product_code'] .' '. $subpice['product'].'; ';
                    }
                }
                $xml[] = '        </Piece>';
            }
            $xml[] = '    </Pieces>';
        }

        return array($xml, $invoice_full_desc);
    }

    public static function invoiceXml($invoice, $pieces, $shipper_xml, $receiver_xml, $additional_services_xml , $sms_xml = '')
    {
        $invoice_start = self::arraySimpleXml('Invoice', $invoice, 'open');
        $invoice_close = '</Invoice>';

        array_unshift($pieces,  '    ' . $additional_services_xml);

        if (!empty($sms_xml)) {
            array_unshift($pieces, '    ' . $sms_xml);
        }
        array_unshift($pieces, '    ' . $receiver_xml);
        array_unshift($pieces, '    ' . $shipper_xml);
        array_unshift($pieces, '  ' . $invoice_start);
        array_push($pieces, '  ' . $invoice_close);
       
        return $pieces;
    }

    public static function generalXml($general_for_xml , $invoices_xml)
    {
        $general_start = self::arraySimpleXml('GeneralInfo', $general_for_xml, 'open');
        $general_close = '</GeneralInfo>';

        array_unshift($invoices_xml, $general_start);
        array_push($invoices_xml, $general_close);
       
        return $invoices_xml;
    }

    public static function urlInvoice()
    {
        $spsr_info = Registry::get('addons.rus_spsr');

        if ($spsr_info['secure_protocol']) {
            if ($spsr_info['server'] == 'test') {
                self::$url_invoice = 'https://lk.spsr.ru';
            } else {
                self::$url_invoice = 'https://cabinet.spsr.ru';
            }
        } else {
            if ($spsr_info['server'] == 'test') {
                self::$url_invoice = 'http://lk.spsr.ru';
            } else {
                self::$url_invoice = 'http://cabinet.spsr.ru';
            }
        }

        return self::$url_invoice;
    }
}
