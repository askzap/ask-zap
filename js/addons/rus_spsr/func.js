(function(_, $) {
    $(document).ready(function() {
        $('#spsr_get_city_link').on('click', fn_get_spsr_city);
    });

    function fn_get_spsr_city() {
        var city = $('#spsr_from_city_name').val();
        var mode = $('#spsr_mode').val();

        $.ceAjax('request', fn_url("shippings.spsr_get_city_data"), {
            data: {
                var_mode: mode,
                var_city: city,
                loc: 'shipping_settings',
                result_ids: 'spsr_city_div',
            },
        });
    }
}(Tygh, Tygh.$));
