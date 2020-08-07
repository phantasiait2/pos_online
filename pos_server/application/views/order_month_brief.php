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

<h2><?=$year?> <?=$month?>月份 月結清單</h2>
<div class="divider"></div>
<table border="1" id="orderTable" width="1200px" style="text-align:center">
        <tr style="background-color:#FFEBEB" id="order_header">
            <td>店家名稱</td>
            <td>月結商品</td>
   
            <td>寄賣商品</td>
            <td>退貨商品</td>
            <td>調貨商品</td>
            <td>銷貨總計</td>
            <td>其他款項</td>
            <td>實收金額</td>
            <td>毛利</td>
        </tr>
<?php 
$monthAll = 0;
$consigmentAll=0;
$backAll=  0;
$adjustAll = 0;
$otherAll =  0;
$profit = 0;
$i=0;
$nonInvoiceAll = 0;
		foreach($shopData as $eachShop):
		 $monthTotal = 0;$consigmentTotal = 0;$backTotal = 0;$monthProfit = 0;$nonInvoiceTotal=0;$adjustTotal = 0;$otherTotal=0;
	 
?>
    <tr <?=($i++%2==1)?'style="background:#EEE"':''?>>
		<td><?=$eachShop['shopInf']['name']?> </td>
		 <?php
		 // montn caculate
        foreach($eachShop['sell']['product'] as $row)
		{
			if($row['nonInvoice']==1) 
				{
					$eachShop['sell']['nonInvoiceProduct'][] = $row;
					continue;
				}					 
			 
			  $subtotal = $row['sellNum']*$row['sellPrice'];
			  $monthProfit += $row['sellNum']*($row['sellPrice']-$row['eachCost']);
              $monthTotal += $subtotal;	 
		}
        ?>

		 <?php
		 // montn caculate
		 if(isset($eachShop['sell']['nonInvoiceProduct']))
        foreach($eachShop['sell']['nonInvoiceProduct'] as $row)
		{
			  $subtotal = $row['sellNum']*$row['sellPrice'];
			  $monthProfit += $row['sellNum']*($row['sellPrice']-$row['eachCost']);
              $nonInvoiceTotal += $subtotal;	 
		}
        ?>
        <td><?=$monthTotal+$nonInvoiceTotal?></td>        
        <?php
		if(isset($eachShop['sell']['consigmentProduct'] ))
			foreach($eachShop['sell']['consigmentProduct'] as $row)
			{
					
					 $row['totalNum'] = $row['consignmentNum'] - $row['remainNum'] ;
					$subtotal = $row['totalNum']*round($row['purchasePrice']);
					$monthProfit+= $row['totalNum']*(round($row['purchasePrice'])-$row['avgCost']);
					$consigmentTotal += $subtotal;	
			}
		?>
        <td><?=$consigmentTotal?></td>
   		<?php 
	   foreach($eachShop['sell']['backProduct'] as $row)
			{
					 if($row['isConsignment'])$subtotal = 0;
					 else
					 {
						$subtotal = $row['totalNum']*round($row['purchasePrice']);
						$monthProfit-=$row['totalNum']*(round($row['purchasePrice'])-$row['buyPrice']);
					 }
				  $backTotal += $subtotal;	
			}
        ?>
		<td><?=$backTotal?></td>
			<?php 
           foreach($eachShop['sell']['adjustProduct'] as $row)
                {
                         if($row['isConsignment'])$subtotal = 0;
                         else
                         {
                            $subtotal = $row['totalNum']*round($row['purchasePrice']);
                            $monthProfit-=$row['totalNum']*(round($row['purchasePrice'])-$row['buyPrice']);
                         }
                      $adjustTotal += $subtotal;	
                }
            ?>
            <td><?=$adjustTotal?></td>
        <?php 
           foreach($eachShop['sell']['otherMoney'] as $row)
                {
                        
                        
                        
                      $otherTotal += $row['money'];	
                }
            ?>
               <td><?=$monthTotal+$nonInvoiceTotal+$consigmentTotal-$backTotal-$adjustTotal ?> </td>
            <td><?=$otherTotal?></td>
        
        
         <td><?=$monthTotal+$nonInvoiceTotal+$consigmentTotal-$backTotal-$adjustTotal+$otherTotal ?> </td>
         <td><?=$monthProfit?></td>
        	        
    </tr>
      <?php 
		$monthAll += $monthTotal;
		$nonInvoiceAll += $nonInvoiceTotal;
		$consigmentAll += $consigmentTotal;
		$backAll +=  $backTotal;
		$adjustAll +=  $adjustTotal;
		$otherAll +=  $otherTotal;
		$profit += $monthProfit;
	  endforeach;?>
        <tr style="background-color:#FFEBEB" id="order_header">
            <td>總計</td>
            <td><?=$monthAll+$nonInvoiceAll?></td>
            <td><?=$consigmentAll?></td>
            <td><?=$backAll?></td>
            <td><?=$adjustAll?></td>
            <td><?=$monthAll+$nonInvoiceAll+$consigmentAll-$backAll-$adjustAll?></td>
             <td><?=$otherAll?></td>
            <td><?=$monthAll+$nonInvoiceAll+$consigmentAll-$backAll-$adjustAll+$otherAll?></td>
            <td><?=$profit?></td>
        </tr>
    </table>
     

        
     
    
</body>
</html>