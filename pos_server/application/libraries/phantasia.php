<?php
class Phantasia extends Controller {

	function intro()
	{
		parent::Controller();
		$this->data['logined'] =$this->login_model->loginchk(&$this->data['my_pid'],&$this->data['myid']);	
	}
	
}
