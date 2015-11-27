<div class="control-group">
    <label class="control-label" for="sberbank_login">{__("addons.rus_sberbank.login")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][login]" id="sberbank_login" value="{$processor_params.login}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sberbank_password">{__("addons.rus_sberbank.password")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][password]" id="sberbank_password" value="{$processor_params.password}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="mode">{__("test_live_mode")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][mode]" id="mode">
            <option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{__("test")}</option>
            <option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{__("live")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="logging">{__("addons.rus_sberbank.logging")}:</label>
    <div class="controls">
        <input type="checkbox" name="payment_data[processor_params][logging]" id="logging" value="Y" {if $processor_params.logging == 'Y'} checked="checked"{/if}/>
    </div>
</div>
