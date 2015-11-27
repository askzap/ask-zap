

{literal}
<script type="text/javascript">
//<![CDATA[
function fn_calculate_total_shipping() 
{
    var $ = Tygh.$;
    params = [];
    parents = $('#shipping_estimation');
    radio = $('input[type=radio]:checked', parents);

    $.each(radio, function(id, elm) {
        params.push({name: elm.name, value: elm.value});
    });

    params.push({name: $('#elm_country').prop('name'), value: $('#elm_country').val()});
    params.push({name: $('#elm_state').prop('name'), value: $('#elm_state').val()});
    params.push({name: $('#elm_zipcode').prop('name'), value: $('#elm_zipcode').val()});

    url = fn_url('checkout.shipping_estimation');

    for (i in params) {
        url += '&' + params[i]['name'] + '=' + escape(params[i]['value']);
    }

    $.ceAjax('request', url, {
        result_ids: 'shipping_estimation',
        method: 'post'
    });
}
//]]>
</script>
{/literal}

{if $location == "sidebox"}
    {assign var="prefix" value="sidebox_"}
{/if}
{if $location == "popup"}
    {assign var="buttons_class" value="hidden"}
{else}
    {assign var="buttons_class" value="buttons-container"}
{/if}
{if $additional_id}
    {assign var="class_suffix" value="-`$additional_id`"}
    {assign var="id_suffix" value="_`$additional_id`"}
{/if}

{if !$cart.user_data && $smarty.session.cart.user_data}
    {assign var="cart" value=$smarty.session.cart}
{/if}

{if $location != "sidebox" && $location != "popup"}

<div id="est_box{$id_suffix}" align="right">
    <div class="ty-estimation-box" align="left">
    <h2>{__("calculate_shipping_cost")}</h2>
    {/if}
        <div id="shipping_estimation{if $location == "sidebox"}_sidebox{/if}{$id_suffix}">

        {if !$smarty.capture.states_built}
        {include file="views/profiles/components/profiles_scripts.tpl" states=1|fn_get_all_states}
        {capture name="states_built"}Y{/capture}
        {/if}

        <form class="cm-ajax" name="{$prefix}estimation_form{$id_suffix}" action="{""|fn_url}" method="post">
        {if $location == "sidebox"}<input type="hidden" name="location" value="sidebox" />{/if}
        {if $additional_id}<input type="hidden" name="additional_id" value="{$additional_id}" />{/if}
            <input type="hidden" name="result_ids" value="shipping_estimation{if $location == "sidebox"}_sidebox{/if}{$id_suffix},shipping_estimation_buttons" />
            <div class="ty-control-group">
                <label class="ty-control-group__label" for="{$prefix}elm_country{$id_suffix}">{__("country")}</label>
                <select id="{$prefix}elm_country{$id_suffix}" class="cm-country cm-location-estimation{$class_suffix} ty-input-text-medium" name="customer_location[country]">
                <option value="">- {__("select_country")} -</option>
                {assign var="countries" value=1|fn_get_simple_countries}
                {foreach from=$countries item="country" key="code"}
                <option value="{$code}" {if ($cart.user_data.s_country == $code) || (!$cart.user_data.s_country && $code == $settings.General.default_country)}selected="selected"{/if}>{$country}</option>
                {/foreach}
                </select>
            </div>

            {$_state = $cart.user_data.s_state}
            {if $_state|fn_is_empty}
                {$_state = $settings.General.default_state}
            {/if}
            
            <div class="ty-control-group">
                <label class="ty-control-group__label" for="{$prefix}elm_state{$id_suffix}">{__("state")}</label>
                <select class="cm-state cm-location-estimation{$class_suffix} {if !$states[$cart.user_data.s_country]}hidden{/if} ty-input-text-medium" id="{$prefix}elm_state{$id_suffix}" name="customer_location[state]">
                    <option value="">- {__("select_state")} -</option>
                    {foreach $states[$cart.user_data.s_country] as $state}
                        <option value="{$state.code}" {if $state.code == $_state}selected="selected"{/if}>{$state.state}</option>
                        {foreachelse}
                        <option label="" value="">- {__("select_state")} -</option>
                    {/foreach}
                </select>
                <input type="text" class="cm-state cm-location-estimation{$class_suffix} ty-input-text-medium {if $states[$cart.user_data.s_country]}hidden{/if}" id="{$prefix}elm_state{$id_suffix}_d" name="customer_location[state]" size="{if $location != "sidebox"}32{else}20{/if}" maxlength="64" value="{$_state}" {if $states[$cart.user_data.s_country]}disabled="disabled"{/if} />
            </div>

{* rus_city dbazhenov*}
            <div id="change_city">

                {if $cities}
                    <div class="ty-control-group">
                        <label class="ty-control-group__label">{__("city")}</label>
                        <select class="" id="elm_city" name="customer_location[city]">
                            <option label="" value="">-- {__("select_city")} --</option>
                            {foreach from=$cities item="city"}
                                {if !$client_city && ($cart.user_data.s_city == $city.city || $city.active == "Y")}
                                    {assign var="input_city" value=$city.city}
                                {else}
                                    {assign var="input_city" value=$client_city}
                                {/if}
                                <option {if !$client_city && ($cart.user_data.s_city == $city.city || $city.active == "Y")}selected="selected"{/if} value="{$city.city}">{$city.city}</option>
                            {/foreach}
                             <option label="" {if $client_city}selected="selected"{/if} value="client_city">-- {__("other_town")} --</option>
                        </select>
                    </div>

                    <div id="client_city" class="ty-control-group {if !$client_city}hidden{/if}">
                        <label class="ty-control-group__label">{__("other_town")}</label>
                        <input type="text" class="ty-input-text-medium" id="elm_city_text" name="customer_location[city]" value="{$input_city}" />
                    </div>
                {else}
                    <div class="ty-control-group">
                        <label  class="ty-control-group__label">{__("city")}</label>
                        <input type="text" class="ty-input-text-medium" id="elm_city" name="customer_location[city]" value="{$cart.user_data.s_city}" autocomplete="on" />
                    </div>
                {/if}

            <!--change_city--></div>

{* rus_city dbazhenov*}

            <div class="ty-control-group">
                <label class="ty-control-group__label" for="{$prefix}elm_zipcode{$id_suffix}" {if $location == "sidebox"}class="ty-nowrap"{/if}>{__("zip_postal_code")}</label>
                <input type="text" class="ty-input-text-medium" id="{$prefix}elm_zipcode{$id_suffix}" name="customer_location[zipcode]" size="{if $location != "sidebox"}25{else}20{/if}" value="{$cart.user_data.s_zipcode}" autofocus />
            </div>

            <div class="{$buttons_class}">
                {include file="buttons/button.tpl" but_text=__("get_rates") but_name="dispatch[checkout.shipping_estimation]" but_role="text" but_id="but_get_rates"}
            </div>

        </form>

            {if $runtime.mode == "shipping_estimation" || $smarty.request.show_shippings == "Y"}
                <div class="clearfix"></div>
                {if !$cart.shipping_failed && !$cart.company_shipping_failed}
                    {if $location == "popup"}
                        <div class="ty-estimation__title">{__("select_shipping_method")}</div>
                    {/if}
                    <form class="cm-ajax" name="{$prefix}select_shipping_form{$id_suffix}" action="{""|fn_url}" method="post">
                        <input type="hidden" name="redirect_mode" value="cart" />
                        <input type="hidden" name="result_ids" value="checkout_totals" />

                        {hook name="checkout:shipping_estimation"}

                        {foreach from=$product_groups key=group_key item=group name="s"}
                            <p>
                                <strong>{__("vendor")}:&nbsp;</strong>{$group.name|default:__("none")}
                            </p>
                            {if !"ULTIMATE"|fn_allowed_for || $product_groups|count > 1}
                                <ul class="bullets-list">
                                    {foreach from=$group.products item="product"}
                                        <li>
                                            {if $product.product}
                                                {$product.product nofilter}
                                            {else}
                                                {$product.product_id|fn_get_product_name}
                                            {/if}
                                        </li>
                                    {/foreach}
                                </ul>
                            {/if}

                            {if $group.shippings && !$group.all_edp_free_shipping && !$group.all_free_shipping && !$group.free_shipping && !$group.shipping_no_required}
                                {foreach from=$group.shippings item="shipping"}

                                    {if $cart.chosen_shipping.$group_key == $shipping.shipping_id}
                                        {assign var="checked" value="checked=\"checked\""}
                                    {else}
                                        {assign var="checked" value=""}
                                    {/if}

                                    {if $shipping.delivery_time}
                                        {assign var="delivery_time" value="(`$shipping.delivery_time`)"}
                                    {else}
                                        {assign var="delivery_time" value=""}
                                    {/if}
                                    
                                    {hook name="checkout:shipping_estimation_method"}
                                    {if $shipping.rate}
                                        {capture assign="rate"}{include file="common/price.tpl" value=$shipping.rate}{/capture}
                                        {if $shipping.inc_tax}
                                            {assign var="rate" value="`$rate` ("}
                                            {if $shipping.taxed_price && $shipping.taxed_price != $shipping.rate}
                                                {capture assign="tax"}{include file="common/price.tpl" value=$shipping.taxed_price class="ty-nowrap"}{/capture}
                                                {assign var="rate" value="`$rate` (`$tax` "}
                                            {/if}
                                            {assign var="inc_tax_lang" value=__('inc_tax')}
                                            {assign var="rate" value="`$rate``$inc_tax_lang`)"}
                                        {/if}
                                    {else}
                                        {assign var="rate" value=__("free_shipping")}
                                    {/if}

                                    <p>
                                        <input type="radio" class="ty-valign" id="sh_{$group_key}_{$shipping.shipping_id}" name="shipping_ids[{$group_key}]" value="{$shipping.shipping_id}" onclick="fn_calculate_total_shipping();" {$checked} /><label for="sh_{$group_key}_{$shipping.shipping_id}" class="ty-valign">{$shipping.shipping} {$delivery_time} - {$rate nofilter}</label>
                                    </p>
                                    {/hook}
                                {/foreach}

                            {else}
                                {if $group.all_edp_free_shipping || $group.shipping_no_required}
                                    <p>{__("no_shipping_required")}</p>
                                {elseif $group.all_free_shipping || $group.free_shipping}
                                    <p>{__("free_shipping")}</p>
                                {else}
                                    <p>{__("text_no_shipping_methods")}</p>
                                {/if}
                            {/if}

                        {/foreach}

                            <p><strong>{__("total")}:</strong>&nbsp;{include file="common/price.tpl" value=$cart.display_shipping_cost class="ty-price"}</p>

                        {/hook}

                        <div class="{$buttons_class}">
                            {include file="buttons/button.tpl" but_text=__("select") but_role="text" but_name="dispatch[checkout.update_shipping]" but_id="but_select_shipping" but_meta="cm-dialog-closer"}
                        </div>

                    </form>
                {else}
                    <p class="ty-error-text">
                        {__("text_no_shipping_methods")}
                    </p>
                {/if}

            {/if}
            <!--shipping_estimation{if $location == "sidebox"}_sidebox{/if}{$id_suffix}--></div>

        {if $location != "sidebox" && $location != "popup"}
    </div>
</div>
{/if}

{if $location == "popup"}
<div class="ty-estimation-buttons buttons-container" id="shipping_estimation_buttons">
    {if $runtime.mode == "shipping_estimation" || $smarty.request.show_shippings == "Y"}
        {include file="buttons/button.tpl" but_text=__("recalculate_rates") but_external_click_id="but_get_rates" but_role="text" but_meta="cm-external-click ty-float-right ty-estimation-buttons__rate"}

        {include file="buttons/button.tpl" but_text=__("select_shipping_method") but_external_click_id="but_select_shipping" but_meta="cm-external-click cm-dialog-closer"}
    {else}
        {include file="buttons/button.tpl" but_text=__("get_rates") but_external_click_id="but_get_rates" but_meta="cm-external-click"}
    {/if}
    <!--shipping_estimation_buttons--></div>
{/if}

{* rus_cities dbazhenov*}

       <script type="text/javascript"  class="cm-ajax-force">
        //<![CDATA[

        (function(_, $) {

            function fn_get_cities(change) 
            {

                var check_country = $("#{$prefix}elm_country{$id_suffix}").val();
                var check_state = $("#{$prefix}elm_state{$id_suffix}").val();

                var url = fn_url('spsr_city.shipping_estimation_city');

                url += '&check_country=' + check_country + '&check_state=' +  check_state ;

                var check_city = $("#elm_city").val();
                url += '&check_city=' + check_city;

                var city_text = $("#elm_city_text").val();
                url += '&city_text=' + city_text;
                
                $.ceAjax('request', url, {
                    result_ids: 'change_city',
                    method: 'post'
                });
            }

            $(document).ready(function() {
                fn_get_cities(false);

                $(_.doc).on('change', '#{$prefix}elm_country{$id_suffix}', function() {
                        $('#elm_city').val('');
                        $('#elm_city_text').val('');
                        fn_get_cities(true);
                });

                $(_.doc).on('change', '#{$prefix}elm_state{$id_suffix}', function() {
                        $('#elm_city').val('');
                        $('#elm_city_text').val('');
                        fn_get_cities(true);
                });

                $(_.doc).on('change', '#elm_city', function() {
                    var inp = $('#elm_city').val();
                    if (inp == 'client_city') {
                        $('#client_city').removeClass('hidden');
                        $('#elm_city_text').val('');
                    } else {
                        $('#elm_city_text').val(inp);             
                        $('#client_city').addClass('hidden');
                    }
                });

            });

        }(Tygh, Tygh.$));
        //]]>
        </script>

{* rus_cities dbazhenov*}