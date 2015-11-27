{* rus_build_pack dbazhenov *}

<div class="controls" id="rbx_currency_div">
    <select name="payment_data[processor_params][payment_method]" id="rbx_currency">
    {foreach from=$rbx_currencies key="group_name" item="cur_names"}
    <optgroup label="{$group_name}">
        {foreach from=$cur_names key="cur_code" item="cur_name"}
            <option value="{$cur_code}" {if $processor_params.payment_method == $cur_code}selected="selected"{/if}>{$cur_name}</option>
        {/foreach}
    </optgroup>
    {foreachelse}
    <option value="--">--</option>
    {/foreach}
    </select>
<!--rbx_currency_div--></div>
