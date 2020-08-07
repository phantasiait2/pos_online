// JavaScript Document
function getDirectShop()
{
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/system/get_direct_shop",
	   data: '',
	   success: function(data){
	
		if(data.result==true)
			for(key in data.shop)
			{
					
				$('#selectShopID').append('<option value='+data.shop[key].shopID+'>'+data.shop[key].name+'</option>');
					
			}
	   }
	 });
	
	
}



function productFormSend()
{

	$.ajax({
	   type: "POST",
	   dataType:"html",
	   url: "/product_flow/flow_report",
	   data: $("#flow_form").serialize(),
	   success: function(data){
	
		$('#product_list').html(data);
	   }
	 });

		
	
	
	
}


function getInventory()
{	
	$.ajax({
	   type: "POST",
	   dataType:"html",
	   url: "/product_flow/get_inventory",
	   data: $("#flow_form").serialize(),
	   success: function(data){
	
		$('#product_list').html(data);
	   }
	 });	
	
}
function getFlowRate()
{
	
	$.ajax({
	   type: "POST",
	   dataType:"html",
	   url: "/product_flow/product_flow_rate",
	   data: $("#flow_form").serialize(),
	   success: function(data){
	
		$('#product_list').html(data);

		$("#product_flow_table").tablesorter({widgets: ['zebra'],sortList: [[8,1]]});

		
	   }
	 });	
	
}

function update_sale()
{
	$.post('/product_flow/product_sale_update',{},function(data)
	{
		if(data.result==true) alert('完成')
		
	},'json')	
	
	
}

function getProductAccounting(excel)
{
	if(excel==1) 
	{
		content = '<div id="create_excel"><h1>檔案產生中，請稍候..</h1><img src="/images/ajax-loader.gif"/></div>';
		openPopUpBox(content,300,280,'closePopUpBox');	
		
	}
	
		$.ajax({
	   type: "post",
	   dataType: "html",
	   data:$("#flow_form").serialize()+'&excel='+excel,
	   url: "/product_flow/product_accounting",
	   success: function(data){
		   if(excel==1)
		   {
			 $('#create_excel').html(data);
			}
		   else
		   {
		   	   
			$('#product_list').html(data);
		   }
	   }
	 });	
	

	
}
var ioTotalNum = 0;
var ionum =0 ;
var productData;
function getAllProduct()
{
    if($('#selectType').val()==2)
    {
        showAll();
        return;
    }
	$('#posResult').html(ioHeader());
	
	$('#saveTemp').html('');
     $.post('/product_flow/get_all_product',{'from':$('#from').val()},function(data)
    {
      
        if(data.result==true)
            {
                 
                var totalNum = data.num;
		
				ioTotalNum = totalNum;
				ionum =0 ;
			    
				purchaseIn()
				
                  $('#sec').html('');
            }
       
       
   },'json')
    
}

function purchaseIn()
{
    $('#sec').append('進貨檔建立中...');
    
     $.post('/product_flow/purchase_in_table',{'from':$('#from').val()},function(data)
    {
      
        if(data.result==true)
            {
                  $('#sec').append('ok<br/>'); 
                sellIn();
				
              
            }
       
       
   },'json')
    
}
function sellIn()
{
      $('#sec').append('銷貨檔建立中...');
    $.post('/product_flow/sell_in_table',{'from':$('#from').val()},function(data)
    {
      
        if(data.result==true)
            {
                
                  $('#sec').append('ok<br/>'); 
                resultIn(0);
				
              
            }
       
       
   },'json')
    
    
    
}

function resultIn(offset)
{
    
    percent = Math.round(offset*100/ioTotalNum);
    $('#progress').html(offset+'/'+ioTotalNum +' '+percent+'%');
    
     $.post('/product_flow/result_in_table',{'from':$('#from').val(),'offset':offset},function(data)
    {
      
        if(data.result==true)
            {
                 
                if(offset+1000<ioTotalNum)  resultIn(offset+1000);
				else showAll();
              
            }
       
       
   },'json')
    
    
    
}
function showAll()
{
    
       $.post('/product_flow/show_all',{'from':$('#from').val(),shopID:$('#selectShopID').val()},function(data)
    {
      
        if(data.result==true)
            {
                 
               for(key in data.product)
                   {
                       var aShopID = $('#selectShopID').val();
                        inputIOContent('posResult',data.product[key],'',aShopID,data.shop) 
                       
                   }
              	 $('#progress').html('<input type="button" onclick="exportExcel(\'進銷存表'+$('#from').val()+'\')" value="匯出Excel">');
            }
       
       
   },'json') 
    
    
}


function ioHeader(token)
{
		if(token!=undefined) name = '日期'
		else name ='品名';
	
		return '<tr><td></td><td></td><td></td><td colspan="3" style="text-align:center" >期初</td></td><td colspan="4" style="text-align:center">進貨</td><td colspan="5" style="text-align:center">銷貨</td><td colspan="3" style="text-align:center">期末</td><td  colspan="5">銷售表現</td><td></td></tr>'+
                            '<tr><td>品號</td><td>店名</td><td>'+name+'</td><td>數量</td><td>單價</td><td>成本</td><td>數量</td><td>單價</td><td>成本</td><td>調撥</td><td>數量</td><td>單價</td><td>成本</td><td>銷售單價</td><td>銷售總額</td><td>數量</td><td>單價</td><td>成本</td><td>總毛利</td><td>均毛利</td><td>均毛利率</td><td><input type="button" value="全部展開" onclick="$(\'.product_all\').toggle()"></td><td><td></tr>'
	
}


function getIO()
{
   content='<h2>請選擇月份區間</h2>from<input type="text" id="from">'+
	   					'<select id="selectType">'+
	   					'<option value="2">讀取上次紀錄</option>'+
	   					'<option value="1">重新載入</option></select>'+
	   					'<select id="selectShopID">'+
	   					'<option value="-1">全部</option>'+
	   				
	   
	   					'</select>'+
							　
						'<input type="button" class="big_button"   value="查詢全部" onclick="; getAllProduct()"/>'+
       
       '<input type="button" class="big_button"   value="查詢單一" onclick="; triOne()"/>'+
                        '<div id="progress"></div>'+
                        '<div id="sec"></div> '+
                        '<table border="1" style="text-align:right" id="posResult">'+
	   					ioHeader()+
               
                        '</table> ';
	content+='<div id="saveTemp"></div>';
       $('#product_list').html(content);                  
    getDirectShop();
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

   //  getAllProduct();
    
}
function openProductDetail(productID)
{
	d = $('#from').val()
	t= d.split('-');
	year = t[0];
	month = t[1];
	
	
	content = '<h1><span id="posResult_name_'+productID+'"></span> 明細(不含寄賣)</h1><table border="1" id="posResult_'+productID+'">'+ioHeader()+'</table>';
		openPopUpBox(content,800,280,'closePopUpBox');	
	
	productDetail(productID,year,month,1);
	
}




function productDetail(productID,year,month,day)
{
	
	if(year%4==0 && month==2)	maxDay = 29;
	else if(month==2)maxDay = 28;
	else if(month==1||month==3||month==5||month==7||month==8||month==10||month==12)maxDay = 31; 
	else maxDay = 30;	
	if(day>maxDay) return;
	  var param = { 'date':year+'-'+month+'-'+day,'productID':productID,'byDay':1
       }
	  
	  
	  $.post('/product_flow/pos_account_io',param,function(data){
       
        if(data.result==true)
        {
			var aShopID = $('#selectShopID').val();
             inputIOContent('posResult_'+productID,data,param['date'],aShopID)	
			 productDetail(productID,year,month,day+1)
                popUpBoxHeight(0);         
                  
        }
         
        
    },'json')
    
	
	
	
	
}
function saveNextMonth(param)
{

	 return;
	$.post('/product_flow/pos_account_io_update',param,function(data){
      
        if(data.result==true)
        {
		       console.log(param); 
                  
        }
		else alert('error')
         
        
    },'json')
    
	
	
}




function posAll()
{
    i = ionum;
    ionum++;
	
	
    percent = Math.round(i*100/ioTotalNum);
    if(percent==100)
		{
            boxProductUpdate();
			$('.tdClass').bind('mouseover',function(){showTitle(this.id);})
			 $('#progress').html('<input type="button" onclick="exportExcel(\'進銷存表'+$('#from').val()+'\')" value="匯出Excel">');
			
			
		}
    else $('#progress').html(i+'/'+ioTotalNum +' '+percent+'%');
    
        var param = { 'from':$('#from').val(),'productID':productData[i].productID,'selectType':$('#selectType').val()
       }
        
   $('#nowProductID_'+i%4).html(productData[i].productID)
        t =  parseInt($('#sec').html());
        $('#sec').html(t+1);
		//alert('<span id="p_'+productData[i].productID+'">'+productData[i].productID+'</span>');
    $.post('/product_flow/pos_account_io',param,function(data){
       
          t =  parseInt($('#sec').html());
        $('#sec').html(t-1);
        if(data.result==true)
        {
			var aShopID = $('#selectShopID').val();
	
			
			//$('#p_'+productData[i].productID).hide();
            
            if(data.mulity!==undefined)
            {
                for(key in data.product)
                {
                    var param = { 'from':$('#from').val(),'productID':data.product[key].inf.productID,'selectType':$('#selectType').val()
       }
                    if(aShopID==-1)saveNextMonth(param);
                    inputIOContent('posResult',data.product[key],'',aShopID) 
                    
                    
                }
                    
            }
            else 
            {
                if(data.inf!==undefined)
                    {
                 var param = { 'from':$('#from').val(),'productID':data.inf.productID,'selectType':$('#selectType').val()}
                
                 if(aShopID==-1)saveNextMonth(param);
                inputIOContent('posResult',data,'',aShopID)	;
                    }
            }
            
             
			 posAll();
                        
                  
        }
         
        
    },'json')
    
    
    
}

function boxProductUpdate()
{
    $.post('/product_flow/box_product_update',{from:$('#from').val()},function(data)
          {
            if(data.result)
                {
                    
                    
                    $('#progress').after($('#from').val()+'盒裝商品更新完成');
                }
        
        
    },'json')
    
    
    
    
}





function triOne()
{
    var productID = prompt("請輸入產品序號?");
    var param = { 'from':$('#from').val(),'productID':productID,'selectType':$('#selectType').val()}
       
         $.post('/product_flow/pos_account_io',param,function(data){
       
        if(data.result==true)
        {
			var aShopID = $('#selectShopID').val();
	
			
			//$('#p_'+productData[i].productID).hide();
            
            if(data.mulity!==undefined)
            {
                for(key in data.product)
                {
                   param = { 'from':$('#from').val(),'productID':data.product[key].inf.productID,'selectType':$('#selectType').val()
                           }
                    if(aShopID==-1)saveNextMonth(param);
                    inputIOContent('posResult',data.product[key],'',aShopID) 
                    
                    
                }
                    
            }
            else 
            {
                   if(data.inf!==undefined)
                       {
                var param = { 'from':$('#from').val(),'productID':data.inf.productID,'selectType':$('#selectType').val()
       }
                
                if(aShopID==-1)saveNextMonth(param);
                inputIOContent('posResult',data,'',aShopID)	
            }
            
            }
                        
                  
        }
         
        
    },'json')
    
    
    
}






function round10(num,dig)
{
	
	if(num==0) return 0;
	var tick = 1;
	if(dig<0)
	for(i=0;i>dig;i--)
	{
	
		tick = tick*10;
	
	}
	
	return Math.round(num * tick)/tick;
	
	
}


function showTitle(id)
{
	a = id.split("_");
	$('#hintBox').detach();
	$('#'+id).append('<div id="hintBox" style=" color:blue">('+a[0]+')</div>');
		
	
}


function openShopDetail(id)
{
	$('.'+id).toggle();
	if($('#save_'+id).html()!='')
	{
		$('#main_'+id).after($('#save_'+id).html());
		$('#save_'+id).html('');
	}
	
	 
	
	
}
var IOindex = 1;
function inputIOContent(id,data,date,aShopID,shop)
{
	
  
	 if(data.inf !== undefined &&data.inf.productNum !== undefined)
                    {  
                        
                       
						
						if(aShopID!=-1)
							{
								
								data.all = data.each[aShopID];		
							
							}
                         
                        
						if(data.all===undefined ||(data.all.O_amount==0&&data.all.P_amount==0&&data.all.S_amount==0&&data.all.E_amount==0));
						else
						{
							
							newAvgCost = data.all.S_avgPrice;
							var totalProfit = round10(data.all.S_totalSellPrice,-2) - round10(newAvgCost*data.all.S_amount,-2);
                            if(data.all.S_amount==0) var profit =0;
							else var profit = totalProfit/data.all.S_amount;
							
                            if(data.all.S_totalSellPrice==0) var profitRatio = 0;
							else var profitRatio = round10(totalProfit*100 / data.all.S_totalSellPrice,-2);
                            
							if(isNaN(profitRatio)||data.all.S_totalSellPrice==0)profitRatio=0;
							
				
							
							
							if(date!='') 
							{
								str = date;
								$('#posResult_name_'+data.inf.productID).html(data.inf.ZHName+'('+data.inf.ENGName+')['+data.inf.language+']');
								myid= 'product_'+data.inf.productID+'_'+date;
								openStr = 'openShopDetail(\''+myid+'\')';
								detailBtn ='';
							}
							else
							{
								str =data.inf.ZHName+'('+data.inf.ENGName+')['+data.inf.language+']';
								
								myid= 'product_'+data.inf.productID;
								openStr = 'openShopDetail(\''+myid+'\')';
								detailBtn ='<input type="button" value="明細" onclick=" openProductDetail(\''+data.inf.productID+'\')">';

							}
							$('#main_'+myid).detach();
                            
                            if(data.all.O_amount==0)O_avgcost = 0 ;
                             else O_avgcost = round10(data.all.O_totalCost/data.all.O_amount,-2)
                            
                            if(data.all.P_amount!=0)P_avgcost = round10(data.all.P_totalCost/data.all.P_amount,-2);
                            else P_avgcost = 0 ;
                            
                            if(data.all.S_amount==0) S_sellPrice = 0;
                            else S_sellPrice = round10(data.all.S_totalSellPrice/data.all.S_amount,-2)
                            
							 $('#'+id).append(
								 '<tr id="main_'+myid+'"><td>'+data.inf.productNum+'</td>'+
									 '<td>總量</td>'+
									 '<td class="tdClass" id="品名_'+IOindex+'">'+str+'</td>'+
									 '<td class="tdClass" id="期初量_'+IOindex+'">'+data.all.O_amount+'</td>'+
									 '<td class="tdClass" id="期初單價_'+IOindex+'">'+O_avgcost+'</td>'+
								 	'<td class="tdClass" id="期初成本_'+IOindex+'">'+round10(data.all.O_totalCost,-2)+'</td>'+
									 '<td class="tdClass" id="進貨數量_'+IOindex+'">'+data.all.P_amount+'</td>'+
								 	'<td class="tdClass" id="進貨單價_'+IOindex+'">'+P_avgcost+'</td>'+
								 '<td class="tdClass" id="進貨成本_'+IOindex+'">'+round10(data.all.P_totalCost,-2)+'</td>'+
                                 '<td class="tdClass"> '+data.all.move+' </td>'+
								 '<td class="tdClass" id="銷售數量_'+IOindex+'">'+data.all.S_amount+'</td>'+
								 '<td class="tdClass" id="銷售單價_'+IOindex+'">'+round10(data.all.S_avgPrice,-2)+'</td>'+
								 '<td class="tdClass" id="銷售成本_'+IOindex+'">'+round10(data.all.S_avgPrice*data.all.S_amount,-2)+'</td>'+
								'<td class="tdClass" id="銷售金額_'+IOindex+'">'+S_sellPrice+'</td>'+
								'<td class="tdClass" id="銷售總額_'+IOindex+'">'+round10(data.all.S_totalSellPrice,-2)+'</td>'+
								'<td class="tdClass" id="期末數量_'+IOindex+'">'+data.all.E_amount+'</td>'+
								'<td class="tdClass" id="期末單價_'+IOindex+'">'+round10(data.all.S_avgPrice,-2)+'</td>'+
								'<td class="tdClass" id="期末成本_'+IOindex+'">'+round10(data.all.E_totalCost,-2)+'</td>'+
								'<td class="tdClass" id="期末總毛利_'+IOindex+'">'+round10(totalProfit,-2)+'</td>'+
								'<td class="tdClass" id="期末均毛利_'+IOindex+'">'+round10(profit)+'</td>'+
								 '<td class="tdClass" id="期末均毛利率_'+IOindex+'">'+round10(profitRatio)+'%</td><td><input type="button" value="展開" onclick="'+openStr+'"></td>'+
								 	'<td>'+detailBtn+'</td>'+
								 
								 	'</tr>');
								
							IOindex++;
							$('#saveTemp').append('<div id="save_'+myid+'" style="display:none;"></div>');
                           
							if(aShopID==-1)
							for(key in shop)
							{	
								var shopID = shop[key].shopID;
							 if(data.each[shopID]===undefined) continue;
								newAvgCost = data.all.S_avgPrice;;
							var totalProfit = round10(data.each[shopID].S_totalSellPrice,-2) - round10(newAvgCost*data.each[shopID].S_amount,-2);
                            
                            if(data.each[shopID].S_amount==0) var profit =0;
							else var profit = totalProfit/data.each[shopID].S_amount;
							
                             if(data.each[shopID].S_totalSellPrice==0) var profitRatio = 0;
							 else var profitRatio = round10(totalProfit*100 / data.each[shopID].S_totalSellPrice,-2);
                  
							if(isNaN(profitRatio)||data.each[shopID].S_totalSellPrice==0)profitRatio=0;
							
							
                             if(data.each[shopID].O_amount==0)O_avgcost = 0 ;
                             else O_avgcost = round10(data.each[shopID].O_totalCost/data.each[shopID].O_amount,-2)
                            
                            if(data.each[shopID].P_amount!=0)P_avgcost = round10(data.each[shopID].P_totalCost/data.each[shopID].P_amount,-2);
                            else P_avgcost = 0 ;
                            
                            if(data.each[shopID].S_amount==0) S_sellPrice = 0;
                            else S_sellPrice = round10(data.each[shopID].S_totalSellPrice/data.each[shopID].S_amount,-2)    
                                
                            
								
							 $('#save_'+myid).append(
								 '<tr class="product_all '+myid+'" style="display:none;background-color:#FF88C2"><td>'+data.inf.productNum+'</td>'+
									 '<td colspan="2">'+shop[key].name+'</td>'+
									 '<td>'+data.each[shopID].O_amount+'</td>'+
									 '<td>'+O_avgcost+'</td>'+
								  	'<td>'+round10(data.each[shopID].O_totalCost,-2)+'</td>'+
									 '<td>'+data.each[shopID].P_amount+'</td><td>'+P_avgcost+'</td>'+
								 	'<td>'+round10(data.each[shopID].P_totalCost)+'</td>'+
                                    '<td>'+data.each[shopID].move+'</td>'+
								 	'<td>'+data.each[shopID].S_amount+'</td><td>'+round10(data.each[shopID].S_avgPrice,-2)+'</td><td>'+round10(data.each[shopID].S_avgPrice*data.each[shopID].S_amount,-2)+'</td>'+
                                    '<td>'+S_sellPrice+'</td><td>'+data.each[shopID].S_totalSellPrice+'</td>'+'<td>'+data.each[shopID].E_amount+'</td><td>'+S_sellPrice+'</td><td>'+round10(data.each[shopID].E_totalCost,-2)+'</td>'+
								 '<td>'+(round10(profit,-2)*data.each[shopID].E_amount)+'</td>'+
								 '<td>'+round10(profit,-2)+'</td>'+
                                 '<td>'+round10(profitRatio,-2)+'%</td></tr>');
							}
					
							
						}
				}
	
}


function getOrderIO()
{
    var from = $('#from_year').val()+'-'+$('#from_mon').val()+'-'+$('#from_day').val();
    var to = $('#to_year').val()+'-'+$('#to_mon').val()+'-'+$('#to_day').val();
   
      var param = { 'from':from, 'to':to}
        $('#product_list').html('<h1>檔案產生中，請稍候..</h1><img src="/images/ajax-loader.gif"/>');
    $.post('/product_flow/get_order_IO',param,function(data){
        
       $('#product_list').html(data);
        
        
        
    },'html');
    
    
}



function exportExcel(name){
 $("#posResult").table2excel({
					exclude: ".noExl",
					name: "Excel Document Name",
					filename: name,
					fileext: ".xls",
					exclude_img: true,
					exclude_links: true,
					exclude_inputs: false
				});
}
