<div id="order_logs">
    {if $logs}
        {assign var="order_statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses:true} 
        <table width="100%" class="table table-middle">
        <thead>
        <tr>
            <th width="5%" class="center">{__("id")}</th>
            <th width="15%">{__("user")}</th>
            <th width="15%" class="left">{__("action")}</th>
            <th width="50%" class="left">{__("description")}</th>
            <th width="15%" class="center">{__("date")}</th>
        </tr>
        </thead>
        {foreach from=$logs item=log}
        {math equation="x+1" x=$log_id|default:0 assign="log_id"}
            <tr>
                <td class="center">#{$log_id}</td>
                <td>{if $log.user_id}
                    <a href="{"profiles.update&user_id=`$log.user_id`"|fn_url}" class="strong">{$log.firstname}&nbsp;{$log.lastname}</a>
                {else}
                    {if $log.action == "rus_order_logs_order_created"}
                        {__('guest')}
                    {else}
                        {__('system')}
                    {/if}
                {/if}</td>
                <td class="left">{__($log.action)}</td>
                <td class="left">{$log.description nofilter}</td>
                <td class="center">
                    {$log.timestamp|date_format:"`$settings.Appearance.date_format`"},&nbsp;{$log.timestamp|date_format:"`$settings.Appearance.time_format`"}
                </td>
            </tr>
        {/foreach}
        </table>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    </table>
<!--order_logs--></div>