<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<link rel="stylesheet" type="text/css" href="/style/pos_product.css" />
<?php if(isset($css))echo $css;?>

<script type="text/javascript" src="/javascript/jquery.js"></script>

<script type="text/javascript" src="/javascript/pos.js"></script>


<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script type="text/javascript" src="/javascript/jquery.tablesorter.js"></script>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


</head>
<?php $time = getdate()?>

<body  style="height:800px">
<div class="webFrame" style="background:#FFF; padding:2px; ">

<h1>銷售資訊分析</h1>
<form id="reportForm" name ="reportForm"action="/accounting/get_day_report" target="_blank" method="post">
<select name="year" id ="year" onChange="getAnalysis()">
<?php for($i=$time['year'];$i>=2011;$i--):?>
<option value="<?=$i?>"><?=$i?></option>
<?php endfor;?>
</select>
年
<select name="mon" id ="mon" onChange="getAnalysis()">
<?php for($i=1;$i<=12;$i++):?>
<option value="<?=$i?>"><?=$i?></option>
<?php endfor;?>
</select>
月

<input type="button" class="big_button"  value="觀看分析表" onClick="getAnalysis()"  />
<input type="button" class="big_button"  value="觀看本月預測" onClick="getPredict();"  />
<input type="button" class="big_button"  value="觀看排行" onClick="getRank($('#year').val()+'-'+$('#mon').val(),$('#shopID').val())"  />
<input type="button" class="big_button"  value="觀看地圖" onClick="$('#map').show();mapIn()"  />

<select name="shopID" id="shopID"  >
<?php if($shopID==0):?>
	<?php foreach($shop as $row):?>
	<option value="<?=$row['shopID']?>"><?=$row['name']?></option>
    <?php endforeach;?>
<?php endif;?>
</select>

</form>


<div id="predict"></div>

<div id="report"></div>

<div id="productRank" style=" display:none">
        <div style="float:left;margin:10px; width:600px ">
        <h1>此月本店銷售</h1>
        <table id="productRankTable_self" border="1px" ></table>
        </div>
        <div style="float:left; margin:10px;width:600px ">
        <h1>此月全省瘋桌遊銷售</h1>
        <table id="productRankTable_all" border="1px" style="float:left"></table>
        </div>
        <div class="divider"></div>
         <div style="clear:both"></div>
        <div style="float:left; margin:10px;width:600px ">
        <h1>本店累積銷售</h1>
        <table id="productRankTable_full_self" border="1px"  style="float:left"></table>
        </div>
        <div style="float:left;margin:10px;width:600px ">
        <h1>全省瘋桌遊累積銷售</h1>
         <table id="productRankTable_full_all" border="1px"  style="float:left"> </table>
         </div>
    </div>

<div id="map"></div>

</div>
</body>
</html>





<script type="application/javascript">

function mapIn()
{
	$('#map').html('<iframe  id="map"  src="http://shipment.phantasia.com.tw/sale/map" width="1200px" height="800px;"></iframe>')
	
	
	
}

// JavaScript Document
function getAnalysis()
{

	//ret = showAddress('新北市板橋區南雅南路二段11-26號');
//alert('b');

		
	//map = initialize(25.020632, 121.545518);
	
	$.ajax({
	   type: "post",
	   dataType: "html",
	   data:$("#reportForm").serialize(),
	   url: "/sale/analysis",
	   success: function(data){

		   $('#shopID').unbind('change');
		   $('#shopID').bind('change',function(){
			getAnalysis();   
			   
			  })
			$('#report').html(data);
		
	   }
	 });	
	
	
	
	
}

function getPredict()
{
	$('#predict').html('<img src="/images/ajax-loader.gif"/>');
	$.ajax({
	   type: "post",
	   dataType: "json",
	   data:$("#reportForm").serialize(),
	   url: "/sale/mon_predict",
	   success: function(data){
			if(data['result']==true)
			{
				$('#predict').html('<h1>本月目前營業額：'+data['saleTotal']+' 本月預估營業額：'+data['salePredict']+'  預估毛利：'+data['predict']+'  '+data['comment']+'</h1>')
			
			}

	   }
	 });		
	
	
	
}

function getRank(date,shopID)
{
	getProductRank(date,shopID,'productRankTable_self');
	getProductRank(date,shopID,'productRankTable_all');
	getProductRank(date,shopID,'productRankTable_full_self');
	getProductRank(date,shopID,'productRankTable_full_all');
	
	
	
}


function getProductRank(date,shopID,id)
{	
  
	$('#productRank').show();
		$('#'+id).html('<tr><td><img src="/images/ajax-loader.gif"/></td></tr>');

	$.post('/sale/get_product_rank',{shopID:shopID,date:date,id:id},function(data)
	{

		
		if(data.result==true)
		{
			
			cleanProductRankTable(id);
			insertRankTable(id,data.ret);
			
		
			
			
		}
		
		
	},'json')	
	
	
	
}


function insertRankTable(id,data)
{
			
					
			for(var i=0;i<50;i++)
			{
					
				var content = '<tr>';
					content+='<td>'+(parseInt(i)+1)+'</td>';
					content+='<td>';
					if(data[i].best30==1 )
                    {
                      if( data[i].onYear==2019) content+='<img src="http://www.phantasia.tw/images/active/2019/30/logo.png" style="width:40px">';
                        else content+='<img src="http://www.phantasia.tw/images/best30.png" style="width:40px">';
                    }
                      
					
					content+= data[i].ZHName+'</td>';
					content+='<td>'+data[i].ENGName+'</td>';
					content+='<td>'+data[i].language+'</td>';
					content+='<td>'+data[i].val+'</td>';
					content+='</tr>';
		
						$('#'+id).append(content);
				
			}	
	
}


function cleanProductRankTable(id)
{
	
	$('#'+id).html(
		'<tr>'+
        	'<td>名次</td>'+
        	'<td>中文</td>'+
        	'<td>英文</td>'+
        	'<td>語言</td>'+
        	'<td>數量</td>'+
        '</tr>')
	
	
	
}






$(document).ready(function(){
		var today = new Date()
		$('#mday').val(today.getDate());
		$('#mon').val(today.getMonth()+1);
	;
});

</script>