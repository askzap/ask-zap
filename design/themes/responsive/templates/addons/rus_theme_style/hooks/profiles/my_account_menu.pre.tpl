{* jmi *}

{capture name="title"}
    <a class="ty-account-info__title" href="{"profiles.update"|fn_url}">
        <i class="ty-icon-moon-user"></i>
        <span class="hidden-phone" {live_edit name="block:name:{$block.block_id}"}>{$title}</span>
        <i class="ty-icon-down-micro ty-account-info__user-arrow"></i>
    </a>
{/capture}