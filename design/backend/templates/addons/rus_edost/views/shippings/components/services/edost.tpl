{* rus_build_edost dbazhenov *}
<fieldset>

{if !$config.demo_mode}
<div class="control-group">
	<label class="control-label" for="user_store_id">{__("store_id")}:</label>
	<div class="controls">
	<input id="user_store_id" type="text" name="shipping_data[service_params][store_id]" size="30" value="{$shipping.service_params.store_id}"/>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="user_server_password">{__("server_password")}:</label>
	<div class="controls">
	<input id="user_server_password" type="text" name="shipping_data[service_params][server_password]" size="30" value="{$shipping.service_params.server_password}"/>
	</div>
</div>
{/if}

<div class="control-group">
	<label class="control-label" for="ship_edost_height">{__("ship_height")}:</label>
	<div class="controls">
	<input id="ship_edost_height" type="text" name="shipping_data[service_params][height]" size="30" value="{$shipping.service_params.height}"/>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="ship_edost_width">{__("ship_width")}:</label>
	<div class="controls">
	<input id="ship_edost_width" type="text" name="shipping_data[service_params][width]" size="30" value="{$shipping.service_params.width}"/>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="ship_edost_length">{__("ship_length")}:</label>
	<div class="controls">
	<input id="ship_edost_length" type="text" name="shipping_data[service_params][length]" size="30" value="{$shipping.service_params.length}"/>
	</div>
</div>

</fieldset>

