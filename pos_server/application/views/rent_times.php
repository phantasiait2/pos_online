<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<table  border="1">
	<tr><td>遊戲名稱</td><td>數量</td></tr>
    <?php foreach($rent as $row):?>
    <tr><td><?=$row['ZHName']?></td><td><?=$row['times']?></td></tr>
    <?php endforeach;?>

</table>
</body>
</html>