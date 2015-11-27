<div class="sidebar-row">
    <h6>{__("addons.address_on_map.shipping_address_on_map")}</h6>
    {if $user_data.s_country_descr || $user_data.s_city || $user_data.s_address}
        <div class="cm-map" style="width: 220px; height: 220px" data-ca-geocode="{$user_data.s_country_descr}, {$user_data.s_city}, {$user_data.s_address}"></div>
    {else}
        {__('no_data')}
    {/if}
</div>
