{script src="js/addons/maps_provider/map.js"}
{script src="js/addons/maps_provider/providers/`$settings.maps_provider.general.map_provider`.js"}
{script src="js/addons/maps_provider/func.js"}


<script type="text/javascript">
//<![CDATA[
(function(_, $) {
    $.extend(_, {
        maps_provider: {$settings.maps_provider_|unserialize|json_encode nofilter}
    });

}(Tygh, Tygh.$));
//]]>
</script>

