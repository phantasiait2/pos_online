<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>銷售預估</title>
</head>

<body>
<table border="1">
				<tr style="background:#EEE">
                	<td>中文</td>
                    <td>英文</td>
                    <td>頂標銷量</td>
                    <td>本店銷量</td>
                    <td style=" width:80px">安全庫存(<?=100-$percent?>%頂標銷量+<?=$percent?>%本店銷量)</td>
                    <td>現在庫存<?=($stockCon==1)?'(扣除)':'(忽略)'?></td>
                    <td>已定數量</td>
                    <td>應訂貨量，大於<?=$lowerNum?>才定</td>
                    
                    
                </tr>
				<?php $i=0;  foreach($product as $row):
					$i++;
				?>
            	<tr  style="background:<?=($i%2==0)?'#FFC':'#FFF'?>" >
                	<td><?=$row['ZHName']?></td>
                    <td><?=$row['ENGName']?></td>
                    
                    <td><?=$row['allNum']  ?></td>
                    <td><?=$row['shopNum'] ?></td>
                    <td><?=$row['safeStock'] ?></td>
                    <td><?=$row['nowNum']?></td>
                    <td><?=$row['orderingNum']?></td>
                    <td><?=$row['orderNum']?></td>
                    
                    
                </tr>
                
                <?php if($i%10==0):?>
                	<tr style="background:#EEE">
                	<td>中文</td>
                    <td>英文</td>
                    <td>頂標銷量</td>
                    <td>本店銷量</td>
                    <td>安全庫存</td>
                    <td>現在庫存</td>
                    <td>已定數量</td>
                    <td>應訂貨量</td>
                </tr>
                
                <?php endif;?>
                
                <?php endforeach;?>
</table>
</body>
</html>