<div id="content_spsr_information" >
{if $spsr_info}
<table class="table table-width">
    <thead>
    <tr>
        <th>{__("shippings.spsr.invoice_number")}</th>
        <th>{__("status")}</th>
        <th class="left">       
            {__("shippings.spsr.invoice_delivery_date")}<br/>
        </th>
        <th class="left">       
            {__("shippings.spsr.insurance_sum")}<br/>
        </th>
        <th class="left">       
            {__("shippings.spsr.cod_delivery_sum")}
        </th>
        <th class="left">     
            {__("shippings.spsr.cod_goods_sum")}  
        </th>
    </tr>
    </thead>
    {foreach from=$spsr_info item="invoice" key="key"}
    {cycle values=",class=\"table-row\"" name="class_cycle" assign="_class"}
    <tr {$_class} style="vertical-align: top;">
        <td>
            {$invoice.invoice_info.ShipmentNumber}
        </td>
        <td class="nowrap">
            {$invoice.invoice_info.CurState}
        </td>
        <td class="nowrap">
            {$invoice.invoice_info.AgreedDate}
        </td>
        <td class="nowrap">
            {include file="common/price.tpl" value=$invoice.invoice_info.InsuranceSum}
        </td>
        <td class="nowrap">
            {include file="common/price.tpl" value=$invoice.invoice_info.CODDeliverySum}
        </td>
        <td class="">
            <strong>{include file="common/price.tpl" value=$invoice.invoice_info.CODGoodsSum}</strong>
        </td> 
    </tr>
    {/foreach}
</table>
{/if}
<!--content_spsr_information--></div>
