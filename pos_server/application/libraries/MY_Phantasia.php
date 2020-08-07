<?php
class MY_Phantasia extends Controller {

	function MY_Phantasia()
	{
		parent::Controller();
		$this->data['logined'] =$this->login_model->loginchk(&$this->data['my_pid'],&$this->data['myid']);	
	}
	
}
?>