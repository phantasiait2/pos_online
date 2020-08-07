<?php 

class mail_model extends Model {
	function Mail_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function myEmailWithEID($email,$eID,$memberID = 0,$priority=99)
	{
		if($email)
		{
			while(strpos($email,',,',0))$email = str_replace(',,',',',$email);
			$datain['email'] = $email;
			$datain['eID'] = $eID;
			$datain['memberID'] = $memberID;
			$datain['priority'] = $priority;
               $re = explode(',',$email);
            $receiverNum = count($re);
            $datain['receiverNum'] = $receiverNum;
			
	
			$this->db->insert('pos_email_list',$datain);
		}
	}
	
	
	
	function myEmail($email,$title,$content,$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: social@phantasia.tw\nReply-To:social@phantasia.tw\n",$memberID = 0,$priority =99,$appellation=0,$back = false
)
	{
		
		
		if($appellation==1)
		{
			$title = '<系統通知>'.$title;
			$content='<h3>您好：<h3>'.	
						$content.
				'<h3>祝 順利  (此為系統發信)</h3>';
			
		}
		
		
		$postData = array(
			'code' => md5('phaMail'),
			'mail' =>$email,
			'title'=>$title,
			'content'=>$content,
			'headers'=>$headers,
			'memberID'=>$memberID
		);
		if($back)$this->paser->post('http://shipment.phantasia.tw/welcome/mailapi/',$postData,true);	
		else
		{
			$eID = $this->creatNewMailContent($title,$content,$headers);
			$this->myEmailWithEID($email,$eID,$memberID,$priority);
		}
	}
	
	function groupEmail($email,$title,$content,	$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: social@phantasia.tw\nReply-To:social@phantasia.tw\n",$memberID = 0,$priority =99)
	{
	
        
        
		$eID = $this->creatNewMailContent($title,$content,$headers);
		if(is_array($email))foreach($email as $row) 	$this->myEmailWithEID($row,$eID,$memberID,$priority);
		else $this->myEmailWithEID($email,$eID,$memberID,$priority);

		
	}
	
	
	
	function creatNewMailContent($title,$content,$header='')
	{
		if(empty($header)||$header=='')	$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: social@phantasia.tw\nReply-To:phant@phantasia.tw\n";
		$datain['subject'] = $title;
		$datain['headers'] = $header;
		$datain['content'] = $content;
		$datain['status'] = 2;
		$this->db->insert('pos_email',$datain);
		return $this->db->insert_id();
		
	}
	function getContent($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('pos_email_content');
		return $query->row_array();
		
	}
	
	
	function sendMail($email,$var)
	{
		
		if(isset($var['id']) && $var['id']!=0)	$id =$var['id']; 
		else 
		{
			if(isset($var['header']))	$id = $this->creatNewMailContent($var['title'],$var['content']);
			else $id = $this->creatNewMailContent($var['title'],$var['content'],$var['header']);
			
		}
		if($email)
		$datain['email'] = $email;
		$datain['eID'] = $id;
		$datain['inTime'] = date("Y-m-d H:i:s");
		if(isset($var['mail']))$datain['memberID'] = $var['memberID'];
		
		/*未完成   插入 */
		$this->db->insert('pos_email_wrong',$datain);

		
	}
	
      function emailNumChk()
    {
        $this->db->select('sum(receiverNum) as total');
        $this->db->where('sendTime >=', date("Y-m-d H:i:s",time() - (60 * 60) ));
        $query = $this->db->get('pos_email_list');
        $data = $query->row_array();
          
        if($data['total']>195)
        {
              $this->myEmail('lintaitin@gmail.com','Phantasia email超量警示',date("Y-m-d H:i:s").'數量'.$data['total'], "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: social@phantasia.tw\nReply-To:phant@phantasia.tw\n", 0,999,1,true);
             return 3;
        }
        else if($data['total']>170) return 2;
        else if($data['total']>150) return 1;
          
        return 0;                 
        
    }
	function getEmail($num)
	{
		
		$this->db->select('*,pos_email_list.id as lID');
		$this->db->where('pos_email_list.status',0);
		$this->db->order_by('priority','desc');
		$this->db->order_by('pos_email_list.id','asc');
		$this->db->limit($num);
		$this->db->join('pos_email','pos_email.id = pos_email_list.eID','left');
		$query = $this->db->get('pos_email_list');
		return $query->result_array();
		
	}
	
	
	
	
	
	function emailChk($newemail)
	{
			
 
		$validator = new email_validation_class;
		$validator->timeout = 1;
 
		if(isset($newemail) && strcmp($newemail,"")) {
		    if(($result = $validator->ValidateEmailBox($newemail))<0) {
        	//echo "不能确定您的信箱是否正确. 您的信箱离这里太远了吧?";
	        return -1;
    		} else {
	        if(!$result) {
    	      //  echo "您输入的信箱地址是不正确的! :)";
				return 0;
;
        	    return;
	        } else {
//	        echo "邮箱合法!";
				return 1;
    
        	}
	    }
		} else {
	//    echo '郵箱地址錯誤';
			return 0;
;	
}		
		
		
	}
	
	
	
	
}

?>