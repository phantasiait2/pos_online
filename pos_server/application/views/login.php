<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>(遠端)瘋桌遊進銷存系統</title>
<link rel="shortcut icon" href="/phantasia/images/favicon.ico"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<?php if(isset($css))echo $css;?>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/reject_enter_key.js"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script type="text/javascript" >
$(document).ready(function()
{
	location.href = 'http://shipment.phantasia.com.tw'	;
	
})

</script>

<script type="text/javascript"> var accountLevel=<?=$level?></script>
<?=($this->uri->segment(2)!='version_update'&&$this->uri->segment(1)!='initial'&&$this->uri->segment(1)!='welcome')?'<script type="text/javascript" src="/javascript/pos.js">':''?></script>
<?php if(isset($js))echo $js;?>

</head>
<body>
 <div style="background:url(/images/login_bg-01.png); height:768px; width:1024px; margin-left:auto; margin-right:auto;">
                         
                            <div id="loginBox">
							<?php if(!$logined):?>
                            <form name="login" onsubmit="return false;">
                            <table id="loginTable">
                            	<tr><th>帳號：</th><tr/>
                                <tr><td><input type="text" id="user_id" name="user_id" class="big_text" / ></td></tr>
                                <tr><th>密碼：</th></tr>
                                <tr><td><input type="password" id="user_pw" name="user_pw" class="big_text"  onkeyUp="if(event.keyCode=='13')login_submit()"/></td></tr>
                                <tr><td><div class="errmsg" id="login_errmsg" style="margin-left:0px;"></div></td></tr>
                                 <tr><td>
                                 <input type="button" value="登入" class="loginBtn" onclick="login_submit();" style="float:left" /></td></tr>
                                <tr><th></th>
                                	<td>
                                		
                                    </td>
                                </tr>
                                
                            </table>
                            </form>
                            <?php else:?>
                             <h2 style="margin-bottom:10px;">您已經登入囉！</h2>
                             <h5>若要登入其他帳號，請先<a href="/welcome/logout">登出</a>，再使用其他帳號登入，以避免您的資料發生錯誤。</h5>
                             <a href="/product">點此繼續其他操作</a>
                             <?php endif;?>
                            </div>
                            
   
</div>
</body>
</html>