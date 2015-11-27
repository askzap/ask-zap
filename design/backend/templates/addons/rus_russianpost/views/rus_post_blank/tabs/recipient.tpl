<fieldset>

    {include file="common/subheader.tpl" title=__("rus_post_blank.subheader.customer")}
    <div>
        <div class="control-group">
            <label for="blank_from_whom" class="control-label">{__("rus_post_blank.whom")} <small>({__("rus_post_blank.line_1")})</small></label>
            <div class="controls">
                <input type="text" name="blank_data[from_whom]" id="blank_from_whom" value="{$order_info.fio}" size="40" class="input-large" />
            </div>
        </div>
        <div class="control-group">
            <label for="blank_from_whom2" class="control-label">{__("rus_post_blank.whom")} <small>({__("rus_post_blank.line_2")})</small></label>
            <div class="controls">
                <input type="text" name="blank_data[from_whom2]" id="blank_from_whom2" value="" size="40" class="input-large" />
            </div>
        </div>
        <div class="control-group">
            <label for="blank_sender_address" class="control-label">{__("address")} <small>({__("rus_post_blank.line_1")})</small></label>
            <div class="controls">
                <input type="text" name="blank_data[sender_address]" id="blank_sender_address" value="{$order_info.s_address}" size="40" class="input-large" />
            </div>
        </div>
        <div class="control-group">
            <label for="blank_sender_address2" class="control-label">{__("address")} <small>({__("rus_post_blank.line_2")})</small></label>
            <div class="controls">
                <input type="text" name="blank_data[sender_address2]" id="blank_sender_address2" value="{$order_info.address_line_2}" size="40" class="input-large" />
            </div>
        </div>
        <div class="control-group">
            <label for="blank_from_index" class="control-label">{__("rus_post_blank.index")}</small></label>
            <div class="controls">
                <input type="text" name="blank_data[from_index]" id="blank_from_index" value="{$order_info.s_zipcode}" size="40" class="input-large" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="blank_recipient_phone">{__("rus_post_blank.recipient_phone")}</label>
            <div class="controls">
                <input type="text" name="blank_data[recipient_phone]" id="blank_recipient_phone" value="{$order_info.recipient_phone}" size="40" class="input-large" />
            </div>
        </div>
    </div>  
</fieldset>