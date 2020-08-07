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

<h2><?=$year?> <?=$month?>月份 週結清單</h2>
<div class="divider"></div>
<table border="1" id="orderTable" width="1200px" style="text-align:center">
        <tr style="background-color:#FFEBEB" id="order_header">
            <td>店家名稱</td>
       <?php $i= 1;foreach($weekArray as $week):$weekAll[$week]=0;?>
       		<td>第<?=$i++?>週(<?=$week?>)</td>
        <?php endforeach;?>
            <td>銷貨總計</td>
            <td>毛利</td>
        </tr>
<?php 
$monthAll = 0;
$profit = 0;
$i=0;
		foreach($shopData as $eachShop):
		 $monthTotal = 0;$monthProfit = 0;
	 
?>
    <tr <?=($i++%2==1)?'style="background:#EEE"':''?>>
		<td><?=$eachShop['shopInf']['name']?> </td>
		 <?php
		 // montn caculate
		 foreach($eachShop['sell'] as $week=>$each):?>
		<?php 
			$weekTotal = 0;
        	foreach($each['product'] as $row)
			{
				
				  $subtotal = $row['sellNum']*$row['sellPrice'];
				  $monthProfit += $row['sellNum']*($row['sellPrice']-$row['eachCost']);
				  $weekTotal += $subtotal;	 
			}
			$weekAll[$week] += $weekTotal;
			$monthTotal += $weekTotal;	 
        ?>
        <td><?=$weekTotal?></td>        
        <?php endforeach;?>
         <td><?=$monthTotal?></td>  
         <td><?=$monthProfit?></td>
        	        
    </tr>
      <?php 
		$monthAll += $monthTotal;
		
		$profit += $monthProfit;
	  endforeach;?>


        <tr style="background-color:#FFEBEB" id="order_header">
            <td>總計</td>
            <?php foreach($weekAll as $row):?>
            	 <td><?=$row?></td>
            <?php endforeach;?>	
            <td><?=$monthAll?></td>
            <td><?=$profit?></td>
        </tr>
    </table>
     

        
     
    
</body>
</html>