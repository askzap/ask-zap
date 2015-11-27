<div class="control-group">
	<label class="control-label" for="mnt_payment_server">{__("mnt_payment_server")}:</label>
	<div class="controls">
		<select name="payment_data[processor_params][mnt_payment_server]" id="mnt_payment_server">
			<option value="www.payanyway.ru" {if $processor_params.mnt_payment_server == "www.payanyway.ru"}selected="selected"{/if}>www.payanyway.ru</option>
			<option value="demo.moneta.ru" {if $processor_params.mnt_payment_server == "demo.moneta.ru"}selected="selected"{/if}>demo.moneta.ru</option>
		</select>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="mnt_id">{__("mnt_id")}:</label>
	<div class="controls">
		<input type="text" name="payment_data[processor_params][mnt_id]" id="mnt_id" value="{$processor_params.mnt_id}" class="input-text" size="60" />
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="mnt_dataintegrity_code">{__("mnt_dataintegrity_code")}:</label>
	<div class="controls">
		<input type="text" name="payment_data[processor_params][mnt_dataintegrity_code]" id="mnt_dataintegrity_code" value="{$processor_params.mnt_dataintegrity_code}" class="input-text" size="60" />
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="mnt_test_mode">{__("test_live_mode")}:</label>
	<div class="controls">
		<select name="payment_data[processor_params][mnt_test_mode]" id="mnt_test_mode">
			<option value="0" {if $processor_params.mnt_test_mode == "0"}selected="selected"{/if}>{__("live")}</option>
			<option value="1" {if $processor_params.mnt_test_mode == "1"}selected="selected"{/if}>{__("test")}</option>
		</select>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="currency_{$payment_id}">{__("currency")}:</label>
	<div class="controls">
		<select name="payment_data[processor_params][currency]" id="currency_{$payment_id}">
			<option value="RUB"{if $processor_params.currency eq "RUB"} selected="selected"{/if}>{__("currency_code_rub")}</option>
			<option value="EUR"{if $processor_params.currency eq "EUR"} selected="selected"{/if}>{__("currency_code_eur")}</option>
			<option value="USD"{if $processor_params.currency eq "USD"} selected="selected"{/if}>{__("currency_code_usd")}</option>
			<option value="GBP"{if $processor_params.currency eq "GBP"} selected="selected"{/if}>{__("currency_code_gbp")}</option>
			<option value="CAD"{if $processor_params.currency eq "CAD"} selected="selected"{/if}>{__("currency_code_cad")}</option>
			<option value="AUD"{if $processor_params.currency eq "AUD"} selected="selected"{/if}>{__("currency_code_aud")}</option>
			<option value="CHF"{if $processor_params.currency eq "CHF"} selected="selected"{/if}>{__("currency_code_chf")}</option>
		</select>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="payment_system">{__("payment_system")}:</label>
	<div class="controls">
		<select name="payment_data[processor_params][payment_system]" id="payment_system" {if !$processor_params.unitId}onchange="fn_payanyway_change_ps($(this).val())"{/if}>
			<option value="payanyway"{if $processor_params.payment_system eq "payanyway"} selected="selected"{/if}>{__("ps_payanyway")}</option>
			<option value="banktransfer"{if $processor_params.payment_system eq "banktransfer"} selected="selected"{/if}>{__("ps_banktransfer")}</option>
			<option value="ciberpay"{if $processor_params.payment_system eq "ciberpay"} selected="selected"{/if}>{__("ps_ciberpay")}</option>
			<option value="comepay"{if $processor_params.payment_system eq "comepay"} selected="selected"{/if}>{__("ps_comepay")}</option>
			<option value="contact"{if $processor_params.payment_system eq "contact"} selected="selected"{/if}>{__("ps_contact")}</option>
			<option value="elecsnet"{if $processor_params.payment_system eq "elecsnet"} selected="selected"{/if}>{__("ps_elecsnet")}</option>
			<option value="euroset"{if $processor_params.payment_system eq "euroset"} selected="selected"{/if}>{__("ps_euroset")}</option>
			<option value="forward"{if $processor_params.payment_system eq "forward"} selected="selected"{/if}>{__("ps_forward")}</option>
			<option value="gorod"{if $processor_params.payment_system eq "gorod"} selected="selected"{/if}>{__("ps_gorod")}</option>
			<option value="mcb"{if $processor_params.payment_system eq "mcb"} selected="selected"{/if}>{__("ps_mcb")}</option>
			<option value="moneta"{if $processor_params.payment_system eq "moneta"} selected="selected"{/if}>{__("ps_moneta")}</option>
			<option value="moneymail"{if $processor_params.payment_system eq "moneymail"} selected="selected"{/if}>{__("ps_moneymail")}</option>
			<option value="novoplat"{if $processor_params.payment_system eq "novoplat"} selected="selected"{/if}>{__("ps_novoplat")}</option>
			<option value="plastic"{if $processor_params.payment_system eq "plastic"} selected="selected"{/if}>{__("ps_plastic")}</option>
			<option value="platika"{if $processor_params.payment_system eq "platika"} selected="selected"{/if}>{__("ps_platika")}</option>
			<option value="post"{if $processor_params.payment_system eq "post"} selected="selected"{/if}>{__("ps_post")}</option>
			<option value="wallet"{if $processor_params.payment_system eq "wallet"} selected="selected"{/if}>{__("ps_wallet")}</option>
			<option value="webmoney"{if $processor_params.payment_system eq "webmoney"} selected="selected"{/if}>{__("ps_webmoney")}</option>
			<option value="yandex"{if $processor_params.payment_system eq "yandex"} selected="selected"{/if}>{__("ps_yandex")}</option>
		</select>
	</div>
</div>

<div id="ps_settings">
	<label class="control-label" >unitId:</label>
	<div class="controls">
		<div id="banktransfer_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][banktransfer][invoice]" value="1">
			<input type="hidden" name="payment_data[processor_params][banktransfer][accountId]" value="75983431"/>
			<input type="text" name="payment_data[processor_params][banktransfer][unitId]" value="{if !isset($processor_params.banktransfer.unitId) || !$processor_params.banktransfer.unitId}705000{else}{$processor_params.banktransfer.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="ciberpay_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][ciberpay][invoice]" value="1">
			<input type="hidden" name="payment_data[processor_params][ciberpay][accountId]" value="19357960"/>
			<input type="text" name="payment_data[processor_params][ciberpay][unitId]" value="{if !isset($processor_params.ciberpay.unitId) || !$processor_params.ciberpay.unitId}489755{else}{$processor_params.ciberpay.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="comepay_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][comepay][invoice]" value="1">
			<input type="hidden" name="payment_data[processor_params][comepay][accountId]" value="47654606"/>
			<input type="text" name="payment_data[processor_params][comepay][unitId]" value="{if !isset($processor_params.comepay.unitId) || !$processor_params.comepay.unitId}228820{else}{$processor_params.comepay.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="contact_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][contact][invoice]" value="1">
			<input type="hidden" name="payment_data[processor_params][contact][accountId]" value="26"/>
			<input type="text" name="payment_data[processor_params][contact][unitId]" value="{if !isset($processor_params.contact.unitId) || !$processor_params.contact.unitId}1028{else}{$processor_params.contact.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="elecsnet_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][elecsnet][invoice]" value="1">
			<input type="hidden" name="payment_data[processor_params][elecsnet][accountId]" value="10496472"/>
			<input type="text" name="payment_data[processor_params][elecsnet][unitId]" value="{if !isset($processor_params.elecsnet.unitId) || !$processor_params.elecsnet.unitId}232821{else}{$processor_params.elecsnet.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="euroset_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][euroset][invoice]" value="1">
			<input type="hidden" name="payment_data[processor_params][euroset][accountId]" value="136"/>
			<input type="text" name="payment_data[processor_params][euroset][unitId]" value="{if !isset($processor_params.euroset.unitId) || !$processor_params.euroset.unitId}248362{else}{$processor_params.euroset.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="forward_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][forward][invoice]" value="1">
			<input type="hidden" name="payment_data[processor_params][forward][accountId]" value="116"/>
			<input type="text" name="payment_data[processor_params][forward][unitId]" value="{if !isset($processor_params.forward.unitId) || !$processor_params.forward.unitId}83046{else}{$processor_params.forward.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="gorod_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][gorod][invoice]" value="1">
			<input type="hidden" name="payment_data[processor_params][gorod][accountId]" value="152"/>
			<input type="text" name="payment_data[processor_params][gorod][unitId]" value="{if !isset($processor_params.gorod.unitId) || !$processor_params.gorod.unitId}426904{else}{$processor_params.gorod.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="mcb_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][mcb][invoice]" value="1">
			<input type="hidden" name="payment_data[processor_params][mcb][accountId]" value="143"/>
			<input type="text" name="payment_data[processor_params][mcb][unitId]" value="{if !isset($processor_params.mcb.unitId) || !$processor_params.mcb.unitId}295339{else}{$processor_params.mcb.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="moneta_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][moneta][invoice]" value="0">
			<input type="text" name="payment_data[processor_params][moneta][unitId]" value="{if !isset($processor_params.moneta.unitId) || !$processor_params.moneta.unitId}1015{else}{$processor_params.moneta.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="moneymail_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][moneymail][invoice]" value="0">
			<input type="text" name="payment_data[processor_params][moneymail][unitId]" value="{if !isset($processor_params.moneymail.unitId) || !$processor_params.moneymail.unitId}1038{else}{$processor_params.moneymail.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="novoplat_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][novoplat][invoice]" value="1">
			<input type="hidden" name="payment_data[processor_params][novoplat][accountId]" value="80314912"/>
			<input type="text" name="payment_data[processor_params][novoplat][unitId]" value="{if !isset($processor_params.novoplat.unitId) || !$processor_params.novoplat.unitId}281129{else}{$processor_params.novoplat.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="plastic_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][plastic][invoice]" value="0">
			<input type="text" name="payment_data[processor_params][plastic][unitId]" value="{if !isset($processor_params.plastic.unitId) || !$processor_params.plastic.unitId}499669{else}{$processor_params.plastic.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="platika_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][platika][invoice]" value="1">
			<input type="hidden" name="payment_data[processor_params][platika][accountId]" value="15662295"/>
			<input type="text" name="payment_data[processor_params][platika][unitId]" value="{if !isset($processor_params.platika.unitId) || !$processor_params.platika.unitId}226272{else}{$processor_params.platika.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="post_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][post][invoice]" value="1" />
			<input type="hidden" name="payment_data[processor_params][post][accountId]" value="15" />
			<input type="text" name="payment_data[processor_params][post][unitId]" value="{if !isset($processor_params.post.unitId) || !$processor_params.post.unitId}1029{else}{$processor_params.post.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="wallet_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][wallet][invoice]" value="0">
			<input type="text" name="payment_data[processor_params][wallet][unitId]" value="{if !isset($processor_params.wallet.unitId) || !$processor_params.wallet.unitId}310212{else}{$processor_params.wallet.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="webmoney_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][webmoney][invoice]" value="0">
			<input type="text" name="payment_data[processor_params][webmoney][unitId]" value="{if !isset($processor_params.webmoney.unitId) || !$processor_params.webmoney.unitId}1017{else}{$processor_params.webmoney.unitId}{/if}" class="input-text" size="10" />
		</div>
		<div id="yandex_settings" style="display:none;">
			<input type="hidden" name="payment_data[processor_params][yandex][invoice]" value="0">
			<input type="text" name="payment_data[processor_params][yandex][unitId]" value="{if !isset($processor_params.yandex.unitId) || !$processor_params.yandex.unitId}1020{else}{$processor_params.yandex.unitId}{/if}" class="input-text" size="10" />
		</div>
		<p class="description">{__("text_payanyway_ids_notice")}</p>
	</div>
</div>

<script type="text/javascript" language="javascript 1.2">
//<![CDATA[
fn_payanyway_change_ps($('#payment_system').val());
function fn_payanyway_change_ps (data, unitId)
{ldelim}
	if (data == 'payanyway')
		$('#ps_settings').hide();
	else
		$('#ps_settings').show();
	$('#ps_settings div[id$="_settings"]').hide();
	$('#'+data+'_settings').toggle();
{rdelim}
//]]>
</script>

<div class="control-group">
	<label class="control-label" for="payanyway_login">{__("payanyway_login")}:</label>
	<div class="controls">
		<input type="text" name="payment_data[processor_params][payanyway_login]" id="payanyway_login" value="{$processor_params.payanyway_login}" class="input-text" size="60" />
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="payanyway_password">{__("payanyway_password")}:</label>
	<div class="controls">
		<input type="text" name="payment_data[processor_params][payanyway_password]" id="payanyway_password" value="{$processor_params.payanyway_password}" class="input-text" size="60" />
	</div>
</div>
