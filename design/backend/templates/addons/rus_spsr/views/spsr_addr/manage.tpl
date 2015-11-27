{capture name="mainbox"}
    <form action="{""|fn_url}" method="post" name="spsr_addr_form" class="cm-hide-inputs">
    {include file="common/pagination.tpl" save_current_page=true save_current_url=true}
        {if $addr_list}
            <table width="100%" class="table table-middle">
                <thead>
                <tr>    
                    <th width="1%">
                        {include file="common/check_items.tpl" class="cm-no-hide-input"}
                    </th>
                    <th width="15%" class="shift-left">{__("company")}</th>
                    <th width="20%">{__("shippings.spsr.fio")}</th>
                    <th width="40%">{__("address")}</th>
                    <th width="19%">{__("phone")}</th>
                    <th width="5%">&nbsp;</th>
                </tr>
                </thead>
                {foreach from=$addr_list item=n}
                <tbody>
                <tr class="cm-row-status" valign="top" >
                    <td class="left {$no_hide_input}">
                        <input type="checkbox" name="addr_ids[]" value="{$n.SborAddr_ID}||{$n.SborAddr_Owner_ID}" class="cm-item cm-no-hide-input" />
                    </td>
                    <td class="{$no_hide_input}">
                        <b>{$n.Organization}</b>
                    </td>
                    <td class="{$no_hide_input}">
                        {$n.FIO}
                    </td>
                    <td class="left nowrap {$no_hide_input}">
                       {$n.PostCode}, {$n.CityName}, {$n.Address}<br/>
                    </td>
                    <td class="left nowrap {$no_hide_input}">
                       {$n.Phone}{if $n.AddPhone}, {$n.AddPhone} {/if}
                    </td>
                    <td class="center nowrap">
                        {capture name="tools_list"}
                            <li>{btn type="list" class="cm-post cm-confirm" text=__("delete") href="spsr_addr.delete?addr_id=`$n.SborAddr_ID`&addr_owner_id=`$n.SborAddr_Owner_ID`"}</li>
                        {/capture}
                        <div class="hidden-tools right">
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
    {include file="common/pagination.tpl"}
    </form>

    {capture name="tools"}
        {capture name="add_new_picker"}
        <form action="{""|fn_url}" method="post" name="add_addr_form" class="form-horizontal form-edit">
            <div class="cm-j-tabs">
                <ul class="nav nav-tabs">
                    <li id="tab_new_addr" class="cm-js active"><a>{__("general")}</a></li>
                </ul>
            </div>

            <div class="cm-tabs-content">
            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="spsr_city_name">{__("city")}:</label>
                    <div class="controls">
                        <input id="spsr_city_name" type="text" name="create_address[city_name]" size="30" value="{if $spsr_data.service_params.from_city_name}{$spsr_data.service_params.from_city_name}{else}{$create_address.city_name}{/if}"/>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="spsr_address">{__("address")}:</label>
                    <div class="controls">
                        <input id="spsr_address" type="text" name="create_address[address]" size="30" value="{$create_address.address}"/>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="spsr_fio">{__("shippings.spsr.fio")}:</label>
                    <div class="controls">
                        <input id="spsr_fio" type="text" name="create_address[fio]" size="30" value="{$create_address.fio}"/>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="spsr_organization">{__("company")}:</label>
                    <div class="controls">
                        <input id="spsr_organization" type="text" name="create_address[organization]" size="30" value="{$create_address.organization}"/>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="spsr_phone">{__("phone")}:</label>
                    <div class="controls">
                        <input id="spsr_phone" type="text" name="create_address[phone]" size="30" value="{$create_address.phone}"/>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="spsr_addphone">{__("phone")} 2:</label>
                    <div class="controls">
                        <input id="spsr_addphone" type="text" name="create_address[addphone]" size="30" value="{$create_address.addphone}"/>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="spsr_postcode">{__("shipping.spsr.postcode")}:</label>
                    <div class="controls">
                        <input id="spsr_postcode" type="text" name="create_address[postcode]" size="30" value="{$create_address.postcode}"/>
                    </div>
                </div>
            </fieldset>
            </div>

            <div class="buttons-container">
                {include file="buttons/save_cancel.tpl" create=true but_name="dispatch[spsr_addr.update]" cancel_action="close"}
            </div>
        </form>
        {/capture}
    {/capture}

    {capture name="adv_buttons"}
        {include file="common/popupbox.tpl" id="new_addr" action="spsr_addr.add" text=__("shippings.spsr.add_addr") content=$smarty.capture.add_new_picker title=__("shippings.spsr.add_addr") act="general" icon="icon-plus"}
    {/capture}

    {capture name="buttons"}
        {capture name="tools_list"}
            {if $addr_list}
                <li>{btn type="delete_selected" dispatch="dispatch[spsr_addr.m_delete]" form="spsr_addr_form"}</li>
            {/if}
        {/capture}
        {dropdown content=$smarty.capture.tools_list}
    {/capture}
{/capture}

{include file="common/mainbox.tpl" title=__("shippings.spsr.addrs_title") content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons buttons=$smarty.capture.buttons content_id="manage_addrs"}
