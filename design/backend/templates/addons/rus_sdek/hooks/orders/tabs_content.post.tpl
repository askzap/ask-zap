{if !empty($data_shipments)}
    <div id="content_sdek_orders">
        {foreach from=$data_shipments item=shipment key="shipment_id"}
            <form action="{""|fn_url}" method="post" name="sdek_form_{$shipment_id}" class="cm-processed-form cm-check-changes">
                <input type="hidden" name="order_id" value="{$order_id}" />
                <input type="hidden" name="add_sdek_info[{$shipment_id}][Order][RecCityCode]" value="{$rec_city_code}" />
                <input type="hidden" name="add_sdek_info[{$shipment_id}][Order][SendCityCode]" value="{$shipment.send_city_code}" />
                <div class="control-group">
                    <div class="control">
                        <h4>{__("shipment")}: <a class="underlined" href="{"shipments.details?shipment_id=`$shipment_id`"|fn_url}" target="_blank"><span>#{$shipment_id} ({__("details")})</span></a></h4>
                    </div>
                    <table width="100%" class="table table-middle">
                    <thead>
                    <tr>    
                        <th width="35%" class="shift-left">{__("sdek.sdek_address_shipping")}</th>
                        <th width="20%">{__("sdek.sdek_tariff")}</th>
                        <th width="25%">
                            {if !empty($shipment.register_id)}
                                {if !empty($shipment.notes)}
                                    {__("sdek.sdek_comment")}
                                {/if}
                            {else}
                                {__("sdek.sdek_comment")}
                            {/if}
                        </th>
                        <th width="5%">{if !$shipment.register_id}{__("shipping_cost")}{/if}</th>
                        <th width="15%">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="cm-row-status" valign="top" >
                        <td class="{$no_hide_input}">
                            {if !empty($shipment.register_id)}
                                {$shipment.address}
                            {else}
                                {if !empty($shipment.rec_address)}
                                    <input type="hidden" name="add_sdek_info[{$shipment_id}][Address][Street]" value="{$shipment.rec_address}" />
                                    <input type="hidden" name="add_sdek_info[{$shipment_id}][Address][House]" value="-" />
                                    <input type="hidden" name="add_sdek_info[{$shipment_id}][Address][Flat]" value="-" />
                                    {$shipment.rec_address}
                                {else}
                                    <select name="add_sdek_info[{$shipment_id}][Address][PvzCode]" class="input-slarge" id="item_modifier_type">
                                        {foreach from=$shipment.offices item=address_shipping}
                                            <option value="{$address_shipping.Code}" {if $address_shipping.Code == $sdek_pvz}selected="selected"{/if}>{$address_shipping.Address}</option>
                                        {/foreach}
                                    </select>
                                {/if}
                            {/if}
                        </td>
                        <td class="left nowrap {$no_hide_input}">
                            <input type="hidden" name="add_sdek_info[{$shipment_id}][Order][TariffTypeCode]" value="{$shipment.tariff_id}" />
                            {$shipment.shipping}
                        </td>
                        <td class="left nowrap">
                            {if !empty($shipment.register_id)}
                                {$shipment.notes}
                            {else}
                                <textarea class="input-textarea checkout-textarea" name="add_sdek_info[{$shipment_id}][Order][Comment]" cols="60" rows="3" value="">{$shipment.comments}</textarea>
                            {/if}
                        </td>
                        <td class="right nowrap">
                            {if $shipment.register_id}
                                <div class="pull-right">
                                    {capture name="tools_list"}
                                            <li>{btn type="list" text=__("sdek.update_status") dispatch="dispatch[orders.sdek_order_status]" form="sdek_form_`$shipment_id`"}</li>
                                            <li>{btn type="list" text=__("delete") dispatch="dispatch[orders.sdek_order_delete]" form="sdek_form_`$shipment_id`"}</li>
                                    {/capture}
                                    {dropdown content=$smarty.capture.tools_list}
                                </div>
                            {else}
                                <input type="text" name="add_sdek_info[{$shipment_id}][Order][DeliveryRecipientCost]" value="{$shipment.delivery_cost}" class="input-mini" size="6"/>
                            {/if}
                        </td>
                        <td class="right nowrap">
                            {if !$shipment.register_id}
                                {include file="buttons/button.tpl" but_role="submit" but_name="dispatch[orders.sdek_order_delivery]" but_text=__("send") but_target_form="sdek_form_`$shipment_id`"}
                            {else}
                                {$ticket_href = "{"orders.sdek_get_ticket?order_id=`$order_info.order_id`&shipment_id=`$shipment_id`"|fn_url}"}

                                {include file="buttons/button.tpl" but_role="submit-link" but_href=$ticket_href but_text=__("sdek.receipt_order") but_meta="cm-no-ajax"}
                            {/if}
                        </td>
                    </tr>
                    </tbody>
                    </table>

                    {if !empty($shipment.sdek_status)}
                        {include file="common/subheader.tpl" title=__("shippings.sdek.status_title") target="#status_information_{$shipment_id}"}
                        <div id="status_information_{$shipment_id}" class="collapse">
                            <table width="100%" class="table table-middle" >
                            <tr>
                                <td>
                                    {__("sdek.lang_status_code")}
                                </td>
                                <td>
                                    {__("sdek.date")}
                                </td>
                                <td>
                                    {__("sdek.lang_status_order")}
                                </td>
                                <td>
                                    {__("sdek.lang_city")}
                                </td>
                            </tr>
                            {foreach from=$shipment.sdek_status item=d_status}
                                <tr>
                                    <td>
                                        {$d_status.id}
                                    </td>
                                    <td>
                                        {$d_status.date}
                                    </td>
                                    <td>
                                        {$d_status.status}
                                    </td>
                                    <td>
                                        {$d_status.city}
                                    </td>
                                </tr>
                            {/foreach}
                            </table>
                        </div>
                    {/if}
                </div>
            </form>
            <hr />
        {/foreach}
    </div>
{/if}
