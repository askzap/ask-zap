<div class="hidden" id="content_logs">
    <input class="hidden" id="val_order_id" value="{$order_info.order_id}" />
    {include file="addons/rus_order_logs/views/orders/components/order_logs.tpl"}
<!--content_logs--></div>

<script type="text/javascript">
    //<![CDATA[
        $.ceEvent('on', 'ce.update_object_status_callback', function() {
            var url = fn_url('orders.update_order_logs?order_id=' + $("#val_order_id").val());
            $.ceAjax('request', url, {
                result_ids: 'order_logs'
            });
        });    
    //]]>
</script>