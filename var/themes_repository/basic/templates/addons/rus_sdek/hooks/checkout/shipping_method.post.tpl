{if $cart.chosen_shipping.$group_key == $shipping.shipping_id && $shipping.module == 'sdek'}

    {if $display == "radio"}

        {assign var="office_count" value=$shipping.data.offices|count}
		{assign var="shipping_id" value=$shipping.shipping_id}
        {assign var="old_office_id" value=$select_office.$group_key.$shipping_id}
        <div class="checkout-select-office">

            {foreach from=$shipping.data.offices item=office}
                <div class="one-office">
                    <input type="radio" name="select_office[{$group_key}][{$shipping.shipping_id}]" value="{$office.Code}" {if $old_office_id == $office.Code || $office_count == 1 || empty($old_office_id)}checked="checked" {assign var="old_office_id" value=$office.Code}{/if} id="office_{$group_key}_{$shipping.shipping_id}_{$office.Code}" class="office-radio" >
                    <div class="office__label">
                        <label for="office_{$office.office_id}" >
						    <p class="one-office__name">{$office.Name}</p>
							<div class="one-office__description">
							    {$office.City}, {$office.Address}<br />
                                {$office.WorkTime}<br />
                                {$office.Phone}<br />
                                {$office.Note}<br />
                            </div>
                        </label>
                    </div>
                </div>
            {/foreach}
        </div>

    {elseif $display == "select"}
        <option value="{$shipping.shipping_id}" {$selected}>{$shipping.shipping} {$delivery_time} - {$rate nofilter}</option>

    {elseif $display == "show"}
        <p>
            {$strong_begin}{$rate.name} {$delivery_time} - {$rate nofilter}{$strong_begin}
        </p>
    {/if}

{/if}
