<?php 
class Msg_model extends Model {
	function Msg_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}

	function insert($title,$content,$show)
	{
		$msg = '';
		if($title!='')$msg = $title.':';
		$msg.=$content;
		
		$datain = array(
			'msg'=> $msg,
			'show'=>$show
		);
		
		
		$this->db->insert('pos_msg',$datain);
			
		
		
	}
	
	function getMsg($type)
	{
		if($type==1)$this->db->where('show',1);
		$query = $this->db->get('pos_msg');
		return $query->result_array();
		
		
	}
	function getWorkList()
	{
		$query = $this->db->get('pos_work_list');
		$data = $query->result_array();
		foreach($data as $row)
		{
			$this->db->where('WID',$row['WID']);
			$this->db->where('status',0);
			$query = $this->db->get('pos_work_list_map');
			$shop = $query->result_array();
			if(!empty($shop))
			{
				$this->db->where('WID',$row['WID']);
				$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_work_list_map.shopID','left');
				$query = $this->db->get('pos_work_list_map');
				$shop = $query->result_array();
					$row['shop'] = $shop;
				$result[] = $row;
				
			}
			
		}
		return $result;
		
		
	}
	
}

?>
