{script src="js/tygh/tabs.js"}

<div class="step-container{if $edit}-active{/if} step-four" data-ct-checkout="billing_options" id="step_four">
    <h2 class="step-title{if $edit}-active{/if} clearfix">
        <span class="float-left">{$number_of_step}</span>
        
        {hook name="checkout:step_four_edit_link_title"}
        <a class="title{if $complete && !$edit} cm-ajax{/if}" {if $complete && !$edit}href="{"checkout.checkout?edit_step=step_four&from_step=`$cart.edit_step`"|fn_url}" data-ca-target-id="checkout_*"{/if}>{__("billing_options")}</a>
        {/hook}
    </h2>

    <div id="step_four_body" class="step-body{if $edit}-active{/if} {if !$edit}hidden{/if}">
        <div class="clearfix">
            
            {if $edit}
                {capture name="final_section"}
                    {include file="views/checkout/components/final_section.tpl" is_payment_step=true}
                {/capture}

                {if $cart|fn_allow_place_order:$auth}
                    {if $cart.payment_id}
                        <div class="clearfix">
                            {include file="views/checkout/components/payments/payment_methods.tpl" payment_id=$cart.payment_id final_section=$smarty.capture.final_section}
                        </div>
                    {else}
                        <div class="checkout-inside-block"><h2 class="subheader">{__("text_no_payments_needed")}</h2></div>

                        <form name="paymens_form" action="{""|fn_url}" method="post">
                            {$smarty.capture.final_section nofilter}
                            <div class="checkout-buttons">
                                {include file="buttons/place_order.tpl" but_text=__("submit_my_order") but_name="dispatch[checkout.place_order]" but_role="big" but_id="place_order"}    
                            </div>
                        </form>
                    {/if}
                {else}
                    {$smarty.capture.final_section nofilter}
                {/if}
            {/if}

        </div>
    </div>
<!--step_four--></div>

<div id="place_order_data" class="hidden">
</div>