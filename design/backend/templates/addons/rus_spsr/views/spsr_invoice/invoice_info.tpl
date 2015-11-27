{capture name="mainbox"}
    {if $invoice.invoice_info}
        {include file="common/subheader.tpl" title=__("information") }
        <table width="100%" class="table table-middle" >
            <thead>
                <tr>
                    <th class="left">
                        {__("status")}
                    </th>
                    <th class="left" >
                        {__("shippings.spsr.invoice_number")}
                    </th>
                    <th class="left">
                        {__("order_id")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.order_number")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.add_courier")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.insurance_sum")}<br/>
                        {__("shippings.spsr.declared_sum")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.cod_goods_sum")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.cod_delivery_sum")}
                    </th>
                    <th class="left">
                        {__("shippings.spsr.timestamp")}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="cm-row-status" valign="top" >
                    <td class="left">
                        {$invoice.invoice_info.CurState}
                    </td>
                    <td class="left">
                        {$invoice.invoice_info.ShipmentNumber}
                    </td>
                    <td class="left">
                         <a href="{"orders.details?order_id=`$invoice.order_id`"|fn_url}" class="underlined">{$invoice.order_id}</a>
                    </td>
                    <td class="left">
                        {$invoice.invoice_info.ShipRefNum}
                    </td>
                    <td class="left">
                        {$invoice.invoice_info.OrderNumber}
                    </td>
                    <td class="left">
                        {$invoice.invoice_info.InsuranceSum}<br/>
                        {$invoice.invoice_info.DeclaredSum}
                    </td>
                    <td class="left">
                        {$invoice.invoice_info.CODGoodsSum}
                    </td>
                    <td class="left">
                        {$invoice.invoice_info.CODDeliverySum}
                    </td>
                    <td class="left">
                        {$invoice.invoice_info.AgreedDate}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="form-horizontal form-edit">
            <fieldset>
                <div class="control-group">
                    <label class="control-label">{__("full_description")}:</label>
                    <div class="controls">
                        {$invoice.invoice_info.FullDescription nofilter}
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">{__("shippings.spsr.pieces_amount")}:</label>
                    <div class="controls">
                        {$invoice.pieces_amount}
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">{__("shippings.spsr.products_amount")}:</label>
                    <div class="controls">
                        {$invoice.products_amount}
                    </div>
                </div>
            </fieldset>
        </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    {if $pieces}
        <h3>{__("shippings.spsr.pieces")}</h3>

        {foreach from=$pieces item=piece}
            {assign var="piece_title" value="{__("shippings.spsr.piece")}: `$piece.item_id`"}
            {include file="common/subheader.tpl" title=$piece_title }

            <table width="100%" class="table table-middle" >
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
                <tr class="cm-row-status" valign="top" >
                    <td class="left">
                        {assign var="code" value=$piece.barcode}
                        <div class="left">
                           <a href="{"orders.spsr_barcode?id=`$code`&width=`$info_barcode.width`&height=`$info_barcode.height`&type=`$info_barcode.type`"|fn_url}"><img src="{"orders.spsr_barcode?id=`$code`&width=`$info_barcode.width`&height=`$info_barcode.height`&type=`$info_barcode.type`"|fn_url}" alt="BarCode" width="{$info_barcode.width}" height="{$info_barcode.height}"></a>
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

            <h4>{__("products")}</h4>
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
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    {capture name="sidebar"}
        {include file="addons/rus_spsr/views/components/shipper_info.tpl" receiver=$invoice.receiver shipper=$invoice.shipper }
    {/capture}

    {capture name="buttons"}
        {include file="buttons/button.tpl" but_role="action" but_href="spsr_invoice.print_invoice?invoice_id=`$invoice.invoice_info.ShipmentNumber`" but_meta="btn-primary" but_text=__("print_packing_slip")}
    {/capture}
{/capture}

{assign var="title" value="{__("shippings.spsr.invoice")}: `$invoice.invoice_info.ShipmentNumber`"}
{include file="common/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=false buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar}
