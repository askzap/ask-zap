{* Shipper address *}
{capture name="shipper_info"}
    {if $shipper.ContactName}
    <p class="strong">
        {$shipper.ContactName},
    </p>
    {/if}
    <div class="clear">
        {if $shipper.Phone}
            <span>{__("phone")}:</span>
            <span>{$shipper.Phone}</span><br/>
        {/if}
        {if $shipper.CompanyName}
            <span>{__("company")}:</span>
            <span>{$shipper.CompanyName}</span><br/>
        {/if}
        {if $shipper.PostCode}
            <span>{$shipper.PostCode}</span><br/>
        {/if}
        {if $shipper.Country}
            <span>{$shipper.Country}</span><br/>
        {/if}
        {if $shipper.Region}
            <span>{$shipper.Region}</span><br/>
        {/if}
        {if $shipper.City}
            <span>{$shipper.City}</span><br/>
        {/if}
        {if $shipper.Address}
            <span>{$shipper.Address}</span><br/>
        {/if}
    </div>
{/capture}

{* customer information *}
{capture name="receiver_info"}
    {if $receiver.ContactName}
    <p class="strong">
        {$receiver.ContactName},
    </p>
    {/if}
    <div class="clear">
        {if $receiver.Phone}
            <span>{__("phone")}:</span>
            <span>{$receiver.Phone}</span><br/>
        {/if}
        {if $receiver.CompanyName}
            <span>{__("company")}:</span>
            <span>{$receiver.CompanyName}</span><br/>
        {/if}
        {if $receiver.PostCode}
            <span>{$receiver.PostCode}</span><br/>
        {/if}
        {if $receiver.Country}
            <span>{$receiver.Country}</span><br/>
        {/if}
        {if $receiver.Region}
            <span>{$receiver.Region}</span><br/>
        {/if}
        {if $receiver.City}
            <span>{$receiver.City}</span><br/>
        {/if}
        {if $receiver.Address}
            <span>{$receiver.Address}</span><br/>
        {/if}
    </div>
{/capture}

<div class="sidebar-row">
    <h6>{__("customer_information")}</h6>
    <div class="profile-info">
        <i class="icon-user"></i>
        {$smarty.capture.receiver_info nofilter}
    </div>
</div>
<hr />

<div class="sidebar-row">
    <h6>{__("company")}</h6>
    <div class="profile-info">
        <i class="exicon-car"></i>
        {$smarty.capture.shipper_info nofilter}
    </div>
</div>
<hr />
