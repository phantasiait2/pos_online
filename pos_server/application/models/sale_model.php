<?php 
class Sale_model extends Model {
	function Sale_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function getProductRank($shopID,$year,$mon)
	{
		$result = array();
//		if($shopID==0&&$year==0&&$mon==0) return $result;
		
		
		
			if($shopID!=0)$this->db->where('shopID',$shopID);
			if($shopID!=100)$this->db->where('shopID !=',100);
			if($year!=0)$this->db->where('year(time)',$year);
			if($mon!=0)$this->db->where('month(time)',$mon);
		
			$this->db->select('productID,sum(pos_product_sell.num) as sellNum',false);
			$this->db->group_by('productID');
			$this->db->limit(500);

			$this->db->order_by('sellNum','DESC');
			$query = $this->db->get('pos_product_sell');		
			$data = $query->result_array();
			
				foreach($data as $row)
				{
					if(!isset($result['p_'.$row['productID']]))
					{
							$p = array();
							$this->db->where('pos_product.type',1);
							$this->db->where('pos_product.productID !=',8884457);
							//$this->db->where('pos_product.openStatus',1);
							$this->db->where('pos_product.category !=','');
							$this->db->where('pos_product.category !=','0');
							$this->db->where('pos_product.productID ',$row['productID']);
							$this->db->join('pos_own','pos_own.productID  = pos_product.productID and pos_own.onYear=2019','left');
							$query = $this->db->get('pos_product');
							$p = $query->row_array();
						
							if(!empty($p))
							{
								$result['p_'.$row['productID']]	 = 
								array(
								'ZHName'=>$p['ZHName'],
								'ENGName'=>$p['ENGName'],
                                'price'=>$p['price'],
								'language'=>$p['language'],
								'best30'=>$p['best30'],
                                'onYear'=>$p['onYear'],
								'val'=>0);
							}
					}
					if(isset($result['p_'.$row['productID']]))$result['p_'.$row['productID']]['val']+=$row['sellNum'];
				}
			
		
		
		
		usort($result,'cmpValue');
		
		
	
		return $result;
	}
	
	function getRfm($shopID,$from,$to,$start)
	{
			$sql = "SELECT *,pos_pha_members.memberID FROM pos_pha_members 
		LEFT JOIN pos_my_member ON pos_my_member.memberID = pos_pha_members .memberID  
		LEFT JOIN (select count(memberID) as frequency ,memberID from 
		( select memberID  from pos_product_sell 
		where ";
		if($shopID!=0) $sql.=" shopID = $shopID and ";
		$sql.=" (`time`)>='$from' and (`time`)<= '$to'
		GROUP BY year(`time`) ,month(`time`) ,day(`time`) ,memberID ) as a
		
		group by memberID ) AS  F
		 ON F.memberID = pos_pha_members .memberID
		
		LEFT JOIN 
		(select MAX(`time`) as recencyTime,sum(sellPrice*num)  as total ,memberID  from pos_product_sell 
		where ";
		if($shopID!=0) $sql.=" shopID = $shopID and ";
		$sql.="  (`time`)>='$from' and (`time`)<= '$to'
		GROUP BY memberID  ) as R
		 ON R.memberID = pos_pha_members .memberID
		
		
		WHERE ";
		if($shopID!=0) $sql.=" pos_my_member.shopID = $shopID and";
		
		$sql.=" pos_pha_members.joinTime<='$to '";
		if($start!=0) $sql.=" and pos_pha_members.joinTime<='$start'";
		
		$sql.=" GROUP BY pos_my_member.memberID order by total DESC";
		echo $sql;
				$query = $this->db->query($sql); 
		return $query->result_array();
		
	}
	function getMemberSale($time)
	{
		
		$sql = "select pos_pha_members.memberID,name,sum(sellPrice*num) as sell ,joinTime from pos_pha_members left join pos_product_sell on pos_product_sell.memberID = pos_pha_members.memberID
		 where joinTime<'".$time."' and pos_product_sell.time<=  DATE_ADD(pos_pha_members.joinTime,INTERVAL 365 DAY)
group by pos_pha_members.memberID
ORDER BY `pos_pha_members`.`joinTime`  ASC  
		
		 ";
		$query = $this->db->query($sql); 
		return $query->result_array();
		
		
	}
	
	
	
	
	
	
	
	
	
	function timeSwitch($date,$switchArray)
	{
		$date = strtotime('2011-01-01 '.$date);
		$num = count($switchArray);
		for($i=0;$i<$num;$i++) $time[$i] = strtotime('2011-01-01 '.$switchArray[$i]);

		for($i=0;$i<$num-1;$i++) 
		{
			if($date>=$time[$i]&&$date<=$time[$i+1]) return $i;				
			
		}
		
		return $i-1;	
		
	}
	function getSanguosha($shopID)
	{
		
		if($shopID!=0) $this->db->where('pos_sanguosha.shopID',$shopID);
		$this->db->where('pos_sanguosha.delete',0);
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_sanguosha.shopID','left');
		$query = $this->db->get('pos_sanguosha');
		return $query->result_array();
		
	}

	function getSaleData($memberID)
	{
		$this->db->select('pos_product_sell.*,pos_product.ZHName,pos_product.ENGName,pos_product.language,pos_product.type,pos_sub_branch.name as shopName,category	');
		$this->db->where('memberID',$memberID);
		$this->db->where('pos_product_sell.shopID !=',100);
		$this->db->join('pos_product','pos_product.productID = pos_product_sell.productID','left');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_product_sell.shopID','left');
		$this->db->order_by('pos_product_sell.time','DESC');
		$query = $this->db->get('pos_product_sell');
		return $query->result_array();
		
	}
	function getRentData($memberID)
	{
		$this->db->select('pos_product_sell.*,pos_product.ZHName,pos_product.ENGName,pos_product.language,pos_sub_branch.name as shopName');
		$this->db->where('memberID',$memberID);
		$this->db->where('pos_product_sell.shopID !=',100);
		$this->db->join('pos_product','pos_product.productID = pos_product_sell.productID','left');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_product_sell.shopID','left');
		$query = $this->db->get('pos_product_sell');
		return $query->result_array();
		
	}
	function getAllMemberPresent($shopID)
	{
		$this->db->select('pos_birthday_present.*,pos_pha_members.name,pos_sub_branch.name as shopName');
		if($shopID!=0)$this->db->where('pos_birthday_present.shopID',$shopID);
		$this->db->order_by('pos_birthday_present.id','desc');
		$this->db->join('pos_pha_members','pos_birthday_present.memberID = pos_pha_members.memberID','left');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_birthday_present.shopID','left');
		$query = $this->db->get('pos_birthday_present');
		return $query->result_array();
		
		
	}

}

?>
