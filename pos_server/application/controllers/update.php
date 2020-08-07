<?php

class Update extends Controller {

	function Update()
	{
		parent::Controller();
		$this->load->model('Version_model');
		$this->data['systemInf'] = $this->Version_model->getSystemInf();
		if($this->data['systemInf']['version']==0.8) $this->data['systemInf']['version'] = '0_8';
		$fun = 'upgrade_'.$this->data['systemInf']['version'];
		$upgradeFun = array('0_8'=>'1_0','1_0'=>'1_2');
		if(isset($upgradeFun[$this->data['systemInf']['version']]))	$this->$fun();
		else redirect('/');
			
		
	}
	
	function upgrade_0_8()
	{
		$sql = "ALTER table system modify `version`  char(5)";
		$this->db->query($sql);
		$this->db->update('system',array('version'=>'1_0'));
		redirect('/');
		
	}
	function upgrade_1_0()
	{
		$this->db->update('system',array('version'=>'1_2'));
		$this->load->dbforge();
		$fields = array(
            'minDiscount' => array('type' => 'int')
		);
		$this->dbforge->add_column('product', $fields);	
		
		redirect('/');
		
		
	}
	
	

	

	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */