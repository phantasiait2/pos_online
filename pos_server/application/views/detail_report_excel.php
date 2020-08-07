
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<?php 

$data[0]['name'] = '綜合分析表';



$data[0]['sheet'][]=array('月結商品進貨:','$'.($monthTotal));
$data[1]['name'] = '月結商品進貨';
$data[1]['sheet'][]=array('序號','出貨單號','日期','進貨金額');

	 $total = 0;$i=1;$num=1;$shipmentID =0;$unArrive =0;$temp;
	foreach($product as $row)
	{
		if($row['shipmentID']!=$shipmentID )
		{
			$shipmentID  =$row['shipmentID'];
			if($i>1)
			{
				'$'.($total);
				$i=1;$total = 0;
			}
		
			$data[1]['sheet'][] = array($num++,'s'.$row['shippingNum'],substr($row['shippingTime'],0,16),'$'.$row['total']);
			$unArriveToken=false;	
			if(substr($row['shippingTime'],5,2)!=substr($row['arriveTime'],5,2))
			{
				 $unArrive+= $row['total'] ;
				 $unArriveToken=true;
				
			}
			$temp = array();	
		}
		$i++;
		 if( $unArriveToken)$unArriveData[] =$row;
	}
	$data[1]['sheet'][] = array('','','','$'.($monthTotal));
	
	
//===
$data[0]['sheet'][]=array('寄賣商品進貨:','$'.($consigmentTotal));
$data[2]['name'] = '寄賣商品進貨';
$data[2]['sheet'][]=array('項次','商品編號','銷售日期','中文','定價','進貨折數','進貨價格','銷貨數量','小計');

;$i=1; $consignmentTotalNum = 0;
if(!empty($consigmentProduct))
foreach($consigmentProduct as $row)
{
			 $row['totalNum'] = $row['consignmentNum'] - $row['remainNum'] ;
			$consignmentTotalNum += $row['totalNum'] ;
			$subtotal = $row['totalNum']*$row['purchasePrice'];
		 		if($row['price']==0) $p = 0;
				else $p = round($row['purchasePrice']*100/$row['price']);
	$data[2]['sheet'][] = array(
		$i++,
		fillZero($row['productNum']),
		$row['timeStr'],
		$row['ZHName'],
		'$'.($row['price']),
		$p.'%',
		'$'.($row['purchasePrice']),
		$row['totalNum'],
		'$'.($subtotal)
		)	;
		
		
}
$data[2]['sheet'][]=array('','','','','','','',$consignmentTotalNum,'$'.($consigmentTotal)) ;
  
  


//===

$data[0]['sheet'][]=array('其他商品進貨:','$'.($otherTotal));
$data[3]['name'] = '其他商品進貨';
$data[3]['sheet'][]=array('項次','名稱','日期','進貨金額','數量','小計');

  

	 $total = 0;$i=1;$num=1;$shipmentID =0;
	foreach($otherProduct as $row)
	{
		$subtotal = $row['purchaseNum']*$row['purchasePrice'];
		  $i++;
		  	$data[3]['sheet'][] = array(
			$num++,
			$row['ZHName'],
			substr($row['time'],0,16),
			$row['purchasePrice'],
			$row['purchaseNum'],
			'$'.$subtotal
			);
		  
		  
	 }
	 $data[3]['sheet'][] =array('','','','','','$'.($otherTotal));


$data[4]['name'] = '退貨金額';
$data[4]['sheet'][]=array('項次','商品編號','退貨日期','中文','英文','定價','原價','出貨折數','出貨價格','退貨原因','退貨數量','小計');


$backTotal=0;$i=1;


if(!empty($backProduct))  
	foreach($backProduct as $row)
	{
			/*============*/
			/*============*/
			$subtotal = $row['totalNum']*round($row['purchasePrice']);
			if($row['isConsignment'])$subtotal = 0;
		  $backTotal += $subtotal;	
		  
		  if($row['isConsignment']==true)$row['purchasePrice'] = 0;
		  
		  $percent = round($row['purchasePrice']*100/$row['price']);
			$data[4]['sheet'][]=
			array(
			$i++,fillZero($row['productNum']),$row['backTime'],$row['ZHName'],$row['ENGName'],$row['language'],
			'$'.($row['price']),$percent.'%',
			'$'.($row['purchasePrice']),$row['orderComment'],$row['totalNum'],
			'$'.($subtotal)
			);
		
	}
	$data[4]['sheet'][]=array('','','','','','','','','','','','$'.($backTotal));
	$data[0]['sheet'][]=array('退貨金額:','$'.($backTotal));
	//=====
	$data[0]['sheet'][]=array('調貨金額:','$'.($adjustTotal));
	$data[5]['name'] = '調貨金額';
	$data[5]['sheet'][]=array('項次','商品編號','調貨日期','調貨地點','中文','英文','定價','原價','出貨折數','出貨價格','調貨原因','調貨數量','小計');

 $adjustTotal=0; $adjustTotal=0;$i=1;
 if(!empty($adjustProduct))
 	foreach($adjustProduct as $row)
 	{
			/*============*/
			if(isset($row['purchaseCount']))$row['purchasePrice'] = round($row['price']*$row['purchaseCount']/100);
			/*============*/

			$subtotal = $row['totalNum']*round($row['purchasePrice']);
			if($row['isConsignment'])$subtotal = 0;
		  $adjustTotal += $subtotal;
		  	$data[5]['sheet'][] = array($i++,fillZero($row['productNum']),$row['time'],$row['destinationName'],$row['ZHName'],
			$row['ENGName'],$row['language'],'$'.($row['price']),round($row['purchasePrice']*100/$row['price']).'%',$row['purchasePrice'],$row['orderComment'],
			$row['totalNum'],'$'.($subtotal));

	 };
	 
	 $data[5]['sheet'][]=array('','','','','','','','','','','','','$'.($adjustTotal));
	 
	//=====
	$data[0]['sheet'][]=array('總營業額(一般銷貨):','$'.($sellTotal));
	$data[6]['name'] = '總營業額(銷貨清單)';
	$data[6]['sheet'][]=array('項次','商品編號','中文','銷貨數量','平均售價','小計','成本','毛利','毛利率');
	
	$sellTotalNum=0;$sellProfit=0;$sellCost=0;$i=1;
	if(!empty($recordOut ))
	foreach($recordOut as $row)
	{
		$sellTotalNum+=$row['totalNum'];
		$sellProfit+=$row['profit'];
		$sellCost+=$row['totalPurchase'];
		if($row['subtotal']>0) $profitRatio = round($row['profit']/$row['subtotal'],2);
		else $profitRatio  = 0 ;
		if($row['totalNum']>0)	$avgSell = round($row['subtotal']/$row['totalNum']);
		else $avgSell = 0;
		$data[6]['sheet'][]=array($i++,fillZero($row['productNum']),$row['ZHName'],$row['totalNum'],'$'.round($avgSell),
		'$'.($row['subtotal']),'$'.($row['totalPurchase']),'$'.($row['profit']),$profitRatio
		
		);
	}
	 if($sellTotal>0)$profitRatio = round($sellProfit/$sellTotal,2);
		else $profitRatio  = 0;
	 $data[6]['sheet'][] = array('','','',$sellTotalNum,'','$'.($sellTotal),
	 '$'.($sellCost),'$'.($sellProfit),$profitRatio);
	 //====
	 $data[0]['sheet'][]=array('總營業額(二手品販賣):','$'.($secondTotal));
	 $data[7]['name'] = '總營業額(二手品販賣清單)';
	 $data[7]['sheet'][]=array('項次','商品編號','中文','售價','成本','毛利','毛利率');
	
	$secondTotalNum=0;$secondSellProfit=0;$secondSellCost=0;$i=1;
	if(!empty($monSecondHand ))
	foreach($monSecondHand as $row)
	{
		$secondTotalNum+=1;
		$secondSellProfit+=$row['sellPrice'] - $row['cost'];
		$secondSellCost+=$row['cost'];
		if($row['sellPrice']>0) $profitRatio = round(($row['sellPrice'] - $row['cost'])/$row['sellPrice'],2);
		else $profitRatio  = 0 ;
		$data[7]['sheet'][]=array($i++,fillZero($row['productNum']),$row['ZHName'],
		'$'.($row['sellPrice']),'$'.($row['cost']),'$'.($row['sellPrice']-$row['cost']),$profitRatio
		
		);
	}
	 if($secondTotal>0)$profitRatio = round($secondSellProfit/$secondTotal,2);
		else $profitRatio  = 0;
	 $data[7]['sheet'][] = array('','','','$'.($secondTotal),
	 '$'.($secondSellCost),'$'.($secondSellProfit),$profitRatio);
	//=====
 //===
  $data[0]['sheet'][]=array('總營業額(商品退貨):','$'.($sellBackTotal));
	 $data[8]['name'] = '總營業額(商品退貨清單)';
	 $data[8]['sheet'][]=array('項次','商品編號','中文','退貨價錢','成本','毛利','原因');
	
	$sellBackTotalNum=0;$sellBackProfit=0;$sellBackCost=0;$i=1;
	if(!empty($monBack ))
	foreach($monBack as $row)
	{
		$sellBackTotalNum+=1;
		$sellBackProfit -=$row['sellPrice'] - $row['cost'];
		$sellBackCost +=$row['cost'];
		$data[8]['sheet'][]=array($i++,fillZero($row['productNum']),$row['ZHName'],
		'$'.($row['sellPrice']),'$'.($row['cost']),'$'.($row['sellPrice']-$row['cost']),$row['comment']);
	}

	 $data[8]['sheet'][] = array('','','','$'.($sellBackTotal),
	 '$'.($sellBackCost),'$'.($sellBackProfit));



	//=====
	$data[9]['name'] = '期初寄賣清單';
	$data[9]['sheet'][]=array('項次','商品編號','中文','寄賣數量','銷售數量','寄賣庫存','成本');


	$lastConsignmentTotalNum=0;$lastConsignmentPrice=0;$i=1;
	if(!empty($lastConsignment))
	foreach($lastConsignment as $row)
	{
		$lastConsignmentTotalNum+=$row['consignmentNum'];
		$lastConsignmentPrice+=$row['purchasePrice'] *$row['remainNum'];
		if($row['nowNum']-$row['remainNum']>0)
		{
			$monthNum = $row['nowNum']-$row['remainNum'];
			$lastStock['product'][]=
			array(
				'productNum'=>$row['productNum'],
				'ZHName'=> $row['ZHName'],
				'totalCost' =>$row['totalCost']-$row['purchasePrice'] *$row['remainNum'] ,
				'nowNum' => $monthNum,
				'type' =>1
			);
			
		}
		$data[9]['sheet'][]=array($i++,fillZero($row['productNum']),$row['ZHName'],$row['consignmentNum'],$row['sellNum'],$row['remainNum'],'$'.$row['purchasePrice']*$row['remainNum']);
	};
	$data[9]['sheet'][] =array('','','','','',$lastConsignmentTotalNum,$lastConsignmentPrice);
	$data[0]['sheet'][]=array('期初寄賣清單:','$'.($lastConsignmentPrice));

	//=====
	$data[10]['name'] = '期末寄賣清單';
	$data[10]['sheet'][]=array('項次','商品編號','中文','寄賣數量','銷售數量','寄賣庫存','成本');


	 $consignmentTotalNum=0;$consignmentPrice=0;$i=1;
	 if(!empty($consignment))
	 foreach($consignment as $row)
	 {
		$consignmentTotalNum+=$row['consignmentNum'];
		$consignmentPrice+=$row['purchasePrice'] *$row['remainNum'];
		if($row['nowNum']-$row['remainNum']>0)
		{
			$monthNum = $row['nowNum']-$row['remainNum'];
			$stock['product'][]=
			array(
				'productNum'=>$row['productNum'],
				'ZHName'=> $row['ZHName'],
				'totalCost' =>$row['totalCost']-$row['purchasePrice'] *$row['remainNum'] ,
				'nowNum' => $monthNum,
				'type' =>1
			);
			
		}
		$data[10]['sheet'][]=array($i++,fillZero($row['productNum']),$row['ZHName'],$row['consignmentNum'],$row['sellNum'],$row['remainNum'],'$'.$row['purchasePrice']*$row['remainNum']);
	} 
	$data[10]['sheet'][]=array('','','','','',$consignmentTotalNum,$consignmentPrice);
		$data[0]['sheet'][]=array('期末寄賣清單:','$'.($consignmentPrice));

	//=====
	$data[11]['name'] = '上期未入庫商品';
	$data[11]['sheet'][]=array('項次','出貨單號','商品編號','中文','數量','成本');
	
	
	 $lastUnArriveNum=0;$lastUnArriveCost=0;$i=1;;
	 if(!empty($lastUnArriveData))
	foreach($lastUnArriveData as $row)
	{
		$lastUnArriveNum+=$row['sellNum'];
		$lastUnArriveCost+=$row['sellNum']*$row['sellPrice'];

		$data[11]['sheet'][]=array($i++,$row['shippingNum'],fillZero($row['productNum']),$row['ZHName'],$row['sellNum'],'$'.($row['sellNum']*$row['sellPrice']));
	 };
	 $data[11]['sheet'][]=array('','','','',$lastUnArriveNum,'$'.($lastUnArriveCost));
	 $data[0]['sheet'][]=array('上期未入庫商品:','$'.($lastUnArriveCost));



	//=====
	$data[12]['name'] = '期初庫存';
	$data[12]['sheet'][]=array('項次','商品編號','中文','數量','成本');
	
	
	 
	
  
  	 $lastStockNum=0;$lastStockCost=0;$i=1;
	 if(!empty($lastStock['product']))
	 foreach($lastStock['product'] as $row)
	 {
        if(isset($row['type']))     
		if($row['type']!=2&$row['type']!=3&&$row['nowNum']!=0)
		{
			$lastStockNum+=$row['nowNum'];
			$lastStockCost+=$row['totalCost'];
			$data[12]['sheet'][] =array($i++,fillZero($row['productNum']),$row['ZHName'],$row['nowNum'],'$'.($row['totalCost']));
	
 		}
     }
		$data[12]['sheet'][] = array('','','',$lastStockNum,'$'.($lastStockCost));
		$data[0]['sheet'][]=array('期初庫存:','$'.($lastStockCost));
	
	
	//=====
	$data[13]['name'] = '期末庫存';
	$data[13]['sheet'][]=array('項次','商品編號','中文','數量','成本');

	$stockNum=0;$stockCost=0;$i=1;;
	if(!empty($stock['product']))
	foreach($stock['product'] as $row)
	{
        if(isset($row['type'])) 
		if($row['type']!=2&$row['type']!=3&&$row['nowNum']!=0)
		{
			$stockNum+=$row['nowNum'];
			$stockCost+=$row['totalCost'];
			$data[13]['sheet'][] =array($i++,fillZero($row['productNum']),$row['ZHName'],$row['nowNum'],'$'.($row['totalCost']));

	 	 }
	}
	$data[13]['sheet'][] = array('','','',$stockNum,'$'.($stockCost));
	$data[0]['sheet'][]=array('期末庫存:','$'.($stockCost));


	//=====
	$data[14]['name'] = '本月開盒遊戲';
	$data[14]['sheet'][]=array('項次','商品編號','中文','數量','成本');

	
	$newInshopNum=0;$newInshopCost=0;$i=1;
	if(!empty($newInshop))
	foreach($newInshop as $row)
	{
		$newInshopNum+=1;
		$newInshopCost+=$row['purchasePrice'];
		$data[14]['sheet'][] =array($i++,fillZero($row['productNum']),$row['ZHName'],1,'$'.($row['purchasePrice']));
	}	
	$data[14]['sheet'][] = array('','','',$newInshopNum,'$'.($newInshopCost));
	$data[0]['sheet'][]=array('本月開盒遊戲:','$'.($newInshopCost));
	
	//=====
	$data[15]['name'] = '所有開盒遊戲';
	$data[15]['sheet'][]=array('項次','商品編號','中文','數量','成本');

	
	$inshopNum=0;$inshopCost=0;$i=1;
	if(!empty($inShopData))
	foreach($inShopData as $row)
	{
            $inshopNum+=1;
            $inshopCost+=$row['purchasePrice'];
		$data[15]['sheet'][] =array($i++,fillZero($row['productNum']),$row['ZHName'],1,'$'.($row['purchasePrice']));
	}	
	$data[15]['sheet'][] = array('','','',$inshopNum,'$'.($inshopCost));
	$data[0]['sheet'][]=array('所有開盒遊戲:','$'.($inshopCost));
	//=====
	$data[16]['name'] = '未入庫商品';
	$data[16]['sheet'][]=array('項次','出貨單號','商品編號','中文','數量','成本');

	$unArriveNum=0;$unArriveCost=0;$i=1;;
	if(!empty($unArriveData))
	foreach($unArriveData as $row)
	{
		$unArriveNum+=$row['sellNum'];
		$unArriveCost+=$row['sellNum']*$row['sellPrice'];
		$data[16]['sheet'][] =array($i++,$row['shippingNum'],fillZero($row['productNum']),$row['ZHName'],$row['sellNum'],'$'.($row['sellNum']*$row['sellPrice']));

	
	 };
	 $data[16]['sheet'][] = array('','','','',$unArriveNum,'$'.($unArriveCost));
	 $data[0]['sheet'][]=array('未入庫商品:','$'.($unArriveCost));

	//=====
	$data[17]['name'] = '其他消耗';
	$data[17]['sheet'][]=array('項次','項目','原因','金額');
	
			
	$i = 1;$monExpensesTotal=0; 
	if(!empty($monExpenses))
	foreach($monExpenses as $row)
	{
		$monExpensesTotal+=$row['MOUT'];
		$data[17]['sheet'][] =array($i++,$row['item'],$row['note'],'$'.($row['MOUT']))	;
	}
		$data[17]['sheet'][] =array('','','','$'.($monExpensesTotal));
	 $data[0]['sheet'][]=array('其他消耗:','$'.($monExpensesTotal));

    //=====
	$data[18]['name'] = '銷貨成本';
	$data[18]['sheet'][]=array('項目','收支');
	$data[0]['sheet'][] =array('銷貨成本：','$'.(-$cost = $monthTotal-$backTotal-$adjustTotal+$lastUnArriveCost+$consigmentTotal+$otherTotal+($lastStockCost)-($stockCost)-$newInshopCost-$unArrive));

	$data[18]['sheet'][]=array('進貨總額','$'.($monthTotal+$consigmentTotal+$otherTotal-$backTotal-$adjustTotal));
	$data[18]['sheet'][]=array('期初存貨','$'.($lastStockCost));
	$data[18]['sheet'][]=array('上期未及入庫商品','$'.($lastUnArriveCost));
	$data[18]['sheet'][]=array('期末存貨','$'.($stockCost));
	$data[18]['sheet'][]=array('開盒','$'.($newInshopCost));
	$data[18]['sheet'][]=array('未入庫商品','$'.($unArrive));
	
	$data[18]['sheet'][]=array('總計','$'.($cost));
	

    //=====
	$data[19]['name'] = '銷貨利潤';
	$profit = $sellTotal+$secondTotal+$sellBackTotal-$cost-$secondSellCost;
	$data[19]['sheet'][] = array('項目','收支');
	$data[19]['sheet'][] = array('營業總額','$'.($sellTotal+$secondTotal+$sellBackTotal));
	$data[19]['sheet'][] = array('銷貨成本','$'.($cost+$secondSellCost));
	$data[19]['sheet'][] = array('總計','$'.($profit));
	$data[0]['sheet'][] = array('銷貨利潤：','$'.($profit));



	if($excel==1)
	{
		include_once($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/PHPExcel/IOFactory.php'); 
		$objPHPExcel = new PHPExcel();
		$rowArray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
		
		$i = 0; $j=0;$k=0;
		foreach($data as $row)
		{
			
			if($i==0)
			{
				$objWorksheet1 = $objPHPExcel->setActiveSheetIndex($i);
			}
			else $objWorksheet1 = $objPHPExcel->createSheet();
			
			$objWorksheet1->setTitle($row['name']);

			$objPHPExcel->setActiveSheetIndex($i);
			$i++;
			$j = 0 ;
			//$objPHPExcel->fromArray($row['sheet']);
			
			
			foreach($row['sheet'] as $col)
			{
				
				$k=0;$j++;
				foreach($col as $item) 
				{
					if(substr($item,0,1)=='$') 
					{
						$item = substr($item,1);
						$objPHPExcel->getActiveSheet()->getStyle($rowArray[$k].$j)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
					}
					$objPHPExcel->getActiveSheet()->getCell($rowArray[$k].$j)->setValue(str_replace("<br/>","\n",$item));
					
					
					$objPHPExcel->getActiveSheet()->getStyle($rowArray[$k].$j)->getAlignment()->setWrapText(true);
					$k++;
					

					
				}
				
				
			}
			for($l=0;$l<$k;$l++)$objPHPExcel->getActiveSheet()->getColumnDimension($rowArray[$l])->setAutoSize(true);
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
		$objWriter->save($_SERVER['DOCUMENT_ROOT']."/pos_server/upload/".$shopID.'_'.$date.".xls"); 
		echo '<h1><a href="http://shipment.phantasia.com.tw/pos_server/upload/'.$shopID.'_'.$date.'.xls">請點我下載:<img src="http://shipment.phantasia.com.tw/images/download.jpg" style=" width:50px"/></a></h1>';
	}
	else
	{
		echo '<input type="button" value="產生excel檔" class="big_button" onclick="getDetailReport(1)">';
		$i = 0; $j=0;$k=0;
		foreach($data as $row)
		{
			$j = 0 ;
			//$objPHPExcel->fromArray($row['sheet']);
			if($i==0) 
			{
				$i++;
				continue;
			}
			echo '<div  class="height"><a onclick="$(\'#t_'.$i.'\').slideToggle()">'.$row['name'].'：</a>';
			if(isset($data[0]['sheet'][$i-1][1]))echo $data[0]['sheet'][$i-1][1];
				echo '<div id="t_'.$i.'" style="display:none">';
					echo '<h2>'.$row['name'].'</h2>';
					
					echo '<table border="1" id="orderTable" width="800px" style="text-align:center">';
					foreach($row['sheet'] as $col)
					{
						
						$k=0;$j++;
						if($j==1)echo '<tr style="background:#FFEBEB">';
						else if($j%2==1)echo '<tr style="background:#EEE">';
						else echo '<tr>';
						
						$count = 0;
						foreach($col as $item) 
						{
							if($item=='') 
							{
								$count++;
								continue;
							}
							elseif($count>0)
							{
								 echo '<td colspan="'.$count.'"></td>';	
								 $count = 0;
							}
							$right = false;	
							if(substr($item,0,1)=='$') 
							{
								$item = substr($item,1);
								$item = number_format($item);
								$item = '$'.$item;
								$right = true;	
							}
							if(is_numeric($item)) $right =true;
							
							if($right) echo '<td style="text-align:right">'.$item.'</td>';	
							else echo '<td>'.$item.'</td>';					
							$k++;					
						}
						echo '</tr>';
						
					}
					echo '</table>';
				echo '</div>';
				
			echo '</div>';
			$i++;
		}
		
		
	}

?>