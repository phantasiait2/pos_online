<?php

class Active extends POS_Controller {

	function Active()
	{
		parent::POS_Controller();
		
			
	}
	
	function index()
	{
		$this->data['display'] = 'active';
		$this->load->view('active',$this->data);	
	}
	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */