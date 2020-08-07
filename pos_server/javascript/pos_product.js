// JavaScript Document
var productTypeOption;
var shipmentCount;
var accountLevel = 100;
var classZh1=new Array('編輯','產序','櫃號','編號','分類','條碼','中文','英文','語言','適用卡套','價錢','進貨折數','進貨價格','供應商','出版商','最低折數','近期入倉','現在庫存','分配','進銷存','銷量','刪除','狀態','備註','查看網頁','各店庫存','商品上架');
var classEng1 = new Array('editBtn','productID','cabinet','productNum','category','barcode','ZHName','ENGName','language','cardSleeveInf','price','buyDiscount','buyPrice','suppliersName',
'publisher','minDiscount','recent','nowNum','allocate','pos','saleDiagram','delete','openStatus','comment','bidExist','shopStock','uploadMart');
var classZh2=new Array('編號','中文','英文','語言','價錢','出貨折數','出貨價格');
var classEng2 = new Array('productNum','ZHName','ENGName','language','price','purchaseCount','purchase');

var classZh3=new Array('櫃號','編號','中文','英文','語言','現在庫存',);
var classEng3 = new Array('cabinet','productNum','ZHName','ENGName','language','nowNum');
var classZh4=new Array('編輯','櫃號','編號','中文','英文','語言','現在庫存','查看網頁','銷量');
var classEng4 = new Array('editSupplierBtn','cabinet','productNum','ZHName','ENGName','language','nowNum','bidExist','saleDiagram');
var classZh = classZh1;
var classEng = classEng1;
var classHeader = new Array(15);
var publisher;
var productIndex =0 ;
jQuery.fn.Scrollable = function(tableHeight, tableWidth) {
	this.each(function(){
		if (jQuery.browser.msie || jQuery.browser.mozilla) {
			var table = new ScrollableTable(this, tableHeight, tableWidth);
		}
	});
};


function showProductIOLink(productID)
{
    var param = { 'from':$('#from').val(),'to':$('#to').val(),'productID':productID
       }
   
    $.post('/product_flow/pos_io',param,function(data)
          {
  
            if(data.result==true)
                {
                    
                         
                    var totalIn = parseInt(data.p.purchaseNum)+parseInt(data.b.backNum)+parseInt(data.a.adjustNum);
                    if(parseInt(data.o.num)+totalIn== parseInt(data.s.sellNum)+parseInt(data.e.num))
                        {
                             checkV  = '正確';
                        }
                    else
                    {
                         var e = parseInt(data.o.num)+totalIn-data.s.sellNum;
                        checkV  = '錯誤';
                        checkV += ' 期末應為'+e
                        
                    }
                    
                    var newAvgCost =Math.round( (data.o.avgCost*data.o.num+data.p.purchaseNum*data.p.purchasePrice)*100/(parseInt(data.o.num)+parseInt(data.p.purchaseNum)),)/100;
                    
                    var sellTotal = -parseInt(data.b.purchasePrice) - parseInt(data.a.purchasePrice) +parseInt(data.s.sellPrice);
                    var sellNum =   -parseInt(data.b.backNum) - parseInt(data.a.adjustNum) + parseInt(data.s.sellNum);
                    var profit = sellTotal - sellNum*newAvgCost;
                    var profitRatio = Math.round(profit*100 / sellTotal,-2);
                
                    $('#posResult').html(
                        data.from+'~'+data.to+
                        '<table border="1" align="center">'+
                            '<tr><td>項目</td><td>數量</td><td>成本</td><td>售出</td>'+
                            '<tr><td>期初+</td><td>'+data.o.num+'</td><td>'+data.o.avgCost+'</td><td></td></tr>'+
                            '<tr><td></td><td></td><td></td><td></td><td></td></tr>'+
                            '<tr><td>進貨+</td><td>'+data.p.purchaseNum+'</td><td>'+data.p.purchasePrice+'</td><td></td></tr>'+
                            '<tr><td></td><td></td><td></td><td></td></tr>'+
                            '<tr><td>退貨+</td><td>'+data.b.backNum+'</td><td></td><td></td></tr>'+
                            '<tr><td>調貨+</td><td>'+data.a.adjustNum+'</td><td></td><td></td></tr>'+
                            '<tr><td>出貨-</td><td>'+data.s.sellNum+'</td><td></td><td>'+data.s.sellPrice+'</td></tr>'+
                            '<tr><td>期末</td><td>'+data.e.num+'</td><td>'+data.e.avgCost+'</td><td></td></tr>'+
                            '<tr><td></td><td></td><td>'+newAvgCost+'</td><td>'+sellTotal+'<br/>'+
                                '總毛利：'+profit+'<br/>'+
                                '毛利率：'+profitRatio+
                        
                        
                            '%</td></tr>'+
                            '<tr><td>檢核</td><td colspan="3">'+checkV+'</td></tr>'+
                        '</table>'
                       
                        
                 
                        );
                    
                        
                        
                        
                     popUpBoxHeight(0);
                    
                    
                    
                    
                }
        
        
        
    },'json')
    
   // window.open('/product_flow/posIO'+param,'_blank');
    
    
}

function showAccountProductIO(productID)
{
	
	
	
	
	
}

function getShopList(id,all)
{
    
    
    $.post('/system/get_shop',{token:1},function(data){
				if(data.result==true)
				{
					content='<h1>請選擇店家</h1>'+
							'<select id='+id+'"_shopID" name="shopID"></select>';
					$('#'+id).html(content)
;					
					if(all==1)$('#'+id+'_shopID').append('<option value="0">全部</option>');
					for( var key in data.shopData)
					{
						$('#'+id+'_shopID').append('<option value="'+data.shopData[key].shopID+'">'+data.shopData[key].name+'</option>');
						
					}
				}
			
		
		},'json')

    
}
function openPOSDetail(productID)
{
    
    
    
 
    fromtime  = $('#from').val();
       toTime = $('#to').val();
      shopID =  $('#posShop_shopID').val();
    
}

function showPOS(productID,ZHName)
{
    
                     	content='<h1>'+ZHName+' 的進銷存查詢</h1><h2>請選擇月份區間</h2>from<input type="text" id="from">'+' to<input type="text" id="to">'+
						'<input type="button" class="big_button"   value="查詢" onclick=";showProductIOLink(\''+productID+'\')"/>'+
                        '<div id="posResult"></div> '+
                        '<div id="posShop"></div>'    
                        '<input type="button" value="查看明細" onclick="openPOSDetail('+productID+')">' ;
                        
                        ;
		      openPopUpBox(content,600,280,'showProductIO');	
                    $('#popUpBoxEnter').hide();
				getShopList('posShop',1);
			
				today = new Date();
				toDate = today.getFullYear()+'-'+(parseInt(today.getMonth()+1));
				lastday = new Date();
				lastday.setMonth(lastday.getMonth())
				fromDate = lastday.getFullYear()+'-'+(parseInt(lastday.getMonth()+1));
				$('#from').val(fromDate);
				$('#to').val(toDate);
			var dates = $( "#from, #to" ).datepicker({
						dateFormat: 'yy-mm' ,
						
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
						}
					});		  	
				
				  //$( "#create_shopID" ).combobox();
					showProductIOLink(productID);
				

    
  


    
    
}

function fillZero(ret)
{
	if(ret==null) ret=0;
	if (ret<10) return '000'+ret.toString();
	else if (ret<100) return '00'+ret.toString();
	else if (ret<1000) return '0'+ret.toString();
	else return ret.toString();
	
	
	
	
}

var zeroNum = 8;
function turnZeroConfirm()
{
	if(confirm('你確定要歸零嗎？'))	
	{
		var content = '<h1>歸零倒數計時中</h1>';
		zeroNum = 8;
		content+='<h2 id="zeroCount">'+zeroNum+'</h2>';
		openPopUpBox(content,600,280,'turnZero');	
		$('#popUpBoxEnter').hide();
		numCount();
	}
	
	
	
	
	
}

function numCount()
{
	zeroNum--;
	$('#zeroCount').html(zeroNum);
	if(zeroNum==0)
	{
		$('#zeroCount').html('點選確定後刪除');
		$('#popUpBoxEnter').show();
	}
	else setTimeout("numCount()",1000);
	
}

function turnZero()
{
	if(zeroNum==0)
	{
	$.post('/product/turn_zero',{},function(data){
		
		if(data.result==true)
		{
			alert('系統已歸零');
			closePopUpBox();
			
		}
		
	},'json')	
	}
	
	
}

function editCabinetSend()
{
	$.ajax({
		   type: "POST",
		   dataType:"json",
		   url: "/product/cabinet_send",
		   data: $("#editCabinetForm").serialize(),
		   success: function(data){
			 
			   if(data.result==true)
			   {  
				
						  
					closePopUpBox();
					loadCabinetProduct();
		
				
			   }
			  
		   }
		 });
	
	
}

function loadCabinetProduct()
{
	editCabinetInitial();
	$.post('/product/get_cabinet_product',{cabinet:$('#cabinetSelect').val()}
	,function(data)
	{
		if(data.result==true)
		{
			
			for(key in data.product)
			{
				
				editCabinetTable(data.product[key]);
				
			}
			
		}
		
		
	},'json')	
	
	
	
}




function cabinetRemove(productID)
{
	$.post('/product/cabinet_remove',{productID:productID,cabinet:$('#cabinetSelect').val()}
	,function(data)
	{
		
		if(data.result==true)
		{
			
			$('#editCabinet_'+productID).detach();	
			
		}
		
		
	},'json')
	
	
	
}



function newCabinet()
{
	var newC = prompt('請輸入櫃號');
	$('#cabinetSelect').append('<option value="'+newC+'">'+newC+'</option>');
	$('#cabinetSelect').val(newC)
	
}




function editCabinet()
{
	
	$.post('/product/get_cabinet',{},function(data)
	{
		if(data.result==true)
		{
	
	
	
				var content='<input type="button" value="新增櫃號" class="big_button" onclick="newCabinet()">';
					
				 content += '<form id="editCabinetForm">'+'<h1>目前正在編輯櫃號<select name="cabinetSelect" id="cabinetSelect" onchange="loadCabinetProduct()">';
				 for(key in data.cabinetList)  content +='<option value="'+data.cabinetList[key].cabinet+'">'+data.cabinetList[key].cabinet+'</option>';
				 content += '</select></h1>';
					
					  content+='<div>'+
					   '<div id="editCabinetQuery"></div>'+
					  '<table id="editCabinetTable" border="1" width="1000px"></table>'+
					  '</div>'+
					   '</form>';
		
			 openPopUpBox(content,1200,600,'editCabinetSend');		
			queryProduct('editCabinet','select');	
			editCabinetInitial();
			
		}
		
		
	},'json')	
	
	
	
}
function editCabinetInitial()
{
  $('#editCabinetTable').html(
			'<tr>'+
			'<td>產品編號</td>'+
			'<td>中文名稱</td>'+
			'<td>英文名稱</td>'+
			'<td>語言</td>'+
			'<td>目前數量</td>'+
			'<td>移除</td>'+
			'</tr>'

	
	)	
	
	
	
}



function editCabinetTable(data)
{

	  if($('#editCabinet_'+data.productID).length!=0){
		  alert('商品已在清單中');
		  return;
	  }	
	
	
	
	var num = 0;
		$('#editCabinetTable').append(
		'<tr  id="editCabinet_'+data.productID+'">'+
			'<td>'+data.productNum+'</td>'+
			'<td><input type="hidden" name ="editCabinet_productID_'+data.productID+'" value='+data.productID+'>'+data.ZHName+'</td>'+
			'<td>'+data.ENGName+'</td>'+
			'<td>'+data.language+'</td>'+
			'<td>'+data.nowNum+'</td>'+
			'<td><input type="button" value="移除" onclick="cabinetRemove('+data.productID+')"></td>'+
			'</tr>'
		)
	
	height =parseInt($('.popUpBox').css('height').substr(0,$('.popUpBox').css('height').length-2))
	if(parseInt($('#editCabinetTable').css('height').substr(0,$('#editCabinetTable').css('height').length-2))+350>height) 
	{
		popUpBoxHeight(height+50);
		
		
	}
	
	
		
	
	
}


function classSelectSet()
{
	
	$('#classSelect').append('<div style="position:absolute; right:30px; cursor:pointer; font-weight:bold" onclick="$(\'#classSelect\').hide(\'fast\')">X</div>');
	for(key in classZh)
	{
		if(classEng[key]!='productID'&&classEng[key]!='barcode'&&classEng[key]!='comment')
		{
			$('#classSelect').append(
				'<li><label><input id="class_'+classEng[key]+'" class="class_chk" type="checkbox" checked="checked" value="1">'+classZh[key]+'</label></li>'
			)
		}
		else
		{
			$('#classSelect').append(
				'<li><label><input id="class_'+classEng[key]+'" class="class_chk" type="checkbox"  value="1">'+classZh[key]+'</label></li>'
			)
			
		}
		
	}
	$('#classSelect').append(
	
		'<div style="clear:both">'+
		'<input type="button" value="全部選取" class="big_button" onclick="$(\'.class_chk\').attr(\'checked\',true)">'+
		'<input type="button" value="全部取消" class="big_button" onclick="$(\'.class_chk\').attr(\'checked\',false)">'+
		'</div>'
	)
}


function selectMyClass()
{
	$('#classSelect').html('');
	var myClassSet = $('#myClassSet').val();
	
	if(myClassSet ==1)
	{ 
		classZh=classZh1;
		classEng = classEng1;
	}
	else if(myClassSet ==2)
	{
		classZh=classZh2;
		classEng = classEng2;
	}
    else if(myClassSet ==3)
	{
		classZh=classZh3;
		classEng = classEng3;
	}
     else if(myClassSet ==4)
	{
		classZh=classZh4;
		classEng = classEng4;
	}
	classSelectSet();
}

function getPublisher()
{
	$.post('/product/get_publisher',{},function(data){
		publisher='<option value="">請選擇</option>';
		for (key in data.publisher)
		{
			
			publisher+='<option value="'+data.publisher[key].publisher+'">'+data.publisher[key].publisher+'</option>';
			
		}
	},'json');
	
}


function editCardSleeve()
{

	if(sleeveOption=='')getCardSleeve();

	
	var content ='請選擇修改的卡套<select id="editCardSleeveSelect" onchange="showCardSleeveEditBox(this.value)">'+sleeveOption+'</select>'+
				'<input type="button" value="新增卡套"class="big_button" onclick="showCardSleeveNewBox()">'+
				'<div id="editCardSleeveBox"></div>';
	
				
	 openPopUpBox(content,600,280,'cardSleeveEditFinish');	
	
	
	
	
}


function showCardSleeveEditBox(id)
{
		var content = '請輸入名稱：<input type="text" id="editCardSleeveName" value="'+sleeveList[id]+'">'+
					'<input type="hidden" id="editCardSleeveID" value="'+id+'">'+
					'<input type="button" value="確認"  onclick = "cardSleeveEditSend()"/>'+
					'<input type="button" value="取消" onclick="$(\'#editCardSleeveBox\').html(\'\')" />';
		$('#editCardSleeveBox').html(content);	
}

function  showCardSleeveNewBox()
{
		var content = '請輸入名稱：<input type="text" id="editCardSleeveName" value="">'+
					'<input type="hidden" id="editCardSleeveID" value="0">'+
					'<input type="button" value="確認"  onclick = "cardSleeveEditSend()"/>'+
					'<input type="button" value="取消" onclick="$(\'#editCardSleeveBox\').html(\'\')" />';
		$('#editCardSleeveBox').html(content);			
	
}

function cardSleeveEditSend()
{


	if(!$('#editCardSleeveID').val()||$('#editCardSleeveName').val()=='') return;
	$.post('/product/card_sleeve_edit',{id:$('#editCardSleeveID').val(),name:$('#editCardSleeveName').val()},
		function(data)
		{
			if(data.result==true)
			{
			
					$('#editCardSleeveBox').html('');
					alert('請點選確定後更新');
			}
			
			
		},'json')	
	
	

	
	
}

function showProductConsignment()
{
	$('#product_list').html(
	'<h1>廠商寄賣管理</h1>'+
	'<input type="button" value="寄賣商品補登"  class="big_button" onclick="consignmentRetro()">'+  
    '<input type="text" id="csSupplierSearch" onkeyup=" shopSearch(\'suppliers_option\',\'csSupplierSearch\',\'orderSupplier\',1)" placeholder="搜尋廠商">'+    
        
        
	'供應商<select name="orderSupplier" id="orderSupplier">'+
			suppliers+
			'</select><input type="button" value="查詢" onclick="showConsignment(0)"> <input type="button" onclick="showConsignment(1)" value="列印本頁">'+
	 '<div id="viewList"></div>'
        
	)
    
  
//showConsignment(0)
	
}





function showConsignment(link)
{
    t = $('#product_time').val();
    k=t.split('-');
    $.post('/product/get_consignment_link',{year:k[0],month:k[1],supplierID:$('#orderSupplier').val()},
           function(data){
        
        if(data.result==true)
            {
                
                
                url = data.url;
                 if(link==0)
                {
                $('#viewList').html('<iframe src="'+url+'" style="width:1000px;height:3000px"></iframe>');

                    }
                else
                {
                    window.open(url);



                }
    
                
            }
        
        
        
    },'json')
    
    

   
    
    
}


function consignmentRetro()
{	
	
	var content ='<h2>寄賣商品補登</h2><div id="consignmentAppendQuery"></div>';
	 content +='<div style=" float:left; min-height:100px;">'+
          '<input type="text" id="reSupplierSearch" onkeyup=" shopSearch(\'suppliers_option\',\'reSupplierSearch\',\'orderSupplierRetro\',1)" placeholder="搜尋廠商">'+   
                '供應商<select name="orderSupplier" id="orderSupplierRetro">'+
			suppliers+
			'</select>'+
			  '<form id="consignmentAppendForm">'+
			  '<table id="consignmentAppendTable" border="1" width="1100px">'+
			  	'<tr>'+
					'<td>產品編號</td>'+
					'<td>中文名稱</td>'+
					'<td>英文名稱</td>'+
					'<td>語言</td>'+
					'<td>原價</td>'+
					'<td>產品進價(稅前)</td>'+
				'</tr>'+
			  '</table>'+
			  '</form>'+
			  '</div>'+
			  '<div class="divider" ; style="clear:both"></div>';
	
	
	
	
		 openPopUpBox(content,1100,500,"consignmentAppendSend");

		queryProduct('consignmentAppend','select');
	
	
}
function consignmentAppendTable(data)
{
	if($('#consignment_product_'+data.productID).length!=0){
				alert('商品已在清單中');
				return;
			}
	
	
	
	$('#consignmentAppendTable').append(
			'<tr ="consignment_product_'+data.productID+'">'+
					'<td>'+data.productNum+'</td>'+
					'<td><input type="hidden" name ="phrchase_productID_'+data.productID+'" value='+data.productID+'>'+data.ZHName+'</td>'+
					'<td>'+data.ENGName+'</td>'+
					'<td>'+data.language+'</td>'+
					'<td>'+data.price+'</td>'+
					'<td><input type="text" class="short_text" value="0"  id="phrchase_num_'+data.productID+'" name = "phrchase_num_'+data.productID+'" '+
					'onclick="$(\'#phrchase_num_'+data.productID+'\').select()" onblur="chkMinus('+data.productID+')" /></td>'+	
			'</td>'+
			'</tr>'
	
	
	
	)
	adjustHeight('#consignmentAppendTable',100,500)//id,addHeight,baseHeight
	
	

	





}

function consignmentAppendSend()
{
	
    if($('#orderSupplierRetro').val()==0) 
	{
		$('#popUpBoxEnter').show()
		alert('請選擇供應商')	;
		
		return ;
		
		
	}
	
    
		$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/consignment_product_send",
	   data: $("#consignmentAppendForm").serialize(),
	   success: function(data){
		
		
		   if(data.result==true)
		   {
			
				 closePopUpBox();
				 	
		   }
		   else 
           {
               $('#popUpBoxEnter').show();
               alert('錯誤，請重新輸入');
           }
	   }
	 });
	
	
	
	
}



function cardSleeveEditFinish()
{
	cardSleeveEditSend();
	closePopUpBox();
	location.reload();
	
}


function setProductTime()
{
	var  time=new Date();
	var month = time.getMonth()+2
	var year = time.getFullYear();
    if(month>12)
        {
            month=1;
            year++;
            
            
        }
	for(i=parseInt(year,10);i>=2011;i--)
	{
		if(i==parseInt(year,10))
		{
			for(j=parseInt(month,10);j>=1;j--)
			{
				var str = i.toString()+'-'+j.toString();
				inOption(str);	
				
			}	
			
		}
		else
		{
			for(j=12;j>=1;j--)
			{
				var str = i.toString()+'-'+j.toString();
				inOption(str);	
			}
		}
	
	}
    
    nowM = time.getMonth()+1;
    $('#product_time').val(time.getFullYear()+'-'+nowM);
	
} 
function productType()
{
	
	productTypeOption ="";
	$.post('/product/get_product_type',{},function(data){
		if(data.result==true)
		{;
			for(key in data.productType)	
			{
				
				productTypeOption+='<option value="'+data.productType[key].typeID+'">'+data.productType[key].name+'</option>';
			}
			
		}
		
		
	},'json');
	
}
function inOption(str)
{
	$('#product_time').append(
	'<option value="'+str+'">'+str+'</option>');
	
}



function getProductByTime()
{
	 findProductStock('product',2,'auto');
	/*
	var time = $('#product_time').val();
	$.post('/product/get_product_list',{time:time},function(data){
		$('#ajaxLoader').html('');
		setProductTable(data.product,1);
		
		
	},'json')	
	*/
	
}

function headerClick(index,e)
{
	
	
	ret = classHeader[index];
	if(!e.shiftKey){
		
		$('.theader').removeClass('headerSortDown');
		$('.theader').removeClass('headerSortUp');
		for(key in classEng)
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
	for(key in classZh)
	{
		if($('#class_'+classEng[key]).is(":checked")) 
		{
			classHeader[key] = -1;
			headerStr +='<th id="head_'+classEng[key]+'">'+classZh[key]+'</th>';	
			IheaderStr +='<th id="Ihead_'+classEng[key]+'" class="theader"  onclick="headerClick('+key+',event)">'+classZh[key]+'</th>';	
		}
	}
	
	
	
	
	
	$('#product_list').html(
		'tips:按住shift鍵可進行多重排序。<span id="pager"></span>'+
		'<table id="product_header_table"  class="tablesorter" style="width:1250px;;text-align:center;position:relative; z-index:2">'+
		'<thead>'+
		'<tr>'+
		 	IheaderStr+
        '</tr>'+
		'</thead>'+
		'<tbody></tbody>'+
		'</table>'+
		'<div style="height:600px;overflow:auto;top:-25px; position:relative">'+
	     '<table id="product_table"  class="tablesorter" style="width:1240px;;text-align:center;">'+
		 '<thead>'+   	
		 '<tr id="head_tr" style="height:10px;">'+
		 	headerStr+
        '</tr>'+
		'</thead>'+
		'<tbody id="product_table_body"></tboday>'+	
	    '</table>'+
		'</div>'
	)	
	
	if(type==1)
	{
		for(key in data)
		{
			productID = data[key].productID;
			 $('#product_table_body').append(
			'<tr id="product_'+data[key].productID+'">'+
			productList(data[key])+
			'</tr>'
			)
             
         checkMart(productID,'uploadMartBtn_'+productID);
		}
	}
	else
	{      productID = data.productID;
			 $('#product_table_body').append(
			'<tr id="product_'+data.productID+'">'+
			productList(data)+
			'</tr>'
			)
		  checkMart(productID,'uploadMartBtn_'+productID);
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
	for(key in classEng)
	{
		$('#Ihead_'+classEng[key]).width($('#head_'+classEng[key]).width());
		$('.td_'+classEng[key]).width($('#head_'+classEng[key]).width());
	}
	$('#head_tr').hide();
	$('#classSelect').hide();	
	
	
}



function productTable(product)
{
			if($('#product_'+product.productID).length!=0){
				alert('商品已在清單中');
				return;
			}	
			 $('#product_table_body').append(
			'<tr id="product_'+product.productID+'">'+
			productList(product)+
			'</tr>'
			);
  
            checkMart(product.productID,'uploadMartBtn_'+product.productID);
        
    
			   $("#product_table").trigger("update"); 

			adjustWidth();
		
}

function shopAmount(productID)
{
	$.post('/order/adjust_amount',{productID:productID},function(data){
  
			if(data.result==true)
			{
				content ='<h1>'+data.shopList[0].ZHName+' - 各店庫存指南</h1>';

				
				content +='<h3>tip:點選店家名稱可查詢店家電話</h3>';
				
				for(key in data.shopList)
				{
					if( data.shopList[key].shopID!=0)
					{
					content+='<a target="_blank" href="http://www.phantasia.tw/consumption/view/'+data.shopList[key].shopID+'">'+
							'<li style="list-style-type:none; height:25px;width:185px; margin-right:1px; margin-bottom:5px; padding-top:4px;float:left">'+
							'<img src="http://www.phantasia.tw/images/shop/title/'+data.shopList[key].shopID+'.jpg" 		onerror="this.src=\'http://www.phantasia.tw/images/logo.ico\'"	style=" height:18px;float:left; margin-left:2px; ">'+
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
	for(key in classZh)
	{
		if($('#class_'+classEng[key]).is(":checked")) 
		{
			
			if(classEng[key]=='editBtn')result +='<td class="td_'+classEng[key]+'"><input type="button" value="編輯" onclick="editBox(\''+product.productID+'\','+row+')"></td>';
			
            	
			else if(classEng[key]=='editSupplierBtn')result +='<td class="td_'+classEng[key]+'"><input type="button" value="編輯" onclick="editSupplierBox(\''+product.productID+'\','+row+')"></td>';
            
            /*else if(classEng[key]=='productNum')result +='<td class="td_'+classEng[key]+'"><input type="text" id="productNum_'+product.productID+'" value="'+product.productNum+'" onchange="editProductNum('+product.productID+',this.value)"></td>';*/
			else if(classEng[key]=='productNum')result +='<td>'+product.productNum+'</td>';
			else if(classEng[key]=='delete')result +='<td class="td_'+classEng[key]+'"><input type="button" value="刪除" onclick="productDelete(\''+product.productID+'\')"></td>';
			else if(classEng[key]=='minDiscount'||classEng[key]=='buyDiscount')result +='<td class="td_'+classEng[key]+'">'+ product[classEng[key]] +'%</td>';	
			else if(classEng[key]=='purchaseCount')
			{

				if(product.concessions!=null) var concessions = product.concessions.split(',');
				else var concessions ={};
			
				if(product.concessionsNum!=null) var concessionsNum = product.concessionsNum.split(',');
				else var concessionsNum ={};				
				result +='<td class="td_'+classEng[key]+'" style=" text-align:left">'+ product[classEng[key]]+'% <br/>';
				if(typeof product['nonJoinPurchaceDiscount']!="undefined" && product['nonJoinPurchaceDiscount']!=0 && $('#shopID').val()==0) 
				{result +=product['nonJoinPurchaceDiscount']+'%<br/>';}
					
				if(concessions.length>0)	
				for(each in concessions)
				{
					result +=	concessions[each]+'% '+concessionsNum[each]+'↑'+'<br/>';
					
				}	
				
				result +='</td>';		
				
				
				
			}
			else if((classEng[key]=='purchase'))
			{
				
				if(product.concessions!=null) var concessions = product.concessions.split(',');
				else var concessions ={};
				result +='<td class="td_'+classEng[key]+'" style=" text-align:right">'+ Math.round((parseInt(product['price'])*parseInt(product['purchaseCount']))/100)
					   +'<br/>';
				for(each in concessions)
				{
					result +=	Math.round((parseInt(product['price'])*parseInt(concessions[each]))/100)
					       +'<br/>';
					
				}	
				result +='</td>';						
				
				
			}
			
			
			else if(classEng[key]=='openStatus'){
				if(product[classEng[key]]==1)result +='<td class="td_'+classEng[key]+'" style="cursor:pointer" onclick="productOpen('+product.productID+',0,'+row+')">開放中</td>';	
				else result +='<td class="td_'+classEng[key]+'" style="cursor:pointer" onclick="productOpen('+product.productID+',1,'+row+')">關閉中</td>';
			}
			else if(classEng[key]=='allocate')result+='<td><input type="button" value="分配" onclick="showAllocate('+product['productID']+')"></td>';
            else if(classEng[key]=='pos')result+='<td><input type="button" value="進銷存" onclick="showPOS('+product['productID']+',\''+product.ZHName+'\')"></td>';
			else if(classEng[key]=='saleDiagram')result+='<td><input type="button" value="銷量" onclick="saleDiagram('+product['productID']+',true)"></td>';
            else if(classEng[key]=='shopStock')result+='<td><input type="button" value="各店庫存" onclick="shopAmount('+product['productID']+'),true"></td>';
			 else if(classEng[key]=='uploadMart')result+='<td><input type="button" value="上傳商城" onclick="uploadMart('+product['productID']+')" id="uploadMartBtn_'+product['productID']+'"></td>';
			
			else if(classEng[key]=='bidExist')
			{
				result +='<td class="td_'+classEng[key]+'">';
				if(product['bidExist']==1&&product['phaBid']!=0)	
				{
					result+='<a  onMouseover ="showImg(\'http://www.phantasia.tw/upload/bg/home/b/'+product['phaBid']+'.jpg\')" href="http://www.phantasia.tw/bg/home/'+product['phaBid']+' " target="_blank">觀看連結</a>';
				
					
				}
				else
				{
					 result+='<input type="button"  onMouseover ="showImg(\'http://shipment.phantasia.com.tw/pos_server/upload/product/img/'+product['productID']+'.jpg\')" value="上傳圖片" onclick="imgUpload('+product['productID']+')">';
					
					 
				}
				result +='</td>';
				
			}
			else if(classEng[key]=='category'){
				result +='<td class="td_handeler td_'+classEng[key]+'"><div class="td_div" id="p_'+x+'_'+row+'" ondblclick="editCategory(\'p_'+x+'_'+row+'\','+product['productID']+')" onclick="changeInput(\'p_'+x+'_'+row+'\',event)" >'+ product[classEng[key]] +'</div>';
				result +='<input type="hidden" name="'+classEng[key]+'" id="v_'+x+'_'+row+'" value="'+ product[classEng[key]] +'">'	;
				result +='</td>';
			}
			else {
				result +='<td class="td_handeler td_'+classEng[key]+'"><div class="td_div" id="p_'+x+'_'+row+'"  onclick="changeInput(\'p_'+x+'_'+row+'\',event)" >'+ product[classEng[key]] +'</div>';
				result +='<input type="hidden" name="'+classEng[key]+'" id="v_'+x+'_'+row+'" value="'+ product[classEng[key]] +'">'	;
				result +='</td>';
			}
			
			x++;
		}
	}
//	result+='</form>';
	
   return result;
	
	
	
}


function imgUpload(productID)
{
		
	window.open('product/img_upload/'+productID, 'Joseph', config='height=600,width=600');
	
	
}

function editProductNum(productID,productNum)
{
	$('#editMsgIn').detach();
	$('body').append('<div id="editMsgIn" style=" margin-left:500px; color:red ;position:absolute; z-index:2; background-color:white;'+
	 'top:'+($(document).scrollTop()+200)+'px;left:200px; ";>儲存中</div>');
	$.post('/product/edit_productNum',{productNum:productNum,productID:productID},function(data)
	{
		if(data.result==true)
		{
			$('#editMsgIn').html('已儲存');
			$('#editMsgIn').fadeOut('slow');	
		}
		else alert(data.errMsg);
		
	},'json')
	
	
}

function change_send()
{
	
	for(i=0;i<5;i++)
	{
	 alert($('#product_form_'+i).serialize());
		
	}
	
	
}





var editStatus=0;

function changeStatus(on)
{
		if(on)
		{
			
			editStatus=1;
			return;
		}
		if(editStatus==1)
		{
			editStatus = 0;
			 saveChange(true);
		}
		else editStatus = 1;
			
	
	
	
	
}


function keyChange(x,y,e)
{
	/*
	if(e.keyCode==121)change_send();
	else if(e.keyCode==13) 
	{
		 changeStatus(false);

		
	}
	else if(editStatus==0)
	{
		switch(e.keyCode)
		{
			//key code
		
			//left
			case 37:
				x--;	
				if(x>=0) $('#p_'+x+'_'+y).trigger('click');
				
			break;
			//up
			case 38:
				y--;	
				if(y>=0) $('#p_'+x+'_'+y).trigger('click');
	
			break;		
			//right
			case 39:
				x++;	
				if(x>=0) $('#p_'+x+'_'+y).trigger('click');
	
			break;
			//down	
			case 40:
				y++;	
				if(y>=0) $('#p_'+x+'_'+y).trigger('click');
			
			break;	
			default:
	
			 changeStatus(true);
			break;
			
			
		}
	}
	*/
}


function saveChange(next)
{
	var id = $('#viewing').parent().attr('id');
	editStatus=0;
	if(!id) return;
	var vid = id.replace("p","v");
	$("#"+vid).val($('#viewing').val());
	$('#viewing').parent().html($('#viewing').val());	
	if(next)
	{
		var to = id.split('_');
		$('#p_'+to[1]+'_'+(parseInt(to[2])+1)).trigger('click');
	}
	
	
}

function floatBox(id,content,shfitx)
{
	$('.floatDiv').detach();
	$('#'+id).after('<div class="floatDiv">多重選擇框<input type="text"></div>');
	$('.floatDiv').css('left',shfitx+'px');
}


function changeInput(id,e)
{
	/*
	if($('#viewing').parent().attr('id')==id) return;
	editStatus=0;
	if($('#viewing').parent().attr('id'))from = $('#viewing').parent().attr('id').split('_');//FOR SHIFT
	to = id.split('_');
	saveChange(false)

		
	if (e.ctrlKey)
	{
		$('#viewing').parent().addClass('td_select');
		$('#'+id).parent().addClass('td_select');
		floatBox(id,content,$('#'+id).width())
				
	}
	else if(e.shiftKey)
	{			
		
			
		toX = parseInt(to[1]);
		fromX = parseInt(from[1]);	
		toY = parseInt(to[2]);
		fromY = parseInt(from[2]);			
		i=fromX	;
		while(1)
		{
			j=fromY	;
			while(1)
			{
					$('#p_'+i+'_'+j).parent().addClass('td_select');
					if(j==toY)break;
					else if(j<toY)j++;
					else j--;
			
			}
			if(i==toX)break;
			else if(i<toX)i++;
			else i--;
			
		}
		floatBox('p_'+i+'_'+j,content,$('#p_'+i+'_'+j).width());
				
	}
	else 
	{
		$('.floatDiv').detach();
		$('.td_handeler').removeClass('td_select');
		if($('#'+id).html().search('<input')<0)
		{
			
			$('#'+id).html('<input type="text" id="viewing"  onclick="changeStatus(true)"  onkeydown="keyChange('+to[1]+','+to[2]+',event)" value="'+$('#'+id).html()+'" style="width:'+$('#'+id).width()+'px" />')
	
			$('#viewing').focus();
		}	
	}
	
*/
	
}

function editCategorySend(id,productID)
{
	$.post('/product/edit_prodct_category',{productID:productID,c:$('#p_edit_product_category').val()},function(data)
	{
		
		if(data.result)
		{
			$('#'+id.replace('p','v')).html($('#p_edit_product_category').val());	
			$('#'+id).html($('#p_edit_product_category').val());
		
			
			
		}
		
		
	},'json')	
	
	
	
	
}
function editCategoryCancel()
{
	$('#p_edit_product_category').parent().html($('#p_edit_product_category').val());
	
}

function editCategory(id,productID)
{
	 editCategoryCancel()
	$('#'+id).html(
	'<select id="p_edit_product_category"  onchange=editCategorySend("'+id+'","'+productID+'")>'+
				'<option value="A">A.親子益智</option>'+
				'<option value="B">B.派對歡樂</option>'+
				'<option value="C">C.輕度策略</option>'+
				'<option value="D">D.重度策略</option>'+
				'<option value="E">E.團隊合作</option>'+
				'<option value="F">F.兩人對戰</option>'+
				'<option value="0">不分類</option>'+
			'</select>'+
	'<input type="button" value="X" onclick="editCategoryCancel()">'		
	)	
	$('#p_edit_product_category').val($('#'+id.replace('p','v')).val());
	
	
	
}


function productOpen(productID,openStatus,row)
{
	$.post('/product/product_open',{productID:productID,openStatus:openStatus},function(data){
			 if(data.result==true)
		   {
			 
				 $('#product_'+data.product.productID).html(productList(data.product));	
		   }
	},'json');

	
	
}

function newProductNumCheck()
{
    
    $.post('/product/get_product_num',{q:$('#newProductNum').val()},function(data)
    {
        
        if(data.result==true)
            {
                
                $('#productNumHint').html(data.productNum);
                
            }
        
        
    },'json')
    
    
}


function newProductForm()
{
	if(accountLevel<50){
		alert('你必須具有店長以上的資格才能新增');
		return;
	}
	content = 
	 '<h1>新增商品資訊</h1>'+
     '<form id="newProductForm">'+
 	'<li>'+
		'商品條碼：<input type="text" placeholder="若無資訊請空白"  name="barcode" id="newBarcode" class="big_text" />';
    if($('#level').val()!=-1)
        {
        content+=
            ' 商品編號：<input type="text"  name="productNum" id="newProductNum" class="big_text" onkeyup="newProductNumCheck()" /><div id="productNumHint" style="color:red"></div>';
        }
    
    content+=
    '</li>'+
    '<li>'+
	'商品中文：<input type="text"  name="ZHName" id="memberID" class="big_text" />'+
    ' 商品英文：<input type="text"  name="ENGName"  class="big_text" />'+
    '</li>'+		 
     '<li>'+
   
    '商品定價：<input type="text"  name="price" id="price" class="medium_text" />'+
	' 進貨折數：<input type="text"  name="buyDiscount" id="buyDiscount" class="medium_text" value=""   onblur="$(\'#buyPrice\').val(Math.round($(\'#price\').val()*this.value/100))"/>'+
	' 進貨價格：<input type="text"  name="buyPrice" id="buyPrice" class="medium_text" />'+
    '</li>'+
	'<li>'+
	'最低折數：<input type="text"  name="minDiscount" id="minDiscount" class="medium_text" />'+
    '</li>'+	
	'<li>商品種類：<select name="productType" id="productType" onchange="if(this.value>1)$(\'#new_product_category\').val(0)">'+productTypeOption+'</select>'+
	' 遊戲分類：<select id="new_product_category"  name="category">'+
				'<option value="A">A.親子益智</option>'+
				'<option value="B">B.派對歡樂</option>'+
				'<option value="C">C.輕度策略</option>'+
				'<option value="D">D.重度策略</option>'+
				'<option value="E">E.團隊合作</option>'+
				'<option value="F">F.兩人對戰</option>'+
                '<option value="G">G.周邊商品</option>'+
                '<option value="X">帳務管理</option>'+
				'<option value="0">不分類</option>'+
			'</select><br/>'+
	' 供應廠商：<input type="text" id="create_supplier_search"  placeholder="搜尋廠商名稱" onkeyup="shopSearch(\'suppliers_option\',\'create_supplier_search\',\'new_product_suppliers\',false)"><select name="suppliers" id="new_product_suppliers">'+suppliers+'</select>'+
	'</li>'+
	'<li>'+
	' 遊戲語言：<select id="new_product_language"  name="language">'+
			'<option value="英">英文</option>'+
		    '<option value="繁中">繁中</option>'+
			'<option value="簡中">簡中</option>'+
			'<option value="德">德文</option>'+
			'<option value="法">法文</option>'+
			'<option value="日">日文</option>'+
			'<option value="韓">韓文</option>'+
			'<option value="荷">荷文</option>'+
			'<option value="其他">其他</option>'+
		'</select>'+	
	' 商品狀態：<select id="new_product_open_status"  name="openStatus">'+
			'<option value="1">開放</option>'+
			'<option value="0">關閉</option>'+
		'</select>'+
	'<li>'+
	'<li>'+
	'出版廠商：<select id="new_select_publisher" onchange="$(\'#new_publisher\').val(this.value)">'+publisher+'</select>'+
	'<input type="text"  name="publisher" id="new_publisher" class="big_text" />'+
	'</li>'+
	'<li><span style="float:left">備註：</span><textarea  style="width:400px;height:100px" name="comment"></textarea></li>'+ 
    '</form>'+
	'<input id="copyID" value=""/><input type="button" value="取得資料" onclick="getInformation()">';
	
	;
	
	 openPopUpBox(content,600,420,'newProduct');	
	$('#new_product').show('slow');
	getLastProduct();
	
	
}

function getInformation()
{
	productID = $('#copyID').val();	
	$.post('/product/get_product',{productID:productID},function(data){
			 if(data.result==true)
		   {
			   $('#newProductNum').val(data.product.productNum);
			   $('#price').val(data.product.price);
			   $('#buyDiscount').val(data.product.buyDiscount);
			   $('#buyPrice').val(data.product.buyPrice);
			   $('#minDiscount').val(data.product.minDiscount);
			   $('#productType').val(data.product.productType);
			   $('#new_product_category').val(data.product.category);
			   $('#new_product_suppliers').val(data.product.suppliers);
			   $('#new_product_language').val(data.product.language);
			   $('#new_product_open_status').val(data.product.openStatus);
			    $('#new_publisher').val(data.product.publisher);
			   

		   }
	},'json')

}
function getLastProduct()
{
	
	$.post('/product/get_last_product',{},function(data){
			 if(data.result==true)
		   {
		
				 $('#copyID').val(data.maxID);	
		   }
	},'json');

	
	
}



function newProduct()
{
		var selector=$('#new_select_publisher').val();
	var textor	=$('#new_publisher').val();
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/new_product",
	   data: $("#newProductForm").serialize(),
	   success: function(data){
		 
		   if(data.result==true)
		   {  
			   var placeID = 'product';
				$('#'+placeID+'_findBarcode').val($('#newBarcode').val());
				$('#'+placeID+'_findProcutNum').val('');
				$('#'+placeID+'_findProcutENGName').val('');
				$('#'+placeID+'_findProcutZHName').val('')		   
		   		
		
			 	if(selector!=textor)getPublisher();
			 	  findProductStock(placeID,0,1);
				  if($('#level').val()==-1)
                      {
                          
                           alert('詳細編輯區');
                          
                      }
                    else
                        {
				  editBox(data.productID,0);
				  alert('請繼續將各經銷價格輸入');
                        }
				closePopUpBox();
			 	$('#new_product').hide('slow');
				
			
		   }
		   else
		   {
			    alert('編號或條碼重複，請重新輸入');
				$('#popUpBoxEnter').show();
		   }
	   }
	 });
	
	
}
var discountIndex = 0;
function discountRule(orderRule)
{
	var content = '';
	for(key in orderRule)
	{
		content+=newDistribute(orderRule[key]);
		
	}
	
	return content;
}


function newDistribute(orderRule)
{

	var result='';
	result +='<div>';
		if(orderRule.distributeType==0)
		{
			//商品預設
			result+='<div style="float:left;width:100px"  id="distributeName_'+discountIndex+'">商品預設'+
					'<input type="hidden" id="distributeType_'+discountIndex+'" value="0"/>'+
					'</div>';
 						
		}
		else if(orderRule=="")
		{
			
			 result+='<div style="float:left;width:100px" id="distributeName_'+discountIndex+'"></div>';
			 result +=discountTurn('distribute','');
			
		}
		else
		{
			//各經銷價格
			result+='<div style="float:left;width:100px"  id="distributeName_'+discountIndex+'">'+orderRule.distributeName+
					'<input type="hidden" id="distributeType_'+discountIndex+'" value="'+orderRule.distributeType+'"/>'+
					'</div>';
			
		
		}	
		
		
	
		if(orderRule!="")result +=discountTurn('distribute',orderRule['concessions']);
			
			
			
		result +='</div>';
		result +='<div style="clear:both"></div>';	

	return result;
	
}


function distributeDefine()
{
	var content='';
	$.post('/order/get_order_distribute',{},function(data){
	
			if(data.result==true)	
			{
					content+='<select id="distributeType_'+discountIndex+'">'
					content+='<option value="0">商品預設</option>';
					for(key in data.distribute)
					{
						content+='<option value="'+data.distribute[key].id+'">'+data.distribute[key].distributeName+'('+data.distribute[key].discount+')</option>';
						
						
					}
					content+='</select>';
							
					
				$('#discountDefine').append(newDistribute(''));
				$('#distributeName_'+(discountIndex-1)).html(content);
//				discountDefine(id)
			}
			else alert('wrong');
		
		}
	,'json')
	
	
	
}


function barcodeGenerate(productID)
{
    
    $.post('/product/barcode_generate',{productID:productID},function(data){
        
        if(data.result==true)
            {
                
                if(confirm('產生條碼'+data.code+',是否匯入?')) $('#editBarcode').val(data.code);
                
            }
        
        
    },'json')
    
    
}

function editSupplierBoxContent(data,row)
{
    console.log(data);
   discountIndex = 0;
			var content= '<div id="editProductBox"><input type="hidden" id="edit_row" value="'+row+'">'+
			'<form id="editProduct" style="text-align:left">'+
			'<h1>編輯商品資料</h1>'+		
			'<div ondblclick="$(\'#editBarcode\').attr(\'readonly\',false);" >商品條碼：'+
			'<input  class="big_text" id="editBarcode" type="text" name="barcode"  value="'+data.product.barcode+'" />'+
			'<input  type="hidden" name="productID"  value="'+data.product.productID+'" />'+
			'商品編號：<input  class="big_text" id="editProductNum" type="text" name="productNum"   value="'+fillZero(data.product.productNum)+'"  ondblclick="$(\'#editProductNum\').attr(\'readonly\',false)"/>'+
			'</div>'+
			'<div>中文名稱：<input type="text" class="big_text" name="ZHName" id="ZHName"  value="'+data.product.ZHName+'"/>'+
			'英文名稱：<input type="text" class="big_text" name="ENGName" id="ENGName"  value="'+data.product.ENGName+'"/>'+
			'</div>'+
			'<div>商品定價：<input type="text" class="medium_text" name="price" id="price"    onblur="$(\'#buyPrice\').val(Math.round($(\'#price\').val()*$(\'#buyDiscount\').val()/100))"  value="'+data.product.price+'"/>'+
			' 進貨折數：<input type="text" class="medium_text" name="buyDiscount" id="buyDiscount"  onblur="$(\'#buyPrice\').val(Math.round($(\'#price\').val()*this.value/100))" value="'+data.product.buyDiscount+'"/>'+
			' 進貨價格：<input type="text" class="medium_text" name="buyPrice" id="buyPrice"  value="'+data.product.buyPrice+'"/></div>'+
			'最低折數：<input type="text"  name="minDiscount"  class="medium_text"   value="'+data.product.minDiscount+'"/>'+		
				'<div>'+
				'<div><span style="float:left">商品附件：</span><textarea  style="width:400px;height:50px" name="patch">'+data.product.patch+'</textarea></div>'+ 
				
			'</div>'+
			
			'<div id="discountDefine"  style=" background-color:#DDD"></div>'+
			'<input type="button" class="big_button" value="新增卡套" onclick="sleeveDefine(\'\')">'+
			'<div id="sleeveDefine"  style=" background-color:#DDD"></div>'+
			'<div>商品種類：<select name="productType" id="editProductType" onchange="if(this.value>1)$(\'#product_category\').val(0)">'+productTypeOption+'</select>'+
			' 遊戲分類：<select id="product_category" name="category">'+
				'<option value="A">A.親子益智</option>'+
				'<option value="B">B.派對歡樂</option>'+
				'<option value="C">C.輕度策略</option>'+
				'<option value="D">D.重度策略</option>'+
				'<option value="E">E.團隊合作</option>'+
				'<option value="F">F.兩人對戰</option>'+
                '<option value="G">G.周邊商品</option>'+
                '<option value="X">帳務管理</option>'+
				'<option value="0">不分類</option>'+
			'</select>'+
			' 供應廠商：<input type="text" id="create_supplier_search"  placeholder="搜尋廠商名稱" onkeyup="shopSearch(\'suppliers_option\',\'create_supplier_search\',\'editSuppliers\',false)"><select name="suppliers" id="editSuppliers">'+suppliers+'</select><br/>'+
			' 成箱數量：<input type="text" name="case" value="'+data.product.case+'">'+
			'</div>'+
			'<div>'+
			' 遊戲語言：<select id="new_product_language"  name="language">'+
					'<option value="英">英文</option>'+
					'<option value="繁中">繁中</option>'+
					'<option value="簡中">簡中</option>'+
					'<option value="德">德文</option>'+
					'<option value="法">法文</option>'+
					'<option value="日">日文</option>'+
					'<option value="韓">韓文</option>'+
					'<option value="荷">荷文</option>'+
					'<option value="其他">其他</option>'+
				'</select>'+	
			
			'</select>'+
			'</div>'+
			'<div>'+
			'出版廠商：<select id="new_select_publisher" onchange="$(\'#new_publisher\').val(this.value)">'+publisher+'</select>'+
			'<input type="text"  name="publisher" id="new_publisher" value="'+data.product.publisher+'" class="big_text" />'+
			'</div>'+
			'<div><span style="float:left">備註：</span><textarea  style="width:400px;height:100px" name="comment">'+data.product.comment+'</textarea></div>'+ 					
			  '<table border="2">'+ 
            '<tr>'+
                '<td>遊玩人數</td>'+
                '<td>'+
                '<input class="input" type="text" name="minpeople" value="'+data.product.minpeople+'">~'+
                '<input class="input" type="text" name="maxpeople" value="'+data.product.maxpeople+'" >人'+
                '</td>'+
            '</tr>'+
            '<tr>'+
              '<td>適合年齡</td>'+
              '<td><input class="input" type="text" name="age" value="'+data.product.age+'" >歲以上</td>'+
            '</tr>'+
            '<tr>'+
             ' <td>出版年</td>'+  
              '<td>'+
                    '<select class="input" id="publishYear" name="year" >'+
                        '<option value="無">無</option>';
            var d=new Date();
					for (i = d.getFullYear();i>2010;i--)
   
            content += '<option value="'+i+'">'+i+'</option>';
                  
             content+=  ' </select>'+
            ' </td>'+
            '</tr>'+
            '<tr>'+
              '<td>遊戲時間</td>'+
             ' <td><input class="input" type="text" name="runtime" value="'+data.product.age+'" ></td>'+
            '</tr>'+
            '<tr>'+
                '<td>語言</td>'+
                '<td>'+
                    '<select class="input" name="language" >'+
                       
                        '<option value="無">無</option>'+            
                        '<option value="繁中">繁中</option>'+
                       '<option value="簡中">簡中</option>'+
                        '<option value="英文">英文</option>'+
                       '<option value="日文">日文</option>'+                
                    '</select>'+
                '</td>'+
            '</tr>'+
                  /*
            '<tr>'+   
                '<td>youtube網址</td>'+
                '<td>'+
                 
                   <?php if(!empty($this->data['rowGetProduct']['video'])):?>
                   <?php $i=0;foreach($this->data['rowGetProduct']['video'] as $row):?>
                   <div class="video" id="video_<?=$i?>_div">
                        <input method="get" class="input" type="text" id="name_<?=$i?>" name="video[]" value="<?=$row?>"  size="50px" />
                        <input type="button" name="del_<?=$i?>" value="刪除" onclick="deltxt(1,<?=$i?>)"/>
                        <br/>
                   </div>
                   <?php $i++;endforeach;?>
                   <?php endif;?>
                
                   '<input type="button" id="addBtn" name="addBtn" value="新增" onClick="addbutton(\'video\');"/>'+
                   '<div class="add_video"></div>'+
                   '<input type="hidden" class="bg_new_input_text" name="num_video" value="" />'+
                '</td>'+
            '</tr>'+
               */
             
            '<tr>'+
                '<td>遊戲特色</td>'+
               ' <td><textarea class="input" name="summary" style="width:400px;height:150px;" wrap="hard">'+data.product.summary+'</textarea>'+
                '</td>'+
            '</tr>'+
            '<tr>'+
                '<td>介紹</td>'+
               ' <td><textarea class="input" name="introduction" style="width:400px;height:150px;" >'+data.product.introduction+'</textarea>'+
            '</td>'+ 
            '</tr>'+
        '</table>'+
        
       ' </form>   ' +       
        '</div>';
			
			
		    openPopUpBox(content,900,620,'editSupplierPdSend');
			
		 	summary =  CKEDITOR.replace( 'summary' );
         introduction =    CKEDITOR.replace( 'introduction' );
			'</div>';
			
	
			
		
			if(data.product.cardSleeve!=null&&data.product.cardSleeve!=0) 
			{
				
				var cardSleeveList = data.product.cardSleeve.split('-');
				for(var each in  cardSleeveList)
				{
					var ret =  cardSleeveList[each].split(',');
					var cardSleeve = {};
					cardSleeve.sleeveSize = ret[0];
					cardSleeve.sleeveNum = ret[1];
					sleeveDefine(cardSleeve)
					
				}
			
			}
    
            $('#publishYear').val(data.product.year);

            $('#editSelectPublisher').val(data.product.publisher);
			$('#edit_product_open_status').val(data.product.openStatus);	
			$('#editProductType').val(data.product.type);
			$('#editSuppliers').val(data.product.suppliers);
			$('#new_product_language').val(data.product.language);
			if(data.product.type>1)$('#product_category').val(0);
			else $('#product_category').val(data.product.category);
 			height =parseInt($('#editProductBox').css('height').substr(0,$('#editProductBox').css('height').length-2))
			popUpBoxHeight(height+50); 
    
}



function editSupplierBox(id,row)
{
	if(accountLevel<50){
		alert('你必須具有店長以上的資格才能修改');
		return;
	}
	$.post('/supplier/get_supplier_product',{productID:id,row:row},function(data){
		
       
		if(data.result==true)
		{
			
			editSupplierBoxContent(data,row)

			
		}
        else{
             
            
             editBoxSupplier(id,row)
            
            
        }
	},'json')
	
	
}

function editBoxSupplier(id)
{
    $.post('/product/get_product',{productID:id},function(data){
		
		if(data.result==true)
		{
			
			editSupplierBoxContent(data);

			
		}
     
	},'json')
    
    
    
}




function editBox(id,row)
{
	if(accountLevel<50){
		alert('你必須具有店長以上的資格才能修改');
		return;
	}
	$.post('/product/get_product',{productID:id},function(data){
		
		if(data.result==true)
		{
			
			discountIndex = 0;
			var content= '<div id="editProductBox"><input type="hidden" id="edit_row" value="'+row+'">'+
			'<form id="editProduct" style="text-align:left">'+
			'<h1>編輯商品資料<input type="button" class="big_button" value="列印標籤" onclick="window.open(\'/welcome/tag?productNum='+data.product.productNum+'\')"><input type="button" class="big_button" value="產生條碼" onclick="barcodeGenerate('+data.product.productID+')"></h1>'+		
			'<div ondblclick="$(\'#editBarcode\').attr(\'readonly\',false);" >商品條碼：'+
			'<input  class="big_text" id="editBarcode" type="text" name="barcode"  value="'+data.product.barcode+'" />'+
			'<input  type="hidden" name="productID"  value="'+data.product.productID+'" />'+
			'商品編號：<input  class="big_text" id="editProductNum" type="text" name="productNum"   value="'+fillZero(data.product.productNum)+'"  ondblclick="$(\'#editProductNum\').attr(\'readonly\',false)"/>'+
			'商品櫃號：<input  class="big_text" id="editProductCabinet" type="text" name="cabinet"   value="'+data.product.cabinet+'"'+
			'</div>'+
			'<div>中文名稱：<input type="text" class="big_text" name="ZHName" id="ZHName"  value="'+data.product.ZHName+'"/>'+
			'英文名稱：<input type="text" class="big_text" name="ENGName" id="ENGName"  value="'+data.product.ENGName+'"/>'+
			'<label><input type="checkBox" value="1"  name="rule" id="rule">精美規則</label>'+
			'</div>'+
			'<div>'+
			'購買限量：<input  class="medium_text" id="limitNum" type="text" name="limitNum"   value="'+data.product.limitNum+'"/>(0表無限)'+
			' 等候進貨時間：<input  class="medium_text" id="waiting" type="text" name="wait"   value="'+data.product.wait+'"/>(0表無限)'+
			'</div>'+
			'<div>商品定價：<input type="text" class="medium_text" name="price" id="price"    onblur="$(\'#buyPrice\').val(Math.round($(\'#price\').val()*$(\'#buyDiscount\').val()/100))"  value="'+data.product.price+'"/>'+
			' 進貨折數：<input type="text" class="medium_text" name="buyDiscount" id="buyDiscount"  onblur="$(\'#buyPrice\').val(Math.round($(\'#price\').val()*this.value/100))" value="'+data.product.buyDiscount+'"/>'+
			' 進貨價格：<input type="text" class="medium_text" name="buyPrice" id="buyPrice"  value="'+data.product.buyPrice+'"/></div>'+
			
				'最低折數：<input type="text"  name="minDiscount"  class="medium_text"   value="'+data.product.minDiscount+'"/>'+
				/*			
				' 出貨折數：<input type="text"  name="purchaceDiscount" id="memberID"  value="'+data.product.purchaseCount+'" class="medium_text" onblur="$(\'#purchasePrice\').val(Math.round($(\'#price\').val()*this.value/100))"/>'+
				' 出貨價格：<input type="text" id="purchasePrice" class="medium_text" value="'+Math.round(data.product.price*data.product.purchaseCount/100)+'"readonly="readonly" >'+				
				' 非加盟商出貨折數：<input type="text"  name="nonJoinPurchaceDiscount" id="nonJoinPurchaceDiscount"  value="'+data.product.nonJoinPurchaceDiscount+'" class="medium_text" onblur="if(fucCheckNUM(this.value)==0) alert(\'請填入數字\')"/>'
				*/
				'<label><input type="checkBox" value="1"  name="nonBonus" id="nonBonus">紅利不累積</label>'+
				'<div>'+
				'<div><span style="float:left">商品附件：</span><textarea  style="width:400px;height:50px" name="patch">'+data.product.patch+'</textarea></div>'+ 
				'<input type="button" class="big_button" value="定義新經銷折數" onclick="distributeDefine(\'\')">若要刪除折扣，將數量跟折扣都空白，按下確定後就完成刪除。'+
			'</div>'+
			
			'<div id="discountDefine"  style=" background-color:#DDD"></div>'+
			'<input type="button" class="big_button" value="新增卡套" onclick="sleeveDefine(\'\')">'+
			'<div id="sleeveDefine"  style=" background-color:#DDD"></div>'+
			'<div>商品種類：<select name="productType" id="editProductType" onchange="if(this.value>1)$(\'#product_category\').val(0)">'+productTypeOption+'</select>'+
			' 遊戲分類：<select id="product_category" name="category">'+
				'<option value="A">A.親子益智</option>'+
				'<option value="B">B.派對歡樂</option>'+
				'<option value="C">C.輕度策略</option>'+
				'<option value="D">D.重度策略</option>'+
				'<option value="E">E.團隊合作</option>'+
				'<option value="F">F.兩人對戰</option>'+
                '<option value="G">G.周邊商品</option>'+
                '<option value="X">帳務管理</option>'+
				'<option value="0">不分類</option>'+
			'</select>'+
			' 供應廠商：<input type="text" id="create_supplier_search"  placeholder="搜尋廠商名稱" onkeyup="shopSearch(\'suppliers_option\',\'create_supplier_search\',\'editSuppliers\',false)"><select name="suppliers" id="editSuppliers">'+suppliers+'</select><br/>'+
			' 成箱數量：<input type="text" name="case" value="'+data.product.case+'">'+
			'</div>'+
			'<div>'+
			' 遊戲語言：<select id="new_product_language"  name="language">'+
					'<option value="英">英文</option>'+
					'<option value="繁中">繁中</option>'+
					'<option value="簡中">簡中</option>'+
					'<option value="德">德文</option>'+
					'<option value="法">法文</option>'+
					'<option value="日">日文</option>'+
					'<option value="韓">韓文</option>'+
					'<option value="荷">荷文</option>'+
					'<option value="其他">其他</option>'+
				'</select>'+	
			' 商品狀態：<select id="edit_product_open_status"  name="openStatus">'+
				'<option value="1">開放</option>'+
				'<option value="0">關閉</option>'+
			'</select>'+
			'</div>'+
			'<div>'+
			'出版廠商：<select id="new_select_publisher" onchange="$(\'#new_publisher\').val(this.value)">'+publisher+'</select>'+
			'<input type="text"  name="publisher" id="new_publisher" value="'+data.product.publisher+'" class="big_text" />'+
			'</div>'+
			'<div><span style="float:left">備註：</span><textarea  style="width:400px;height:100px" name="comment">'+data.product.comment+'</textarea></div>'+ 	
			'網站上的資訊：<span id="phabgName"></span><input type="hidden"  name="phaBid" id="phaBid" value="'+data.product.phaBid+'" class="big_text" />'+
			
			' <div class="bg_new_table_list_input">'+
               '<input type="text" name="games" id="phagames"  class="bg_new_input_text" onkeyup="searchPlayingBg();" onblur="deleteSearchArea();" onchange="deleteSearchArea();" autocomplete="off" placeholder="在此搜尋桌上遊戲"  />'+
                 '<input type="hidden" name="family" id="familyList" value=""/>'+
                 '<input type="hidden" name="games_num" id="games_num"/>'+
                 ' <div id="games_hidden_area"><!-- hidden input here --></div>'+
                 ' <div id="bg_join_games_anchor"><!-- search list here --></div>'+
			'<div>'+
			'<a href="#" onclick="phaBgBreate()"> 新增此遊戲到網站上</a></div>'+
			'<div class="divider"></div>'+
			'<div id="150chk"><input type="button" class="big_button" value="加入150商品清單" onclick="addTopProduct('+data.product.productID+')"></div>'+
			'<td>填寫最近到貨日：</td><td><input type="text" id="preTime_'+data.product.productID+'" name="purchase_preTime" class="big_text">'+
				'到貨數量<input type="text" onchange="changePreTimeNum('+data.product.productID+')" id="alreadyOrder_'+data.product.productID+'" class="big_text">'+
			'</td>'
			
			
			
			'</form>'+
			'</div>';
			
			
		    openPopUpBox(content,900,620,'editSend');
			getAlreadyOrder(data.product.productID);
			
		
			if(data.product.phaBid!=0)
			{
				$('#phabgName').html('<a href="http://www.phantasia.tw/bg/home/'+data.product.phaBid+'" target="_blank">http://www.phantasia.tw/bg/home/'+data.product.phaBid+'</a>'+
					'<input type="button" value="將本遊戲由網站資料刪除" class="PHAButton" onclick="deletePhabg()"/>'			);
			
			}
			if(data.product.rule==1)     $('#rule').attr('checked','checked');
			if(data.product.nonBonus==1) $('#nonBonus').attr('checked','checked');
		
			
			
			$('#discountDefine').html(discountRule(data.productOrderRule));
			/*
			if(data.product.concessions!=null) var concessions = data.product.concessions.split(',');
			else var concessions ={};
			if(data.product.concessionsNum!=null) var concessionsNum = data.product.concessionsNum.split(',');
			else var concessionsNum ={};				
			for(each in concessions)
			{
				
				discountDefine({concessions:concessions[each],concessionsNum:concessionsNum[each]});
			}
			
			*/	
			if(data.product.cardSleeve!=null&&data.product.cardSleeve!=0) 
			{
				
				var cardSleeveList = data.product.cardSleeve.split('-');
				for(var each in  cardSleeveList)
				{
					var ret =  cardSleeveList[each].split(',');
					var cardSleeve = {};
					cardSleeve.sleeveSize = ret[0];
					cardSleeve.sleeveNum = ret[1];
					sleeveDefine(cardSleeve)
					
				}
			
			}
		
			
			
			$('#editSelectPublisher').val(data.product.publisher);
			$('#edit_product_open_status').val(data.product.openStatus);	
			$('#editProductType').val(data.product.type);
			$('#editSuppliers').val(data.product.suppliers);
			$('#new_product_language').val(data.product.language);
			if(data.product.type>1)$('#product_category').val(0);
			else $('#product_category').val(data.product.category);
 			height =parseInt($('#editProductBox').css('height').substr(0,$('#editProductBox').css('height').length-2))
			popUpBoxHeight(height+50);
			chkTopProduct(data.product.productID);
			
		}
	},'json')
	
	
}
function chkTopProduct(productID)
{
	
	$.post('/product/chk_top_product',{productID:productID},function(data)
	{
		if(data.result==true)$('#150chk').html('<h1>此商品為150商品 <input type="button" value="從150中移出" onclick="deleteTopProduct('+productID+')"></h1>');
		else $('#150chk').html('<input type="button" class="big_button" value="加入150商品清單" onclick="addTopProduct('+productID+')">');
	},'json')	
	
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


function sleeveDefine(data)
{
	if(sleeveOption=='')getCardSleeve();
	$('#sleeveDefine').append('<div  id="sleeveDefine_'+sleeveIndex+'">卡套尺寸：<select class="sleeveSize" id="sleeveSize_'+sleeveIndex+'"><option value="*">不需牌套</option>'+sleeveOption+'</select>'+
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





function phaBgBreate()
{
	
	if(confirm('請先確認網站上沒有該遊戲後再新增，點選是新增，點選否取消'))
	{
		if($('#ZHName').val()==''||$('#ENGName').val()=='')
		{
			alert('中文或英文名稱不可空白')	;
			return;	
		}
		if($('#price').val()=='')
		{
			
			alert('價格不可空白')	;
			return;		
		}
		
	$.post('/product/pha_bg_create',{ZHName:$('#ZHName').val(),ENGName:$('#ENGName').val(),price:$('#price').val()},function(data){
		if(data.result==true)
		{
			alert('新增已完成');
			addGames($('#ZHName').val(),data.bid,1);
			
		}
	},'json')
		
		
	}
	
	
	
}
function deletePhabg()
{
	$('#phabgName').html('');
	$('#phaBid').val(0);
	
	
}
var timeStamp=0;
function searchPlayingBg(){
	timeStamp++;
	var bgName = $('#phagames').val();

	if ( bgName == '' ) {
		$('#bg_join_games_list').detach();
		return false;
	}
$.post("/product/pha_search",{name:bgName,timeStamp:timeStamp},function(data){
	
	if(data.timeStamp>=timeStamp)
	{
		
		$('#bg_join_games_anchor').html('<div id="bg_join_games_search" ><ul id="game_list" style=" height:200px;overflow:scroll"></ul></div>');		
		if(data.result==true)
		{
			for(key in data.bgList)
			{
				$('#game_list').append(
				'<li id="game_'+data.bgList[key].bid+'" style="height:50px">'+
					'<img src="http://www.phantasia.tw/upload/bg/home/s/'+data.bgList[key].bid+'.jpg" style="width:50px;height:50px; float:left">'+
					'<a href="#" onmousemove="$(\'#game_'+data.bgList[key].bid+'\').css(\'background\', \'#DDD\');"'+
								 'onmouseout="$(\'#game_'+data.bgList[key].bid+'\').css(\'background\', \'white\');"'+
								 'onmousedown="addGames(\''+data.bgList[key].cha_name+'\',\''+data.bgList[key].bid+'\',0);">'+
					'<div>'+data.bgList[key].cha_name+'('+data.bgList[key].eng_name+')'+
					'</div></a>'+
					'</li>'
				
				);
			}
			
		}
		else
		{
			$('#game_list').append('<li>抱歉，找不到這款遊戲...<a href="#" onmousedown="window.open(\'/bg/create\');"> 現在新增？ </a></li>');
			
		}	
	}
	},'json');

}

function addGames(chaname,bid,isNew)
{
		if(isNew==1)
		{
			$('#phabgName').html('<a href="http://www.phantasia.tw/bg/audit/'+bid+'" target="_blank">'+chaname+'</a>'+
					'<input type="button" value="將本遊戲由網站資料刪除" class="PHAButton" onclick="deletePhabg()"/>'
		);			
		}
		else
		{
		$('#phabgName').html('<a href="http://www.phantasia.tw/bg/home/'+bid+'" target="_blank">'+chaname+'</a>'+
						'<input type="button" value="將本遊戲由網站資料刪除" class="PHAButton" onclick="deletePhabg()"/>'
		);
		}
			
	$('#phaBid').val(bid);
	$('#bg_join_games_search').detach();

}
function editSupplierPdSend()
{
	var selector=$('#editSelectPublisher').val();
	var textor	=$('#edit_publisher').val();
	var row = $('#edit_row').val();
	var concessions = '';
	var concessionsNum = '';
	var distributeType = '';
	
	
	var cardSleeve='';
	$('.sleeveSize').each(function(i){
	
		id = this.id.substr(11);

		if($('#sleeveSize_'+id).length!=0)
		{
			if($('#sleeveSize_'+id).val()=='*')
			{
				cardSleeve = '*-';
				
			}
			else cardSleeve+=$('#sleeveSize_'+id).val()+','+$('#sleeveNum_'+id).val()+'-';
		}
	})
    
   

	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/supplier/edit_supplier_pd_send",
	   data: $("#editProduct").serialize()+'&distributeType='+distributeType+'&concessions='+concessions+'&concessionsNum='+concessionsNum+'&cardSleeve='+cardSleeve+'&summary='+summary.getData()+'&introduction='+introduction.getData(),
	   success: function(data){
		   if(data.result==true)
		   {
			   
			 	
				
				// $('#product_'+data.product.productID).html(productList(data.product,row));	
				 	closePopUpBox();
		   }
		   else 
		   {
			   alert(data.errMsg);
			   $('#popUpBoxEnter').show();
		   }
	   }
	 });
	 

	
}


function editSend()
{
	var selector=$('#editSelectPublisher').val();
	var textor	=$('#edit_publisher').val();
	var row = $('#edit_row').val();
	var concessions = '';
	var concessionsNum = '';
	var distributeType = '';
	
	
	

	for(i=0;i<discountIndex;i++)
	{
			
		distributeType+=$('#distributeType_'+i).val()+',';
		
			$('.distributeDiscount_'+i).each(function()
			{
	
					
					concessions+=$(this).val()+',';
			})
			$('.distributeNum_'+i).each(function()
			{
				
					concessionsNum+=$(this).val()+',';

			})	
			concessions+='-';
			concessionsNum+='-';
	}
	var cardSleeve='';
	$('.sleeveSize').each(function(i){
	
		id = this.id.substr(11);

		if($('#sleeveSize_'+id).length!=0)
		{
			if($('#sleeveSize_'+id).val()=='*')
			{
				cardSleeve = '*-';
				
			}
			else cardSleeve+=$('#sleeveSize_'+id).val()+','+$('#sleeveNum_'+id).val()+'-';
		}
	})

	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/edit_send",
	   data: $("#editProduct").serialize()+'&distributeType='+distributeType+'&concessions='+concessions+'&concessionsNum='+concessionsNum+'&cardSleeve='+cardSleeve,
	   success: function(data){
		   if(data.result==true)
		   {
			   
			 	if(selector!=textor)
				{
					getPublisher();
				
				}
				
				 $('#product_'+data.product.productID).html(productList(data.product,row));	
				 	closePopUpBox();
		   }
		   else 
		   {
			   alert(data.errMsg);
			   $('#popUpBoxEnter').show();
		   }
	   }
	 });
	 

	
}

function deleteShipmentShop()
{
	$.post('/product/delete_shipment_shop',{shopID:$('#shipmentShopID').val()},function(data){
			if(data.result==true) getShipmentShop(0)

		},'json')
	
}
function getShipmentShop(shopID)
{
	
	$.post('/product/get_shipment_shop',{shop_type:$('input[name=shop_type]:checked').val()},function(data){
			var content = '<select name="shopID" id="shipmentShopID">';
			for(key in data.shopData)
			{
				content	+='<option value="'+data.shopData[key].shopID+'">'+data.shopData[key].name+'</option>';
				
			}
			content += '</select>';
			
			if($('input[name=shop_type]:checked').val()==1)
			{
				
				content += '<input type="hidden"  id="shipment_type" value="1"/>'+
							'<input type="button"  id="new_shop_btn" class="big_button"value="刪除" onclick="deleteShipmentShop()"/>'+
						   '<input type="button"  id="new_shop_btn" class="big_button"value="新增出貨地點" onclick="$(\'#new_shop\').show()"/>'+
						  
							'<div id="new_shop" style="display:none;">'+
							'<input type="text"  value="" id="newShopName" class="big_text">'+
							'<input type="button"  value="確認" class="big_button"  onclick="newShipmentShop()"/>'+
							'<input type="button"  value="取消" class="big_button" onclick="$(\'#new_shop\').hide()"/>'+
							'</div>';
				$('#new_shop_btn').show();
				$('#shipmentCount').val(100);
			
			}
			else
			{
				 $('#new_shop_btn').hide();
				 content +='<input type="hidden"  id="shipment_type" value="0"/>';
			}
			$('#shopSelecter').html(content);
            if(shopID!=0)$('#shipmentShopID').val(shopID);
		
	},'json')
 
}

function newShipmentShop()
{
	$.post('/product/new_shipment_shop',{name:$('#newShopName').val()},function(data)
	{
		if(data.result==true)
		{
			$('#new_shop').hide();
			getShipmentShop(data.shopID);
	
		}
		else alert('名稱重複');
	},'json')	

	
}

function productDelete(productID)
{
	if(confirm('你確定要刪除？'))
	{
		$.post('/product/delete',{productID:productID},function(data)
		{
			if(data.result==true)
			{
				
				$('#product_'+productID).detach();
			}
			else alert('名稱重複');
		},'json')	
	}
}

function purchase()
{
	$.post('product/get_temp_purchase',{},function(data)
	{
		if(data.result==true)
		{
			
		
			for(key in data.tempList)	content	+='';
			if(key==0)wareroomIO('purchase');
			else
			{
				result='<h1>請選擇進貨的類型</h1>'	;
				result+='<input type="button" value="新進商品" class="big_button" onclick="wareroomIO(\'purchase\')">';
				result+='<div class="divider"></div>';
				result+='<h2>暫存清單</h2>'	;
				result+=content;
				openPopUpBox(result,500,600,'closePopUpBox');		
				
			}
			
			
			
			
		}
		
		
	},'json')	
	
	
	
}



function purchaseInf(supplierID)
{
		$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/get_supplier_inf/",
	   data: 'supplierID='+supplierID,
		async:false,
	   success: function(data){
		
		 
		
		   if(data.result==true)
		   {
					
				 var remain = data.budget.limitAmount - data.budget.used;
          
            $('#budget').html('目前可用預算為：'+remain);
			$('#purchase_name').html(data.suppliers.name);
			$('#purchase_unfied').html(data.suppliers.IDNumber);
			$('#purchase_phone').html(data.suppliers.phone);
			$('#purchase_email').html(data.suppliers.email);
		
			;
			$("input[name=taxType][value="+data.suppliers.invoice+"]").attr('checked',true); 
			
			 changeTaxType();
					
				 
				 
				 	
		   }
	
	   }
	 });
	
	
	
	
	
	
	
	
}
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
	

	
	
	
}



function wareroomIO(type)
{
	if(type=='purchase')
	{
		content='<h1>進貨</h1><div id="dumplistCavas"><input type="button" class="big_button" value="從採購單下載" onclick="showOrderList()">'+
				'<select id="orderStatus" " onchange="showOrderList()">'+
					'<option value="2">採購完成</option>'+
					'<option value="-1">已到貨(入庫過)</option>'+
				'</select><br/></div>'+
		'<div id="order_container"></div><br/>';
	}
	else if(type=='purchaseOrder') content='<h1>採購單</h1>';
	 dumpOrderList= Array();
	content +=
			'<input type="text" id="supplierSearch" onkeyup=" shopSearch(\'suppliers_option\',\'supplierSearch\',\'purchaseSuppliers\',1);purchaseInf($(\'#purchaseSuppliers\').val())" placeholder="搜尋廠商">'+
			'<form id="supplierInf">'+
				'<select name="suppliers" id="purchaseSuppliers" onchange="purchaseInf(this.value)">'+suppliers+'</select>'+
                '<span style="color:red" id="budget"></span>'+
			'<table border="1" style="width:800px; margin-bottom:20px; margin-left:200px">'+	
				'<tr><td>廠商名稱</td><td id="purchase_name"></td><td>統一編號</td><td  id="purchase_unfied"></td></tr>'+
				'<tr style=" background-color:#EBEBFF"><td>連絡電話</td><td id="purchase_phone"></td><td>聯絡信箱</td><td id="purchase_email"></td></tr>'+
				'<tr style=" background-color:#EBEBFF">'+
					'<td>發票日期</td>'+
					'<td><input type="date" id="purchase_accountTime" name="accountTime" class="big_text"  value="0" ></td>'+
					'<td>稅務方式</td><td>'+
					'<div id="taxType">'+
						'<label  style=" cursor:pointer" for="radio1">'+ 
					  '<input onchange=" changeTaxType()" type="radio" id="radio0" name="taxType" value="0" checked="checked">外加 </label>'+
					  '<label  style=" cursor:pointer" for="radio2"> <input onchange=" changeTaxType()"  type="radio" id="radio1" name="taxType" value="1" >內含 </label>'+
					  '<label   style=" cursor:pointer" for="radio3"><input onchange=" changeTaxType()" type="radio" id="radio2" name="taxType" value="2"> 不開 </label>'+
					'</div>'+
				'</td></tr>'+
					'<tr id="bTax" style=" background-color:#FF3333"><td>進貨攤提(稅前)</td><td><input type="text"  value="0" id="purchase_total_bTax" name="purchase_total_bTax" class="big_text"  onchange="caculatePurchasePrice()"></td>'+
						'<td>運費攤提(稅前) </td><td><input type="text" id="freight_bTax" name="freight_bTax" class="big_text"  value="0" onchange="caculatePurchasePrice()" ></td></tr>'+
					'<tr id="aTax"  style=" background-color:#CCFF33"><td>進貨攤提(稅後)</td><td><input type="text"  value="0" id="purchase_total_aTax" name="purchase_total_aTax" class="big_text"  onchange="caculatePurchasePrice()"></td>'+
					'<td>運費攤提(稅後) </td><td><input type="text" id="freight_aTax" class="big_text" name="freight_aTax"   value="0"  onchange="caculatePurchasePrice()"></td></tr>';
		  		if(type=='purchaseOrder') content +=	'<tr><td></td><td></td><td>預計到貨日</td><td><input type="text" id="purchase_preTime" name="purchase_preTime" class="big_text"></td></tr>';
                content+='<tr><td>採購方式</td><td><label  style=" cursor:pointer" for="typeRadio0">'+ 
					  '<input  type="radio" id="typeRadio0" name="buyType" value="0" checked="checked">買斷</label>'+
					  '<label  style=" cursor:pointer" for="typeRadio1"> <input   type="radio" id="typeRadio1" name="buyType" value="1" >寄賣</label>'+
					 '</td><td>備註欄</td><td><textarea id="purchase_comment" name="comment" height="100px" class="big_text"> </textarea></td></tr>';
	
			content+='</table>'+
			'</form>'+
			 '<div id="'+type+'Query"></div>'+
			  '<div class="divider" ; style="clear:both"></div>'+	
			  '<div style=" float:left; min-height:100px;">'+
			  
			  '<form id="'+type+'Form">'+
 			  '<table id="'+type+'Table" border="1" width="1200px">'+
			  	'<tr>'+
					'<td colspan="5" style=" background-color:#E8CCFF">產品資訊</td>'+
					'<td colspan="3" style=" background-color:#FFBB66">價格資訊</td>'+
					'<td colspan="2" style=" background-color:#99FF99">進貨資訊</td>'+
					'<td colspan="5" style=" background-color:#66FFFF">含運資訊</td>'+
                    '<td colspan="2" style=" background-color:rgb(255, 51, 51)">功能</td>'+
				'</tr>'+
				'<tr>'+
					'<td style=" background-color:#E8CCFF">產品編號</td>'+
					'<td style=" background-color:#E8CCFF">中文名稱</td>'+
					'<td style=" background-color:#E8CCFF">英文名稱</td>'+
					'<td style=" background-color:#E8CCFF">語言</td>'+
					'<td style=" background-color:#E8CCFF">目前數量</td>'+
					'<td style=" background-color:#FFBB66">原價</td>'+
					'<td style=" background-color:#FFBB66">進價</td>'+
					'<td style=" background-color:#FFBB66">折數</td>'+
					'<td style=" background-color:#99FF99">產品數量</td>'+
					'<td style=" background-color:#99FF99">小計</td>'+
					'<td style=" background-color:#66FFFF">攤提(單價)</td>'+
					'<td style=" background-color:#66FFFF">單價</td>'+
					'<td style=" background-color:#66FFFF">上回進價</td>'+
					'<td style=" background-color:#66FFFF">折數</td>'+
					'<td style=" background-color:#66FFFF">小計</td>'+
                    '<td >備註</td>'+
					'<td >刪除</td>'+
				'</tr>'+
			
			  '</table>'+
			  	'<span style=" float:right; font-size:10pt;">*"依輸入"的意思為，依照輸入的小計為主，反之未勾選則以單價計算為主</span>'+
			  '</form>'+
			  '</div>'+
			  '<div class="divider" ; style="clear:both"></div>'+
			  '<table style="width:800px; margin-bottom:20px; margin-left:200px">';
			content +='<tr style=" background-color:#EBEBFF"><td></td><td></td><td>本次實際付款總額</td><td><span id="orderTotal"></span><br/><span id="aTaxTotal"></span></td></tr>'+
				  '</table>'+
			   '<h1 id="IOMSG"><h1>'
			  ;
			  ;
			 
			  
	 openPopUpBox(content,1200,600,''+type+'Create',true);
	 changeTaxType();
	 	if(type=='purchase') $('#popUpBoxEnter').bind('click',function(){updateDumpOrder()})		
	 //getShipmentShop(0);
	 queryProduct(type,'select');
	var purchase_preTime = $( "#purchase_preTime").datepicker({
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
								
							}
						});		  	





}

function changeTaxType()
{

	$('#aTax').hide();
	$('#bTax').hide();
	$('#aTaxTotal').hide();
	switch($('input[name=taxType]:checked').val())
	{
		
		case '0':
			$('#bTax').fadeIn();
		break;	

		case '1':
			$('#aTax').fadeIn();
		break;	

		case '2':
			$('#bTax').fadeIn();
		break;	
		
	}
	caculatePurchasePrice();
}


function purchaseCreate()
{
	if($('#purchaseSuppliers').val()==0) 
	{
		$('#popUpBoxEnter').show()
		alert('請選擇供應商')	;
		
		return ;
		
		
	}
	
	$('#IOMSG').html('正在建立進貨資訊...');
	
	
		$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/purchase_create/",
	   data: $("#supplierInf").serialize()+'&orderTotal='+$('#orderTotal').html(),
	   success: function(data){
		
		 
		
		   if(data.result==true)
		   {
					
				$('#IOMSG').html('進貨資訊建立完成...');
				purchaseSend(data.purchaseID)
					
				 
				 
				 	
		   }
		   else alert('錯誤，請重新輸入');
	   }
	 });
	
	
	

	
	
}

function purchaseOrderCreate()
{
	if($('#purchaseSuppliers').val()==0) 
	{
		$('#popUpBoxEnter').show()
		alert('請選擇供應商')	;
		
		return ;
		
		
	}
	
	
	if($('#purchase_preTime').val()=='')
	{
		
		if(confirm('為此次訂貨設定到貨時間吧'))
		{
			$('#popUpBoxEnter').fadeIn();
			return;	
		}
		
		
	}
	
	
	
	
	$('#IOMSG').html('正在建立採購單...');
		

	
		$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/purchase_order_create/",
	   data: $("#supplierInf").serialize()+'&'+$('#orderTotal').html(),
	   success: function(data){
		
	
		
		   if(data.result==true)
		   {
					
				$('#IOMSG').html('採購單資訊建立完成...');
				purchaseOrderSend(data.purchaseID)
					
				 
				 
				 	
		   }
		   else alert('錯誤，請重新輸入');
	   }
	 });
	
	
	

	
	
}


function getLastPrice(productID)
{
	$.post('/product/get_last_price',{productID:productID},function(data)
	{
		if(data.result==true)
		{
			$('#phrchase_lstprice_'+productID).html(data.purchasePrice);
			
			
		}
		
	},'json')
	
	
}

function orgPriceTurnZero()
{
	if(confirm('你確定要全部歸零？'))	
	{
		
	$('.phrchase_orgprice').each(function()
	{
			
		$(this).val(0);
		
	}
	)
	caculatePurchasePrice();
	}
	
	
}
var index = 0;
function purchaseTableTemplate(data,type,countTotal)
{
    
	if(type=="purchaseOrderTable" && $('#phrchase_productID_'+data.productID).length!=0)
	{
		 alert('商品已選擇');
		 return 
	}

	var num = 0;
    
		if(data.nowNum===undefined)data.nowNum = '' ;
        if(data.buyNum===undefined)purchaseNum = 0 ;
        else 
        {
            v = data.buyNum - data.comNum;
            if(data.case===undefined || data.case==0)purchaseNum = v;
            else  
            {
                c = Math.ceil((data.buyNum - data.comNum)/data.case);
                purchaseNum = c * data.case;
            }
        }
    var comment = '';
        index++;
      
        
		if(type=='purchaseOrderTable') 
        {
            comment='<input type="text" name="comment_'+data.productID+'" value="">';
            
            tail = '';
            
        }
        else tail ='_'+index;
		$('#'+type).append(
		'<tr id="phrchase_productID_'+data.productID+tail+'">'+
			'<td  id="productNum_'+data.productID+tail+'">'+data.productNum+'</td>'+
			'<td><input type="hidden" name ="phrchase_productID_'+data.productID+tail+'" value='+data.productID+'>'+data.ZHName+'</td>'+
			'<td>'+data.ENGName+'</td>'+
			'<td>'+data.language+'</td>'+
			'<td>'+data.nowNum+'</td>'+
			'<td class="sell_price" id="sell_price_'+data.productID+tail+'">'+data.price+'</td>'+
			'<td><input type="text" class="short_text phrchase_orgprice"   id="phrchase_orgprice_'+data.productID+tail+'"  value="'+data.buyPrice+'"  name = "phrchase_price_'+data.productID+tail+'" onchange="caculatePurchasePrice();" /></td>'+
			'<td  id="phrchase_discount_'+data.productID+tail+'">'+
			Math.round(data.buyPrice*100/data.price)+
			'%</td>'+
			'<td style=" background-color:#99FF99"><input type="text" class="short_text purchase_num" value="'+purchaseNum+'"  id="phrchase_num_'+data.productID+tail+'" name = "phrchase_num_'+data.productID+tail+'" '+
					'onclick="$(\'#phrchase_num_'+data.productID+tail+'\').select()" onblur="chkMinus(\''+data.productID+tail+'\')" onchange="caculatePurchasePrice();" /></td>'+	
			'<td><label style=" cursor:pointer"><input id="byEnter_'+data.productID+tail+'" type="checkbox" value="1">依輸入</label>'+
				'<input type="text"  class="medium_text"   id="phrchase_subtotal_'+data.productID+tail+'" name="phrchase_subtotal_'+data.productID+tail+'"  value="0" onfocus = "$(\'#byEnter_'+data.productID+tail+'\' ).attr(\'checked\', true ); " onchange="caculatePurchasePrice();"/></td>'+
			'<td id="phrchase_freight_'+data.productID+tail+'" ></td>'+	
			'<td id="purchase_by_freight_'+data.productID+tail+'">含運單價</td>'+
			'<td id="phrchase_lstprice_'+data.productID+tail+'"></td>'+
			'<td id="purchase_by_freight_dc_'+data.productID+tail+'">含運折數</td>'+	
			'<td id="purchase_by_freight_total_'+data.productID+tail+'">含運小計</td>'+	
            '<td id="purchase_by_comment">'+comment+'</td>'+				
			'<td><input type="button" value="刪除" onclick="$(\'#phrchase_productID_'+data.productID+'\').detach();caculatePurchasePrice();"></td>'+
			
			'</tr>'
		)
		getLastPrice(data.productID);
	
		
		if(countTotal)caculatePurchasePrice();
	
	adjustHeight('#'+type,100,550)
	return tail;
	
}






function purchaseOrderTable(data,countTotal)
{
		if(typeof countTotal === 'undefined')  countTotal  = true;
	purchaseTableTemplate(data,'purchaseOrderTable',countTotal);
	
	
}


function purchaseTable(data,countTotal)
{
	if(typeof countTotal === 'undefined') countTotal  = true;
	
	return purchaseTableTemplate(data,'purchaseTable',countTotal);
	
}



function changeFreight(productID,feright)
{
	if(fucCheckNUM(freight)==0) 
	{
		alert('運費輸入錯誤');
		return;
		
	}	
	/*
	price = parseFloat($('#sell_price_'+productID).html());	

	$('#phrchase_freight_'+productID).val(feright);
	buyPrice = $('#phrchase_freight_'+productID).val()+($('#phrchase_orgprice_'+productID).val());
	$('#phrchase_price_'+productID).val(buyPrice);
	
	$('#phrchase_discount_'+productID).html(Math.round(buyPrice*100)/price+'%');
	*/
}



function caculatePurchasePrice()
{
	if($('input[name=taxType]:checked').val()%2==0) type = 'bTax';
	else type = 'aTax';
	
	
	var freight = parseFloat($('#freight_'+type).val()) 
	var p = parseFloat($('#purchase_total_'+type).val());


	if(fucCheckNUM(freight)==0) 
	{
		alert('運費輸入錯誤');
		return;
		
	}
	var totalPrice = 0;

	$('.sell_price').each(function()
	{
		
		
		productID = this.id.substr(11);
		
		totalPrice += parseFloat($(this).html())*parseFloat($('#phrchase_num_'+productID).val());
		
		
	})	
	
	
	orderTotal = 0 ;FreightTotal = 0;enterKey = false;
	var productID;
	$('.sell_price').each(function()
	{
		productID = this.id.substr(11);
		price = parseFloat($(this).html());	
		if(totalPrice!=0 && $('#phrchase_num_'+productID).val()!=0 && p!=0)
		{
			$('#phrchase_freight_'+productID).html(freight*price/totalPrice);
			$('#phrchase_orgprice_'+productID).val(p*price/totalPrice);
			
		}
		
		if(totalPrice!=0 && $('#phrchase_num_'+productID).val()!=0)
		{
			$('#phrchase_freight_'+productID).html(formatFloat(freight*price/totalPrice,2));
		}
		else
		{
			 $('#phrchase_freight_'+productID).html(0);
			  //$('#phrchase_orgprice_'+productID).val(0);
			 
		}
		
		
		if($('#byEnter_'+productID).is(':checked'))
		{
			 
			 subTotal = parseFloat($('#phrchase_subtotal_'+productID).val()); 	
			 $('#phrchase_orgprice_'+productID).val( parseFloat($('#phrchase_subtotal_'+productID).val())/parseFloat($('#phrchase_num_'+productID).val()));
			buyPrice = parseFloat($('#phrchase_orgprice_'+productID).val());
			enterKey = true;
		}
		else
		{
			buyPrice = parseFloat($('#phrchase_orgprice_'+productID).val());
			subTotal = buyPrice * parseFloat($('#phrchase_num_'+productID).val()) 
		}
		FreightBuyPrice = parseFloat($('#phrchase_orgprice_'+productID).val())+ parseFloat($('#phrchase_freight_'+productID).html());	
		$('#purchase_by_freight_'+productID).html(formatFloat(FreightBuyPrice,2));
		$('#phrchase_discount_'+productID).html(Math.round($('#phrchase_orgprice_'+productID).val()*100)/price+'%');
		$('#purchase_by_freight_dc_'+productID).html(formatFloat(Math.round(FreightBuyPrice*100)/price,2)+'%');
		
		if(parseFloat($('#phrchase_lstprice_'+productID).html())!= FreightBuyPrice) $('#phrchase_lstprice_'+productID).css('color','red');
		else $('#phrchase_lstprice_'+productID).css('color','black');
		

		$('#phrchase_subtotal_'+productID).val(subTotal);
		orderTotal += subTotal; 
		
		
		FreightSubTotal = parseFloat($('#purchase_by_freight_'+productID).html()) * parseFloat($('#phrchase_num_'+productID).val());
		 FreightTotal += FreightSubTotal
		$('#purchase_by_freight_total_'+productID).html(formatFloat(FreightSubTotal,2));
		
	})	
	
	if(enterKey) var subt = 0;
	else 
	{	var subt = 0;
		if(p!=0) subt = (parseFloat(p) - parseFloat(orderTotal));
		if(freight!=0) subt += (parseFloat(freight) - (parseFloat(FreightTotal)- parseFloat(orderTotal)));
	}

	if(subt!=0)	
	{
		$('#purchase_subtotal_'+productID).val(parseFloat($('#purchase_subtotal_'+productID).val()) +parseFloat(subt));
		$('#purchase_by_freight_total_'+productID).val(parseFloat($('#purchase_by_freight_total_'+productID).val()) +parseFloat(subt));
	}
	
	
	$('#orderTotal').html(FreightTotal + subt);
	
	
	if($('input[name=taxType]:checked').val()==0)
	{
		tax = Math.round(parseFloat($('#orderTotal').html()) * 0.05 );
		v = parseFloat($('#orderTotal').html()) + parseFloat(tax);
		$('#aTaxTotal').html('含稅金額:'+v).fadeIn();;
		
	}
	
		
}
function jiSuan(obj)
	{
		var newStr = "";
		var count = 0;

		if(obj.value.indexOf(".")==-1)
		{
			for(var i=obj.value.length-1;i>=0;i--)
			{
				if(count % 3 == 0 && count != 0)
				{
					newStr = obj.value.charAt(i) + "," + newStr;
				}
				else
				{
					newStr = obj.value.charAt(i) + newStr;
				}
				count++;
			}
			obj.value = newStr + ".00";
		}
		else
		{
		    for(var i=obj.value.indexOf(".")-1;i>=0;i--)
			{
				if(count % 3 == 0 && count != 0)
				{
					newStr = obj.value.charAt(i) + "," + newStr;
				}
				else
				{
					newStr = obj.value.charAt(i) + newStr;
				}
				count++;
			}

			obj.value = newStr + (obj.value + "00").substr((obj.value + "00").indexOf("."),3);
		}

	}



function chkMinus(productID)
{
	if(fucCheckNUM($("#phrchase_num_"+productID).val())==0)
	{
		alert('這不是一個數字');
		$("#phrchase_num_"+productID).val(0);
	}
	else if($("#phrchase_num_"+productID).val()<0&&accountLevel<50)
	{
		alert('你必須有店長權限才能退貨');
		$("#phrchase_num_"+productID).val(0);
	}
	
	
}

function shipmentChangeCount()
{
	var spTotalCount =$('#spTotalCount').val();
	if(fucCheckNUM(spTotalCount))
	{
		$('.sp_count').val(spTotalCount) ;
		
	}
	else 
	{
		alert('請輸入數字');
		return;
	}
	
}


function shippingTable(data)
{
	var num = 0;

	
		$('#shippingTable').append(
		'<tr >'+
			'<td>'+data.productNum+'</td>'+
			'<td><input type="hidden" name ="sp_pID_'+data.productID+'" value='+data.productID+'>'+data.ZHName+'</td>'+
			'<td>'+data.ENGName+'</td>'+
			'<td><input type="hidden" name ="sp_price_'+data.productID+'" value='+data.price+'>'+data.price+'</td>'+
			'<td>'+data.num+'</td>'+
			'<td><input type="text" class="short_text sp_count"  name ="sp_count_'+data.productID+'" value='+data.phrchaseCount+'></td>'+
			'<td><input type="text" class="short_text" value="1" name = "sp_num_'+data.productID+'"></td>'+
		'</tr>'
		)
	height =parseInt($('.popUpBox').css('height').substr(0,$('.popUpBox').css('height').length-2))
	if(parseInt($('#shippingTable').css('height').substr(0,$('#shippingTable').css('height').length-2))+350>height) 
	{
		
		popUpBoxHeight(height+50);
		
	}

	
}
function purchaseSend(purchaseID)
{


	$('#IOMSG').html('正在更新庫存...');
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/purchase_send/"+purchaseID,
	   data: $("#purchaseForm").serialize(),
	   success: function(data){
		
		   $('.popUpBox').html(data);
		
		   if(data.result==true)
		   {
			   	$('#IOMSG').html('庫存更新完畢');
				// findProductStock('product',2,'auto');
					showRoomTable('purchase')
				 closePopUpBox();
				 productNumCheck(purchaseID);
				 
				 
				if(data.content) 
				{
					var myWindow = window.open("", "MsgWindow", "width=600, height=280");
					myWindow.document.write(data.content);
				
					openPopUpBox(data.content,600,280,'closePopUpBox');			
				}
				height =parseInt($('.popUpBox').css('height').substr(0,$('.popUpBox').css('height').length-2))
				popUpBoxHeight(height+50);
				
				 
				 
				 	
		   }
		   else alert('錯誤，請重新輸入');
	   }
	 });
	
	
	
	
}
function  purchaseNumChk(purchaseID)
{
   $.post('/product/get_product_check',{purchaseID:purchaseID},function(data)
	{
       
		if(data.result==true)
		{
			content='<h1>商品總量檢核結果</h1>';
     
        
            content+='<form id="productNumCheck" border="1"><table>'+
                     '<tr><td>時間</td><td>商品名稱</td><td>電腦紀錄庫存</td><td>入庫及架上總共數量</td></tr>';
            	for( var key in data.product)
                    {
                        if(data.product[key].stockNum!=data.product[key].checkNum) color = 'style="color:red"';
                        else color = '';
                        content+='<tr><td>'+data.product[key].time+'</td>'+
                                 '<td>'+data.product[key].ZHName+'</td>'+
                                 '<td '+color+' >'+data.product[key].stockNum+'</td>'+
                                 '<td '+color+' >'+data.product[key].checkNum+'</td>'+
                                  '</tr>';
                        
                        
                    }

                
            content+='</table>';
            openPopUpBox(content,600,280,'closePopUpBox');			
            
            
				
				
		}
       else productNumCheck(purchaseID);
		
	},'json')	
    
    
    
    
}







function productNumCheck(purchaseID)
{
    
   $.post('/product/get_purchase',{purchaseID:purchaseID,type:'purchase'},function(data)
	{
       
		if(data.result==true)
		{
			content='<h1>商品總量檢核</h1>';
            content+='<h2>請確實填寫數量</h2>';
            content+='<input type="hidden" id="check_purchaseID" value="'+purchaseID+'">'
            content+='<form id="productNumCheck"><table border="1">'+
                     '<tr><td>商品名稱</td><td>入庫及架上總共數量</td></tr>';
            	for( var key in data.product)
                    {
                        
                        content+='<tr><td>'+data.product[key].ZHName+'</td><td>'+
                                    '<input type="hidden" name="productID[]" value="'+data.product[key].productID+'">'+
                                 '<input type="text" class="short_text" name="checkNum[]"></td></tr>';
                        
                        
                    }

                
           content+='</table>';
        openPopUpBox(content,600,280,'productNumCheckSend');			
            
            
				
				
		}
		
	},'json')	
    
    
    
    
}

function productNumCheckSend()
{
    purchaseID = $('#check_purchaseID').val();

    $.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/product_num_check_send/"+purchaseID,
	   data: $("#productNumCheck").serialize(),
	   success: function(data){
		
	

		   if(data.result==true)
		   {
			 
				// findProductStock('product',2,'auto');
					showRoomTable('purchase')
				 closePopUpBox();
				 //productNumCheck(purchaseID);
				 
				 
								 
				 	
		   }
		   else alert('錯誤，請重新輸入');
	   }
	 });
	
    
    
    
    
    
}






function purchaseOrderSend(purchaseID)
{


	$('#IOMSG').html('正在更新...');
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/purchase_order_send/"+purchaseID,
	   data: $("#purchaseOrderForm").serialize(),
	   success: function(data){
		
		   $('.popUpBox').html(data);
		   if(data.result==true)
		   {
			   	$('#IOMSG').html('更新完畢');
				// findProductStock('product',2,'auto');
				showRoomTable('purchaseOrder');
				 closePopUpBox();
				 
				 
				 
				if(data.content) 
				{
					openPopUpBox(data.content,600,280,'closePopUpBox');			
				height =parseInt($('.popUpBox').css('height').substr(0,$('.popUpBox').css('height').length-2))
				popUpBoxHeight(height+50);
				}
				
					if(data.preTime=='7777-07-07')  var msg =  '廠商缺貨中';
					else if(data.preTime=='3333-03-03') var msg  = '目前暫無進貨計畫';
					else if(data.preTime=='9999-09-09') var msg  = '此商品已絕版';
					else var msg  = '到貨時間為：'+data.preTime;
			
				 	if(data.phantri.length>0)
  					if(confirm('有人在問題解決中心詢問本次相關產品到貨時間，是否一律以'+msg+'回覆'))
					{
						for(key in data.phantri ) preTimeSubmit(msg,data.phantri[key]);
					}
				 
				 	
		   }
		   else alert('錯誤，請重新輸入');
	   }
	 });
	
	
	
	
}
function print_purchase(purchaseID,type,status)
{
		$.post('/product/get_purchase',{purchaseID:purchaseID,type:type},function(data)
	{
		if(data.result==true)
		{
			if(status=='print')
				{
					
					orderNumber = 0;
			var	 content ='<h1 style="text-align:center;">採　購　單</h1><div id="orderAppendQuery"></div>';
			content +='<input type="hidden" id="shopDiscount" value="'+data.purchase.discount+'">'+
						'<input type="hidden" id="shopID" value="'+data.purchase.shopID+'">'+	
					'<form id="orderForm">'+
						
						'<input type="hidden" name="orderID" value="'+data.purchase.id+'">'
						
		
			
			content+='</div>';
			
			content +='<div id="orderTableContent"></div>';
		
		
				if(data.purchase.comment!=0&&data.purchase.comment!='')
				{
				content +='<h2>訂單備註：</h2><div style=" background-color:#CCC">'+
								data.purchase.comment+
								'</div>';
				}
		
			content +='</form>';
			$('.product').html(content)	
			$('#orderTableContent').html(purchaseTableList(data,type,'print'));
			if(data.purchase.tax==1)
            {
                $('#order_total_tax').html('<span color="red">上列金額已含稅</span>');
                var thisTotal = data.purchase.total;
            }
            else  var thisTotal = data.purchase.total*1.05;

			result = '<h3 style="float:right">總額  新台幣 '+formatCurrencyTenThou(thisTotal)+'   元整</h3>'+
			'<div style="clear:both"></div>';
            $('.product').append(result)

            contentCheck ='<div class="break" style="page-break-after:always;clear:both;">--以下空白--</div>';
					
					
					
					
				}
			else
				{
						contentCheck = '<h1 style="text-align:center;">進貨驗收單</h1><div id="orderTableCheckContent"></div>';
            $('.product').append(contentCheck)	
            $('#orderTableCheckContent').html(purchaseTableList(data,type,'check'));
					 $('#orderTableCheckContent').append('<h1>進貨注意事項(確認請打勾)：</h1>');
                    
                 
                    if(data.purchase.invoiceWay==0)
                    {    
                    	$('#orderTableCheckContent').append('<h2 style="color:red"><input type="checkbox" style="width:30px;height:30px">此單隨貨附發票，請拍照回傳，若有錯誤已經回報</h2>');
                    }
                    
                    
					$('#orderTableCheckContent').append('<h2><input type="checkbox" style="width:30px;height:30px">入庫數量皆已確認，如有錯誤已經回報</h2>');
                    $('#orderTableCheckContent').append('<h2><input type="checkbox" style="width:30px;height:30px">入庫商品外觀皆已檢查，如有錯誤已經回報</h2>');
                    $('#orderTableCheckContent').append('<h2><input type="checkbox" style="width:30px;height:30px">架上及新進總商品數量已經點清</h2>');
                    $('#orderTableCheckContent').append('<h2>進貨驗收簽名：_____________________</h1>');
				}
		
            /*
			'<div style="float:left">'+
				'訂購廠商名稱：幻遊天下股份有限公司<br/>'+                   
				'營利事業統一編號：53180059<br/>'+
				'帳戶資訊：中國信託(822)城東分行071540-245257<br/>'+
			'</div>'+
                                                               
			'<div style="float:right">'+
				'負責人 姓名：黃家樺<br/>'+
				'廠 商 電 話：02-22630120<br/>'+
				'廠 商 傳 真：02-22630110<br/>'+
				'廠 商 電 郵：service@phantasia.tw<br/>'+
			'</div>'+
			'<div style="clear:both"></div>'+
			'<img src="/images/seal.png" style="float:right"/>';
			*/
				
			//countTotal(1);
		}
		
	},'json')	
	
	
}

function formatCurrencyTenThou(num) {
    num = num.toString().replace(/\$|\,/g,'');
    if(isNaN(num))
    num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num*10+0.50000000001);
    cents = num%1;
    num = Math.floor(num/10).toString();
    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
   		 num = num.substring(0,num.length-(4*i+3))+','+num.substring(num.length-(4*i+3));
    return (((sign)?'':'-') + num );
}

function purchase_print(purchaseID,type,status)
{
	
	window.open('/product/print_out/'+type+'/'+purchaseID+'/'+status, "_blank");
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

function purchaseOrderAppendTable(data)
{
	purchaseID = $('#purchaseID').val();

	$.post('/product/purchase_order_append',{productID:data.productID,purchaseID:purchaseID},function(data)
	{
		if(data.result==true)
		{
			 showPurchase(purchaseID,'purchaseOrder')
			
		}
		
		
		
	},'json')	
	


}

function deletePurchaseProduct(id)
{
    
    if(confirm('你確定要刪除這個品項？'))
    {
        chagePriceSend(id,1);
        
        
    }
    
    
}

function chagePriceSend(id,deleteToken)
{
	purchaseID = $('#purchaseID').val();
		
	$.post('/product/change_purchase_price',{productID:$('#productID_'+id).val(),'purchase_preTime':$('#purchase_preTime').val(),'id':id,'purchasePrice':$('#purchasePrice_'+id).val(),num:$('#num_'+id).val(),deleteToken:deleteToken},function(data)
	{
	
		if(data.result==true)
		{
           
            if(deleteToken==1)
            {
                
                $('#orderRow_'+id).fadeOut();
            }
            
			changetotal(purchaseID)
			changePrice = parseInt($('#purchasePrice_'+id).val());
			price = parseInt($('#price_'+id).html());
			num = parseInt($('#num_'+id).val());
				
			$('#subtotal_'+id).html(parseFloat(Math.round(changePrice * num*100)/100))
			$('#purchaseCount_'+id).html(Math.round(changePrice*100/price,0)+'%')
		
			
		}
		
	},'json')
	
	
	
}

function changeInf()
{
	purchaseID = $('#purchaseID').val();
	
	$.post('/product/purchance_change_inf',{purchaseID:purchaseID,preTime:$('#purchase_preTime').val(),status:$('#purchase_status').val()},function(data)
	{
		if(data.result==true)
		{
			 showPurchase(purchaseID,'purchaseOrder')
			 showRoomTable('purchaseOrder');
		}	
		else alert('儲存錯誤');
		
		
	},'json')
		
		
	
	
	
}




function changetotal(purchaseID)
{
	//alert(purchaseID);
	$.post('/product/update_purchase_total',{purchaseID:purchaseID},function(data)
	{
		if(data.result==true)
		{
			$('#order_total').html('總價：'+formatCurrencyTenThou(data.total));	
			$('#order_total_tax').html('稅後：'+formatCurrencyTenThou(Math.round(data.total*1.05)));
		}
		
		
	},'json');
	
	
} 


function showPurchaseTest(purchaseID,type)
{
	 wareroomIO(type);
	
	dumpOrder(purchaseID,'purchase');

		$('#popUpBoxEnter').detach();
		$('#popUpBoxCancel').before('<input type="button" id="popUpBoxEnter" value="確定" class="big_button" onclick="$(\'#popUpBoxEnter\').hide();purchaseEditSend('+purchaseID+');">');
	$('#dumplistCavas').detach();
	$('.purchase_num').attr("readonly",true);
	
}

function purchaseEditSend(purchaseID)
{
		if($('#purchaseSuppliers').val()==0) 
		{
			$('#popUpBoxEnter').show()
			alert('請選擇供應商')	;
			
			return ;
			
			
		}
		
		$('#IOMSG').html('正在建立進貨資訊...');
		

			$.ajax({
		   type: "POST",
		   dataType:"json",
		   url: "/product/purchase_inf_edit/",
		   data: $("#supplierInf").serialize()+'&orderTotal='+$('#orderTotal').html()+'&purchaseID='+purchaseID,
		   success: function(data){
			
			 
			
			   if(data.result==true)
			   {
						
					$('#IOMSG').html('進貨資訊建立完成...');
					purchaseEditDetailSend(purchaseID)
						
					 
					 
						
			   }
			   else alert('錯誤，請重新輸入');
		   }
		 });
		
	
	
	
	
}

function purchaseEditDetailSend(purchaseID)
{
	$('#IOMSG').html('正在更新庫存...');
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/purchase_edit_send/"+purchaseID,
	   data: $("#purchaseForm").serialize(),
	   success: function(data){
		
		   $('.popUpBox').html(data);
		
		   if(data.result==true)
		   {
			   	$('#IOMSG').html('庫存更新完畢');
				// findProductStock('product',2,'auto');
					showRoomTable('purchase')
				 closePopUpBox();
				 
				 productNumCheck(purchaseID);
				 
				if(data.content) 
				{
					var myWindow = window.open("", "MsgWindow", "width=600, height=280");
					myWindow.document.write(data.content);
				
					openPopUpBox(data.content,600,280,'closePopUpBox');			
				}
				height =parseInt($('.popUpBox').css('height').substr(0,$('.popUpBox').css('height').length-2))
				popUpBoxHeight(height+50);
				
				 
				 
				 	
		   }
		   else alert('錯誤，請重新輸入');
	   }
	 });
	
	
}



function showPurchase(purchaseID,type)
{
	
	$.post('/product/get_purchase',{purchaseID:purchaseID,type:type},function(data)
	{
		if(data.result==true)
		{
			orderNumber = 0;
			var	 content ='<div id="purchaseOrderAppendQuery"></div>';
				content +='<input type="hidden" id="freight" value="0">';
				content +='<input type="hidden" id="purchaseID" value="'+purchaseID+'">';
				content +='<div id="orderTableContent"></div>';
				content +='<div>'+data.content+'</div>';
				content +='<div><input type="button" class="big_button" value="列印採購單" onclick="purchase_print('+purchaseID+',\''+type+'\',\'print\')">'	
			content +='<input type="button" class="big_button" value="列印進貨驗收單" onclick="purchase_print('+purchaseID+',\''+type+'\',\'check\')"></div>'	
				if(type=='purchase')	content +='<div><input type="button" class="big_button" value="修改價錢" onclick="showPurchaseTest('+purchaseID+',\''+type+'\')"></div>'	
				
				
				content +='<input type="button" value="前一張" onclick="showPurchase('+(parseInt(purchaseID)-1)+',\''+type+'\')" class="big_button">'+
						 '<input type="button" value="下一張" onclick="showPurchase('+(parseInt(purchaseID)+1)+',\''+type+'\')" class="big_button">'									
				 openPopUpBox(content,1100,200,'closePopUpBox',true);
				
				if(data.purchase.length==0)
				{
					 $('#orderTableContent').html(type+purchaseID+'查無資料')
					 return;
				}
				
			
				
				
				
	
				 $('#orderTableContent').html(purchaseTableList(data,type,status));
				
				 $('#purchase_status').val(data.purchase.statusID)
					var purchase_preTime = $( "#purchase_preTime").datepicker({
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
										
							},
							onClose:function(data)
								{
									
									//alert(id);
									 changeInf()
									
								}
						});		  	

					
				if(type=='purchaseOrder')
				
				{ 	
					queryProduct('purchaseOrderAppend','select');
					//popUpBoxHeight(height+500);
					
				}
				popUpBoxHeight(0);
				$('#popUpBoxEnter').bind('click',function(){showRoomTable(type)})
					if(data.purchase.tax==1) $('#order_total_tax').html('<span color="red">上列金額已含稅</span>');
			//countTotal(1);
		}
		
	},'json')	
	
	
}
var orderNumber;
function purchaseProductView(data,setting,status)
{
		var Zh = new Array('序號','品號','櫃號','中文','英文','語言','零售定價','進貨折數','進貨價格','進貨數量','缺貨數量','top100','小計','手冊','備註','銷售圖','刪除','檢核','總量');
	var Eng = new Array('order','productNum','cabinet','ZHName','ENGName','language','price','purchaseCount','purchasePrice','num','lackNum','top100','subtotal','rule','orderComment','saleDiagram','rowDelete','checkCol','totalNum');


		var content = ''
		if(orderKey==false)
		{
			content = '<tr style="background-color:#FFEBEB" id="order_header">';
			for( var key in Zh)
			{	
				
					if(typeof setting[Eng[key]] == "undefined" ||setting[Eng[key]]==true)
					{
					  	content+='<td>'+Zh[key]+'</td>';
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
		content+='<tr id="orderRow_'+data.rowID+'" style="background-color:'+color+';-webkit-print-color-adjust:exact;">';
		
		
		for(row in Zh)
		{
			
			if(typeof setting[Eng[row]] == "undefined" ||setting[Eng[row]]==true)
			{
				
				if(Eng[row]=='order')content+='<td>'+orderNumber+'</td>';
				else if(Eng[row]=='productNum')content+='<td>'+fillZero(data[Eng[row]])+'<input type="hidden" id="productID_'+data.rowID+'" value="'+data.productID+'"></td>';
				else if(Eng[row]=='rule')
				{
					if(data.rule==1)content+='<td style="color:red">附手冊</td>';
					else content+='<td>無</td>';
				}
				else if(Eng[row]=='purchasePrice' && status!='print')
				{
					content+='<td><input  id="purchasePrice_'+data.rowID+'" type="text" value="'+data.purchasePrice+'" onblur="chagePriceSend(\''+data.rowID+'\',0)" onchange="chagePriceSend(\''+data.rowID+'\',0)"></td>';	
					
				}
				else if(Eng[row]=='num'  && status!='print' && status!='check')
				{
					content+='<td><input id="num_'+data.rowID+'" type="text" value="'+data.num+'" onchange="chagePriceSend(\''+data.rowID+'\',0)"></td>';	
					
				}
				else if(Eng[row]=='purchaseCount')
				{
										
					purchaseCount = Math.round(parseInt(data.purchasePrice)*100/parseInt(data.price));
					content+='<td id="purchaseCount_'+data.rowID+'" onclick = "changePurchaseCount(\''+data.rowID+'\')">'+purchaseCount+'%</td>';

				}
				else if(Eng[row]=='subtotal')
				{
					content+='<td id="subtotal_'+data.rowID+'">'+parseFloat(Math.round(data['purchasePrice']*data['num']*100)/100) +
					'</td>';
				}
				else if(Eng[row]=='comment')
				{
					
					
					content+='<td><input class="productComment" type="text" id="productComment_'+data.rowID+'" name="'+Eng[row]+'_'+data.rowID+'" value="'+data['orderComment']+'"></td>';
				}			
				else if(Eng[row]=='saleDiagram')
				{
					
					
					content+='<td><input class="productComment" type="button" onmouseover="saleDiagram('+data.productID+',false)" value="銷量"></td>';
				}	
                else if (Eng[row]=='lackNum')
                    {
                        content+='<td style="color:red">'+data['lackNum']+'</td>';
                        
                        
                    }
                 else if (Eng[row]=='top100')
                    {
                        if(data['top100'] && data['top100'] > 0 )    content+='<td style="color:red">top100</td>';
                        else    content+='<td style="color:red"></td>';
                        
                        
                    }
                else if (Eng[row]=='rowDelete')
                    {
                        
                          content+='<td style="color:red"><input type="button" value="刪除" onclick=" deletePurchaseProduct('+data.rowID+')"/></td>';
                        
                        
                    }
                 else if (Eng[row]=='checkCol')
                    {
                        content+='<td>量：__缺：__<br/>損：__膜：__</td>';
                        
                        
                    }
				else if (data[Eng[row]])
				{
					content+='<td id="'+Eng[row]+'_'+data.rowID+'">'+data[Eng[row]]+'</td>';
					
				}
				else content+='<td id="'+Eng[row]+'_'+data.rowID+'"></td>';
			}
		}
			
		content+='</tr>';
  
    if(status=='check' && data['barcode'].length<10){
        
        content+='<tr>'+
            '<td  style="color:red" colspan="10" >此品項條碼可能有誤，請於系統入庫時更正</td></tr>';
        
    }
			return content;
}

function saleDiagram(productID,op)
{
	
	content='<div><h1>銷售曲線</h1><iframe src="/product_flow/product_sale_flow/'+productID+'" style="width:650px; height:400px"><iframe><div>';
	
	if(op==false)$('#diagram').html(content);
	 else openPopUpBox(content,650,600,'closePopUpBox');

	
	
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
	$('#purchasePrice_'+id).focus().select();
	$('#purchasePrice_'+id).val( 
		Math.round(parseInt($('#price_'+id).html())*parseInt($('#purchaseCount_'+id).html().substr(0,parseInt($('#purchaseCount_'+id).html().length)-1))/100));
	//countTotal(99);	
	purchaceCountKey = true;
}

function countTotal(NUM)
{
	//未修正
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
			for(i=0;i<5;i++)
			{
		
				if($('#concessions_'+id+'_'+i).length>0)
				{
					if(num>=parseInt($('#concessionsNum_'+id+'_'+i).html()))
					{
						
						$('#sellPrice_'+id).val( Math.round(parseInt($('#price_'+id).html())*parseInt($('#concessions_'+id+'_'+i).html())/100))
						
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



function purchaseTableList(data,type,status)
{
	orderKey =false;
    vcheck = false;
    if(status=='check') vcheck = true;
	if(type=='purchase'||data.purchase.status=='已到貨')
	{
		status='print';
		data.purchase.preTime = data.purchase.timeStamp;
	}
	width = 1100;
	
	if(data.purchase.type==1) way = '寄賣';
    else way = '買斷';
    
	content=
	'<div>'+
	'單號：'+type+data.purchase.purchaseID+' 建立時間：'+data.purchase.timeStamp+' 供應商：'+data.purchase.supplier+' 狀態:'+way+' '+data.purchase.status+' <br/>';;
	if(status=='print'|| status=='check')content+='預計到貨日：'+data.purchase.preTime;
	else
	{
		
		 content+='預計到貨日<input type="text" id="purchase_preTime" name="purchase_preTime" value="'+data.purchase.preTime+'"class="big_text">';
		/*
		 content+='<select id="purchase_status" onchange="changeInf()">'+
		 				'<option value="0">採購中</option>'+
						'<option value="1">採購單送出</option>'+
						'<option value="2">採購單成立</option>'+
		 		'</select>';
			*/
				if(data.purchase.lastStatus!='')content+='<input type="button" class="big_button" value=" 退回: '+data.purchase.lastStatus+'" onclick = "$(\'#purchase_status\').val('+data.purchase.lastStatusID+');changeInf();">'
	
		content+='目前狀態:'+data.purchase.status+'<input type="hidden"  id="purchase_status" readonly="readonly" value="'+data.purchase.statusID+'">'
		
		
		

		
		
		if(data.purchase.nextStatus!='')
			if(data.purchase.nextLevel==0|| data.purchase.nextLevel==$('#level').val())
				content+='<input type="button" class="big_button" value=" Next: '+data.purchase.nextStatus+'" onclick = "$(\'#purchase_status\').val('+data.purchase.nextStatusID+');changeInf();">'
					
	}
	
	
	content+= '<table border="1" id="purchaseOrderTable" style="width:'+width+'px;text-align:center" >';
	if(type=='showingPrint') 
	{
		content+='<tr><td colspan="15">採購單編號：o'+data.product[0].purchaseID+'</td></tr>';
		type= 'print';
	}
	;
	product = data.product;

	num = 0 ;
    
     if(vcheck==true)
    {
    var setting ={price:false,purchaseCount:false,purchasePrice:false,lackNum:false,top100:false,subtotal:false,orderComment:false,rowDelete:false,saleDiagram:false}  ;  
        
  

        
    }
    else if(status=='print')
    {
    var setting ={rowDelete:false,saleDiagram:false,checkCol:false}  ;  
        
        

        
    }
   
    else var setting = {checkCol:false};
    
    
	for( var key in product)
	{
		
		
		content+=purchaseProductView(product[key],setting,status)
		
		
		
		 num += parseInt(product[key].num);
		
		
	} 	
				
	content+='</table>';
	content+='<div style="text-align:right;width:'+width+'px">種類：'+(parseInt(key)+1)+'</div>';
	content+='<div style="text-align:right;width:'+width+'px">件數：'+(parseInt(num))+'</div>';
	if(status!='check')
	{
		
		content+='<div style="text-align:right;width:'+width+'px" id="order_total">總價：'+formatCurrencyTenThou(data.total)+'</div>';
	content+='<div style="text-align:right;width:'+width+'px" id="order_total_tax">稅後：'+formatCurrencyTenThou(Math.round(data.total*1.05))+'</div>';
		
	}
		
	content+='<div id="diagram"></div>';
	content+='</div>';
	
					
						
			
	
	return content;	
}




function getPurchaseList(offset,num,type)
{

	orderListOffset= offset+num;
	var s = $('#purchase_suppliers').val();

	$.post('/product/get_purchase_list',{offset:offset,num:num,type:type,suppliers:$('#purchase_suppliers').val(),status:100},function(data)
	{
		if(data.result==true)
		{
			if(offset==0)
			{
				$('#product_list').html(wareroomTable(type));		
				$('#purchase_suppliers').val(s);
			}
			for( var key in data.purchaseList)
			{
				if(data.purchaseList[key].tax==1)
				{
					data.purchaseList[key].bTaxTotal = formatCurrencyTenThou(data.purchaseList[key].total/1.05);
					 data.purchaseList[key].total = formatCurrencyTenThou( data.purchaseList[key].total )
				}
				else
				{
					 data.purchaseList[key].bTaxTotal =formatCurrencyTenThou (data.purchaseList[key].total);
					 data.purchaseList[key].total = formatCurrencyTenThou(Math.round(data.purchaseList[key].total*1.05));
					
					
				}
				
				
                	if(data.purchaseList[key].type==1) way = '寄賣';
                else way = '';
    
                
				var content ='<tr id="order_'+data.purchaseList[key].purchaseID+'">';
					content+=
						'<td id="orderNum_'+data.purchaseList[key].purchaseID+'">p'+data.purchaseList[key].purchaseID+'</td>'+
						'<td>'+data.purchaseList[key].timeStamp+'</td>';
					if(type=='purchase')content+='<td><input type="text" onfocus="showEditMsg('+data.purchaseList[key].purchaseID+')"  id="accountTime_'+data.purchaseList[key].purchaseID+'" value="'+data.purchaseList[key].accountTime+'"></td>';
					content+='<td>'+data.purchaseList[key].supplier+'</td>'+
						'<td style="text-align:right">$'+data.purchaseList[key].bTaxTotal+'</td>'+
						'<td style="text-align:right">$'+data.purchaseList[key].total+'</td>'+
						'<td>'+data.purchaseList[key].comment+'</td>'+
						'<td>'+way+' '+data.purchaseList[key].status+'</td>'+
						'<td>'+

							'<input type="button" value="查看" onclick="showPurchase('+data.purchaseList[key].purchaseID+',\''+type+'\')"class="big_button" >'+
						'</td>';
                if(type=='purchase')
                    content+=  '<td>'+
							'<input type="button" value="入庫檢核" onclick="purchaseNumChk('+data.purchaseList[key].purchaseID+')" class="big_button" >'+
						'</td>';
				
				if(type=='purchaseOrder')		
				content+='<td><input type="button" value="刪除" onclick="deletePurchaseOrder('+data.purchaseList[key].purchaseID+')"class="big_button" ></td>';


		


						content+='</tr>';
	
				$('#warmroom_list').append(content)
				if(type=='purchase')
				{
				   
				   var dates = $( "#accountTime_"+data.purchaseList[key].purchaseID ).datepicker({
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
								
						
									editPurchaseAccounting(focusID);
									
							}
							
						});		  	
				   
				   
				  }
				
			}
		}
		else
		{
				$('#warmroom_list').append('沒有其他資料了');
				$('#moreOrderBtn').hide();
		}
	},'json')	
	
	
}

var focusID = 0;
function showEditMsg(id)
{
	editPurchaseAccounting(focusID)
$('#editMsgIn').detach();
	$('body').append('<div id="editMsgIn" style=" margin-left:500px; color:red ;position:absolute; z-index:2; background-color:white;'+
	 'top:'+($(document).scrollTop()+200)+'px;left:200px; ";>請按一下旁邊即可儲存</div>');
		focusID = id;
		

}

function editPurchaseAccounting(id)
{
	if(id==0) return;
	$.post('/product/edit_purchase_accounting',{purchaseID:id,accountTime:$('#accountTime_'+id).val()},
	function(data)
	{
		if(data.result==true)	$('#editMsgIn').html($('#orderNum_'+id).html()+'已儲存');
		else $('#editMsgIn').html('儲存失敗，請洽管理員');
		setTimeout(function(){$('#editMsgIn').detach();},2000);
	},'json');
	
	
}

function deletePurchaseOrder(purchaseID)
{
if(confirm('你確定要刪除？'))
	{
		$.post('/product/delete_purchase_order',{purchaseID:purchaseID},function(data)
		{
			if(data.result==true)
			{	
			
				$('#order_'+purchaseID).detach();
			}
		},'json')
	}	
	
}


function showRoomTable(type)
{
		
		var s = $('#purchase_suppliers').val();
	
		$('#product_list').html(wareroomTable(type));
	  $("#purchase_suppliers").val(s);
		


		orderListOffset= 0;
		getPurchaseList(orderListOffset,15,type);	
	
			 
	
	
	
}


function wareroomTable(type)
{
	
	var  result='';
	r = $('#product_suppliers').html();
	if(type=='purchase') result+='<h1>進貨紀錄 <span style=" font-size:14pt"><input type="text" id="create_supplier_search"  placeholder="搜尋廠商名稱" onkeyup="shopSearch(\'suppliers_option\',\'create_supplier_search\',\'purchase_suppliers\',false)">請選擇供應商</span><select id="purchase_suppliers" onchange="showRoomTable(\''+type+'\')">'+r+'</select>'+
    '<input type="button" class="big_button" value="查詢" onclick="showRoomTable(\''+type+'\')">'+
    '<input type="button" class="big_button" value="進貨月報" onclick="getMonthSupplier()"></h1>';
	else if(type=='purchaseOrder') result+='<h1>採購清單 <span style=" font-size:14pt"><input type="text" id="create_supplier_search"  placeholder="搜尋廠商名稱" onkeyup="shopSearch(\'suppliers_option\',\'create_supplier_search\',\'purchase_suppliers\',false)">請選擇供應商</sapn><select id="purchase_suppliers" " onchange="showRoomTable(\''+type+'\')">'+r+'</select><input type="button" class="big_button" value="查詢" onclick="showRoomTable(\''+type+'\')"></h1>'+
        '<input type="button" class="big_button" value="預算表設定" onclick="budgetSet()">';
	result+='<img src="/images/purchase.jpg" style=" width:1000px"><table id="warmroom_list" border="1" style="text-align:center">'+
				'<tr>';

		result+='<td>單號</td>';
		result+='<td>建立日期</td>';
		if(type=='purchase')result+='<td>發票日期</td>';
		result+='<td>供應商名稱</td>'+
				'<td>總價(稅前)</td>'+
				'<td>總價(稅後)</td>'+
				'<td style="width:200px;">備註</td>'+
				'<td>狀況</td>';
		result+='<td>查看</td>';
        result+='<td></td>';
		result+='<td></td>';
		result+='</tr>'+
			'</table>';
	
		result+= '<input type="button" value="查看更多" id="moreOrderBtn" onclick="getPurchaseList(orderListOffset,10,\''+type+'\');"class="big_button"/>';
	
	return result;
}


function getMonthSupplier()
{
    r = $('#product_suppliers').html();
    
					content='<h1>請選擇店家</h1>'+
							'<input type="text" id="create_supplier_search_month"  placeholder="搜尋廠商名稱" onkeyup="shopSearch(\'suppliers_option\',\'create_supplier_search_month\',\'purchase_suppliers_month\',false)">請選擇供應商</span><select id="purchase_suppliers_month" onchange="getMonthPurchase()">'+r+'</select><br/>'+
							'<select id="monthCheck_year" name="year" onchange="changeMonth()"></select>年'+
							'<select id="monthCheck_month" name="month" onchange="changeMonth()"></select>月'+
							
							'<input type="text" id="monthFromDate"> ~ <input type="text" id="monthToDate">'+
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
					
					changeMonth();
					
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
										getMonthPurchase();
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
										getMonthPurchase();
								}
						});		  	
				
			
		
    
}
function changeMonth()
{
    
    $('#monthFromDate').val($('#monthCheck_year').val()+'-'+$('#monthCheck_month').val()+'-'+'1');
	//	nextMonth = parseInt($('#monthCheck_month').val())+1;
   $('#monthToDate').val($('#monthCheck_year').val()+'-'+$('#monthCheck_month').val()+'-'+31)
    getMonthPurchase();;
    
}

function getMonthPurchase()
{
    supplierID = $('#purchase_suppliers_month').val();
    
    from =  $('#monthFromDate').val();
    to =  $('#monthToDate').val();
    $('#check_container').html('<img src="/images/ajax-loader.gif"/>');

    $.post('/product/get_month_purchase',{from:from,to:to,supplierID:supplierID},function(data)
          {
            if(data.result==true)
            {
          
				$('#check_container').html(
                '<table id="month_list" border="1" style="text-align:center">'+
				'<tr>'+
                    '<td>單號</td>'+
		            '<td>建立日期</td>'+
	               '<td>發票日期</td>'+
		          '<td>供應商名稱</td>'+
				  '<td>總價(稅前)</td>'+
				  '<td>總價(稅後)</td>'+
				  '<td style="width:200px;">備註</td>'+
				  '<td>狀況</td>'+
		          '<td>查看</td>'+
                  '<td></td>'+
		       '</tr>'+
			     '</table>'
                
                );		
                var allTotal= 0 ;var  allbTaxTotal= 0 ;
			for( var key in data.purchaseList)
			{
				if(data.purchaseList[key].tax==1)
				{
			         allTotal+= parseInt(data.purchaseList[key].total);
                    allbTaxTotal+= parseInt(data.purchaseList[key].total/1.05 );
                    
                    data.purchaseList[key].bTaxTotal = formatCurrencyTenThou(data.purchaseList[key].total/1.05);
					 data.purchaseList[key].total = formatCurrencyTenThou( data.purchaseList[key].total )
				}
				else if(data.purchaseList[key].tax==0)
				{
                     allTotal+=  parseInt(data.purchaseList[key].total*1.05 );
                    allbTaxTotal+= parseInt(data.purchaseList[key].total) ;
                    
					 data.purchaseList[key].bTaxTotal =formatCurrencyTenThou (data.purchaseList[key].total);
					 data.purchaseList[key].total = formatCurrencyTenThou(Math.round(data.purchaseList[key].total*1.05));
					
					
				}
                else{
                    
                       allTotal+=  parseInt(data.purchaseList[key].total);
                    allbTaxTotal+= parseInt(data.purchaseList[key].total) ;
                    
					 data.purchaseList[key].bTaxTotal =formatCurrencyTenThou (data.purchaseList[key].total);
					 data.purchaseList[key].total = formatCurrencyTenThou(Math.round(data.purchaseList[key].total));
                    
                    
                    
                }
				
				
                	if(data.purchaseList[key].type==1) way = '寄賣';
                else way = '';
    
                
				var content ='<tr id="order_'+data.purchaseList[key].purchaseID+'">';
					content+=
						'<td id="orderNum_'+data.purchaseList[key].purchaseID+'">p'+data.purchaseList[key].purchaseID+'</td>'+
						'<td>'+data.purchaseList[key].timeStamp+'</td>';
					content+='<td>'+data.purchaseList[key].accountTime+'</td>';
					content+='<td>'+data.purchaseList[key].supplier+'</td>'+
						'<td style="text-align:right">$'+data.purchaseList[key].bTaxTotal+'</td>'+
						'<td style="text-align:right">$'+data.purchaseList[key].total+'</td>'+
						'<td>'+data.purchaseList[key].comment+'</td>'+
						'<td>'+way+' '+data.purchaseList[key].status+'</td>'+
						'<td>'+

							'<input type="button" value="查看" onclick="showPurchase('+data.purchaseList[key].purchaseID+',\'purchase+\')"class="big_button" >'+
						'</td>';
                
                   


						content+='</tr>';
                 
	
				$('#month_list').append(content)
                
                
                }
                $('#check_container').prepend('總金額(稅前)：'+allbTaxTotal+' 稅後：'+allTotal)
                 popUpBoxHeight(0);
            
            }
        
        
    },'json')
    
    
}


function supplierDeleteSend(id)
{
	if(confirm('您確定要刪除這個供應商？'))
	if(confirm('資料刪除無法回復，請您確認'))
	$.post('/product/supplier_delete',{id:id},
		function(data)
		{	
		if(data.result==true)	$('#editMsgIn').html($('#sulpplierName_'+id).val()+' 資料已刪除');
		else $('#editMsgIn').html('刪除失敗，請洽管理員');
		setTimeout(function(){$('#editMsgIn').detach();},2000);
			editSupplier();
			
		},'json')	
	
	
}

function newSupplierSend()
{
	$.post('/product/supplier_edit',{name:$('#editSupplierName').val(),order:$('#editSupplierOrder').val(),day:$('#editSupplierDay').val()},
function(data)
		{	
		if(data.result==true)	$('#editMsgIn').html('資料已儲存');
		else $('#editMsgIn').html('儲存失敗，請洽管理員');
		setTimeout(function(){$('#editMsgIn').detach();},2000);
			editSupplier();
			
		},'json')	
	
}

function supplierEditSend(id)
{
	
	
	$.post('/product/supplier_edit',{id:id,name:$('#supplierName_'+id).val(),order:$('#supplierOrder_'+id).val(),day:$('#supplierDay_'+id).val(),IDNumber:$('#supplierID_'+id).val(),phone:$('#supplierPhone_'+id).val(),address:$('#supplierAddress_'+id).val(),email:$('#supplierEmail_'+id).val(),invoice:$('#supplierInvoice_'+id).val(),invoiceWay:$('#supplierInvoiceWay_'+id).val()},
		function(data)
		{	
		if(data.result==true)	$('#editMsgIn').html($('#sulpplierName_'+id).val()+' 資料已儲存');
		else $('#editMsgIn').html('儲存失敗，請洽管理員');
		setTimeout(function(){$('#editMsgIn').detach();},2000);
			
			
		},'json')	
	
	
}





function  showSupplierNewBox()
{
		$('#editSupplierBox').slideDown();			
	
}
function magicSet()
{
	$.post('/system/get_shop',{},function(data){
				if(data.result==true)
				{
					content='<input type="button" value="新增魔風店家"class="big_button" onclick="$(\'#newMagicShop\').toggle()">'+
							'<div style="display:none; background-color:#FFBB00; " id="newMagicShop">'+
							'<h1>請選擇店家</h1>'+
							'<select id="magic_shopID"></select>'+
							'<input type="button" value="確定新增"class="big_button" onclick="selectNewMagicShop()">'+

							'</div>'
							;
					
					content +='<div style="clear:both"></div><table id="magicTable" border="1" style="float:left">'+	
						'<tr>'+
							'<td>店家名稱</td>'+
							'<td>店家等級</td>'+
							'<td>進貨方式</td>'+
							'<td>刪除</td>'+
						'</tr>'+
					'</table>';
					
					
					content +='<div style="float:left; margin-left:10px;margin-bottom:10px "><h1>魔風直送管理</h1><div id="magicOrderCavans"></div></div>';
					
					
					
					content +='<div style="float:left; margin-left:10px"><h1>魔風集單管理</h1><div id="magicOrderCollectCavans"></div></div>';
					
				
					$('#product_list').html(content);
					for( var key in data.shopData)
					{
						
							$('#magic_shopID').append('<option value="'+data.shopData[key].shopID+'">'+data.shopData[key].name+'</option>');
						
					}
					
					$.post('/order/get_all_magic_status',{},function(data)
					{
						
						
						if(data.result==true)
						{
							
							for( var key in data.magicShop)appendMagicTable(data.magicShop[key]);
							
							
						}
						
					},'json')
					
					getMagicOrder();
					getMagicOrderCollect();
					
				}
			
		
		},'json')
	

	
	
	
	
	
}
function newPokemon()
{
    
    
    
    
}

function savePokemon(id)
{

    $.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/pokemon_set",
	   data: $("#poke_"+id).serialize(),
	   success: function(data){
		
         
		   if(data.result==true)
		   {  
			 pokemonSet();
			
		   }
           else alert('儲存失敗');
		  
	   }
	 });
	

	
    
    
}


function pokemonSet()
{
	$.post('/order/get_pokemon',{},function(data){
				if(data.result==true)
				{
					content='<input type="button" value="新增商品"class="big_button" onclick="newPokemon()">';
	                content +='<div style="clear:both"></div><table id="pokemonTableList" border="1" style="float:left">'+	
						'<tr>'+
							'<td></td>'+
							
							'<td>刪除</td>'+
						'</tr>'+
					'</table>';
	
                   
					$('#product_list').html(content);
					for( var key in data.product)
					{
                     
                        content='<tr>'+
                                    '<td>'+
                                    '<form id="poke_'+data.product[key].id+'">'+    
                                    '<h1><input type="hidden" name="id" value="'+data.product[key].id+'">'+
                                    '名稱：<input class="big_text" type="text" name="name" value="'+data.product[key].name+'">'+
                                    ' 價錢：<input class="medium_text" type="number" name="sellPrice" value="'+data.product[key].sellPrice+'"></h1>';
                             
                        for(var each in data.product[key].detail)
                            {
                                content+=
                                    '<span class="p_'+data.product[key].detail[each][0]+'">'+data.product[key].detail[each][0]+'</span>'+
                                    '<input type="hidden" name="productID[]" value="'+data.product[key].detail[each][0]+'">'+    
                                    ' 數量<input  class="medium_text" type="number" name="num[]" value="'+data.product[key].detail[each][1]+'">'+
                                    ' 金額<input  class="medium_text" type="number" name="eachSellPrice[]" value="'+data.product[key].detail[each][2]+'">'+
                                    '<br/>';        
                                
                                getProductInf(data.product[key].detail[each][0]);
                            }
                        content+='</form></td>'+
                            '<td><input type="button" value="儲存" class="big_button" onclick="savePokemon('+data.product[key].id+')"><input type="button" value="刪除" class="big_button" onclick="deletePokemon('+data.product[key].id+')"></td></tr>';
						$('#pokemonTableList').append(content);
					
					}
					
					
				}
			
		
		},'json')
	

	
	
	
	
	
}

function deletePokemon(id)
{
    if(confirm('您確定要刪除？'))
      $.post('/product/delete_pokemon',{id:id},function(data){
        
        if(data.result==true)
            {
                
               	pokemonSet()
                
            }
        
        
        
    },'json')
    
    
}

function getProductInf(productID)
{
    $.post('/product/get_product_inf',{productID:productID},function(data){
        
        if(data.result==true)
            {
                
                $('.p_'+productID).html(data.inf.ZHName);
                
            }
        
        
        
    },'json')
    
    
    
}

function newPokemon()
{
	content='<h1>新增寶可夢販售商品商品</h1><div id="pokemonQuery"></div><div id="pokemon_list"></div>';
	openPopUpBox(content,1100,480,'newPokemonSend')
	$('#pokemon_list').html(
	'<form id="newPokemonForm"><table id="pokemonListNewTable" border="1" width="900px" style=" font-size:14pt">'+
		'<tr>'+
			'<th>商品編號</th>'+
			'<th>中文名稱</th>'+
			'<th>英文名稱</th>'+
			'<th>語言</th>'+
			'<th>取消</th>'+
		'</tr>'+
	'</table></form>')
	queryProduct('pokemon','select');

	
	
	
}
function newPokemonSend()
{
		
	$.ajax({
		   type: "POST",
		   dataType:"json",
		   url: "/product/new_pokemon_send",
		   data: $("#newPokemonForm").serialize(),
		   success: function(data){
			 
			 $('.popUpBox').html(data)
			   if(data.result==true)
			   {  
				
						  
					closePopUpBox();
					pokemonSet()
				
			   }
			  
		   }
		 });
	
	
}


var pokemonNewNum = 0 ;
function pokemonTable(data)
{
	$('#pokemonListNewTable').append(
		'<tr id="newPokemon_'+pokemonNewNum+'">'+
			'<td><input type="hidden" name="newPokemonProduct_'+data.productID+'" value="'+data.productID+'">'+data.productNum+'</td>'+
			'<td>'+data.ZHName+'</td>'+
			'<td>'+data.ENGName+'</td>'+
			'<td>'+data.language+'</td>'+
			'<td><input type="button" value="取消" onclick="$(\'#newPokemon_'+pokemonNewNum+'\').detach()"></td>'+
		'</tr>')
	pokemonNewNum++;
				
	height =parseInt($('#pokemonListNewTable').css('height').substr(0,$('#pokemonListNewTable').css('height').length-2))
	popUpBoxHeight(height+480);
}


                    
                    

function getMagicOrderCollect()
{
	$('#magicOrderCollectCavans').html('<img src="/images/ajax-loader.gif"/>');
	$.post('/product/get_week_magic_order',{},function(data)
	{
		
		if(data.result==true)
		{
			$('#magicOrderCollectCavans').html('<table id="magicOrderCollectTable" border="1" >'+	
						'<tr>'+
							'<td>商品中文</td>'+
							'<td>商品英文</td>'+
							'<td>集單數量</td>'+
							'<td>現在庫存</td>'+
							'<td>尚需採購</td>'+
							'<td>分配</td>'+
						'</tr>'+
					'</table>');
				for( var key in data.product)
							{
								if(data.product[key].nowNum<0) shortNum = -data.product[key].nowNum;
								else shortNum = 0;
								
								nowNum =(parseInt(data.product[key].nowNum)+parseInt(data.product[key].buyNum));
								
								$('#magicOrderCollectTable').append(
									'<tr>'+
										'<td>'+data.product[key].ZHName+'</td>'+
										'<td>'+data.product[key].ENGName+'</td>'+
										'<td>'+data.product[key].buyNum+'</td>'+
										'<td>'+nowNum+'</td>'+
										'<td>'+shortNum+'</td>'+
										'<td><input type="button" value="訂貨狀況" class="big_button" onclick="showAllocate('+data.product[key].productID+')"></td>'+
									'<tr>'
								
								
								)
							}
							
			
			
		}
		
		
		
	},'json')
	
	
	
	
}

function getMagicOrder()
{
		$('#magicOrderCavans').html('<table id="magicOrderTable" border="1" >'+	
						'<tr>'+
							'<td>店家名稱</td>'+
							'<td>訂單編號</td>'+
							'<td>下單時間</td>'+
							'<td>總額</td>'+
							'<td>查看</td>'+
							'<td>刪除</td>'+
							'<td>採購入庫送物流</td>'+
						'</tr>'+
					'</table>');
	$.post('/order/get_magic_order',{},function(data)
					{
						
						
						if(data.result==true)
						{
							
							for( var key in data.magicOrder)
							{
								$('#magicOrderTable').append(
									'<tr>'+
										'<td>'+data.magicOrder[key].name+'</td>'+
										'<td>O'+data.magicOrder[key].orderNum+'</td>'+
										'<td>'+data.magicOrder[key].orderTime+'</td>'+
										'<td>'+data.magicOrder[key].total+'</td>'+
										'<td><input type="button" value="查看" onclick="	window.open(\'/order/print_out/order/'+data.magicOrder[key].orderID+'\', \'_blank\')"  class="big_button" ></td>'+
										'<td><input type="button" value="刪除" onclick="magicOrderDelete('+data.magicOrder[key].orderID+')" class="big_button" ></td>'+
										'<td><input type="button" value="採購入庫送物流" onclick="magicSendOut('+data.magicOrder[key].orderID+')"  class="big_button" ></td>'+
									'<tr>'
								
								
								)
							}
							
							
						}
						
					},'json')
		
	
	
}
function magicOrderDelete(orderID)
{
	if(confirm('你確定要刪除這份魔風訂單~?'))
	{
		$.post('/product/delete_magic_order',{orderID:orderID},function(data)
		{
			if(data.result==true)
			{
				alert('已經將訂單完全刪除');
					getMagicOrder();
			}
		},'json')
	}
	
}
function magicSendOut(orderID)
{
	if(confirm('現在將幫您完成 魔風直送訂單的後續手續'))
	{
		content='<h1>目前正在自動完成，請不要關閉</h1>';
		
		content+='<h2><span id="status_0">。</span>配貨數量設定</h2>';
		content+='<h2><span id="status_1">。</span>建立採購單</h2>';
		content+='<h2><span id="status_2">。</span>入庫</h2>';
		content+='<h2><span id="status_3">。</span>建立出貨單</h2>';//orderToShipment($orderID)
		content+='<h2><span id="status_4">。</span>送達物流</h2>';//$.post('/order/shipment_to_out',{'shipmentID':id},function(data)
		
		openPopUpBox(content,1000,300,'Send');		
		magicProcess(orderID);
	}
}


function magicProcess(orderID)
{
	$.post('/product/get_magic_status',{orderID:orderID},function(data)
	{
		if(data.result==true)
		{
			var status = parseInt(data.status)
			for(i = 0 ;i<status;i++)$('#status_'+ i).html('<img src="/images/confirm.png"/>');
			$('#status_'+status).html('<img src="/images/ajax-loader.gif"/>');
			switch(status)
			{
				case 0:
				//配貨			
			
				magicProcessfun('magic_allocate',{orderID:orderID});
				break;	
				case 1:
				//採購
				magicProcessfun('magic_purchase_order',{orderID:orderID});
				break;
				case 2:
				//入庫
				magicProcessfun('magic_purchase_in',{orderID:orderID,purchaseID:data.purchaseID});
				
				break;
				case 3:
				//出貨
				magicProcessfun('magic_order_to_shipment',{orderID:orderID});
				
				break;
				case 4:
				//送物流
				magicProcessfun('magic_ship_out',{orderID:orderID,shipmentID:data.shipmentID});
				break;
				case 5:
				//完成
					alert('完成所有手續');
					closePopUpBox()
					getMagicOrder();
				break;
				
				
				
			}	
			
			
		}
		
		
	},'json')		
	
}

function magicProcessfun(url,dataIn)
{
	orderID = dataIn.orderID;
	$.post('/product/'+url,dataIn,function(data)
	{
		
		if(data.result==true) magicProcess(orderID);
		
	},'json')	
	
}













function selectNewMagicShop()
{
	
	var data = {shopName:$('#magic_shopID :selected').text(),shopID:$('#magic_shopID').val(),core:0,six:0};
	
	appendMagicTable(data);
	
}


function appendMagicTable(data)
{
	var content = '<tr id="s_'+data.shopID+'">'+
						'<td>'+data.shopName+'</td>'+
						'<td><select id="core_'+data.shopID+'" onchange=" changeMagicType('+data.shopID+')"><option value="0">非核心</option><option value="1">核心</option></select></td>'+
						'<td><select id="six_'+data.shopID+'" onchange=" changeMagicType('+data.shopID+')"><option value="2">彈性出貨，但滿6就直送</option></select></td>'+
						'<td><input type="button" class="big_button" value="刪除" onclick="deleteMagicShop('+data.shopID+')"></td>'
					'<tr>';
	
	
	$('#magicTable').append(content);
	$('#core_'+data.shopID).val(data.core);
	$('#six_'+data.shopID).val(data.six);
	
					
	
	
}
function deleteMagicShop(shopID)
{
	
	if(confirm('你確定要刪除？'))	
	$.post('/order/delete_magic_shop',{shopID:shopID},function(data)
	{
		
		if(data.result==true)
		{
			
				$('#s_'+shopID).detach();
			
			
		}
		
	},'json')	
	
	
	
}
function changeMagicType(shopID)
{
	
	$.post('/order/change_magic_type',{shopID:shopID,core:$('#core_'+shopID).val(),six:$('#six_'+shopID).val()},function(data)
	{
		if(data.result==true) ;
		else alert('儲存錯誤')
		
	},'json')
	
	
}





function editSupplier()
{
	var content ='<input type="button" value="新增供應商"class="big_button" onclick="showSupplierNewBox()">'+	
					'<div id="editSupplierBox" style="display:none; background-color:#FFBB00; height:50px">'+
					'請輸入名稱：<input type="text" class="medium_text" id="editSupplierName" value="">'+
					'<input type="hidden" id="editSupplierID" class="short_text" value="0">'+
					'順序：<input type="text" id="editSupplierOrder"class="short_text" value="99">'+
					'到貨天數：<input type="text" id="editSupplierDay" value="30">'+
					'<input type="button" value="確認"class="big_button"   onclick = "newSupplierSend()"/>'+
					'<input type="button" value="取消"class="big_button"  onclick="$(\'#editSupplierBox\').hide(\'\')"/>'+
					'</div>	';
	
	$('#productQuery').html(content)		
	$.post('/product/get_suppliers',{},function(data)
	{
		if(data.result==true)
		{
			
			$('#product_list').html('<table id="supplierTable" border="1"></table>');
				var tableContent = '<tr style="background-color:#FFC991">'+
						'<td>供應名稱</td>'+
						'<td>排列順序</td>'+
						'<td>進貨時程</td>'+
						'<td>統一編號</td>'+
						'<td>連絡電話</td>'+
						'<td>廠商地址</td>'+
						'<td>電子信箱</td>'+
						'<td>是否開立發票</td>'+
						'<td></td>'+
					'</tr>'	;
				$('#supplierTable').append(tableContent)
				
				for( var key in data.suppliers)
				{
					
					
					var color = '';
					if(key%2==0) color='style="background-color:#EEF"';
					if(key%15==14)$('#supplierTable').append(tableContent);
					
					var content = '<tr '+color+'>'+
			'<td><input type="text" onblur="supplierEditSend('+data.suppliers[key].supplierID+')" id="supplierName_'+data.suppliers[key].supplierID+'" value="'+data.suppliers[key].name+'"></td>'+
			'<td><input type="text" class="short_text" onfocus="showEditMsg()" onblur="supplierEditSend('+data.suppliers[key].supplierID+')" id="supplierOrder_'+data.suppliers[key].supplierID+'" value="'+data.suppliers[key].order+'"></td>'+
			'<td><input type="text" class="short_text" onfocus="showEditMsg()"  onblur="supplierEditSend('+data.suppliers[key].supplierID+')" id="supplierDay_'+data.suppliers[key].supplierID+'" value="'+data.suppliers[key].day+'"></td>'+
			'<td><input type="text" class="medium_text" onfocus="showEditMsg()"  onblur="supplierEditSend('+data.suppliers[key].supplierID+')" id="supplierID_'+data.suppliers[key].supplierID+'" value="'+data.suppliers[key].IDNumber+'"></td>'+
			'<td><input type="text" class="big_text"  onfocus="showEditMsg()" onblur="supplierEditSend('+data.suppliers[key].supplierID+')" id="supplierPhone_'+data.suppliers[key].supplierID+'" value="'+data.suppliers[key].phone+'"></td>'+
			'<td><input type="text" class="big_text"  onfocus="showEditMsg()"  onblur="supplierEditSend('+data.suppliers[key].supplierID+')" id="supplierAddress_'+data.suppliers[key].supplierID+'" value="'+data.suppliers[key].address+'"></td>'+
			'<td><input type="text" class="big_text" onfocus="showEditMsg()"  onblur="supplierEditSend('+data.suppliers[key].supplierID+')" id="supplierEmail_'+data.suppliers[key].supplierID+'" value="'+data.suppliers[key].email+'"></td>'+
			'<td>'+
				'<select  onchange="supplierEditSend('+data.suppliers[key].supplierID+')" id="supplierInvoice_'+data.suppliers[key].supplierID+'">'+
					'<option value="0">外加</option>'+
					'<option value="1">內含</option>'+
					'<option value="2">不開</option>'+
				'</select>'+
                '<select  onchange="supplierEditSend('+data.suppliers[key].supplierID+')" id="supplierInvoiceWay_'+data.suppliers[key].supplierID+'">'+
					'<option value="0">隨貨附</option>'+
					'<option value="1">整批附</option>'+
					
				'</select>'+
			'</td>'+
			'<td><input type="button"  class="big_button"  value="刪除" onclick="supplierDeleteSend('+data.suppliers[key].supplierID+')"/ ></td>';
					
					$('#supplierTable').append(content);
					$('#supplierInvoice_'+data.suppliers[key].supplierID).val(data.suppliers[key].invoice);
					$('#supplierInvoiceWay_'+data.suppliers[key].supplierID).val(data.suppliers[key].invoiceWay);
				}

			
			
	
			
		}
		
		
		
	},'json')
	
	
		
	
	
	
}
function showEditMsg(id)
{
	$('#editMsgIn').detach();
	$('body').append('<div id="editMsgIn" style=" margin-left:500px; color:red ;position:absolute; z-index:2; background-color:white;'+
	 'top:'+($(document).scrollTop()+200)+'px;left:200px; ";>請按一下旁邊即可儲存</div>');

	
	

}



function supplierEditFinish()
{
	supplierEditSend();
	closePopUpBox();
	location.reload();
	
}

function productAnnounceTable(data)
{
	
	var num = 0;
		$('#productAnnounceTable').append(
		'<tr >'+
			'<td>'+data.productNum+'</td>'+
			'<td><input type="hidden" name ="phrchase_productID_'+data.productID+'" value='+data.productID+'>'+data.ZHName+'</td>'+
			'<td>'+data.ENGName+'</td>'+
			'<td>'+data.language+'</td>'+
			'<td><input type="text" name="url_'+data.productID+'"></td>'+
			'<td><textarea  name="comment_'+data.productID+'" style="width:300px; height:300px"></textarea></td>'+
			'</tr>'
		)
	
	height =parseInt($('.popUpBox').css('height').substr(0,$('.popUpBox').css('height').length-2))
	if(parseInt($('#productAnnounceTable').css('height').substr(0,$('#productAnnounceTable').css('height').length-2))+350>height) 
	{
		
		popUpBoxHeight(height+50);
		
	}
	
	
	
	
	
}


function productAnnounceSend()
{
	
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/announce",
	   data: $("#productAnnounceForm").serialize(),
	   success: function(data){
		 
		   if(data.result==true)
		   {  
				alert('信件已寄出');
				closePopUpBox();
	
			
		   }
		  
	   }
	 });
	

	
	
	
}
function productIOType()
{ 	

		
		$('#productQuery').html(
                    '<script type="text/javascript" src="/javascript/pos_order.js"></script>'+
					'from<input type="text" id="from">'+' to<input type="text" id="to">'+
					'<label><input type="checkBox" value="1" checked="checked" id="passZero">略過0</label>'+
					'<label><input type="checkBox" value="1" checked="checked" id="shopGroup">合併店家</label>'+
					'<select id="IOset">'+
						'<option value="IO">進貨+出貨</option>'+
						'<option value="I">進貨</option>'+
						'<option value="O">出貨</option>'+
					'</select>'+
					'<select id="CBset">'+
						'<option value="CB">寄賣+買斷</option>'+
						'<option value="B">買斷</option>'+
						'<option value="C">寄賣</option>'+
					'</select>'+
					'<input type="text" class="big_text query" id="query" value=""  onkeyup="if(enter()){productIOOffset=0;showProductIO(100)}">'+
					'店家名稱：<input type="text" class="big_text query "id="shopQuery">'+
					'<input type="button" class="big_button"   value="查詢" onclick="productIOOffset=0;showProductIO(100)"/>'
					
				)	
				today = new Date();
				toDate = today.getFullYear()+'-'+(parseInt(today.getMonth())+1)+'-'+today.getDate();
				lastday = new Date();
				lastday.setMonth(lastday.getMonth()-1)
				fromDate = lastday.getFullYear()+'-'+(parseInt(lastday.getMonth())+1)+'-'+lastday.getDate();
				$('#from').val(fromDate);
				$('#to').val(toDate);
			var dates = $( "#from, #to" ).datepicker({
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
						}
					});		  	
				


}




var productIOOffset = 0;
var pin = 0;
var pout = 0;
var pback = 0;
function showProductIO(num)
{
	if($('#shopGroup').is(":checked"))
	{
		var shopGroup = 1;
		num = 999999;
	}
	else var shopGroup = 0;
	
 $('#waitting').show();
	$.post('/product/get_product_IO',{shopID:0,offset:productIOOffset,num:num,query:$('#query').val(),from:$('#from').val(),to:$('#to').val(),'shopQuery':$('#shopQuery').val(),'shopGroup':shopGroup},function(data)
	{
		
		if(data.result==true)
		{
			if(productIOOffset==0)
			{
				
				//initialized
				$('#product_list').html('');
				$('#product_list').html('<table id="productIOTable" border="1"></table><img  id="waitting" src="/images/ajax-loader.gif"/>');	
				$('#product_list').append('<div id="showMore"><input type="button"  class="big_button" onclick=" showProductIO(100)" value="查看更多"/></div>');	
				$('#productIOTable').append('<tr><td>產品編號</td><td>產品名稱</td><td>語言</td><td>出貨單號</td><td>日期</td><td>進貨數量</td><td>退貨數量</td><td>出貨數量</td><td>地點</td>'+
				'<td>收件人</td><td>類型</td><td>出貨價</td><td>進貨價</td></tr>')
				 pin = 0;
				 pout = 0;
				 pback = 0;
				 psell = 0;
				
			}
			var passCount = 0;
			var IOset = $('#IOset').val();
			var CBset = $('#CBset').val();
			if(data.product.length>0)
			for(key in data.product)
			{
				
				if($('#passZero').is(":checked") && data.product[key].inNum==0&& data.product[key].outNum==0 && data.product[key].backNum==0)passCount++;
				else
				{
					if((IOset=='IO'||(IOset=='I'&&(data.product[key].toWhere==''||data.product[key].backNum!=0))||(IOset=='O'&&data.product[key].toWhere!=''))&& ((CBset=='B'&&data.product[key].type==0)||(CBset=='C'&&data.product[key].type==1)||CBset=='CB') )
					{
					
						switch(data.product[key].type)
						{
							
							case '0':
								type='買斷';
							break;
							case '1':
								type='<span style="color:red">寄賣</span>';
							break;
							default:
								type='';
							break;	
						}
						$('#productIOTable').append('<tr>'+
						'<td>'+data.product[key].productNum+'</td>'+
						'<td>'+data.product[key].ZHName+'('+data.product[key].ENGName+')</td>'+
						'<td>'+data.product[key].language+'</td>'+
						'<td>s'+data.product[key].shippingNum+'<input type="button" value="查看" onclick="showShipment('+data.product[key].shipmentID+',\'staff\')"></td>'+
						'<td>'+data.product[key].time.substr(0,16)+'</td>'+
						'<td style=" text-align:right">'+data.product[key].inNum+'</td>'+
						'<td style=" text-align:right">'+data.product[key].backNum+'</td>'+
						'<td style=" text-align:right">'+data.product[key].outNum+'</td>'+
						'<td style=" text-align:right">'+data.product[key].toWhere+'</td>'+
						'<td style=" text-align:right">'+data.product[key].receiver+'</td>'+
						'<td style=" text-align:right">'+type+'</td>'+
						'<td style=" text-align:right">'+Math.round(data.product[key].sellPrice)+'</td>'+
						'<td style=" text-align:right">'+Math.round(data.product[key].purchasePrice)+'</td>'+
						'</tr>');
						pin+=parseInt(data.product[key].inNum);
						pout+=parseInt(data.product[key].outNum);
						pback+=parseInt(data.product[key].backNum);
						if(data.product[key].type==0)psell+=parseInt(data.product[key].sellPrice * data.product[key].outNum);
						
					}
					else passCount++;
				}
			}
			else
			{
				$('#productIOTable').append('<tr><td></td>'+
						'<td></td>'+
						'<td></td>'+
						'<td></td>'+
						'<td style=" text-align:right">'+pin+'</td>'+
						'<td style=" text-align:right">'+pback+'</td>'+
						'<td style=" text-align:right">'+pout+'</td>'+
						'<td style=" text-align:right"></td>'+
						'<td style=" text-align:right">'+psell+'</td>'+
						'</tr>');
				 $('#showMore').html('沒有更多其他的資料了')
			}
			productIOOffset += num;
			if(passCount!=0)showProductIO(passCount);
			else $('#waitting').hide();
			
		}
		
		
		
	},'json')	
	
	
}

function newProductAnnounce()
{
	var type = 	'productAnnounce';
			
	var content = '<div id="'+type+'Query"></div>'+
			  '<div style=" float:left; height:400px;">'+
			  '<form id="'+type+'Form">'+
			  '<table id="'+type+'Table" border="1" width="1000px">'+
			  	'<tr>'+
					'<td>產品編號</td>'+
					'<td>中文名稱</td>'+
					'<td>英文名稱</td>'+
					'<td>語言</td>'+
					'<td>網址</td>'+
					'<td>備註</td>'+
			
				'</tr>'+
			  '</table>'+
			  '</form>'
			  '</div>';

	 openPopUpBox(content,1000,600,''+type+'Send');		
	 //getShipmentShop(0);
	 queryProduct(type,'select');	
	
	
	
}

function top10Send(productID)
{
	if($('#top10_'+productID).is(":checked")) top10 = 1;
	else top10 = 0;
	$.post('/product/top10_product',{top10:top10,productID:productID},function(data)
	{
		
		
		
	},'json')
		
	
	
}
function eliminateProduct(shopID)
{
		$.post('/product/get_eliminate_product',{shopID:shopID},function(data)
	{
		if(data.result==true)
		{
		content='<h1>淘汰商品清單</h1>'
		content += '<select onchange="eliminateProduct(this.value)">';
		
		for(i=0;i<=10;i++)
		{
			if(shopID ==i )content+='<option selected="selected">'+i+'</option>';
			else	content+='<option>'+i+'</option>';
		}
		content+='</select>';
		content+= '<table id="topProductTable" border="1"  class="tablesorter"></table><table id="notTopProductTable" border="1"  class="tablesorter"></table>';
			openPopUpBox(content,1000,600,'closePopUpBox');		
			var header = '<thead><tr><th>序號</th><th>十大</th><th>編號</th><th>產品名稱</th><th>語言</th><th>庫存</th><th>各店庫存</th>'+
										'<th>近六月出貨</th><th>近六月賣出</th><th>連結</th></tr></thead>';
			$('#notTopProductTable').append(header);
			
			
			i=0;
			for(key in data.notProduct)
			{
				if(data.notProduct[key].nowNum==0&&data.notProduct[key].shopNum==0&&data.notProduct[key].sellNum&&data.notProduct[key].orderNum) continue;
				i++;
				$('#notTopProductTable').append('<tr id="notProduct'+data.notProduct[key].productID+'"><td>'+i+'</td><td>'+fillZero(data.notProduct[key].productNum)+'</td>'+
											'<td></td>'+
											'<td>'+data.notProduct[key].ZHName+'('+data.notProduct[key].ENGName+')</td><td>'+data.notProduct[key].language+'</td>'+
											'<td>'+data.notProduct[key].nowNum+'</td>'+
											'<td>'+data.notProduct[key].shopNum+'</td>'+
											'<td>'+data.notProduct[key].orderNum+'</td>'+
											'<td>'+data.notProduct[key].sellNum+'</td>'+
											'<td><a href="http://www.phantasia.tw/bg/home/'+data.notProduct[key].phaBid+'" target="_blank">連結</td>'+
											'</tr>');
			}			
			height =parseInt($('#notTopProductTable').css('height').substr(0,$('#notTopProductTable').css('height').length-2))
			popUpBoxHeight(height+100);
			//$("#topProductTable").tablesorter({widgets: ['zebra'],sortList: [[7,0],[6,0]]});
				$("#notTopProductTable").tablesorter({widgets: ['zebra'],sortList: [[6,1],[5,1]]});
		}
		
		
		
	},'json')	
	
	
	
	
}
function getShouldOrderNum(productID)
{
		var day = parseInt($('#day_'+productID).html());
		var comNum = parseInt($('#comNum_'+productID).html());
		var shopNum = 0; //always ingone shop stock
		var orderNum = parseInt($('#orderNum_'+productID).html());
		var sellNum = parseInt($('#sellNum_'+productID).html());
		var boxcase = parseInt($('#case_'+productID).val());
		var shouldOrder = parseInt($('#shouldOrder_'+productID).html());


		var num = Math.round(((sellNum*1.5+orderNum) - comNum - shopNum)  *day/90 ,0);
		if(num<0) num = 0;
		if(isNaN(num)) num = 0;
		if(num+comNum<0) num = num-comNum;
		if(boxcase==0)
		{
			var result = num;
		}
		else
		{
			if((num%boxcase)/boxcase>0.33)
			{
					var result = Math.floor(num / boxcase)+1;
			}
			else var result = Math.floor(num / boxcase);
	
			
		}
		$('#shouldOrder_'+productID).html(parseInt(result));

}





var shouldNum = 0;
 function getComNum(productID,subtotal)
 {
					
			var num = parseInt($('#comNum_'+productID).html());
			if(subtotal*1.2>num)
			{
				 $('#comNum_'+productID).css("color","red");
				 
			}
		

	
		
		

	 
	 
	 
	 
	 
}

function changeCase(productID)
{
 $.post('/product/change_case',{productID:productID,case:$('#case_'+productID).val()},function(data){
		
		if(data.result==false) alert('儲存失敗');
		else getShouldOrderNum(productID);
		
		
		
	},'json');
	
	
	
}

function changePreTimeNum(productID)
{
	
	$.post('/order/change_pretime',{productID:productID,preTime:$('#preTime_'+productID).val(),num:$('#alreadyOrder_'+productID).val()},
	function(data){
				if(data.preTime=='7777-07-07')  var msg =  '廠商缺貨中，尚不知詳細到貨日';
					else if(data.preTime=='3333-03-03') var msg  = '目前暫無進貨計畫';
					else if(data.preTime=='9999-09-09') var msg  = '此商品已絕版';
					else var msg  = '到貨時間為：'+data.preTime;
		
				if(data.phantri.length>0)
  					if(confirm('有人在問題解決中心詢問本產品到貨時間，是否一律以到貨時間：'+msg+'回覆，並且結案？'))
					{
						 preTimeSubmit(msg,data.phantri);
					}
				 
		
		
	},'json')
	
	
	
	
}



function getAlreadyOrder(productID)
{
	$.post('/product/already_order',{productID:productID},function(data){
	
				if(data.result==true)
				{
						 $('#preTime_'+productID).val(data.pre.preTime)
						  $('#alreadyOrder_'+productID).val(data.pre.num)
			
			
				}
					var dates = $('#preTime_'+productID).datepicker({
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
									 changePreTimeNum(productID)
									
								}
							});			
	},'json')

	
}
function supplierChangeSend(productID)
{
	var value = $('#editSuppliers_'+productID).val();
	$.post('/product/edit_single_item_product',{productID:productID,itemName:'suppliers',value:value},function(data)
	{
		if(data.result==true)
		{
				$('#supplier_'+productID).html(data.ret)
			
		}
		
		
	},'json')
	
	
	
}


function supplierChange(productID)
{
	$('#supplier_'+productID).html(
	'<select name="suppliers" id="editSuppliers_'+productID+'" onchange="supplierChangeSend('+productID+')">'+suppliers+'</select>');
	
	
}

function getNotTopProduct()
{
	$.post('/product/get_not_top_product',{topProduct:1},function(data)
	{

		i=0;
			for(key in data.notProduct)
			{
				//if(data.notProduct[key].comNum>=0) continue;
				i++;
				$('#notTopProductTable').append('<tr id="notProduct'+data.notProduct[key].productID+'"><td>'+i+'</td><td>'+fillZero(data.notProduct[key].productNum)+'</td>'+
											'<td></td>'+
											'<td>'+data.notProduct[key].ZHName+'('+data.notProduct[key].ENGName+')</td><td>'+data.notProduct[key].language+'</td>'+
											'<td>'+data.notProduct[key].supplierName+'</td>'+
											'<td>'+data.notProduct[key].day+'</td>'+
											'<td>'+data.notProduct[key].nowNum+'</td>'+
											'<td id="comNum_'+data.notProduct[key].productID+'">'+data.notProduct[key].comNum+'</td>'+
											'<td>'+data.notProduct[key].orderNum+'</td>'+
											'<td>'+data.notProduct[key].sellNum+'</td>'+
											'<td></td>'+
											'<td></td>'+
											'<td><input type="button" value="加入150清單" onclick="addTopProduct('+data.notProduct[key].productID+')"></td>'+
											'<td><a href="http://www.phantasia.tw/bg/home/'+data.notProduct[key].phaBid+'" target="_blank">連結</td>'+
											'</tr>');
										//	getComNum(data.notProduct[key].productID,(data.notProduct[key].shopNum+data.notProduct[key].nowNum));	
			}		
			$("#notTopProductTable").tablesorter({widgets: ['zebra'],sortList: [[10,1],[9,1]]});
				
	},'json')		
}


function getTopProduct()
{
	
	$('#product_list').html('<img  id="waitting" src="/images/ajax-loader.gif"/>');
	$.post('/product/get_top_product',{topProduct:1},function(data)
	{
		if(data.result==true)
		{
		content='<h1>150產品清單</h1><input type="button" value="重新排序" onclick=\'$("#topProductTable").trigger("update");\'><table id="topProductTable" border="1"  class="tablesorter"></table><table id="notTopProductTable" border="1"  class="tablesorter"></table>';
			//openPopUpBox(content,1400,600,'closePopUpBox');		
			$('#product_list').html(content);
			var header = '<thead><tr><th>序號</th><th>十大</th><th>編號</th><th>產品名稱</th><th>語言</th><th>供應商</th><th>到貨天數</th><th>庫存</th><th>配完庫存</th>'+
										'<th>近三月出貨</th><th>近三月賣出</th><th>成箱數</th><th>應訂箱數</th><th>分配</th><th>銷量</th><th>已定數量</th><th>到貨時間</th><th>連結</th><th>上架</th></tr></thead>';
			$('#topProductTable').append(header);
			$('#notTopProductTable').append(header);
			i=0;
			for(key in data.product)
			{
				chk ='';
				if(data.product[key].top10==1) chk='checked="checked"';
			
				i++;
				$('#topProductTable').append('<tr id="topProduuct_'+data.product[key].productID+'"><td>'+i+'</td>'+
											'<td ><input type="checkbox" id="top10_'+data.product[key].productID+'" onclick="top10Send('+data.product[key].productID+')" '+chk+'></td>'+
											'<td>'+fillZero(data.product[key].productNum)+'</td><td>'+data.product[key].ZHName+'('+data.product[key].ENGName+')</td><td>'+data.product[key].language+'</td>'+
											'<td  id="supplier_'+data.product[key].productID+'" ondblclick="supplierChange('+data.product[key].productID+')">'+data.product[key].supplierName+'</td>'+
											'<td id="day_'+data.product[key].productID+'">'+data.product[key].day+'</td>'+
											'<td>'+data.product[key].nowNum+'</td>'+
											'<td id="comNum_'+data.product[key].productID+'">'+data.product[key].comNum+'</td>'+
								
											'<td id="orderNum_'+data.product[key].productID+'">'+data.product[key].orderNum+'</td>'+
											'<td id="sellNum_'+data.product[key].productID+'">'+data.product[key].sellNum+'</td>'+
											'<td><input type="text"  onchange="changeCase('+data.product[key].productID+')" class="short_text" id="case_'+data.product[key].productID+'" value="'+data.product[key]['case']+'"></td>'+
											
											'<td id="shouldOrder_'+data.product[key].productID+'">'+data.product[key].shouldOrder+'</td>'+
											'<td><input type="button" value="分配" onclick="showAllocate('+data.product[key]['productID']+')"></td>'+
											'<td><input type="button" value="銷量" onclick="saleDiagram('+data.product[key]['productID']+',true)"></td>'+
										
											'<td ><input type="text" value="0" class="short_text" id="alreadyOrder_'+data.product[key].productID+'" onchange="changePreTimeNum('+data.product[key].productID+')" value="'+data.product[key].pre.num+'"></td>'+
											'<td><input type="text" value="0000-00-00" id="preTime_'+data.product[key].productID+'" value="'+data.product[key].pre.preTime+'"></td>'+
											'<td><input type="button" value="刪除" onclick="deleteTopProduct('+data.product[key].productID+')"></td>'+
											'<td><a href="http://www.phantasia.tw/bg/home/'+data.product[key].phaBid+'" target="_blank">連結</td>'+
                                             '<td><input type="button" value="上傳商城" onclick="uploadMart('+data.product[key].productID+')" id="uploadMartBtn_'+data.product[key].productID+'"></td>'+	
                                             
											'</tr>');
                       checkMart(data.product[key].productID,'uploadMartBtn_'+data.product[key].productID);
					getComNum(data.product[key].productID,(data.product[key].shopNum+data.product[key].nowNum));
						
					
					getAlreadyOrder(data.product[key].productID);							
			}
			shouldNum = i;
		
			$("#topProductTable").tablesorter({
				headers:{13:{sorter:'digit'},12:{sorter:'digit'},11:{sorter:'digit'},10:{sorter:'digit'},9:{sorter:'digit'},8:{sorter:'digit'}},
				
				widgets: ['zebra'],sortList: [[10,0],[9,0]]});
			getNotTopProduct()
		}
		
		
		
	},'json')	
	
}

function showAllocate(productID)
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
            '　配完庫存：<span id="remain"></span> <input type="button" value="按比例分配貨品" onclick="autoAllocate('+productInf.remainNum+')"></div>';

			content += '<form id="allocateForm">';
			content += '<input type="hidden" name="productID" id="allocateProductID" value="'+productID+'">';
			content +='<table border="1" style="width:1100px;text-align:center" >';				
			content += '<tr style="background-color:#FFEBEB">'+
							'<td>店家</td>'+
							'<td>訂貨單號</td>'+
							'<td>訂貨時間</td>'+
							'<td>訂貨數量</td>'+
							'<td>配貨數量</td>'+
					   '<tr>';
			
			var totalNum = 0;
			for( var key in data.product )		   
			{
				if(key % 2 ==0) bgColor ='white';
				else bgColor = '#EEE';
				
				
				content += '<tr id="allocateOrderRow_'+data.product[key].rowID+'" style="background-color:'+bgColor+'">'+
								'<td id="shopName_'+key+'">'+data.product[key].shopName+'</td>'+
								'<td>'+ magicCheck(data.product[key].magic)+' '+data.product[key].orderNum+'</td>'+
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
			openPopUpBox(content,1100,280,'reAllocateSend');
			countRemain();
		}
	},'json')	
	
	
	

}


function addTopProduct(productID)
{
	
	$.post('/product/add_top_product',{productID:productID},function(data)
			{	
				if(data.result==true)
				{
					alert('加入完成');
					$('#notProduct'+productID).detach();	
					chkTopProduct(productID);
				}
			
			}
		,'json')
	
}

function deleteTopProduct(productID)
{
	if(confirm('你確定要刪除？'))
	{
		$.post('/product/delete_top_product',{productID:productID},function(data)
			{	
				if(data.result==true)
				{
						alert('已離開150商品');
					$('#topProduuct_'+productID).detach();	
					chkTopProduct(productID);
				}
			
			}
		,'json')
	}
}
function newPackage()
{
	content='<h1>新增盒進包出商品</h1><h2>單數項為盒進，雙數項為包出</h2><div id="packageQuery"></div><div id="package_list"></div>';
	openPopUpBox(content,1100,480,'newPackageSend')
	$('#package_list').html(
	'<form id="packageForm"><table id="packageListNewTable" border="1" width="900px" style=" font-size:14pt">'+
		'<tr>'+
			'<th>商品編號</th>'+
			'<th>中文名稱</th>'+
			'<th>英文名稱</th>'+
			'<th>語言</th>'+
			'<th>取消</th>'+
		'</tr>'+
	'</table></form>')
	queryProduct('package','select');

	
	
	
}
function newPackageSend()
{
		
	$.ajax({
		   type: "POST",
		   dataType:"json",
		   url: "/product/new_package_send",
		   data: $("#packageForm").serialize(),
		   success: function(data){
			 
			 $('.popUpBox').html(data)
			   if(data.result==true)
			   {  
				
						  
					closePopUpBox();
					 editPackage();
				
			   }
			  
		   }
		 });
	
	
}


var packageNewNum = 0 ;
function packageTable(data)
{
	$('#packageListNewTable').append(
		'<tr id="packageNew_'+packageNewNum+'">'+
			'<td><input type="hidden" name="packageNewProduct_'+data.productID+'" value="'+data.productID+'">'+data.productNum+'</td>'+
			'<td>'+data.ZHName+'</td>'+
			'<td>'+data.ENGName+'</td>'+
			'<td>'+data.language+'</td>'+
			'<td><input type="button" value="取消" onclick="$(\'#packageNew_'+packageNewNum+'\').detach()"></td>'+
		'</tr>')
	packageNewNum++;
				
	height =parseInt($('#packageListTable').css('height').substr(0,$('#packageListTable').css('height').length-2))
	popUpBoxHeight(height+480);
}







//盒進包出
function editPackage()
{
	
	$('#productQuery').html(
		'<div class="divider"></div>'+
		'<div style=" background-color:#66F">'+
			'<input class="big_button" style=" background-color:#906"  type="button" value="新增商品" onclick="newPackage()">'+
		'</div>'+
		'<div class="divider"></div>'
	)
	
	$.post('/product/get_package',{},function(data)
	{
		if(data.result==true)
		{
			$('#product_list').html('<table id="packageListTable" border="1" style="width:880px">'+
				'<tr>'+
					'<td>盒裝商品名稱</td>'+
					'<td>每盒可換包數</td>'+
					'<td>散裝商品名稱</td>'+
					'<td>刪除</td>'+
				'</tr>'+
			'</table>')
		for(key in data['package'])
		{
			
				$('#packageListTable').append(		
				'<tr>'+
						'<td>1盒 '+data['package'][key].boxZHName+'('+data['package'][key].boxENGName+')['+data['package'][key].boxLanguage+']</td>'+
						'<td>可換 <input type="text" id="package_'+data['package'][key].boxProductID+'"  value="'+data['package'][key].unitToBox+'" onfocus="showEditMsg(package_'+data['package'][key].boxProductID+')"  onchange="packageEdit(this.value,'+data['package'][key].boxProductID+')"></td>'+
						'<td>'+data['package'][key].unitZHName+'('+data['package'][key].unitENGName+')['+data['package'][key].unitLanguage+']</td>'+
						'<td><input type="button" value="刪除" onclick="deltePackage('+data['package'][key].boxProductID+')"></td>'+
						
					'</tr>'
			)
			
		}
				
			
		}	
		
		
	},'json')
	
	
	
	
	
	
}


var focusID = 0;
function showEditMsg(id)
{
	$('#'+focusID).trigger('change');
$('#editMsgIn').detach();
	$('body').append('<div id="editMsgIn" style=" margin-left:500px; color:red ;position:absolute; z-index:2; background-color:white;'+
	 'top:'+($(document).scrollTop()+200)+'px;left:200px; ";>請按一下旁邊即可儲存</div>');
		focusID = id;

}
function deltePackage(boxProductID)
{
	if(confirm('你確定要刪除?'))
	$.post('/product/delete_package',{boxProductID:boxProductID},function(data){
		if(data.result==true)
		{
			editPackage()
			alert('已刪除')
			
			
		}
		
	},'json')
	
	
	
}





function packageEdit(num,boxProductID)
{
	$.post('/product/package_edit',{num:num,boxProductID:boxProductID},function(data){
		if(data.result)
		{
			
			
			if(data.result==true)	$('#editMsgIn').html('已儲存');
			else $('#editMsgIn').html('儲存失敗，請洽管理員');
			setTimeout(function(){$('#editMsgIn').detach();},2000);
			
			
		}
		
	},'json')
	
	
	
	
	
}


function consumeTableIn(data)
{
	
		$('#consumeListTable').append(
			'<tr id="consume_'+data.productID+'">'+
				'<td><input type="hidden" name="packageNewProduct_'+data.productID+'" value="'+data.productID+'">'+data.productNum+'</td>'+
				'<td>'+data.ZHName+'</td>'+
				'<td>'+data.ENGName+'</td>'+
				'<td>'+data.language+'</td>'+
				'<td><input type="button" value="刪除" onclick=" deleteConsume('+data.productID+')"></td>'+
			'</tr>')
	
	
	
}


function consumeTable(data)
{
	productData = data
	$.post('/product/new_consume',{productID:data.productID},function(data){
		if(data.result==true)
		{
			consumeTableIn(productData)
	
		}
	},'json')
}






function editConsume()
{
	
	$('#productQuery').html('<h1>新增消耗品</h1><div id="consumeQuery"></div><div id="consume_list"></div>')
	
	
	$('#consume_list').html(
	'<form id="consumeForm"><table id="consumeListTable" border="1" width="900px" style=" font-size:14pt">'+
		'<tr>'+
			'<th>商品編號</th>'+
			'<th>中文名稱</th>'+
			'<th>英文名稱</th>'+
			'<th>語言</th>'+
			'<th>取消</th>'+
		'</tr>'+
	'</table></form>')
	queryProduct('consume','select');
	$.post('/product/get_consume',{},function(data)
	{
		if(data.result==true)
		{
			
			for(key in data.consume)
			{
		
				consumeTableIn(data.consume[key]);
				
			}	
			
		}
		
	},'json')
	
}





function deleteConsume(productID)
{
	if(confirm('你確定要刪除?'))
	$.post('/product/delete_consume',{productID:productID},function(data){
		if(data.result==true)
		{
			$('#consume_'+productID).detach();
			
			
		}
		
	},'json')
	
	
	
}

//prepay
function prepayTableIn(data)
{
	
		$('#prepayListTable').append(
			'<tr id="prepay_'+data.productID+'">'+
				'<td><input type="hidden" name="prepayNewProduct_'+data.productID+'" value="'+data.productID+'">'+data.productNum+'</td>'+
				'<td id="prepayName_'+data.productID+'">'+data.ZHName+'</td>'+
				'<td>'+data.ENGName+'</td>'+
				'<td>'+data.language+'</td>'+
				'<td><input type="text" class="short_text" onchange="editPackingNum('+data.productID+')" id="packingNum_'+data.productID+'"  value="'+data.packingNum+'"></td>'+
				'<td><input type="text" onchange="editPackingNum('+data.productID+')" id="comment_'+data.productID+'"  value="'+data.comment+'"></td>'+
				'<td><input type="button" value="刪除" onclick=" deletePrepay('+data.productID+')"></td>'+
				'<td id="prepayNum_'+data.productID+'">0</td>'+
				'<td id="preNum_'+data.productID+'">0</td>'+
				'<td id="sumNum_'+data.productID+'">0</td>'+
				
			'</tr>')
	getPrepayNum(data.productID);
		
	
}
function getPrepayNum(productID)
{
	$.post('/order/get_pre_paynum',{productID:productID},function(data)
	{
		if(data.result==true)
		{
			$('#prepayNum_'+productID).html(data.prePayNum);
			$('#preNum_'+productID).html(data.preNum)
			$('#sumNum_'+productID).html(data.prePayNum+data.preNum)
			
			var content = '<h1>'+$('#prepayName_'+productID).html()+'</h1><table border="1">';
			content +='<tr><td colspan="2"><h1>預付清單</h1></tr>';	
			for(key in data.prePayList)
			{
				if(data.prePayList[key].orderNum>0)
				content+= '<tr><td>'+data.prePayList[key].name+'</td><td>'+data.prePayList[key].orderNum+'</td>';
			}
			content += '<tr><td colspan="2"><h1>預定清單</h1></tr>';	
			for(key in data.preList)
			{
				if(data.preList[key].orderNum>0)
				content+= '<tr><td>'+data.preList[key].shopName+'</td><td>'+data.preList[key].buyNum+'</td>';
			}
			content+='</table>';
		
			$('#prepay_'+productID).hover(function()
			{
				$('#showing').html(content);
				
			})
			
			
			
		}
		
		
	},'json')	
	
	
}


function prepayTable(data)
{
	productData = data
	$.post('/order/new_prepay',{productID:data.productID,packingNum:$('#packingNum_'+data.productID).val()},function(data){
		if(data.result==true)
		{
			prepayTableIn(productData)
	
		}
	},'json')
}


function editPackingNum(productID)
{
	$.post('/order/edit_prepay',{productID:productID,packingNum:$('#packingNum_'+productID).val(),comment:$('#comment_'+productID).val()},function(data){
		if(data.result==true)
		{
			
	
		}
	},'json')
}



function editPrepay()
{
	
	$('#productQuery').html('<h1>新增預付品</h1><div id="prepayQuery"></div><div style=" margin-top:20px" id="prepay_list"></div>')
	
	
	$('#prepay_list').html(
	'<form id="prepayForm"><table id="prepayListTable" border="1" width="900px" style=" font-size:14pt; float:left">'+
		'<tr>'+
			'<th>商品編號</th>'+
			'<th>中文名稱</th>'+
			'<th>英文名稱</th>'+
			'<th>語言</th>'+
			'<th>成箱數量</th>'+
			'<th>備註</th>'+
			'<th>取消</th>'+
			'<th>預付總和</th>'+
			'<th>預定總和</th>'+
			'<th>總計</th>'+
			
		'</tr>'+
	'</table></form>'+
	'<div id="showing" style=" position:fixed; float:left;width:320px; margin-left:900px;  border:solid">'+
		
	'</div>'
	
	
	
	)
	queryProduct('prepay','select');
	$.post('/order/prepayorder',{},function(data)
	{
		if(data.result==true)
		{
			
			for(key in data.product)
			{
		
				prepayTableIn(data.product[key]);
				
			}	
			
		}
		
	},'json')
	
}
function deletePrepay(productID)
{
	if(confirm('你確定要刪除?'))
	$.post('/order/delete_prepay',{productID:productID},function(data){
		if(data.result==true)
		{
			$('#prepay_'+productID).detach();
			
			
		}
		
	},'json')
	
	
	
}




//end prepay 


//盤點
//盤點
function newChkProduct()
{
		
	$.post('/product/new_chk_product',{},function(data)
	{
		closePopUpBox();
		if(data.result==true)checkProduct(data.checkID);
		
	},'json');
	
	
	
	
}
function goToChk()
{
	
	checkProduct($('#chkListID').val())
	closePopUpBox();
}



function getChkList()
{

	$.post('/product/get_chk_product_list',{},function(data)
	{
		
		if(data.result==true)
		{
			content='<h1>庫存盤點紀錄</h1>';
			content+='<select id="chkListID">'
			
			for(key in data.chkList)
			{
				content+='<option value="'+data.chkList[key].checkID+'">'+data.chkList[key].time+'</option>';
				
				
			}	
			content+='</select>';
			
			content+='<h1>或是...</h1><div><input type="button" class="big_button" value="建立一次新盤點" onclick="newChkProduct()">';
			openPopUpBox(content,600,280,'goToChk')
			
		}
		
	},'json')
}
function checkProduct(checkID)
{
	
	$('#productQuery').html(
		'<div class="divider"></div>'+
		'<input type="hidden" value="'+checkID+'" id="checkID">'+
		'<div style=" background-color:#66F">'+
			'<input class="big_button" style=" background-color:#906"  type="button" onclick="saveChkProduct('+checkID+',\'\')" value="儲存本次結果">'+
			'<input class="big_button" style=" background-color:#906"  type="button" value="盤盈狀況" onclick="saveChkProduct('+checkID+',\'overage\')">'+
			'<input class="big_button" style=" background-color:#906"  type="button" value="盤損狀況" onclick="saveChkProduct('+checkID+',\'damage\')">'+ 
		'</div>'+
		'<div class="divider"></div>'+
		'<div id="chkProductQuery"></div>'
	
	)
	
	$('#product_list').html('<img  src="/images/ajax-loader.gif"/>')		

	$.post('/product/get_chk_product',{checkID:checkID},function(data)
	{
		
	$('#chkProductTable').detach();	
	$('#product_list').after('<form id="chkProductForm"><table id="chkProductTable" border="1" width="1150px" style=" font-size:14pt">'+
		'<thead>'+					 
		'<tr>'+
			'<th>編號</th>'+
			'<th>商品編號</th>'+
			'<th>中文名稱</th>'+
			'<th>英文名稱</th>'+
			'<th>語言</th>'+
			'<th>電腦庫存</th>'+
			'<th>實際數量</th>'+

		'</tr>'+
        '</thead>'+
		'<tbody id="chkProductTableBody">'+
        '</tbody>'+ 		 
	'</table></form>')
	stockIndex = 0;
	queryProduct('chkProduct','select');
		if(data.result==true)
		{
			for(key in data.product)
			{
					
				 chkProductTable(data.product[key],true);
				
			}	
			
			
			
		}
		$('#product_list').html('');
		
	},'json')
	
	
}
function stockRecover(checkID,productID,nowNum,realNum,status)
{
		$.post('/product/stock_recover',{checkID:checkID,productID:productID,nowNum:nowNum,realNum:realNum,status:status},function(data)
		{
			
			if(data.result==true)
			{
				$('#stock_recover_'+productID).html('已更正');	
				
				
				
			}
			
			
			
			
		},'json')
		
	
	
	
	
	
}

function chkProductDelete(productID,checkID)
{
		if(confirm('你確定要刪除?'))
		{
		$.post('/product/chk_product_delete',{productID:productID,checkID:checkID},function(data)
		{
			if(data.result==true)
			{
				$('#chk_overage_productID_'+productID).detach();	
				
				
				
			}
			
			
			
			
		},'json')
		
		}
	
	
	
	
}



function chkProductOverage(checkID)
{
	

	$.post('/product/get_overage',{checkID:checkID},function(data)
	{
		
		if(data.result==true)
		{
			content ='<h1>盤盈表</h1>'
			content+=
			'<table id="chkOverageProductTable" border="1" width="850px" style=" font-size:14pt">'+
				'<tr>'+
					'<th>編號</th>'+
					'<th>商品編號</th>'+
					'<th>中文名稱</th>'+
					'<th>英文名稱</th>'+
					'<th>語言</th>'+
					'<th>電腦庫存</th>'+
					'<th>實際數量</th>'+
					'<th>盤盈數量</th>'+
					'<th>處理方式</th>'+
				'</tr>'+
			'</table>'
				OstockIndex=0;
			openPopUpBox(content,900,280,'closePopUpBox');	
				
			for(key in data.product)
			{
				if(data.product[key].status==0) btn ='<input id="recover_button_'+data.product[key].productID+'" type="button" value="更正" onclick="stockRecover('+checkID+','+data.product[key].productID+','+data.product[key].nowNum+','+data.product[key].realNum+',1)" >';
				else btn = '已更正';
				OstockIndex++;
				$('#chkOverageProductTable').append('<tr id="chk_overage_productID_'+data.product[key].productID+'">'+
						'<td>'+OstockIndex+'</td>'+
						'<td>'+data.product[key].productNum+'</td>'+
						'<td><input type="hidden" name ="chk_overage_productID_'+data.product[key].productID+'" value="'+data.product[key].productID+'">'+data.product[key].ZHName+'</td>'+
						'<td>'+data.product[key].ENGName+'</td>'+
						'<td>'+data.product[key].language+'</td>'+
						'<td><input type="hidden" name="chk_nowNum_'+data.product[key].productID+'"  value="'+data.product[key].nowNum+'" id="chk_overage_nowNum_'+data.product[key].productID+'">'+data.product[key].nowNum+'</td>'+
						'<td><input type="text" name="chk_realNum_'+data.product[key].productID+'"   value="'+data.product[key].realNum+'" id="chk_overage_realNum_'+data.product[key].productID+'"  onchange="chkProductNum(\''+data.product[key].productID+'\')"></td>'+
						'<td><input type="hidden" name="chk_overNum_'+data.product[key].productID+'"   value="'+(data.product[key].realNum-data.product[key].nowNum)+'" id="chk_overage_realNum_'+data.product[key].productID+'">'+(data.product[key].realNum-data.product[key].nowNum)+'</td>'+
						'<td id="stock_recover_'+data.product[key].productID+'">'+btn+'</td>'+
					'</tr>'
					)			
					
				$('#recover_button_'+data.product[key].productID).click(function(){});	
				height =parseInt($('#chkOverageProductTable').css('height').substr(0,$('#chkOverageProductTable').css('height').length-2));
				popUpBoxHeight(height+100);
				
					
			}	
			
			
			
		}
		
	},'json')
	
}

var stockIndex = 0;
var OstockIndex = 0;
var DstockIndex = 0;

function chkProductDamage(checkID)
{
	
	$.post('/product/get_damage',{checkID:checkID},function(data)
	{
		
		if(data.result==true)
		{
			content ='<h1>盤虧表</h1>'
			content+=
			'<a onclick="$(\'.stockAll\').hide();$(\'.stocknot\').show();">未更正</a>｜<a onclick="$(\'.stockAll\').hide();$(\'.stockok\').show();">已更正</a>｜<a onclick="$(\'.stockAll\').show()">全部</a>'+		
			'<table id="chkDamageProductTable" border="1" width="850px" style=" font-size:14pt">'+
				'<tr>'+
					'<th>編號</th>'+
					'<th>商品編號</th>'+
					'<th>中文名稱</th>'+
					'<th>英文名稱</th>'+
					'<th>語言</th>'+
					'<th>成本</th>'+
					'<th>電腦庫存</th>'+
					'<th>實際數量</th>'+
					'<th>盤虧數量</th>'+
					'<th>處理方式</th>'+
					'<th>刪除</th>'+
				'</tr>'+
			'</table>'
				DstockIndex = 0;
			openPopUpBox(content,1000,280,'closePopUpBox');	
			for(key in data.product)
			{
				if(data.product[key].status==0)
				{
					 btn ='<input id="recover_button_'+data.product[key].productID+'" type="button" value="更正" onclick="stockRecover('+checkID+','+data.product[key].productID+','+data.product[key].nowNum+','+data.product[key].realNum+',1)" >';
					stockClass= 'stocknot';	 
				}
				else
				{
					 btn = '已更正';
					 stockClass = 'stockok'
				}
				//alert(data.product[key].productID);
						DstockIndex++;
						$('#chkDamageProductTable').append('<tr class="stockAll '+stockClass+'" id="chk_overage_productID_'+data.product[key].productID+'">'+
						'<td>'+DstockIndex+'</td>'+
						'<td>'+data.product[key].productNum+'</td>'+
						'<td><input type="hidden" name ="chk_overage_productID_'+data.product[key].productID+'" value="'+data.product[key].productID+'">'+data.product[key].ZHName+'</td>'+
						'<td>'+data.product[key].ENGName+'</td>'+
						'<td>'+data.product[key].language+'</td>'+
						'<td>'+data.product[key].cost+'</td>'+
						'<td><input type="hidden" name="chk_nowNum_'+data.product[key].productID+'"  value="'+data.product[key].nowNum+'" id="chk_overage_nowNum_'+data.product[key].productID+'">'+data.product[key].nowNum+'</td>'+
						'<td><input type="hidden" name="chk_realNum_'+data.product[key].productID+'"   value="'+data.product[key].realNum+'" id="chk_overage_realNum_'+data.product[key].productID+'"  onchange="chkProductNum(\''+data.product[key].productID+'\')">'+data.product[key].realNum+'</td>'+
						'<td><input type="hidden" name="chk_overNum_'+data.product[key].productID+'"   value="'+(data.product[key].nowNum-data.product[key].realNum)+'" id="chk_overage_realNum_'+data.product[key].productID+'">'+(data.product[key].nowNum-data.product[key].realNum)+'</td>'+
						'<td id="stock_recover_'+data.product[key].productID+'" class="">'+btn+'</td>'+
						'<td><input  type="button" value="刪除" onclick="chkProductDelete('+data.product[key].productID+','+checkID+')" ></td>'+

					'</tr>'
					)			
					
				$('#recover_button_'+data.product[key].productID).click(function(){stockRecover(data.product[key].productID,data.product[key].nowNum,data.product[key].realNum,-1)});	

				height =parseInt($('#chkDamageProductTable').css('height').substr(0,$('#chkDamageProductTable').css('height').length-2));
				popUpBoxHeight(height+100);
				
					
			}	
			
			
			
		}
		
	},'json')
	
}







function chkProductTable(data,notsay)
{
	$('.chk_productID').css('background-color','');
	if($('#chk_productID_'+data.productID).length!=0)
	{
		$('#chk_realNum_'+data.productID).focus();
		$('#chk_productID_'+data.productID).css('background-color','#FF88C2');
		SayIt('商品重複','chkProduct');
		 return 
	}
	if(!notsay)SayIt(data.ZHName,'chkProduct');
	var num = 0;
	if(data.realNum) realNum = data.realNum;
	else realNum = 0;
		stockIndex++;
		$('#chkProductTableBody').prepend(
		'<tr class="chk_productID" id="chk_productID_'+data.productID+'" >'+
			'<td>'+stockIndex+'</td>'+
			'<td>'+data.productNum+'</td>'+
			'<td><input type="hidden" name ="chk_productID_'+data.productID+'" value='+data.productID+'>'+data.ZHName+'</td>'+
			'<td>'+data.ENGName+'</td>'+
			'<td>'+data.language+'</td>'+
			'<td><input type="hidden" name="chk_nowNum_'+data.productID+'" value="'+data.nowNum+'" id="chk_nowNum_'+data.productID+'">'+data.nowNum+'</td>'+
			'<td><input type="text" name="chk_realNum_'+data.productID+'"   value="'+realNum+'" id="chk_realNum_'+data.productID+'"  onchange="chkProductNum(\''+data.productID+'\')"></td>'+
			'</tr>'
		)
	$('#product_list').html('');
	$('#chk_realNum_'+data.productID).select();
	$('#chk_productID_'+data.productID).css('background-color','#FF88C2');
	$('input').keypress(function(e) {
    code = e.keyCode ? e.keyCode : e.which; // in case of browser compatibility
    if(code == 13) {
        e.preventDefault();
        // do something
        /* also can use return false; instead. */
        }
    });
	$('#editMsgIn').detach();
	$('body').append('<div id="editMsgIn" style=" margin-left:500px; color:red ;position:absolute; z-index:2; background-color:white;'+
	 'top:'+($(document).scrollTop()+200)+'px;left:200px; ";></div>');
	
	
}


function chkProductNum(id)
{
	if(fucCheckNUM($("#chk_realNum_"+id).val())<=0)
	{
		SayIt('數字錯誤','chkProduct');
		$("#chk_realNum_"+id).val(0);
	}
	else SayIt($("#chk_realNum_"+id).val(),'chkProduct');
	
	//$('#chkProduct_findBarcode').select();
	
	$.post('/product/save_product',{checkID:$('#checkID').val(),productID:id,nowNum:$("#chk_nowNum_"+id).val(),realNum:$("#chk_realNum_"+id).val()},function(data)
	{
		if(data.result==true)	$('#editMsgIn').html('已儲存');
		else $('#editMsgIn').html('儲存失敗，請洽管理員');
		setTimeout(function(){$('#editMsgIn').detach();},2000);
			
		
		
	},'json')
	
	
	
	
	
	
}

function saveChkProduct(checkID,next)
{
	content='<h1>儲存中...請稍後</h1>';
	content+='<h3 id="chk_pro">進度<sapn id="chk_progress">0</sapn>%<h3>';
	
	var totalLength = $("#chkProductForm").serialize().length;
	
	 openPopUpBox(content,600,280,'closePopUpBox');
	 index = -1;
nextkey = false;
	 while(index!=0)
	 {
		
		 lastindex = index;
		 if(index==-1) lastindex = 0 ;
		 
			
			 if($("#chkProductForm").serialize().indexOf("&",index+3000)==-1)
			 {
				 result = $("#chkProductForm").serialize().substr(lastindex); //後面沒東西了
				index = 0; 
				nextkey = true;
			 }
			 else
			 {
			 
			 index = $("#chkProductForm").serialize().indexOf('chk_productID_',$("#chkProductForm").serialize().indexOf("&",index+3000));
			  
			result =$("#chkProductForm").serialize().substr(lastindex,index-lastindex-1);
			 }
		 
			/*
			if(index==-1||index==0)	result = $("#chkProductForm").serialize().substr(lastindex+1);
			else result =$("#chkProductForm").serialize().substr(lastindex,index-lastindex-1);
			*/
 		
			  $('#chk_progress').html(Math.round(((index)*100/totalLength)))
			 $.ajax({
		   type: "POST",
		   dataType:"json",
		   url: "/product/save_chk_product",
		   data: 'checkID='+checkID+'&'+result,
		   success: function(data){
			 
		
			   if(data.result==true)
			   {  
			 
			   		if(nextkey)
					{
					 nextkey = false;
						//index=0;
						$('#chk_pro').html('正在為您載入下一個程序...');
						if(next=='overage') chkProductOverage(checkID);
						else if(next=='damage') chkProductDamage(checkID);
						else $('#chk_pro').html('儲存完畢，點選確定關閉視窗');
						
					}
					else
					{
						//index+=3000;
						
						
						
					}
			   
					
				
			   }
			  
		   }
		 });

		
	}
	
	
	/*
		
	
*/	
	







}



//dump purchase order


var orderListOffect = 0;
function showOrderList()
{
	$('#order_container').html(	
		'<table id="order_list" border="1">'+
			'<tr>'+
				'<td>訂單編號</td>'+
				'<td>採購日期</td>'+
				'<td> 廠  商</td>'+
				'<td>訂單總價</td>'+
				'<td>訂單狀況</td>'+
				'<td>查看訂單</td>'+
			'</tr>'+
		'</table>'+
		'<input type="button" value="查看更多" id="moreOrderBtn"onclick="getOrderList(orderListOffect,5);"class="big_button"/>'
	 	);
	orderListOffect =0;
	getOrderList(orderListOffect,5);
		
}
function getOrderList(offect,num)
{
	if(dumpOrderList.length>0 &&  confirm('這樣將會清除下面的所有資料，按確定繼續') )
	{
		
		wareroomIO('purchase')	
		showOrderList();
		return;
	}
	
	
	if($('#orderStatus').val()==-1)if(!confirm('請注意，已入庫表示曾經入過庫存，若無特殊情況請避免重覆入庫，是否要入庫？'))return;
	
	
	
	orderListOffect = offect+num;
	$.post('/product/get_purchase_list',{offect:offect,num:num,status:$('#orderStatus').val(),type:'purchaseOrder'},function(data)
	{
		
		
		if(data.result==true)
		{
			for(key in data.purchaseList)
			{
				
					if($('#orderListID_'+data.purchaseList[key].id).length>0)	;
					else	
					{
					$('#order_list').append(
						'<tr id="orderListID_'+data.purchaseList[key].purchaseID+'">'+
						'<td>s'+data.purchaseList[key].purchaseID+'</td>'+
				
						'<td>'+data.purchaseList[key].timeStamp+'</td>'+
						'<td>'+data.purchaseList[key].supplier+'</td>'+
						'<td>'+data.purchaseList[key].total+'</td>'+
						'<td>'+data.purchaseList[key].status+'</td>'+
						'<td><input type="button" value="匯入" onclick="dumpOrder('+data.purchaseList[key].purchaseID+',\'purchaseOrder\')"class="big_button" ></td>'+
						'</tr>'
					)
					}
				
				
			}
		}
		else
		{
				$('#order_list').append('沒有其他資料了');
				$('#moreOrderBtn').hide();
		}
	},'json')	
	
	
}
var dumpOrderList= Array();
function dumpOrder(purchaseID,dumpType)
{    
	
		if($('#orderStatus').val()==-1)if(!confirm('請注意，已入庫表示曾經入過庫存，若無特殊情況請避免重覆入庫，是否要入庫？'))return;
	$('#orderListID_'+purchaseID).html('<td colspan="5">採購單下載中..請稍候<img src="/images/ajax-loader.gif"/></td>');
	$.post('/product/get_purchase',{purchaseID:purchaseID,type:dumpType},function(data)
	{
			
		if(data.result==true)
		{
			
			$('#purchaseSuppliers').val(data.purchase.supplierID);
			purchaseInf(data.purchase.supplierID)
           
			$("input[name=taxType]").removeAttr('checked');
			
			$("input[name=taxType][value="+data.purchase.tax+"]").attr('checked',true); 
			
            $("input[name=buyType]").removeAttr('checked');
			
			$("input[name=buyType][value="+data.purchase.type+"]").attr('checked',true); 
            
		
			$('#purchase_comment').val(data.purchase.comment);
			 changeTaxType();
			
			if($('input[name=taxType]:checked').val()%2==0) type = 'bTax';
			else type = 'aTax';
			$('#purchase_total_'+type).val(data.purchase.shareFee);
			$('#freight_'+type).val(data.purchase.shippingFee);
			$('#purchase_accountTime').val(data.purchase.accountTime);
			
			
			
			
			
			for(key in dumpOrderList)
			{
				if(dumpOrderList[key]==purchaseID)
				{
					alert('訂單已匯入');
					$('#orderListID_'+purchaseID).hide();
					return;
				}
				
		
			}	
	
			
	
			for(key in data.product)
			{
				/*
				if(dumpType=='purchase')
				{
					
					purchaseTable(data.product[key],false);
				}
				else purchaseOrderTable(data.product[key],false);
				*/
				var tail = purchaseTable(data.product[key],false);
				$('#phrchase_num_'+data.product[key].productID+tail).val(data.product[key].num)
				$('#phrchase_orgprice_'+data.product[key].productID+tail).val(data.product[key].purchasePrice)
				//$('#phrchase_price_'+data.product[key].productID).val(data.product[key].purchasePrice)
				$('#byEnter_'+data.product[key].productID+tail).attr('checked',true); 
			
				$('#phrchase_subtotal_'+data.product[key].productID+tail).val(data.product[key].purchaseTotal);				
				
                if(dumpType=='purchase')
                $('#productNum_'+data.product[key].productID+tail).append('<input type="hidden" name="purchaseRowID_'+data.product[key].productID+tail+'" value="'+data.product[key].rowID
                                                                          +'">');
			}
			if(dumpType=='purchase')$('.purchase_num').attr("readonly",true);
			caculatePurchasePrice();
			
			dumpOrderList[dumpOrderList.length] = purchaseID;
			$('#orderListID_'+purchaseID).hide();
			$('#order_container').hide();
       
		}
		
	},'json')	
	
	
}
function updateDumpOrder()
{
	for(key in dumpOrderList)
	{
		dumpFinish(dumpOrderList[key]);
		
	}	
	
	
}
function dumpFinish(purchaseID)
{
	$.post('/product/purchase_order_dump_finish',{purchaseID:purchaseID},function(data)
	{
			if(data.result!=true) alert('系統出現錯誤，請洽管理員');
		
	},'json')		
	
	
}


function budgetSet()
{

    
    r = $('#product_suppliers').html();
	content='<span style=" font-size:14pt">請選擇供應商</sapn><select id="budget_purchase_suppliers">'+r+'</select>'+    
    '<input type="button" class="big_button"   value="加入預算表" onclick="newBudget()"></h1>'+
    '<form id="budgetForm">'+
    '<select id="budgetTime" name="budgetTime" onchange="changeBudget()"> '+$('#product_time').html()+'  </select>'
    ;
        
    content+='<table border="1" id="budgetTable">'+   
        '</table></form>'  ;
      

    
  openPopUpBox(content,500,300,'saveBudgetResult');
    var  time=new Date();
    nowM = time.getMonth()+1;
    $('#budgetTime').val(time.getFullYear()+'-'+nowM);
	
    changeBudget();
    
    
}
               
function changeBudget()
  {
       $('#budgetTable').html( '<tr>'+
            '<td>供應商名稱</td>'+
            '<td>預算金額</td>'+
            '<td>已使用金額</td>'+
            '<td>剩餘金額</td>'+
        
        '</tr>');
      
      $.post('/product/get_budget_by_time',{'dateString':$('#budgetTime').val()},function(data)
            {
          
            if(data.result==true)
                {
          
            for(key in data.budgetList)
                {
                    
                     addToBudget(data.budgetList[key].supplierID,data.budgetList[key].supplierName,data.budgetList[key].limitAmount,data.budgetList[key].used)
                    
                }
                 popUpBoxHeight(0);
                }
          
          
      },'json')
      
      
      
      
  }

function newBudget()
{
    var name = $("#budget_purchase_suppliers option[value="+$('#budget_purchase_suppliers').val()+"]").text();
   addToBudget( $('#budget_purchase_suppliers').val(),name,0,0);
    
}
    
               
function addToBudget(supplierID,supplierName,limitAmount,used)
{
    
    if($('#budgetTr_'+supplierID).length!=0){
		  alert('已在清單中');
		
	  }	
    else
    {
       var  remain = limitAmount - used;
        
         content =  '<tr id="budgetTr_'+supplierID+'">'+
            '<td><input type="hidden" name="supplierID[]" value="'+supplierID+'">'+supplierName+'</td>'+
            '<td><input type="text" name="budgetLimit[]" value="'+limitAmount+'"></td>'+
            '<td>'+used+'</td>'+
             '<td>'+remain+'</td>'+
        
        '</tr>';
    
	  $('#budgetTable').append(content)
    
    
        
        
        
    }
   
    
}

function saveBudgetResult()
{
    
    if($('#level').val()!=80 && $('#level').val()!=150)
        {
            
            alert('請委請主管放行修改');
            return ;
            
        }
    
   

    $.ajax({
		   type: "POST",
		   dataType:"json",
		   url: "/product/budget_update",
		   data: $("#budgetForm").serialize(),
		   success: function(data){
			 
              
			   if(data.result==true)
			   {  
				
						  
					closePopUpBox();
				  alert('儲存完畢');
			   }
			  
		   }
		 });
	
    
    
    
    
    
}
function stockBack()
{
    
    content = '<div id="loading"></div><table border="1" id="stockBackTable">'+
                '<tr>'+
                                '<td>年</td>'+
                                '<td>出貨盒數</td>'+
                                '<td>退貨盒數</td>'+
                                '<td>退貨盒數比</td>'+
                                '<td>盒損退貨盒數</td>'+
                                '<td>盒損退貨盒數比</td>'+
                                '<td>缺件退貨盒數</td>'+
                                '<td>缺件退貨盒數比</td>'+
                                '<td>退賣退貨盒數</td>'+
                                '<td>退賣退貨盒數比</td>'+
                                '<td>展場退貨盒數</td>'+
                                '<td>展場退貨盒數比</td>'+
                                '<td>其他退貨盒數</td>'+
                                '<td>其他退貨盒數比</td>'+
                                '<td>誤寄退貨盒數</td>'+
                                '<td>誤寄退貨盒數比</td>'+
                                '<td>出貨金額</td>'+
                                '<td>退貨金額</td>'+
                                '<td>退貨金額比</td>'+
                                '<td>盒損退貨金額</td>'+
                                '<td>盒損退貨金額比</td>'+
                                '<td>缺件退貨金額</td>'+
                                '<td>缺件退貨金額比</td>'+
                                '<td>退賣退貨金額</td>'+
                                '<td>退賣退貨金額比</td>'+
                                '<td>展場退貨金額</td>'+
                                '<td>展場退貨金額比</td>'+
                                '<td>其他退貨金額</td>'+
                                '<td>其他退貨金額比</td>'+
                                '<td>誤寄退貨金額</td>'+
                                '<td>誤寄退貨金額比</td>'+
                              '</tr>' 
        
            '</table>';
    $('#product_list').html(content);
    
    
     stockBackRatio(2011);
    
    
    
}

function stockBackRatio(year)
{
   
    d = new Date();
    y = d.getFullYear();
    if(year>y)return;
    else{
         $('#loading').html('<img src="/images/ajax-loader.gif"/>正在讀取'+year+'的資料，當年度或首次讀取需要等候較長時間');
        $.post('/product_flow/stock_ratio/'+year,{},function(data)
              {
           
                    if(data.result==true)
                        {
                    content = '<tr>'+
                                '<td>'+year+'</td>'+
                                '<td>'+data.shipTotal.num+'</td>'+
                                '<td>'+data.backTotal.num+'</td>'+
                                '<td>'+data.numPercent+'%</td>';
                        for(i=1;i<=6;i++)
                            {
                                if(data.backTotal.reason[i]!==undefined && data.backTotal.reason[i]['num'] !== undefined)
                                {
                                  p = data.backTotal.reason[i].num*10000/data.shipTotal.num;
                                 content+= '<td>'+data.backTotal.reason[i].num+'</td>'+ 
                                         '<td>'+Math.round(p)/100+'%</td>'
                                }
                                else 
                                {
                                      content+= '<td>0</td>'+ 
                                         '<td>0%</td>'
                                    
                                    
                                }
                                
                            } 
                             content+=    
                                '<td>'+data.shipTotal.price+'</td>'+
                                '<td>'+data.backTotal.price+'</td>'+
                                 '<td>'+data.totalPercent+'%</td>';
                             for(i=1;i<=6;i++)
                            {
                                if(data.backTotal.reason[i]!==undefined && data.backTotal.reason[i].price !== undefined)
                                {
                                  p = data.backTotal.reason[i].price*100000/data.shipTotal.price;
                                 content+= '<td>'+data.backTotal.reason[i].price+'</td>'+ 
                                          '<td>'+Math.round(p)/100+'%</td>'
                                }
                                 else 
                                {
                                      content+= '<td>0</td>'+ 
                                         '<td>0%</td>'
                                    
                                    
                                }
                            }         
                                    
                                    
                                    
                                    
                              content+= '</tr>';
                        }
                     $('#loading').html('');
                    $('#stockBackTable').append(content);
                    stockBackRatio(++year);
              }
              ,'json')
        
        
    }
    
    
    
}
function stockBackAll()
{
    
    content = '<div id="loading"></div><table border="1" id="stockBackTable">'+
                '<tr>'+
                                '<td>id</td>'+
                                '<td>備註</td>'+
                                '<td>結論</td>'+
                              '</tr>' 
        
            '</table>';
    $('#product_list').html(content);
    
    
     stockBackComment(2011);
    
    
    
}
function backStatus(id,reason)
{
    
    openPopUpBox('<h1>儲存中請稍後</h1>',600,280,'closePopUpBox');	
    $.post('/product_flow/back_reason',{id:id,reason:reason},function(data)
          {
        
            closePopUpBox();
            if(data.result==true) $('#tr_back_'+id).detach();
            else alert('錯誤 請洽管理員');
            
        
    },'json')



}
function stockBackComment(year)
{
     d = new Date();
    y = d.getFullYear();
    if(year>y)return;
    else{
         $('#loading').html('<img src="/images/ajax-loader.gif"/>正在讀取'+year+'的資料，當年度或首次讀取需要等候較長時間');
             $('#stockBackTable').append('<tr><td>'+year+'</td></tr>');
        $.post('/product_flow/get_all_stock_back/',{year:year},function(data)
              {
                
                    if(data.result==true)
                        {
                            for(key in data.backData )
                                {
                                content = '<tr id="tr_back_'+data.backData[key].id+'">'+
                                    '<td >'+data.backData[key].id+'</td>'+
                                     '<td>'+data.backData[key].comment+'</td>'+
                                    '<td style="width:400px"><input type="button" class="big_button" onclick="backStatus('+data.backData[key].id+',1)" value="盒損">'+
                                    '<input type="button" class="big_button" onclick="backStatus('+data.backData[key].id+',2)" value="缺件">'+
                                    '<input type="button" class="big_button" onclick="backStatus('+data.backData[key].id+',3)"  value="退賣">'+
                                    '<input type="button" class="big_button"onclick="backStatus('+data.backData[key].id+',4)"  value="展場">'+
                                     '<input type="button" class="big_button"onclick="backStatus('+data.backData[key].id+',6)"  value="誤寄">'+
                                    '<input type="button" class="big_button" onclick="backStatus('+data.backData[key].id+',5)"  value="其他">'+
                                    
                                    
                                
                                        
                                    '</td>'+
                                  '</tr>'
                                 $('#stockBackTable').append(content);
                                }
                        }
                     $('#loading').html('');
                   
                    stockBackComment(++year);
              }
              ,'json')
        
        
    }
    
    
    
}

function quickPurchase()
{
    
      content = 
              '<input type="button"  class="big_button"  onclick="qpAll()"  value="查看全部供應商"><br/>'+
              '<h3>或選擇一個供應商</h3>'+     
              '<select onchange="qpSelect()" id="qpSupplier">'+suppliers+'</select>'+
          
              '<div id="qpContainer"></div>';
    
    
    	openPopUpBox(content,800,280,'');
    
    

    
    
}
function qpSelect()
{
     content = '<table id="qpTable" border="1"></table><img id="qpLoading" src="/images/ajax-loader.gif"/>';
     $('#qpContainer').html(content);
    qpData($('#qpSupplier').val(),$('#qpSupplier :selected').text(),{},-1);
    
}

function qpAll()
{
     $.post('/product/get_suppliers',{},function(data)
	{
               
    content = '<table id="qpTable" border="1"></table><img id="qpLoading" src="/images/ajax-loader.gif"/>';
     $('#qpContainer').html(content);
 
    
        qpTable(data.suppliers,0);
        
    },'json');
    
    
}
      

function qpTable(suppliers,index)
{
    if(!suppliers[index])
    {
     $('#qpLoading').detach();
        return;
    }
    var supplierID = suppliers[index].supplierID;
    var name = suppliers[index].name;
    qpData(supplierID,name,suppliers,index);
    
}

function qpData(supplierID,name,suppliers,index)
{
    if(supplierID==0) return;
         $.post('/order/quick_purchase',{supplierID:supplierID},function(data)
    {
       
         
        var firstToken = false;
        for(key in data.product)
            {
           
                 var v =  parseInt(data.product[key].martSaveNum)+parseInt(data.product[key].buyNum)-data.product[key].comNum;
                    if(v>0)
                    {
                        if(firstToken==false)
                        {
                                   $('#qpTable').append('<tr><td colspan="8" style="background-color:#FFEBEB">'+name+'</td>'+
                          
                             '</tr>');     
        
                     $('#qpTable').append('<tr><td>編號</td><td>中文名稱</td><td>英文名稱</td>'+
                             '<td>TOP</td>'+
                             '<td>公司庫存</td>'+
                            '<td>訂單數量</td>'+
							'<td>電商保留　</td>'+
                             '<td>需求差額</td>'+
                             '</tr>');      
                            firstToken = true;
                            
                        }
                        
                        
                  if(data.product[key].top>0) var top = 'top100  ';
                        else  var top = '';
        $('#qpTable').append('<tr><td>'+data.product[key].productNum+'</td>'+
                             '<td>'+data.product[key].ZHName+'</td>'+
                             '<td>'+data.product[key].ENGName+'</td>'+
                             '<td>'+top+'</td>'+
                            '<td>'+data.product[key].comNum+'</td>'+
                             '<td>'+data.product[key].buyNum+'</td>'+
							 '<td>'+data.product[key].martSaveNum+'</td>'+
                             '<td>'+v+'</td>'+
                             '</tr>');
                         popUpBoxHeight(0);
                    }
            }
          $('#qpTable').append('<tr style="height:80px"><td colspan="6" style="background-color:#AAAAAA"><input type="button" class="big_button" onclick="purchaseOrderParse('+supplierID+')" value="匯入採購單" ></td>'+
                          
                             '</tr>');
                         popUpBoxHeight(0); 
             
             
             
             
             
        if(index!=-1)qpTable(suppliers,index+1);
             else $('#qpLoading').detach();
    },'json');
    
    
}

function purchaseOrderParse(supplierID)
{
    wareroomIO('purchaseOrder');
    $('#purchaseSuppliers').val(supplierID);
      $.post('/order/quick_purchase',{supplierID:supplierID},function(data)
    {
          
        for(key in data.product)
        {
         var v =  data.product[key].buyNum-data.product[key].comNum;
          if(v > 0 &&　v >= 2*data.product[key].case/3)  purchaseOrderTable(data.product[key]);

           
        }
 
          
      },'json')
    
    
    
    
}

function checkMart(productID,id)
{
     
       $.post
('/product/check_mart',{productID:productID},function(data)
          {
      
        
            if(data.result==true)
                {
                    
                $('#'+id).detach();
                  
                }
             
        
        
    },'json') 
    
    
}

function uploadMart(productID)
{
    $.post
('/product/check_mart',{productID:productID},function(data)
          {
      
        
            if(data.result==false)
                {
                    
                     doUploadMart(productID)
                    
                }
            else alert('已經上架囉');
        
        
    },'json')
    
    
    
    
}


function doUploadMart(productID)
{
       $.post('/product/upload_to_mart',{productID:productID},function(data)
          {
        
            if(data.result==true)
                {
                    
                   alert('上架完成囉'); 
                    
                }
          
        
        
    },'json')
    
    
    
    
}

$(document).ready(function(){
	getSuppliers();
	getPublisher();
	 setProductTime();
	 productType();	
	 queryProduct('product','auto');
	 $('#warroomBtn').hide();
	 classSelectSet();
	
	
})

