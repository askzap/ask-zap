{script src="js/addons/rus_payments/yandex_money.js"}

{assign var="avisio_url" value="payment_notification.payment_aviso?payment=yandex_money"|fn_url:'C':'https'}
{assign var="check_url" value="payment_notification.check_order?payment=yandex_money"|fn_url:'C':'https'}
{assign var="fail_url" value="payment_notification.error?payment=yandex_money"|fn_url:'C':'https'}
{assign var="success_url" value="payment_notification.ok?payment=yandex_money"|fn_url:'C':'https'}

{include file="common/subheader.tpl" title=__("information") target="#yandex_money_payment_instruction_`$smarty.request.payment_id`"}
<div id="yandex_money_payment_instruction_{$smarty.request.payment_id}" class="in collapse">
{hook name="rus_payments:yandex_money_payment_instructions"}
{__("text_yandex_money_urls", ["[avisio_url]" => $avisio_url, "[check_url]" => $check_url, "[fail_url]" => $fail_url, "[success_url]" => $success_url])}
{/hook}

{hook name="rus_payments:yandex_market_processor_https_text"}
{assign var="check_https" value="HTTPS"|defined}

{if !$check_https}
{__("text_yandex_money_https")}
{/if}
{/hook}
</div>


{include file="common/subheader.tpl" title=__("settings") target="#yandex_money_payment_settings_`$smarty.request.payment_id`"}
<div id="yandex_money_payment_settings_{$smarty.request.payment_id}" class="in collapse">

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
    <label class="control-label" for="shop_id">{__("shop_id")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][shop_id]" id="shop_id" value="{$processor_params.shop_id}" class="input-text-large"  size="60" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="scid">{__("scid")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][scid]" id="scid" value="{$processor_params.scid}" class="input-text-large"  size="60" />
    </div>
</div>

<div class="control-group" id="yandex_md5_div_{$smarty.request.payment_id}">
    <label class="control-label" for="md5_shoppassword_{$smarty.request.payment_id}">{__("md5_shoppassword")}:</label>
    <div class="controls">
        <input type="text" maxlength="20" size="21" name="payment_data[processor_params][md5_shoppassword]" id="md5_shoppassword_{$smarty.request.payment_id}" value="{if $ya_md5}{$ya_md5}{else}{$processor_params.md5_shoppassword}{/if}" class="input-text-large span4"  size="60" />
        <br />
        <a href="#" id="md5_generate_link_{$smarty.request.payment_id}">{__("generate")}</a>
    </div>
    
    <script type="text/javascript" class="cm-ajax-force">
    //<![CDATA[
        (function(_, $) {
        $(document).ready(function() {
          $('#md5_generate_link_{$smarty.request.payment_id}').on('click', fn_get_md5_password_{$smarty.request.payment_id});
        });

        function fn_get_md5_password_{$smarty.request.payment_id}() {
          var md5_shoppassword = $('#md5_shoppassword_{$smarty.request.payment_id}').val();
          $.ceAjax('request', '{fn_url("payments.yandex_get_md5_password")}', {
          data: {
              payment: 'yandex_money',
              md5_shoppassword: md5_shoppassword,
              result_ids: 'yandex_md5_div_' + {$smarty.request.payment_id},
              payment_id: {$smarty.request.payment_id},
          },
          });
        }
        }(Tygh, Tygh.$));
    //]]>
    </script>
<!--yandex_md5_div_{$smarty.request.payment_id}--></div>

</div>

<div class="control-group">
    <label class="control-label" for="logging">{__("addons.rus_payments.logging")}:</label>
    <div class="controls">
        <input type="checkbox" name="payment_data[processor_params][logging]" id="logging" value="Y" {if $processor_params.logging == 'Y'} checked="checked"{/if} class="input-text-large"  size="60" />
    </div>
</div>

{hook name="rus_payments:yandex_money_processor_mws_settings"}

{include file="common/subheader.tpl" title=__("yandex_merchant_web_services") target="#yandex_merchant_web_services_`$smarty.request.payment_id`"}
<div id="yandex_merchant_web_services_{$smarty.request.payment_id}" class="in collapse">
    <fieldset>

        <div class="control-group">
            <label class="control-label" for="certificate_filename">{__("yandex_certificate_filename")} {include file="common/tooltip.tpl" tooltip=__("addons.rus_payments.pkcs12_format")} :</label>
            <div class="controls" id="certificate_filename">
                {if $processor_params.certificate_filename}
                    <div class="text-type-value pull-left">
                        {$processor_params.certificate_filename}
                        <a href="{'payments.delete_certificate?payment_id='|cat:$payment_id|fn_url}" class="cm-ajax" data-ca-target-id="certificate_filename">
                            <i class="icon-remove-sign cm-tooltip hand" title="{__('remove')}"></i>
                        </a>
                    </div>
                {/if}

                <div {if $processor_params.certificate_filename}class="clear"{/if}>{include file="common/fileuploader.tpl" var_name="payment_certificate[]"}</div>
            <!--certificate_filename--></div>
        </div>

        <div class="control-group">
            <label class="control-label" for="yandex_p12_password">{__("yandex_p12_password")} {include file="common/tooltip.tpl" tooltip=__("addons.rus_payments.p12_password_descr")}:</label>
            <div class="controls">
                <input type="text" name="payment_data[processor_params][p12_password]" id="yandex_p12_password" value="{$processor_params.p12_password}" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="yandex_postponed_payments_enabled">{__("yandex_postponed_payments_enabled")}:</label>
            <div class="controls"><input type="checkbox" name="payment_data[processor_params][postponed_payments_enabled]" id="yandex_postponed_payments_enabled" class="cm-yandex-money-mws-enabled" value="Y"{if $processor_params.postponed_payments_enabled == "Y"} checked="checked"{/if}></div>
        </div>

        {assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}

        <div class="control-group">
            <label class="control-label" for="yandex_unconfirmed_order_status">{__("yandex_unconfirmed_order_status")}:</label>
            <div class="controls">
                <select name="payment_data[processor_params][unconfirmed_order_status]" id="yandex_unconfirmed_order_status">
                    {foreach from=$statuses item="s" key="k"}
                        <option value="{$k}" {if $processor_params.unconfirmed_order_status == $k || !$processor_params.unconfirmed_order_status && $k == 'yandex_money_postponed_order_status'|fn_get_storage_data}selected="selected"{/if}>{$s}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="yandex_confirmed_order_status">{__("yandex_confirmed_order_status")}:</label>
            <div class="controls">
                <select name="payment_data[processor_params][confirmed_order_status]" id="yandex_confirmed_order_status">
                    {foreach from=$statuses item="s" key="k"}
                        <option value="{$k}" {if $processor_params.confirmed_order_status == $k || !$processor_params.confirmed_order_status && $k == 'P'}selected="selected"{/if}>{$s}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="yandex_canceled_order_status">{__("yandex_canceled_order_status")}:</label>
            <div class="controls">
                <select name="payment_data[processor_params][canceled_order_status]" id="yandex_canceled_order_status">
                    {foreach from=$statuses item="s" key="k"}
                        <option value="{$k}" {if $processor_params.canceled_order_status == $k || !$processor_params.canceled_order_status && $k == 'I'}selected="selected"{/if}>{$s}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="yandex_returns_enabled">{__("yandex_returns_enabled")}:</label>
            <div class="controls"><input type="checkbox" name="payment_data[processor_params][returns_enabled]" id="yandex_returns_enabled" value="Y"{if $processor_params.returns_enabled == "Y"} checked="checked"{/if}></div>
        </div>

        <div class="control-group">
            <label class="control-label" for="yandex_returned_order_status">{__("yandex_returned_order_status")}:</label>
            <div class="controls">
                <select name="payment_data[processor_params][returned_order_status]" id="yandex_returned_order_status">
                    {foreach from=$statuses item="s" key="k"}
                        <option value="{$k}" {if $processor_params.returned_order_status == $k || !$processor_params.returned_order_status && $k == 'yandex_money_refunded_order_status'|fn_get_storage_data}selected="selected"{/if}>{$s}</option>
                    {/foreach}
                </select>
            </div>
        </div>

    </fieldset>
</div>

{/hook}

{include file="common/subheader.tpl" title=__("yandex_payment_types") target="#yandex_payment_types_`$smarty.request.payment_id`"}
<div id="yandex_payment_types_{$smarty.request.payment_id}" class="in collapse">

    <fieldset>

    <div class="control-group">
        <label class="control-label" for="yandex_money_payment_yandex">{__("yandex_payment_yandex")}:</label>
        <div class="controls">
            <input type="checkbox" name="payment_data[processor_params][payments][pc]" id="yandex_money_payment_yandex" class="cm-yandex-money-payment-type" value="PC"{if $processor_params.payments && $processor_params.payments.pc} checked="checked"{/if}></div>
    </div>
    
    <div class="control-group">
        <label class="control-label" for="yandex_money_payment_card">{__("yandex_payment_card")}:</label>
        <div class="controls"><input type="checkbox" name="payment_data[processor_params][payments][ac]" id="yandex_money_payment_card" class="cm-yandex-money-payment-type" value="AC"{if $processor_params.payments && $processor_params.payments.ac} checked="checked"{/if}></div>
    </div>
    
    <div class="control-group">
        <label class="control-label" for="yandex_money_payment_terminal">{__("yandex_payment_terminal")}:</label>
        <div class="controls"><input type="checkbox" name="payment_data[processor_params][payments][gp]" id="yandex_money_payment_terminal" class="cm-yandex-money-payment-type" value="GP"{if $processor_params.payments && $processor_params.payments.gp} checked="checked"{/if}></div>
    </div>
    
    <div class="control-group">
        <label class="control-label" for="yandex_money_payment_phone">{__("yandex_payment_phone")}:</label>
        <div class="controls"><input type="checkbox" name="payment_data[processor_params][payments][mc]" id="yandex_money_payment_phone" class="cm-yandex-money-payment-type" value="MC"{if $processor_params.payments && $processor_params.payments.mc} checked="checked"{/if}></div>
    </div>

    <div class="control-group">
        <label class="control-label" for="yandex_money_payment_webmoney">{__("yandex_payment_webmoney")}:</label>
        <div class="controls"><input type="checkbox" name="payment_data[processor_params][payments][wm]" id="yandex_money_payment_webmoney" class="cm-yandex-money-payment-type" value="WM"{if $processor_params.payments && $processor_params.payments.wm} checked="checked"{/if}></div>
    </div>

    <div class="control-group">
        <label class="control-label" for="yandex_money_payment_alfabank">{__("yandex_payment_alfabank")}:</label>
        <div class="controls"><input type="checkbox" name="payment_data[processor_params][payments][ab]" id="yandex_money_payment_alfabank" class="cm-yandex-money-payment-type" value="AB"{if $processor_params.payments && $processor_params.payments.ab} checked="checked"{/if}></div>
    </div>

    <div class="control-group">
        <label class="control-label" for="yandex_money_payment_sberbank">{__("yandex_payment_sberbank")}:</label>
        <div class="controls"><input type="checkbox" name="payment_data[processor_params][payments][sb]" id="yandex_money_payment_sberbank" class="cm-yandex-money-payment-type" value="SB"{if $processor_params.payments && $processor_params.payments.sb} checked="checked"{/if}></div>
    </div>

    <div class="control-group">
        <label class="control-label" for="yandex_money_payment_ma">{__("yandex_payment_masterpass")}:</label>
        <div class="controls"><input type="checkbox" name="payment_data[processor_params][payments][ma]" id="yandex_money_payment_ma" class="cm-yandex-money-payment-type" value="MA"{if $processor_params.payments && $processor_params.payments.ma} checked="checked"{/if}></div>
    </div>

    <div class="control-group">
        <label class="control-label" for="yandex_money_payment_psbank">{__("yandex_payment_psbank")}:</label>
        <div class="controls"><input type="checkbox" name="payment_data[processor_params][payments][pb]" id="yandex_money_payment_psbank" class="cm-yandex-money-payment-type" value="PB"{if $processor_params.payments && $processor_params.payments.pb} checked="checked"{/if}></div>
    </div>
    
    </fieldset>

</div>

<div class="control-group">
    <label class="control-label" for="currency_{$payment_id}">{__("currency")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][currency]" id="currency_{$payment_id}">
            <option value="RUB"{if $processor_params.currency == "RUB"} selected="selected"{/if}>{__("currency_code_rur")}</option>
            <option value="USD"{if $processor_params.currency == "USD"} selected="selected"{/if}>{__("currency_code_usd")}</option>
            <option value="EUR"{if $processor_params.currency == "EUR"} selected="selected"{/if}>{__("currency_code_eur")}</option>
            <option value="UAH"{if $processor_params.currency == "UAH"} selected="selected"{/if}>{__("currency_code_uah")}</option>
            <option value="KZT"{if $processor_params.currency == "KZT"} selected="selected"{/if}>{__("currency_code_kzt")}</option>
        </select>
    </div>
</div>
