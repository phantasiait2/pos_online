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
<h2><?=$consignment[$from]['system']['name']?> <?=$from?>~<?=$to?>寄賣結帳總表</h2>
<div class="divider"></div>




<?php


 if(!empty($consignment)):?>
<h2>寄賣品總價</h2>
<table border="1" id="orderTable" width="1200px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>         
        <td>商品編號</td>
        <td>銷售日期</td>
        <td>中文</td>
        <td>英文</td>
        <td>語言</td>
        <td>定價</td>
        <td>出貨折數</td>
        <td>出貨價格</td>
        <td>銷售數量</td>
        <td>小計</td>
    </tr>
    <?php ;$i=1;
    $consigmentTotal = 0;
    foreach($consignment as $each)
        if(!empty($each['consigmentProduct']))
    foreach($each['consigmentProduct'] as $row):?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    <?php 
			
			
			 $row['totalNum'] = $row['consignmentNum'] - $row['remainNum'] ;
			$subtotal = $row['totalNum']*round($row['purchasePrice']);
            if(!isset( $productList[$row['productID']]['sellNum'])) $productList[$row['productID']]['sellNum'] = 0;
			     $productList[$row['productID']]['sellNum'] += $row['totalNum'];
		  $consigmentTotal += $subtotal;	
		
		  
	?>
    	<td><?=$i++?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['timeStr']?></td>
        <td><?=$row['ZHName']?></td>
        <td><?=$row['ENGName']?></td>
        <td><?=$row['language']?></td>
        <td style="text-align:right"><?=$row['price']?></td>
        <td><?=$row['purchaseCount']?>%</td>
        <td style="text-align:right"><?=$row['purchasePrice']?></td>
        <td><?=$row['totalNum']?></td>

        <td style="text-align:right"><?=number_format($subtotal)?></td>
    </tr>	 
    <?php endforeach;?>
    
</table>
<h2  style="text-align:right">寄賣品總價：<?=number_format($consigmentTotal)?></h2>
<?php endif;?>


<table  border="1"  width="1200px" style="text-align:center">
    <tr style="background-color:#FFEBEB" id="order_header">
        <td>品名</td>
        <td>寄賣數量</td>
        <td>結算數量</td>
         <td>理論寄賣</td>
        <td>店內數量</td>
        <td>紀錄資料</td>
    </tr>

<?php $i = 0;
    
    foreach($productList as $row):
            if(isset($row['inf'])):?>
    

            
         	<tr <?=($i++%2==0)?'style="background:#EEE"':''?>>
        <td> <?=$row['inf']['ZHName']?></td>
        <td>  <?=$row['consignmentNum']?></td>
        <td><?=$row['sellNum']?></td>
        <td><?=$row['consignmentNum']-$row['sellNum']?></td>
        <td><?=$row['nowNum']?></td>
        <td>
        <input type="button" class="patchBtnR" value="依實際數量補登" onclick="consignmentPatch(<?=$row['inf']['productID']?>,<?=$row['consignmentNum']?>,<?=$shopID?>)">
        
        
        <?php if($row['consignmentNum']-$row['sellNum']>=$row['nowNum']):?>
        <input type="button" class="patchBtn" value="依店內數量補登" onclick="consignmentPatch(<?=$row['inf']['productID']?>,<?=$row['nowNum']?>,<?=$shopID?>)">
        <?php endif;?>
        </td>
    </tr>
         
          
        
    <?php endif;?>
<?php endforeach;?>
	

    </table>
<input type="button" value="全部依實際寄賣數量補登" onclick="$('.patchBtnR').click();"/>
 <input type="button" value="全部依店內數量補登" onclick="$('.patchBtn').click();"/>
<?php if($printToken!=1):?>
<form action="/order/get_consignment_record" method="post" target="_blank">

	<input type="hidden" name="shopID" value="<?=$shopID?>"/>
    <input type="hidden" name="monthFromDate" value="<?=$from?>"/>
    <input type="hidden" name="monthToDate" value="<?=$to?>"/>
    <input type="hidden" name="printToken" value="1"/>
    <input type="submit"  value="列印本頁"  class="big_button"/>

</form>
<?php endif;?>
</div>
</body>
</html>