{if $show}
    <a id="trial" class="cm-dialog-opener cm-dialog-auto-size hidden cm-dialog-non-closable" data-ca-target-id="trial_dialog"></a>
{/if}

<div class="hidden trial-expired-dialog" title="{__("trial_expired", ["[product]" => $smarty.const.PRODUCT_NAME])}" id="trial_dialog">
    {if $store_mode_errors}
        <div class="alert alert-error notification-content">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {foreach from=$store_mode_errors item="message"}
            <strong>{$message.title}:</strong> {$message.text nofilter}<br>
        {/foreach}
        </div>
    {/if}

    <form name="trial_form" action="{""|fn_url}" method="post">
        <input type="hidden" name="redirect_url" value="{$config.current_url}">
        <input type="hidden" name="store_mode" value="full">
        <div  style="padding: 0 10px;" class="trial-expired">
            <p style="margin: 20px 0;">{__("text_input_license_code", ["[product]" => $smarty.const.PRODUCT_NAME])}</p>

            <div style="text-align: center;" class="license {if $store_mode_errors} type-error{/if} item">
                    <input type="text" name="license_number" class="{if $store_mode_errors} type-error{/if}" value="{$store_mode_license}" placeholder="{__("please_enter_license_here")}">
                    <input name="dispatch[settings.change_store_mode]" type="submit" value="{__("activate")}" class="btn btn-primary">
            </div>

            {if "ULTIMATE"|fn_allowed_for}
                {assign var="buy_link" value="http://www.cs-cart.ru/cs-cart-rus-pack.html"}
            {else}
                {assign var="buy_link" value="http://www.cs-cart.ru/multi-vendor-rus-pack.html"}
            {/if}

            {assign var="buy_license" value="<p style=\"text-align: center;\"><a class=\"btn btn-warning btn-large btn-buy\" style=\"color: #fff; font-weight: bold\" target=\"_blank\" href=\"$buy_link\">{__("buy_license")}</a></p>"}

            <p style="margin: 20px 0;">{__("text_buy_new_license", ["[buy_license]" => {$buy_license nofilter}])}</p>
        </div>
    </form>
</div>

<script type="text/javascript">
Tygh.$(document).ready(function(){$ldelim}
    {if $show}
        Tygh.$('#trial').trigger('click');
    {/if}
{$rdelim});
</script>
