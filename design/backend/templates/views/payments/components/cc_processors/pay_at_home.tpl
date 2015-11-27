{* rus_build_pack dbazhenov *}

{assign var="r_url" value="payment_notification.process?payment=pay_at_home"|fn_url:'C':'http'}
{assign var="c_url" value="payment_notification.cancel?payment=pay_at_home"|fn_url:'C':'http'}
{assign var="d_url" value="payment_notification.decline?payment=pay_at_home"|fn_url:'C':'http'}
<p>{__("text_pd_notice", ["[return_url]" => $r_url, "[cancel_url]" => $c_url, "[decline_url]" => $d_url])}</p>
<p>{__("pd_shop_id_notice")}</p>
<p>{__("pd_gate_password_notice")}</p>
<hr>
<div class="control-group">
    <label class="control-label" for="pd_shop_id">{__("pd_shop_id")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][pd_shop_id]" id="pd_shop_id" value="{$processor_params.pd_shop_id}"   size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="pd_login">{__("pd_login")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][pd_login]" id="pd_login" value="{$processor_params.pd_login}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="pd_gate_password">{__("pd_gate_password")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][pd_gate_password]" id="pd_gate_password" value="{$processor_params.pd_gate_password}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="pd_test">{__("test_live_mode")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][test]" id="pd_test">
            <option value="Y" {if $processor_params.test == "Y"}selected="selected"{/if}>{__("test")}</option>
            <option value="N" {if $processor_params.test == "N"}selected="selected"{/if}>{__("live")}</option>
        </select>
    </div>
</div>
