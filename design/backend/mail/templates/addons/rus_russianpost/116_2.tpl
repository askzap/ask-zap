{if $data.print_bg == 'Y'}
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
        <img style="width: 141mm; height: 200mm;" src="{$images_dir}/addons/rus_russianpost/116_2.png">
       </div>
    </div>
</body>
</html>
{/if}