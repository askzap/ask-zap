{if $order_info.payment_method.processor_params}
    {if $order_info.payment_method.processor_params.sbrf_enabled}
        {assign var="sbrf_settings" value=$order_info.payment_method.processor_params}
        {if $sbrf_settings.sbrf_enabled=="Y"}
            <div id="content_payment_information" class="{if $selected_section != "payment_information"}hidden{/if}">
                    <div class="sbrf">
                        <table class="ty-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ty-left">{__("sbrf_recepient")}</td>
                                        <td class="ty-left">{$sbrf_settings.sbrf_recepient_name|unescape}</td>
                                    </tr>
                                    <tr>
                                        <td class="ty-left">{__("sbrf_inn")}</td>
                                        <td class="ty-left">{$sbrf_settings.sbrf_inn|unescape}</td>
                                    </tr>
                                    <tr>
                                        <td class="ty-left">{__("sbrf_kpp")}</td>
                                        <td class="ty-left">{$sbrf_settings.sbrf_kpp|unescape}</td>
                                    </tr>
                                    <tr>
                                        <td class="ty-left">{__("sbrf_okato_code")}</td>
                                        <td class="ty-left">{$sbrf_settings.sbrf_okato_code|unescape}</td>
                                    </tr>
                                    <tr>
                                        <td class="ty-left">{__("sbrf_settlement_account")}</td>
                                        <td class="ty-left">{$sbrf_settings.sbrf_settlement_account|unescape}</td>
                                    </tr>
                                    <tr>
                                        <td class="ty-left">{__("sbrf_bank")}</td>
                                        <td class="ty-left">{$sbrf_settings.sbrf_bank|unescape}</td>
                                    </tr>
                                    <tr>
                                        <td class="ty-left">{__("sbrf_bik")}</td>
                                        <td class="ty-left">{$sbrf_settings.sbrf_bik|unescape}</td>
                                    </tr>
                                    <tr>
                                        <td class="ty-left">{__("sbrf_cor_account")}</td>
                                        <td class="ty-left">{$sbrf_settings.sbrf_cor_account|unescape}</td>
                                    </tr>
                                    <tr>
                                        <td class="ty-left">{__("sbrf_kbk")}</td>
                                        <td class="ty-left">{$sbrf_settings.sbrf_kbk|unescape}</td>
                                    </tr>
                                    <tr>
                                        <td class="ty-left">{__("sbrf_payment")}</td>
                                        <td class="ty-left">{$sbrf_settings.sbrf_prefix|unescape} №{$order_info.order_id}</td>
                                    </tr>
                                    <tr>
                                        <td class="ty-left"><img src="{$url_qr_code}" alt="QrCode" width="{$sbrf_settings.sbrf_qr_print_size}" height="{$sbrf_settings.sbrf_qr_print_size}" /></td>
                                        <td class="ty-left">{__("sbrf_qr_info")}</td>
                                    </tr>
                                </tbody>
                        </table>
                    </div>
            </div>
        {else}
            <div id="content_payment_information" class="{if $selected_section != "payment_information"}hidden{/if}">
                <p class="ty-no-items">{__("sbrf_information_not_found")}</p>
            </div>
        {/if}
    {/if}

    {if $order_info.payment_method.processor_params.account_enabled}
        {assign var="account_settings" value=$order_info.payment_method.processor_params}
        {assign var="payment_info" value=$order_info.payment_info}
        {if $account_settings.account_enabled == "Y"}
            <div id="content_payment_information" class="{if $selected_section != "payment_information"}hidden{/if}">
                <div class="account">
                    {include file="common/subheader.tpl" title=__("addons.rus_payments.company_info")}
                    <table class="ty-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ty-left">{__("addons.rus_payments.organization_customer")}</td>
                                <td class="ty-left" width="57%">{$account_settings.account_recepient_name|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("address")}</td>
                                <td class="ty-left">{$account_settings.account_address|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("phone")}</td>
                                <td class="ty-left">{$account_settings.account_phone|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("addons.rus_payments.account_kpp")}</td>
                                <td class="ty-left">{$account_settings.account_kpp|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("inn_customer")}</td>
                                <td class="ty-left">{$account_settings.account_inn|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("addons.rus_payments.account_current")}</td>
                                <td class="ty-left">{$account_settings.account_current|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("addons.rus_payments.account_personal")}</td>
                                <td class="ty-left">{$account_settings.account_personal|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("addons.rus_payments.account_bank")}</td>
                                <td class="ty-left">{$account_settings.account_bank|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("addons.rus_payments.account_bik")}</td>
                                <td class="ty-left">{$account_settings.account_bik|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("addons.rus_payments.account_cor")}</td>
                                <td class="ty-left">{$account_settings.account_cor|unescape}</td>
                            </tr>
                        </tbody>
                    </table><br />
                    {if $payment_info}
                    {include file="common/subheader.tpl" title=__("customer_information")}
                    <table class="ty-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ty-left">{__("addons.rus_payments.organization_customer")}</td>
                                <td class="ty-left" width="57%">{$payment_info.organization_customer|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("inn_customer")}</td>
                                <td class="ty-left">{$payment_info.inn_customer|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("address")}</td>
                                <td class="ty-left">{$payment_info.address|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("zip_postal_code")}</td>
                                <td class="ty-left">{$payment_info.zip_postal_code|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("phone")}</td>
                                <td class="ty-left">{$payment_info.phone|unescape}</td>
                            </tr>
                            <tr>
                                <td class="ty-left">{__("addons.rus_payments.bank_details")}</td>
                                <td class="ty-left">{$payment_info.bank_details|unescape}</td>
                            </tr>
                        </tbody>
                    </table>
                    {/if}
                </div>
            </div>
        {else}
            <div id="content_payment_information" class="{if $selected_section != "payment_information"}hidden{/if}">
                <p class="ty-no-items">{__("addons.rus_payments.account_information_not_found")}</p>
            </div>
        {/if}
    {/if}
{/if}
