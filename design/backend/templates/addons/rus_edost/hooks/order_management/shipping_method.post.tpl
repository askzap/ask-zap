{if $product_groups}
    {foreach from=$product_groups key=group_key item=group}
        {if $group.shippings && !$group.shipping_no_required}

            {foreach from=$group.shippings item=shipping}
                {if $cart.chosen_shipping.$group_key == $shipping.shipping_id}
                
                    {assign var="old_office_id" value=$old_ship_data.$group_key.office_id}

                    {assign var="shipping_id" value=$shipping.shipping_id}

                    {if $shipping.data.office}
                        {assign var="office_count" value=$shipping.data.office|count}
                    
                            {if $office_count == 1}
                                {foreach from=$shipping.data.office item=office}
                                <div class="sidebar-row">
                                    <input type="hidden" name="select_office[{$group_key}][{$shipping_id}]" value="{$office.office_id}"id="office_{$office.office_id}" > 
                                    {$office.name}
                                    <p class="muted">
                                    {$office.address}<br />
                                    {$office.tel}<br />
                                    {$office.schedule}<br />
                                    </p>
                                </div>    
                                {/foreach}
                            {else}
                                {foreach from=$shipping.data.office item=office}
                                <div class="sidebar-row">
                                    <div class="control-group">
                                        <div class="controls">
                                            <label for="office_{$office.office_id}" class="radio">
                                                <input type="radio" name="select_office[{$group_key}][{$shipping_id}]" value="{$office.office_id}" {if $old_office_id == $office.office_id}checked="checked"{/if} id="office_{$office.office_id}" > {$office.name}
                                            </label>
                                            <p class="muted">{$office.address}<br />
                                            {$office.tel}<br />
                                            {$office.schedule}<br />
                                            </p>
                                        </div>    
                                    </div> 
                                </div>    
                                {/foreach}                  
                            {/if}
                    {/if}
                {/if}
            {/foreach}
        {/if}
    {/foreach}
{/if}