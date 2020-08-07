<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>
<link rel="stylesheet" type="text/css" href="/style/pos.css?data=20180709" />
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pos_product_query.js"></script>
<script type="text/javascript" src="/javascript/pos_order.js?t=8"></script>
<script type="text/javascript" src="/javascript/pos.js?date=20160414"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>

<script type="text/javascript">
$(document).ready(function()
{
	 queryProduct('orderChk','select');
		$('#orderChk_hide').attr("checked", "checked");;
})
</script>
</head>
<body>

    <h3>裝箱檢核系統</h3>
    <h2>請輸入訂單號碼：<input type="number" id="shippingNum" class="big_text" onkeyup="if(enter())checkShipment()">
    <input type="button" class="big_button" value="確認" onclick="checkShipment()"></h2>
    
    <h1 >正在裝箱的訂單號：<span id="nowShip"></span></h1>
    <h3>裝箱時間：<span id="boxTime"></span></h3>
    <div id="orderInf"></div>
<div class="mainContent" style="width:1100px; ">
 
<div id="orderChkQuery"></div>


<div id="orderView">
</div>




    <div id="orderErr" style="display:none"><h1>錯誤商品清單：</h1></div>

    <div id="sound"></div>
</div>


<div style="clear:both"></div>
<input  type="button" style="" value="-" onclick="sameAll()" >
</body>
</html>