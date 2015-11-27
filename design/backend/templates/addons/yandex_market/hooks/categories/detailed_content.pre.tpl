
{include file="common/subheader.tpl" title=__("yml") target="#yandex_market_addon"}

<div id="yandex_market_addon" class="in collapse">
    
    <div class="control-group">
        <label class="control-label" for="yml_export">{__("yml_disable_cat")}:</label>
        <div class="controls">
        <input type="hidden" value="N" name="category_data[yml_disable_cat]"/>
        <input type="checkbox" class="cm-toggle-checkbox" value="Y" name="category_data[yml_disable_cat]" id="yml_export" {if $category_data.yml_disable_cat == "Y"} checked="checked"{/if} />
        </div>
    </div>

    {if $addons.yandex_market.market_category == "Y"}
        {include file="addons/yandex_market/common/ym_categories_selector.tpl" name="category_data[yml_market_category]" value=$category_data.yml_market_category}            
    {/if}

</div>