{foreach from=$order_info.shipping item="shipping_method"}
    {if ($shipping_method.office_data)  && ($shipping_method.module == 'edost')}
        <br />
        <p class="strong">
            {$shipping_method.office_data.name}
        </p>
        <p class="muted">
            {$shipping_method.office_data.address}<br />
            {$shipping_method.office_data.tel}<br />
            {$shipping_method.office_data.schedule}<br />
        </p>
        <p>
            <a target="_blank" href="http://www.edost.ru/office.php?c={$shipping_method.office_data.office_id}">{__("edost.header.office_map")}</a>
        </p>
    {/if}
{/foreach}