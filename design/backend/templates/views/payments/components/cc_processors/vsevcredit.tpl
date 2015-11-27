{* rus_build_pack dbazhenov *}
{assign var="return_url" value="payment_notification.complete?payment=vsevcredit&order_id=`$ldelim`order_id`$rdelim`"|fn_url:'C':'http'}
{assign var="api_url" value="payment_notification.notify?payment=vsevcredit"|fn_url:'C':'http'}
<p>{__("vsevkredit_url_notice", ["[return_url]" => $return_url, "[api_url]" => $api_url])}</p>
<hr>
<div class="control-group">
    <label class="control-label" for="vsevcredit_shop_id">{__("vsevcredit_shop_id")}:</label>
    <div class="controls">
    	<input type="text" name="payment_data[processor_params][vvc_shop_id]" id="vsevcredit_shop_id" value="{$processor_params.vvc_shop_id}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="vsevcredit_secret">{__("vsevcredit_secret")}:</label>
    <div class="controls">
    	<input type="text" name="payment_data[processor_params][vvc_secret]" id="vsevcredit_secret" value="{$processor_params.vvc_secret}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="vsevcredit_test_mode">{__("test_live_mode")}:</label>
    <div class="controls">
    	<select name="payment_data[processor_params][test_mode]" id="vsevcredit_test_mode">
    	    <option value="0" {if $processor_params.test_mode == "0"}selected="selected"{/if}>{__("live")}</option>
    	    <option value="1" {if $processor_params.test_mode == "1"}selected="selected"{/if}>{__("test")}</option>
    	</select>
    </div>
</div>