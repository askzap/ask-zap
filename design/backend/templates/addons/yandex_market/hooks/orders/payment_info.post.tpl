{if $order_info.yandex_market}

    <div class="control-group">
        <div class="control-label">{__("method")}</div>
        <div id="tygh_payment_info" class="controls">{__("yandex_market")}</div>
    </div>

    <div class="control-group">
        <div class="control-label">{__("order_id")}</div>
        <div class="controls">{$order_info.yandex_market.order_id}</div>
    </div>

    <div class="control-group">
        <div class="control-label">{__("payment_type")}</div>
        {if $order_info.yandex_market.payment_type}
            <div class="controls">{__("yml_payment_type_{$order_info.yandex_market.payment_type|strtolower}")}</div>
        {/if}
    </div>

    <div class="control-group">
        <div class="control-label">{__("payment_method")}</div>
        {if $order_info.yandex_market.payment_method}
            <div class="controls">{__("yml_payment_method_{$order_info.yandex_market.payment_method|strtolower}")}</div>
        {/if}
    </div>

    {if $order_info.yandex_market.status}
        <div class="control-group">
            <div class="control-label">{__("status")}</div>
            <div class="controls">{$order_info.yandex_market.status}</div>
        </div>
    {/if}

    {if $order_info.yandex_market.substatus}
        <div class="control-group">
            <div class="control-label">{__("reason")}</div>
            <div class="controls">{__("yml_substatus_{$order_info.yandex_market.substatus|strtolower}")}</div>
        </div>
    {/if}

{/if}