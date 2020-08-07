
function login_submit(){
	
	$.post("/welcome/login_chk",{user_id:$('#user_id').val(),user_pw:$('#user_pw').val()},function(data)
				{
					
					if (data.result == false){
						$('#login_errmsg').html('帳號或密碼錯誤').show();
					}else{
						$('#login_errmsg').hide();
						location.href='/product';
					}
				}		
	
	,'json')
	

}
