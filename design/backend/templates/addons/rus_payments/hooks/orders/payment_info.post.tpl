{$pdata = $order_info.payment_method}
{$pinfo = $order_info.payment_info}

{$show_refund = false}

{if $pdata.processor == 'Yandex.Money'}
    {$show_refund = $show_refund || $pdata.processor_params.returns_enabled == 'Y' && ($pinfo.yandex_confirmed_time || !$pinfo.yandex_postponed_payment) && !$pinfo.yandex_canceled_time && !$pinfo.yandex_refunded_time}
{/if}

{if $pdata.processor == 'Avangard'}
    {$show_refund = $show_refund || !$pinfo.avangard_canceled_time && !$pinfo.avangard_refunded_time && $pinfo.avangard_ticket}
{/if}

{if $show_refund}
    <div class="btn-group">
        <a class="btn cm-dialog-opener cm-dialog-auto-size" data-ca-target-id="rus_payments_refund_dialog">{__("addons.rus_payments.refund")}</a>
    </div>
    <div class="hidden" title="{__("addons.rus_payments.refund")}" id="rus_payments_refund_dialog">
        <form action="{""|fn_url}" method="post" class="rus-payments-refund-form cm-form-dialog-closer" name="refund_form">
            <input type="hidden" name="refund_data[order_id]" value="{$order_info.order_id}" />
            <div class="control-group">
                <label class="control-label" for="rus_payments_refund_amount">{__("addons.rus_payments.amount")} ({$currencies.$primary_currency.symbol nofilter})</label>
                <div class="controls">
                    <input type="text" name="refund_data[amount]" id="rus_payments_refund_amount" class="input-small" value="{$order_info.total|default:"0.00"|fn_format_price:$primary_currency:null:false}" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="rus_payments_refund_cause">{__("addons.rus_payments.cause")}</label>
                <div class="controls">
                    <textarea name="refund_data[cause]" cols="55" rows="3" id="rus_payments_refund_cause"></textarea>
                </div>
            </div>
            <div class="buttons-container">
                <a class="cm-dialog-closer cm-cancel tool-link btn">{__("cancel")}</a>
                {include file="buttons/button.tpl" but_text=__("refund") but_meta="" but_name="dispatch[orders.rus_payments_refund]" but_role="button_main"}
            </div>
        </form>
    <!--rus_payments_refund_dialog--></div>
{/if}

{if $processor_script == 'sbrf.php'}
    <div class="btn-group">
        <a class="btn-small cm-ajax" href="{"orders.send_sbrf_receipt?order_id=`$order_info.order_id`"|fn_url}">{__("send")}</a>
        <a class="btn-small cm-new-window" href="{"orders.print_sbrf_receipt?order_id=`$order_info.order_id`"|fn_url}">{__("print_invoice")}</a>
    </div>
{/if}

{if $processor_script == 'account.php'}
    <div class="btn-group">
        <a class="btn-small cm-ajax" href="{"orders.send_account_payment?order_id=`$order_info.order_id`"|fn_url}">{__("send")}</a>
        <a class="btn-small cm-new-window" href="{"orders.print_invoice_payment?order_id=`$order_info.order_id`"|fn_url}">{__("print_invoice")}</a>
    </div>
{/if}