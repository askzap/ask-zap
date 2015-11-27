{assign var="data_id" value=$data_id|substr: 2}
{assign var="_pos" value=$_class|strpos:' cm-geolocation'}
{assign var="_class" value=$_class|substr:0:$_pos scope="parent"}
{if empty($value) || ($data_id == 'country' && empty($user_data.b_state) && empty($user_data.s_state))}
    {if $data_id == 'address' || $data_id == 'city' || $data_id == 'state' || $data_id == 'country'}
        {assign var="_class" value=$_class|cat:' cm-geolocation-'|cat:$data_id scope="parent"}
    {/if}
{/if}
