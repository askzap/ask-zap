
<div class="sidebar-row">
    <form action="{""|fn_url}" method="post" name="courier_form">
        <h6>{__("search")}</h6>
        {capture name="simple_search"}
            {include file="addons/rus_spsr/views/components/period_selector.tpl" period=$period display="form"}
        {/capture}
        {include file="common/advanced_search.tpl" no_adv_link=true simple_search=$smarty.capture.simple_search not_saved=true dispatch="spsr_courier.manage"}
    </form>
</div>
