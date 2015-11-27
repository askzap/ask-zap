{assign var="callback_url" value="payment_notification.notify?payment=qiwi_rest"|fn_url:'C':'https'}
<p>{__("text_qiwi_rest_callback_url", ["[callback_url]" => $callback_url])}</p>

<hr>

<div class="control-group">
    <label class="control-label" for="qiwi_shop_id">{__("addons.qiwi_rest.shop_id")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][shop_id]" id="qiwi_shop_id" value="{$processor_params.shop_id}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="qiwi_login">{__("addons.qiwi_rest.login")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][login]" id="qiwi_login" value="{$processor_params.login}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="qiwi_password">{__("addons.qiwi_rest.password")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][passwd]" id="qiwi_password" value="{$processor_params.passwd}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="qiwi_lifetime">{__("addons.qiwi_rest.select_lifetime")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][lifetime]" id="qiwi_lifetime">
            <option value="60" {if $processor_params.lifetime == "60"}selected="selected"{/if}>1 {__("addons.qiwi_rest.hour")}</option>
            <option value="720" {if $processor_params.lifetime == "720"}selected="selected"{/if}>12 {__("addons.qiwi_rest.how_hours")}</option>
            <option value="1440" {if $processor_params.lifetime == "1440"}selected="selected"{/if}>1 {__("addons.qiwi_rest.day")}</option>
            <option value="10080" {if $processor_params.lifetime == "10080"}selected="selected"{/if}>7 {__("addons.qiwi_rest.how_days")}</option>
            <option value="20160" {if $processor_params.lifetime == "20160"}selected="selected"{/if}>14 {__("addons.qiwi_rest.how_days")}</option>
            <option value="43200" {if $processor_params.lifetime == "43200"}selected="selected"{/if}>30 {__("addons.qiwi_rest.how_days")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="qiwi_invoice_type">{__("addons.qiwi_rest.invoice_type")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][invoice_type]" id="qiwi_invoice_type">
            <option value="create" {if $processor_params.invoice_type == "create"}selected="selected"{/if}>{__("addons.qiwi_rest.invoice_create")}</option>
            <option value="external" {if $processor_params.invoice_type == "external"}selected="selected"{/if}>{__("addons.qiwi_rest.invoice_external_page")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="logging">{__("addons.qiwi_rest.logging")}:</label>
    <div class="controls">
        <input type="checkbox" name="payment_data[processor_params][logging]" id="logging" value="Y" {if $processor_params.logging == 'Y'} checked="checked"{/if}/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="currency">{__("currency")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][currency]" id="currency">
            <option value="RUB" {if $processor_params.currency == "RUB"}selected="selected"{/if}>{__("currency_code_rur")}</option>
            <option value="EUR" {if $processor_params.currency == "EUR"}selected="selected"{/if}>{__("currency_code_eur")}</option>
            <option value="USD" {if $processor_params.currency == "USD"}selected="selected"{/if}>{__("currency_code_usd")}</option>
        </select>
    </div>
</div>

{include file="common/subheader.tpl" title=__("addons.qiwi_rest.text_status_map") target="#text_qiwi_status_map"}

<div id="text_qiwi_status_map" class="in collapse">
    {assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}

    <div class="control-group">
        <label class="control-label" for="elm_qiwi_waiting">{__("addons.qiwi_rest.waiting")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][statuses][waiting]" id="elm_qiwi_waiting">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}" {if (isset($processor_params.statuses.waiting) && $processor_params.statuses.waiting == $k) || (!isset($processor_params.statuses.waiting) && $k == 'O')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="elm_qiwi_paid">{__("addons.qiwi_rest.paid")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][statuses][paid]" id="elm_qiwi_paid">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}" {if (isset($processor_params.statuses.paid) && $processor_params.statuses.paid == $k) || (!isset($processor_params.statuses.paid) && $k == 'P')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="elm_qiwi_refunded">{__("addons.qiwi_rest.unpaid")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][statuses][refunded]" id="elm_qiwi_refunded">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}" {if (isset($processor_params.statuses.refunded) && $processor_params.statuses.refunded == $k) || (!isset($processor_params.statuses.refunded) && $k == 'I')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="elm_qiwi_rejected">{__("addons.qiwi_rest.rejected")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][statuses][rejected]" id="elm_qiwi_rejected">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}" {if (isset($processor_params.statuses.rejected) && $processor_params.statuses.rejected == $k) || (!isset($processor_params.statuses.rejected) && $k == 'I')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="elm_qiwi_expired">{__("addons.qiwi_rest.expired")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][statuses][expired]" id="elm_qiwi_expired">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}" {if (isset($processor_params.statuses.expired) && $processor_params.statuses.expired == $k) || (!isset($processor_params.statuses.expired) && $k == 'I')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>