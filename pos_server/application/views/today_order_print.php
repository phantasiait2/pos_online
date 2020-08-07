<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pos_order.js"></script>

<script type="text/javascript">
$(document).ready(function()
{
	
		todayOrder(<?=$type?>,"<?=$date?>");
		
})
</script>
</head>
<body>

<div class="product" style="width:1100px; ">
<input type="button" value="go" onclick="todayOrder(<?=$type?>,'<?=$date?>');">
</div>
<div style="clear:both"></div>
</body>
</html>