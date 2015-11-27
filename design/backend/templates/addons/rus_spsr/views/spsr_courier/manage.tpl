{capture name="mainbox"}
    <form action="{""|fn_url}" method="post" name="spsr_courier_form" class="cm-hide-inputs">
    <input type="hidden" name="fake" value="1" />
    {include file="common/pagination.tpl" save_current_page=true save_current_url=true}
        {if $couriers}
            <table width="100%" class="table table-middle">
            <thead>
            <tr>    
                {if $period.period == "A"}
                <th width="1%">       
                    {include file="common/check_items.tpl" class="cm-no-hide-input"}
                </th>
                {/if}
                <th width="15%" class="shift-left"><b>{__("ID")}</b>
                    {if $period.period == "A"}
                        <span class="small"><br/>Session ID<br/>
                        Session owner id</span>
                    {/if}
                </th>
                <th width="15%" class="shift-left">{__("status")}</th>
                <th width="20%">{__("date")}</th>
                <th width="20%">
                    {__("period")} {__("from")} {__("to")}
                </th>
                <th width="20%">
                    {__("address")}
                </th>
                <th width="5%">&nbsp;</th>
            </tr>
            </thead>
            {foreach from=$couriers item=c}
            <tbody>
            <tr class="cm-row-status" valign="top" >
                {if $period.period == "A"}
                <td class="left {$no_hide_input}">
                    <input type="checkbox" name="courier_ids[]" value="{$c.Order_ID}||{$c.Order_Owner_ID}" class="cm-item cm-no-hide-input" />
                </td>
                {/if}
                <td class="{$no_hide_input}">
                    {$c.OrderNumber}
                    {if $period.period == "A"}
                     <span class="small"><br/>{$c.Order_ID}</br>
                        {$c.Order_Owner_ID}</br>
                    </span>
                    {/if}
                </td>
                <td class="{$no_hide_input}">
                    {$c.OrderState}<br/> 
                </td>
                <td class="left nowrap {$no_hide_input}">
                   {$c.DateOfCreate}
                </td>
                <td class="left nowrap {$no_hide_input}">
                    {$c.PlanningDT_From}</br>
                    {$c.PlanningDT_To}
                </td>
                <td class="left nowrap {$no_hide_input}">
                    {$c.FIO}</br>
                    {if $period.period == "A"}
                        {$c.CityName}</br>
                    {/if}
                    {$c.courieress}
                </td>
                <td class="center nowrap">
                    {if $period.period == "A"}
                        {capture name="tools_list"}
                                <li>{btn type="list" class="cm-post cm-confirm" text=__("delete") href="spsr_courier.delete?courier_id=`$c.Order_ID`&courier_owner_id=`$c.Order_Owner_ID`"}</li>
                        {/capture}
                        <div class="hidden-tools right">
                            {dropdown content=$smarty.capture.tools_list}
                        </div>
                    {/if}
                </td>
            </tr>
            {/foreach}
            </tbody>
            </table>
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}
    {include file="common/pagination.tpl"}
    </form>

    {capture name="tools"}
        {capture name="add_new_picker"}
            <form action="{""|fn_url}" method="post" name="add_courier_form" class="form-horizontal form-edit">
            <div class="cm-j-tabs">
                <ul class="nav nav-tabs">
                    <li id="tab_new_courier" class="cm-js active"><a>{__("general")}</a></li>
                </ul>
            </div>
            <div class="cm-tabs-content">
            <fieldset>
                {if spsr_services}
                    <div class="control-group">
                        <label class="control-label" for="spsr_service">{__("shippings.spsr.service_label")}:</label>
                        <div class="controls">
                            <select name="spsr_courier[service]" id="spsr_service">
                                {foreach from=$spsr_services item="service"}
                                    <option {if $service.ID == 28}selected="selected"{/if} value="{$service.ID}">{$service.Name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                {/if}

                {if $addr_list}
                    <div class="control-group">
                        <label class="control-label" for="spsr_sbor_addr">{__("shippings.spsr.sbor_addr_label")}:</label>
                        <div class="controls">
                            <select name="spsr_courier[sbor_addr]" id="spsr_sbor_addr">
                                {foreach from=$addr_list item="addr"}
                                    <option  value="{$addr.SborAddr_ID}||{$addr.SborAddr_Owner_ID}||{$addr.FIO}||{$addr.CityName1}">{$addr.Organization}/{$addr.CityName1}/{$addr.Address}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                {/if}

                <div class="control-group">
                    <label class="control-label" for="spsr_necesserydate">{__("shippings.spsr.necesserydate")}:</label>
                    <div class="controls">
                        {include file="common/calendar.tpl" date_id="spsr_necesserydate" date_name="spsr_courier[necesserydate]" date_val="{$smarty.const.TIME}" start_year=$settings.Company.company_start_year}
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="spsr_necesserytime">{__("shippings.spsr.necesserytime")}:</label>
                    <div class="controls">
                        <select name="spsr_courier[necesserytime]" id="spsr_necesserytime">
                            <option value="AM">{__("shippings.spsr.invoice_delivery_time.am")}</option>
                            <option value="PM">{__("shippings.spsr.invoice_delivery_time.pm")}</option>
                            <option value="FM">{__("shippings.spsr.invoice_delivery_time.wd")}</option>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="spsr_description">{__("description")}:</label>
                    <div class="controls">
                    <input id="spsr_description" type="text" name="spsr_courier[description]" size="100" value="" class="input-large"/>
                    </div>
                </div>  

                <div class="control-group">
                    <label class="cm-required control-label" for="spsr_placescount">{__("shippings.spsr.placescount")}:</label>
                    <div class="controls">
                    <input id="spsr_placescount" type="text" name="spsr_courier[placescount]" size="100" value="0"/>
                    </div>
                </div>  

                <div class="control-group">
                    <label class="cm-required control-label" for="spsr_weight">{__("weight")}:</label>
                    <div class="controls">
                    <input id="spsr_weight" type="text" name="spsr_courier[weight]" size="100" value="0"/>
                    </div>
                </div>  
                <div class="control-group">
                    <label class="cm-required control-label" for="spsr_length">{__("length")}:</label>
                    <div class="controls">
                    <input id="spsr_length" type="text" name="spsr_courier[length]" size="100" value="0"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="cm-required control-label" for="spsr_width">{__("width")}:</label>
                    <div class="controls">
                    <input id="spsr_width" type="text" name="spsr_courier[width]" size="100" value="0"/>
                    </div>
                </div>  
                <div class="control-group">
                    <label class="cm-required control-label" for="spsr_depth">{__("height")}:</label>
                    <div class="controls">
                    <input id="spsr_depth" type="text" name="spsr_courier[depth]" size="100" value="0"/>
                    </div>
                </div> 
            </fieldset>
            </div>

            <div class="buttons-container">
                {include file="buttons/save_cancel.tpl" create=true but_name="dispatch[spsr_courier.update]" cancel_action="close"}
            </div>
        </form>
        {/capture}
    {/capture}

    {capture name="adv_buttons"}
        {include file="common/popupbox.tpl" id="new_courier" action="spsr_courier.add" text=__("shippings.spsr.add_courier") content=$smarty.capture.add_new_picker title=__("shippings.spsr.add_courier") act="general" icon="icon-plus"}
    {/capture}

    {capture name="buttons"}
        {capture name="tools_list"}
            {if $couriers && $period.period == "A"}
                <li>{btn type="delete_selected" dispatch="dispatch[spsr_courier.m_delete]" form="spsr_courier_form"}</li>
            {/if}
        {/capture}
        {dropdown content=$smarty.capture.tools_list}
    {/capture}
{/capture}

{capture name="sidebar"}
    {include file="addons/rus_spsr/views/components/couriers_search_form.tpl" period=$period.period search=$period}
{/capture}

{include file="common/mainbox.tpl" title=__("shippings.spsr.couriers_title") content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons buttons=$smarty.capture.buttons content_id="manage_couriers" sidebar=$smarty.capture.sidebar}
