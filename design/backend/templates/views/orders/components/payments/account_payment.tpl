
<div class="ty-control-group">
    <label for="organization_customer" class="ty-control-group__title">{__("addons.rus_payments.organization_customer")}</label>
    <input type="text" name="payment_info[organization_customer]" id="organization_customer" value="{$cart.payment_info.organization_customer}" size="20">
</div>

<div class="ty-control-group">
    <label for="inn_customer" class="ty-control-group__title">{__("inn_customer")}</label>
    <input type="text" name="payment_info[inn_customer]" id="inn_customer" value="{$cart.payment_info.inn_customer}" size="20" maxlength="12">
</div>

<div class="ty-control-group">
    <label for="phone_customer" class="ty-control-group__title">{__("phone")}</label>
    <input type="text" name="payment_info[phone]" id="phone_customer" value="{$cart.payment_info.phone}" size="20">
</div>

<div class="ty-control-group">
    <label for="address_customer" class="ty-control-group__title">{__("address")}</label>
    <input type="text" name="payment_info[address]" id="address_customer" value="{$cart.payment_info.address}" size="20">
</div>

<div class="ty-control-group">
    <label for="zip_postal_code" class="ty-control-group__title">{__("zip_postal_code")}</label>
    <input type="text" name="payment_info[zip_postal_code]" id="zip_postal_code" value="{$cart.payment_info.zip_postal_code}" size="6">
</div>

<div class="ty-control-group">
    <label for="bank_details" class="ty-control-group__title">{__("addons.rus_payments.bank_details")}</label>
    <textarea id="bank_details" size="35"  cols="30" rows="5" name="payment_info[bank_details]" value="" class="ty-input-textarea cm-autocomplete-off" >{$cart.payment_info.bank_details}</textarea>
</div>