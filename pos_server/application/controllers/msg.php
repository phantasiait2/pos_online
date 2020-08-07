<?php

class Msg extends POS_Controller {

	function Msg()
	{
		parent::POS_Controller();
		
		
	}
	
	function index()
	{
		$this->load->model('Msg_model');
		$this->load->model('System_model');
		$data['js'] = $this->preload->getjs('date_format');
			$this->data['css'] = $this->preload->getcss('jquery-ui-1.8.16.custom');
		$this->data['js'] = $this->preload->getjs('jquery-ui-1.8.16.custom.min');
		$this->data['shopList'] = $this->System_model->getShop(true);;
		$this->data['msg'] = $this->Msg_model->getMsg(0);

		////$this->data['workList'] = $this->Msg_model->getWorkList();
	    $this->data['workList'] = array();
		$this->data['display'] = 'msg';
		$this->load->view('template',$this->data);

		
	}
	function insert_announce()
    {
        $this->load->model('System_model');
        $this->db->insert('pos_announce',array('title'=>$this->input->post('title'),'content'=>$this->input->post('content'),'source'=>1,'time'=>date("Y-m-d H:i:s")));
		$announceID = $this->db->insert_id();
        $result = '各位好：<br/>'.$this->input->post('content').'</div>';
		$result .="<br/>幻遊天下股份有限公司 敬上 (此為系統自動發送訊息)";
		$email = $this->input->post('email');
		
        $mailTo[]= array('lintaitin@gmail.com');
 
		foreach($this->input->post('shop') as $index=>$row)
		{
           
			$shopID = $index;
			$this->db->insert('pos_announce_check',array('announceID'=>$announceID,'shopID'=>$shopID,'announceTime'=>date("Y-m-d H:i:s") ));

            if($email==1)
            {
               $shopData = $this->System_model->getShopByID($shopID);
                 $mailTo[] = $shopData['email'];
            }
        
           
		}
        foreach($mailTo as $row)
			{
			mb_internal_encoding('UTF-8');
			$this->Mail_model->myEmail($mailTo ,$this->input->post('title').date("Y-m-d H:i:s") ,$result);	
			}
		
		
			
			
			
		
       
	$data['result'] = true;
		echo json_encode($data);
		exit(1);
		                 
                          
    }
	
	function insert_work()
	{
		$this->db->insert('pos_work_list',array('title'=>$this->input->post('title'),'content'=>$this->input->post('content')));
		$WID = $this->db->insert_id();
		
		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phantasia0000@gmail.com\n";
		$result = '各位好：<br/>'.$this->input->post('content').'</div>';
		$result .="<br/>幻遊天下股份有限公司 敬上 (此為系統自動發送訊息)";
		$email = $this->input->post('email');
		$mailTo = 'lintaitin@gmail.com,phoenickimo@hotmail.com,phantasia0000@gmail.com';
		foreach($this->input->post('shop') as $index=>$row)
		{
			$shopID = $index;
			$this->db->insert('pos_work_list_map',array('WID'=>$WID,'shopID'=>$shopID,'dueDay'=>$this->input->post('dueDay')));
			$mailTo .= ','.$row['email'];
		}
		if($email==1)
		{
			mb_internal_encoding('UTF-8');
			$this->Mail_model->myEmail($mailTo ,mb_encode_mimeheader($this->input->post('title').date("Y-m-d H:i:s"),'UTF-8') ,$result,$headers);		
			
		}
	$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	function insert_msg()
	{
		$this->load->model('Msg_model');
		$title = $this->input->post('title');
		$content = $this->input->post('content');
		$show = $this->input->post('show');
		$email = $this->input->post('email');
		$this->Msg_model->insert($title,$content,$show);
		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phantasia0000@gmail.com\n";
		$result = '各位好：<br/>'.$content.'</div>';
		$result .="<br/>幻遊天下 產品部 敬上 (此為系統自動發送訊息)";
		if($email==1)
		{
			$mailTo = array('lintaitin@gmail.com','phoenickimo@hotmail.com','phantasia0000@gmail.com');
			
			$shopList = $this->System_model->getShop();
			foreach($shopList as $row)
			{
				$mailTo[] = $row['email'];
			}
			
			foreach($mailTo as $row)
			{
			mb_internal_encoding('UTF-8');
			$this->Mail_model->myEmail($row,mb_encode_mimeheader($title.date("Y-m-d H:i:s"),'UTF-8') ,nl2br($result),$headers);		
			}
		}
		
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	function update_msg()
	{
	
		$this->load->model('Msg_model');
		$id = $this->input->post('id');
		$content = $this->input->post('content');
		$show = $this->input->post('show');
		$order = $this->input->post('order');
		$this->db->where('id',$id);
		$this->db->update('pos_msg',array('msg'=>$content,'show'=>$show,'order'=>$order));
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}	
	function update_work()
	{
	
		$this->load->model('Msg_model');
		$WID = $this->input->post('WID');
		$content = $this->input->post('content');
		$dueDay = $this->input->post('dueDay');
		$title = $this->input->post('title');
		$this->db->where('WID',$WID);
		$this->db->update('pos_work_list',array('content'=>$content,'title'=>$title));
		$this->db->where('WID',$WID);
		$this->db->update('pos_work_list_map',array('dueDay'=>$dueDay));

		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}	
	
	
	function work_finish()
	{
		$this->load->model('Msg_model');
		$WSID = $this->input->post('WSID');
		
		$this->db->where('WSID',$WSID);
		$this->db->update('pos_work_list_map',array('status'=>1));
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}	
	function delete_msg()
	{
		$this->load->model('Msg_model');
		$id = $this->input->post('id');
		$this->db->where('id',$id);
		$this->db->delete('pos_msg');
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}	
	
	function delete_work()
	{
		$this->load->model('Msg_model');
		$id = $this->input->post('id');
		$this->db->where('WID',$id);
		$this->db->delete('pos_work');
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */