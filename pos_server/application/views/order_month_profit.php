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
       
<?php 
$monthAll = 0;
$consigmentAll=0;
$backAll=  0;
$adjustAll = 0;
$otherAll =  0;
$profit = 0;
$i=0;
$nonInvoiceAll = 0;
$haveAll = 0;
$inAll = 0;
$outAll = 0 ;
$webAll = 0;
$homeAll = 0;
    
    
$join['monthAll']= 0;
$join['consigmentAll']=0;
$join['backAll']=  0;
$join['adjustAll'] = 0;
$join['otherAll'] =  0;
$join['profit'] = 0;
$join['nonInvoiceAll'] = 0;
$join['haveAll'] = 0;
$join['inAll'] = 0;
$join['outAll'] = 0 ;
$join['webAll'] = 0;
$join['homeAll'] = 0;    
    

		foreach($shopData as $eachShop):
		 $monthTotal = 0;$consigmentTotal = 0;$backTotal = 0;$monthProfit = 0;$nonInvoiceTotal=0;$adjustTotal = 0;$otherTotal=0;
	     $outTotal = 0;$inTotal = 0;$webTotal = 0;$homeTotal=0;
    $d = 0;
	

    foreach($eachShop['sell'] as $row) $d+=count($row);

    if($d<=45) continue;
    
?>
          
  
  
   <?php if($i%15==0):?>
        <tr style="background-color:#FFEBEB" id="order_header">
            <td>店家名稱</td>
            <td>月結商品</td>
   
            <td>寄賣商品</td>
            <td>退貨商品</td>
            <td>調貨商品</td>
            <td>銷貨總計</td>
            <td>商城店取</td>
            <td>商城宅配</td>
            <td>發票小計</td>
            <td>其他款項</td>
            <td>紅利協助攤提</td>
            <td>紅利讓其他店攤</td>
            <td>應收金額</td>
            <td>已收金額</td>
            <td>尚欠金額</td>
            <td>毛利</td>
        </tr>
   
   <?php endif?>
   
   
   
   
    <tr style="text-align:right;<?=($i++%2==1)?'background:#EEE':''?>">
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
        <td><?=number_format($monthTotal+$nonInvoiceTotal)?></td>        
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
        <td><?=number_format($consigmentTotal)?></td>
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
		<td><?=number_format($backTotal)?></td>
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
            <td><?=number_format($adjustTotal)?></td>
      
               <td><?=number_format($monthTotal+$nonInvoiceTotal+$consigmentTotal-$backTotal-$adjustTotal) ?> </td>
            
            <?php if(isset($eachShop['sell']['web']['wbOrder'] ))
                    foreach($eachShop['sell']['web']['wbOrder'] as $row){ $webTotal-=$row['subTotal'];}?>      
            <td><?=number_format($webTotal)?></td>
            
            
               
            <?php if(isset($eachShop['sell']['web']['wbOrderHome'] ))foreach($eachShop['sell']['web']['wbOrderHome'] as $row){ $homeTotal-=$row['profit'];}?>   
            
            <td><?=number_format($homeTotal)?></td>
            <td><?=number_format($monthTotal+$nonInvoiceTotal+$consigmentTotal-$backTotal-$adjustTotal+$webTotal+$homeTotal)?></td>   
               
             <?php 
           foreach($eachShop['sell']['otherMoney'] as $row)
                {
                    
                        
                        
                      $otherTotal += $row['money'];	
                }
            ?>
            <td><?=number_format($otherTotal)?></td>
              <?php 
           foreach($eachShop['sell']['outBonus'] as $row)
                {
                        
                        
                        
                      $outTotal += $row['cost'];	
                }
            ?>
        	<td><?=number_format($outTotal)?></td>
            <?php 
           foreach($eachShop['sell']['inBonus'] as $row)
                {
                        
                        
                        
                      $inTotal += $row['cost'];	
                }
            ?>
        	<td><?=number_format($inTotal)?></td>
      
        
         <td><?=number_format($shouldTotal = $monthTotal+$nonInvoiceTotal+$consigmentTotal-$backTotal-$adjustTotal+$otherTotal-$inTotal+$outTotal+$webTotal+$homeTotal )?> </td>
         <?php $have = 0;
           foreach($eachShop['checkRecord'] as $row) $have += $row['amount'];	
              
            ?>

         <td><?=number_format($have)?></td>
         <td><?=number_format($shouldTotal - $have)?></td>
         <td><?=number_format($monthProfit)?></td>
        	        
    </tr>
      <?php 
		$monthAll += $monthTotal;
		$nonInvoiceAll += $nonInvoiceTotal;
		$consigmentAll += $consigmentTotal;
		$backAll +=  $backTotal;
		$adjustAll +=  $adjustTotal;
		$otherAll +=  $otherTotal;
		$profit += $monthProfit;
		$haveAll +=$have;
		$inAll +=$inTotal;
		$outAll +=$outTotal;
        $homeAll +=$homeTotal;             
        $webAll +=$webTotal;
            if($eachShop['shopInf']['shopID']<1000) 
            {
                $join['monthAll'] += $monthTotal;
		        $join['nonInvoiceAll'] += $nonInvoiceTotal;
		        $join['consigmentAll'] += $consigmentTotal;
		$join['backAll'] +=  $backTotal;
		$join['adjustAll'] +=  $adjustTotal;
		$join['otherAll'] +=  $otherTotal;
		$join['profit'] += $monthProfit;
		$join['haveAll'] +=$have;
		$join['inAll'] +=$inTotal;
		$join['outAll'] +=$outTotal;
        $join['homeAll'] +=$homeTotal;             
        $join['webAll'] +=$webTotal;
                
            }
	  endforeach;?>
        <tr style="background-color:#FFEBEB" id="order_header">
            <td>店家名稱</td>
            <td>月結商品</td>
   
            <td>寄賣商品</td>
            <td>退貨商品</td>
            <td>調貨商品</td>
            <td>銷貨總計</td>
            <td>商城店取</td>
            <td>商城宅配</td>
            <td>發票小計</td>
            <td>其他款項</td>
            <td>紅利協助攤提</td>
            <td>紅利讓其他店攤</td>
            <td>應收金額</td>
            <td>已收金額</td>
            <td>尚欠金額</td>
            <td>毛利</td>
        </tr>
        <tr style="background-color:#FFEBEB;text-align:right" id="order_header">
            <td>加盟店小計</td>
            <td><?=number_format($join['monthAll']+$join['nonInvoiceAll'])?></td>
            <td><?=number_format($join['consigmentAll'])?></td>
            <td><?=number_format($join['backAll'])?></td>
            <td><?=number_format($join['adjustAll'])?></td>
            <td><?=number_format($join['sellAll']=$join['monthAll']+$join['nonInvoiceAll']+$join['consigmentAll']-$join['backAll']-$join['adjustAll'])?></td>
            <td><?=number_format($join['webAll'])?></td>
            <td><?=number_format($join['homeAll'])?></td>
            <td><?=number_format($join['countAll']=$join['monthAll']+$join['nonInvoiceAll']+$join['consigmentAll']-$join['backAll']-$join['adjustAll']+$join['webAll']+$join['homeAll'])?></td>
            <td><?=number_format($join['otherAll'])?></td>
        
            <td><?=number_format($join['outAll'])?></td>
            <td><?=number_format($join['inAll'])?></td>
            <td><?=number_format($join['shouldAll']=$join['monthAll']+$join['nonInvoiceAll']+$join['consigmentAll']-$join['backAll']-$join['adjustAll']+$join['otherAll']-$join['inAll']+$join['outAll']+$join['webAll']+$join['homeAll'])?></td>
            <td><?=number_format($join['haveAll'])?></td>
            <td><?=number_format($join['shouldAll'] - $join['haveAll'])?></td>
            <td><?=number_format($join['profit'])?></td>
        </tr>
        <tr style="text-align:right" id="order_header">
            <td>其他店小計</td>
            <td><?=number_format($monthAll+$nonInvoiceAll-($join['monthAll']+$join['nonInvoiceAll']))?></td>
            <td><?=number_format($consigmentAll-$join['consigmentAll'])?></td>
            <td><?=number_format($backAll-$join['backAll'])?></td>
            <td><?=number_format($adjustAll-$join['adjustAll'])?></td>
            <td><?=number_format($monthAll+$nonInvoiceAll+$consigmentAll-$backAll-$adjustAll-$join['sellAll'])?></td>
            <td><?=number_format($webAll-$join['webAll'])?></td>
            <td><?=number_format($homeAll-$join['homeAll'])?></td>
            <td><?=number_format($monthAll+$nonInvoiceAll+$consigmentAll-$backAll-$adjustAll+$webAll+$homeAll-$join['countAll'])?></td>
            <td><?=number_format($otherAll-$join['otherAll'])?></td>
        
            <td><?=number_format($outAll-$join['outAll'])?></td>
            <td><?=number_format($inAll-$join['inAll'])?></td>
            <td><?=number_format($nonjoin['shouldAll']=$monthAll+$nonInvoiceAll+$consigmentAll-$backAll-$adjustAll+$otherAll-$inAll+$outAll+$webAll+$homeAll-$join['shouldAll'])?></td>
            <td><?=number_format($haveAll-$join['haveAll'])?></td>
            <td><?=number_format($nonjoin['shouldAll'] - ($haveAll-$join['haveAll']))?></td>
            <td><?=number_format($profit-$join['profit'])?></td>
        </tr>          
           
                   
                           
                                   
        <tr style="background-color:#FFEBEB;text-align:right" id="order_header">
            <td>總計</td>
            <td><?=number_format($monthAll+$nonInvoiceAll)?></td>
            <td><?=number_format($consigmentAll)?></td>
            <td><?=number_format($backAll)?></td>
            <td><?=number_format($adjustAll)?></td>
            <td><?=number_format($monthAll+$nonInvoiceAll+$consigmentAll-$backAll-$adjustAll)?></td>
            <td><?=number_format($webAll)?></td>
            <td><?=number_format($homeAll)?></td>
            <td><?=number_format($monthAll+$nonInvoiceAll+$consigmentAll-$backAll-$adjustAll+$webAll+$homeAll)?></td>
            <td><?=number_format($otherAll)?></td>
        
            <td><?=number_format($outAll)?></td>
            <td><?=number_format($inAll)?></td>
            <td><?=number_format($shouldAll=$monthAll+$nonInvoiceAll+$consigmentAll-$backAll-$adjustAll+$otherAll-$inAll+$outAll+$webAll+$homeAll)?></td>
            <td><?=number_format($haveAll)?></td>
            <td><?=number_format($shouldAll - $haveAll)?></td>
            <td><?=number_format($profit)?></td>
        </tr>
    </table>
     

        
     
    
</body>
</html>