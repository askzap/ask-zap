{foreach from = $order_info.shipping item=data_shipping}
{if ($data_shipping.module == 'sdek')}
	{if $use_shipments}
		<ul>
			{foreach from=$order_info.shipping item="shipping_method"}
				<li>{if $shipping_method.office_data}   
					<br />
					<p class="strong">
						{$shipping_method.office_data.Name}
					</p>
					<p class="muted">
						{$shipping_method.office_data.Address}<br />
						{$shipping_method.office_data.Phone}<br />
						{$shipping_method.office_data.WorkTime}<br />
					</p>
				{/if}
				</li>
			{/foreach}
		</ul>
	{else}
		{foreach from=$order_info.shipping item="shipping" name="f_shipp"}
			{if $shipments[$shipping.group_key].carrier && $shipments[$shipping.group_key].tracking_number}
				{include file="common/carriers.tpl" carrier=$shipments[$shipping.group_key].carrier tracking_number=$shipments[$shipping.group_key].tracking_number}
				{$shipping.shipping}&nbsp;({__("tracking_num")}<a {if $smarty.capture.carrier_url|strpos:"://"}target="_blank"{/if} href="{$smarty.capture.carrier_url nofilter}">{$shipments[$shipping.group_key].tracking_number}</a>)
			{else}
				{$shipping.shipping}
			{/if}
			{if !$smarty.foreach.f_shipp.last}<br>{/if}
		{/foreach}
	{/if}
{/if}
{/foreach}