<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script type="text/javascript">



function recover_set(id)
{
;
	productID = parseInt($('#p_'+id).html());

	$.post('/welcome/product_recover',{productID:productID},function(data)
	{
		if(data.result==true)
		{
				$('#p_'+id).html('');
				recover_set(parseInt(id)+1);
			
		}
		
		},'json')	
	
	
	
}


</script>
</head>

<body>

<input type="button" class="big_button" onclick="recover_set(1)" value="偵錯模式" >

<?php $i = 0 ;foreach($product as $row):$i++?>
<span id="s_<?=$i?>"></span><span id="p_<?=$i?>"><?=$row['productID']?></span>
<?php endforeach;?>

</body>
</html>