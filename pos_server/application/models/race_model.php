<?php 
class Race_model extends Model {
	function Race_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function getRace()
	{
		$query = $this->db->get('pos_race');
		$data = $query->result_array();
		foreach($data as $row)
		{
			$out[$data['key']] = $row['val'];
			
			
		}
		return $out;

		
	}
	
	function getRaceProduct($date,$productList)
	{
		$this->db->where('shopID <',1000);
		$this->db->where('shopID !=',100);
		$query = $this->db->get('pos_sub_branch');
		$shopList = $query->result_array();
		
		foreach($shopList as $row)
		{
			$shopID = $row['shopID'];
			$data[$shopID]['sale'] = $this->Accounting_model->getMonReport(0,0,$shopID,$date);
			
	
			foreach ($data[$shopID]['sale'] as $each)
			{
				
				foreach($productList as $col)
				{
					
					if($each['productID']==$col['productID'])
					{
					
						if(!isset($result[$shopID][$col['productID']]))	$result[$shopID][$col['productID']] = 0;
						$result[$shopID][$col['productID']] +=$each['sellNum'];
						
					} 
				}
				
					
			}
			
			
		}
	
		return $result;
		
		
		
	}
	
	
	function getBest30($shopID = 0 ,$year,$month)
	{
		if($shopID != 0 )
		{
			$this->db->where('pos_product_sell.shopID',$shopID);
				$this->db->group_by('pos_product_sell.account');
		}
		else
		{
			$this->db->where('pos_product_sell.shopID <',1000);
				$this->db->group_by('pos_product_sell.account , pos_product_sell.shopID');
			
			
			
		}
         if($month<=3 || ($year==2020 && $month==4)) $onYear = $year-1;
        else $onYear = $year;
        
		$this->db->where('year(time)',$year);
		$this->db->where('isOnline',0);
		$this->db->where('pos_product_sell.shopID !=',100);
		//$this->db->where('pos_product_sell.shopID !=',666);
        if($month!=0)$this->db->where('month(time)',$month);
		$this->db->join('pos_own','pos_own.productID = pos_product_sell.productID','left');
		
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_product_sell.shopID','left');
		
		$this->db->join('pos_best30_inf','pos_best30_inf.shopID = pos_product_sell.shopID and pos_best30_inf.account = pos_product_sell.account','left');
		
		$this->db->where('pos_own.best30',1);

	$this->db->where('pos_own.onYear',$onYear);
		$this->db->select('sum(pos_own.bonus*pos_product_sell.num) as bonusTotal,pos_product_sell.account,pos_sub_branch.name as shopName,pos_sub_branch.shopID,pos_best30_inf.name,address,IDNumber,phone,bankCode,bankAccount');
	
		//$this->db->order_by('bonusTotal','ASC');
		$q = $this->db->get('pos_product_sell');
		return $q->result_array();
		
		
	}
	function getAccountInfo($shopID,$account)
	{
		
		$this->db->where('shopID',$shopID);
		$this->db->where('account',$account);
		
		$query = $this->db->get('pos_best30_inf');
		return $query->row_array();
		
		
		
		
		
	}
	
	
	function getbest30Inf()
	{
		$this->db->where('best30',1);
        $this->db->where('onYear',2020);
		$query = $this->db->get('pos_own');
		
		return $query->result_array();
	}
		
	function getMartProduct($shopID,$month)
	{
		
		
		
            ;
        $this->db->select('*,pos_online_order_result.memberID as newMemberID');
      
        
        $this->db->join('pos_cs_order','pos_cs_order.csOrderID = pos_online_order_result.csOrderID','left');
        $this->db->where('shopID',$shopID);
		 $this->db->where('month(pos_online_order_result.time)',$month);
        
        $query = $this->db->get('pos_online_order_result');
        $data = $query->result_array();
		
		return $data;
;		
	}
	
	function getBest30Major($year,$month)
	{
		
		$this->db->where('pos_own_major.year',$year);
		$this->db->where('pos_own_major.month',$month);
		$q = $this->db->get('pos_own_major');
		$sellData =  $q->result_array();
		$sellResult = array();
		foreach($sellData as $row)
		{
			$row['t'] = 0;
			$sellResult[$row['productID']] =$row;
			
		}
		return $sellResult;
		
	}
	
	
	
	function getBest30MajorSell($shopID,$year,$month)
	{
		
		$this->db->select('*,sum(num) as t');
		if($shopID!=0) $this->db->where('shopID',$shopID);
		$this->db->where('year(time)',$year);
		$this->db->where('month(time)',$month);
		$this->db->where('pos_own_major.year',$year);
		$this->db->where('pos_own_major.month',$month);
		$this->db->join('pos_product_sell','pos_own_major.productID = pos_product_sell.productID','left');
		$this->db->group_by('pos_product_sell.productID');
		$q = $this->db->get('pos_own_major');
		$sell =  $q->result_array();
		$major = array();
		$sellResult = $this->getBest30Major($year,$month);
		foreach($sell as $row)
		{
			if($row['t'] >=$row['times']) $major[$row['productID']] = $row;
			$sellResult[$row['productID']] = $row;
		}
		$r['sellResult']  = $sellResult;	
		$r['major'] = $major;
		return $r;
	}
	function getBest30MajorAllSell($year,$month)
	{
		
		$this->db->select('*,sum(num) as t');
	
		$this->db->where('year(time)',$year);
		$this->db->where('month(time)',$month);
		$this->db->where('pos_own_major.year',$year);
		$this->db->where('pos_own_major.month',$month);
		$this->db->join('pos_product_sell','pos_own_major.productID = pos_product_sell.productID','left');
		$this->db->group_by('pos_product_sell.productID');
		$this->db->group_by('pos_product_sell.shopID');
		$q = $this->db->get('pos_own_major');
		$sell =  $q->result_array();
		
		
		$major = array();
		$sellResult = $this->getBest30Major($year,$month);
		$r['majorInf']  = $sellResult;
		foreach($sell as $row)
		{
			if($row['t'] >=$row['times']) $major[$row['shopID']][$row['productID']] = $row;
			$sellResult[$row['shopID']][$row['productID']] = $row;
		}
		
		$r['sellResult']  = $sellResult;	
		$r['major'] = $major;
		return $r;
	}
	function getSellNum($shopID,$year,$month,$account,$productID)
	{
		$this->db->where('shopID',$shopID);
		$this->db->where('account',$account);
		$this->db->where('productID',$productID);
		
		$this->db->where('year(time)',$year);
		$this->db->where('month(time)',$month);
		$q = $this->db->get('pos_product_sell');
		$data =  $q->result_array();
		$num = 0;
		foreach($data as $row)
		{
			
			$num +=$row['num'];
			
		}
		return $num;
	}
	
		
		
		
	function getBest30Detail($shopID,$year,$month,$account)
	{
		   if($month<=3 || ($year==2020 && $month==4))$onYear = $year-1;
        else $onYear = $year;
	echo $onYear;
		$r  = $this->getBest30MajorSell($shopID,$year,$month);
		$sellResult = $r['sellResult'] ;	
		$major = $r['major'] ;
		$this->db->where('shopID',$shopID);
		$this->db->where('account',$account);
		
		$this->db->where('year(time)',$year);
		$this->db->where('month(time)',$month);
		$this->db->join('pos_own','pos_own.productID = pos_product_sell.productID','left');
		
		$this->db->join('pos_product','pos_product.productID = pos_product_sell.productID','left');
		
		$this->db->where('pos_own.best30',1);
	
          
        $this->db->where('pos_own.onYear',$onYear);
        
		$this->db->select('pos_own.bonus as best30Bonus,account,pos_product_sell.num as sellNum,pos_product_sell.*,ZHName,ENGName,price,minDiscount,language');
	
		$this->db->order_by('pos_product_sell.time','ASC');
		$q = $this->db->get('pos_product_sell');
		$data =  $q->result_array();
		
		if(!empty($major))
		foreach($data as $row)
		{
			
			if(isset($major[$row['productID']]))
			{
				
				$row['best30Bonus'] = $major[$row['productID']]['upbonus'];
				$row['major'] = 1;
				
			}
				
			$final[] = $row;
		
		
		}
		else $final = $data;
		
		
		$result['data'] = $final;
		$result['sell'] = $sellResult;
		
		return $result;
	}
	
	
	
}

?>
