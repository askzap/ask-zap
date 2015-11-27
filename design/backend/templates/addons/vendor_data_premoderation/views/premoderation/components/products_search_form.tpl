{capture name="section"}
<div class="sidebar-row">
    <h6>{__("search")}</h6>
    <form action="{""|fn_url}" name="pre_moderation_search_form" method="get" class="cm-disable-empty">

    {if $smarty.request.redirect_url}
    <input type="hidden" name="redirect_url" value="{$smarty.request.redirect_url}" />
    {/if}
    {if $selected_section != ""}
    <input type="hidden" id="selected_section" name="selected_section" value="{$selected_section}" />
    {/if}
    
    {$extra nofilter}
        <div class="sidebar-field">
            <label>{__("find_results_with")}:</label>
            <input type="text" name="q" size="20" value="{$search.q}">
        </div>

        <div class="sidebar-field">
            <label>{__("search_in_category")}:</label>
            {if "categories"|fn_show_picker:$smarty.const.CATEGORY_SHOW_ALL}
                {if $search.cid}
                    {assign var="s_cid" value=$search.cid}
                {else}
                    {assign var="s_cid" value="0"}
                {/if}
                {include file="pickers/categories/picker.tpl" data_id="location_category" input_name="cid" item_ids=$s_cid hide_link=true hide_delete_button=true default_name=__("all_categories") extra=""}
            {else}
                {include file="common/select_category.tpl" name="cid" id=$search.cid}
            {/if}
        </div>
    
        <div class="sidebar-field">
            <label>{__("status")}:</label>
            <select name="approval_status">
                <option value="all" {if $search.approval_status == "all"}selected="selected"{/if}>{__("all")}</option>
                <option value="Y" {if $search.approval_status == "Y"}selected="selected"{/if}>{__("approved")}</option>
                <option value="P" {if $search.approval_status == "P"}selected="selected"{/if}>{__("pending")}</option>
                <option value="N" {if $search.approval_status == "N"}selected="selected"{/if}>{__("disapproved")}</option>
            </select>
        </div>
        
        <div class="sidebar-field">
            {include file="common/select_vendor.tpl"}
        </div>

        <div class="sidebar-field">
            {include file="buttons/search.tpl" but_name="dispatch[$dispatch]"}
        </div>
    
    </form>
</div>

{/capture}
{include file="common/section.tpl" section_content=$smarty.capture.section}