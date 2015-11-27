
{if $carrier == "ems"}
    {$url = "http://www.russianpost.ru/Tracking20/?`$tracking_number`" scope=parent}
    {$carrier_name = __("ems") scope=parent}

{elseif $carrier == "russian_post"}
    {$url = "http://www.russianpost.ru/Tracking20/?`$tracking_number`" scope=parent}
    {$carrier_name = __("russian_post") scope=parent}

{elseif $carrier == "russian_post_calc"}
    {$url = "http://www.russianpost.ru/Tracking20/?`$tracking_number`" scope=parent}
    {$carrier_name = __("russian_post") scope=parent}
{/if}

{capture name="carrier_name"}
{$carrier_name}
{/capture}

{capture name="carrier_url"}
{$url nofilter}
{/capture}
