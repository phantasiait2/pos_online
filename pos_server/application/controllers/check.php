<?php

class Check extends POS_Controller {

	function Check()
	{
		parent::POS_Controller();
		$this->data['css'] = $this->preload->getcss('shipment');
		/*
		if(!$this->System_model->chkShop($this->input->post('shopID'),$this->input->post('licence')))
		{
			$data['result']	 = false;
			echo json_encode($data);
			exit(1);
			
		}
			*/
	}
	
	
	
	private function iframeConfirm()
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
			$this->session->set_userdata('shopID',  $shopID );
			$this->data['shopID'] = $shopID ;
		
	  }
		return;
		
	}
	function index()
	{
		$this->data['css'] = $this->preload->getcss('shipment');
		$this->data['display'] = 'order';
		$this->load->view('template',$this->data);	
	}
	function check_recode()
	{
		$datain = array(
		 'memberID' => $this->input->post('memberID'),
		 'name' => $this->input->post('name'),
		 'checkIN' => $this->input->post('checkIN'),
		 'checkOut' => $this->input->post('checkOut'),
		 'shopID' => $this->input->post('shopID')
		 
		);
		$this->db->insert('pos_check',$datain);
		$result['result'] =true;
		echo json_encode($result);
		exit(1);
			
		
		
	}
	function get_check_in()
	{
		$this->load->model('Check_model');
		$data = $this->Check_model->getCheckIN();
		echo json_encode($data);
		exit(1);		
		
		
	}
	
	function check_out()
	{
		$id = $this->input->post('id');
		$this->load->model('Check_model');
		$result['checkData'] = $this->Check_model->getTimeDiff($id);
		$result['result'] = true;
		echo json_encode($result);
		exit(1);	
		
	}
	function real_check_out()
	{

		$id = $this->input->post('id');
		$this->db->where('id',$id);
		$this->db->update('checkIN',array('checkOut'=>date("Y-m-d H:i:s")));
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		
	}
	function clear()
	{
		$sql ="UPDATE checkIN  set checkOut = '".date("Y-m-d H:i:s")."' WHERE checkOut is null";
		$this->db->query($sql);
		$result['result'] = true;
		echo json_encode($result);
		exit(1);		
	}
	function remain_num()
	{
		$this->load->model('Check_model');
		$date = $this->input->post('date');
		$shopID = $this->input->post('shopID');
	
				$this->load->model('system_model');
			$this->data['systemInf'] =  $this->system_model->getShopByID(	$shopID );
		//先確認公休否
		if($this->Check_model->reserveHolidayCheck($date,$shopID ,$this->data['systemInf']['holiday'])==false)$data['remain'] = -99;
		else
		{
			$r = $this->Check_model->getReserve($date,$shopID);	
			$p = 0 ; $t = 0;
			if(!empty($r))
				foreach($r as $row)
				{
					if($row['confirm']!=-1)
					{
						$p+=$row['people'];
						$t+=$row['table'];
					}
				}
	
		
			if(empty($this->data['systemInf']['tableNum']))
			{
				$this->data['totalTable']  = 0;
				$this->data['saveTable'] = 0;
			}
			else
			{		
				$table = explode(',',$this->data['systemInf']['tableNum']);
				$this->data['totalTable'] = $table[0]+$table[1]+$table[2];
				$this->data['saveTable'] = $this->data['totalTable'] - $table[3];
			}
			$dateArray = getdate(strtotime($date));
			
		
			$data['remain'] = ( (int)$this->data['saveTable'] - (int)$t ) *6;
		
		}
		//if($dateArray['wday']== $this->data['systemInf']['holiday'])
		//echo $data['remain'] ;
		$data['result'] = true;
		//print_r($data);
		echo json_encode($data);
		exit(1);	
		
	}
	
	
	function new_holiday()
	{
		$this->db->insert('pos_reserve_holiday',array('shopID'=>$this->data['shopID'],'date'=>$this->input->post('date')))	;
			$data['result'] = true;
		//print_r($data);
		echo json_encode($data);
		exit(1);
	}
	function delete_holiday()
	{	
	
		$this->db->where('date',$this->input->post('date'));	
		$this->db->where('shopID',$this->data['shopID']);	
		 $this->db->delete('pos_reserve_holiday');
			$data['result'] = true;
		//print_r($data);
		echo json_encode($data);
		exit(1);
	}
    function set_holiday()
	{	
	
		$datain['holiday']=$this->input->post('holiday');	
		$this->db->where('shopID',$this->data['shopID']);	
		 $this->db->update('pos_sub_branch',$datain);
			$data['result'] = true;
		//print_r($data);
		echo json_encode($data);
		exit(1);
	}
	
	
	function seat_holiday()
	{
		$this->iframeConfirm();
		$this->load->model('system_model');
		$this->load->model('check_model');
		$this->data['systemInf'] =  $this->system_model->getShopByID($this->data['shopID']);
		$this->data['seatHoliday'] =  $this->check_model->getSeatHoliday($this->data['shopID']);
		
	
		$this->load->view('seat_holiday',$this->data);
		
	}
	
	
	function reserve_seat()
	{
        if($this->data['logined']==false)	$this->iframeConfirm();
		$this->load->model('system_model');
		$this->data['systemInf'] =  $this->system_model->getShopByID($this->data['shopID']);
		if(empty($this->data['systemInf']['tableNum']))
		{
			$this->data['totalTable']  = '請先登入您店內的桌數';
			$this->data['saveTable'] = '請先登入您店內的桌數';
		}
		else
		{		
			$table = explode(',',$this->data['systemInf']['tableNum']);
			$this->data['totalTable'] = $table[0]+$table[1]+$table[2];
			$this->data['saveTable'] = $this->data['totalTable'] - $table[3];
		}
	
		$this->load->view('seat_reserve',$this->data);
	}
	
	function get_reserve()
	{
		
		$this->load->model('Check_model');
		$date = $this->input->post('date');
		$shopID = $this->data['shopID'];
		$data = $this->Check_model->getReserve($date,$shopID);
		$p = 0 ; $t = 0;
		foreach($data as $row)
		{
			if($row['confirm']!=-1)
			{
				$p+=$row['people'];
				$t+=$row['table'];
			}
		}
		$result['people'] = $p;
		$result['table'] = $t;
		$result['reserve'] = $data;
		$result['result'] = true;
		echo json_encode($result);
		exit(1);		
	}
	function get_brief_reserve()
	{
		$this->load->model('Check_model');
		$date = $this->input->post('date');
		$shopID = $this->data['shopID'];
		$r = explode('-',$date);
		
				$this->load->model('system_model');
		$this->data['systemInf'] =  $this->system_model->getShopByID($this->data['shopID']);

		for($i=1;$i<=31;$i++)
		{
			$date = $r[0].'-'.$r[1].'-'.$i;
			
			$result['reserve'][$date]['holiday'] = $this->Check_model->reserveHolidayCheck($date,$shopID,$this->data['systemInf']['holiday']);
			$data = $this->Check_model->getReserve($date,$shopID);
		
			$p = 0 ; $t = 0;
			foreach($data as $row)
			{
				if($row['confirm']!=-1)
				{
					$p+=$row['people'];
					$t+=$row['table'];
				}
			}
			$result['reserve'][$date]['people'] = $p;
			$result['reserve'][$date]['table'] = $t;
		}
		$result['result'] = true;
		echo json_encode($result);
		exit(1);		
	
	}
	
	
	function test()
	{
		
		$shop = $this->paser->post('http://www.phantasia.tw/booking/get_shop_space',array('shopID'=>11),true) ;	
		print_r($shop);
	}
	
	function booking_confirm_email()
	{
		$this->load->model('Check_model');
		$r = $this->Check_model->getReserveByID($this->input->post('id'));
		
		
		
		$s = $this->paser->post('http://www.phantasia.tw/booking/get_shop_space',array('shopID'=>$r['shopID']),true) ;
		
		
		$this->load->model('Mail_model');
		if(!empty($r))
		{
		$title = "您在瘋桌遊的訂位已經確認囉";
			$content='<h2>親愛'.$r['name'].' 先生/小姐 您好：</h2>'.
 					  '<p>感謝在瘋桌遊網站上訂位。<br/>'.
					  '您的訂位時間，'.$r['time'].'已經確認囉。<br/>'.
					  '店名：'.$s['shop']['name'].'<br>'.
					  '地址：'.$s['shop']['address'].'<br>'.
					  '電話：'.$s['shop']['contact'].'<br>'.
					  '營業時間'.$s['shop']['shophour'].'<br>'.
					  '<br/>'.	
					  
						'瘋桌遊益智遊戲專賣店期待您的蒞臨。<br/>'.
						'祝您 事事順心。<br/>'.
						'(此信由電腦系統直接發出，若有更動請直接電洽店家)';
						
		$this->Mail_model->myEmail($r['email'],$title,$content); 
		}
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		
		
	}
	function booking_edit()
	{
	
		$shopID = $this->data['shopID'];
		$datain  = array
		(
			
			'time'    =>$this->input->post('time'),
			'people'  =>$this->input->post('people'),
			'phone'   =>$this->input->post('phone'),
			'email'   =>$this->input->post('email'),
			'confirm' =>$this->input->post('confirm'),
			'table'   => $this->input->post('table'),
			'comment' =>$this->input->post('comment'),
		);
		if($this->input->post('confirm')==1)
		{
				$this->load->model('Check_model');
			$r = $this->Check_model->getReserveByID($this->input->post('id'));
			
			if($r['confirm']==0) $this->booking_confirm_email() ;//寄發確認信。;
		}
		$this->db->where('id',$this->input->post('id'));
		$this->db->update('pos_reserve',$datain);
		$result['result'] = true;
		echo json_encode($result);
		exit(1);		
	
		
	}
	function calendar()
	{
		$this->iframeConfirm();
		$this->load->model('system_model');
		$this->data['systemInf'] =  $this->system_model->getShopByID($this->data['shopID']);
		if(empty($this->data['systemInf']['tableNum']))
		{
			$this->data['totalTable']  = '請先登入您店內的桌數';
			$this->data['saveTable'] = '請先登入您店內的桌數';
		}
		else
		{		
			$table = explode(',',$this->data['systemInf']['tableNum']);
			$this->data['totalTable'] = $table[0]+$table[1]+$table[2];
			$this->data['saveTable'] = $this->data['totalTable'] - $table[3];
		}
	
		$this->load->view('calendar',$this->data);
	}
	function insertCalendar(){
		$this->load->model('Check_model');
		date_default_timezone_set("Asia/Taipei");
		$business = $this->input->post('business');
		$account = $this->input->post('account');
		$shopID = $this->input->post('shopID');
		$targetStr = "target".$business;
		$datepickerStr = "datetimepicker".$business;
		$reasonStr = "reason".$business;
		$thingStr ="thing".$business;
		$placeStr ="place".$business;
		$howStr = "how".$business;
		$fromStr = "from".$business;
		$target = $this->input->post($targetStr);
		$date = $this->input->post($datepickerStr);
		$reason = $this->input->post($reasonStr);
		$thing = $this->input->post($thingStr);
		$place = $this->input->post($placeStr);
		$how = $this->input->post($howStr);
		$from = $this->input->post($fromStr);
		if($business==1) $data['business']="店務";
		else if($business==2) $data['business']="活動";
		$data['account'] = $account;
		$data['shopID'] = $shopID;
		$data['target'] = $target;
		$data['finishdate'] = $date;
		$data['date'] = date('Y-m-d H:i:s');
		$data['reason'] = $reason;
		$data['thing'] = $thing;
		$data['place'] = $place;
		$data['how'] = $how;
		$data['from'] = $from;
 		$this->Check_model->insertCalendar($data);
		$data['result'] = true;
		echo json_encode($data);
	}
	function getThingNum()
	{
		$this->load->model('Check_model');
		$date = $this->input->post('date');
		$shopID = $this->data['shopID'];
		$r = explode('-',$date);
		
		$this->load->model('system_model');
		$this->data['systemInf'] =  $this->system_model->getShopByID($this->data['shopID']);

		for($i=1;$i<=31;$i++)
		{
			$date = $r[0].'-'.$r[1].'-'.$i;
			$snum = $this->Check_model->getShopNum($date,$shopID);
			$anum = $this->Check_model->getActNum($date,$shopID);
			$result['reserve'][$date]['snum'] = $snum;
			$result['reserve'][$date]['anum'] = $anum;
		}
		$result['result'] = true;
		echo json_encode($result);
		exit(1);		
	}
	function getThing()
	{
		$this->load->model('Check_model');
		$date = $this->input->post('date');
		$shopID = $this->data['shopID'];
		$data = $this->Check_model->getThing($date,$shopID);
		$result['reserve'] = $data;
		$result['result'] = true;
		echo json_encode($result);
		exit(1);		
	}
	function thingEdit()
	{
		$shopID = $this->data['shopID'];
		$datain  = array
		(
			'target' =>$this->input->post('target'),
			'reason' =>$this->input->post('reason'),
			'thing'  =>$this->input->post('thing'),
			'place'  =>$this->input->post('place'),
			'how'    =>$this->input->post('how'),
			'from'   =>$this->input->post('from'),
			'status' =>$this->input->post('status'),
		);
		$this->db->where('id',$this->input->post('id'));
		$this->db->update('pos_calendar',$datain);
		$result['result'] = true;
		echo json_encode($result);
		exit(1);		
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */