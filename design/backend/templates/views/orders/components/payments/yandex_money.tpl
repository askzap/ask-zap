
{assign var="yandex_payments" value=$payment_method.processor_params.payments}

{if $yandex_payments|count > 1}
    <div class="control-group">
        <label for="yandex_payment_type">{__("select_yandex_payment")}</label>
        <select name="payment_info[yandex_payment_type]" id="yandex_payment_type">
            {if $payment_method.processor_params.payments.pc}
                <option value="pc"> {__('yandex_payment_yandex')}</option>
            {/if}
            {if $payment_method.processor_params.payments.ac}
                <option value="ac"> {__('yandex_payment_card')}</option>
            {/if}
            {if $payment_method.processor_params.payments.gp}
                <option value="gp"> {__('yandex_payment_terminal')}</option>
            {/if}
            {if $payment_method.processor_params.payments.mc}
                <option value="mc"> {__('yandex_payment_phone')}</option>
            {/if}
            {if $payment_method.processor_params.payments.wm}
                <option value="wm"> {__('yandex_payment_webmoney')}</option>
            {/if}
            {if $payment_method.processor_params.payments.ab}
                <option value="ab"> {__('yandex_payment_alfabank')}</option>
            {/if}
            {if $payment_method.processor_params.payments.sb}
                <option value="sb"> {__('yandex_payment_sberbank')}</option>
            {/if}
            {if $payment_info.processor_params.payments.ma}
                <option value="ma"> {__('yandex_payment_masterpass')}</option>
            {/if}
            {if $payment_info.processor_params.payments.pb}
                <option value="pb"> {__('yandex_payment_psbank')}</option>
            {/if}
        </select>
    </div>
{elseif is_array($yandex_payments)}
    <input type="hidden" name="payment_info[yandex_payment_type]" value="{$yandex_payments|key}">
{else}
    <input type="hidden" name="payment_info[yandex_payment_type]" value="">
{/if}
