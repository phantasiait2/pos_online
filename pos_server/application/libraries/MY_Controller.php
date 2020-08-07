<?php
class MY_Controller extends Controller {

	function MY_Controller()
	{
		parent::Controller();
		$this->data['logined'] =$this->login_model->loginchk(&$this->data['my_pid'],&$this->data['myid']);	
	}
	
}
?>