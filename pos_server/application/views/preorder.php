


<script type="text/javascript">
  function getComNum(productID,subtotal)
 {
	 $.post('/order/get_available_num',{productID:productID},function(data){
		
		if(data.result==true)
		{;				
			$('#comNum_'+productID).html(data.num);
			if(subtotal*1.2>data.num)
			{
				 $('#comNum_'+productID).css("color","red");
				 
			}
		
		}
		
		
	},'json');
	
	 
	 
	 
	 
	 
}

</script>

<div style="background-color:#FFF">

 
<table border="solid">
	<tr>
    	<td>產品/店名</td>
    	<?php foreach($shopList as $row):?>
       		 <td><?=$row['name']?></td>
        <?php endforeach;?>
    		 <td>總計</td>
              <td>公司</td>
    </tr>
	
	<?php 
		$productID = 0 ; 
		$j=0;
		$num = count($product);
		for($i=0;$i<$num;$i):?>
		
		<tr <?=($j++%2==0)?'style="background:#CCC"':''?>>
    		 <td><?=$product[$i]['ZHName']?></td>	
			
	<?php	
			$subTotal = 0;	
    		foreach($shopList as $row)
			{
				
				if(isset($product[$i]['shopID'])&&$product[$i]['shopID']==$row['shopID'])	 
				{
					$subTotal += $product[$i]['num'];
					
					  echo '<td>'.$product[$i]['num'].'</td>';
							 $i++;
					
				}
				else echo '<td>0</td>';
				
      		  
			}
			
			 echo '<td>'.$subTotal.'</td>';
			 echo '<td id="comNum_'.$product[$i-1]['productID'].'"></td>';
	?>
		</tr>
        <script type="text/javascript"> getComNum(<?=$product[$i-1]['productID']?>,<?=$subTotal?>)</script>
        <?php endfor;?>	

</table>
</div>
