<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<script type="application/javascript">

var oauth_token;
var oauth_token_secret;
function user_verify()
{
	$.post('/social/plurk_verify',{},function(data)
	{
		if(data.result==true)
		{
			window.open(data.url);	
			oauth_token = data.oauth_token;
			oauth_token_secret=data.oauth_token_secret;
		
		}
		
	},'json'
	
	)		
	

}

function user_num_verify()
{
	alert(oauth_token);
	$.post('/social/plurk_access',{oauth_verifier:$('#verifytxt').val(),oauth_token:oauth_token,oauth_token_secret:oauth_token_secret},function(data)
	{
		if(data.result==true)
		{
			alert('success');
			oauth_token = data.oauth_token;
			oauth_token_secret=data.oauth_token_secret;
						
		}
	},'json')

	
	
}

function plurk_send()
{
	alert(oauth_token);
	$.post('/social/plurk_send',{oauth_token:oauth_token,oauth_token_secret:oauth_token_secret},function(data)
	{
		if(data.result==true)
		{
			alert('success');
			
			
		}
	},'json')	
	
}



</script>



<body>
<input type="button" onclick="user_verify()"  value="user verify" >;
<div id="verifyNum">
	<input type="text" id="verifytxt">
    <input type="button" onclick="user_num_verify()"  value="user verify" >;
</div>
<div id="oauth_token">
	

</div>

   <input type="button" onclick="plurk_send()"  value="user verify" >;





</body>
</html>