(function(_, $) {

    $.ceEvent('on', 'ce.commoninit', function(context) {
        var maps = context.find('.cm-map');
        var geolocations = $('.cm-geolocation-address', context).add(
            '.cm-geolocation-city', context).add(
            '.cm-geolocation-state', context).add(
            '.cm-geolocation-country', context);

        if (maps.length || geolocations.length) {

            $.ceMap('init');

            if (maps.length) {
                maps.each(function() {
                    $(this).ceMap('showObjects', $(this).data('caGeocode'), 1);
                });
            }

            if (geolocations.length) {
                $.ceMap('getUserLocation', function(loc) {
                    $.each(geolocations, function(i, elm) {
                        var $elm = $(elm);
                        if ($elm.hasClass('cm-geolocation-address')) {
                            $elm.val(loc.address);
                        } else if ($elm.hasClass('cm-geolocation-city')) {
                            $elm.val(loc.city);
                        } else if ($elm.hasClass('cm-geolocation-state')) {
                            setTimeout(function() {
                                if (loc.state_code) {
                                    $elm.val(loc.state_code);
                                } else {
                                    var el = $('option', $elm).filter(':contains(' + loc.state + ')').eq(0);;
                                    if (el.length) {
                                        $elm.val(el.val());
                                    }
                                }
                                $elm.trigger('change');
                            });
                        } else if ($elm.hasClass('cm-geolocation-country')) {
                            $elm.val(loc.country_code);
                            $elm.trigger('change');
                        }
                    });
                });
            }
        }
    });
}(Tygh, jQuery));

