<div id="content_rus_pickup">

<div class="control-group">
    <label class="control-label" for="elm_pickup_avail">{__("rus_pickup.pickup_avail")}:</label>
    <div class="controls">
        <label class="checkbox">
            <input type="hidden" name="store_location_data[pickup_avail]" value="N" />
            <input type="checkbox" name="store_location_data[pickup_avail]" id="elm_pickup_avail" value="Y" {if $store_location.pickup_avail != "N"}checked="checked"{/if}/>
        </label>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="elm_pickup_surcharge">{__("surcharge")}:</label>
    <div class="controls">
        <input id="elm_pickup_surcharge" type="text" name="store_location_data[pickup_surcharge]" class="input-mini" value="{$store_location.pickup_surcharge}" size="4"> {$currencies.$primary_currency.symbol nofilter}
    </div>
</div>

<div class="control-group">
    <label for="elm_pickup_address" class="control-label">{__("address")}</label>
    <div class="controls">
        <input class="input-large" type="text" name="store_location_data[pickup_address]" id="elm_pickup_address" size="55" value="{$store_location.pickup_address}" />
    </div>
</div>

<div class="control-group">
    <label for="elm_pickup_phone" class="control-label">{__("phone")}</label>
    <div class="controls">
        <input class="input-large" type="text" name="store_location_data[pickup_phone]" id="elm_pickup_phone" size="55" value="{$store_location.pickup_phone}" />
    </div>
</div>

<div class="control-group">
    <label for="elm_pickup_work_time" class="control-label">{__("rus_pickup.work_time")}</label>
    <div class="controls">
        <input class="input-large" type="text" name="store_location_data[pickup_time]" id="elm_pickup_work_time" size="55" value="{$store_location.pickup_time}" />
    </div>
</div>

{if $destinations}

<div class="control-group">
    <label class="control-label">{__("locations")}:</label>
    <div class="controls">
        {foreach from=$destinations item=destination}
        <label class="checkbox inline" for="destinations_{$destination.destination_id}">
        <input type="checkbox" name="pickup_destinations_ids[]" id="destinations_{$destination.destination_id}" {if $d_ids && $destination.destination_id|in_array:$d_ids} checked="checked"{/if} value="{$destination.destination_id}"/>{$destination.destination}
        </label>
        {/foreach}
    </div>
</div>

{/if}

</div>
