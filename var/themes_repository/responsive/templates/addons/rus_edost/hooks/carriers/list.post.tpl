
{if $carrier == "edost"}
    {$url = fn_url("edost.services&shipment_id=$shipment_id&carrier=`$carrier`&tracknumbers=`$tracking_number`") scope=parent}
    {$carrier_name = $carrier scope=parent}
{/if}

