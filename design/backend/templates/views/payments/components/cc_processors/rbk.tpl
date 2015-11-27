{* rus_build_pack dbazhenov *}

{assign var="r_url" value="payment_notification.placement?payment=rbk"|fn_url:'C':'http'}
<p>{__("text_rbk_notice", ["[result_url]" => $r_url])}</p>
<p>{__("rbk_secret_key_notice")}</p>
<p>{__("rbk_login_notice")}</p>
<hr>

<div class="control-group">
    <label class="control-label" for="rbk_login">{__("rbk_login")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][rbk_eshopId]" id="rbk_login" value="{$processor_params.rbk_eshopId}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="rbk_secretKey">{__("rbk_secret_key")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][rbk_secretKey]" id="rbk_secretKey" value="{$processor_params.rbk_secretKey}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="rbk_paymethod">{__("rbk_select_pay_method")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][rbk_paymethod]" id="rbk_paymethod">
            <option value="rbkmoney" {if $processor_params.rbk_paymethod == "rbkmoney"}selected="selected"{/if}>{__("pay_rbkmoney")}</option>
            <option value="bankcard" {if $processor_params.rbk_paymethod == "bankcard"}selected="selected"{/if}>{__("pay_bankcard")}</option>
            <option value="exchangers" {if $processor_params.rbk_paymethod == "exchangers"}selected="selected"{/if}>{__("pay_exchangers")}</option>
            <option value="terminals" {if $processor_params.rbk_paymethod == "terminals"}selected="selected"{/if}>{__("pay_terminals")}</option>
            <option value="prepaidcard" {if $processor_params.rbk_paymethod == "prepaidcard"}selected="selected"{/if}>{__("pay_prepaidcard")}</option>
            <option value="postrus" {if $processor_params.rbk_paymethod == "postrus"}selected="selected"{/if}>{__("pay_postrus")}</option>
            <option value="mobilestores" {if $processor_params.rbk_paymethod == "mobilestores"}selected="selected"{/if}>{__("pay_mobilestores")}</option>
            <option value="transfers" {if $processor_params.rbk_paymethod == "transfers"}selected="selected"{/if}>{__("pay_transfers")}</option>
            <option value="ibank" {if $processor_params.rbk_paymethod == "ibank"}selected="selected"{/if}>{__("pay_ibank")}</option>
            <option value="sberbank" {if $processor_params.rbk_paymethod == "sberbank"}selected="selected"{/if}>{__("pay_sberbank")}</option>
            <option value="svyaznoy" {if $processor_params.rbk_paymethod == "svyaznoy"}selected="selected"{/if}>{__("pay_svyaznoy")}</option>
            <option value="euroset" {if $processor_params.rbk_paymethod == "euroset"}selected="selected"{/if}>{__("pay_euroset")}</option>
            <option value="contact" {if $processor_params.rbk_paymethod == "contact"}selected="selected"{/if}>{__("pay_contact")}</option>
            <option value="mts" {if $processor_params.rbk_paymethod == "mts"}selected="selected"{/if}>{__("pay_mts")}</option>
            <option value="uralsib" {if $processor_params.rbk_paymethod == "uralsib"}selected="selected"{/if}>{__("pay_uralsib")}</option>
            <option value="handybank" {if $processor_params.rbk_paymethod == "handybank"}selected="selected"{/if}>{__("pay_handybank")}</option>
            <option value="ocean" {if $processor_params.rbk_paymethod == "ocean"}selected="selected"{/if}>{__("pay_ocean")}</option>
            <option value="ibankuralsib" {if $processor_params.rbk_paymethod == "ibankuralsib"}selected="selected"{/if}>{__("pay_ibankuralsib")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="rbk_lang">{__("rbk_language")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][rbk_language]" id="rbk_lang">
            <option value="ru" {if $processor_params.rbk_language == "ru"}selected="selected"{/if}>RU</option>
            <option value="en" {if $processor_params.rbk_language == "en"}selected="selected"{/if}>EN</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="rbk_currency">{__("currency")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][currency]" id="rbkcurrency">
            {foreach from=""|fn_get_simple_currencies key="code" item="currency"}
                <option value="{$code}"{if $processor_params.currency == $code} selected="selected"{/if}>{$currency}</option>
            {/foreach}
        </select>
    </div>
</div>
