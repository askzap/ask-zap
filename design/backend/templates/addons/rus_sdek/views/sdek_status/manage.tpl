{capture name="mainbox"}
    {assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
    <form action="{""|fn_url}" method="post" name="sdek_status_form" class="form-horizontal form-edit">
    {include file="common/pagination.tpl" save_current_page=true save_current_url=true}
    {if $data_status}
        <input type="hidden" name="page" value="{$smarty.request.page}" />
        {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
        {assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
        <table width="100%" class="table table-middle" >
            <thead>
            <tr>
                <th width="3%" class="nowrap"><a class="cm-ajax" href="{"`$c_url`&sort_by=order_id&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("order")}{if $search.sort_by == "order"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                <th width="3%" class="nowrap"><a class="cm-ajax" href="{"`$c_url`&sort_by=shipment_id&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("shipment")}{if $search.sort_by == "order"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                <th width="3%" class="nowrap"><a class="cm-ajax" href="{"`$c_url`&sort_by=id&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("sdek.lang_status_code")}{if $search.sort_by == "order"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                <th width="3%" class="nowrap"><a class="cm-ajax" href="{"`$c_url`&sort_by=timestamp&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("sdek.date")}{if $search.sort_by == "order"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                <th width="3%" class="nowrap"><a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("sdek.lang_status_order")}{if $search.sort_by == "order"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                <th width="3%" class="nowrap"><a class="cm-ajax" href="{"`$c_url`&sort_by=city&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("sdek.lang_city")}{if $search.sort_by == "order"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$data_status item=d_status}
                <tr>
                    <td>
                        <a class="underlined" href="{"orders.details?order_id=`$d_status.order_id`"|fn_url}"><span>{$d_status.order_id}</span></a>
                    </td>
                    <td>
                        <a class="underlined" href="{"shipments.details?shipment_id=`$d_status.shipment_id`"|fn_url}"><span>#{$d_status.shipment_id}</span></a>
                    </td>
                    <td>
                        {$d_status.id}
                    </td>
                    <td>
                        {$d_status.timestamp|date_format:"`$settings.Appearance.date_format`"}
                    </td>
                    <td>
                        {$d_status.status}
                    </td>
                    <td>
                        {$d_status.city}
                    </td>
                </tr>
            {/foreach}
            <tbody>
        </table>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
    {include file="common/pagination.tpl"}
    </form>
{/capture}

{capture name="sidebar"}
    {include file="common/saved_search.tpl" dispatch="sdek_status.manage" view_type="sdek_status"}
    {include file="addons/rus_sdek/views/components/invoice_search_form.tpl" period=$period.period status=$period.status search=$period}
{/capture}

{include file="common/mainbox.tpl" title=__("shippings.sdek.status_title") content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons sidebar=$smarty.capture.sidebar}
