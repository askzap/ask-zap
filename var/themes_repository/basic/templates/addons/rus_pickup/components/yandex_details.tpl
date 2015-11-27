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
                {
                    'store_location_id' : '{$store_locations.store_location_id}',
                    'group_key' : '{$group_key}',
                    'shipping_id' : '{$shipping.shipping_id}',
                    'country' :  '{$store_locations.country|escape:javascript nofilter}',
                    'latitude' : {$store_locations.latitude|doubleval},
                    'longitude' : {$store_locations.longitude|doubleval},
                    'name' :  '{$store_locations.name|escape:javascript nofilter}',
                    'description' : '{$store_locations.description|escape:javascript nofilter}',
                    'city' : '{$store_locations.city|escape:javascript nofilter}',
                    'country_title' : '{$store_locations.country_title|escape:javascript nofilter}',
                    'pickup_address' : '{$store_locations.pickup_address|escape:javascript nofilter}',
                    'pickup_phone' : '{$store_locations.pickup_phone|escape:javascript nofilter}',
                    'pickup_time' : '{$store_locations.pickup_time|escape:javascript nofilter}'
                }
                {if !$smarty.foreach.st_loc_foreach.last},{/if}
            ]
        };

        $.ceEvent('on', 'ce.commoninit', function(context) {
            if (context.find('#' + options.map_container).length) {
               $.cePickup('show', options);
            }
        });

    }(Tygh, Tygh.$));
</script>

