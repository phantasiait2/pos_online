

<?php foreach($product as $recordOut):



?>

<h1><?=$recordOut['product']['name']?>消費明細</h1>
<?php if(!empty($recordOut)):?>    
    <table border="1" width="1200px;" style="font-size:14pt; text-align:center">
    <tr>
        <th style="text-align:center; width:300px">店家名稱</th>
        <th style="text-align:center; width:100px">總數</th>
         <?php for($i=1;$i<=$mday;$i++):?>
		<th style="text-align:right; width:100px; <?=weekDayColor($firstWeekDay++%7)?>">
			<?=$i?>
 		</th>
        <?php endfor;?>
    </tr>
    <?php $j= 0; foreach($recordOut['shop'] as $row):if(isset($row['shopID'])):?>
    <tr  <?=($j++%2==0)? 'style="background-color:#F0F0F6"':''?>>
    	 <td><?=$row['name']?></td>
         <td><?=(isset($row['totalNum']))?$row['totalNum']:0?></td>
         <?php for($i=1;$i<=$mday;$i++):?>
		<td id="td_<?=$row['shopID']?>_<?=$recordOut['product']['productID']?>_<?=$i?>" style="text-align:right;<?=(isset($row[$i]))? 'background-color:yellow':''?>" 
        onmouseover="showDate(<?=$i?>,'td_<?=$row['shopID']?>_<?=$recordOut['product']['productID']?>_<?=$i?>')" onmouseout="" >
			<?=(isset($row['m_'.$i]))?$row['m_'.$i]:''?>
 		</td>
        <?php endfor;?>
        </td>
    </tr>
    <?php endif;endforeach;
else: echo'今日無消費';
endif;
?>
</table>
<?php endforeach;?>