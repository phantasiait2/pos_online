<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<form action="/order/safe_stock" method="post" id="frm1">
安全量大於
<select name="threshold" onChange="document.getElementById('frm1').submit();">
<?php for($i=1;$i<=20;$i++):?>
	<option value="<?=$i?>" <?=($threshold==$i)?'selected="selected"':''?>><?=$i?></option>

<?php endfor;?>
</select>
</form>
<table border="1">
	<tr>
    	<td></td>
        <td>品名</td>
        <td>語言</td>
        <td>供應商</td>
        <td>現存量</td>
        <td>安全量</td>
        <td>應定量((安全量+安全量-現存量)*1.2)*進貨天數/30天</td>
        <td>小計</td>
    </tr>
<?php
$total = 0;
 foreach($lowerSafeStock as $row):
 if($row['safeNum']>=$threshold):
 
 ?>
	
	<tr>
    	<td></td>
        <td><?=$row['ZHName']?>(<?=$row['ENGName']?>)</td>
        <td><?=$row['language']?></td>
        <td><?=$row['supplier']?></td>
        <td><?=$row['nowNum']?></td>
        <td><?=$row['safeNum']*$row['day']/30?></td>
        <td><?=$num = ceil(($row['safeNum']*$row['day']/30*2-$row['nowNum'])*1.2)?></td>
        <td><?=round($num*$row['buyPrice'])?></td>
    </tr>

<?php 
$total+=round($num*$row['buyPrice']);
endif;
endforeach;?>
</table>
total:<?=$total?>
