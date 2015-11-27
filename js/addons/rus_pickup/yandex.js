(function(_, $) {
    (function($) {

        var maps = [];
        var saved_point = null;
        var map_params = [];

        var latitude = 0;
        var longitude = 0;
        var zoom = 0;

        var latitude_name = '';
        var longitude_name = '';
        var map_container = '';

        var start_init_pickupmaps = false;
        var finish_init_pickupmaps = false;

        var methods = {

            init: function(options, callback) {

                var group_key = options.group_key;

                if (! ('pickupmaps' in window)) {

                    if (!start_init_pickupmaps) {
                        start_init_pickupmaps = true;

                        $.getScript('//api-maps.yandex.ru/2.1/?ns=pickupmaps&lang=' + options.language, function () {
                            pickupmaps.ready(function () {
                                finish_init_pickupmaps = true;
                                $.cePickup('init', options, callback);
                            });
                        });
                    } else {
                        setTimeout(function() { $.cePickup('init', options, callback)}, 500);
                    }

                    return false;
                }

                if (!start_init_pickupmaps || (start_init_pickupmaps && !finish_init_pickupmaps)) {
                    setTimeout(function() { $.cePickup('init', options, callback)}, 500);

                    return false;
                }

                latitude = options.latitude;
                longitude = options.longitude;
                map_container = options.map_container;

                zoom = options.zoom;

                // Required fields - zoom, center
                map_params[group_key] = {
                    zoom: 12,
                    type: 'yandex#map',
                    center: [latitude, longitude],
                    controls: ['default']
                };


                if (_.area == 'A') {
                    $.extend(map_params[group_key], {
                        draggableCursor: 'crosshair',
                        draggingCursor: 'pointer'
                    });
                } else {
                    $.extend(map_params[group_key], {
                        zoom: zoom
                    });
                }

                if (typeof(callback) == 'function') {
                    callback();
                }
            },

            destroyMaps: function(group_key)
            {
                maps.forEach(function(element, index) {
                    maps[index].destroy();
                });
            },

            show: function(options)
            {
                if (typeof options == "undefined") {
                    return false;
                }

                var group_key = options.group_key;

                if (!map_params[group_key]) {
                    return $.cePickup('init', options, function() {

                        $.cePickup('show', options);
                    });
                }

                if (maps[group_key]) {
                    maps[group_key].destroy();
                } 
                    
                maps[group_key] = new pickupmaps.Map(document.getElementById(options.map_container), map_params[group_key]);

                maps[group_key].controls.remove('searchControl');
                maps[group_key].behaviors.disable(['scrollZoom']);
                
                var marker;
                storeData = options.storeData;

                for (var keyvar = 0; keyvar < storeData.length; keyvar++) {

                    var marker_html = '<div style="padding-right: 10px"><strong>' + storeData[keyvar]['name'];

                    marker_html += '</strong><p>';

                    if (storeData[keyvar]['city'] != '') {
                        marker_html += storeData[keyvar]['city'] + ', ';
                    }

                    if (typeof(storeData[keyvar]['pickup_address']) !== 'undefined') {
                        marker_html += storeData[keyvar]['pickup_address'];
                    }

                    if (typeof(storeData[keyvar]['pickup_phone']) !== 'undefined') {
                        marker_html += '<br/>' + storeData[keyvar]['pickup_phone'];
                    }

                    if (typeof(storeData[keyvar]['pickup_time']) !== 'undefined') {
                        marker_html += '<br/>' + storeData[keyvar]['pickup_time'];
                    }

                    if (typeof(storeData[keyvar]['description']) !== 'undefined') {
                        marker_html += '<br/>' + storeData[keyvar]['description'];
                    }

                    if (options['selectStore'] === true) {
                        marker_html += '<p><a data-ca-shipping-id="' + storeData[keyvar]['shipping_id'] + '" data-ca-group-key="' + storeData[keyvar]['group_key'] + '" data-ca-location-id="' + storeData[keyvar]['store_location_id'] + '" class="cm-map-select-location ty-btn ty-btn__tertiary text-button">Выбрать</a></p>';            
                    }

                    marker_html += '</p></div>';

                    marker = new pickupmaps.Placemark([ storeData[keyvar]['latitude'], storeData[keyvar]['longitude'] ], {
                        balloonContentBody: marker_html
                    });

                    maps[group_key].geoObjects.add(marker);

                }

                if (storeData.length == 1) {

                    maps[group_key].setCenter(marker.geometry.getCoordinates());
                    maps[group_key].setZoom(zoom);

                } else {

                    pickupmaps.geoQuery(maps[group_key].geoObjects).applyBoundsToMap(maps[group_key]);

                    var select = $('.ty-one-store__radio-' + group_key + ':checked').attr('value');

                    if (!select) {
                        var select = $('.one-store__radio:checked').attr('value');
                    }

                    if (select) {
                        $.each(storeData, function( key, value ) {
                            if (value['store_location_id'] == select) {
                                maps[group_key].setCenter([value['latitude'],value['longitude']]);
                                maps[group_key].setZoom(zoom);
                                return false;
                            }
                        });
                    }
                }

            },

            saveLocation: function()
            {
                if (saved_point) {
                    $('#' + latitude_name).val(saved_point[0]);
                    $('#' + latitude_name + '_hidden').val(saved_point[0]);
                    $('#' + longitude_name).val(saved_point[1]);
                    $('#' + longitude_name + '_hidden').val(saved_point[1]);
                }

                saved_point = null;
            },

            selectLocation: function(location, group_key, shipping_id)
            {
	            if (maps[group_key]) {
                    maps[group_key].destroy();
                }
                
                $('#store_' + group_key + '_' + shipping_id + '_' + location).prop("checked", true);
                fn_calculate_total_shipping_cost();
            },

            viewLocation: function(latitude, longitude, group_key)
            {
                maps[group_key].setCenter([latitude, longitude]);
                maps[group_key].setZoom(zoom);
            },

            viewLocations: function(group_key)
            {
                pickupmaps.geoQuery(maps[group_key].geoObjects).applyBoundsToMap(maps[group_key]);
            }
        };

        $.extend({
            cePickup: function(method) {
                if (methods[method]) {
                    return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
                } else {
                    $.error('ty.map: method ' +  method + ' does not exist');
                }
            }
        });
    })($);

    $(document).ready(function() {

        $(document).on('click', '.cm-map-select-store', function(e) {

            $.cePickup('destroyMaps');

            fn_calculate_total_shipping_cost();
        });

        $(document).on('click', '.cm-map-save-location', function () {
            $.cePickup('saveLocation');
        });

        $(document).on('click', '.cm-map-select-location', function () {
            var jelm = $(this);
            var location = jelm.data('ca-location-id');
            var group_key = jelm.data('ca-group-key');
            var shipping_id = jelm.data('ca-shipping-id');

            $.cePickup('selectLocation', location, group_key, shipping_id);
        });

        $(document).on('click', '.cm-map-view-location', function () {
            var jelm = $(this);
            var latitude = jelm.data('ca-latitude');
            var longitude = jelm.data('ca-longitude');
            var group_key = jelm.data('ca-group-key');

            $.cePickup('viewLocation', latitude, longitude, group_key);

            if ($(this).data('ca-scroll')) {
                var id = $(this).data('ca-scroll');
                $.scrollToElm($('#' + id));
            }
        });

        $(document).on('click', '.cm-map-view-locations', function () {
            var jelm = $(this);
            var group_key = jelm.parent().data('ca-group-key');

            $.cePickup('viewLocations', group_key);

            if ($(this).data('ca-scroll')) {
                var id = $(this).data('ca-scroll');
                $.scrollToElm($('#' + id));
            }
        });

    });
}(Tygh, Tygh.$));
