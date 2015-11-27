{assign var="map_provider" value=$addons.store_locator.map_provider}
{assign var="map_provider_api" value="`$map_provider`_map_api"}
{assign var="map_container" value="map_canvas"}

{if $map_provider == 'yandex'}
    {if $store_locations}
        {include file="addons/rus_pickup/views/pickup/components/maps/yandex.tpl"}

        <div class="ty-pickup-location">
            <div class="ty-pickup-location__map-wrapper" id="{$map_container}"></div>
            <div class="ty-wysiwyg-content ty-pickup-location__locations-wrapper" id="stores_list_box">
                {if $store_locations|count > 1}
                    <div class="ty-pickup-location__item-all_stores">
                        <div class="ty-pickup-location__item-view">{include file="buttons/button.tpl" but_role="text" but_meta="cm-map-view-locations ty-btn__tertiary" but_text=__("view_all")}</div>
                    </div>
                    <hr />
                {/if}

                {foreach from=$store_locations item=stores key=city_name}
                    <h2 class="ty-pickup-location__city-title">{$city_name}</h2>
                    <div class="ty-pickup-location_city-items">
                    {foreach from=$stores item=loc key=num}
                        <div class="ty-pickup-location__item ty-column3" id="loc_{$loc.store_location_id}">
                            <h3 class="ty-pickup-location__item-title">{$loc.name}</h3>
                            
                            <div class="ty-pickup-location__item-desc">{$loc.description nofilter}</div>

                            {if $loc.city || $loc.country_title}
                                <span class="ty-pickup-location__item-country">{if $loc.city}{$loc.city}, {/if}{$loc.country_title}</span>
                            {/if}
                            
                            <div class="ty-pickup-location__item-view">
                                {include file="buttons/button.tpl" but_role="text" but_meta="cm-map-view-location ty-btn__tertiary" but_text=__("view_on_map") but_extra="data-ca-latitude={$loc.latitude} data-ca-longitude={$loc.longitude} data-ca-scroll={$map_container}"}
                            </div>
                        </div>
                    {/foreach}
                    </div>
                    <hr />
                {/foreach}

                {if $store_locations|count > 1}
                    <div class="ty-pickup-location__item-all_stores">
                        <div class="ty-pickup-location__item-view">{include file="buttons/button.tpl" but_role="text" but_meta="cm-map-view-locations ty-btn__tertiary" but_text=__("view_all") but_extra="data-ca-scroll={$map_container}"}</div>
                    </div>
                    <hr />
                {/if}

            </div>
        </div>
    {else}
        <p class="ty-no-items">{__("no_data")}</p>
    {/if}
{/if}

{capture name="mainbox_title"}{__("rus_pickup.pick_up_points")}{/capture}