
{include file="common/subheader.tpl" title=__("addons.rus_payments.company_info") target="#company_info"}
<div id="company_info" class="in collapse">
    <div class="control-group">
        <label class="control-label" for="account_recepient_name">{__("recipient")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][account_recepient_name]" id="account_recepient_name" value="{$processor_params.account_recepient_name}">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="account_address">{__("address")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][account_address]" id="account_address" value="{$processor_params.account_address}">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="account_phone">{__("phone")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][account_phone]" id="account_phone" value="{$processor_params.account_phone}" size="80">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="account_kpp">{__("addons.rus_payments.account_kpp")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][account_kpp]" id="account_kpp" value="{$processor_params.account_kpp}" size="9" maxlength="9">
        </div>
    </div>
        
    <div class="control-group">
        <label class="control-label" for="account_inn">{__("inn_customer")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][account_inn]" id="account_inn" value="{$processor_params.account_inn}" size="12" maxlength="12">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="account_current">{__("addons.rus_payments.account_current")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][account_current]" id="account_current" value="{$processor_params.account_current}" size="20" maxlength="20">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="account_bank">{__("addons.rus_payments.account_bank")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][account_bank]" id="account_bank" value="{$processor_params.account_bank}"  size="80">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="account_bik">{__("addons.rus_payments.account_bik")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][account_bik]" id="account_bik" value="{$processor_params.account_bik}"  size="9" maxlength="9">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="account_cor">{__("addons.rus_payments.account_cor")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][account_cor]" id="account_cor" value="{$processor_params.account_cor}"  size="20" maxlength="20">
        </div>
    </div>

    <div class="control-group hidden">
        <input type="hidden" name="payment_data[processor_params][account_enabled]" value="Y">
    </div>

    <div class="control-group">
        <label class="control-label">{__("addons.rus_payments.invoice_print")}:</label>
        <div class="controls">{include file="common/attach_images.tpl" image_name="path_stamp" image_key=$id image_object_type="stamp" image_pair=$payment_image.path_stamp no_detailed="Y" hide_titles="Y" image_object_id=$id}</div>
    </div>

    <div class="control-group">
        <label class="control-label" for="print_width">{__("addons.rus_payments.account_print_width")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][account_print_width]" id="print_width" value="{if $processor_params.account_print_width}{$processor_params.account_print_width}{else}120{/if}"  size="10" maxlength="10">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="print_height">{__("addons.rus_payments.account_print_height")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][account_print_height]" id="print_height" value="{if $processor_params.account_print_height}{$processor_params.account_print_height}{else}120{/if}"  size="10" maxlength="10">
        </div>
    </div>

    {assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}

    <div class="control-group">
        <label class="control-label" for="account_order_status">{__("account_order_status")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][account_order_status]" id="account_order_status">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}" {if $processor_params.account_order_status == $k}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>

{if $account_fields}
    {include file="common/subheader.tpl" title=__("addons.rus_payments.user_info") target="#user_info"}
    <div id="user_info" class="in collapse">
        {foreach from=$account_fields item="account_field" key="key_name"}
            <div class="control-group">
                <label class="control-label" for="account_field_inn">{$account_field.name}:</label>
                <div class="controls">
                    <select name="payment_data[processor_params][fields_account][{$key_name}]">
                        <option value=""></option>
                        <optgroup label="{__("contact_information")}">
                            {foreach from=$profile_fields['C'] item="profile_field"}
                                {if $profile_field.profile_show == 'Y' || $profile_field.checkout_show == 'Y' || $profile_field.partner_required == 'Y'}
                                    <option {if $processor_params.fields_account[$key_name] == $profile_field.field_id}selected="selected"{/if} value="{$profile_field.field_id}">{$profile_field.description}</option>
                                {/if}
                            {/foreach}
                        </optgroup>

                        <optgroup label="{__("billing_address")}">
                            {foreach from=$profile_fields['B'] item="profile_field"}
                                {if $profile_field.profile_show == 'Y' || $profile_field.checkout_show == 'Y' || $profile_field.partner_required == 'Y'}
                                    <option {if $processor_params.fields_account[$key_name] == $profile_field.field_id}selected="selected"{/if} value="{$profile_field.field_id}">{$profile_field.description}</option>
                                {/if}
                            {/foreach}
                        </optgroup>

                        <optgroup label="{__("shipping_address")}">
                            {foreach from=$profile_fields['S'] item="profile_field"}
                                {if $profile_field.profile_show == 'Y' || $profile_field.checkout_show == 'Y' || $profile_field.partner_required == 'Y'}
                                    <option {if $processor_params.fields_account[$key_name] == $profile_field.field_id}selected="selected"{/if} value="{$profile_field.field_id}">{$profile_field.description}</option>
                                {/if}
                            {/foreach}
                        </optgroup>
                    </select>
                </div>
            </div>
        {/foreach}
    </div>
{/if}
