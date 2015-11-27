{foreach from=$order_info.shipping item="shipping_method"}
    {if $shipping_method.module == 'sdek'}
        {assign var="sdek_shipping" value=true}
    {/if}
{/foreach}

{if $sdek_shipping}
{foreach from=$order_info.shipping item="shipping_method"}
    <li>{if $shipping_method.office_data}   
            <p class="ty-strong">
                {$shipping_method.office_data.Name}
            </p>
            <p class="ty-muted">
                {$shipping_method.office_data.Address}<br />
                {$shipping_method.office_data.WorkTime}<br />
                {$shipping_method.office_data.Phone}<br />
                {$shipping_method.office_data.Note}<br />
            </p>
        {/if}
    </li>
{/foreach}
{/if}