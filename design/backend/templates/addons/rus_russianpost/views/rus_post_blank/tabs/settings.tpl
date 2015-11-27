<fieldset>

   <div class="control-group">
        <label for="blank_total" class="control-label">{__("rus_post_blank.total_cod")}<br/><small>({__("rus_post_blank.113")})</small></label>
        <div class="controls">
            <input type="text" name="blank_data[total]" id="blank_total" value="{$pre_total.113}" size="40" class="input-large" />
        </div>
    </div>

    <div class="control-group">
        <label for="blank_total_cen" class="control-label">{__("rus_post_blank.total_cen")}<br/>
            <small>({__("rus_post_blank.116")}, {__("rus_post_blank.7p")}, {__("rus_post_blank.7b")})</small></label>
        <div class="controls">
            <input type="text" name="blank_data[total_cen]" id="blank_total_cen" value="{$pre_total.116}" size="40" class="input-large" />
        </div>
    </div>

    <div class="control-group">
        <label for="blank_total_cod" class="control-label">{__("rus_post_blank.total_cod")}<br/><small>({__("rus_post_blank.116")}, {__("rus_post_blank.7p")}, {__("rus_post_blank.7b")})</small></label>
        <div class="controls">
            <input type="text" name="blank_data[total_cod]" id="blank_total_cod" value="{$pre_total.116}" size="40" class="input-large" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="blank_not_total">{__("addons.rus_russianpost.not_total")}:</label>
        <div class="controls">
            <label class="checkbox">
                <input type="checkbox" name="blank_data[not_total]" id="blank_not_total" value="Y"/>
            </label>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="elm_options_type">{__("sender")} <small>({__("rus_post_blank.7p")}, {__("rus_post_blank.7b")})</small></label>
        <div class="controls">
            <select class="span3" name="blank_data[sender]" id="blank_sender">
                <option value="1" >{__("company")}</option>
                <option value="0" >{__("rus_post_blank.fiz")}</option>
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="blank_print_bg">{__("rus_post_blank.print_bg")} {include file="common/tooltip.tpl" tooltip=__('rus_post_blank.print_bg.tooltip')}:</label>
        <div class="controls">
            <label class="checkbox">
                <input type="hidden" name="blank_data[print_bg]" value="N" />
                <input type="checkbox" name="blank_data[print_bg]" id="blank_print_bg" checked="checked" value="Y"/>
            </label>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="blank_print_pdf">PDF:</label>
        <div class="controls">
            <label class="checkbox">
                <input type="hidden" name="blank_data[print_pdf]" value="N" />
                <input type="checkbox" name="blank_data[print_pdf]" id="blank_print_pdf" checked="checked" value="Y"/>
            </label>
        </div>
    </div>

    {include file="common/subheader.tpl" title=__("rus_post_blank.112")}

    <div class="control-group">
        <label class="control-label" for="blank_print_sms_for_sender">{__("rus_post_blank.sms_for_sender")}</label>
        <div class="controls">
            <label class="checkbox">
                <input type="hidden" name="blank_data[sms_for_sender]" value="N" />
                <input type="checkbox" name="blank_data[sms_for_sender]" id="blank_print_sms_for_sender" checked="checked" value="Y"/>
            </label>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="blank_print_sms_for_recepient">{__("rus_post_blank.sms_for_recepient")}</label>
        <div class="controls">
            <label class="checkbox">
                <input type="hidden" name="blank_data[sms_for_recepient]" value="N" />
                <input type="checkbox" name="blank_data[sms_for_recepient]" id="blank_print_sms_for_recepient" checked="checked" value="Y"/>
            </label>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="blank_text">{__("rus_post_blank.text")} <small>({__("rus_post_blank.line_1")})</small></label>
        <div class="controls">
            <input type="text" name="blank_data[text1]" id="blank_text" value="" size="40" maxlength="35" class="input-large" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="blank_text2">{__("rus_post_blank.text")} <small>({__("rus_post_blank.line_2")})</small></label>
        <div class="controls">
            <input type="text" name="blank_data[text2]" id="blank_text2" value="" size="40" maxlength="35" class="input-large" />
        </div>
    </div>
</fieldset>