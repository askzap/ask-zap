{if $order_info.payment_method.processor_params}
	{if $order_info.payment_method.processor_params.sbrf_enabled}
		{assign var="sbrf_settings" value=$order_info.payment_method.processor_params}
		{if $sbrf_settings.sbrf_enabled=="Y"}
            {include file="buttons/button.tpl" but_role="text" but_text=__("sbrf_print_receipt") but_href="orders.print_sbrf_receipt?order_id=`$order_info.order_id`" but_meta="cm-new-window ty-btn__text" but_icon="ty-icon-print orders-print__icon"}
		{/if}
	{/if}

	{if $order_info.payment_method.processor_params.account_enabled}
        {include file="buttons/button.tpl" but_role="text" but_text=__("addons.rus_payments.invoice_payment") but_href="orders.print_invoice_payment?order_id=`$order_info.order_id`" but_meta="cm-new-window ty-btn__text" but_icon="ty-icon-print orders-print__icon"}
    {/if}
{/if}