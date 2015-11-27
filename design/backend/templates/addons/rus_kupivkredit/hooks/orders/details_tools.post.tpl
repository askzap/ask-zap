{* rus_build_kupivkredit dbazhenov *}
{assign var="processor" value=$order_info.payment_method.payment_id|fn_get_payment_method_data}
{if $processor.processor == 'Kupivkredit' || $processor.processor == 'kupivkredit'}
    {if $order_info.status == 'P' || $order_info.status == 'C'}
        {assign var="partner_id" value=$processor.processor_params.kvk_shop_id}
        {assign var="api_key" value=$processor.processor_params.kvk_api_key}
        {assign var="sig" value=$processor.processor_params.kvk_secret}
        {assign var="test" value=$processor.processor_params.test}

        <li class="divider"></li>
        <li>
            {btn type="list" text=__("kupivkredit_cancel_order") href="orders.kvk_cancel?order_id=`$order_info.order_id`&partner_id=`$partner_id`&api_key=`$api_key`&sig=`$sig`&test=`$test`"}
        </li>
        <li>
            {btn type="list" text=__("kupivkredit_complete_order") href="orders.kvk_complete?order_id=`$order_info.order_id`&partner_id=`$partner_id`&api_key=`$api_key`&sig=`$sig`&test=`$test`"}
        </li>
        <li>
            {btn type="list" text=__("kupivkredit_confirm_order") href="orders.kvk_confirm?order_id=`$order_info.order_id`&partner_id=`$partner_id`&api_key=`$api_key`&sig=`$sig`&test=`$test`"}
        </li>
    {/if}
{/if}