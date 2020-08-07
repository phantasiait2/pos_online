// JavaScript Document
var doit = false;
function getInshopWithoutCs()
{
	$.post('/product/get_inshop_without_cs',{},function(data)
	{
		if(data.result==true)
		{
			$('#analyze').hide();
			
			content='<div><a target="_blank" href="http://www.phantasia.tw/bg/home/'+data.product.phaBid+'">'+
					'<img src="http://www.phantasia.tw/phantasia/upload/bg/home/s/'+data.product.phaBid+'.jpg">'+data.product.ZHName+'('+data.product.language+')</a>'+
					' 沒有卡套資訊，幫忙新增吧！<input class="big_button" type="button" value="是的" onclick="editCardSleeve(\''+data.product.productID+'\',\''+data.product.ZHName+'\')">'+
					                        '<input  class="big_button" type="button" onclick="noNeedCardSleeve(\''+data.product.productID+'\');closePopUpBox();"value="不需牌套">'+
											'<input  class="big_button" type="button" onclick="getInshopWithoutCs()" value="下一款">'+
											'<input  class="big_button" type="button" onclick="skip()" value="觀看銷售資訊">'+
											
											'</div>'
			openPopUpBox(content,1000,70,'');	
			$('#popUpBoxEnter').hide();
			$('#popUpBoxCancel').hide();
		}
		
	},'json')
		
	
	
}



function skip()
{
	
	$('#analyze').show();
	closePopUpBox();
	
}



function editCardSleeve(productID,ZHName)
{
	content ='新增'+ZHName+'的卡套資訊';
	content += '<input type="hidden" id="editCardSleeveID" value="'+productID+'">';
	content +='<input type="button" class="big_button" value="新增卡套" onclick="sleeveDefine(\'\')">'+
	          '<div id="sleeveDefine"  style=" background-color:#DDD"></div>';
	openPopUpBox(content,600,180,'cardSleeveSend');	

	sleeveDefine('');

	
	
}

function cardSleeveSend()
{
	doit = true;
	var cardSleeve='';
	$('.sleeveSize').each(function(i){
	
		id = this.id.substr(11);

		if($('#sleeveSize_'+id).length!=0)
		cardSleeve+=$('#sleeveSize_'+id).val()+','+$('#sleeveNum_'+id).val()+'-';
	})	
	$.post('/product/edit_product_card_sleeve',{productID:$('#editCardSleeveID').val(),cardSleeve:cardSleeve},function(data)
	{
		if(data.result==true)
		{
			closePopUpBox();
			getInshopWithoutCs();
			
		}
		
	},'json')
	
	
	
	
}


function noNeedCardSleeve(productID)
{
	doit = true;
		$.post('/product/edit_product_card_sleeve',{productID:productID,cardSleeve:'*'},function(data)
	{
		if(data.result==true)
		{
			getInshopWithoutCs();
			closePopUpBox();
		}
		
	},'json')
	
}

function sleeveDefine(data)
{
	if(sleeveOption=='')getCardSleeve();
	$('#sleeveDefine').append('<div  id="sleeveDefine_'+sleeveIndex+'">卡套尺寸：<select class="sleeveSize" id="sleeveSize_'+sleeveIndex+'">'+sleeveOption+'</select>'+
							'卡套數量：<input type="text" " id="sleeveNum_'+sleeveIndex+'">'+
							'<input type="button" class="big_button" value="刪除" onclick="sleeveDefineDel('+sleeveIndex+')">'+
							'</div>');	
	
	if(data!=''){
		$('#sleeveSize_'+sleeveIndex).val(data.sleeveSize);
		$('#sleeveNum_'+sleeveIndex).val(data.sleeveNum);
		
	}
	sleeveIndex++;
	
}

function sleeveDefineDel(index)
{
	
	$('#sleeveDefine_'+index).detach();	
	
	
	
}
var sleeveIndex = 0;
var sleeveOption = '';
var sleeveList = Array();

function getCardSleeve()
{
	sleeveOption = '';

		$.ajax({
		async:false,	
	   type: "POST",
	   dataType:"json",
	   url: "/product/get_card_sleeve",
	   success: function(data){
		   
		   if(data.result==true)
		   {
		   for(var key in data.cardSleeve)
		   {
				sleeveOption+='<option value="'+data.cardSleeve[key].CSID+'">'+data.cardSleeve[key].CSsize+'</option>';
			 	sleeveList[data.cardSleeve[key].CSID]  = data.cardSleeve[key].CSsize;
		    }
		   }
		   
		 }
		})

	
	
	
}

