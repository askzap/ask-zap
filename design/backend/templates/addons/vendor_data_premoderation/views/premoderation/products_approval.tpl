{capture name="mainbox"}

{capture name="sidebar"}
    {include file="addons/vendor_data_premoderation/views/premoderation/components/products_search_form.tpl" dispatch="premoderation.products_approval"}
{/capture}

<form action="{""|fn_url}" method="post" name="manage_products_form">
<input type="hidden" name="category_id" value="{$search.cid}" />

{include file="common/pagination.tpl" save_current_page=true save_current_url=true}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
{assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

{if $products}
<table class="table table-middle">
<thead>
    <tr>
        <th class="left" width="5%">{include file="common/check_items.tpl"}</th>
        <th width="50%"><a class="cm-ajax" href="{"`$c_url`&sort_by=product&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("name")}{if $search.sort_by == "product"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
        <th width="30%"><a class="cm-ajax" href="{"`$c_url`&sort_by=company&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("vendor")}{if $search.sort_by == "company"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
        <th width="10%"><a class="cm-ajax" href="{"`$c_url`&sort_by=approval&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("status")}{if $search.sort_by == "approval"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
        <th width="5%">&nbsp;</th>
    </tr>
</thead>
<tbody>
{foreach from=$products item=product}
<tr>
    <td class="left">
        <input type="checkbox" name="product_ids[]" value="{$product.product_id}" class="cm-item" /></td>
    <td>
        <input type="hidden" name="products_data[{$product.product_id}][product]" value="{$product.product}" />
        <a href="{"products.update?product_id=`$product.product_id`"|fn_url}" {if $product.status == "N"}class="manage-root-item-disabled"{/if}>{$product.product nofilter}</a>
        {include file="views/companies/components/company_name.tpl" object=$product}
    </td>
    <td>
        <input type="hidden" name="products_data[{$product.product_id}][company_id]" value="{$product.company_id}" />
        {$product.company_name}
    <td>
        {if $product.approved == "Y"}{__("approved")}{elseif $product.approved == "P"}{__("pending")}{else}{__("disapproved")}{/if}
        <input type="hidden" name="products_data[{$product.product_id}][current_status]" value="{$product.approved}" />
    <td class="nowrap">
        {capture name="approve"}
            {include file="addons/vendor_data_premoderation/views/premoderation/components/approval_popup.tpl" name="approval_data[`$product.product_id`]" status="Y" product_id=$product.product_id company_id=$product.company_id}
            <div class="buttons-container">
                {include file="buttons/save_cancel.tpl" but_text=__("approve") but_name="dispatch[premoderation.products_approval.approve.`$product.product_id`]" cancel_action="close"}
            </div>
        {/capture}
        
        {capture name="disapprove"}
            {include file="addons/vendor_data_premoderation/views/premoderation/components/approval_popup.tpl" name="approval_data[`$product.product_id`]" status="N" product_id=$product.product_id company_id=$product.company_id}
            <div class="buttons-container">
                {include file="buttons/save_cancel.tpl" but_text=__("disapprove") but_name="dispatch[premoderation.products_approval.disapprove.`$product.product_id`]" cancel_action="close"}
            </div>
        {/capture}
        
        {if $product.approved == "Y" || $product.approved == "P"}
            {include file="common/popupbox.tpl" id="disapprove_`$product.product_id`" text="{__("disapprove")} \"`$product.product`\"" content=$smarty.capture.disapprove link_text=" " act="edit" icon="icon-thumbs-down"}
        {/if}
        
        {if $product.approved == "P" || $product.approved == "N"}
            {include file="common/popupbox.tpl" id="approve_`$product.product_id`" text="{__("approve")} \"`$product.product`\""  content=$smarty.capture.approve link_text=" " act="edit" icon="icon-thumbs-up"}
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

{if $products}
    {capture name="approve_selected"}
        {include file="addons/vendor_data_premoderation/views/premoderation/components/reason_container.tpl" type="approved"}
        <div class="buttons-container">
            {include file="buttons/save_cancel.tpl" but_text=__("proceed") but_name="dispatch[premoderation.m_approve]" cancel_action="close" but_meta="cm-process-items"}
        </div>
    {/capture}
    {include file="common/popupbox.tpl" id="approve_selected" text=__("approve_selected") content=$smarty.capture.approve_selected link_text=__("approve_selected")}    

    {capture name="disapprove_selected"}
        {include file="addons/vendor_data_premoderation/views/premoderation/components/reason_container.tpl" type="declined"}
        <div class="buttons-container">
            {include file="buttons/save_cancel.tpl" but_text=__("proceed") but_name="dispatch[premoderation.m_decline]" cancel_action="close" but_meta="cm-process-items"}
        </div>
    {/capture}
    {include file="common/popupbox.tpl" id="disapprove_selected" text=__("disapprove_selected") content=$smarty.capture.disapprove_selected link_text=__("disapprove_selected")}

    {capture name="buttons"}
        {include file="buttons/button.tpl" but_role="submit-link" but_target_id="content_approve_selected" but_text=__("approve_selected") but_meta="cm-process-items cm-dialog-opener" but_target_form="manage_products_form"}
        {include file="buttons/button.tpl" but_role="submit-link" but_target_id="content_disapprove_selected" but_text=__("disapprove_selected") but_meta="cm-process-items cm-dialog-opener" but_target_form="manage_products_form"}
    {/capture}
{/if}

</form>

{/capture}
{include file="common/mainbox.tpl" title=__("product_approval") content=$smarty.capture.mainbox select_languages=true sidebar=$smarty.capture.sidebar buttons=$smarty.capture.buttons}