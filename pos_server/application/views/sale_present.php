<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<link rel="stylesheet" type="text/css" href="/style/sale.css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="/javascript/jquery.js"></script>
</head>
<body>
<table border="1" style="margin:auto">
	<tr>
    	<td>會員編號</td>
        <td>姓名</td>
        <td>領取時間</td>
        <td>領取地點</td>
        <td>刪除</td>
    </tr>
<?php foreach($presentData as $row):?>
	<tr id="pres_<?=$row['id']?>">
    	<td><?=$row['memberID']?></td>
        <td><?=$row['name']?></td>
        <td><?=$row['time']?></td>
        <td><?=$row['shopName']?></td>
        <td><input type="button" onClick="deletePresent(<?=$row['id']?>)" value="刪除"></td>
    </tr>	


<? endforeach;?>
</table>

<script type="text/javascript">
function deletePresent(id)
{
	if(confirm('你確定要刪除這筆資料？'))
	{
		$.post('/sale/present_delete',{id:id},function(data)
		{
			if(data.result==true)
			{
				
				$('#pres_'+id).fadeOut();
			}
			
		},'json')	
		
		
		
	}	
	
	
}


</script>
</body>
</html>
