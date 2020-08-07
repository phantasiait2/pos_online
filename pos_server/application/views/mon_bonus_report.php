<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div class="accounting" style="width:1200px">
<h1><?=$date?>月份 會員紅利領取明細</h1>
<?php if(empty($bonusChangeData)):?>    
    <h1>本月份沒有會員兌換喔！！</h1>
 <?php else:?>
  <h1>紅利兌換明細</h1>
    <table border="1" width="1200px;" style="font-size:14pt; text-align:center">
    <tr>
    	<td>會員</td>
    	<td>時間</td>
        <td>品名</td>
        <td>語言</td>
       	<td>金額</td>
        <td>使用紅利</td>

    <tr>
   <?php $total = 0;
   foreach($bonusChangeData as $row):$total+=$row['useBonus'];?>
    <tr>
    	<td><?=$row['memberID'].$row['memberName']?></td>
    	<td><?=$row['time']?></td>
        <td><?=$row['ZHName']?></td>
        <td><?=$row['language']?></td>
        <td><?=$row['price']?></td>
        <td><?=$row['useBonus']?></td>
    <tr>
   <?php endforeach;?>
    	
    
    
    </table>
        <h1>總兌換點數：<?=$total?></h1>
<?php endif;?>

</div>