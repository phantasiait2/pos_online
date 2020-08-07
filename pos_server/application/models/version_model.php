<?php 
class Version_model extends Model {
	function Version_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function getSystemInf()
	{
		$sql = "SELECT * FROM system";
		$query = $this->db->query($sql); 
		return $query->row_array();
		
	}

	
	


}

?>
