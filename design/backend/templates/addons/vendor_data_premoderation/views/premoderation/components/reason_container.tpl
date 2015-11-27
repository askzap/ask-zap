<div class="form-horizontal">
	<div class="control-group">
	    <label class="control-label" >{__("reason")}:</label>
	    <div class="controls">
	    	<textarea name="action_reason_{$type}" id="action_reason_{$type}" cols="50" rows="4" class="input-text"></textarea>
	    </div>
	</div>
	
	<div class="control-group cm-toggle-button">
	    <label class="control-label" for="action_notification_{$type}">{__("notify_vendors_by_email")}</label>
	    <div class="controls">
	    	<input type="checkbox" name="action_notification_{$type}" id="action_notification_{$type}" value="Y" checked="checked">
	    </div>
	</div>
</div>