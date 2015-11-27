{* rus_build_pack dbazhenov *}

<div class="control-group">
    <label class="control-label" for="sbrf_recepient_name">{__("sbrf_recepient_name")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_recepient_name]" id="sbrf_recepient_name" value="{$processor_params.sbrf_recepient_name}"  size="80">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sbrf_kpp">{__("sbrf_kpp")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_kpp]" id="sbrf_kpp" value="{$processor_params.sbrf_kpp}"  size="9" maxlength="9">
    </div>
</div>
    
<div class="control-group">
    <label class="control-label" for="sbrf_inn">{__("sbrf_inn")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_inn]" id="sbrf_inn" value="{$processor_params.sbrf_inn}"  size="12" maxlength="12">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sbrf_okato_code">{__("sbrf_okato_code")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_okato_code]" id="sbrf_okato_code" value="{$processor_params.sbrf_okato_code}"  size="11" maxlength="11">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sbrf_settlement_account">{__("sbrf_settlement_account")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_settlement_account]" id="sbrf_settlement_account" value="{$processor_params.sbrf_settlement_account}"  size="20" maxlength="20">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sbrf_account_id">{__("sbrf_account_id")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_account_id]" id="sbrf_account_id" value="{$processor_params.sbrf_account_id}"  size="80">
    </div>
</div>
    
<div class="control-group">
    <label class="control-label" for="sbrf_bank">{__("sbrf_bank")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_bank]" id="sbrf_bank" value="{$processor_params.sbrf_bank}"  size="80">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sbrf_bik">{__("sbrf_bik")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_bik]" id="sbrf_bik" value="{$processor_params.sbrf_bik}"  size="9" maxlength="9">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sbrf_cor_account">{__("sbrf_cor_account")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_cor_account]" id="sbrf_cor_account" value="{$processor_params.sbrf_cor_account}"  size="20" maxlength="20">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sbrf_kbk">{__("sbrf_kbk")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_kbk]" id="sbrf_kbk" value="{$processor_params.sbrf_kbk}"  size="29" maxlength="29">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sbrf_prefix">{__("addons.rus_payments.sbrf_prefix")} {include file="common/tooltip.tpl" tooltip=__("addons.rus_payments.sbrf_prefix_details")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_prefix]" id="sbrf_prefix" value="{$processor_params.sbrf_prefix}" size="80">
    </div>
</div>

<div class="control-group hidden">
    <input type="hidden" name="payment_data[processor_params][sbrf_enabled]" value="Y">
</div>

<div class="control-group">
    <label class="control-label" for="sbrf_qr_resolution">{__("sbrf_qr_resolution")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_qr_resolution]" id="sbrf_qr_resolution" value="{if $processor_params.sbrf_qr_resolution}{$processor_params.sbrf_qr_resolution}{else}200{/if}"  size="10" maxlength="10">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="sbrf_qr_print_size">{__("sbrf_qr_print_size")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][sbrf_qr_print_size]" id="sbrf_qr_print_size" value="{if $processor_params.sbrf_qr_print_size}{$processor_params.sbrf_qr_print_size}{else}200{/if}"  size="10" maxlength="10">
    </div>
</div>

{assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}

<div class="control-group">
    <label class="control-label" for="sbrf_invoice_order_status">{__("sbrf_invoice_order_status")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][invoice_order_status]" id="sbrf_invoice_order_status">
            {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if $processor_params.invoice_order_status == $k}selected="selected"{/if}>{$s}</option>
            {/foreach}
        </select>
    </div>
</div>
