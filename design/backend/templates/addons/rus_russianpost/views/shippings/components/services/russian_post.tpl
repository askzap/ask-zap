<fieldset>
    {if $code == 'ems'}

        <div class="control-group">
            <label class="control-label" for="ship_ems_mode">{__("ems_mode")}</label>
            <div class="controls">
                <select id="ship_ems_mode" name="shipping_data[service_params][mode]">
                    <option value="regions" {if $shipping.service_params.mode == "regions"}selected="selected"{/if}>{__("ems_region")}</option>
                    <option value="cities" {if $shipping.service_params.mode == "cities"}selected="selected"{/if}>{__("ems_city")}</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="ship_ems_delivery_time_plus">{__("ems_delivery_time_plus")}</label>
            <div class="controls">
                <input id="ship_ems_delivery_time_plus" type="text" name="shipping_data[service_params][delivery_time_plus]" size="30" value="{$shipping.service_params.delivery_time_plus}" />
            </div>
        </div>

    {elseif $code == 'russian_post'}

        <div class="control-group">
            <label for="ship_russian_post_shipping_type" class="control-label">{__("russian_post_shipping_type")}:</label>
            <div class="controls">
                <select id="ship_russian_post_shipping_type" name="shipping_data[service_params][shipping_type]">
                    <option value="ground" {if $shipping.service_params.shipping_type == "ground"}selected="selected"{/if}>{__("ground")}</option>
                    <option value="air" {if $shipping.service_params.shipping_type == "air"}selected="selected"{/if}>{__("air")}</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="ship_russian_post_package_type" class="control-label">{__("russian_post_package_type")}:</label>
            <div class="controls">
                <select id="ship_russian_post_package_type" name="shipping_data[service_params][package_type]">
                    <option value="zak_band" {if $shipping.service_params.package_type == "zak_band"}selected="selected"{/if}>{__("zak_band")}</option>
                    <option value="zak_kart" {if $shipping.service_params.package_type == "zak_kart"}selected="selected"{/if}>{__("shipping.russianpost.zak_kart")}</option>
                    <option value="zak_pis" {if $shipping.service_params.package_type == "zak_pis"}selected="selected"{/if}>{__("zak_pis")}</option>
                    <option value="ob_pos" {if $shipping.service_params.package_type == "ob_pos"}selected="selected"{/if}>{__("shipping.russianpost.ob_pos")}</option>
                    <option value="cen_band" {if $shipping.service_params.package_type == "cen_band"}selected="selected"{/if}>{__("cen_band")}</option>
                    <option value="cen_pos" {if $shipping.service_params.package_type == "cen_pos"}selected="selected"{/if}>{__("cen_pos")}</option>
                    <option value="cen_pis" {if $shipping.service_params.package_type == "cen_pis"}selected="selected"{/if}>{__("cen_pis")}</option>
                </select>
            </div>
        </div>

    {elseif $code == 'russian_post_calc'}

        <div class="control-group">
            <label class="control-label" for="user_key">{__("authentication_key")}</label>
            <div class="controls">
                <input id="user_key" type="text" name="shipping_data[service_params][user_key]" size="30" value="{$shipping.service_params.user_key}"/>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="user_key_password">{__("authentication_password")}</label>
            <div class="controls">
                <input id="user_key_password" type="password" name="shipping_data[service_params][user_key_password]" size="30" value="{$shipping.service_params.user_key_password}" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="package_type">{__("russianpost_shipping_type")}</label>
            <div class="controls">
                <select id="package_type" name="shipping_data[service_params][shipping_type]">
                    <option value="rp_main" {if $shipping.service_params.shipping_type == "rp_main"}selected="selected"{/if}>{__("ship_russianpost_shipping_type_rp_main")}</option>
                    <option value="rp_1class" {if $shipping.service_params.shipping_type == "rp_1class"}selected="selected"{/if}>{__("ship_russianpost_shipping_type_rp_1class")}</option>
                </select>
            </div>
        </div>

        <span>{__("ship_russianpost_register_text")}</span>
    {/if}

</fieldset>

{if $code == 'russian_post'}
<script type="text/javascript">
//<![CDATA[
var elm = Tygh.$('#ship_russian_post_shipping_type');
fn_disable_rupost_package_type(elm);
elm.on('change', function(e) {$ldelim}
    fn_disable_rupost_package_type(Tygh.$(this));
{$rdelim});
function fn_disable_rupost_package_type(elm) {$ldelim}
    if (elm.val() == 'air') {$ldelim}
        Tygh.$('#ship_russian_post_package_type').find('[value="cen_band"],[value="cen_pos"]').attr('disabled', 'disabled');
    {$rdelim} else {$ldelim}
        Tygh.$('#ship_russian_post_package_type').find('[value="cen_band"],[value="cen_pos"]').removeAttr('disabled');
    {$rdelim}
{$rdelim}
//]]>
</script>
{/if}
