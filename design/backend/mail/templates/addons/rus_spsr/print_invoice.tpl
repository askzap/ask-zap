<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head></head>

<body>
{if $invoice_info}
    {literal}
    <style type="text/css" media="screen,print">
        body,p,div {
            color: #000000;
            font: 12px Arial;
        }
        body {
            padding: 0;
            margin: 0;
        }
        a, a:link, a:visited, a:hover, a:active {
            color: #000000;
            text-decoration: underline;
        }
        a:hover {
            text-decoration: none;
        }
    </style>
    <style media="print">
        body {
            background-color: #ffffff;
        }
        .scissors {
            display: none;
        }
    </style>
    {/literal}
    {include file="common/scripts.tpl"}
    {if !$company_placement_info}
        {assign var="company_placement_info" value=$order_info.company_id|fn_get_company_placement_info}
    {/if}

    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f6f8; height: 100%;">
    <tr>
        <td align="center" style="width: 100%; height: 100%; padding: 24px 0;">
        <div style="background-color: #ffffff; border: 1px solid #e6e6e6; margin: 0px auto; padding: 0px 44px 0px 46px; width: 510px; text-align: left;">
            {assign var="profile_fields" value='I'|fn_get_profile_fields}
            {if $invoice_info.receiver}
                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding-top: 32px;">
                <tr valign="top">
                    <td width="100%" align="center" style="border-bottom: 1px dashed #000000; padding-bottom: 20px;">
                        <h3 style="font: bold 17px Tahoma; padding: 0px 0px 3px 1px; margin: 0px;">{__("ship_to")}:</h3>
                        {if $invoice_info.receiver.CompanyName}
                        <p style="margin: 2px 0px 3px 0px;">
                            {$invoice_info.receiver.CompanyName}
                        </p>
                        {/if}
                        {if $invoice_info.receiver.ContactName}
                        <p style="margin: 2px 0px 3px 0px;">
                            {$invoice_info.receiver.ContactName}
                        </p>
                        {/if}
                        {if $invoice_info.receiver.PostCode || $invoice_info.receiver.Country || $invoice_info.receiver.Region}
                        <p style="margin: 2px 0px 3px 0px;">
                            {$invoice_info.receiver.PostCode} {$invoice_info.receiver.Country} {$invoice_info.receiver.Region}
                        </p>
                        {/if}
                        {if $invoice_info.receiver.City || $invoice_info.receiver.Address}
                        <p style="margin: 2px 0px 3px 0px;">
                            {$invoice_info.receiver.City} {$invoice_info.receiver.Address}
                        </p>
                        {/if}
                        {if $invoice_info.receiver.Address}
                        <p style="margin: 2px 0px 3px 0px;">
                            {$invoice_info.receiver.Address}
                        </p>
                        {/if}
                        {include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.S}
                    </td>
                </tr>
                <tr valign="top" class="scissors">
                    <td width="100%" style="padding-left: 20px;">
                        <img src="{$images_dir}/scissors.gif" border="0" />
                    </td>
                </tr>
                </table>
            {/if}
            {* Customer info *}

            <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>
                <td style="width: 50%; padding: 14px 0px 0px 2px;">
                    <h2 style="font: bold 12px Arial; margin: 0px 0px 3px 0px;">{$invoice_info.shipper.CompanyName}</h2>
                    {$invoice_info.shipper.PostCode}  {$invoice_info.shipper.Country}<br />
                    {$invoice_info.shipper.Region}<br />
                    {$invoice_info.shipper.City}<br />
                    {$invoice_info.shipper.Address}<br />
                    <table cellpadding="0" cellspacing="0" border="0">
                    <tr valign="top">
                        <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px;    white-space: nowrap;">{__("shippings.spsr.fio")}:</td>
                        <td width="100%">{$invoice_info.shipper.ContactName}</td>
                    </tr>
                    {if $invoice_info.shipper.Phone}
                    <tr valign="top">
                        <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px;    white-space: nowrap;">{__("phone1_label")}:</td>
                        <td width="100%">{$invoice_info.shipper.Phone}</td>
                    </tr>
                    {/if}
                    {if $company_placement_info.company_website}
                    <tr valign="top">
                        <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("web_site")}:</td>
                        <td width="100%">{$company_placement_info.company_website}</td>
                    </tr>
                    {/if}
                    {if $company_placement_info.company_orders_department}
                    <tr valign="top">
                        <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("email")}:</td>
                        <td width="100%"><a href="mailto:{$company_placement_info.company_orders_department}">{$company_placement_info.company_orders_department|replace:",":"<br>"|replace:" ":""}</a></td>
                    </tr>
                    {/if}
                    </table>
                </td>
                <td style="padding-top: 14px;" valign="top">
                    <h2 style="font: bold 17px Tahoma; margin: 0px;">{__("packing_slip_for_order")}&nbsp;#{$invoice_info.ship_ref_num}</h2>
                    <table cellpadding="0" cellspacing="0" border="0">
                            <tr valign="top">
                                <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("order_id")}:</td>
                                <td>{$invoice_info.order_id}</td>
                            </tr>
                            <tr valign="top">
                                <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("shippings.spsr.shipment_number")}:</td>
                                <td>{$invoice_info.invoice_number}</td>
                            </tr>
                            <tr valign="top">
                                <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("shippings.spsr.status")}:</td>
                                <td>{$invoice_info.invoice_info.CurState}</td>
                            </tr>
                            <tr valign="top">
                                <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("shippings.spsr.add_courier")}:</td>
                                <td>{$invoice_info.invoice_info.OrderNumber}</td>
                            </tr>
                            <tr valign="top">
                                <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("order_date")}:</td>
                                <td>{$invoice_info.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
                            </tr>
                            <tr valign="top">
                                <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("shipment_date")}:</td>
                                <td>{$invoice_info.invoice_info.AgreedDate}</td>
                            </tr>
                    </table>
                </td>
            </tr>
            </table>
            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 20px 0px 24px 0px;">
            <tr valign="top">
                {if $profile_fields.B}
                <td width="54%">
                    <h3 style="font: bold 17px Tahoma; padding: 0px 0px 3px 1px; margin: 0px;">{__("bill_to")}:</h3>
                    {if $order_info.b_firstname || $order_info.b_lastname}
                    <p style="margin: 2px 0px 3px 0px;">
                        {$order_info.b_firstname} {$order_info.b_lastname}
                    </p>
                    {/if}
                    {if $order_info.b_address || $order_info.b_address_2}
                    <p style="margin: 2px 0px 3px 0px;">
                        {$order_info.b_address} {$order_info.b_address_2}
                    </p>
                    {/if}
                    {if $order_info.b_city || $order_info.b_state_descr || $order_info.b_zipcode}
                    <p style="margin: 2px 0px 3px 0px;">
                        {$order_info.b_city}{if $order_info.b_city && ($order_info.b_state_descr || $order_info.b_zipcode)},{/if} {$order_info.b_state_descr} {$order_info.b_zipcode}
                    </p>
                    {/if}
                    {if $order_info.b_country_descr}
                    <p style="margin: 2px 0px 3px 0px;">
                        {$order_info.b_country_descr}
                    </p>
                    {/if}
                    {include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.B}
                </td>
                {/if}
                <td width="54%">
                    <h3 style="font: bold 17px Tahoma; padding: 0px 0px 3px 1px; margin: 0px;">{__("ship_to")}:</h3>
                    {if $invoice_info.receiver.CompanyName}
                    <p style="margin: 2px 0px 3px 0px;">
                        {$invoice_info.receiver.CompanyName}
                    </p>
                    {/if}
                    {if $invoice_info.receiver.ContactName}
                    <p style="margin: 2px 0px 3px 0px;">
                        {$invoice_info.receiver.ContactName}
                    </p>
                    {/if}
                    {if $invoice_info.receiver.PostCode || $invoice_info.receiver.Country}
                    <p style="margin: 2px 0px 3px 0px;">
                        {$invoice_info.receiver.PostCode}, {$invoice_info.receiver.Country}
                    </p>
                    {/if}
                    {if $invoice_info.receiver.City || $invoice_info.receiver.Region}
                    <p style="margin: 2px 0px 3px 0px;">
                        {$invoice_info.receiver.Region}, {$invoice_info.receiver.City}
                    </p>
                    {/if}
                    {if $invoice_info.receiver.Address}
                    <p style="margin: 2px 0px 3px 0px;">
                        {$invoice_info.receiver.Address}
                    </p>
                    {/if}
                    {if $invoice_info.receiver.Phone}
                    <p style="margin: 2px 0px 3px 0px;">
                        {$invoice_info.receiver.Phone}
                    </p>
                    {/if}
                    {include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.S}
                </td>
            </tr>
            </table>
            {* Customer info *}
        
            <table cellpadding="0" cellspacing="0" border="0">
            <tr valign="top">
                <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("status")}:</td>
                <td width="100%">{include file="common/status.tpl" status=$order_info.status display="view"}</td>
            </tr>
            <tr valign="top">
                <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("payment_method")}:</td>
                <td valign="bottom">{$order_info.payment_method.payment|default:" - "}</td>
            </tr>
            </table>
            {* Ordered products *}

            {foreach from=$pieces item="piece"}
                <table cellpadding="0" cellspacing="0" border="0" style="background-color: #fff; margin-top: 20px;">
                    <tr>
                        <td colspan="8">
                            {assign var="code" value=$piece.barcode}
                            <img src="{"orders.spsr_barcode?id=`$code`&width=`$info_barcode.width`&height=`$info_barcode.height`&type=`$info_barcode.type`"|fn_url}" alt="BarCode" width="{$info_barcode.width}" height="{$info_barcode.height}">
                        </td>
                    </tr>
                    <tr valign="top">
                        <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("shippings.spsr.piece")}:</td>
                        <td valign="bottom">
                            {$piece.item_id}
                        </td>
                        <td style=" padding-right: 30px; "></td>
                        <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("size")}:</td>
                        <td valign="bottom">
                            {$piece.data.length} / {$piece.data.width} / {$piece.data.height}
                        </td>
                        <td style=" padding-right: 30px; "> </td>
                        <td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{__("weight")}:</td>
                        <td valign="bottom">
                            {$piece.data.weight}
                        </td>
                    </tr>
                </table>

                <table width="100%" cellpadding="0" cellspacing="1" style="background-color: #dddddd;">
                <tr>
                    <th width="70%" style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;">{__("product")}</th>
                    <th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;">{__("sku")}</th>
                    <th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;">{__("quantity")}</th>
                    <th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;">{__("price")}</th>
                </tr>
                {foreach from=$piece.data.products item="oi"}
                    <tr>
                        <td style="padding: 5px 10px; background-color: #ffffff;">
                            {$oi.product|default:__("deleted_product") nofilter}
                        </td>
                        <td style="padding: 5px 10px; background-color: #ffffff; text-align: left;">{$oi.product_code}</td>
                        <td style="padding: 5px 10px; background-color: #ffffff; text-align: center;">{$oi.amount}</td>
                        <td style="padding: 5px 10px; background-color: #ffffff; text-align: center;">{$oi.price}</td>
                    </tr>
                {/foreach}
                </table>
            {/foreach}    
            {* Ordered products *}

            {if $order_info.notes}
                <div style="float: left; padding-top: 20px;"><strong>{__("notes")}:</strong></div>
                <div style="padding-left: 7px; padding-bottom: 15px; overflow-x: auto; clear: both; width: 505px; height: 100%; padding-bottom: 20px; overflow-y: hidden;">{$order_info.notes|wordwrap:90:"\n":true|nl2br}</div>
            {/if}
         </div>
        </td>
    </tr>
    </table>
{/if}
</body>
</html>