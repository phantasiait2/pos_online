<?php

class Timer extends POS_Controller {

	function Timer()
	{
		parent::POS_Controller();
		
			
	}
	
	function index()
	{
		$this->data['display'] = 'timer';
		$this->load->view('template',$this->data);	
	}
	
	function new_thing_send()
	{
		$datain['name']= $this->input->post('name');

		$datain['content']= $this->input->post('content');

		$datain['estimateTime']= $this->input->post('time');
		
		$datain['creatTime'] = date("Y-m-d H:i:s");
		$datain['status'] = 0;
		$this->db->insert('pos_timer',$datain);
		$result['result'] = true;

		echo json_encode($result);
		exit(1);
	
		
		
		
	}
	function get_to_do()
	{
		$this->load->model('Timer_model');
		$status= $this->input->post('status');

		$result['thing'] = $this->Timer_model->getToDo($status);
		$result['result'] = true;

		echo json_encode($result);
		exit(1);
	
		
		
		
	}
	
	function change_status()
	{
		$status= $this->input->post('status');
		$id= $this->input->post('id');
		$datain['status'] = $status;
		if($id == -1)$datain['endTime']=date("Y-m-d H:i:s");
		$this->db->where('id',$id);
		$this->db->update('pos_timer',$datain);
		
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		
		
	}
	
	function missoion_start()
	{
		$this->load->model('Timer_model');
		$id= $this->input->post('id');
		$this->db->where('id',$id);
		$this->db->where('startTime','0000-00-00 00:00:00');
		$this->db->update('pos_timer',array('startTime'=>date("Y-m-d H:i:s")));
		$result['thing'] = $this->Timer_model->getThing($id);
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		
	}
	
	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */