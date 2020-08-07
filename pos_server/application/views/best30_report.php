<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type="text/javascript">
	$(document).ready(function(){
		$('.productID').each(function()
					{
			
			
			loadInout($(this).val());
		})
		
		
	})
	
	
	var t_key = 0;
	function loadInout(productID)
	{
	
		date = $('#year').val()+'-'+$('#mon').val()+'-31';
		  var param = { 'from':$('#from_'+productID).html(),'to':date,'productID':productID
       }
    
    $.post('/product_flow/pos_io',param,function(data)
          {
  
		  
            if(data.result==true)
                {
					$('#discount_'+data.inf.productID).html(data.inf.buyDiscount+'%');
					$('#price_'+data.inf.productID).html(data.inf.price);	
                    $('#InTotal_'+data.inf.productID).html(data.p.purchaseNum);
					sellNum = data.s.sellNum - data.a.adjustNum;
					$('#shipOutTotal_'+data.inf.productID).html(sellNum);
					$('#sellTotal_'+data.inf.productID).html(data.s.sellPrice);
					$('#shipOutRatio_'+data.inf.productID).html(Math.round(sellNum*100/ parseInt($('#target_'+data.inf.productID).html()))+'%');
					 t_key++;
					loadSell(data.inf.productID)
                    lodaTotalSell(data.inf.productID);
                }
        
        
        
    },'json')
		
		
	}
	function loadSell(productID)
	{
			date = $('#year').val()+'-'+$('#mon').val()+'-31 23:59:59';
			from = $('#year').val()+'-'+$('#mon').val()+'-1';
		  var param = { 'from':from,'to':date,'productID':productID }
  
    $.post('/race/get_sell_record',param,function(data)
          {
				if(data.result)	
				{
					var sellNum = data.s.sellNum - data.a.adjustNum;
					$('#shipOutMonth_'+productID).html(sellNum);
					$('#sellOut_'+productID).html(data.sr.sellNum);	
					  t_key--;
					    
                    if(t_key==0)
					{
						
						$("#best30_table").tablesorter({
				headers:{6:{sorter:'digit'},7:{sorter:'digit'},11:{sorter:'digit'},10:{sorter:'digit'},9:{sorter:'digit'},8:{sorter:'digit'}},
					widgets: ['zebra'],sortList: [[8,1]]});
						
						
						
					}
				
				}
	
		},'json')
	}
	
    
    function lodaTotalSell(productID)
    {
        
        	date = $('#year').val()+'-'+$('#mon').val()+'-31 23:59:59';
			from = $('#from_'+productID).html();
		  var param = { 'from':from,'to':date,'productID':productID }
   
    $.post('/race/get_end_sell_record',param,function(data)
          {
				if(data.result)	
				{
								
					$('#TotalSellOut_'+productID).html(data.sr.sellNum);	
				
					
                   
				}
	
		},'json')
        
        
        
    }
	
</script>	

<input type="hidden" id="year" value="<?=$year?>" >
<input type="hidden" id="mon"  value="<?=$mon?>" >
<h1>統計到<?=$year?>-<?=$mon?>月底</h1>
<table border="1" 
id="best30_table"  class="tablesorter"
style= "background-color:white;text-align:right;font-size:14pt">
	<thead>
		<tr>
		
		<th>名稱</th>
		<th>定價</th>
		<th>折扣</th>
		<th>目標數量</th>
		<th>目標起算日</th>
		<th>目標終止日</th>
		<th>目前累積進貨量</th>
		<th>目前累積出貨量</th>
		<th>出貨金額</th>
		<th>目標達成率</th>
	
		<th>本月出貨量</th>
		<th>本月末端銷售量</th>
		<th>總末端銷售量</th>
	
	</tr>
		
	</thead>
	<tbody>

	<?php foreach( $product as $row):?>
		<tr>
		
		<td><input type="hidden" class="productID" value="<?=$row['productID']?>"/><?=$row['name']?></td>
		<td id="price_<?=$row['productID']?>"></td>
		<td id="discount_<?=$row['productID']?>"></td>
		<td id="target_<?=$row['productID']?>"><?=$row['target']?></td>
		<td id="from_<?=$row['productID']?>"><?=$row['targetStart']?></td>
		<td id="to_<?=$row['productID']?>"><?=$row['targetEnd']?></td>
		<td id="InTotal_<?=$row['productID']?>"></td>
		<td id="shipOutTotal_<?=$row['productID']?>"></td>
		<td id="sellTotal_<?=$row['productID']?>"></td>
		<td id="shipOutRatio_<?=$row['productID']?>">目標達成率</td>
		<td id="shipOutMonth_<?=$row['productID']?>">本月出貨量</td>
		<td id="sellOut_<?=$row['productID']?>">本月末端銷售量</td>
		<td id="TotalSellOut_<?=$row['productID']?>">本月末端銷售量</td>
		<td id="">
		<input type="button" value="銷量走勢圖"  onclick="saleDiagram(<?=$row['productID']?>,true)"/>
		</td>
		
	
	</tr>

	
	<?php endforeach;?>
	</tbody>
</table>






