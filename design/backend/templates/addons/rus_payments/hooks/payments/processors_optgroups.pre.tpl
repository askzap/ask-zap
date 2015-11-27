<optgroup label="{__("russian_payments")}">
    {foreach from=$payment_processors item="processor"}
        {if $processor.russian == "Y"}
            <option value="{$processor.processor_id}" {if $payment.processor_id == $processor.processor_id}selected="selected"{/if}>{$processor.processor}</option>
        {/if}
    {/foreach}
</optgroup>
