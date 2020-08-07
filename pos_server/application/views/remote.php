<form action="/system/stock_dump" method="post">
<select id="shopID" name="shopID"  onChange="shopStatus()">
<?php foreach($shopData as $row):?>
	<option value="<?=$row['shopID']?>"><?=$row['name']?></option>
<?php endforeach;?>

</select>

<label>
<input type="radio" name="status" id="handShakeOpen" value="1" onChange="changeStatus()"/>開啟
<input type="radio" name="status" id="handShakeClose" value="0" onChange="changeStatus()"/>關閉

</label>
<h1 id="wait" style="display:none">等待交握中</h1>

<div id="handShake" style="display:none">
<textarea style="width:800px; height:300px" id="sql"></textarea>
<input type="button" value="送出" onclick="getData(0)">
<input type="button" value="轉取" onclick="getData(1)">
<input type="button" value="重試" onclick="resultParse()">

================================================
<input type="text" value="" name="month">;
<input type="submit" value="庫存更正" >

<div id="result"></div>


</div>

</form>
<script type="text/javascript">

$('#shopID').val(<?=$shopID?>);
shopStatus();
function  shopStatus()
{
	$('#wait').hide();$('#handShake').hide();
	$.post('/system/get_shop_status',{shopID:$('#shopID').val()},function(data)
	{
			if(data.result==true)
			{
				if(data.handShake==1) $('#handShakeOpen').attr("checked",true);
				else   $('#handShakeClose').attr("checked",true);
				if(data.handShake==1)
				{
					
					
					if(data.handShakeSign==0)$('#wait').show();
					else $('#handShake').show();
				}
			}
	
	},'json')	
	
	
}

function  changeStatus()
{
	$.post('/system/set_shop_status',{shopID:$('#shopID').val(),status:$('input["status"]:checked').val()},function(data)
	{
			if(data.result==true)
			{
				location.href='/system/remote/'+$('#shopID').val();
			}
	
	},'json')	
	
	
}

function getData(token)
{
	
	$('#result').html('<img src="/images/ajax-loader.gif"/>');
	
	$.post('/system/sql_send',{sql:$('#sql').val(),shopID:$('#shopID').val()},function(data)
	{
			if(data.result==true)
			{
			
				resultChk(token)
				
			}
			
	
	},'json')	
	
}
function resultChk(token)
{
		
		$.post('/system/result_chk',{shopID:$('#shopID').val()},function(data)
	{
			if(data.result==true)
			{
			
				resultParse(token);
				
				
			}
			else setTimeout(function(){resultChk(token)},1000);
			
	
	},'json')	
	
}


function resultParse(token)
{	
	if(token==1) url = '/system/get_data_pass';
	else  url = '/system/get_data';
	
	
	$.post(url,{shopID:$('#shopID').val()},function(data)
	{
			$('#result').html(data);		
	
	})	
}


</script>