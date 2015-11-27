<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
    <head>
            <title>{__("sbrf_receipt")}</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta http-equiv="Content-Language" content="en" />

            <style type="text/css">
                {literal}
                    p{padding:5px 0 0 5px}
                    li{list-style-type:none;padding-bottom:5px;padding:6px 0 0 5px}
                    .receipt{font-family:"Times New Roman",Times,serif;font-size:14px; line-height: 17px}
                    .receipt strong{font-family:"Times New Roman",Times,serif;font-size:12px;}
                    .print-receipt{font-size: 11px !important; text-decoration: none}
                    .receipt-list {padding: 0; margin: 0}
                    .text-button {
                        font-family: Arial;
                        color: #08c;
                        font-size: 11px !important;
                        text-decoration: none;
                    }
                    .text-button:hover {
                        text-decoration: underline
                    }
                    .icon-print:before {
                        content: "\e716";
                    }
                    [class^="icon-"]:before, [class*=" icon-"]:before {
                        font-family: 'glyphs';
                        font-style: normal;
                        font-weight: normal;
                        line-height: 1;
                    }
                    @font-face {
                        font-family: 'glyphs';
                        {/literal}
                        src:url('{$fonts_path}/glyphs.eot');
                        src:url('{$fonts_path}/glyphs.eot?#iefix') format('embedded-opentype'),
                        url('{$fonts_path}/glyphs.svg#glyphs') format('svg'),
                        url('{$fonts_path}/glyphs.woff') format('woff'),
                        url('{$fonts_path}/glyphs.ttf') format('truetype');
                        {literal}
                        font-weight: normal;
                        font-style: normal;
                    }
                {/literal}
            </style>
    </head>

    <body>
        {assign var="sbrf_settings" value=$order_info.payment_method.processor_params}
        <div class="receipt">
            <table width="720" style="border:#000000 1px solid;" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="220" valign="top" height="250" align="center" style="border-bottom:#000000 1px solid; border-right:#000000 1px solid;"><strong>{__("sbrf_notification")}</strong></td>
                    <td valign="top" style="border-bottom:#000000 1px solid; border-right:#000000 1px solid;">
                        <ul class="receipt-list">
                        <li><strong>{__("sbrf_recepient")}: </strong>{$sbrf_settings.sbrf_recepient_name|unescape}</li>
                        <li><strong>{__("sbrf_kpp")}: </strong>{$sbrf_settings.sbrf_kpp|unescape}&nbsp;&nbsp;&nbsp;<strong>{__("sbrf_inn")}: </strong>{$sbrf_settings.sbrf_inn|unescape}</li>
                        <li><strong>{__("sbrf_okato_code")}: </strong>{$sbrf_settings.sbrf_okato_code|unescape}&nbsp;&nbsp;&nbsp;&nbsp;<strong>{__("sbrf_settlement_account")}: </strong> {$sbrf_settings.sbrf_settlement_account|unescape}&nbsp;&nbsp;</li>
                        {if $sbrf_settings.sbrf_account_id}<li><strong>{__("sbrf_account_id")}: </strong>{$sbrf_settings.sbrf_account_id|unescape}</li>{/if}
                        <li><strong>{__("in")}: </strong> {$sbrf_settings.sbrf_bank|unescape}</li>
                        <li><strong>{__("sbrf_bik")}: </strong>{$sbrf_settings.sbrf_bik|unescape}&nbsp;<strong>{__("sbrf_cor_account")}: </strong>{$sbrf_settings.sbrf_cor_account|unescape}</li>
                        <li><strong>{__("sbrf_kbk")}: </strong> {$sbrf_settings.sbrf_kbk|unescape}</li>
                        <li><strong>{__("sbrf_payment")}: </strong> {$sbrf_settings.sbrf_prefix|unescape} #{$order_info.order_id}</li>
                        <li><strong>{__("sbrf_payer")}: </strong>{$order_info.b_firstname}&nbsp;{$order_info.b_lastname}</li>
                        <li><strong>{__("sbrf_payer_address")}: </strong> {if $order_info.b_zipcode}{$order_info.b_zipcode},&nbsp;{/if}{if $order_info.b_country_descr}{$order_info.b_country_descr},&nbsp;{/if}{if $order_info.b_state_descr}{$order_info.b_state_descr}, &nbsp;{/if}{if $order_info.b_address}{$order_info.b_address}{/if}{if $order_info.b_address_2},&nbsp;{$order_info.b_address_2}{/if}</li>
                        <li><strong>{__("sbrf_payer_inn")}: </strong>____________&nbsp;&nbsp;&nbsp;&nbsp; <strong>{__("sbrf_payer_account_id")}: </strong> ______________</li>
                        <li><strong>{__("sbrf_summ")}: </strong> {$total_print nofilter} &nbsp;&nbsp;&nbsp;&nbsp;<strong>{__("sbrf_bank_summ")}: </strong>_________{__("sbrf_rub")}&nbsp;&nbsp;___&nbsp;&nbsp;{__("sbrf_kop")}</li>
                        </ul>
                        <br /><br /><br />
                        {__("signature")}:________________________ {__("date")}:&quot; __ &quot;&nbsp;_______&nbsp;&nbsp;{$smarty.now|date_format:"%Y"} {__("sbrf_g")} <br /><br />
                    </td>
                </tr>
                <tr>
                    <td width="220" valign="top" height="250" align="center" style="border-bottom:#000000 1px solid; border-right:#000000 1px solid;">&nbsp;<strong>{__("sbrf_receipt_")}</strong></td>
                    <td valign="top" style="border-bottom:#000000 1px solid; border-right:#000000 1px solid;">
                        <ul class="receipt-list">
                        <li><strong>{__("sbrf_recepient")}: </strong>{$sbrf_settings.sbrf_recepient_name|unescape}</li>
                        <li><strong>{__("sbrf_kpp")}: </strong>{$sbrf_settings.sbrf_kpp|unescape}&nbsp;&nbsp;&nbsp;<strong>{__("sbrf_inn")}: </strong>{$sbrf_settings.sbrf_inn|unescape}</li>
                        <li><strong>{__("sbrf_okato_code")}: </strong>{$sbrf_settings.sbrf_okato_code|unescape}&nbsp;&nbsp;&nbsp;&nbsp;<strong>{__("sbrf_settlement_account")}: </strong> {$sbrf_settings.sbrf_settlement_account|unescape}&nbsp;&nbsp;</li>
                        {if $sbrf_settings.sbrf_account_id}<li><strong>{__("sbrf_account_id")}: </strong>{$sbrf_settings.sbrf_account_id|unescape}</li>{/if}
                        <li><strong>{__("in")}: </strong> {$sbrf_settings.sbrf_bank|unescape}</li>
                        <li><strong>{__("sbrf_bik")}: </strong>{$sbrf_settings.sbrf_bik|unescape}&nbsp;<strong>{__("sbrf_cor_account")}: </strong>{$sbrf_settings.sbrf_cor_account|unescape}</li>
                        <li><strong>{__("sbrf_kbk")}: </strong> {$sbrf_settings.sbrf_kbk|unescape}</li>
                        <li><strong>{__("sbrf_payment")}: </strong> {$sbrf_settings.sbrf_prefix|unescape} #{$order_info.order_id}</li>
                        <li><strong>{__("sbrf_payer")}: </strong>{$order_info.b_firstname}&nbsp;{$order_info.b_lastname}</li>
                        <li><strong>{__("sbrf_payer_address")}: </strong> {if $order_info.b_zipcode}{$order_info.b_zipcode},&nbsp;{/if}{if $order_info.b_country_descr}{$order_info.b_country_descr},&nbsp;{/if}{if $order_info.b_state_descr}{$order_info.b_state_descr}, &nbsp;{/if}{if $order_info.b_address}{$order_info.b_address}{/if}{if $order_info.b_address_2},&nbsp;{$order_info.b_address_2}{/if}</li>
                        <li><strong>{__("sbrf_payer_inn")}: </strong>____________&nbsp;&nbsp;&nbsp;&nbsp; <strong>{__("sbrf_payer_account_id")}: </strong> ______________</li>
                        <li><strong>{__("sbrf_summ")}: </strong> {$total_print nofilter} &nbsp;&nbsp;&nbsp;&nbsp;<strong>{__("sbrf_bank_summ")}: </strong>_________{__("sbrf_rub")}&nbsp;&nbsp;___&nbsp;&nbsp;{__("sbrf_kop")}</li>
                        </ul>
                        <br /><br /><br />
                        {__("signature")}:________________________ {__("date")}:&quot; __ &quot;&nbsp;_______&nbsp;&nbsp;{$smarty.now|date_format:"%Y"} {__("sbrf_g")} <br /><br />
                    </td>
                </tr>
            </table>

            <table width="720" style="border: none; margin-top: 20px" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="center" colspan="2" align="left" style="border: none;">
                    <h2>{__("sbrf_qr_title")}</h2>
                    </td>
                </tr>
                <tr>
                    <td valign="center" colspan="2" align="left" style="border: none;">
                    {__("sbrf_payment")}: {__("sbrf_order_payment")} №{$order_info.order_id}<br/>
                    {__("sbrf_summ")}: {$total_print nofilter} 
                    </td>
                </tr>
                <tr>
                    <td width="{$sbrf_settings.sbrf_qr_print_size}" valign="center" height="{$sbrf_settings.sbrf_qr_print_size}" align="center" style="border: none;"><img src="{"orders.get_qr?order_id=`$order_info.order_id`"|fn_url}" alt="BarCode" width="{$sbrf_settings.sbrf_qr_print_size}" height="{$sbrf_settings.sbrf_qr_print_size}" /></td>
                    <td valign="center" style="border: none;">{__("sbrf_qr_info")}</td>
                </tr>
            </table>

        </div>
        <br/>
        <span><i class="icon-print"></i><a class="text-button" href="javascript:window.print()">{__("sbrf_print")}</a></span>
    </body>
</html>
