<?php

class Upload extends Controller {

	function Upload()
	{
		parent::Controller();
		redirect("/pos_server/upload/".$this->uri->segment(2));
		
	}
	
	
	

	

	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */