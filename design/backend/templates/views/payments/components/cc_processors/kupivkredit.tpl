{* rus_build_kupivkredit dbazhenov *}
<p>{__("kvk_registration_instructions")}</p>
<hr>
<div class="control-group">
    <label class="control-label" for="kupivkredit_shop_id">{__("kupivkredit_shop_id")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][kvk_shop_id]" id="kupivkredit_shop_id" value="{$processor_params.kvk_shop_id}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="kupivkredit_api_key">{__("kupivkredit_api_key")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][kvk_api_key]" id="kupivkredit_api_key" value="{$processor_params.kvk_api_key}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="kupivkredit_secret">{__("kupivkredit_secret")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][kvk_secret]" id="kupivkredit_secret" value="{$processor_params.kvk_secret}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="kupivkredit_test">{__("test_live_mode")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][test]" id="kupivkredit_test">
            <option value="Y" {if $processor_params.test == "Y"}selected="selected"{/if}>{__("test")}</option>
            <option value="N" {if $processor_params.test == "N"}selected="selected"{/if}>{__("live")}</option>
        </select>
    </div>
</div>