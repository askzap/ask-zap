<p>{__("text_paymaster_notice")}</p>
<hr>
<div class="control-group">
    <label class="control-label" for="merchant_id">{__("merchant_id")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text-large"  size="60" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="paymaster_key">{__("paymaster_key")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][paymaster_key]" id="paymaster_key" value="{$processor_params.paymaster_key}" class="input-text-large"  size="60" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="payment_method_{$payment_id}">{__("payment_method")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][payment_method]" id="payment_method_{$payment_id}">
            <option value=""{if empty($processor_params.payment_method)} selected="selected"{/if}>{__("pay_select_paymaster")}</option>
            <option value="WebMoney"{if $processor_params.payment_method == "WebMoney"} selected="selected"{/if}>{__("pay_webmoney")}</option>
            <option value="AlfaBank"{if $processor_params.payment_method == "AlfaBank"} selected="selected"{/if}>{__("pay_alfabank")}</option>
            <option value="VTB24"{if $processor_params.payment_method == "VTB24"} selected="selected"{/if}>{__("pay_vtb24")}</option>
            <option value="RSB"{if $processor_params.payment_method == "RSB"} selected="selected"{/if}>{__("pay_rsb")}</option>
            <option value="Yandex"{if $processor_params.payment_method == "Yandex"} selected="selected"{/if}>{__("pay_yandex")}</option>
            <option value="Beeline"{if $processor_params.payment_method == "Beeline"} selected="selected"{/if}>{__("pay_beeline")}</option>
            <option value="Qiwi"{if $processor_params.payment_method == "Qiwi"} selected="selected"{/if}>{__("pay_qiwi")}</option>
            <option value="EuroSet"{if $processor_params.payment_method == "EuroSet"} selected="selected"{/if}>{__("pay_euroset")}</option>
            <option value="BankCard"{if $processor_params.payment_method == "BankCard"} selected="selected"{/if}>{__("pay_bankcard")}</option>
            <option value="PSB"{if $processor_params.payment_method == "PSB"} selected="selected"{/if}>{__("pay_psb")}</option>
            <option value="Svyaznoy"{if $processor_params.payment_method == "Svyaznoy"} selected="selected"{/if}>{__("pay_svyaznoy")}</option>
            <option value="MTS"{if $processor_params.payment_method == "MTS"} selected="selected"{/if}>{__("pay_mts")}</option>
            <option value="Megafon"{if $processor_params.payment_method == "Megafon"} selected="selected"{/if}>{__("pay_megafon")}</option>
            <option value="Rostelecom"{if $processor_params.payment_method == "Rostelecom"} selected="selected"{/if}>{__("pay_rostelecom")}</option>
            <option value="SberBankSpasibo"{if $processor_params.payment_method == "SberBankSpasibo"} selected="selected"{/if}>{__("pay_sberbank_spasibo")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sing_algo">{__("addons.rus_payments.sign_algo")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][sing_algo]" id="sing_alg">
            <option value="md5" {if $processor_params.sing_algo == "md5"}selected="selected"{/if}>MD5</option>
            <option value="sha256" {if $processor_params.sing_algo == "sha256"}selected="selected"{/if}>SHA256</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="currency_{$payment_id}">{__("currency")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][currency]" id="currency_{$payment_id}">
            <option value="RUB"{if $processor_params.currency == "RUB"} selected="selected"{/if}>{__("currency_code_rur")}</option>
            <option value="EUR"{if $processor_params.currency == "EUR"} selected="selected"{/if}>{__("currency_code_eur")}</option>
            <option value="USD"{if $processor_params.currency == "USD"} selected="selected"{/if}>{__("currency_code_usd")}</option>
        </select>
    </div>
</div>

