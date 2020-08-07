<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>問題清單</title>
<SCRIPT type="text/javascript" src="http://www.google.com/jsapi"></SCRIPT>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pos.js"></script>

<script type="text/javascript" src="/javascript/pos_product.js"></script>

<script type="text/javascript" src="/javascript/pos_order.js"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>

<script type="text/javascript" src="/javascript/pos_phantri.js"></script>

<script type="text/javascript" src="/javascript/jquery.lightbox-0.5.min.js"></script>
<link rel="stylesheet" type="text/css" href="/style/jquery-ui-1.8.16.custom.css" />
<script type="text/javascript" src="/javascript/jquery-ui-1.8.16.custom.min.js" ></script>
<script type="text/javascript" src="/javascript/pos_product_query.js"></script>
<script type="text/javascript" src="/javascript/jquery.form.js"></script>  
<script type="text/javascript" src="/javascript/JSG.ImageUploader.js"></script>  

<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<link rel="stylesheet" type="text/css" href="/style/jquery.lightbox-0.5.css" media="screen">

<script type="text/javascript">
$(document).ready(function() {

	$('.img a').lightBox();

	myshow('<?=$type?>');
	
   autoLoad('<?=$type?>','<?=$status?>',0)
})
;
function shopSearch(option,searchID,target,backfunction)
{

	
	var minMatch = 99;
	var chageValue = 0;
	$('.'+option).each(function(){
		
		var judge = $(this).html().indexOf($('#'+searchID).val());
		if(judge!=-1)
		{
			if(judge<minMatch) 
			{
				chageValue = $(this).val();	
				minMatch = judge;
			}
			
			
			
			
		}
		 	
		
	})
	$('#'+target).val(chageValue);
	

	if(chageValue>0 &&backfunction==true)showShipmentList('<?=($shopID>0)?'watch':'staff'?>',$('#selectOrderStatus').val());
	
	
}

    function reload()
    {
        location.href='/phantri/tracking/<?=$this->uri->segment(3)?>/<?=$this->uri->segment(4)?>/'+$('#select_shopID').val();
        
        
    }
    
    
	function myshow(n)
	{
	
		if(n=='全部')$('.allTopic').show();
		else
		{
		$('.allTopic').hide();
		$('.'+n).show();	
		}
		
	}
	
	function autoLoad(type,status,offset)
	{
		
		$.post('/phantri/autoload',{type:type,status:status,offset:offset,shopID:$('#select_shopID').val()},function(data)
		{
			
			if(data!='<h1>查無問題</h1>')
			{
				$('#trackingTable').append(data);
				 $('.img a').lightBox();
				autoLoad(type,status,offset+30);
				
			}
			else myshow(type);
				
			
		})	
		
		
	}
	
	function getProductPreTime(productID)
	{
		content='<td>到貨數量<input type="text" value="0" class="medium_text" id="alreadyOrder_'+productID+'" onchange="changePreTimeNum('+productID+')" value=""></td>'+
				'<td>到貨時間<input type="text" value="0000-00-00" id="preTime_'+productID+'" value=""></td>';
				
		openPopUpBox(content,400,280,'pageReload');
		$('#popUpBoxCancel').hide();
		getAlreadyOrder(productID);
		
		
	}
	function pageReload()
	{
		onEditing = false;
		location.reload();
	}
	
	
</script>
<style type="text/css">
.button {
	-moz-box-shadow:inset 0px 1px 0px 0px #f29c93;
	-webkit-box-shadow:inset 0px 1px 0px 0px #f29c93;
	box-shadow:inset 0px 1px 0px 0px #f29c93;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #fe1a00), color-stop(1, #ce0100) );
	background:-moz-linear-gradient( center top, #fe1a00 5%, #ce0100 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#fe1a00', endColorstr='#ce0100');
	background-color:#fe1a00;
	-webkit-border-top-left-radius:20px;
	-moz-border-radius-topleft:20px;
	border-top-left-radius:20px;
	-webkit-border-top-right-radius:20px;
	-moz-border-radius-topright:20px;
	border-top-right-radius:20px;
	-webkit-border-bottom-right-radius:20px;
	-moz-border-radius-bottomright:20px;
	border-bottom-right-radius:20px;
	-webkit-border-bottom-left-radius:20px;
	-moz-border-radius-bottomleft:20px;
	border-bottom-left-radius:20px;
	text-indent:0;
	border:1px solid #d83526;
	display:inline-block;
	color:#ffffff;
	font-family:Arial;
	font-size:15px;
	font-weight:bold;
	font-style:normal;
	height:40px;
	line-height:40px;
	width:80%;
	text-decoration:none;
	text-align:center;
	text-shadow:1px 1px 0px #b23e35;
	cursor:pointer;
}
.button:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ce0100), color-stop(1, #fe1a00) );
	background:-moz-linear-gradient( center top, #ce0100 5%, #fe1a00 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ce0100', endColorstr='#fe1a00');
	background-color:#ce0100;
}.button:active {
	position:relative;
	top:1px;
}</style>
<style type="text/css">  
.JSGImgPreview {  
  float: left; background: url() no-repeat center 50% #FFFAD9;   
  width: 160px; height: 120px; border: solid 1px #0080FF; margin: 0 5px;  
} 
.allTopic{
	display:none;
	} 
</style>  
<style type="text/css">
.button2 {
	-moz-box-shadow:inset 0px 1px 0px 0px #fce2c1;
	-webkit-box-shadow:inset 0px 1px 0px 0px #fce2c1;
	box-shadow:inset 0px 1px 0px 0px #fce2c1;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ffc477), color-stop(1, #fb9e25) );
	background:-moz-linear-gradient( center top, #ffc477 5%, #fb9e25 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffc477', endColorstr='#fb9e25');
	background-color:#ffc477;
	-webkit-border-top-left-radius:20px;
	-moz-border-radius-topleft:20px;
	border-top-left-radius:20px;
	-webkit-border-top-right-radius:20px;
	-moz-border-radius-topright:20px;
	border-top-right-radius:20px;
	-webkit-border-bottom-right-radius:20px;
	-moz-border-radius-bottomright:20px;
	border-bottom-right-radius:20px;
	-webkit-border-bottom-left-radius:20px;
	-moz-border-radius-bottomleft:20px;
	border-bottom-left-radius:20px;
	text-indent:0;
	border:1px solid #eeb44f;
	display:inline-block;
	color:#ffffff;
	font-family:Arial;
	font-size:15px;
	font-weight:bold;
	font-style:normal;
	height:50px;
	line-height:50px;
	padding-left:10px;
	padding-right:10px;

	text-decoration:none;
	text-align:center;
	text-shadow:1px 1px 0px #cc9f52;
	margin-left:10px;
}
.button2:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #fb9e25), color-stop(1, #ffc477) );
	background:-moz-linear-gradient( center top, #fb9e25 5%, #ffc477 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#fb9e25', endColorstr='#ffc477');
	background-color:#fb9e25;
}.button2:active {
	position:relative;
	top:1px;
}</style>

<style type="text/css">  
.JSGImgPreview {  
  float: left; background: url() no-repeat center 50% #FFFAD9;   
  width: 160px; height: 120px; border: solid 1px #0080FF; margin: 0 5px;  
}  
</style>  

       <style>
	   		.waitReply{
				border:solid;
				border-left-color:#F00;
				border-right-color:#F00;
				border-bottom-width:0;
				border-left-width:10px;
				border-right-width:10px;
				border-top-width:0;
				
			}
	   
	   </style>
            	

</head>

<body>
<?php if($this->data['shopID']==0):?>
        <input type="text" id="shop_search" placeholder="搜尋出貨地點"   onkeyup="shopSearch('select_shopID_op','shop_search','select_shopID',true)">
<select name="shopID" id="select_shopID" onchange="reload()">
		<option class="select_shopID_op" value="0">全部</option>
	<?php foreach($shop as $row):?>
		<?php if($shopID==0||$row['shopID']==$shopID):?>
        <option <?=($this->uri->segment(5)==$row['shopID'])?'selected="selected"':''?> class="select_shopID_op"  value="<?=$row['shopID']?>"><?=$row['name']?></option>
        <?php endif;?>
    <?php endforeach;?>
   
     
</select>
<input type="button" class="big_button" value="查詢" onclick="reload()">
<?php else:?>
    <input type="hidden" id="select_shopID" value="<?=$this->data['shopID']?>">
 <?php endif;?>
<?php if($this->uri->segment(3)=='open'):?>
<h1  style="color:#FFF; font-weight:bold">您現在觀看的是追蹤中的問題</h1>
<?php else:?>
<h1 style="color:#FFF; font-weight:bold">您現在觀看的是已結案的問題</h1>
<?php endif;?>

<a href="/phantri/tracking/<?=$this->uri->segment(3)?>/<?=$this->PR_track_model->typeToNum('全部')?>/<?=$this->uri->segment(5)?>"><input class="button" type="button" value="全部"  style="width:200px;"  ></a>
<a href="/phantri/tracking/<?=$this->uri->segment(3)?>/<?=$this->PR_track_model->typeToNum('到貨詢問')?>/<?=$this->uri->segment(5)?>"><input class="button" type="button" value="到貨詢問"  style="width:200px;"  /></a>
<a href="/phantri/tracking/<?=$this->uri->segment(3)?>/<?=$this->PR_track_model->typeToNum('缺件回報')?>/<?=$this->uri->segment(5)?>"><input class="button" type="button" value="缺件回報"  style="width:200px;"  /></a>
<a href="/phantri/tracking/<?=$this->uri->segment(3)?>/<?=$this->PR_track_model->typeToNum('產品相關問題')?>/<?=$this->uri->segment(5)?>"><input class="button" type="button" value="產品相關問題"  style="width:200px;"  /></a>
<a href="/phantri/tracking/<?=$this->uri->segment(3)?>/<?=$this->PR_track_model->typeToNum('活動執行')?>/<?=$this->uri->segment(5)?>"><input class="button" type="button" value="活動執行"  style="width:200px;"  /></a>
<a href="/phantri/tracking/<?=$this->uri->segment(3)?>/<?=$this->PR_track_model->typeToNum('海報DM名片等印刷品')?>/<?=$this->uri->segment(5)?>"><input class="button" type="button" value="海報DM名片等印刷品"  style="width:200px;"  /></a>
<a href="/phantri/tracking/<?=$this->uri->segment(3)?>/<?=$this->PR_track_model->typeToNum('系統問題')?>/<?=$this->uri->segment(5)?>"><input class="button" type="button" value="系統問題"  style="width:200px;"  /></a>
<a href="/phantri/tracking/<?=$this->uri->segment(3)?>/<?=$this->PR_track_model->typeToNum('經營相關')?>/<?=$this->uri->segment(5)?>"><input class="button" type="button" value="經營相關"  style="width:200px;"  /></a>
<a href="/phantri/tracking/<?=$this->uri->segment(3)?>/<?=$this->PR_track_model->typeToNum('會計問題')?>/<?=$this->uri->segment(5)?>"><input class="button" type="button" value="會計問題"  style="width:200px;"  /></a>
<a href="/phantri/tracking/<?=$this->uri->segment(3)?>/<?=$this->PR_track_model->typeToNum('退貨問題')?>/<?=$this->uri->segment(5)?>"><input id="backBtn" onclick="" class="button" type="button" value="退貨問題"  style="width:200px;"  /></a>
<input class="button2"  type="button" id="showBtn" value="只顯示待回覆"  style="position:absolute; left:20px; top:100px;" onclick="$('#showBtn').hide();myshow('<?=$type?>');$('.alreadyReply').hide();;" >


<table border="1" style="width:80%; color:#FFF" align="center"  >

<tbody id="trackingTable" >
   			
</tbody>

</table>


 </body>
</html>