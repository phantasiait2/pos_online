<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pos.js"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	 getProduct()
})	


var total = 0 ;
function getProduct()
{
		$('#progress').html('正在讀取庫存資料....請稍後<img src="/images/ajax-loader_new.gif"/>');
		$.post('/system/product_amount_purchased',{},function(data){
			total = data.product.length
			updateAmount(data.product,0)
		
	},'json')
	
}


function updateAmount(productData,key)
{
	
$('#progress').after(productData[key].productID);
	$.post('/system/product_amount_update',{productID:productData[key].productID},function(data){
		
		
		if(data.result==true)
		{
			key++;
			if(key >=total)
			{
				
				$('#progress').html('更新完成<a href="/">請點此回首頁</a>');
				
				location.replace('/')
			}
			else
			{
			//	$('#progress').after(','+data.product.ZHName+','+data.product,nowNum+'<br/>')
				$('#progress').html ('更新進度 '+key+'/'+total);
				updateAmount(productData,key)
				
				
			}
	
			
		}
		
		
	},'json')	
	
	
	
	
}





</script>


</head>

<body>
<h1>正在更新庫存系統....請勿關閉此頁面</h1>
<h2 id="progress"></h2>

</body>
</html>