<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script type="text/javascript" src="/javascript/jquery.js"></script>
</head>

<script type="application/javascript">

	function postSubmit()
	{
		var url='http://<?=$_SERVER['HTTP_HOST']?>/'+$('#url').val();
		
		var postStr="paserurl="+$('#url').val();
		var key=false;
		for(i=0;i<15;i++)
		{
			if($('#name_'+i).val()!='')
			{
				if(key==true) postStr+='&'+$('#name_'+i).val()+'='+$('#value_'+i).val();
				
			}
		}
		
		if($('#type').val()==0) var dataType='html';
		else dataType='json';
        alert(postStr);
			$('#result').html('');
		$.ajax({
				type: 'post',
				data: postStr,
				url: '/welcome/post_paser_send',
				dataType: dataType,
				success: function(data){
				
					$('#result').html(data);
				}
			});
	
		
	 	
			
			
		
		
	}

</script>

<body>


請輸入API網址<input type="text" name="paserurl" id="url"/>
<select id="type">
<option value="0">normal</option>
<option value="1">json</option>

</select>
<?php for($i=0;$i<15;$i++):?>
	<div><input type="text" id="name_<?=$i?>" />
    <input type="text" id="value_<?=$i?>" /></div>

<?php endfor?>

<input type="button"  onclick="postSubmit()"  value="送出"/>
<form name="" action="/server/index.php/user/photo_upload" enctype="multipart/form-data" method="post">


</form>
<div>result:</div>
<div id="result"></div>


</body>
</html>