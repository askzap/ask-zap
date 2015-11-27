{* rus_build_kupivkredit dbazhenov *}
{script src="js/lib/jquery/jquery.min.js"}
{script src="js/tygh/core.js"}
<script src="{$url}/widget/vkredit.js"></script>

<script type="text/javascript">
    var callback_close = function(decision) {
        $(window.location).prop('href', '{$url_return nofilter}&decision=' + decision);
    };

    var callback_decision = function(decision) {
        $(window.location).prop('href', '{$url_decision nofilter}&decision=' + decision);
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

</script>

<script type="text/javascript">
    Tygh.$(document).ready(function() {
        vkredit.openWidget();
    });

    $('#closeWidget').click(function () {
        $(window.location).prop('href', '{$url_return nofilter}' + '&decision=closed');
    });
</script>
