{foreach from=$order_info.shipping item="shipping" key="shipping_id" name="f_shipp"}
    {if $shipping.module == 'edost' && $shipping.office_data}
        <div class="well orders-right-pane form-horizontal">
            <div class="control-group shift-top">
                <div class="control-label">
                    {include file="common/subheader.tpl" title=__("edost.header.shipping_office")}
                </div>
            </div>

            <p class="strong">
            {$shipping.office_data.name}
            </p>
            <p class="muted">
            {$shipping.office_data.address}<br />
            {$shipping.office_data.tel}<br />
            {$shipping.office_data.schedule}<br />
            <a target="_blank" href="http://www.edost.ru/office.php?c={$shipping.office_data.office_id}">{__("edost.header.office_map")}</a>
            </p>
        </div>
    {/if}
{/foreach}