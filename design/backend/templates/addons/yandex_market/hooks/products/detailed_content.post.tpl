
{include file="common/subheader.tpl" title=__("yml") target="#yandex_market_addon"}

<div id="yandex_market_addon" class="in collapse">
    <div class="control-group cm-no-hide-input">
        <label for="product_description_brand" class="control-label {if ($addons.yandex_market.includes_required_fields == 'Y' && $addons.yandex_market.export_type == 'vendor_model')}cm-required{/if}">{__("yml_brand")}:</label>
        <div class="controls"><input type="text" name="product_data[yml_brand]" id="product_description_brand" size="55" value="{$product_data.yml_brand}" class="input-text-large" />
            {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id='product' name="update_all_vendors[product]"}</div>
    </div>

    <div class="control-group cm-no-hide-input">
        <label for="product_description_model" class="control-label {if ($addons.yandex_market.includes_required_fields == 'Y' && $addons.yandex_market.export_type == 'vendor_model')}cm-required{/if}">{__("yml_model")}:</label>
        <div class="controls"><input type="text" name="product_data[yml_model]" id="product_description_model" size="55" value="{$product_data.yml_model}" class="input-text-large" />
            {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id='product' name="update_all_vendors[product]"}
        </div>
    </div>

    {if $addons.yandex_market.type_prefix == "Y"}
        <div class="control-group cm-no-hide-input">
            <label for="product_type_prefix" class="control-label">{__("yml_type_prefix")}:</label>
            <div class="controls"><input type="text" name="product_data[yml_type_prefix]" id="product_type_prefix" size="55" value="{$product_data.yml_type_prefix}" class="input-text-large" />
                {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id='product' name="update_all_vendors[product]"}
            </div>
        </div>  
    {/if}
    
    <div class="control-group cm-no-hide-input">
        <label for="product_description_yml_sales_notes" class="control-label">{__("yml_sales_notes")}:</label>
        <div class="controls"><input type="text" name="product_data[yml_sales_notes]" id="product_description_yml_sales_notes" size="50" value="{$product_data.yml_sales_notes}" class="input-text-large" />
            {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id='product' name="update_all_vendors[product]"}</div>
    </div>

    {if $addons.yandex_market.market_category == "Y"}
        {include file="addons/yandex_market/common/ym_categories_selector.tpl" name="product_data[yml_market_category]" value=$product_data.yml_market_category}            
    {/if}

    <div class="control-group cm-no-hide-input">
        <label for="product_description_origin_country" class="control-label">{__("yml_country")}:</label>
        <div class="controls"><input type="text" name="product_data[yml_origin_country]" id="product_description_origin_country" size="55" value="{$product_data.yml_origin_country}" class="input-text-large" />
            {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id='product' name="update_all_vendors[product]"}</div>
    </div>
    
    <div class="control-group cm-no-hide-input">
        <label for="product_description_manufacturer_warranty" class="control-label">{__("yml_manufacturer_warranty")}:</label>
        <div class="controls"><input type="text" name="product_data[yml_manufacturer_warranty]" id="product_description_manufacturer_warranty" size="55" value="{$product_data.yml_manufacturer_warranty}" class="input-text-large" />
            {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id='product' name="update_all_vendors[product]"}</div>
    </div>
    
    <div class="control-group cm-no-hide-input">
        <label for="product_description_seller_warranty" class="control-label">{__("yml_seller_warranty")}:</label>
        <div class="controls"><input type="text" name="product_data[yml_seller_warranty]" id="product_description_seller_warranty" size="55" value="{$product_data.yml_seller_warranty}" class="input-text-large" />
            {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id='product' name="update_all_vendors[product]"}</div>
    </div>

    <div class="control-group cm-no-hide-input">
        <label for="product_description_yml_export_yes" class="control-label">{__("yml_export_yes")}:</label>
        <div class="controls">
            <select name="product_data[yml_export_yes]" id="product_description_yml_export_yes">
                <option value="Y" {if $product_data.yml_export_yes == "Y"}selected="selected"{/if}>{__("yml_true")}</option>
                <option value="N" {if $product_data.yml_export_yes == "N"}selected="selected"{/if}>{__("yml_false")}</option>
            </select>
        </div>
    </div>
	
	{if $addons.yandex_market.local_delivery_cost == "Y"}
	    <div class="control-group cm-no-hide-input">
            <label for="product_description_cost" class="control-label">{__("yml_cost")}:</label>
            <div class="controls">
			    <input type="text" name="product_data[yml_cost]" id="product_description_cost" size="10" value="{$product_data.yml_cost}" class="input-small" />
            </div>
        </div>
	{/if}

    <div class="control-group cm-no-hide-input">
        <label for="product_description_adult" class="control-label">{__("yml_adult")}:</label>
        <div class="controls">
            <select name="product_data[yml_adult]" id="product_description_adult">
                <option value="N" {if $product_data.yml_adult == "N"}selected="selected"{/if}>{__("yml_false")}</option>
                <option value="Y" {if $product_data.yml_adult == "Y"}selected="selected"{/if}>{__("yml_true")}</option>
            </select>
        </div>
    </div>

    <div class="control-group cm-no-hide-input">
        <label for="product_description_delivery" class="control-label">{__("yml_delivery")}:</label>
        <div class="controls">
            <select name="product_data[yml_delivery]" id="product_description_delivery">
                <option value="Y" {if $product_data.yml_delivery == "Y"}selected="selected"{/if}>{__("yml_true")}</option>
                <option value="N" {if $product_data.yml_delivery == "N"}selected="selected"{/if}>{__("yml_false")}</option>
            </select>
        </div>
    </div>
    
    <div class="control-group cm-no-hide-input">
        <label for="product_description_store" class="control-label">{__("yml_store")}:</label>
        <div class="controls">
            <select name="product_data[yml_store]" id="product_description_store">
                <option value="Y" {if $product_data.yml_store == "Y"}selected="selected"{/if}>{__("yml_true")}</option>
                <option value="N" {if $product_data.yml_store == "N"}selected="selected"{/if}>{__("yml_false")}</option>
            </select>
        </div>
    </div>
    
    <div class="control-group cm-no-hide-input">
        <label for="product_description_pickup" class="control-label">{__("yml_pickup")}:</label>
        <div class="controls">
            <select name="product_data[yml_pickup]" id="product_description_pickup">
                <option value="Y" {if $product_data.yml_pickup == "Y"}selected="selected"{/if}>{__("yml_true")}</option>
                <option value="N" {if $product_data.yml_pickup == "N"}selected="selected"{/if}>{__("yml_false")}</option>
            </select>
        </div>
    </div>
    
    <div class="control-group cm-no-hide-input">
        <label for="product_description_bid" class="control-label">{__("yml_bid")}:</label>
        <div class="controls">
            <input type="text" name="product_data[yml_bid]" id="product_description_bid" size="10" value="{$product_data.yml_bid}" class="input-small" />
        </div>
    </div>
    
    <div class="control-group cm-no-hide-input">
        <label for="product_description_cbid" class="control-label">{__("yml_cbid")}:</label>
        <div class="controls">
            <input type="text" name="product_data[yml_cbid]" id="product_description_cbid" size="10" value="{$product_data.yml_cbid}" class="input-small" />
        </div>
    </div>

</div>
