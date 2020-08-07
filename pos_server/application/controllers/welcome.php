<?php

class Welcome extends POS_Controller {

	function Welcome()
	{
		parent::POS_Controller();
			
	}
	
	function index()
	{
		redirect('/welcome/login');
		$this->data['css'] = $this->preload->getcss('pos');
		$this->data['js'] = $this->preload->getjs('pos_client_index');
		$this->data['display'] = 'index';
		$this->load->view('template',$this->data);	
	}
	function login_status()
	{
		$data['result'] = $this->data['logined'];
       
		echo json_encode($data);
		exit(1);
	}
	
	function get_ship()
    {
        $this->db->select('shopID,shipOut');
        $this->db->where('joinType',1);
        $query = $this->db->get('pos_sub_branch');
        $data['shipData'] = $query->result_array();
        $data['result']  = true;
        echo json_encode($data);
		exit(1);
        
        
    }
	
	function product_num()
	{
		$query = $this->db->get('pos_product');
		$data = $query->result_array();
		$this->load->model('PO_model');
		foreach($data as $row)
		{
			$postData = array(
			'bid'    =>$row['phaBid'],
			'productID'    =>$row['productID']);
			$r = $this->paser->post('http://www.phantasia.tw/bg/chk_bg',$postData,false);		
			if($r!=0)
			{
				echo $row['productID'].','.$r['min_people'].','.$r['max_people'].'</br>';
//					$this->db->where('productID',$row['productID']);
	//			$this->db->update('pos_product',array('num'=>$r));
			}
			
		}
		
	}
	function paser_transfer()
	{
		set_time_limit(0);
		ini_set('max_execution_time', 0);;
		$orgUrl = $this->input->post('orgUrl');

		echo  $this->paser->post($orgUrl ,$_POST,false);	
		
		
		
	}
	
	
	function product_recover()
	{
		$productID = $this->input->post('productID');
		$this->load->model('Order_model');
		$this->Order_model->reAllocateOrder($productID);
			$data['result']  = true;;
		echo json_encode($data);
		;
	}
	
    function post_test()
    {
         $this->load->model('Order_model');
        
        $this->load->model('cs_order_model');
        $postData = array();
       //  $r = $this->paser->post('http://shipment.phantasia.com.tw/csorder/order_update',$postData,false);
        echo    $r; 
        //$this->cs_order_model->mallDataTransfer();
        $this->load->view('test');
        
        
        
    }
    
    function getmail()
    {
        
        
        $this->load->helper('recivemail');
          
        
        $m = new mailControl();
  ;

    
        $r = $m->mailReceived();
       // print_r($r);
        
        foreach($r['mail'] as $row)
        {
           foreach($row['attachList'] as $each)
           {
               $savePath = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/einvoice/';
             
                $this-> zip_test($savePath.$each['pathname'])  ;
               
           }
        }
       
        
        
        
        
        
    }
    function  zip_test($path='')
    {
        echo $path;
        $savePath = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/einvoice/';
        if($path=='')$path = $savePath.'201911/2813233518895.zip';
        
        $zip = new ZipArchive;
        if ($zip->open($path) === TRUE)
        {
            $zip->extractTo($savePath.'xls/');
            $zip->close();
            
             $zip =  zip_open($path);
        if ($zip)
            {
            while ($zip_entry = zip_read($zip))
            {
                $filename = zip_entry_name($zip_entry);
            //echo "<p>";
            //echo "Name: " . $filename . "<br />";
                
                  include_once($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/PHPExcel/IOFactory.php');  
		      //echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		      $reader = PHPExcel_IOFactory::createReader('Excel5'); // 讀取舊版 excel 檔案  
		      $PHPExcel = $reader->load($savePath.'xls/'.$filename); // 檔案名稱  
		      $sheet = $PHPExcel->getSheet(1); // 讀取第2個工作表(編號從 0 開始)  
              $highestRow = $sheet->getHighestRow(); // 取得總列數  
            $dateStr = $sheet->getCellByColumnAndRow(0,1)->getValue() ;//讀取   
                $this->load->model('accounting_model');
              $d = explode('：',$dateStr); 
            //     echo 'date:'.$d[1];
              $num = $sheet->getCellByColumnAndRow(4, 5)->getValue() ;//讀取締5列第5行。       
               echo 'uploadNum:'.$num.'<br/>';   
                 $exp_num = $this->accounting_model->getXmloutNum($d[1]);
                echo 'expNum:'.$exp_num.'<br/>';  
                
                $content='<h3>您好</h3>';
                
                $content.='<h4>'.$d[1].'發票上傳紀錄：</h4>';
                $content.='<h4>理論應上傳發票：'.$exp_num.'</h4>';
                $content.='<h4>實際已上傳發票：'.$num.'</h4>';
                
                
                
                
                
                
                if($exp_num==$num)
                {
                    $result = '成功';
                    $content.='<h4>數量正確！請上平台核實內容</h4>';
                    unlink($path) ;
                    unlink($savePath.'xls/'.$filename);
                    
                }
                
                else 
                {
                    $result = '有誤';
                      $content.='<h4 style="color:red">數量錯誤！請通知系統管理員檢查上傳機制</h4>';
                    
                    
                }
                
                
                
                mb_internal_encoding('UTF-8');
                
			$title = $d[1].'電子發票上傳'.$result ;
			$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			
				$this->Mail_model->myEmail('phantasia.ac@gmail.com'.','.'lintaitin@gmail.com',$title,$content,$headers,100);
				
			 
                
                
                
                
                
            }
              zip_close($zip);
            
            }
            
            
           
        } else {
            echo 'failed';
        }
        
        
        
            

          
    }
    
    
    function get_post()
    {
        
         $this->db->where('shopID',1);
          $this->db->where('id >',650946);
          $q = $this->db->get('pos_cash_register');
          $remain = 14912;
          $data = $q->result_array();
          foreach( $data as $row)
          {
              
              $new = $remain + $row['MIN'] - $row['MOUT'];
              if($new!= $row['remain'])
              {
                  
                  echo $row['id'].','.$new.'<br/>';
                  $this->db->where('id',$row['id']);
                  $this->db->update('pos_cash_register',array('remain'=>$new));
              }
              $remain = $new;
              
              
          }
          
          
        print_r($_POST);
     
        
        
    }
    
    function iframe_connect()
	{
		$this->load->model('System_model');
        $shopID = $this->input->get('shopID');
        $licence = $this->input->get('licence');
        $account = $this->input->get('account');

		if(empty($shopID))$shopID =  $this->uri->segment(3);
		if(empty($licence))$licence =  $this->uri->segment(4);
		if(empty($account))$account =  $this->uri->segment(5);
       
     
		if($this->System_model->chkShop($shopID,$licence))
		{
			$this->session->set_userdata('account',  $account);
			$this->session->set_userdata('aid', 999);
			$this->session->set_userdata('shopID',  $shopID);
			$this->data['shopID'] = $shopID;
		    redirect($this->input->get('url'));
	  } 
        else redirect('/');
            
		return;
		
	}
    function product_test()
    {
        $url = 'http://www.kktcg.com/index.php?route=product/search&search=UGD010211';
        $result =  $this->paser->post($url,array(''),false);
		  
        $a = $this->cut('<div class="image">','</div>',$result);
    
        $link = $this->cut('<img src="','" alt',$a);
        echo '<img src="'.$link.'">';
    //   $result =  $this->paser->post($link,array(''),false);
      //  echo $result;
        
        
        
    }
	function getptt()
	{
        
        $url = $this->input->get('url');
        $id = $this->input->get('id');
		$type = 1; //早盤
		$result =  $this->paser->post($url,array(''),false);
		$a = explode('<div class="push">',$result);
        foreach($a as $row)
        {
            $d = array();
            $s = $this->cut('<span class="f3 hl push-userid">','</span>',$row);
            if(strlen($s)>0)  $d['userID'] =$s;
            
             $s = $this->cut('<span class="f3 push-content">:','</span>',$row);
            if(strlen($s)>0)  $d['content'] =$s;
            
             $s = $this->cut('<span class="push-ipdatetime">','</span>',$row);
            if(strlen($s)>0) 
            {
               
               
                $d['time'] =date('Y-m-d H:i:s', strtotime($s));
                 //   echo $d['time'];
            }
                
            
            
            if(!empty($d))
            {
                if($d['userID']==$id) $data['data'][] = $d;
             
                
            }
        }
           $this->load->view('ptt',$data);
        
	}
    
     function cut($begin,$end,$str){
        $b = mb_strlen($begin);
         $str = strstr($str,$begin);
        
    $e = mb_strpos($str,$end) - $b ;

    return trim(mb_substr($str, $b ,$e));
} 
    
    function tagptt()
    {
        $this->db->where('taged',0);
        $this->db->order_by('id');
        $q = $this->db->get('ptt');
        $data['data'] = $q->result_array();
        
        $this->load->view('ptt',$data);
        
        
    }
    function tagout()
    {
        $id= $this->input->post('id');
        $data['way']= $this->input->post('way');
        $data['taged'] = 1;
        $this->db->where('id',$id);
        $this->db->update('ptt',$data);
        
        $data['result'] = true;
		echo json_encode($data);
	
    }
    
    
	function bgfestival()
	{
		
		 
		$shopID = $this->uri->segment(4);
        $page = $this->uri->segment(3);
        
        
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<table>';
        for($i=1*95*$page+1;$i<=95*($page+1);$i++)       
        {
           $cardNumber =  str_pad($shopID,2,'0',STR_PAD_LEFT).str_pad($i,3,'0',STR_PAD_LEFT);
           $q = substr(md5('phantasia bg festival2019'.$cardNumber),0,8);
               echo '<tr><td>：'.$cardNumber.'</td><td>：'.$q.'</td></tr>';
        }
        echo '</table>';
        
		
		
		
	}
	
	function post_paser()
    {

        $this->load->model('Paser');
        echo $r = $this->Paser->post($this->input->get('paserurl'),$_GET,false);
        
        
        
        
    }
    function post_paser_send()
    {
       
        
        
        
    }
	
	function login()
	{
		$this->data['js'] = $this->preload->getjs('login');
		$this->data['css'] = $this->preload->getcss('form');
		$this->data['display'] = '/login';
		$this->load->view('login',$this->data);		
	}
	function please_login(){

		$this->data['css'] = $this->preload->getcss('form');
		$this->data['display'] = 'welcome/please_login';
		$this->load->view('template',$this->data);		
		
	}
	function login_chk()
	{
		$data['result']=false;
		$user_id = $this->input->post('user_id');
		$user_pw = $this->input->post('user_pw');

		if ($user_id==""or $user_pw=="")
		{
			$data['result']=false;
		
		} 
		else
		{
			$sqlstr="select id as aid,account,level,shopID from pos_account where account='".$user_id."'
					and pw='".md5($user_pw)."'";
					
			$result=$this->db->query($sqlstr);
			$row=$result->result_array();
			if($result->num_rows()==1)
			{	
				$this->session->set_userdata('account',  $row[0]['account']);
				$this->session->set_userdata('aid',  $row[0]['aid']);
				$this->session->set_userdata('level',  $row[0]['level']);
				$this->session->set_userdata('shopID',  $row[0]['shopID']);
				$data['result']=true;
                if($row[0]['level']==-1)
                {
                    
                    $this->db->insert('pos_supplier_login',array('shopID'=>$row[0]['shopID'],'time'=>date('Y-m-d H:i:s')));
                    
                    
                }
			
			}
		}
	
		echo json_encode($data);
		;
		
	}
	function debug()
	{
		$this->session->set_userdata('debug',  1);	
		echo json_encode(array('result'=>true));
		;
		
	}
	function logout()
	{
		$this->session->set_userdata('account', '');
		$this->session->set_userdata('aid',  '');
		$this->session->set_userdata('level',  '');
		$this->session->set_userdata('shopID',  '');
		$this->session->set_userdata('debug',  '');
		$this->session->sess_destroy();

         if(!empty($_SERVER["HTTP_CLIENT_IP"])){
              $IP = $_SERVER["HTTP_CLIENT_IP"];
             }
             elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
              $IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
             }
             elseif(!empty($_SERVER["REMOTE_ADDR"])){
              $IP = $_SERVER["REMOTE_ADDR"];
             }
             else{
              $IP = "無法取得IP位址！";
             }
      $this->db->where('IP',$IP);

        $this->db->delete('pos_ip_login');
        
        
                    
		redirect("/welcome/login");	
		
	}
	function header_search()
	{
		if(isset($_REQUEST['term']))$term=$_REQUEST['term'];
		redirect("/bg/dictionary?order=popular&category=all&term=".$term."&search_type=name");	
		
	}
	function get_shop()
	{
		

			$token = true;
	
		$data['shopData'] = $this->System_model->getShop($token);
		$data['result'] = true;
		echo json_encode($data);
		;
	}
	function email_test()
	{
			$this->load->helper('phpmailer');
		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: 瘋桌遊益智遊戲專賣店 \nReply-To:social@phantasia.tw\n";
		
		
		// 產生 Mailer 實體
$mail = new PHPMailer();

// 設定為 SMTP 方式寄信
$mail->IsSMTP();

// SMTP 伺服器的設定，以及驗證資訊
$mail->SMTPAuth = true;      
$mail->Host = "cs.potia.net"; //此處請填寫您的郵件伺服器位置,通常是mail.網址。如果您MX指到外地，那這邊填入www.XXX.com 即可
$mail->Port = 465; //ServerZoo主機的郵件伺服器port為 25 
$mail->SMTPSecure = 'ssl';

// 信件內容的編碼方式       
$mail->CharSet = "utf-8";

// 信件處理的編碼方式
$mail->Encoding = "base64";

// SMTP 驗證的使用者資訊
$mail->Username = "system@phantasia.com.tw";  // 此處為驗証電子郵件帳號,就是您在ServerZoo主機上新增的電子郵件帳號，＠後面請務必一定要打。
$mail->Password = "p53180059";  //此處為上方電子郵件帳號的密碼 (一定要正確不然會無法寄出)

// 信件內容設定  
$mail->From = "social@phantasia.tw"; //此處為寄出後收件者顯示寄件者的電子郵件 (請設成與上方驗証電子郵件一樣的位址)
$mail->FromName = "系統測試"; //此處為寄出後收件者顯示寄件者的名稱
$mail->Subject = "PHPMailer寄信測試標題"; //此處為寄出後收件者顯示寄件者的電子郵件標題
$mail->Body = "這是一封測是信件哦!";   //信件內容 
$mail->IsHTML(true);

// 收件人
$mail->AddAddress("lintaitin@gmail.com", "XXX系統通知信"); //此處為收件者的電子信箱及顯示名稱

// 顯示訊息
if(!$mail->Send()) {     
echo "Mail error: " . $mail->ErrorInfo;     
}else {     
echo "Mail sent";     
} 
	}
	
	function mailapi()
	{
		if($this->input->post('code')!=md5('phaMail')) exit(1);
		
        if($this->input->post('priority')==0) $p = 100;
        else $p = $this->input->post('priority');
        
		$this->Mail_model->myEmail($this->input->post('mail'),$this->input->post('title'),urldecode($this->input->post('content')),$this->input->post('headers'),$this->input->post('memberID'),$p);
			
		
		
	}
	
	function g_mailapi()
	{
		if($this->input->post('code')!=md5('phaMail')) exit(1);
		
		$this->Mail_model->groupEmail(json_decode($this->input->post('mail'),true),$this->input->post('title'),urldecode($this->input->post('content')),$this->input->post('headers'));
			
		
		
	}
	
	
	function email_send()
	{
		$this->load->helper('emailchk_helper');	
		$times  = 0;
		$oneMNum = 3;
		
		// get oneMNum  email 
		$emailList=$this->Mail_model->getEmail($oneMNum);
            
        $this->load->model('Order_model');
        $this->load->model('Product_model');
        $this->load->model('cs_order_model');
       
            
            
      
	
		mb_internal_encoding('UTF-8');
		$this->load->helper('phpmailer');
		 $sender = 'social@phantasia.tw';
        $overEmail = $this->Mail_model->emailNumChk();
        $oneMNum  -=$overEmail;
		foreach($emailList as $row)
		{
			 $needChk = false;
			if($row['shopID'] !=0) $needChk = true;
			$r = 9;
			// need check 
			
			if($r!=0||!$needChk)
			{
				
				
				
				
				
					$content = '<div>'.$row['content'].'</div>';
					//$content .= '<div>--<br/>'.$row['signature'].'</div>';

					if(empty($row['headers']))
					{
						if($row['shopID']==0)$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: 瘋桌遊益智遊戲專賣店 \nReply-To:service@phantasia.tw\n";
						else $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom:瘋桌遊益智遊戲專賣店 \nReply-To:phantasia".str_pad($data['emailData']['shopID'],4,0,STR_PAD_LEFT )."@gmail.com\n";
	
						
					}
					else $headers =$row['headers']; 
/*
			$SMTP_Validator = new SMTP_validateEmail();
			$SMTP_Validator->from_domain = '"mail.phantasia.tw';
			// do the validation
			$SMTP_Validator->debug = true;
			$results = $SMTP_Validator->validate(array($row['email']), $sender);
			$times++;
			
			// send email? 
			if (!$results[$email]) {
				$this->db->where('id',$row['lID']);
						$this->db->update('pos_email_list',array('result'=>-1,'err'=>'The email addresses you entered is not valid','status'=>1,'sendTime'=>date("Y-m-d H:i:s")));
						
			  echo 'gail';
			} 
			
			*/
			
			if($times>=$oneMNum);//留給email validate
			else {
			 



			
			
			
			// 產生 Mailer 實體
			$mail = new PHPMailer();
			
			
			
			// 設定為 SMTP 方式寄信
			$mail->IsSMTP();
			
			// SMTP 伺服器的設定，以及驗證資訊
			$mail->SMTPAuth = true;      
			$mail->Host = "bear.potia.net"; //此處請填寫您的郵件伺服器位置,通常是mail.網址。如果您MX指到外地，那這邊填入www.XXX.com 即可
			$mail->Port = 465; //ServerZoo主機的郵件伺服器port為 25 
			$mail->SMTPSecure = 'ssl';
			
			// 信件內容的編碼方式       
			$mail->CharSet = "utf-8";
			
			// 信件處理的編碼方式
			$mail->Encoding = "base64";
			
			// SMTP 驗證的使用者資訊
			$mail->Username = "system@phantasia.com.tw";  // 此處為驗証電子郵件帳號,就是您在ServerZoo主機上新增的電子郵件帳號，＠後面請務必一定要打。
			$mail->Password = "p53180059!";  //此處為上方電子郵件帳號的密碼 (一定要正確不然會無法寄出)
			
			// 信件內容設定  
			$mail->From = "system@phantasia.com.tw"; //此處為寄出後收件者顯示寄件者的電子郵件 (請設成與上方驗証電子郵件一樣的位址)
			$mail->FromName = "瘋桌遊益智遊戲專賣店"; //此處為寄出後收件者顯示寄件者的名稱
			$mail->Subject = $row['subject']; //此處為寄出後收件者顯示寄件者的電子郵件標題
			$mail->Body =$content;   //信件內容 
			$mail->IsHTML(true);
			
			// 收件人
			
			$mailList = explode(',',$row['email']);
			foreach($mailList as $each)$r = $mail->AddAddress($each); //此處為收件者的電子信箱及顯示名稱
			
			 $mail->Send();
								//
			
								//@mail($row['email'],$row['subject'],$content,$headers);
								$times++;
                	$this->db->where('id',$row['lID']);
						$this->db->update('pos_email_list',array('result'=>$r,'err'=>$mail->ErrorInfo,'status'=>1,'sendTime'=>date("Y-m-d H:i:s")));
						}
						
					
						
						
						
					}
		}
		$t = getdate();
    
		
        if($t['minutes']%30==0)$this->fetch_web_order();
        
         $this->cs_order_model->mallDataTransfer();
        if($t['hours']!=13&&$t['hours']!=14&&$t['minutes']%3==0)$this->clean_buffer();
        echo 'finish';
            //有信寄的時候，看看網路有沒有訂單
	}
	
	function clean_buffer()
	{
	
		$this->db->where('finTime','0000-00-00 00:00:00');
        $this->db->order_by('inTime','ASC');
		$query = $this->db->get('pos_buffer');
		$data = $query->row_array();
	
		if(!empty($data))
		{
		$postData['bufferID'] = $data['id'];
		$this->paser->post_ignore('http://possvr.phantasia.com.tw'.$data['function'],$postData,false);	
		                                  
		
		$this->db->where('id', $data['id']);
		$this->db->update('pos_buffer',array('finTime'=>date("Y-m-d H:i:s")));
            if(strpos($data['function'],'buffer_current_amount')>0)$this->clean_buffer();
		
		}
       
	}
	
	function longfun()
	{
		$title  = '長時間運時 ajax function shipment '.$this->input->post('url');
		$content=$title .date("Y-m-d H:i:s").'<br/>時間為'.$this->input->post('t').'秒 shopID:'.$this->data['shopID'];
		if($this->input->post('url')!='/welcome/longfun')	
		{
			$this->db->insert('pos_longer',array(
			'url'=>$this->input->post('url'),
			'runTime'=>$this->input->post('t'),
			'time'=>date("Y-m-d H:i:s"),
			'shopID'=>$this->data['shopID'],
			'type'=>'shipment ajax'
			)
			
			
			);
			//if($this->input->post('t')>120)$this->Mail_model->myEmail('lintaitin@gmail.com',$title,$content,'',0,99,1);
		}

		
		echo json_encode(array('result'=>true));
	
	}
    function fetch_web_order()
    {
       $this->load->model('cs_order_model');
         $key = true;
         
        	$data['content'] = '運行';
        for($page=1;$key;$page++)
        {
            $url = $this->data['martadmDomain'].'receipt?page='.$page.'&tid=&tstatus=1&select_sumbit=1&date=&hr=0&min=0&date2=&hr2=0&min2=0&oid=&search_order=&search_select=1&search_price_select=&search_price_order=&returnWay=json';
        $r = $this->paser->post($url,array(),true);
            print_r($r);
        $key = false;
        echo $url;
         
        foreach($r['orderlist'] as $row)
        {
            
           foreach($row as $each)
           {
               
               $ret = $this->cs_order_model->newWebOrder( $each['orderid'],$each['orderdate']);
               if($ret==true) 
               {
                 
                   echo $each['orderid'].'<br/>';
                   	$data['content'] .= $each['orderid'].'<br/>';
               }
           }
              $key=true;   
            
        }
            
        }
        echo 'done';
        $data['mail'] = 'lintaitin@gmail.com';
		$data['title'] = 'fetch_web_order in welcome';
		

		$data['code'] = md5('phaMail');
        if($data['content'] !='運行')
		$this->paser->post('http://shipment.phantasia.tw/welcome/mailapi',$data,true);	
    }
    
    function allowance_invoice_test()
    {
          $this->load->model('accounting_model');
          $allowanceNum = '2019081300000002';
          

           $this->data['invoiceInf'] = $this->accounting_model->getAllowanceData($allowanceNum);
    
         $ProductArrays = json_decode( $this->data['invoiceInf']['productDetail'],true);
        
     
        $this->data['ProductArrays'] =$ProductArrays;
        $this->load->view('invoice_print_allowance',$this->data);
        
    }
    function invoice_test()
    {
        $this->load->helper('barcode');
     $this->load->helper('qrcode');
      $this->load->model('accounting_model');
        $invoiceNum = $this->uri->segment(3);
         $this->data['reprint'] = $this->uri->segment(4);
         $ex = $this->uri->segment(5);
        /*
         $this->data['invoiceInf'] = array(
             'year'           => '108',
             'period'         => '07-08',
            'InoviceDateTime' => '2019-07-22 23:59:59',
             'InvoiceCode'    => 'SV',
            'InvoiceNum'   => $invoiceNum,
            'RandomNumber'    => $this->accounting_model->generateRandomNumber($invoiceNum),
             'total'          =>  1610,
             'BuyerIdentifier'=>'00000000',
             'SellerIdentifier'=>'53180059',
             'shopName'       =>'瘋桌遊購物商城',
             'orderNum'       =>'EC8616'
         )
              $invoiceNum = '70971954';
              */
        $incoiceCode = 'SC';
       $this->data['invoiceInf'] = $this->accounting_model->getEInvoiceData($invoiceNum,$incoiceCode);
        ;
        $t = explode(' ', $this->data['invoiceInf']['InoviceDateTime']);     
        $date = explode('-',$t[0]);
        $year = $date[0]-1911;
        $month = str_pad( $date[1], 2, "0" ,STR_PAD_LEFT);  
        $day = str_pad( $date[2], 2, "0" ,STR_PAD_LEFT);  ;
        $total  =   $this->data['invoiceInf']['total'];
        $InvoiceDate = $year.$month.$day;
        $InoviceTime = str_replace(':','',$t);
        $InvoiceNumber = $this->data['invoiceInf']['InvoiceCode'].$this->data['invoiceInf']['InvoiceNum'];
        $RandomNumber = $this->data['invoiceInf']['RandomNumber'];
        $SalesAmount = 0;
        $sellTotalBtax = str_pad($SalesAmount, 8, "0" ,STR_PAD_LEFT); 
        $TaxAmount = 0;
        $TotalAmount = dechex($total);
        $sellTotalAtax = str_pad($TotalAmount, 8, "0" ,STR_PAD_LEFT);  
        
        $BuyerIdentifier = $this->data['invoiceInf']['BuyerIdentifier'];
        $RepresentIdentifier ='00000000';//電子發票證明聯二維條碼規格已不使用代表店，請填入00000000 8碼字串。
        $SellerIdentifier = $this->data['invoiceInf']['SellerIdentifier'];
      $BusinessIdentifier = '53180059';
        
        $invoicStr = $InvoiceNumber.$RandomNumber;

                    
        $AESKey  =  '6729BBE168E033597EF04CBFADC4CBC4' ;//'以字串方式記載加密金鑰之 HEX 值。'           
        $r = $this->accounting_model->getEncrypt($invoicStr, $AESKey);
        
        $qrStr = $InvoiceNumber.    
                 $InvoiceDate.
                 $RandomNumber.
                 $sellTotalBtax.
                 $sellTotalAtax.
                 $BuyerIdentifier.
                 $SellerIdentifier.
                 $r ;
        /*
        12. ProductArrays : 單項商品資訊
ProductArrays 中包含產品的陣列 (ProductArray)，此產品陣列應包含 :
i. Product Code : 以字串方式記載透過條碼槍所掃出之條碼資訊。
ii. Product Name : 以字串方式記載商品名稱。
iii. ProductQty : 以字串方式記載商品數量。
iv. ProductSaleAmount : 以字串方式載入商品銷售額 (整數未稅)，若無法分離稅項
則記載為字串0。
v. ProductTaxAmount : 以字串方式載入商品稅額(整數)，若無法分離稅項則記載為
字串0。
vi. ProductAmount : 以字串方式載入商品金額(整數含稅)。
*/      
        $ProductArrays = json_decode( $this->data['invoiceInf']['productDetail'],true);
        
        
            
        
        
        $num = count($ProductArrays);
        $totalNum = 0; $productStr ='';$productStr2 = '**'; $productStr2Key = false;
        foreach($ProductArrays as $row)
        {
            
           $totalNum+= (int)$row['Quantity'];
            
        
            $str=':'.str_replace(':','-',$row['Description']).':'.$row['Quantity'].':'.$row['Amount'];
           
            if($productStr2Key||(strlen($productStr)+strlen($str))>200)
            {
                
                 $productStr2Key = true;
                if(strlen($productStr2)+strlen($str)<250) $productStr2.= $str;
            }
            else $productStr.= $str;
            
        }
  
        $code = 2; //UTF8編碼
        
        $qrStr.=':**********:'.$num.':'.$totalNum.':'.$code.$productStr;

        $name = $year.$month.$InvoiceNumber.$RandomNumber;
            
       
        
       $bc = new Barcode39($name); 

// set text size 
$bc->barcode_text_size = 5; 

// set barcode bar thickness (thick bars) 
$bc->barcode_bar_thick = 3; 

// set barcode bar thickness (thin bars) 
$bc->barcode_bar_thin = 1; 

        
        
//$bc->barcode_use_dynamic_width = false;

	/**
	 * Barcode width (if not using dynamic width)
	 *
	 * @var int $barcode_width
	 */
//$bc->barcode_width = 200;
    
    $bc->barcode_text = false; 
        $dirMain = $_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice/'.$this->data['invoiceInf']['year'];
        if(!is_dir($dirMain)) mkdir($dirMain);
         $dirMainAll =$dirMain.'/'.$this->data['invoiceInf']['period'].'/';
        if(!is_dir($dirMainAll)) mkdir($dirMainAll);
        
        
        
        
// save barcode GIF file 
         $f =$dirMainAll.$name.'.gif';
   
        $r = $bc->draw($f);;
       
        
        $this->data['code'] = $name;
        
         $this->data['bcT'] ='http://www.barcodesinc.com/generator/image.php?code='.$name.'&style=68&type=C39&width=873&height=83&xres=3&font=3';
        
        // set BarcodeQR object 
        $this->data['qrT']=$this->googleQrcode($qrStr,$dirMainAll.'/qr_'.$name.'.png');;
        $this->data['qrT2']= $this->googleQrcode($productStr2,$dirMainAll.'/qr_'.$name.'2.png');
        
        
        /*
        $qr = new BarcodeQR(); 

    // create Text QR code 
        $qr->text($qrStr); 

// display new QR code image 
        $f =                                                  
        $qr->draw(500, $f );
        
           // create Text QR code 
        $qr->text($productStr2); 

// display new QR code image 
        $f =$dirMainAll.'/qr_'.$name.'2.png';                                                 
        $qr->draw(500, $f );
        */
        $this->data['ProductArrays'] =$ProductArrays;
       
  
      $this->load->view('invoice_print_bc',$this->data);	
        
    }
    
    
    function googleQrcode($qrStr,$f)
    {
        $len = strlen($qrStr);

                        
          if($len<100) $l = 120;
          else if($len<151)   $l = 177;
          else if($len<200) $l = 200;
         else $l = $len;
        
         $qr = new BarcodeQR(); 
      
    // create Text QR code 
        $qr->text($qrStr); 
        $qr->draw($l, $f );
        
      
        
        return $f;
       // return "http://chart.apis.google.com/chart?cht=qr&chs=".$l.'x'.$l."&chld=L|0&chl=".$qrStr;
        
        
        
    }
    function xml_void_invoice()
    {
        $this->load->model('accounting_model');
         $invoiceNum = '70971954';
         $incoiceCode = 'SV';

          $invoiceInf = $this->accounting_model->getVoidEInvoiceData($invoiceNum,$incoiceCode);
        $this->accounting_model->C0701xml($invoiceInf);
        
    }
    
    function xml_confirm()
    {
        
        $this->load->model('accounting_model');
        $invoiceNum = '70971954';
        $incoiceCode = 'SV';

        $invoiceInf = $this->accounting_model->getConfirmEInvoiceData($incoiceCode.$invoiceNum);
      //  print_r($invoiceInf);
        $this->accounting_model->A0102xml($invoiceInf);
        
    
    }
    
    function xml_cancel_invoice()
    {
        $this->load->model('accounting_model');
             $invoiceNum = '70971954';
        $incoiceCode = 'SV';

        $invoiceInf = $this->accounting_model->getCancelEInvoiceData($invoiceNum,$incoiceCode);
        $this->accounting_model->C0501xml($invoiceInf);
        
    }
    function xml_allowance_Cancel()
    {
        
         $this->load->model('accounting_model');
             $allowanceNum = '2013120400000003';
   

        $invoiceInf = $this->accounting_model->getCancelAllowanceData($allowanceNum);
        $this->accounting_model->D0501xml($invoiceInf);
        
        
        
    }
  
    
    
    function xml_allowance()
    {
        
        $this->load->model('accounting_model');

    $allowanceNum = '2013120400000003';
          

           $invoiceInf = $this->accounting_model->getAllowanceData($allowanceNum);
   
        //$users_array['Details'] = json_decode($invoiceInf['productDetail'],true);
      
        $this->accounting_model->D0401xml($invoiceInf);
    }
    
     function xml_reject()
    {
        
         $this->load->model('accounting_model');
             
        $invoiceNum = '09260100';
        $incoiceCode = 'AA';

        $invoiceInf = $this->accounting_model->getRejectData($incoiceCode.$invoiceNum);
      
        $this->accounting_model->B0601xml($invoiceInf);
        
        
        
    }
    function xml_invoice()
    {
          $this->load->model('accounting_model');
        $invoiceNum = '70971954';
        $incoiceCode = 'SV';
       $invoiceInf = $this->accounting_model->getEInvoiceData($invoiceNum,$incoiceCode);
        $this->accounting_model->C0401xml($invoiceInf);
        
        
        /*
        $invoiceInf = array(
             'year'           => '108',
             'period'         => '07-08',
            'InoviceDateTime' => '2019-07-22 23:59:59',
             'InvoiceCode'    => 'SV',
            'InvoiceNum'   => $invoiceNum,
            'RandomNumber'    => $this->accounting_model->generateRandomNumber($invoiceNum),
             'total'          =>  1610,
             'BuyerIdentifier'=>'00000000',
             'SellerIdentifier'=>'53180059',
             'shopName'       =>'瘋桌遊購物商城',
             'orderNum'       =>'EC8616',
            'DonateMark'      =>0,//0：非捐贈發票 1：捐贈發票
            'CarrierType'     =>'3J0002',//1. 手機條碼為 3J0002  2. 自然人憑證條碼為 CQ0001
            'CarrierId1'      =>'/4EB0GMQ',
               'CarrierId2'   =>'/4EB0GMQ',
               'PrintMark'    =>'Y',// N OR Y
            'NPOBAN'          =>'' //發票捐贈對象
            
         );
       */
       
    }
    
    function a0401_xml()
    {
         $this->load->model('accounting_model');
        //A0401
       
        $eInvoiceData =  $this->accounting_model->getEInvoiceData(0,0,0);
         $type ='A0401';
         $dirMain = $_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice_xml/'.date('Y-m-d');
        if(!is_dir($dirMain)) mkdir($dirMain);
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
        $sendStr = '發票建檔資訊：';
        foreach($eInvoiceData as $invoiceInf)             
              $sendStr.='<br/>'.$this->accounting_model->C0401xml($invoiceInf,$type);
        
        $sendStr.='<br/>共'.count($eInvoiceData).'張';
        mb_internal_encoding('UTF-8');
        
		$title = '幻遊天下股份有限公司(瘋桌遊)電子發票開立彙整通知【'.date('Y-m-d').'】';	
        $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
        
        $this->Mail_model->myEmail('phantasia.ac@gmail.com,lintaitin@gmail.com',$title,$sendStr,$headers);
        
    }
    
    function large_xml()
    {
        
        
        $this->load->model('accounting_model');
        //C0401
       
        $eInvoiceData =  $this->accounting_model->getEInvoiceData(0,0,0);
         $type ='C0401';
         $dirMain = $_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice_xml/'.date('Y-m-d');
        if(!is_dir($dirMain)) mkdir($dirMain);
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
          $sendStr = '發票建檔資訊：';
        foreach($eInvoiceData as $invoiceInf)             
                $sendStr.='<br/>'.$this->accounting_model->C0401xml($invoiceInf);
        
        $sendStr.='<br/>共'.count($eInvoiceData).'張';
        mb_internal_encoding('UTF-8');
        

    
        
        //C0501
         $eInvoiceData =  $this->accounting_model->getCancelEInvoiceData(0,0,0);
        $type ='C0501';
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
         $sendStr .= '<br/>發票作廢資訊：';
         foreach($eInvoiceData as $invoiceInf)             
              $this->accounting_model->C0501xml($invoiceInf);
        
          $sendStr.='<br/>共'.count($eInvoiceData).'張';
        
        //C0701
        $eInvoiceData =  $this->accounting_model-> getVoidEInvoiceData(0,0,0);
 
        $type ='C0701';
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
         foreach($eInvoiceData as $invoiceInf)             
              $this->accounting_model->C0701xml($invoiceInf);
        //D0401
        $eInvoiceData =  $this->accounting_model->getAllowanceData(0,0);
        $type ='D0401';
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
         foreach($eInvoiceData as $invoiceInf)             
              $this->accounting_model->D0401xml($invoiceInf);
        
        //D0501
        $eInvoiceData =  $this->accounting_model->getCancelAllowanceData(0,0)   ;
        $type ='D0501';
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
         foreach($eInvoiceData as $invoiceInf)             
              $this->accounting_model->D0501xml($invoiceInf);
        
      
        
        $title = '幻遊天下股份有限公司(瘋桌遊)電子發票開立彙整通知【'.date('Y-m-d').'】';	
        $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
        
        $this->Mail_model->myEmail('phantasia.ac@gmail.com,lintaitin@gmail.com',$title,$sendStr,$headers);
        
        
        if(date('m')%2==1&&date('d')>=28 ||date('m')%2==0)
        {
            
            $lm = date('m')- 2;
            $y =  date('Y');
            if($lm<=0)
            {
                $lm = 12;
                $y--;
            }
            $r = $this->accounting_model->getWinList($y,$lm);
            
            if($r===false)
            {
                $title = $y.'-'.$lm.'期 中獎發票清冊未上傳';
                
                $content =$title.'<br/> 請速通知系統管理員到 電子發票平台下載上傳，<br/>以利會計寄發客戶';
                  $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			
                $this->Mail_model->myEmail('lintaitin@gmail.com,phantasia.ac@gmail.com',$title,$content,$headers);   
                
                
            }
            else if($r['sending']==false)
            {
                
                 $title = $y.'-'.$lm.'期 中獎發票清冊未寄發客戶';
                
                $content =$title.'<br/> 請速通知會計完成中獎發票列印寄發';
                $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			
                $this->Mail_model->myEmail('lintaitin@gmail.com,phantasia.ac@gmail.com',$title,$content,$headers,0,100,1);   
                
            }
            
            
            
            
        }
        
        
        echo 'done';
    }
    
    
    function empty_invoice_xml()
    {
        $t = getdate();
        $this->load->model('accounting_model');
        $dirMain = $_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice_xml/'.date('Y-m-d');
        if(!is_dir($dirMain)) mkdir($dirMain);
        $YearMonth='10808';
        
          //E0402
        $eInvoiceData =  $this->accounting_model->getAllTrack($YearMonth);
         $type ='E0402';
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
         foreach($eInvoiceData as $invoiceInf)             
              $this->accounting_model->E0402xml($YearMonth,$invoiceInf['InvoiceTrack']) ;
        
        
    }
    function zec_search()
    {
        $orderNum = $this->input->post('orderNum');
        
           $this->db->where('orderNum',$orderNum);
            $query= $this->db->get('pos_zec_mod');
           $data['data'] = $query->row_array();
        $data['result']=true;
        		echo json_encode($data);
        
        
		exit(1);
    }
    
    function zec_invoice()  
    {
      //  $this->db->where('transportRemarks >',0);
        $this->load->model('Accounting_model');
         $q = $this->db->get('pos_einvoice');
        $p = $q->result_array();
        foreach($p as $row)
        {
        
            
            $datain = array(
                'shipmentID'=>37205,
                'invoice'=>$row['InvoiceNumber'],
                'price' =>$row['total'],
                'date'  =>$row['InoviceDateTime']
            
            );
            $this->db->insert('pos_order_invoice',$datain);
            
        }
        echo 'done';
       
        
    }
    function zec_mail()
    {
        
        $q = $this->db->get('pos_zec_mod');
        $data = $q->result_array();
        foreach($data as $row)
        {
            
            if(empty($transportData)) $transportData = '';
            $title = '墨敵賽 Vote to Decide出貨地點最後確認信-嘖嘖平台募資，瘋桌遊發貨'.$row['orderNum'];
            
            
        $emailTitle = '<img src="https://mart.phantasia.tw/images/logo.png" width="200px"><h1 style="line-height:2;">'.$title.'</h1>       
<p style="width:100%; margin-top:20px;">尊敬的客戶 '.$row['BuyName'].' ，您好!</p>'.
'<p>感謝您在嘖嘖平台與我們一同集資，桌遊 墨敵賽 Vote to decide<br/>'.
'在經過了幾個禮拜的印刷之後，我們準備於8月中旬出貨<br/>'.
'特定來信跟您確認出貨地址</p>'.
'<p>收件人：'.$row['receiver'].'<br/>電話：'.str_pad($row['phone'], 10, "0" ,STR_PAD_LEFT).'<br/>地址:'.$row['address'].'<br/></p>'.
'<div style="color:red">若您想修改收件資訊，或改為超商取件，請於2019/8/3前 透過『嘖嘖平台』站內訊息提供資訊或來信至service@phantasia.tw</div>'.            
            
            
'<p>若想了解詳細資訊,<br/>請到嘖嘖平台/<a target="_blank" href="https://www.zeczec.com/projects/vote-to-decide">查詢購買紀錄</a></p><br/>
<p>本訂單已經完成所有付款程序</p>
<p>如有問題或意見，請聯繫我們</p>
<p>感謝您的訂購!</p>

<p style="width:100%; margin-top:10px;">
[ 防詐騙提醒 ]<br/>
＊不要依指示到ATM前操作<br/>
＊接獲+字號開頭的電話勿理會<br/>
＊客服人員不會主動打電話聯絡誤設多期扣款<br/><br/>  

在您完成繳費程序之後，瘋桌遊購物商城不會以電話要求購買人操作ATM進行轉帳或<br/>
重新設定、變更付款條件及程序、要求您設定成分期付款或是購買點數，<br/>
若有接到類似電話，請立即來電(02)8671-9616向客服詢問或<br/>
透過官方網頁查詢商品購買進度。<br/><br/>

如有任何疑問，可撥打客服專線 0909-103913<br/><br/>

服務時間:<br/>
周一 ~ 周五 上午10:00 ~ 17:00<br/> <br/>

國定假日及假日非服務時間，可先透過客服信箱反應，將於服務時間內盡快回覆。</p >';
        
        $textTable = '';
        $textTableCont = '';
        $textResult = ''; 
            $product = array();
        if($row['V']>0) $product[] = array('pID'=>865
                                         ,'ZHName'=>'墨敵賽'
                                         ,'num'=>$row['V']
                                        
                                        )    ;
           if($row['cool']>0) $product[] = array('pID'=>866
                                         ,'ZHName'=>'墨敵賽:酷卡'
                                         ,'num'=>$row['cool']
                                        
                                        )    ;
           if($row['card']>0) $product[] = array('pID'=>125
                                         ,'ZHName'=>'65*90厚套(65張入)'
                                         ,'num'=>$row['card']*3
                                        
                                        )    ;
           if($row['cat']>0) $product[] = array('pID'=>867
                                         ,'ZHName'=>'墨敵賽:石虎擴充'
                                         ,'num'=>$row['cat']
                                        
                                        )    ;    
            if($row['special']>0)
            {
                
                switch($row['optionID'])
                {
                        
                        case 19081:
                        $product[] = array('pID'=>865
                                         ,'ZHName'=>'墨敵賽'
                                         ,'num'=>4
                                        
                                        )    ; 
                        $product[] = array('pID'=>866
                                         ,'ZHName'=>'墨敵賽:酷卡'
                                         ,'num'=>4
                                        
                                        );
                        break;
                        case 19082:
                        $product[] = array('pID'=>865
                                         ,'ZHName'=>'墨敵賽'
                                         ,'num'=>1
                                        
                                        )    ; 
                        $product[] = array('pID'=>36
                                         ,'ZHName'=>'炸彈競技場'
                                         ,'num'=>1
                                        
                                        );
                        $product[] = array('pID'=>867
                                         ,'ZHName'=>'墨敵賽:酷卡'
                                         ,'num'=>1
                                        
                                        );
                        break;
                        case 19085:
                        $product[] = array('pID'=>865
                                         ,'ZHName'=>'墨敵賽'
                                         ,'num'=>1
                                        
                                        )    ; 
                        $product[] = array('pID'=>36
                                         ,'ZHName'=>'炸彈競技場'
                                         ,'num'=>1
                                        
                                        );
                              $product[] = array('pID'=>115
                                         ,'ZHName'=>'巨龍峽谷'
                                         ,'num'=>1
                                        
                                        )    ; 
                        $product[] = array('pID'=>609
                                         ,'ZHName'=>'兔兔農場'
                                         ,'num'=>1
                                        
                                        );
                          $product[] = array('pID'=>182
                                         ,'ZHName'=>'牛仔很茫'
                                         ,'num'=>1
                                        
                                        );
                        $product[] = array('pID'=>866
                                         ,'ZHName'=>'墨敵賽:酷卡'
                                         ,'num'=>1
                                        
                                        );
                        break;
                        
                        
                }
                
                
                
                
            }
            
        if(!empty($product))
        {
            $textTableTitle = '<table max-width="100%" rules="all" cellpadding="10" style="border:3px #c7c5c5  dashed;padding:3px;">
            <tr>
                <td style="padding:5px; background-color: #efd7da;" colspan="5">*訂單編號:'.$row['orderNum'].'_商品清單</td>
            </tr>
            <tr style="background-color: #c7c5c5;">
                <td>商品編號</td>
                <td>商品名稱</td>
                <td style="padding:5px;">數量</td>
         
            </tr>';
            
            $num =0;
            foreach($product as $each)
               
            {
                $num += $each['num'];               
                $textTableCont = $textTableCont.'<tr><td style="padding:8px;">'.$each['pID'].'</td><td style="padding:8px;">'.$each['ZHName'].'</td><td style="padding:8px;">'.$each['num'] .'</td>
               </tr>';
            }
				               
            $textTableEnd = $textTableTitle.$textTableCont.'</table>';
            
            $textResult = $emailTitle.$textTableEnd.'<div style="font-size:150%; padding:10px 15px;">訂單一共'.$num.'件商品, <br/>總額NT$'. $row['total'] .' 元</div><a style="color:red;" target="_blank" href="https://www.zeczec.com/projects/vote-to-decide">=>詳細購物資訊請至"嘖嘖平台 [ 贊助紀錄 ] "查詢</a>';
          
            
        }
            mb_internal_encoding('UTF-8');
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';  
        echo $row['email'].'<br/>';    
        echo  $textResult.'<div style="clear:both></div>';
            $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			
            $this->Mail_model->myEmail($row['email'],$title,$textResult,$headers);    
           // exit(1);
            
            
            
        }
        
    }
    
function zec_ship_code_back()
    {
        $this->load->model('Order_model');
       
            $handle = fopen($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/zec.csv','r');
            $this->load->model('Product_model');
            while(!feof($handle))
            {
                $contents = fgets($handle,1000);

                $contents_array = explode(',',$contents);
             
                if(isset($contents_array[3])) 
                {
                      //訂單號碼
                    $id = $contents_array[3];
                    $orderNum = trim($contents_array[4]);
                    echo $id.','.$orderNum.'<br/>';
                                      // $this->db->where('id',$id);
                        $this->db->where('orderNum',$orderNum);
                        
                          $datain = array(
                           'transportRemarks'=>$contents_array[1],//新竹物流
                            'packageNumber'=>$contents_array[0]
                     
                        )  ;
                    
                          $this->db->update('pos_zec_mod',$datain);   
                   
                }
              

            }
            fclose($handle);
		  
		$result['result'] = true;
	
    
        
        
        
    }
    
    function zec()
    {
        
        $this->db->order_by('buyID');
        $q = $this->db->get('pos_zec');
        $data = $q->result_array();
        /*
        18564	墨敵賽+酷卡
        18563	墨敵賽
        19136	墨敵賽+酷卡
        19085	李小恩全套
        19082	炸彈加墨敵賽
        19083	酷卡
        19081	4套墨敵賽+4酷卡
        19135	墨敵賽
        20195	卡套3包
        20548	石虎擴充
        20645	墨敵賽+石虎+3包卡套+酷卡
        
        */
        $buyID = 0 ;
        foreach($data as $row)
        {
            
           if($buyID!=$row['buyID']) 
           {
               if($buyID!=0)
               {
                    $datain = $buyData ;
                   $datain['id'] = '';
                  // $this->db->insert('pos_zec_mod',$datain);
                   //print_r($datain);
                   $this->db->where('buyID',$datain['buyID']);
                   $this->db->update('pos_zec_mod',array('total'=>$datain['total']));
               }

               
               
               
               $buyID = $row['buyID'];
               $buyAddress = $row['address'];
               $buyData = $row;
               
                $buyData['V']= 0;
                $buyData['cool']= 0;
                $buyData['card']= 0;
                $buyData['cat']= 0;
                $buyData['special']= 0;
                $buyData['addressErr']= 0;
                $buyData['total'] = 0;               
               
           }
            if($buyAddress != $row['address'])
            {
                
                $buyData['addressErr'] = 1;
                
                
            }
            
            $buyData['total'] += $row['total'];
            switch($row['optionID'])
               {
                       
                   case 18564:
                        $buyData['V']+=1;  
                        $buyData['cool']+=1;  
                   break;   
                   case 18563:
                        $buyData['V']+=1;  
                   break;   
                    case 19136:
                        $buyData['V']+=1;  
                        $buyData['cool']+=1;  
                   break;    
                   case 19083:
                        $buyData['cool']+=1;  
                   break;
                   case 19135:
                        $buyData['V']+=1;  
                   break;   
                   case 20195:
                        $buyData['card']+=1;  
                   break; 
                    case 20548:
                        $buyData['cat']+=1;  
                   break;  
                    case 20645:
                        $buyData['V']+=1;  
                        $buyData['cool']+=1; 
                        $buyData['card']+=1; 
                       $buyData['cat']+=1; 
                   break;  
                   default:
                        $buyData['special']+=1;
                    
                   break;
                    
               }
               
            
        }
        
        
        
        
        
    }


    function exiptoShip()
    {
         $this->load->model('Product_model');
        $this->load->model('Order_model');
        $this->db->where('shopID',998);
        $this->db->where('orderTime >=','2019-09-01');
		$query = $this->db->get('pos_order');
		$orderList = $query->result_array();
		foreach($orderList as $row)
		{
			$this->Order_model->orderToShipment($row['id'],true,1);

			
		}
        
        
    /*    
         $this->load->model('Order_model');
        $this->db->where('time >','2019-09-01');
        $this->db->where('shopID',998);
        $q = $this->db->get('pos_product_sell');
        $data = $q->result_array();
        
        $checkID = 0;$result =array();
        foreach($data as $row)
        {
            
            if($checkID!=$row['checkID'])
            {
                $checkID = $row['checkID'];
                
            }
             $result[$checkID]['time'] = $row['time'];
            $result[$checkID]['product'][] = $row;
            
            
        }
        $destinationShopID = 998;
            foreach($result as $key=> $p)
            {
                $checkID = $key;
                $addressID = $this->Order_model->orderAddress($checkID,$checkID,'',$destinationShopID);
		$maxNum = $this->Order_model->getMaxOrderNum();
                $maxNum = $this->Order_model->getMaxOrderNum();
                        $orderDatain = array(
                            'status' =>1,//下好訂單
                            'shopID' =>   $destinationShopID,
                            'orderTime' =>$p['time'],
                            'orderNum' =>$maxNum+1,
                            'type'  =>0,//
                            'orderComment' => $checkID,
                            'addressID' => $addressID
                            );
                        $this->db->insert('pos_order',$orderDatain);
                        $newOrderID = $this->db->insert_id();			

                $total = 0 ;

        
			foreach( $p['product'] as $row)
			{
				$comment = '電腦自動下單';      
                $sellPrice =  $row['sellPrice'];
                
              
                
                
				//出貨品項
				 //導入order
                 
					$orderDetaildatain = array(
						'orderID'=>$newOrderID,
						'sellPrice' =>$sellPrice ,
						'buyNum' => $row['num'],
						'sellNum' => $row['num'],
						'productID' => $row['productID'],
						'comment' => $checkID
					);
					$this->db->insert('pos_order_detail',$orderDetaildatain); 
					$total += $sellPrice  *$row['num'];
			
				 //===========
            }    
    
            $this->db->where('id',$newOrderID)	;
			$this->db->update('pos_order',array('total'=>$total));
            }
        
        
      
        
      */  
        
    }

    function EC_to_AC()
    {
        
        $date =  date("Y-m-d");
         $this->load->model('Order_model');
          $this->load->model('Product_model');
         $this->load->model('Accounting_model');
           
         $this->load->model('Member_model');
        $total = 0 ;
        for($platFormID=1;$platFormID<=8;$platFormID++)
        {
                $r= $this->Order_model->getEcPlatformOrder($platFormID,2,$date,$date,'pos_ec_order.updateTime');
            foreach($r as $row)
            {
               $product =  $this->Order_model-> getOrderDetailByID($row['orderID']);
               
            
               $cash_data = $this->Accounting_model->registerIO($row['total'],0,0,1,'sales','phantasiaSystem',1,$row['orderID'],666);
        
                 echo $row['memberID'].'<br/>';
                if($platFormID!=1 || $row['memberID']==0)
                {
                    $memberID = 999999;
                }
                else $memberID = $row['memberID'];
                if($memberID == 999999) $eachBonus = 0;
                else
                {
                    
                    
                    
                    
                     $level =  $this->Member_model-> getMemberLevelByID($memberID,0);
                    $eachBonus = $level['bonus'];
                }
                
                $datain = array();
                
                foreach($product as $each)
                {
                    $purchasePrice =$this->Product_model->getAvgCost($each['productID']);
                $datain[] = array(
					'productID'  => $each['productID'],
					'memberID'  => $memberID,
					'num'       => $each['buyNum'],
					'sellPrice' => $each['sellPrice'],
					'time'      => date("Y-m-d H:i:s"),
					'checkID'	=> $cash_data['id'],
					'purchasePrice' =>$purchasePrice,
				    'sellID'      =>$each['rowID'],
                    'comment' =>$row['platformName'].$row['ECOrderNum'].' '.$each['comment'],
					'bonus' =>$eachBonus * $each['buyNum'] * $each['sellPrice']
                    
					);
                    
                    
                    
                }
                   
                    $postData['memberID'] = $memberID;

                 $postData['account'] ='phantasiaSystem';
                $postData['postStr'] = 	json_encode($datain);    
        
                    echo $this->paser->ECPost('product/pay',$postData);
                
                $total+=$row['total'];
                
                
                
                
                
                
            }
            
            
            
        }
        if($total!=0)
        $cash_data = $this->Accounting_model->registerIO(0,$total,0,2,$date.'營業額','phantasiaSystem',2,$row['orderID']+1,666);
    }
    
  function correct_invoice()
  {
      
      
      $this->db->where('depID',4);
      
      $q = $this->db->get('pos_einvoice');
      $d =$q->result_array();
      foreach($d as $row)
      {
          
          $p = json_decode($row['productDetail'],true);
          $i = 1;
          $key = false;
          foreach($p as $each)
          {
              
              if($i++>1) $key = true;
              
              
              
          }
          if($key)  
            {
                $i = 1;$record = array();
               foreach($p as $each)
            {

                    $record[] = 
                    array(
                        'Description'=> $each['Description'],
                        'Quantity'=> $each['Quantity'],
                        'UnitPrice'=> $each['UnitPrice'],
                        'Amount'=>$each['Amount'],
                        'SequenceNumber' => str_pad($i++,3,0,STR_PAD_LEFT)
                    );
                   
                   




            }
       
              
              $pstr = json_encode($record,true);
           
              
              echo $row['InvoiceNumber'].'<br/>'.$pstr.'<br/>';
              
              $this->db->where('InvoiceNumber',$row['InvoiceNumber']);
              $this->db->update('pos_einvoice',array('productDetail'=>$pstr,'xmlOut'=>0));
              
              
          }
          
          
      }
      
     
      
      
      
  }
     function tag()
      {
              $this->load->model('Product_model');
         $b1 = '8888' ; //瘋桌遊內碼一開頭
         $b2 = '00';  //00薄套 10 厚套 20有色厚套 30卡盒 40卡冊 41卡冊內頁
         $b3 = '065090'; //各品類編碼 共6碼
         //以上共12碼
         
         
         $code = $b1.$b2.$b3;
         
         $m = ($code[1]+$code[3]+$code[5]+$code[7]+$code[9]+$code[11])*3;
         /*   1)	將偶位數值相加乘3 。	7+0+2+1+5+6=21 , 21*3=63*/
         
                 
             
        /*   (2)	將奇位數值相加。4+1+0+1+0+2=8*/
         $n = ($code[0]+$code[2]+$code[4]+$code[6]+$code[8]+$code[10])*3;
         
        /*(3)	將步驟1.2中所求得的值相加，取其個位數之值。63+8=71*/
         $o = ($m+$n)%10;
 
        /*(4)	以10減去步驟3中所求得的值，即為該EAN條碼之檢查碼。*/
             
        $chkNum = 10-$o;
         $code = $code.$chkNum; 
            $this->data['code'] = $code;
         $type = $this->input->get('type');
         $purchaseID = $this->input->get('purchaseID');
          $productNum = $this->input->get('productNum');
        
 
         $this->data['page']  =  $this->input->get('page');
         $this->data['topage']  =  $this->input->get('topage');
     
         $this->data['num']  =  $this->input->get('num');
         
         if(empty($this->data['num'])) $this->data['num'] = 21;
         if( $type == 'purchaseOrder')
         {
          $this->data['product'] = $this->Product_model->getPurchaseDetailByID($purchaseID,$type);
          
         }
         else
         {
             $this->data['product'][] =  $this->Product_model->getProductByProductNum($productNum);
             
             $this->data['page'] = 1;
              $this->data['topage'] = 1;
         }
          // $this->data['topage'] = min($this->data['page'], $this->data['topage']);
          $this->load->view('tag',$this->data);	
          
      }
      
    function mart()
    {
        $t  = $this->input->get('t');
        $url = $this->data['martDomain'].$t;
        redirect($url.'?domain=phantasia');
        
        
        
        
    }
        
      function testtt()
      {
          
          $s = '{"cancel":[0],"productID":["8884732"],"num":["3"],"sellPrice":["899"],"csOrderID":6786,"usage":1,"comment":"\u5546\u57ce\u8a02\u55ae\u7de8\u865f11431\u9700\u8981\u767c\u7968\u8207\u7d71\u7de8\uff5e\u62ac\u982d\uff1a\u793e\u5718\u6cd5\u4eba\u53f0\u5317\u5e02\u5411\u65e5\u8475\u6148\u5584\u5354\u6703 \u7d71\u7de8\uff1a26335403 \u5730\u5740\uff1a\u53f0\u5317\u5e02\u677e\u5c71\u5340\u5357\u4eac\u6771","memberID":"","email":"timchen0114@gmail.com","phone":"0928493122","name":"\u8305\u6167\u9f61","title":"","IDNumber":"","discount":100,"outDate":""}';
          $postData = json_decode($s,true);
            echo    $this->paser->post($this->data['serverDomain'].'csorder/order_update',$postData,false);
             /* 
          
           echo 'ss';
         $ECOrderID = 11251;
            $memberData = $this->paser->post($this->data['martadmDomain'].'receipt/get_new_member_data',array('orderID'=>$ECOrderID),true);
         
          print_r($memberData);
          */
      }
    
    

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */