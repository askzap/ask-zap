{capture name="mainbox"}

{capture name="tabsbox"}

<form action="{""|fn_url}" method="post" name="print_form" class="form-horizontal form-edit ">
<input type="hidden" class="cm-no-hide-input" name="fake" value="1" />
<input type="hidden" class="cm-no-hide-input" name="order_id" value="{$smarty.request.order_id}" />

<div id="content_recipient">
    {include file="addons/rus_russianpost/views/rus_post_blank/tabs/recipient.tpl"}
</div>
<div id="content_sender">
    {include file="addons/rus_russianpost/views/rus_post_blank/tabs/sender.tpl"}
</div>

<div id="content_settings">
    {include file="addons/rus_russianpost/views/rus_post_blank/tabs/settings.tpl"}
</div>

{capture name="buttons"}
    {include file="buttons/button.tpl" but_text=__("rus_post_blank.112") but_meta="cm-new-window" but_name="dispatch[rus_post_blank.print.112]" but_role="submit-link" but_target_form="print_form"}
    {include file="buttons/button.tpl" but_text=__("rus_post_blank.113") but_meta="cm-new-window" but_name="dispatch[rus_post_blank.print.113]" but_role="submit-link" but_target_form="print_form"}
    {include file="buttons/button.tpl" but_text=__("rus_post_blank.116") but_meta="cm-new-window" but_target="_blank" but_name="dispatch[rus_post_blank.print.116]" but_role="submit-link" but_target_form="print_form"}
    {include file="buttons/button.tpl" but_text=__("rus_post_blank.7p") but_meta="cm-new-window" but_name="dispatch[rus_post_blank.print.7p]" but_role="submit-link" but_target_form="print_form"}
    {include file="buttons/button.tpl" but_text=__("rus_post_blank.7b") but_meta="cm-new-window" but_target="_blank" but_name="dispatch[rus_post_blank.print.7b]" but_role="submit-link" but_target_form="print_form"}
{/capture}

</form>

{/capture}
{include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox track=true}

{/capture}

{*{assign var="title" value="{__("rus_post_blank.li.print")}: `$pre_data`"}*}
{assign var="title" value="{__("rus_post_blank.li.print")}:"}


{include file="common/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=true buttons=$smarty.capture.buttons}
