{if $addons.rus_unisender.send_sms_user == 'Y' && !empty($order_info.order_id)}
<li><a><label for="{$prefix}_{$id}_notify_unisender_users">
    <input type="checkbox" name="__notify_unisender_users" id="{$prefix}_{$id}_notify_unisender_users" value="Y" checked="checked" onclick="Tygh.$('input[name=__notify_unisender_users]').prop('checked', this.checked);" />
    {__("addons.rus_unisender.notify_unisender_users")}</label></a>
</li>
{/if}