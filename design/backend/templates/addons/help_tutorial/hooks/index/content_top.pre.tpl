{if ($runtime.controller == "block_manager" && $runtime.mode == "manage")}
    {include file="addons/help_tutorial/components/video.tpl" count="2" item="Tv7AZhmLwkw" item2="RseUfuFdctg"}
{elseif ($runtime.controller == "themes" && $runtime.mode == "manage")}
    {include file="addons/help_tutorial/components/video.tpl" item="BVOLfcROTyg"}
{elseif ($runtime.controller == "store_import" && $runtime.mode == "index")}
    {include file="addons/help_tutorial/components/video.tpl" item="cCJOoAZnCqk"}
{elseif ("ULTIMATE"|fn_allowed_for && $runtime.controller == "companies")}
    {include file="addons/help_tutorial/components/video.tpl" item="eUam0Puui3M"}
{elseif ($runtime.controller == "index" && $runtime.mode == "index")}
    {include file="addons/help_tutorial/components/video.tpl" item="5STIqzsPU9c"}
{elseif ($runtime.controller == "seo_rules" && $runtime.mode == "manage")}
    {include file="addons/help_tutorial/components/video.tpl" item="JUFXyew6lig"}
{/if}