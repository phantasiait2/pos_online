<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type="text/javascript" src="/javascript/jquery.js"></script>
<script  type="text/javascript">

	function autoWeb()
	{
		
		
		
		$('#linkWeb').show();
		
		$('#buttonPanel').hide()
			
			//iframe	
			if($('#ZHName').val()==''||$('#ENGName').val()=='')
		{
			alert('中文或英文名稱不可空白')	;
			window.close();
			return;	
		}
		if($('#price').val()=='')
		{
			
			alert('價格不可空白')	;
			window.close();
			return;		
		}
		
	$.post('/product/pha_bg_create',{ZHName:$('#ZHName').val(),ENGName:$('#ENGName').val(),price:$('#price').val()},function(data){
		if(data.result==true)
		{
			
			
			content='<iframe src="https://www.phantasia.tw/bg_controller/sys_fast_upload/'+data.bid+'/'+$('#productID').val()+'" style="width:600px; height:500px"></iframe>';
			
			$('#linkWeb').html(content);
			
		}
	},'json')
		
		
	}


</script>


<title>Untitled Document</title>
</head>

<body>

<h1>正在幫產品設定封面</h1>

<div id="buttonPanel">
<input type="button"  style="width:100px; height:150px" value="僅在系統上傳" onclick="$('#onlySys').show();$('#buttonPanel').hide()">



<input type="button" style="width:100px; height:150px" value="同步上傳到網頁" onclick="autoWeb()">
</div>


	<input type="hidden" name="ZHName" id="ZHName" value="<?=$product['ZHName']?>">
    	<input type="hidden" name="ENGName"  id="ENGName" value="<?=$product['ENGName']?>">
        	<input type="hidden" name="price"  id="price" value="<?=$product['price']?>">

<div id="onlySys" style="display:none">

<?=$productID?><br/>;
		<form name="gallery_photo_form" enctype="multipart/form-data"  action="/product/photo_upload" method="post">
		<input type="hidden" name="productID"  id="productID" value="<?=$productID?>">
		<input type="file" name="file_name">
		<input type="submit"  class="PHAButton" style="margin-right:10px" value="upload" />
		</form>
</div>
<div id="linkWeb"  style="display:none">
<h1>正在連線到網站....</h1>


</div>




</body>
</html>