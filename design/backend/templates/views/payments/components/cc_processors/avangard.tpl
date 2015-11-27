{assign var="redirect_url" value="payment_notification.notify?payment=avangard"|fn_url:'C':'https'}
<p>
    {__("text_avangard_redirect_url", ["[redirect_url]" => $redirect_url])}
</p>
<hr>

<div class="control-group">
    <label class="control-label" for="shop_id">{__("avangard_shop_id")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][shop_id]" id="shop_id" value="{$processor_params.shop_id}"  size="60">
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="password">{__("avangard_password")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][password]" id="password" value="{$processor_params.password}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="av_sign">{__("addons.rus_payments.avangard_av_sign")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][av_sign]" id="av_sign" value="{$processor_params.av_sign}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="logging">{__("addons.rus_payments.logging")}:</label>
    <div class="controls">
        <input type="checkbox" name="payment_data[processor_params][logging]" id="logging" value="Y" {if $processor_params.logging == 'Y'} checked="checked"{/if}/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="avangard_returns_enabled">{__("addons.rus_payments.avangard_returns_enabled")}:</label>
    <div class="controls">
        <input type="checkbox" name="payment_data[processor_params][returns_enabled]" id="avangard_returns_enabled" value="Y"{if $processor_params.returns_enabled == "Y"} checked="checked"{/if}>
    </div>
</div>

{assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}

<div class="control-group">
    <label class="control-label" for="avangard_returned_order_status">{__("addons.rus_payments.avangard_returned_order_status")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][returned_order_status]" id="avangard_returned_order_status">
            {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if $processor_params.returned_order_status == $k}selected="selected"{/if}>{$s}</option>
            {/foreach}
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="avangard_paid_order_status">{__("addons.rus_payments.avangard_paid_order_status")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][paid_order_status]" id="avangard_paid_order_status">
            {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if $processor_params.paid_order_status == $k || !$processor_params.paid_order_status && $k == 'P'}selected="selected"{/if}>{$s}</option>
            {/foreach}
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="avangard_failed_order_status">{__("addons.rus_payments.avangard_failed_order_status")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][failed_order_status]" id="avangard_failed_order_status">
            <option value="I" {if $processor_params.failed_order_status == 'I'}selected="selected"{/if}>{__('incompleted')}</option>
            {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if $processor_params.failed_order_status == $k || !$processor_params.failed_order_status && $k == 'F'}selected="selected"{/if}>{$s}</option>
            {/foreach}
        </select>
    </div>
</div>
