{$type = $field.field_name|substr: 2}

{if $type == 'city'}
    {script src="js/addons/rus_cities/func.js"}
{/if}
