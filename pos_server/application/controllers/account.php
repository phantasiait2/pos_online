<?php

class Account extends POS_Controller {

	function Account()
	{
		parent::POS_Controller();
		$this->data['css'] = $this->preload->getcss('pos');
		$this->load->model('Account_model');
			
	}
	
	function index()
	{
		
		$this->data['js'] = $this->preload->getjs('pos_account');
		$this->data['accountList'] = $this->Account_model->getAllAccount($this->data['level'],$this->data['shopID']);
		$this->data['shopList'] = $this->System_model->getShop();
        $this->load->model('Product_model');
        $this->data['supplierList'] = $this->Product_model->getSuppliers();
        
       
		$this->data['display'] = 'account';
	
		$this->load->view('template',$this->data);	
	}
	function new_account()
	{
		$data['result']	 = false;
		if($this->data['level']>=50)
		{
			$account = $this->input->post('account');
			$pw = $this->input->post('pw');
			$level = $this->input->post('level');
			$shopID = $this->input->post('shopID');
			if($level>$this->data['level']) $level =$this->data['level'];
			if(!$r = $this->Account_model->getAccountByAccount($account))
			{
				$datain = array(
					'account' =>$account,
					'pw'      =>md5($pw),
					'level'   =>$level,
					'shopID'  =>$shopID 
				);
				$this->db->insert('pos_account',$datain);	
				$data['result']	 = true;
			}
			else
			{
				if($r['pw']=='delete')
				{	
					$this->db->where('id',$r['id']);
					$this->db->update('pos_account',array('pw'=>md5($pw)));
					$data['result']	 = true;
				}
				
			}
			
			
		}
		echo json_encode($data);
		exit(1);
	}
	
	function level_edit()
	{
		$data['result']	 = false;
		$aid = $this->input->post('aid');
		if($this->data['level']>=50 ||$aid==$this->data['aid'])
		{		
			
			$level = $this->input->post('level');
			$email = $this->input->post('email');	
			$shopID = $this->input->post('shopID');	
			if($level>$this->data['level']) $level =$this->data['level'];
			$this->db->where('id',$aid);
			$this->db->update('pos_account',array('level'=>$level,'email'=>$email,'shopID'=>$shopID));
			$data['result']	 = true;
		}
		echo json_encode($data);
		exit(1);
	}
	function delete()
	{
		$data['result']	 = false;
		if($this->data['level']>=50)
		{		
			$aid = $this->input->post('aid');
			$this->db->where('id',$aid);
			$this->db->update('pos_account',array('pw'=>'delete'));
			$data['result']	 = true;
		}
		echo json_encode($data);
		exit(1);;		
		
	}
	function change_pw()
	{
		$data['result']	 = false;
		$aid = $this->data['aid'];
		$old_pw = $this->input->post('old_pw');
		$new_pw = $this->input->post('new_pw');
		if($this->Account_model->chkAccount($aid,$old_pw))
		{		
			$this->db->where('id',$aid);
			$this->db->update('pos_account',array('pw'=>md5($new_pw)));
			$data['result']	 = true;
		}
		echo json_encode($data);
		exit(1);		
		
	}
	
	function get_email_function()
    {
        
        $data['emailFunction'] = $this->Account_model->getEmailFunction();
        
        $data['result']	 = true;
	
		echo json_encode($data);
		exit(1);
        
        
    }
    
    function get_email_detail()
    {
        
        $emailFunctionID = $this->input->post('emailFunctionID');
    
        $data['detail'] = $this->Account_model->getEmailFunctionDetail($emailFunctionID);      
        $data['result']	 = true;
	
		echo json_encode($data);
		exit(1); 
        
        
    }
    
    function email_detail_send()
    {
        $emailFunctionID = $this->input->post('emailFunctionID');
        $datain['content'] =  $this->input->post('content');
        $datain['function'] =  $this->input->post('function');
        
        if($emailFunctionID==0)
        {
            $this->db->insert('pos_email_function',$datain);
            $emailFunctionID = $this->db->insert_id();
        }
        else 
        {   
             $this->db->where('id',$emailFunctionID);
             $this->db->update('pos_email_function',$datain);
       
            
        }
          $data['result']	 = true;
	
		echo json_encode($data);
		exit(1);
    }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */