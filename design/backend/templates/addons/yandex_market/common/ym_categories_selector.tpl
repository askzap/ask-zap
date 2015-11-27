{script src="js/addons/yandex_market/ym_categories.js"}

{$obj_id = $obj_id|default:"ym_categories"}

<div class="control-group cm-no-hide-input">
    <label for="product_type_prefix" class="control-label">{__("yml_market_category")}:</label>
    <div class="controls" id="{$obj_id}_box">
        <input type="text" name="{$name}" size="200" value="{$value}" class="input-large cm-ym-categories" /></br>
        {include file="common/popupbox.tpl" id="{$obj_id}_popup" href="ym_categories.picker?obj_id={$obj_id}" link_text=__("yml_market_category_link") text=__("yml_market_category_list_title") act="link"}
    </div>
</div>  

