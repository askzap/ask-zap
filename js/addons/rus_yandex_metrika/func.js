(function(_, $) {
    $(document).on('click', 'button[type=submit][name^="dispatch[checkout.add"]', function() {
        $.ceEvent('one', 'ce.formajaxpost_' + $(this).parents('form').prop('name'), function(form, elm) {
            if (_.yandex_metrika.settings.collect_stats_for_goals.basket == 'Y') {
                if (typeof window['yaCounter' + _.yandex_metrika.settings.id] != "undefined") {
                    window['yaCounter' + _.yandex_metrika.settings.id].reachGoal('basket', {});
                }
            }
        });
    });

    $(document).on('click', '.cm-submit[id^="button_wishlist"]', function() {
        $.ceEvent('one', 'ce.formajaxpost_' + $(this).parents('form').prop('name'), function(form, elm) {
            if (_.yandex_metrika.settings.collect_stats_for_goals.wishlist == 'Y') {
                if (typeof window['yaCounter' + _.yandex_metrika.settings.id] != "undefined") {
                    window['yaCounter' + _.yandex_metrika.settings.id].reachGoal('wishlist', {});
                }
            }
        });
    });

    $(document).on('click', 'a[id^=opener_call_request]', function() {
        if (_.yandex_metrika.settings.collect_stats_for_goals.buy_with_one_click_form_opened == 'Y') {
            if (typeof window['yaCounter' + _.yandex_metrika.settings.id] != "undefined") {
                window['yaCounter' + _.yandex_metrika.settings.id].reachGoal('buy_with_one_click_form_opened', {});
            }
        }
    });

    $.ceEvent('on', 'ce.formajaxpost_call_requests_form', function(form, elm) {
        if (_.yandex_metrika.settings.collect_stats_for_goals.call_request == 'Y') {
            if (typeof window['yaCounter' + _.yandex_metrika.settings.id] != "undefined") {
                window['yaCounter' + _.yandex_metrika.settings.id].reachGoal('call_request', {});
            }
        }
    });
}(Tygh, jQuery));

(function (d, w, c, _, $) {

    (w[c] = w[c] || []).push(function() {
        try {
            _.yandex_metrika.settings.params = w.yaParams || {};

            w['yaCounter' + _.yandex_metrika.settings.id] = new Ya.Metrika(_.yandex_metrika.settings);

            var goals_scheme = _.yandex_metrika.goals_scheme;

            $.each(_.yandex_metrika.settings.collect_stats_for_goals, function(goal_name, enabled) {
                if (
                    enabled == 'Y'
                    && goals_scheme[goal_name].controller
                    && goals_scheme[goal_name].controller == _.yandex_metrika.current_controller
                    && goals_scheme[goal_name].mode == _.yandex_metrika.current_mode
                ) {
                    w['yaCounter' + _.yandex_metrika.settings.id].reachGoal(goal_name, _.yandex_metrika.settings.params);
                }

            });

        } catch(e) { };
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f);
    } else {
        f();
    }

})(document, window, "yandex_metrika_callbacks", Tygh, Tygh.$);