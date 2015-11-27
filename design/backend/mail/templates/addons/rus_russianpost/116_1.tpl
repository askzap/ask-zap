<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
{literal}
<style type="text/css" media="screen,print">

body,p,div,td {
    color: #000000;
    font: 12px Arial;
}
body {
    padding: 0;
    margin: 0;
}
a, a:link, a:visited, a:hover, a:active {
    color: #000000;
    text-decoration: underline;
}
a:hover {
    text-decoration: none;
}
</style>

{/literal}
</head>

<body style="width: 100%; height: 100%; margin: 0 ; padding: 0;">
    <div style="width: {$addons.rus_russianpost.116_list_width}mm; height: {$addons.rus_russianpost.116_list_height}mm;">
        <div style="top: {$addons.rus_russianpost.116_top}mm; left: {$addons.rus_russianpost.116_left}mm; width: 141mm; height: 200mm; position: relative; outline: 1px solid rgb(240,240,240)" >
        {if $data.print_bg == 'Y'}
        <img style="width: 141mm; height: 200mm;" src="{$images_dir}/addons/rus_russianpost/116_1.png">
        {/if}
        <span style="position: absolute;height: 5.5mm;width: 86mm;top: 45mm;left: 9mm;text-align: center;font: 10pt 'Arial';">{if $data.not_total}б/ц{elseif $data.total_cen}{$data.total_cen}{else}б/ц{/if}</span>
        <span style="position: absolute;height: 5.5mm;width: 86mm;top: 53mm;left: 9mm;text-align: center;font: 10pt 'Arial';">{if !$data.not_total}{$data.total_cod}{/if}</span>

        <span style="position: absolute;height: 5.5mm;width: 79mm;top: 60mm;left: 17mm;font: 10pt 'Arial';">{$data.from_whom}</span>
        <span style="position: absolute;height: 5.5mm;width: 22mm;top: 60mm;left: 70mm;;font: 10pt 'Arial';">{$data.recipient_phone}</span>
        <span style="position: absolute;height: 10.5mm;width: 87mm;top: 64mm;left: 9mm;font: 10pt 'Arial';text-indent: 10mm;line-height: 17pt;">{$data.sender_address}</span>
        <span style="position: absolute;height: 5.5mm;width: 86mm;top: 71mm;left: 9mm;font: 10pt 'Arial';">{$data.sender_address2}</span>
        <span style="position: absolute;height: 5.5mm;width: 36mm;top: 76.5mm;left: 66.5mm;font: 12pt 'Arial';letter-spacing: 6.6pt;">
            <span style="position: absolute; left: 0mm;">{$data.from_index.0}</span>
            <span style="position: absolute; left: 4.5mm;">{$data.from_index.1}</span>
            <span style="position: absolute; left: 9.5mm;">{$data.from_index.2}</span>
            <span style="position: absolute; left: 14mm;">{$data.from_index.3}</span>
            <span style="position: absolute; left: 18.5mm;">{$data.from_index.4}</span>
            <span style="position: absolute; left: 23.5mm;">{$data.from_index.5}</span>
        </span>

        <span style="position: absolute;height: 5.5mm;width: 65mm;top: 84mm;left: 20mm;font: 10pt 'Arial';">{if $data.sender == '1'}{$data.whom}{else}{$data.fio}{/if}</span>
        <span style="position: absolute;height: 5.5mm;width: 45mm;top: 84mm;left: 85mm;font: 10pt 'Arial';">{if $data.sender == '1'}{$data.whom2}{else}{$data.fio2}{/if}</span>
        <span style="position: absolute;height: 5.5mm;width: 116mm;top: 91mm;left: 17mm;font: 10pt 'Arial';">{if $data.sender == '1'}{$data.where}{else}{$data.fiz_addres}{/if}</span>
        <span style="position: absolute;height: 5.5mm;width: 91mm;top: 96mm;left: 9mm;font: 10pt 'Arial';">{if $data.sender == '1'}{$data.where2}{else}{$data.fiz_addres2}{/if}</span>
        {if $data.sender == '1'}
            <span style="position: absolute;height: 5.5mm;width: 36mm;top: 95.5mm;left: 101.5mm;font: 12pt 'Arial';letter-spacing: 6.6pt;">
                <span style="position: absolute; left: 0mm;">{$data.index.0}</span>
                <span style="position: absolute; left: 4.5mm;">{$data.index.1}</span>
                <span style="position: absolute; left: 9.5mm;">{$data.index.2}</span>
                <span style="position: absolute; left: 14mm;">{$data.index.3}</span>
                <span style="position: absolute; left: 18.5mm;">{$data.index.4}</span>
                <span style="position: absolute; left: 23.5mm;">{$data.index.5}</span>
            </span>
        {else}
            <span style="position: absolute;height: 5.5mm;width: 36mm;top: 95.5mm;left: 101.5mm;font: 12pt 'Arial';letter-spacing: 6.6pt;">
                <span style="position: absolute; left: 0mm;">{$data.fiz_index.0}</span>
                <span style="position: absolute; left: 4.5mm;">{$data.fiz_index.1}</span>
                <span style="position: absolute; left: 9.5mm;">{$data.fiz_index.2}</span>
                <span style="position: absolute; left: 14mm;">{$data.fiz_index.3}</span>
                <span style="position: absolute; left: 18.5mm;">{$data.fiz_index.4}</span>
                <span style="position: absolute; left: 23.5mm;">{$data.fiz_index.5}</span>
            </span>
        {/if}

        <span style="position: absolute;height: 5.5mm;width: 22mm;top: 109mm;left: 24mm;text-align: center;font: 10pt 'Arial';">{$data.fiz_doc}</span>
        <span style="position: absolute;height: 5.5mm;width: 14mm;top: 109mm;left: 55mm;text-align: center;font: 10pt 'Arial';">{$data.fiz_doc_serial}</span>
        <span style="position: absolute;height: 5.5mm;width: 16mm;top: 109mm;left: 73mm;text-align: center;font: 10pt 'Arial';">{$data.fiz_doc_number}</span>
        <span style="position: absolute;height: 5.5mm;width: 17mm;top: 109mm;text-align: center;left: 99mm;font: 10pt 'Arial';">{$data.fiz_doc_date}</span>
        <span style="position: absolute;height: 5.5mm;width: 7mm;top: 109mm;text-align: center;left: 120mm;font: 10pt 'Arial';">{$data.fiz_doc_date2}</span>
        <span style="position: absolute;height: 5.5mm;width: 125mm;top: 114mm;left: 7mm;text-align: center;font: 10pt 'Arial';">{$data.fiz_doc_creator}</span>

        <span style="position: absolute; height: 5.5mm; width: 36mm; top: 159mm; left: 22mm; text-align: center; font: 10pt 'Arial';">{if $data.not_total}б/ц{elseif $data.clear_total_cen}{$data.clear_total_cen}{else}б/ц{/if}</span>
        <span style="position: absolute;height: 5.5mm;width: 38mm;top: 159mm;left: 83mm;text-align: center;font: 10pt 'Arial';">{if !$data.not_total && $data.total_cod}{$order_info.total}{/if}</span>

        <span style="position: absolute;height: 5.5mm;width: 112mm;top: 167mm;left: 17mm;font: 10pt 'Arial';">{$data.from_whom}</span>
        <span style="position: absolute;height: 5.5mm;width: 22mm;top: 167mm;left: 70mm;;font: 10pt 'Arial';">{$data.recipient_phone}</span>
        <span style="position: absolute;height: 10.5mm;width: 121mm;top: 174mm;left: 9mm;font: 10pt 'Arial';text-indent: 10mm;line-height: 17pt;">{$data.sender_address}</span>
        <span style="position: absolute;height: 5.5mm;width: 90mm;top: 182mm;left: 9mm;font: 10pt 'Arial';">{$data.sender_address2}</span>
        <span style="position: absolute;height: 5.5mm;width: 36mm;top: 181mm;left: 101.5mm;font: 12pt 'Arial';letter-spacing: 6.6pt;">
            <span style="position: absolute; left: 0mm;">{$data.from_index.0}</span>
            <span style="position: absolute; left: 4.5mm;">{$data.from_index.1}</span>
            <span style="position: absolute; left: 9.5mm;">{$data.from_index.2}</span>
            <span style="position: absolute; left: 14mm;">{$data.from_index.3}</span>
            <span style="position: absolute; left: 18.5mm;">{$data.from_index.4}</span>
            <span style="position: absolute; left: 23.5mm;">{$data.from_index.5}</span>
        </span>
        </div>
    </div>
</body>
</html>