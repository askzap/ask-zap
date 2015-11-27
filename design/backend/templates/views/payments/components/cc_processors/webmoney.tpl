{* rus_build_pack dbazhenov *}

<div>
<p>{__("text_webmoney_notice")}</p>
</div>
<hr>

<div class="control-group">
    <label class="control-label" for="lmi_payee_purse">{__("lmi_payee_purse")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][lmi_payee_purse]" id="lmi_payee_purse" value="{$processor_params.lmi_payee_purse}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="lmi_secret_key">{__("addons.rus_payments.wm_secret_key")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][lmi_secret_key]" id="lmi_secret_key" value="{$processor_params.lmi_secret_key}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="lmi_mode">{__("addons.rus_payments.wm_test_live_mode")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][lmi_mode]" id="lmi_mode">
            <option value="0" {if $processor_params.lmi_mode == "0"}selected="selected"{/if}>{__("addons.rus_payments.wm_live")}</option>
            <option value="1" {if $processor_params.lmi_mode == "1"}selected="selected"{/if}>{__("addons.rus_payments.wm_test")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="lmi_sim_mode">{__("lmi_sim_mode")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][lmi_sim_mode]" id="lmi_sim_mode">
            <option value="0" {if $processor_params.lmi_sim_mode == "0"}selected="selected"{/if}>{__("wm_success_mode")}</option>
            <option value="1" {if $processor_params.lmi_sim_mode == "1"}selected="selected"{/if}>{__("wm_error_mode")}</option>
            <option value="2" {if $processor_params.lmi_sim_mode == "2"}selected="selected"{/if}>{__("wm_combine_mode")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="lmi_payment_desc">{__("order_prefix")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][lmi_payment_desc]" id="lmi_payment_desc" value="{$processor_params.lmi_payment_desc}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sing_algo">{__("addons.rus_payments.sign_algo")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][sing_algo]" id="sing_alg">
            <option value="sha256" {if $processor_params.sing_algo == "sha256"}selected="selected"{/if}>SHA256</option>
            <option value="md5" {if $processor_params.sing_algo == "md5"}selected="selected"{/if}>MD5</option>
        </select>
    </div>
</div>