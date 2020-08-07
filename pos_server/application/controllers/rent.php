<?php

class Rent extends POS_Controller {

	function Rent()
	{
		parent::POS_Controller();
		$this->data['css'] = $this->preload->getcss('pos');
		$this->data['js'] = $this->preload->getjs('barcode');
		$this->load->model('Product_model');
			
	}
	
	
	function index()
	{

		
		$this->data['js'] = $this->preload->getjs('jquery.tablesorter');
		$this->data['js'] = $this->preload->getjs('jquery.fixedheadertable');
		$this->data['js'] = $this->preload->getjs('pos_product');
		$this->data['js'] = $this->preload->getjs('pos_product_query');
		$this->data['display'] = 'product';
		$this->load->view('template',$this->data);	
	}
	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */