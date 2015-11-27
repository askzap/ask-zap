
<div id="content_rus_spsr_invoice">
    {if $spsr_new_invoice}
        {include file="addons/rus_spsr/hooks/orders/components/spsr_packages_create.tpl"}
    {/if}

    {if $spsr_packages}
        {include file="addons/rus_spsr/hooks/orders/components/spsr_invoices_create.tpl"}
    {/if}

    {if $registers}
        {include file="addons/rus_spsr/hooks/orders/components/spsr_invoices.tpl"}
    {/if}
<!--content_rus_spsr_invoice--></div>
