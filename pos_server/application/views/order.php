
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
 
<?php if(isset($css))echo $css;?>
<script type="text/javascript" src="/javascript/jquery.js"></script>

<script type="text/javascript" src="/javascript/pos.js"></script>
<script type="text/javascript" src="/javascript/pos_order.js?v20200312"></script>
<script type="text/javascript" src="/javascript/pos_product_query.js"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script type="text/javascript" src="/javascript/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/javascript/jquery.lightbox-0.5.min.js"></script>
<script type="text/javascript" src="/javascript/date_format.js"></script>
<script type="text/javascript" src="/javascript/pos_phantri.js"></script>
<script type="text/javascript" src="/javascript/pos_allocate.js"></script>

<script type="text/javascript" src="/javascript/jquery.tablesorter.js"></script>
<script type="text/javascript" src="/javascript/jquery.form.js"></script>  
<script type="text/javascript" src="/javascript/JSG.ImageUploader.js"></script>  

<link rel="stylesheet" type="text/css" href="/style/jquery.lightbox-0.5.css" media="screen">
<script type="text/javascript">



<?php
    $Dshift = -5;
	$date = getdate (mktime(0, 0, 0, date("m")+2,date("d")+$Dshift))



?>
var Dshift = <?=$Dshift?>;
$(document).ready(function()
{
	
	 getSuppliers()
	
})


</script>
<style>
	.productBox{
		float:left; 
		width:380px; 
		height:255px;
		padding:5px;
		margin:0px 0px 2px 3px;
		-moz-box-shadow: 0 0 5px #888; 
		-webkit-box-shadow: 0 0 5px#888; 
		box-shadow: 0 0 5px #888;	
		
	}	

	.productImgBox{
	float:left; width:100px; height:235px	
		
	}
	
	.productImg{
		 float:left; width:100px; max-height:180px	
	}
	.shoppingBox{
		
		float:left; width:280px; overflow:hidden;	
		
	}	

</style>
<style type="text/css">  
.JSGImgPreview {  
  float: left; background: url() no-repeat center 50% #FFFAD9;   
  width: 160px; height: 120px; border: solid 1px #0080FF; margin: 0 5px;  
}  
</style>  


</head>
<body  style="height:800px">
<div class="webFrame" style="background:#FFF;">

<form action="" method="post">
<input type="hidden" id="shopID" value="<?=$shopID?>">
<input type="hidden" id="myshopID" value="<?=$shopID?>">
<input type="hidden" id="select_shopID" value="<?=$shopID?>">
<input type="hidden" id="remote" value="<?=(isset($remote))?$remote:0?>">


<input type="button"  class="big_button"  value="我要訂貨"    onclick="selectType('clientOrder');"/ >
<input type="button"  class="big_button"  value="我要退貨"    onclick="backOrAdjust('backProduct',0);"/ >
<input type="button"  class="big_button"  value="點數兌換區"   onclick="changePoint()">

<input type="button"  class="big_button"  value="新品集單區"   onclick="orderCollect(true)">
<?php if($shopID<1000):?>
<a href="https://www.facebook.com/groups/1448835402012832/" target="_new">
<input type="button" class="big_button" value="調貨平台" ></a>
<input type="button"  class="big_button"  value="調出貨品" onclick="adjustSelectShop();"/ >

<?php endif;?>
<input type="button"  class="big_button"  value="訂貨中清單" onclick="showOrderList('watch',0);"/ >
<input type="button"  class="big_button"  value="訂貨處理狀況" onclick="shipmentView('shipment');"/ >

<input type="button"  class="big_button"  value="查看出貨紀錄" onclick="showShipmentList('watch',5);"/ >
<input type="button"  class="big_button"  value="查看退貨紀錄" onclick="showBOAList('Back','watch');"/ >
<input type="button"  class="big_button"  value="商品補缺件" onclick="shortProduct();"/ >

<?php if($shopID<1000):?>
<input type="button"  class="big_button"  value="查看調貨紀錄" onclick="showBOAList('Adjust','watch');"/ >
<? endif;?>
<input type="button"  class="big_button"  value="查看寄賣清單" onclick="consignmentShop()"/ >
<input type="button"  class="big_button"  value="月結紀錄" onclick="branchMonthCheck(<?=$shopID?>)"/ >

<!--
<?php if($shopID<1000):?>
<input  type="button" class="big_button" style=" background-color:#EEEE00"   value="冬季預付清單"   onclick=" seePrepay()">
<? endif;?>
-->
<input type="button"  class="big_button"  value="調整出貨地址" onclick="shippingAddress(<?=$shopID?>)"/ >
</form>
<?php if($shopData['cashType']==1):?> 
	<div>您為月結客戶，訂單滿<?=$distribute['shippingFee']?>在每周<?=$shopData['shipOut']?>固定出貨。</div>
<?php else:?>
	<div><img style="float:left;width:900px" src="/images/shipping.jpg"><img  style="float:left; width:300px" src="/images/cash.jpg"></div>
 <?php endif;?>   
 

<div style="clear:both"></div>

<div id="clientOrderQuery"></div>
<div id="product_list"><img src="/images/order.jpg" style="float:left"><img src="/images/status.png" style="float:left"></div>
    <div style="clear:both"></div>
    
<div id="newProduct">    
	<h1>新品入荷 商品限時集單區<img src="/images/newproduct.jpg"></h1>
                    <div id="orderCollect"></div>
    </div>

</div>

</div>
</body>
</html>