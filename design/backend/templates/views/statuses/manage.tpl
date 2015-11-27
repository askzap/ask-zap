{script src="js/tygh/tabs.js"}

{capture name="mainbox"}

<div class="items-container" id="statuses_list">
{if $statuses}
<table class="table table-middle table-objects">
{foreach from=$statuses item="s" key="key"}
    {if $s.is_default !== "Y"}
        {assign var="cur_href_delete" value="statuses.delete?status=`$s.status`&type=`$type`"}
    {else}
        {assign var="cur_href_delete" value=""}
    {/if}

    {capture name="tool_items"}
        {hook name="statuses:list_extra_links"}{/hook}
    {/capture}

    {capture name="extra_data"}
        {hook name="statuses:extra_data"}{/hook}
    {/capture}

    {include file="common/object_group.tpl" id=$s.status|lower text=$s.description href="statuses.update?status=`$s.status`&type=`$type`" href_delete=$cur_href_delete delete_target_id="statuses_list" header_text="{__("editing_status")}: `$s.description`" no_table=true nostatus=true tool_items=$smarty.capture.tool_items extra_data=$smarty.capture.extra_data}

{/foreach}
</table>
{else}
    <p class="no-items">{__("no_data")}</p>
{/if}
<!--statuses_list--></div>

{capture name="adv_buttons"}
    {capture name="add_new_picker"}
        {include file="views/statuses/update.tpl" status_data=[]}
    {/capture}
    {capture name="tools_list"}
        {hook name="statuses:button"}{/hook}
        {if !("ULTIMATE"|fn_allowed_for && $runtime.company_id)}
            {if $smarty.request.type == 'G'}
                {assign var="icon" value=""}
            {else}
                {assign var="icon" value="icon-plus"}
            {/if}
            <li>{include file="common/popupbox.tpl" id="add_new_status"  action="statuses.add" text=__("new_status") content=$smarty.capture.add_new_picker link_text=__("add_status") act="link"}</li>
        {/if}
    {/capture}
    {dropdown content=$smarty.capture.tools_list icon="icon-plus" no_caret=true placement="right"}

    {hook name="statuses:adv_buttons"}{/hook}
{/capture}

{/capture}
{include file="common/mainbox.tpl" title=$title content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons select_languages=true}