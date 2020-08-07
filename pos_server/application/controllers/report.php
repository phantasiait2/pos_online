<?php

class Report extends POS_Controller {

	function Order()
	{
		parent::POS_Controller();
		
		
	}
	private function iframeConfirm()
	{
		$this->load->model('System_model');
		 $shopID = $this->input->get('shopID');
        $licence = $this->input->get('$licence');
        $account = $this->input->get('$account');
        
        
        
		if(empty($shopID))$shopID =  $this->uri->segment(3);
		if(empty($licence))$licence =  $this->uri->segment(4);
		if(empty($account))$account =  $this->uri->segment(5);
       
		if($this->System_model->chkShop($shopID,$licence))
		{
			$this->session->set_userdata('account',  $account);
			$this->session->set_userdata('aid', 999);
			$this->session->set_userdata('shopID',$shopID);
			$this->data['shopID'] = $shopID;
		
	  }
		return;
		
	}
	function index()
	{
		if($this->uri->segment(3)!=0)	
		{
			$this->iframeConfirm();
			$data['account'] =  $this->uri->segment(5);
			//$data['email'] = $this->uri->segment(6);
			
			
			
		}
		else
		{
			 $data['level']  = $this->data['level'];
			 $data['account']  = $this->data['account'];
			 $this->load->model('Account_model');
			 $accountData = $this->Account_model->getAccount($this->data['aid']);
			 $data['email'] =  $accountData['email'];
		}
		$data['shopID'] = $this->data['shopID'];
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<script type="text/javascript" src="/javascript/jquery.js"></script>
			<link rel="stylesheet" type="text/css" href="/style/pos.css">';
		$data['display'] = 'bug_report';
		if($this->data['shopID']==0)$this->load->view('template',$data);
		else $this->load->view('bug_report',$data);
	}
	
	function get_list()
	{
		$this->load->model('Report_model');
		$limit = $this->input->post('limit');
		$offset = $this->input->post('offset');
		$shopID= $this->data['shopID'];
		
		$data['bug_list'] = $this->Report_model->getReportList($offset,$limit,$shopID,false);
		echo json_encode($data);
		exit(1);
		
	}
	
	function problem_send()
	{
		$datain['reporter'] = $this->input->post('reporter');
		$datain['email'] = $this->input->post('email');
		for($i=1;$i<=3;$i++)
		{
			$problem[]  = array(
				'operation' => $this->input->post('operation'.$i),
				'description' => $this->input->post('description'.$i),
				'expected' => $this->input->post('expected'.$i)
			);
		}
					
		
		
		$datain['reportTime'] = date("Y-m-d H:i:s");
		$datain['shopID'] =$this->data['shopID'];
		$problemStr = "<div>回報者：".$datain['reporter']."</div>";
		foreach($problem as $row)
		{
			
			$datain['operation'] = $row['operation'];
			$datain['description'] = $row['description'];
			$datain['expected'] = $row['expected'];
			if($datain['operation']!=''||$datain['description']!=''||$datain['expected']!='' )
			{
			$this->db->insert('pos_bug_report',$datain);
			$problemStr.=
				   "<div>操作：".$row['operation']."</div>".
				   "<div>描述：".$row['description']."</div>".
				   "<div>預期：".$row['expected'] ."</div>".
				   "<div>==========================</div>";
			}
		}
		
		$mailTo = array('lintaitin@gmail.com,lintaitin@hotmail.com,social@phantasia.tw');
		$time = getdate();
		$result = '<link rel="stylesheet" type="text/css" href="http://shipment.phantasia.com.tw/style/pos.css">';
		$result .="<h1>錯誤回報</h1>";
		$result .=$problemStr;
				   
		
		
	
		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
		
		foreach($mailTo as $row)
		{
			 mb_internal_encoding('UTF-8');
			$this->Mail_model->myEmail($row,mb_encode_mimeheader('新增錯誤回報，'.$datain['reportTime'],'UTF-8') ,$result,$headers);
			

		}
		$result .='<div class="divider"></div>'.
				"<div>我們將盡快替您處理問題，感謝您的回報以及耐心等待</div>".
				"<div>瘋桌遊開發團隊敬上</div>";
		 mb_internal_encoding('UTF-8');		
		$this->Mail_model->myEmail($datain['email'] ,mb_encode_mimeheader('錯誤回報，'.$datain['reportTime']."已送出" ,'UTF-8'),$result,$headers);
		$data['result'] = true;;
		echo json_encode($data);
		exit(1);
	}
	function progress()
	{
		$this->load->model('Report_model');
		$id= $this->input->post('id');
	
		$data = $this->Report_model->getReport($id);
		
		if($data['status']==1)
		{
			$fixtime = date("Y-m-d H:i:s");
			$this->Report_model->sendMail($data,$fixtime);
			$this->db->set('fixtime',$fixtime);
		}
		
		$this->db->set('status','status+1',false);
		$this->db->where('id',$id);
		$this->db->update('pos_bug_report');
		$data['result'] = true;
		
		echo json_encode($data);
		exit(1);	
	}
	function fix()
	{
		$this->load->model('Report_model');
		$id= $this->input->post('id');
		
		$result= $this->input->post('result');
		$data = $this->Report_model->getReport($id);
		if($result==1)
		{
				$this->db->set('status',3);
				
				
				
		}
		else
		{
			
				$this->db->set('description',$data['description'].'<div style="color:red">錯誤未修正！</div>');
				$this->db->set('status',0);	
		$result = '<link rel="stylesheet" type="text/css" href="http://shipment.phantasia.com.tw/style/pos.css">';
		$result .="<h1>錯誤回報</h1>".
				   "<div>回報者：".$data['reporter']."</div>".
				   "<div>操作：".$data['operation']."</div>".
				   "<div>描述：".$data['description']."</div>".
				   "<div>預期：".$data['expected'] ."</div>";
		
		
	
			$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			$mailTo = array('lintaitin@gmail.com');
			foreach($mailTo as $row)
			{
				 mb_internal_encoding('UTF-8');

				$this->Mail_model->myEmail($row,mb_encode_mimeheader('錯誤仍存在回報，'.$data['reportTime'],'UTF-8') ,$result,$headers);
			}
			$result .='<div class="divider"></div>'.
					"<div>我們將盡快替您處理問題，感謝您的回報以及耐心等待</div>".
					"<div>瘋桌遊開發團隊敬上</div>";
			mb_internal_encoding('UTF-8');
			$this->Mail_model->myEmail($data['email'] ,mb_encode_mimeheader('錯誤回報，'.$data['reportTime']."已送出",'UTF-8') ,$result,$headers);
				
		}
		$this->db->where('id',$id);
		$this->db->update('pos_bug_report');
		$data['result']	 = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	function auto_send()
	{
		$this->load->model('Report_model');
		$data = $this->Report_model->getReportList(0,1000,0,true);
		foreach($data as $row)
		{
			$this->Report_model->sendMail($row,date("Y-m-d H:i:s"));
		}
	}
	
	function err_report()
	{
		$err = $this->input->post('err');
		$shopID = $this->input->post('shopID');
		$datain = array(
			'err' =>$err,
			'shopID' => $shopID,
			'time' => date("Y-m-d H:i:s")
		);
		$this->db->insert('pos_err_report',$datain);
		$data['result']	 = true;
		echo json_encode($data);
		exit(1);		
		
	}
	function err_delete()
	{
		$id = $this->input->post('id');
		$this->db->where('id',$id);
		$this->db->delete('pos_err_report');
		$data['result']	 = true;
		echo json_encode($data);
		exit(1);		
		
		
	}
	
	
	function get_err_list()
	{
		if($this->data['shopID']==0);
		$this->load->model('Report_model');	
		$data['errList'] = $this->Report_model->getErrList();
		$data['result']	 = true;
		echo json_encode($data);
		exit(1);	}
	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */