{script src="js/addons/rus_pickup/yandex.js"}

<script type="text/javascript" class="cm-ajax-force">
    (function(_, $) {
        var options = {
            'latitude': {$smarty.const.STORE_LOCATOR_DEFAULT_LATITUDE|doubleval},
            'longitude': {$smarty.const.STORE_LOCATOR_DEFAULT_LONGITUDE|doubleval},
            'map_container': '{$map_container}',
            'zoom': {if !empty($sl_settings.yandex_zoom)} {$sl_settings.yandex_zoom} {else} 16 {/if},
            'controls': [ 
                'zoomControl',
                'typeSelector',
                'rulerControl',
            ],
            'language': '{$smarty.const.CART_LANGUAGE}',
            'storeData': [
            {foreach from=$store_locations item="loc" name="st_loc_foreach" key="key"}
            {
                'store_location_id' : '{$loc.store_location_id}',
                'group_key' : '{$group_key}',
                'shipping_id' : '{$shipping.shipping_id}',
                'country' :  '{$loc.country|escape:javascript nofilter}',
                'latitude' : {$loc.latitude|doubleval},
                'longitude' : {$loc.longitude|doubleval},
                'name' :  '{$loc.name|escape:javascript nofilter}',
                'description' : '{$loc.description|escape:javascript nofilter}',
                'city' : '{$loc.city|escape:javascript nofilter}',
                'country_title' : '{$loc.country_title|escape:javascript nofilter}',
                'pickup_surcharge' : {$loc.pickup_surcharge|doubleval},
                'currency' : '{$currencies.$secondary_currency.symbol  nofilter}',
                'pickup_address' : '{$loc.pickup_address|escape:javascript nofilter}',
                'pickup_phone' : '{$loc.pickup_phone|escape:javascript nofilter}',
                'pickup_time' : '{$loc.pickup_time|escape:javascript nofilter}'
            }
            {if !$smarty.foreach.st_loc_foreach.last},{/if}
            {/foreach}            
            ]
        };

        $.ceEvent('on', 'ce.commoninit', function(context) {
            if (context.find('#' + options.map_container).length) {
               $.cePickup('show', options);
            }
        });

    }(Tygh, Tygh.$));
</script>

