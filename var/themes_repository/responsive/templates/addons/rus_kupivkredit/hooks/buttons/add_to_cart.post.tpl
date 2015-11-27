{* rus_build_kupivkredit *}
{assign var="c_url" value=$config.current_url|escape:url}
{if $quick_view || $but_role == "action"}
    {assign var="but_meta" value="kupivkredit-button-mini"}
{else}
    {assign var="but_meta" value="kupivkredit-button"}
{/if}

{if $settings.General.allow_anonymous_shopping == "allow_shopping" || $auth.user_id}
    {include file="buttons/button.tpl" but_id="kvk_`$but_id`" but_text=__("kupivkredit_button") but_name="dispatch[checkout.add.kvk_activate.`$obj_id`]" but_onclick=$but_onclick but_href=$but_href but_target=$but_target but_role=$but_role|default:"text" but_meta=$but_meta}
{/if}