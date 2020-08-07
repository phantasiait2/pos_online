<?php
class POS_Controller extends Controller {

	function POS_Controller()
	{
		parent::Controller();
		date_default_timezone_set("Asia/Taipei");
		//===========
	      parse_str(substr(strstr($_SERVER['REQUEST_URI'], '?', false),1) ,$_GET);
        // GET 的處理 http://stackoverflow.com/questions/14046884/codeigniter-this-input-get-not-working  ans3
		
		
		$this->data['version'] = '1_5';
		$this->data['serverDomain'] = 'http://shipment.phantasia.com.tw/';
		$this->data['posDomain'] = 'http://possvr.phantasia.com.tw/';
        $this->data['martadmDomain']  = 'https://martadm.phantasia.tw/';
         $this->data['martDomain']  = 'https://mart.phantasia.tw/';
        $this->data['website']  = 'https://www.phantasia.tw/';
		$this->data['version'] = '1_5';
		$this->data['shopID'] = 0;
		$this->data['level'] = 100;
		$this->load->model('System_model');
		$this->load->model('login_model');
		$this->load->model('Mail_model');
		
		$this->data['logined']  = false;
        
			
		$controller = array('welcome','sale','order','csorder','race');
		$function = array('index','send_mail','get_day_report','get_month_check','month_check_auto_send','check_month_profit',
		   'auto_send','attach_consignment','get_sell_info','sanguosha','sanguosha_check_id','birthday','purchase_msg','ten_day_report',
			  'ted_day_mail','fax','get_email_send','purchase_send','email_result_post','email_split_send','email_send','active',
				'show_consignment_product','member_expired_mail_send','auto_shut_down','content_email_split_send','month_check_prepare',
				'get_not_top_product','get_top_product','stock_agent','stock_agent_mail','week_check_auto_send','each_shop_report_send_mail',
				'get_each_day_report','holding','change_pretime','reserve','reserve_seat','remain_num','booking_confirm_email','birthday_prepare','boa_ok','auto_set_fin',
				'purchase_send','purchase_order_dump_finish','shipment_to_out','purchase_order_send','purchance_change_inf','problem_ask_send','reply','predit_notify','send_mail_prepare','consignment_repair','web_shop_order','malldata','order_update','cs_order_ready_all','problem_ask_api','auto_order_sender','auto_order','three_way_merage','get_new_member_data','product_flow_update','sell_total_num_parse','get_sell_num'
				,'product_io_correction','edit_invoice','best30','best30_detail','get_month_verify','send_order_IO','get_order_IO','show_invoice','show_invoice_detail','invoice_generate','get_einvoice_inf','get_invoice_detail','invalid_invoice','get_consignment','get_all_consignment_report','member_bonus_notify'
				);
				
		$passkey = false;		
		foreach($controller as $row)
			if($this->uri->segment(1)==$row)$passkey = true;	
		
		foreach($function as $row)
			if($this->uri->segment(2)==$row)$passkey = true;		
				
        			
			// $this->db->insert('pos_test',array('content'=>json_encode($this->data).json_encode($_POST)));	
				

		if(!$passkey)			   
		{
			
			if($this->System_model-> chkShop($this->input->post('shopID'),$this->input->post('licence'))==true) 
			{
				
				$this->data['shopID'] = $this->input->post('shopID');
                
			}
			else if($this->data['logined'] = $this->login_model->loginChk($this->data['aid'],$this->data['account'],$this->data['level'],$this->data['shopID'])==false) 
			{
				
                redirect('/welcome/login');
			}
		//	echo $this->data['shopID'];
			
		}
     	else
		{
			$this->data['account'] = 'sys';
			 $this->data['logined'] = $this->login_model->loginChk($this->data['aid'],$this->data['account'],$this->data['level'],$this->data['shopID']);
		}
		
		$date = getdate();
		$file = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/log/log'.$date['mon'];
		$f = fopen($file,'a+');
		$url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3);
		$in = $this->data;
		$in['post'] = $_POST;
		fprintf($f,"%s\n",date("Y-m-d H:i:s").' '.$url.json_encode($in));
		fclose($f);	
		//echo 'system restarting please wait';
		//exit(1);
		
		//$this->log_model->record($this->data['my_pid']);
		//$this->log_model->onlineUpdate($this->data['my_pid']);
		// $this->db->insert('pos_test',array('content'=>json_encode($this->data).json_encode($_POST)));
		//錯誤檢測代碼
		if(isset($this->data['account']))	$this->PO_model->errDetect($this->data['account']);
		
		
        if(isset($_GET['GTP'] ) && $_GET['GTP'] == 1) $_POST = $_GET;
        
        
	}
	
}
?>