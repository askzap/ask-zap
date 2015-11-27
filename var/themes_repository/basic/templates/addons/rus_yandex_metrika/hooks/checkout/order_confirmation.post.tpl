<!-- orders metrika -->
{if $order_info}
    <script type="text/javascript">
    var yaParams = {
        order_id: "{$order_info.order_id}",
        order_price: {$order_info.total},
        currency: "{$order_info.secondary_currency}",
        exchange_rate: 1,
        goods:
        [
            {foreach from=$order_info.products item=products}
            {
                id: "{$products.product_id}",
                name: "{$products.product}",
                price: {$products.price},
                quantity: {$products.amount}
            },
            {/foreach}
        ]
    };
    </script>
{/if}
