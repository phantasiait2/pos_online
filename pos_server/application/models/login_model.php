<?php 
class login_model extends Model {
	function login_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}

	function loginChk(&$aid,&$account,&$level,&$shopID)
	{
		$this->load->library('session');
		//登入判斷
		$aid = $this->session->userdata('aid');
		
        
        if(empty($aid))$k =  $this->dbSession();//for chrome iframe pb 20200313
        
		if(empty($aid))
		{
			if(isset($k['result'])&&$k['result']) 
            {
            
            $shopID = $k['shopID'];
            $aid = 0 ;
            $level = 100;
            $account = $k['account'];
            return true;
            }         
            else return false;
		} 
		else
		{
			$shopID = $this->session->userdata('shopID');
			$account = $this->session->userdata('account');
			$aid = $this->session->userdata('aid');
			$level = $this->session->userdata('level');
		
			return true;
		}

	}

    function dbSession()
    {
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
              $IP = $_SERVER["HTTP_CLIENT_IP"];
             }
             elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
              $IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
             }
             elseif(!empty($_SERVER["REMOTE_ADDR"])){
              $IP = $_SERVER["REMOTE_ADDR"];
             }
             else{
              $IP = "無法取得IP位址！";
             }
     

        $this->db->where('IP',$IP);
        $this->db->where('timeStamp >=',date("Y-m-d H:i:s",time()-3600*60));
        $q = $this->db->get('pos_ip_login');
        $d = $q->row_array();
        if( empty($d)) $r['result'] = false;
        else
        {
            $r['result'] = true;
            $r['shopID'] = $d['shopID'];
            $r['account'] = $d['account'];
            $this->db->where('id',$d['id']);
            $this->db->update('pos_ip_login',array('timeStamp'=>date("Y-m-d H:i:s")));
            
            
        }
     
        return $r;
    }

}

?>
