<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<div class="accounting" style="width:1200px">
<h1><?=$date?>月份 財務分析報表</h1>

<h2>進貨</h2>
	<div class="height"><a onclick="$('#monthProduct').slideToggle()">月結商品進貨：</a><?='$'.number_format($monthTotal)?></div>
    <div id="monthProduct" style=" display:none">
<h2>訂購品清單</h2>
<table border="1" width="500px" >
	<tr style="background-color:#FFEBEB" id="order_header">
    <td>序號</td>
    <td>出貨單號</td>
    <td>日期</td>
    <td>進貨金額</td>
    </tr>
    <?php 
	 $total = 0;$i=1;$num=1;$shipmentID =0;$unArrive =0;$temp;
	foreach($product as $row):
	
	if($row['shipmentID']!=$shipmentID ) :
	$shipmentID  =$row['shipmentID'];
	 ?> 
     <?php if($i>1):?>
    	<td style="text-align:right;"><?='$'.number_format($total);?></td></tr>
     <?php 	$i=1;$total = 0;endif?>
	<tr >
    	<td><?=$num++?></td>
        <td  style="text-align:left; ">s<?=$row['shippingNum'];?></td>
    	<td><?=substr($row['shippingTime'],0,16);?> </td>
    <?php 
		 $unArriveToken=false;	
		if(substr($row['shippingTime'],5,2)!=substr($row['arriveTime'],5,2))
		{
			 $unArrive+= $row['total'] ;
			 $unArriveToken=true;
			
		}
		
	$temp = array();	
	?>
	
	<?php endif;?>
    <?php $subtotal = $row['sellNum']*$row['sellPrice'];
		  $total += $subtotal;
		  $shipmentComment = $row['shipmentComment'];
		  $i++;
		  if( $unArriveToken)$unArriveData[] =$row;
	?>
    <?php endforeach;?>
    <td style="text-align:right"><?='$'.number_format($total);?></td></tr>
    <tr><td colspan="3"></td><td style="text-align:right"><?='$'.number_format($monthTotal)?></td></tr>
	</table>   
    
    </div>
    
    
    <div  class="height"><a onclick="$('#consignmentProduct').slideToggle()">寄賣商品進貨：</a><?='$'.number_format($consigmentTotal)?></div>
    <div id="consignmentProduct" style="display:none">
	<h2>寄賣商品進貨</h2>
    <table border="1" id="orderTable" width="800px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>         
        <td>商品編號</td>
        <td>銷售日期</td>
        <td>中文</td>
        <td>定價</td>
        <td>進貨折數</td>
        <td>進貨價格</td>
        <td>銷貨數量</td>
        <td>小計</td>
    </tr>
    <?php ;$i=1; $consignmentTotalNum = 0;if(!empty($consigmentProduct))foreach($consigmentProduct as $row):?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    <?php 
			
			 $row['totalNum'] = $row['consignmentNum'] - $row['remainNum'] ;
			$consignmentTotalNum += $row['totalNum'] ;
			$subtotal = $row['totalNum']*$row['purchasePrice'];
	?>
    	<td><?=$i++?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['timeStr']?></td>
        <td><?=$row['ZHName']?></td>
        <td style="text-align:right"><?='$'.number_format($row['price']);?></td>
        <td><?=round($row['purchasePrice']*100/$row['price'])?>%</td>
      <td style="text-align:right"><?='$'.number_format($row['purchasePrice']);?></td>
        <td><?=$row['totalNum']?></td>
        <td style="text-align:right"><?='$'.number_format($subtotal);?></td>
    </tr>	 
    <?php endforeach;?>
    <tr><td colspan="7"></td><td><?=$consignmentTotalNum?></td><td style="text-align:right"><?='$'.number_format($consigmentTotal)?></td></tr>
	</table>
	</div>
    
    <div  class="height"><a onclick="$('#otherProduct').slideToggle()">其他商品進貨：</a><?='$'.number_format($otherTotal)?></div>
    <div id="otherProduct" style=" display:none">
<h2>訂購品清單</h2>
<table border="1" id="monthTable" width="500px" style="text-align:center">
	<tr>
    <td>序號</td>
    <td>名稱</td>
    <td>日期</td>
    <td>進貨金額</td>
    <td>數量</td>
    <td>小計</td>
    </tr>
    <?php 
	 $total = 0;$i=1;$num=1;$shipmentID =0;
	foreach($otherProduct as $row):
		$subtotal = $row['purchaseNum']*$row['purchasePrice'];
		  $i++;
	 ?> 
	<tr >
    	<td><?=$num++?></td>
        <td><?=$row['ZHName']?></td>
    	<td><?=substr($row['time'],0,16);?> </td>
        <td><?=$row['purchasePrice']?></td>
        <td><?=$row['purchaseNum']?></td>
    	<td><?=$subtotal?></td>
    <?php endforeach;?>
    <tr><td colspan="5"></td><td style="text-align:right"><?='$'.number_format($otherTotal);?></td></tr>
</table>    
    
    </div>

 <div  class="height"><a onclick="$('#backProduct').slideToggle()">退貨金額：</a>

<div id="backProduct" style=" display:none">
  <?php $backTotal=0;if(!empty($backProduct)):?>
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
			/*============*/
			$row['purchasePrice'] = round($row['price']*$row['purchaseCount']/100);
			/*============*/
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
        <td style="text-align:right"><?='$'.number_format($row['price'])?></td>
        <td><?=round($row['purchasePrice']*100/$row['price'])?>%</td>
        <td style="text-align:right">
			<?=($row['isConsignment'])?'0':'$'.number_format($row['purchasePrice'])?>
        </td>
        <td><?=$row['orderComment']?></td>
        <td><?=$row['totalNum']?></td>
        <td style="text-align:left"><?='$'.number_format($subtotal)?></td>
    </tr>	 
    <?php endforeach;?>
      <tr style="text-align:right">
       	<td colspan="11"></td>
        <td><?=$backTotal?></td>
      </tr>
  
</table>
<?php endif;?>
</div><?='$'.number_format($backTotal)?>
</div>


<div  class="height"><a onclick="$('#adjustProduct').slideToggle()">調貨金額：</a>
<div id="adjustProduct" style=" display:none">
<?php $adjustTotal=0;if(!empty($adjustProduct)):?>
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
    <?php $adjustTotal=0;$i=1;foreach($adjustProduct as $row):?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    <?php 
			/*============*/
			$row['purchasePrice'] = round($row['price']*$row['purchaseCount']/100);
			/*============*/

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
        <td style="text-align:right"><?='$'.number_format($row['price'])?></td>
        <td><?=round($row['purchasePrice']*100/$row['price'])?>%</td>
        <td style="text-align:right">
			<?=($row['isConsignment'])?'0':round($row['price']*$row['purchaseCount']/100)?>
        </td>
        <td><?=$row['orderComment']?></td>
        <td><?=$row['totalNum']?></td>
        <td style="text-align:right"><?='$'.number_format($subtotal)?></td>
    </tr>	 
    <?php endforeach;?>
      <tr style="text-align:right">
       	<td colspan="12"></td>
        <td style="float:right"><?='$'.number_format($adjustTotal)?></td>
      </tr>
</table>
<?php endif;?>
</div><?='$'.number_format($adjustTotal)?></div>





   
   
   <div  class="height"><a onclick="$('#sellProduct').slideToggle()">總營業額：</a><?='$'.number_format($sellTotal+$secondTotal+$sellBackTotal)?></div>
    <div id="sellProduct" style="display:none">
	<h2>銷貨清單</h2>
    <table border="1" id="orderTable" width="800px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>         
        <td>商品編號</td>
        <td>中文</td>
        <td>銷貨數量</td>
        <td>平均售價</td>
        <td>小計</td>
        <td>成本</td>
        <td>毛利</td>
        <td>毛利率</td>        
    </tr>
    <?php $sellTotalNum=0;$sellProfit=0;$sellCost=0;$i=1;if(!empty($recordOut ))foreach($recordOut as $row):
		$sellTotalNum+=$row['totalNum'];
		$sellProfit+=$row['profit'];
		$sellCost+=$row['totalPurchase'];
	?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    	<td><?=$i++?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['ZHName']?></td>
        <td style="text-align:right"><?=$row['totalNum']?></td>
        <td style="text-align:right"><?='$'.number_format(round($row['subtotal']/$row['totalNum']),2);?></td>
        <td style="text-align:right"><?='$'.number_format($row['subtotal'])?></td>
        <td style="text-align:right"><?='$'.number_format($row['totalPurchase'])?></td>
        <td style="text-align:right"><?='$'.number_format($row['profit']);?></td>
         <td style="text-align:right"><?=($row['subtotal']>0)?number_format($row['profit']/$row['subtotal'],2):'0';?></td>
    </tr>	 
    <?php endforeach;?>
    <tr style="text-align:right">
       	<td colspan="3"></td>
        <td><?=$sellTotalNum?></td>
        <td></td>
    	<td ><?='$'.number_format($sellTotal)?></td>
        <td ><?='$'.number_format($sellCost)?></td>
        <td><?='$'.number_format($sellProfit)?></td>
        <td><?=($sellTotal>0)? number_format($sellProfit/$sellTotal,2):''?></td>
      </tr>

	</table>
    <?php if(!empty($monSecondHand)):?>
<h2>二手品明細</h2>
<table border="1" width="900px;" style="font-size:14pt">
<tr>
        <th style="text-align:center">時間</th>
        <th style="text-align:center">商品名稱</th>
        <th style="text-align:center">銷售價格</th>
   </tr>
<?php foreach($monSecondHand as $row):?>
<tr>
	<td><?=substr($row['time'],0,16)?></td>    
    <td><?=$row['ZHName']?></td>
    <td><?=$row['sellPrice']?></td>
</tr>
<?php endforeach;?>
</table>
<?php endif;?>

<?php if(!empty($monBack)):?>
<h2>退貨明細</h2>
<table border="1" width="900px;" style="font-size:14pt">
<tr>
        <th style="text-align:center">會員編號</th>
        <th style="text-align:center">時間</th>
        <th style="text-align:center">商品名稱</th>
        <th style="text-align:center">商品數量</th>
        <th style="text-align:center">退貨價格</th>
        <th style="text-align:center">退貨原因</th>
        <th style="text-align:center">小計</th>
   </tr>
<?php foreach($monBack as $row):?>
<tr>
    <td><? /* $row['memberID']=='999999'?'非會員':$row['memberID'] */?></td>
	<td><?=substr($row['backTime'],0,16)?></td>    
    <td><?=$row['ZHName']?></td>
    <td><?=$row['sellNum']?></td>
    <td><?=$row['sellPrice']?></td>
	<td><?=$row['backComment']?></td>
    <td><?=$row['sellPrice']*$row['sellNum']?></td>
</tr>
<?php endforeach;?>
</table>
<?php endif;?>


	</div>
   <div  class="height"><a onclick="$('#lastConsignment').slideToggle()">期初寄賣清單：</a>
   <div id="lastConsignment" style="display:none">
	<h2>寄賣清單</h2>
     <table border="1" id="orderTable" width="800px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>         
        <td>商品編號</td>
        <td>中文</td>
        <td>寄賣數量</td>
        <td>銷售數量</td>
        <td>寄賣庫存</td>
        <td>成本</td>
      
    </tr>
    <?php $lastConsignmentTotalNum=0;$lastConsignmentPrice=0;$i=1;if(!empty($lastConsignment))foreach($lastConsignment as $row):
		$lastConsignmentTotalNum+=$row['consignmentNum'];
		$lastConsignmentPrice+=$row['purchasePrice'] *$row['remainNum'];
		if($row['nowNum']-$row['remainNum']>0)
		{
			$monthNum = $row['nowNum']-$row['remainNum'];
			$lastStock['product'][]=
			array(
				'productNum'=>$row['productNum'],
				'ZHName'=> $row['ZHName'],
				'totalCost' =>$row['purchasePrice']*$monthNum ,
				'nowNum' => $monthNum,
				'type' =>1
			);
			
		}
		
	?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    	<td><?=$i++?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['ZHName']?></td>
        <td style="text-align:right"><?=$row['consignmentNum'];?></td>
        <td style="text-align:right"><?=$row['sellNum']?></td>
        <td style="text-align:right"><?=$row['remainNum']?></td>
         <td style="text-align:right"><?=$row['purchasePrice']*$row['remainNum']?></td>
        
    </tr>	 
    <?php endforeach;?>
    <tr style="text-align:right">
       	<td colspan="5"></td>
        <td><?=$lastConsignmentTotalNum?></td>
        <td><?=$lastConsignmentPrice?></td>
      </tr>

	</table>
	</div>
    </div>   
    
   <div  class="height"><a onclick="$('#consignment').slideToggle()">期末寄賣清單：</a>
   <div id="consignment" style="display:none">
	<h2>寄賣清單</h2>
     <table border="1" id="orderTable" width="800px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>         
        <td>商品編號</td>
        <td>中文</td>
        <td>寄賣數量</td>
        <td>銷售數量</td>
        <td>寄賣庫存</td>
        <td>成本</td>
      
    </tr>
    <?php $consignmentTotalNum=0;$consignmentPrice=0;$i=1;if(!empty($consignment))foreach($consignment as $row):
		$consignmentTotalNum+=$row['consignmentNum'];
		$consignmentPrice+=$row['purchasePrice'] *$row['remainNum'];
		if($row['nowNum']-$row['remainNum']>0)
		{
			$monthNum = $row['nowNum']-$row['remainNum'];
			$stock['product'][]=
			array(
				'productNum'=>$row['productNum'],
				'ZHName'=> $row['ZHName'],
				'totalCost' =>$row['purchasePrice']*$monthNum ,
				'nowNum' => $monthNum,
				'type' =>1
			);
			
		}
	
	?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    	<td><?=$i++?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['ZHName']?></td>
        <td style="text-align:right"><?=$row['consignmentNum'];?></td>
        <td style="text-align:right"><?=$row['sellNum']?></td>
        <td style="text-align:right"><?=$row['remainNum']?></td>
         <td style="text-align:right"><?=$row['purchasePrice']*$row['remainNum']?></td>
        
    </tr>	 
    <?php endforeach;?>
    <tr style="text-align:right">
       	<td colspan="5"></td>
        <td><?=$consignmentTotalNum?></td>
        <td><?=$consignmentPrice?></td>
      </tr>

	</table>
	</div>
    </div>   

   <div  class="height"><a onclick="$('#lastUnArrive').slideToggle()">上期未入庫商品：</a>
   <div id="lastUnArrive" style="display:none">
	<h2>上期未入庫商品</h2>
    <table border="1" id="orderTable" width="800px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>  
        <td>出貨單號</td>       
        <td>商品編號</td>
        <td>中文</td>
        <td>數量</td>
        <td>成本</td>
      
    </tr>
    <?php $lastUnArriveNum=0;$lastUnArriveCost=0;$i=1;;if(!empty($lastUnArriveData))
		foreach($lastUnArriveData as $row):
		$lastUnArriveNum+=$row['sellNum'];
		$lastUnArriveCost+=$row['sellNum']*$row['sellPrice'];


	?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    	<td><?=$i++?></td>
         <td><?=$row['shippingNum']?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['ZHName']?></td>
        
        <td style="text-align:right"><?=$row['sellNum'];?></td>
        <td style="text-align:right"><?='$'.number_format($row['sellNum']*$row['sellPrice'])?></td>
    </tr>	 
    <?php endforeach;?>
    <tr style="text-align:right">
       	<td colspan="4"></td>
        <td><?=$lastUnArriveNum?></td>
    	<td ><?='$'.number_format($lastUnArriveCost)?></td>
      </tr>

	</table>
	</div><?='$'.number_format($lastUnArriveCost)?>
    </div>   

  
   <div  class="height"><a onclick="$('#laststock').slideToggle()">期初庫存：</a>
   <div id="laststock" style="display:none">
	<h2>期初庫存</h2>
    <table border="1" id="orderTable" width="800px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>         
        <td>商品編號</td>
        <td>中文</td>
        <td>數量</td>
        <td>成本</td>
      
    </tr>
    <?php $lastStockNum=0;$lastStockCost=0;$i=1;if(!empty($lastStock['product']))foreach($lastStock['product'] as $row):
		if($row['type']!=2&$row['type']!=3&&$row['nowNum']!=0):
		$lastStockNum+=$row['nowNum'];
		$lastStockCost+=$row['totalCost'];
	?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    	<td><?=$i++?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['ZHName']?></td>
        <td style="text-align:right"><?=$row['nowNum'];?></td>
        <td style="text-align:right"><?='$'.number_format($row['totalCost'])?></td>
    </tr>	 
    <?php endif;endforeach;?>
    <tr style="text-align:right">
       	<td colspan="3"></td>
        <td><?=$lastStockNum?></td>
    	<td ><?='$'.number_format($lastStockCost)?></td>
      </tr>

	</table>
	</div><?='$'.number_format($lastStockCost)?>
    </div>   
    
   <div  class="height"><a onclick="$('#stock').slideToggle()">期末庫存：</a>
   <div id="stock" style="display:none">
	<h2>期末庫存</h2>
    <table border="1" id="orderTable" width="800px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>         
        <td>商品編號</td>
        <td>中文</td>
        <td>數量</td>
        <td>成本</td>
      
    </tr>
    <?php $stockNum=0;$stockCost=0;$i=1;;if(!empty($stock['product']))foreach($stock['product'] as $row):
		if($row['type']!=2&$row['type']!=3&&$row['nowNum']!=0):
		$stockNum+=$row['nowNum'];
		$stockCost+=$row['totalCost'];
	?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    	<td><?=$i++?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['ZHName']?></td>
        <td style="text-align:right"><?=$row['nowNum'];?></td>
        <td style="text-align:right"><?='$'.number_format($row['totalCost'])?></td>
    </tr>	 
    <?php endif;endforeach;?>
    <tr style="text-align:right">
       	<td colspan="3"></td>
        <td><?=$stockNum?></td>
    	<td ><?='$'.number_format($stockCost)?></td>
      </tr>

	</table>
	</div><?='$'.number_format($stockCost)?>
    </div>   



   <div  class="height"><a onclick="$('#newInshop').slideToggle()">開盒遊戲：</a>
   <div id="newInshop" style="display:none">
	<h2>本月開盒遊戲</h2>
    <table border="1" id="orderTable" width="800px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>         
        <td>商品編號</td>
        <td>中文</td>
        <td>數量</td>
        <td>成本</td>
      
    </tr>
    <?php $newInshopNum=0;$newInshopCost=0;$i=1;if(!empty($newInshop))foreach($newInshop as $row):
		$newInshopNum+=1;
		$newInshopCost+=$row['purchasePrice'];
	?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    	<td><?=$i++?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['ZHName']?></td>
        <td style="text-align:right">1</td>
        <td style="text-align:right"><?='$'.number_format($row['purchasePrice'])?></td>
    </tr>	 
    <?php endforeach;?>
    <tr style="text-align:right">
       	<td colspan="3"></td>
        <td><?=$newInshopNum?></td>
    	<td ><?='$'.number_format($newInshopCost)?></td>
      </tr>

	</table>
          <div  class="height"><a onclick="$('#inshop').slideToggle()">所有開盒遊戲：</a> </div>   
       <div id="inshop" style="display:none;">
        <h2>所有開盒遊戲</h2>
        <table border="1" id="orderTable" width="800px" style="text-align:center">
        <tr style="background-color:#FFEBEB" id="order_header">
            <td>項次</td>         
            <td>商品編號</td>
            <td>中文</td>
            <td>數量</td>
            <td>成本</td>
          
        </tr>
        <?php $inshopNum=0;$inshopCost=0;$i=1;if(!empty($inShopData))foreach($inShopData as $row):
            $inshopNum+=1;
            $inshopCost+=$row['purchasePrice'];
        ?>
        <tr <?=($i%2==0)?'style="background:#EEE"':''?>>
            <td><?=$i++?></td>
            <td><?=fillZero($row['productNum'])?></td>
            <td><?=$row['ZHName']?></td>
            <td style="text-align:right">1</td>
            <td style="text-align:right"><?='$'.number_format($row['purchasePrice'])?></td>
        </tr>	 
        <?php endforeach;?>
        <tr style="text-align:right">
            <td colspan="3"></td>
            <td><?=$inshopNum?></td>
            <td ><?='$'.number_format($inshopCost)?></td>
          </tr>
    
        </table>
    
        
        
        </div>   

	</div><?='$'.number_format($newInshopCost)?>

   <div  class="height"><a onclick="$('#unArrive').slideToggle()">未入庫商品：</a>
   <div id="unArrive" style="display:none">
	<h2>未入庫商品</h2>
    <table border="1" id="orderTable" width="800px" style="text-align:center">
	<tr style="background-color:#FFEBEB" id="order_header">
    	<td>項次</td>  
        <td>出貨單號</td>       
        <td>商品編號</td>
        
        <td>中文</td>
        <td>數量</td>
        <td>成本</td>
      
    </tr>
    <?php $unArriveNum=0;$unArriveCost=0;$i=1;;if(!empty($unArriveData))
		foreach($unArriveData as $row):
		$unArriveNum+=$row['sellNum'];
		$unArriveCost+=$row['sellNum']*$row['sellPrice'];


	?>
 	<tr <?=($i%2==0)?'style="background:#EEE"':''?>>
    	<td><?=$i++?></td>
         <td><?=$row['shippingNum']?></td>
        <td><?=fillZero($row['productNum'])?></td>
        <td><?=$row['ZHName']?></td>
        
        <td style="text-align:right"><?=$row['sellNum'];?></td>
        <td style="text-align:right"><?='$'.number_format($row['sellNum']*$row['sellPrice'])?></td>
    </tr>	 
    <?php endforeach;?>
    <tr style="text-align:right">
       	<td colspan="4"></td>
        <td><?=$unArriveNum?></td>
    	<td ><?='$'.number_format($unArriveCost)?></td>
      </tr>

	</table>
	</div><?='$'.number_format($unArriveCost)?>
    </div>   

   <div  class="height"><a onclick="$('#monExpenses').slideToggle()">其他消耗：</a>
    <div id="monExpenses" style="display:none">
	<h2>銷貨利潤</h2>
        <table border="1" id="orderTable" width="800px" style="text-align:center">
		     <tr style="background-color:#FFEBEB" id="order_header">
            <td>項次</td>
            <td>項目</td>         
            <td>原因</td>
            <td>金額</td>
          
        </tr>
		<?php $i = 1;$monExpensesTotal=0; foreach($monExpenses as $row):
				$monExpensesTotal+=$row['MOUT'];
		
		?>
        <tr><td><?=$i++?></td><td><?=$row['item']?></td><td><?=$row['note']?></td><td style="text-align:right"><?='$'.number_format($row['MOUT'])?></td></tr>   
        <?php endforeach;?> 
      <tr style="text-align:right">
            <td colspan="3"></td>
            <td ><?='$'.number_format($monExpensesTotal)?></td>
          </tr>

        </table>
             

    </div>
   <?='$'.number_format($monExpensesTotal)?></div>   
   <div  class="height"><a onclick="$('#cost').slideToggle()">銷貨成本：<?='$'.number_format($cost = $monthTotal-$backTotal-$adjustTotal+$lastUnArriveCost+$consigmentTotal+$otherTotal+($lastStockCost)-($stockCost)-$newInshopCost-$unArrive)?></a></div>
   		<div id="cost" style="display:none">
		<h2>銷貨成本</h2>
        <table border="1" id="orderTable" width="800px" style="text-align:center">
		<tr style="background-color:#FFEBEB" id="order_header">
        	<td>項目</td>
            <td>收支</td>
        </tr>
        <tr>
        	<td>進貨總額</td>
			<td style="text-align:right"><?='$'.number_format($monthTotal+$consigmentTotal+$otherTotal-$backTotal-$adjustTotal)?></td>            
        </tr>
        <tr>
        	<td>期初存貨</td>
			<td style="text-align:right"><?='$'.number_format($lastStockCost)?></td>            
        </tr>        
		<tr>
        	<td>上期未及入庫商品</td>
            <td style="text-align:right"><?='$'.number_format($lastUnArriveCost)?></td>
        </tr>

        <tr>
        	<td>期末存貨</td>
			<td style="text-align:right"><?='$'.number_format($stockCost)?></td>            
        </tr>        
        <tr>
        	<td>開盒</td>
			<td style="text-align:right"><?='$'.number_format($newInshopCost)?></td>            
        </tr>   
		<tr>
        	<td>未入庫商品</td>
            <td style="text-align:right"><?='$'.number_format($unArrive)?></td>
        </tr>




        <tr style=" background-color:#CCC">
        	<td>總計</td>
			<td style="text-align:right"><?='$'.number_format($cost)?></td>            
        </tr>     
        </table>
        </div>           
   <div  class="height"><a onclick="$('#sellProfit').slideToggle()">銷貨利潤：</a><?='$'.number_format($profit = $sellTotal-$cost)?></div>
    <div id="sellProfit" style="display:none">
	<h2>銷貨利潤</h2>
        <table border="1" id="orderTable" width="800px" style="text-align:center">
		<tr style="background-color:#FFEBEB" id="order_header">
        	<td>項目</td>
            <td>收支</td>
        </tr>
        <tr>
        	<td>銷貨總額</td>
			<td><?='$'.number_format($sellTotal)?></td>            
        </tr>
        <tr>
        	<td>銷貨成本</td>
			<td style=" color:#F00">(<?='$'.number_format($cost)?>)</td>            
        </tr>        
        <tr>
        	<td>總計</td>
            <td><?='$'.number_format($profit)?></td>
        </tr>
        </table>
                

    </div>

   
   
   
   
</div>