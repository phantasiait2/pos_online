<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<table border="1">
	<tr><td>會員編號</td><td>會員名稱</td><td>消費額度</td><td>加入時間</td></tr>
    <?php foreach($member as $row):
		?>
	
	<tr><td><?=$row['memberID']?></td>
    <td><?=$row['name']?></td>
 	<td><?=$row['sell']?></td>
    <td><?=$row['joinTime']?></td></tr>
	<? endforeach;?>    
    

</table>


</body>
</html>