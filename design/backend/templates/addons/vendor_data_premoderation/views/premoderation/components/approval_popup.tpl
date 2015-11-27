<div class="form-horizontal">
	<div class="control-group">
		<label class="control-label" for="{$name}">{__("reason")}:</label>
	    <div class="controls">
	    	<input type="hidden" name="{$name}[company_id]" value="{$company_id}" />
	    	<input type="hidden" name="{$name}[product_id]" value="{$product_id}" />
	    	<input type="hidden" name="{$name}[status]" value="{$status}" />
	    	<textarea name="{$name}[reason_{$status}]" id="{$name}" cols="50" rows="4" class="input-text"></textarea>
	    </div>
	</div>
	
	<div class="control-group cm-toggle-button">
		<label class="control-label" for="notify_user_{$product_id}">{__("notify_vendor_by_email")}</label>
	    <div class="controls">
	    	<input type="checkbox" name="{$name}[notify_user_{$status}]" id="notify_user_{$product_id}" value="Y" checked="checked">
	    </div>
	</div>
</div>