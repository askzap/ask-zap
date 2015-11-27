(function(_, $) {

    /**
    * Map
    */
    (function($) {

        var handlers = {};
        var is_ready = false;
        var pool = [];

        var methods = {
            showObjects: function(request, limit) {
                handlers.showObjects.apply(this, arguments);
            },
        };

        var static_methods = {
            init: function(settings) {
                var settings = settings || {};
                handlers.init.call(this, settings, function() {
                    is_ready = true;
                    $.each(pool, function(i, cb) {
                        cb();
                    });
                    pool = [];
                });
            },
            getUserLocation: function(callback) {
                handlers.getUserLocation.call(this, function(loc) {
                    if (loc) {
                        callback({
                            country: loc.country,
                            country_code: loc.country_code,
                            state: loc.state,
                            state_code: loc.state_code,
                            city: loc.city,
                            address: loc.address,
                        });
                    }
                });
            }
        };

        $.fn.ceMap = function(method) {
            if (methods[method]) {
                var self = this, args = Array.prototype.slice.call(arguments, 1);
                var callback = function() {
                    methods[method].apply(self, args);
                }
                if (is_ready) {
                    return callback();
                } else {
                    pool.push(callback);
                }
            } else {
                $.error('ce.map: method ' +  method + ' does not exist');
            }
        };

        $.ceMap = function(action) {
            if (action == 'handlers') {
                handlers = arguments[1];
            } else if (static_methods[action]) {
                var args = Array.prototype.slice.call(arguments, 1), self = this;
                var callback = function() {
                    static_methods[action].apply(self, args);
                };
                if (is_ready || action == 'init') {
                    return callback();
                } else {
                    pool.push(callback);
                }
            } else {
                $.error('ce.map: method ' +  method + ' does not exist');
            }
        }

    })($);

}(Tygh, jQuery));
