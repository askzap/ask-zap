{script src="js/lib/inputmask/jquery.inputmask.min.js"}
{script src="js/addons/qiwi_rest/jquery.inputmask-multi.js"}
{script src="js/addons/qiwi_rest/input_mask.js"}

<div class="control-group">
    <label for="qiwi_phone_number" class="control-label cm-required">{__("phone")}</label>
    <div class="controls">
        <input id="qiwi_phone_number" size="35" type="text" name="payment_info[phone]" value="{$cart.user_data.b_phone|fn_qiwi_rest_normalize_phone}" class="input-big cm-mask" />
    </div>
</div>


