(function(_, $) {
    $("#city").autocomplete({
        source: function( request, response ) {
            var check_country;
            check_country = "RU";
            getSdekCities(check_country, request, response);
        }
    });

    function getSdekCities(check_country, request, response) {

        $.ceAjax('request', fn_url('city_sdek.autocomplete_city?q=' + encodeURIComponent(request.term) + '&check_country=' + check_country), {
            callback: function(data) {
                response(data.autocomplete);
            }
        });
    }

    $(document).ready(function(){
        $('#sdek_get_city_link').on('click', fn_get_sdek_city);
    });

    function fn_get_sdek_city() {
        var city = $('#city').val();

        $.ceAjax('request', fn_url("city_sdek.sdek_get_city_data"), {
            data: {
                var_city: city,
                loc: 'shipping_settings',
                result_ids: 'sdek_city_div',
            },
        });
    }

}(Tygh, Tygh.$));
