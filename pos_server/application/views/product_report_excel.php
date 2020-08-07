
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<?php 

$data[0]['name'] = '商品綜合分析表';




	$index = 1;
	$data[$index]['name'] = '期初庫存';
	$data[$index]['sheet'][]=array('項次','產序','商品編號','中文','數量','成本','成本小計','近三月營業額','近三月賣出');
  	 $lastStockNum=0;$lastStockCost=0;$i=1;
	 if(!empty($lastStock['product']))
	 foreach($lastStock['product'] as $row)
	 {
		if(isset($row['type'])&&$row['type']!=2&$row['type']!=3&&$row['nowNum']!=0)
		{
			$lastStockNum+=$row['nowNum'];
			$lastStockCost+=$row['avgCost']*$row['nowNum'];
			$data[$index]['sheet'][] =array($i++,$row['productID'],fillZero($row['productNum']),$row['ZHName'],$row['nowNum'],$row['avgCost'],'$'.($row['avgCost']*$row['nowNum']),$row['flowRate'],$row['flowNum']);
	
 		}
     }
		$data[$index]['sheet'][] = array('','','',$lastStockNum,'$'.($lastStockCost));
		$data[0]['sheet'][]=array('期初庫存:','$'.($lastStockCost));
	//=====
	$index ++;
	$data[$index]['name'] = '期初寄賣庫存';
	$data[$index]['sheet'][]=array('項次','商品編號','中文','數量','成本','近三月營業額','近三月賣出');
  	 $lastConsignmentStockNum=0;$lastConsignmentStockCost=0;$i=1;
	 if(!empty($lastConsignmentStock))
	 foreach($lastConsignmentStock as $row)
	 {
		if($row['type']!=2&$row['type']!=3&&$row['num']!=0)
		{
			$lastConsignmentStockNum+=$row['num'];
			$lastConsignmentStockCost+=$row['avgCost']*$row['num'];
			$data[$index]['sheet'][] =array($i++,fillZero($row['productNum']),$row['ZHName'],$row['num'],'$'.($row['avgCost']*$row['num']),$row['flowRate'],$row['flowNum']);
	
 		}
     }
		$data[$index]['sheet'][] = array('','','',$lastConsignmentStockNum,'$'.($lastConsignmentStockCost));
		$data[0]['sheet'][]=array('期初寄賣庫存:','$'.($lastConsignmentStockCost));

	//=====
	//=====
	$index++;
	$data[$index]['name'] = '進貨';
	$data[$index]['sheet'][]=array('項次','商品編號','中文','數量','成本');

	$inNum=0;$inCost=0;$i=1;;
	if(!empty($inStock))
	foreach($inStock as $row)
	{
		if($row['num']!=0)
		{
			$inNum+=$row['num'];
			$inCost+=$row['purchasePrice']*$row['num'];
			$data[$index]['sheet'][] =array($i++,fillZero($row['productNum']),$row['ZHName'],$row['num'],'$'.($row['purchasePrice']*$row['num']));

	 	 }
	}
	$data[$index]['sheet'][] = array('','','',$inNum,'$'.($inCost));
	$data[0]['sheet'][]=array('進貨:','$'.($inCost));
	
	//退貨
	$index++;
	$data[$index]['name'] = '退貨';
	$data[$index]['sheet'][]=array('項次','店家','商品編號','中文','數量','成本','原因');

	$backNum=0;$backCost=0;$i=1;;
	if(!empty($backStock))
	foreach($backStock as $row)
	{
		if(isset($row['num'])&&$row['num']!=0)
		{
			$backNum+=$row['num'];
			$backCost+=$row['purchasePrice']*$row['num'];
			$data[$index]['sheet'][] =array($i++,$row['name'],fillZero($row['productNum']),$row['ZHName'],$row['num'],'$'.($row['purchasePrice']*$row['num']),$row['comment']);

	 	 }
	}
	$data[$index]['sheet'][] = array('','','',$backNum,'$'.($backCost));
	$data[0]['sheet'][]=array('退貨:','$'.($backCost));
	//調貨
	$index++;
	$data[$index]['name'] = '調貨';
	$data[$index]['sheet'][]=array('項次','店家','商品編號','中文','數量','成本','原因');

	$adjustNum=0;$adjustCost=0;$i=1;;
	if(!empty($adjustStock))
	foreach($adjustStock as $row)
	{
		if(isset($row['num'])&&$row['num']!=0)
		{
			$adjustNum+=$row['num'];
			$adjustCost+=$row['purchasePrice']*$row['num'];
			$data[$index]['sheet'][] =array($i++,$row['name'],fillZero($row['productNum']),$row['ZHName'],$row['num'],'$'.($row['purchasePrice']*$row['num']),$row['comment']);

	 	 }
	}
	$data[$index]['sheet'][] = array('','','',$adjustNum,'$'.($adjustCost));
	$data[0]['sheet'][]=array('調貨:','$'.($adjustCost));
	//=====
	//出貨
	$index++;
	$data[$index]['name'] = '出貨';
	$data[$index]['sheet'][]=array('序號','出貨單號','日期','金額');

	$outTotal = 0;$i=1;$num=1;$shipmentID =0;
	foreach($outStock as $row)
	{
		$outTotal+=$row['total'];
		$data[$index]['sheet'][] = array($num++,'s'.$row['shippingNum'],substr($row['shippingTime'],0,16),'$'.$row['total']);
		
	}
	$data[$index]['sheet'][] = array('','','','$'.($outTotal));
	$data[0]['sheet'][]=array('出貨:','$'.($outTotal));
	//寄賣出貨
	
	$index++;
	$data[$index]['name'] = '寄賣出貨';
	$data[$index]['sheet'][]=array('序號','店家','金額');

	$consignmentOutTotal = 0;$i=1;$num=1;
	foreach($outConsignment as $row)
	{
		$consignmentOutTotal+=$row['total'];
		$data[$index]['sheet'][] = array($num++,$row['name'],'$'.$row['total']);
		
	}
	$data[$index]['sheet'][] = array('','','','$'.($consignmentOutTotal));
	$data[0]['sheet'][]=array('寄賣出貨:','$'.($consignmentOutTotal));
	
	//=====	
	//=====
	$index++;
	$data[$index]['name'] = '期末寄賣庫存';
	$data[$index]['sheet'][]=array('項次','商品編號','中文','數量','成本','近三月營業額','近三月賣出');

	$consignmentStockNum=0;$consignmentStockCost=0;$i=1;;
	if(!empty($consignmentStock))
	foreach($consignmentStock as $row)
	{
		if($row['type']!=2&$row['type']!=3&&$row['num']!=0)
		{
			$consignmentStockNum+=$row['num'];
			$consignmentStockCost+=$row['avgCost']*$row['num'];;
			$data[$index]['sheet'][] =array($i++,fillZero($row['productNum']),$row['ZHName'],$row['num'],'$'.($row['avgCost']*$row['num']),$row['flowRate'],$row['flowNum']);

	 	 }
	}
	$data[$index]['sheet'][] = array('','','',$consignmentStockNum,'$'.($consignmentStockCost));
	$data[0]['sheet'][]=array('期末寄賣庫存:','$'.($consignmentStockCost));

	//=======
	//=====
	$index++;
	$data[$index]['name'] = '期末庫存';
	$data[$index]['sheet'][]=array('項次','產序','商品編號','中文','數量','比率','成本','成本小計','近三月營業額','近三月賣出');

	$stockNum=0;$stockCost=0;$i=1;;
	if(!empty($stock['product']))
	foreach($stock['product'] as $row)
	{
		if(empty($row['productID'])) continue;
		if(!isset($row['type'])) $row['type']=0;
		if($row['type']!=2&&$row['type']!=3&&$row['nowNum']!=0)
		{
			$stockNum+=$row['nowNum'];
			$stockCost+=$row['avgCost']*$row['nowNum'];;
			if($row['price']!=0)$r = round($row['avgCost']/$row['price'],2);
			else $r = 0 ;
			$data[$index]['sheet'][] =array($i++,$row['productID'],fillZero($row['productNum']),$row['ZHName'],$row['nowNum'],$r,$row['avgCost'],'$'.($row['avgCost']*$row['nowNum']),$row['flowRate'],$row['flowNum']);

	 	 }
	}
	$data[$index]['sheet'][] = array('','','',$stockNum,'$'.($stockCost));
	$data[0]['sheet'][]=array('期末庫存:','$'.($stockCost));

	//=======
	
    //=====
	$index++;
	$data[$index]['name'] = '銷貨成本';
	$data[$index]['sheet'][]=array('項目','收支');
	$data[0]['sheet'][] =array('銷貨成本：','$'.($cost =$lastConsignmentStockCost+ $lastStockCost+$inCost+$backCost+$adjustCost-($stockCost)-$consignmentStockCost));
	$data[$index]['sheet'][]=array('期初存貨','$'.($lastStockCost));
	$data[$index]['sheet'][]=array('期初寄賣存貨','$'.($lastConsignmentStockCost));
	$data[$index]['sheet'][]=array('進貨總額','$'.($inCost));
	$data[$index]['sheet'][]=array('被退貨總額','$'.($backCost));
	$data[$index]['sheet'][]=array('店家調貨貨總額','$'.($adjustCost));
	$data[$index]['sheet'][]=array('期末存貨','$'.($stockCost));
	$data[$index]['sheet'][]=array('期末寄賣存貨','$'.($consignmentStockCost));
	$data[$index]['sheet'][]=array('總計','$'.($cost));
	

    //=====
	$index++;
	$data[$index]['name'] = '銷貨利潤';
	$profit = $outTotal*1.05-$cost;
	$data[$index]['sheet'][] = array('項目','收支');
	$data[$index]['sheet'][] = array('營業總額','$'.($consignmentOutTotal+$outTotal));
	$data[$index]['sheet'][] = array('銷貨成本','$'.($cost));
	$data[$index]['sheet'][] = array('稅額','$'.($outTotal*0.05));
	$data[$index]['sheet'][] = array('總計','$'.($profit));
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
		$objWriter->save($_SERVER['DOCUMENT_ROOT']."/pos_server/upload/product/0_".$date.".xls"); 
		echo '<h1><a href="http://shipment.phantasia.com.tw/pos_server/upload/product/0_'.$date.'.xls">請點我下載:<img src="http://shipment.phantasia.com.tw/images/download.jpg" style=" width:50px"/></a></h1>';
	}
	else
	{
		echo '<input type="button" value="產生excel檔" class="big_button" onclick="getProductAccounting(1)">';
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