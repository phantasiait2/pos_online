<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>共同購買產品搜尋</title>
</head>

<body>

<?php foreach($productList as $row):?>
	<table>
		<tr>
          <th style="text-align:center">時間</th>
        <th style="text-align:center">商品名稱</th>
        <th style="text-align:center">商品數量</th>
        <th style="text-align:center">商品成本</th>
        <th style="text-align:center">銷售價格</th>
       </tr>
	
    	<?php foreach($row as $each):?>
    	<tr>
        <td><?=substr($each['time'],0,16)?></td>    
        <td><?=$each['ZHName']?></td>
        <td><?=$each['sellNum']?></td>
        <td style="text-align:right; color:#900"><?=$each['purchasePrice']?></td>
        <td style="text-align:right"><?=$each['sellPrice']?></td>
		</tr>
    	<?php endforeach;?>
    
    
    </table>
    
    
<?php endforeach;?>




</body>
</html>