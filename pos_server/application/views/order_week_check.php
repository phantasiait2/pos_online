<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php if (isset($title)) echo $title.'ver'.str_replace('_','.',$systemInf['version']).'｜';?>瘋桌遊</title>
<link rel="shortcut icon" href="/phantasia/images/favicon.ico"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />


</head>
<body  style="background-color:#FFF">
<div id="monthCheck">
<h2><?=$system['name']?> <?=$year?> <?=$month?>月份 <?=$week?>日 週結清單</h2>
<div class="divider"></div>

<?php $consigmentTotal = 0;$backTotal = 0;$nonInvoiceTotal= 0;
	$shipmentID = 0;$adjustTotal=0;$shipmentComment = '';$otherTotal = 0;
	$outBonusTotal = 0;$inBonusTotal = 0;
?>	

<h2>訂購品清單</h2>
<table border="1" id="orderTable" width="1200px" style="text-align:center">
	
    <?php 
	$this->monthTotal = 0;
	 $total = 0;$shipmentID  = 0;$shipmentComment='';
	foreach($product as $row):
	if($row['shipmentID']!=$shipmentID ) :
	
	$shipmentID  =$row['shipmentID'];
	$i=1;
	 ?> 
     <?php if($total>0):?>
     <tr height="50px" style="background-color:#FFC"><td colspan="8"> <?=$shipmentComment?></td><td colspan="3" style="text-align:right">總價:<?=number_format($total);?></td></tr>
     <?php $total = 0;endif?>
	<tr height="50px"><td colspan="11" style="text-align:left">出貨單號：s<?=$row['shippingNum'];?> 出貨日期:<?=substr($row['shippingTime'],0,16);?> </td></tr>
    <tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>
        <td>商品編號</td>
        <td>中文</td>
        <td>英文</td>
        <td>語言</td>
        <td>定價</td>
        <td>出貨折數</td>
        <td>出貨價格</td>
        <td>出貨數量</td>
        <td>小計</td>
	    <td>備註</td>
    </tr>
    <?php endif;?>

 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    <?php $subtotal = $row['sellNum']*$row['sellPrice'];
		  $this->monthTotal += $subtotal;	
		  $total += $subtotal;
		  $shipmentComment = $row['shipmentComment'];
	?>
    	<td><?=$i++?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['ZHName']?></td>
        <td><?=$row['ENGName']?></td>
        <td><?=$row['language']?></td>
        <td style="text-align:right"><?=$row['price']?></td>
        <td><?=($row['price']!=0)?round($row['sellPrice']*100/$row['price']):'0'?>%</td>
        <td style="text-align:right"><?=$row['sellPrice']?></td>
        <td><?=$row['sellNum']?></td>
        <td style="text-align:right"><?=number_format($subtotal)?></td>
	    <td><?=$row['comment']?></td> 
    </tr>	 
    <?php endforeach;?>
     <tr height="50px" style="background-color:#FFC"><td colspan="8"> <?=$shipmentComment?></td><td colspan="3" style="text-align:right">總價:<?=number_format($total);?></td></tr>
</table>
<h2  style="text-align:right">本週應結訂購品總價：<?=number_format($this->monthTotal)?></h2>
<div class="divider"></div>

<div class="divide"></div>
<P style="page-break-after:always">&nbsp;</P>
<h2  style="text-align:right; color:#F00">本月已付金額明細</h2>

<table border="1" style="width:500px; float:right;text-align:right">
	<tr style="background-color:#FFEBEB" ><th>項目</th><th>金額</th></tr>
     <?php if(!empty($checkRecord)):?>
     	<?php $have = 0;foreach($checkRecord as $row):$have+=$row['amount']?>
           <tr>
           		<td>已結款項<?=$row['date']?></td>
          		<td><?=number_format($row['amount'])?></td>
           </tr>
    	<?php endforeach;?>
    <?php endif;?>   
</table>


<div class="divide" style=" clear:both"></div>


<?php if($printToken!=1):?>
<form action="/order/get_week_check" method="post" target="_blank">

	<input type="hidden" name="shopID" value="<?=$shopID?>"/>
    <input type="hidden" name="year" value="<?=$year?>"/>
    <input type="hidden" name="month" value="<?=$month?>"/>
     <input type="hidden" name="week" value="<?=$week?>"/>
    <input type="hidden" name="printToken" value="1"/>
    <input type="submit"  value="列印本頁"  class="big_button"/>

</form>
<?php endif;?>
</div>
</body>
</html>