
{if $spsr_register}
    {include file="common/subheader.tpl" title=__("shippings.spsr.invoice_result_text") target="#spsr_result"}
    <div id="spsr_result" class="collapse in">
        <input type="hidden" name="spsr_check_invoices[order_id]" value="{$order_info.order_id}" />
        <table width="100%" class="table table-middle">
            <thead>
                <tr>    
                    <th class="left">
                        {__("status")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.invoice_number")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.order_number")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.add_courier")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.insurance_sum")}<br/>
                        {__("shippings.spsr.declared_sum")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.cod_goods_sum")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.cod_delivery_sum")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.timestamp")}
                    </th>
                    <th class="left" width="5%">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$spsr_invoice_info item=invoices_data key=key}
                {if $invoices_data.invoice_info.CurState}
                <form action="{""|fn_url}" method="post" name="spsr_form_{$invoices_data.invoice_info.ShipmentNumber}" class="cm-processed-form cm-check-changes">
                    <input type="hidden" name="order_id" value="{$order_info.order_id}" />
                    <input type="hidden" name="selected_section" value="rus_spsr_invoice" />
                    <tr class="cm-row-status" valign="top" height="130">
                        <td class="left">
                            {$invoices_data.invoice_info.CurState}
                        </td>
                        <td class="left">
                            <input type="hidden" name="spsr_bind[invoices][{$invoices_data.invoice_info.ShipRefNum}]" value="{$invoices_data.invoice_info.ShipmentNumber}" />
                            {$invoices_data.invoice_info.ShipmentNumber}
                        </td>
                        <td class="left">
                            {$invoices_data.invoice_info.ShipRefNum}
                        </td>
                        <td class="left">
                            {$invoices_data.courier_key}
                        </td>
                        <td class="left">
                            {$invoices_data.invoice_info.InsuranceSum}<br/>
                            {$invoices_data.invoice_info.DeclaredSum}
                        </td>
                        <td class="left">
                            {$invoices_data.invoice_info.CODGoodsSum}
                        </td>
                        <td class="left">
                            {$invoices_data.invoice_info.CODDeliverySum}
                        </td>
                        <td class="left">
                            {$invoices_data.invoice_info.AgreedDate}
                        </td>
                        <td class="right nowrap">
                            <div class="pull-right">
                                {capture name="tools_list"}
                                    <li>{btn type="list" text=__("shipping.spsr.ticket") href="spsr_invoice.invoice_info?invoice_id=`$invoices_data.invoice_info.ShipmentNumber`"}</li>
                                    <li>{btn type="list" text=__("shipping.spsr.ticket_spsr") href="`$url_invoice`/pdf/invoicepdf.php?fn=FullInvoiceInfo&ICN=`$invoices_data.invoice_info.ShipRefNum`&InvoiceNumber=`$invoices_data.invoice_info.ShipmentNumber`"}</li>
                                {/capture}
                                {dropdown content=$smarty.capture.tools_list}
                            </div>
                        </td>
                    </tr>

                    {if !$invoices_data.courier_key}
                        <tr class="cm-row-status" valign="top" >
                            <td class="left" colspan="9">
                                {if $spsr_couriers}
                                    <div class="control-group">
                                        <label class="control-label" for="spsr_bind_couriers">{__("shipping.spsr.active_couriers")}:</label>
                                        <div class="controls">
                                            <select name="spsr_bind[active_courier]" style="width: 300px;" id="spsr_bind_couriers">
                                                {foreach from=$spsr_couriers item="c"}
                                                    <option value="{$c.Order_ID}||{$c.Order_Owner_ID}||{$c.OrderNumber}">{$c.OrderNumber}/{$c.CityName}, {$c.Address}/{$c.OrderState}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <br />
                                        <div class="buttons-container">
                                            {include file="buttons/button.tpl" but_role="submit" but_meta="btn-primary" but_name="dispatch[orders.bind_order_to_invoice]" but_text=__("shippings.spsr.buttons.bind_order") form="spsr_form_{$invoices_data.invoice_info.ShipmentNumber}"}
                                        </div>
                                    </div>
                                {else}
                                    {include file="common/subheader.tpl" title=__("shippings.spsr.spsr_no_active_couriers") form="spsr_form_`$spsr_register.register_id`"}
                                    <a href="{"spsr_courier.manage"|fn_url}">{__("shippings.spsr.add_courier")}</a>
                                {/if}
                            </td>
                        </tr>
                    {/if}
                </form>
                {/if}
            {/foreach}
            </tbody>
        </table>
    </div>
    <hr />
{/if}

{if $spsr_register_status == 'Y'}
    {include file="common/subheader.tpl" title=__("shippings.spsr.invoice_session_subheader") target="#spsr_registers"}
    <div id="spsr_registers" class="collapse in">
        <p>{__("shippings.spsr.invoice_session_text")}</p>

        <table width="100%" class="table table-middle" >
            <thead>
                <tr>
                    <th class="left">      
                        {__("status")}
                    </th>
                    <th class="left" >       
                        {__("shippings.spsr.register_id")}
                    </th>
                    <th class="left">  
                        {__("shippings.spsr.session_id")}
                    </th>
                    <th class="left">       
                        {__("shippings.spsr.session_owner_id")}
                    </th>
                    <th class="left">  
                        {__("shippings.spsr.timestamp")}     
                    </th>
                </tr>
            </thead>

            {foreach from=$registers item="spsr_register"}
                {if $spsr_register.status == 'S'}
                    <input type="hidden" name="spsr_check_session[{$spsr_register.register_id}][order_id]" value="{$order_info.order_id}" />
                    <input type="hidden" name="spsr_check_session[{$spsr_register.register_id}][status]" value="{$spsr_register.status}" />
                    <input type="hidden" name="spsr_check_session[{$spsr_register.register_id}][register_id]" value="{$spsr_register.register_id}" />
                    <input type="hidden" name="spsr_check_session[{$spsr_register.register_id}][session_id]" value="{$spsr_register.session_id}" />
                    <input type="hidden" name="spsr_check_session[{$spsr_register.register_id}][session_owner_id]" value="{$spsr_register.session_owner_id}" />
                    <input type="hidden" name="spsr_check_session[{$spsr_register.register_id}][timestamp]" value="{$spsr_register.timestamp}" />

                    <tbody>
                        <tr class="cm-row-status" valign="top" >
                            <td class="left">
                                {$spsr_register.status}
                            </td>
                            <td class="left">
                                {$spsr_register.register_id}
                            </td>
                            <td class="left">
                                {$spsr_register.session_id}
                            </td>
                            <td class="left">
                                {$spsr_register.session_owner_id}
                            </td>
                            <td class="left">
                                {$spsr_register.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"|default:"n/a"}
                            </td>
                        </tr>
                    </tbody>
                {/if}
            {/foreach}
        </table>

        <div class="buttons-container">
            {include file="buttons/button.tpl" but_role="submit" but_meta="btn-primary" but_name="dispatch[orders.spsr_check_session]" but_text=__("shippings.spsr.buttons.check_register")}
        </div>
    </div>
    <hr />
{/if}
