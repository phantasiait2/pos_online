<?php

class Supplier extends POS_Controller {

	function Supplier()
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
		$this->data['display'] = 'account';
	
		$this->load->view('template',$this->data);	
	}
	
    
    function product()
    {
         $this->data['css'] = $this->preload->getcss('jquery-ui-1.8.16.custom');
		$this->data['js'] = $this->preload->getjs('jquery-ui-1.8.16.custom.min');
		$this->data['js'] = $this->preload->getjs('jquery.tablesorter');
		$this->data['js'] = $this->preload->getjs('jquery.fixedheadertable');
		$this->data['js'] = $this->preload->getjs('pos_product');
		$this->data['js'] = $this->preload->getjs('pos_product_query');
		$this->data['js'] = $this->preload->getjs('pos_discount');
        $this->data['display'] = 'supplier_product';
        $this->load->view('template',$this->data);	
    
        
    }
    function edit_supplier_pd_send()
    {
        
        
        $datain['content'] = json_encode($_POST);
        $datain['productID'] = $this->input->post('productID');
        $datain['time'] = date('Y-m-d H:i:s');
        $this->db->where('productID', $datain['productID']);
         $this->db->delete('pos_product_temp');
        
        
        $this->db->insert('pos_product_temp',$datain);
        $result['result'] = true;
        echo json_encode($result);
		exit(1);
        
    }
    
    function get_supplier_product()
    {
        
     $datain['productID'] = $this->input->post('productID');
        $datain['time'] = date('Y-m-d H:i:s');
        $this->db->where('productID', $datain['productID']); 
        $q =$this->db->get('pos_product_temp');
        $re = $q->row_array();
        $result['result'] = true;
        if(empty($re )) $result['result'] = false;
        else
        {
           $result['product'] = json_decode($re['content']);
            
            
        }
        
        
        echo json_encode($result);
		exit(1);
        
    }
    
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */