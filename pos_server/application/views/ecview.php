<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php if (isset($title)) echo $title.'ver'.str_replace('_','.',$systemInf['version']).'｜';?>瘋桌遊</title>
<link rel="shortcut icon" href="/phantasia/images/favicon.ico"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/style/prototype.css?data=20180709" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<?php if(isset($css))echo $css;?>
<SCRIPT type="text/javascript" src="http://www.google.com/jsapi"></SCRIPT>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pos.js?date=20160414"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script type="text/javascript" src="/javascript/pos_order.js"></script>



<?php if(isset($js))echo $js;?>

</head>
<body >
<<h1></h1>
<?php foreach($platform as $row):?>
<input type="button" value="<?=$row['platformName']?>" class="big_button">
<?php endforeach;?>
</body>
</html>