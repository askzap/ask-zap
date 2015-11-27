{script src="js/lib/maskedinput/jquery.maskedinput.min.js"}

{script src="js/lib/inputmask/jquery.inputmask.min.js"}
{script src="js/lib/inputmask-multi/jquery.inputmask-multi.js"}
{script src="js/lib/jquery-bind-first/jquery.bind-first-0.2.3.js"}

<script type="text/javascript">
    (function(_, $) {
        _.qiwi_phone_masks_list = {$qiwi_phone_mask_codes nofilter};
        {if $addons.qiwi_rest.phone_mask}
        _.qiwi_phone_mask = '{$addons.qiwi_rest.phone_mask}'
        {/if}
    }(Tygh, Tygh.$));
</script>

{script src="js/addons/qiwi_rest/input_mask.js"}

<div class="ty-qiwi-rest">
    <div class="ty-qiwi-rest__control-group ty-control-group">
        <label for="qiwi_phone_number" class="ty-control-group__title cm-required">{__("phone")}</label>
        <input id="qiwi_phone_number" size="35" type="text" name="payment_info[phone]" value="{$phone_normalize}" class="ty-input-big cm-mask" />
    </div>
</div>