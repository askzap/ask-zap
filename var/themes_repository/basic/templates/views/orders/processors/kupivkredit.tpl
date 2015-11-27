<!DOCTYPE html>
<html lang="{$smarty.const.CART_LANGUAGE}">
<head>

    {scripts}
    {script src="js/lib/jquery/jquery.min.js"}
    {script src="js/tygh/core.js"}
    {/scripts}
    <script src="{$url}/widget/vkredit.js"></script>

    <script type="text/javascript">

        (function(_, $) {

            var index_script = '{""|fn_url:"C":"rel"|escape:javascript nofilter}';
            var callback_close = function(decision) {
                $(window.location).prop('href', index_script+'?dispatch=payment_notification.close&order_id={$order_id}&payment=kupivkredit&decision='+decision);
            };
            var callback_decision = function(decision) {
                $(window.location).prop('href', index_script+'?dispatch=payment_notification.decision&order_id={$order_id}&payment=kupivkredit&decision='+decision);
            };

            vkredit = new VkreditWidget(1,
                    '{$order_total|escape:javascript nofilter}',
                    {
                        order: '{$base|escape:javascript nofilter}',
                        sig: '{$sig|escape:javascript nofilter}',
                        callbackUrl: window.location.href,
                        onClose: callback_close,
                        onDecision: callback_decision
                    }
            );

            $(document).ready(function(){
                vkredit.openWidget();

                $('#closeWidget').click(function () {
                    $(window.location).prop('href', index_script+'?dispatch=payment_notification.close&order_id={$order_id}&payment=kupivkredit&decision=closed');
                });
            });

        }(Tygh, Tygh.$));

    </script>
</head>

<body>
</body>

</html>