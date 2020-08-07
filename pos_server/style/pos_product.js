// JavaScript Document
var productTypeOption;
var shipmentCount;
var accountLevel = 100;
var classZh=new Array('編輯','編號','分類','條碼','中文','英文','語言','價錢','進貨折數','進貨價格','出貨折數','出貨價格','供應商','最低折數','現在庫存','刪除');
var classEng = new Array('editBtn','productNum','category','barcode','ZHName','ENGName','language','price','buyDiscount','buyPrice','purchaseCount','purchase','suppliersName','minDiscount','nowNum','delete');
var suppliers;
jQuery.fn.Scrollable = function(tableHeight, tableWidth) {
	this.each(function(){
		if (jQuery.browser.msie || jQuery.browser.mozilla) {
			var table = new ScrollableTable(this, tableHeight, tableWidth);
		}
	});
};

function fillZero(ret)
{
	if(ret==null) ret=0;
	if (ret<10) return '000'+ret.toString();
	else if (ret<100) return '00'+ret.toString();
	else if (ret<1000) return '0'+ret.toString();
	else return ret.toString();
	
	
}

function classSet()
{
		
	$('#classSelect').append('<div style="position:absolute; right:30px; cursor:pointer; font-weight:bold" onclick="$(\'#classSelect\').hide(\'fast\')">X</div>');
	for(key in classZh)
	{
		if(classEng[key]!='barcode')
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


function getSuppliers()
{
	$.post('/product/get_suppliers',{},function(data){
		suppliers='';
		for (key in data.suppliers)
		{
			
			suppliers+='<option value="'+data.suppliers[key].supplierID+'">'+data.suppliers[key].name+'</option>';
			
		}
	},'json');
	
}


function setProductTime()
{
	var  time=new Date();
	var month = time.getMonth()+1;
	var year = time.getFullYear();
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
	var time = $('#product_time').val();
	$.post('/product/get_product_list',{time:time},function(data){
		$('#ajaxLoader').html('');
		setProductTable(data.product,1);
		
		
	},'json')	
	
	
}


function setProductTable(data,type)
{
	var headerStr = '';
	for(key in classZh)
	{
		if($('#class_'+classEng[key]).is(":checked")) 
		{
			headerStr +='<th>'+classZh[key]+'</th>';	
		}
	}
	
	
	
	
	
	$('#product_list').html(
		'tips:按住shift鍵可進行多重排序。'+
	     '<table id="product_table"  class="tablesorter" style="width:950px;;text-align:center">'+
		 '<thead>'+   	
		 '<tr>'+
		 	headerStr+
        '</tr>'+
		'</thead>'+
		'<tbody id="product_table_body"></tboday>'+
		 '<tfoot>'+   	
		 '<tr>'+
		 	headerStr+
        '</tr>'+
		'</tfoot>'+		
	    '</table>'
	)	
	
	if(type==1)
	{
		for(key in data)
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

	//$("#product_table").tablesorter({widgets: ['zebra'],sortList: [ [1,0],[2,0]]});
	$("#product_table").Scrollable(200, 800);
	$('#classSelect').hide();

}

function productList(product)
{
	
	
	var result='';
	for(key in classZh)
	{
		if($('#class_'+classEng[key]).is(":checked")) 
		{
			
			if(classEng[key]=='editBtn')result +='<td><input type="button" value="編輯" onclick="editBox(\''+product.productID+'\')"></td>';
			else if(classEng[key]=='productNum')result +='<td>'+fillZero(product.productNum)+'</td>';
			else if(classEng[key]=='delete')result +='<td><input type="button" value="刪除" onclick="productDelete(\''+product.productID+'\')"></td>';
			else if(classEng[key]=='purchase'){ result +='<td>'+ Math.round(product['purchaseCount']*product['price']/100) +
			'</td>';
			}
			else if(classEng[key]=='minDiscount'||classEng[key]=='purchaseCount'||classEng[key]=='buyDiscount')result +='<td>'+ product[classEng[key]] +'%</td>';	
			else result +='<td>'+ product[classEng[key]] +'</td>';	
		}
	}
   return result;
	
	
	
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
		'商品條碼：<input type="text"  name="barcode" id="newBarcode" class="big_text" />'+
    	' 商品編號：<input type="text"  name="productNum" id="newProductNum" class="big_text" />'+

    '</li>'+
    '<li>'+
	'商品中文：<input type="text"  name="ZHName" id="memberID" class="big_text" />'+
    ' 商品英文：<input type="text"  name="ENGName"  class="big_text" />'+
    '</li>'+		 
     '<li>'+
   
    '商品定價：<input type="text"  name="price" id="price" class="medium_text" />'+
	' 進貨折數：<input type="text"  name="buyDiscount\" id="memberID" class="medium_text" value=""   onblur="$(\'#buyPrice\').val(Math.round($(\'#price\').val()*this.value/100))"/>'+
	' 進貨價格：<input type="text"  name="buyPrice" id="buyPrice" class="medium_text" />'+
    '</li>'+
	'<li>'+
	'最低折數：<input type="text"  name="minDiscount" id="memberID" class="medium_text" />'+
	' 出貨折數：<input type="text"  name="purchaceDiscount" id="memberID" class="medium_text" onblur="$(\'#purchasePrice\').val(Math.round($(\'#price\').val()*this.value/100))"/>'+
	' 出貨價格：<input type="text"  id="purchasePrice" class="medium_text" readonly="readonly" >'+
    '</li>'+	
	'<li>商品種類：<select name="productType" onchange="if(this.value>1)$(\'#new_product_category\').val(0)">'+productTypeOption+'</select>'+
	' 遊戲分類：<select id="new_product_category"  name="category">'+
				'<option value="A">A.親子益智</option>'+
				'<option value="B">B.派對歡樂</option>'+
				'<option value="C">C.輕度策略</option>'+
				'<option value="D">D.重度策略</option>'+
				'<option value="E">E.團隊合作</option>'+
				'<option value="F">F.兩人對戰</option>'+
				'<option value="0">不分類</option>'+
			'</select>'+
	' 供應廠商：<select name="suppliers">'+suppliers+'</select>'+
	'</li>'+
	'<li>'+
	' 遊戲語言：<select id="new_product_language"  name="language">'+
			'<option value="中">中文</option>'+
			'<option value="英">英文</option>'+
			'<option value="德">德文</option>'+
			'<option value="法">法文</option>'+
			'<option value="日">日文</option>'+
			'<option value="韓">韓文</option>'+
			'<option value="其他">其他</option>'+
		'</select>'+	
	'</li>'+
    '</form>';
	
	 openPopUpBox(content,600,280,'newProduct');	
	$('#new_product').show('slow');
	
	
	
}



function newProduct()
{
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
		   		
			 
			   findProductStock(placeID,0,1);
				closePopUpBox();
			 	$('#new_product').hide('slow');
				
			
		   }
		   else alert('編號或條碼重複，請重新輸入');
	   }
	 });
	
	
}




function editBox(id)
{
	if(accountLevel<50){
		alert('你必須具有店長以上的資格才能修改');
		return;
	}
	$.post('/product/get_product',{productID:id},function(data){
		if(data.result==true)
		{
			
			var content= '<form id="editProduct" style="text-align:left">'+
			'<h1>編輯商品資料</h1>'+		
			'<div ondblclick="$(\'#editBarcode\').attr(\'readonly\',false);" >商品條碼：'+
			'<input  class="big_text" id="editBarcode" type="text" name="barcode"  value="'+data.product.barcode+'" />'+
			'<input  type="hidden" name="productID"  value="'+data.product.productID+'" />'+
			'商品編號<input  class="big_text" id="editProductNum" type="text" name="productNum"   value="'+fillZero(data.product.productNum)+'"  ondblclick="$(\'#editProductNum\').attr(\'readonly\',false)"/>'+
			'</div>'+
			'<div>中文名稱：<input type="text" class="big_text" name="ZHName"  value="'+data.product.ZHName+'"/>'+
			'英文名稱：<input type="text" class="big_text" name="ENGName"  value="'+data.product.ENGName+'"/></div>'+
			'<div>商品定價：<input type="text" class="medium_text" name="price" id="price"  value="'+data.product.price+'"/>'+
			' 進貨折數：<input type="text" class="medium_text" name="buyDiscount"  onblur="$(\'#buyPrice\').val(Math.round($(\'#price\').val()*this.value/100))" value="'+data.product.buyDiscount+'"/>'+
			' 進貨價格：<input type="text" class="medium_text" name="buyPrice" id="buyPrice"  value="'+data.product.buyPrice+'"/></div>'+
			'<div>'+
				'最低折數：<input type="text"  name="minDiscount"  class="medium_text"   value="'+data.product.minDiscount+'"/>'+				
				' 出貨折數：<input type="text"  name="purchaceDiscount" id="memberID"  value="'+data.product.purchaseCount+'" class="medium_text" onblur="$(\'#purchasePrice\').val(Math.round($(\'#price\').val()*this.value/100))"/>'+
				' 出貨價格：<input type="text" id="purchasePrice" class="medium_text" value="'+Math.round(data.product.price*data.product.purchaseCount/100)+'"readonly="readonly" >'+				
			'</div>'+
			'<div>商品種類：<select name="productType" id="editProductType" onchange="if(this.value>1)$(\'#product_category\').val(0)">'+productTypeOption+'</select>'+
			' 遊戲分類：<select id="product_category" name="category">'+
				'<option value="A">A.親子益智</option>'+
				'<option value="B">B.派對歡樂</option>'+
				'<option value="C">C.輕度策略</option>'+
				'<option value="D">D.重度策略</option>'+
				'<option value="E">E.團隊合作</option>'+
				'<option value="F">F.兩人對戰</option>'+
				'<option value="0">不分類</option>'+
			'</select>'+
			' 供應廠商：<select name="suppliers" id="editSuppliers">'+suppliers+'</select>'+
			'</div>'+
			'<div>'+
			' 遊戲語言：<select id="new_product_language"  name="language">'+
					'<option value="中">中文</option>'+
					'<option value="英">英文</option>'+
					'<option value="德">德文</option>'+
					'<option value="法">法文</option>'+
					'<option value="日">日文</option>'+
					'<option value="韓">韓文</option>'+
					'<option value="其他">其他</option>'+
				'</select>'+	
						
			'</div>'+
			'</form>';
			
		    openPopUpBox(content,600,280,'editSend');	
			$('#editProductType').val(data.product.type);
			$('#editSuppliers').val(data.product.suppliers);
			$('#new_product_language').val(data.product.language);
			if(data.product.type>1)$('#product_category').val(0);
			else $('#product_category').val(data.product.category);
 
			
		}
	},'json')
	
	
}


function editSend()
{
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/edit_send",
	   data: $("#editProduct").serialize(),
	   success: function(data){
		 
		   if(data.result==true)
		   {
			 
				 $('#product_'+data.product.productID).html(productList(data.product));	
		   }
		   else alert('錯誤，請重新輸入');
	   }
	 });
	closePopUpBox();
	
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
				getProductByTime();
		
			}
			else alert('名稱重複');
		},'json')	
	}
}


function wareroomIO(type)
{
	if(type=="shipping") 
	{
		content='<h1>出貨單</h1>'+
		        '<div>目的地：'+
				'<label><input type="radio" name="shop_type" value="0" onclick="getShipmentShop()" checked="checked" >瘋桌遊</label>'+
				'<label><input type="radio" name="shop_type" value="1" onclick="getShipmentShop()">其他</label>'+
				'<div>折數<input type="text" id="spTotalCount"class="short_text" value="65"onclick="(\'#spTotalCount\').select()" onblur="shipmentChangeCount()">'+
				'<input type="button" class="big_button" value="全部套用" onclick="shipmentChangeCount()">'+
				'</div>'+
				'<div id="shopSelecter"></div>'+
				'</div>'+
				'<div class="divider"></div>';
		
		
	}
	else  content='<h1>進貨</h1>';
	content += '<div id="'+type+'Query"></div>'+
			  '<div class="clear"></div>'+
			  '<div id="'+type+'Select"></div>'+
			  
			  '<div style=" float:left; height:400px;">'+
			  '<form id="'+type+'Form">'+
			  '<table id="'+type+'Table" border="1" width="800px">'+
			  	'<tr>'+
					'<td>產品編號</td>'+
					'<td>中文名稱</td>'+
					'<td>英文名稱</td>'+
					'<td>產品價格</td>'+
					'<td>目前數量</td>'+
					'<td>產品折數</td>'+
					'<td>產品數量</td>'+
				'</tr>'+
			  '</table>'+
			  '</form>'
			  '</div>';

	 openPopUpBox(content,800,600,''+type+'Send');		
	 getShipmentShop(0);
	 queryProduct(type,'select');
	


}
function purchaseTable(data)
{
	var num = 0;
		$('#purchaseTable').append(
		'<tr >'+
			'<td>'+data.productNum+'</td>'+
			'<td><input type="hidden" name ="phrchase_productID_'+data.productID+'" value='+data.productID+'>'+data.ZHName+'</td>'+
			'<td>'+data.ENGName+'</td>'+
			'<td>'+data.price+'</td>'+
			'<td>'+data.num+'</td>'+
			'<td>'+data.buyDiscount+'</td>'+
			'<td><input type="text" class="short_text" value="0"  id="phrchase_num_'+data.productID+'" name = "phrchase_num_'+data.productID+'" '+
					'onclick="$(\'#phrchase_num_'+data.productID+'\').select()" onblur="chkMinus('+data.productID+')" /></td>'+	
			'</tr>'
		)
	
	height =parseInt($('.popUpBox').css('height').substr(0,$('.popUpBox').css('height').length-2))
	if(parseInt($('#purchaseTable').css('height').substr(0,$('#purchaseTable').css('height').length-2))+350>height) 
	{
		
		$('.popUpBox').css('height',height+50);	
		
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
		
		$('.popUpBox').css('height',height+50);	
		
	}

	
}
function purchaseSend()
{

	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/purchase_send",
	   data: $("#purchaseForm").serialize(),
	   success: function(data){
		
		   $('.popUpBox').html(data);
		
		   if(data.result==true)
		   {
			   
				 getProductByTime();
				getProductByTime();
				 closePopUpBox();
				 	
		   }
		   else alert('錯誤，請重新輸入');
	   }
	 });
	
	
	
	
}

function shippingSend()
{	
	var shopID = $('#shipmentShopID').val();
	var shopType =$('#shipment_type').val();
	if(shopID.substr(1)=="000000") {
		alert('請選擇出貨店家');
		return;
	}
	else if($("#shippingForm").serialize()==''){
		alert('你必須輸入至少一個貨品')	;
	}
	
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/shipping_send",
	   data: 'shopID='+shopID+'&shopType='+shopType+'&'+$("#shippingForm").serialize(),
	   success: function(data){
		
		   if(data.result==true)
		   {
			   
				 getProductByTime();
				
				 closePopUpBox();	
		   }
		   else alert('錯誤，請重新輸入');
	   }
	 });
	
	
	
	
}






$(document).ready(function(){
	getSuppliers();
	 setProductTime();
	 productType();	
	 queryProduct('product','auto');
	 classSet();
	
	
})

