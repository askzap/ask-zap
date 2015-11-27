{* rus_build_pack dbazhenov *}

<script type="text/javascript">
//<![CDATA[
(function(_, $) {
    $(document).ready(function() {
        fn_get_rbx_currencies();
        $('#rbx_get_currencies').on('click', fn_get_rbx_currencies);
    });

    function fn_get_rbx_currencies() {
        var merchantid = $('#rbx_merchantid').val();
        $.ceAjax('request', '{fn_url("payment_notification.rbx_get_currencies")}', {
            data: {
                payment: 'robokassa',
                merchantid: merchantid,
                result_ids: 'rbx_currency_div',
                payment_id: {$smarty.request.payment_id},
            },
        });
    }
}(Tygh, Tygh.$));
//]]>
</script>
{assign var="r_url" value="payment_notification.result?payment=robokassa"|fn_url:'C':'http'}
{assign var="p_url" value="payment_notification.return?payment=robokassa"|fn_url:'C':'http'}
{assign var="f_url" value="payment_notification.cancel?payment=robokassa"|fn_url:'C':'http'}

<div>
    {__("text_robokassa_notice", ["[r_url]" => $r_url, "[p_url]" => $p_url, "[f_url]" => $f_url])}
</div> 
<hr>

<div class="control-group">
    <label class="control-label" for="rbx_merchantid">{__("merchantid")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][merchantid]" id="rbx_merchantid" value="{$processor_params.merchantid}"  size="60"><a href="#" id="rbx_get_currencies">{__("payments.robokassa.get_currencies")}</a>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="rbx_password1">{__("password1")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][password1]" id="rbx_password1" value="{$processor_params.password1}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="rbx_password2">{__("password2")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][password2]" id="rbx_password2" value="{$processor_params.password2}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="rbx_descr">{__("description")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][details]" id="rbx_descr" value="{$processor_params.details}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="rbx_mode">{__("test_live_mode")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][mode]" id="rbx_mode">
            <option value="test"{if $processor_params.mode == 'test'} selected="selected"{/if}>{__("test")}</option>
            <option value="live"{if $processor_params.mode == 'live'} selected="selected"{/if}>{__("live")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="currency_{$payment_id}">{__("currency")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][currency]" id="currency_{$payment_id}">
            <option value="RUB"{if $processor_params.currency == "RUB"} selected="selected"{/if}>{__("currency_code_rub")}</option>
            <option value="USD"{if $processor_params.currency == "USD"} selected="selected"{/if}>{__("currency_code_usd")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="rbx_currency">{__("payment_method")}:</label>
    {include file="views/payments/components/cc_processors/robokassa_cur_selectbox.tpl"}
</div>
