{* rus_build_mailru dbazhenov *}
{include file="common/subheader.tpl" title=__("mailru") target="#tovary_mailru_addon"}
<div id="tovary_mailru_addon" class="in collapse">
    <div class="control-group">
        <label for="mailru_product_description_brand" class="control-label">{__("mailru_brand")}:</label>
        <div class="controls"><input type="text" name="product_data[mailru_brand]" id="mailru_product_description_brand" size="55" value="{$product_data.mailru_brand}" class="input-text-large" />
        </div>
    </div>
	
	<div class="control-group">
        <label for="mailru_product_description_model" class="control-label">{__("mailru_model")}:</label>
        <div class="controls"><input type="text" name="product_data[mailru_model]" id="mailru_product_description_model" size="55" value="{$product_data.mailru_model}" class="input-text-large" />
        </div>
    </div>
	
	<div class="control-group">
        <label for="mailru_product_description_type_prefix" class="control-label">{__("mailru_type_prefix")}:</label>
        <div class="controls"><input type="text" name="product_data[mailru_type_prefix]" id="mailru_product_description_type_prefix" size="55" value="{$product_data.mailru_type_prefix}" class="input-text-large" />
        </div>
    </div>
	
	{if $addons.rus_tovary_mailru.local_delivery_cost == "Y"}
	    <div class="control-group cm-no-hide-input">
            <label for="mailru_product_description_cost" class="control-label">{__("mailru_cost")}:</label>
            <div class="controls">
			    <input type="text" name="product_data[mailru_cost]" id="mailru_product_description_cost" size="10" value="{$product_data.mailru_cost}" class="input-small" />
            </div>
        </div>
	{/if}
		
	<div class="control-group cm-no-hide-input">
        <label for="product_description_mailru_delivery" class="control-label">{__("mailru_delivery")}:</label>
        <div class="controls">
            <select name="product_data[mailru_delivery]" id="product_description_mailru_delivery">
                <option value="Y" {if $product_data.mailru_delivery == "Y"}selected="selected"{/if}>{__("mailru_true")}</option>
                <option value="N" {if $product_data.mailru_delivery == "N"}selected="selected"{/if}>{__("mailru_false")}</option>
            </select>
        </div>
    </div>
	
	<div class="control-group cm-no-hide-input">
        <label for="product_description_mailru_pickup" class="control-label">{__("mailru_pickup")}:</label>
        <div class="controls">
            <select name="product_data[mailru_pickup]" id="product_description_mailru_pickup">
                <option value="Y" {if $product_data.mailru_pickup == "Y"}selected="selected"{/if}>{__("mailru_true")}</option>
                <option value="N" {if $product_data.mailru_pickup == "N"}selected="selected"{/if}>{__("mailru_false")}</option>
            </select>
        </div>
    </div>
    
    <div class="control-group">
        <label for="mailru_product_description_mcp" class="control-label">{__("mailru_mcp")}:</label>
        <div class="controls">
            <input type="text" name="product_data[mailru_mcp]" id="mailru_product_description_mcp" size="10" value="{$product_data.mailru_mcp}" class="input-small" />
        </div>
    </div>

    <div class="control-group cm-no-hide-input">
        <label for="product_description_mailru_export" class="control-label">{__("mailru_export")}:</label>
        <div class="controls">
            <select name="product_data[mailru_export]" id="product_description_mailru_export">
                <option value="Y" {if $product_data.mailru_export == "Y"}selected="selected"{/if}>{__("mailru_true")}</option>
                <option value="N" {if $product_data.mailru_export == "N"}selected="selected"{/if}>{__("mailru_false")}</option>
            </select>
        </div>
    </div>
</div>
