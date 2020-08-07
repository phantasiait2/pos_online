<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
.flowTable tr:hover{
	background-color:#FF9;
	
	
}


</style>
時間：<?=$from?>~<?=$to?>
<table border="1" class="flowTable">
    <tr>
	    <td>商品編號</td>
    	<td>商品中文</td>
        <td>商品英文</td>
        <td>商品進價</td>
        <?php foreach($shopData as $row):?>
        	<?php if($amount==1):?><td><?=$row['name']?>上期存貨</td><?php endif;?>
        	<?php if($purchase==1):?><td><?=$row['name']?>進貨</td><?php endif;?>
            <?php if($customerBack==1):?><td><?=$row['name']?>客人退貨</td><?php endif;?>
        	<?php if($amount==1):?><td><?=$row['name']?>存貨</td><?php endif;?>
	        <?php if($sell==1):?><td><?=$row['name']?>賣出</td><?php endif;?>
             <?php if($back==1):?><td><?=$row['name']?>退公司</td><?php endif;?>
              <?php if($adjust==1):?><td><?=$row['name']?>調他店</td><?php endif;?>
        <?php endforeach;?>
    </tr>
    
    <?php $i=0;foreach($flowList as $row):?>
      <?php //if(isset($row['shopData'][0]['nowNum'])&&$row['shopData'][0]['nowNum']>2&&(!isset($row['shopData'][1]['nowNum'])||$row['shopData'][1]['nowNum']==0)):?>
    <tr <?=($i++%2==0)?'style="background-color:#EEE"':''?>>
    	<?php if(isset($row['product'])):?>
		        <td><?=$row['product']['productNum']?></td>
    			<td><?=$row['product']['ZHName']?></td>
                <td><?=$row['product']['ENGName']?></td>
				<td><?=round($row['product']['buyPrice'])?></td>
    		<?php foreach($row['shopData'] as $each):?>
                <?php if($amount==1):
						if (!isset($each['lastNum']))$each['lastNum']=0;
				?>
                       <td <?=(isset($row['shopData'][0]['lastNum'])&&$row['shopData'][0]['lastNum']>0&&$each['lastNum']==0)?'bgcolor="#FF0000"':""?>  >			
					    <?=$each['lastNum']?>
                        </td>
			    <?php endif;?>	    		
                <?php if($purchase==1):?><td><?=(isset($each['purchaseNum']))?$each['purchaseNum']:0?></td><?php endif;?>
                <?php if($customerBack==1):?><td><?=(isset($each['customerBackNum']))?$each['customerBackNum']:0?></td><?php endif;?>

                <?php if($amount==1):
						if (!isset($each['nowNum']))$each['nowNum']=0;
				?>
                       <td <?=(isset($row['shopData'][0]['nowNum'])&&$row['shopData'][0]['nowNum']>0&&$each['nowNum']==0)?'bgcolor="#FF0000"':""?>  >			
					    <?=$each['nowNum']?>
                        </td>
			    <?php endif;?>
                <?php if($sell==1):?><td><?=(isset($each['sellNum']))?$each['sellNum']:0?></td><?php endif;?>
				<?php if($back==1):?><td><?=(isset($each['backNum']))?$each['backNum']:0?></td><?php endif;?>
                <?php if($adjust==1):?><td><?=(isset($each['adjustNum']))?$each['adjustNum']:0?></td><?php endif;?>
             <?php endforeach;?>
        <?php endif;?>
        <?php if($i%20==0):?>
        <tr>
        <td>商品編號</td>
    	<td>商品中文</td>
        <td>商品英文</td>
        <td>商品進價</td>
        <?php foreach($shopData as $row):?>
        	<?php if($amount==1):?><td><?=$row['name']?>上期存貨</td><?php endif;?>
        	<?php if($purchase==1):?><td><?=$row['name']?>進貨</td><?php endif;?>
            <?php if($customerBack==1):?><td><?=$row['name']?>客人退貨</td><?php endif;?>
        	<?php if($amount==1):?><td><?=$row['name']?>存貨</td><?php endif;?>
	        <?php if($sell==1):?><td><?=$row['name']?>賣出</td><?php endif;?>
             <?php if($back==1):?><td><?=$row['name']?>退公司</td><?php endif;?>
              <?php if($adjust==1):?><td><?=$row['name']?>調他店</td><?php endif;?>
        <?php endforeach;?>
        </tr>
        <?php endif;?>
    </tr>
     <?php //endif;?>
    <?php endforeach;?>

</table>