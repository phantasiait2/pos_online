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
<h2><?=$system['name']?> <?=$year?> <?=$month?>月份 月結清單</h2>
<div class="divider"></div>

<!--訂購品清單-->
<?php $this->load->view('sell_list',$product); ?>


<?php $consigmentTotal = 0;$backTotal = 0;$nonInvoiceTotal= 0;
	$shipmentID = 0;$adjustTotal=0;$shipmentComment = '';$otherTotal = 0;
	$outBonusTotal = 0;$inBonusTotal = 0;$webTotal = 0; $homeTotal = 0;
?>	



<?php


 if(!empty($consigmentProduct)):?>
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
    <?php ;$i=1;foreach($consigmentProduct as $row):?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    <?php 
			
			
			 $row['totalNum'] = $row['consignmentNum'] - $row['remainNum'] ;
			$subtotal = $row['totalNum']*round($row['purchasePrice']);

			
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
<?php endif;



?>
	
	
	<?php if(!empty($consigmentErrProduct)):?>
<h2>寄賣品庫存錯誤清單</h2>
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
        <td>寄賣數量</td>
        <td>銷售數量</td>
        <td>庫存數量</td>
        <td>誤差</td>
        <td>小計</td>
    </tr>
    <?php ;$i=1;foreach($consigmentErrProduct as $row):?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    <?php 
			/*
			if($row['sellNum']>$row['consignmentNum'])$row['totalNum'] = $row['consignmentNum'];
			else $row['totalNum'] = $row['sellNum'];
	
			
		  $consigmentTotal += $subtotal;	
		  */
		  	$missNum = $row['consignmentNum'] - ($row['sellNum']+$row['nowNum']);
			$subtotal = $missNum *round($row['price']*$row['purchaseCount']/100);
		  
	?>
    	<td><?=$i++?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td>?????</td>
        <td><?=$row['ZHName']?></td>
        <td><?=$row['ENGName']?></td>
        <td><?=$row['language']?></td>
        <td style="text-align:right"><?=$row['price']?></td>
        <td><?=$row['purchaseCount']?>%</td>
        <td style="text-align:right"><?=round($row['price']*$row['purchaseCount']/100)?></td>
        <td><?=$row['consignmentNum']?></td>
        <td><?=$row['sellNum']?></td>
        <td><?=$row['nowNum']?></td>
        <td><?=$missNum?></td>
        <td style="text-align:right"><?=number_format($subtotal)?></td>
    </tr>	 
    <?php endforeach;?>
    
</table>

<?php endif;?>


<?php if(!empty($backProduct)):?>
<h2>退貨總價</h2>
<table border="1" id="orderTable" width="1200px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>         
        <td>商品編號</td>
        <td>退貨日期</td>
        <td>中文</td>
        <td>英文</td>
        <td>語言</td>
        <td>定價</td>
        <td>出貨折數</td>
        <td>出貨價格</td>
         <td>退貨原因</td>
        <td>退貨數量</td>
        <td>小計</td>
        
    </tr>
    <?php ;$i=1;foreach($backProduct as $row):?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    <?php 
			$subtotal = $row['totalNum']*round($row['purchasePrice']);
			if($row['isConsignment'])$subtotal = 0;
		  $backTotal += $subtotal;	
	?>
    	<td><?=$i++?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['backTime']?></td>
        <td><?=$row['ZHName']?></td>
        <td><?=$row['ENGName']?></td>
        <td><?=$row['language']?></td>
        <td style="text-align:right"><?=$row['price']?></td>
        <td><?=($row['price']!=0)? round($row['purchasePrice']*100/$row['price']):0?>%</td>
        <td style="text-align:right">
			<?=($row['isConsignment'])?'0':round($row['purchasePrice'])?>
        </td>
        <td><?=$row['orderComment']?></td>
        <td><?=$row['totalNum']?></td>
        <td style="text-align:right"><?=number_format($subtotal)?></td>
    </tr>	 
    <?php endforeach;?>
    
</table>

<h2  style="text-align:right">退貨總價：<?=number_format($backTotal)?></h2>
<?php endif;?>
<?php if(!empty($adjustProduct)):?>
<h2>調貨總價</h2>
<table border="1" id="orderTable" width="1200px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>         
        <td>商品編號</td>
        <td>調貨日期</td>
        <td>調貨地點</td>
        <td>中文</td>
        <td>英文</td>
        <td>語言</td>
        <td>定價</td>
        <td>出貨折數</td>
        <td>出貨價格</td>
        <td>調貨原因</td>
        <td>調貨數量</td>
        <td>小計</td>
        
    </tr>
    <?php $aID = 0 ;$adjustTotal=0;$i=1;foreach($adjustProduct as $row):?>
 
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    <?php 
			$subtotal = $row['totalNum']*round($row['purchasePrice']);
			if($row['isConsignment'])$subtotal = 0;
		  $adjustTotal += $subtotal;	
	?>
    	<td><?=$i++?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['time']?></td>
         <td><?=$row['destinationName']?></td>
        <td><?=$row['ZHName']?></td>
        <td><?=$row['ENGName']?></td>
        <td><?=$row['language']?></td>
        <td style="text-align:right"><?=$row['price']?></td>
        <td><?=round($row['purchasePrice']*100/$row['price'],0)?>%</td>
        <td style="text-align:right">
			<?=($row['isConsignment'])?'0':round($row['purchasePrice'])?>
        </td>
        <td><?=$row['orderComment']?></td>
        <td><?=$row['totalNum']?></td>
        <td style="text-align:right"><?=number_format($subtotal)?></td>
    </tr>	 
    <?php endforeach;?>
    
</table>


<h2  style="text-align:right">調出貨品總價：<?=number_format($adjustTotal)?></h2>
<?php endif;?>


<?php if(!empty($web['wbOrder'])):?>
<h1>網路商城明細</h1>
<table border="1" width="1200px;" style="font-size:14pt">
<tr>
       <th style="text-align:center">取貨時間</th>
        <th style="text-align:center">訂單編號</th>
    <th style="text-align:center">姓名</th>
    <th style="text-align:center">會員編號</th>
        <th style="text-align:center">訂單金額
(A)</th>
    <th style="text-align:center">現場辦會員退差額
(B)</th>
    <th style="text-align:center">修正後訂單金額
(C)</th>
    <th style="text-align:center">商城行銷費用
(D)=(C)* 15%</th>
    <th style="text-align:center">網路付款金額
(E)
</th>
    <th style="text-align:center">刷卡費用
(F)
</th>
    <th style="text-align:center">款項合計
D+E+F
</th>
   </tr>
<?php foreach($web['wbOrder'] as $row): $webTotal-=$row['subTotal']?>
<tr  style="text-align:right">
<td><?=$row['time']?></td>  
	<td><?=$row['csOrderNum']?></td>    
  
    <td><?=$row['name']?></td>  
    <td><?=($row['newMemberID']==0)?'未加入會員':$row['newMemberID']?></td>
     <td><?=$row['total']?></td>
     <td><?=$row['diff']?></td>
     <td><?=$row['finalTotal']?></td>
     <td><?=-$row['fee']?></td> 
    <td><?=-$row['webPay']?></td>
     <td><?=-$row['creditFee']?></td>
     <td><?=-$row['subTotal']?></td>
    
    
    
    
</tr>
<?php endforeach;?>
</table>
<h2  style="text-align:right">商城店取總價：<?=number_format($webTotal)?></h2>


<?php endif;?>
    <?php  if(!empty($web['wbOrderHome'])):?>
<h1>會員宅配到府分潤</h1>
<table border="1" width="1200px;" style="font-size:14pt">
<tr>
       
        <th style="text-align:center">訂單編號</th>
    <th style="text-align:center">姓名</th>
    <th style="text-align:center">會員編號</th>
        <th style="text-align:center">訂單總額
(A)</th>
    <th style="text-align:center">店家分潤
(B)</th>
   </tr>
<?php foreach($web['wbOrderHome'] as $row):$homeTotal-=$row['profit']?>
<tr  style="text-align:right">
	<td><?=$row['csOrderNum']?></td>    
  
    <td><?=$row['name']?></td>  
    <td><?=($row['memberID']==0)?'未加入會員':$row['memberID']?></td>
     <td><?=$row['total']?></td>
     <td><?=$row['profit']?></td>
  
    
    
</tr>
<?php endforeach;?>
</table>
<h2  style="text-align:right">商城宅配總價：<?=number_format($homeTotal)?></h2>
<?php endif;?>
</div>




<?php if(!empty($outBonus)):?>
<h2>紅利成本均攤(其他店家兌換，協助攤提)</h2>
<table border="1" id="orderTable" width="1200px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>
        <td>會員編號</td>          
        <td>商品編號</td>
        <td>兌換日期</td>
        <td>兌換地點</td>
        <td>中文</td>
        <td>英文</td>
        <td>語言</td>
        <td>定價</td>
        <td>商品成本</td>
        <td>負擔成本</td>
    </tr>
    <?php $aID = 0 ;$outBonusTotal=0;$i=1;foreach($outBonus as $row):$outBonusTotal+=$row['cost'];?>
 
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    	<td><?=$i++?></td>
        <td><?=(isset($row['memberID']))?$row['memberID']:''?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['time']?></td>
        <td><?=$row['shopName']?></td>
        <td><?=$row['ZHName']?></td>
        
        <td><?=$row['ENGName']?></td>
        <td><?=$row['language']?></td>
        <td style="text-align:right"><?=$row['price']?></td>
        <td style="text-align:right">
			<?=round($row['purchasePrice'])?>
        </td>
        <td><?=$row['cost']?></td>

    </tr>	 
    <?php endforeach;?>
    
</table>
<h2  style="text-align:right">紅利協助他店攤提成本總價：<?=number_format($outBonusTotal)?></h2>
<?php endif;?>

<?php if(!empty($inBonus)):?>
<h2>紅利成本均攤(讓其他店攤)</h2>
<table border="1" id="orderTable" width="1200px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>   
        <td>會員編號</td>      
        <td>商品編號</td>
        <td>兌換日期</td>
        <td>攤提店家</td>
        <td>中文</td>
        <td>英文</td>
        <td>語言</td>
        <td>定價</td>
        <td>商品成本</td>
        <td>攤提成本</td>
    </tr>
    <?php $aID = 0 ;$inBonusTotal=0;$i=1;foreach($inBonus as $row):$inBonusTotal+=$row['cost'];?>
 
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    	<td><?=$i++?></td>
        <td><?=(isset($row['memberID']))?$row['memberID']:''?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['time']?></td>
         <td><?=$row['shopName']?></td>
        <td><?=$row['ZHName']?></td>
       
        <td><?=$row['ENGName']?></td>
        <td><?=$row['language']?></td>
        <td style="text-align:right"><?=$row['price']?></td>
        <td style="text-align:right">
				<?=round($row['purchasePrice'])?>
        </td>
        <td><?=$row['cost']?></td>

    </tr>	 
    <?php endforeach;?>
    
</table>
<h2  style="text-align:right">紅利攤提成本總價：<?=number_format($inBonusTotal)?></h2>
<?php endif;?>





<?php if(!empty($otherMoney)):?>
<h2>其他款項</h2>
<table border="1" id="orderTable" width="1200px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>         
        <td>款項原因</td>
        <td>金額</td>
    </tr>
    <?php $otherTotal=0;$i=1;foreach($otherMoney as $row):?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    <?php 
		  $otherTotal += $row['money'];	
	?>
    	<td><?=$i++?></td>
        <td><?=$row['reason']?></td>
         <td style="text-align:right"><?=number_format($row['money'])?></td>       
    </tr>	 
    <?php endforeach;?>
</table>
<h2  style="text-align:right">其他款項總價：<?=number_format($otherTotal)?></h2>
<?php endif;?>

<div class="divide"></div>
<P style="page-break-after:always">&nbsp;</P>
<h2  style="text-align:right; color:#F00">本月總計金額</h2>

<table border="1" style="width:500px; float:right;text-align:right">
	<tr style="background-color:#FFEBEB" ><th>項目</th><th>金額</th></tr>
    <tr><td>訂購品總價</td><td style="text-align:right"><?=number_format($this->monthTotal)?></td></tr>
    <tr><td>退貨總價</td><td style="text-align:right"><?=number_format(-$backTotal)?></td></tr>
    <tr><td>調出貨品總價</td><td style="text-align:right"><?=number_format(-$adjustTotal)?></td></tr>
    <tr><td>寄賣品總價</td><td style="text-align:right"><?=number_format($consigmentTotal)?></td></tr>
    <tr><td>商城店取總價</td><td style="text-align:right"><?=number_format($webTotal)?></td></tr>
    <tr><td>商城宅配總價</td><td style="text-align:right"><?=number_format($homeTotal)?></td></tr>
    
    <tr><td>紅利協助他店攤提成本總價</td><td style="text-align:right"><?=number_format($outBonusTotal)?></td></tr>
    <tr><td>紅利攤提成本總價</td><td style="text-align:right"><?=number_format(-$inBonusTotal)?></td></tr>
    
    <tr><td>其他款項總價</td><td style="text-align:right"><?=number_format($otherTotal)?></td></tr>
     <tr style="color:#F00; font-weight:bold"><th  style="text-align:right">本月應付總價</th><th style="text-align:right"><?=number_format($total = $consigmentTotal+$nonInvoiceTotal+$outBonusTotal+$this->monthTotal-$backTotal-$adjustTotal-$inBonusTotal+$otherTotal+$webTotal - $homeTotal)?></th></tr>
     <?php if(!empty($checkRecord)):?>
     	<?php $have = 0;foreach($checkRecord as $row):$have+=$row['amount']?>
           <tr>
           		<td>已結款項<?=$row['date']?></td>
          		<td><?=number_format(-$row['amount'])?></td>
           </tr>
    	<?php endforeach;?>
		<tr style="color:#F00; font-weight:bold"><th  style="text-align:right">本月尚須付款總價</th><th style="text-align:right"><?=number_format($total-$have)?></th></tr>	
    <?php endif;?>
    
</table>


<div class="divide" style=" clear:both"></div>


<?php if($printToken!=1):?>
<form action="/order/get_month_check" method="post" target="_blank">

	<input type="hidden" name="shopID" value="<?=$shopID?>"/>
    <input type="hidden" name="year" value="<?=$year?>"/>
    <input type="hidden" name="month" value="<?=$month?>"/>
    <input type="hidden" name="printToken" value="1"/>
    <input type="submit"  value="列印本頁"  class="big_button"/>

</form>
<?php endif;?>
</div>
</body>
</html>