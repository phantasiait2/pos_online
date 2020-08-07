// JavaScript Document

function save(id)
{
	
	$.post('/account/level_edit',{aid:id,email:$('#email_'+id).val(),level:$('#power_'+id).val(),shopID:$('#shopID_'+id).val()},function(data)
	{
	
		if(data.result!=true)
		{
				alert('權限不足');
				closePopUpBox();
				location.reload();
		}
		else alert('權限已修改')
		
	},'json')	
	
	
}

function accountDelete(id)
{
	if(confirm('您確定要刪除此帳號？'))
	$.post('/account/delete',{aid:id},function(data)
	{
		if(data.result!=true)
		{
				alert('權限不足');
				
		}
		else 
		{
				alert('帳號已刪除');
				closePopUpBox();
				location.reload()
			
			
		}
		
		
	},'json')	
	
	
}
function changePwForm()
{
	content = 
	 '<h1>修改密碼</h1>'+
     '<form id="newAccountForm">'+
     '<li>'+
    	'請輸入舊密碼：<input type="password"  name="pw" id="account_old_pw" class="big_text" />'+
    '</li>'+
     '<li>'+
    	'請輸入新密碼：<input type="password"  name="pw" id="account_new_pw" class="big_text" />'+
    '</li>'+
	     '<li>'+
    	'請再輸入一次：<input type="password"  name="pw" id="account_check_pw" class="big_text" />'+
    '</li>'+

    '</form>';
	
	 openPopUpBox(content,600,180,'changePw');	


}

function changePw()
{
	if($('#account_new_pw').val()!=$('#account_check_pw').val()||$('#account_new_pw').val()==''){
			alert('兩次輸入不同');
			return;
	}
	$.post('/account/change_pw',{new_pw:$('#account_new_pw').val(),old_pw:$('#account_old_pw').val()},
	function(data)
	{
		
		if(data.result==true)
		{
			alert('密碼已修改');
			closePopUpBox();
			location.reload();
		}
		else alert('密碼錯誤');
	
	},'json')	
	
	
	
}

function getShop(id)
{
	$.post('/system/get_shop',{},function(data){
		
           $('#'+id).html('<option value="0">幻遊天下</option>');
		for(key in data.shopData)
		{
            
        
			$('#'+id).append('<option value="'+data.shopData[key].shopID+'">'+data.shopData[key].name+'</option>');
		}
		
	},'json')
	
	
}

function getSupplier(id)
{
    
    
    $.post('/product/get_suppliers',{},function(data){
		
         $('#'+id).html('');
		for(key in data.suppliers)
		{
            
          
			$('#'+id).append('<option value="'+data.suppliers[key].supplierID+'">'+data.suppliers[key].name+'</option>');
		}
		
	},'json')
    
    
    
    
    
}






function newAccountForm()
{
	content = 
	 '<h1>新增帳號</h1>'+
     '<form id="newAccountForm">'+
     '<li>'+
    	'帳號：<input type="text"  name="account" id="account" class="big_text" />'+
    	'密碼：<input type="password"  name="pw" id="new_account_pw" class="big_text" />'+
    '</li>'+
	'<li>'+
	'權限：<select onchange="changeOp(\'new_account_level\',\'new_account_shop\')" id="new_account_level"  name="level">'+
				'<option value="100">商品管理</option>'+
				'<option value="80">財務管理</option>'+
				'<option value="50">店長</option>'+
				'<option value="10">店員</option>'+
				'<option value="5">訂購廠商</option>'+
                '<option value="-1">供應商</option>'+
			'</select>'+
	'</li>'+
	'<li><select id="new_account_shop" name="shopID">'+
	
	'</select></li>'+
    '</form>';
	getShop('new_account_shop');
	 openPopUpBox(content,600,180,'newAccount');	


}


function changeOp(fromID,toID)
{
    
    if($('#'+fromID).val()==-1) getSupplier(toID);
    else getShop(toID);
    
    
    
}
function newAccount()
{
	
	$.post('/account/new_account',{account:$('#account').val(),pw:$('#new_account_pw').val(),level:$('#new_account_level').val(),shopID:$('#new_account_shop').val()},
	function(data)
	{
		
		if(data.result==true)
		{
			alert('帳號已新增');
			closePopUpBox();
			location.reload();
		}
		else alert('帳號重複或權限不足');
	
	},'json')	
	
	
}


function emailManger()
{
   $.post('/account/get_email_function',{},
          function(data)
          {
            if(data.result==true)
                {
                    $('.product').html('<h1>email管理</h1>'+
                                       '<h2><input type="button" value="新增" onclick="newEmailData()"></h2><table border="1" id="emailTable"></table>');
                    $('#emailTable').append('<tr><td>email原因</td><td>使用程式</td><td></td></tr>');
                    
                    for(key in data.emailFunction)
                    {
                      
                          $('#emailTable').append('<tr>'+
                                                  '<td>'+data.emailFunction[key].id+'</td>'+
                                                  '<td>'+data.emailFunction[key].content+'</td>'+
                                                  '<td>'+data.emailFunction[key].function+'</td>'+
                                                  '<td><input type="button" class="big_button"  onclick="editEmail('+data.emailFunction[key].id+')"value="編輯內容及寄送對象"></td>'+
                                                  '<td><input type="button" class="big_button"  onclick="" value="刪除"></td>'+
                                                                                                    '</tr>');
                        
                        
                    }
                    
                    
                    
                }
          }
          
          
          ,'json');
    
    
    
    
}

function editEmail(id)
{
    
     newEmailData();
    $.post('/account/get_email_detail',{emailFunctionID:id},function(data){
         
   
            if(data.result==true)
                {  
                 
                 
                    $('#number').html(data.detail.id);
                    $('#emailFunctionID').val(data.detail.id);
                    $('#content').val(data.detail.content);
                    $('#function').val(data.detail.function);
                    
                }
           
        
    },'json')
    
    
}


function newEmailData()
{
    
    var content = '<input type="hidden" id="emailFunctionID" value="0"><table border="1">'+
        '<tr><td>編號</td><td id="number">新增EMAIL規則</td></tr>'+
        '<tr><td>內容</td><td><textarea id="content"></textarea></td></tr>'+
        '<tr><td>Function</td><td><textarea id="function"></textarea></td></tr>'+
        '<tr><td>emailList</td><td></tr>'+   
    '</table>';
    
     openPopUpBox(content,800,580,'newEmailDataSend');	
    
    
}


function newEmailDataSend()
{
      $.post('/account/email_detail_send',{id:$('#emailFunctionID').val(),emailFunctionID:$('#emailFunctionID').val(),content:$('#content').val(),function:$('#function').val()},function(data){
            if(data.result)
                {
                  closePopUpBox();
                    emailManger();
                }
           
        
    },'json')
    
    
    
}

