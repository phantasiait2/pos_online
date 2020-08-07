<?php 

class PO_model extends Model {
	function PO_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function getProductNum($shopID,$productID,$date)
	{
		
		$t = explode('-',$date);

		$year = $t[0];
		$month = $t[1];
		
				$sql = "select * from pos_current_product_amount
						where shopID = $shopID and productID = $productID
					 limit 0,1";
		/*$sql = "select * from pos_product_amount
						where ((year= $year  and month<= $month )or year< $year) and shopID = $shopID and productID = $productID
					order by `year` desc,`month` desc limit 0,1";			 
					*/
		$query = $this->db->query($sql);
		$ret =  $query->row_array();	
//		print_r($ret);
		if(empty($ret)||empty($ret['num'])||$ret['num']=='')return 0;
		return $ret['num'];
	}
	function getProductAmountInf($shopID,$productID,$date)
	{
		$t = explode('-',$date);
		$now =  getdate();

		$year = $t[0];
		$month = $t[1];
		
		if($year==$now['year']&&$month==$now['mon'])
		{
		$sql = "select * from pos_current_product_amount
						where shopID = $shopID and productID = $productID
					 limit 0,1";
		}
		else
		{
		$sql = "select * from pos_product_amount
						where ((year= $year  and month<= $month )or year< $year) and shopID = $shopID and productID = $productID
					order by `year` desc,`month` desc limit 0,1";
								 
		}
		$query = $this->db->query($sql);
		return  $query->row_array();	
//		
	}
	
	function getShopList()
	{
		$this->db->where('shopID <=',600);
		$this->db->where('shopID !=',2);
		$this->db->where('shopID !=',100);
			$query = $this->db->get('pos_sub_branch');
		return  $query->result_array(); 
		
	}

	function arraySort(&$data,$index,$order = 'ASC')
	{
		$num = count($data);

		for($i=0;$i<$num;$i++)
		{
			$lim = $data[$i][$index];
			$swap = $i;
			for($j=$i;$j<$num;$j++)
			{
				if(($order=='DESC' && $data[$j][$index] > $lim)||($order=='ASC' && $data[$j][$index] < $lim))
				{
					$lim = $data[$j][$index];
					$swap =$j; 
				}
			}	
			$swapArray =  $data[$swap];
			$data[$swap] = $data[$i];
			$data[$i] = $swapArray;
		}
		
		
	}
	
	function errDetect($account)
	{
		$this->load->library('session');
		$debug =  $this->session->userdata('debug');

		if($debug==1 && $this->uri->segment(2)!='login_status')
		{
				$this->session->set_userdata('debug', 0);	
				echo '資料庫檢測！<br/>';
		}
	
	
		

	}
	
	function getAvailableNum($productID,$returnOrderNum = false)
	{
		$this->db->where('productID',$productID);
		$this->db->where('shopID',0);
		$this->db->order_by('year','DESC');
		$this->db->order_by('month','DESC');
		$query = $this->db->get('pos_product_amount');	
		$product = $query->row_array();
		$order = $this->getProductInorderList($productID);
		if(!isset($product['num'])) $nowNum = 0;
		else $nowNum = $product['num'];
		$stockNum = $nowNum;
		$totalBuyNum = 0;	
		
		//計算總訂貨量
		foreach($order as $row)
		{
			$totalBuyNum+=$row['buyNum'];
		
		}
		if($returnOrderNum==false)	return $nowNum - $totalBuyNum;
		else
		{
			$r['nowNum'] = $nowNum - $totalBuyNum;
			$r['buyNum'] = $totalBuyNum;
			return $r;
		}
	}
	function getProductInorderList($productID)
	{

			$this->db->select('pos_sub_branch.name as shopName,pos_order_detail.id as odID,pos_order_detail.id as rowID,pos_order_detail.*,pos_order.*');
			$this->db->where('pos_order.status !=',0);//尚未出貨，或是尚未完全出貨
			$this->db->where('pos_order_detail.status',0);//尚未出貨，或是尚未完全出貨。
			$this->db->where('pos_order_detail.productID',$productID);
			$this->db->join('pos_order','pos_order.id = pos_order_detail.orderID');
			$this->db->join('pos_sub_branch','pos_order.shopID = pos_sub_branch.shopID','left');
			$this->db->order_by('pos_order.orderTime');
			$query = $this->db->get('pos_order_detail');
			return  $query->result_array();
	}
    
    function getCsorderNum($shopID,$productID)
    {
       
        $this->db->join('pos_cs_order_detail','pos_cs_order_detail.csorderID = pos_cs_order.csOrderID','left');
        
        $this->db->where('pos_cs_order.usage',1);
         $this->db->where('pos_cs_order.deleteToken',0);
        $this->db->where('pos_cs_order.shopID',$shopID);
        $this->db->where('pos_cs_order_detail.productID',$productID);
        $this->db->where('(pos_cs_order.cargoStatus + pos_cs_order.cashStatus) <',2);
       
         $query = $this->db->get('pos_cs_order');
        $r = $query->result_array();
   
        $num = 0;
        foreach($r as $row) $num+=$row['num'];
        return $num;
        
    }
	
}

?>