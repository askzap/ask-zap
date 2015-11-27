
{__("yml_market_category_list_text")}

<form id="{$obj_id}_form">

    {foreach from=$categories_tree key="key" item="category"}

        <label for="variant_{$key}" class="radio">
            <input type="radio" name="ym_categories_list" data-ca-category="{$category|escape:'html'}" id="variant_{$key}" />{$category}
        </label>

    {/foreach}

</form>

<div class="buttons-container">
    <a class="cm-dialog-closer cm-cancel tool-link btn">{__("cancel")}</a>
    {include file="buttons/button.tpl" but_name="submit" but_text=__("select") but_role="button_main" but_meta="btn-primary cm-ym-category-select" but_target_form="{$obj_id}_form"}
</div>