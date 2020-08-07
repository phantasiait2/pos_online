<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<table border="1">
	<tr><td>會員編號</td><td>會員名稱</td><td>會員電話</td><td>會員email</td><td>最近一次來店時間(多少周以前)</td><td>來店次數</td><td>消費額度</td><td>累積紅利</td></tr>
    <?php foreach($rfm as $row):
		if($row['memberID']!=''):
		if(empty($row['recencyTime']))
		{
			
			if(strtotime($row['joinTime'])-strtotime($from)>0)
			{
				$row['recencyTime'] = $row['joinTime'];
				$row['frequency'] +=1;
				$row['total']+=300;
			}
			else $row['recencyTime']= $from; 
		}
		if(empty($row['frequency']))$row['frequency']=0;
		if(empty($row['total']))$row['total']=0;
		if(empty($row['myBonus']))$row['myBonus']=0;
		?>
	
	<tr><td><?=$row['memberID']?></td><td><?=$row['name']?></td><td><?=$row['phone']?></td><td><?=$row['email']?></td><td><?=ceil((strtotime($to) - strtotime($row['recencyTime'])+1)/(24*3600*7))?></td><td><?=$row['frequency']?></td><td><?=$row['total']?></td><td><?=$row['myBonus']?></td></tr>
	<? endif;endforeach;?>    
    

</table>


</body>
</html>