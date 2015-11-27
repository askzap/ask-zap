{script src="js/addons/rus_spsr/func.js"}

{include file="common/subheader.tpl" title=__("shippings.spsr.user_data")}
<div class="control-group">
    <label class="control-label" for="spsr_old_piece_id">{__("shippings.spsr.piece_barcodes")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.piece_barcodes.tooltip")}:</label>
    <div class="controls">
        <input id="spsr_old_piece_id" type="text" name="shipping_data[service_params][piece_barcodes]" size="60" value="{$shipping.service_params.piece_barcodes}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_from_city_name">{__("shipping.spsr.from_city_name")} {include file="common/tooltip.tpl" tooltip=__("shipping.spsr.from_city_name.tooltip")}:</label>
    <div class="controls">
        <input id="spsr_from_city_name" type="text" name="shipping_data[service_params][from_city_name]" size="60" {if !empty($shipping.service_params.from_city_name)} value="{$shipping.service_params.from_city_name}" {else} value="{$city}" {/if} />
        <a href="#" id="spsr_get_city_link">{__("shipping.spsr.get_city_data")} {include file="common/tooltip.tpl" tooltip=__("shipping.spsr.get_city_data.tooltip")}</a>
    </div>
</div>

<div id="spsr_city_div">
{if $shipping.service_params.from_city_id && $shipping.service_params.from_city_owner_id !== false && !$spsr_new_city_data}
    <div class="control-group">
        <label class="control-label" for="spsr_city_id">{__("shipping.spsr.city_id")} {include file="common/tooltip.tpl" tooltip=__("shipping.spsr.city_id.tooltip")}:</label>
        <div class="controls">
            <input id="spsr_city_id" type="text" name="shipping_data[service_params][from_city_id]" size="60" value="{$shipping.service_params.from_city_id}"/>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="spsr_city_owner_id">{__("shipping.spsr.city_owner_id")}:</label>
        <div class="controls">
            <input id="spsr_city_owner_id" type="text" name="shipping_data[service_params][from_city_owner_id]" size="60" value="{$shipping.service_params.from_city_owner_id}"/>
        </div>
    </div>
{elseif $spsr_new_city_data}
    <div class="control-group">
        <label class="control-label" for="spsr_city_id">{__("shipping.spsr.city_id")} {include file="common/tooltip.tpl" tooltip=__("shipping.spsr.city_id.tooltip")}:</label>
        <div class="controls">
            <input id="spsr_city_id" type="text" name="shipping_data[service_params][from_city_id]" size="60" value="{$spsr_new_city_data.from_city_id}"/>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="spsr_city_owner_id">{__("shipping.spsr.city_owner_id")}:</label>
        <div class="controls">
            <input id="spsr_city_owner_id" type="text" name="shipping_data[service_params][from_city_owner_id]" size="60" value="{$spsr_new_city_data.from_city_owner_id}"/>
        </div>
    </div>
{else}
    <div class="control-group">
        <label class="control-label" for="spsr_city_id">{__("shipping.spsr.city_id")} {include file="common/tooltip.tpl" tooltip=__("shipping.spsr.city_id.tooltip")}:</label>
        <div class="controls">
            <input id="spsr_city_id" type="text" name="shipping_data[service_params][from_city_id]" size="60" value="{$from_city_id}"/>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="spsr_city_owner_id">{__("shipping.spsr.city_owner_id")}:</label>
        <div class="controls">
            <input id="spsr_city_owner_id" type="text" name="shipping_data[service_params][from_city_owner_id]" size="60" value="{$from_city_owner_id}"/>
        </div>
    </div>
{/if}
<!--spsr_city_div--></div>

{if $type_products}
<div class="control-group">
    <label class="control-label" for="spsr_product_type">{__("shippings.spsr.product_type")}  {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.product_type_default.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][default_product_type]" id="spsr_product_type">
            {foreach from=$type_products key=key_type item="type"}
                <option {if ($shipping.service_params.default_product_type == $type.Value) || (!$shipping.service_params.default_product_type && $key_type == 0)}selected="selected"{/if} value="{$type.Value}">{$type.Name}</option>
            {/foreach}
        </select>
    </div>
</div>
{/if}

<div class="control-group">
    <label class="control-label" for="spsr_invoice_plat_type">{__("shippings.spsr.invoice_plat_type")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][plat_type]" id="spsr_invoice_plat_type">
            <option value="1" {if $shipping.service_params.plat_type == "1"} selected="selected"{/if}>{__("shippings.spsr.plat_type.1")}</option>
            <option value="2" {if $shipping.service_params.plat_type == "2"} selected="selected"{/if}>{__("shippings.spsr.plat_type.2")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_to_be_called_for">{__("shippings.spsr.to_be_called_for")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][to_be_called_for]" id="spsr_to_be_called_for">
            <option value="0" {if $shipping.service_params.to_be_called_for == "0"} selected="selected"{/if}>{__("no")}</option>
            <option value="1" {if $shipping.service_params.to_be_called_for == "1"} selected="selected"{/if}>{__("yes")}</option>
        </select>
    </div>
</div>

{include file="common/subheader.tpl" title=__("shippings.spsr.invoice_additional_subheader")}
<div class="control-group">
    <label class="control-label" for="spsr_invoice_insurance_type">{__("shippings.spsr.invoice_insurance_type")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_insurance_type.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][insurance_type]" id="spsr_invoice_insurance_type">
            <option value="INS" {if $shipping.service_params.insurance_type == 'INS'} selected="selected"{/if}>{__("shippings.spsr.insurance_type.ins")}</option>
            <option value="VAL" {if $shipping.service_params.insurance_type == 'VAL'} selected="selected"{/if}>{__("shippings.spsr.insurance_type.val")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_invoice_dues_order">{__("shippings.spsr.invoice_dues_order")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][dues_order]" id="spsr_invoice_dues_order">
            <option value="0" {if $shipping.service_params.dues_order == "0"} selected="selected"{/if}>{__("no")}</option>
            <option value="1" {if $shipping.service_params.dues_order == "1"} selected="selected"{/if}>{__("yes")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_invoice_additional_cod">{__("shippings.spsr.invoice_additional_cod")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_additional_cod.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][cod]" id="spsr_invoice_additional_cod">
            <option value="1" {if $shipping.service_params.cod == '1'} selected="selected"{/if}>{__("yes")}</option>
            <option value="0" {if $shipping.service_params.cod == '0'} selected="selected"{/if}>{__("no")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_invoice_additional_part_delivery">{__("shippings.spsr.invoice_additional_part_delivery")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_additional_part_delivery.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][part_delivery]" id="spsr_invoice_additional_part_delivery">
            <option value="1" {if $shipping.service_params.part_delivery == '1'} selected="selected"{/if}>{__("yes")}</option>
            <option value="0" {if $shipping.service_params.part_delivery == '0'} selected="selected"{/if}>{__("no")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_invoice_additional_return_doc">{__("shippings.spsr.invoice_additional_return_doc")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_additional_return_doc.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][return_doc]" id="spsr_invoice_additional_return_doc">
            <option value="0" {if $shipping.service_params.return_doc == '0'} selected="selected"{/if}>{__("no")}</option>
            <option value="1" {if $shipping.service_params.return_doc == '1'} selected="selected"{/if}>{__("shippings.spsr.invoice_additional_return_doc.1")}</option>
            <option value="1" {if $shipping.service_params.return_doc == '2'} selected="selected"{/if}>{__("shippings.spsr.invoice_additional_return_doc.2")}</option>
            <option value="3" {if $shipping.service_params.return_doc == '3'} selected="selected"{/if}>{__("shippings.spsr.invoice_additional_return_doc.3")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_invoice_additional_check_contents">{__("shippings.spsr.invoice_additional_check_contents")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_additional_check_contents.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][check_contents]" id="spsr_invoice_additional_check_contents">
            <option value="1" {if $shipping.service_params.check_contents == '1'} selected="selected"{/if}>{__("yes")}</option>
            <option value="0" {if $shipping.service_params.check_contents == '0'} selected="selected"{/if}>{__("no")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_invoice_additional_verify">{__("shippings.spsr.invoice_additional_verify")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_additional_verify.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][verify]" id="spsr_invoice_additional_verify">
            <option value="1" {if $shipping.service_params.verify == '1'} selected="selected"{/if}>{__("yes")}</option>
            <option value="0" {if $shipping.service_params.verify == '0'} selected="selected"{/if}>{__("no")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_invoice_additional_try_on">{__("shippings.spsr.invoice_additional_try_on")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_additional_try_on.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][try_on]" id="spsr_invoice_additional_try_on">
            <option value="1" {if $shipping.service_params.try_on == '1'} selected="selected"{/if}>{__("yes")}</option>
            <option value="0" {if $shipping.service_params.try_on == '0'} selected="selected"{/if}>{__("no")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_invoice_additional_by_hand">{__("shippings.spsr.invoice_additional_by_hand")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_additional_by_hand.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][by_hand]" id="spsr_invoice_additional_by_hand">
            <option value="1" {if $shipping.service_params.by_hand == '1'} selected="selected"{/if}>{__("yes")}</option>
            <option value="0" {if $shipping.service_params.by_hand == '0'} selected="selected"{/if}>{__("no")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_invoice_additional_paid_by_receiver">{__("shippings.spsr.invoice_additional_paid_by_receiver")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_additional_paid_by_receiver.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][paid_by_receiver]" id="spsr_invoice_additional_paid_by_receiver">
            <option value="1" {if $shipping.service_params.paid_by_receiver == '1'} selected="selected"{/if}>{__("yes")}</option>
            <option value="0" {if $shipping.service_params.paid_by_receiver == '0'} selected="selected"{/if}>{__("no")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_invoice_additional_agreed_delivery">{__("shippings.spsr.invoice_additional_agreed_delivery")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_additional_agreed_delivery.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][agreed_delivery]" id="spsr_invoice_additional_agreed_delivery">
            <option value="1" {if $shipping.service_params.agreed_delivery == '1'} selected="selected"{/if}>{__("yes")}</option>
            <option value="0" {if $shipping.service_params.agreed_delivery == '0'} selected="selected"{/if}>{__("no")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_invoice_additional_idc">{__("shippings.spsr.invoice_additional_idc")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_additional_idc.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][idc]" id="spsr_invoice_additional_idc">
            <option value="1" {if $shipping.service_params.idc == '1'} selected="selected"{/if}>{__("yes")}</option>
            <option value="0" {if $shipping.service_params.idc == '0'} selected="selected"{/if}>{__("no")}</option>
        </select>
    </div>
</div>

{include file="common/subheader.tpl" title=__("shippings.spsr.invoice_sms_subheader")}
<div class="control-group">
    <label class="control-label" for="spsr_invoice_sms_to_shipper">{__("shippings.spsr.invoice_sms_to_shipper")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_sms_to_shipper.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][sms_to_shipper]" id="spsr_invoice_sms_to_shipper">
            <option value="1" {if $shipping.service_params.sms_to_shipper == '1'} selected="selected"{/if}>{__("yes")}</option>
            <option value="0" {if $shipping.service_params.sms_to_shipper == '0'} selected="selected"{/if}>{__("no")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_invoice_sms_to_receiver">{__("shippings.spsr.invoice_sms_to_receiver")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_sms_to_receiver.tooltip")}:</label>
    <div class="controls">
        <select name="shipping_data[service_params][sms_to_receiver]" id="spsr_invoice_sms_to_receiver">
            <option value="1" {if $shipping.service_params.sms_to_receiver == '1'} selected="selected"{/if}>{__("yes")}</option>
            <option value="0" {if $shipping.service_params.sms_to_receiver == '0'} selected="selected"{/if}>{__("no")}</option>
        </select>
    </div>
</div>

{include file="common/subheader.tpl" title=__("shippings.spsr.bag_size_s_header")}
<div class="control-group">
    <label class="control-label" for="spsr_bag_size_s_length">{__("length")}:</label>
    <div class="controls">
    <input id="spsr_bag_size_s_length" type="text" name="shipping_data[service_params][bag_size][s][length]" size="60" value="{$shipping.service_params.bag_size.s.length}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_bag_size_s_width">{__("width")}:</label>
    <div class="controls">
    <input id="spsr_bag_size_s_width" type="text" name="shipping_data[service_params][bag_size][s][width]" size="60" value="{$shipping.service_params.bag_size.s.width}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_bag_size_s_height">{__("height")}:</label>
    <div class="controls">
    <input id="spsr_bag_size_s_height" type="text" name="shipping_data[service_params][bag_size][s][height]" size="60" value="{$shipping.service_params.bag_size.s.height}"/>
    </div>
</div>

{include file="common/subheader.tpl" title=__("shippings.spsr.bag_size_m_header")}
<div class="control-group">
    <label class="control-label" for="spsr_bag_size_m_length">{__("length")}:</label>
    <div class="controls">
    <input id="spsr_bag_size_m_length" type="text" name="shipping_data[service_params][bag_size][m][length]" size="60" value="{$shipping.service_params.bag_size.m.length}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_bag_size_m_width">{__("width")}:</label>
    <div class="controls">
    <input id="spsr_bag_size_m_width" type="text" name="shipping_data[service_params][bag_size][m][width]" size="60" value="{$shipping.service_params.bag_size.m.width}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_bag_size_m_height">{__("height")}:</label>
    <div class="controls">
    <input id="spsr_bag_size_m_height" type="text" name="shipping_data[service_params][bag_size][m][height]" size="60" value="{$shipping.service_params.bag_size.m.height}"/>
    </div>
</div>

{include file="common/subheader.tpl" title=__("shippings.spsr.bag_size_l_header")}
<div class="control-group">
    <label class="control-label" for="spsr_bag_size_l_length">{__("length")}:</label>
    <div class="controls">
    <input id="spsr_bag_size_l_length" type="text" name="shipping_data[service_params][bag_size][l][length]" size="60" value="{$shipping.service_params.bag_size.l.length}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_bag_size_l_width">{__("width")}:</label>
    <div class="controls">
    <input id="spsr_bag_size_l_width" type="text" name="shipping_data[service_params][bag_size][l][width]" size="60" value="{$shipping.service_params.bag_size.l.width}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="spsr_bag_size_l_height">{__("height")}:</label>
    <div class="controls">
    <input id="spsr_bag_size_l_height" type="text" name="shipping_data[service_params][bag_size][l][height]" size="60" value="{$shipping.service_params.bag_size.l.height}"/>
    </div>
</div>
