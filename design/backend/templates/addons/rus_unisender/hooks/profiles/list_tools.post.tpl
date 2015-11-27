{if $search.user_type == "C" && $show_unisender_tool}
    <li>{btn type="list" text=__("addons.rus_unisender.add_selected_to_unisender") dispatch="dispatch[unisender.add_selected]" form="userlist_form"}</li>
{/if}
