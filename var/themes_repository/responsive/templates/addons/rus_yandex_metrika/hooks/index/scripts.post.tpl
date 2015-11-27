{script src="js/addons/rus_yandex_metrika/func.js"}

{strip}
    <script type="text/javascript">
        (function(_, $) {
            $.extend(_, {
                yandex_metrika: {
                    goals_scheme: {$yandex_metrika_goals_scheme|json_encode nofilter},
                    settings: {
                        id: {$addons.rus_yandex_metrika.counter_number|default:"''" nofilter},
                        {if $addons.rus_yandex_metrika.clickmap == 'Y'} clickmap: true,{/if}
                        {if $addons.rus_yandex_metrika.external_links == 'Y'} trackLinks: true,{/if}
                        {if $addons.rus_yandex_metrika.denial == 'Y'} accurateTrackBounce: true,{/if}
                        {if $addons.rus_yandex_metrika.track_hash == 'Y'} trackHash: true,{/if}
                        {if $addons.rus_yandex_metrika.visor == 'Y'} webvisor: true,{/if}
                        collect_stats_for_goals: {$addons.rus_yandex_metrika.collect_stats_for_goals|json_encode nofilter},
                    },
                    current_controller: '{$runtime.controller}',
                    current_mode: '{$runtime.mode}'
                }
            });
        }(Tygh, Tygh.$));
    </script>
{/strip}
