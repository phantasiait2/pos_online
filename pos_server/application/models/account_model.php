<?php 
class Account_model extends Model {
	function Account_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function getAllAccount($level,$shopID)
	{
		$this->db->select('id as aid,account,level,email,shopID');
		if($shopID!=0)$this->db->where('shopID',$shopID);
		$this->db->where('level <=',$level);
		$this->db->where('pw !=','delete');
        $this->db->order_by('level','ASC');
		$this->db->order_by('shopID','ASC');
		$query = $this->db->get('pos_account');
		return $query->result_array();
	}
	
	function getAccount($aid)
	{
		$this->db->where('id',$aid);
		$query = $this->db->get('pos_account');
		return $query->row_array();
			
	}
	function chkAccount($aid,$pw)
	{
		$this->db->where('id',$aid);
		$this->db->where('pw',md5($pw));
		$query = $this->db->get('pos_account');
		if($query->num_rows()>=1)return $query->row_array();
		else return false;
			
	}
	
	function getAccountByAccount($account)
	{
		$this->db->where('account',$account);
		$query = $this->db->get('pos_account');
		if($query->num_rows()>=1)return $query->row_array();
		else return false;
			
	}
    
    function getEmailFunction()
    {
     
        $query = $this->db->get('pos_email_function');
        $data = $query->result_array();
        
        return $data;
        
    }
    
    
    function getEmailFunctionDetail($emailFunctionID)
    {
        $this->db->where('id',$emailFunctionID);
        $query = $this->db->get('pos_email_function');
        $data = $query->row_array();
        
        return $data;
    
    }

}

?>
