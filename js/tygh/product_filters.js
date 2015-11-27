(function(_, $) {
    'use strict';

    var base_url;
    var ajax_ids;

    (function($){

        function generateHash(container)
        {
            var features = {};
            var hash = [];

            container.find('input.cm-product-filters-checkbox:checked').each(function() {
                var elm = $(this);
                if (!features[elm.data('caFilterId')]) {
                    features[elm.data('caFilterId')] = [];
                }
                features[elm.data('caFilterId')].push(elm.val());
            });

            for (var k in features) {
                hash.push(k + '-' + features[k].join('-'));
            }

            return hash.join('.');
        }

        function getProducts(url, obj)
        {
            if (ajax_ids) {
                $.ceAjax('request', url, {
                    result_ids: ajax_ids,
                    full_render: true,
                    save_history: true,
                    caching: true,
                    scroll: '.ty-mainbox-title',
                    obj: obj
                });
            } else {
                $.redirect(url);
            }

            return false;
        }

        function setHandler()
        {
            $(_.doc).on('change', '.cm-product-filters-checkbox', function() {
                var self = $(this);
                var container = self.parents('.cm-product-filters');

                return getProducts($.attachToUrl(base_url, 'features_hash=' + generateHash(container)), self);
            });
        }

        function setCallback()
        {
            // re-init filters
            $.ceEvent('on', 'ce.commoninit', function(context) {

                context.find('.cm-product-filters').each(function() {
                    var self = $(this);
                    if (self.data('caBaseUrl')) {
                        base_url = self.data('caBaseUrl');
                        ajax_ids = self.data('caTargetId');
                    }
                });

                initSlider(context);
            });

            $.ceEvent('on', 'ce.filterdate', function(elm, time_from, time_to) {
                $('#elm_checkbox_' + elm.prop('id')).val(time_from + '-' + time_to).prop('checked', true).trigger('change');
            });
        }

        function initSlider(parent)
        {
            parent.find('.cm-range-slider').each(function() {
                var elm = $(this);
                var id = elm.prop('id');
                var json_data = $('#' + id + '_json').val();
                if (elm.data('uiSlider') || !json_data) {
                    return false;
                }
                var data = $.parseJSON(json_data) || null;
                if (!data) {
                    return false;
                }

                elm.slider({
                    disabled: data.disabled,
                    range: true,
                    min: data.min,
                    max: data.max,
                    step: data.step,
                    values: [data.left, data.right],
                    slide: function(event, ui) {
                        $('#' + id + '_left').val(ui.values[0]);
                        $('#' + id + '_right').val(ui.values[1]);
                    },
                    change: function(event, ui){
                        var replacement = ui.values[0] + '-' + ui.values[1];
                        if (data.extra) {
                            replacement = replacement + '-' + data.extra;
                        }
                        $('#elm_checkbox_' + id).val(replacement).prop('checked', true).trigger('change');
                    }
                });

                if (data.left != data.min || data.right != data.max) {
                    var replacement = data.left + '-' + data.right;
                    if (data.extra) {
                        replacement = replacement + '-' + data.extra;
                    }

                    $('#elm_checkbox_' + id).val(replacement).prop('checked', true);
                }

                $('#' + id + '_left').off('change').on('change', function() {
                    var v1 = parseInt($('#' + id + '_left').val(), 10);
                    var v2 = parseInt($('#' + id + '_right').val(), 10);
                    $('#' + id).slider('values', [(isNaN(v1) ? 0 : v1), (isNaN(v2) ? 0 : v2)]);
                });
                $('#' + id + '_right').off('change').on('change', function() {
                    var v1 = parseInt($('#' + id + '_left').val(), 10);
                    var v2 = parseInt($('#' + id + '_right').val(), 10);
                    $('#' + id).slider('values', [(isNaN(v1) ? 0 : v1), (isNaN(v2) ? 0 : v2)]);
                });

                if (elm.parents('.filter-wrap').hasClass('open')) {
                    elm.parent('.price-slider').show();
                }
            });
        }

        setCallback();
        setHandler();
    })($);

}(Tygh, Tygh.$));
