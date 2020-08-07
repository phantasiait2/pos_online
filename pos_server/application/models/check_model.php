<?php 
class Check_model extends Model {
	function Check_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function getCheckIN()
	{
		$sql = "SELECT * FROM checkIN WHERE checkOut is null";
		$query = $this->db->query($sql);
		return $query->result_array();
		
	}
	
	function isChecked($memberID)
	{
		

		$sql = "SELECT * FROM checkIN WHERE memberID = '$memberID' AND checkOut is null";
		$query = $this->db->query($sql);
		if($query->num_rows()==0) return false;
		else  return true;
		
		
	}
	function getSeatHoliday($shopID)
	{
		$date = date("Y-m-d");
		$this->db->where('date >=',$date);	
		$this->db->where('shopID',$shopID);	
		$query = $this->db->get('pos_reserve_holiday');
		return $query->result_array();
	}
	
	
	
	
	function reserveHolidayCheck($date,$shopID,$holiday)
	{
		$dateArray = getdate(strtotime($date));
		if($dateArray['wday']== $holiday)return false;
		else
		{
			$this->db->where('date',$date);	
			$this->db->where('shopID',$shopID);	
			$query = $this->db->get('pos_reserve_holiday');
			if($query->num_rows()>=1) return false;
			else return true;
		}
		
	}
	
	function getTimeDiff($id)
	{
	  $sql = "SELECT *, TIMEDIFF('".date("Y-m-d H:i:s")."',checkIN) as diff FROM checkIN WHERE id =$id";
	  $query = $this->db->query($sql);
	  return $query->row_array();
		
	}

	function getReserve($date,$shopID)
	{
		$ymd = explode('-',$date);
		if($shopID!=0)$this->db->where('shopID',$shopID);
		if(isset($ymd[0])&&isset($ymd[1])&&isset($ymd[2]))
		{
			$this->db->where('year(time)',$ymd[0])	;
			$this->db->where('month(time)',$ymd[1])	;
			$this->db->where('day(time)',$ymd[2])	;
	
		  $query = $this->db->get('pos_reserve');
		  return $query->result_array();
		}
		return array();
	}
	function getReserveByID($id)
	{
			$this->db->where('id',$id);
		  $query = $this->db->get('pos_reserve');
		  return $query->row_array();
	}
}

?>
