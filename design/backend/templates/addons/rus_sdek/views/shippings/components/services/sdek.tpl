{script src="js/addons/rus_sdek/func.js"}
<fieldset>

<div class="control-group">
    <label class="control-label" for="authlogin">{__("shippings_sdek.authlogin")}</label>
    <div class="controls">
        <input id="authlogin" type="text" name="shipping_data[service_params][authlogin]" size="30" value="{$shipping.service_params.authlogin}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="authpassword">{__("shippings_sdek.authpassword")}</label>
    <div class="controls">
        <input id="authpassword" type="text" name="shipping_data[service_params][authpassword]" size="30" value="{$shipping.service_params.authpassword}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="dateexecute">{__("shippings.sdek.dateexecute")}</label>
    <div class="controls">
        <select id="dateexecute" name="shipping_data[service_params][dateexecute]">
            <option value="0" {if $shipping.service_params.dateexecute == "0"}selected="selected"{/if}>0</option>
            <option value="1" {if $shipping.service_params.dateexecute == "1"}selected="selected"{/if}>1</option>
            <option value="2" {if $shipping.service_params.dateexecute == "2"}selected="selected"{/if}>2</option>
            <option value="3" {if $shipping.service_params.dateexecute == "3"}selected="selected"{/if}>3</option>
            <option value="4" {if $shipping.service_params.dateexecute == "4"}selected="selected"{/if}>4</option>
            <option value="5" {if $shipping.service_params.dateexecute == "5"}selected="selected"{/if}>5</option>
            <option value="6" {if $shipping.service_params.dateexecute == "6"}selected="selected"{/if}>6</option>
            <option value="7" {if $shipping.service_params.dateexecute == "7"}selected="selected"{/if}>7</option>
            <option value="8" {if $shipping.service_params.dateexecute == "8"}selected="selected"{/if}>8</option>
            <option value="9" {if $shipping.service_params.dateexecute == "9"}selected="selected"{/if}>9</option>
            <option value="10" {if $shipping.service_params.dateexecute == "10"}selected="selected"{/if}>10</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="city">{__("shippings.sdek.sendercityid")}</label>
    <div class="controls">
        <input x-autocompletetype="city" type="text" id="city" name="shipping_data[service_params][sendercityid]" size="30" maxlength="64" value="{$shipping.service_params.sendercityid}" class=""/>
        <a href="#" id="sdek_get_city_link">{__("shipping.sdek.get_city_data")} {include file="common/tooltip.tpl" tooltip=__("shipping.sdek.get_city_data.tooltip")}</a>
    </div>
</div>

<div id="sdek_city_div">
{if $shipping.service_params.from_city_id && !$sdek_new_city_data}
    <div class="control-group">
        <label class="control-label" for="sdek_city_id">{__("shipping.sdek.city_id")} {include file="common/tooltip.tpl" tooltip=__("shipping.sdek.city_id.tooltip")}:</label>
        <div class="controls">
            <input id="sdek_city_id" type="text" name="shipping_data[service_params][from_city_id]" size="60" value="{$shipping.service_params.from_city_id}"/>
        </div>
    </div>
    {else}
    <div class="control-group">
        <label class="control-label" for="sdek_city_id">{__("shipping.sdek.city_id")} {include file="common/tooltip.tpl" tooltip=__("shipping.sdek.city_id.tooltip")}:</label>
        <div class="controls">
            <input id="sdek_city_id" type="text" name="shipping_data[service_params][from_city_id]" size="60" value="{$sdek_new_city_data.from_city_id}"/>
        </div>
    </div>
{/if}
<!--sdek_city_div--></div>

<div class="control-group">
    <label class="control-label" for="sendercitypostcode">{__("shippings.sdek.sendercitypostcode")}</label>
    <div class="controls">
        <input id="sendercitypostcode" type="text" name="shipping_data[service_params][sendercitypostcode]" size="30" value="{$shipping.service_params.sendercitypostcode}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ship_tariff">{__("shippings.sdek.tariffid")}</label>
    <div class="controls">
        <select id="ship_tariff" name="shipping_data[service_params][tariffid]">
            <option value="136" {if ($shipping.service_params.tariffid) == 136}selected="selected"{/if}>{__("sdek_tariff_sklad")}</option>
            <option value="137" {if ($shipping.service_params.tariffid) == 137}selected="selected"{/if}>{__("sdek_tariff_dver")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ship_width">{__("ship_width")}</label>
    <div class="controls">
        <input id="ship_width" type="text" name="shipping_data[service_params][width]" size="30" value="{$shipping.service_params.width}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ship_height">{__("ship_height")}</label>
    <div class="controls">
        <input id="ship_height" type="text" name="shipping_data[service_params][height]" size="30" value="{$shipping.service_params.height}" />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ship_length">{__("ship_length")}</label>
    <div class="controls">
        <input id="ship_length" type="text" name="shipping_data[service_params][length]" size="30" value="{$shipping.service_params.length}" />
    </div>
</div>
</fieldset>
