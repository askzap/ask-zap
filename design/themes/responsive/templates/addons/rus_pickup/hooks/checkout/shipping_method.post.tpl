{if $cart.chosen_shipping.$group_key == $shipping.shipping_id && $shipping.module == 'pickup'}

    {if $display == "radio"}

        {assign var="store_count" value=$shipping.data.stores|count}
        {assign var="shipping_id" value=$shipping.shipping_id}
        {assign var="old_store_id" value=$select_store.$group_key.$shipping_id}

        {if $shipping.service_params.display}
            {assign var="display_type" value=$shipping.service_params.display}
        {else}
            {assign var="display_type" value="ML"}
        {/if}

        {assign var="store_locations" value=$shipping.data.stores}

        {if $display_type != 'L'}
            {assign var="map_container" value="map_canvas_$group_key"}
            {include file="addons/rus_pickup/components/yandex.tpl"}
        {/if}

        <div class="clearfix ty-checkout-select-store__map-full-div">
        {if $display_type == 'M'}
            {if $store_count > 1}
                <h3>{__("available")}: {$store_count}
                <div data-ca-group-key="{$group_key}" class="ty-checkout-select-store__item-view">{include file="buttons/button.tpl" but_role="text" but_meta="cm-map-view-locations ty-btn__tertiary" but_text=__("view_all")}</div>
                </h3>
            {/if}
            <div class="ty-checkout-select-store__map-full" id="{$map_container}"></div>
        {elseif $display_type == 'ML'}
            <div class="ty-checkout-select-store__map" id="{$map_container}"></div>
        {/if}

    {if $display_type != 'M'}
        {if $display_type == 'L'}
            <div class="ty-checkout-select-store__list">
        {else}
            <div class="ty-checkout-select-store">
        {/if}
                {if $store_count > 1 && $display_type != 'L'}
                <div data-ca-group-key="{$group_key}" class="ty-checkout-select-store__item-view">{include file="buttons/button.tpl" but_role="text" but_meta="cm-map-view-locations ty-btn__tertiary" but_text=__("view_all")}</div>
                {/if}

                {foreach from=$shipping.data.stores item=store}
                    <div class="ty-one-store">
                        <input type="radio" name="select_store[{$group_key}][{$shipping.shipping_id}]" value="{$store.store_location_id}" {if $old_store_id == $store.store_location_id || $store_count == 1}checked="checked"{/if} id="store_{$group_key}_{$shipping.shipping_id}_{$store.store_location_id}" class="ty-one-store__radio-{$group_key}  ty-valign cm-map-select-store">
                        <div class="ty-one-store__label">
                        <label for="store_{$group_key}_{$shipping.shipping_id}_{$store.store_location_id}" class="ty-valign"  >
                            <p class="ty-one-store__name">{$store.name} {if $store.pickup_rate}({include file="common/price.tpl" value=$store.pickup_rate}){/if}</p>
                            <div class="ty-one-store__description">
                            {$store.city}{if $store.pickup_address}, {$store.pickup_address}{/if}</br>
                            {if $store.pickup_phone}{__("phone")}: {$store.pickup_phone}</br>{/if}
                            {if $store.pickup_time}{__("rus_pickup.work_time")}: {$store.pickup_time}</br>{/if}     
                            {if $store.description}
                                <a id="sw_store_description_{$store.store_location_id}" class="cm-combination ty-cart-content__detailed-link detailed-link">{__("description")}</a>
                                <div id="store_description_{$store.store_location_id}" class="hidden">
                                {$store.description nofilter}
                                </div>
                                </br>
                            {/if}
                            </div>
                        </label>

                    {if $display_type != 'L'}           
                        <a data-ca-latitude="{$store.latitude}" data-ca-longitude="{$store.longitude}" data-ca-group-key="{$group_key}" class="cm-map-view-location" >{__("view_on_map")}</a>
                    {/if}
                        </div>
                    </div>
                {/foreach}
            </div>
    {else}

        {foreach from=$shipping.data.stores item=store}
            {if $old_store_id == $store.store_location_id || $store_count == 1}
                    <div class="ty-one-store__select-store">

                        <p class="ty-one-store__name">{$store.name} {if $store.pickup_rate}({include file="common/price.tpl" value=$store.pickup_rate}){/if}</p>
                        <div class="ty-one-store__description">
                        {$store.city}{if $store.pickup_address}, {$store.pickup_address}{/if}</br>
                        {if $store.pickup_phone}{__("phone")}: {$store.pickup_phone}</br>{/if}
                        {if $store.pickup_time}{__("rus_pickup.work_time")}: {$store.pickup_time}</br>{/if}     
                        {if $store.description}
                            <a id="sw_store_description_{$store.store_location_id}" class="cm-combination ty-cart-content__detailed-link detailed-link">{__("description")}</a>
                            <div id="store_description_{$store.store_location_id}" class="hidden">
                            {$store.description nofilter}
                            </div>
                            </br>
                        {/if}
                        </div>

                        {if $store_count > 1}
                            <div data-ca-group-key="{$group_key}" class="ty-checkout-select-store__item-view">{include file="buttons/button.tpl" but_role="text" but_meta="cm-map-view-locations ty-btn__tertiary" but_text=__("view_all")}</div>
                        {/if}
                    </div>
            {/if}
            <input type="radio" class="ty-one-store__radio-{$group_key}  hidden" name="select_store[{$group_key}][{$shipping.shipping_id}]" value="{$store.store_location_id}" {if $old_store_id == $store.store_location_id || $store_count == 1}checked="checked"{/if} id="store_{$group_key}_{$shipping.shipping_id}_{$store.store_location_id}">
        {/foreach}

    {/if}

    </div>

    {elseif $display == "select"}
        <option value="{$shipping.shipping_id}" {$selected}>{$shipping.shipping} {$delivery_time} - {$rate nofilter}</option>
    {elseif $display == "show"}
        <p>
            {$strong_begin}{$rate.name} {$delivery_time} - {$rate nofilter}{$strong_begin}
        </p>
    {/if}

{/if}