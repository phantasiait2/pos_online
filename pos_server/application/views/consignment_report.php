<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
時間：<?=$from?>~<?=$to?>
<table border="1">
    <tr>
    	<td>商品中文</td>
        <td>商品英文</td>
        <td>語言</td>
        <td>定價</td>
        <?php foreach($shopData as $row):?>
        	<?php if($purchase==1):?><td><?=$row['name']?>進貨</td><?php endif;?>
        	<?php if($amount==1):?><td><?=$row['name']?>存貨</td><?php endif;?>
	        <?php if($sell==1):?><td><?=$row['name']?>賣出</td><?php endif;?>
        <?php endforeach;?>
        <td>總數</td>
        <td>小計</td>
    </tr>
    
    <?php $i=0;$total = 0 ;foreach($flowList as $row):?>
    <tr <?=($i++%2==0)?'style="background-color:#EEE"':''?>>
    	<?php if(isset($row['product'])):?>
    			<td><?=$row['product']['ZHName']?></td>
                <td><?=$row['product']['ENGName']?></td>
                <td><?=$row['product']['language']?></td>
                <td><?=$row['product']['price']?></td>
    		<?php $rowtotal = 0 ;$k=0;foreach($row['shopData'] as $each):?>
     
	    		
                <?php if($purchase==1):?><td><?=(isset($each['purchaseNum']))?$each['purchaseNum']:0?></td><?php endif;?>
                <?php if($amount==1):?>
                	<td><?php if(isset($each['nowNum']))
						{
							if($each['nowNum']>0)
							{
								if($k++<=2)$rowtotal+=$each['nowNum'];
								else $rowtotal++;
							}
							echo $each['nowNum'];
						}
						else echo '0';
						?>		
                    </td>
				<?php endif;?>
                <?php if($sell==1):?><td><?=(isset($each['sellNum']))?$each['sellNum']:0?></td><?php endif;?>
             <?php endforeach;?>
             <td><?=$rowtotal?></td>
             <td>
			 	<?php
             		echo $rowtotal = $rowtotal*$row['product']['buyPrice'];
					if($rowtotal>0)		$total +=$rowtotal;
			 	?>
             </td>
        <?php endif;?>
        	
    </tr>
    <?php endforeach;?>

</table>
總價：<?=$total?>
