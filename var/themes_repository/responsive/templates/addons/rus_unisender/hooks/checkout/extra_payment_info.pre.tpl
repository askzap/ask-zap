{if $unisender_page_mailing_lists && $addons.rus_unisender.unisender_show_at_checkout == 'Y'}
    {assign var="show_newsletters_content" value=false}

    {if ($page_mailing_lists)}
        {foreach from=$page_mailing_lists item=list}
            {if $list.show_on_checkout}
                {assign var="show_newsletters_content" value=true}
            {/if}
        {/foreach}
    {/if}

    {if (!$page_mailing_lists) || (!$show_newsletters_content)}
        {include file="common/subheader.tpl" title=__("addons.rus_unisender.text_unisender_signup_for_subscriptions")}
    {/if}

    {script src="js/addons/rus_unisender/func.js"}
    <div class="ty-unisender-container unisender-container" id="unisender_{$tab_id}">
        {foreach from=$unisender_page_mailing_lists item=list}
            <div class="ty-select-field">
                <label><input type="checkbox" name="unisender_lists[]" {if $unisender_user_mailing_lists[$list.list_id]}checked="checked"{/if} value="{$list.list_id}"  class="checkbox cm-unisender-subscribe" />{$list.title}</label>
            </div>
        {/foreach}
    <!--unisender_{$tab_id}--></div>
{/if}
