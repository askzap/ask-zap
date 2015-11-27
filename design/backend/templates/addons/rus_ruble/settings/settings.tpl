
<fieldset>
    {assign var="magic_key" value=$addons.rus_ruble.cron_key|urlencode}
    <input type="hidden" name="magic_key" value="{$magic_key}"/>

    {if $exist_currency_rub}
        <div class="control-group">
            <label class="control-label" for="symbol_update">{__('rus_ruble.symbol_update')}:</label>
            <div class="controls" id="symbol_update">
                <br />
                {include file="buttons/button.tpl" but_role="submit" but_name="dispatch[currencies_sync.symbol_update]" but_text=__("rus_ruble.symbol_update_button")}
            </div>
        </div>
    {else}
        <div class="control-group">
            <label class="control-label" for="symbol_update">{__('rus_ruble.currency_install')}:</label>
            <div class="controls" id="symbol_update">
                <p>{__('rus_ruble.currency_install_info')}</p>
                <br />
                {include file="buttons/button.tpl" but_role="submit" but_name="dispatch[currencies_sync.symbol_install]" but_text=__("rus_ruble.currency_install_button")}
            </div>
        </div>
    {/if}

    <div class="control-group">
        <label class="control-label" for="symbol_update">{__('rus_ruble.currency_sync')}:</label>
        <div class="controls" id="symbol_update">
            <p>{__('rus_ruble.currencies_sync_info')}</p>
            <br />
            {include file="buttons/button.tpl" but_role="submit" but_name="dispatch[currencies_sync.sync]" but_text=__("rus_ruble.currency_sync_button")}

        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="symbol_update">{__('rus_ruble.magic_keygen')}:</label>
        <div class="controls" id="symbol_update">
            {include file="buttons/button.tpl" but_role="submit" but_name="dispatch[currencies_sync.keygen]" but_text=__("rus_ruble.magic_keygen_button")}
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="symbol_update">{__('rus_ruble.currency_sync_auto')}:</label>
        <div class="controls" id="symbol_update">
            {__('rus_ruble.auto_info')}
            <br />
            {"currencies_sync.sync_cron?magic_key=`$magic_key`"|fn_url}
            <br />
            {__('rus_ruble.auto_instruction_data')}
            <br />
            {__('rus_ruble.auto_instruction_data_end')}
        </div>
    </div>


</fieldset>

