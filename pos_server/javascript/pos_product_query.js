// JavaScript Document
//placeID :  query bar way (must be distinct) ex: aa will put to <div id="aaQuery">
//autoSelect : auto select or not(show select page or not)
//putin : select form id 
var suppliers;
var suppliersList = new Array();
var suppliersOrderList = new Array();
var suppliersDayList = new Array();
 google.load("language", "1");

function queryProduct(placeID,autoSelect)
{

	if(autoSelect=='select') autoSelect = 0;
	else autoSelect =1 ;

	
		
	content = '<div style="text-align:left" class="productQueryContainer">'+
			  '商品條碼：<input type="number" name="productBacordName" class="big_text query"  id="'+placeID+'_findBarcode" value="" onkeyup="findProductStock(\''+placeID+'\',1,\''+autoSelect+'\')" onfocus="clearOther(this.id)"/>'+
			  '　商品編號：<input type="text" class="big_text query"   id="'+placeID+'_findProcutNum" value="" onkeyup="findProductStock(\''+placeID+'\',1,\''+autoSelect+'\')" onfocus="clearOther(this.id)"/>';

	if(placeID=='product'||placeID=='clientOrder')
	{
        	orderOption= '<option value="0">無</option>' ;
        for(key in classZh)
        {
            if(classEng[key]!='editBtn'&&classEng[key]!='delete')
                orderOption+='<option value="'+classEng[key]+'">'+classZh[key]+'</option>';

        }
		content+= '排序1：<select id="'+placeID+'_order1" >'+orderOption+'</select>'+
				  '<select id="'+placeID+'_sequence1" >'+
					'<option value="ASC">順</option>'+
					'<option value="DESC">倒</option>'+
				  '</select>';
        /*
				  '排序2：<select id="'+placeID+'_order2" >'+orderOption+'</select>'+
				  '<select id="'+placeID+'_sequence2" >'+
					'<option value="ASC">順</option>'+
                    '<option value="DESC">倒</option>'+
				  '</select>'+
                  */
				content+='顯示筆數：<select id="'+placeID+'_num" >'+
					'<option value="50">50</option>'+
					'<option value="100" selected="selected">100</option>'+
					'<option value="200">200</option>'+
					'<option value="0">全部</option>'+
				   '</select>' +
				'<input type="hidden" id="'+placeID+'_start"  value="0" />';
				
	}
    content+='<label><input id="'+placeID+'_hide" type="checkbox" value="1"/>顯示隱藏商品</label>';
   
    
     content+=	
			  '<select id="'+placeID+'_xclass" >'+
			   	'<option value="0" >不顯示帳務管理品項</option>'+
			    '<option value="1" >只顯示帳務管理品項</option>'+
			    '<option value="2" selected="selected">全部顯示(含商品及帳務)</option>'+			  
			  '</select>';
    
    
    
	content+='<div class="clear"></div>'+
			  '中英名稱：<input type="text" class="big_text query"  style=" width:475px" placeholder="在此搜尋產品名稱"  id="'+placeID+'_findProcutQuery" value="" onfocus="clearOther(this.id)" onChange ="if(!$(\'#'+placeID+'_autoselect\').is(\':checked\')) findProductStock(\''+placeID+'\',0,\''+autoSelect+'\')"; />';
	if(placeID=='product'||placeID=='clientOrder'||placeID=='purchase'||placeID=='editCabinet')
	{
				if($('#shopID').val()<1000)
				{			
					content+=		  
				  '供應商:<select id="'+placeID+'_suppliers" >'+suppliers+'</select>'
				}
			  content+=	
			  '狀態:<select id="'+placeID+'_status" >'+
			   	'<option value="2" selected="selected">全部</option>'+
			    '<option value="1" >開放中</option>'+
			    '<option value="0">關閉中</option>'+			  
			  '</select>';
 		content+=	
			  ';150商品:<select id="'+placeID+'_150" >'+
			   	'<option value="2" selected="selected">全部</option>'+
				'<option value="3" >Top Product</option>'+
			    '<option value="1" >150</option>'+
			    '<option value="0">非150</option>'+			  
			  '</select>';				 		  
	}
    
	content+='<label><input id="'+placeID+'_autoselect" type="checkbox" value="1"/>自動選擇(適用於連續)</label>';


    content+= '<input type="button" class="big_button"  value="查詢商品" '+
			  'onClick="$(\'#'+placeID+'_start\').val(0);findProductStock(\''+placeID+'\',0,\''+autoSelect+'\')" />'+
    		  '<input type="button" class="big_button"  value="取消" onClick="clearProduct(\''+placeID+'\' )" />'+
			  '<span style="color:red;display:none" id="search_show" >請輸入搜尋內容</span>'
			  '<div id="'+placeID+'divSayIt" style=" float:left; z-index:-5; width:0px;height:0px;overflow:hidden"></div>'+
			  '</div>';
	
	$('#'+placeID+'Query').html(content);
    if(placeID=='product') $('#'+placeID+'_xclass').val(0);
	getSuppliers();
	if(autoSelect==1)$('#'+placeID+'_autoselect').attr("checked",true);
	$('#'+placeID+'Select').detach();
	$('#'+placeID+'Query').after('<div class="clear"></div>'+
		  '<div id="'+placeID+'Select"></div>');

}

function clearOther(id)
{
	
	var tmp = $('#'+id).val();
	if(tmp=='')	$('.query').val('');
}



function clearProduct(placeID)
{
	$('#product_list').html('');
	$('#'+placeID+'Select').hide();
	$('#'+placeID+'_findBarcode').val('');
	$('#'+placeID+'_findProcutNum').val('');
	$('#'+placeID+'_findProcutENGName').val('');
	$('#'+placeID+'_findProcutZHName').val('')
}

function changePage(placeID,type,auto,page)
{
	$('#'+placeID+'_start').val((page-1)*$('#'+placeID+'_num').val());
	 findProductStock(placeID,type,auto);
}

productTableChk =false;
function findProductStock(placeID,type,auto){
	
	
	$('#search_show').hide();
	$('#'+placeID+'_selectList_table_container').hide();//close selector
	if(type==1){
		if(enter()==false) return;
		$('#'+placeID+'_start').val(0);
	}
	if(auto!=1&&$('#'+placeID+'_findBarcode').val()==''&&$('#'+placeID+'_findProcutNum').val()==''&&$('#'+placeID+'_findProcutQuery').val()=='')
	{
		$('#search_show').show();
		return;	
	}
	
	
	if(auto!=1&&$('#'+placeID+'_findBarcode').val()==''&&$('#'+placeID+'_findProcutNum').val()=='')auto=0;
	if($('#'+placeID+'_autoselect').is(":checked"))auto=1;
	else auto=0;
    if($('#'+placeID+'_hide').is(":checked"))hide=1;
	else hide=0;
   
    xclass = $('#'+placeID+'_xclass').val();
     $('#'+placeID+'Select').show().html('<img src="/images/ajax-loader.gif"/>')
 
    
$.post('/product/get_product_stock',{barcode:$('#'+placeID+'_findBarcode').val(),productNum:$('#'+placeID+'_findProcutNum').val(),ENGName:$('#'+placeID+'_findProcutENGName').val(),query:$('#'+placeID+'_findProcutQuery').val(),suppliers:$('#'+placeID+'_suppliers').val(),order1:$('#'+placeID+'_order1').val(),sequence1:$('#'+placeID+'_sequence1').val(),order2:$('#'+placeID+'_order2').val(),sequence2:$('#'+placeID+'_sequence2').val(),num:$('#'+placeID+'_num').val(),start:$('#'+placeID+'_start').val(),
status:$('#'+placeID+'_status').val(),topProduct:$('#'+placeID+'_150').val()
,placeID:placeID,hide:hide,xclass:xclass

},function(data){
		if(data.result==true)
		{
			
			
			if((data.product.length>1&&auto==0)){
				 selectList(placeID,data.product);
				 if((placeID=='product'||placeID=='clientOrder')&&$('#product_header_table').length==0) 
                 {
                     setProductTable('',1)
                       productTableChk = true;
                 }
			
			}
			else
			{
				$('#'+placeID+'Select').html('');

				switch(placeID)
				{
                    case  'chkProduct':
                        chkProductTable(data.product[0]);
                    break;    
					case 'checkout' :
						checkoutTable(data.product[0]);
					break;
					case 'shipping' :
						setProductTable(data.product[0]);
					break;
					case 'purchase' :
						for(key in data.product){
							purchaseTable(data.product[key]);
							SayIt(data.product[key].ZHName,placeID);
						}
					break;
                    case 'purchaseOrder' :
						for(key in data.product){
							purchaseOrderTable(data.product[key]);
							
						}
					break;
					case 'product' :
					    if(auto==0)
                            {
                                
                                if(!productTableChk)
                                {
                                    setProductTable(data.product,1);
                                    productTableChk = true;
                                }
                                else  productTable(data.product[0],1);
                                
                            }
                        else
                        {
                          setProductTable(data.product,1);
                              productTableChk = true;
						var pages = parseInt(data.totalNum/parseInt($('#'+placeID+'_num').val()));
						if(data.totalNum%parseInt($('#'+placeID+'_num').val())!=0) pages++;
						page = parseInt($('#'+placeID+'_start').val()/$('#'+placeID+'_num').val())+1;
						$('#pager').append('<a  class="pager_page" id="'+placeID+'_pager_first" onclick="changePage(\''+placeID+'\','+type+','+auto+','+1+')">第一頁</a>｜');

						if(page!=1){
							$('#pager').append('<a  class="pager_page" id="'+placeID+'_pager_last" onclick="changePage(\''+placeID+'\','+type+','+auto+','+(page-1)+')">上一頁</a>｜');
						}
						for(i=1;i<=pages;i++)
						{
							$('#pager').append('<a  class="pager_page" id="'+placeID+'_pager_'+i+'" onclick="changePage(\''+placeID+'\','+type+','+auto+','+i+')">'+i+'</a> ');
					
						}
						if(page!=pages){
							$('#pager').append('<a  class="pager_page" id="'+placeID+'_pager_next" onclick="changePage(\''+placeID+'\','+type+','+auto+','+(page+1)+')">｜下一頁</a>');
						}						
						$('#pager').append('<a  class="pager_page" id="'+placeID+'_pager_first" onclick="changePage(\''+placeID+'\','+type+','+auto+','+pages+')">｜最後一頁</a>');
						$('#'+placeID+'_pager_'+page).css('font-size','14pt');  
                            
                        }
						
						
					break;
					
					case 'clientOrder' ://for client order
						for(key in data.product){
							clientOrderTable(data.product[key]);
						}						// do nothing not suppose in here 
						
					break;	
						
					case 'orderAppend':
						for(key in data.product){
							orderAppendTable(data.product[key],1);
						}
					break;	
					case 'consignment':		
						for(key in data.product)
						{
							consignmentTable(data.product[key],1);
						}
					break;
					case 'backProduct':
						for(key in data.product)
						{
							backProductTable(data.product[key])
							
						}					
					
					break
					case 'backOrAdjust':
						for(key in data.product)
						{
							backOrAdjustTable(data.product[key])
							
						}					
					
					break
					
					
					case 'productAnnounce':
						for(key in data.product)
				        {
							productAnnounceTable(data.product[key])
							
						}					
					
					break;
					case 'editCabinet':
						for(key in data.product)
						{
							editCabinetTable(data.product[key])
							
						}					
					
					break	
					case 'orderChk' :
						orderChkTable(data.product[0]);
					break;	
                    case 'consignmentAppend':
                        consignmentAppendTable(data.product[0])
                    break; 
                    case 'cs_product':
                        cs_productTable(data.product[0])
                    break;     
                    default:
						alert('不開放批次輸入');
					break;	
				}
			}
			
				$('#'+placeID+'_findBarcode').val('');
				$('#'+placeID+'_findProcutNum').val('');
			
		}
		else 
		{
			$('#'+placeID+'Select').html('<h1>查無此商品，請重新查詢</h1>');
				SayIt('查無此商品，請重新查詢',placeID);
		}
	
	},'json')


}
var selectData;
function selectList(placeID,data)
{

	$('#'+placeID+'Select').show().html(
		
	     '<div id="'+placeID+'_selectList_table_container">'+
		 	'<div style=" position:absolute;right:35px;cursor:pointer;color:#800" onclick="$(\'#'+placeID+'_selectList_table_container\').hide()">X</div>'+
			 '<div class="selectList_table" >'+
			 	'<table id="'+placeID+'_selectList_table"></table>'+
			'</div>'+
		'</div>'
	);
	$('#'+placeID+'_selectList_table').width((parseInt($('#'+placeID+'_selectList_table').parent().width())-20)+'px');

	for(key in data)
	{

	var color ='#000;font-weight:bold';
			if(data[key].nowNum<1 && placeID =='checkout')  color = '#777';
			$('#'+placeID+'_selectList_table').append(
				'<tr id="select_'+placeID+'_'+data[key].productID+'" style="color:'+color+'" onclick="'+placeID+'Table(selectData['+key+']);$(\'#select_'+placeID+'_'+data[key].productID+'\').hide()">'+
					'<td><input type="checkbox"  class="selectListChk"  value="1" ><td>'+
					'<td style="width:50px">'+data[key].language+'</td>'+
					'<td>'+data[key].nowNum+'</td>'+
					'<td>'+data[key].ZHName+'('+data[key].ENGName+')</td>'+
				'</tr>'
			);
			if(key%2==0)$('#'+placeID+'_'+data[key].productID).css('background-color','#FFF');		
	}
	$('#'+placeID+'_selectList_table').append();
	selectData = data;
	
	
}


function getSuppliers()
{
	
	$('body').append('<div class="popUpCover supplierCover" style="display:none"><h1 style="color:white">頁面讀取中...</h1></div>')
	$('.supplierCover').css('height',$(document).scrollTop()+$(window).height());	
	$('.supplierCover').css('width',$(window).width());	
	$('.supplierCover').fadeIn('fast');

	
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   sync:false,
	   url: "/product/get_suppliers",
	   data: $("#preorderContent").serialize(),
	   success: function(data){
		  suppliers='';
		for (key in data.suppliers)
		{
			
			suppliers+='<option  class="suppliers_option" value="'+data.suppliers[key].supplierID+'">'+data.suppliers[key].name+'</option>';
			suppliersList[data.suppliers[key].supplierID] = data.suppliers[key].name;
			suppliersOrderList[data.suppliers[key].supplierID] = data.suppliers[key].order;
			suppliersDayList[data.suppliers[key].supplierID] = data.suppliers[key].day;
		}
		
		$('#product_suppliers').html(suppliers);
		$('.supplierCover').fadeOut();
		$('.supplierCover').detach();
	   }
	   })	
	





	
}
function SayIt(Rlt,placeID) {
       var lng = 'zh-CN';
        var strURL = 'http://translate.google.com.tw/translate_tts?q=' + Rlt + '&tl=' + lng;
        var embed = '<embed src="' + strURL + '" autostart="true" />';
	
        $('#'+placeID+'divSayIt').html(embed);
}

function showImg(url,width,height)
{
	if(!width) width =200;
	if(!height) height = 200;
	 content='<img style=" max-width:'+width+'px;max-height:'+height+'px" src="'+url+'">';
	 openPopUpBox(content,width,height+40,'closePopUpBox');	
	 $('.popUpCover').hide();
}

