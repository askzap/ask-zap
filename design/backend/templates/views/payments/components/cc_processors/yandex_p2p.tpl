{if $settings.Security.secure_checkout == 'Y'}
    {assign var="redirect_url" value="payment_notification.process?payment=yandex_p2p"|fn_url:'C':'https'}
{else}
    {assign var="redirect_url" value="payment_notification.process?payment=yandex_p2p"|fn_url:'C':'http'}
{/if}
<p>
    {__("text_yandex_money_redirect_url", ["[redirect_url]" => $redirect_url])}
</p>
<hr>

<div class="control-group">
    <label class="control-label" for="payee_id">{__("rus_payments.yandex_money_payee_id")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][payee_id]" id="payee_id" value="{$processor_params.payee_id}" size="60" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="client_id">{__("addons.rus_payments.app_id")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][client_id]" id="client_id" value="{$processor_params.client_id}" class="span7" size="60" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="secret_key">{__("addons.rus_payments.oauth2_client_secret")}:</label>
    <div class="controls">
        <textarea class="span7" name="payment_data[processor_params][secret_key]" id="secret_key">{$processor_params.secret_key}</textarea>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="logging">{__("addons.rus_payments.logging")}:</label>
    <div class="controls">
        <input type="checkbox" name="payment_data[processor_params][logging]" id="logging" value="Y" {if $processor_params.logging == 'Y'} checked="checked"{/if}/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sw_block_test_code">{__("test_mode")}:</label>
    <div class="controls">
        <input type="checkbox" class="cm-switch-availability cm-switch-visibility" name="payment_data[processor_params][test_mode]" id="sw_block_test_code" {if $processor_params.test_mode eq "Y"}checked="checked"{/if} size="60" value="Y"/>
    </div>
</div>

<div class="control-group" id="block_test_code"{if $processor_params.test_mode != "Y"} style="display: none;"{/if}>
    <label class="control-label" for="test_code">{__("rus_payments.yandex_money_test_code")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][test_code]" id="test_code" {if $processor_params.test_mode != "Y"}disabled="disabled"{/if}>
            <option value="success"{if empty($processor_params.test_code) || $processor_params.test_code eq "success"} selected="selected"{/if}>success</option>
            <option value="contract_not_found"{if $processor_params.test_code eq "contract_not_found"} selected="selected"{/if}>contract_not_found</option>
            <option value="not_enough_funds"{if $processor_params.test_code eq "not_enough_funds"} selected="selected"{/if}>not_enough_funds</option>
            <option value="limit_exceeded"{if $processor_params.test_code eq "limit_exceeded"} selected="selected"{/if}>limit_exceeded</option>
            <option value="money_source_not_available"{if $processor_params.test_code eq "money_source_not_available"} selected="selected"{/if}>money_source_not_available</option>
            <option value="illegal_param_csc"{if $processor_params.test_code eq "illegal_param_csc"} selected="selected"{/if}>illegal_param_csc</option>
            <option value="payment_refused"{if $processor_params.test_code eq "payment_refused"} selected="selected"{/if}>payment_refused</option>
            <option value="authorization_reject"{if $processor_params.test_code eq "authorization_reject"} selected="selected"{/if}>authorization_reject</option>
            <option value="account_blocked"{if $processor_params.test_code eq "account_blocked"} selected="selected"{/if}>account_blocked</option>
            <option value="illegal_param_ext_auth_success_uri"{if $processor_params.test_code eq "illegal_param_ext_auth_success_uri"} selected="selected"{/if}>illegal_param_ext_auth_success_uri</option>
            <option value="illegal_param_ext_auth_fail_uri"{if $processor_params.test_code eq "illegal_param_ext_auth_fail_uri"} selected="selected"{/if}>illegal_param_ext_auth_fail_uri</option>
        </select>
    </div>
</div>
