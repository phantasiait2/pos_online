// JavaScript Document
var productTypeOption;
var pw ='phant1234'
var accountLevel = 100;
var classZh=new Array('編號','分類','公司庫存','商品連結','中文','英文','語言','價錢','出貨折數','出貨價格','供應商','出版商','最低折數','現在庫存','訂購數量','已預定','取消');
var classEng = new Array('productNum','category','comNum','bidExist','ZHName','ENGName','language','price','purchaseCount','purchase','suppliersName','publisher','minDiscount','nowNum','purchaseNum','preorder','cancel');
var classHeader = new Array(15);
var productIndex =0 ;
var orderKey = false;
var consignmentKey = false;
var orderNumber = 0;
var onShopping =false;
var shippingWrong = '';


Date.prototype.addDays = function(days) {
  this.setDate(this.getDate() + days);
  return this;
}
function fillZero(ret)
{
	if(ret==null) ret=0;
	if (ret<10) return '000'+ret.toString();
	else if (ret<100) return '00'+ret.toString();
	else if (ret<1000) return '0'+ret.toString();
	else return ret.toString();
	
	
}

function deleteAll()
{
    $('.orderRowDeleteClass').trigger('click');
    
}


function autoScroll(id)
{
	topToken = false;
	var $body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
	var sTop =  $('#'+id).offset().top;
			$body.animate({
				scrollTop: sTop
			}, 2000);
		return false;	
	
}

function onShoppingCheck()
{
	if(onShopping==true)
	{
		if( confirm('目前正在購物中，要離開嗎？')==true)
		{
			onShopping = false;
		}
		
		else return false;
		
	}	
	
}


function productType()
{
	
	productTypeOption ="";
	$.post('/product/get_product_type',{},function(data){
		if(data.result==true)
		{;
			for( var key in data.productType)	
			{
				
				productTypeOption+='<option value="'+data.productType[key].typeID+'">'+data.productType[key].name+'</option>';
			}
			
		}
		
		
	},'json');
	
}

function headerClick(index,e)
{
	
	
	ret = classHeader[index];
	if(!e.shiftKey){
		
		$('.theader').removeClass('headerSortDown');
		$('.theader').removeClass('headerSortUp');
		for( var key in classEng)
		{
			classHeader[key] =-1;
		}
	}

	
	
	if(ret<=0){
		 classHeader[index] =1;
		 $('#Ihead_'+classEng[index]).addClass('headerSortDown');
	}
	else{
		 classHeader[index] =0;
		 $('#Ihead_'+classEng[index]).addClass('headerSortUp');		
		
	}
	
	$('#head_'+classEng[index]).trigger(e);
	
	
}



function setProductTable(data,type)
{
	var headerStr = '';
	var IheaderStr = '';
	productIndex =  0;
	for( var key in classZh)
	{
			if(classEng[key]=='suppliersName'&&$('#shopID').val()>1000);
			else
			{
			classHeader[key] = -1;
			headerStr +='<th id="head_'+classEng[key]+'">'+classZh[key]+'</th>';	
			IheaderStr +='<th id="Ihead_'+classEng[key]+'" class="theader"  onclick="headerClick('+key+',event)">'+classZh[key]+'</th>';	
			}
	}
	
	
	
	
	$('#product_list').html(
		'<div id="product_order">'+
		'<form id="productList">'+
		'<input type="hidden" name="shopID" value="'+$("#shopID").val()+'">'+
		'tips:按住shift鍵可進行多重排序。<span id="pager"></span>'+
		'<table id="product_header_table"  class="tablesorter" style="width:1250px;;text-align:center;position:relative; z-index:2">'+
		'<thead>'+
		'<tr>'+
		 	IheaderStr+
        '</tr>'+
		'</thead>'+
		'<tbody></tbody>'+
		'</table>'+
		'<div style="overflow:auto;top:-25px; position:relative">'+
	     '<table id="product_table"  class="tablesorter" style="width:1240px;;text-align:center;">'+
		 '<thead>'+   	
		 '<tr id="head_tr" style="height:10px;">'+
		 	headerStr+
        '</tr>'+
		'</thead>'+
		'<tbody id="product_table_body"></tboday>'+	
	    '</table>'+
		'</div>'+
		'</form>'+
		'</div>'
	)	
	$('#product_order').after('<div id="orderConfirmBtn"  style="height:50px; background-color:#800; text-align:center; width:1240px;"><input type="button" style="float:left" class="big_button" value="確認訂單送出" onclick="orderConfirm(0)">'+
							 '<input type="button" style="float:left"  class="big_button" value="取消本次訂單" onclick="clearProduct(\'product\');$(\'#carOption\').detach()"></div>'
							
							 	)
	$('#product_list').after('<div id="carOption" style="height:25px"></div>');
	carKey =false;
	carToggle();
	if(type==1)
	{
		for( var key in data)
		{
			
			 $('#product_table_body').append(
			'<tr id="product_'+data[key].productID+'">'+
			productList(data[key])+
			'</tr>'
			)
		}
	}
	else
	{
			 $('#product_table_body').append(
			'<tr id="product_'+data.productID+'">'+
			productList(data)+
			'</tr>'
			)
		
	}
	$("#product_table").tablesorter({widgets: ['zebra']});
	adjustWidth();
	$('#classSelect').hide();

}



function adjustWidth()
{
	$('#head_tr').show();
	$(".td_div").each(function(){
		$(this).width(($(this).parent().attr('width')));
		
	})
	for( var key in classEng)
	{
		$('#Ihead_'+classEng[key]).width($('#head_'+classEng[key]).width());
		$('.td_'+classEng[key]).width($('#head_'+classEng[key]).width());
	}
	$('#head_tr').hide();
	$('#classSelect').hide();	
	
	
}



function clientOrderTable(product,notupdate)
{
		$('.car_'+product.productID).html('已加入購物車');
		if($('#product_header_table').length==0)setProductTable('',1);
			if($('#product_'+product.productID).length!=0){
				alert('商品已在清單中');
				return;
			}	
			else if(product.openStatus==0)
			{
				alert(product.ZHName+'('+product.ENGName+')['+product.language+']\n現在不開放訂購');
				return;
			}
			else if (product.type==8 && $('#magicToken').val()==0)
			{
				alert('魔法風雲會商品請至魔法風雲會專區訂購~');
				return;
				
			}
			
			 
			
			 $('#product_table_body').append(
			 	'<img class="orderWait" src="/images/ajax-loader.gif">'
			 )
				 $('#product_table_body').append(
					'<tr  id="product_'+product.productID+'">'+
					productList(product)+
					'</tr>'
					);
					
				if(product.orderNum>=0)$('#purchase_num_'+product.productID).val(product.orderNum);
				
				
				$('.orderWait').detach();	
			
				 limitCheck(product.productID);	
								
		
			
	
			if(notupdate!=1) 
			{
				$("#product_table").trigger("update");
				adjustWidth();
			}
			checkIsConsignment(product.productID,'td_purchasenum_'+product.productID);
		
		
}

function checkIsConsignment(productID,putID)
{
	
	$.post('/order/chk_consignment',{productID:productID},function(data)
	{
		
		if(data.result==true)
		{
			
			
			$('#'+putID).append('<span style="color:red">寄賣品</span>')	;
			
		}
		
	},'json')	
	
	
}


function productList(product,row)
{
	
	
	if(!row)
	{ 
		row = productIndex;
		productIndex++;
	}
	var result='';
	var x= 0;
//	result+='<form id="product_form_'+row+'">';

	for( var key in classZh)
	{
			if(classEng[key]=='editBtn')result +='<td class="td_'+classEng[key]+'"><input type="button" value="編輯" onclick="editBox(\''+product.productID+'\','+row+')"></td>';
			else if(classEng[key]=='productNum')
			{
					result +='<td class="td_'+classEng[key]+'"><input type="hidden" name="proudctID_'+product.productID+'" value="'+product.productID+'"/>'
					+fillZero(product.productNum)+'</td>';
			}
			else if(classEng[key]=='delete')result +='<td class="td_'+classEng[key]+'"><input type="button" value="刪除" onclick="productDelete(\''+product.productID+'\')"></td>';
			else if((classEng[key]=='purchase'))
			{
				


				result +='<td class="td_'+classEng[key]+'" style=" text-align:right">';
				result += concessionPrice(product['price'],product['purchaseCount'],product.concessions,'<br/>');
				result +='</td>';						
				
				
				
			}
			
			else if(classEng[key]=='minDiscount'||classEng[key]=='buyDiscount')result +='<td class="td_'+classEng[key]+'">'+ product[classEng[key]] +'%</td>';	
			else if(classEng[key]=='purchaseCount')
			{
				
				result+= '<td class="td_'+classEng[key]+'" style=" text-align:left">'+
						concession(product[classEng[key]],product.concessions,product.concessionsNum,'<br/>')+
						'<div style="display:none" class="concessions" id="concessions_'+product.productID+'">'+product.concessions+'</div>'+
						'<div style="display:none" id="concessionsNum_'+product.productID+'">'+product.concessionsNum+'</div>'
						'</td>';
				
				
			}
			else if(classEng[key]=='openStatus'){
				if(product[classEng[key]]==1)result +='<td class="td_'+classEng[key]+'" style="cursor:pointer" onclick="productOpen('+product.productID+',0,'+row+')">開放中</td>';	
				else result +='<td class="td_'+classEng[key]+'" style="cursor:pointer" onclick="productOpen('+product.productID+',1,'+row+')">關閉中</td>';
			}
			else if(classEng[key]=='bidExist')
			{
				result +='<td class="td_'+classEng[key]+'">';
				if(product['bidExist']==1&&product['phaBid']!=0)	result+='<a href="http://www.phantasia.tw/bg/home/'+product['phaBid']+' " target="_blank">觀看連結</a>';
				result +='</td>';
				
			}
			
			else if(classEng[key]=='purchaseNum')
			{
				//var limitNum = Math.round(parseInt(product.comNum)/5);
                limitNum = parseInt(product.limitNum);
				if(product.ZHName.indexOf('卡套')>=0)limitNum = 0;
				result +='<td class="td_'+classEng[key]+'" id="td_purchasenum_'+product.productID+'">'+
						 '<div class="btn_minus" onclick="changeAmount(0,\'purchase_num_'+product.productID+'\')" style=" margin-left:10px;"></div>'+
						 '<input type="text" class="short_text" id="purchase_num_'+product.productID+'"  name="num_'+product.productID+'" onblur="chkMinus('+product.productID+')" value="1" style="float:left">'+
						 '<div class="btn_plus" onclick="changeAmount(1,\'purchase_num_'+product.productID+'\')"></div>'+
						 '<span id="buyingNum_'+product.productID+'" style="display:none">'+parseInt(product.orderingNum)+'</span>'+
						 '<span id="limitNum_'+product.productID+'" style="display:none">'+limitNum+'</span>'+
						 '</td>'
						 ;
				
			}
			else if(classEng[key]=='preorder') result +='<td class="td_'+classEng[key]+'" id="'+classEng[key]+'_'+product.productID+'">'+product.preOrderNum+'</td>';
			else if(classEng[key]=='cancel')result +='<td class="td_'+classEng[key]+'"><input type="button" value="取消" onclick="$(\'#product_'+product.productID+'\').detach()"></td>'; 
			else if (classEng[key]=='suppliersName'&&$('#shopID').val()>1000);//do nothing
			else {
				result +='<td class="td_handeler td_'+classEng[key]+'" id="'+classEng[key]+'_'+product.productID+'">'+ product[classEng[key]] +'</td>';
			}
			
			x++;
	}
//	result+='</form>';
	
   return result;
	
	
	
}
function ecnomySet(productID)
{
	var concessions = $('#concessions_'+productID).html();
	var concessionsNum = $('#concessionsNum_'+productID).html();

			
	if(concessions!=null) var concessionsList = concessions.split(',');
	else var concessionsList ={};
	if(concessionsNum!=null) var concessionsNumList = concessionsNum.split(',');
	else var concessionsNumList ={};						
	
	var token = false;
	var orderNum = $('#purchase_num_'+productID).val();
	
	for(var each in concessionsList)
	{
		
		if(!token&&parseInt(concessionsNumList[each])>0&&parseInt(orderNum)<=parseInt(concessionsNumList[each]))
		{
			
			
			orderNum = concessionsNumList[each];
			
			token = true;	
			
		}

	
		
	}	
	
 $('#purchase_num_'+productID).val(orderNum);
	
}


function economyCaculate()
{
	
	$('.concessions').each(
	function(){
	
		var productID = this.id.substr(12);
		ecnomySet(productID);

		
		
	})
	alert('經濟訂貨量已設定完成');	
	
	
}



function concession(purchaseCount,concessions,concessionsNum,separator)
{
	
			
				var result='';
				result +=(parseInt(purchaseCount))+'% '	   
							
					if(concessions!=null) var concessionsList = concessions.split(',');
					else var concessionsList ={};
				
				
					if(concessionsNum!=null) var concessionsNumList = concessionsNum.split(',');
					else var concessionsNumList ={};						
					
					for(each in concessionsList)
					{
						result +=separator+concessionsList[each]+'% '+concessionsNumList[each]+'<img  style=" height:20px" src="/images/↑.png">';
						
					}	
		return result;		
	
	
}

function concessionPrice(price,purchaseCount,concessions,separator)
{
	var result ='';
		result+= Math.round((parseInt(price)*(parseInt(purchaseCount)))/100);
						
				 
					if(concessions!=null) var concessionsList = concessions.split(',');
					else var concessionsList ={};
					for(each in concessionsList)
					{
						result += separator+Math.round((parseInt(price)*parseInt(concessionsList[each]))/100);
							
					}	
	
	return result;
	
}

var printLine;
function orderTable(data,type,status,orderNum)
{
    
	orderKey =false;
	 width = '100%';
	

	content=
	'<div>'+
	'<table border="1" class="orderTableClass" id="orderTable" style="width:100% ;text-align:center" >';
	if(type=='showingPrint') 
	{
		content+='<tr><td colspan="15">訂單編號：'+magicCheck(data.product[0].magic)+'o'+data.product[0].orderNum+'</td></tr>';
		type= 'print';
	}
	;
	product = data.product;

	num = 0 ; printLine = 4; p = 1;pageLimit = 40;
	for( var key in product)
	{
		var numKey = false;
        if(printLine>pageLimit && pageLimit!=0)
        {    
            printLine = 0;
            p++;
        	orderKey =false;
            content+=	'</table>'+
                    '<div class="break" style="page-break-after:always;clear:both;"><h3>'+orderNum+'續下頁 </h3></div>'+
                    '<h1>'+orderNum+'續上頁 第'+p+'頁</h1>'+
                    '<table border="1" class="orderTableClass"  style="width:'+width+'px;text-align:center" >';
        }
        
        
        
        
        printLine++; pageLimit = 0;		 	
		if(type=='shipment') content+=productViewRow(product[key],{count:false,rowDelete:false,shippingStatus:false,eachCost:false});
		else if(type=='shipmentStaff') content+=productViewRow(product[key],{count:false,rowDelete:false,shippingStatus:false});
		else if(type=='print')
        {
            content+=productViewRow(product[key],{count:false,box:false,cabinet:false,orderNum:false,rowDelete:false,shippingStatus:false,eachCost:false});
            pageLimit = 35;
        }
		else if(type=='noPricePrint')
        {
            content+=productViewRow(product[key],{count:false,box:false,cabinet:false,orderNum:false,price:false,rowDelete:false,shippingStatus:false,purchaseCount:false,
		purchasePrice:false,subtotal:false,eachCost:false});
            pageLimit = 35;   
        }
		else if(type=='noPriceShowingPrint') 
        {
            content+=productViewRow(product[key],{count:false,box:false,cabinet:false,orderNum:false,rowDelete:false,shippingStatus:false,purchaseCount:false,purchasePrice:false,subtotal:false,eachCost:false});
            pageLimit = 35;
        }
        else if(type=='pickupList')
        {
            content+=productViewRow(product[key],{box:false,orderNum:false,price:false,rowDelete:false,shippingStatus:false,purchaseCount:false,purchasePrice:false,subtotal:false,eachCost:false,checkcount:false});
            pageLimit = 35;
        }
        else if(type=='boxin')
        {
            content+=productViewRow(product[key],{count:false,orderNum:false,cabinet:true,price:false,rowDelete:false,shippingStatus:false,purchaseCount:false,purchasePrice:false,subtotal:false,eachCost:false,checkcount:false},'boxin');
            pageLimit = 35;
        }
        else if(type=='orderChk')
        {
            content+=productViewRow(product[key],{count:true,orderNum:false,cabinet:true,price:false,rowDelete:false,shippingStatus:false,purchaseCount:false,purchasePrice:false,subtotal:false,eachCost:false,checkcount:false});
            pageLimit = 35;
        }
        else if(type='watchPrint')
		{
			
			 content+=productViewRow(product[key],{cabinet:false,count:false,rowDelete:false,orderNum:false,eachCost:false,shippingStatus:false},status);
			 num += parseInt(product[key].OSBANum);
			numKey=true;
		}
		else
		{
			
			 content+=productViewRow(product[key],{cabinet:false,count:false,rowDelete:false,orderNum:false,eachCost:false},status);
			 num += parseInt(product[key].OSBANum);
			numKey=true;
		}
		if(numKey==false) num += parseInt(product[key].sellNum);
     
		
	} 	
				
	content+='</table>';
	content+='<div style="text-align:right;width:'+width+'px">種類：'+(parseInt(key)+1)+'</div>';
	content+='<div style="text-align:right;width:'+width+'px">件數：'+(parseInt(num))+'</div>';
	 if(type!='boxin'  && type!='pickupList' &&type!='noPricePrint' && type!='noPriceShowingPrint' )content+='<div style="text-align:right;width:'+width+'px" id="order_total">總價：<span id="order_confirm_total">'+data.total+'</span></div>';
    
    
	content+='</div>';
	
					
						
			
	
	return content;
}


function orderSend()
{
	if($('#receiver').val()=='')
		if(!confirm('收件人地址空白，仍要繼續?'))
		{
			 $('#popUpBoxEnter').show();
			 return;
		}
		
	if(confirm('請再次確定訂單內容，按確定送出後將無法更改'))
	{
		var commentStr ='';
		$('.productComment').each(function(){
			rowID = this.id.substr(15)	;
			comment = this.value;
			commentStr+=rowID+','+comment+'[#]';
			
		})
		
		
		
	
		
		$.post('/order/order_send',{orderID:$('#orderID').val(),order_comment:$('#order_comment').val(),commentStr:commentStr
		,receiver:$('#receiver').val(),address:$('#address').val(),phone:$('#phone').val(),comID:$('#comID').val()},function(data)
		{
			
			if(data.result==true)
			{
				alert('訂單號碼：'+data.orderNum+'\n訂單已送達，請到訂貨處理狀況觀看出貨狀況');
				onShopping = false;
				$('#product_list').html('');
				$('#carOption').detach();
				shipmentView('shipment',data.addressID);
				closePopUpBox();
				
				
			}
		},'json')	
	}
	
	
}




var order_comment='';
function orderConfirm(type)
{
    loginChk();
	content = '資料傳送中，請稍候..<img src="/images/ajax-loader.gif"/>';
     openPopUpBox(content,200,100,'closePopUpBox');
    
    if(type==0)
        {
            url = "/order/order_confirm";
            data = $("#productList").serialize();
            dataType = 'json';
        }
    else if(type==1)
        {
             url = "/order/pokemon_confirm";
            data = $("#pokemonForm").serialize();
          
            dataType = "json";
            
        }
	var content = '';
		$.ajax({
	   type: "POST",
	   dataType:dataType,
	   url: url,
	   data: data,
	   success: function(data){
         
		   if(data.result==true)
		   {
			   
				content =orderTable(data,'order',"採購中");
                content +='<input type="hidden" id="orderID" value="'+data.orderID+'">';
				
				content += '<select  id="order_receiver" name="receiverID"  onchange="changeReceiver()">'+
			   				'<option value="0">選擇收件人及地址</option>';
			for(each in data.address)
			{
				if(each==0) addressID = data.address[each].id;
				content+= '<option value="'+data.address[each].id+'">'+data.address[each].receiver+'</option>';
			}
			content += '</select>';	
			content +='收件人： <input type="text" name="receiver" id="receiver"></input>';
			content +='<div style="clear:both"></div>';
			content +='地址： <input type="text" name="address" id="address" style=" width:400px"></input>';;
			content +='<div style="clear:both"></div>';
			content +='電話： <input type="text" name="phone" id="phone"></input>';;
            content +='客戶統編： <input type="text" name="comID" id="comID"></input>';;
               content +='email： <input type="text" name="email" id="email"></input>';;
            content +='<div style="clear:both"></div>';   
          content +='客戶載具：<select name="CarrierType" id="CarrierType">'+
                '<option value="">無</option>'+
                '<option value="3J0002">手機條碼</option>'+
                '<option value="CQ0001">自然人憑證</option>'+
                '</select> <input type="text" class="big_text" name="CarrierId1" id="CarrierId1" placeholder="載具條碼無則空白" >';
           content += '捐贈代碼：<input type="text" class="big_text" name="NPOBAN" id="NPOBAN" placeholder="無則空白" >';
			content +='<div style="clear:both"></div>';
		
				
              
    
               
               
               
               
			   	 content +='訂貨備註欄：<textarea name="order_comment" id="order_comment"  onkeyUp = "	order_comment = $(\'#order_comment\').val();"style=" width:900px; height:100px">'+data['orderComment'] +'</textarea>';
			   openPopUpBox(content,1100,280,'orderSend');
				height =parseInt($('#orderTable').css('height').substr(0,$('#orderTable').css('height').length-2))
				popUpBoxHeight(height+300)
				//countTotal(1);
				if(data.address[0].id)
				{
					$('#order_receiver').val(data.address[0].id);
					
				changeReceiver();		   
				}
          
               if(parseInt($('#order_confirm_total').html())>parseInt($('#orderLimmitUsed').html()))
                {
                    alert('訂貨額度超過上限，請通知主管調整上限');
                    $('#popUpBoxEnter').hide();
                    
                    
                }
               
               
			    $('#popUpBoxCancel').unbind('click');
			   $('#popUpBoxCancel').click(function(){
				   
					
				    orderCancel(data.orderID);
				  })
			}
			else 
			{
				alert('訂單沒有成功送出，請確認你的訂貨商品數量')
				
			}	  
	   }
	   })
		 	
	
	
}


function orderCancel(id)
{
	$.post('/order/order_cancel',{orderID:id},function(data){
		if(data.result==true)
		{
			closePopUpBox();
		}
	},'json')
		
	
	
	
	
}

function orderSplit(orderID)
{
	
	var count = 0;
	var errno = 0;
	var splitIDList='';
	$('.order_split').each(function(index)
	{
		if($(this).is(':checked'))
		{
			var id =this.id.substr(12);
			splitIDList += id+',';
			count++;
		}
	
	
		
	})	

	if(count<1) alert('你必須選擇一個清單以上進行分拆');
	else
	{
		splitIDList= splitIDList.substr(0,splitIDList.length-1);
		content='<h2>你確定要這些遊戲移到新的訂單嗎？</h2>';
		content+='<h3>請選擇出貨類型</h3>'+
					'<select id="split_order_type" name="order_type">'+
						'<option value="0">買斷</option>'+
						'<option value="1">寄賣</option>'+
					'</select>';
		content+='訂單類型：訂單已送達';
		content+='<input type="hidden" id="splitOrderID" value="'+orderID+'">';
		content+='<input type="hidden" id="splitIDList" value="'+splitIDList+'">';
		content+='<div id="hiddenPlace" style="display:none">'+$('.popUpBox').html()+'</div>';
		var splitOrderType = $('#order_type').val();
		openPopUpBox(content,800,200,'orderSplitSend');
		$('#split_order_type').val(splitOrderType);
		
	}
		
}


function orderSplitSend()
{

	$.post('/order/order_split',{splitIDList:$('#splitIDList').val(),orderID:$('#splitOrderID').val(),type:$('#split_order_type').val()},
	function(data)
	{
		if(data.result==true)
		{
			

			alert('訂單已成功新增，您可以繼續動作');	
			splitIDList = $('#splitIDList').val();
			splitOrderType = $('#split_order_type').val()
			 openPopUpBox($('#hiddenPlace').html(),1100,200,'orderUpdate');
				for( var key in data.splitData)
				{
					
					$('#orderRow_'+data.splitData[key]).detach();				
				}
			$('#order_type').val(splitOrderType);				 
			 height =parseInt($('#orderTable').css('height').substr(0,$('#orderTable').css('height').length-2))
	  		 popUpBoxHeight(height+300)
		}
		
	},'json')
	
	
	
}

function orderCombine()
{
	var shopName = '';
	var shopStatus = '';
	var orderType = '';
	var count = 0;
	var errno = 0;
	var orderNumList='';
	var orderIDList='';
	$('.order_check').each(function(index)
	{
		if($(this).is(':checked'))
		{
			var id =this.id.substr(12);
			
			if(shopName === '') shopName = $('#shopName_'+id).html();
			if(shopStatus === '') shopStatus = $('#shopStatus_'+id).html();
			if(orderType === '') orderType = $('#orderType_'+id).html();
			if(shopName!=$('#shopName_'+id).html()) errno =1;
			else if(shopStatus!=$('#shopStatus_'+id).html())errno =2;
			else if(orderType!=$('#orderType_'+id).html())errno =3;
			else  
			{	
				
				orderNumList+=$('#orderNum_'+id).html()+',';
			
				orderIDList += id+',';
			}
			
				count++;
		}
	
	
		
	})	
	if(errno==1) alert('你只能合併相同一個店家的清單');
	else if(errno ==2) alert('你只能合併相同狀態的訂單');
	else if(count<=1) alert('你必須選擇兩個清單以上進行合併');
	else
	{
		passToken = true;
		if(errno ==3) 
		{
			
			if(confirm('訂單中有寄賣也有買斷，仍然要合併？'))passToken = true;
			else passToken = false;
			
		}
		
		if(passToken ==true)
		{
			orderNumList = orderNumList.substr(0,orderNumList.length-1);
			orderIDList= orderIDList.substr(0,orderIDList.length-1);
			content='<h2>你確定要合併編號： '+orderNumList+' 的清單嗎？</h2>';
			content+='<h3>請選擇出貨類型</h3>'+
						'<select id="order_type" name="order_type">'+
							'<option value="0">買斷</option>'+
							'<option value="1">寄賣</option>'+
						'</select>';
			content+='訂單類型：'+shopStatus;
			content+='<input type="hidden" id="orderIDList" value="'+orderIDList+'">';
			 openPopUpBox(content,800,200,'orderCombineSend');
			 if(orderType=='寄賣') $('#order_type').val(1);
		}
	}
	
}





function orderCombineSend()
{
	$.post('/order/combine',{orderIDList:$('#orderIDList').val(),order_type:$('#order_type').val()},
	function(data)
	{
		if(data.result==true)
		{
			
			showOrderList("staff",0);
			showOrder(data.orderID,"訂單已送達","staff");
	
		}
		
	},'json')
	
	
	
}

function limitCheck(productID)
{
	if($('#shopID').val()>0)
	{
	buyingNum = parseInt($('#buyingNum_'+productID).html());
	limitNum = parseInt($('#limitNum_'+productID).html());
	nowNum = parseInt($('#nowNum_'+productID).html());

		if((parseInt(buyingNum)+parseInt($('#purchase_num_'+productID).val()))+nowNum>limitNum&&limitNum!=0)
		{
						alert('您已經訂購'+(parseInt(buyingNum)+parseInt($('#purchase_num_'+productID).val()-1))+'個\n'+
						       '您的庫存'+nowNum+'\n'+
								'您的庫存及訂購數量超過上限');
						if(limitNum-parseInt(buyingNum)-nowNum<=0)$('#product_'+productID).detach();
						else $("#purchase_num_"+productID).val(limitNum-parseInt(buyingNum)-nowNum);
		}	
	}
	return 0 ;
}




function chkMinus(productID)
{
	if(fucCheckNUM($("#purchase_num_"+productID).val())==0)
	{
		alert('這不是一個數字');
		$("#purchase_num_"+productID).val(0);
	}
	else if($("#purchase_num_"+productID).val()<=0)
	{
		alert('不可為負值或0');
		$("#purchase_num_"+productID).val(0);
	}

	var ret = limitCheck(productID);

	
	
}	
function orderListTable(type,arive)
{
	
	var  result='';
	if(type=='shipment') 
	{
		result+='<h1>出貨紀錄'+
            '<input type="submit" class="big_button" value="列印揀貨裝箱單" onclick="$(\'#showType\').val(\'boxin\');$(\'#shipmentListForm\').submit()" >'+
            '<input type="submit" class="big_button" value="列印出貨單" onclick="$(\'#showType\').val(\'shipment\');$(\'#shipmentListForm\').submit()" >'+
             '<a href="/order/order_check" target="_blank"><input type="button" class="big_button" value="裝箱檢核頁面" ></a>'+
            '<a href="/order/order_address_list" target="_blank"><input type="button" class="big_button" value="匯出託運表格" ></a>'+
            '<form action="/order/ship_code_back" target="_blank" method="post" enctype="multipart/form-data">'+
'物流回傳:<input type="file" name="file" id="file" /><input  type="submit" name="submit" value="上傳檔案" />'+
'</form>'+

            
            
            '</h1>';
        
			if($('#select_shopID').val()>0)
			{
				result+='<a target="_blank" href="/order/check_sheet/'+$('#select_shopID').val()+'/'+arive+'/'+$('#fromDate').val()+'/'+$('#toDate').val()+'/'+$('#selectOrderType').val()+'" ><input type="button" class="big_button" value="對帳單列印"></a><span id="totalAmount"></span>'
			}

	}
	else if(type=='shipmentWatch')
    {
        result+='<h1>正準備要出貨的清單</h1>';
        
    }
	else if(arive==false)  result+='<h1>訂貨中清單</h1>';
	else if(fucCheckNUM(arive)!=0);
	else result+='<h1>到貨清單</h1>';
	result+='<form id="shipmentListForm"  method="post" action="/order/select_print_out" target="_blank"><input type="hidden" id="showType" name = "showType" value="boxin"><table id="order_list" border="1" style="text-align:center; width:1150px">'+
				'<tr>';
			
	if(type=='shipment'||type=='shipmentWatch') 
	{
		
		result+='<td>出貨單號</td>';
		result+='<td>出貨日期</td>';
				
	}
	else    
	{
		result+='<td>訂單編號</td>';
		result+='<td>下單日期</td>';
	}
					
			result+='<td>店家名稱</td>'+
					'<td>出貨地點</td>'+
					'<td>訂單類別</td>'+
					'<td>訂單總價</td>'+
					'<td >訂單備註</td>';
					
					
	if((type!='shipment' && type!='shipmentWatch') || $('#myshopID').val()!=0) result+='<td>訂單狀況</td><td>物流單號</td>';
	else
	{
		result+= 	'<td>訂單狀況</td>'+
				
					'<td>物流單號</td>'+
					'<td>收款日期</td>'+
					'<td>備查</td>';	
		
	
	}
	if($('#myshopID').val()==0 &&arive!=false )
	{
		result+='<td>發票</td>';
		result+='<td>送達物流</td>';
	}
		result+='<td>查看訂單</td>';
	if($('#myshopID').val()==0)result+='<td>刪除訂單</td>';
					
			result+='</tr>'+
			'</table>';
	result+='</form>';
	if(type=='shipment')
	{
		if($('#myshopID').val()!=0)result+= '<input type="button" value="查看更多" id="moreOrderBtn"onclick="getShipmentList(shipmentListOffset,10,\''+type+'\','+arive+');"class="big_button"/>'	;
		else result+= '<input type="button" value="查看更多" id="moreOrderBtn" onclick="getShipmentList(shipmentListOffset,10,\'staff\','+arive+');"class="big_button"/>'	;
		
		
	}
	else if(type!='shipmentWatch')		result+= '<input type="button" value="查看更多" id="moreOrderBtn" onclick="getOrderList(orderListOffset,10,\''+type+'\','+arive+');"class="big_button"/>';
	
	return result;
}



function checkInvoice(id,total,shopID,finishTime)
{

  ;
	$.post('/order/check_invoice',{id:id,total:total,shopID:shopID,selectOrder: $('#selectOrderStatus').val()},
	function(data)
	{
		
		 
		if(data.result==true) 
		{
            
             $('#invBtn_'+id).css('background-image','url(/images/index_redbutton_02.png)');
			$('#order_'+id).css('background-color','white');
			if($('#report_invoice').is(':checked'))$('#order_'+id).hide();
          
               
		}
		else
		{
			
			 $('#invBtn_'+id).css('background-image','url(/images/index_purplebutton_02.png)');
		}
		if(finishTime=='0000-00-00 00:00:00'&& data.order.type==0 )
                    {
			         $('#order_'+id).css('background-color','#FFFF33');
			
                    }
                    else
                    {

                         $('#order_'+id).css('background-color','white');
                    }
		
	},'json')
		
	
	
}


var orderListOffset= 0;
function showOrderList(type,arive,shopID)
{

	
	if(onShoppingCheck()==false) return;
	else $('#clientOrderQuery').html('');
	$('#carOption').detach();
	$('#clientOrderQuery').html('')
	$('#productQuery').html('');
	$('#productSelect').detach();
	shippingWrong = '';
	waitString ='';
	if(arive==0||arive==6)
	{
		$('#product_list').html(orderListTable(type,arive));
		orderListOffset= 0;
	
		getOrderList(orderListOffset,15,type,arive);
	}
	else if(arive==5)
	{
		
		shipmentView(type,0,shopID);
	}
	else
	{
		
		showShipmentList(type,0);
	}
		
}


function getOrderList(offset,num,type,arive)
{

	orderListOffset= offset+num;
	if($('#selectOrderType').length>0) orderType = $('#selectOrderType').val();
	else orderType = 2;
	$.post('/order/get_order_list',{offset:offset,num:num,arive:arive,shopID:$('#select_shopID').val(),orderType:orderType},function(data)
	{
		if(data.result==true)
		{
			if(offset==0)$('#product_list').html(orderListTable(type,arive));		
			for( var key in data.orderList)
			{
				
				var content ='<tr id="order_'+data.orderList[key].id+'">';
					content+=
						'<td id="orderNum_'+data.orderList[key].id+'">o'+data.orderList[key].orderNum+'</td>'+
						'<td>'+data.orderList[key].orderTime+'</td>'+
						'<td id="shopName_'+data.orderList[key].id+'">'+data.orderList[key].shopName+'</td>'+
						'<td id="shopName_'+data.orderList[key].id+'">'+data.orderList[key].receiver+'</td>';
					if(data.orderList[key].type!=1) content+='<td id="orderType_'+data.orderList[key].id+'">買斷</td>';
					else content+='<td id="orderType_'+data.orderList[key].id+'"><span style="color:blue">寄賣</span></td>';
					content+='<td>'+data.orderList[key].total+'</td>'+
						'<td>'+data.orderList[key].orderComment+'</td>'+
						'<td>'+data.orderList[key].orderStatus+'</td>'+
						
						'<td>'+
							'<input type="hidden" id ="ordetType_'+data.orderList[key].id+'" value="'+data.orderList[key].type+'">'+	
							'<input type="button" value="查看" onclick="showOrder('+data.orderList[key].id+',\''+data.orderList[key].status+'\',\'watch\')"class="big_button" >'+
						'</td>';
						
				if(type=='staff')content+='<td><input type="button" value="刪除" onclick="deleteOrder('+data.orderList[key].id+')"class="big_button" ></td>';

						content+='</tr>';
	
				$('#order_list').append(content)
					
				
			}
		}
		else
		{
				$('#order_list').append('沒有其他資料了');
				$('#moreOrderBtn').hide();
		}
	},'json')	
	
	
}
function orderAddressUpdate()
{
     if($('#ECContent')) ECOrderUpdate();
    else
    $.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/order_address_update",
	   data: $("#orderForm").serialize(),
	   success: function(data){
		   
		   if(data.result ==true) 
		   {
			  showShipmentList('staff',$('#selectOrderStatus').val());
			   closePopUpBox();
		   }
	   }
	   
	   })
    
}
function orderUpdate()
{
	
    if($('#address').val()==''){
        
        alert('請輸入地址');
        $('#popUpBoxEnter').show();
        return;
        
    }
    if($('#receiver').val()==''){
        
        alert('請輸入收件人');
         $('#popUpBoxEnter').show();
        return;
        
    }
   
    if($('#ECContent')) ECOrderUpdate();
    
    
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/order_update",
	   data: $("#orderForm").serialize(),
	   success: function(data){
		   
		   if(data.result ==true) 
		   {
               showShipmentList('staff',$('#selectOrderStatus').val());
			  // showOrderList('staff','0');
			   closePopUpBox();
		   }
	   }
	   
	   })
	
	
}

function shipmentUpdate()
{
	
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/shipment_update",
	   data: $("#shipmentForm").serialize(),
	   success: function(data){
		   
		   if(data.result ==true) 
		   {
			   shipmentView('staff')
			   closePopUpBox();
		   }
	   }
	   
	   })
	
	
}

function deleteShipment(shipmentID)
{
	
	$('#delBtn_'+shipmentID).hide();
	
	if(confirm('你確定要刪除？'))
	{
		$.post('/order/delete_shipment',{shipmentID:shipmentID},function(data)
		{
			if(data.result==true)
			{	
			
				$('#order_'+shipmentID).detach();
			}
			else
			{
				 alert('無法刪除')
				 $('#delBtn_'+shipmentID).show();
			}
		},'json')
	}
	
}


function deleteOrder(orderID)
{
	if(confirm('你確定要刪除？'))
	{
		$.post('/order/delete_order',{orderID:orderID},function(data)
		{
			if(data.result==true)
			{	
			
				$('#order_'+orderID).detach();
			}
		},'json')
	}
	
}

function deleteBackOrder(backID)
{
	if(confirm('你確定要刪除？'))
	{
		$.post('/order/delete_back_order',{backID:backID},function(data)
		{
			if(data.result==true)
			{	
			
				$('#order_'+backID).detach();
			}
			else{'此單無法刪除囉'}
		},'json')
	}
	
}

function recommendOrder()
{
	$.post('/order/order_recomment',{},function(data){
		if(data.result==true)	recommendTable(data.product)
		
	},'json')	
	
	
}

function consignmentOrder()
{
	if(confirm('訂單於每周一會自動產生，你現在要手動產生嗎？'))
	{
		$.post('/order/attach_consignment',{},function(data){
			if(data.result==true)	location.reload();
			
		},'json')	
	}
	
}

var dateHideToken =  false ;
function dateHide()
{
		if(!dateHideToken) 
		{
			dateHideToken = true;	
			$('#dateHide').val('顯示有日期的');
			$('.preTime').each(
				function(){
					var ID = this.id.substr(8)
					
						if($('#'+this.id).val()!='null')$('#orderRow_'+ID).hide();		
					
				}	
				
			)
		}
		else
		{
			dateHideToken = false;	
			$('#dateHide').val('隱藏有日期的');
			$('.preTime').each(
				function(){
					var ID = this.id.substr(8)
					$('#orderRow_'+ID).show();		
					
				}	
				
			)
			
		}
	
	
	
}


function waitNumChoose()
{
	var num = $('#waitNum').val();
		
		
			$('.recommendNum').each(
				function(){
					var ID = this.id.substr(13)
					if(parseInt($('#'+this.id).html())<num)$('#orderRow_'+ID).hide();		
					else $('#orderRow_'+ID).show();	
					
				}	
				
			)
			

	
	
}





function recommendTable(product)
{
	var Zh=new Array('項次','編號','中文','英文','語言','定價','定貨折數','定貨價格','供應商','應訂貨數量','預計到貨時間','訂貨店家','小計');
	var Eng = new Array('number','productNum','ZHName','ENGName','language','price','buyDiscount','buyPrice','supplierName','recommendNum','preTime','shopList','subtotal');
	

		var content = '<h1>建議訂貨清單</h1>'+
		'<input type="button" id="dateHide" onclick="dateHide()" value="隱藏有日期的">' +
		'等待量大於<select id="waitNum" onchange="waitNumChoose()">'+
			'<option value="1">1</option>'+
			'<option value="2">2</option>'+
			'<option value="3">3</option>'+
			'<option value="4">4</option>'+
			'<option value="5">5</option>'+
		'</select>'+
		'<option value="1">1</option>'+
		'<table id="recommentTable" border="1" width="1100px">';
		content+='<tr style="background-color:#FFEBEB">';
		for( var key in Zh)
		{	
		
				content+='<td>'+Zh[key]+'</td>';
		}
		content+='</tr>';
		for( var key in product)
		{
			data = product[key];
			content+='<tr class="orderRow" id="orderRow_'+data.productID+'">';
			for(row in Zh)
			{
				if(Eng[row]=='productNum')content+='<td>'+fillZero(data['productNum'])+'</td>';
				else if(Eng[row]=='number')content+='<td>'+(parseInt(orderNumber++)+1)+'</td>';
				else if(Eng[row]=='buyDiscount')content+='<td id="purchaseCount_'+data.id+'">'+data[Eng[row]]+'%</td>';
				else if(Eng[row]=='subtotal')
				{
					 num = data['recommendNum'];
					content+='<td id="subtotal_'+data.productID+'">'+data['buyPrice']*num+
							 '</td>';
				}
				
				else if(Eng[row]=='preTime')
				{
					content+='<td><input type="text"  class="preTime" id="preTime_'+data.productID+'"  onfocus="preTimeChoose('+data.productID+')" value="'+data.preTime+'"  ></td>';
	
					
					
				}
				
				else if (data[Eng[row]])
				{
					content+='<td class="'+Eng[row]+'" id="'+Eng[row]+'_'+data.productID+'">'+data[Eng[row]]+'</td>';
					
				}
				else content+='<td></td>';
			}
		}	
		content+='</tr>';
		content+='</table>';
		openPopUpBox(content,1100,200,'closePopUpBox');
		height =parseInt($('#recommentTable').css('height').substr(0,$('#recommentTable').css('height').length-2))
		popUpBoxHeight(height+200);		
	
	
	
		for( var key in product)
		{
			id=product[key].productID
			var dates = $('#preTime_'+id).datepicker({
								dateFormat: 'yy-mm-dd' ,
								
								yearRange: '1930',
								monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
								changeMonth: true,
								numberOfMonths: 1,
								onSelect: function( selectedDate ) {
									var option = this.id == "from" ? "minDate" : "maxDate",
										instance = $( this ).data( "datepicker" ),
									
										date = $.datepicker.parseDate(
											instance.settings.dateFormat ||
											$.datepicker._defaults.dateFormat,
											selectedDate, instance.settings );
									dates.not( this ).datepicker( "option", option, date );
								},
								onClose:function(data)
								{
									
									//alert(id);
									 changePreTime(preTimeID)
									
								}
							});			
		
		
			
		}
	
	
	
	
}

var preTimeID = 0
function preTimeChoose(productID)
{
	 changePreTime(preTimeID)
	preTimeID = productID;
}

function changePreTime(productID)
{


		$.post('/order/change_pretime',{productID:productID,preTime:$('#preTime_'+productID).val()},function(data)
		{
		
			if(data.result==true)
			{
		
				if(data.phantri.length>0)
  					if(confirm('有人在問題解決中心詢問本次相關產品到貨時間，是否一律以到貨時間：'+data.preTime+'回覆，並且結案？'))
					{
						 preTimeSubmit(preTime,data.phantri);
					}
				 
				return;
				
			}
			else alert('錯誤');
			
			
			
			
		},'json')

	
}




var  watchType ='';
function showOrder(orderID,status,type)
{

	$.post('/order/get_order',{orderID:orderID},function(data)
	{
		if(data.result==true)
		{
			orderNumber = 0;
			if(data.order.type==1 || data.order.shopID==666 ) c='';
			else c = 'checked="checked"';
		;
			var	 content ='<div id="orderAppendQuery"></div>';
			content +='<input type="hidden" id="shopDiscount" value="'+data.order.discount+'">'+
						'<input type="hidden" id="shopID" value="'+data.order.shopID+'">'+	
						'<label><input type="checkbox" id="create_discount" '+c+' >折數自動調整</label>'+
						'<label><input type="checkbox" id="repeat_item" '+c+' >可輸入重複品項</label>'+
					'<form id="orderForm">'+
						'<input type="hidden" name="orderID" value="'+data.order.id+'">'+
						'<div style="text-align:left">訂單編號：o'+data.order.orderNum+' 訂貨店家：'+data.order.name+' 訂貨時間：'+data.order.orderTime.substr(0,16)+' 訂單狀態:'+data.order.status;
			if(data.order.type==0)content+=	' 訂單類別：買斷';
			else if(data.order.type==1)content+=	' 訂單類別：寄賣';
			else content+=	' 訂單類別：買斷(調貨)';
			 
			
			content+='</div>';
				
			
			content +='<div id="orderTableContent"></div>';
		
			if(type=='watch')
			{
				if(data.order.orderComment!=0&&data.order.orderComment!='')
				{
					content +='<h2>訂單備註：</h2><div style=" background-color:#CCC">'+
								data.order.orderComment+
								'</div>';
					
				}
				
			}
			else
			{
				content +='<h2>訂單備註：</h2><div style=" background-color:#CCC">'+
								'<textarea name="orderComment" style=" width:1000px; height:100px">'+data.order.orderComment+'</textarea>'+
								'</div>';
							
			}
	       		content +='<div><input type="button" class="big_button" value="列印定貨單" onclick="order_print('+data.order.id+')"></div>';
                content +='<div><input type="button"  class="big_button"  value="卡套配量預估" onclick="cardSleeveExp('+data.order.id+')"/ ></div>';
        if(data.order.status !='採購中')    
                content +='<div><input type="button" class="big_button" value="複製訂單" onclick="copyOrder('+data.order.orderNum+')"></div>'	
                
            if(data.order.shopID==666)content +='<div style="display:none">';
                    
                       
			if(type=="staff"&&data.order.status!="已到貨"&&data.order.status!="已送達物流")
			{
                
                   
                
                
                content +='訂單類別：<select name="order_type" id="order_type">'+
                                '<option value="0">買斷</option>'+
                                '<option value="1">寄賣</option>'+
                            '</select>';
              

                content +='訂單狀態：<select name="order_staus" id="order_staus">'+
                                '<option value="1">訂單已送達</option>'+
                            '</select>';
                content +='<div class="divider"></div>';

					
						
			}
            
            content += '<select  id="order_receiver" name="receiverID"  onchange="changeReceiver()">'+
			   				'<option value="0">選擇收件人及地址</option>';
			for(each in data.address)
			{
				content+= '<option value="'+data.address[each].id+'">'+data.address[each].receiver+'</option>';
			}
			content += '</select>';	
			content +='收件人： <input type="text" class="big_text" name="receiver" id="receiver"></input>';
			content +='<div style="clear:both"></div>';
			content +='地址： <input type="text" class="big_text" name="address" id="address" style=" width:400px"></input>';;
			content +='<div style="clear:both"></div>';
			content +='電話： <input type="text" class="big_text" name="phone" id="phone"></input>';;
            content +='客戶統編： <input type="text" class="big_text" name="comID" id="comID"  placeholder="無則空白"></input>';;
            content +='email： <input type="text" name="email" id="email"></input>';;
            content +='<div style="clear:both"></div>';   
            content +='客戶載具：<select name="CarrierType" id="CarrierType">'+
                '<option value="">無</option>'+
                '<option value="3J0002">手機條碼</option>'+
                '<option value="CQ0001">自然人憑證</option>'+
                '</select> <input type="text" class="big_text" name="CarrierId1" id="CarrierId1" placeholder="載具條碼無則空白" >';
           content += '捐贈代碼：<input type="text" class="big_text" name="NPOBAN" id="NPOBAN" placeholder="無則空白" >';
               
			content +='<div style="clear:both"></div>';
            if(data.order.shopID==666)content +='</div>';
             
			content +='</form><div id="cardSleeveinf" style="background-color:#DDDDDD"></div>';
		//	if(type=="staff"&&data.order.statusID!=2&&data.order.statusID!=3)content +='<input type="button" class="big_button" value="列印出貨單" onclick="order_print()">';
	//		else content +='<input type="button" class="big_button" value="列印出貨單" onclick="window.open(\'/order/print_out/'+data.order.id+'\', \'_blank\')">';
				if(type=='staff') fun = 'orderUpdate';
				else fun = 'orderAddressUpdate';
   
          
;	      if(data.order.shopID==666)
            {
                content +='<form id="ECOrderForm"><div id="ECContent">'+
                    '<input type="hidden" name="orderID" class="big_text" value="'+data.order.id+'">'+
                    '<select id="ECOrderPlatformID" name="platformID"></select>'+
                    '平台訂單編號： <input type="text" class="big_text" name="ECOrderNum" id="ECOrderNum" value="'+data.ECOrder.ECOrderNum+'"/>'+        
                    '<select id="transportID" name="transportID">'+
                        '<option value="2">宅配</option>'+
                        '<option value="3">7-11</option>'+
                        '<option value="4">三大超商</option>'+
                        '<option value="5">海外</option>'+
                    '</select><br/>'+
                    '收件人： <input type="text"   class="big_text"name="receiverName" id="receiverName" value="'+data.ECOrder.receiverName+'"/>'+
                    '電話： <input type="text"   class="big_text" name="receiverPhone" id="receiverPhone" value="'+data.ECOrder.receiverPhone+'"/>'+
                    '會員編號： <input type="number"  class="big_text" name="memberID" id="memberID" value="'+data.ECOrder.memberID+'" />'+
                    '<br/>'+
                    '客戶統編： <input type="text" class="big_text" name="comID" id="comID"  value="'+data.ECOrder.comID+'" placeholder="無則空白"></input>'+
                     '客戶載具：<select name="CarrierType" id="CarrierType">'+
                '<option value="">無</option>'+
                '<option value="3J0002">手機條碼</option>'+
                '<option value="CQ0001">自然人憑證</option>'+
                '</select> <input type="text" class="big_text" name="CarrierId1" id="CarrierId1" value="'+data.ECOrder.CarrierId1+'" placeholder="載具條碼無則空白" >'+
                  '捐贈代碼：<input type="text" class="big_text" name="NPOBAN" id="NPOBAN" value="'+data.ECOrder.NPOBAN+'"  placeholder="無則空白" ><br/>'+
                    
                    
                    '地址： <input type="text" class="big_text" name="receiverAddress" id="receiverAddress" value="'+data.ECOrder.receiverAddress+'"style="width:400px"/><br/>'+
                    'email： <input type="text" class="big_text" name="receiverEmail" id="receiverEmail" value="'+data.ECOrder.email+'" style="width:400px"/><br/>'+   			
                    '<select id="payway" name="payway">'+
                        '<option value="1">貨到付款</option>'+
                        '<option value="0">平台支付</option>'+ 
                    '</select><br/>'+    
                     '<h2>平台備註：</h2><div style=" background-color:#CCC">'+
								'<textarea name="remark" style=" width:1000px; height:100px">'+data.ECOrder.remark+'</textarea>'+
								'</div>'+ 
                   '</div></form>';

                     content +='<div class="divider"></div>';
 
            
                    
            }
            
             if(type=='staff')
			content +=	'<input id="copyID" type="number" value="'+(data.order.orderNum-1)+'"/><input type="button" value="取得資料" onclick="getOrderInformation()">';
            
				watchType = type;
				 openPopUpBox(content,1100,200,fun,true);
				 ecPlatformSelector(data.ECOrder.platformID);
            $('#payway').val(data.ECOrder.payway);
             $('#transportID').val(data.ECOrder.transportID);// ECContent
             $('#CarrierType').val(data.ECOrder.CarrierType);
          
				 if( typeof data.address[0]!= 'undefined')
				{
                    if(data.order.addressID!=0)$('#order_receiver').val(data.order.addressID);
					else $('#order_receiver').val(data.address[0].id);
				    changeReceiver();	   
				}
				
				 $('#orderTableContent').html(orderTable(data,type,status));
				
				 $('#order_type').val(data.order.type);
                
				 $('#order_staus').val(data.order.statusID);
				height =parseInt($('#orderTable').css('height').substr(0,$('#orderTable').css('height').length-2))
				popUpBoxHeight(height+300);
				if(type=='staff')
				{
					if(data.order.status!="已到貨"&&data.order.status!="已送達物流")queryProduct('orderAppend','select');
					popUpBoxHeight(height+500);
					
				}
				
			//countTotal(1);
		}
		
	},'json')	
	
	
}

function copyOrder(orderNum)
{
    
    v = $('#orderTableContent').html();

    closePopUpBox(false);
    
    content = '<form id="copyFrom"><h1>你正在複製訂單O'+orderNum+'</h1><input type="hidden" name="orderNum" value="'+orderNum+'">'+v;

	$.post('/system/get_shop',{token:1,show:1},function(data){
				if(data.result==true)
				{
					content +='<h1>請選擇出貨類型</h1>'+
							'<select id="create_type" name="create_type">'+
								'<option value="0">買斷</option>'+
								'<option value="1">寄賣</option>'+
							'</select>'+
                            '<h1>請選擇出貨店家</h1>'+
                            '<input type="button" value="全部選取" class="big_button" onclick="$(\'.shop_check\').attr(\'checked\',true)">'+
		   			        '<input type="button" value="全部取消" class="big_button" onclick="$(\'.shop_check\').attr(\'checked\',false)">'+
                            '<div id="copy_shopID">'
                    for( var key in data.shopData)
					{
						if(data.shopData[key].shopID<800)
                            {
							 content+='<label><input type="checkbox" name="shop['+data.shopData[key].shopID+']"'+
                                                     'class="shop_check" value="1" />'+data.shopData[key].name+'</label>';
                            }

                                                 
					} 
                        
                        
                        
                        
                    content+='</div>'+
                            '<input type="button" class="big_button" onclick="startCopy('+orderNum+')" value="開始複製" >'+
                            '</form>'
							;
					
					//openPopUpBox(content,500,200,'createSend',true);
                    $('#product_list').html(content);
				
				  //$( "#create_shopID" ).combobox();
                       
				}
			
		
		},'json')
    
    
    
    
    
    
    
}

function startCopy(orderNum)
{ 
 	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/copy_order",
	   data: $("#copyFrom").serialize(),
	   success: function(data){
		  
		   showOrderList('staff','0')
	   }
	   
	   })
	
    
    
    
}

function  getOrderInformation()
{
		orderNum = $('#copyID').val();
		$.post('/order/get_order',{orderNum:orderNum},function(data)
		{
			
			if(data.result==true)
			{
				
			
					for(key in data.product)
					{
						orderAppendTable(data.product[key],data.product[key].OSBANum);
						
					}
					
					
					
				countTotal(99);	
				
			}
				
			
			
		},'json');
	
	
	
}



function changeReceiver()
{

	$.post('/order/get_address',{id:$('#order_receiver').val()},function(data)
	{
		if(data.result==true)
		{
			$('#receiver').val(data.record.receiver);
			$('#address').val(data.record.address);
			$('#phone').val(data.record.phone);
			$('#comID').val(data.record.comID);
            $('#CarrierType').val(data.record.CarrierType);
            $('#CarrierId1').val(data.record.CarrierId1);
            $('#NPOBAN').val(data.record.NPOBAN);
		}
		
		
		
	},'json')

}

function print_order(orderID)
{

	$.post('/order/get_order',{orderID:orderID},function(data)
	{
		if(data.result==true)
		{
			orderNumber = 0;
			var	 content ='<h1 style="text-align:center; font-size:18pt;font-weight:bold">報　價　單</h1><div id="orderAppendQuery"></div>';
			content +='<input type="hidden" id="shopDiscount" value="'+data.order.discount+'">'+
						'<input type="hidden" id="shopID" value="'+data.order.shopID+'">'+	
					'<form id="orderForm">'+
						
						'<input type="hidden" name="orderID" value="'+data.order.id+'">'+
						'<div style="text-align:left; float:left; font-size:18pt;font-weight:bold">'+data.order.receiver+' 台照 </div><div style=" float:right;font-size:16pt"">訂貨日期：'+data.order.orderTime.substr(0,11)+'訂單編號：o'+data.order.orderNum;
			if(data.order.type==0)content+=	' 訂單類別：買斷';
			else if(data.order.type==1) content+=	' 訂單類別：寄賣';
			 else content+=	' 訂單類別：買斷(調貨)';
			
			content+='</div>';
			
			content +='<div id="orderTableContent"></div>';
		
		
				if(data.order.orderComment!=0&&data.order.orderComment!='')
				{
				content +='<h2>訂單備註：</h2><div style=" background-color:#CCC">'+
								data.order.orderComment+
								'</div>';
				}
		
			content +='</form>';
			$('.product').html(content)	
				 $('#orderTableContent').html(orderTable(data,'watchPrint','訂單已送達'));
			
			result = '<h3>總額  新台幣 '+moneyTurn(data.order.total)+'   元整</h3>'+
			'<div style="float:left">'+
				'報價廠商名稱：幻遊天下股份有限公司<br/>'+                   
				'營利事業統一編號：53180059<br/>'+
            '匯款資訊：兆豐國際商業銀行(017)板橋分行20609-013372<br/>'+
			'</div>'+
                                                               
			'<div style="float:right">'+
				
				'廠 商 電 話：02-86719616<br/>'+
				'廠 商 傳 真：02-86719006<br/>'+
				'廠 商 電 郵：service@phantasia.tw<br/>'+
			'</div>'+
			'<div style="clear:both"></div>'+
			'<img src="/images/seal.png" style="float:right;width:4cm"/ >';
			
			$('.product').append(result);
            $('#printTitle').html(data.order.receiver+data.order.orderTime.substr(0,11)+' o'+data.order.orderNum);
			//countTotal(1);
		}
		
	},'json')	
	
	
}

function cardSleeveExp(orderID)
{
    
    $.post('/order/card_sleeve_exp',{orderID:orderID},function(data)
          {
        
            if(data.result==true)
            {
                
                content= '<h1>卡套數量預估</h1>';
                
                for(key in data.product)
                    {
                      content+= data.product[key]['ZHName']+'X'+data.product[key]['buyNum']+':'+data.product[key]['cardSleeveInf'];
                        if(data.product[key]['cardSleeveInf']=='') content+='<br/>';
                        
                        
                    }
                content+='<h1>卡套數量合計</h1><form id="cardInf"><input type="hidden" name="orderID" value="'+orderID +'">' ;
                for(key in data.card)
                    { 
                        var  n = Math.ceil(data.card[key]['need'] / data.card[key]['pack']);
                        content+=data.card[key]['name']+
                        '<input type="hidden" name="productID[]" value="'+data.card[key]['productID'] +'">'   + 
                        ' X'+n+'包'+
                        '<input type="hidden" name="OSBANum[]" value="'+n +'"><br/>'      
                        
                        
                    }
                content+='</form><input type="button" class="big_button" value="將卡套加入訂單" onclick="addCard()">';
                $('#cardSleeveinf').html(content);
                 popUpBoxHeight(0);
            }
        
    },'json')
    
    
    
    
    
}


function ZHmoney(dig)
{
	switch(dig)	
	{
		case 0: return '零';
		case 1: return '壹';
		case 2: return '貳';
		case 3: return '參';
		case 4: return '肆';
		case 5: return '伍';
		case 6: return '陸';
		case 7: return '柒';
		case 8: return '捌';
		case 9: return '玖';
			
		
		
		
		
	}
	
	
	
}


function moneyTurn(total)
{
	//個位
	a = ZHmoney(total%10);
	b = ZHmoney(parseInt(total%100/10));
	c = ZHmoney(parseInt(total%1000/100));
	d = ZHmoney(parseInt(total%10000/1000));
	e = ZHmoney(parseInt(total%100000/10000));	
	f = ZHmoney(parseInt(total%1000000/100000));	
	
	return f+' 拾 '+e+' 萬 '+d+' 千 '+c+' 百 '+b+' 拾 '+a;
	
	
	
}


function addCard()
{
    
    $.ajax({
	   type: "POST",
	   dataType:"json",
       async: false ,
	   url: "/order/add_to_order",
	   data:$("#cardInf").serialize(),
	   success: function(data){
           if(data.result==true)showOrder(data.orderID,'訂單已送達','watch');
           
           
           
       }})
        
        
        
    
    
}
function print_box_cehck(shipmentID,showing)
{
    var	 content;
  $.ajax({
	   type: "POST",
	   dataType:"json",
       async: false ,
	   url: "/order/get_shipment",
	   data:'shipmentID='+shipmentID+'&showing='+showing,
	   success: function(data){
		  
		  if(data.result==true)
		{
			
			
             account = '<div style="float:left;font-size=14pt";>s'+data.order.shippingNum+'</div><div style="float:left; font-weight:bold;margin-bottom:10px; ">開始出貨時間：<br/><span style="font-size:60pt">___:___</span><br/></div>';
			
            content ='<div id="orderAppendQuery"></div>';
			content +='<input type="hidden" id="shopDiscount" value="'+data.order.discount+'">'+
					 '<form id="orderForm">'+
					 
						'<input type="hidden" name="orderID" value="'+data.order.id+'">'+
						'<div style="width:1000px;float:left;">'+
	
						'</div>'+
						'<div style="clear:both"></div>'+
						'<div style="text-align:left;">'
			orderNum = 0;
				
			if(showing==1)
			{
				
				sliceData = {product:'',total:0};
				for(key in data.product )
				{
				
					if(data.product[key].orderNum!=orderNum)
					{
						
						if(orderNum!=0)
						{
							if(showtype=='boxin') content+=orderTable(sliceData,'boxin','訂單已送達',data.order.shippingNum);
							else if(price==1)content +=orderTable(sliceData,'showingPrint','訂單已送達',data.order.shippingNum);
							else content +=orderTable(sliceData,'noPriceShowingPrint','訂單已送達',data.order.shippingNum);
                        
						}
						sliceData['product'] = new Array;	
						orderNum = data.product[key].orderNum;
						sliceData.total = 0 ;
					}
					sliceData.total += data.product[key]['sellNum'] *data.product[key]['sellPrice'];
					sliceData.product.splice(sliceData.product.length,0,data.product[key])
					
				}
                if(showtype=='boxin') content+=orderTable(sliceData,'boxin','訂單已送達',data.order.shippingNum);
				else if(data.order.showPrice==1)content +=orderTable(sliceData,'showingPrint','訂單已送達',data.order.shippingNum);
				else content +=orderTable(sliceData,'noPriceShowingPrint','訂單已送達',data.order.shippingNum);
				if(price==1)content +='<h2>總價：'+data.total+'</h2>';
			
			}
			else 
			{
                 
                if(showtype=='boxin') content+=orderTable(data,'boxin','訂單已送達',data.order.shippingNum);
				else if(data.order.showPrice==1)content +=orderTable(data,'print','訂單已送達',data.order.shippingNum);
				else content +=orderTable(data,'noPricePrint','訂單已送達',data.order.shippingNum);
			}
			content +='</form>';
			
			
			if(data.order.shipmentComment!=0&&data.order.shipmentComment!='')
			{
			content +='<h2>訂單備註：</h2><div>'+
							data.order.shipmentComment+
							'</div>';
			}						
			$('.product').html(content)		;
           
			//countTotal(1);
  
	   }
       }})
	    return content;
}



function print_out(shipmentID,showing,price,showtype)
{
    var	 content;
  $.ajax({
	   type: "POST",
	   dataType:"json",
       async: false ,
	   url: "/order/get_shipment",
	   data:'shipmentID='+shipmentID+'&showing='+showing,
	   success: function(data){
		  
		  if(data.result==true)
		{
			   if(data.order.type==0)type=' 買斷';
							else if(data.order.type==1) type=	'寄賣';
							else type=	'買斷(調貨)'
                            
            var account = '<div style="float:left ;" ><h1 style="font-size:20pt">S'+data.order.shippingNum+'<h1>'+
                            '<img src="/images/phalogo.png" style="float:left; height:50px;">'+
                    '<div style="float:left;margin-left:5px; font-size:20pt"><span style="font-size:18pt">'+type+'</span><br/>'+padLeft(data.order.shopID,4)+
						'</div>'+
                    '</div>';                
                            
			
				 account += 	
                                '<div style="float:left; font-weight:bold;margin-bottom:10px; font-size:12pt;width:80% ">'+
								'幻遊天下股份有限公司'+
								'瘋桌遊益智遊戲專賣店 出貨單<br/>'+
								'新北市板橋區南雅南路二段11-26號1F '+
								'電話：02-8671-9616 傳真：02-8671-9006<br/>'+
								'兆豐國際商業銀行(017)板橋分行 '+
								'幻遊天下股份有限公司 20609013372<br/>'+

							'</div>';
				
		
            
            if(showtype=='boxin') account = '<div style="float:left;font-size:24pt";><span style="font-size:50pt">'+data.order.shippingNum+'</span><br/>裝箱單</div><div style="float:left;margin-left:20px; font-weight:bold;margin-bottom:10px; ">請填入裝箱時間：<br/><span style="font-size:60pt">___:___</span><br/></div>';
				 content ='<div id="orderAppendQuery"></div>';
            content +='<div style="text-align:center"><h2></h2></div>';
			content +='<input type="hidden" id="shopDiscount" value="'+data.order.discount+'">'+
					 '<form id="orderForm">'+
						'<input type="hidden" name="orderID" value="'+data.order.id+'">'+
						'<div style="width:100%;float:left;">'+account;
							
            
                if(data.order.thisComID) comID = data.order.thisComID;
                    else comID = data.order.comID;
            if(showtype=='boxin')
                {
                    content+=
							'<div style="float:left;font-weight:bold;font-size:12pt; width:30%">'+
								'出貨單號： s'+data.order.shippingNum+'<br/>'+data.order.shippingTime+'<br/>'+
								'客戶名稱：'+data.order.name+'<br/>統一編號：'+comID+'<br/>收件人：'+data.order.receiver+'<br/>'+
                            '</div>';
                    
                    
                }
            else 
            {
                content+=
							'<div style="float:left;font-weight:bold;font-size:12pt; width:80%">'+
								'出貨單號： s'+data.order.shippingNum+' '+data.order.shippingTime+'<br/>'+
								'客戶名稱：'+data.order.name+' 統一編號：'+comID+' 收件人：'+data.order.receiver+'<br/>'+
                            '</div>';
				
                                
            }              ;
							
						content+='<div style="clear:both"></div>'+
						'<div style="text-align:left;">'
			orderNum = 0;
				
			if(showing==1)
			{
				
				sliceData = {product:'',total:0};
				for(key in data.product )
				{
				
					if(data.product[key].orderNum!=orderNum)
					{
						
						if(orderNum!=0)
						{
							if(showtype=='boxin') content+=orderTable(sliceData,'boxin','訂單已送達',data.order.shippingNum);
							else if(data.order.showPrice==1&&price!=0)content +=orderTable(sliceData,'showingPrint','訂單已送達',data.order.shippingNum);
							else content +=orderTable(sliceData,'noPriceShowingPrint','訂單已送達',data.order.shippingNum);
                        
						}
						sliceData['product'] = new Array;	
						orderNum = data.product[key].orderNum;orderAddressUpdate
						sliceData.total = 0 ;
					}
					sliceData.total += data.product[key]['sellNum'] *data.product[key]['sellPrice'];
					sliceData.product.splice(sliceData.product.length,0,data.product[key])
					
				}
                if(showtype=='boxin') content+=orderTable(sliceData,'boxin','訂單已送達',data.order.shippingNum);
				else if(data.order.showPrice==1&&price!=0)content +=orderTable(sliceData,'showingPrint','訂單已送達',data.order.shippingNum);
				else content +=orderTable(sliceData,'noPriceShowingPrint','訂單已送達',data.order.shippingNum);
				if(data.order.showPrice==1)content +='<h2>總價：'+data.total+'</h2>';
			
			}
			else 
			{
                 
                if(showtype=='boxin') content+=orderTable(data,'boxin','訂單已送達',data.order.shippingNum);
				else if(data.order.showPrice==1&&price!=0)content +=orderTable(data,'print','訂單已送達',data.order.shippingNum);
				else content +=orderTable(data,'noPricePrint','訂單已送達',data.order.shippingNum);
			}
			content +='</form>';
			
			
			if(data.order.shipmentComment!=0&&data.order.shipmentComment!='')
			{
			content +='<h2>訂單備註：</h2><div>'+
							data.order.shipmentComment+
							'</div>';
			}
            
             if(data.order.shopID==666) content+= '<h2><table border="1" width="100%" style="color:red"><tr><td>！！！此單不用裝箱！！！！</td></tr></table></h2>';
            content+='<div style="clear:both"></div><h2>出貨單號： s'+data.order.shippingNum+'</h2>';
           if(showtype=='shipment' && data.order.showPrice   ==1 ){
                    
               if(data.invoice.length>0) var t = '';
               else t = '欲查看發票資訊可至<img src="'+data.order.urlCode+'&e=L&s=2&v=5">';
               
                  content+='<div style="height:6cm" id="invoiceFrame_'+data.order.shippingNum+'">'+t+'</div>';

                    
                }    
			$('.product').html(content)		; 
          
            showPrintInvoice('invoiceFrame_'+data.order.shippingNum,data.invoice);
           $('#printTitle').html(data.order.receiver+data.order.shippingTime.substr(0,11)+' s'+data.order.shippingNum);
			//countTotal(1);
         $('.orderTableClass').css('width','100%');
        if(showtype=='boxin')$('.orderTableClass').css('font-size','12pt');
            else $('.orderTableClass').css('font-size','12pt');
        
	   }
       }})
	    return content;
}

function showPrintInvoice(id,invoiceList)
{
     var  invoiceOrder = 0;
    for(key in invoiceList)
        {
             if(invoiceOrder++==0)
             {
             
                 $('#'+id).html('');
             }
            getPrintInvoice(id,invoiceList[key].invoice,invoiceList[key].total);
            
            
        }
    
    
}
function getPrintInvoice(id,invoiceNumber,total)
{
   
  
    
    $.post('/accounting/show_invoice_detail',{InvoiceNumber:invoiceNumber,total:total},function(data){
       
        if(data.result==true)
            {
                
                if(data.invoiceInf!='')
                    {
                       
                        
                        if(data.invoiceInf['BuyerIdentifier']!='0000000000')
                         {
                            
                       $('#'+id).append('<iframe id="in_'+invoiceNumber+'" style="transform:rotate(-90deg);border:none;margin-left:-240px;   overflow: hidden;transform-origin:right top;;width:230px;height:230px"  src="http://mart.phantasia.tw/order/show_invoice/'+invoiceNumber+'/'+data.code+'"></iframe><div style="clear:both"></div>');
                             $('#in_'+invoiceNumber).height('18cm');
                             
                         }
                        else 
                        {        
                        
                              $('#'+id+'_c').append('<iframe id="in_'+invoiceNumber+'" style="transform:rotate(-90deg);border:none;margin-left:-240px;   overflow: hidden;transform-origin:right top;;width:230px;height:230px"  src="http://mart.phantasia.tw/order/show_invoice/'+invoiceNumber+'/'+data.code+'"></iframe><div style="clear:both"></div>');
                             $('#in_'+invoiceNumber).height('18cm');
                        
                        
                        }
                        
                        
                    }
                    else 
                    {
                      
                        $('#'+id).append('發票'+invoiceNumber+'已開立');
                    }
                 
             ; 
                
            }
            else 
            {
                
                $('#'+id).append('發票'+invoiceNumber+'已開立');
            }
        
        
        
    },'json')
    
    
    
}


function showTodayOrder()
{
	content='<a href="/order/today_print_out/4"><input type="button" class="big_button" value="訂單已完成"></a>';
	content+='<a href="/order/today_print_out/2"><input type="button" class="big_button" value="已送達物流"></a>';
		
	openPopUpBox(content,500,200,'closePopUpBox');
	
}
function todayOrder(type,date)
{
	
	$.post('/order/get_today_shipment',{type:type,date:date},function(data)
	{
		
		if(data.result==true)
		{
		
		
		content = orderTable(data,'noPricePrint','訂單已送達');
		$('.product').html(content)		;
		}
		else $('.product').html('sssasa')
		
	},'json')
		
	
	
}





function selectOrder(idList,showType)
{
    
    $.post('/order/get_select_shipment',{idList:idList},function(data)
	{
         
		var pageBreakKey = false;
		if(data.result==true)
		{
		var Today=new Date();
            content = '';
            /*
            if(showType=='boxin')
                {
                    content = '<h2>'+ Today.getFullYear()+ "/" + (Today.getMonth()+1) + "/" + Today.getDate()+
                        ' 今日揀貨明細</h2>'+orderTable(data,'pickupList','訂單已送達');
                    pageBreakKey = true;
                  
                }
            else  content = '';
            */
            $('.mainContent').html(content);
          
            for(key in data.orderList)
            {
               
                //$('.product').append('<iframe style="width:1200px" src="/order/print_out/boxin/'+data.orderList[key].id+'/0/1"></iframe>')
                    if(pageBreakKey)$('.mainContent').append('<div class="break" style="page-break-after:always;clear:both;">--以下空白--</div');  
               var  r = print_out(data.orderList[key].id,0,1,showType);
                
                if(showType!='boxin');
                else if(showType=='boxin')
                {
                   
                    r+= '<h2><table border="1" width="100%"><tr><td>經辦：</td><td>檢核：</td></tr></table></h2>';
                   
                }
                
                $('.mainContent').append(r);
              pageBreakKey = true;
            }
            
            
		}
		else $('.mainContent').html('今日沒有內容')

      //  $('.break').css('page-break-after','always');
		 $('.orderTableClass').css('width','  100%');
        $('.orderTableClass').css('font-size','12pt');
    
        
        
	},'json')
		
    
    

}


function padLeft(str,lenght){

   str= String(str);
    if(str.length >= lenght)

        return str;

    else

        return padLeft("0" +str,lenght);

}


function back_print_out(orderID)
{

	$.post('/order/get_order_back',{orderID:orderID},function(data)
	{
		if(data.result==true)
		{
			orderNumber = 0;
			var	 content ='<div id="orderAppendQuery"></div>';
			content +='<form id="orderBackForm">'+
						'<input type="hidden" name="orderID"  id="order_backID" value="'+data.order.id+'">'+
						'<div style="text-align:left">退單編號：b'+data.order.id+' 店家：'+data.order.name+' 狀態:'+data.order.status;
			 
			
			content+='</div>';
			
			content +=orderBackTable(data,'print',data.order.status);
			content +='<h2>退單備註：</h2><div style=" background-color:#CCC">'+data.order.comment+'</div>';
			$('.product').html(content)		;
		
			
			//countTotal(1);
		}
		
	},'json')	
	
		


	
	
}






function order_print(orderID)
{
	
	window.open('/order/print_out/order/'+orderID, "_blank");
	/*
		$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/order_update",
	   data: $("#orderForm").serialize(),
	   success: function(data){
		  
		   if(data.result ==true)  window.open('/order/print_out/'+data.orderID, "_blank");
	   }
	   
	   })
	*/
}




function order_back_print()
{
		$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/order_back_update",
	   data: $("#orderBackForm").serialize(),
	   success: function(data){
		  
		   if(data.result ==true)  window.open('/order/back_print_out/'+data.orderID, "_blank");
	   }
	   
	   })
	
}
function orderRowDeleteApply(orderID,id,addressID)
{
	
	if(confirm('你確定要申請取消訂貨？'))
	{
		orderRowDelete(orderID,id);
		/*直接刪除 免看cashtype
		$.post('/order/product_delete_apply',{orderID:orderID,id:id},function(data){
			
			if(data.result==true){
					shipmentView('shipment',addressID);
				
				
				}
			else 	orderRowDelete(orderID,id);
		},'json')
		*/	
	}	
	
}
var rownum = 0;
function productViewRow(data,setting,status)
{
	
	
	var Zh = new Array('撿貨','裝箱','點貨','數量','櫃號','訂單編號','中文','英文','品號','語言','定價','出貨折數','出貨價格','成本','數量','小計','備註','出貨狀況','刪除');
	var Eng = new Array('count','box','checkcount','OSBANum','cabinet','orderNum','ZHName','ENGName','productNum','language','price','purchaseCount','purchasePrice','eachCost','OSBANum_2','subtotal','orderComment','shippingStatus','rowDelete');
	

		var content = ''
		if(orderKey==false)
		{
             rownum = 0;    
			content = '<tr style="background-color:#FFEBEB" id="order_header">';
			for( var key in Zh)
			{	
				
					if(typeof setting[Eng[key]] == "undefined" ||setting[Eng[key]]==true)
					{
					  if(Zh[key]=='刪除')
					  {
						  alert(Eng[key]);
						  alert(setting[Eng[key]])
						  alert(setting['rowDelete']);
					  }
						content+='<td class="header_'+Eng[key]+'">'+Zh[key]+'</td>';
                        rownum++;
					}
			}
			content+=
			'</tr>';
			orderNumber = 0;
			orderKey = true;
		}

		if(orderNumber%2==1) color = '#FAFAFA';
		else color ='#FFF'
		orderNumber++;
   
        if(status=='boxin')
        if(data['orderComment'].indexOf('盒損')>=0 ||data['orderComment'].indexOf('工作')>=0  )
        {
            
            color = '#FF0088';
            
        }
    
		 content+='<tr class="orderRow" id="orderRow_'+data.rowID+'" style="background-color:'+color+';-webkit-print-color-adjust:exact;">';
        var checkBtn =  false;
		for(row in Zh)
		{
			
			if(typeof setting[Eng[row]] == "undefined" ||setting[Eng[row]]==true)
			{
				
				if(Eng[row]=='count')
                {
                    if(data.productID==8881935) var fun =  'membercheck(4,'+data.orderID+','+data.rowID+')';
                     else if(data.productID==8881935) var fun =  'membercheck(5,'+data.orderID+','+data.rowID+')';
                    else  var fun =  'handCheck('+data.rowID+')';
                    content+='<td class="countcolumn count_'+data['productID']+'" style="background-color:red"  id="count_'+data.rowID+'"><input type="button" value="檢核" onclick="'+fun+'" ></td>';
                    checkBtn = true;
                }
                else if(Eng[row]=='box') 
                {
            if(data.productID==8881437||data.productID==8882321||data.productID==8881695||data.productID==8884457) var num =data['OSBANum'];
                    else var num = '';
                    content+='<td class="boxcolumn box_'+data['productID']+'" style="color:red; font-weight:bold; font-size:14pt" id="box_'+data.rowID+'">'+num+'</td>';
                }
				else if(Eng[row]=='productNum')content+='<td style="font-size:10pt">'+fillZero(data[Eng[row]])+'</td>';
				else if(Eng[row]=='orderNum')content+='<td>o'+data[Eng[row]]+'</td>';
				else if(Eng[row]=='number')content+='<td>'+(parseInt(orderNumber)+1)+'</td>';
			
				else if(Eng[row]=='purchaseCount')
				{
										
					purchaseCount = Math.round(parseInt(data.sellPrice)*100/parseInt(data.price));
					content+='<td id="purchaseCount_'+data.rowID+'">'+purchaseCount+'%</td>';

				}
				else if(Eng[row]=='purchasePrice')
				{
					content+='<td class="purchasePrice" id="purchasePrice_'+data.rowID+'">'+data['sellPrice']+'</td>';
				}
				else if(Eng[row]=='subtotal')
				{
					content+='<td id="subtotal_'+data.rowID+'">'+data['sellPrice']*data['OSBANum']+'</td>';
				}
				else if(Eng[row]=='shippingStatus' )
				{
						if(data.status==1) content+='<td>已出貨</td>';
						else if(data.status==-1)content+='<td>已刪除</td>';
						else  content+='<td>貨品等待中</td>';
							
					
				}
				else if(Eng[row]=='orderComment' )
				{
					
                    var t = '';
                    if(status=='採購中')  var t='<input class="productComment" type="text" id="productComment_'+data.rowID+'" name="'+Eng[row]+'_'+data.rowID+'" value="'+data['orderComment']+'">';
                    else t = data['orderComment'];
					content+='<td style="font-size:8pt">'+t+'</td>';
                    if(data[Eng[row]].length>10) printLine++;
                    
				}
				else if (Eng[row]=='OSBANum')
				{
					content+='<td id="'+Eng[row]+'_'+data.rowID+'" style=" font-weight:bold; font-size:14pt">'+data[Eng[row]]+'</td>';
					
				}
                else if (Eng[row]=='OSBANum_2')
				{
					content+='<td id="'+Eng[row]+'_'+data.rowID+'" style=" font-weight:bold; font-size:14pt">'+data['OSBANum']+'</td>';
					
				}
				 else if (Eng[row]=='language')
				{
					content+='<td id="'+Eng[row]+'_'+data.rowID+'" >'+data['language'].substr(0,1)+'</td>';
					
				}
				
				else if (data[Eng[row]])
				{
                    
                    var t ='';
                    if(data[Eng[row]].length>16) t='style="font-size:8pt"';
                    if(data[Eng[row]].length>20) printLine++;
                   
					content+='<td id="'+Eng[row]+'_'+data.rowID+'" '+t+'>'+data[Eng[row]]+'</td>';
					
				}
				else content+='<td id="'+Eng[row]+'_'+data.rowID+'"></td>';
			}
		}
			
		content+='</tr>';
    
        	if(data.rule==1)
            {
                if(checkBtn)
             checkTD='<td class="countcolumn count_'+data['productID']+'_rule" style="background-color:red" id="count_'+data.rowID+'_rule"><input type="button" value="檢核" onclick="handCheck(\''+data.rowID+'_rule\')"></td>';
                else   checkTD = '';
                
                boxTD =  '<td class="boxcolumn box_'+data['productID']+'_rule" style="color:red; font-weight:bold; font-size:14pt" id="box_'+data.rowID+'_rule"></td>';
                content+='<tr style="color:red">'+checkTD+boxTD+'<td id="OSBANum_'+data.rowID+'_rule">'+data.OSBANum+'</td><td colspan="'+(rownum-2)+'">'+data.ZHName+'   精美手冊</td></tr>';
                printLine++;
            }
    
            if(data.patch!='')
            {
                if(checkBtn)
             checkTD='<td class="countcolumn count_'+data['productID']+'_patch" id="count_'+data.rowID+'_patch"><input type="button" value="檢核" onclick="handCheck(\''+data.rowID+'_patch\')"></td>';
                else   checkTD = '';
                
                boxTD =  '<td class="boxcolumn box_'+data['productID']+'_patch" style="color:red; font-weight:bold; font-size:14pt" id="box_'+data.rowID+'_patch"></td>';
                content+='<tr style="color:red">'+checkTD+boxTD+'<td id="OSBANum_'+data.rowID+'_patch">'+data.OSBANum+'</td><td colspan="'+(rownum-2)+'">'+data.ZHName+data.patch+'</td></tr>';
                printLine++;
            }
				
		return content;
	
	
}



function orderRow(data,type,status,setting)
{
	var Zh=new Array('項次','編號','中文','英文','語言','定價','出貨折數','出貨價格','數量','公司庫存','小計','備註','刪除');
	var Eng = new Array('number','productNum','ZHName','ENGName','language','price','purchaseCount','purchasePrice','sellNum','comNum','subtotal','orderComment','rowDelete');
	

		var content = ''
		if(orderKey==false)
		{
			content = '<tr style="background-color:#FFEBEB" id="order_header">';
			
				for( var key in Zh)
				{	
						if(typeof setting[Eng[key]] == "undefined" ||setting[Eng[key]]==true)content+='<td class="header_'+Eng[key]+'">'+Zh[key]+'</td>';
                   ;
				}

	
			content+=
			'</tr>';
			orderNumber = 0;
			orderKey = true;
		}
		parseInt(orderNumber++);

		 content+='<tr  class="orderRow" id="orderRow_'+orderNumber+'">';

		
		for(row in Zh)
		{
			
			
			if(Eng[row]=='count')content+='<td></td>';
			else if(Eng[row]=='productNum')content+='<td>'+fillZero(data[Eng[row]])+'</td>';
			else if(Eng[row]=='number')content+='<td>'+orderNumber+'</td>';
			else if(Eng[row]=='purchaseCount')
			{
				
				
				
				purchaseCount = (parseInt(data[Eng[row]]));

					content+='<td id="purchaseCount_'+orderNumber+'" onclick = "changePurchaseCount(\''+orderNumber+'\')">'+purchaseCount+'%</td>';


			}
			else if(Eng[row]=='purchasePrice')	
			{
				
				content+='<td id="price_row_'+orderNumber+'" class="priceRow" ><input type="text"  id="sellPrice_'+orderNumber+'" name="sellPrice_'+orderNumber+'" value="'+data['sellPrice']+'" onblur="countTotal(this.value)"></td>';
				
				
			}
			else if((type=='watch'||type=='order')&&Eng[row]=='sellNum');
			else if(type=='result'&&Eng[row]=='buyNum');
			else if(type=='shipment'&&Eng[row]=='buyNum');
			else if((type=='shipment')&&Eng[row]=='sellNum')
			{
				if(data['sellNum']==-1)data['sellNum'] =0;
				content+='<td>'+data[Eng[row]]+
						'<input type="hidden"  id="sellNum_'+orderNumber+'" value="'+data['sellNum']+'" />'+
						'</td>';
			}
			else if(Eng[row]=='rowDelete'){
					
					if(typeof(data.orderID)=="undefined")
					{
					content+='<td>'+
							'<input type="button"  value="刪除" onclick="$(\'#orderRow_'+orderNumber+'\').detach()" />'+
						'</td>';	
						
					}
					else
					{
					content+='<td>'+
							'<input  type="button"  value="刪除" onclick="orderRowDelete('+data.orderID+','+data.rowID+')" />'+
						'</td>';				
					}
			}
			
			else if(Eng[row]=='sellNum')
			{
						data['sellNum']=0;
						content+='<td>'+
									'<input type="hidden"  name="productID_'+orderNumber+'" class="order_product_'+data['productID']+'" id="order_product_'+orderNumber+'"" value="'+data['productID']+'"/>'+
									'<input type="text"  name="sellNum_'+orderNumber+'" value="'+data['sellNum']+'" class="sellRow" id="sellNum_'+orderNumber+'" onblur="countTotal(this.value)"/>';
					
					if(data.concessions!=null) var concessions = data.concessions.split(',');
					else var concessions ={};
				
				
					if(data.concessionsNum!=null) var concessionsNum = data.concessionsNum.split(',');
					else var concessionsNum ={};				
					for(each in concessions)
					{
						content+='<div  style="display:none" id="concessions_'+data['productID']+'_'+each+'">'+concessions[each]+'</div>';
						content+='<div  style="display:none" id="concessionsNum_'+data['productID']+'_'+each+'">'+concessionsNum[each]+'</div>';
						
					}	
		
						
						content+='</td>';
					
			}
			else if(Eng[row]=='subtotal')
			{
				if(data['sellNum']!=-1) num = data['sellNum'];
				else num = data['buyNum'];
				content+='<td id="subtotal_'+orderNumber+'">'+data['sellPrice']*num+
						 '</td>';
			}
			else if(type=='staff'&&Eng[row]=='orderComment'&&status!="已到貨"&&status!="已送達物流"){
				
               
                   
                
                
                
				content+='<td><input type="text" id="comment_'+orderNumber+'"  name="comment_'+orderNumber+'" value="" /></td>';
				

			}
			else if( Eng[row]=='shippingStatus' )
			{
					if(data.status==1) content+='<td>已出貨</td>';
					else if(data.status==-1) content+='<td>已刪除</td>';
					else  content+='<td>貨品等待中</td>';
						
				
			}
            
            
            
			else if(Eng[row]=='comNum')
			{
					if(type!='watch'&&type!='shipment')content+='<td id="'+Eng[row]+'_'+orderNumber+'">'+data[Eng[row]]+'</td>';
				
				
			}
			else if (data[Eng[row]])
			{
                
                
                
				content+='<td id="'+Eng[row]+'_'+orderNumber+'">'+data[Eng[row]]+'</td>';
				
			}
			
			else 
			{
				
				content+='<td></td>';
			}
		}
			
		content+='</tr>';
		return content
	
	
}


var purchaceCountKey = true;
function changePurchaseCount(id)
{
	if(purchaceCountKey==false)return;
	purchaceCountKey = false;
	var purchaseCount =($('#purchaseCount_'+id).html().substr(0,parseInt($('#purchaseCount_'+id).html().length)-1))
	$('#purchaseCount_'+id).html('<input type="text" id="changePurchaseCount" value="'+purchaseCount+'" style=" width:20px" onblur="recoverePurchaseCount('+id+')">%');
	$('#changePurchaseCount').focus().select();
}

function recoverePurchaseCount(id)
{
	
	$('#purchaseCount_'+id).html($('#changePurchaseCount').val()+'%');
	$('#sellPrice_'+id).val( 
		Math.round(parseInt($('#price_'+id).html())*parseInt($('#purchaseCount_'+id).html().substr(0,parseInt($('#purchaseCount_'+id).html().length)-1))/100));
	countTotal(99);	
	purchaceCountKey = true;
}



function countTotal(NUM)
{
	
	if(fucCheckNUM(NUM)==0)
	{
		alert('請填入數字');
		return;
	}
	var subtotal = 0;
	var total = 0;
	$('.priceRow').each(function(i){
		id = this.id.substr(10);
		if($('#sellNum_'+id).length!=0) num = parseInt($('#sellNum_'+id).val());
		else num = parseInt($('#buyNum_'+id).val())
	

		if(isNaN(num)==true)
		{
			num = (parseInt($('#buyNum_'+id).html()))
		}
		
		if($('#create_discount').length!=0 &&$('#create_discount').is(":checked"))		
		{
			var productID = $('#order_product_'+id).val()
			for(i=0;i<5;i++)
			{
		
				if($('#concessions_'+productID+'_'+i).length>0)
				{
					if(num>=parseInt($('#concessionsNum_'+productID+'_'+i).html()))
					{
						
						$('#sellPrice_'+id).val( Math.round(parseInt($('#price_'+id).html())*parseInt($('#concessions_'+productID+'_'+i).html())/100))
						
					}
					
				}
				
			}
		}
	
		subtotal = parseInt($('#sellPrice_'+id).val())*num;
	
		if(parseInt($('#price_'+id).html())==0)discount = 0;
		else discount = parseInt(Math.round($('#sellPrice_'+id).val()*100/parseInt($('#price_'+id).html())))
		
		$('#purchaseCount_'+id).html(discount+'%');
		$('#subtotal_'+id).html(subtotal);
		total+=subtotal;
	}
	)
		$('#order_total').html('總價：'+total);

	
}



function orderAppendTable(data,num)
{
	 num = num || 1;
	data.buyNum = 0;
	data.sellNum = 0;	
	data.sellPrice = Math.round(data.price*data.purchaseCount/100);	
	if($('.order_product_'+data.productID).length>0 && !$('#create_discount').is(":checked"))alert('商品已存在');
	else
	{
		$.post('/order/get_product_discount',{productID:data.productID,shopID:$('#shopID').val()},function(result)
		{
			if(result.result==true)
			{
				
			
				 result.product.comNum = data.comNum;
				$('#orderTable').append(orderRow(result.product,'staff','',{}));	
				onumber = orderNumber;
				countTotal(-1);
				height =parseInt($('#orderTable').css('height').substr(0,$('#orderTable').css('height').length-2))
				popUpBoxHeight(height+800);
				
			  $('#sellNum_'+onumber).val(num);
              $('#comment_'+onumber).val(data.orderComment);
			}
			
			
		},'json')
		
		
		
	}
		
}
function createShopSearch(option,searchID,target)
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
	
}
function openSuggest()
{
	var shopID = $('#create_shopID').val();
	content='<h2>開盒清單建立中，需要較長時間，請稍候...</h2>';
	openPopUpBox(content,500,200,'closePopUpBox');
	
	$.post('/order/open_suggest',{shopID:shopID},function(data)
	{
		if(data.result==true)showOrder(data.orderID,'已送達物流','watch')
		
	},'json')
	
}
function createOrder()
{
	$('#product_list').html('');
	$.post('/system/get_shop',{},function(data){
				if(data.result==true)
				{
					content='<h1>請選擇出貨店家</h1>'+
							'<input type="text" id="create_shop_search"  placeholder="搜尋出貨地點" onkeyup="shopSearch(\'create_shopID_op\',\'create_shop_search\',\'create_shopID\',false)">'+
							'<select id="create_shopID" name="shopID"></select>'+
							'<h1>請選擇出貨類型</h1>'+
							'<select id="create_type" name="create_type">'+
								'<option value="0">買斷</option>'+
								'<option value="1">寄賣</option>'+
							'</select>'
							'<input style="float:left" type="button" id="" value="匯入建議開盒清單" class="big_button" onclick="openSuggest()">'
							;
					
					openPopUpBox(content,500,200,'createSend',true);
		
					for( var key in data.shopData)
					{
						
							$('#create_shopID').append('<option  class ="create_shopID_op" value="'+data.shopData[key].shopID+'">'+data.shopData[key].name+'</option>');
						
					}
				  //$( "#create_shopID" ).combobox();
					
				}
			
		
		},'json')
		
	
	
}

function createSend()
{
	$.post('/order/order_create',{shopID:$('#create_shopID').val(),create_type:$('#create_type').val()},function(data){
				if(data.result==true)
				{
					showOrder(data.orderID,'訂單已送達','staff');
			   
				 				
				}
			
		
		},'json')
	
	
	
}

function consignmentSend()
{
		
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/consignment_send",
	   data: $("#consignmentForm").serialize(),
	   success: function(data){
		   if(data.result==true)
		   {
			   
			   
				closePopUpBox();
			}	  
	   }
	   })
	
}




function consignmentShop()
{
	consignmentKey = false;
	formMaster=false;
	if( $('#consignment_shopID').length>0)
	{
		shopID = 	$('#consignment_shopID').val()
		formMaster=true;
	}
	else shopID = $('#shopID').val();
	
	
			var today = new Date()
		$('#mday').val();
		$('#mon').val(today.getMonth()); //2019 0605 暫時

	if($('#consignment_year').length<=0)  var year = today.getFullYear();
	else var year =$('#consignment_year').val();
	if($('#consignment_mon').length<=0)  var mon = today.getMonth()+1;
	else var mon =$('#consignment_mon').val();
	if($('#consignment_query').length<=0)  var query = '';
	else var query =$('#consignment_query').val();	
	
	$.post('/order/get_consignment',{shopID:shopID,year:year,mon:mon,query:query},function(data){
		if(data.result==true)
		{
			consignNumber = 0;
			var content = '<div id="consignmentQuery"></div>';
				content+='<select name="consignment_year" id="consignment_year" onchange="consignmentShop()">';
				for(i=2011;i<= today.getFullYear();i++)
				{
							content+='<option value="'+i+'">'+i+'</option>';
				}
				content+='</select>年';
				
				content+='<select name="consignment_mon" id="consignment_mon" onchange="consignmentShop()">';
				for(i=1;i<= 12;i++)
				{
							content+='<option value="'+i+'">'+i+'</option>';
				}
				content+='</select>月';
				content+='<input type="hidden" name="consignmentShopID" id="consignment_shopID" value="'+data.shopID+'">';

				content+='<input name="consignment_query" id="consignment_query" value="'+query+'" placeholder="請輸入遊戲名稱" onkeyUp="if(enter())consignmentShop();">';
				content+='<input type="button" value="搜尋" onclick="consignmentShop()">';
								
				content+='<form id ="consignmentForm" >'+
						  	'<input type="hidden" name="consignmentShopID" id="consignment_shopID" value="'+data.shopID+'">';
		
				
				content+='<input type="hidden" name="consignmentSatus" id="consignmentSatus" value="'+data['status']+'">'
			
			
			
				content+='<table id="consignmentTable" border="1" style="width:1000px"></table>'+
				         '<input type="hidden" name="deleteConsignmentProduct" id="deleteConsignmentProduct" value=0>'+
					      '</form>'	;
				if(formMaster)content+='<input type="button" value="寄賣品退回" onclick="consignmentBack('+data.shopID+','+year+','+mon+')">';
						
			openPopUpBox(content,1000,200,'consignmentSend');
			/*
			if(formMaster) $('#consignmentSatus').val(data.status);
			if(formMaster||data.status==1)queryProduct('consignment','select');
			*/
			consignNumber = 0;
			for( var key in data.consignment)
			{
		
				consignmentTable(data.consignment[key],data['status']);
			}
			$('#consignment_year').val(year);
			$('#consignment_mon').val(mon);
			
		}
		
		
	},'json')
	
}

function consignmentBack(shopID,year,mon)
{
	$.post('/order/consignment_back',{shopID:shopID,year:year,mon:mon},function(data)
	{
		
		if(data.result==true)
		{
			
				showOrder(data.orderID,'訂單已送達','watch');
		}
	},'json')	
	
}
function consignmentTable(data,consignmentSatus)
{
	
	$('#consignmentTable').append(consignmentRow(data,'staff',consignmentSatus));	
	height =parseInt($('#consignmentTable').css('height').substr(0,$('#consignmentTable').css('height').length-2))
	popUpBoxHeight(height+500);
		
}



function consignmentRow(data,type,status)
{
	if($('#co_row_'+data['productID']).length>0) 
	{
		$('#consignmentSelect').html('<h1>商品重複</h1>');
		return;
	}
	
	var Zh=new Array('項次','編號','中文','英文','語言','定價','出貨折數','出貨價格','寄賣數量','本月銷售數量','現在庫存','寄賣庫存','買斷庫存','刪除');
	var Eng = new Array('number','productNum','ZHName','ENGName','language','price','purchaseCount','purchasePrice','consignmentNum','sellNum','nowNum','remainNum','purchaseNowNum','delete');

		var content = ''
		if(consignmentKey==false)
		{
			content = '<tr style="background-color:#FFEBEB" id="consign_header">';
			for( var key in Zh)
			{	
		
					if((type=='order'&&Eng[key]!='sellNum')||(type=='result'&&Eng[key]!='buyNum')||(type=='staff')||(type=='watch'))	content+='<td>'+Zh[key]+'</td>';
			}
			content+='</tr>';
			consignmentKey = true;
			
		 }


		 content+='<tr id="co_row_'+data['productID']+'">';

		 
		for(row in Zh)
		{
			
			if(Eng[row]=='count')content+='<td></td>';
			else if(Eng[row]=='productNum')content+='<td>'+fillZero(data[Eng[row]])+'</td>';
			else if(Eng[row]=='number')
			{
				if(parseInt(consignNumber)%20==19) consignmentKey = false;
				content+='<td>'+(parseInt(consignNumber++)+1)+'<input type="hidden"  name="productID'+data['productID']+'" value="'+data['productID']+'" /></td>';
			}
			else if(Eng[row]=='purchaseCount')content+='<td>'+data[Eng[row]]+'%</td>';
			else if(Eng[row]=='purchasePrice')	
			{
				content+='<td id="price_'+data.id+'" class="priceRow" >'+
				Math.round(data.purchaseCount*data.price/100)+
				'</td>';
			}
			else if (typeof data[Eng[row]] != "undefined" )
			{
				content+='<td>'+data[Eng[row]]+'</td>';
				
			}
			else if (Eng[row]=='delete')
			{
                
                var t='<input type="button" value="更正寄賣數量" onclick="updateConsignment('+data['productID']+')">';
             
              if(accountLevel != 100 ||$('#myshopID').val()!=0)  t='';
				if(data['consignmentNum']==0)	content+='<td>'+t+'<input type="button" value="刪除" onclick="deleteConsignment('+data['productID']+')"></td>';
				else 	content+='<td>'+t+'</td>';

			}
			
			else if(Eng[row]=='purchaseNowNum')
			{
				 purchaseNowNum = data['nowNum'] - (data['remainNum'] );//現在庫存-寄賣庫存
			
					
				
				content+='<td>'+purchaseNowNum+'</td>';
				
			}

			else content+='<td></td>';
		}
			
		content+='</tr>';
		return content
	
	
}
function updateConsignment(productID)
{
    
    v = prompt("請輸入修改數字","0");
    if (v != null)
    $.post('/order/consignment_num_update',{productID:productID,shopID:$('#consignment_shopID').val(),num:v},function(data)
	{
		
        if(data.result==true)
		{
			 consignmentShop();
		}
	
	},'json')
	
    
}


function deleteConsignment(productID)
{	
    var year =$('#consignment_year').val();
   var mon =$('#consignment_mon').val();

	if( $('#consignment_shopID').length>0)
	{
		shopID = 	$('#consignment_shopID').val()
		formMaster=true;
	}
	else shopID = $('#shopID').val();	
	$.post('/order/consignment_delete',{productID:productID,year:year,mon:mon,shopID:shopID},function(data)
	{
		if(data.result==true)
		{
			$('#co_row_'+productID).detach();	
			$('#deleteConsignmentProduct').val($('#deleteConsignmentProduct').val()+'_'+productID);
		}
		else
		{
			alert('寄賣數量必須為0')	
			
		}
	},'json')
	
}


function showConsignment(type)
{
	
	if(type=="staff")
	{
		$.post('/system/get_shop',{},function(data){
				if(data.result==true)
				{
					content='<h1>請選擇店家</h1>'+
							'<select id="consignment_shopID" name="shopID"></select>';
					
					openPopUpBox(content,500,200,'consignmentShop');
					$('#consignment_shopID').append('<option value="-1">觀看全部寄賣狀況</option>');
					$('#consignment_shopID').append('<option value="0">幻遊天下</option>');
					for( var key in data.shopData)
					{
						
							$('#consignment_shopID').append('<option value="'+data.shopData[key].shopID+'">'+data.shopData[key].name+'</option>');
						
					}
				}
			
		
		},'json')
	}
	else consignmentShop();
	
	
	
}
function changeWeek()
{
	$('#monthCheck_week').html('');
		var x = new Date($('#monthCheck_year').val(),$('#monthCheck_month').val()-1,1);
					n = x.getDay();
					var toDay = 0;
					var month  =$('#monthCheck_month').val();
					last =false;
					for(t=0;last==false;t++)
					{
						fromDay = toDay+1
						toDay = 1+(6-n)+7*t;
						if(month==1||month==3||month==5||month==7||month==8||month==10||month==12)
						{
							if (toDay>=31)
							{
								 toDay = 31;
								last =true;
							}
						}
						else if(month==2)
						{
							if($('#monthCheck_year').val()%4==0)
							{
									if (toDay>=29) toDay=29;
									last =true;
							}
							else if (toDay>=28) 
							{
								toDay=28;
								last =true;
							}
						}
						else 
						if (toDay>=30)
						{
							toDay=30;
								last =true;
						}
						
						$('#monthCheck_week').append('<option value="'+fromDay+'-'+toDay+'">'+fromDay+'~'+toDay+'</option>');
					}
					
						
	
}
function weekCheck()
{
	$.post('/system/get_shop',{},function(data){
				if(data.result==true)
				{
					content='<h1>請選擇店家</h1>'+
							'<select id="monthCheck_shopID" name="shopID" onchange="getWeekCheck()">></select><br/>'+
							'<select id="monthCheck_year" name="year" onchange="changeWeek();getWeekCheck()"></select>年'+
							'<select id="monthCheck_month" name="month" onchange="changeWeek();getWeekCheck()"></select>月'+
							'<select id="monthCheck_week" name="week" onchange="getWeekCheck()"></select>日'+
							'<div id="check_container"></div>'
							;
					
					openPopUpBox(content,1200,200,'closePopUpBox');
					
	
					for( var key in data.shopData)
					{
						
							$('#monthCheck_shopID').append('<option value="'+data.shopData[key].shopID+'">'+data.shopData[key].name+'</option>');
						
					}
					var d=new Date();
					for (i = d.getFullYear();i>2010;i--)$('#monthCheck_year').append('<option value="'+i+'">'+i+'</option>');
					for (i =1 ;i<=12;i++)$('#monthCheck_month').append('<option value="'+i+'">'+i+'</option>');
					
					if(d.getMonth()==0)
					{
						$('#monthCheck_month').val(12);	
						$('#monthCheck_year').val(d.getFullYear()-1);	
						
						
					}
					else $('#monthCheck_month').val(d.getMonth());
					
				 	//$('#monthCheck_month').val(data.week[0].toDay);
					
					changeWeek();
				
					
					
					getWeekCheck();
					  	
				}
			
		
		},'json')
	
}

function inshopGift()
{
	
	window.open('http://shipment.phantasia.com.tw/product/inshop_detect/'+$('#monthCheck_shopID').val());
	
}



function monthCheck()
{
	
		$.post('/system/get_shop',{},function(data){
				if(data.result==true)
				{
					content='<h1>請選擇店家</h1>'+
							'<select id="monthCheck_shopID" name="shopID" onchange="getMonthCheck()">></select><input type="button"  onclick="inshopGift();" value="觀看開盒獎勵領取"><br/>'+
							'<select id="monthCheck_year" name="year" onchange="getMonthCheck()"></select>年'+
							'<select id="monthCheck_month" name="month" onchange="getMonthCheck()"></select>月'+
							'<label><input type="button" id="checkMonth" onclick="shopMonthSend();" value="寄出本月月結帳單"></label>'+
							'<input type="text" id="monthFromDate"> ~ <input type="text" id="monthToDate">'+
							'<div id="check_container"></div>'
							;
					
					openPopUpBox(content,1200,200,'closePopUpBox');
					
	
					for( var key in data.shopData)
					{
						
							$('#monthCheck_shopID').append('<option value="'+data.shopData[key].shopID+'">'+data.shopData[key].name+'</option>');
						
					}
					var d=new Date();
					for (i = d.getFullYear();i>2010;i--)$('#monthCheck_year').append('<option value="'+i+'">'+i+'</option>');
					for (i =1 ;i<=12;i++)$('#monthCheck_month').append('<option value="'+i+'">'+i+'</option>');
					if(d.getMonth()==0)
					{
						$('#monthCheck_month').val(12);	
						$('#monthCheck_year').val(d.getFullYear()-1);	
						
						
					}
					else $('#monthCheck_month').val(d.getMonth());
					$('#monthFromDate').val($('#monthCheck_year').val()+'-'+$('#monthCheck_month').val()+'-'+'1');
					
					nextMonth = parseInt($('#monthCheck_month').val())+1;
					$('#monthToDate').val($('#monthCheck_year').val()+'-'+nextMonth+'-'+1)
					
					getMonthCheck();
						var monthFromDate = $( "#monthFromDate").datepicker({
							dateFormat: 'yy-mm-dd' ,
							yearRange: '1930',
							monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
							changeMonth: true,
							numberOfMonths: 1,
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates.not( this ).datepicker( "option", option, date );
							},
								onClose:function(data)
								{
										getMonthCheck();
								}
							
						});		  	
			var monthToDate = $( "#monthToDate").datepicker({
							dateFormat: 'yy-mm-dd' ,
							yearRange: '1930',
							monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
							changeMonth: true,
							numberOfMonths: 1,
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates.not( this ).datepicker( "option", option, date );
							},
								onClose:function(data)
								{
										getMonthCheck();
								}
						});		  	
				}
			
		
		},'json')
	
}

function consignmentCheck()
{
	
		$.post('/system/get_shop',{},function(data){
				if(data.result==true)
				{
					content='<h1>請選擇店家</h1>'+
							'<select id="monthCheck_shopID" name="shopID" onchange="getConsignmentRecord()">></select>'+
						
							'<input type="text" id="monthFromDate"> ~ <input type="text" id="monthToDate">'+
                            '<input type="button" class="big_button" value="送出" onclick="getConsignmentRecord();">'+
							'<div id="check_container"></div>'
							;
					
					openPopUpBox(content,1200,200,'closePopUpBox');
					
	
					for( var key in data.shopData)
					{
						
							$('#monthCheck_shopID').append('<option value="'+data.shopData[key].shopID+'">'+data.shopData[key].name+'</option>');
						
					}
					var d=new Date();
					for (i = d.getFullYear();i>2010;i--)$('#monthCheck_year').append('<option value="'+i+'">'+i+'</option>');
					for (i =1 ;i<=12;i++)$('#monthCheck_month').append('<option value="'+i+'">'+i+'</option>');
                    
                        year = d.getFullYear();
                      month =d.getMonth();
					if(month==0)
					{
						month = 12;
                        year --;
						$('#monthCheck_year').val(d.getFullYear()-1);	
						
						
					}
					
					$('#monthFromDate').val(year+'-'+month+'-'+'1');
					
					nextMonth = parseInt(month)+1;
					$('#monthToDate').val(year+'-'+nextMonth+'-'+1)
					
					
						var monthFromDate = $( "#monthFromDate").datepicker({
							dateFormat: 'yy-mm-dd' ,
							yearRange: '1930',
							monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
							changeMonth: true,
							numberOfMonths: 1,
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates.not( this ).datepicker( "option", option, date );
							},
								onClose:function(data)
								{
										
								}
							
						});		  	
			var monthToDate = $( "#monthToDate").datepicker({
							dateFormat: 'yy-mm-dd' ,
							yearRange: '1930',
							monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
							changeMonth: true,
							numberOfMonths: 1,
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates.not( this ).datepicker( "option", option, date );
							},
								onClose:function(data)
								{
										
								}
						});		  	
				}
			
		
		},'json')
	
}
var shopList = '';
function otherMoney()
{
	
		$.post('/system/get_shop',{},function(data){
				if(data.result==true)
				{
					content='<h1>請選擇店家</h1>'+
							'<select id="monthCheck_shopID" name="shopID" onchange="getOtherMoney()">></select><br/>'+
							'<select id="monthCheck_year" name="year" onchange="getOtherMoney()"></select>年'+
							'<select id="monthCheck_month" name="month" onchange="getOtherMoney()"></select>月'+
							'<div id="check_container"></div>'
							;
					
					openPopUpBox(content,1200,200,'closePopUpBox');
					
					$('#monthCheck_shopID').append('<option value="0">全部</option>');
					 shopList = '';
					for( var key in data.shopData)
					{
						var option = '<option value="'+data.shopData[key].shopID+'">'+data.shopData[key].name+'</option>';
						 shopList +=option;
							$('#monthCheck_shopID').append(option);
						
					}
					var d=new Date();
					for (i = d.getFullYear();i>2010;i--)$('#monthCheck_year').append('<option value="'+i+'">'+i+'</option>');
					for (i =1 ;i<=12;i++)$('#monthCheck_month').append('<option value="'+i+'">'+i+'</option>');
					if(d.getMonth()==0)
					{
						$('#monthCheck_month').val(12);	
						$('#monthCheck_year').val(d.getFullYear()-1);	
						
						
					}
					else $('#monthCheck_month').val(d.getMonth());
		
					
					getOtherMoney()
				}
		
		},'json')
	
}


function haveMoney()
{
	
		$.post('/system/get_shop',{},function(data){
				if(data.result==true)
				{
					content='<h1>請選擇店家</h1>'+
							'<select id="monthCheck_shopID" name="shopID" onchange="getHaveMoney()">></select><br/>'+
							'<select id="monthCheck_year" name="year" onchange="getHaveMoney()"></select>年'+
							'<select id="monthCheck_month" name="month" onchange="getHaveMoney()"></select>月'+
							'<div id="check_container"></div>'
							;
					
					openPopUpBox(content,600,200,'closePopUpBox');
					
					$('#monthCheck_shopID').append('<option value="0">全部</option>');
					 shopList = '';
					for( var key in data.shopData)
					{
						var option = '<option value="'+data.shopData[key].shopID+'">'+data.shopData[key].name+'</option>';
						 shopList +=option;
							$('#monthCheck_shopID').append(option);
						
					}
					
                    var d = new Date();
					for (i = d.getFullYear()+1;i>2010;i--)$('#monthCheck_year').append('<option value="'+i+'">'+i+'</option>');
					for (i =1 ;i<=12;i++)$('#monthCheck_month').append('<option value="'+i+'">'+i+'</option>');
					if(d.getMonth()==0)
					{
						$('#monthCheck_month').val(12);	
						$('#monthCheck_year').val(d.getFullYear()-1);	
						
						
					}
					else
                    {
                        $('#monthCheck_year').val(d.getFullYear());
                        $('#monthCheck_month').val(d.getMonth()+1);
                    }
                    
		
						
					getHaveMoney()
				}
		
		},'json')
	
}






function updateOtherMoney(id)
{
	$('#other_msg').html('儲存中.....');

	$.post('/order/update_other_money',{id:id,money:$('#money_'+id).val(),reason:$('#reason_'+id).val()},function(data)
	{
		
		if(data.result==true) $('#other_msg').html('儲存完成');
		else $('#other_msg').html('儲存失敗，請洽管理員');
		
	},'json')	
	
	
}




function deleteOtherMoney(id)
{
	if(confirm('你確定要刪除嗎~?'))
	{
	$('#other_msg').html('刪除中.....');

	$.post('/order/delete_other_money',{id:id},function(data)
	{
		
		if(data.result==true) getOtherMoney();
	
		
	},'json')	
	}
	
	
}




function newOtherMoney()
{
	$('#other_msg').html('儲存中.....');

	$.post('/order/new_other_money',{shopID:$('#newShop').val(),money:$('#newMoney').val(),reason:$('#newReason').val(),year:$('#monthCheck_year').val(),month:$('#monthCheck_month').val()},function(data)
	{
		
		if(data.result==true)	getOtherMoney();
	
		
	},'json')		
	

	
	
}
function newHaveMoney()
{
	$('#other_msg').html('儲存中.....');

	$.post('/order/new_have_money',{shopID:$('#newShop').val(),money:$('#newMoney').val(),date:$('#newDate').val()},function(data)
	{
		
		if(data.result==true)	getHaveMoney();
	
		
	},'json')		
	

	
	
}

var haveID = 0;
function haveSelect(id)
{
	haveID = id;
}


function updateHaveMoney(id)
{
	$('#other_msg').html('儲存中.....');
	if(id==0) id = haveID;
	$.post('/order/update_have_money',{id:id,money:$('#money_'+id).val(),date:$('#date_'+id).val()},function(data)
	{
		 haveMoneySum();
		if(data.result==true) $('#other_msg').html('儲存完成');
		else $('#other_msg').html('儲存失敗，請洽管理員');
		
	},'json')	
	
	
}




function deleteHaveMoney(id)
{
	if(confirm('你確定要刪除嗎~?'))
	{
	$('#other_msg').html('刪除中.....');

	$.post('/order/delete_have_money',{id:id},function(data)
	{
		
		if(data.result==true) getHaveMoney();
	
		
	},'json')	
	}
	
	
}



function getOtherMoney()
{
		$('#check_container').html('<img src="/images/ajax-loader.gif"/>');
		
		$.post('/order/get_other_money',{shopID:$('#monthCheck_shopID').val(),year:$('#monthCheck_year').val(),month:$('#monthCheck_month').val()},function(data){
			
				if(data.result==true)
				{
					$('#check_container').html('<div id="other_msg" style="color:red"></div><table id="otherMoneyTable" border="1"></table>'+
                   
                    '<input type="button" onclick="$(\'#new_money\').slideDown()"  class="big_button"  value="新增'+$('#monthCheck_year').val()+'-'+$('#monthCheck_month').val()+'其他款項">');
					$('#check_container').append(
						'<div id="new_money" style=" display:none;background-color:#FEE">'+
						'請選擇店家<select id="newShop">'+shopList+'</select>'+
						'原因<textarea  style=" width:300px; height:50px" id="newReason"></textarea>'+
						'金額(可輸入負數)<input type="text" id="newMoney" value="">'+
						'<input class="big_button" onclick="newOtherMoney()" type="button" value="送出">'+
						'</div>'
					);
					
					
					
					
					
					$('#otherMoneyTable').append(
						'<tr>'+
							'<td>款項年份</td>'+
							'<td>款項月份</td>'+
							'<td>店家名稱</td>'+
							'<td>加減原因</td>'+
							'<td>加減金額</td>'+	
							'<td>刪除</td>'+			
						'</tr>'
					)
					for(key in data.otherMoney)
					{
						$('#otherMoneyTable').append(
						'<tr>'+
							'<td>'+data.otherMoney[key].year+'</td>'+
							'<td>'+data.otherMoney[key].month+'</td>'+
							'<td>'+data.otherMoney[key].name+'</td>'+
							'<td><textarea style=" width:300px; height:50px" onchange="updateOtherMoney('+data.otherMoney[key].id+')" id="reason_'+data.otherMoney[key].id+'" >'+data.otherMoney[key].reason+'</textarea></td>'+
							'<td><input type="text" onchange="updateOtherMoney('+data.otherMoney[key].id+')" id="money_'+data.otherMoney[key].id+'" value="'+data.otherMoney[key].money+'"></td>'+	
							'<td><input type="button" onclick="deleteOtherMoney('+data.otherMoney[key].id+')" value="刪除"></td>'+	
						'</tr>'
					)
						
						
					}
				
				
				height =parseInt($('#check_container').css('height').substr(0,$('#check_container').css('height').length-2));
				popUpBoxHeight(height+200);
				}
			
				
				
		},'json');
	
	
	
	
	
}

function haveMoneySum()
{
    var total = 0 ;;
    $('.havMoneyValue').each(function(){
        
        total+=parseInt(this.value);
        
    })
 
    $('#haveMoneyTotal').html('總額：'+total);
    
}
function getHaveMoney()
{
		$('#check_container').html('<img src="/images/ajax-loader.gif"/>');
		
		$.post('/order/get_have_money',{shopID:$('#monthCheck_shopID').val(),year:$('#monthCheck_year').val(),month:$('#monthCheck_month').val()},function(data){
			
			
				if(data.result==true)
				{
					$('#check_container').html('<div id="other_msg" style="color:red"></div><table id="otherMoneyTable" border="1"></table>' +
                '<h2 id="haveMoneyTotal"></h2>'+
                        '<input type="button" onclick="$(\'#new_money\').slideDown()"  class="big_button"  value="新增'+$('#monthCheck_year').val()+'-'+$('#monthCheck_month').val()+'已收款項">');
					$('#check_container').append(
						'<div id="new_money" style=" display:none;background-color:#FEE">'+
						'請選擇店家<select id="newShop">'+shopList+'</select><br/>'+
						'日期<input type="text" id="newDate" value=""><br/>'+
						'金額<input type="text"  id="newMoney" value=""><br/>'+
						'<input class="big_button" onclick="newHaveMoney()" type="button" value="送出">'+
						'</div>'
					);
						var newDate = $( '#newDate').datepicker({
							dateFormat: 'yy-mm-dd' ,
							yearRange: '1930',
							monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
							changeMonth: true,
							numberOfMonths: 1,
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates.not( this ).datepicker( "option", option, date );
							},
								onClose:function(data)
								{
										
								}
						});		  	
						
						
					
					
					
					
					$('#otherMoneyTable').append(
						'<tr>'+
							'<td>款項日期</td>'+
							'<td>店家名稱</td>'+
							'<td>金額</td>'+	
							'<td>刪除</td>'+			
						'</tr>'
					)
					for(key in data.otherMoney)
					{
						$('#otherMoneyTable').append(
						'<tr>'+
							'<td> <input type="text" onfocus="haveSelect('+data.otherMoney[key].id+')"   id="date_'+data.otherMoney[key].id+'" value="'+data.otherMoney[key].date+'" ></td>'+
							'<td>'+data.otherMoney[key].name+'</td>'+
							'<td><input type="text" class="havMoneyValue" onchange="updateHaveMoney('+data.otherMoney[key].id+')" id="money_'+data.otherMoney[key].id+'" value="'+data.otherMoney[key].amount+'"></td>'+	
							'<td><input type="button" onclick="deleteHaveMoney('+data.otherMoney[key].id+')" value="刪除"></td>'+	
						'</tr>'
							)
							var id = data.otherMoney[key].id;
						
							var monthToDate = $( '#date_'+data.otherMoney[key].id).datepicker({
							dateFormat: 'yy-mm-dd' ,
							yearRange: '1930',
							monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
							changeMonth: true,
							numberOfMonths: 1,
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates.not( this ).datepicker( "option", option, date );
							},
								onClose:function(data)
								{
										updateHaveMoney(0);
								}
						});		  	
						
						
						 haveMoneySum();
						
					}
				
				
				height =parseInt($('#check_container').css('height').substr(0,$('#check_container').css('height').length-2));
				popUpBoxHeight(height+200);
				}
			
				
				
		},'json');
	
	
	
	
	
}





function branchMonthCheck(shopID)
{
	

					content='<h1>請選擇月份</h1>'+
							'<input type="hidden" id="monthCheck_shopID" name="shopID" value="'+shopID+'" onchange="getMonthCheck()">'+
							'<select id="monthCheck_year" name="year" onchange="getMonthCheck()"></select>年'+
							'<select id="monthCheck_month" name="month" onchange="getMonthCheck()"></select>月'+
							'<div id="check_container"></div>'
							;
					
					openPopUpBox(content,1200,200,'closePopUpBox');
					
					var d=new Date();
					for (i = d.getFullYear();i>2010;i--)$('#monthCheck_year').append('<option value="'+i+'">'+i+'</option>');
					for (i =1 ;i<=12;i++)$('#monthCheck_month').append('<option value="'+i+'">'+i+'</option>');
					if(d.getMonth()==0)
					{
						$('#monthCheck_month').val(12);	
						$('#monthCheck_year').val(d.getFullYear()-1);	
						
					}
					else $('#monthCheck_month').val(d.getMonth());
					getMonthCheck();
	
}

function monthProfit()
{
	
		content='<h1>請選擇月份</h1>'+
				'<select id="monthCheck_year" name="year" onchange="getMonthProfit()"></select>年'+
				'<select id="monthCheck_month" name="month" onchange="getMonthProfit()">></select>月'+
				'<div id="check_container"></div>'
				;
		
		openPopUpBox(content,1200,200,'closePopUpBox');
		
		var d=new Date();
		for (i = d.getFullYear();i>2010;i--)$('#monthCheck_year').append('<option value="'+i+'">'+i+'</option>');
		for (i =1 ;i<=12;i++)$('#monthCheck_month').append('<option value="'+i+'">'+i+'</option>');
		if(d.getMonth()==0)
		{
			$('#monthCheck_month').val(12);	
			$('#monthCheck_year').val(d.getFullYear()-1);	
			
		}
		else $('#monthCheck_month').val(d.getMonth()+1);
			getMonthProfit();	
	
}
function weekProfit()
{
	
		content='<h1>請選擇月份</h1>'+
				'<select id="monthCheck_year" name="year" onchange="getWeekProfit()"></select>年'+
				'<select id="monthCheck_month" name="month" onchange="getWeekProfit()">></select>月'+
				'<div id="check_container"></div>'
				;
		
		openPopUpBox(content,1200,200,'closePopUpBox');
		
		var d=new Date();
		for (i = d.getFullYear();i>2010;i--)$('#monthCheck_year').append('<option value="'+i+'">'+i+'</option>');
		for (i =1 ;i<=12;i++)$('#monthCheck_month').append('<option value="'+i+'">'+i+'</option>');
		if(d.getMonth()==0)
		{
			$('#monthCheck_month').val(12);	
			$('#monthCheck_year').val(d.getFullYear()-1);	
			
		}
		else $('#monthCheck_month').val(d.getMonth());
			getWeekProfit();	
	
}

function getWeekProfit()
{
	$('#check_container').html('<img src="/images/ajax-loader.gif"/>');
		$.post('/order/check_week_profit',{year:$('#monthCheck_year').val(),month:$('#monthCheck_month').val()},function(data){
			
		
				$('#check_container').html(data);
				height =parseInt($('#check_container').css('height').substr(0,$('#check_container').css('height').length-2));
				popUpBoxHeight(height+100);
				
		});
	
}

function preLoadMonth(shop,key,maxV)
{
	$.post('/order/month_check_prepare_imm',{shopID:shop[key]['shopID'],year:$('#monthCheck_year').val(),month:$('#monthCheck_month').val()},function(data)
	{
		if(data.result==true)
		{
			$('#month_progress').html(Math.round(key*100/maxV));
			key++;
			if(key<maxV)preLoadMonth(shop,key,maxV);
			else showMonthProfit();
			
		}
	},'json')		
}
function showMonthProfit()
{
		$.post('/order/check_month_profit',{year:$('#monthCheck_year').val(),month:$('#monthCheck_month').val()},function(data){
			
		
				$('#check_container').html(data);
				height =parseInt($('#check_container').css('height').substr(0,$('#check_container').css('height').length-2));
				popUpBoxHeight(height+100);
				
		});
	
}
function getMonthProfit()
{
	$('#check_container').html('<img src="/images/ajax-loader.gif"/>正在建立暫存檔<span id="month_progress">0</span>%');
		$.post('/order/get_month_check_shop',{year:$('#monthCheck_year').val(),month:$('#monthCheck_month').val()},function(data)
		{
			if(data.result==true)
			{
				preLoadMonth(data.shopList,0,data.shopList.length)
				
			}	
			
		},'json')

	
	
	
}

function getWeekCheck()
{
		$('#check_container').html('<img src="/images/ajax-loader.gif"/>');
	
	
		$.post('/order/get_week_check',{shopID:$('#monthCheck_shopID').val(),year:$('#monthCheck_year').val(),month:$('#monthCheck_month').val(),week:$('#monthCheck_week').val()},function(data){
			
				$('#check_container').html(data);
			
				height =parseInt($('#check_container').css('height').substr(0,$('#check_container').css('height').length-2));
				popUpBoxHeight(height+130);
				
		});
	
}

function shopMonthSend()
{
	alert('信件寄發中，請稍後！！')	;
	$.post('/order/shop_month_check_send',{shopID:$('#monthCheck_shopID').val(),year:$('#monthCheck_year').val(),month:$('#monthCheck_month').val()},function(data){
		
		if(data.result==true) alert('信件已寄出')	;
		
	},'json')
		
	
	
}


function getMonthCheck()
{
		$('#check_container').html('<img src="/images/ajax-loader.gif"/>');
		//if($('#checkMonth').is(':checked'))isCheck = 1;
		//else isCheck = 0;
	
		$.post('/order/get_month_check',{shopID:$('#monthCheck_shopID').val(),year:$('#monthCheck_year').val(),month:$('#monthCheck_month').val(),monthFromDate:$('#monthFromDate').val(),monthToDate:$('#monthToDate').val()},function(data){
			
				$('#check_container').html(data);
			
				height =parseInt($('#check_container').css('height').substr(0,$('#check_container').css('height').length-2));
				popUpBoxHeight(height+130);
				
		});
	
		
	
}

function getConsignmentRecord()
{
		$('#check_container').html('<img src="/images/ajax-loader.gif"/>');
		//if($('#checkMonth').is(':checked'))isCheck = 1;
		//else isCheck = 0;
	
		$.post('/order/get_consignment_record',{shopID:$('#monthCheck_shopID').val(),year:$('#monthCheck_year').val(),month:$('#monthCheck_month').val(),monthFromDate:$('#monthFromDate').val(),monthToDate:$('#monthToDate').val()},function(data){
			
				$('#check_container').html(data);
			
				height =parseInt($('#check_container').css('height').substr(0,$('#check_container').css('height').length-2));
				popUpBoxHeight(height+130);
				
		});
	
		
	
}

function getOtherShop()
{
	$('#selectOrderStatus').val(-2);
	$.post('/order/get_order_distribute',{},function(data){
		
		if(data.result==true)
		{
			var distributeOption ;
			distributeOption+='<option value="0">尚未設定</option>';
			for (key in data.distribute)
			{
				distributeOption+='<option value="'+data.distribute[key].id+'">'+data.distribute[key].distributeName+'</option>';
				
			}
			
			
			getOrderDsitribute(distributeOption)
		}
	},'json');
	
	
}


function getOrderDsitribute(distributeOption)
{
    
    
    
			$.post('/order/get_other_shop',{shopID:$('#select_shopID').val()},function(data){
			if(data.result==true)
			{
				$('#product_list').html('<input type="button"  class="big_button"  value="新增商家" onclick="createOtherShop()"/ >');
				$('#product_list').append('＊可以利用上方的店家選擇器選擇或搜尋店家'+
                                          '<table id="other_shop" style="width:1000px" border="1"></table>'+
                                          '<table id="other_shop_hide"  style="width:1000px" border="1"></table>');
				var tableContent = '<tr  style="background-color:#FFC991">'+
						'<td>店家名稱</td>'+
						'<td>出貨等級</td>'+
						'<td>結帳方式</td>'+
						'<td>狀態</td>'+
						'<td>發票類型</td>'+
                        '<td></td>'+
						
						
					
					
						
					'</tr>'	;
				$('#other_shop').append('<tr><td colspan="6"><h2>運行中的店家資訊</h2></td></tr>'+tableContent)
				$('#other_shop_hide').append('<tr><td colspan="6"><h2>隱藏的店家資訊</h2></td></tr>'+tableContent)
				var show = 0;
				var hide = 0;
				for( var key in data.otherShop)
				{
				
					var color = '';
					if(data.otherShop[key].show==1)
					{
						 show++;
					 	if(show%2==0) color='style="background-color:#EEF"';
						if(show%10==9)$('#other_shop').append(tableContent);
					}
					else 
					{
						if(hide%2==0) color='style="background-color:#EEF"';
						if(hide%10==9)$('#other_shop_hide').append(tableContent);
						hide++;
					}
				
					
					
					
					//cashtype judge
					if(data.otherShop[key].cashType==1) 
					{
						var cashType ='<td>月結'+
									'<select  onchange="editShopName('+data.otherShop[key].shopID+')" id="shipping_day_'+data.otherShop[key].shopID+'"><option value="一">一</option><option value="二">二</option><option value="三">三</option><option value="四">四</option><option value="五">五</option><option value="六">六</option></select>'+
									'<input type="button"  class="big_button"  value="改現款" onclick="changeCashType('+data.otherShop[key].shopID+',0)"/ ></td>'
					}
					else 
					{
						var cashType ='<td>現款<input type="hidden"id="shipping_day_'+data.otherShop[key].shopID+'" value="0"><input type="button"  class="big_button"  value="改月結"  onclick="changeCashType('+data.otherShop[key].shopID+',1)"/ ></td>';
					}
					
					
                   if(data.otherShop[key].shopID<1000) var shopJoin = '<select onchange="editShopName('+data.otherShop[key].shopID+')" id="shop_join_'+data.otherShop[key].shopID+'">'+
                       '<option value="1">加盟中</option>'+
                       '<option value="0">停止加盟</option>'+
                       '</select>';
                    else shopJoin = '';
                    
                    
                    
					//status judge
					if(data.otherShop[key].show==1) var status ='<td><input type="button"  class="big_button"  value="隱藏" onclick="showShop('+data.otherShop[key].shopID+',0)"/ ><input type="button"  class="big_button"  value="刪除" onclick="deleteShop('+data.otherShop[key].shopID+')"/ >'+
                    shopJoin+
                   
                    '</td>'
					else 
					{
						var status ='<td><input type="button"  class="big_button"  value="顯示"  onclick="showShop('+data.otherShop[key].shopID+',1)"/ ><input type="button"  class="big_button"  value="刪除" onclick="deleteShop('+data.otherShop[key].shopID+')"/ >'+
                            shopJoin+'</td>';
						var color='style="background-color:#DDD"';
					}
					
					
					
				
				
				
					var content = '<tr '+color+'>'+			
						'<td id="orderShopName_'+data.otherShop[key].shopID+'"><input type="text" onfocus="showEditMsg()"   class="big_text" onblur="editShopName('+data.otherShop[key].shopID+')" id="editShopName_'+data.otherShop[key].shopID+'" value="'+data.otherShop[key].name+'"></td>'+						
						'<td><select id="distributeType_'+data.otherShop[key].shopID+'" onchange="editShopName('+data.otherShop[key].shopID+')">'+distributeOption+'</select></td>'+
						cashType+
						status+
						'<td><select onchange="editShopName('+data.otherShop[key].shopID+')" id="editShop_invoiceType_'+data.otherShop[key].shopID+'"><option value="3">三聯式發票</option><option value="2">二聯式發票</option></select><br/>'+
						'<select onchange="editShopName('+data.otherShop[key].shopID+')" id="editShop_invoiceByShip_'+data.otherShop[key].shopID+'"><option value="0">整批寄送</option><option value="1">隨貨寄送</option></select>'+
					
					
						'</td>'+	
	                    '<td><input type="button" class="big_button" value="詳細資料" onclick="showShopDetail('+data.otherShop[key].shopID+')"></td>'+
						'</tr>'+
                        '<tr class="hideInf"  id="inf_'+data.otherShop[key].shopID+'">'+
                        '<td colspan="6" style=" background-color:#FF8888">'+
                            '<div id="content_'+data.otherShop[key].shopID+'">'+
                            '統一編號:<input type="text"  class="medium_text" onfocus="showEditMsg()" onblur="editShopName('+data.otherShop[key].shopID+')" id="editShop_comID_'+data.otherShop[key].shopID+'" value="'+data.otherShop[key].comID+'"><br/>'+
                            '電子信箱:<input type="text" class="big_text" style="width:500px" onfocus="showEditMsg()" onblur="editShopName('+data.otherShop[key].shopID+')" id="editShop_email_'+data.otherShop[key].shopID+'"  value="'+data.otherShop[key].email+'">'+
						'<span id="recent_'+data.otherShop[key].shopID+'"></span><br/>'+	
                            '通訊地址:<input type="text"  style="width:500px" class="big_text"onfocus="showEditMsg()"  onblur="editShopName('+data.otherShop[key].shopID+')" id="editShop_address_'+data.otherShop[key].shopID+'" value="'+data.otherShop[key].address+'"><br/>'+
                            '聯絡人員:<input type="text"  class="medium_text"onfocus="showEditMsg()"  onblur="editShopName('+data.otherShop[key].shopID+')" id="editShop_contactPerson_'+data.otherShop[key].shopID+'" value="'+data.otherShop[key].contactPerson+'"><br/>'+
                            '電話號碼:<input type="text"  class="medium_text" onfocus="showEditMsg()" onblur="editShopName('+data.otherShop[key].shopID+')" id="editShop_phone_'+data.otherShop[key].shopID+'" value="'+data.otherShop[key].phone+'"><br/>'+
                            '傳真號碼:<input type="text"  class="medium_text" onfocus="showEditMsg()" onblur="editShopName('+data.otherShop[key].shopID+')" id="editShop_fax_'+data.otherShop[key].shopID+'" value="'+data.otherShop[key].fax+'"><br/>'+
                         '寄件備註:<input type="text"  class="big_text" onfocus="showEditMsg()" onblur="editShopName('+data.otherShop[key].shopID+')" id="editShop_shipComment_'+data.otherShop[key].shopID+'" value="'+data.otherShop[key].shipComment+'"><br/>'+
                        '出貨單：<select id="editShop_showPrice_'+data.otherShop[key].shopID+'" onchange="showEditMsg();editShopName('+data.otherShop[key].shopID+')" >'+
                        '<option selected="selected" value="1">顯示出貨價</option>'+
                        '<option value="0">隱藏出貨價</option>'+
                        '</select><br/>'+
                        '送貨日<select id="editShop_shipInterval_'+data.otherShop[key].shopID+'" onchange="showEditMsg();editShopName('+data.otherShop[key].shopID+')" >'+
                        '<option selected="selected" value="1">隔日配</option>'+
                        '<option value="2">隔2日配</option>'+
                        '<option value="3">隔3日配</option>'+
                          '</select><br/>'+
                        '送貨時間<select id="editShop_assignTime_'+data.otherShop[key].shopID+'" onchange="showEditMsg();editShopName('+data.otherShop[key].shopID+')" >'+
                        '<option selected="selected" value="0">不指定</option>'+
                        '<option value="1">09~13</option>'+
                        '<option value="2">13~17</option>'+
                        '<option value="3">17~20</option>'+

                        '</select>'
                            '</div>'+
                        '</td>'+
					'</tr>'+
                    '<tr style="height:10px"><td></td></tr>';
															
					if(data.otherShop[key].show==1)$('#other_shop').append(content)	;
					else $('#other_shop_hide').append(content)	;
					
					$('#editShop_invoiceType_'+data.otherShop[key].shopID).val(data.otherShop[key].invoiceType);
				
					$('#editShop_assignTime_'+data.otherShop[key].shopID).val(data.otherShop[key].assignTime);
                    $('#editShop_shipInterval_'+data.otherShop[key].shopID).val(data.otherShop[key].shipInterval);
                    $('#editShop_showPrice_'+data.otherShop[key].shopID).val(data.otherShop[key].showPrice);
					$('#distributeType_'+data.otherShop[key].shopID).val(data.otherShop[key].distributeType);
					$('#shipping_day_'+data.otherShop[key].shopID).val(data.otherShop[key].shipOut);
                    $('#shop_join_'+data.otherShop[key].shopID).val(data.otherShop[key].jointype);
                    
                    
					//recentOrder(data.otherShop[key].shopID);
				}
                if(show==0)$('#other_shop').hide();
				if(hide==0)$('#other_shop_hide').hide();
                
                
                
				$('.hideInf').hide();
				
			}
		},'json');	
	
	
}
function showShopDetail(shopID)
{
    
  $('.hideInf').hide();
  $('#inf_'+shopID).show();
  $('#content_'+shopID).slideDown();   
    
}
function recentOrder(shopID)
{
	$.post('/order/get_recent_order',{shopID:shopID},function(data){
		if(data.result==true) 
		{
			$('#recent_'+shopID).html(data.order.orderTime);
		}
		else 
		{
			alert('最近訂單日期登入錯誤')
			
		}		
	},'json');
	
	
	
}



function shopEdit(shopID)
{
	$('#orderShopName_'+shopID).html('<input type="text" id="editShopName_'+shopID+'" value="'+$('#orderShopName_'+shopID).html()+'">');
	$('#orderShopDiscount_'+shopID).html('<input type="text" id="editDiscount_'+shopID+'" value="'+$('#orderShopDiscount_'+shopID).html()+'">');
	$('#shopEditBtn_'+shopID).val('儲存');
	$('#shopEditBtn_'+shopID).attr('onclick','');
	$('#shopEditBtn_'+shopID).bind('click',function(){
			
		editShopName(shopID);
	});

	
	
}




function editShopName(shopID)
{
	
	
	$.post('/order/edit_shop_name',{shopID:shopID,name:$('#editShopName_'+shopID).val(),distributeType:$('#distributeType_'+shopID).val(),shipOut:$('#shipping_day_'+shopID).val(),address:$('#editShop_address_'+shopID).val(),contactPerson:$('#editShop_contactPerson_'+shopID).val(),phone:$('#editShop_phone_'+shopID).val(),fax:$('#editShop_fax_'+shopID).val(),invoiceType:$('#editShop_invoiceType_'+shopID).val(),showPrice:$('#editShop_showPrice_'+shopID).val(),invoiceByShip:$('#editShop_invoiceByShip_'+shopID).val(),comID:$('#editShop_comID_'+shopID).val(),email:$('#editShop_email_'+shopID).val(),jointype:$('#shop_join_'+shopID).val(),shipComment:$('#editShop_shipComment_'+shopID).val()
,assignTime:$('#editShop_assignTime_'+shopID).val(),shipInterval:$('#editShop_shipInterval_'+shopID).val()	
	},function(data){
		if(data.result==true) 
		{
			//getOtherShop();
			if(data.result==true)	$('#editMsgIn').html($('#editShopName_'+shopID).val()+' 資料已儲存');
			else $('#editMsgIn').html('儲存失敗，請洽管理員');
			setTimeout(function(){$('#editMsgIn').detach();},2000);
			
			
		}
		else 
		{
			alert('修改失敗')
			
		}		
	},'json');
	
	
	
	
}

function changeCashType(shopID,cashType)
{

	$.post('/order/change_change_type',{shopID:shopID,cashType:cashType},function(data){
		if(data.result==true) 
		{
			getOtherShop();
		}	
	},'json');	
	
}
function showShop(shopID,status)
{

	$.post('/order/show_shop',{shopID:shopID,status:status},function(data){
		if(data.result==true) 
		{
			getOtherShop();
		}	
	},'json');	
	
}


function deleteShop(shopID)
{
	if(confirm('你確定要刪除？'))
	{
	$.post('/order/delete_shop',{shopID:shopID},function(data){
		if(data.result==true) 
		{
			getOtherShop();
		}
		else 
		{
			alert('修改失敗')
			
		}		
	},'json');	
	}
}

function createOtherShop()
{
	
	content= '<h1>請輸入商家名稱</h1>'+
			'<input type="text" id="newShopName"  name="name" / >';
	openPopUpBox(content,400,200,'newOtherShopSend');		
	
	
}

function newOtherShopSend()
{
	
	$.post('/order/new_other_shop',{name:$('#newShopName').val()},function(data){
		if(data.result==true) 
		{
			getOtherShop();
		}
		else 
		{
			alert('新增失敗')
			
		}
		closePopUpBox();
	},'json')
}


function getDistribute()
{
	$.post('/order/get_order_distribute',{},function(data){
		
		if(data.result==true)
		{
			$('#product_list').html('<input type="button"  class="big_button"  value="新增規則" onclick="newDistributeForm(\'newDistributeForm\')"/ >');
				$('#product_list').append('<div id="newDistributeForm" ></div><table id="distributeTtable" border="1"></table>');
				$('#distributeTtable').append(
					'<tr>'+
						'<td>名稱</td>'+
						'<td>原則折數</td>'+
						'<td>滿額折數</td>'+
						'<td>指定免運費額</td>'+
						'<td>特殊商品定價</td>'+
						'<td></td>'+
						'<td></td>'+
					'</tr>'	
				)
				for( var key in data.distribute)
				{
					$('#distributeTtable').append(
					'<tr>'+
						'<td ><input type="text" id="distributeName_'+data.distribute[key].id+'" value="'+data.distribute[key].distributeName+'"></td>'+
						'<td ><input type="text" id="distributeDiscount_'+data.distribute[key].id+'" value="'+data.distribute[key].discount+'"></td>'+
						'<td id="upDiscount_'+data.distribute[key].id+'"><input type="button" value="新增滿額規則" onclick="newUpDiscount(\''+data.distribute[key].id+'\')"></td>'+
						'<td ><input type="text" id="distributeShipping_'+data.distribute[key].id+'" value="'+data.distribute[key].shippingFee+'"></td>'+
						'<td><input type="button"  class="big_button"  value="查看" onclick="getDistributeProduct(\''+data.distribute[key].id+'\')"/ ></td>'+
						'<td><input type="button"  class="big_button"  value="儲存" onclick="updateDistribute(\''+data.distribute[key].id+'\')"/ ></td>'+
						'<td><input type="button"  class="big_button"  value="刪除" onclick="deleteDistribute(\''+data.distribute[key].id+'\')"/ ></td>'+
					'</tr>'	
					)	
					
					for (index in data.distribute[key].upDiscountList)
					{
						var id = +data.distribute[key].id+'_'+index;
						$('#upDiscount_'+data.distribute[key].id).append(upDiscount(id));
						$('#upDiscount_type_'+id).val(data.distribute[key].upDiscountList[index][0]);
						$('#upDiscount_money_'+id).val(data.distribute[key].upDiscountList[index][1]);
						$('#upDiscount_discount_'+id).val(data.distribute[key].upDiscountList[index][2]);
					}
					
					
					
				}
				
		}
	},'json');	
	
	
	
}


function newUpDiscount(distributeType)
{
	var i = 0 ;
	//alert($('#upDiscount_container_'+distributeType+'_'+i).length);
	while($('#upDiscount_container_'+distributeType+'_'+i).length>0)i++	;
		//alert(i)
	$('#upDiscount_'+distributeType).append(upDiscount(distributeType+'_'+i));
	
	
}

function upDiscount(id)
{
	thisList = id.split('_')
	distributeType = thisList[0];
	content ='<div id="upDiscount_container_'+id+'">'+
				'<select class="upDiscount_'+distributeType+'" id="upDiscount_type_'+id+'">'+
							'<option value="0">單筆</option>'+
							'<option value="1">累積</option>'+
						'</select>'+
						'<input class="upDiscount_'+distributeType+'" type="text" id="upDiscount_money_'+id+'" />元，折扣：'+
						'<input class="upDiscount_'+distributeType+'" type="text" id="upDiscount_discount_'+id+'" />'+
			'</div>'			
	return content;						
							
	
	
	
}


function updateDistribute(distributeType)
{
	
	var distributeName = $('#distributeName_'+distributeType).val();
	var distributeDiscount = $('#distributeDiscount_'+distributeType).val();
	var shippingFee = $('#distributeShipping_'+distributeType).val();
	var upDiscount = '';
	i = 0;
	$('.upDiscount_'+distributeType).each(function(){
			upDiscount+=$(this).val()+',';
			i++;
			if(i%3==0) upDiscount+='-';
	})
	
	$.post('/order/update_distribute',{distributeType:distributeType,distributeName:distributeName,distributeDiscount:distributeDiscount,upDiscount:upDiscount,shippingFee:shippingFee},
		function(data)
		{
			if(data.result==true)
			{
				alert('修改完成')	;
				
			}
			else alert('修改失敗');
			
			
		},'json')
	
	
}



function deleteDistribute(distributeType)
{
	
	$.post('/order/delete_distribute',{distributeType:distributeType},function(data)
	{
		if(data.result==true)
		{
				
			getDistribute()
		}
		else alert('你必須先把定義為此條件的經銷商換成其他條件');
		
	},'json')	
	
	
}




function newDistributeForm(id)
{
		$('#'+id).hide();
		$('#'+id).html(
			'<form id="distributeForm">'+
				'<table>'+
					'名稱：<input type="text" name="name" >'+
					'折數：<input type="text" name="discount" >'+
				'</table>'+
			'</form>'+
			'<input type="button" value="確定" onclick="distributeSend(\''+id+'\')">'+
			'<input type="button" value="取消" onclick="$(\'#'+id+'\').slideUp()">'
		).slideDown();
	
}

function getDistributeProduct(distributeType)
{
	$.post('/order/get_distribute_product_list',{distributeType:distributeType},function(data)
	{
		if(data.result==true)
		{
				content='<table id="distributeProductTable" border="1">';
				content+='<tr>'+
							'<td>遊戲名稱</td>'+
							'<td>遊戲原價</td>'+
							'<td>折扣內容</td>'+
							'<td></td>'+
							'<td></td>'+
						'</tr>'
			for(key in data.product)
			{
					product = data.product[key];
					content+='<tr id="product_'+product.productID+'">'+
							'<td>'+product.ZHName+'('+product.ENGName+')</td>'+
							'<td>'+product.price+'</td>'+
							'<td style=" text-align:left; width:400px">'+discountTurn('product_',product['concessions'])+'</td>'+
								'<td><input type="button"  class="big_button"  value="儲存" onclick="updateDistributeProduct('+product['productID']+','+distributeType+','+(discountIndex-1)+')"/ ></td>'+
								'<td><input type="button"  class="big_button"  value="刪除" onclick="deleteDistributeProduct('+product['productID']+','+distributeType+','+(discountIndex-1)+')"/ ></td>'+
							'</tr>'
	
			}
			content+='</table>';
			openPopUpBox(content,1100,200,'closePopUpBox');
			height =parseInt($('.popUpBox').css('height').substr(0,$('.popUpBox').css('height').length-2))
			
			if(parseInt($('#distributeProductTable').css('height').substr(0,$('#distributeProductTable').css('height').length-2))+350>height) 
			{
				popUpBoxHeight(parseInt($('#distributeProductTable').css('height').substr(0,$('#distributeProductTable').css('height').length-2))+100);
				
				
			}
			
			
		}
	
		
	},'json')
	
	
	
}

function deleteDistributeProduct(productID,distributeType,index)
{
	if(confirm('你確定要刪除?'))
	{
	 $('#product_'+productID).detach();
	 updateDistributeProduct(productID,distributeType,index)
	}
}



function updateDistributeProduct(productID,distributeType,index)
{
	var	distributeNum = '';
	var	distributeDiscount = '';
	$('.distributeNum_'+index).each(function(){
				
		distributeNum+=$(this).val()+',';
		
	})
		$('.distributeDiscount_'+index).each(function(){
				
		distributeDiscount+=$(this).val()+',';
		
	})
	
	$.post('/order/update_distribute_product',{productID:productID,distributeType:distributeType,distributeNum:distributeNum,distributeDiscount:distributeDiscount},function(data)
	{
		
		if(data.result==true)
		{
			
		  	alert('修改完成');
			
		}
		
	},'json')
	
}


function distributeSend(id)
{
	
			$.ajax({
		   type: "POST",
		   dataType:"json",
		   url: "/order/new_order_distribute",
		   data: $("#distributeForm").serialize(),
		   success: function(data){
		   
		   		$('#'+id).slideUp();
				getDistribute()
	
		   	}
			});
	

	
}





function backOrAdjustTable(data)
{
	
		
	
	  if($('#back_product_'+data.productID).length!=0){
		  alert('商品已在清單中');
		  return;
	  }	
	
	
	
	var num = 0;
		$('#backOrAdjustTable').append(
		'<tr  id="back_product_'+data.productID+'">'+
			'<td>'+data.productNum+'</td>'+
			'<td><input type="hidden" name ="BOA_productID_'+data.productID+'" value='+data.productID+'>'+data.ZHName+'</td>'+
			'<td>'+data.ENGName+'</td>'+
			'<td>'+data.language+'</td>'+
			'<td>'+data.nowNum+'</td>'+
			'<td style="width:50px">'+
					'<div class="btn_minus" onclick="changeAmount(0,\'purchase_num_'+data.productID+'\')" style=" margin-left:10px;"></div>'+
					'<div style="float:left">	<input type="text" class="short_text" value="1"  id="purchase_num_'+data.productID+'" name = "purchase_num_'+data.productID+'" '+
					'onclick="$(\'#purchase_num_'+data.productID+'\').select()" onblur="chkMinus(\''+data.productID+'\')" /></div>'+
					'<div class="btn_plus" onclick="changeAmount(1,\'purchase_num_'+data.productID+'\')"></div>'+
			'</td>'+	
			'<td>'+
                  '<select style="display:none" class="back_reason" id="back_reason_'+data.productID+'" name="back_reason_'+data.productID+'">'+
                        '<option value="0">請選擇退貨原因</option>'+
                        '<option value="1">盒損</option>'+
                        '<option value="2">缺件或瑕疵</option>'+
                        '<option value="3">退賣</option>'+
                        '<option value="5">展場</option>'+
                        '<option value="6">誤寄</option>'+
                        '<option value="7">其他</option>'+
                    '</select>'+
					'<textarea name="comment_'+data.productID+'" class="BOA_comment"></textarea>'+
            
			'</td>'+		
			'</tr>'
		)
	if($('#BOAtype').val()=='backProduct') $('.back_reason').show();
      
	height =parseInt($('.popUpBox').css('height').substr(0,$('.popUpBox').css('height').length-2))
	if(parseInt($('#backOrAdjustTable').css('height').substr(0,$('#backOrAdjustTable').css('height').length-2))+350>height) 
	{
		popUpBoxHeight(height+50);
		
		
	}
	
	
	
	
	
	
	
}

function changeAmount(token,id)
{


	if(token==1)	
	{
		productID = id.substr(13);
		
		$('#'+id).val(parseInt($('#'+id).val())+1);
		limitCheck(productID);
	}
	else if(parseInt($('#'+id).val())>0)$('#'+id).val(parseInt($('#'+id).val())-1);
	
	
}





//======================order back  and adjust function 

function backOrAdjustSend()
{
	$('#adjust_company').hide();
	$('#adjust_myself').hide();
	var errno = 0;
	if($('#BOAtype').val()=='backProduct') var form='退';
	else var form='調';
	
  
    
    if($('#BOAtype').val()=='backProduct') 
    {
        
        $('.back_reason').each(function(){
            if($(this).val()==0&&errno==0)
            {
                alert('你必須對為每一個退貨商品選擇註解');
                $('#popUpBoxEnter').show();
                $('#adjust_company').show();
                $('#adjust_myself').show();
                errno = 1;
            }


        })
    }
    else
    {
        $('.BOA_comment').each(function(){
		if($(this).val()==''&&errno==0)
		{
			alert('你必須對為每一個'+form+'貨商品留下註解');
			$('#popUpBoxEnter').show();
			$('#adjust_company').show();
			$('#adjust_myself').show();
			errno = 1;
		}
		
		
	})
        
        
    }
    
	
	
	if(form=='退' && !myImageUploader1.isReady())
	{
		alert('圖檔上傳中，請稍後再試');
		$('#popUpBoxEnter').show();
		errno = 1;
		
	}
	

	
	
	
	
	if(errno==0)
	{
		var url = "/order/back_or_adjust";
		if($('#BOAtype').val()=='adjust')url+='/'+$('#adjust_way').val();
		$.ajax({
		   type: "POST",
		   dataType:"json",
		   url: url,
		   data: $("#backOrAdjustForm").serialize(),
		   success: function(data){
			 
			   if(data.result==true)
			   {  
			   		
					
					if($('#BOAtype').val()=='adjust')				   
					{
                        if($('#destinationShopID').val()==0)
                            {
                               confirm('商品調回總公司，請至進貨處下載進貨單'); 
                                
                                
                            }
                        else
                        {
						confirm(form+'貨資訊已送出，庫存會在稍後更新');
						
							 showBOAList('adjust','watch');	  
							showOrderAdjust(data.adjustID);
                        }
                        
                    }
					else
					{
						
						
						afterBackSend(data.backID,myImageUploader1.getFiles())
						
						
						
					}
					
					closePopUpBox();
				
			   }
			  
		   }
		 });
	}

	
	
	
}

function afterBackSend(backID,file)
{
	
	$.post('/order/after_back_send',{backID:backID,file:file},function(data)
	{
		 showBOAList('Back','watch');	  
		showOrderBack(backID,'watch')
		
	},'json')
	
}


function adjustSelectShop()
{
	/*
	if($('#remote').val()==1)
	{
		
		if(!confirm('你現在在遠端模式，強烈不建議您進行調貨，會造成庫存的錯誤，是否仍要繼續？'))return;
		if(!confirm('進行完調貨後，請記得回到店內本機電腦進行手動扣減數量'))return;
	}
	*/
	if(onShoppingCheck()==false) return;
		else $('#clientOrderQuery').html('');
		$('#carOption').detach();
	    $('#product_list').html('');
			$.post('/system/get_shop',{token:1},function(data){
				if(data.result==true)
				{
					content='<h1>請選擇店家</h1>'+
							'<select id="adjust_shopID" name="shopID"></select>';
					
					openPopUpBox(content,500,200,'adjustSelectShopDone');
					
               
					for( var key in data.shopData)
					{
							if($('#myshopID').val()!=data.shopData[key].shopID)	$('#adjust_shopID').append('<option value="'+data.shopData[key].shopID+'">'+data.shopData[key].name+'</option>');
						
					}
                    $('#adjust_shopID').append('<option value="0">幻遊天下總公司倉庫</option>');
                         
				}
			
		
		},'json')

	
}

function adjustSelectShopDone()
{
	backOrAdjust('adjust',$('#adjust_shopID').val());
	
}

function addShippingFee()
{
	
	alert('調貨運費品項為1元，利用數量增減可以調整價錢')
	var shippingFee = {
		'productNum':'PHA100',
		'productID':'8882207',
		'ZHName':'調貨運費(1元)',
		'ENGName':'請選擇數量調整價錢',
		'language':'其他',
		'nowNum':'0'		
		}
	backOrAdjustTable(shippingFee)
	
	
	
}




var myImageUploader1;	

function backOrAdjust(type,destinationShopID)
{
	
	if(onShoppingCheck()==false) return;
	else $('#clientOrderQuery').html('');
	$('#carOption').detach();
	$('#product_list').html('');
	if(type=='backProduct') var form='退';
	else var form='調';

	var content = '<h1>'+form+'貨清單</h1>';
	if(type=='backProduct') content+='第一步：店家提出申請，若為盒損請上傳照片，選擇退貨品項<br/>'+
								
									'<div id="upload_block">'+  
								  '<h2>圖檔上傳區，可上傳 8 張圖</h2>'+  
						  '<div id="preview_block"></div>'+  
			  	   '<div style="clear: both;">'+
					    '<span id="image_input_block"></span>'+
 			 		'</div> ' +
                  
				  '</div> ';
				  
								
									
	
	
	content+=
			  '<div id="backOrAdjustQuery" style=" margin-top:20px;"></div>'+
			  '<div>'+
			  '<form id="backOrAdjustForm">'+
			  '<input type="button" class="big_button" value="加入運費" onclick="addShippingFee()" id="shippingFeeBtn">'+
			  '<input type="hidden" id="BOAtype" name="BOAtype" value='+type+'>'+
			  '<input type="hidden" id="destinationShopID" name="destinationShopID" value='+destinationShopID+'>'+
			  '<table id="backOrAdjustTable" border="1" width="1000px">'+
			  	'<tr>'+
					'<td>產品編號</td>'+
					'<td>中文名稱</td>'+
					'<td>英文名稱</td>'+
					'<td>語言</td>'+
					'<td>目前數量</td>'+
					'<td>'+form+'貨數量</td>'+		
					'<td>'+form+'貨原因</td>'+		
				'</tr>'+
			  '</table>'+
			  '</form>'+
			  '</div>'+
			  '<div id="after"></div>'
			  ;




	 openPopUpBox(content,1000,600,'backOrAdjustSend');		
	if(type=='backProduct') 
	{
		$('#shippingFeeBtn').hide();
			 myImageUploader1 = new JSG.imgUploader({
    fileLimits: 8,
    actionUrl: '/phantri/photo_upload',
    inputContainer: 'image_input_block',
    previewContainer: 'preview_block',
	loadingIcon: '/images/loading_indicator_big.gif',  
  	deleteIcon: '/images/icon_delete.gif'
  });
  $('#after').html(	'2.公司審核通過<br/>'+
									'3.店家按下產品寄回(庫存扣減)<br/>'+
									'4.完成退貨<br/>');
	
	
	
	
	
	
	}
	 //getShipmentShop(0);
	 queryProduct('backOrAdjust','select');	
	
	if(type=='adjust')
	{
		
		$('#popUpBoxEnter').detach();
		$('#popUpBoxCancel').before('<input type="hidden"  id="adjust_way" value="0" >');	
		$('#popUpBoxCancel').before('<input type="button" class="big_button"  id="adjust_myself" value="自行寄送" >');	
		$('#popUpBoxCancel').before('<input type="button"  class="big_button"   id="adjust_company" value="由公司協助寄送" >');	
		
		$('#adjust_myself').click(function(){ adjustPanel(0)});
		$('#adjust_company').click(function(){ adjustPanel(1)});
		
	}
	
}
function deleteAdjustOrder()
{
	alert("由於調貨資訊已經送到對方電腦，並且已經扣存您電腦的庫存，無法直接刪除。\n若要更正，請向對方要求再把相同貨品調回，以利更正自身庫存。")	
	
	
	
	
	
}
function adjustPanel(t)
{
	$('#adjust_company').hide();
	$('#adjust_myself').hide();
	if(t==0)
	{
		
		if(confirm('您將自行寄送調貨物品，請記得輸入運費喔！按確定繼續，按取消鍵重新設定運費'))
		{
			$('#adjust_way').val(t);
				backOrAdjustSend();
		}
		else
		{
			$('#adjust_company').show();
			$('#adjust_myself').show();
			
		}
		
	}
	else
	{
		if(confirm('您請求由公司協助寄送調貨物品，請您將貨品送回公司以利寄送'))
		{
			$('#adjust_way').val(t);
				backOrAdjustSend();
		}
		else
		{
			$('#adjust_company').show();
			$('#adjust_myself').show();
			
		}
		
	}
	

		
	
	
}

function showOrderBack(orderID,power)
{

	$.post('/order/get_order_back',{orderID:orderID},function(data)
	{
		if(data.result==true)
		{
			orderNumber = 0;
			var	 content ='<div id="orderAppendQuery"></div>';
			content +='<form id="orderBackForm">'+
						'<input type="hidden" name="orderID"  id="order_backID" value="'+data.order.id+'">'+
						'<div style="text-align:left">退單編號：b'+data.order.id+' 店家：'+data.order.name+' 狀態:'+data.order.status;
			 
			
			content+='</div>';
			
			content +=orderBackTable(data,power,data.order.status);
		
			if(power!="staff")content +='<h2>訂單備註：</h2><div style=" background-color:#CCC">'+data.order.comment+'</div>';
			if(power=="staff")
			{
				if(data.order.statusID<3)
				{
 	  			content +='退單狀態：<select name="order_status" id="order_status">'+
							'<option value="0">退單審核中</option>'+
			   				'<option value="1">同意寄回</option>'+
							'<option value="2">不同意寄回</option>'+
						'</select>';
					content +='<h2>退單備註：</h2><textarea name="back_comment"  id="order_back_comment" style=" width:800px">'+data.order.comment+'</textarea>';	
				}
				else 
				{
					content +='<input type="hidden"    name="order_status" id="order_status" value="4" >';	
					
				}
				
			
			}
			
			
			content +='</form>'+ '<div class="img">';
				
				
				for(key in data.order['img'])
				{
        			content+='<a href="'+data.order['img'][key]+'">'+
							'<img src="'+data.order['img'][key]+'"   style="max-width:100px; max-height:100px" >'+
				            '</a>'
				}
			
			content+='</div><div style="clear:both"></div>';
			if(power=="staff")content +='<input type="button" class="big_button" value="列印退貨單" onclick="order_back_print()">';
			else content +='<input type="button" class="big_button" value="列印退貨單" onclick="window.open(\'/order/back_print_out/'+data.order.id+'\', \'_blank\')">';
				if(power=='staff') fun = 'orderBackUpdate';
				else 
				{				
					fun ='orderBackProgress';
					content+= '<input type="hidden" id="order_status" value="'+data.order.statusID+'">';
				}
				
				
			   openPopUpBox(content,1100,200,fun);
			   $('.img a').lightBox();
			  if(fun =='orderBackProgress' && data.order.statusID==1) 
			  {
				  /*
					if($('#remote').val()==1)
					{
						
						if(!confirm('你現在在遠端模式，強烈不建議您進行退貨，會造成庫存的錯誤，是否仍要繼續？'))return;
						if(!confirm('進行完退貨後，請記得回到店內本機電腦進行手動扣減數量'))return;
					}	
				*/
				  $('#popUpBoxEnter').val('點此設定產品已寄回，扣減庫存');
				}
			  if(power=="staff"&&data.order.statusID==3)$('#popUpBoxEnter').val('已收到貨品，點此完成退貨手續');
			  else  $('#order_status').val(data.order.statusID);
			  height =parseInt($('#orderTable').css('height').substr(0,$('#orderTable').css('height').length-2))
	  popUpBoxHeight(height+200);

			//countTotal(1);
		}
		
	},'json')	
	
	
}

function showOrderAdjust(adjustID) 
{
	$.post('/order/get_order_adjust',{adjustID:adjustID},function(data)
	{
		if(data.result==true)
		{
			orderKey = false;
			content= '';
			content+='<h2>調出店家：'+data.adjust.fromShopName+'</h2>';
			content+='<h2>收貨店家：'+data.adjust.destinationShopName+'</h2>';
			content+=
			'<div>'+
			'<table border="1" id="orderTable" style="width:1100px;text-align:center" >';
			product = data.product;
			for( var key in product)
			{
				content+=productViewRow(product[key],
				{
					subtotal:false,
					purchasePrice:false,
					purchaseCount:false,
					shippingStatus:false,
					count:false,
					orderNum:false,
					rowDelete:false});
				
			} 	
						
			content+='</table>';
			content+='</div>';
			openPopUpBox(content,1100,200,'closePopUpBox');
			height =parseInt($('#orderTable').css('height').substr(0,$('#orderTable').css('height').length-2))
			popUpBoxHeight(height+200);			
		}
		
	},'json')

	
	
}



function orderBackTable(data,type,status)
{
	orderKey =false;
	content=
	'<div>'+
	'<table border="1" id="orderTable" style="width:1100px;text-align:center" >';
	product = data.product;
	for( var key in product)
	{
		product[key]['sellPrice'] = product[key]['purchasePrice'] ;
		content+=productViewRow(product[key],
		{
			subtotal:false,
			purchasePrice:true,
			purchaseCount:true,
			shippingStatus:false,
			count:false,
			orderNum:false,
			rowDelete:false});
		
	} 	
				
	content+='</table>';
	content+='</div>';
	return content;
}


function orderBackRowDelete(backID,productID)
{
	if(confirm('你確定要刪除？'))
	{
		$.post('/order/order_back_product_delete',{backID:backID,productID:productID},function(data){
			
			if(data.result==true){
					$('#orderRow_'+productID).detach();
				
				
				}
		},'json')	
	}
	
}

function orderBackProgress()
{
	if($('#order_status').val()==1){
		if(confirm('已同意寄回，是準備寄回，點選確定將寄回狀態更改為寄回中，若否則點選取消'))
		{
			var orderBackID = $('#order_backID').val();
			$.ajax({
			   type: "POST",
			   dataType:"json",
			   url: "/order/order_back_update",
			   data: 'orderID='+orderBackID+'&order_status=3',
			   success: function(data){
				   
				   if(data.result ==true)
				   {
					   closePopUpBox();
					  	confirm('退貨資訊已送出，庫存會在稍後更新');
					 	  showBOAList('Back','watch');	  
						 showOrderBack(orderBackID,'watch');
						
						  $('#backBtn').trigger('click');
				   }
			   }
			   
			   })			
		
			
		}
		else  closePopUpBox();		
		
	}
	else  closePopUpBox();

	
	
}






function orderBackUpdate()
{
	if($('#order_status').val()==2&&$('#order_back_comment').val()==''){
		alert('請友善地回答不可退貨的原因');
		$('#popUpBoxEnter').show();
		return;
		
	}

	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/order_back_update",
	   data: $("#orderBackForm").serialize(),
	   success: function(data){
		   
		   if(data.result ==true)
		   {
			   closePopUpBox();
			   showBOAList('Back','staff');
			   
						  $('#backBtn').trigger('click');
		   }
	   }
	   
	   })
	
	
}
//adjust

var BOAListOffset = 0;
function showBOAList(type,power)
{
	if(onShoppingCheck()==false) return;
		else $('#clientOrderQuery').html('');
$('#clientOrderQuery').html('')
	$('#productQuery').html('');
	$('#productSelect').detach();
	nowList = type;
	$('#product_list').html(BOAListTable(type,power));
	BOAListOffset= 0;
	getBOAList(BOAListOffset,15,type,power);

	
	
	
}

function BOAListTable(type,power)
{
	if(type=='Back') var form='退';
	else var form='調';
	
	var result='<h1'+form+'貨清單</h1>'+
				'<table id="BOA_list" border="1" style="text-align:center">'+
				'<tr>';
			result+=
					'<td>'+form+'單編號</td>'+
					'<td>'+form+'貨申請日期</td>';
					
			if(type=='Back')result+='<td>退貨寄出日期</td>';
			else result+='<td>調出店家</td>';
	
			if(type=='Back')result+='<td>退貨收到日期</td>';
		
			result+='<td>'+form+'貨店家</td>';
			if(type=='Back')result+='<td>退單狀況</td>';
			
			result+='<td>查看訂單</td>'+
	                '<td>刪除訂單</td>'+
					'</tr>'+
			'</table>'+
			'<input type="button" value="查看更多" id="moreOrderBtn" onclick="getBOAList(BOAListOffset,10,\''+type+'\',\''+power+'\');"class="big_button"/>'
	
	return result;
}


function getBOAList(offset,num,type,power)
{


	BOAListOffset= offset+num;
	$.post('/order/get_boa_list',{offset:offset,num:num,shopID:$('#select_shopID').val(),type:type},function(data)
	{
		
		if(data.result==true)
		{
			if(offset==0)$('#product_list').html(BOAListTable(type));	
			 product = data.BOAList;
			 
			for( var key in product)
			{
				
				var content ='<tr id="order_'+product[key].id+'">';
					
						
					if(type == 'Back')
					{
						content+=	
						'<td id="BOAID_'+product[key].id+'">b'+product[key].id+'</td>'+
						'<td>'+product[key].requestTime+'</td>'+
						'<td>'+product[key].backTime+'</td>'+
							'<td>'+product[key].backToken+'</td>'+
						'<td id="shopName_'+product[key].id+'">'+product[key].shopName+'</td>'+
						'<td id="shopStatus_'+product[key].id+'">'+product[key].status+'</td>';
					}
					else
					{	
						content+=	
						'<td id="BOAID_'+product[key].id+'">a'+product[key].id+'</td>'+
						'<td>'+product[key].time+'</td>'+
						'<td>'+product[key].fromShopName+'</td>'+
						'<td id="shopName_'+product[key].id+'">'+product[key].destinationShopName+'</td>';
						
					}
					
						content+=						
						'<td><input type="button" value="查看" onclick="showOrder'+type+'('+product[key].id+',\''+power+'\')"class="big_button" ></td>';
					
				content+='<td><input type="button" value="刪除" onclick="delete'+type+'Order('+product[key].id+')"class="big_button" ></td>';

						content+='</tr>';
	
				$('#BOA_list').append(content)
					
				
			}
		}
		else
		{
				$('#BOA_list').append('沒有其他資料了');
				$('#moreOrderBtn').hide();
		}
	},'json')	
	
	
}











//==

var shipmentType ='month';
function shipmentTypeChange(token)
{
	if(token==true) 
	{
		if(shipmentType =='month') shipmentType ='consignment';
		else shipmentType ='month';
	}
	
	if(shipmentType =='month')
	{
		$('#shipmentType').val('觀看買斷商品');
		shipmentType = 'consignment';
		$('#monthShipping_container').hide();
		$('#consigmentShipping_container').show();
		
	}
	else
	{
		$('#shipmentType').val('觀看寄賣商品');
		shipmentType = 'month';
		$('#consigmentShipping_container').hide();
		$('#monthShipping_container').show();
	}
	
	
	
}


var waitString ='';
var needShipping = false;
var dirNeedShipping = false;
function shipmentView(type,addressID,shopID)
{
	needShipping = false;
    dirNeedShipping = false;
	if(onShoppingCheck()==false) return;
		else $('#clientOrderQuery').html('');
	$('#carOption').detach();
	$('#selectOrderStatus').val(5);
    if(!shopID)
    {
        shopID = $('#select_shopID').val();
        if($('#select_shopID').val()==0) 
        {
            getAvailableOrder('product_list')
            return;
        }
	
    }
	
		
		$.post('/order/shipment_view',{shopID:shopID,addressID:addressID},function(data){
			
			if(data.result==true){
						addressList ='';
						for(key in data.addressList)
						{
							if(data.addressList[key].addressID==25) data.addressList[key].receiver = '(未指定地點)';
							 addressList+='<input type="button"  class="big_button"  onclick="shipmentView(\''+type+'\','+data.addressList[key].addressID+')" value="'+data.addressList[key].receiver+'">';
						}
						content=
								'<div style="float:left;border-top:double; margin-top:2px;  margin-bottom:2px">'+
								'<input type="button" id="shipmentType" class="big_button"  value="觀看寄賣商品" onclick="shipmentTypeChange()">'+
									'<div style=" clear:both"></div>'+
									'<div id="goingList"></div>'+
									'<div style=" clear:both"></div>'+
								'<div style="float:left">'+	
									
									'<div id="monthShipping_container">';
									
						if(type=='staff' &&data.order['month'][0])content+='<input type="button" class="big_button" value="產生出貨單" onclick="shipmentCreate(\'monthShipping_ok_form\',0,'+data.order['month'][0].addressID+')">';
						else; 
					
						content+= '<div>請選擇出貨地點(收件人)<span id="monthAddressList">'+addressList+'</span><h2 style="color:red">找不到訂的貨品嗎？請點選上方收件人看看是不是寄件地址不同的原因喔</h2></div>';
						
						content+=  '<div style=" background:#FFF5EB	; padding-bottom::30px">';//start of color container	
						
						if(typeof(data.order['month'][0]) != 'undefined')content+=   '<h1>'+ data.order['month'][0].receiver  +' 本次可出貨之<span style=" color:red">買斷</span>商品</h1>';
						else content+= '<h1>本次無可出貨品</h1>';
						content+=   '<div id="shippingStatus"></div>';

                        if(typeof(data.order['month'][0]) != 'undefined' && data.order['month'][0].thisComID!='') comID = ' 客戶統編：'+data.order['month'][0].thisComID;
                        else comID ='';
						if(data.order['month'][0])		
						content+= 	'<div style=" clear:both">收件人：'+data.order['month'][0].receiver+' 地址：'+data.order['month'][0].address+' 電話：'+data.order['month'][0].phone+comID+' <span class="shippingTotal"></span>';
						
						if(typeof(data.order['month'][0]) != 'undefined' )
						{
                            if(data.distribute.cashType!=1)
							content+=   '<input type="button" id="shipOutBtn" class="big_button"  value="匯款後請點此通知" onclick="shipmentOut('+data.order['month'][0].addressID+',0)">';
                            else{
                                content+=   '<input type="button" id="shipOutBtn" class="big_button"  value="請於次工作日出貨" onclick="shipmentOut('+data.order['month'][0].addressID+',1)">';
                                
                                
                            }
						}
						
						content+=   '</div>';
						content+=	'<form id="monthShipping_ok_form">'+
									'<table border="1" id="monthShipping_ok" style="width:1100px;text-align:center" ></table>'+
									'</form>'+
									'<div class="shippingTotal" style="float:right; font-size:14pt"></div>'+
									'<div style="clear:both"></div>'+
									
									'<div id="orderCommentContainer">'+
									'<h1>訂單備註</h1>'+
									'<table id="orderComment"  border="1"  style="width:1100px;text-align:center">'+
										'<tr><td>訂單編號</td><td>下單時間</td><td>備註內容</td><td>查看訂單</td></tr>'+
									'</table>'+
									'</div>';
						content+=  '<div style=" height:20px"></div>';									
						content+=  '</div>';//end of color container	


						content+=  '<div style=" background:#EBFFFF; padding-bottom::30px">';//start of color container			 
						content+=	'<h1  style="border-top:double; margin-top:20px;  margin-bottom:2px">以下商品為無現貨</h1>'+
                                   
									'<table border="1" id="monthShipping_wait" style="width:1100px;text-align:center" ></table>'+
									'</div>'
						content+=  '</div>';//end of color container			
						
						
						//寄賣品的部份
						content+='<div id="consigmentShipping_container">';
						if(type=='staff' &&data.order['consignment'][0])content+='<input type="button" class="big_button" value="產生出貨單" onclick="shipmentCreate(\'consigmentShipping_ok_form\',1,'+data.order['consignment'][0].addressID+')">';
									
					
						 content +='<div>請選擇出貨地點<span  id="consignmentAddressList">'+addressList+'</span></div>';
					 								content+=  '<div style=" background:#FFF5EB	; padding-bottom::30px">';//start of color container
  						    content +='<h1>本次可出貨之<span style=" color:red">寄賣商品</span></h1>';	

							if(data.order['consignment'][0])			
						  content +=		
									'<div style=" clear:both">收件人：'+data.order['consignment'][0].receiver+' 地址：'+data.order['consignment'][0].address+' 電話：'+data.order['consignment'][0].phone+'</div>'
							
							
							content +=	
									'<form id="consigmentShipping_ok_form">'+
									'<table border="1" id="consignmentShipping_ok" style="width:1100px;text-align:center" ></table>'+
									'</form>';
							content+=  '</div>';//end of color container			
		
							content+=  '<div style=" background:#FFF5EB	; padding-bottom::30px">';//start of color container			 
							content+='<h1 style="border-top:double; margin-top:20px;  margin-bottom:2px">以下商品為無現貨</h1>'+
                                  
									'<table border="1" id="consignmentShipping_wait" style="width:1100px;text-align:center;" ></table>'+										
									'</div>'+
									'</div>';
							content+=  '<div style=" height:20px"></div>';									
							content+=  '</div>';//end of color container		
						  //available list
						  content+='<div style="float:left; width:150px;" id="available_container"></div></div>';
								;
								$('#product_list').html(content)
								
								getShipmentList(-1,15,type,4,shopID);
								
								if(type=='staff')getAvailableOrder('available_container');
								shipmentTypeChange(1);
								var product = data.order['month'];
								orderKey =false;
								monthShippingOkIndex = 1;
								monthShippingWaitIndex = 1 ;
								consignmentShippingOkIndex = 1;
								consignmentShippingWaitIndex = 1;
								var orderNum = 0;
								var orderCommentKey = false;
								if(product.length>0&&product[0].orderComment!='')
								{	
										
									$('#orderComment').append(
									'<tr><td>'+magicCheck(product[0].magic)+'o'+product[0].orderNum+'</td><td>'+product[0].orderTime.substr(0,16)+'</td><td>'+product[0].orderComment+'</td><td><input type="button" value="查看" onclick="showOrder('+product[0].orderID+',\'訂單已送達\',\'watch\')" class="big_button"></td></tr>');
									orderCommentKey = true;
								}
								
								
								var shippingTotal = 0 ;
                                var selfTotal = 0;
								for(var key in product)
								{
									if(orderNum==0) orderNum = product[key].orderNum;
									else if(orderNum!=product[key].orderNum)
									{
										if(product[key].orderComment!='')	
										{
											
										$('#orderComment').append(
										'<tr><td>'+magicCheck(product[0].magic)+' o'+product[key].orderNum+'</td><td>'+product[key].orderTime.substr(0,16)+'</td><td>'+product[key].orderComment+'</td><td><input type="button" value="查看" onclick="showOrder('+product[key].orderID+',\'訂單已送達\',\'watch\')" class="big_button"></td></tr>');
										orderCommentKey = true;
										}
										orderNum = product[key].orderNum; 	
									}
								
								
								
									if(product[key].sellNum!=0) 
									{
										
										//已訂購可出貨
										if(parseInt(product[key].buyNum)> parseInt(product[key].sellNum))
										{
											
											//剩下需等待
											
											var waitProduct = jQuery.extend(true, {}, product[key]);
											
											
											waitProduct.buyNum = parseInt(waitProduct.buyNum) - parseInt(waitProduct.sellNum);
												
											waitProduct.sellNum = 0;
											$('#monthShipping_wait').append(shipmentRow(waitProduct,type,status,monthShippingWaitIndex++));												
											
										
														
										}
										
										shippingTotal += product[key].sellPrice * product[key].sellNum;
                                     
                                        
                                        if((product[key].productNum.indexOf("PH"))!=-1)selfTotal += product[key].sellPrice * product[key].sellNum;
										product[key].productComment = product[key].productComment.replace('貨量不足','');
										
									$('#monthShipping_ok').append(shipmentRow(product[key],type,status,monthShippingOkIndex++));
										
										
										
									}
									else $('#monthShipping_wait').append(shipmentRow(product[key],type,status,monthShippingWaitIndex++));
									
								} 	
                              
								if(orderCommentKey==false)$('#orderCommentContainer').hide();//沒有備註，收起來
                                    
                    
								$('.shippingTotal').html('本次可出貨金額：$'+shippingTotal  );
                            
                                                      
								
								if(data.distribute.cashType==1)
								{
									                                              //商城指定出貨
									if(data.order['month'][0]!==undefined&&data.distribute.shippingFee>=shippingTotal && data.order['month'][0].shipmentStatus<3)
									{
										$('#shippingStatus').html('<div style="color:red	">目前未達指定免運費額!<label style=" font-size:14pt" ><input type="radio" value="1" id="shipmentStatusOk" name="shipmentStatus" onclick="shipmentStatusChange()">加運費出貨</label>'+
						'<label style=" font-size:14pt" ><input type="radio" value="2" id="shipmentStatus5000" name="shipmentStatus"  onclick="shipmentStatusChange()">免運再出貨</label></div>');
				
									if(data.order['month'][0]&&data.order['month'][0].shipmentStatus==1) $('#shipmentStatusOk').attr("checked",true);
										else if(data.order['month'][0]&&data.order['month'][0].shipmentStatus==2)  $('#shipmentStatus5000').attr("checked",true);

											
										
									}
									else
									{
									
                                            
                                        
											$('#shippingStatus').html('目前狀態將於本週'+data.distribute.shipOut+'出貨');
                                         if(data.order['month'][0]!==undefined&& data.order['month'][0].shipmentStatus ==3) $('#shippingStatus').append('，因為有商城貨物將主動出貨');
									}
								}
								else if(data.distribute.shippingFee>=shippingTotal && selfTotal <3000)
								{
									needShipping = true;
								
									$('.shippingTotal').append('+100元運費');
								}
								
								if(shippingTotal<=8000  && selfTotal <3000)dirNeedShipping = true;
								 if ($('#shipmentStatusOk').val()==1 &&  data.order['month'][0].shipmentStatus!=3)
								 {
									 
									 needShipping = true;
								
									$('.shippingTotal').append('+100元運費');

									 
								 }
								
	                                      if(data.distribute.cashType!=1) $('.shippingTotal').append( '<br/>匯款資訊:兆豐國際商業銀行(017)板橋分行<br/>幻遊天下股份有限公司 20609013372');     
                                                        
								
								
						/*
							
						'目前狀態已達2倍運費指定額，可指定於下個工作日(一~五)出貨'
						*/
						
								$('.shippingFee').html(data.distribute.shippingFee);
								product = data.order['consignment'];
								
								orderKey =false;
								var orderNum = 0
								for( var key in product)
								{
									

									if(product[key].sellNum!=0) 
									{
									

										if(parseInt(product[key].buyNum)> parseInt(product[key].sellNum))
										{
											
											//剩下需等待
											var waitProduct = jQuery.extend(true, {}, product[key]);
										
											waitProduct.buyNum = parseInt(waitProduct.buyNum) - parseInt(waitProduct.sellNum);
											waitProduct.sellNum = 0;
											$('#consignmentShipping_wait').append(shipmentRow(waitProduct,type,status,consignmentShippingWaitIndex++));												
											
										
																
										}
										
											//已訂購可出貨
										$('#consignmentShipping_ok').append(shipmentRow(product[key],type,status,consignmentShippingOkIndex++));
									}
									else $('#consignmentShipping_wait').append(shipmentRow(product[key],type,status,consignmentShippingWaitIndex++));
														
								} 	
											
				
								waitList = waitString.split(',')
								
								for(key in waitList)
								{
									
									onDate(waitList[key]);
								}
					
				

								
				}
		},'json')	
	
}







function shipmentStatusChange()
	{
	
		shopID =$('#select_shopID').val();

		$.post('/order/change_shipment_status',{shopID:shopID,status:$('input[name=shipmentStatus]:checked').val()},function(data){
				
					if(data.result==true)
					{
						alert('狀態已改變');
					}
				
				
			},'json')			
		
		
	}


function getAvailableOrder(id)
{
	
	$('#'+id).html('<table id="availableTable" border=1; style="margin-left:10px" ><tr style="background-color:#FFEBEB"><td colspan="3">月結客戶</td></tr><tr style="background-color:#FFEBEB">'+
					'<td></td>'+
					'<td>訂貨店家</td>'+
					'<td>類型</td>'+
					'<td>可出貨金額</td>'+
					'<td>出</td>'+
	                '</tr></table>'+
					'<table id="availableTable_cash" border=1; style="margin-left:10px" ><tr style="background-color:#FFEBEB"><td colspan="3">現款客戶</td></tr><tr style="background-color:#FFEBEB">'+
					'<td></td>'+
					'<td>訂貨店家</td>'+
					'<td>類型</td>'+
					'<td>可出貨金額</td>'+
					'<td>出</td>'+
					
	                '</tr></table>'
					
					
					);
	$.post('/order/get_available_shop_order',{},function(data)
	{
		if(data.result==true)
		{
			for(key in data.availableShipment)
			{
				var type='';
				var typetoken;
			
				if (data.availableShipment[key].type==1)
				 {
					 type= '寄賣';
					 typeToken='consignment';
				 }
				else 
				{
					type='<span style="color:red">買斷</span>' ;
					 typeToken='month';
				}
			
				switch(data.availableShipment[key].shipOut)
				{
					case '一':
					 color= '#FFE';
					break; 
					case '二':
					 color= '#EFE';
					break; 
					case '三':
					 color= '#FEE';
					break; 
					case '四':
					 color= '#FEF';
					break; 
					case '五':
					 color= '#EEE';
					break;  
					default:
					color = 'FFF';
					
					
				}
				content = '<tr  style=" cursor:pointer; background-color:'+color+'" onclick="shipmentType = \''+typeToken+'\' ;$(\'#select_shopID\').val('+data.availableShipment[key].shopID+');showOrderList(\'staff\',$(\'#selectOrderStatus\').val(),'+data.availableShipment[key].shopID+')">'+
						'<td>'+data.availableShipment[key].shipOut+'</td>'+
						'<td>'+data.availableShipment[key].name+'</td>'+
						'<td>'+type+'</td>'+
						'<td>'+data.availableShipment[key].totalPrice+'</td>'+
						'<td id="'+typeToken+'_shipping_'+data.availableShipment[key].shopID+'"></td>'+
						'</tr>';
						
				var totalPrice = data.availableShipment[key].totalPrice	;
				var shippingFee = data.availableShipment[key].shippingFee	;
				if(data.availableShipment[key].cashType==1)
				{
				
					
						$('#availableTable').append(content);
						if (data.availableShipment[key].type==0)
						{
						
                           d=new Date(); 

                            ww=d.getDay();

                            if (ww==0) wDay="日";
                            if (ww==1) wDay="一";
                            if (ww==2) wDay="二";
                            if (ww==3) wDay="三";
                            if (ww==4) wDay="四";
                            if (ww==5) wDay="五";
                            if (ww==6) wDay="六";

      
                            if(parseInt(data.availableShipment[key].totalPrice)>parseInt(data.availableShipment[key].shippingFee)||data.availableShipment[key].shipmentStatus==1||data.availableShipment[key].shipmentStatus==3)
                            {
                               
                                  if(data.availableShipment[key].shipOut!=''&&data.availableShipment[key].shipOut!=wDay) $('#month_shipping_'+data.availableShipment[key].shopID).html('<img src="/images/pause.jpg" style="width:15px">');
                                      else  $('#month_shipping_'+data.availableShipment[key].shopID).html('<img src="/images/confirm.png">');
                   
                                
                            }
							else 
							{
							
								$('#month_shipping_'+data.availableShipment[key].shopID).html('<img src="/images/delete.png">');
							}
						}
				}
				else $('#availableTable_cash').append(content);
			}
		}
		
	},'json')	
	
	
}






function shipmentCreate(id,type,addressID,shipOutDay)
{
    
       
    
	
	if( needShipping == true)
	{
		
		if(confirm('未達運費指定額，是否加入運費？'))
		{
		var shippingFee = {'rowID':41406,'orderNum':8586,'productNum':'PHA99','ZHName':'運費','ENGName':'Freight','language':'其他','price':100,'sellPrice':100,'purchaseCount':100,'nonJoinPurchaceDiscount':0,'avgCost' : 100,'purchasePrice':100,'eachCost':100,'buyNum':1,'sellNum':1,'num':1,'subtotal':100,'productComment':''}

		 $('#monthShipping_ok').append(shipmentRow(shippingFee,'staff','訂單已完成',monthShippingOkIndex++));	
		}
		
	}
	 	
	$('.product_check').each(
		function()
		{
			
			if($(this).is(":checked")) $('.check_row_'+$(this).val()).val(1);
			else $('.check_row_'+$(this).val()).val(0);
			
		}
	
	)
	
	shippingWrongMsg = '';
	if(shippingWrong!='')
	{
		wrongProduct = shippingWrong.split('-');
		var num = wrongProduct.length;
		for(key in wrongProduct)
		{
			wrongProductInfo = wrongProduct[key].split(',');
			if( $('.check_row_'+wrongProductInfo[0]).val()==1) shippingWrongMsg+=wrongProductInfo[1]+'\n';
			
		}
		
		
		if(shippingWrongMsg!='')
		if(confirm('以下物品數量不足\n'+shippingWrongMsg+'是否仍要出貨?'))
		{
			warehorse=prompt("請輸入倉管密碼:");
			if(warehorse!=pw)
			{
				alert('密碼錯誤，請聯絡倉管出貨');
				 return;
			}
			
			
		}
		else return;
			
		
	}
	
	
	
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/shipment_create",
	   data: 'shopID='+$('#select_shopID').val()+'&create_type='+type+'&addressID='+addressID+'&'+$("#"+id).serialize(),
	   success: function(data){
		   
		  if(data.result==true)
		  {
			  showShipment(data.shipmentID,'staff')
			  
		  }
		  else{alert('沒有可供出貨的商品')};
	   }
	   },'json');



}

function shopShipmentCreate(id,type,addressID)
{
	
	if( needShipping == true || dirNeedShipping==true)
	{
		
		var shippingFee = {'rowID':41406,'orderNum':8586,'productNum':'PHA99','ZHName':'運費','ENGName':'Freight','language':'其他','price':100,'sellPrice':100,'purchaseCount':100,'nonJoinPurchaceDiscount':0,'avgCost' : 100,'purchasePrice':100,'eachCost':100,'buyNum':1,'sellNum':1,'num':1,'subtotal':100,'productComment':''}

		 $('#monthShipping_ok').append(shipmentRow(shippingFee,'staff','訂單已完成',monthShippingOkIndex++));	
		
	}
	
	$('.product_check').each(
		function()
		{
			
			if($(this).is(":checked")) $('.check_row_'+$(this).val()).val(1);
			else $('.check_row_'+$(this).val()).val(0);
			
		}
	
	)
	
		
	
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/shipment_create",
	   data: 'shopID='+$('#shopID').val()+'&create_type='+type+'&addressID='+addressID+'&'+$("#"+id).serialize(),
	   success: function(data){
		   
		  if(data.result==true)
		  {
			  
			  processOut(data.shipmentID,addressID)
		  }
		  else{alert('沒有可供出貨的商品')};
	   }
	   },'json');



}


function processOut(shipmentID,addressID)
{
	
	$.post('/order/shipment_update',{shipmentID:shipmentID,shipment_type:0,shipment_status:4,shipmentComment:'',shipmentCode:'',addressID:addressID},function(data)
	{
		
		if(data.result==true)
		{
				
			showShipment(shipmentID,'watch')
			shipmentView('shipment');
			
		}
		
	},'json')
		
	
	
}




function changePrice(shipmentID)
{
	$('#popUpBoxEnter').hide();
	$('.purchasePrice').each(
	function()
	{
		a = this.id.split('_');
		$('#'+this.id).html('<input type="text" id="changePrice_'+a[1]+'" value="'+$('#'+this.id).html()+'" onchange="chagePriceSend(\''+a[1]+'\','+shipmentID+')">');	
		
		
	}
	
	)
	
	
}

function chagePriceSend(id,shipmentID)
{
		
	$.post('/order/change_row_price',{'rowID':id,'purchasePrice':$('#changePrice_'+id).val(),'shipmentID':shipmentID},function(data)
	{
		
		if(data.result==true)
		{
			changePrice = parseInt($('#changePrice_'+id).val());
			price = parseInt($('#price_'+id).html());
			num = parseInt($('#OSBANum_'+id).html());
				
			$('#subtotal_'+id).html(changePrice * num)
			$('#purchaseCount_'+id).html(Math.round(changePrice*100/price,0)+'%')
		
			changetotal()
		}
		
	},'json')
	
	
	
}

function changetotal()
{
 
	$.post('/order/change_total',{shipmentID:$('#shipmentID').val()},function(data)
	{
		if(data.result==true)
		{
			$('#order_total').html('總價：'+data.total);	
			
		}
		
		
	},'json');
	
	
} 


function showShipment(shipmentID,type)
{
	
	$.post('/order/get_shipment',{shipmentID:shipmentID},function(data)
	{
		
		if(data.result==true)
		{
			orderNumber = 0;
			var	 content ='<div id="orderAppendQuery"></div>';
			content +='<input type="hidden" id="shopDiscount" value="'+data.order.discount+'">'+
						'<input type="hidden" id="shopID" value="'+data.order.shopID+'">'+	
					'<form id="shipmentForm">'+
						'<input type="hidden" id="shipmentID" name="shipmentID" value="'+shipmentID+'">'+
						'<div style="text-align:left">出貨單號：s'+data.order.shippingNum+' 出貨時間：'+data.order.shippingTime+' 訂貨店家：'+data.order.name+' 訂單狀態:'+data.order.status+' 統一編號：'+data.order.thisComID
						
			if(data.order.type==0)content+=	' 訂單類別：買斷';
			else if(data.order.type==1) content+=	' 訂單類別：寄賣';
			else content+=	' 訂單類別：買斷(調貨)';
			 if(data.order.shipmentCode!='')content+=' 查件代號：'+data.order.shipmentCode;
			 if(data.order.charge!='')content+=' 收款日期：'+data.order.charge;
			   if(data.order.note!='')content+=' 備查：'+data.order.note;
			content+=' 裝箱時間：'+data.order.boxTime;
           
            content+=' 完成時間：'+data.order.finishTime;
             content+=' 箱數：'+data.order.boxNum;
			content+='</div>';
			
			content +='<div id="orderTableContent"></div>';
		
			if(type!='staff' ||data.order.status=='已到貨'||data.order.status=='已送達物流' )
			{
				
					content +='收件人:' +data.order.receiver;
					content +='<div style="clear:both"></div>';
					content +='地址： '+data.order.address;;;
					content +='<div style="clear:both"></div>';
					content +='電話： '+data.order.phone;;
                if(data.order.thisComID) content+='客戶統編：'+data.order.thisComID;
				if(data.order.shipmentComment!=0&&data.order.shipmentComment!='')
				{	
				content +='<h2>訂單備註：</h2><div style=" background-color:#CCC">'+
								data.order.shipmentComment+
								'</div>';
										
				}
			}
			else
			{
				content += '<select  id="order_receiver" name="receiverID"  onchange="changeReceiver()">'+
			   				'<option value="0">選擇收件人及地址</option>';
				for(each in data.address)
				{
					if(each==0) addressID = data.address[each].id;
					content+= '<option value="'+data.address[each].id+'">'+data.address[each].receiver+'</option>';
				}
				content += '</select>';	
				content +='收件人： <input type="text" name="receiver" id="receiver"></input>';
				content +='<div style="clear:both"></div>';
				content +='地址： <input type="text" name="address" id="address" style=" width:400px"></input>';;
				content +='<div style="clear:both"></div>';
				content +='電話： <input type="text" name="phone" id="phone"></input>';;
                content +='客戶統編： <input type="text" name="comID" id="comID"></input>';;
                content +='email： <input type="text" name="email" id="email"></input>';;
				content +='<div style="clear:both"></div>';
		      
               content +='客戶載具：<select name="CarrierType" id="CarrierType">'+
                '<option value="">無</option>'+
                '<option value="3J0002">手機條碼</option>'+
                '<option value="CQ0001">自然人憑證</option>'+
                '</select> <input type="text" class="big_text" name="CarrierId1" id="CarrierId1" placeholder="載具條碼無則空白" >';
           content += '捐贈代碼：<input type="text" class="big_text" name="NPOBAN" id="NPOBAN" placeholder="無則空白" >';
               
			content +='<div style="clear:both"></div>';
				content +='<h2>訂單備註：</h2><div style=" background-color:#CCC">'+
								'<textarea name="shipmentComment" style=" width:1000px; height:100px">'+data.order.shipmentComment+'</textarea>'+
								'</div>';
			}
			if(type=="staff"&&data.order.status!="已到貨"&&data.order.status!="已送達物流")
			{
   			content +='訂單類別：<select name="shipment_type" id="shipment_type">'+
			   				'<option value="0">買斷</option>'+
							'<option value="1">寄賣</option>'+
						'</select>';
             
   			content +='訂單狀態：<select name="shipment_status" id="shipment_status">'+
							'<option value="4">訂單處理完成</option>'+
							'<option value="2">已送達物流</option>'+
						'</select>';
			
                content+='物流單號：<input type="text" name="shipmentCode" value="'+data.order.shipmentCode+'"><br/>'
                   content +='收款方式：<select name="shipment_payway" id="shipment_payway">'+
                                '<option value="0">款到發貨</option>'+
                                '<option value="1">貨到付款</option>'+
                            '</select>';  
              content +='到貨日期：<select name="shipment_assignDate" id="shipment_assignDate">'+
                                '<option value="0000-00-00">預設</option>';
                    for(key in data.arriveDate)
                        {
                                
                            content += '<option value="'+data.arriveDate[key]+'">'+data.arriveDate[key]+'</option>';
                            
                        }
            content+='<option value="">貨到付款</option>'+
                            '</select>';
        
        
            content +='到貨時間：<select name="shipment_assignT" id="shipment_assignT">'+
                               '<option selected="selected" value="0">預設</option>'+
                                '<option value="1">09~13</option>'+
                                '<option value="2">13~17</option>'+
                                '<option value="3">17~20</option>'+
                            '</select>';  
       
                
              content +='發票內容：<select name="shipment_invoice" id="shipment_invoice">'+
                                '<option value="0">簡易</option>'+
                                '<option value="1">明細</option>'+
                            '</select>';           
                
					
			}
			content +='</form>';
		//	if(type=="shipment"&&data.order.statusID!=2&&data.order.statusID!=3)content +='<input type="button" class="big_button" value="列印出貨單" onclick="order_print()">';
		//	else 
		//	{
				content +='<label><input type="checkbox" id="shipmentShowPrice"  value="1"';
				if(data.order.showPrice==1) content+='checked="checked"';
				content +='/>顯示價錢</label>';
                content +='<input type="button" class="big_button" value="列印裝箱單" onclick="$(\'#popUpBoxEnter\').trigger(\'click\');printShipment('+data.order.id+',0,\'boxin\');">';
            
				var str ='';
			
				content +='<input type="hidden" id="invoiceByShip" value="'+data.order.invoiceByShip+'">';
				content +='<input type="button" class="big_button" value="列印出貨單" onclick="$(\'#popUpBoxEnter\').trigger(\'click\');printShipment('+data.order.id+',0,\'shipment\');">';
				content +='<input type="button" class="big_button" value="按訂單列印出貨單" onclick="$(\'#popUpBoxEnter\').trigger(\'click\');printShipment('+data.order.id+',1,\'shipment\');">';
				
		//	}
			
			if(type=='staff')
			{
				content +='<input type="button" class="big_button" value="修改價錢" onclick="changePrice('+shipmentID+')">';
			    content+='<input type="button" value="開立電子發票" class="big_button" onclick="confirmOrderInvoice('+data.order.id+')">';
				
				 fun = 'shipmentUpdate';
				 content +='<div class="divider"></div><div id="invoiceBlock"></div>';
				 content+='<table>';
				 content+='<tr><td>營業額：</td><td>'+data.order.total+'</td></tr>';
				 content+='<tr><td>毛利：</td><td>'+data.order.profit+'<td></tr>';
				 content+='<tr><td>物流：</td><td>'+data.order.shipmentFee+'</td></tr>';
				 if(data.order.shopID==1039)
				 {
					 give=Math.round(data.order.total*0.15,0)
					 content+='<tr><td>奉獻：</td><td>'+give+'</td></tr>';
				 }
				 else give = 0;
				 
				 var pretotal = Math.round(data.order.total/1.05,0);
				 content+='<tr><td>稅前營業額：</td><td>'+pretotal+'</td></tr>';
				 content+='<tr><td>稅額：</td><td>'+(data.order.total-pretotal)+'</td></tr>';
				 content+='<tr><td>稅後淨利：</td><td>'+(data.order.profit-give-data.order.shipmentFee-(data.order.total-pretotal))+'</td></tr>';
				 content+='</table>';
				 
			}
			else fun = 'closePopUpBox';

				
				
				 openPopUpBox(content,1100,300,fun);
				
				 $('#order_receiver').val(data.order.addressID);
				 changeReceiver();
				 if(type=='staff') $('#orderTableContent').html(orderTable(data,'shipmentStaff',status));
				 else  $('#orderTableContent').html(orderTable(data,'shipment',status));
				 $('#shipment_type').val(data.order.type);
                $('#shipment_payway').val(data.order.payway);
                $('#shipment_assignDate').val(data.order.assignDate);
                $('#shipment_assignT').val(data.order.assignT);
                $('#shipment_invoice').val(data.order.invoiceDetail);
            
				 $('#shipment_status').val(data.order.statusID);
				height =parseInt($('#orderTable').css('height').substr(0,$('#orderTable').css('height').length-2))
				popUpBoxHeight(height+600);
				
				//$('#popUpBoxCancel').attr('onclick','shipment_delete()');
				
			//countTotal(1);
		}
		
	},'json')	
		
	
	
	
}


function checkShipment()
{
	shippingNum = $('#shippingNum').val();
	$.post('/order/get_shipment',{shippingNum:shippingNum},function(data)
	{
		
		  
		  if(data.result==true)
		{
            $('#shippingNum').val('');
			$('#nowShip').html('s'+shippingNum);
        
              account='<input type="hidden" id="shipmentID" value="'+data.order.id+'"><div  id="btnContent" style="float:left; margin-left:20px;font-weight:bold; width:500px"></div>';
              $('#orderInf').html(account);
            
            if(data.order.shopID==666)
                {
                    $('#btnContent').html( '<div style="display:none;background-color:#FF0000" id="btnContent2"><div style="font-size:14pt;padding:1px;">宅配</div></div>'+
                             '<div style="display:none;background-color:#00FFFF" " id="btnContent5"><div style="font-size:14pt;;padding:1px;">海外</div></div>'+            '<div style="display:none;background-color:#77FF00" " id="btnContent3"><div style="font-size:14pt;padding:1px;">7-11</div></div>'+
                            '<div style="display:none;background-color:#00FFFF" " id="btnContent4"><div style="font-size:14pt;;padding:1px;">三大超</div></div>'
                           );
                    $('#btnContent').css('float','none');  
                    $('#btnContent').css('width','1000px');  
                    
                  
                    var t = 0 ;
                    
                    for(key in data.product)
                        {
                            
                            var btn = '<input type="button" onclick="checkECOrder('+data.product[key].shipmentID+','+data.product[key].orderID+')" class="ECBTN'+data.product[key].transportID+' big_button"  style="background-image: url(/images/index_purplebutton_02.png);" id="ECBTN_'+data.product[key].orderID+'" value="" />'+
                                '<input type="hidden" class="ECCheck" value="0" id="ECCheck_'+data.product[key].orderID+'" >';
                            
                              insertKey = false;
                            if($('#ECBTN_'+data.product[key].orderID).val()!==undefined)  insertKey = true;
                            else
                            {
                              
                              
                                $('.ECBTN'+data.product[key].transportID).each(function(){
                                 
                                    var id = parseInt(this.id.substr(6));
                                   
                                    if(id > data.product[key].orderID)
                                    {
                                 
                                        if(!insertKey)
                                        {
                                            $('#ECBTN_'+id).before(btn);
                                            insertKey = true;
                                            getECOrder(data.product[key].orderID);
                                        }
 
                                    }
                                    
                                })
                                
                                 if(!insertKey) 
                                 {
 
                                     $('#btnContent'+data.product[key].transportID).show().append(btn);
                                     getECOrder(data.product[key].orderID);
                                 }

                                
                                
                            }
                           
                            
                            
                            
                        }
                    
    
                }
            else{
                          $('#btnContent').append(
								'出貨單號： s'+data.order.shippingNum+'<br/>'+
								'列印時間： '+data.order.shippingTime+'<br/>'+
								'客戶名稱：'+data.order.name+'<br/>'+
								'統一編號 ：'+data.order.comID+'<br/>'+
								'收件人 ：'+data.order.receiver+'<br/>'+
								'客戶電話： '+data.order.phone+'<br/>'+
								'寄送地址： '+data.order.address+'<br/>')
                
                
                
            }

              account=  '<div style="float:left"><span id="finBtnCont"><input id="finBtn" type="button" style="width:150px;height:100px;font-size:24pt" value="完成出貨" onclick="orderBoxFinish('+data.order.id+',0)"></span>'+
                '<input type="button" style="margin-left:20px; width:150px;height:100px;;font-size:24pt" value="清除重來" onclick="$(\'#shippingNum\').val($(\'#nowShip\').html().substr(1));checkShipment()">'+
                  '<span id="ECFinBtn"></div>'+
            '</div>'+
                '<div style="clear:both"></div>';
            $('#orderInf').append(account);
           
            checkDetail(data);
            
            
            
            
            
            
            
        
	   }
        else alert('單號錯誤，請重新輸入');
		
		
	},'json')	
		
	
	
	
}

function checkDetail(data)
{
    
      var d = new Date();
                m = d.getMonth()+1;
               var n = d.getFullYear()+'-'+m+'-'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
            
               
                 if(data.order.boxTime!='0000-00-00 00:00:00') 
                {

                    $('#boxTime').html(data.order.boxTime);
                }
                else $('#boxTime').html(n);
         content ='<div id="orderAppendQuery"></div>';
            
            
			content +='<input type="hidden" id="shopDiscount" value="'+data.order.discount+'">'+
					 '<form id="orderForm">'+
					 
						'<input type="hidden" name="orderID" value="'+data.order.id+'">'+
						'<div style="width:1000px;float:left;">'+
	
						'</div>'+
						'<div style="clear:both"></div>'+
						'<div style="text-align:left;">'
			 content+=orderTable(data,'orderChk','訂單已送達');//

			content +='</form>';

			if(data.order.shipmentComment!=0&&data.order.shipmentComment!='')
			{
			content +='<h2>訂單備註：</h2><div>'+
							data.order.shipmentComment+
							'</div>';
			}						
			$('#orderView').html(content)		;
            
            
            $('#orderChk_findBarcode').focus();
			//countTotal(1);
            $('#orderErr').html('<h1>錯誤商品清單：</h1>');
            $('#orderErr').hide();
            $('#finBtn').css('background-color','');
            if(data.order.boxTime!='0000-00-00 00:00:00') sameAll();
    
    
    
}



function checkECOrder(shipmentID,orderID)
{
    
    $.post('/order/get_order_in_shipment',{shipmentID:shipmentID,orderID:orderID},function(data) {
        
        if(data.result==true)
            {
               
                
                $('#finBtnCont').html('<input id="finBtn" type="button" style="width:150px;height:100px;font-size:24pt" value="完成出貨" onclick="orderBoxFinish('+orderID+',1)">');
                ecCheckAll();
                checkDetail(data);
                
                
                
                
            }
        
        
        
    },'json')
    
    
}

function getECOrder(orderID)
{
    
    $.post('/order/get_ec_order',{orderID:orderID},function(data)
    {
        
        if(data.result==true)
            {
                $('#ECBTN_'+orderID).val(data.order.platformName+data.order.ECOrderNum);
                
                
                
          

                if(data.order.boxTime!='0000-00-00 00:00:00') 
                {
                     $('#ECCheck_'+orderID).val(1);

                    $('#ECBTN_'+orderID).css('color','black');
                }

                ecCheckAll();
                
            }
        
        
    },'json')
    
    
    
}

function orderBoxSendOut(id,num,type)
{
    closePopUpBox();
  $.post('/order/order_box_finish',{shipmentID:id,num:num,boxTime:$('#boxTime').html(),type:type},function(data)
          {
        if(data.result==true) 
        {
            confirm('完成出貨！！');
            
            $('#ECCheck_'+id).val(1);
            if(type!=1)location.reload();
            else
            {
                 ecCheckAll();
                $('#ECBTN_'+id).css('color','black');
               
            }
        }
            
        
        
        
    },'json') 
    
}


function orderBoxFinish(id,type)
{
  
    if(!checkAll())
        {
            alert('請檢查出貨內容是否正確')
            
        }
    else
    {
        
        content='<h1>請輸入出貨件數</h1>'+
                '<input type="button" class="big_button" value="1" style="width:100px;font-size:72pt" onclick="orderBoxSendOut('+id+',1,'+type+')">'+
             '<input type="button" class="big_button" value="2" style="width:80px;font-size:60pt" onclick="orderBoxSendOut('+id+',2,'+type+')">'+
         '<input type="button" class="big_button" value="3" style="width:80px;font-size:60pt" onclick="orderBoxSendOut('+id+',3,'+type+')">'+
         '<input type="button" class="big_button" value="4" style="width:50px;font-size:36pt" onclick="orderBoxSendOut('+id+',4,'+type+')">'+
         '<input type="button" class="big_button" value="5" style="width:50px;font-size:36pt" onclick="orderBoxSendOut('+id+',5,'+type+')"><br/>'+
             '其他數量：<input type="text" id="otherNum"><input type="button" class="big_button" value="送出" style="font-size:18pt" onclick="orderBoxSendOut('+id+',$(\'#otherNum\').val(),'+type+')">';
        openPopUpBox(content,600,320,'closePopUpBox');
        
        $('#popUpBoxEnter').hide();
        
        
        
    }
    
    
    
    
    
}

function membercheck(memberLevel,orderID,rowID)
{
    
    var content=
			'<h1>會員快速新增</h1>'+
           
            '<input type="hidden" value="0" id="memberChk">'+
			'<div>編號：<input type="number" id="new_memberID"  class="new_member big_text" placeholder="0000000" onchange="memberQuickChk()" />'+
           
            '</div>'+
           
            '<div style="color:red;display:none"  id="memberWrong">＊＊會員編號錯誤＊＊</div>'+
			                 '<input type="hidden" id="member_orderID" name="orderID">'+
                '<input type="hidden" id="member_rowID" name="rowID">'+
                 '<input type="hidden" id="member_level" name="member_level">';
               
			 openPopUpBox(content,400,330,'quickNewMember');	
	       $('#member_level').val(memberLevel);
    $('#member_orderID').val(orderID);
    
     $('#member_rowID').val(rowID);
    
}

function memberQuickChk()
{
    if($('#new_memberID').val()>99999)
    {
        
        $('#memberChk').val(0);
         $.post('/order/member_quick_chk',{memberID: $('#new_memberID').val()
},function(data)
          {
            
            if(data.result==true)
            {
                $('#memberChk').val(1);
                $('#memberWrong').html('會員編號正確').show();
                $('#memberWrong').css('color','green');    
            }
            else
            {
                $('#memberChk').val(0);
                $('#memberWrong').html('會員編號錯誤').show();
                $('#memberWrong').css('color','red');  
            }
        
      },'json')
        
    }
    else 
    {
        $('#memberChk').val(1);
        $('#memberWrong').hide();
    }
   
    
    
    
}


function quickNewMember()
{
	if($('#memberChk').val()==0)
	{
		alert('會員編號錯誤喔');
		$('#popUpBoxEnter').show(	);
		return;	
	} 
        
 var rowID = $('#member_rowID').val();
 	var memberID = $('#new_memberID').val();
	$.post('/order/quick_new_member',{memberID:$('#new_memberID').val(),level:$('#member_level').val(),orderID:  $('#member_orderID').val()},
           function(data){
	
	
		
			if(data.result!=0)
			{
                closePopUpBox();
				alert('會員編號：'+memberID+'資料已經新增,請記得放入抵用券')
				
				$('#box_'+rowID).html(1);
                checkAll();
				
			}
			else 
			{
                alert(data.wrong);
				
				$('#popUpBoxEnter').show();

			}
			if (data.result==-1)
			{
                closePopUpBox();
				alert('會員資料背景作業，結帳完成後會上傳。');
				
			}
			
		
		
		},'json')	
}
function handCheck(rowID)
{
    
    v = prompt('請輸入數量');
    
    $('#box_'+rowID).html(v);
    checkAll();
    
}


function sameAll()
{
    
     $('.boxcolumn').each(function()
        {
        
         var rowID = this.id.substr(4);
 
         
         var realNum =  parseInt($('#OSBANum_'+rowID).html());
        $('#box_'+rowID).html(realNum)
          
          
        
        
    })
    checkAll()
}
function ecCheckAll()
{
    
   var key = true;
    $('.ECCheck').each(function()
                      {
          var rowID = this.id.substr(8);
     
            if($(this).val()!=1) key = false;
     
        
        
        
    })  
    if(key==true)$('#ECFinBtn').html('<input  type="button" style="width:350px;height:100px;background-color:red;font-size:24pt" value="電商訂單全部完成出貨" onclick="orderBoxSendOut('+$('#shipmentID').val()+',0,0)"></span>')
}



function checkAll()
{
     $key = true;
   
    $('.boxcolumn').each(function()
        {
        
         var rowID = this.id.substr(4);
 
         r = parseInt($('#box_'+rowID).html());
         var realNum =  parseInt($('#OSBANum_'+rowID).html());
          if(r!=realNum) 
          {
              $key = false;
                $('#count_'+rowID).css('background-color','red');
          }
         else{
             $('#count_'+rowID).css('background-color','white');
             
         }
          
        
        
    })
    if($('#orderErr').html()!='<h1>錯誤商品清單：</h1>')$key = false;
    
    
    
    
    if($key)
    {
        
       
        $('#finBtn').css('background-color','red');
        return true;
    }
    else 
    {
         $('#finBtn').css('background-color','');
         return false;
    }
    
    
    
}





orderErrID = 0;
function orderChkTable(data,num)
{
    if(!num) num = 1;
    var alreadyNum = 0;
    var search = false;
    $('.box_'+ data.productID).each(function(){
        search = true;
        var rowID = this.id.substr(4);
 
        r = parseInt($('#box_'+rowID).html());
        
        if(isNaN(r)) r = 0 ; 
        r += num;
        
        var realNum =  parseInt($('#OSBANum_'+rowID).html());
        
        if(r >  realNum) 
        {
            num = r-realNum;
            r = realNum
        }
        else num = 0;
        $('#box_'+rowID).html(r);
        
        
    })    
    
    if(search==false ||num>0)
    {
        playSound(1)
      $('#orderErr').show();
        orderErrID++;
      content= '<div id="orderErr_'+orderErrID+'"><input type="button" value="刪除" onclick="deleteChkErr('+orderErrID+')" />'+data.ZHName+'('+data.ENGName+')'+data.language+data.productNum+'</div>';
      $('#orderErr').append(content);
    }
    else playSound(0);
   $('#orderChk_findBarcode').focus();
    checkAll();

    
}

function deleteChkErr(id)
{
    $('#orderErr_'+id).detach();
    checkAll();
}

function playSound(i){
		
    var music=new Array("http://shipment.phantasia.com.tw/upload/right.mp3","http://shipment.phantasia.com.tw/upload/wrong.mp3","http://shipment.phantasia.com.tw/upload/select.mp3");
    
    var e=  '<embed width=0 height=0 src="'+music[i]+'" autostart="true"></embed>';
    $('#sound').html(e)
	
   
    //alert(e);
	}

function invoicePrepare(shopID,total,shipmentNum,shipmentID,comID)
{

	$key =  true;
	if($('#invoiceByShip').val()!=1)
	{
		if(confirm('此店家不需隨貨附發票，仍要印出發票？'))
		{
			
			$key =  true;
		}
		else{ return;}
		
	}
	if($key)
	$.post('/system/get_shop_status',{shopID:shopID},function(data)
	{


		if(data.result==true)
		{
			
            if($('#shipment_invoice').val()==1)
            {
                var item  = '';
                $('.orderRow').each(function()
                {
                    
                    var a = this.id.split('_');
                  
                   item  += 
                	'<input type="hidden" id="invoice_item" name="item[]" value="'+$('#ZHName_'+ a[1]).html().substr(0,30)+'">'+
					'<input type="hidden" id="invoice_uniPrice" name="uniPrice[]" value="'+$('#purchasePrice_'+ a[1]).html()+'">'+
                    '<input type="hidden" id="invoice_num" name="num[]" value="'+$('#OSBANum_'+ a[1]).html()+'">';
                    
                    
                })
                
            }
            else  
            {
                var item  = 
                	'<input type="hidden" id="invoice_item" name="item[]" value="桌遊一批">'+
					'<input type="hidden" id="invoice_uniPrice" name="uniPrice[]" value="'+total+'">'+
                    '<input type="hidden" id="invoice_num" name="num[]" value="1">';
            }
            
            
            
            
					$('#invoiceBlock').html(
				'<form id="invoice_form" action="http://localhost/accounting/invoice_api">'+
					'<input type="hidden" id="invoice_title" name="title" >'+
					'<input type="hidden" id="invoice_invoiceCode" name="invoiceCode" >'+
					item+
					'<input type="hidden" id="invoice_comment"  name="comment">'+
					'<input type="hidden" id="invoice_shipmentID"  name="shipmentID">'+
				'</form>'

				)

        if(comID!='')$('#invoice_invoiceCode').val(comID);
		if(comID=='' && data.shopData.invoiceType==3)	
		{
		
			$('#invoice_invoiceCode').val(data.shopData.comID);
			
		}
            /*
		$('#invoice_item').val('桌遊一批');
		$('#invoice_num').val(1);	
		$('#invoice_uniPrice').val(total);
        */
		$('#invoice_comment').val('S'+shipmentNum+' '+data.shopData.name);
		$('#invoice_shipmentID').val(shipmentID);	
			queryStr = $('#invoice_form').serialize();	
		
			$('#invoiceBlock').html('<iframe style="width:600px;height:100px;" src="http://localhost/accounting/invoice_api?'+queryStr+'"></iframe>');
		}
		
	
	

	},'json')
	
	
}


function printShipment(orderID,type,showType)
{
		if($('#shipmentShowPrice').is(':checked'))price = 1;
			else price = 0;

	
	window.open('/order/print_out/'+showType+'/'+orderID+'/'+type+'/'+price, '_blank')
	
	
}






var shipmentListOffset= 0;
function showShipmentList(type,arive)
{

	if(arive==0||arive==6)
	{
		showOrderList(type,arive);
	
		return;
	}
    if(arive==-1)
	{
       
		pointFrame();
	
		return;
	}
    if(arive==-2)
    {
        getOtherShop();
        return;
	
        
    }
     if(arive==-3)
    {
        getPlatFormOrder($('#ECplatform').val(),$('#ECStatus').val())
        return;
	
        
    }
    
    
	//arive = $('#selectOrderStatus').val();
	$('#productQuery').html('');
	$('#productSelect').detach();

	//$('#product_list').html(orderListTable(type,arive));
	shipmentListOffset= 0;

	getShipmentList(shipmentListOffset,15,type,arive);

		
}

var focusID = 0;
function showEditMsg(id,type)
{
    if(type=='EC')editEcOrder(focusID)
	else editShipment(focusID)
$('#editMsgIn').detach();
	$('body').append('<div id="editMsgIn" style=" margin-left:500px; color:red ;position:absolute; z-index:2; background-color:white;'+
	 'top:'+($(document).scrollTop()+200)+'px;left:200px; ";>請按一下旁邊即可儲存</div>');
		focusID = id;
		

}


function editShipment(id)
{
	if(id==0) return;
	$.post('/order/edit_shipment',{id:id,shipmentCode:$('#shipmentCode_'+id).val(),charge:$('#charge_'+id).val(),note:$('#note_'+id).val()},
	function(data)
	{
		if(data.result==true)	$('#editMsgIn').html($('#orderNum_'+id).html()+'已儲存');
		else $('#editMsgIn').html('儲存失敗，請洽管理員');
		setTimeout(function(){$('#editMsgIn').detach();},2000);
	},'json');
	
	
}

function editEcOrder(id)
{
    
   if(id==0) return;
	$.post('/order/edit_ec_order',{id:id,shipmentCode:$('#shipmentCode_'+id).val(),charge:$('#charge_'+id).val(),note:$('#note_'+id).val()},
	function(data)
	{
		if(data.result==true)	$('#editMsgIn').html($('#ECOrderNum_'+id).html()+'已儲存');
		else $('#editMsgIn').html('儲存失敗，請洽管理員');
		setTimeout(function(){$('#editMsgIn').detach();},2000);
	},'json'); 
    
    
}



function editInvoice()
{
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/edit_invoice",
	   data: $("#invoiceForm").serialize(),
	   success: function(data){
		   
		   if(data.result==true)
		   {
			  
			   checkInvoice($('#invoiceID').val(),$('#invoiceTotal').val(),$('#invoiceShopID').val())
				closePopUpBox();  
		   }
	   }
	   });

	
	
}

function getInvoice(id,total,shopID,type)
{
    if(type==='EC') url= 'get_ec_invoice';
    else url = 'get_invoice';
	$.post('/order/'+url,{id:id},function(data)
	{
		if(data.result==true)
		{
            var showInvoice = '';t
			var content='<h1>發票清單</h1><form id="invoiceForm">'+
            '<input type="hidden" name="type" id="type" value="'+type+'">'+    
			'<input type="hidden" name="id" id="invoiceID" value="'+id+'">'+
			'<input type="hidden" name="total" id="invoiceTotal" value="'+total+'">'+
			'<table border="1">';
				content+='<tr>';
				content+='<td>發票號碼</td>';
				content+='<td>日期</td>';
				content+='<td>價錢</td>';	
				content+='</tr>';
			var i=1; var invKey = false
			for(key in data.invoice)
			{
				content+='<tr>';
				content+='<td><input type="text" name="invoice_'+i+'" value="'+data.invoice[key].invoice+'"></td>';
				content+='<td><input type="text" name="iDate_'+i+'" id="iDate_'+i+'" value="'+data.invoice[key].date+'"></td>';
				content+='<td><input type="text" name="iPrice_'+i+'"  value="'+data.invoice[key].price+'"></td>';	
				content+='</tr>';
             
                if(data.invoice[key].RandomNumber !==null)
                    {
                showInvoice+= '<div  style="height:19cm;width:230px;" ><iframe id="in_'+data.invoice[key].invoice+'" style="overflow: hidden;;width:230px;height:18cm"  src="https://mart.phantasia.tw/order/show_invoice/'+data.invoice[key].invoice+'/'+data.invoice[key].code+'"></iframe><div style="clear:both"></div><a target="_blank" href="https://mart.phantasia.tw/order/show_invoice/'+data.invoice[key].invoice+'/'+data.invoice[key].code+'">發票列印</a><div style="clear:both"></div></div>'; 
                       
                    }
                
				i++;
			}
            if(i==1) invKey =true;
           
			for(i;i<=5;i++)
			{
				content+='<tr>';
				content+='<td><input type="text" name="invoice_'+i+'"></td>';	
				content+='<td><input type="text" name="iDate_'+i+'" id="iDate_'+i+'"></td>';	
				content+='<td><input type="text" name="iPrice_'+i+'"></td>';	
				content+='</tr>';
				
			}
			
			content+='</table></form><input type="hidden" id="invoiceShopID" value="shopID">';
            if(invKey)content+='<input type="button" value="開立電子發票" class="big_button" onclick="confirmOrderInvoice('+id+',\''+type+'\')">';
			openPopUpBox(content+showInvoice,600,320,'editInvoice');
			for(i=1;i<=10;i++)
			{
			var dates = $( "#iDate_"+i ).datepicker({
							dateFormat: 'yy-mm-dd' ,
							yearRange: '1930',
							monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
							defaultDate:"-2m",
							changeMonth: true,
							numberOfMonths: 3,
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates.not( this ).datepicker( "option", option, date );
							}
						});		  	
			}
		}
		
	},'json')
	
	
	
}

function confirmOrderInvoice(shipmentID,type)
{
    if(type=="EC")
    {
       var url ='get_ec_order';
       var shopname = '瘋桌遊購物商城';
    }
    else
    {
        var url ='get_shipment';
        var shopname = '瘋桌遊產品部';
    }
    $.post('/order/'+url,{shipmentID:shipmentID},function(data){
                    if(data.result==true)
                        {
                            
                            content = '<h1>電子發票開立</h1><form id="invoice_form"><table style="width:680px">';
                             content += '<tr><td>出貨單位：</td><td><input type="text" class="big_text" name="shopName" value="'+shopname+'" ></td></tr>';
                            content += '<tr><td>抬頭：</td><td><input type="text" class="big_text" name="title" ></td>';
                            content += '<td> 統一編號：</td><td><input type="text" class="big_text" name="invoiceCode" id="invoice_invoiceCode" ></td></tr>';
                            content += ' <tr><td>載具：<select name="CarrierType" id="invoice_CarrierType">'+
                                        '<option value="">無</option>'+
                                        '<option value="3J0002">手機條碼</option>'+
                                        '<option value="CQ0001">自然人憑證</option>'+
                                        '</select></td><td><input type="text" class="big_text" name="CarrierId1" id="invoice_CarrierId1" placeholder="載具條碼無則空白" ></td>';
                            content += '<td> 捐贈代碼：</td><td><input type="text" class="big_text" name="NPOBAN" id="invoice_NPOBAN"></td></tr>';
                            content +='<tr><td>發票內容：</td><td><select name="shipment_invoice" id="shipment_invoice">'+
                                '<option value="0">簡易</option>'+
                                '<option value="1">明細</option>'+
                            '</select></td>';  
                            content +=  '<td>訂單編號：</td><td><input type="text" class="big_text" name="orderNum" id="invoice_orderNum" value="" ></td></tr>';
                             
                            content += '<tr>';
                            content += '<td>備註：</td>';
                                content +='<td colspan="3"><input type="text" class="big_text" style="width:400px" id="invoice_comment" name="comment"  ><input type="hidden" id="invoice_shipmentID"  name="shipmentID"></td><input type="hidden" id="invoice_type"  name="invoice_type" value="'+type+'"></td>';
                            content +='</tr>';
                             content += '<tr>';
                            content += '<td>E-mail</td>';
                                content +='<td colspan="3"><input type="text" class="big_text" style="width:400px" id="invoice_email" name="email"  ></td>'
                            content +='</tr>';
                            content +='<tr>';
                             content +='<td colspan="2"></td>';
                                content +='<td>總金額</td>'
                                content +='<td id="invoiceTotal"></td>';
                                content +='</tr>';
                            content	+='</table></form>'
                            openPopUpBox(content,700,300,'shipmentInvoiceSend');
                             
                            if(type=='EC')
                            {
                                $('#invoice_orderNum').val(data.order.ECOrderNum);
                                $('#invoice_invoiceCode').val(data.order.comID);
                                 $('#shipment_invoice').val(1);
                            }
                            else 
                            {
                                $('#invoice_orderNum').val('S'+data.order.shippingNum+' '+data.shopData.name);
                                if(data.order.thisComID!='')$('#invoice_invoiceCode').val(data.order.thisComID);
                                if(data.order.thisComID=='' && data.shopData.invoiceType==3)	
                                {

                                    $('#invoice_invoiceCode').val(data.shopData.comID);

                                }
                                 $('#shipment_invoice').val(data.order.invoiceDetail);
                                
                            }
                            
		                       $('#invoice_shipmentID').val(shipmentID);
                              
                            $('#invoice_CarrierType').val(data.order.CarrierType);
                           
                            $('#invoice_CarrierId1').val(data.order.CarrierId1);
                            $('#invoice_NPOBAN').val(data.order.NPOBAN);
                            $('#invoiceTotal').html(data.total);
                            
                            
                            if(data.order.email!=='') $('#invoice_email').val(data.order.email);
                            else  $('#invoice_email').val(data.shopData.email);
                      
                        }
           },'json')
    
    
    
 
	
    
    
    
    
    
}

function shipmentInvoiceSend()
    {
        
       	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/shipment_invoice_generate",
	   data: $("#invoice_form").serialize(),
	   success: function(data){
           
      
         if(data.result==true)
             {
                 
                 checkInvoice(data.shipmentID,data.total,data.shopID)
                 closePopUpBox();
                 alert('發票已開立');
             }
           
       }
            
        
        
        
        })
        
        
    }




function getShipmentList(offset,num,type,arive,shopID)
{

	shipmentListOffset= offset+num;
	if(offset==-1) transOffset = 0 ; 
	else transOffset = offset;
	if($('#selectOrderType').length>0) orderType = $('#selectOrderType').val();
	else orderType = 2;

    if(!shopID) shopID = $('#select_shopID').val();

	$.post('/order/get_shipment_list',{offset:transOffset,num:num,arive:arive,shopID:shopID,orderType:orderType,fromDate:$('#fromDate').val(),toDate:$('#toDate').val()},function(data)
	{
		
		if(data.result==true)
		{
			if(offset==0)$('#product_list').html(orderListTable('shipment',arive));	
			if(offset==-1)	$('#goingList').html(orderListTable('shipmentWatch',false));
			for( var key in data.shipmentList)
			{
				
	
				var content;
					content ='<tr id="order_'+data.shipmentList[key].shipmentID+'">';
					content+=
						'<td id="orderNum_'+data.shipmentList[key].shipmentID+'">'+
                        '<label><input type="checkBox" name="selectID[]" value="'+data.shipmentList[key].shipmentID+'">'+
                        
                        's'+data.shipmentList[key].shippingNum+'</label></td>'+
						'<td>'+data.shipmentList[key].shippingTime+'</td>'+
						
						'<td id="shopName_'+data.shipmentList[key].shipmentID+'">'+data.shipmentList[key].shopName+'</td>'+
						'<td id="shopName_'+data.shipmentList[key].shipmentID+'">'+data.shipmentList[key].receiver+'</td>';
					if(data.shipmentList[key].type!=1) content+='<td id="orderType_'+data.shipmentList[key].shipmentID+'">買斷</td>';
					else content+='<td id="orderType_'+data.shipmentList[key].shipmentID+'"><span style="color:blue">寄賣</span></td>';
					content+='<td class="shipimet_total">'+data.shipmentList[key].total+'</td>'+
						'<td>'+data.shipmentList[key].shipmentComment+'</td>'+
						'<td id="shopStatus_'+data.shipmentList[key].shipmentID+'">'+data.shipmentList[key].colorStatus+'</td>';
					if($('#myshopID').val()==0)
					{
						if(!$('#report_list').is(':checked'))	
						{
                            if(data.shipmentList[key].payway==1)var payway ='貨到付款';
                            else var payway = '';
                            
                            
                            
					content+=	
			
						'<td><input type="text" style=" width:90px"  id="shipmentCode_'+data.shipmentList[key].shipmentID+'" value="'+data.shipmentList[key].shipmentCode+'"  onblur="editShipment('+data.shipmentList[key].shipmentID+')" onfocus="showEditMsg('+data.shipmentList[key].shipmentID+')" ></td>'+
						'<td>'+payway+'<input type="text" style=" width:90px"  id="charge_'+data.shipmentList[key].shipmentID+'" value="'+data.shipmentList[key].charge+'"  onfocus="showEditMsg('+data.shipmentList[key].shipmentID+')" ></td>'+
						'<td><input type="text" style=" width:90px"  id="note_'+data.shipmentList[key].shipmentID+'" value="'+data.shipmentList[key].note+'"  onfocus="showEditMsg('+data.shipmentList[key].shipmentID+')" onchange="editShipment('+data.shipmentList[key].shipmentID+')"></td>'+
					
						'<td>'+
							'<input type="button" id="invBtn_'+data.shipmentList[key].shipmentID+'" value="發票" onclick="getInvoice('+data.shipmentList[key].shipmentID+','+data.shipmentList[key].total+','+data.shipmentList[key].shopID+')"class="big_button" >'+
						'</td>'+
							'<td>';
							
							if(data.shipmentList[key].status=='訂單處理完成')
							{
								content+='<input type="button" value="送達物流" onclick="shipOut('+data.shipmentList[key].shipmentID+')"class="big_button" >'+
										'</td>'
							}
						;
						}
						else
						{
							 	content+=	
						'<td>'+data.shipmentList[key].shipmentCode+'</td>'+
						'<td>'+data.shipmentList[key].charge+'</td>'+
						'<td>'+data.shipmentList[key].note+'</td>'
						}
							
					}	
					else
					{
						
						content+=	
						'<td>'+data.shipmentList[key].shipmentCode+'</td>';
						
					}	
						
						
					content+=	
						'<td>'+
							'<input type="hidden" id ="ordetType_'+data.shipmentList[key].shipmentID+'" value="'+data.shipmentList[key].type+'">'+	
							'<input type="button" value="查看" onclick="showShipment('+data.shipmentList[key].shipmentID+',\''+type+'\')"class="big_button" >'+
						'</td>';
						
				if(type=='staff')
                {
                    content+='<td><input type="button" id="delBtn_'+data.shipmentList[key].shipmentID+'" value="刪除" onclick="deleteShipment('+data.shipmentList[key].shipmentID+')" class="big_button" ></td>';
                    
                 

                        
                }
						content+='</tr>';
	
				$('#order_list').append(content)
				checkInvoice(data.shipmentList[key].shipmentID,data.shipmentList[key].total,data.shipmentList[key].shopID,data.shipmentList[key].finishTime);
				if($('#myshopID').val()==0)	
				{
					var ID = data.shipmentList[key].shipmentID;
					
					var dates = $( "#charge_"+data.shipmentList[key].shipmentID ).datepicker({
							dateFormat: 'yy-mm-dd' ,
							yearRange: '1930',
							monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
							changeMonth: true,
							numberOfMonths: 1,
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates.not( this ).datepicker( "option", option, date );
							},
							onClose:function()
							{
								
						
									editShipment(focusID);
									
							}
							
						});		  	
					
				}
			}
			
			$('#moreOrderBtn').trigger('click')
		}
		else
		{
			if(offset==0)$('#order_list').html('<h1>查無資料</h1>');
				total = 0
				if(offset!=-1)$('#order_list').append('沒有其他資料了');
				$('#moreOrderBtn').hide();
				$('.shipimet_total').each(function(){
					  total+=parseInt($(this).html())
					
					
				})
				$('#totalAmount').html('總金額：'+total+' 元')
		}
	},'json')	
	
	
}
function shipOut(id)
{
	
	$.post('/order/shipment_to_out',{'shipmentID':id},function(data)
	{
		if(data.result==true)	
		{
				$('#shopStatus_'+id).html('<span style="color="green"">已送達物流</span>');
		}
		
		
	},'json')
	
}



function getCheckBtn(id,token)
{
	
	
	
	if(token)
	{
		$('.product_check').attr('checked',true);
		$('#'+id).html('<input type="button" value="全消" class="big_button" onclick="getCheckBtn(\''+id+'\',false)">');
		
	
	}
	else
	{
		$('.product_check').attr('checked',false);
		$('#'+id).html('<input type="button" value="全選" class="big_button" onclick="getCheckBtn(\''+id+'\',true)">');
	}
	
	
	
	
	
}



function shipmentRow(data,type,status,index)
{

	var Zh=new Array('項次','核取','訂單編號','櫃號','商品編號','中文','英文','語言','定價','出貨折數','出貨價格','訂貨數量','出貨數量','公司庫存','小計','備註','預計貨日','刪除');
	var Eng = new Array('number','check','orderNum','cabinet','productNum','ZHName','ENGName','language','price','purchaseCount','purchasePrice','buyNum','sellNum','num','subtotal','productComment','shippingDate','rowDelete');
	

		var content = '';
		if(index==1)
		{
			content = '<tr style="background-color:#FFEBEB" id="order_header">';
			for( var key in Zh)
			{	
					if(Eng[key]=='check'){ 
						content+='<td id="checkTitle_'+data.rowID+'"><input type="button" value="全消" class="big_button" onclick="getCheckBtn(\'checkTitle_'+data.rowID+'\',false)"></td>';  
						
						
						}
					else if((type=='order'&&Eng[key]!='sellNum'&&Eng[key]!='eachCost')||
					   (type=='watch'&&Eng[key]!='rowDelete'&&Eng[key]!='num'&&Eng[key]!='eachCost')||
					   (type=='result'&&Eng[key]!='buyNum'&&Eng[key]!='eachCost')||
					   (type=='staff')||
					   (type=='shipment'&&Eng[key]!='rowDelete'&&Eng[key]!='eachCost'&&Eng[key]!='num')
					   
					   )content+='<td class="header_'+Eng[key]+'">'+Zh[key]+'</td>';
					   
					
			}
			content+=
			'</tr>';
	
		}

		 var bgColor = '#FFF';
		 if(data.sellNum>parseInt(data.num)) 
		 {
			 bgColor = 'red';
			 if(data.sellNum != 0)shippingWrong +=data.rowID+','+data.ZHName+'-';
			 
		 }
		 content+='<tr class="orderRow" id="orderRow_'+data.rowID+'" style="background-color:'+bgColor+'" >';

		for(row in Zh)
		{
			
			
			if(Eng[row]=='count')content+='<td></td>';
			else if(Eng[row]=='productNum')content+='<td>'+fillZero(data[Eng[row]])+'</td>';
			else if(Eng[row]=='orderNum')content+='<td>'+magicCheck(data.magic)+'o'+data[Eng[row]]+'</td>';
			else if(Eng[row]=='number')content+='<td>'+(index)+'</td>';
			else if(Eng[row]=='check')
			{
				
				if(data.sellNum != 0)
				{
					content+='<td><input type="checkbox" class="product_check" checked="checked"  value="'+data.rowID+'" ></td>';	
				}
				else content+='<td></td>';	
			}
			
			
			else if(Eng[row]=='purchaseCount')
			{
				
				
				if(data['nonJoinPurchaceDiscount']!=0)	purchaseCount =data['nonJoinPurchaceDiscount'];
				else purchaseCount = data[Eng[row]];
				
				purchaseCount = (parseInt(purchaseCount));
				
				purchaseCount = Math.round(data['sellPrice']*100/data['price']);
				if(type=='staff'&&status!="已到貨"&&status!="已送達物流")
				{
					content+='<td id="purchaseCount_'+data.rowID+'" onclick = "changePurchaseCount(\''+data.rowID+'\')">'+purchaseCount+'%</td>';
				}
				else 
				{
					content+='<td id="purchaseCount_'+data.rowID+'">'+purchaseCount+'%</td>';
					
				}
			}
			else if(Eng[row]=='purchasePrice')	
			{
				
				if(type=='staff'&&status!="已到貨"&&status!="已送達物流")content+='<td id="price_row_'+data.rowID+'" class="priceRow" ><input type="text"  id="sellPrice_'+data.rowID+'" name="sellPrice_'+data.rowID+'" value="'+data['sellPrice']+'" onblur="countTotal(this.value)"></td>';
				else 
				{
					content+='<td  id="price_row_'+data.rowID+'" class="priceRow" >'+data['sellPrice']+'<input type="hidden" name="sellPrice_'+data.rowID+'" id="sellPrice_'+data.rowID+'" value="'+data['sellPrice']+'"></td>';
					
				}
				
			}
			else if(Eng[row]=='eachCost') {if(type=='staff')content+='<td id="'+Eng[row]+'_'+data.rowID+'">'+data['avgCost']+'</td>';}
			else if(type=='order'&&Eng[row]=='sellNum');
			else if(type=='result'&&Eng[row]=='buyNum');
			else if(type=='watch'&&Eng[row]=='sellNum')
			{
				if(data['sellNum']==-1)data['sellNum'] =0;
				content+='<td>'+data[Eng[row]]+
						'<input type="hidden"  id="sellNum_'+data.rowID+'" value="'+data['sellNum']+'" />'+
						'</td>';
			}
			else if(Eng[row]=='rowDelete' && data.rowID!=41406){
				
				if(type=='staff'&&status!="已到貨"&&status!="已送達物流")
				{
					content+='<td>'+
							'<input class="orderRowDeleteClass" type="button"  value="刪除" onclick="orderRowDelete('+data.orderID+','+data.rowID+')" />';
					if(data['applyDelete']==1)content+='申請取消訂貨中';
					content+='</td>';				
				}
				else
				{
					
					
					if(data['applyDelete']==0)
					
					{
					content+='<td>'+
							'<input type="button"  value="取消訂貨" onclick="orderRowDelete('+data.orderID+','+data.rowID+' )" />'+
						'</td>';	
					}
					else
					{
						content+='<td>申請取消訂貨中</td>';	
						
					}
				}
			}
			
			else if(Eng[row]=='sellNum')
			{
					if((status!="已到貨"&&status!="已送達物流"))
					{
						if(data['sellNum']==-1)data['sellNum']=data['buyNum'];
						content+='<td>'+
						
									'<input type="hidden"  name="check_'+data['rowID']+'" class="check_row_'+data['rowID']+'" value="1"/>'+
									'<input type="hidden"  name="rowID_'+data['rowID']+'" id="order_row_'+data['rowID']+'" value="'+data['rowID']+'"/>'+
									
									'<input type="hidden"  name="sellNum_'+data['rowID']+'" id="sellNum_'+data['rowID']+'" value="'+data['sellNum']+'"/>'+
									data['sellNum']+'<br/>';
									
						if(type=='staff')content+='<input type="button"  value="重新配貨" onclick="reAllocate('+data['productID']+','+data.addressID+')" />';
								
									
					
						if(data.concessions!=null) var concessions = data.concessions.split(',');
						else var concessions ={};
					
					
						if(data.concessionsNum!=null) var concessionsNum = data.concessionsNum.split(',');
						else var concessionsNum ={};				
						for(each in concessions)
						{
							content+='<div  style="display:none" id="concessions_'+data['rowID']+'_'+each+'">'+concessions[each]+'</div>';
							content+='<div  style="display:none" id="concessionsNum_'+data['rowID']+'_'+each+'">'+concessionsNum[each]+'</div>';
							
						}	
			
							
							content+='</td>';
					}
					else
					{
						content+='<td>'+
									'<input type="hidden"  name="sellNum_'+data['rowID']+'" value="'+data['sellNum']+'" class="sellRow" id="sellNum_'+data.rowID+'" />'+data['sellNum']+
								'</td>';						
					}
			}
			else if(Eng[row]=='subtotal')
			{
				if(data['sellNum']!=-1) num = data['sellNum'];
				else num = data['buyNum'];
				content+='<td id="subtotal_'+data.id+'">'+data['sellPrice']*num+
						 '</td>';
			}
			else if(Eng[row]=='productComment'&&status!="已到貨"&&status!="已送達物流"){
				
				content+='<td><input type="text" name="productComment_'+data['rowID']+'" onchange="commentChange(this.value,'+data['rowID']+')"  value="'+data['productComment']+'"  ></td>';

				

			}
			else if(Eng[row]=='num')
			{
					if(type=='staff')content+='<td id="'+Eng[row]+'_'+data.rowID+'">'+data[Eng[row]]+'</td>';
				
				
			}
			else if (Eng[row]=='shippingDate')
			{
				if(data.sellNum == 0)
				{
					
					if(data.preTime) var waitDate=data.preTime;
					else var waitDate=data.shippingDate;
					
					content+='<td>';
					if(type=="staff")
					{
						
						content+='<input type="hidden" id="dateProductID_'+data.rowID+'" value="'+data.productID+'" ><input type="text" id="shippingDate_'+data.rowID+'" value="'+waitDate+'"  >';
						
						waitString +=data.rowID+',';
						  	
					}
					else
					{
						if(waitDate=='7777-07-07')content+='廠商暫時缺貨中';
						else if(waitDate=='3333-03-03')content+='暫不進貨';
						else if(waitDate=='9999-09-09')content+='<span style="color:red">已斷貨，請取消訂購</span>';
						else if(waitDate!='0000-00-00')content+='<input type="button" value="到貨日'+waitDate+'\n點選觀看預計分配量" onclick="allocateNum('+data.productID+')">';
						else if(waitDate=='0000-00-00'&&data.wait!=0)content+='需等候約'+data.wait+'天以上的叫貨時間';
						else content+='進貨時間未定';
			
						
						
					}
					if($('#shopID').val()<1000)	 content+='<br/><input type="button" style=" cursor:pointer" onclick="adjustInf('+data.productID+',\''+data.language+'\')" value="調貨資訊" /></td>';
					
					

					
					
				}
				
				else
				{
				//var myDate=new Date();
				//myDate.setDate(myDate.getDate() +((4+7)-myDate.getDay())%7);
				//content+='<td>'+dateFormat(myDate, "yyyy-mm-dd")+'</td>';
				}
			}
			else if (data[Eng[row]])
			{
				content+='<td id="'+Eng[row]+'_'+data.rowID+'">'+data[Eng[row]]+'</td>';
				
			}
			
			else 
			{
				
				content+='<td></td>';
			}
		}
			
		content+='</tr>';
		
		return content ;
	
	
}

function commentChange(comment,rowID)
{
	
	
	$.post('/order/comment_change',{rowID:rowID,comment:comment},function()
	{
		
		if(data.result==true);
		
	},'json')
	
	
}
function allocateNum(productID)
{
	$.post('/order/get_pre_allocate',{productID:productID},function(data)
	{
		if(data.result==true)
		{
			if(confirm('你同意預計分配數量為變動數量，可能因為其他因素改變。'))
			{
				content='<h1>預計分配數量</h1>';
				content+='<h2>此商品總共預計被分配到的數量為</h2>'+
						'<h2>'+data.preAllocateNum+'</h2>';
				content+='<h2>請注意預計分配數量為變動數量，可能因為其他因素改變。</h2>'
				openPopUpBox(content,400,280,'closePopUpBox');
			}



		}
	},'json')	
	
	
}



function reAllocate(productID,addressID)
{
	$.post('/order/get_product_in_order',{productID:productID},function(data)
	{
		if(data.result==true)
		{
			productInf =data.productInf.product[0];
			content ='<h1>'+productInf.ZHName+'-'+productInf.ENGName+'('+productInf.language+')的配貨</h1>';
				content +='<div>目前庫存：<span >'+productInf.nowNum+'</span>'+
			 '  <span style="color:red">電商保留：<span id="martNum">'+productInf.martNum+'</span></span>'+
			 '  可用庫存：<span id="nowNum">'+productInf.remainNum+'</span>'+
            '  需求數量：<span id="need"></span>'+
			'  配完庫存：<span id="remain"></span> <input type="button" value="按比例分配貨品" onclick="autoAllocate('+productInf.nowNum+')"></div>';

			content += '<form id="allocateForm">';
			content += '<input type="hidden" name="productID" id="allocateProductID" value="'+productID+'">';
			content +='<table border="1" style="width:1100px;text-align:center" >';				
			content += '<tr style="background-color:#FFEBEB">'+
							'<td>店家</td>'+
							'<td>訂貨單號</td>'+
							'<td>訂貨時間</td>'+
							'<td>訂貨數量</td>'+
							'<td>配貨數量</td>'+
							'<td>刪除</td>'+
					   '<tr>';
			
			var totalNum = 0;
			for( var key in data.product )		   
			{
				if(key % 2 ==0) bgColor ='white';
				else bgColor = '#EEE';
				content += '<tr id="allocateOrderRow_'+data.product[key].rowID+'"style="background-color:'+bgColor+'">'+
								'<td id="shopName_'+key+'">'+data.product[key].shopName+'</td>'+
								'<td>'+magicCheck(data.product[key].magic)+' o'+data.product[key].orderNum+'</td>'+
								'<td>'+data.product[key].orderTime+'</td>'+
								'<td class="buyNum" id="buy_'+key+'">'+data.product[key].buyNum+'</td>'+
								'<td><input type="hidden" id="shopID_'+key+'" value="'+data.product[key].shopID+'"><input type="hidden" name="orderDetailID_'+key+'" value="'+data.product[key].orderDetailID+'">'+
								'<input type="text" class="allocateNum" id="allocate_'+data.product[key].rowID+'" name="allocate_'+key+'" value="'+data.product[key].sellNum+'" onchange="countRemain()"></td>'+
								'<td><input type="button"  value="刪除" onclick="orderRowDelete('+data.product[key].orderID+','+data.product[key].rowID+')" /></td>'+
						   '<tr>';				
				totalNum += parseInt(data.product[key].sellNum);
			}
			

			
			content+='</table>';	
			content += '</form>';	
			content += '<input type="hidden" id="reAddressID" value="'+addressID+'">';								
			openPopUpBox(content,1100,280,'reAllocateSend');
			countRemain();
		}
	},'json')	
	
	
	

}





var dateToken =new Array ;
function onDate(id)
{

	  if(typeof(dateToken['#'+id] )=='undefined')
	{
		
		dateToken['#'+id] = true
		var dates = $('#shippingDate_'+id).datepicker({
							dateFormat: 'yy-mm-dd' ,
							
							yearRange: '1930',
							monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
							changeMonth: true,
							numberOfMonths: 1,
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
								
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates.not( this ).datepicker( "option", option, date );
							},
							onClose:function(data)
							{
								changeShippingDate(id);
								
							}
						});			
	
	
	}
}

function changeShippingDate(id)
{
	
	
		$.post('/order/change_pretime',{productID:$('#dateProductID_'+id).val(),preTime:$('#shippingDate_'+id).val()},function(data)
		{
		
			if(data.result==true)
			{
				preTime = $('#shippingDate_'+id).val();
				if(data.phantri.length>0)
  					if(confirm('有人在問題解決中心詢問本次相關產品到貨時間，是否一律以到貨時間：'+preTime+'回覆，並且結案？'))
					{
						 preTimeSubmit(preTime,data.phantri);
					}
				 

				
				return;
				
			}
			else alert('錯誤');
			
			
			
			
		},'json')
	
	

	
	
}




//shopping car model

function selectType(placeID)
{
	
	if(onShoppingCheck()==false) return;
	else $('#clientOrderQuery').html('');
	
	
	$('#carOption').detach();
	$('#'+placeID+'Query').html('');
	$('#product_list').html('');
	

var txtDateString = "2018/02/23";
var currDate = Date.parse((new Date()).toDateString());
var txtDate = Date.parse(txtDateString);

	

if ( txtDate.valueOf() >currDate.valueOf() )
{
alert('[產品部公告]2/21(三)起恢復出貨，2/23(五)適逢倉庫遷移暫停出貨一日<br/>')
}
	
	
	
	
	
	shoppingCar('clientOrder');
	
	
	/*
	$.post('/order/time_chk',{},function(data)
	{
		if(data.result==true)	shoppingCar('clientOrder');
		else 
		{
			$('#'+placeID+'Query').html(
				'<h1>系統維護中</h1>'+
				'<h2>週一到周五早上9:00~12:00為系統維護時間</h2>'+
				'<h2>請利用其他時間定貨，謝謝您的合作</h2>'
			
			
			
			);	
			
		}
	},'json')
	*/

	
	
	/*
	$('#product_list').html(
	 '<h1>請選擇訂貨方式</h1>'+
	 '<div style=" float:left; margin: 20px 0 0 20px">'+
	'<input type="button" value="傳統模式"  class="big_button"  style=" height:100px" onclick="queryProduct(\'clientOrder\',\'select\');$(\'#product_list\').html(\'\')" >'+
	'<input type="button" value="購物車模式"  class="big_button" style=" height:100px"  onclick="shoppingCar(\'clientOrder\');$(\'#product_list\').html(\'\')">'
	);
	*/
}
function shipmentOut(addressID,token)
{
    $('#shipmentOut').hide();
    loginChk();
    
    if(token==0)var sStr = prompt("請輸入匯款金額?");
    else 
    {
    if( dirNeedShipping==true)
        {
           if(confirm('您訂購的商品未滿8000快速出貨金額，是否加入運費？'))
            {
                
                  
                var sStr ='月結客戶直接出貨';
            } 
            else  $('#shipmentOut').show();
        }
        
        else    var sStr ='月結客戶直接出貨';
        
      
    }
			if(sStr!=null&&sStr!="")
			{
                   alert('讀取中...請稍後<img src="/images/ajax-loader.gif"/>');
				$.post('/order/shipment_out',{money:sStr},function(data){
					
						if(data.result==true)
						{
							
							shopShipmentCreate('monthShipping_ok_form',1,addressID)
							confirm('已經發信通知囉，確認無誤後會在下個出貨日將產品寄出，謝謝');
                            alert('讀取中...請稍後<img src="/images/ajax-loader.gif"/>');
							
						}	
						else
						{
							
							
							alert('登入逾時，請重新登入喔');
							location.href('/welcome/login');
						}
					
					
					},'json')
				
				
				
				
			}
			else
			{
				alert("請輸入一組數字");
			}
	
	
	
	
}

function cheapOrder()
{
	
	$('#top150').val(-1)	;
		findInShoppingCar(0,0);
}


function shoppingCar(placeID)
{
	
	var today = new Date();
	var dt = new Date(today.getFullYear(),today.getMonth(),today.getDate()+Dshift);
	var lastOrder = new Date(dt.getFullYear(),dt.getMonth()+1,dt.getDate()+Dshift);
	var newPreOrder = new Date(dt.getFullYear(),dt.getMonth()+2,dt.getDate()+Dshift);
     
	 if(suppliers=='')
	 {
		 $('#product_list').html('<h1>資料讀取中，請稍候</h1>');
		 settimeout(function(){shoppingCar(placeID)},1000)
		return;	 
	 }
	  
	onShopping = true;
	$('#product_list').detach();
	$('#seepreorder').detach();
	$('#preorder').detach();
	$('#normalOrder').detach();
	$('#preditOrder').detach();
	
	$('#resultUrl').detach();	
	$('#magicOrderBtn').detach();	
    $('#pokemonOrderBtn').detach();	
	$('#economyOrder').detach();
	$('#cheapOrder').detach();	
	if($('#shopID').val()<=1000)
	{
		var preBtn = 
					'<input id ="preditOrder"  type="button" value="訂貨小精靈"  class="big_button" onclick="orderElf()">'+
		//'<input id ="preditOrder"  type="button" value="拉密大賽資格"  class="big_button" onclick="getRumi()">'+
                    '<img id="magicOrderBtn" src="/images/magic.jpg" style="cursor:pointer; height:50px" onclick="magicOrder(\''+placeID+'\')">'+
                    '<img id="pokemonOrderBtn" src="/images/pokemon.png" style="cursor:pointer; height:50px" onclick="pokemonOrder(\''+placeID+'\')">'+
            
                    '<span style="color:red" id="orderLimmit"></span>'+    
                    '<span style="color:red" id="orderLimmitUsed"></span>'+           
						'<span id="resultUrl" style="float:right"></span>'+
					'<input id ="economyOrder" style="background-image:url(/images/index_purplebutton_02.png); float:right"  type="button" value="點此設定經濟訂貨量"  class="big_button" onclick="economyCaculate()">'
	
						
	}
	else	var preBtn = '';
	$('#'+placeID+'Query').before(
		'<input id ="cheapOrder"  type="button" value="限時促銷專區"  class="big_button" onclick="cheapOrder()">'+
				preBtn+
		'<div id="product_list">'+
		
		'</div>');


	$('#'+placeID+'Query').show();	
	$('#'+placeID+'Query').html(
		'<div style="clear:both"></div><input type="hidden"  name="status" id="openStatus" value="1">'+
		'TOP商品<select name="top150" id="top150">'+
			'<option value="all">全部顯示</option>'+
			'<option value="30">精選30</option>'+
			'<option value="1">150商品清單</option>'+
			'<option value="10">TOP商品清單</option>'+
			'<option value="0">非150商品清單</option>'+
			'<option value="-1">促銷商品</option>'+
			'</select>'+
		'商品分類<select name="orderType" id="orderType">'+
			'<option value="-1">全部</option>'+
			'<option value="1">遊戲販售</option>'+
			
			
			'<option value="5">其他</option>'+
			'<option value="7">寶可夢及其他卡牌</option>'+
			'<option value="8">魔法風雲會</option>'+
			'</select>'+
		'遊戲分類<select name="orderCategory" id="orderCategory">'+
			'<option value="-1">全部</option>'+
			'<option value="A">家庭遊戲</option>'+
			'<option value="B">派對歡樂</option>'+
			'<option value="C">輕度策略</option>'+
			'<option value="D">重度策略</option>'+
			'<option value="E">團隊合作</option>'+
			'<option value="F">兩人對戰</option>'+
           '<option value="G">周邊商品</option>'+
			'</select>'+	
		'排序依據<select name="orderCondition" id="orderCondition">'+
			'<option value="0">最近到貨</option>'+
            '<option value="6">商品名稱</option>'+
			'<option value="1">熱門商品</option>'+
            '<option value="5">商品名稱</option>'+
			'<option value="2">商品售價(低->高)</option>'+
		    '<option value="3">商品售價(高->低)</option>'+
		 	'<option value="4">公司庫存(高->低)</option>'+
			'</select>'+	

		'供應商<select name="orderSupplier" id="orderSupplier">'+
			suppliers+
			'</select>'+
		'呈現方式<select name="orderShowWay" id="orderShowWay">'+
			'<option value="0">方格</option>'+
		
			'</select>'+	
				
		'<input type="text" id="queryString"  class="big_text" placeholder="在此搜尋商品" onkeyup="findInShoppingCar(1,0)">'+
		'<input type="hidden" value="0"  id="magicToken" >'+	
		'<input type="button" value="查詢"  class="big_button" onclick=" findInShoppingCar(0,0)">'+
		'</div>'+	
		'<div id="shoppingList"  style="float:left;  width:1250px;; " ></div>'+
		'<div style=" clear:both"></div>'+
        

						
					'<div id="recommandContainer">'+
                    '<div id="recommandLeftBtn" onclick="recommandMove(\'left\')" ></div>'+
					'<div id="shoppingRecommand"  style="float:left;  class="scroll-content" width:1250px;" ><img src="/images/ajax-loader.gif"/></div>'+
					'<div id="recommandRightBtn" onclick="recommandMove(\'right\')"  ></div>'+
					'<div>'+
		'<div class="clearfix"></div>'
	 
	);
		
	if($('#shopID').val()<=1000 &&dt.getDate()>3)$('#preorder').show();
	findInShoppingCar(0,0,0);
	loadingRecommandData();

    loadOrderLimit();
}

function loadOrderLimit()
{
  
 $.post('/order/get_order_limit',{},function(data){
     
     if(data.result==true)
         {
           
             $('#orderLimmit').html('尚有訂貨餘額為：');
             $('#orderLimmitUsed').html( data.shopStatus.limitAmount);
            
             
         }
     
     
 },'json')   
    
    
    
    
}

function magicOrder(placeID)
{
	if($('#carOption').length>0)
	{
		if(confirm('魔法風雲會商品須獨立訂購，這樣將會清除購物車的資料，確定？'))
		{
				clearProduct('product');
				$('#carOption').detach();
		}
		else return;
	}
     $('#newProduct').hide();
	$('#'+placeID+'Query').html(
		'<h2 id="magicStatus" style=" background-color:#A05200;color:white">magic type</h2>'+
		'<div style="clear:both"></div>'+
		'<div id="selectOption">'+
			'<input type="hidden" value="1"  id="openStatus" >'+
			'<input type="hidden" value="all"  id="top150" >'+
			'<input type="hidden" value="F"  id="orderCategory" >'+
			'<input type="hidden" value="0"  id="orderCondition" >'+
			'<input type="hidden" value="0"  id="orderSupplier" >'+
			'<input type="hidden" value="0"  id="orderShowWay" >'+	
			'<input type="hidden" value="1"  id="magicToken" >'+
			'<h2>魔法風雲會專區</h2>'+				
		'<input type="text" id="queryString"  class="big_text" placeholder="在此搜尋商品" onkeyup="findInShoppingCar(1,0)">'+
		'<input type="button" value="查詢"  class="big_button" onclick=" findInShoppingCar(0,0)">'+
		'</div>'+	
		'<div id="shoppingList"  style="float:left;  width:1250px;; " ></div>'+
		'<div style=" clear:both"></div>'
	
	);
	
	findInShoppingCar(0,0,0);
	$.post('/order/magic_status',{},function(data)
	{
		
		if(data.result==true)
		{
	
			$('#magicStatus').html(data.magicStatusStr)
		}
		
	},'json')
	
	
}
function pokemonOrder(placeID)
{
	if($('#carOption').length>0)
	{
		if(confirm('寶可夢商品須獨立訂購，這樣將會清除購物車的資料，確定？'))
		{
				clearProduct('product');
				$('#carOption').detach();
		}
		else return;
	}
   
    $('#newProduct').hide();
	$('#'+placeID+'Query').html(
		'<h2 id="pokemonStatus" style=" background-color:#A05200;color:white">'+
            '*寶可夢集換卡片訂購注意事項：<br/>'+
            '1.訂購商品滿11000即可由廠商直送，廠商固定周三或周四出貨。<br/>'+
            '2.寶可夢系列商品必須先行匯款，需於出貨日前一工作日中午前完成匯款<br/>'+
            '3.訂購未滿11000之店家，需等待總倉集貨，再隨其他桌遊一同出貨，需時較長<br/>'+
        '</h2>'+
		'<div style="clear:both"></div>'+
			'<h2>寶可夢專區</h2>'+				
        '</div>'+	
		'<div id="shoppingList"  style="float:left;  width:1250px;" ></div>'+
		'<div style=" clear:both"></div>'
	
	);
	

	$.post('/order/get_pokemon',{},function(data)
	{
		
		if(data.result==true)
		{
             $('#shoppingList').html('');
            		$('#shoppingList').append('<form id="pokemonForm"><table border="1" id="pokemonSell"></table></form>');
	           for( var key in data.product)
					{
                     
                        content='<tr>'+
                                   
                                   
                                    '<td><input type="hidden"  value="'+data.product[key].id+'">'+
                                    data.product[key].name+' </td>'+
                                    '<td style="text-align:right">$<span id="sellPrice_'+data.product[key].id+'">'+data.product[key].sellPrice+'</span></td>';
                             
                     
                        content+=
                            	'<td><input class="allChange" type="hidden" name="id[]" value="'+data.product[key].id+'"><div class="btn_minus" onclick="changeAmount(-1,\'pokemon_num_'+data.product[key].id+'\');pokemonTotal()"></div><input type="text" class="short_text pokemonSellList" id="pokemon_num_'+data.product[key].id+'" name="num[]" onchange="pokemonTotal()" onblur="" value="0" style="float:left"><div class="btn_plus" onclick="changeAmount(1,\'pokemon_num_'+data.product[key].id+'\');pokemonTotal()"></div>'
                                '</td></tr>';
                       $('#pokemonSell').append(content);
				
					
					}
		          $('#shoppingList').append('<h1>總金額：$<span id="pokemonTotal">0</span>元</h1>');
             $('#shoppingList').append('<input type="button" style="float:left" class="big_button" value="確認訂單送出" onclick="orderConfirm(1)">');
		}
		
	},'json')
	
	
}

function pokemonTotal()
{
    
    var total = 0;
    $('.pokemonSellList').each(function()
                              {
        
         var a = this.id.split('_');
         var id = a[2];
         var sellPrice = parseInt($('#sellPrice_'+id).html());
         total+= sellPrice * this.value
    })
    $('#pokemonTotal').html(total);
    
    
    
}


function seePreorder(year,month)
{
		
	$.post('/order/preorder',{year:year,month:month},function(data){
		
		if(data.result == true)
		{
			
			$('#preorderContent').append('<input type="hidden" name="shopID" value="'+data.shopID+'">');
			for(key in data.product)
			{
				if(data.product[key].orderNum>0)
				{
					$('#preorderContent').append('<input type="hidden" name="productID_'+key+'" value="'+data.product[key].productID+'">');
					$('#preorderContent').append('<input type="hidden" name="num_'+key+'" value="'+data.product[key].orderNum+'">');
				}
			
				
			}	
			
			preoderTable();
			
		}
		
		
		},'json')
	
	
		 	

	
}

function seePrepay()
{
	
		if(onShoppingCheck()==false) return;
		else $('#clientOrderQuery').html('');
$('#clientOrderQuery').html('')
	$('#productQuery').html('');
	$('#productSelect').detach();
	
		
	$.post('/order/prepayorder',{},function(data){
		
		if(data.result == true)
		{
			
			$('#product_list').html('<div id="prePayorder" style=" display:none; background-color:#FFCCCC">'+
									'<h1>此表格僅統計「預付商品」，若需預訂，請直接系統上下單</h1>'+
									'<form id="prepayorderContent">'+
									'<table border="solid" id="prePayorderTable">'+
        						    '</table>'+
									'<div style="text-align:right" id="prePayTotal"></div>'+
									'</form>'+
									'</div>')

			
			
			
			
			
			
			
			$('#prePayorderTable').html('<tr><td>中文</td><td>英文</td><td>語言</td><td>備註</td><td>價錢</td><td>出貨價</td><td>成箱數量</td><td>成箱出貨單價</td><td>預付數量</td><td>小計</td><td>商品連結</td></tr>');
			$('#prePayorder').show();
			for(key in data.product)
			{
			    	
					if(data.product[key].orderNum>=data.product[key].packingNum) ratio = 0.6;
					else ratio = 0.65;
				content='<tr><td>'+data.product[key].ZHName+'</td>'+
				         '<td>'+data.product[key].ENGName+'</td>'+
						 '<td>'+data.product[key].language+'</td>'+
						 '<td>'+data.product[key].comment+'</td>'+
						 '<td id="price_'+data.product[key].productID+'">'+data.product[key].price+'</td>'+
						 '<td>'+Math.round((data.product[key].price*0.65))+'</td>'+
						 '<td id="packing_'+data.product[key].productID+'">'+data.product[key].packingNum+'</td>'+
						 '<td>'+Math.round((data.product[key].price*0.6))+'</td>'+
						 '<td><input type="text" class="short_text" onchange="prePayTotal('+data.product[key].productID+')" id="orderNum_'+data.product[key].productID+'" value="'+data.product[key].orderNum+'"></td>'+
						 '<td class="preEachTotal" id="orderTotal_'+data.product[key].productID+'">'+Math.round((data.product[key].price*ratio))*data.product[key].orderNum+'</td><td>';
				if(data.product[key]['bidExist']==1&&data.product[key]['phaBid']!=0)	
				{
					content+='<a  onMouseover ="showImg(\'http://www.phantasia.tw/upload/bg/home/b/'+data.product[key]['phaBid']+'.jpg\')" href="http://www.phantasia.tw/bg/home/'+data.product[key]['phaBid']+' " target="_blank">觀看連結</a>';
				
					
				}
					 
					content+='</td></tr>';

				
				
					$('#prePayorderTable').append(content);
				
			
				
			}	
			

			
		}
		
		
		},'json')
	
	
}
function savePrePay(productID,num)
{
	$.post('/order/save_prepay',{productID:productID,orderNum:num},function(data)
	{
		
	},'json')
	
}



function prePayTotal(productID)
{
	
	if(fucCheckNUM($('#orderNum_'+productID).val())==0)
	{
		alert('這不是一個數字');
		$('#orderNum_'+productID).val(0);
	}
	else if($("#orderNum_"+productID).val()<0)
	{
		alert('不可為負值');
		$("#orderNum_"+productID).val(0);
	}
	
	if($('#orderNum_'+productID).val()>=parseInt($('#packing_'+productID).html())) ratio = 0.6;
	else ratio = 0.65;
	
	$('#orderTotal_'+productID).html(Math.round(parseInt($('#price_'+productID).html())*ratio)*$('#orderNum_'+productID).val());
	savePrePay(productID,$('#orderNum_'+productID).val());
	total = 0 ;
	$('.preEachTotal').each(function()
	{
		total+=parseInt($(this).html());
		
	})
	
	$('#prePayTotal').html('總金額為：'+total);
	
}

function preoderTable()
{
	var content = '';
		$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/order_confirm",
	   data: $("#preorderContent").serialize(),
	   success: function(data){
		   if(data.result==true)
		   {
			   
				content =orderTable(data,'order',"採購中");
                content +='<input type="hidden" id="orderID" value="'+data.orderID+'">';
				
				content += '<select  id="order_receiver" name="receiverID"  onchange="changeReceiver()">'+
			   				'<option value="0">選擇收件人及地址</option>';
			for(each in data.address)
			{
				if(each==0) addressID = data.address[each].id;
				content+= '<option value="'+data.address[each].id+'">'+data.address[each].receiver+'</option>';
			}
			content += '</select>';	
			content +='收件人： <input type="text" name="receiver" id="receiver"></input>';
			content +='<div style="clear:both"></div>';
			content +='地址： <input type="text" name="address" id="address" style=" width:400px"></input>';;
			content +='<div style="clear:both"></div>';
			content +='電話： <input type="text" name="phone" id="phone"></input>';;
            content +='客戶統編： <input type="text" name="comID" id="comID"></input>';;
            content +='email： <input type="text" name="email" id="email"></input>';;
             content +='<div style="clear:both"></div>';   
          content +='客戶載具：<select name="CarrierType" id="CarrierType">'+
                '<option value="">無</option>'+
                '<option value="3J0002">手機條碼</option>'+
                '<option value="CQ0001">自然人憑證</option>'+
                '</select> <input type="text" class="big_text" name="CarrierId1" id="CarrierId1" placeholder="載具條碼無則空白" >';
           content += '捐贈代碼：<input type="text" class="big_text" name="NPOBAN" id="NPOBAN" placeholder="無則空白" >';
               
			content +='<div style="clear:both"></div>';
				
			   	 content +='訂貨備註欄：<textarea name="order_comment" id="order_comment"  onkeyUp = "	order_comment = $(\'#order_comment\').val();"style=" width:900px; height:100px">預定清單</textarea>';
			   openPopUpBox(content,1100,280,"$('#popUpBoxCancel').click");
				height =parseInt($('#orderTable').css('height').substr(0,$('#orderTable').css('height').length-2))
				popUpBoxHeight(height+300)
				//countTotal(1);
				if(data.address[0].id)
				{
					$('#order_receiver').val(data.address[0].id);
					
				changeReceiver();		   
				}
			    $('#popUpBoxCancel').unbind('click');
			   $('#popUpBoxCancel').click(function(){
				   
					
				    orderCancel(data.orderID);
				  })
			}
			else alert('您沒有預定')	;
	   }
	   })	
	
	
	
	
	
	
}





function preorder(placeID,year,month)
{
	
	if(onShoppingCheck()==false) return;
	$('#'+placeID+'Query').hide();
	$('#preorder').hide();
	$('#normalOrder').show();
	$('#product_list').html('讀取中請稍候<img src="/images/ajax-loader2.gif">')
	
	$.post('/order/preorder',{year:year,month:month},function(data){
		
		if(data.result == true)
		{
			for(key in data.product)
			{
				clientOrderTable(data.product[key],1);
				
			}	
			
			$('#orderConfirmBtn').html('<input type="button" class="big_button" value="儲存變更" onclick="preorderSave('+year+','+month+')">');
		
			
			
		}
		
		
		},'json')
	
//	
	
	
	
}

function preorderSave(year,month)
{
	
		$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/preorder_save",
	   data: $("#productList").serialize(),
	   success: function(data){
		   if(data.result==true) alert('訂單已儲存');
		   	seePreorder(year,month);
		   }
		});
	
	
	
}






var selectData;

function findInShoppingCar(type,onPage,queryPages)
{
	if(type==1){
		if(enter()==false) return;
	}
    loginChk();
	var queryData = {queryString:$('#queryString').val(),openStatus:$('#openStatus').val(),top150:$('#top150').val(),orderCategory:$('#orderCategory').val(),orderCondition:$('#orderCondition').val(),suppliers:$('#orderSupplier').val(),orderType:$('#orderType').val(),
	magicToken:$('#magicToken').val()};
	var perPageNum = 9;
	//表第一次
		
	if(onPage==0)
	{
			queryOffset = 0;
		
			$('#shoppingList').html('');
			
			$('#shoppingProductInfo').detach();
			$('#shoppingProductInfoOption').detach();
			infoKey = false;
			$('#clientOrderQuery').append('<div id="shoppingProductInfo" style=" margin-top:5px; width:1250px; height:850px; overflow:auto"></div>');
			$('#clientOrderQuery').append('<div  id="shoppingProductInfoOption"  style=" width:1px; color:white; background:black"></div>');
			infoToggle();
			$('#shoppingList').append('<div class="pageList"></div>');
			$('#shoppingList').append('<div style="clear:both"></div>');
			

			$('#shoppingList').append('<div id="shoppingProductList"></div>');
			$('#shoppingList').append('<div style="clear:both"></div>');
			$('#shoppingList').append('<div class="pageList"></div>');
			
			$('.pageList').append('<span class="lastPage"></span>');
			$('.pageList').append('<span class="pagenum"><img src="/images/ajax-loader2.gif"></span>');
			$('.pageList').append('<span class="nextPage"></span>');
			selectDataList = new Array();
			contentList = new Array();
			
			$('#shoppingProductInfoOption').before(
					
				);


			//create page list
		
			$.post('/order/count_search',queryData,function(data)
			{
			
				if(data.result==true)
				{
					
					 queryPages = Math.ceil(data.totalNum / perPageNum);
					if(data.totalNum==0) $('#shoppingList').html('<h1>找不到相關遊戲喔！可能是關鍵字錯誤或是範圍錯誤喔!</h1>');
					else $('.pageList').append('(共'+queryPages+'頁)');
					showShoppingData(1,queryData,perPageNum,queryPages)
				}
			},'json')
			
		
	}
	else showShoppingData(onPage,queryData,perPageNum,queryPages);
	
	
}
var carKey = true;

function carToggle()
{
	if(carKey==true)
	{	
		carKey = false;
		$('#product_list').slideUp();
		$('#carOption').css('margin-top','0px');
		$('#carOption').css('margin-left','auto');
		$('#carOption').css('margin-right','auto');
	
		$('#carOption').html('(顯示購物車內容)<img src="/images/down.png"  style=" cursor:pointer;  z-index:100; position:absolute" onclick="carToggle()">');

	
	}
	else
	{
		
		carKey = true;
		$('#product_list').slideDown();
		$('#carOption').css('margin-top','-30px');
		$('#carOption').html('<span style=" color:white">(隱藏購物車)</span><img src="/images/up.png"  style=" cursor:pointer;  " onclick="carToggle()">');
		
	}
	
}




var infoKey = true;
function infoToggle()
{
	
	
	if(infoKey==true)
	{	
		
		//$('#shoppingProductInfoOption').html('<img src="/images/right.png"  style=" cursor:pointer; margin-top:200px; z-index:100; position:absolute" onclick="infoToggle()">');
		infoKey = false;
		
	}
	else
	{
		

	
		infoKey = true;
		//$('#shoppingProductInfoOption').html('<img src="/images/left.png"  style=" cursor:pointer; margin-top:200px; z-index:100; position:absolute" onclick="infoToggle()">');
		
	}
}
function preorderProduct(id,productID)
{
	$.post('/order/preorder_product',{productID:productID},function(data)
	{
		if(data.result==true) $('#'+id+'_'+productID).html(data.num);
		
		
	},'json')	
	
	
	
	
}




function orderElf()
{
	option = '';
	for(i =0;i<=100;i++)
	{
	option += '<option value="'+i+'">'+i+'</option>';
	}
	
	
	content='<h1>訂貨小精靈</h1>'+
			'<div  id="elf" style="text-align:left">'+
			'<div>本店銷售佔比<select id="percent">'+option+'</select>%(與頂標店家銷售平均比較)</div>'+
			'<div>安全庫存係數<select id="safeCon">'+option+'</select>(填0~100的數字,ex 12 表示預估訂貨量*1.2)</div>'+
			'<div><label><input type="checkbox" id="stockCon" class="tiny_text" value="1" checked="checked">自動扣減庫存及訂貨中數量</label></div>'+
			'<div>應定量大於多少才定<select id="lowerNum">'+option+'</select></div>'+
			'<div><label><input type="checkbox" id="economyOrderID" class="tiny_text" value="1" checked="checked">最經濟訂貨量</label></div>'+

			'</div>';
	
	openPopUpBox(content,500,300,"orderElfUrl");
	$('#percent').val(60);
	$('#safeCon').val(10);
	$('#lowerNum').val(0);
	
}

function orderElfUrl()
{
	
	if($('#stockCon').is(':checked')) stockCon = 1;
	else stockCon = 0;
	if($('#economyOrderID').is(':checked')) economyOrder = 1;
	else economyOrder = 0;
	var url = '/order/estimate/json/'+$('#percent').val()+'/'+$('#safeCon').val()+'/'+stockCon+'/'+$('#lowerNum').val();
	var htmlUrl = '/order/estimate/html/'+$('#percent').val()+'/'+$('#safeCon').val()+'/'+stockCon+'/'+$('#lowerNum').val();
	$('#elf').html('讀取中...請稍後<img src="/images/ajax-loader.gif"/>');
	$('#popUpBoxCancel').val('關閉視窗');
	$.post(url,{},function(data){
		if(data.result==true)
		{
			var total = data.product.length;	
//			alert(total);
			for(key in data.product)
			{
				$('#elf').html(key/total);
				clientOrderTable(data.product[key],1)	;
				
			}
			adjustWidth();
			 $("#product_table").trigger("update");
			$('#elf').html(
				'<h1>載入完成</h1>'+
				'<h2>已載入購物車清單</h2>'
				
			
			
			);
			$('#resultUrl').html('<a href="'+htmlUrl+'" target="_blank"><input type="button" class="big_button" value="觀看分析表"></a>')
			alert('已載入至購物車清單');
			$('#popUpBoxCancel').trigger('click');
			if(economyOrder)  $('#economyOrder').trigger('click');	
		}
		
		
	},'json')
	
	
}



function preorderCheck(year,month,day)
{
	return ;//取消
	if(day<=20) return;
	if($('#shopID').length==0||$('#shopID').val()>1000||$('#shopID').val()==0) return;
	$.post('/order/preorder_check',{year:year,month:month},function(data)
	{
		if(data.result==false)alert('你尚未完成'+month+'月份預定商品，請到我要訂貨點選'+month+'月份預定商品，完成預定');
	
		
	},'json')	
	
	
}



function getProductInfo(productID)
{
	$('#shoppingProductInfo').html('<img src="/images/ajax-loader2.gif">');
	$.post('/order/get_product_info',{productID:productID},function(data)
	{
		$('#shoppingProductInfo').html(data);
		
	})	
	
	
}


function showShoppingData(onPage,queryData,perPageNum,queryPages)
{
		
	queryData.start = (onPage-1) * perPageNum;
	queryData.num = perPageNum;
	$('#shoppingProductList').html('<img src="/images/ajax-loader2.gif">');
		if(onPage>1) 
			{
				$('.lastPage').html('<a onclick="findInShoppingCar(0,1,'+queryPages+')">首頁</a>|<a class="previous" onclick="findInShoppingCar(0,'+(onPage-1)+','+queryPages+')">上一頁</a>｜');
			}
			else $('.lastPae').html('');
			$('.pagenum').html('');

			if(onPage<=5) var startPageCode = 1;
			else var startPageCode = onPage-5;
			
			if((onPage+5)>queryPages) var endPageCode = queryPages;
			else var endPageCode = onPage+5;
			
			for(i=startPageCode;i<=endPageCode;i++)
			{
				if(i==onPage)$('.pagenum').append(' <span style="font-weight:bold">'+i+'</span>');
				else $('.pagenum').append(' <a onclick="findInShoppingCar(0,'+i+','+queryPages+')">'+i+'</a>');
			}
		
			if(onPage<queryPages)
			{
				 $('.nextPage').html('｜<a class="next" onclick="findInShoppingCar(0,'+(onPage+1)+','+queryPages+')">下一頁</a>｜<a onclick="findInShoppingCar(0,'+queryPages+','+queryPages+')">末頁</a>');
				 ;
			}
			else $('.nextPage').html('');	
			loadingShoppingData(onPage,queryData,false);
		
			selectData = selectDataList[onPage];
		
			$('#shoppingProductList').html(contentList[onPage]);
			queryData.start = (onPage) * perPageNum;
			if(onPage<queryPages)loadingShoppingData(onPage+1,queryData,true);
	
	
	 
	
}
function getShoopingAlreadyOrder(productID)
{

	
	$.post('/product/already_order',{productID:productID},function(data){
		if(data.result==true)
		{	
			$('#numChange_'+productID).html('預計'+data.pre.preTime+'到貨');
			
			
		}},'json')

}
var selectDataList;
var contentList;
function contentUpdate(onPage)
{
	contentList[onPage] = $('#shoppingProductList').html();	
	
}
function shoppingFrame(productData,key,onPage,isMain)
{

	var content ='';
	
	content+='<div class="productBox">';
				content+='<div class="productImgBox">';
	
				if(productData.best30==1)
				{
					
					content+='<img src="http://www.phantasia.tw/images/best30.png" style="width:50px;float:right;position:absolute;margin-left:50px;z-index:2">';
					
				}
	
	
				content+='<a href="#shoppingProductInfo"><img  style="float:left" onclick ="autoScroll(\'shoppingProductInfo\');getProductInfo('+productData.productID+')" class="productImg" src="'+productData.img+'" onError="this.src=\'http://www.phantasia.tw/upload/bg/home/b/0.jpg\';"></a>';
				if($('#shopID').val()<1000)	content+='<div><input type="button" class="big_button" onclick="adjustInf('+productData.productID+',\''+productData.language+'\')" value="調貨資訊" /></div>';
							
								content+='<div style=" background-color:#DDDDDD; margin-right:5px"><h3>銷售表現：</h3>'
								content+='<div style=" ">'+productData.flowNum+'/每店,月</div>';
				if($('#shopID').val()<1000)	content+='<div style="color:	#FF0088">'+productData.myFlow+'/本店,月</div>';
				content+='</div></div>';
				content+=	
					'<div id="shopping_'+productData.productID+'" class="shoppingBox">'+
			
						'<li>中文：'+productData.ZHName+'</li>'+
						'<li>英文：'+productData.ENGName+'</li>'+
						'<li>語言：'+productData.language+'</li>'+
						'<li>公司庫存：<span id="numChange_'+productData.productID+'">';
				switch(productData.preTime)
				{
					
					case '3333-03-03': word = '暫不進貨'	;
					break;
					case '7777-07-07': word = '廠商缺貨中'	;
					break;
					case '9999-09-09': word = '商品已絕版'	;
					break;
					
					default: 
						if(productData.preTime) word = '預計'+productData.preTime+'到貨'	;
						else word = 0;
					break;
					
					
				}			
						
						
				content+=(productData.comNum>100)?'足量':(productData.comNum>0)?productData.comNum:word;
				content+=(productData.comNum<=0 && productData.preTime!='3333-03-03' && productData.preTime!='7777-07-07' && productData.preTime!='9999-09-09' &&!productData.preTime)?arriveBtn(productData.productID,true) :'';
				content+='</span></li>';
				
			//	if($('#numChange_'+productData.productID).html()==null) getShoopingAlreadyOrder(productData.productID);
				var sameContent = '';
    
               
                    for(eachkey in productData['sameProduct'])
                    {
                        if(sameContent=='')   sameContent = ' [他版數量]:'; 
                        sameContent += productData['sameProduct'][eachkey]['language']+':('+productData['sameProduct'][eachkey]['num']+')';


                    }
    
                
                
                
    
    
				if($('#shopID').val()<1000)
				content+='<li>我的庫存：'+productData.nowNum+'<span style="color:#BB5500" id="same_product_'+productData.productID+'">'+sameContent+'</span></li>';
						
				content+=		'<li>定價：'+productData.price+'</li>'+
						'<li>出貨：'+concession(productData.purchaseCount,productData.concessions,productData.concessionsNum,' ; ')+'</li>';
				if(productData.orderingNum!=0)content+='<li style="color:red">訂購或運送中：'+productData.orderingNum+'</li>';
				
				if(productData.cardSleeveInf=='')content+='<li>適用卡套：無</li>';
				else content+='<li>適用卡套：'+productData.cardSleeveInf+'</li>';
				if(productData.rule==1)content+='<li style="color:red">*含精美規則</li>';
				if(productData['bidExist']==1&&productData['phaBid']!=0) 
				{
					content+='<li><a href="http://www.phantasia.tw/bg/home/'+productData.phaBid+'" target="_blank">觀看連結</a></li>';
				}
				content+='<li class="car_'+productData.productID+'" >';
				if($('#product_'+productData.productID).length!=0)content+='已加入購物車'		;
				else 
				{
					if(isMain==true)content+='<a onclick="clientOrderTable(selectData['+key+']); contentUpdate('+onPage+')" id="incar_'+productData.productID+'" ><img  style=" height:30px" src="/images/shopcar.jpg" >放入購物車</a></li>';
					else content+='<a onclick="clientOrderTable(recommandData['+key+']); " id="incar_'+productData.productID+'" ><img  style=" height:30px" src="/images/shopcar.jpg" >放入購物車</a></li>';
				}
				content+='</li>';
				if(productData.isConsignment)content+= '<span style="color:red">寄賣品</span>';
				
				
				content+=
					'</div>'+
				'</div>';
	

 return content;	
}
//點數兌換 公司端
function pointFrame()
{
    
   // $('#product_list').html('<h1>點數兌換區</h1>');
    $('#selectOrderStatus').val(-1);
  
    
    $('#product_list').html('<h2>點數兌換專區</h2>'+
							
							'<input type="button" value="點數明細" class="big_button" onclick="pointDetail(0)">'+
							'<input type="button" value="點數增減" class="big_button" onclick="pointAdd()">'+
                            '<input type="button" value="兌換商品清單" class="big_button" onclick="pointChangeProduct()">'+
                            
                            '<h1><span id="myName"></span><h1><h2>目前累積點數為：<span id="myPoint"></span><h2>'+
							'<div id="point_content"></div>'
						   					   
						   
						   
						   );
    	getPointInf();
}

function pointChangeProduct()
{
   
    
    $('#point_content').html('<div id="pointProductAppendQuery"></div><form id="changePointForm"><div id="saveMessage"></div><table border="1"  class="fancytable" id="pointChangeTable"></table></form>');
    	queryProduct('pointProductAppend','select');
    	$.post('/order/get_point_change',{},function(data)
		   {
				if(data.result==true)
					{
						
						$('#pointChangeTable').append(
							'<tr  class="headerrow">'+
                            '<td>編號</td>'+
                            '<td>產品</td>'+
							'<td>原價</td>'+
							'<td>兌換點數</td>'+
                            '<td>操作</td>'+
							'</tr>'+
												
							')')
						for(key in data.product)
						{
                            pointProductAppendTable(data.product[key]);
								
						}
						
					}
	
		   },'json')
		
	
    
    
    
}


function pointProductAppendTable(product)
{
    if(!product.point) point = product.price;
    else point = product.point;

    if($('#point_'+product.productID).html()!=null)
    {
        
        alert('產品已在清單中');
        $('#point_'+product.productID).focus();
        return;
    }
    
    	$('#pointChangeTable').append(
                            '<tr><td>'+product.productNum+'</td>'+
							'<td>'+product.ZHName+'</td>'+
							'<td>'+product.price+'</td>'+
							'<td id="point_'+product.productID+'"><input type="text" onchange="pointProductUpdate('+product.productID+')" class="big_text" id="point_ratio_'+product.productID+'" value="'+point+'"><input class="allChange" type="hidden" name="productID[]" value="'+product.productID+'"></td>'+
						
							'<td><input type="button" class="big_button" value="移除" onclick=" removePointProduct('+product.productID+')"></td>'+
							'</tr>'+
												
							')')
							
        pointProductUpdate(product.productID);
    
    
    
}

function removePointProduct(productID)
{
    if(confirm('你確定要移除？'))
        $.post('/order/point_product_delete',{productID:productID}
        ,function(data){
        
            if(data.result==true)
                {
                   pointChangeProduct();
                    
                    
                }
        
        
        
    },'json')
    
    
    
}


function pointProductUpdate(productID)
{
    
    
    if(checkPoint($('#point_ratio_'+productID).val())==false)
    {
            alert('請數入正確的數字') ;
        return;
    }
    
    
    
    $.post('/order/point_product_update',{productID:productID,point:$('#point_ratio_'+productID).val()}
        ,function(data){
        
            if(data.result==true)
                {
                    $('#saveMessage').html('儲存完畢');
                    setTimeout(function(){$('#saveMessage').html('');},3000);
                    
                    
                }
        
        
        
    },'json')
    
    
    
    
    
}





function pointAdd()
{
    
    content='<h1>店家名稱:'+$('#myName').html()+'</h1>'+
            '<h2>原因：</h2>'+
            '<textarea style="width:300px;height:100px" id="point_comment"></textarea><br/>'+
            '點數：<input type="text" class="big_text" onchange="checkPoint(this.value)" value="0" id="point">'+
            '<div>(扣除點數請直接輸入負數)</div>';
    
    
            
    
    openPopUpBox(content,1000,500,"pointAddSend");
    
}

function checkPoint(num)
{
    if(fucCheckNUM(num)==0)
	{
		alert('請填入數字');
     
		return false;
	}
    return true;
}


function pointAddSend()
{
    if($('#point_comment').val()=='')
        {
            alert('請輸入原因');
               $('#popUpBoxEnter').show();
            return;
            
            
        }
    if(checkPoint($('#point').val())==false) 
    {
           $('#popUpBoxEnter').show();
        return;
    }
    $.post('/order/point_add',{point:$('#point').val(),shopID:$('#select_shopID').val(),'comment':$('#point_comment').val()},function(data){
        
        if(data.result==true)
            {
                
               closePopUpBox() ;
                pointFrame();
              
                
            }
        
        
    },'json')
    
    
    
    
}


function changePoint()
{
	
	$('#product_list').html('<h2>點數兌換專區</h2>'+
							
							'<input type="button" value="點數明細" class="big_button" onclick="pointDetail(0)">'+
							'<input type="button" value="兌換點數" class="big_button" onclick="pointChange()">'+
                            '<h2><span id="myName"></span>目前累積點數為：<span id="myPoint"></span><h2>'+
							'<div id="point_content"></div>'
						   					   
						   
						   
						   );
	getPointInf();
	
	
}

function getPointInf()
{
    if($('#select_shopID')) shopID = $('#select_shopID').val();
    else shopID = 0;
    pointDetail(0);

	$('#clientOrderQuery').html('');
	$.post('/order/get_point_inf',{shopID:shopID},function(data)
		   {
			
				if(data.result==true)
					{
						$('#myName').html(data.shopInf.name);
						$('#myPoint').html(data.shopInf.point);
						
						 
					}
	
		   },'json')
	
	
}
	
var line = 0;
function pointDetail(offset)
{
      if($('#select_shopID')) shopID = $('#select_shopID').val();
    else shopID = 0;
     if(offset==0)
     {
         line = 0;
	   $('#point_content').html('<table border="1" style="margin:10px; " id="pointTable" class="fancytable"></table><div id="morePointBtn"></div>');
     }
    num = 20;
   
	$.post('/order/get_point_detail',{offset:offset,num:20,shopID:shopID},function(data)
		   {
  
				if(data.result==true)
					{
                      newData = false;
						if(line==0)
						$('#pointTable').append(
							'<tr  class="headerrow" style="color:white;text-align:center"><td>時間</td>'+
							'<td>進出點數</td>'+
							'<td>備註</td>'+
							'<td>訂單編號</td>'+
						
							'</tr>'+
												
							')');
                        
                        
						for(key in data.detail)
						{
                            if(line++%2==1) c = 'datarowodd';
                            else c = 'dataroweven';
                            newData = true;
                        
                            content='<tr class="'+c+'">'+
                            '<td>'+data.detail[key].time.substr(0,11)+'</td>'+
							'<td>'+data.detail[key].point+'</td>'+
							'<td>'+data.detail[key].comment+'</td>'+
							'<td>';
                            if(data.detail[key].orderID!="" &&data.detail[key].orderID!=0)
                            content+='<input class="big_button" type="button" value="查看訂單明細" onClick="showOrder('+data.detail[key].orderID+',\'訂單已送達\',\'watch\')">';
                            content+='</td>'+
							'</tr>'+
								
							')';
                            
								$('#pointTable').append(content);
							
							
						}
                        offset+=num;
						if(newData)	$('#morePointBtn').html('<input type="button" onclick="pointDetail('+offset+')" class="big_button" value="查看更多">');
                        else $('#morePointBtn').html('沒有其他資料了');
						
					}
	
	
		   },'json')
		
	
	
	
	
}


 



function pointChange()
{
	$('#point_content').html('<h2>註：若需兌換包膜服務，一盒遊戲30點，請至問題中心留言辦理</h2><form id="changePointForm"><table border="1"  class="fancytable" id="pointChangeTable"></table></form>');
	
		$('#remainPoint').html($('#myPoint').html());
	$.post('/order/get_point_change',{},function(data)
		   {
				if(data.result==true)
					{
						
						$('#pointChangeTable').append(
							'<tr  class="headerrow"><td>編號</td><td>產品</td>'+
							'<td>原價</td>'+
							'<td>兌換點數</td>'+
							'<td>兌換數量</td>'+
							'<td>點數小計</td>'+
							'</tr>'+
												
							')')
						for(key in data.product)
						{
								$('#pointChangeTable').append(
                            '<tr><td>'+data.product[key].productNum+'</td>'+
							'<td>'+data.product[key].ZHName+'</td>'+
							'<td>'+data.product[key].price+'</td>'+
							'<td id="point_'+data.product[key].productID+'">'+data.product[key].point+'</td>'+
							'<td><input  type="hidden" name="point[]" value="'+data.product[key].point+'"><input class="allChange" type="hidden" name="productID[]" value="'+data.product[key].productID+'"><div class="btn_minus" onclick="changeAmount(-1,\'change_num_'+data.product[key].productID+'\');changeTotal()"></div><input type="text" class="short_text" id="change_num_'+data.product[key].productID+'" name="num[]" onchange="changeTotal()" onblur="" value="0" style="float:left"><div class="btn_plus" onclick="changeAmount(1,\'change_num_'+data.product[key].productID+'\');changeTotal()"></div></td>'+
							'<td id="subtotal_'+data.product[key].productID+'"></td>'+
							'</tr>'+
												
							')')
							
							
						}
						$('#pointChangeTable').append(
							'<tr><td></td>'+
							'<td>剩餘點數:</td>'+
							'<td id="remainPoint">'+$('#myPoint').html()+'</td>'+
							'<td>兌換總點數</td>'+
							'<td id="ChangPoint">0</td>'+
							'</tr>'+
												
							')')
						$('#pointChangeTable').append(
							'<tr><td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td><input type="button" class="big_button" value="送出" onclick=" changeSubmit()"></td>'+
							'</tr>'+
												
							')')
						
					}
	
		   },'json')
		
	
	
	
	
}
var passToken = false;
function changeTotal()
{
	
	$('#remainPoint').html(	$('#myPoint').html() ) ;
	var changeTotal = 0;
	 passToken = false;
    passkey = true;
	$('.allChange').each(function()
		{
		
			productID = $(this).val();
	
			if(fucCheckNUM($("#change_num_"+productID).val())==0)
			{
				alert('這不是一個數字');
				$("#change_num_"+productID).val(0);
				$('#subtotal_'+productID).html(0);
				return
			}
			else if($("#change_num_"+productID).val()<0)
			{
				alert('不可為負值');
				$("#change_num_"+productID).val(0);
				$('#subtotal_'+productID).html(0);
				return
			}

			var subtotal = $('#point_'+productID).html() * $('#change_num_'+productID).val();
		  
			remain = $('#remainPoint').html() - subtotal;
			changeTotal += subtotal;
			if(changeTotal>$('#myPoint').html())
			{
                if(passkey)alert('點數不足，請調整數量!');
                passkey = false;
				passToken = false;
				return
			}
			else passToken = true;
			$('#subtotal_'+productID).html(subtotal);
			$('#remainPoint').html(remain);
			$('#ChangPoint').html(changeTotal)
	})
	
	
	
	
}

function changeSubmit()
{
	changeTotal();
	if(passToken == false) return;
	alert('正在建立你的兌換單...');
	$('#changePointForm').serialize();
    
    
	$.ajax({
	   type: "POST",
	   dataType:"json",

	   url: "/order/change_point_send",
	   data:$('#changePointForm').serialize(),
	   success: function(data){
     ;
		if(data.result==true)
		{
            showOrder(data.output.orderID,'已送達物流','watch');
           changePoint();
        }
           
       }
    })
           
}







function adjustInf(productID,language)
{
	$.post('/order/adjust_amount',{productID:productID},function(data){
  
			if(data.result==true)
			{
				content ='<h1>'+data.shopList[0].ZHName+' - 調貨資訊指南</h1>';

				content+='<img src="/images/adjustFlow.png">'
				content +='<h3>tip:點選店家名稱可查詢店家電話</h3>';
				if(language=='繁中') 
				{
					content+='<h1 style="color:red">此商品為中文商品，請先連絡產品部02-86719616,phantasia.pm@gmail.com<br/>確認到貨時間，再行調貨</h1>';
					alert('此商品為中文商品，請先連絡產品部確認到貨時間，再行調貨')
				}
				for(key in data.shopList)
				{
					if( data.shopList[key].shopID!=0)
					{
                        str = '';
                        if(data.shopList[key].num<=0)
                        {
                            data.shopList[key].num = '無庫存';
                            str = 'color:#BBB';
                        }
					content+='<a target="_blank" href="http://www.phantasia.tw/consumption/view/'+data.shopList[key].shopID+'">'+
							'<li style="list-style-type:none; height:25px;width:185px; margin-right:1px; margin-bottom:5px; padding-top:4px;float:left;'+str+'">'+
							'<img src="http://www.phantasia.tw/images/shop/title/'+data.shopList[key].shopID+'.jpg" style=" height:18px;float:left; margin-left:2px; ">'+
							'<div style="float:left">'+
							data.shopList[key].name+':'+
							data.shopList[key].num+'';
					content+='</div></li></a>'
					}
					
				}
				content+='<div style="clear:both"></div>';
				openPopUpBox(content,1000,500,"closePopUpBox");
				
			}
		
	},'json')
	
}




function loadingShoppingData(onPage,queryData,async)
{
	
	if(contentList[onPage] &&contentList[onPage]!='') return;

	$.ajax({
	   type: "POST",
	   dataType:"json",
	   async:async,
	   url: "/order/search_product",
	   data:queryData,
	   success: function(data){
		if(data.result==true)
		{
			
			selectDataList[onPage] = data.product;
			contentList[onPage]  ='';
			for( var key in data.product)
			{	
				contentList[onPage] += shoppingFrame(data.product[key],key,onPage,true);;

				//!!!!checkIsConsignment(data.product[key].productID,'shopping_'+data.product[key].productID);
				if($('#orderShowWay').val()==1)
				{
					$('.productBox').css('float','none');
				
					$('.shoppingBox').css('float','none');
					$('.shoppingBox li').css('float','left');
					
						
					
					
					
				}	
			}
			
		}
	   }
	})	
	
	
	
	
	
}

var recommandData;
function loadingRecommandData()
{
	var queryData = {queryString:'',openStatus:1,start:0,num:50,recommand:1};
	
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/search_product",
	   async:true,
	   data: queryData,
	   success: function(data){
		if(data.result==true)
		{
			recommandData = data.product;
			recommandMax = data.product.length;
			recommandIndex = 0;
			 recommandFrame();
		}
	   }
	})		
	
}

var recommandIndex = 0;
var recommandShow = 3;
var recommandMax = 0
function recommandFrame()
{
	var index = recommandIndex
	$('#shoppingRecommand').html('');
	for(var i=1;i<=recommandShow;i++)
	{
		index = (index)%recommandMax;
		$('#shoppingRecommand').append(shoppingFrame(recommandData[index],index,0,false));
		index++;
	}
	
	
}

function recommandMove(dir)
{
	if(dir=='left')recommandIndex+=recommandMax-recommandShow;
	else recommandIndex+=recommandShow;
	
	 recommandFrame();
}




function consignmentNum()
{
	$('#consignmentContnent').html('<img src="/images/ajax-loader.gif"/>');
	$.post('/order/get_consignment_num',{year:$('#consignmentYear').val(),month:$('#consignmentMonth').val()},function(data)
	{
		
		if(data.result==true)
		{
			$('#consignmentContnent').html('');
			for(key in data.product)
			{
				$('#consignmentContnent').append(
				'<input type="button" value="列印" onclick="">'+
				'<div>'+data.product[key]+'<div>'
				)
				
				
				
				
			}
			
			
		}
		
	},'json')
	
	
	
	
	
	
	
}





function consignmentClose()
{
	$('#consignmentContnent').html('<img src="/images/ajax-loader.gif"/>');
	$.post('/order/get_consignment_factory',{year:$('#consignmentYear').val(),month:$('#consignmentMonth').val()},function(data)
	{
		
		if(data.result==true)
		{
				$('#consignmentContnent').html('');
			for(key in data.product)
			{
				$('#consignmentContnent').append(
				'<input type="button" value="列印" onclick="">'+
				'<div>'+data.product[key]+'<div>'
				)
				
				
				
				
			}
			
			
		}
		
	},'json')
	
	
	
	
	
	
	
}



	






function updateCollect(id)
{
	var comment = $('#comment_'+id).val();
	var target = $('#target_'+id).val();
	var deadline = $('#deadline_'+id).val().replace('/','-');
	var deadTime = $('#deadTime_'+id).val();
	var joinComment = $('#joinComment_'+id).val();
	var outComment = $('#outComment_'+id).val();
	
	$.post('/order/update_collect',
		   {id:id,comment:comment,target:target,deadline:deadline,
								   deadTime:deadTime,joinComment:joinComment,outComment:outComment},function(data)
		   			{
						if(data.result==true)
									{

										alert('儲存完畢');


									}
		  
		
			
					}
		  
		  			
		  
		  
		  ,'json')
	
	
}

function collectOrderTransfer(id)
{
	var	 content ='<h1>正在進行訂單的轉換...請稍候</h1>'+			
				'<div id="orderTransfer"><h1>請勿關閉視窗</h1><img src="/images/ajax-loader.gif"></div>';
			 openPopUpBox(content,1100,280,'openPopUpBox');

					popUpBoxHeight(0);
	
	
	$.post('/order/collect_order_transfer',{id:id},function(data)
	{
		
		
		if(data.result==true)
			{
				$('#orderTransfer').html('訂單轉換完成<br/>');
				for(key in data.orderList)
				{
						$('#orderTransfer').append('訂單編號:o'+data.orderList[key]+'<br/>');
						
				}
				
				$('#orderTransfer').append('請關閉視窗');
					popUpBoxHeight(0);
				
			}
		
		
		
	},'json')
	
	
	
	
}
var collectoffect = 0;
function nextCollect()
{
	

	loadCollect(collectoffect++,'edit');
	
}

function getCollect(offect)
{
    
	collectoffect = 0;
      $('#product_list').html('<input type="button"  class="big_button"  value="新增集單內容" onclick="creatCollectOrder()"/ >');
				$('#product_list').append('<div id="newDistributeForm" ></div><table id="collectOrderTable" border="1" width=1250></table>'+
								'<input type="button"  id="loadMore" class="big_button" value="查看更多" onclick="nextCollect()">');
		nextCollect();	
    
 
}
function orderCollect(token)
{
    if(token==true) $('#product_list').html('');

	$('#orderCollect').html('');
	loadCollect(0,'show');
	
	
	
}
			

           
function  loadCollect(offset,type)
    {
        
        
         $.post('/order/get_collect',{offset:offset,type:type},function(data){
        
        if(data.result==true)
            {
                
      			if(type=='edit')
				{
					for( var key in data.collectOrder)
					{
						orderCollectEditAppend(data.collectOrder[key],'append')
					}
				
				
				}
				else
				{
					for( var key in data.collectOrder)
					{
						content='<div class="productBox" style="height:auto">';
                        content+='<h1>截止時間：'+data.collectOrder[key].deadline+' '+data.collectOrder[key].deadTime+'</h1>';		
						content+='<div id="progressbar_'+data.collectOrder[key].id+'"> <div style="float: left;            margin-left: 20%;margin-top: 5px;font-weight: bold;text-shadow: 1px 1px 0 #fff; " class="progress-label" id="progressbar_'+data.collectOrder[key].id+'_bar"></div></div>'+
									'<div style="float:left">你目前的訂購數量</div>'+
						'<div class="btn_minus" onclick="changeAmount(0,\'purchase_num_'+data.collectOrder[key].id+'\')" style=" margin-left:10px;"></div>'+
					'<div style="float:left"><input type="num" class="short_text" value="0"  id="purchase_num_'+data.collectOrder[key].id+'" name = "purchase_num_'+data.collectOrder[key].id+'" '+
					'onclick="$(\'#purchase_num_'+data.collectOrder[key].id+'\').select()" onblur="chkMinus(\''+data.collectOrder[key].id+'\')" /></div>'+
					'<div class="btn_plus" onclick="changeAmount(1,\'purchase_num_'+data.collectOrder[key].id+'\')"></div>';    
                            if(data.collectOrder[key].orderToken)
						content+=
						'<input type="button" class="big_button" onclick="updateCollectNum('+data.collectOrder[key].id+','+data.collectOrder[key].target+')" value="儲存" style="float:left;margin-top:0px">'	
						else content+='<span>已截止</span>';
						;
                            
                        content+= '<div style="clear:both"></div>'+nl2br(data.collectOrder[key].comment);
					
						if($('#myshopID').val()<1000) content+="<br/>===<br/>"+nl2br(data.collectOrder[key].joinComment);
						else content+="<br/>===<br/>"+nl2br(data.collectOrder[key].outComment);
						

						
						
						$('#orderCollect').append(content);
						
						getCollectProgress(data.collectOrder[key].id,data.collectOrder[key].target);
					}
					
				}
                
                
                
            }
        
        
    },'json')
    
        
        
        
        
    }


function orderCollectEditAppend(collectOrderData,type)
{
	if(collectOrderData.status==0)
	 {
	   	var openBtn = '<input type="button" class="big_button" value="關閉" 		onclick="openCloseCollect('+collectOrderData.id+',1)">';
	   
		var openCss = '';
	   
	 }
	else{
		
		var openBtn = '<input type="button" class="big_button" value="開啓" 		onclick="openCloseCollect('+collectOrderData.id+',0)">';
		
		var openCss = 'background-color:gray';
	}
	
	
	if(collectOrderData.productID!=0)
		{
			
			var btn = '<br/>商品已綁定:'+collectOrderData.ZHName+
					'<input type="button" class="big_button" value="轉換定單" onclick="collectOrderTransfer('+collectOrderData.id+')">'
			
			
		}
	else var btn ='';
		content = '<tr>'+
						'<td>集單內容</td>'+
						'<td>集單目標數量</td>'+
						'<td>集單到期日中止時間</td>'+
						'<td>加盟店訊息</td>'+
						'<td>非加盟店訊息</td>'+
						'<td>進度</td>'+
						'<td></td>'+
						
					'</tr>'+
						'<tr style="'+openCss+'">'+
							'<td ><textarea style="width:300px;height:300px" id="comment_'+collectOrderData.id+'">'+
							collectOrderData.comment+
							'</textarea>'+
							'</td>'+
							'<td ><input type="num" class="short_text" id="target_'+collectOrderData.id+'" value="'+collectOrderData.target+'"></td>'+
							 '<td ><input type="date" id="deadline_'+collectOrderData.id+'" value="'+collectOrderData.deadline+'">'+
							 '<br/>終止時間<br/><input type="time" id="deadTime_'+collectOrderData.id+'" value="'+collectOrderData.deadTime+'"></td>'+
							'<td ><textarea style="width:200px;height:200px" id="joinComment_'+collectOrderData.id+'">'+ collectOrderData.joinComment+'</textarea></td>'+
							'<td ><textarea style="width:200px;height:200px" id="outComment_'+collectOrderData.id+'">'+
							collectOrderData.outComment+'</textarea></td>'+
							'<td id="orderlist_'+collectOrderData.id+'"></td>'+
							'<td>'+
							'<input type="button" class="big_button" value="儲存" 		onclick="updateCollect('+collectOrderData.id+')">'+openBtn+
							
							'<br/><input type="button" class="big_button" value="商品綁定" onclick="productBind('+collectOrderData.id+')">'+btn +
							'</td>'+
						'</tr>'	+
							'<tr>'+
							
							'<td colspan="7" id="progress_'+collectOrderData.id+'">'+
								'<div id="progressbar_'+collectOrderData.id+'"> <div style="float: left;            margin-left: 20%;margin-top: 5px;font-weight: bold;text-shadow: 1px 1px 0 #fff; " class="progress-label" id="progressbar_'+collectOrderData.id+'_bar"></div></div>'+
							
							
							'</td>'+
							'</tr>';
							
				if(type=="append")$('#collectOrderTable').append(content);
				else $('#collectOrderTable').prepend(content);
	
	
						getCollectProgress(collectOrderData.id,collectOrderData.target);

	
	
}

function openCloseCollect(id,openStatus)
{
	
	$.post('/order/collect_order_update',{id:id,status:openStatus},function(data)
	{
		if(data.result==true)
			{
				
				
				getCollect(0);
			}
		
		
		
	},'json')
	
	
	
}


function productBind(id)
{
	var	 content ='<h1>你正在進行商品的綁定</h1><div id="productBindQuery"></div>';
			content +='<input type="hidden" id="collectID" value="'+id+'">';	
			content +='<div id="productBindContent"><table border="1" id="prodcutBindTableContent"></table></div>';
		
			 openPopUpBox(content,1100,280,'productBindSend');
			queryProduct('productBind','select');
			
	
	
					popUpBoxHeight(0);
	
	
	
}

function productBindSend()
{
	$.post('/order/collect_order_update',{id:$('#collectID').val(),productID:$('#bind_productID').val()},function(data)
	{
		if(data.result==true)
			{
				
				closePopUpBox();
				getCollect(0);
			}
		
		
		
	},'json')
	
	
	
}

function productBindTable(data)
{
	$('#prodcutBindTableContent').html('<tr><td>編號</td><td>中文</td><td>英文</td><td>語言</td></tr>');
	$('#prodcutBindTableContent').append('<tr><td><input type="hidden" id="bind_productID" value="'+data.productID+'">'+data.productNum+'</td><td>'+data.ZHName+'</td><td>'+data.ENGName+'</td><td>'+data.language+'</td></tr>');
	
	
	
	
}
function creatCollectOrder()
{
	
	
	$.post('/order/create_order_collect',{},function(data)
		  {
		
			if(data.result==true)
				{
					
					orderCollectEditAppend(data.collect,'prepend');
					
				}
		
		
	},'json')
	
}

function updateCollectNum(id,target)
{
	loginChk();
	$.post('/order/update_collect_num',{id:id,num:$('#purchase_num_'+id).val()},function(data){
		
		if(data.result==true)
			{
				getCollectProgress(id,target);
				
				
			}
		
	},'json')
	
	
	
	
}
function getCollectProgress(id,max)
{
	$('#orderlist_'+id).html('');
	$.post('/order/get_collect_progress',{id:id},function(data){
		if(data.result==true)
			{
				
				
				progress("progressbar_"+id,0,Math.round(data.total*100/max),data.total,max);
				for(key in data.collectOrderList)
				{
					$('#orderlist_'+id).append(data.collectOrderList[key].shopName+':'+data.collectOrderList[key].num+'<br/>');
					if(data.collectOrderList[key].shopID==$('#myshopID').val())
						{
							$('#purchase_num_'+id).val(data.collectOrderList[key].num)
							
						}
					 
					
					
					
				}
				if($('#orderlist_'+id).html()=='')$('#orderlist_'+id).html('無人訂購');
			}
		
		
	},'json')
	
	
	
}
function  getRumi()
{
	content = '讀取中，請稍候...<img src="/images/ajax-loader.gif"/>';
	openPopUpBox(content,600,400,'closePopUpBox');
	$.post('/order/get_rumi',{},function(data)
	{

		if(data.result==true)
		{
			content = '<h1>拉密世界大賽參賽資格</h1><table id="rumiTable" border="1"></table>';
			openPopUpBox(content,600,400,'closePopUpBox');
			$('#rumiTable').append('<tr><td>品名</td><td>2019/6/1後已出貨數量</td><td>金額</td><td>訂購或運送中數量</td><td>金額</td><td>小計</td></tr>');
			var total = 0;
			for(key in data.rumkub)
				{
					var subtotal = parseInt(data.rumkub[key].sellnum.sellTotal)+parseInt(data.rumkub[key].orderingNum.sellTotal);
					$('#rumiTable').append('<tr>'+
							'<td>'+data.rumkub[key].name+'</td>'+
							'<td>'+data.rumkub[key].sellnum.orderingNum+'</td>'+
							'<td>'+data.rumkub[key].sellnum.sellTotal+'</td>'+
							'<td>'+data.rumkub[key].orderingNum.orderingNum+'</td>'+
							'<td>'+data.rumkub[key].orderingNum.sellTotal+'</td>'+
							'<td>'+subtotal+'</td></tr>');
					total += subtotal;
					
				}
			str = '<h1>目前訂購總金額為'+total+'</h1>';
			if(total>=18000)	str+='<h1>已符合資格</h1>';
			else str+='<h1>還差'+(18000-total)+'方符合辦賽資格</h1>';
			$('#rumiTable').after(str);
			
			
		}
		
	},'json')
	
	
	
}

function getRumiFrame()
{
	

	content = '<h1>拉密世界大賽參賽資格</h1><table id="rumiTable" border="1"></table>';
	openPopUpBox(content,800,400,'closePopUpBox');
	getRumiAll(1)
}
function getRumiAll(shopID)
{
	
	$.post('/order/get_rumi',{shopID:shopID},function(data)
	{

		if(data.result==true)
		{
			
		//	$('#rumiTable').append('<tr><td>品名</td><td>1/1後已出貨數量</td><td>金額</td><td>訂購或運送中數量</td><td>金額</td><td>小計</td></tr>');
			var total = 0;
			for(key in data.rumkub)
				{
					var subtotal = parseInt(data.rumkub[key].sellnum.sellTotal)+parseInt(data.rumkub[key].orderingNum.sellTotal);
					/*
					$('#rumiTable').append('<tr>'+
							'<td>'+data.rumkub[key].name+'</td>'+
							'<td>'+data.rumkub[key].sellnum.orderingNum+'</td>'+
							'<td>'+data.rumkub[key].sellnum.sellTotal+'</td>'+
							'<td>'+data.rumkub[key].orderingNum.orderingNum+'</td>'+
							'<td>'+data.rumkub[key].orderingNum.sellTotal+'</td>'+
							'<td>'+subtotal+'</td></tr>');
					*/	
					total += subtotal;
					
				}
			str = '<tr><td>'+data.shopInf.name+'</td><td><h1>目前訂購總金額為'+total+'</h1></td>';
			if(total>=18000)	str+='<td><h1>已符合資格</h1></td>';
			else str+='<td><h1>還差'+(18000-total)+'方符合辦賽資格</h1></td>';
			$('#rumiTable').append(str);
			if(shopID<50)getRumiAll(shopID+1);
			
		}
		
	},'json')
	
	
	
	
	
}




function progress(id,now,max,num,target)
{
	
	setTimeout(function(){
		
		if(now<=max)
		{
			diff = target - num;
			
		
		
			if(num >= target)
			{
				
				$('#'+id+'_bar').html(num+'/'+target+' '+now+'% 集單完成！！！');
				
			}
			else 	$('#'+id+'_bar').html(num+'/'+target+' '+now+'% 距離集單目標尚有：'+diff+'套');
			
			if(now > 100 )pro = 100;
			else pro = now;
				$("#"+id).progressbar({
						  value: pro
						
						});
			progress(id,now+1,max,num,target);
			
			
			
		}	
		
		
	},10)
	
	
}

function nl2br (str, is_xhtml) {   
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

//===========================orderback function end
function addressDelete(id,shopID)
{
	
	if(confirm('您確定要刪除這個地址?'))
	$.post('/order/delete_address',{id:id},function(data)
	{
	
		if(data.reuslt==true)
		{
				
			 shippingAddress(shopID)
		}
		
	},'json')
	
	
}
function addressUpdate(id,shopID)
{
	if($('#default_'+id).is(':checked'))isCheck = 1;
	else isCheck = 0;
	
	$.post('/order/update_address',{id:id,shopID:shopID,receiver:$('#receiver_'+id).val(),address:$('#address_'+id).val(),phone:$('#phone_'+id).val(),comID:$('#comID_'+id).val(),defaultToken:isCheck},function(data)
	{
			if(data.result==true)	$('#editMsgIn').html($('#receiver_'+id).val()+' 資料已儲存');
			else $('#editMsgIn').html('儲存失敗，請洽管理員');
			setTimeout(function(){$('#editMsgIn').detach();},2000);		
		
	},'json')	
	
}
function shippingAddress(shopID)
{
		if(onShoppingCheck()==false) return;
	else $('#clientOrderQuery').html('');
	$('#carOption').detach();
	$('#clientOrderQuery').html('')
	$('#productQuery').html('');
	$('#productSelect').detach();
	shippingWrong = '';
	waitString ='';


	$.post('/order/get_shop_address',{shopID:shopID},function(data)
	{
		if(data.result==true)
		{
			$('#product_list').html('<table id="addressTable" border="1"></table>')
			$('#addressTable').append('<tr><th>收件人</th><th>地址</th><th>電話</th><th>刪除</th><th>預設(請點選下方圈圈)</th></tr>');
			
			for (key in data.address)
			{
					$('#addressTable').append(
					'<tr>'+
					'<td><input type="text" onfocus="showEditMsg('+data.address[key].id+')" onblur="addressUpdate('+data.address[key].id+','+shopID+')" class="medium_text"  id="receiver_'+data.address[key].id+'" value="'+data.address[key].receiver+'"></td>'+
					'<td><input type="text"  onfocus="showEditMsg('+data.address[key].id+')"  onblur="addressUpdate('+data.address[key].id+','+shopID+')"  class="big_text"  id="address_'+data.address[key].id+'"value="'+data.address[key].address+'"></td>'+
					'<td><input type="text"  onfocus="showEditMsg('+data.address[key].id+')"  onblur="addressUpdate('+data.address[key].id+','+shopID+')"  class="medium_text"  id="phone_'+data.address[key].id+'" value="'+data.address[key].phone+'"></td>'+
                     '<td><input type="text"  onfocus="showEditMsg('+data.address[key].id+')"  onblur="addressUpdate('+data.address[key].id+','+shopID+')"  class="medium_text"  id="comID_'+data.address[key].id+'" value="'+data.address[key].comID+'"></td>'+
                       '<td><input type="text"  onfocus="showEditMsg('+data.address[key].id+')"  onblur="addressUpdate('+data.address[key].id+','+shopID+')"  class="big_text"  id="email_'+data.address[key].id+'" value="'+data.address[key].email+'"></td>'+  
                        
                        
                        
                    '<td>客戶載具：<select name="CarrierType" id="CarrierType_'+data.address[key].id+'">'+
                '<option value="">無</option>'+
                '<option value="3J0002">手機條碼</option>'+
                '<option value="CQ0001">自然人憑證</option>'+
                '</select> <input type="text" onfocus="showEditMsg('+data.address[key].id+')"  onblur="addressUpdate('+data.address[key].id+','+shopID+')" class="big_text" name="CarrierId1" id="CarrierId1_'+data.address[key].id+'" placeholder="載具條碼無則空白" value="'+data.address[key].CarrierId1+'" >'+
                '<td>捐贈代碼：<input type="text"  onfocus="showEditMsg('+data.address[key].id+')"  onblur="addressUpdate('+data.address[key].id+','+shopID+')"  class="big_text"  id="NPOBAN_'+data.address[key].id+'" value="'+data.address[key].NPOBAN+'" placeholder="無則空白"></td>'+     
                    
           
                      
					'<td><input type="button" onclick="addressDelete('+data.address[key].id+','+shopID+')" value="刪除"></td>'+
					'<td><input type="radio" onclick=" showEditMsg('+data.address[key].id+');addressUpdate('+data.address[key].id+','+shopID+')" style=" font-size:18pt" name="defaultAddress" id="default_'+data.address[key].id+'" value="'+data.address[key].id+'"></td>'+
					'</tr>');
					if(data.address[key].defaultToken==1)
					{
						$('#default_'+data.address[key].id).attr("checked", true);
					
					}
                    $('#CarrierType_'+data.address[key].id).val(data.address[key].CarrierType);
			}
			
		}
		
		
		
		
	},'json' )





}

function consignmentPatch(productID,num,shopID)
{
    $.post('/order/consignment_patch',{productID:productID,num:num,shopID:shopID},
           function(data)
	{
        if(data.result==true)   
            {
                
                alert('完成');
                
            }
        
    },'json')
    
    
    
}
//ec
function ecPlatformSelector(platformID)
{
    
     $.post('/order/get_ec_platform',{},function(data){
        
        if(data.result==true)
        {
               
                for(key in data.platform)
                    {
                    
                       
                        $('#ECOrderPlatformID').append('<option value="'+data.platform[key].platFormID+'">'+data.platform[key].platformName+'</option>')
                    }
                $('#ECOrderPlatformID').val(platformID);
                
            }
        
        
        
    },'json')

    
    
}


function ecView()
{
	  $('#product_list').html('<h1>電子商務管理</h1><div id="ecCanvas"></div><div id="ecContent"></div>');
      $('#ecCanvas').html('<input type="button" value="新增電商訂單" onclick="newECOrder();" class="big_button">');
    
    
    $.post('/order/get_ec_platform',{},function(data){
        
        if(data.result==true)
        {
                $('#ecCanvas').append('<select id="ECStatus"><option value="-1">不出貨</option><option value="0">全部</option></select>'+
                                      '<select id="ECplatform"><option value="0">全部</option></select>'+
                                      
                                      '<input type="button" value="全部" class="big_button" onclick="getPlatFormOrder(0,0)">');
                for(var i=1;i<=8;i++)  $('#ECStatus').append('<option value="'+i+'">'+ECStatusTrans(i)+'</option>')
                 
                for(key in data.platform)
                    {
                    
                        $('#ecCanvas').append('<input type="button" value="'+data.platform[key].platformName+'" class="big_button" onclick="getPlatFormOrder('+data.platform[key].platFormID+',0)">');
                        $('#ECplatform').append('<option value="'+data.platform[key].platFormID+'">'+data.platform[key].platformName+'</option>')
                    }
                
                
            }
        
        
        
    },'json')
}
 function ECStatusTrans(ECStatus)
{
   
    switch(parseInt(ECStatus))
        {
                
            case -1: return '不出貨';
            case  1: return '待出貨'; 
            case  2: return '貨物運送中'; 
            case  3: return '貨品抵達';     
            case  4: return '待取貨';     
            case  5: return '已完成取貨'; 
            case  6: return '退貨'; 
            case  7: return '取消訂單'; 
            case  8: return '準備中';     
        }
    
    
    
}
	//-1.不出貨, 1待出貨,2貨物運送中 3.貨品抵達 4.待取貨 5.已完成取貨 6.退貨 7.取消訂單 8.準備中
function getPlatFormOrder(id,status)
{
    $('#ECplatform').val(id);
    $('#ECStatus').val(status);
    
   $('#ecContent').html('<img class="s" src="/images/ajax-loader.gif">');
        $.post('/order/get_ec_platform_order',{id:id,status:status,fromDate:$('#fromDate').val(),toDate:$('#toDate').val()},function(data){
        
        if(data.result==true)
            {
                $('#ecContent').html(ecOrderListTable())
                 for( var key in data.platformOrder)
                    {
                         if(data.platformOrder[key].payway==1)var payway ='貨到付款';
                            else var payway = '';
 
                        var content ='<tr id="order_'+data.platformOrder[key].ECID+'">';
                            content+=
                                '<td id="orderNum_'+data.platformOrder[key].ECID+'">o'+data.platformOrder[key].orderNum+'</td>'+
                                '<td>'+data.platformOrder[key].orderTime+'</td>'+
                                '<td id="shopName_'+data.platformOrder[key].ECID+'">'+data.platformOrder[key].platformName+'</td>'+
                                '<td id="ECOrderNum_'+data.platformOrder[key].ECID+'">'+data.platformOrder[key].ECOrderNum+'</td>'+
                                '<td id="shopName_'+data.platformOrder[key].ECID+'">'+data.platformOrder[key].receiverName+'</td>';
                           content+='<td id="orderType_'+data.platformOrder[key].ECID+'">'+data.platformOrder[key].transportName+'</td>'+
                                    '<td>'+data.platformOrder[key].total+'</td>'+
                                '<td>'+data.platformOrder[key].orderComment+'</td>'+
                                '<td>'+data.platformOrder[key].orderStatus+'</td>';
                            content+='<td><input type="number" style=" width:90px"  id="shipmentCode_'+data.platformOrder[key].ECID+'" value="'+data.platformOrder[key].trackingNumber+'"  onblur="editECOrder('+data.platformOrder[key].ECID+')" onfocus="showEditMsg('+data.platformOrder[key].ECID+',\'EC\')" ></td>'+
						'<td>'+payway+'<input type="text" style=" width:90px"  id="charge_'+data.platformOrder[key].ECID+'" value="'+data.platformOrder[key].charge+'"  onfocus="showEditMsg('+data.platformOrder[key].ECID+',\'EC\')" ></td>'+
						'<td><input type="text" style=" width:90px"  id="note_'+data.platformOrder[key].ECID+'" value="'+data.platformOrder[key].note+'"  onfocus="showEditMsg('+data.platformOrder[key].ECID+',\'EC\')" onchange="editECOrder('+data.platformOrder[key].ECID+')"></td>'+
					
						'<td>';
                        if(data.platformOrder[key].platformName=='官網')
                        {
						content+=	'<input type="button" id="invBtn_'+data.platformOrder[key].ECID+'" value="發票" onclick="getInvoice('+data.platformOrder[key].ECID+','+data.platformOrder[key].total+','+data.platformOrder[key].shopID+',\'EC\')"class="big_button" >';
                        }
						content+='</td>'+    
                               '<td>'+
                                    '<input type="hidden" id ="ordetType_'+data.platformOrder[key].id+'" value="'+data.platformOrder[key].type+'">'+	
                                    '<input type="button" value="查看" onclick="showOrder('+data.platformOrder[key].id+',\''+data.platformOrder[key].status+'\',\'watch\')"class="big_button" >'+
                                '</td>';

                        content+='<td><input type="button" value="刪除" onclick="deleteOrder('+data.platformOrder[key].id+')"class="big_button" ></td>';

                                content+='</tr>';

                        $('#order_list').append(content)
                            checkInvoice(data.platformOrder[key].ECID,data.platformOrder[key].total,data.platformOrder[key].shopID,data.platformOrder[key].finishTime);
                        
                   if($('#myshopID').val()==0)	
                    {
                        var ID = data.platformOrder[key].ECID;

                        var dates = $( "#charge_"+data.platformOrder[key].ECID ).datepicker({
                                dateFormat: 'yy-mm-dd' ,
                                yearRange: '1930',
                                monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
                                changeMonth: true,
                                numberOfMonths: 1,
                                onSelect: function( selectedDate ) {
                                    var option = this.id == "from" ? "minDate" : "maxDate",
                                        instance = $( this ).data( "datepicker" ),
                                        date = $.datepicker.parseDate(
                                            instance.settings.dateFormat ||
                                            $.datepicker._defaults.dateFormat,
                                            selectedDate, instance.settings );
                                    dates.not( this ).datepicker( "option", option, date );
                                },
                                onClose:function()
                                {


                                        editEcOrder(focusID);

                                }

                            });		  	

                    }

                        

                    }
                        
                        
                    }
            
        },'json')
               
	
}
    
    
function ecOrderListTable()
    {
        
        var  result='';
	 result+='<h1>訂貨中清單</h1>';
	result+='<form id="shipmentListForm"  method="post" action="/order/select_print_out" target="_blank"><input type="hidden" id="showType" name = "showType" value="boxin"><table id="order_list" border="1" style="text-align:center; width:1150px">'+
				'<tr>';
			
	
		result+='<td>訂單編號</td>';
		result+='<td>下單日期</td>';
	
					
			result+='<td>平台名稱</td>'+
                    '<td>平台編號</td>'+
					'<td>出貨地點</td>'+
					'<td>寄送方式</td>'+
					'<td>訂單總價</td>'+
					'<td>訂單備註</td>';
					
					

		result+= 	'<td>訂單狀況</td>'+					
					'<td>物流單號</td>'+
					'<td>收款日期</td>'+
					'<td>備查</td>';	
		
	

		result+='<td>發票</td>';
		result+='<td>查看訂單</td>';
	if($('#myshopID').val()==0)result+='<td>刪除訂單</td>';
					
			result+='</tr>'+
			'</table>';
	result+='</form>';
	
	
	return result;
        
        
    }

function newECOrder()
{
    content ='<input type="hidden" value="666" id="create_shopID">'+
             '<input type="hidden" value="0" id="create_type">';
    content += '資料傳送中，請稍候..<img src="/images/ajax-loader.gif"/>';
     openPopUpBox(content,200,100,'closePopUpBox');
   createSend();
  
    
}
function  ECOrderUpdate()
{    
    
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/ec_order_update",
	   data: $("#ECOrderForm").serialize(),
	   success: function(data){
		   
        
		   if(data.result ==true) 
		   {
			     closePopUpBox();
		   }
	   }
	   
	   })
}

$(document).ready(function(){
	 productType();	
	    orderCollect(false);
	 
$(window).bind('beforeunload',function()
{
	if(onShopping==true) return '您正在購物中';
})

$(document).keydown(function(event){  
   		if(event.which == "37") {  $(".previous").click(); return;}  
        if(event.which == "39") {  $(".next").click(); return;}  
});  		
})

