<?php

class Member extends POS_Controller {
	
	function Member()
	{
		parent::POS_Controller();
		$this->load->model('System_model');
		$this->load->model('Member_model');
		
		
		if($this->uri->segment(2)=='birthday'&&$this->data['shopID']!=0&&!$this->System_model->chkShop($this->input->post('shopID'),$this->input->post('licence')))
		{
			if(!$this->iframeConfirm())
			{
			
				$data['result']	 = false;
				echo json_encode($data);
				exit(1);
			}
			
		}
	}
	function booking()
	{
		
		if($this->input->post('table')==0)$table = ceil($this->input->post('bookingNumber')/6);
		else $table = $this->input->post('table');
		
		if($this->input->post('select_shop')==0) $shopID =  $this->data['shopID'];
		else $shopID = $this->input->post('select_shop');
		$datain  = array
		(
			'name'    =>$this->input->post('bookingPeople'),
			'time'    =>str_replace("T"," ",$this->input->post('datetimepicker')),
			'people'  =>$this->input->post('bookingNumber'),
			'phone'   =>$this->input->post('bookingPhone'),
			'email'   =>$this->input->post('bookingEmail'),
			'confirm' =>$this->input->post('confirm'),
			'table'   => $table,
			'shopID'  => $shopID,
			'comment' =>$this->input->post('comment'),
			'dir' =>$this->input->post('dir')
		);
		
		$this->db->insert('pos_reserve',$datain);
		$d = explode(' ',$this->input->post('datetimepicker'));
		$data['date'] = str_replace('/','-',$d[0]);
		$data['result']	 = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	
	function reserve()
	{
		
	
		$title = '［重要］來自瘋桌遊網站的訂位訊息！！'.date("Y-m-d H:i:s");
		$shopID = $this->input->post('select_shop');
		$content = '訂位人：'.$this->input->post('bookingPeople').'<br/>';
		$content .= '訂位時間：'.$this->input->post('datetimepicker').'<br/>';
		$content .= '訂位人數：'.$this->input->post('bookingNumber').'<br/>';
		$content .= '聯絡電話：'.$this->input->post('bookingPhone').'<br/>';
		$content .= '聯絡email：'.$this->input->post('bookingEmail').'<br/>';
        $content .= '備註：'.$this->input->post('bookingComment').'<br/>';
        $content .= '行銷來源：'.$this->input->post('dir').$this->input->post('dir_inf').'<br/>';
            
		
		
		$content .="請於收到信後，盡速回覆客人訂位確認訊息！<br/>";
		
		$content .="(此信由系統發出，直接回覆客人將收不到訊息，請利用上方聯絡email)";
		
		$s = $this->System_model->getShopByID($shopID);
		$this->Mail_model->myEmail($s['email'].',lintaitin@gmail.com',$title,$content);
		
		 $this->paser->post('http://possvr.phantasia.com.tw/system/delete_notifaction_temp',array('shopID'=>$shopID),true) ;
        $_POST['comment'] = $this->input->post('bookingComment');
		$this->booking();
	
		
	}
	
	
	
	function get_sale()
	{
	
		$memberID = $this->input->post('memberID');
		$shopID = $this->input->post('shopID');
		$licence = $this->input->post('licence');
		if(empty($shopID))$shopID = 0;
		if(empty($licence))$licence = 0;
		
		$data['url'] = 'http://shipment.phantasia.com.tw/sale/member/'.$shopID.'/'.$licence.'/'.substr(md5('p'.$memberID),0,5).$memberID;
		$data['result'] = true;
	
		echo json_encode($data);
		exit(1);	
	}
	
	function member_list()
	{
		
		$this->data['css'] = $this->preload->getcss('branch');
		$this->data['js'] = $this->preload->getjs('pos_member');
		$this->data['css'] = $this->preload->getcss('jquery-ui-1.8.16.custom');
		$this->data['js'] = $this->preload->getjs('jquery-ui-1.8.16.custom.min');
		
		$this->data['display'] = 'member';
		$this->load->view('template',$this->data);	
		
		
	}
	 function iframeConfirm()
	{
		$this->load->model('System_model');
		$shopID =  $this->uri->segment(3);
		$licence =  $this->uri->segment(4);
	
		if($this->System_model->chkShop($shopID,$licence))
		{
			$this->session->set_userdata('aid', 999);
			$this->session->set_userdata('shopID',  $this->uri->segment(3));
			$this->data['shopID'] = $this->uri->segment(3);
			return true;
		}
		return false;
		
	}
	function get_email_send()
	{
		$this->iframeConfirm();
		$this->data['signature'] = $this->Member_model->getSignature($this->data['shopID']);
		
		$this->load->view('member_email',$this->data);		
		
	}
	function email_preview()
	{
	
		$datain['emailData'] = $_POST;
		$this->load->view('email_preview',$datain);	
	}
	function email_confirm()
	{
		$datain = $_POST;
		$datain['shopID'] = $this->data['shopID'];
		$datain['status'] = 0; 
		$datain['time'] = date("Y-m-d H:i:s"); 
	
		$this->db->insert('pos_email',$datain);
		$id = $this->db->insert_id();
		$data['result']	 = true;
		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";

		mb_internal_encoding('UTF-8');
		$title = '來自店家編號'.$datain['shopID'].'的發信請求,['.$datain['subject'].']';
		$content = '<a href="http://shipment.phantasia.com.tw/member/get_email_preview/'.$id .'">點此觀看內容</a><br>請審核<br/>此信件由系統發出，請勿直接回覆';
		$this->Mail_model->myEmail('lintaitin@gmail.com',$title,$content,$headers);
        $data['shopID']  = $datain['shopID'];
		echo json_encode($data);
		exit(1);
		
	}
	function email_all()
	{
			$this->load->view('member_all_email');
	}
	function remote_email_send()
	{
		 $this->email_send();
		$data['result']	 = true;
		echo json_encode($data);
		exit(1);
	}
	function test()
	{
		ignore_user_abort(true);
		
		$r = $this->paser->post('http://www.phantasia.tw/booking/get_shop_space',array('shopID'=>1),true) ;
		print_r($r);
	}
	
	function email_result_post($id = 0)
	{
		ignore_user_abort(true);
		mb_internal_encoding('UTF-8');	
		
		$data['emailData']['id'] = $this->input->post('id');
		if($data['emailData']['id'] ==0) $data['emailData']['id'] = $this->uri->segment(3);
		$this->db->where('id',$data['emailData']['id'] );
		$this->db->update('pos_email',array('status'=>2));
		
		
		$data['emailData'] =  $this->Member_model->getEmailPreview($data['emailData']['id']);	

		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
		$title = '信件編號：'.$data['emailData']['id'].'-'.$data['emailData']['subject'].',已經發出' ;
		$content = '<a href="http://shipment.phantasia.com.tw/member/get_email_preview/'.$data['emailData']['id']  .'">點此觀看內容</a><br/>';
		
		$notSure = $this->Member_model->getWrongEmail($data['emailData']['id'],-1);
		$wrong = $this->Member_model->getWrongEmail($data['emailData']['id'],0);
	
		
		if(isset($wrong)&&!empty($wrong))
		{
			$content .='<h2>以下名單email錯誤，無法收到信件：</h2>';
			foreach($wrong as $row) $content.=$row['memberID'].','.$row['email'].','.$row['err'].'<br/>';
		}
		if(isset($notSure)&&!empty($notSure))
		{
			$content .='<h2>以下名單email，無法確定是否收到信件：</h2>';
			foreach($notSure as $row) $content.=$row['memberID'].','.$row['email'].','.$row['err'].'<br/>';
		}
		
		$this->db->where('id',$data['emailData']['id']);
		$this->db->update('pos_email',array('status'=>2));
		
		
		$content.='此信件由系統發出，請勿直接回覆';
		
		$this->Mail_model->myEmail('phantasia'.str_pad($data['emailData']['shopID'],4,0,STR_PAD_LEFT ).'@gmail.com,lintaitin@gmail.com',$title,$content,$headers);
		$data['email'] = 'phantasia'.str_pad($data['emailData']['shopID'],4,0,STR_PAD_LEFT ).'@gmail.com';
		$data['result']	 = true;
		echo json_encode($data);
		exit(1);
	
		
	}
	function member_expired_mail_send()
	{
		
		$data = $this->paser->post('http://possvr.phantasia.com.tw/member/member_expired',array(),true) ;
		
		
		foreach($data['memberExpired'] as $row)
		{
			
				
				$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: 瘋桌遊益智遊戲專賣店 \nReply-To:social@phantasia.tw\n";
				$this->Mail_model->myEmail($row['email'],$row['subject'],$row['content'],$headers);
				
		}
		
		
		
	}
	
	function content_email_split_send()
	{
		ignore_user_abort(true);
		ini_set('max_execution_time',360000);
		
		mb_internal_encoding('UTF-8');
		$title = mb_encode_mimeheader($this->input->post('subject'));
		$content = $this->input->post('content');
		$email = $this->input->post('email');
	
		@$this->Mail_model->myEmail($email,$title,$content,$headers);
			
		
	}
	
	
	
	function email_split_send()
	{
		ignore_user_abort(true);
		$this->load->helper('emailchk_helper');
		ini_set('max_execution_time',360000);
	
		$id = $this->input->post('id');
		$headers = $this->input->post('headers');
		$data['emailData'] =  $this->Member_model->getEmailPreview($id);	
		if($data['emailData']['shopID']==0)		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: 瘋桌遊益智遊戲專賣店 \nReply-To:social@phantasia.tw\n";
		else $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom:phantasia".str_pad($data['emailData']['shopID'],4,0,STR_PAD_LEFT )."@gmail.com\nReply-To:phantasia".str_pad($data['emailData']['shopID'],4,0,STR_PAD_LEFT )."@gmail.com\n";
		mb_internal_encoding('UTF-8');
		$title = mb_encode_mimeheader($data['emailData']['subject']);
		$content = '<div>'.$data['emailData']['content'].'</div>';
		$content .= '<div>--<br/>'.$data['emailData']['signature'].'</div>';
		$email = $this->input->post('email');
		$memberID = $this->input->post('memberID');
	
		@$this->Mail_model->myEmail($email,$title,$content,$headers,0,1);
	
		exit(1);
					
			
	
		
		
	}
	
	function email_send_list()
	{
	  $this->load->helper('emailchk_helper');
	//  print_r($_POST);
		$memberID = $this->input->post('memberID');

		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom:瘋桌遊益智遊戲專賣店\nReply-To:social@phantasia.tw\n";
		mb_internal_encoding('UTF-8');
		ini_set('max_execution_time','480000000');
		$data['emailData']['content'] = $this->input->post('content');
		$data['emailData']['signature']= $this->input->post('signature');	
			$data['emailData']['subject']= $this->input->post('subject');	
		
		
		$title = mb_encode_mimeheader($data['emailData']['subject']);
		$content = '<div>'.$data['emailData']['content'].'</div>';
		$content .= '<div>--<br/>'.$data['emailData']['signature'].'</div>';
	
		$memberID = $this->input->post('memberID');

		$member = $this->Member_model->getMemberByID($memberID,0);

		if(empty($member['email']))$r = 0;
		else $r = @$this->Member_model->emailChk($member['email']);

		if($r!=0)
		{
			
				@$this->Mail_model->myEmail($member['email'],$title,$content,$headers);
					
		}
		$data['email'] =$member['email'];
		$data['re'] = $r;
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	function get_sending_num()
    {
      
        $id = $this->input->post('id');
     
        $data['emailData'] =  $this->Member_model->getEmailPreview($id);
        $member = $this->Member_model->getMemberEmail($data['emailData']['shopID'],$data['emailData']['sex'],$data['emailData']['fromAge'],$data['emailData']['toAge']);
        
        $result['result'] =true;
        $result['totalNum'] = $member;

    
        echo json_encode($result);
		exit(1);
        
    }
    
    function get_ec_sending_num()
    {
        
        $member = $this->Member_model->getECMemberEmail(0,0);
        
        $result['totalNum'] = $member;
        $result['result'] = true;
        echo json_encode($result);
		exit(1);
        
        
    }
    
    
    function ec_email_send()
    {
        ignore_user_abort(true);
		mb_internal_encoding('UTF-8');
		ini_set('max_execution_time','480000000');
		$id = $this->input->post('id');
       
        $offset = $this->input->post('offset');
       
        
         $num = $this->input->post('num');
      
       
        $data['emailData'] =  $this->Member_model->getEmailPreview($id);
        $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom:phantasia".str_pad($data['emailData']['shopID'],4,0,STR_PAD_LEFT )."@gmail.com\nReply-To:phantasia".str_pad($data['emailData']['shopID'],4,0,STR_PAD_LEFT )."@gmail.com\n";
        
        
        $member = $this->Member_model->getECMemberEmail($offset,$num);
	   $i= 0 ;
		foreach($member as $row)
		{
			
			$i++;
			     $mailList[] = $row['email'];
	
			
		}
     $eID = $this->input->post('eID');
		$postData = array(
			'code' => md5('phaMail'),
		    'mail' =>json_encode($mailList,true),
			'title'=>$data['emailData']['subject'],
			'content'=>$data['emailData']['content'].'<div>--<br/>'.$data['emailData']['signature'].'</div>',
			'headers'=>$headers,
            'eID'    =>$eID,
            'SEID'   =>$id
			
		);
		
		
	
		$data = $this->paser->post('http://shipment.phantasia.tw/welcome/g_mailapi_with_eid/',$postData,true);
       
		$data['emailData']['content'] = '';
		$data['status']	 ='finish';
		$data['result']	 = true;
        echo json_encode($data);
		exit(1);
		
        
    }
    
    
    
    
    
	function email_send()
	{
		
		ignore_user_abort(true);
		mb_internal_encoding('UTF-8');
		ini_set('max_execution_time','480000000');
		$id = $this->input->post('id');
		//$id = 367241;
		$offset = $this->input->post('offset');
		$eID = $this->input->post('eID');
		$data['emailData'] =  $this->Member_model->getEmailPreview($id);
		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom:phantasia".str_pad($data['emailData']['shopID'],4,0,STR_PAD_LEFT )."@gmail.com\nReply-To:phantasia".str_pad($data['emailData']['shopID'],4,0,STR_PAD_LEFT )."@gmail.com\n";
	
		
		
		$this->db->where('id',$id);
		$this->db->update('pos_email',array('status'=>3));
		
	
	
		$mailData['id'] = $id;
	
		$member = $this->Member_model->getMemberEmail($data['emailData']['shopID'],$data['emailData']['sex'],$data['emailData']['fromAge'],$data['emailData']['toAge'],$this->input->post('offset'),$this->input->post('num'));
	
		
		//$member[] = array('email'=>'lintaitin@gmail.com');
		//$member[] = array('email'=>'taitin0407@gmail.com');
		//$member[] = array('email'=>'phantasia.pm@gmail.com');
		
		
		//$this->load->helper('emailchk_helper');
		$i= 0 ;
		foreach($member as $row)
		{
			//echo $row['email'].'<br/>';
			$i++;
			$mailList[] = $row['email'];
			
			
			/*
			 $this->Mail_model->myEmailWithEID( $row['email'],$mailData['id'], $row['memberID'],0);
				*/
			
		}
		
		if($i==0)
		{
			
				$data['result']	 = true;
				echo json_encode($data);
				exit(1);
			
		}
			
		$postData = array(
			'code' => md5('phaMail'),
			'mail' =>json_encode($mailList,true),
			'title'=>$data['emailData']['subject'],
			'content'=>$data['emailData']['content'].'<div>--<br/>'.$data['emailData']['signature'].'</div>',
			'headers'=>$headers,
			'eID'    =>$eID,
            'SEID'   =>$id
		);
	
        
        
        
		
		;
		 $data = $this->paser->post('http://shipment.phantasia.tw/welcome/g_mailapi_with_eid/',$postData,true);	
		
		$data['emailData']['content'] = '';
		$data['status']	 ='finish';
		$data['result']	 = true;
		echo json_encode($data);
		exit(1);
		
			
	}
	
	function email_test()
    {
        $data['emailData']['subject'] = 'ss';
        $mailList[] = 'lintaitin@gmail.com';
        $data['emailData']['content'] = 'test';
        $eID = 0;
        $headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
        
        $postData = array(
			'code' => md5('phaMail'),
			'mail' =>json_encode($mailList,true),
			'title'=>$data['emailData']['subject'],
			'content'=>$data['emailData']['content'].'<div>--<br/></div>',
			'headers'=>$headers,
			'eID'    =>$eID
		);
	
        
        	
		
		;
		$r = $this->paser->post('http://shipment.phantasia.tw/welcome/g_mailapi_with_eid/',$postData,false);	echo $r;
		
        
        
    }
	
	function email_audit_send()
	{
	
		;
		$id = $this->input->post('id');
		
		$data['emailData'] =  $this->Member_model->getEmailPreview($id);
		if($data['emailData']['type']==0)
		{
			 $datain['status'] = 2;
			 //發email
			 //$this->email_send();
		}
		else 
		{
			mb_internal_encoding('UTF-8');
			$datain['status'] = 1;
			$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			$title = mb_encode_mimeheader('信件編號：'.$data['emailData']['id'].'-'.$data['emailData']['subject'].',已經通過審核' );
		$content = '<a href="http://shipment.phantasia.com.tw/member/get_email_preview/'.$data['emailData']['id']  .'">點此觀看內容</a><br/>您可以上系統將信件發出囉<br/>此信件由系統發出，請勿直接回覆';
		
		$this->Mail_model->myEmail('phantasia'.str_pad($data['emailData']['shopID'],4,0,STR_PAD_LEFT ).'@gmail.com,lintaitin@gmail.com',$title,$content,$headers);
	
			
		}
       $this->db->where('id',$id);
		$this->db->update('pos_email',$datain);
		
		$result['result']	 = true;
		echo json_encode($result);
		exit(1);
		
	}
	
	
	function index()
	{
		$this->data['css'] = $this->preload->getcss('branch');
		$this->data['js'] = $this->preload->getjs('pos_member');
		$this->data['css'] = $this->preload->getcss('jquery-ui-1.8.16.custom');
		$this->data['js'] = $this->preload->getjs('jquery-ui-1.8.16.custom.min');
			
		
		
		$this->data['display'] = 'member';
		$this->load->view('template',$this->data);	
	}
	
	function birthday_prepare()
	{
		$shop =  $this->System_model->getShop(true);
		foreach($shop as $row)
		{
			if($row['shopID']<700)
			{
					$this->db->insert('pos_announce_check',array('shopID'=>$row['shopID'],'announceID'=>1,'confirm'=>0));	
					 $this->paser->post('http://possvr.phantasia.com.tw/system/delete_notifaction_temp',array('shopID'=>$row['shopID']),true) ;
			}
			
		}
		
	}
	
	function birthday()
	{
		$time = getdate();
		$data = $this->Member_model->getMonthMember($time['mon'])	;
		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: 瘋桌遊益智遊戲專賣店\nReply-To:social@phantasia.tw\n";

		$content =	$this->paser->post('http://www.phantasia.tw/birthday',array(),false);
ini_set('max_execution_time',360000);
				 
		  mb_internal_encoding('UTF-8');
		  foreach($data as $row)@$this->Mail_model->myEmail($row['email'],mb_encode_mimeheader("瘋桌遊祝您生日快樂！",'UTF-8'),$content,$headers);
		  
		$this->Mail_model->myEmail('lintaitin@gmail.com',mb_encode_mimeheader("瘋桌遊祝您生日快樂！",'UTF-8'),$content,$headers);

	
		
	}
	
	function get_email_preview()
	{
		$shopID = $this->data['shopID'];
	
		$id = $this->input->post('id');
		if($id==0) $id = $this->uri->segment(3);
		$datain['emailData'] =  $this->Member_model->getEmailPreview($id);
	
		$this->load->view('email_preview',$datain);	
		
	}
	function get_sending_email_list()
	{
		
		
		$shopID = $this->data['shopID'];

		$offset = $this->input->post('offset');
	
		$num =10;
		$data['emailList'] = $this->Member_model->getSendingEmailList($shopID,$offset,$num)	;
		$i = 0; 
		$num = count($data['emailList']);
		for($i=0; $i<$num;$i++)
		{
			switch($data['emailList'][$i]['sex'])	
			{
				case 0:
					$data['emailList'][$i]['sex'] ='不限';
				break;
				case 1:
					$data['emailList'][$i]['sex'] ='男性';
				break;
				case 2:
					$data['emailList'][$i]['sex'] ='女性';
				break;				
				
				
				
			};			
			switch($data['emailList'][$i]['status'])
			{
				case 0:
					$data['emailList'][$i]['status'] ='待審核';
				break;
				case 1:
					$data['emailList'][$i]['status'] ='待發送';
				break;
				case 2:
					$data['emailList'][$i]['status'] ='已發送';
				break;	
				
				case 3:
					$data['emailList'][$i]['status'] ='發送中'.$this->Member_model->getProgress($data['emailList'][$i]['id']).'%';
					
				break;	
				
				
			}
		$data['emailList'][$i]['con'] = $data['emailList'][$i]['sex'].','.$data['emailList'][$i]['fromAge'] .'~'. $data['emailList'][$i]['toAge'] ;

			
		}
		$data['result']	 = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	function active()
	{		
	
		ignore_user_abort(true);
		$id = $this->input->post('id');
		$offset = $this->input->post('offset');
		$data = $this->Member_model->getAlltheMember()	;
		
		
		
		
		$title="[瘋桌遊]2014台灣三國殺王者之戰開始報名囉！！";
		$content =	$this->paser->post('http://www.phantasia.tw/active/newsN',array(),false);
		
		if($id==0)
		{
			$datain['content'] = $content;
			$datain['subject'] = $title;
			//$datain['headers'] = $headers;
			$datain['shopID'] = 0;
			$datain['status'] = 3; 
			$datain['time'] = date("Y-m-d H:i:s"); 
	
			$this->db->insert('pos_email',$datain);
			$id = $this->db->insert_id();
				
			
			
		}
		
		
			ini_set('max_execution_time',360000);	 
		  mb_internal_encoding('UTF-8');
		  $i = 0;

		foreach($data as $row)
		{
			//echo $row['email'].'<br/>';
			$i++;
			if($offset>$i) continue;
				
			if($i-$offset>50)
			{
				sleep(5);
				$this->paser->post_ignore('http://shipment.phantasia.com.tw/member/active',array('id'=>$id,'offset'=>$offset+50)) ;
				$result['status']	 ='not end';
				$data['result']	 = true;
				echo json_encode($data);
				exit(1);
		
				
				
			}
			
			$mailData['email'] = $row['email'];
			$mailData['memberID'] = $row['memberID'];
			$mailData['id'] = $id;
			$this->paser->post_ignore('http://shipment.phantasia.com.tw/member/email_split_send',$mailData) ;
			//$this->db->insert('pos_test',array('num'=>$i));
			
		}
		sleep(10);
		$this->paser->post_ignore('http://shipment.phantasia.com.tw/member/email_result_post',$mailData) ;


		
	}
	
	
	function get_member()
	{
		$shopID = $this->input->post('shopID');
		$memberID = $this->input->post('memberID');
		$name = $this->input->post('name');
        $this->load->model('Member_model');
		$phone = $this->Member_model->phoneForm($this->input->post('phone'));
		
		
		$data['memberData'][0] = $this->Member_model->getMemberByID($memberID,$shopID);
     
		if ($data['memberData'][0]==false ||$data['memberData'][0]['memberID']==0)
		{
         
			$data['memberData'] = $this->Member_model->getMemberByNameOrPhone($name,$phone,$shopID);
			
		}
		if($data['memberData'][0]==false)$data['result'] = false;
		else
		{
			$data['result'] = true;
			$length = count($data['memberData']);
			for($i = 0 ;$i<$length ; $i++)
			{
				//shop has no privilage,so only can get some data
				$data['memberData'][$i]['isShopMember']=$this->Member_model->isShopMember($data['memberData'][$i]['memberID'],$shopID);
				if($data['memberData'][$i]['isShopMember']==false)
				{
					$data['memberData'][$i]['phone']   = "";
					$data['memberData'][$i]['address'] = "";
					$data['memberData'][$i]['email'] = "";
					$data['memberData'][$i]['birthday'] = "";	
										
				}
				//member level adjust
				if(empty($data['memberData'][$i]['levelName'])||$data['memberData'][$i]['shopID']!= $shopID){
					$data['memberData'][$i]['levelName'] = "一般會員";
				}
			}
		}
		echo json_encode($data);
		exit(1);
	}
	function member_inf_chk()
	{
		
		$memberID = $this->input->post('memberID');
		$phone = $this->input->post('phone');
		$data['result'] = $this->Member_model->chkMemberInf($memberID,$phone);
		echo json_encode($data);
		exit(1);		
		
		
	}
	
	function get_coord()
	{
		$data['coord'] = $this->Member_model->getCoord();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
	}
	
	
	function insert_coord()
	{
		$latitude = $this->input->post('latitude');
		$longitude = $this->input->post('longitude');
		$datain = array('latitude'=>$latitude,'longitude'=>$longitude);
		$this->db->insert('pos_coordinate',$datain);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
		
	}
	
	function get_all_member()
	{
		$shopID = $this->input->post('shopID');
		$data['memberData'] = $this->Member_model->getAllMember($shopID);
		if($data['memberData'][0]==false)$data['result'] = false;
		else
		{
			$data['result'] = true;
			$length = count($data['memberData']);
			for($i = 0 ;$i<$length ; $i++)
			{
				$data['memberData'][$i]['isShopMember'] = true;
				//member level adjust
				if(empty($data['memberData'][$i]['levelName'])){
					$data['memberData'][$i]['levelName'] = "一般會員";
				}
			}
			
		}
		echo json_encode($data);
		exit(1);
	}

	function quick_new_member()
	{
		$levelID =  $this->input->post('levelID');
		$licenceCode = $this->input->post('licenceCode');
		$time =getdate();
		if($this->input->post('timeStamp')==0) $timeStamp =  date("Y-m-d H:i:s");
		else $timeStamp = $this->input->post('timeStamp');
		
		
		if($this->input->post('joinTime')==0) $joinTime =  date("Y-m-d") ;
		else $joinTime = $this->input->post('joinTime');
		$data = array(
		'memberID' => $this->input->post('memberID'),
		'name'     => $this->input->post('name'),
		'joinTime'     => date("Y-m-d") ,
		'timeStamp'     => $timeStamp
		);
		$shopID = 	$this->input->post('shopID');
		$levelID = 	$this->input->post('levelID');
		if($this->Member_model->getMemberByID($data['memberID'],$shopID)==false)
		{
			$levelData['levelID'] = $levelID;
			$levelData['shopID']= $shopID;
			$levelData['memberID']= $data['memberID'];			
			if($levelID>1) 
			{
				
				$levelData['reNew'] = 1;
				$levelData['dueTime'] = date ("Y-m-d", mktime (0,0,0,$time['mon'] ,$time['mday']+30,$time['year']));

			}
			$this->db->insert('pos_shop_member',$levelData);	
			$this->db->insert('pos_pha_members',$data);
			
			
			
			$data['result'] = true;
		}
		else $data['result'] = false;
		
		echo json_encode($data);
		exit(1);		
	}
	function new_member()
	{
		$time =getdate();
		$data = array(
		'memberID' => $this->input->post('memberID'),
		'name'     => $this->input->post('name'),
		'phone'    => $this->input->post('phone'),
		'email'    => $this->input->post('email'),
		'birthday'    => $this->input->post('birthday'),
		'sex'    => $this->input->post('sex'),
		'address'    => $this->input->post('address'),
		'joinTime'     => date("Y-m-d"),
		'timeStamp'     => date("Y-m-d H:i:s")  
		);	
		$shopID =$this->input->post('shopID');
		$levelID=$this->input->post('levelID');
		if($this->Member_model->getMemberByID($data['memberID'],$shopID)==false)
		{
			$levelData['levelID'] = $levelID;
			$levelData['shopID']= $shopID;
			$levelData['memberID']= $data['memberID'];	
			$levelData['timeStamp']=date("Y-m-d H:i:s");
			if($levelID>1) 
			{
				
				$levelData['reNew'] = 1;
				$levelData['dueTime'] = date ("Y-m-d", mktime (0,0,0,$time['mon'] ,$time['mday']+30,$time['year']));

			}
			$this->db->insert('pos_shop_member',$levelData);	
			$this->db->insert('pos_pha_members',$data);
			
			
			
			$this->db->where('memberID',$memberID);
			$this->db->delete('pos_grave');
	

			
			
			$data['result'] = true;
		}
		else $data['result'] = false;
		
		echo json_encode($data);
		exit(1);			
	}
	
	
	
	
	function get_member_level()
	{
		
		$data['memberLevel'] = $this->Member_model->getMemberLevel();
 		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
	}
	
	function update_member()
	{

        $_POST['shopID']  = 0;
        $_POST['licence'] = '150e4e2633d2d5aa712b6a41fcd6ba01';
		$getData = $this->paser->post($this->data['posDomain'].'member/update_member',$_POST,true);
    
     
		if(isset($getData['result'])){
			if($getData['result'])$data['result'] = 1;	
			else $data['result'] = 0;
		}

        
		echo json_encode($data);
		exit(1);
		
	}
	function edit_level()
	{
		$postData = array(
			'memberID'       => $this->input->post('memberID'),
			'levelID'          => $this->input->post('level'),
			'shopID'         =>$this->input->post('shopID')
		);
		$wantRenew = $this->input->post('wantRenew');
		$wantRenewTimes = $this->input->post('wantRenewTimes');
		$shopMember = $this->Member_model->getMemberLevelByID($postData['memberID'],$postData['shopID']);
		$levelData['timeStamp']=date("Y-m-d H:i:s");
		if(isset($shopMember['levelID']))
		{
			
			if($postData['levelID']>1)
			{
				
				if($shopMember['levelID']==$postData['levelID'])
				{//續約
					if($wantRenew==1)
					{
						
						$time['year']=substr($shopMember['dueTime'],0,4);		
						$time['mon']=substr($shopMember['dueTime'],5,2);		
						$time['mday']=substr($shopMember['dueTime'],8);	
							
						$levelData['reNew']  = $shopMember['reNew'] +$wantRenewTimes;
						$levelData['dueTime'] = date ("Y-m-d", mktime (0,0,0,$time['mon'] ,$time['mday']+30*$wantRenewTimes,$time['year']));
						$this->db->where('memberID',$postData['memberID']);
						$this->db->where('shopID',$postData['shopID']);
						$this->db->update('pos_shop_member',$levelData);						
					}
				}
				else 
				{
					$time =getdate();
					$levelData['levelID'] =	$postData['levelID'];	
					$levelData['reNew']=$wantRenewTimes;
					$levelData['dueTime'] = date ("Y-m-d", mktime (0,0,0,$time['mon'] ,$time['mday']+30*$wantRenewTimes,$time['year']));
					$this->db->where('memberID',$postData['memberID']);
					$this->db->where('shopID',$postData['shopID']);
					$this->db->update('pos_shop_member',$levelData);						
					
				}
				
			}
		

			
		}
		else
		{
			$levelData['levelID'] =	$postData['levelID'];
			$levelData['shopID'] =	$postData['shopID'];
			$levelData['memberID'] =	$postData['memberID'];
			if($postData['levelID']>1)
			{
				$time =getdate();
				
				$levelData['reNew']=$wantRenewTimes;		
				$levelData['dueTime'] = date ("Y-m-d", mktime (0,0,0,$time['mon'] ,$time['mday']+30*$wantRenewTimes,$time['year']));
			}
			$this->db->insert('pos_shop_member',$levelData);
			
		}
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		
		
				
		
		
	}
	
	function member_delete()
	{
		$memberID =$this->input->post('memberID');
		
		$this->db->where('memberID',$memberID);
		$query =$this->db->get('pos_pha_members');
		$data = $query->result_array();
		foreach($data as $row)
		{
			$this->System_model->grave(0,$row['memberID'],$row);	
			
			
		}
		
		
		
		$this->db->where('memberID',$memberID);
		$this->db->delete('pos_bonus_change');
			
		$this->db->where('memberID',$memberID);
		$this->db->delete('pos_member_bonus');
	
		$this->db->where('memberID',$memberID);
		$this->db->delete('pos_my_member');
			
			
		$this->db->where('memberID',$memberID);
		$this->db->delete('pos_pha_members');

        $this->load->model('Search_model');
        $this->Search_model->deleteIndex($memberID,0);
	      
                
        
        
		$this->db->where('memberID',$memberID);
		$this->db->delete('pos_shop_member');						
			
			
		$this->db->where('memberID',$memberID);
		$this->db->update('pos_product_sell',array('memberID'=>999999));
		$this->paser->post('http://possvr.phantasia.com.tw/member/delete_member_temp',array('memberID'=>$memberID),true) ;
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		
	}
	function member_chenge()
	{
		$orgMemberID =$this->input->post('orgMemberID');	
		$toMemberID =$this->input->post('toMemberID');	
		
        
        
		
      
	           
		
		
		$r = $this->Member_model->getMemberByID($toMemberID,0);
		
		
		if($r==false)
		{
            
            $this->db->where('memberID',$orgMemberID);
            $query =$this->db->get('pos_pha_members');
            $data = $query->result_array();
            $this->load->model('Search_model');
            foreach($data as $row)
            {
                $this->System_model->grave(0,$row['memberID'],$row);	


            }
            $this->Search_model->deleteIndex($orgMemberID,0);
            
            
            
            
			$this->db->where('memberID',$orgMemberID);
			$this->db->update('pos_bonus_change',array('memberID'=>$toMemberID));
			
			$this->db->where('memberID',$orgMemberID);
			$this->db->update('pos_member_bonus',array('memberID'=>$toMemberID));
	
			$this->db->where('memberID',$orgMemberID);
			$this->db->update('pos_my_member',array('memberID'=>$toMemberID));
			
			
			$this->db->where('memberID',$orgMemberID);
			$this->db->update('pos_pha_members',array('memberID'=>$toMemberID,'timeStamp'=> date("Y-m-d H:i:s")));
			 $this->Search_model->updateIndex($data[0]['name'],false,$toMemberID,0);
            $this->System_model->deleteFromGrave(0,$toMemberID);
            
			$this->db->where('memberID',$orgMemberID);
			$this->db->update('pos_shop_member',array('memberID'=>$toMemberID,'timeStamp'=> date("Y-m-d H:i:s")));						
			
			
			$this->db->where('memberID',$orgMemberID);
			$this->db->update('pos_product_sell',array('memberID'=>$toMemberID));
			
			$this->paser->post('http://possvr.phantasia.com.tw/member/delete_member_temp',array('memberID'=>$orgMemberID),true) ;
			$this->paser->post('http://possvr.phantasia.com.tw/member/delete_member_temp',array('memberID'=>$toMemberID),true) ;
			
            
            
            
			$result['result'] = true;
		}
		else $result['result'] = false;
		echo json_encode($result);
		exit(1);
		
		
	}
	
    
    function transfer()
    {
        //批次轉移
        $this->db->where('shopID',32);
        $query = $this->db->get('pos_my_member');
        
        $member = $query->result_array();
        foreach($member as $row)
        {
            $this->db->insert('pos_my_member',array('shopID'=>24,'memberID'=>$row['memberID']));
            
            
            
            
        }
        echo 'done' ;
            
        
        
    }
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */