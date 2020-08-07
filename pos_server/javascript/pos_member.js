// JavaScript Document

function getEmailAudit(offset)
{
	$.post('/member/get_sending_email_list',{offset:offset,num:10},function(data){
		
			if(data.result==true)
			{
				
				if(data.result==true)
			{
				if(offset==0)
				{
					
					content='<table border="1" id="emailTable">';
					content+='<tr><td>店名</td><td>條件</td><td>時間</td><td>主旨</td><td>狀態</td><td>查看</td><td>動作</td><td>寄發結果</td></tr>';
					content+='</table>'
					content+='<input type="button" class="big_button" value="查看更多" id="moreEmail">';
					$('.product').html(content);
				
					
					
				}
				for(key in data.emailList)
				{
					
					var content='';
					content+='<tr><td>'+data.emailList[key].name+'</td><td>'+data.emailList[key].con+'</td>'+
							  '<td>'+data.emailList[key].time+'</td>'+
							  '<td>'+data.emailList[key].subject+'</td>'+
							  '<td>'+data.emailList[key].status+'</td>'+
							  '<td><input type="button"  class="big_button"  value="查看" onclick=" window.open(\'/member/get_email_preview/'+data.emailList[key].id+'\', \'_blank\')"></td>';
							  ;
					if(data.emailList[key].status=='待審核')	content+='<td><input type="button" class="big_button" value="審核通過" onclick=" emailAuditSend('+data.emailList[key].id+')"></td>';
					else content+='<td></td>';
					
					if(data.emailList[key].status=='發送中100%' )	content+='<td><input type="button" class="big_button" value="發送寄送結果" onclick=" emailResult('+data.emailList[key].id+')"></td>';
					else content+='<td></td>';
					
							  
					content+='</tr>';
					$('#emailTable').append(content);
					
				}
			
				$('#moreEmail').unbind('click');
				$('#moreEmail').bind('click',function(){getEmailAudit(offset+10)})
			
			}
		
				
				
			}
		
		
		
		},'json')
	
	
}	

function emailResult(id)
{
	$.post('/member/email_result_post',{id:id},function(data)
	{
		if(data.result==true)
		{
				
			alert('結果已經寄到'+data.email+'信箱');
			
		}
		
		
	},'json')
	
	
}

function emailAuditSend(id)
{
	getEmailAudit(0);
	$.post('/member/email_audit_send',{id:id},function(data)
	{
		if(data.result==true)
		{
            if(confirm('是否先發電商?'))startSending(id,'EC');
            else startSending(id);
			
		}
		
		
	},'json')
	
	
}


function startSending(id,type)
{
       if(type=='EC')  url = '/member/get_ec_sending_num';
        else url = '/member/get_sending_num';
    
   
    $.post(url,{id:id},function(data)
          {
    
            if(data.result==true)
                {
                    
                    var content='<h1>正在設定發送...</h1>'+
                          '<span id="nowSend">0</span><span>/'+data.totalNum+'</span>'
                   openPopUpBox(content,600,300,'');
                     sendingProgress(id,0,data.totalNum,0,type);
                    
                }
        
        
        
    },'json')
    
    
    
    
}
function sendingProgress(id,offset,total,eID,type)
{
    
    var num = 100;
        if(type=='EC')  url = '/member/ec_email_send';
        else url = '/member/email_send';
    
        $.post(url,{id:id,offset:offset,num:num,eID:eID},function(data)
          {
        
            if(data.result==true)
                {
                    $('#nowSend').html(offset)
                    if(offset<total)
                        {
                            sendingProgress(id,offset+num,total,data.eID,type);
                        }
                    else 
                    {
                          $('#nowSend').html('發送完成請關閉視窗')
                        if(type =='EC')
                            {
                                
                                  $('#nowSend').append('<input type="button" class="big_button" value="同步發送會員箱" onclick="startSending('+id+')">');
                                
                            }
                      
                    }
                    
                }
        
        
        
    },'json')
    
    
    
}



  // JavaScript Document
   
   

  function tableCreater(set){
			  var tableName = set.tableName;	 
			  var isShopMember = set.isShopMember;	
			  var appendID = 'tr_'+set.tableName+'_'+set.index;	
			  var memberID = set.memberID;
			  if(set.index%2==0) bgColor='#EEE';
			  else bgColor="#FFF";
			  $('#'+tableName).append('<tr id="'+appendID+'"  style="background:'+bgColor+'"></tr>');
			  $('#'+appendID).append('<input type="hidden" name="m_'+set.memberID+'"  value="'+set.memberID+'"/>');
			  $('#'+appendID).append('<td>'+set.memberID+'</td>');
		  this.tableInput = function(id,value,width){
	  
					  var str ='<td><input type="text" name="'+id+'"  id="'+id+'" class="memberInput" value="'+value+'" style="width:'+width+'px"/></td>';
					  $('#'+appendID).append(str);
					   if(value==''||value=='undefined'||value=='null'||value==undefined||value==null||value=='0000-00-00') 
					  {
						 if(!isShopMember)$('#'+id).val('此為保密資料'); 
						 else $('#'+id).val('按此進行修改'); 
					  }
					   if(!isShopMember)
						  { 
						  	 $('#'+id).attr('readonly','readonly');				
						  }
						else
						  {
						  $('#'+id).bind('click',function(){
								   $('#'+id).select();
									  hasEdit(tableName.substr(12))
								  })	
						  }
					  
					  
		  }
		  this.tableSelect = function(id,value,option){
					 
					if(isShopMember){ 
					  var str ='<td><select name="'+id+'" id="'+id+'">'+
							  option+
							  '</select></td>';
					  $('#'+appendID).append(str);
					  $('#'+id).val(value);
					}
					else
					{
						$('#'+appendID).append('<td id="'+id+'"></td>');
						if(value==1) $('#'+id).html('男<input type="hidden" name="'+id+'" value="1" >');	
						else $('#'+id).html('女<input type="hidden" name="'+id+'" value="0" >');	
						
					}
					  
		  }
		  this.tableLevel = function(id,value){
				  var str ='<td id="'+id+'"></td>';
				  $('#'+appendID).append(str);
				  $('#'+id).html(value);
  
		  };
		  this.tableButton = function(id,value,con){
				  var str ='<td><input type="button" id="'+id+'"  value="'+value+'"/></td>';
				  $('#'+appendID).append(str);
				  $('#'+id).bind('click',function(){
									 levelEdit(memberID,con);
							  })		
  
			  }	
		 this.tableSaleButton = function(id,value,con){
				  var str ='<td><input type="button" id="'+id+'"  value="'+value+'"/></td>';
				  $('#'+appendID).append(str);
				  $('#'+id).bind('click',function(){
									 saleRecord(memberID);
							  })		
  
			  }		  								
   		 this.tableOptionButton = function(id,value,con){
				  var str ='<td><input type="button" id="'+id+'"  value="'+value+'"/></td>';
				  $('#'+appendID).append(str);
				  $('#'+id).bind('click',function(){
									 memberOption(memberID);
							  })		
  
			  }		  								
   
  }
  

  
  
  function findMember(type){
	  
	  if(type==1){
		  if(enter()==false) return;
	  }
	  var id = $('#search_memberID').val();
	  var name = $('#search_name').val();
	  var phone = $('#search_phone').val();	
  
  
	  $.post('/member/get_member',{memberID:id,name:name,phone:phone,remote:1},function(data){
  
			  memberTable()	
			  if(data.result==false) memberTable(false);
			  else memberTable(data.memberData);
			  if(data.remote==false) alert('無法與遠端連線，部分資料無法取得');
		  },'json')
	  $('#search_phone').val('') ;
	  $('#search_memberID').val('');
	  $('#search_name').val('') ;
  }
  
  function memberInfChk()
  {
	  $.post('/member/member_inf_chk',{memberID:$('#infChkMemberID').val(),phone:$('#infChkPhone').val()},function(data){
				if(data.result==true)
				{
					$('#chk_inf').hide();
					$('#levelEditDiv').show('fast');
					$('#popUpBoxEnter').show();
					$('#popUpBoxCancel').show();
					$('#levelEditIsShopMember').val(1);
				}
				else alert('資訊錯誤，請重新輸入');
		  },'json')
	  
	  
	  
  }
  
  
  
  function newMember()
  {
	  var content = '<h1>新增會員資料</h1>'+
				   '<form id="newMemberForm">'+
				   '<div class="membetInput">'+
					  '會員編號：<input type="text" id="newMemberID" name="memberID" class="big_text" />'+
					  ' 姓名：<input type="text" id="newName" name="name" class="big_text"/>'+
					'</div>'+
					'<div class="membetInput">'+
						'電子信箱：<input type="text" name="email" class="big_text"/>'+					
						' 電話：<input type="text" name="phone" class="big_text"/>'+
					'</div>'+
				   '<div class="membetInput">'+
					  '生日：<input type="text" id="newBirth" name="birthday" value="0000-00-00" class="big_text" " style="width:80px"/>'+
					  ' 性別：<select name="sex"><option value="1">男</option><option value="2">女</option></select>'+
					  '　會員等級：<select name="level">'+memberLevel+'</select>'+
				  '</div>'+
				   '<div class="membetInput">'+
					   '地址：<input type="text" name="address" class="big_text" style="width:490px"/>'+
				   '</div>'+
				 	'<div class="membetInput">'+
					   '備註：<textarea name="note"style="width:490px"></textarea>'+
				   '</div>'+
				   '</form>'
				   ;
	  
	  
	  openPopUpBox(content,600,300,'sendNewMember');
	  $('#newMemberID').bind('keyup',function(){
				if(enter())$('#newName').focus();
	  })
	  
	  $('#newBirth').bind('click',function(){
									  if($('#newBirth').val()=='0000-00-00')	$('#newBirth').select();
	  })
	  
  }
  
  
  
  
  function sendNewMember()
  {
	   	if($('#newMemberID').val()==''||$('#newName').val()==''){
		alert('資料不完全，請重新輸入');
		return;
		}

	  $.ajax({
		 type: "POST",
		 dataType: "json",
		 url: "/member/new_member",
		 data: $("#newMemberForm").serialize(),
		 success: function(data){

			 if(data.result==0)
			 {
				  alert('會員編號重複囉');
				  
			 }
			 else
			{
				 if(data.result==1)
				 {
					$('#msgColumn').html("資料已儲存");
					
				 }
				 else $('#msgColumn').html("遠端連線失敗，稍後將上傳");
				 $('#search_memberID').val($('#newMemberID').val());
				 findMember(2);
				   closePopUpBox()
			 }

		 }
	   });		  
	  
  }
  
  
  
  
  
  
  function findAllMember()
  {
	  
	  
	  $.post('/member/get_all_member',{},function(data){	
		  
			  if(data.result==false) alert('查無資料');
			  else memberTable(data.memberData);
		  },'json')		
	  
	  
  }
  
  
  
  function memberTable(data)
  {
  
	  clearSearch();
	  $('#memberData').html('');
	  $('#memberData').append('<div id="pageList" style="margin-top:10px;width:1024px;"></div><div style="clear:both">');
	   i = 0;j=0;tr=0;
	   var num = 20;
	  for(key in data)
	  {
		  i++;
		  var str='';
		  if((i%num)==1)
		  {
			  j++;
			  $('#memberData').append(
				  '<div id="tablePage_'+j+'" class="tablePage" >'+
				  '<form name="memberDataForm_'+j+'" id="memberDataForm_'+j+'">'+
				  '<table id="memberTable_'+j+'" class="memberTable" width="1200px" border="1" style=" text-align:center">'+
				  '<tr>'+
					'<td style="width:80px">會員編號</td>'+
					'<td style="width:80px">姓名</td>'+
					'<td>會員電話</td>'+
					'<td>會員信箱</td>'+
					'<td>會員生日</td>'+
					'<td>會員地址</td>'+
					'<td>性別</td>'+
					'<td>會員等級</td>'+
					'<td>到期日</td>'+
					'<td>續約次數</td>'+
					'<td>等級修改</td>'+
					'<td>備註</td>'+
					'<td>銷售記錄</td>'+
					'<td>操作</td>'+
				  '</tr>'+    
				  '</table>'+
				  '</form>'+
				  '</div>'
				  );
			  if(j>1)	$('#tablePage_'+j).hide();			
		  }
		  
		  var tabler = new tableCreater({
				  'tableName'    : 'memberTable_'+j,
				  'isShopMember' : data[key].isShopMember,
				  'memberID'     : data[key].memberID,
				  'index'        : tr++
			  
			  }) 
		  
		  tabler.tableInput('N_'+data[key].memberID,data[key].name,60);
		  tabler.tableInput('P_'+data[key].memberID,data[key].phone,120);
		  tabler.tableInput('E_'+data[key].memberID,data[key].email,120);
		  tabler.tableInput('B_'+data[key].memberID,data[key].birthday,80);
		 
		  		$('#B_'+data[key].memberID).datepicker({
					dateFormat: 'yy-mm-dd' ,
					changeMonth: true,
					changeYear: true,
					yearRange: '1930',
					monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月']
				});

		  
		  tabler.tableInput('A_'+data[key].memberID,data[key].address,250);
		  tabler.tableSelect('SEX_'+data[key].memberID,data[key].sex,'<option value="1">男</option><option value="2">女</option>');
  
		  tabler.tableLevel('L_'+data[key].memberID,data[key].levelName.replace(",","<br/>"));
		  tabler.tableLevel('D_'+data[key].memberID,data[key].dueTime.replace(",","<br/>"));
		  tabler.tableLevel('R_'+data[key].memberID,data[key].reNew.replace(",","<br/>"));
		
		  tabler.tableButton('btn_'+data[key].memberID,'修改等級',data[key]);
		  tabler.tableInput('NO_'+data[key].memberID,data[key].note,80);
		  tabler.tableSaleButton('salebtn_'+data[key].memberID,'銷售記錄',data[key]);
	    tabler.tableOptionButton('optionbtn_'+data[key].memberID,'操作',data[key]);
		   if((i%num)==0){
			   $('#tablePage_'+j).append('<input type="button" value="確認修改" class="big_button" onclick="memberEdit('+j+')" />');
			   $('#tablePage_'+j).append('<input type="button" value="取消" class="big_button" onclick="clearSearch()" />');
  
		   }		
	  }
			  
		   if((i%num)!=0){
			   $('#tablePage_'+j).append('<input type="button" value="確認修改" class="big_button" onclick="memberEdit('+j+')" />');
			   $('#tablePage_'+j).append('<input type="button" value="取消" class="big_button" onclick="clearSearch()" />');
  
		   }	
	   if (j>1) $('#pageList').append('<a style=" font-size:14pt; margin-right:10px;float:left; height:25px" onclick="lastPage()">上一頁</a>');
	   for(k=1;k<=j;k++)
	   {
		   pageEdit[k]=false;
		  $('#pageList').append('<a style=" font-size:14pt; margin-right:10px; float:left; height:25px" id="page_'+k+'" onclick="changePage('+k+')">'+k+'</a>');
	   }
		if (j>1) $('#pageList').append('<a style=" font-size:14pt; margin-right:10px;float:left; height:25px" onclick="nextPage()">下一頁</a>');
		  $('#page_1').css('font-size','18pt');	
		  changePage(1);
  }
  
  function changePage(page)
  {
	  $('.tablePage').hide();
	  $('#page_'+nowPage).css('font-size','14pt');	
	  $('#page_'+nowPage).css('color','#400');		
	  if(pageEdit[nowPage]){
			  $('#msgColumn').html('自動儲存中...');
		  memberEdit(nowPage);
	  }
	  nowPage = page;	
	  $('#page_'+page).css('font-size','18pt');	
	    $('#page_'+nowPage).css('color','red');		
	  $('#tablePage_'+page).show();		
	  
  }
  
  function lastPage()
  {
	  if(nowPage>1)changePage(nowPage-1);
  }
  
  function nextPage()
  {
	  changePage(nowPage+1);
  }
  
  
  function hasEdit(page)
  {
	  pageEdit[page] = true;
	  
  }
  
  
  function findMemberBirthPresent()
  {
	 $.post('/member/get_present',{},function(data)
	{
			 
		  $('#memberData').html('<iframe  src="'+data.url+'"  style="width:1000px;height:800px"></iframe>');
		
	},'json')  

	
	  
  }	
  function getSendingEmailList(offset)
  {
	   $.post('/member/get_sending_email_list',{offset:offset},function(data)
	{
			 
			if(data.result==true)
			{
				if(offset==0)
				{
					
					content='<table border="1" id="emailTable">';
					content+='<tr><td>收件對象</td><td>條件</td><td>時間</td><td>主旨</td><td>狀態</td><td>查看</td><td>動作</td></tr>';
					content+='</table>'
					content+='<input type="button" class="big_button" value="查看更多" id="moreEmail">';
					openPopUpBox(content,600,800,'closePopUpBox');	
					
					
				}
				for(key in data.emailList)
				{
					
					var content='';
					content+='<tr><td>本店會員</td><td>'+data.emailList[key].con+'</td>'+
							  '<td>'+data.emailList[key].time+'</td>'+
							  '<td>'+data.emailList[key].subject+'</td>'+
							  '<td>'+data.emailList[key].status+'</td>'+
							  '<td><input type="button"  class="big_button"  value="查看" onclick=" window.open(\'/member/get_email_preview/'+data.emailList[key].id+'\', \'_blank\')"></td>';
							  ;
					if(data.emailList[key].status=='待發送')	content+='<td><input type="button" class="big_button"  onclick="remoteEmailSend('+data.emailList[key].id+')" value="送出信件"></td>';
					else content+='<td></td>';
							  
					content+='</tr>';
					$('#emailTable').append(content);
					
				}
			
				height =parseInt($('#emailTable').css('height').substr(0,$('#emailTable').css('height').length-2))
				popUpBoxHeight(height+150);
				$('#moreEmail').unbind('click');
				$('#moreEmail').bind('click',function(){getSendingEmailList(offset+10)})
			
			}
			$('#popUpBoxEnter').hide();
			$('#popUpBoxCancel').hide();
	},'json')  

	  
	  
	  
  }
  function remoteEmailSend(id)
  {
      startSending(id);
      
      /*
	   $.post('/member/remote_email_send',{id:id},function(data)
	{
			if(data.result==true)getSendingEmailList(0)		;
	
	},'json')  
    */
	  
 }
   function memberEmailSend()
  {
	 $.post('/member/get_email_send',{},function(data)
	{
			 
		//  $('#memberData').html('<iframe  src="'+data.url+'"  style="width:1000px;height:800px"></iframe>');
			var content= '<iframe  src="'+data.url+'"  style="width:1000px;height:800px"></iframe>';
			openPopUpBox(content,1000,800,'closePopUpBox');	
			$('#popUpBoxEnter').hide();
			$('#popUpBoxCancel').hide();
	},'json')  

	
	  
  }	
  function memberDelete(memberID)
  {
	  	 if(confirm('您確定要將會員'+memberID+'的所有資料刪除?'))	  
	$.post('/member/member_delete',{memberID:memberID},function(data)
	{
		if(data.result==true)  
		{
			alert('會員已刪除');
			closePopUpBox();
			$('#search_memberID').val(memberID);
			findMember(2);
		}
	  
	},'json')
  }
  function memberOption(memberID)
  {
	content='<h1>會員操作</h1>';
	content+='<input type="button" class="big_button" value="刪除會員所有資料"  onclick="memberDelete('+memberID+')"><br/>';
	content+='<input type="button" class="big_button" value="會員編號移轉" onclick="chengeMemeberID('+memberID+')"><input type="text" class="big_text" id="toMemberID">';
  
	openPopUpBox(content,600,500,'closePopUpBox');	  
  }
  
  function chengeMemeberID(orgMemberID)
  {
	 toMemberID =  $('#toMemberID').val()
	 if(toMemberID=='')
  	{
		alert('請填入移轉會員資料');
		return;	
	}
	 if(confirm('您確定要將會員'+orgMemberID+'的所有資料移轉到'+toMemberID+'?'))	  
	$.post('/member/member_chenge',{orgMemberID:orgMemberID,toMemberID:toMemberID},function(data)
	{
		if(data.result==true)
		{
			alert('資料移轉完成');
			closePopUpBox();
			$('#search_memberID').val(toMemberID);
			findMember(2);
		}
		else alert('移轉處會員資料已存在');
		
		
		
	},'json')	 
	  
 }
  
  function saleRecord(memberID)
  {
	$.post('/member/get_sale',{memberID:memberID},function(data)
	{
		if(data.result==true)
		{
			var content= '<iframe  src="'+data.url+'" style="width:1000px; height:450px"></iframe>';
			openPopUpBox(content,1000,500,'closePopUpBox');	
		}
		
	},'json')  
	  
  }
  
  function memberBatchEdit(j)
  {
	  save = 0;
	  for(i=0;i<20;i++)
	  {
		     
		  if($("#tr_memberTable_"+j+"_"+i).length>0)
		  {
			
			  save++;
			 memberEditRow(j,i,$("#tr_memberTable_"+j+"_"+i).find('input[name],select[name],textarea[name]').serialize());
			  //memberEditRow(j,i,$('#temp').serialize())
		  }
		  
	  }
	  
	  totalSave = save;
       content=' <h3>正在進行資料的儲存...<span id="progress">0</span>/'+totalSave+'</h3>';
	     openPopUpBox(content,500,100,'closePopUpBox');
		$('#progress').progressbar({value: 0});
		$('#popUpBoxEnter').hide();
	  
   }
   
   function memberEditRow(j,i,post)
   {
	 

		pIndex = 0;
	  $.ajax({
		 type: "POST",
		 dataType: "json",
		 url: "/member/update_member",
		 data: post,
		 success: function(data){

			 if(data.result==true)
			 {
				  $('#msgColumn').html("資料已儲存");
				  pageEdit[j] = false;
				 
				  save--;
				  $('#progress').html(totalSave-save);
				  if(save==0)
				  {
					closePopUpBox();  
				  }
			 }
		 }
	   });		
		
   }  
  
  
  function memberEdit(j)
  {
	  memberBatchEdit(j);
	  /*
	  pIndex = 0;
	  $.ajax({
		 type: "POST",
		 dataType: "json",
		 url: "/member/update_member",
		 data: $("#memberDataForm_"+j).serialize(),
		 success: function(data){
		
			 if(data.result==true)
			 {
				  $('#msgColumn').html("資料已儲存");
				  pageEdit[j] = false;
			 }
		 }
	   });	
	  */
  }
  
 
  
  var nowPage = 1;
  var pageEdit = Array();
