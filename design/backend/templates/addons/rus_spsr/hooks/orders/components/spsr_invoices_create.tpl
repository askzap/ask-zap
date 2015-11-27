
<div class="buttons-container">
    {include file="buttons/button.tpl" but_role="submit" but_meta="btn-primary" but_name="dispatch[orders.spsr_clear_invoice]" but_text=__("shippings.spsr.buttons.spsr_clear_invoice")}
    {include file="buttons/button.tpl" but_role="submit" but_meta="btn-primary" but_name="dispatch[orders.spsr_create_invoice]" but_text=__("shippings.spsr.buttons.spsr_create_invoice")}
</div>

<h3>{__("shippings.spsr.invoice_package_barcode_check")}</h3>

<div id="spsr_packages" class="collapse in">
    {include file="common/subheader.tpl" title=__("shippings.spsr.invoice_settings") target="#spsr_settings_packages"}
    <div id="spsr_settings_packages" class="collapse in">
        <div class="control-group">
            <label class="control-label" for="spsr_invoice_delivery_date">{__("shippings.spsr.invoice_delivery_date")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_delivery_date.tooltip")}:</label>
            <div class="controls">
                {include file="common/calendar.tpl" date_id="spsr_invoice_delivery_date" date_name="spsr_invoice[delivery_date]" date_val=$order_info.timestamp start_year=$settings.Company.company_start_year}
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="spsr_invoice_delivery_time">{__("shippings.spsr.invoice_delivery_time")} {include file="common/tooltip.tpl" tooltip=__("shippings.spsr.invoice_delivery_time.tooltip")}:</label>
            <div class="controls">
                <select name="spsr_invoice[delivery_time]" id="spsr_invoice_delivery_time">
                    <option value="AM">{__("shippings.spsr.invoice_delivery_time.am")}</option>
                    <option value="PM">{__("shippings.spsr.invoice_delivery_time.pm")}</option>
                    <option value="WD">{__("shippings.spsr.invoice_delivery_time.wd")}</option>
                    <option value="AM1">{__("shippings.spsr.invoice_delivery_time.am1")}</option>
                    <option value="PM1">{__("shippings.spsr.invoice_delivery_time.pm1")}</option>
                    <option value="PM2">{__("shippings.spsr.invoice_delivery_time.pm2")}</option>
                    <option value="WD1">{__("shippings.spsr.invoice_delivery_time.wd1")}</option>
                </select>
            </div>
        </div>

        {if $addr_list}
            <div class="control-group">
                <label class="control-label" for="spsr_sbor_addr">{__("shippings.spsr.sbor_addr_label")}:</label>
                <div class="controls">
                    <select name="spsr_invoice[sbor_addr]" id="spsr_sbor_addr">
                        {foreach from=$addr_list item="addr"}
                            <option  value="{$addr.SborAddr_ID}|{$addr.SborAddr_Owner_ID}">{$addr.Organization}/{$addr.CityName1}/{$addr.Address}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        {/if}
    </div>

    {foreach from=$spsr_invoices item=invoice key=key}
        {assign var="invoice_title" value="{__("shippings.spsr.invoice")} #`$invoice.shipment.shipment_id`"}
        {include file="common/subheader.tpl" title="`$invoice_title`" target="#spsr_invoice_`$key`"}

        <h4><a class="underlined" href="{"shipments.details?shipment_id=`$invoice.shipment.shipment_id`"|fn_url}"><span>{__("details")}</span></a></h4>

        <div class="control-group">
            <input type="hidden" name="spsr_invoice[invoice_product_code]" value="{$invoice.spsr_tariff.Code}"/>
            <label>{__("shippings.spsr.invoice_product_code")}: {$invoice.spsr_tariff.TariffType}</label>
        </div>

        <div id="spsr_invoice_{$key}" class="collapse in">
            {foreach from=$invoice.packages item=piece}
                <h4>{__("shippings.spsr.piece")}: {$piece.item_id}</h4>
                <table width="100%" class="table table-middle">
                    <thead>
                    <tr>    
                        <th class="left" width="65%">
                            {__("shippings.spsr.barcodes")}
                        </th>
                        <th class="right" width="5%">
                            {__("weight")}
                        </th>
                        <th class="right" width="5%">
                            {__("length")}
                        </th>
                        <th class="right" width="5%">
                            {__("width")}
                        </th>
                        <th class="right" width="5%">  
                            {__("height")}
                        </th>
                        <th class="right" width="15%">
                            {__("shippings.spsr.product_type")}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="cm-row-status" valign="top">
                        <td class="left">
                            {assign var="code" value=$piece.item_id|fn_rus_spsr_barcode_number}
                            <div class="left">
                               <a href="{"orders.spsr_barcode?id=`$code`&width=`$info_barcode.width`&height=`$info_barcode.height`&type=`$info_barcode.type`"|fn_url}"><img src="{"orders.spsr_barcode?id=`$code`&width=`$info_barcode.width`&height=`$info_barcode.height`&type=`$info_barcode.type`"|fn_url}" alt="BarCode" width="{$info_barcode.width}" height="{$info_barcode.height}"></a>
                            </div>
                            <div class="product-code">
                                <input type="text" name="spsr_invoice[barcodes][{$key}][{$piece.item_id}]" size="15" maxlength="32" value="{$code}" class="span2" />
                            </div>
                        </td>
                        <td class="right">
                            {$piece.data.weight}
                        </td>
                        <td class="right">
                            {$piece.data.length}
                        </td>
                        <td class="right">
                            {$piece.data.width}
                        </td>
                        <td class="right">
                            {$piece.data.height}
                        </td>
                        <td class="right">
                            {$piece.data.description}     
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table width="100%" class="table table-middle" >
                    <thead>
                    <tr>    
                        <th class="left" width="30%">      
                            {__("product")}
                        </th>
                        <th class="left" width="10%">       
                            {__("product_code")}
                        </th>
                        <th class="left" width="15%">       
                            {__("price")}
                        </th>
                        <th class="left" width="10%">  
                            {__("qty")}     
                        </th>
                        <th class="right" width="5%">       
                            {__("weight")}
                        </th>
                        <th class="right" width="5%">  
                            {__("length")}     
                        </th>
                        <th class="right" width="5%">       
                            {__("width")}
                        </th>
                        <th class="right" width="5%">  
                            {__("height")}     
                        </th>
                        <th class="right" width="15%">  
                            {__("shippings.spsr.product_type")}     
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$piece.data.products item=product}
                    <tr class="cm-row-status" valign="top" >
                        <td class="left">
                            {$product.product}
                        </td>
                        <td class="left">
                            {$product.product_code}
                        </td>
                        <td class="left">
                            {include file="common/price.tpl" value=$product.price}
                        </td>
                        <td class="left">
                            {$product.amount}
                        </td>
                        <td class="right">
                            {$product.weight}
                        </td>
                        <td class="right">
                            {$product.length}
                        </td>
                        <td class="right">
                            {$product.width}
                        </td>
                        <td class="right">
                            {$product.height}
                        </td>
                        <td class="right">
                            {$product.product_type}
                        </td>
                    </tr>
                    {/foreach}
                    </tbody>
                </table>
            <hr/>
            {/foreach}
        <!--spsr_invoice_{$key}--></div>
    {/foreach}

    <h3>{__("total")}</h3>
    <table width="100%" class="table table-middle" >
        <tr>
            <td width="25%">
                {__("products")}: {$spsr_total.amount}<br/>
            <td/>
            <td width="25%">
                {__("subtotal")}: {$spsr_total.cost}<br/>
            <td/>
            <td width="25%">
                {__("shipping_cost")}: {$spsr_total.shipping_cost}<br/>
            <td/>
            <td width="25%">
                {__("shippings.spsr.pieces_amount")}: {$spsr_total.packages_count}<br/>
            <td/>
        </tr>
    </table>
</div>
<hr />
