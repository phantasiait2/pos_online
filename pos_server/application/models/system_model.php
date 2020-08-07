<?php 
class System_model extends Model {
	function System_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function chkShop($shopID,$licence,$account='')
	{
		if(empty($licence))return false;
		$this->db->where('shopID',$shopID);
		$this->db->where('licenceCode',$licence);
		$query = $this->db->get('pos_sub_branch');
		if($query->num_rows()>=1) 
		{
             //$IP = $_SERVER["HTTP_CLIENT_IP"];
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


             $datain['IP']  = $IP;
             $datain['timeStamp'] =date("Y-m-d H:i:s");
             $datain['shopID'] = $shopID;
            if($account=='') $datain['account'] = 'sys';
            else $datain['account'] = $account;
            
             $this->db->where('shopID',$shopID); 
             $q = $this->db->get('pos_ip_login');
             $d = $q->row_array();
             if(empty($d))
             {
                 $this->db->insert('pos_ip_login',$datain);
                 
             }
             else
             {
                 $this->db->where('shopID',$shopID); 
                 $this->db->update('pos_ip_login',$datain);
                 
             }
            
            
			
			return true;
		}
		else return false;
		
		
	}
	function getShopByID($shopID)
	{
		
		$this->db->where('pos_sub_branch.shopID',$shopID);
		$this->db->join('pos_sub_branch_inf','pos_sub_branch_inf.shopID= pos_sub_branch.shopID','left');
		$query= $this->db->get('pos_sub_branch');
		return $query->row_array()	;
		
	}
	function getDirectShop()
    {
        $query= $this->db->get('pos_direct_branch');
		return $query->result_array();	
        
        
        
        
        
    }
	
	function getShop($token = false,$show = true)
	{
		if($token) $this->db->where('shopID <=',1000);
		 $this->db->where('shopID !=',0);
		$this->db->select('shopID,name,email,cashType,joinType,distributeType');
		//$this->db->where('shopID !=',100);
		if($show==true)$this->db->where('show',1);
        if($token)$this->db->where('joinType',1);
		$this->db->order_by('shopID','ASC');
		$query= $this->db->get('pos_sub_branch');
		return $query->result_array();	
		
		
	}
	function getAllMemberData()
	{
		$this->db->select('memberID');
		$query = $this->db->get('pos_pha_members');
		return $query->result_array();
	}

	function getMemberData($timeStamp)
	{
		$this->db->select('memberID,name');
		$this->db->where('timeStamp >=' ,$timeStamp);
		$query = $this->db->get('pos_pha_members');
		return $query->result_array();
	}
	function getShopMemberData($timeStamp)
	{
		$this->db->select('memberID,shopID,dueTime,levelID,reNew,myBonus');
		$this->db->where('timeStamp >=' ,$timeStamp);
		$query = $this->db->get('pos_shop_member');
		return $query->result_array();
	}	
	function getAllProductData()
	{
		$this->db->select('productID');
		$query = $this->db->get('pos_product');
		return $query->result_array();	
	
	}	
	function getProductData($timeStamp,$offset)
	{
		$num = 20;
		$this->db->select('productID,productNum,purchaseCount,barcode,ZHname,ENGName,language,price,minDiscount,type,category');
		$this->db->where('timeStamp >=' ,$timeStamp);
		$this->db->limit($offset*$num,$num);
		$query = $this->db->get('pos_product');
		return $query->result_array();
	}	
	
		
	function getProductType($timeStamp)
	{
		$this->db->select('typeID,name,order');
		$this->db->where('timeStamp >=' ,$timeStamp);
		$query = $this->db->get('pos_product_type');
		return $query->result_array();
	}	
	function isClearning($shopID)
	{
		$this->db->where('shopID',$shopID);
		$this->db->where('dirtyBit',1);
		$query = $this->db->get('pos_sub_branch');
		if($query->num_rows()>=1) return true;
		else return false;
		
		
	}
	
	function grave($type,$mainID,$data)
	{
		$datain = array(
			'type' =>$type,
			'mainID'=>$mainID,
			'data'=> json_encode($data),
			'timeStamp'=>date("Y-m-d H:i:s")
		)	;
		$this->db->insert('pos_grave',$datain);
		
	}
    
    function deleteFromGrave($type,$mainID)
    {
        $this->db->where('mainID',$mainID);
        $this->db->where('type',$type);
        $this->db->delete('pos_grave');
        
        
        
    }
    
    function addPoint($shopID,$point,$comment,$orderID = 0)
    {
        
             
        $datain = array(
                    'shopID'  =>$shopID,
                    'comment' =>$comment,
                    'point'   => $point,
                    'orderID' =>$orderID,
                    'time'    =>date('Y-m-d H:i:s')
              )  ; 
    
         $this->db->insert('pos_feedback_point',$datain) ;
        
   
      
       $data['shopInf'] = $this->getShopByID($shopID);
        
       
        mb_internal_encoding('UTF-8');
        $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
        $title = '點數回饋存入通知 '.date('Y-m-d H:i:s');
			if(!empty( $data['shopInf']['email']))
			{
				//;
				$this->Mail_model->groupEmail( $data['shopInf']['email'],$title,'<h1>總存入點數：'.$point.'</h1>'.$comment,$headers);
				
			}
		
        
      $this->updateAllPoint($shopID);
        return $datain;
      
    }
	
    
     function updateAllPoint($shopID)
    {
        $this->db->select_sum('point');
        $this->db->where('shopID',$shopID);
		$q = $this->db->get('pos_feedback_point');
        $d = $q->row_array();
        
        $this->db->where('shopID',$shopID);
        $this->db->update('pos_sub_branch_inf',array('point'=>$d['point']));
        
        
    }
}

?>
