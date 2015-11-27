(function(_, $) {

    $.ceMap('handlers', {
        showObjects: function(request, limit) {
            var $self = this;

            csymaps.geocode(request, { results: limit || 10 }).then(function(res) {
                var coords = res.geoObjects.get(0).geometry.getCoordinates();
                map = new csymaps.Map($self.get(0), {
                    center: coords,
                    zoom: 13,
                });
                map.behaviors.disable(['scrollZoom']);
                map.geoObjects.add(res.geoObjects);
            });
        },

        init: function(settings, callback) {
            var default_settings = {
                key: _.maps_provider.yandex_key,
                lang: _.cart_language,
            };

            settings = $.extend(default_settings, settings);

            var url = '//api-maps.yandex.ru/2.1/?ns=csymaps&lang=' + settings.lang;

            if (settings.key) {
                url += '&key=' + settings.key;
            }

            $.getScript(url, function() {
                csymaps.ready(function() {
                    callback();
                });
            });
        },

        getUserLocation: function(callback) {
            csymaps.geolocation.get({provider: 'yandex'}).then(function(res) {
                var geocoder_meta_data = res.geoObjects.get(0).properties.get('metaDataProperty.GeocoderMetaData'), 
                    kind = geocoder_meta_data.kind, prop, nested = false ;

                var location = {
                    counry: '',
                    country_code: '',
                    state: '',
                    state_code: '',
                    city: '',
                    address: '',
                };

                do {
                    geocoder_meta_data = nested || geocoder_meta_data;
                    nested = false;
                    for (prop in geocoder_meta_data) {
                        if (geocoder_meta_data.hasOwnProperty(prop)) {
                            if (typeof(geocoder_meta_data[prop]) === 'object') {
                                nested = geocoder_meta_data[prop];
                            } else if (prop == 'CountryName') {
                                location.country = geocoder_meta_data[prop];
                            } else if (prop == 'CountryNameCode') {
                                location.country_code = geocoder_meta_data[prop];
                            } else if (prop == 'AdministrativeAreaName') {
                                location.state = geocoder_meta_data[prop];
                            } else if (prop == 'AdministrativeAreaNameCode') {
                                location.state_code = geocoder_meta_data[prop];
                            } else if (prop == 'LocalityName') {
                                location.city = geocoder_meta_data[prop];
                            } else if (prop == 'DependentLocalityName' || prop == 'ThoroughfareName' || prop == 'PremiseNumber') {
                                location.address += geocoder_meta_data[prop] + ' ';
                            }

                        }
                    }
                } while (nested);

                callback(location);

            }, function() {
                console.log('error');
            });
        },

    });

}(Tygh, Tygh.$));
