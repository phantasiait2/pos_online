<?php $time = getdate()?>
<div class="product">
查詢報表日期 
<form id="reportForm" name ="reportForm"action="/accounting/get_day_report" target="_blank" method="post">
<select name="year" id ="year">
<?php for($i=$time['year']+1;$i>=2011;$i--):?>
<option value="<?=$i?>"><?=$i?></option>
<?php endfor;?>
</select>
年
<select name="mon" id ="mon" onchange="functionSwitch()" >
<?php for($i=1;$i<=12;$i++):?>
<option value="<?=$i?>"><?=$i?></option>
<?php endfor;?>
</select>
月
<select name="mday" id="mday"onchange="getDayReport()" >
<?php for($i=1;$i<=31;$i++):?>
<option value="<?=$i?>"><?=$i?></option>
<?php endfor;?>
</select>
日

<input type="button" class="big_button"  value="查詢日報表" onClick="getDayReport()"  />
<?php if($shopID==0):?>
<input type="button" class="big_button"  value="查詢總日報表" onClick="getEachDayReport()"  />

<input type="button" class="big_button"  value="查詢總月報表" onClick="getMonReportAll(1)"  />


<?php endif;?>

<input type="button" class="big_button"  value="查詢月報表" onClick="getMonReport()"  />
<input type="button" class="big_button"  value="查詢逐日月報表" onClick="getMonReportByDay()"  />
<input type="button" class="big_button"  value="查詢逐月報表" onClick="getMonReportByMonthCheck()"  />
<input type="button" class="big_button"  value="查詢分析報表" onClick="getDetailReport(0)"  />
<input type="button" class="big_button"  value="查詢好事" onClick="getDayGoodthing()"  />
<a href="/race/best30_online"><input type="button" class="big_button"  value="查詢精選30銷售"   /></a>
<?php if($shopID==0):?>
<input type="button" class="big_button"  value="精選30報表" onClick="getBest30Report()"  />
<input type="button" class="big_button"  value="發票管理" onClick="getEInvoice()"  />
<?php endif;?>



<select name="shopID" id="shopID"  >
<?php if($shopID==0):?>
	<?php foreach($shop as $row):?>
	<option value="<?=$row['shopID']?>"><?=$row['name']?></option>
    <?php endforeach;?>
    </select>
<input type="hidden" value="<?=$row['shopID']?>" id="maxShopID">
<?php endif;?>

<input type="button" class="big_button"  id="print" value="列印本頁"  onclick="document.forms.reportForm.submit()" style="display:none" />
</form>
<div class="divider"></div>	
<div class="register" id="registerOUTPUT" style="display:none">
支出金額：<input type="text" class="big_text"  id="registerval2" />
支出原因：<input type="text" class="big_text" id="registerOUTPUTNote"  />
<input type="button" class="big_button"  value="支出" onClick="registerIO(2)"  />

<input type="button" class="big_button"  value="取消" onClick="$('.register').hide('fast');"  />
</div>
<div class="register" id="registerOUT" style="display:none">
提領金額：<input type="text" class="big_text"  id="registerval0" />
<input type="button" class="big_button"  value="提領" onClick="registerIO(0)"  />
<input type="button" class="big_button"  value="取消" onClick="$('.register').hide('fast');"  />
</div>
<div class="register" id="registerIN" style="display:none">
置入金額：<input type="text" class="big_text"  id="registerval1"/>
<input type="button" class="big_button"  value="置入" onClick="registerIO(1)"  />
<input type="button" class="big_button"  value="取消" onClick="$('.register').hide('fast');"  />
</div>
<div id="report"> </div>


</div>
<style>
#hintBox{
	position:absolute;
	z-index:4;
	
	
	
}
</style>


<script type="application/javascript">

var nowfunction;
function functionSwitch()
{
        
    switch(nowfunction)
        {
                
            case 1 :  getDayReport(); break;
            case 2 :  getEachDayReport(); break;
            case 3 :  getMonReport(); break;    
            case 4 :  getMonReportAll(1); break;    
            case 5 :  getMonReportByDay(); break;    
            case 6 :  getMonReportByMonthCheck(); break;
            case 7 :  getDetailReport(0); break;
            case 8 :  getDayGoodthing(); break;    
            case 9 :  getBest30Report(); break;  
            case 10 :  getEInvoice(); break;  
            default :    getMonReport() ;
                
        }
    
    
    
}
    
    
    
    
function showDate(day,id)
{
	$('#hintBox').detach();
	$('#'+id).append('<div id="hintBox" style=" color:blue">('+day+')</div>');
		
	
}

function getWinList()    
{
    $.post('/accounting/get_win_list',{year:$('#year').val(),month:$('#mon').val()},function(data){
        
    
        if(data.result==true)
            {
             $('#invoiceContent').html(
			'<table id="invoiceTable" border="1">'+
				'<tr>'+
					'<td>發票開立時間</td>'+
					'<td>發票號碼</td>'+
					'<td>訂單編號</td>'+
                    '<td>統一編號</td>'+
					'<td>開立金額</td>'+
					'<td>明細</td>'+
                    '<td>中獎金額</td>'+
                    '<td>寄發狀況</td>'+
                    '<td>情況註記</td>'+
					
				'</tr>'+
			'</table>')
			for(key in data.winlist.data)
			{
                bgcolor = 'white';
                btn = '';
                
				   if(data.winlist.data[key].sending=='0000-00-00 00:00:00') 
                    {
                     	 btn+= '<input type="button" class="big_button" value="點此註記寄發給客戶" onclick="sendWinInvoice(\''+data.winlist.data[key].InvoiceNumber+'\')">';
                    }
                    else 
                    {

                        btn=data.winlist.data[key].sending;

                    }
                   
                
    
                
                if(data.winlist.data[key].invoiceData.BuyerIdentifier=='0000000000')data.winlist.data[key].invoiceData.BuyerIdentifier='';
				$('#invoiceTable').append(
				'<tr style=" background-color:'+bgcolor+'">'+
					'<td>'+data.winlist.data[key].invoiceData.InoviceDateTime+' </td>'+
					'<td>'+data.winlist.data[key].invoiceData.InvoiceNumber+' </td>'+
					'<td>'+data.winlist.data[key].invoiceData.orderNum+' </td>'+
                    '<td>'+data.winlist.data[key].invoiceData.BuyerIdentifier+' </td>'+
					'<td>'+data.winlist.data[key].invoiceData.total+' </td>'+
					'<td><input type="button" class="big_button" value="正本" onclick="showInvoiceDetail(\''+data.winlist.data[key].InvoiceNumber+'\',0)"><input type="button" class="big_button" value="副本" onclick="showInvoiceDetail(\''+data.winlist.data[key].InvoiceNumber+'\',1)"></td>'+
                    '<td>'+data.winlist.data[key].amount+' </td>'+
                    '<td>'+btn+'</td>'+
                    '<td><textarea onchange="sendWinInvoiceMemo(\''+data.winlist.data[key].InvoiceNumber+'\',this.value)" style="width:300px;">'+data.winlist.data[key].memo+'</textarea></td>'+
				
				'</tr>')
				
			}
                
                
            }
        else{
            
                content='<h1>上傳中獎清冊解密檔案</h1>';
                content+='<h3>您尚未上傳中獎清冊檔案</h3>';
                content+='<h3>請通知管理員下載清冊後，解密上傳</h3>';
                 content += '<form id="uploadForm" method="post" enctype="multipart/form-data" action="/accounting/winlist_send"> '
		          content+='<input type="file" name="file"   >';
                 content+='<input type="hidden" name="year" value="'+$('#year').val()+'"   >';
                content+='<input type="hidden" name="month" value="'+$('#mon').val()+'"   >';
                content+='</form>'
	
		openPopUpBox(content,600,300,'invoiceSetSend');
            
            
            
        }
        
        
        
    },'json')
    
    
    
    
}
    
function sendWinInvoiceMemo(InvoiceNumber,memo)
    {
          $.post('/accounting/send_win_memo/',{InvoiceNumber:InvoiceNumber,memo:memo},function(data)
              {
            if(data.result==true)
                {
                    
                   // getWinList();
                    
                    
                }
                
            
        },'json')
        
        
    }

function sendWinInvoice(InvoiceNumber)    
    {
        $.post('/accounting/send_win_invoice/',{InvoiceNumber:InvoiceNumber},function(data)
              {
            
            if(data.result==true)
                {
                    
                    getWinList();
                    
                    
                }
                
            
        },'json')
        
        
    }
    
    
function getEInvoice()
    {
        
        nowfunction = 10;
        content ="<h1>發票控制面板</h1>";
        content+='<input type="button" class="big_button" value="查看中獎發票" onclick="getWinList()">';
        content+="<div class='divider'></div><div id='invoiceDep'></div>";
	    content+='<div class="divider" style="clear:both"></div></div><div id="invoiceContent"></div>';
	

         $('#report').html(content)
        
        $.post('/accounting/get_einvoice_inf',{'mon':$('#mon').val(), 'year':$('#year').val()},function(data)
            {
                if(data.result==true)
                    {
                        
                        for(key in data.dep)
                            {
                                
                                   
                                   //content+='<h2><input type="button" onclick="deleteInvoiceNum()"class="big_button" value="刪除發票號碼"></h2>';

                                 
                                   content='<div style="border:solid;float:left; margin-left:5px" id="dep_'+data.dep[key].depID+'">'+
                                       '<h1>'+data.dep[key].name+'</h1>'+
                                       '選擇上方月份<input type="button" class="big_button" value="查看發票明細" onclick="getInvoiceDetail('+data.dep[key].depID+')">'+
                                       '<input type="button" class="big_button" value="匯入發票字軌" id="inputTrackBtn_'+data.dep[key].depID+'"  onclick="invoiceSet('+data.dep[key].depID+',\''+data.dep[key].name+'\')">'+
                                       
                                       '</div>';
                                   $('#invoiceDep').append(content);
                                 
                                
                              
                                
                                if(data.dep[key].invoice[0]!==undefined)
                                {
                                     
                                     content='<input type="button" class="big_button" value="直接開立發票" onclick="signInvoice('+data.dep[key].depID+',\''+data.dep[key].name+'\','+data.dep[key].timeChanger+',\''+data.dep[key].invoice[0].updateTime+'\')">';
                                
                                    content+='<h2>發票字軌：<span id="invoiceWd_'+data.dep[key].depID+'"></span>　'+data.dep[key].invoice[0]['YearMonth']+'</h2>'	;
                                    content+='<h2>發票起訖號碼：：<input type="text" class="medium_text" id="invoiceSt_'+data.dep[key].depID+'" readonly="readonly">~<input type="text" class="medium_text" id="invoiceEnd_'+data.dep[key].depID+'" readonly="readonly"></h2>';
                                   content+='<h2>下一張發票號碼：<input type="text" class="medium_text" id="invoiceNext_'+data.dep[key].depID+'"></span><input type="button" onclick="changeNextInvoice('+data.dep[key].invoice[0]['id']+')" class="big_button" value="確定修改"></h2>';
                                     ;
                                        $('#dep_'+data.dep[key].depID).append(content);
                                    
                                    
                                       $('#invoiceWd_'+data.dep[key].depID).html(data.dep[key].invoice[0]['InvoiceTrack']);	
                                       $('#invoiceSt_'+data.dep[key].depID).val(data.dep[key].invoice[0]['InvoiceBeginNo']);
                                       $('#invoiceEnd_'+data.dep[key].depID).val(data.dep[key].invoice[0]['InvoiceEndNo'])	;
                                       $('#invoiceNext_'+data.dep[key].depID).val(data.dep[key].invoice[0]['InvoiceNext'])	;
                                
                                    for(var ikey in data.dep[key].invoice )
                                        {
                                            if(ikey==0) continue;
                                            $('#dep_'+data.dep[key].depID).append('已匯入'+data.dep[key].invoice[ikey]['YearMonth']+' '+data.dep[key].invoice[ikey]['InvoiceTrack']+' '+data.dep[key].invoice[ikey]['InvoiceBeginNo']+'~'+data.dep[key].invoice[ikey]['InvoiceEndNo']+'<br/>');
                                            
                                            
                                            
                                        }

                                }
                              else
                              {
                                  $('#inputTrackBtn_'+data.dep[key].depID).show();


                              }
                                
                            }
                    
                        
                        
                    }
            
            
        },'json')
        
        
        
    }

               
 function invoiceSet(id,name)
	{
		content =' <h1>部門名稱：'+name+'</h1>';
        content += '<h1>請匯入發票字軌檔</h1>';
        content += '<form id="uploadForm" method="post" enctype="multipart/form-data" action="/accounting/invoice_set"> '
		content+='<input type="file" name="file"   >';
      content+='<input type="hidden" name="depID" value="'+id+'"   >';
        content+='</form>'
	
		openPopUpBox(content,600,300,'invoiceSetSend');
		
	}

 
	function invoiceSetSend()
	{
        $('#uploadForm').submit();
       
        
        
        
	}              
               
               
               
 
function deleteInvoiceNum()
{
	$.post('accounting/invoice_num_delete',{},function(data)
	{
		if(data.result==true)
		{
			showInvoice();
			alert('清除完成')	
			
		}
		
	},'json')
	
}
function changeNextInvoice()
{
	 var rex = new RegExp("[0-9]");
	
	if(
		rex.exec($('#invoiceNext').val())!= null &&
		$('#invoiceNext').val().length==8 &
		parseInt($('#invoiceNext').val())>= parseInt($('#invoiceSt').val()) &&
	 	parseInt($('#invoiceNext').val())<= parseInt($('#invoiceEnd').val()))
	{
		/*
		$.post('accounting/invoice_set',{'wd':$('#invoiceWd').html(),'st':$('#invoiceSt').val(),'end':$('#invoiceEnd').val(),'next':$('#invoiceNext').val()},function(data)
		{
			if(data.result)
			{
				
				alert('設定完成');
				 showInvoice();
			}
		
		
		},'json')
		*/
        alert('功能未開放')
	}
	else
	{
		
		alert('請輸入正確的發票號碼')
	}
}

function getInvoiceDetail(depID)
{
	
	$.post('/accounting/get_invoice_detail',{year:$('#year').val(),month:$('#mon').val(),depID:depID},function(data)
	{
		
	
		if(data.result==true)
		{
			$('#invoiceContent').html(
			'<table id="invoiceTable" border="1">'+
				'<tr>'+
					'<td>發票開立時間</td>'+
					'<td>發票號碼</td>'+
					'<td>訂單編號</td>'+
                    '<td>統一編號</td>'+
					'<td>開立金額</td>'+
					'<td>明細</td>'+
					'<td>作廢</td>'+
				'</tr>'+
			'</table>')
			for(key in data.invoiceDetail)
			{
                bgcolor = 'white';
                btn = '';
                
				
                if(data.invoiceDetail[key].void==0) 
                    {
                     
                        if(accountLevel>=100)
                            {
					 btn+= '<input type="button" class="big_button" value="註銷" onclick="voidInvoice(\''+data.invoiceDetail[key].InvoiceNumber+'\','+depID+')">';
					 bgcolor = 'gray';
                            }
                        if(data.invoiceDetail[key].invalid==0) 
                    {
                        btn+= '<input class="big_button" type="button" value="作廢" onclick="invalidInvoice(\''+data.invoiceDetail[key].InvoiceNumber+'\','+depID+')">';
                        bgcolor = 'white';
                    }
                    else 
                    {

                        btn='已作廢';

                    }
                    }
                
                
                    else{
                        btn='已註銷';
                        
                        
                    }
                
				
                
                if(data.invoiceDetail[key].BuyerIdentifier=='0000000000')data.invoiceDetail[key].BuyerIdentifier='';
				$('#invoiceTable').append(
				'<tr style=" background-color:'+bgcolor+'">'+
					'<td>'+data.invoiceDetail[key].InoviceDateTime+' </td>'+
					'<td>'+data.invoiceDetail[key].InvoiceNumber+' </td>'+
					'<td>'+data.invoiceDetail[key].orderNum+' </td>'+
                    '<td>'+data.invoiceDetail[key].BuyerIdentifier+' </td>'+
					'<td>'+data.invoiceDetail[key].total+' </td>'+
					'<td><input type="button" class="big_button" value="正本" onclick="showInvoiceDetail(\''+data.invoiceDetail[key].InvoiceNumber+'\',0)"><input type="button" class="big_button" value="副本" onclick="showInvoiceDetail(\''+data.invoiceDetail[key].InvoiceNumber+'\',1)"></td>'+
					'<td>'+btn+'</td>'+
				'</tr>')
				
			}
		}
		
	},'json')	
	
	
}
function showInvoiceDetail(InvoiceNumber,type)
{
	$.post('/accounting/show_invoice_detail',{InvoiceNumber:InvoiceNumber},function(data)
	{
		if(data.result==true)
		{
			content='<iframe id="invoiceIframe" src="/accounting/show_invoice/'+InvoiceNumber+'/'+data.code+'/html/'+type+'" style="width:300px;height:400px"></iframe>';
            openPopUpBox(content,320,450,'goPrint');
	       $('#popUpBoxEnter').val('列印');
			
		}
		
	},'json')
	
	
}

function goPrint()
    {
        
       window.open($('#invoiceIframe').attr('src'), '發票列印', config='height=500,width=500');
        
        
    }


function signInvoice(depID,name,timeChanger,time)
{
	content = '<form id="invoice_form">抬頭：<input type="text" class="big_text" name="title"  placeholder="無則空白">';
    content += '<input type="hidden" name="handIN" value="1">';
     content += '<input type="hidden" name="api" value="0">';
	content += ' 統一編號：<input type="text" class="medium_text" name="invoiceCode" placeholder="無則空白" ><br/>';
    content += ' Email：<input type="text" class="big_text" name="email" placeholder="無則空白" ><br/>'
    content += ' 載具：<select name="CarrierType">'+
                '<option value="">無</option>'+
                '<option value="3J0002">手機條碼</option>'+
                '<option value="CQ0001">自然人憑證</option>'+
                '</select><input type="text" class="big_text" name="CarrierId1" placeholder="載具條碼無則空白" >';
    content += ' 捐贈代碼：<input type="text" class="medium_text" name="NPOBAN"  placeholder="無則空白" ><br/>';
    content +=  '店家名稱：<input type="text" class="medium_text" name="shopName" value="'+name+'" >';
    content += '<input type="hidden" class="medium_text" name="depID" value="'+depID+'" >'
    content +=  '訂單編號：<input type="text" class="medium_text" name="orderNum" value="" >';
    if(timeChanger==1)
    {
        var time1 = dateFormat(new Date(),"yyyy-mm-dd HH:mm:ss");
        
        content += '<br/>發票開立時間；<input type="text" class="big_text" name="invoiceTime" id="invoiceTime" value="'+time1+'" >';
        
    }
    else         content += '<input type="hidden"  name="invoiceTime" id="invoiceTime" value="" >';

    
    
	content += '<table border="1" width="590">';
				content +='<tr>';
					content +='<td>項目</td>';
					content +='<td>單價</td>';
					content +='<td>數量</td>';
					content +='<td>小計</td>';
				content +='</tr>';
	
		for(i=0;i<10;i++)
		{
				content +='<tr>';
					content +='<td><input type="text" class="medium_text" id="item_'+i+'" name="item[]"></td>';
					content +='<td><input type="text" class="medium_text" id="uniPrice_'+i+'" name="uniPrice[]" onchange="caculateSub()"></td>';
					content +='<td><input type="text" class="medium_text" id="num_'+i+'" name="num[]"  onchange="caculateSub()"></td>';
					content +='<td id="subtotal_'+i+'"></td>';
				content +='</tr>';	
			
		}
	       content +='<tr>';
					content +='<td>備註：</td>';
					content +='<td colspan="3"><input type="text" class="big_text" id="invoice_comment" name="comment"  ></td>'
					
				content +='</tr>';
    
			content +='<tr>';
					content +='<td colspan="2"></td>';
					content +='<td>總金額</td>'
					content +='<td id="invoiceTotal"></td>';
				content +='</tr>';
	content	+='</table></form>'
	openPopUpBox(content,600,600,'signInvoiceSend');
	
 
    
    
    var dates = $( "#invoiceTime").datetimepicker({
							dateFormat: 'yy-mm-dd ' ,
        
                            timeFormat: "HH:mm:ss",
                            minDate : time
							
						});		  	
	
}

function caculateSub()
{
	total = 0 ;
	for(i=0;i<10;i++)
	{
		uniPrice = parseInt($('#uniPrice_'+i).val());
		num = parseInt($('#num_'+i).val());
		subTotal = uniPrice * num ;
		if(isNaN(subTotal))subTotal = 0 ;
		total += subTotal; 
		$('#subtotal_'+i).html(subTotal);
	}
	$('#invoiceTotal').html(total)
}



function signInvoiceSend()
{
   
		$.ajax({
		   type: "POST",
		   dataType:"json",
		   url: "/accounting/invoice_generate",
		   data: $("#invoice_form").serialize(),
		   success: function(data){
			 
			
			   if(data.result==true)
			   {  
					
					
					//$('#invoiceFrame').html('<iframe width:1000; src="/accounting/invoice_print_out/'+data.invoiceID+'"></iframe>');
  
					closePopUpBox();
		              alert('發票已開立');
				getEInvoice();
                   
			   }
               else{
                   
                   alert('發票字軌錯誤');
               }
			  
		   }
		 });
	
	
}
function invalidInvoice(invoiceNumber,depID)
{
    var sStr = prompt("請輸入作廢原因");
    if(sStr=='')
    {
        alert('你必須輸入原因');
        return;
    }
	if(confirm('你確定要作廢這張發票'))
	{
		$.post('accounting/invalid_invoice',{invoiceNumber:invoiceNumber,CancelReason:sStr},function(data)
		{
			if(data.result==true)
			{
				 getInvoiceDetail(depID);	
				
			}
			 
			
			
		},'json')
		
		
	}	
	
	
	
	
}

function voidInvoice(invoiceNumber,depID)
{
    var sStr = prompt("請輸入註銷原因");
    if(sStr=='')
    {
        alert('你必須輸入原因');
        return;
    }
	if(confirm('你確定要註銷這張發票'))
	{
		$.post('accounting/void_invoice',{invoiceNumber:invoiceNumber,VoidReason:sStr},function(data)
		{
			if(data.result==true)
			{
				 getInvoiceDetail(depID);	
				
			}
			
			
			
		},'json')
		
		
	}	
	
	
	
	
}

function showInvoice()
{
	
	
}
   
    

// JavaScript Document
function getBest30Report()
{
	nowfunction = 9;	
	$.ajax({
	   type: "post",
	   dataType: "html",
	   data:$("#reportForm").serialize(),
	   url: "/race/best30_report",
	   success: function(data){

		   /*
		   $('#reportForm').attr('action','/race/best30_report');
		   $('#shopID').unbind('change');
		   $('#shopID').bind('change',function(){
			getBest30Report();   
			   
			  })
		   */
			$('#report').html(data);
			$('#print').show();
	   }
	 });		



}
function getDayReport()
{
    nowfunction = 1;
	$.ajax({
	   type: "post",
	   dataType: "html",
	   data:$("#reportForm").serialize(),
	   url: "/accounting/get_day_report",
	   success: function(data){

		   $('#reportForm').attr('action','/accounting/get_day_report');
		   $('#shopID').unbind('change');
		   $('#shopID').bind('change',function(){
			getDayReport();   
			   
			  })
		   
			$('#report').html(data);
			$('#print').show();
	   }
	 });	
	
	
	
	
}
	
function getDayReport()
{
	$.ajax({
	   type: "post",
	   dataType: "html",
	   data:$("#reportForm").serialize(),
	   url: "/accounting/get_day_report",
	   success: function(data){

		   $('#reportForm').attr('action','/accounting/get_day_report');
		   $('#shopID').unbind('change');
		   $('#shopID').bind('change',function(){
			getDayReport();   
			   
			  })
		   
			$('#report').html(data);
			$('#print').show();
	   }
	 });	
	
	
	
	
}
function getEachDayReport()
{
    nowfunction = 2;
	$.ajax({
	   type: "post",
	   dataType: "html",
	   data:$("#reportForm").serialize(),
	   url: "/accounting/get_each_day_report",
	   success: function(data){

		   $('#reportForm').attr('action','/accounting/get_each_day_report');
		   $('#shopID').unbind('change');
		 
		   
			$('#report').html(data);
			$('#print').show();
	   }
	 });	
	
	
	
	
}

var monReportTotal = 0;
var monReportMonth = 0 ;
var monProfitTotal = 0;

function getMonReportByMonthData(mon,year,toMon,toYear)
{
		$.post( "/accounting/get_mon_report_data",{mon:mon,year:year,shopID:$('#shopID').val()},function(data){
			
			if(data.result==true)
			{
				
				if(mon%2==0)var content = '<tr>';
				else var content = '<tr style="background-color:#F0F0F6" >';
				
				  content+='<td style=" background-color:yellow">'+year+'</td>';
				  content+='<td style=" background-color:yellow">'+mon+'</td>';
				  var  order= new Array(6,2,1,3,4,7,8,9,5,10,11,12);
				  
				for(i in order)
				{
						///content+='<td>'+data.shopItem[i]["count"]+'</td>';
				
						flag =0;
					for(key in data.shopItem[order[i]])
					{
						if(flag%2==1)content+='<td>'+data.shopItem[order[i]][key]+'</td>';
						 	flag++;
						
					}
				}
				content+='<td>'+data.total+'</td>';
				content+='<td>'+data.monVerify+'</td>';
			    content+='</tr>';
				$('#reportByMonth').append(content);
				monReportTotal+=data.total;
				monProfitTotal+=data.monVerify;
                if(data.total!=0) monReportMonth++;
				mon++;
				if(mon>12) 
				{
					year++;
					mon = 1;
				}
				if(year<toYear || (year==toYear&&mon<=toMon))getMonReportByMonthData(mon,year,toMon,toYear);
                else 
                    {
                        avg = Math.round(monReportTotal/monReportMonth);
						profitAvg = Math.round(monProfitTotal/monReportMonth);	
						var passTD =(order.length+2);
                        content='<tr><td colspan="'+passTD+'"></td><td>總額：'+monReportTotal+'<br/>平均：'+avg+'</td><td>毛利總額：'+monProfitTotal+'<br/>毛利平均：'+profitAvg+'</td></tr>';
                        
                        	$('#reportByMonth').append(content);
                        
                        
                    }
				
				
			}
			
			
		},'json')
	
	
	
	
}

    
    
function   getMonReportAll()
    {
        
        nowfunction = 4;
         $('#report').html('<table id="reportByMonth" border="1" width="1200px;" style="font-size:14pt; text-align:center"></table>');
			var shopItem = new Array('店號','店名','總營業額','去年業績','成長率','會員費用','場地消費','遊戲販售','遊戲租賃','餐飲','魔獸世界','魔法風雲會','其他','二手商品','商城進出','退貨商品','總營業額','毛利');
			
			$('#reportByMonth').append('<tr id="shopItem"></tr>')
			for(key in shopItem)$('#shopItem').append('<td>'+shopItem[key]+'</td>')
            
            getMonReportAllData(1);
        
        
    }
    
    
function getMonReportAllData(shopID)
{
    var maxID = $('#maxShopID').val();
    var mon = $('#mon').val();
    var year = $('#year').val();
   
			
   
    
		$.post( "/accounting/get_mon_report_data",{mon:mon,year:year,shopID:shopID},
               function(data)
               {			
			if(data.result==true)
			{
				if(data.exist==true)
                    {
				if(mon%2==0)var content = '<tr style="text-align:right">';
				else var content = '<tr style="background-color:#F0F0F6;text-align:right" >';
				
				  content+='<td style=" background-color:yellow">'+shopID+'</td>';
				  content+='<td style=" background-color:yellow">'+data['shopInf']['name']+'</td>';
                content+='<td>'+data.total.toLocaleString('en-US')+'</td>';
                content+='<td>'+data.lastyear.total.toLocaleString('en-US')+'</td>';
                content+='<td>'+Math.round((data.total-(data.lastyear.total))*100/data.lastyear.total )+'%</td>';
				  var  order= new Array(6,2,1,3,4,7,8,9,5,10,11);
				  
				for(i in order)
				{
						///content+='<td>'+data.shopItem[i]["count"]+'</td>';
				
						flag =0;
					for(key in data.shopItem[order[i]])
					{
						if(flag%2==1)content+='<td>'+data.shopItem[order[i]][key].toLocaleString('en-US')+'</td>';
						 	flag++;
						
					}
				}
				content+='<td>'+data.total.toLocaleString('en-US')+'</td>';
				content+='<td>'+data.monVerify.toLocaleString('en-US')+'</td>';
          
			    content+='</tr>';
				$('#reportByMonth').append(content);
                    }
			
                shopID++;
				if(shopID<maxID)getMonReportAllData(shopID);
				
				
            }
			
			
		},'json')
	
	
	
	
}


function getMonReportByMonthCheck()
{
    nowfunction = 6;
		content='<h2>請輸入起訖年月</h2>'+
		'從<select id ="fromYear">'+$('#year').html()+'</select>'+
		'<select id ="fromMon">'+$('#mon').html()+'</select>~~'+
		'到<select id ="toYear">'+$('#year').html()+'</select>'+
		'<select id ="toMon">'+$('#mon').html()+'</select>';
		openPopUpBox(content,300,280,'getMonReportByMonth');	
	    var d=new Date();
	$('#toMon').val(d.getMonth());
	
}


    
    
    


function getMonReportByMonth()
{
			$('#report').html('<h1>'+$("#shopID").find(":selected").text()+' '+$('#fromYear').val()+'-'+$('#fromMon').val()+'~'+$('#toYear').val()+'-'+$('#toMon').val()+'</h1><table id="reportByMonth" border="1" width="1200px;" style="font-size:14pt; text-align:center"></table>');
			var shopItem = new Array('年','月','會員費用','場地消費','遊戲販售','遊戲租賃','餐飲','魔獸世界','魔法風雲會','課程收入','其他','商城進出','二手商品','退貨商品','總營業額','毛利');
			
			$('#reportByMonth').append('<tr id="shopItem"></tr>')
			for(key in shopItem)$('#shopItem').append('<td>'+shopItem[key]+'</td>')
			monReportTotal = 0;
            monReportMonth = 0 ;
		
		getMonReportByMonthData($('#fromMon').val(),$('#fromYear').val(),$('#toMon').val(),$('#toYear').val())
	closePopUpBox();
}





function getMonReport()
{
    
    nowfunction = 3;
		$.ajax({
	   type: "post",
	   dataType: "html",
	   data:$("#reportForm").serialize(),
	   url: "/accounting/get_mon_report",
	   success: function(data){
		    $('#reportForm').attr('action','/accounting/get_mon_report');
		   $('#shopID').unbind('change');
		   $('#shopID').bind('change',function(){
			getMonReport();   
			   
			  })		   
			$('#report').html(data);
			$('#print').show();
	   }
	 });
	
	
}
function getMonReportByDay()
{
    nowfunction = 5;
		$.ajax({
	   type: "post",
	   dataType: "html",
	   data:$("#reportForm").serialize(),
	   url: "/accounting/get_mon_report_by_day",
	   success: function(data){
		    $('#reportForm').attr('action','/accounting/get_mon_report_by_day');
		   $('#shopID').unbind('change');
		   $('#shopID').bind('change',function(){
			getMonReportByDay();   
			   
			  })		   
			$('#report').html(data);
			$('#print').show();
	   }
	 });
	
	
}

function getDetailReport(excel)
{
    nowfunction = 7;
	if(excel==1) 
	{
		content = '<div id="create_excel"><h1>檔案產生中，請稍候..</h1><img src="/images/ajax-loader.gif"/></div>';
		openPopUpBox(content,300,280,'closePopUpBox');	
		getDetailDataShow(excel);
	}
	else
	{
		
		$('#report').html('<h1>正在暫存期初庫存，首次讀取需要耗費較長時間，請耐心等候..</h1><img src="/images/ajax-loader.gif"/>');
		$.post('/accounting/stock_preload',{year:$('#year').val(),mon:$('#mon').val()-1,shopID:$('#shopID').val()},function(data){
			if(data.result==true)
			{
				$('#report').html('<h1>正在暫存期末庫存，首次讀取需要耗費較長時間，請耐心等候..</h1><img src="/images/ajax-loader.gif"/>');

				$.post('/accounting/stock_preload',{year:$('#year').val(),mon:$('#mon').val(),shopID:$('#shopID').val()},function(data){
				if(data.result==true)
				{
					$('#report').html('<h1>頃印資料中，首次讀取需要耗費較長時間，請耐心等候..</h1><img src="/images/ajax-loader.gif"/>');
					getDetailDataShow(excel);
				}
				},'json');
			}
		
		},'json')
		
		
		
	}
	
}

function getDetailDataShow(excel)
{
	$.ajax({
	   type: "post",
	   dataType: "html",
	   data:$("#reportForm").serialize()+'&excel='+excel,
	   url: "/accounting/get_detail_report",
	   success: function(data){
		   if(excel==1)
		   {
			 $('#create_excel').html(data);
			}
		   else
		   {
		    $('#reportForm').attr('action','/accounting/get_detail_report');
		   $('#shopID').unbind('change');
		   $('#shopID').bind('change',function(){
			getDetailReport(0);   
			   
			  })		   
			$('#report').html(data);
			$('#print').show();
		   }
	   }
	 });	
}


   function getDayGoodthing()
{
    nowfunction = 8;
	$.ajax({
	   type: "post",
	   dataType: "html",
	   data:$("#reportForm").serialize(),
	   url: "/accounting/goodthing",
	   success: function(data){

		   $('#reportForm').attr('action','/accounting/goodthing');
		   $('#shopID').unbind('change');
		   $('#shopID').bind('change',function(){
			getDayGoodthing();   
			   
			  })
		   
			$('#report').html(data);
			
	   }
	 });	
	
	
	
	
} 
function getOwnProduct()
{
	$.ajax({
	   type: "post",
	   dataType: "html",
	   data:$("#reportForm").serialize(),
	   url: "/accounting/get_own_product",
	   success: function(data){

		   $('#reportForm').attr('action','/accounting/get_own_product');
		   $('#shopID').unbind('change');
		   $('#shopID').bind('change',function(){
			getOwnProduct();   
			   
			  })
		   
			$('#report').html(data);
			
	   }
	 });	
	
	
	
	
} 
   


$(document).ready(function(){
		var today = new Date()
		$('#mday').val(today.getDate());
		$('#mon').val(today.getMonth()+1);
        $('#year').val(today.getFullYear());
   
    <?php if($this->uri->segment(3)=='invoice'):?>
    getEInvoice();
    <?php else:?>
		getDayReport();
    <?php endif;?>
});
</script>

