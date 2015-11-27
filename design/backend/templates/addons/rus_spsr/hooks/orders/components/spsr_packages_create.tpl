
<div class="buttons-container">
    {include file="buttons/button.tpl" but_role="submit" but_meta="btn-primary" but_name="dispatch[orders.spsr_create_packages]" but_text=__("shippings.spsr.buttons.spsr_create_packages")}
</div>

<h3>{__("shippings.spsr.invoice_pieces_check")}</h3>

<div id="spsr_invoice" class="collapse in">
    {foreach from=$spsr_data_invoice item=invoice key=key}
        {assign var="spsr_invoice_title" value="{__("shippings.spsr.invoice")}: #{$invoice.shipment.shipment_id}"}
        {include file="common/subheader.tpl" title="`$spsr_invoice_title`" target="#spsr_invoice_`$key`"}

        <h5><a class="underlined" href="{"shipments.details?shipment_id=`$invoice.shipment.shipment_id`"|fn_url}"><span>{__("details")}</span></a></h5>

        {if $invoice.spsr_tariffs}
            {foreach from=$invoice.spsr_tariffs item="tariff"}
                <input type="hidden" value="{$tariff.Total_Dost}" id="spsr_cost_tariff_{$key}_{$tariff.Code}" />
            {/foreach}
            <div id="spsr_tariff_{$key}" class="control-group">
                <label class="control-label" for="spsr_invoice_product_code">{__("shippings.spsr.invoice_product_code")}:</label>
                <div class="controls">
                    <select name="add_invoice_product_code[{$invoice.shipment.shipment_id}]" id="spsr_invoice_product_code_{$key}">
                        {foreach from=$invoice.spsr_tariffs item="tariff"}
                            <option value="{$tariff.Code}" {if ($tariff.Code == 'PelSt')}selected="selected"{/if}>{$tariff.TariffType} ({$tariff.Total_Dost})</option>
                        {/foreach}
                    </select>
                </div>
            <!--spsr_tariff_{$key}--></div>
        {/if}

        <div id="spsr_invoice_{$key}" class="collapse in">
            {capture name="pieces"}
            {math equation="x + 1" assign="num" x=$invoice.amount}
            {section name=foo start=1 loop=$num step=1}
                <option value="{$smarty.section.foo.index}">{__("shippings.spsr.piece")} {$smarty.section.foo.index}</option>
            {/section}
            {/capture}

            {capture name="bags"}
                <option value="s">{__("shippings.spsr.s")} ({$invoice.package_info.bag_size.s.length}/{$invoice.package_info.bag_size.s.width}/{$invoice.package_info.bag_size.s.height})</option>
                <option value="m">{__("shippings.spsr.m")} ({$invoice.package_info.bag_size.m.length}/{$invoice.package_info.bag_size.m.width}/{$invoice.package_info.bag_size.m.height})</option>
                <option value="l">{__("shippings.spsr.l")} ({$invoice.package_info.bag_size.l.length}/{$invoice.package_info.bag_size.l.width}/{$invoice.package_info.bag_size.l.height})</option>
                <option value="x">{__("shippings.spsr.individual_size")}</option>
            {/capture}

            {__("override_product_data")}     
            <select id="change_piece_checked_{$key}" style="width: 130px;">
                {$smarty.capture.pieces nofilter}
            </select> 
            <select id="change_piece_bag_checked_{$key}" style="width: 150px;">
                {$smarty.capture.bags nofilter}
            </select>

            <table width="100%" class="table table-middle" >
            <thead>
            <tr>    
                <th class="left">
                    {include file="common/check_items.tpl" check_target="invoice_`$key`"}
                </th>
                <th class="left" width="30%">
                    {__("product")}<br/>
                    {__("product_code")}
                </th>
                <th class="center" width="5%">
                    {__("price")}
                </th>
                <th class="center" width="5%">
                    {__("qty")}
                </th>
                <th class="left" width="10%">
                    {__("weight")}<br/>
                    {__("shippings.spsr.size")}
                </th>
                <th class="left" width="5%">
                    {__("shippings.spsr.product_type")}
                </th>
                <th class="center" width="25%">
                    {__("shippings.spsr.pieces")}
                </th>
                <th class="center" width="20%">
                    {__("shippings.spsr.pieces_bag")}
                </th>
            </tr>
            </thead>

            <tbody>
            {foreach from=$invoice.products key="p_key" item=product}
                <tr class="cm-row-status" valign="top" >
                    <input type="hidden" name="add_product_piece[{$key}][{$p_key}][id]" value="{$product.product_id}" />
                    <td class="left">
                        <input type="checkbox" id="invoice_{$key}_{$p_key}" value="{$p_key}" class="checkbox cm-item-invoice_{$key} cm-item-status-{$product.status|lower}" /></td>
                    <td class="left">
                        {$product.product}<br/>
                        {$product.product_code}
                    </td>
                    <td class="center">
                        {include file="common/price.tpl" value=$product.price}
                    </td>
                    <td class="center">
                        {$product.amount}
                    </td>
                    <td class="left">
                        {$product.weight}<br/>
                        {$product.length} / {$product.width} / {$product.height}
                    </td>
                    <td class="left">
                        {$product.product_type}
                    </td>
                    <td class="center">
                        <select id="piece_{$key}_{$p_key}" style="width: 130px;" name="add_product_piece[{$key}][{$p_key}][piece]">
                            {$smarty.capture.pieces nofilter}
                        </select> 
                    </td>
                    <td class="center">
                        <select class="size_bag_{$key}_{$p_key}" id="size_{$key}_{$p_key}" style="width: 150px;" name="add_product_piece[{$key}][{$p_key}][bag]">
                            {$smarty.capture.bags nofilter}
                        </select>
                    </td>
                </tr>
            {/foreach}

            {if $invoice.package_info.cod || $invoice.package_info.plat_type != 1 || $invoice.package_info.paid_by_receiver}
                <tr class="cm-row-status" valign="top" >
                    <td class="left">
                    </td>
                    <td class="left">
                        {__("shipping")}
                    </td>
                    <td class="center">
                        <input id="invoice_cost_{$key}" type="text" name="add_invoice_ship_cost[{$invoice.shipment.shipment_id}]" size="12" value="{$invoice.invoice_shipping_cost|fn_format_price:$primary_currency:null:false}" class="span2"/>
                    </td>
                    <td class="center">
                    </td>
                    <td class="left">
                    </td>
                    <td class="left">
                    </td>
                    <td class="center">
                    </td>
                    <td class="center">
                    </td>
                </tr>
            {/if}
            </tbody>
            </table>

            <script type="text/javascript">
            //<![CDATA[
            (function(_, $) {
                $(document).ready(function() {
                    $(_.doc).on('change', '.size_bag_{$key}' , function() {

                        var id = this.id;
                        var value = this.value;
                        var replace_data = 'size_' + {$key} + '_';
                        id = id.replace(replace_data, "");
                        var piece = $('#piece_' + {$key} + '_' + id).val();

                        $('.size_bag_{$key}').each( function() {
                            var _id = this.id;
                            _id = _id.replace(replace_data, "");
                            var _piece = $('#piece_' + {$key} + '_' + _id).val();
                            if(_piece == piece) {
                                $(this).val(value);
                            }
                        });

                    });

                    $(_.doc).on('change', '#change_piece_checked_{$key}' , function() {
                        var value = this.value;
                        $('.cm-item-invoice_{$key}').each( function() {
                            var elm = $(this);
                            if(elm.prop('checked')) {
                                var elm_id = elm.prop('value');
                                $('#piece_{$key}_' + elm_id).val(value);
                            }   
                        });
                    });

                    $(_.doc).on('change', '#change_piece_bag_checked_{$key}' , function() {
                        var value = this.value;
                        $('.cm-item-invoice_{$key}').each( function() {
                            var elm = $(this);
                            if(elm.prop('checked')) {
                                var elm_id = elm.prop('value');
                                $('#size_{$key}_' + elm_id).val(value);
                            }   
                        });
                    });

                    $(_.doc).on('change', '#spsr_invoice_product_code_{$key}' , function() {
                        var value = this.value;
                        var cost = $('#spsr_cost_tariff_{$key}_' + value).val();
                        $('#invoice_cost_{$key}').val(cost);
                    });
                });

            }(Tygh, Tygh.$));
            //]]>
            </script>
        <!--spsr_invoice_{$key}--></div>
    {/foreach}
</div>
<hr />