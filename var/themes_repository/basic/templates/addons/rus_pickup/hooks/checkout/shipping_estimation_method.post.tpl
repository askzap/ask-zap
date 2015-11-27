{if $shipping.service_code == 'pickup'}
    {if $cart.chosen_shipping.$group_key == $shipping.shipping_id}
        <div class="clearfix">
            <div class="checkout-select-store__estimation">
                {assign var="store_count" value=$shipping.data.stores|count}
                {assign var="shipping_id" value=$shipping.shipping_id}
                {assign var="old_store_id" value=$select_store.$group_key.$shipping_id}

                {assign var="store_locations" value=$shipping.data.stores}

                {foreach from=$shipping.data.stores item=store}
                    <div class="one-store">
                        <input type="radio" name="select_store[{$group_key}][{$shipping.shipping_id}]" value="{$store.store_location_id}" {if $old_store_id == $store.store_location_id || $store_count == 1}checked="checked"{/if} id="store_{$group_key}_{$shipping.shipping_id}_{$store.store_location_id}" class="one-store__radio valign" onclick="fn_calculate_total_shipping();">
                        <div class="one-store__label">
                            <label for="store_{$group_key}_{$shipping.shipping_id}_{$store.store_location_id}" class="valign"  >
                                <span class="one-store__name">
                                    {$store.name} 
                                    {if $store.pickup_rate}({include file="common/price.tpl" value=$store.pickup_rate}){/if}
                                </span>
                                {if $store.pickup_address}
                                <span class="mutted">
                                    <br/>{$store.pickup_address}
                                </span>
                                {/if}
                            </label>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    {/if}
{/if}

