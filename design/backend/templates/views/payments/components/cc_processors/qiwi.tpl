{if !soap_exist}
<p>{__("qiwi_soap_attention")}</p>
{/if}
{assign var="q_url" value="http"|fn_payment_url:"qiwi.php?parameter=update"}
<p>{__("qiwi_url_notice", ["[qiwi_url]" => $q_url])}</p>
<p>{__("qiwi_login_notice")}</p>
<p>{__("qiwi_alarm_notify")}</p>
<hr>

<div class="control-group">
    <label class="control-label" for="qiwi_login">{__("rus_payments.qiwi_api_id")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][login]" id="qiwi_login" value="{$processor_params.login}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="qiwi_password">{__("password")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][passwd]" id="qiwi_password" value="{$processor_params.passwd}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="qiwi_alarm">{__("qiwi_select_alarm")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][alarm]" id="qiwi_alarm">
            <option value="0" {if $processor_params.alarm == "0"}selected="selected"{/if}>{__("qiwi_no_alarm")}</option>
            <option value="1" {if $processor_params.alarm == "1"}selected="selected"{/if}>{__("qiwi_sms_alarm")}</option>
            <option value="2" {if $processor_params.alarm == "2"}selected="selected"{/if}>{__("qiwi_call_alarm")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="qiwi_lifetime">{__("qiwi_select_lifetime")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][lifetime]" id="qiwi_lifetime">
            <option value="60" {if $processor_params.lifetime == "60"}selected="selected"{/if}>1 {__("rus_payments.hour")}</option>
            <option value="720" {if $processor_params.lifetime == "720"}selected="selected"{/if}>12 {__("rus_payments.how_hours")}</option>
            <option value="1440" {if $processor_params.lifetime == "1440"}selected="selected"{/if}>1 {__("rus_payments.day")}</option>
            <option value="10080" {if $processor_params.lifetime == "10080"}selected="selected"{/if}>7 {__("rus_payments.how_days")}</option>
            <option value="20160" {if $processor_params.lifetime == "20160"}selected="selected"{/if}>14 {__("rus_payments.how_days")}</option>
            <option value="43200" {if $processor_params.lifetime == "43200"}selected="selected"{/if}>30 {__("rus_payments.how_days")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="qiwi_location">{__("qiwi_select_location")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][location]" id="qiwi_location">
            <option value="https://mylk.qiwi.ru/services/ishop" {if $processor_params.location == "https://mylk.qiwi.ru/services/ishop"}selected="selected"{/if}>https://mylk.qiwi.ru/services/ishop</option>
            <option value="http://ishop.qiwi.ru/services/ishop" {if $processor_params.location == "http://ishop.qiwi.ru/services/ishop"}selected="selected"{/if}>http://ishop.qiwi.ru/services/ishop</option>
        </select>
    </div>
</div>