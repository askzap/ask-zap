{if $cart.chosen_shipping.$group_key == $shipping.shipping_id && $shipping.module == 'pickup'}

    {if $display == "radio"}

        {assign var="store_count" value=$shipping.data.stores|count}
        {assign var="shipping_id" value=$shipping.shipping_id}
        {assign var="old_store_id" value=$select_store.$group_key.$shipping_id}

        {assign var="store_locations" value=$shipping.data.stores}
        {assign var="map_container" value="map_canvas"}
        {include file="addons/rus_pickup/components/yandex.tpl"}

        <div class="clearfix">

            <div class="checkout-select-store__map" id="{$map_container}"></div>

            <div class="checkout-select-store">
                {if $store_count > 1}
                <div class="checkout-select-store__item-view">{include file="buttons/button.tpl" but_role="text" but_meta="cm-map-view-locations" but_text=__("view_all")}</div>
                {/if}
                {foreach from=$shipping.data.stores item=store}
                    <div class="one-store">
                        <input type="radio" name="select_store[{$group_key}][{$shipping.shipping_id}]" value="{$store.store_location_id}" {if $old_store_id == $store.store_location_id || $store_count == 1}checked="checked"{/if} id="store_{$group_key}_{$shipping.shipping_id}_{$store.store_location_id}" class="one-store__radio valign cm-map-select-store" onclick="fn_calculate_total_shipping_cost();">
                        <div class="one-store__label">

                        <label for="store_{$group_key}_{$shipping.shipping_id}_{$store.store_location_id}" class="valign ">
                            <p class="one-store__name">{$store.name} {if $store.pickup_rate}({include file="common/price.tpl" value=$store.pickup_rate}){/if}</p>
                            <div class="one-store__description">
                            {$store.city}{if $store.pickup_address}, {$store.pickup_address}{/if}</br>
                            {if $store.pickup_phone}{__("phone")}: {$store.pickup_phone}</br>{/if}
                            {if $store.pickup_time}{__("rus_pickup.work_time")}: {$store.pickup_time}</br>{/if}                        
                            {$store.description nofilter}
                            </div>
                        </label>

                        <a data-ca-latitude="{$store.latitude}" data-ca-longitude="{$store.longitude}" class="cm-map-view-location" >{__("view_on_map")}</a>

                        </div>
                    </div>
                {/foreach}


            
            </div>
        </div>

    {elseif $display == "select"}
        <option value="{$shipping.shipping_id}" {$selected}>{$shipping.shipping} {$delivery_time} - {$rate nofilter}</option>

    {elseif $display == "show"}
        <p>
            {$strong_begin}{$rate.name} {$delivery_time} - {$rate nofilter}{$strong_begin}
        </p>
    {/if}

{/if}