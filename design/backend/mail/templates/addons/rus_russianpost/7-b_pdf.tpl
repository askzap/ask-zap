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

<body style="width: 148mm; height: 105mm;">
    <div style="width: 148mm; height: 105mm; position: relative;" >
        {if $data.print_bg == 'Y'}
        <img style="width: 148mm; height: 105mm;" src="{$images_dir}/addons/rus_russianpost/7-b.jpg">
        {/if}
        <span style="position: absolute;height: 6.5mm;width: 79mm;top: 33mm;left: 65mm;text-align: center;font: 10pt 'Arial';">{if $data.total_cen}{$data.total_cen}{else}б/ц{/if}</span>
        <span style="position: absolute;height: 6.5mm;width: 79mm;top: 44mm;left: 65mm;text-align: center;font: 10pt 'Arial';">{$data.total_cod}</span>

        <span style="position: absolute;height: 10.5mm;width: 63mm;top: 28mm;left: 2mm;font: 10pt 'Arial';text-indent: 13mm;line-height: 16pt;">{if $data.sender == '1'}{$data.whom}{else}{$data.fio}{/if}</span>
        <span style="position: absolute;height: 10.5mm;width: 62mm;top: 40.5mm;left: 2mm;font: 10pt 'Arial';text-indent: 13mm;line-height: 15pt;">{if $data.sender == '1'}{$data.where}{else}{$data.fiz_addres}{/if}</span>
        <span style="position: absolute;height: 11.5mm;width: 62mm;top: 46mm;left: 2mm;font: 10pt 'Arial';line-height: 17pt;">{if $data.sender == '1'}{$data.where2}{else}{$data.fiz_addres2}{/if}</span>
        <span style="position: absolute;height: 5.5mm;width: 31mm;top: 57.5mm;left: 33.5mm;font: 12pt 'Arial';letter-spacing: 7.6pt;">
            {if $data.sender == '1'}
                <span style="position: absolute; left: 0mm;">{$data.index.0}</span>
                <span style="position: absolute; left: 5.5mm;">{$data.index.1}</span>
                <span style="position: absolute; left: 10.5mm;">{$data.index.2}</span>
                <span style="position: absolute; left: 15.5mm;">{$data.index.3}</span>
                <span style="position: absolute; left: 20.5mm;">{$data.index.4}</span>
                <span style="position: absolute; left: 25.5mm;">{$data.index.5}</span>
            {else}
                <span style="position: absolute; left: 0mm;">{$data.fiz_index.0}</span>
                <span style="position: absolute; left: 5.5mm;">{$data.fiz_index.1}</span>
                <span style="position: absolute; left: 10.5mm;">{$data.fiz_index.2}</span>
                <span style="position: absolute; left: 15.5mm;">{$data.fiz_index.3}</span>
                <span style="position: absolute; left: 20.5mm;">{$data.fiz_index.4}</span>
                <span style="position: absolute; left: 25.5mm;">{$data.fiz_index.5}</span>
            {/if}   
        </span>
        <span style="position: absolute;height: 11.5mm;width: 76mm;top: 51mm;left: 69mm;font: 10pt 'Arial';text-indent: 11mm;line-height: 17pt;">{$data.from_whom}</span>
        <span style="position: absolute;height: 4.5mm;width: 76mm;top: 58mm;left: 69mm;;font: 10pt 'Arial';">{$data.from_whom2}</span>
        <span style="position: absolute;height: 4.5mm;width: 22mm;top: 58mm;left: 120mm;;font: 10pt 'Arial';">{$data.recipient_phone}</span>
        <span style="position: absolute;height: 10.5mm;width: 76mm;top: 64mm;left: 69mm;font: 10pt 'Arial';text-indent: 10mm;line-height: 17pt;">{$data.sender_address}</span>
        <span style="position: absolute;height: 4.5mm;width: 75mm;top: 71mm;left: 70mm;font: 10pt 'Arial';">{$data.sender_address2}</span>
        <span style="position: absolute;height: 5.5mm;width: 31mm;top: 75mm;left: 114.2mm;font: 12pt 'Arial';letter-spacing: 7.5pt;">
            <span style="position: absolute; left: 0mm;">{$data.from_index.0}</span>
            <span style="position: absolute; left: 5mm;">{$data.from_index.1}</span>
            <span style="position: absolute; left: 10mm;">{$data.from_index.2}</span>
            <span style="position: absolute; left: 15mm;">{$data.from_index.3}</span>
            <span style="position: absolute; left: 20mm;">{$data.from_index.4}</span>
            <span style="position: absolute; left: 25mm;">{$data.from_index.5}</span>
        </span>
    </div>
</body>
</html>