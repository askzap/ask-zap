{* jmi *}


{if $runtime.layout.style_id == 'Russian_theme'}
<i class="ty-minicart__icon ty-icon-basket{if $smarty.session.cart.amount} filled{else} empty{/if}"></i>
<span class="ty-minicart-title{if !$smarty.session.cart.amount} empty-cart{/if} ty-hand">
	<span class="ty-block ty-minicart-title__header ty-uppercase">{__("my_cart")}</span>
	   <span class="ty-block">
        {if $smarty.session.cart.amount}
            {$smarty.session.cart.amount}&nbsp;{__("items")} {__("for")}&nbsp;{include file="common/price.tpl" value=$smarty.session.cart.display_subtotal}
        {else}
            {__("cart_is_empty")}
        {/if}
       </span>
</span>
{/if}