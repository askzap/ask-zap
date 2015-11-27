
<div class="sidebar-row">
    <form action="{""|fn_url}" method="post" name="courier_form">
        <h6>{__("search")}</h6>

        <div class="sidebar-field">
           <label for="status_selects">{__("status")}:</label>
            <select name="status" id="status_selects">
                <option value="A" {if $status == "A"}selected="selected"{/if}>{__("shippings.spsr.status.all")}</option>
                <option value="D" {if $status == "D"}selected="selected"{/if}>{__("shippings.spsr.status.d")}</option>
                <option value="P" {if $status == "P"}selected="selected"{/if}>{__("shippings.spsr.status.p")}</option>
            </select>
        </div>

        {capture name="simple_search"}
            {include file="addons/rus_spsr/views/components/period_selector.tpl" period=$period display="form"}
        {/capture}
        {include file="common/advanced_search.tpl" no_adv_link=true simple_search=$smarty.capture.simple_search not_saved=true dispatch="spsr_invoice.manage"}
    </form>
</div>
