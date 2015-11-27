{* rus_build_yandex_metrika dbazhenov *}

<script type="text/javascript">
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
</script>
<noscript><div><img src="//mc.yandex.ru/watch/{$addons.rus_yandex_metrika.counter_number}" style="position:absolute; left:-9999px;" alt="" /></div></noscript>

