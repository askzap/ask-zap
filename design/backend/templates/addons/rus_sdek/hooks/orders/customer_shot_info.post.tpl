{foreach from=$order_info.shipping item="shipping" key="shipping_id" name="f_shipp"}
    {if ($shipping.module == 'sdek') && ($shipping.office_data)}
            <div class="well orders-right-pane form-horizontal">    
			<div class="control-group shift-top">
				<div class="control-label">
					{include file="common/subheader.tpl" title=__("shippings_sdek.header_shipping_office")}
				</div>
			</div>

			<p class="strong">
			{$shipping.office_data.Name}
			</p>
			<p class="muted">
			{$shipping.office_data.Address}<br />
            {$shipping.office_data.WorkTime}<br />
            {$shipping.office_data.Phone}<br />
            {$shipping.office_data.Note}<br />
			</p>
		</div>
	{/if}
{/foreach}

