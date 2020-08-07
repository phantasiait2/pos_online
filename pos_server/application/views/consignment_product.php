<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>寄賣商品結算</title>
</head>

<body>
<h1><?=$year.'-'.$month.'  '.$product[0]['suppliers']?></h1>

<table border="1">
	<tr>
    	<td>產品編號</td>
        <td>中文</td>
        <td>英文</td>
        <td>語言</td>
        <td>前期寄賣數量</td>
        <td>本期新增數量</td>
        <td>本期剩餘數量</td>
        <td>本期賣出數量</td>
        <td>售價</td>
        <td>批發單價</td>
        <td>本期應結小計</td>    
    </tr>

	<?php $total = 0;foreach($product as $row):?>
    <tr>
    	<td><?=$row['productNum']?></td>
        <td><?=$row['ZHName']?></td>
        <td><?=$row['ENGName']?></td>
        <td><?=$row['language']?></td>
        <td><?=$row['lastConsignmentNum']?></td>
        <td><?=$row['newNum']?></td>
        <td><?=$row['thisConsignmentNum']?></td>
        <td><?=$sellNum = ($row['lastConsignmentNum']+$row['newNum'])-$row['thisConsignmentNum']?></td>
        <td><?=$row['price']?></td>
        <td><?=$buyPrice = round($row['price']*($row['buyDiscount']*1.05)/100,0)?></td>
        <td><?=$sellNum*$buyPrice?></td>    
    </tr>
    
    
    
    <?php $total+=$sellNum*$buyPrice;endforeach;?>
    


</table>

<h2>本期總金額為<?=$total?></h2>





</body>
</html>