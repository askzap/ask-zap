<div class="control-group">
    <label class="control-label" for="paypal_adv_merchant_login">{__("merchant_login")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][merchant_login]" id="paypal_adv_merchant_login" value="{$processor_params.merchant_login}" class="input-text" size="60" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="paypal_adv_api_user">{__("api_user")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][api_user]" id="paypal_adv_api_user" value="{$processor_params.api_user}" class="input-text" size="60" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="paypal_adv_api_partner">{__("api_partner")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][api_partner]" id="paypal_adv_api_partner" value="{$processor_params.api_partner}" class="input-text" size="60" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="paypal_adv_api_password">{__("api_password")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][api_password]" id="paypal_adv_api_password" value="{$processor_params.api_password}" class="input-text" size="60" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="paypal_adv_currency">{__("currency")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][currency]" id="paypal_adv_currency">
            <option value="USD"{if $processor_params.currency == "USD"} selected="selected"{/if}>{__("currency_code_usd")}</option>
            <option value="EUR"{if $processor_params.currency == "EUR"} selected="selected"{/if}>{__("currency_code_eur")}</option>
            <option value="CAD"{if $processor_params.currency == "CAD"} selected="selected"{/if}>{__("currency_code_cad")}</option>
            <option value="AUD"{if $processor_params.currency == "AUD"} selected="selected"{/if}>{__("currency_code_aud")}</option>
            <option value="BRL"{if $processor_params.currency == "BRL"} selected="selected"{/if}>{__("currency_code_brl")}</option>
            <option value="GBP"{if $processor_params.currency == "GBP"} selected="selected"{/if}>{__("currency_code_gbp")}</option>
            <option value="CZK"{if $processor_params.currency == "CZK"} selected="selected"{/if}>{__("currency_code_czk")}</option>
            <option value="DKK"{if $processor_params.currency == "DKK"} selected="selected"{/if}>{__("currency_code_dkk")}</option>
            <option value="HKD"{if $processor_params.currency == "HKD"} selected="selected"{/if}>{__("currency_code_hkd")}</option>
            <option value="HUF"{if $processor_params.currency == "HUF"} selected="selected"{/if}>{__("currency_code_huf")}</option>
            <option value="ILS"{if $processor_params.currency == "ILS"} selected="selected"{/if}>{__("currency_code_ils")}</option>
            <option value="JPY"{if $processor_params.currency == "JPY"} selected="selected"{/if}>{__("currency_code_jpy")}</option>
            <option value="MXN"{if $processor_params.currency == "MXN"} selected="selected"{/if}>{__("currency_code_mxn")}</option>
            <option value="TWD"{if $processor_params.currency == "TWD"} selected="selected"{/if}>{__("currency_code_twd")}</option>
            <option value="NZD"{if $processor_params.currency == "NZD"} selected="selected"{/if}>{__("currency_code_nzd")}</option>
            <option value="NOK"{if $processor_params.currency == "NOK"} selected="selected"{/if}>{__("currency_code_nok")}</option>
            <option value="PHP"{if $processor_params.currency == "PHP"} selected="selected"{/if}>{__("currency_code_php")}</option>
            <option value="PLN"{if $processor_params.currency == "PLN"} selected="selected"{/if}>{__("currency_code_pln")}</option>
            <option value="SGD"{if $processor_params.currency == "SGD"} selected="selected"{/if}>{__("currency_code_sgd")}</option>
            <option value="SEK"{if $processor_params.currency == "SEK"} selected="selected"{/if}>{__("currency_code_sek")}</option>
            <option value="CHF"{if $processor_params.currency == "CHF"} selected="selected"{/if}>{__("currency_code_chf")}</option>
            <option value="THB"{if $processor_params.currency == "THB"} selected="selected"{/if}>{__("currency_code_thb")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="paypal_adv_testmode">{__("test_live_mode")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][testmode]" id="paypal_adv_testmode">
            <option value="Y" {if $processor_params.testmode == "Y"}selected="selected"{/if}>{__("test")}</option>
            <option value="N" {if $processor_params.testmode == "N"}selected="selected"{/if}>{__("live")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="paypal_adv_layout">{__("payments.paypal_adv_layout")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][layout]" id="paypal_adv_layout">
            <option value="templateA" {if $processor_params.layout == "templateA"}selected="selected"{/if}>{__("payments.layout_a")}</option>
            <option value="templateB" {if $processor_params.layout == "templateB"}selected="selected"{/if}>{__("payments.layout_b")}</option>
            <option value="minLayout" {if $processor_params.layout == "minLayout"}selected="selected"{/if}>{__("payments.layout_c")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label cm-color" for="paypal_adv_payflowcolor">{__("payflowcolor")}:</label>
    <div class="controls">
        {include file="common/colorpicker.tpl" cp_name="payment_data[processor_params][payflowcolor]" cp_id="paypal_adv_payflowcolor" cp_value=$processor_params.payflowcolor}
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="paypal_adv_header_image">{__("header_image")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][header_image]" id="paypal_adv_header_image" value="{$processor_params.header_image}" class="input-text"  size="60" maxlength="127" />
    </div>
</div>

<div class="control-group">
    <label class="control-label cm-color" for="paypal_adv_button_bgcolor">{__("button_bgcolor")}:</label>
    <div class="controls">
        {include file="common/colorpicker.tpl" cp_name="payment_data[processor_params][button_bgcolor]" cp_id="paypal_adv_button_bgcolor" cp_value=$processor_params.button_bgcolor}
    </div>
</div>

<div class="control-group">
    <label class="control-label cm-color" for="paypal_adv_button_text_color">{__("button_text_color")}:</label>
    <div class="controls">
        {include file="common/colorpicker.tpl" cp_name="payment_data[processor_params][button_text_color]" cp_id="paypal_adv_button_text_color" cp_value=$processor_params.button_text_color}
    </div>
</div>

<div class="control-group">
    <label class="control-label cm-color" for="paypal_adv_collapse_bg_color">{__("collapse_bg_color")}:</label>
    <div class="controls">
        {include file="common/colorpicker.tpl" cp_name="payment_data[processor_params][collapse_bg_color]" cp_id="paypal_adv_collapse_bg_color" cp_value=$processor_params.collapse_bg_color}
    </div>
</div>

<div class="control-group">
    <label class="control-label cm-color" for="paypal_adv_collapse_text_color">{__("collapse_text_color")}:</label>
    <div class="controls">
        {include file="common/colorpicker.tpl" cp_name="payment_data[processor_params][collapse_text_color]" cp_id="paypal_adv_collapse_text_color" cp_value=$processor_params.collapse_text_color}
    </div>
</div>

<div class="control-group">
    <label class="control-label cm-color" for="paypal_adv_label_text_color">{__("label_text_color")}:</label>
    <div class="controls">
        {include file="common/colorpicker.tpl" cp_name="payment_data[processor_params][label_text_color]" cp_id="paypal_adv_label_text_color" cp_value=$processor_params.label_text_color}
    </div>
</div>
