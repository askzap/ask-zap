{* rus_build_edost *}

{if $cart.shippings_extra.sum.pricediff}
<div class="control-group">
    {__("edostcod_naloz_plus")}<b>{include file="common/price.tpl" value=$cart.shippings_extra.sum.pricediff}</b><br>
</div>
{/if}

{if $cart.shippings_extra.sum.transfer}
<div class="control-group">
    <p style="color: #FF0000; display: inline;">{__("edostcod_naloz_transfer")}<b> {include file="common/price.tpl" value=$cart.shippings_extra.sum.transfer}</b></p><br>
</div>
{/if}

{if $cart.shippings_extra.sum.total}
<div class="control-group">
    {__("edostcod_naloz_total")}<b>{include file="common/price.tpl" value=$cart.shippings_extra.sum.total}</b><br>
</div>
{/if}