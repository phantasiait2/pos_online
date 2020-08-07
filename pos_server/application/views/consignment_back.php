<input type="button" onclick="start()" value="開始" style="margin:100px; font-size:15px">

<div id="msg"></div>
<script>


function start()
{
		
	i = 0 ;
	 goback(22,2015,3);
//	goback(17,2015,3);
	
}

function goback(i,year,month)
{
	if(year==2015&&month==7) 
	{
		goback(++i,2014,4);
		return;
	}
	if(i>22)
	{
		$('#msg').html('全部轉換完成');
		return;
	}
	$('#msg').html('正在進行店家'+i+',的轉換'+year+'-'+month);
	$.post('/order/consignment_amount_back',{shopID :i,year:year,month:month},function(data)
	{
		
		if(data.result==true)
		{
			//alert('ok');
			goback(i,data.time['year'],data.time.mon);
			
		}
		
	},'json')	
	
	
	
}
</script>