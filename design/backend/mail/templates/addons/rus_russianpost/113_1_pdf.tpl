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

<body style="width: 195mm; height: 185mm;">
    <div style="width: 195mm; height: 185mm; position: relative;" >
        {if $data.print_bg == 'Y'}
        <img style="width: 195mm; height: 185mm; " src="{$images_dir}/addons/rus_russianpost/113_1.png">
        {/if}
        <span style="position: absolute;height: 5.5mm;width: 15mm;top: 42mm;left: 153mm;text-align: center;font: 11pt 'Arial';">{$data.total_rub}</span>
        <span style="position: absolute;height: 5.5mm;width: 5mm;top: 42mm;left: 177mm;text-align: center;font: 11pt 'Arial';">{$data.total_kop}</span>
        <span style="position: absolute;height: 5.5mm;width: 120mm;top: 47mm;left: 70mm;text-align: center;font: 10pt 'Arial';">{$data.total}</span>

        {if $data.sender == '1'}
            <div style="position: absolute;height: 10.5mm;width: 120mm;top: 54.5mm;left: 70mm;text-indent: 10mm;font: 10pt 'Arial';margin: 0;line-height: 14.5pt;">{$data.whom}</div>
            <div style="position: absolute;height: 4.5mm;width: 120mm;top: 60mm;left: 70mm;font: 10pt 'Arial';margin: 0;">{$data.whom2}</div>
            <div style="position: absolute;height: 10.5mm;width: 120mm;top: 65mm;left: 70mm;text-indent: 10mm;font: 10pt 'Arial';margin: 0;line-height: 14.5pt;">{$data.where}</div>
            <div style="position: absolute;height: 4.5mm;width: 120mm;top: 70mm;left: 70mm;font: 10pt 'Arial';margin: 0;">{$data.where2}</div>

            <div style="position: absolute;height: 4.5mm;width: 20mm;top: 74.5mm;left: 173mm;font: 11pt 'Arial';margin: 0;">
                <span style="position: absolute; left: 0mm;">{$data.index.0}</span>
                <span style="position: absolute; left: 3mm;">{$data.index.1}</span>
                <span style="position: absolute; left: 6mm;">{$data.index.2}</span>
                <span style="position: absolute; left: 9mm;">{$data.index.3}</span>
                <span style="position: absolute; left: 12mm;">{$data.index.4}</span>
                <span style="position: absolute; left: 15mm;">{$data.index.5}</span>
            </div>
        {else}
            <div style="position: absolute;height: 10.5mm;width: 120mm;top: 54.5mm;left: 70mm;text-indent: 10mm;font: 10pt 'Arial';margin: 0;line-height: 14.5pt;">{$data.fio}</div>
            <div style="position: absolute;height: 4.5mm;width: 120mm;top: 60mm;left: 70mm;font: 10pt 'Arial';margin: 0;">{$data.fio2}</div>
            <div style="position: absolute;height: 10.5mm;width: 120mm;top: 65mm;left: 70mm;text-indent: 10mm;font: 10pt 'Arial';margin: 0;line-height: 14.5pt;">{$data.fiz_addres}</div>
            <div style="position: absolute;height: 4.5mm;width: 120mm;top: 70mm;left: 70mm;font: 10pt 'Arial';margin: 0;">{$data.fiz_addres2}</div>

            <div style="position: absolute;height: 4.5mm;width: 20mm;top: 74.5mm;left: 173mm;font: 11pt 'Arial';margin: 0;">
                <span style="position: absolute; left: 0mm;">{$data.fiz_index.0}</span>
                <span style="position: absolute; left: 3mm;">{$data.fiz_index.1}</span>
                <span style="position: absolute; left: 6mm;">{$data.fiz_index.2}</span>
                <span style="position: absolute; left: 9mm;">{$data.fiz_index.3}</span>
                <span style="position: absolute; left: 12mm;">{$data.fiz_index.4}</span>
                <span style="position: absolute; left: 15mm;">{$data.fiz_index.5}</span>
            </div>
        {/if}

        <div style="position: absolute;height: 4.5mm;width: 40mm;top: 84mm;left: 79.7mm;font: 11pt 'Arial';margin: 0; ">
            <span style="position: absolute; left: 0mm;">{$data.inn.0}</span>
            <span style="position: absolute; left: 3.1mm;">{$data.inn.1}</span>
            <span style="position: absolute; left: 6.2mm;">{$data.inn.2}</span>
            <span style="position: absolute; left: 9.3mm;">{$data.inn.3}</span>
            <span style="position: absolute; left: 12.4mm;">{$data.inn.4}</span>
            <span style="position: absolute; left: 15.5mm;">{$data.inn.5}</span>
            <span style="position: absolute; left: 18.6mm;">{$data.inn.6}</span>
            <span style="position: absolute; left: 21.7mm;">{$data.inn.7}</span>
            <span style="position: absolute; left: 24.8mm;">{$data.inn.8}</span>
            <span style="position: absolute; left: 27.9mm;">{$data.inn.9}</span>
        </div>

        <div style="position: absolute;height: 4.5mm;width: 62mm;top: 84mm;left: 130.5mm;font: 11pt 'Arial';margin: 0; ">
            <span style="position: absolute; left: 0mm;">{$data.kor.0}</span>
            <span style="position: absolute; left: 3mm;">{$data.kor.1}</span>
            <span style="position: absolute; left: 6mm;">{$data.kor.2}</span>
            <span style="position: absolute; left: 9mm;">{$data.kor.3}</span>
            <span style="position: absolute; left: 12mm;">{$data.kor.4}</span>
            <span style="position: absolute; left: 15mm;">{$data.kor.5}</span>
            <span style="position: absolute; left: 18mm;">{$data.kor.6}</span>
            <span style="position: absolute; left: 21mm;">{$data.kor.7}</span>
            <span style="position: absolute; left: 24mm;">{$data.kor.8}</span>
            <span style="position: absolute; left: 27mm;">{$data.kor.9}</span>
            <span style="position: absolute; left: 30mm;">{$data.kor.10}</span>
            <span style="position: absolute; left: 33mm;">{$data.kor.11}</span>
            <span style="position: absolute; left: 36mm;">{$data.kor.12}</span>
            <span style="position: absolute; left: 39mm;">{$data.kor.13}</span>
            <span style="position: absolute; left: 42mm;">{$data.kor.14}</span>
            <span style="position: absolute; left: 45mm;">{$data.kor.15}</span>
            <span style="position: absolute; left: 48mm;">{$data.kor.16}</span>
            <span style="position: absolute; left: 51mm;">{$data.kor.17}</span>
            <span style="position: absolute; left: 54mm;">{$data.kor.18}</span>
            <span style="position: absolute; left: 57mm;">{$data.kor.19}</span>
        </div>

        <div style="position: absolute;height: 4.5mm;width: 85mm;top: 89.5mm;left: 105mm;font: 11pt 'Arial';margin: 0;">{$data.bank}</div>

        <div style="position: absolute;height: 4.5mm;width: 100mm;top: 94mm;left: 87.5mm;font: 11pt 'Arial';margin: 0; ">
            <span style="position: absolute; left: 0mm;">{$data.ras.0}</span>
            <span style="position: absolute; left: 3mm;">{$data.ras.1}</span>
            <span style="position: absolute; left: 6mm;">{$data.ras.2}</span>
            <span style="position: absolute; left: 9mm;">{$data.ras.3}</span>
            <span style="position: absolute; left: 12mm;">{$data.ras.4}</span>
            <span style="position: absolute; left: 15mm;">{$data.ras.5}</span>
            <span style="position: absolute; left: 18mm;">{$data.ras.6}</span>
            <span style="position: absolute; left: 21mm;">{$data.ras.7}</span>
            <span style="position: absolute; left: 24mm;">{$data.ras.8}</span>
            <span style="position: absolute; left: 27mm;">{$data.ras.9}</span>
            <span style="position: absolute; left: 30mm;">{$data.ras.10}</span>
            <span style="position: absolute; left: 33mm;">{$data.ras.11}</span>
            <span style="position: absolute; left: 36mm;">{$data.ras.12}</span>
            <span style="position: absolute; left: 39mm;">{$data.ras.13}</span>
            <span style="position: absolute; left: 42mm;">{$data.ras.14}</span>
            <span style="position: absolute; left: 45mm;">{$data.ras.15}</span>
            <span style="position: absolute; left: 48mm;">{$data.ras.16}</span>
            <span style="position: absolute; left: 51mm;">{$data.ras.17}</span>
            <span style="position: absolute; left: 54mm;">{$data.ras.18}</span>
            <span style="position: absolute; left: 57mm;">{$data.ras.19}</span>
        </div>

        <div style="position: absolute;height: 4.5mm;width: 30mm;top: 94mm;left: 164mm;font: 11pt 'Arial';margin: 0; ">
            <span style="position: absolute; left: 0mm;">{$data.bik.0}</span>
            <span style="position: absolute; left: 3mm;">{$data.bik.1}</span>
            <span style="position: absolute; left: 6mm;">{$data.bik.2}</span>
            <span style="position: absolute; left: 9mm;">{$data.bik.3}</span>
            <span style="position: absolute; left: 12mm;">{$data.bik.4}</span>
            <span style="position: absolute; left: 15mm;">{$data.bik.5}</span>
            <span style="position: absolute; left: 18mm;">{$data.bik.6}</span>
            <span style="position: absolute; left: 21mm;">{$data.bik.7}</span>
            <span style="position: absolute; left: 24mm;">{$data.bik.8}</span>
        </div>

        <div style="position: absolute;height: 10.5mm;width: 73mm;top: 128mm;left: 70mm;text-indent: 15mm;font: 10pt 'Arial';margin: 0;line-height: 14.5pt;">{$data.from_whom}</div>
        <div style="position: absolute;height: 4.5mm;width: 120mm;top: 133.5mm;left: 70mm;font: 10pt 'Arial';margin: 0;">{$data.from_whom2}</div>

        <div style="position: absolute;height: 10.5mm;width: 120mm;top: 138mm;left: 70mm;text-indent: 34mm;font: 10pt 'Arial';margin: 0;line-height: 14.5pt;">{$data.sender_address}</div>
        <div style="position: absolute;height: 10.5mm;width: 120mm;top: 143.5mm;left: 70mm;font: 10pt 'Arial';margin: 0;">{$data.sender_address2}</div>
        <div style="position: absolute;height: 4.5mm;width: 93mm;top: 148.5mm;left: 70mm;font: 10pt 'Arial';margin: 0;">{$data.sender_address3}</div>

        <div style="position: absolute;height: 4.5mm;width: 19mm;top: 148mm;left: 173.5mm;font: 11pt 'Arial';margin: 0; ">
            <span style="position: absolute; left: 0mm;">{$data.from_index.0}</span>
            <span style="position: absolute; left: 3mm;">{$data.from_index.1}</span>
            <span style="position: absolute; left: 6mm;">{$data.from_index.2}</span>
            <span style="position: absolute; left: 9mm;">{$data.from_index.3}</span>
            <span style="position: absolute; left: 12mm;">{$data.from_index.4}</span>
            <span style="position: absolute; left: 15mm;">{$data.from_index.5}</span>
        </div>
    </div>
</body>
</html>
