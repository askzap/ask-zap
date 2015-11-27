{capture name="mainbox"}
    <form action="{""|fn_url}" method="post" name="spsr_invoices_form" class="cm-hide-inputs">
    {include file="common/pagination.tpl" save_current_page=true save_current_url=true}
        {if $invoices}
            <table width="100%" class="table table-middle" >
                <thead>
                    <tr>    
                        <th class="left">
                            {__("status")}
                        </th>
                        <th class="left" >
                            {__("shippings.spsr.invoice_number")}
                        </th>
                        <th class="left" >
                            {__("order_id")}
                        </th>
                        <th class="left">
                            {__("shippings.spsr.add_courier")}
                        </th>
                        <th class="left">
                            {__("shippings.spsr.invoice_plat_type")}
                        </th>
                        <th class="left">
                            {__("shippings.spsr.date_create")}
                        </th>
                        <th class="left">
                            {__("shippings.spsr.agreed_send")}
                        </th>
                        <th class="left">
                            {__("shippings.spsr.insurance_sum")}<br/>
                            {__("shippings.spsr.declared_sum")}
                        </th>
                        <th class="left" width="5%">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$invoices item=invoice key=key}
                    <tr class="cm-row-status" valign="top" >
                        <td class="left">
                            {$invoice.invoice.CurState}
                        </td>
                        <td class="left">
                            {$invoice.invoice.InvoiceNumber}
                        </td>
                        <td class="left">
                            {if $invoice.invoice.order_id}
                                <a href="{"orders.details?order_id=`$invoice.invoice.order_id`"|fn_url}" class="underlined">#{$invoice.invoice.order_id}<a>
                            {/if}
                        </td>
                        <td class="left">
                            {$invoice.invoice.courier_key}
                        </td>
                        <td class="left">
                            {$invoice.invoice.Payer}
                        </td>
                        <td class="left">
                            {$invoice.invoice.Receipt_Date}
                        </td>
                        <td class="left">
                            {$invoice.invoice.DeliveryDateWaitFor}
                        </td>
                        <td class="left">
                            {$invoice.invoice.InsuranceCost}<br/>
                            {$invoice.invoice.DeclaredCost}
                        </td>
                        <td class="left">
                            <div class="pull-right">
                                {capture name="tools_list"}
                                    <li>{btn type="list" text=__("shipping.spsr.ticket") href="spsr_invoice.invoice_info?invoice_id=`$invoice.invoice.InvoiceNumber`"}</li>
                                    <li>{btn type="list" text=__("shipping.spsr.ticket_spsr") href="`$url_invoice`/pdf/invoicepdf.php?fn=FullInvoiceInfo&ICN=`$invoice.invoice.GCInvoiceNumber`&InvoiceNumber=`$invoice.invoice.InvoiceNumber`"}</li>
                                {/capture}
                                {dropdown content=$smarty.capture.tools_list}
                            </div>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}
        <hr />
    {include file="common/pagination.tpl"}
    </form>

    {capture name="buttons"}
        {capture name="tools_list"}
            {if $invoices && $period.period == "A"}
                <li>{btn type="delete_selected" dispatch="dispatch[spsr_courier.m_delete]" form="spsr_courier_form"}</li>
            {/if}
        {/capture}
        {dropdown content=$smarty.capture.tools_list}
    {/capture}
{/capture}

{capture name="sidebar"}
    {include file="addons/rus_spsr/views/components/invoice_search_form.tpl" period=$period.period status=$period.status search=$period}
{/capture}

{include file="common/mainbox.tpl" title=__("shippings.spsr.invoices_title") content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons buttons=$smarty.capture.buttons content_id="manage_invoices" sidebar=$smarty.capture.sidebar}
