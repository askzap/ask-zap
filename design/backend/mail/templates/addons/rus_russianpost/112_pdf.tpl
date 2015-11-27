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

<body style="width: {$addons.rus_russianpost.112_list_width}mm; height: {$addons.rus_russianpost.112_list_height}mm;">
    <div style="width: {$addons.rus_russianpost.112_list_width}mm; height: {$addons.rus_russianpost.112_list_height}mm; position: relative;" >
        {if $data.print_bg == 'Y'}
        <img style="width: 200mm; height: 283mm;" src="{$images_dir}/addons/rus_russianpost/112.jpg">
        {/if}

        <span style="position: absolute;height: 6.5mm;width: 10mm;top: 62mm;left: 6mm;text-align: center;font: 25pt 'Arial';">&#10004;</span>
        <span style="position: absolute;height: 6.5mm;width: 140mm;top: 52mm;left: 54mm;text-align: center;font: 11pt 'Arial';">{$data.total}</span>
        <span style="position: absolute;height: 6.5mm;width: 18mm;top: 56mm;left: 9mm;text-align: center;font: 11pt 'Arial';">{$data.total_rub}</span>
        <span style="position: absolute;height: 6.5mm;width: 10mm;top: 56mm;left: 33mm;text-align: center;font: 11pt 'Arial';">{$data.total_kop}</span>
        {if $data.sms_for_sender == 'Y'}
            <div style="position: absolute;height: 4.5mm;width: 38mm;top: 60.5mm;left: 155.7mm;font: 11pt 'Arial';margin: 0; ">
                <span style="position: absolute; left: 0mm;">{$data.company_phone.0}</span>
                <span style="position: absolute; left: 4mm;">{$data.company_phone.1}</span>
                <span style="position: absolute; left: 8mm;">{$data.company_phone.2}</span>
                <span style="position: absolute; left: 12mm;">{$data.company_phone.3}</span>
                <span style="position: absolute; left: 16mm;">{$data.company_phone.4}</span>
                <span style="position: absolute; left: 20mm;">{$data.company_phone.5}</span>
                <span style="position: absolute; left: 24mm;">{$data.company_phone.6}</span>
                <span style="position: absolute; left: 28mm;">{$data.company_phone.7}</span>
                <span style="position: absolute; left: 32mm;">{$data.company_phone.8}</span>
                <span style="position: absolute; left: 36mm;">{$data.company_phone.9}</span>
            </div>
        {/if}
        {if $data.sms_for_recepient == 'Y'}
            <div style="position: absolute;height: 4.5mm;width: 38mm;top: 66.5mm;left: 155.7mm;font: 11pt 'Arial';margin: 0; ">
                <span style="position: absolute; left: 0mm;">{$data.recipient_phone.0}</span>
                <span style="position: absolute; left: 4mm;">{$data.recipient_phone.1}</span>
                <span style="position: absolute; left: 8mm;">{$data.recipient_phone.2}</span>
                <span style="position: absolute; left: 12mm;">{$data.recipient_phone.3}</span>
                <span style="position: absolute; left: 16mm;">{$data.recipient_phone.4}</span>
                <span style="position: absolute; left: 20mm;">{$data.recipient_phone.5}</span>
                <span style="position: absolute; left: 24mm;">{$data.recipient_phone.6}</span>
                <span style="position: absolute; left: 28mm;">{$data.recipient_phone.7}</span>
                <span style="position: absolute; left: 32mm;">{$data.recipient_phone.8}</span>
                <span style="position: absolute; left: 36mm;">{$data.recipient_phone.9}</span>
            </div>
        {/if}
        <span style="position: absolute;height: 4.5mm;width: 192mm;top: 73mm;left: 2mm;font: 11pt 'Arial';text-indent: 16mm;line-height: 15pt;">{if $data.sender == '1'}{$data.whom}{else}{$data.fio}{/if}</span>
        <span style="position: absolute;height: 5.5mm;width: 188mm;top: 79.5mm;left: 6mm;font: 11pt 'Arial';text-indent: 12mm;line-height: 18pt;">{if $data.sender == '1'}{$data.where}{else}{$data.fiz_addres}{/if}</span>
        <span style="position: absolute;height: 5.5mm;width: 157mm;top: 86mm;left: 6mm;font: 11pt 'Arial';text-indent: 0mm;line-height: 18pt;">{if $data.sender == '1'}{$data.where2}{else}{$data.fiz_addres2}{/if}</span>
        <span style="position: absolute;height: 5.5mm;width: 31mm;top: 86.5mm;left: 165.5mm;font: 12pt 'Arial';letter-spacing: 7.6pt;">
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

        <div style="position: absolute;height: 4.5mm;width: 55mm;top: 94.5mm;left: 31.7mm;font: 11pt 'Arial';margin: 0; ">
            <span style="position: absolute; left: 0mm;">{$data.text1.0}</span>
            <span style="position: absolute; left: 5mm;">{$data.text1.1}</span>
            <span style="position: absolute; left: 9mm;">{$data.text1.2}</span>
            <span style="position: absolute; left: 14mm;">{$data.text1.3}</span>
            <span style="position: absolute; left: 19mm;">{$data.text1.4}</span>
            <span style="position: absolute; left: 23mm;">{$data.text1.5}</span>
            <span style="position: absolute; left: 28mm;">{$data.text1.6}</span>
            <span style="position: absolute; left: 33mm;">{$data.text1.7}</span>
            <span style="position: absolute; left: 37mm;">{$data.text1.8}</span>
            <span style="position: absolute; left: 42mm;">{$data.text1.9}</span>
            <span style="position: absolute; left: 47mm;">{$data.text1.10}</span>
            <span style="position: absolute; left: 51mm;">{$data.text1.11}</span>
            <span style="position: absolute; left: 56mm;">{$data.text1.12}</span>
            <span style="position: absolute; left: 61mm;">{$data.text1.13}</span>
            <span style="position: absolute; left: 66mm;">{$data.text1.14}</span>
            <span style="position: absolute; left: 71mm;">{$data.text1.15}</span>
            <span style="position: absolute; left: 75mm;">{$data.text1.16}</span>
            <span style="position: absolute; left: 80mm;">{$data.text1.17}</span>
            <span style="position: absolute; left: 85mm;">{$data.text1.18}</span>
            <span style="position: absolute; left: 89mm;">{$data.text1.19}</span>
            <span style="position: absolute; left: 94mm;">{$data.text1.20}</span>
            <span style="position: absolute; left: 99mm;">{$data.text1.21}</span>
            <span style="position: absolute; left: 103mm;">{$data.text1.22}</span>
            <span style="position: absolute; left: 108mm;">{$data.text1.23}</span>
            <span style="position: absolute; left: 113mm;">{$data.text1.24}</span>
            <span style="position: absolute; left: 117mm;">{$data.text1.25}</span>
            <span style="position: absolute; left: 122mm;">{$data.text1.26}</span>
            <span style="position: absolute; left: 127mm;">{$data.text1.27}</span>
            <span style="position: absolute; left: 131mm;">{$data.text1.28}</span>
            <span style="position: absolute; left: 136mm;">{$data.text1.29}</span>
            <span style="position: absolute; left: 141mm;">{$data.text1.30}</span>
            <span style="position: absolute; left: 145mm;">{$data.text1.31}</span>
            <span style="position: absolute; left: 150mm;">{$data.text1.32}</span>
            <span style="position: absolute; left: 155mm;">{$data.text1.33}</span>
            <span style="position: absolute; left: 159mm;">{$data.text1.34}</span>
        </div>

        <div style="position: absolute;height: 4.5mm;width: 55mm;top: 102mm;left: 31.7mm;font: 11pt 'Arial';margin: 0; ">
            <span style="position: absolute; left: 0mm;">{$data.text2.0}</span>
            <span style="position: absolute; left: 5mm;">{$data.text2.1}</span>
            <span style="position: absolute; left: 9mm;">{$data.text2.2}</span>
            <span style="position: absolute; left: 14mm;">{$data.text2.3}</span>
            <span style="position: absolute; left: 19mm;">{$data.text2.4}</span>
            <span style="position: absolute; left: 23mm;">{$data.text2.5}</span>
            <span style="position: absolute; left: 28mm;">{$data.text2.6}</span>
            <span style="position: absolute; left: 33mm;">{$data.text2.7}</span>
            <span style="position: absolute; left: 37mm;">{$data.text2.8}</span>
            <span style="position: absolute; left: 42mm;">{$data.text2.9}</span>
            <span style="position: absolute; left: 47mm;">{$data.text2.10}</span>
            <span style="position: absolute; left: 51mm;">{$data.text2.11}</span>
            <span style="position: absolute; left: 56mm;">{$data.text2.12}</span>
            <span style="position: absolute; left: 61mm;">{$data.text2.13}</span>
            <span style="position: absolute; left: 66mm;">{$data.text2.14}</span>
            <span style="position: absolute; left: 71mm;">{$data.text2.15}</span>
            <span style="position: absolute; left: 75mm;">{$data.text2.16}</span>
            <span style="position: absolute; left: 80mm;">{$data.text2.17}</span>
            <span style="position: absolute; left: 85mm;">{$data.text2.18}</span>
            <span style="position: absolute; left: 89mm;">{$data.text2.19}</span>
            <span style="position: absolute; left: 94mm;">{$data.text2.20}</span>
            <span style="position: absolute; left: 99mm;">{$data.text2.21}</span>
            <span style="position: absolute; left: 103mm;">{$data.text2.22}</span>
            <span style="position: absolute; left: 108mm;">{$data.text2.23}</span>
            <span style="position: absolute; left: 113mm;">{$data.text2.24}</span>
            <span style="position: absolute; left: 117mm;">{$data.text2.25}</span>
            <span style="position: absolute; left: 122mm;">{$data.text2.26}</span>
            <span style="position: absolute; left: 127mm;">{$data.text2.27}</span>
            <span style="position: absolute; left: 131mm;">{$data.text2.28}</span>
            <span style="position: absolute; left: 136mm;">{$data.text2.29}</span>
            <span style="position: absolute; left: 141mm;">{$data.text2.30}</span>
            <span style="position: absolute; left: 145mm;">{$data.text2.31}</span>
            <span style="position: absolute; left: 150mm;">{$data.text2.32}</span>
            <span style="position: absolute; left: 155mm;">{$data.text2.33}</span>
            <span style="position: absolute; left: 159mm;">{$data.text2.34}</span>
        </div>

        <div style="position: absolute;height: 4.5mm;width: 55mm;top: 112mm;left: 19.7mm;font: 11pt 'Arial';margin: 0; ">
            <span style="position: absolute; left: 0mm;">{$data.inn.0}</span>
            <span style="position: absolute; left: 5mm;">{$data.inn.1}</span>
            <span style="position: absolute; left: 9mm;">{$data.inn.2}</span>
            <span style="position: absolute; left: 14mm;">{$data.inn.3}</span>
            <span style="position: absolute; left: 19mm;">{$data.inn.4}</span>
            <span style="position: absolute; left: 23mm;">{$data.inn.5}</span>
            <span style="position: absolute; left: 28mm;">{$data.inn.6}</span>
            <span style="position: absolute; left: 33mm;">{$data.inn.7}</span>
            <span style="position: absolute; left: 37mm;">{$data.inn.8}</span>
            <span style="position: absolute; left: 42mm;">{$data.inn.9}</span>
            <span style="position: absolute; left: 46mm;">{$data.inn.10}</span>
            <span style="position: absolute; left: 51mm;">{$data.inn.11}</span>
        </div>

        <div style="position: absolute;height: 4.5mm;width: 91mm;top: 112mm;left: 102.5mm;font: 11pt 'Arial';margin: 0; ">
            <span style="position: absolute; left: 0mm;">{$data.kor.0}</span>
            <span style="position: absolute; left: 4mm;">{$data.kor.1}</span>
            <span style="position: absolute; left: 9mm;">{$data.kor.2}</span>
            <span style="position: absolute; left: 14mm;">{$data.kor.3}</span>
            <span style="position: absolute; left: 18mm;">{$data.kor.4}</span>
            <span style="position: absolute; left: 23mm;">{$data.kor.5}</span>
            <span style="position: absolute; left: 28mm;">{$data.kor.6}</span>
            <span style="position: absolute; left: 32mm;">{$data.kor.7}</span>
            <span style="position: absolute; left: 37mm;">{$data.kor.8}</span>
            <span style="position: absolute; left: 42mm;">{$data.kor.9}</span>
            <span style="position: absolute; left: 47mm;">{$data.kor.10}</span>
            <span style="position: absolute; left: 51mm;">{$data.kor.11}</span>
            <span style="position: absolute; left: 56mm;">{$data.kor.12}</span>
            <span style="position: absolute; left: 61mm;">{$data.kor.13}</span>
            <span style="position: absolute; left: 65mm;">{$data.kor.14}</span>
            <span style="position: absolute; left: 70mm;">{$data.kor.15}</span>
            <span style="position: absolute; left: 75mm;">{$data.kor.16}</span>
            <span style="position: absolute; left: 79mm;">{$data.kor.17}</span>
            <span style="position: absolute; left: 84mm;">{$data.kor.18}</span>
            <span style="position: absolute; left: 88mm;">{$data.kor.19}</span>
        </div>

        <div style="position: absolute;height: 4.5mm;width: 145mm;top: 118.5mm;left: 49mm;font: 11pt 'Arial';margin: 0;">{$data.bank}</div>

        <div style="position: absolute;height: 4.5mm;width: 100mm;top: 124mm;left: 26.5mm;font: 11pt 'Arial';margin: 0; ">
            <span style="position: absolute; left: 0mm;">{$data.ras.0}</span>
            <span style="position: absolute; left: 5mm;">{$data.ras.1}</span>
            <span style="position: absolute; left: 9mm;">{$data.ras.2}</span>
            <span style="position: absolute; left: 14mm;">{$data.ras.3}</span>
            <span style="position: absolute; left: 19mm;">{$data.ras.4}</span>
            <span style="position: absolute; left: 23mm;">{$data.ras.5}</span>
            <span style="position: absolute; left: 28mm;">{$data.ras.6}</span>
            <span style="position: absolute; left: 33mm;">{$data.ras.7}</span>
            <span style="position: absolute; left: 37mm;">{$data.ras.8}</span>
            <span style="position: absolute; left: 42mm;">{$data.ras.9}</span>
            <span style="position: absolute; left: 47mm;">{$data.ras.10}</span>
            <span style="position: absolute; left: 51mm;">{$data.ras.11}</span>
            <span style="position: absolute; left: 56mm;">{$data.ras.12}</span>
            <span style="position: absolute; left: 61mm;">{$data.ras.13}</span>
            <span style="position: absolute; left: 65mm;">{$data.ras.14}</span>
            <span style="position: absolute; left: 70mm;">{$data.ras.15}</span>
            <span style="position: absolute; left: 75mm;">{$data.ras.16}</span>
            <span style="position: absolute; left: 79mm;">{$data.ras.17}</span>
            <span style="position: absolute; left: 84mm;">{$data.ras.18}</span>
            <span style="position: absolute; left: 89mm;">{$data.ras.19}</span>
        </div>

        <div style="position: absolute;height: 4.5mm;width: 40mm;top: 124mm;left: 153.5mm;font: 11pt 'Arial';margin: 0; ">
            <span style="position: absolute; left: 0mm;">{$data.bik.0}</span>
            <span style="position: absolute; left: 5mm;">{$data.bik.1}</span>
            <span style="position: absolute; left: 9mm;">{$data.bik.2}</span>
            <span style="position: absolute; left: 14mm;">{$data.bik.3}</span>
            <span style="position: absolute; left: 19mm;">{$data.bik.4}</span>
            <span style="position: absolute; left: 23mm;">{$data.bik.5}</span>
            <span style="position: absolute; left: 28mm;">{$data.bik.6}</span>
            <span style="position: absolute; left: 33mm;">{$data.bik.7}</span>
            <span style="position: absolute; left: 37mm;">{$data.bik.8}</span>
        </div>

        <span style="position: absolute;height: 5.5mm;width: 106mm;top: 129mm;left: 12mm;font: 11pt 'Arial';text-indent: 12mm;line-height: 18pt;">{$data.from_whom}</span>

        <span style="position: absolute;height: 5.5mm;width: 75mm;top: 129mm;left: 119mm;font: 11pt 'Arial';text-indent: 12mm;line-height: 18pt;">{$data.from_whom2}</span>

        <span style="position: absolute;height: 5.5mm;width: 188mm;top: 136mm;left: 6mm;font: 11pt 'Arial';text-indent: 40mm;line-height: 18pt;">{$data.sender_address2}</span>

        <span style="position: absolute;height: 5.5mm;width: 164mm;top: 143mm;left: 0mm;font: 11pt 'Arial';text-indent: 7mm;line-height: 18pt;">{$data.sender_address}</span>

        <span style="position: absolute;height: 5.5mm;width: 31mm;top: 143.5mm;left: 166mm;font: 12pt 'Arial';letter-spacing: 7.6pt;">
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