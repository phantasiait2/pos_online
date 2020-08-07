<?php
class Phantasia extends Controller {

	function Phantasia()
	{
		parent::Controller();
		$this->data['logined'] =$this->login_model->loginchk(&$this->data['my_pid'],&$this->data['myid']);	
		echo 'ddddd';
	}
	
}
?>