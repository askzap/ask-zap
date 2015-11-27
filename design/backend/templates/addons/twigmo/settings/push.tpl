<div id="twg_push">

{include file="addons/twigmo/settings/components/contact_twigmo_support.tpl"}

{include file="common/subheader.tpl" title=__("twgadmin_send_push_notifications")}

{if $platinum_stores}
<fieldset>

    <div class="control-group form-field">
        <label for="elm_tw_push_store" class="control-label">{__("twgadmin_select_store_short")}:</label>
        <div class="controls">
            <select id="elm_tw_push_store" name="push[access_id]">
                {foreach from=$platinum_stores item="store"}
                    <option value="{$store.access_id}">{$store.company}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group form-field">
        <label for="elm_tw_push_text" id="elm_tw_push_text_label" class="control-label">{__("twgadmin_push_notification_text")}:</label>
        <div class="controls">
            <textarea name="push[message]" id="elm_tw_push_text" maxlength="{$max_push_length}"></textarea>
            <div class="twg-push-counter" id="twg_letters_remain">{$max_push_length}</div>
        </div>
    </div>

    <div class="control-group form-field">
        <div class="controls">
            {foreach from=$platinum_stores item="store" name=stores}
                <div class="twg-app-label twg-push-comment" id="push_comment_{$store.access_id}" {if !$smarty.foreach.stores.first}style="display: none"{/if}>
                    {$store.push_comment nofilter}
                </div>
            {/foreach}
            <div class="twg-app-label">{__("twgadmin_push_notification_comment")}</div>
        </div>
    </div>

    <div class="control-group form-field">
        <div class="controls">
            {include file="buttons/button.tpl" but_role="submit" but_meta="btn-primary cm-ajax cm-confirm" but_name="dispatch[twigmo_push.send]" but_text=__("send")}
            {*{include file="buttons/button.tpl" but_role="submit" but_meta="btn-primary cm-new-window" but_name="dispatch[addons.tw_svc_auth_te]" but_text=__("twgadmin_open_te")}*}
        </div>
    </div>

</fieldset>

<script type="text/javascript">
    //<![CDATA[
    {literal}
    $(document).ready(function() {
        {/literal}
        var max_push_length = {$max_push_length};
        {literal}
        var onTextChange = function() {
            var limit = parseInt($(this).attr('maxlength'));
            var text = $(this).val();
            var chars = text.length;
            $("#twg_letters_remain").html(max_push_length - chars);
            if(chars > limit) {
                $(this).val(text.substr(0, limit));
            }
        };

        var onStoreChange = function() {
            $('div.twg-push-comment').hide();
            $('#push_comment_' + $(this).val()).show();
        };

        var onTabChange = function() {
            var pushMessageIsRequired = $(this).attr('id') == 'twigmo_twg_push';
            $('#elm_tw_push_text_label').toggleClass('cm-required', pushMessageIsRequired);
        };

        $('#elm_tw_push_text').keypress(onTextChange).keyup(onTextChange);
        $('#elm_tw_push_store').change(onStoreChange);
        $('li.cm-js').click(onTabChange);
    });
    {/literal}
    //]]>
</script>

{/if}

<!--twg_push--></div>
