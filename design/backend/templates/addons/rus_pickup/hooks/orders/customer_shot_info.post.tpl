{foreach from=$order_info.shipping item="shipping" key="shipping_id" name="f_shipp"}
    {if $shipping.module == 'pickup' && $shipping.store_data}
        <div class="well orders-right-pane form-horizontal">
            <div class="control-group shift-top">
                <div class="control-label">
                    {include file="common/subheader.tpl" title=__("rus_pickup.pickup")}
                </div>
            </div>

            <p class="strong">
            {$shipping.store_data.name}
            </p>
            <p class="muted">
            {$shipping.store_data.city}, {$shipping.store_data.pickup_address}<br />
            {$shipping.store_data.pickup_phone}<br />
            {__("rus_pickup.work_time")}: {$shipping.store_data.pickup_time}<br />
            </p>
        </div>
    {/if}
{/foreach}
