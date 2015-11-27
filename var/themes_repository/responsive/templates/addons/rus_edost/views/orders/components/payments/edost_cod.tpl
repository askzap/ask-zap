{* rus_build_edost *}
<div class="ty-control-group">
    {if $cart.shippings_extra.sum}
        {if $cart.shippings_extra.sum.pricediff}
            {__("edostcod_naloz_plus")}<b>{include file="common/price.tpl" value=$cart.shippings_extra.sum.pricediff}</b><br>
        {/if}
        {if $cart.shippings_extra.sum.transfer}
            <p style="color: #FF0000; display: inline;">{__("edostcod_naloz_transfer")}<b> {include file="common/price.tpl" value=$cart.shippings_extra.sum.transfer}</b></p><br>
        {/if}
        {if $cart.shippings_extra.sum.total}
            {__("edostcod_naloz_total")}<b>{include file="common/price.tpl" value=$cart.shippings_extra.sum.total}</b><br>
        {/if}
    {/if}
</div>