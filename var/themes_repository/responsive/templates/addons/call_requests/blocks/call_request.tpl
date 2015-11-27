{** block-description:tmpl_call_request **}

<div class="ty-cr-phone-number-link">
    <div class="ty-cr-phone"><span><span class="ty-cr-phone-prefix">{$phone_number.prefix}</span>{$phone_number.postfix}</span></div>
    <div class="ty-cr-link">{include file="addons/call_requests/views/call_requests/components/popup.tpl" product=false link_text=__("call_requests.request_call")}</div>
</div>