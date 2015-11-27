{foreach from=$order_info.shipping item="shipping_method"}
	{if $shipping_method.store_data}
        <p class="strong">
            {$shipping_method.store_data.name}
        </p>
        <p class="muted">
            {$shipping_method.store_data.city}{if $shipping_method.store_data.pickup_address}, {$shipping_method.store_data.pickup_address}{/if}</br>
            {if $shipping_method.store_data.pickup_phone}
                {__("phone")}: {$shipping_method.store_data.pickup_phone}</br>
            {/if}
            {if $shipping_method.store_data.pickup_time}
                {__("rus_pickup.work_time")}: {$shipping_method.store_data.pickup_time}</br>
            {/if}
            {$shipping_method.store_data.description nofilter}
        </p>

        {assign var="store_count" value=1}
        {assign var="shipping_id" value=$order_info.shipping.shipping_id}

        {assign var="store_locations" value=$shipping_method.store_data}
        {assign var="map_container" value="map_canvas"}
        {include file="addons/rus_pickup/components/yandex_details.tpl"}
        <div class="clearfix checkout-select-store__map-full-div">
            <div class="checkout-select-store__map-details" id="{$map_container}"></div>
        </div>
    {/if}
{/foreach}