<?php 
class paser extends Model {
	function paser()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function get($url,$data,$decode =false)
	{
		
		$i = 0;
		$dataStr='';
		while(list($key , $val) = each($data))
		{
			$i++;
			if($i>1)$dataStr.='&';
			$dataStr.=$key.'='.$val;
		}	
		$url = $url.'?'.$dataStr;
	
		if(substr($url,0,7)!="http://" && substr($url,0,8)!="https://")$url = "https://".$url;	
  
		
		$ch = curl_init();
	
		curl_setopt ($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		
		curl_setopt($ch, CURLOPT_REFERER, $url);
		ob_start();
		curl_exec ($ch);
		curl_close ($ch);
		$fin= ob_get_contents();
		ob_end_clean();	
		
		if($decode)return json_decode($fin,true);	
		else return $fin;		
	}
	function ECPost($url,$data,$decode=false)
    {
        $data['shopID'] = 666;
        $data['licence'] = '150e4e2633d2d5aa712b6a41fcd6ba01';
        return $this->post('http://possvr.phantasia.com.tw/'.$url,$data,$decode);
        
        
        
    }
	function post($url,$data,$decode)
	{
				
			
		$i = 0;
		$dataStr='';
        /*
		if(strpos($url,'phantasia.com.tw',0)!=false) 
		{
			$data['orgUrl'] = $url ;
			$url = 'http://shipment.phantasia.tw/welcome/paser_transfer';
			
		}
        */
        /*
		while(list($key , $val) = each($data))
		{
			$i++;
			if($i>1)$dataStr.='&';
		
            if(is_array($val))
            {
                
                foreach($val as $row)
                $dataStr.=$key.'[]='.$row;
                
            }
            else 	$dataStr.=$key.'='.$val;
		}	
		*/
        //shake hand code
         $data['shakeHandCode'] = 'IlovePhantasia';
        
        
        
	   $dataStr = http_build_query($data,'flags_');
		
		
		if(substr($url,0,7)!="http://" && substr($url,0,8)!="https://")$url = "https://".$url;	

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dataStr);
		ob_start();
		curl_exec ($ch);
		curl_close ($ch);
		$fin= ob_get_contents();
		ob_end_clean();	
		
		if($decode)return json_decode($fin,true);	
		else return $fin;	
		
		
	}

	function post_test($url,$data,$decode)
	{
				
			
		$i = 0;
		$dataStr='';
		if(strpos($url,'phantasia.com.tw',0)!=false) 
		{
			$data['orgUrl'] = $url ;
			$url = 'http://shipment.phantasia.tw/welcome/paser_transfer';
		}
		while(list($key , $val) = each($data))
		{
			$i++;
			if($i>1)$dataStr.='&';
			$dataStr.=$key.'='.$val;
		}	
		
		if(substr($url,0,7)!="http://")$url = "http://".$url;	
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dataStr);
		
		ob_start();
			
		curl_exec ($ch);
			
		curl_close ($ch);
		$fin= ob_get_contents();
		ob_end_clean();	
		
		if($decode)return json_decode($fin,true);	
		else return $fin;	
		
		
	}
	
	function post_ignore($url,$data)
	{
				
			
		$i = 0;
		$dataStr='';
			if(strpos($url,'phantasia.com.tw',0)!=false) 
		{
			$data['orgUrl'] = $url ;
			$url = 'http://shipment.phantasia.tw/welcome/paser_transfer';
		}
		while(list($key , $val) = each($data))
		{
			$i++;
			if($i>1)$dataStr.='&';
			$dataStr.=$key.'='.$val;
		}	
		
		if(substr($url,0,7)!="http://")$url = "http://".$url;	
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dataStr);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		ob_start();
		curl_exec ($ch);
		curl_close ($ch);
		return;
		
		
	}
	



}

?>
