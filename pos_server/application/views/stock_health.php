<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php $r = $sellNum/$comNum;?>
<h1>權值：<?=$r?>(總賣出數量<?=$sellNum?>/公司庫存量<?=$comNum?>)越低庫存水位越高</h1>

<table>
    <tr>
        <td>名稱</td>
        <td>銷量</td>
        <td>cr</td>
        <td>正代表庫存需補充，負數代表應該減量</td>
        
        
    </tr>
	<?php  $totalM = 0;$totalP = 0;foreach($product as $row): $cr = round($row['comNum']*$r,0);$d = $row['sellNum']-$cr;if($cr>0)if($d>0)$totalP +=$d;else $totalM +=$d?>
	<tr><td><?=$row['ZHName']?></td><td><?=$row['sellNum']?></td><td><?=$cr?></td><td style="color:<?=($cr>0&&$d<0)?'red':'#000'?>"><?=$d?></td></tr>
    <?php endforeach;?>
</table>

<h1>健康值(0最健康)：<?=($totalM + $totalP)*(abs($totalM) + $totalP)/((abs($totalM + $totalP)))?>,差賣過剩<?=$totalM?> 好賣不足<?=$totalP?>(離差越大 庫存越不健康好賣的貨不夠多不好賣的貨太多)</h1>
</body>
</html>