<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>email sending</title>
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />

<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
<script type="text/javascript" src="https://cdn.ckeditor.com/4.6.2/full/config.js?t=H0CG"></script>
<script type="text/javascript" src="https://cdn.ckeditor.com/4.6.2/full/styles.js?t=H0CG"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.ckeditor.com/4.6.2/full/plugins/scayt/skins/moono-lisa/scayt.css">
<link rel="stylesheet" type="text/css" href="https://cdn.ckeditor.com/4.6.2/full/plugins/wsc/skins/moono-lisa/wsc.css">
<link rel="stylesheet" type="text/css" href="https://cdn.ckeditor.com/4.6.2/full/plugins/copyformatting/styles/copyformatting.css">

</head>

<body style="width:900px; color:#FFF">
<form id="emailform" action="/member/email_preview" method="post" target="_blank">
	<table>
	    <tr><td>目標：</td>
        	<td>本店會員</td>	
        </tr>
    	<tr>
        	<td>性別：</td>
            <td>
            	<select name="sex">
                	<option value="0">不限</option>
                    <option value="1">男性</option>
                    <option value="2">女性</option>
                    
                </select>
             </td>
         </tr>
         <tr>    
             <td>年齡：</td>
             <td>
             <select name="fromAge">
             		<?php for($i=0;$i<=99;$i++):?>
                	<option value="<?=$i?>"><?=$i?></option>
 					<?php endfor;?>
                </select>~
			 <select name="toAge">
             		<?php for($i=99;$i>=0;$i--):?>
                	<option value="<?=$i?>"><?=$i?></option>
 					<?php endfor;?>
                </select>                
             </td>
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
            	<textarea name="signature" style="width:800px; height:200px" id="signature"><?=$signature?></textarea>
						<script type="text/javascript">
								signature = CKEDITOR.replace( 'signature', { customConfig : 'config_memberemail.js'} );
							</script>
             
            </td>
       </tr>        
    </table>
    <input type="hidden" name="type" value="0" id="sendType">
    <input type="button" class="big_button" value="送出審核(審核後直接發出)" onclick="$('#sendType').val(0);emailConfirm()">
    <input type="button" class="big_button" value="送出審核(審核後再由我發信)" onclick="$('#sendType').val(1);emailConfirm()">
    <input type="submit" class="big_button" value="預覽">
      <input type="button" class="big_button" value="取消" onclick="parent.location.href ='http://localhost/member';">
    
    


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
		
		$('body').append('<div class="popUpCover" style="display:none"></div>')
		$('.popUpCover').css('height',$(document).scrollTop()+$(window).height());	
		$('.popUpCover').css('width',$(window).width());	
		$('.popUpCover').fadeIn('fast');
		$('.popUpCover').html('<img src="/images/ajax-loader.gif" style=" margin-top:400px"/>資料傳輸中...')
		//var editor = CKEDITOR.replace("content");
		$('#content').val(mainContent.getData());
		$('#signature').val(signature.getData());
	
		$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/member/email_confirm",
	   data: $("#emailform").serialize(),
	   success: function(data){
		  
		 	if(data.result==true)  
			{
				alert('資料已送出');
				if(data.shopID!=0)parent.location.href ='http://localhost/member';
                else location.reload();
				
			}
		 
		 }
		})
		
	}


</script>
</body>
</html>