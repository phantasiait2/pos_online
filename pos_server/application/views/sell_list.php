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
     
     <tr height="50px" style="background-color:#FFC"><td colspan="8"> <?=$shipmentComment?></td><td colspan="3" style="text-align:right">總價:<?=number_format($total);?></td></tr>
     <?php $total = 0;?>
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
<h2  style="text-align:right">訂購品總價：<?=number_format($this->monthTotal)?></h2>
<div class="divider"></div>