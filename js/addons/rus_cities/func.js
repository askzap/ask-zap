(function(_, $){

    $(function(){

        $("[name='user_data[b_city]']").autocomplete({
            source: function(request, response) {
                var type = this.element.attr('name').substr(10,1);
                getRusCities(type, request, response);
            }
        });

        $("[name='user_data[s_city]']").autocomplete({
            source: function(request, response) {
                var type = this.element.attr('name').substr(10,1);
                getRusCities(type, request, response);
            }
        });

        function getRusCities(type, request, response) {

            var check_country = $("[name='user_data[" + type + "_country]']").val();
            var check_state = $("[name='user_data[" + type + "_state]']").val();

            $.ceAjax('request', fn_url('city.autocomplete_city?q=' + encodeURIComponent(request.term) + '&check_state=' + check_state + '&check_country=' + check_country), {
                callback: function(data) {
                    response(data.autocomplete);
                }
            });
        }

    });

})(Tygh, Tygh.$);

