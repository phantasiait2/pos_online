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
	   $.post('/member/remote_email_send',{id:id},function(data)
	{
			if(data.result==true)getSendingEmailList(0)		;
	
	},'json')  
	  
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
  
  function memberEdit(j)
  {
	  
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
	  
  }
  
  
  var nowPage = 1;
  var pageEdit = Array();
