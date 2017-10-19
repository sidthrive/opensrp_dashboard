<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>OpenSRP Reporting | <?=isset($title)?"$title":""?></title>

    <!-- css -->
    <?php if(isset($css)){
        foreach($css as $c){
            echo '<link href="'.assets_url().$c.'" rel="stylesheet">'."\n";
    }} ?>
  </head>