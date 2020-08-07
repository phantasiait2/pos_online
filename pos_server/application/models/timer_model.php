<?php
class Timer_model extends Model {
	function Timer_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}

	function getToDo($status)
	{
		
		$this->db->where('status',$status);
		$query = $this->db->get('pos_timer');
		return $query->result_array();
	}
	
	function getThing($id)
	{
			
		$this->db->where('id',$id);
		$query = $this->db->get('pos_timer');
		return $query->row_array();
		
		
	}
	
}

?>