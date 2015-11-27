{capture name="mainbox"}
    {capture name="tabsbox"}
    <form action="{""|fn_url}" method="post" name="unisender_form" enctype="multipart/form-data">
        <input type="hidden" name="fake" value="1" />

        <div id="content_fields" class="hidden">
            {include file="addons/rus_unisender/views/unisender/components/unisender_fields.tpl"}
        </div>
    </form>
    {/capture}
    {include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox group_name=$runtime.controller active_tab=$smarty.request.selected_section track=true}
{/capture}

{capture name="buttons"}
    {include file="buttons/save.tpl" but_name="dispatch[unisender.update]" but_role="submit-link" but_target_form="unisender_form"}
{/capture}

{include file="common/mainbox.tpl" title=__("addons.rus_unisender.unisender") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons}
