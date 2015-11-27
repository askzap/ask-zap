
{include file="common/subheader.tpl" title=__("shippings.spsr.service_label") target="#spsr_product_type"}
<div id="spsr_product_type" class="collapsed in">
    <div class="control-group">
        <label class="control-label" for="spsr_product_type">{__("shippings.spsr.product_type")}:</label>
        <div class="controls">
        <select name="product_data[spsr_product_type]" id="spsr_necesserytime">
            {foreach from=$type_products item="type"}
                <option {if $product_data.spsr_product_type == $type.Value}selected="selected"{/if} value="{$type.Value}">{$type.Name}</option>
            {/foreach}
        </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="product_weight">{__("weight")} ({$settings.General.weight_symbol nofilter}):</label>
        <div class="controls">
            <input type="text" name="product_data[weight]" id="product_weight" size="10" value="{$product_data.weight|default:"0"}" class="input-long" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="product_box_length">{__("length")}:</label>
        <div class="controls">
            <input type="text" name="product_data[box_length]" id="product_box_length" size="10" value="{$product_data.box_length|default:"0"}" class="input-long shipping-dependence" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="product_box_width">{__("width")}:</label>
        <div class="controls">
            <input type="text" name="product_data[box_width]" id="product_box_width" size="10" value="{$product_data.box_width|default:"0"}" class="input-long shipping-dependence" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="product_box_height">{__("height")}:</label>
        <div class="controls">
            <input type="text" name="product_data[box_height]" id="product_box_height" size="10" value="{$product_data.box_height|default:"0"}" class="input-long shipping-dependence" />
        </div>
    </div>
</div>