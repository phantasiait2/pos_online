<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>invoice</title>
</head>

<body>
<table border="1">
	<tr>
		<td>店名</td>
        <td></td>
        <td>發票號碼</td>
        <td>日期</td>
        <td>金額</td>
        
	</tr>
    <?php foreach($invoice as $row):?>
    	<tr>
        <td><?=$row['name']?></td>
		<td>s<?=$row['shippingNum']?></td>
        <td><?=$row['invoice']?></td>
        <td><?=$row['date']?></td>
        <td><?=$row['price']?></td>
        
	</tr>
    <?php endforeach;?>
</table>

</body>
</html>