<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>庫存精靈</title>
<style>
 .odd{
	 
}
.even{
	background-color:#CFC;
	
	
}
.mytable tr:hover{
	 background-color:#FCF;
	 
}

</style>
</head>

<body>


<a href="http://shipment.phantasia.com.tw/product/stock_agent">http://shipment.phantasia.com.tw/product/stock_agent</a>
<h1>Top清單內 應訂</h1>
<table border="1" class="mytable" >
<?php 
$s = '';$i = 1;
foreach($product as $row):?>
	<?php
		if($row['shouldOrder']<=0 ||(isset( $row['pre']['num'])&& $row['pre']['num']>$row['shouldOrder']) )continue;
	 if($s!=$row['supplierName']):
    $s = $row['supplierName']
    
    ?>
    <tr style="background-color:#F60">
    <th colspan="12"><?=$row['supplierName']?>   到貨天數：<?=$row['day']?></th>
    </tr>
    <tr>
        <th>序號</th>
        <th>編號</th>
        <th>產品名稱</th>
        <th>語言</th>
        <th>配完庫存</th>
        <th>近三月出貨</th>
        <th>近三月賣出</th>
        <th>成箱數</th>
        <th>應訂箱數</th>
        <th>進貨折數</th>
        <th>已定數量</th>
        <th>到貨日期</th>
     </tr>
     <?php endif;?>  
    <tr  class="<?=($i%2==0)?'even':'odd'?>"  >
        <td><?=$i++?></td>
        <td><?=$row['productNum']?></td>
       <td><?=$row['ZHName']?></td>
        <td><?=$row['language']?></td>
       <td><?=$row['comNum']?></td>
        <td><?=$row['orderNum']?></td>
       <td><?=$row['sellNum']?></td>
       <td><?=$row['case']?></td>
       <td><?=$row['shouldOrder']?></td>
        <td><?=round($row['purchasePrice']*100/$row['price'],0)/100?></td>
        <td><?=isset($row['pre']['preTime'])?$row['pre']['num']:''?></td>
        <td>
		<?php if(isset($row['pre']['preTime']))
			  {
					if($row['pre']['preTime']=='7777-07-07')	echo '<span style=" color:red">廠商缺貨</span>';
					else if($row['pre']['preTime']=='3333-03-03') echo '<span style=" color:blue">暫不進貨</span>';
					else if($row['pre']['preTime']=='6666-06-06') echo '<span style=" color:blue">規劃中</span>';
					
					else if($row['pre']['preTime']=='9999-09-09') echo '<span style=" color:red">廠商缺貨</span>';
					else echo $row['pre']['preTime'];
			   }
		?>
		</td>
    </tr>

<?php endforeach;?>



</table>



<h1>非Top清單內 應訂</h1>
<table border="1" class="mytable" >
<?php 
$s = '--';$i = 1;
foreach($notProduct as $row):?>
	<?php
		if(!(isset($row['comNum'])) || $row['comNum']>=0  )continue;
	 if($s!=$row['supplierName']):
    $s = $row['supplierName']
    
    ?>
    <tr style="background-color:#F60">
    <th colspan="12"><?=$row['supplierName']?>   到貨天數：<?=$row['day']?></th>
    </tr>
    <tr>
        <th>序號</th>
        <th>編號</th>
        <th>產品名稱</th>
        <th>語言</th>
        <th>配完庫存</th>
        <th>近三月出貨</th>
        <th>近三月賣出</th>
        <th>成箱數</th>
        <th>應訂箱數</th>
        <th>進貨折數</th>
         <th>已定數量</th>
        <th>到貨日期</th>
     </tr>
     <?php endif;?>  
    <tr  class="<?=($i%2==0)?'even':'odd'?>" >
        <td><?=$i++?></td>
        <td><?=$row['productNum']?></td>
       <td><?=$row['ZHName']?></td>
        <td><?=$row['language']?></td>
       <td><?=$row['comNum']?></td>
        <td><?=$row['orderNum']?></td>
       <td><?=$row['sellNum']?></td>
       <td><?=$row['case']?></td>
       <td><?=$row['shouldOrder']?></td>
        <td><?=($row['price']!=0)?round($row['purchasePrice']*100/$row['price'],0)/100:0?></td>
       <td><?=isset($row['pre']['preTime'])?$row['pre']['num']:''?></td>
        <td>
		<?php if(isset($row['pre']['preTime']))
			  {
					if($row['pre']['preTime']=='7777-07-07')	echo '<span style=" color:red">廠商缺貨</span>';
					else if($row['pre']['preTime']=='3333-03-03') echo '<span style=" color:blue">暫不進貨</span>';
					else if($row['pre']['preTime']=='9999-09-09') echo '<span style=" color:red">廠商缺貨</span>';
					else echo $row['pre']['preTime'];
			   }
		?>
		</td>

    </tr>

<?php endforeach;?>



</table>


</body>
</html>