{foreach from=$cart.shipping item=sdek_shipping}
    {if $sdek_shipping.module == 'sdek'}
        {if $product_groups}
            {foreach from=$product_groups key=group_key item=group}
                {if $group.shippings && !$group.shipping_no_required}

                        {foreach from=$group.shippings item=shipping}
                            {if $cart.chosen_shipping.$group_key == $shipping.shipping_id}
                            
                                {assign var="old_office_id" value=$old_ship_data.$group_key.office_id}

                                {assign var="select_id" value=$select_office.$group_key.$shipping_id}

                                {assign var="shipping_id" value=$shipping.shipping_id}

                                {if $shipping.data.offices}
                                    {assign var="office_count" value=$shipping.data.offices|count}
                                
                                        {if $office_count == 1}
                                            {foreach from=$shipping.data.offices item=office}
                                            <div class="sidebar-row">
                                                <input type="hidden" name="select_office[{$group_key}][{$shipping_id}]" value="{$office.Code}" id="office_{$group_key}_{$shipping_id}_{$office.Code}" checked="checked"> 
                                                {$office.Name} 
                                                <p class="muted">
                                                {$office.Address}<br />
                                                {$office.WorkTime}<br />
                                                {$office.Phone}<br />
                                                {$office.Note}<br />
                                                </p>
                                            </div>    
                                            {/foreach}
                                        {else}
                                            {foreach from=$shipping.data.offices item=office}
                                            <div class="sidebar-row">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <label for="office_{$group_key}_{$shipping_id}_{$office.Code}" class="radio">
                                                            <input type="radio" name="select_office[{$group_key}][{$shipping_id}]" value="{$office.Code}" {if $select_id == $office.Code || $old_office_id == $office.Code}checked="checked"{/if} id="office_{$group_key}_{$shipping_id}_{$office.Code}" >
                                                            {$office.Name} {include file="common/tooltip.tpl" tooltip=$office.Note}
                                                            <p class="muted">
                                                            {$office.Address}<br />
                                                            {$office.WorkTime}<br />
                                                            {$office.Phone}<br />
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
    {/if}
{/foreach}
