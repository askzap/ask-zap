<div id="mp_settings_block">

{foreach from=$mp_provider_templates item="mp_map_template" key="mp_map_provider" name="mp_providers"}
<div class="control-group setting-wide {if $addons.maps_provider.map_provider != $mp_map_provider} hidden{/if}" id="settings_container_{$mp_map_provider}">
        {include file=$mp_map_template}
</div>
{/foreach}

</div>

<script type="text/javascript">
//<![CDATA[
Tygh.$(document).ready(function(){$ldelim}
var $ = Tygh.$;

{literal}
$(':input[id$=map_provider]').on('change', function() {
    var selected_map_provider = $(':input[id$=map_provider]').val();

    $('[id^=settings_container_]').addClass('hidden');
    $('#settings_container_' + selected_map_provider).removeClass('hidden');
});
{/literal}
{$rdelim});
//]]>
</script>

