<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>email sending</title>
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<script type="text/javascript" src="http://www.phantasia.tw/libs/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
</head>

<body style="width:900px; color:#FFF">
<form id="emailform" action="/member/email_preview" method="post" target="_blank">
	<table>
	    <tr><td>目標：</td>
        	<td><input type="text" value="" name="target" id="target"></td>	
        </tr>
    	<tr><td>主旨：</td><td><input type="text" name="subject" id="subject" style="width:800px"></td></tr>
	 	<tr><td></td><td colspan="1">內容：</td><tr>
        <tr><td></td><td><textarea name="content" style="width:800px; height:500px" id="content"></textarea>	
						<script type="text/javascript">
								mainContent = CKEDITOR.replace( 'content', { customConfig : 'config_memberemail.js'} );
							</script></td></tr>
        <tr><td></td><td colspan="1">簽名檔：</td><tr>
        <tr><td></td>
        	<td>
            	<textarea name="signature" style="width:800px; height:200px" id="signature">瘋桌遊益智遊戲專賣店</textarea>
						<script type="text/javascript">
								signature = CKEDITOR.replace( 'signature', { customConfig : 'config_memberemail.js'} );
							</script>
             
            </td>
       </tr>        
    </table>
     <input type="submit" class="big_button" value="預覽" >
    <input type="button" class="big_button" value="開始發送" onclick="emailConfirm()">
    
    


</form>
<script>
	function emailConfirm()
	{
		if($('#subject').val()=='')
		{
			alert('請輸入主旨');
			$('#subject').focus();
			return;
		}
		
			
		
		
	content = '<div style=" background-color:white;">'+
			'<table border="1" style=" width:800px; background-color:white; color:black">'+
				'<tr><td>正在傳送:</td>'+
					'<td id="sending"></td>'+
					'<td>已傳送:<span id="sended">0</span></td>'+
				'</tr>'+
				'<tr>'+
					'<td>成功傳送:<span id="success" onclick="$(\'#successList\').toggle()" style=" cursor:pointer">0</span><div id="successList" style="display:none"></div></td>'+
					'<td>傳送失敗:<span id="fail" onclick="$(\'#failList\').toggle()" style=" cursor:pointer">0</span><div id="failList" style="display:none"></div></td>'+
					'<td>結果未知:<span id="unknown" onclick="$(\'#unknownList\').toggle()" style=" cursor:pointer">0</span><div id="unknownList" style="display:none"></div></td>'+
				'</tr>'+	
			'</table>'+
		'</div>';
		openPopUpBox(content,850,450,closePopUpBox,true);
		$('#content').val(mainContent.getData());
		$('#signature').val(signature.getData());
		var str = $('#target').val();
		 a = str.split(',')
	
		queryString =  $("#emailform").serialize();
		for(key in  a)
		{
			
			r = a[key].split('~');	
			
			if(r.length>1) for(var i=r[0];i<=r[1];i++)    setTimeout("emailSend("+i+",'"+queryString+"')",i*10000);  
			else emailSend(r[0],queryString)
			
		}
	   
		$('#sending').html('傳送完成');	
		
	
		
	}
	
	
	
	
	function emailSend(memberID,queryString)
	{
			$('#sending').html(memberID);

			$.ajax({
		   type: "POST",
		   async: true,
		   dataType:"json",
		   url: "/member/email_send_list",
		   data: queryString+'&memberID='+memberID,
		   success: function(data){
			  
			  
				if(data.result==true)  
				{
					
					$('#sended').html(parseInt($('#sended').html())+1);
					switch(data.re)
					{
						case -1:
							$('#unknown').html(parseInt($('#unknown').html())+1);
							$('#unknownList').append(memberID+' '+data.email+'<br/>');
						break;	
						case 0:
							$('#fail').html(parseInt($('#fail').html())+1);
							$('#failList').append(memberID+' '+data.email+'<br/>');						
						break;
						case 1:
							$('#success').html(parseInt($('#success').html())+1);
							$('#successList').append(memberID+' '+data.email+'<br/>');						
						break;
						
						
						
					}
				}
			 
			 }
			})	
		
		
	}

</script>
</body>
</html>