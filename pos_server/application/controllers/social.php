<?php

class Social extends POS_Controller {

	function Social()
	{
		parent::POS_Controller();
				// 初始設定
		define('CONSUMER_KEY', "nZy9YDiLwDwb");
		define('CONSUMER_SECRET', "bRhf8Jt3PLP0uR9rWuRSuT1EWbgUMw9u");
		define('OAUTH_TOKEN','QLqQySMHoXcP');
		define('OAUTH_TOKEN_SECRET','cHCogaazpJArblGaidMxSiOl6xExnbFL');
		define('OAUTH_REQUEST_TOKEN','http://www.plurk.com/OAuth/request_token');
		define('OAUTH_AUTHORIZATION_TOKEN','http://www.plurk.com/OAuth/authorize');
		define('OAUTH_ACCESS_TOKEN','http://www.plurk.com/OAuth/access_token');
			
	}
	
	function index()
	{
		$this->data['css'] = $this->preload->getcss('shipment');
		$this->data['display'] = 'social_view';
		$this->load->view('template',$this->data);	
	}
	function changeLine()
	{
		
		echo '<br/>----------<br/>';	
	}
	function plurk_verify()
	{
		// 運行範例
		$ch = $this->plurk_REQUEST(OAUTH_REQUEST_TOKEN,10);
		$rs = curl_exec($ch);
		parse_str($rs, $output);
		$data['url'] = OAUTH_AUTHORIZATION_TOKEN.'?oauth_token='.$output['oauth_token'];
		$data['result']=true;
		$data['oauth_token'] = $output['oauth_token'];
		$data['oauth_token_secret'] = $output['oauth_token_secret'];
		echo json_encode($data);
		exit(1);
		
				
		
	}

	function plurk_access()
	{
		
		
		$oauth_token_secret = $this->input->post('oauth_token_secret');
		$ch = $this->plurk_REQUEST(OAUTH_ACCESS_TOKEN,10,$_POST,$oauth_token_secret);
		$rs = curl_exec($ch);
		parse_str($rs, $output);
		$data['result']=true;		
		$data['oauth_token'] = $output['oauth_token'];
$data['oauth_token_secret'] = $output['oauth_token_secret'];
		echo json_encode($data);
		exit(1);
		
	}
	function plurk_send()
	{
		$host = "http://www.plurk.com";
		$app = "/APP/Timeline/plurkAdd";
		//$app = "/APP/Timeline/getPlurks";
		//$app = "/APP/Profile/getOwnProfile";
		
		$_POST['content']  = 'ssssssss';
		$_POST['qualifier'] = 'says';
		$_POST['lang'] = 'tr_ch';
		
		$oauth_token_secret = $this->input->post('oauth_token_secret');	
		
		print_r($_POST);
		$ch = $this->plurk_REQUEST($host.$app,10,$_POST,$oauth_token_secret);
		$rs = curl_exec($ch);
		parse_str($rs, $output);
		$this->changeLine();
		print_r($output);		
		
		
		
	
	}


// 函式庫
/**
 * Get Request Token
 * 
 * @param OAuth Request 網址 $url
 * @param 預設運行秒數 $s
 */
	function plurk_REQUEST($url='',$s=10,$nc=array(),$oauth_token_secret=''){
		if($url){
			$nonce = md5(uniqid(rand(), true));
			$nc['oauth_timestamp'] = time();
			$nc["oauth_nonce"]=substr($nonce,0,8);
			$nc["oauth_consumer_key"]	= CONSUMER_KEY;
			$nc["oauth_version"] 		= "1.0";
			$nc["oauth_signature_method"]= 'HMAC-SHA1';
			$ch = curl_init($url);
			uksort($nc, 'strcmp');
			$shtxt = 'POST&'.urlencode($url).'&'.urlencode(http_build_query($nc));
		
			$shapw = urlencode(base64_encode(hash_hmac('sha1', $shtxt , CONSUMER_SECRET.'&'.$oauth_token_secret, true)));
			$nc['oauth_signature'] = $shapw;
			
			foreach ($nc as $k => $v) {
				$kv[] = $k.'="'.$v.'"';
			}
			$header[]  = 'Content-Type: application/x-www-form-urlencoded';
			$header[] = 'Authorization: OAuth ' . implode(',', $kv);
	echo http_build_query($nc);
		
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($nc));
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);		
			curl_setopt($ch, CURLOPT_USERAGENT,		 'anyMeta/OAuth 1.0 - ($LastChangedRevision: 174 $)');
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_TIMEOUT, $s);
			return $ch;
		} else {
			return null;
		}
	}	
	function generate_random_string($length = 8) {
    $chars = '0123456789';
    $string = '';
    for ($p = 0; $p < $length; $p++) {
		
	$string .= $chars[mt_rand(0, strlen($chars)-1)];
    }
    return $string;
}

	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */