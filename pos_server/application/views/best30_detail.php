	<h1>本店主打商品銷售總數</h1>
  	　<?php foreach($sell as $row):?>
			<div style="width:222px;height:30px;float:left"><?=$row['name']?>:<?=($row['t'])?>
			<img src="<?=($row['t'] >=$row['times'])?'/images/confirm.png':'/images/delete.png'?>">目標:<?=$row['times']?></div>
  	　<?php endforeach ;?>
  
<div style="clear:both"></div>
  
  
  <h1>消費明細</h1>
<?php if(!empty($record)):?>    
    <table border="1" width="900px;" style="font-size:14pt; text-align:center">
    <tr>
        <th style="text-align:center">會員編號</th>
        <th style="text-align:center">時間</th>
        <th style="text-align:center">商品名稱</th>
        <th style="text-align:center">語言</th>
        <th style="text-align:center">商品數量</th>
        <th style="text-align:center">商品成本</th>
        <th style="text-align:center">銷售價格</th>
        <th style="text-align:center">小計</th>
        <th style="text-align:center">備註</th>
        <th style="text-align:center">獎金</th>

    </tr>
    <?php $total = 0; foreach($record as $row): $total+=$row['best30Bonus']*$row['sellNum']*(1-$row['isOnline'])?>
    <tr
    
    	<?php
			
				 $token=false;
            if($row['memberID']=='999999'&&$row['sellPrice']!=$row['price']) $token=true;
            if(($row['memberID']!='999999'&&$row['sellPrice']<round($row['price']*0.9))||
                ( $row['memberID']!='999999'&&$row['sellPrice']<round($row['price']*$row['minDiscount']/100)))$token=true;
	  
		?>
        style=" color:<?=$token?'red':'black'?>"
     >
        <td><?=$row['memberID']=='999999'?'非會員':$row['memberID']?></td>
        <td><?=substr($row['time'],0,16)?></td>    
        <td><?=$row['ZHName']?><?=!empty($row['rent'])?'('.$row['rent']['ZHName'].')':''?></td>
        <td><?=$row['language']?></td>
        <td><?=$row['sellNum']?></td>
        <td style="text-align:right; color:#900"><?=$row['purchasePrice']?></td>
        <td style="text-align:right"><?=$row['sellPrice']?></td>
        <td style="text-align:right"><?=$row['sellPrice']*$row['sellNum']?></td>
         <td style="text-align:right"><?=$row['comment']?>
         	<?php if($row['isOnline']==1):?>
         	商城
         	<?php elseif(isset($row['major'])):?>         	
         	《主打加碼》
         	<?php endif;?>
         </td>
         
         
          <td style="text-align:right"><?=$row['best30Bonus']*$row['sellNum']*(1-$row['isOnline'])?></td>
          
         
    </tr>
    <?php endforeach;?>
		<tr><td colspan="8"></td><td>總計：</td><td style="text-align:right"><?=$total?></td></tr>	
	
			
	<?php else: echo'今日無消費';
endif;
?>
</table>