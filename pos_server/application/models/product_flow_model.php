<?php 
class Product_flow_model extends Model {
	function Product_flow_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function getFlow($from,$to,$shopIDList,$sqlList,$fetch)
	{
		$sql = "
		SELECT  pos_product.*,pos_suppliers.name as suppliersName FROM pos_product 
		LEFT JOIN pos_suppliers
		ON pos_product.suppliers = pos_suppliers.supplierID where type=1 or type=7 or type =8 or type =4  or type =2
		
		";

		$query = $this->db->query($sql); 
		$data = $query->result_array();
		$i = 0;
		foreach($shopIDList as $shopID)
		{
	
				if($fetch['sell']) $shop[$shopID] ['sellData']= $this-> getSell($from,$to,$shopID);
				
				if($fetch['purchase'])$shop[$shopID] ['purchaseData']= $this-> getPurchase($from,$to,$shopID);
				if($fetch['adjust'])$shop[$shopID] ['adjustData']= $this-> getAdjust($from,$to,$shopID);
				
				if($fetch['back'])$shop[$shopID] ['backData']= $this-> getBack($from,$to,$shopID);
				echo 's';
				if($fetch['customerBack'])$shop[$shopID] ['customerBackData']= $this-> getCustomerBack($from,$to,$shopID);
					echo 'sa';
				if($fetch['amount'])
				{
					//echo $sqlList[$i]['lastAmount'];
					if(isset($sqlList[$i]['amount']))$shop[$shopID] ['amountData']= $this-> getAmount($sqlList[$i]['amount']);
					if(isset($sqlList[$i]['lastAmountData']))$shop[$shopID] ['lastAmountData']= $this-> getAmount($sqlList[$i]['lastAmount']);
					
					$i++;
				}
				
		}	
		$i =0;	
		foreach($data as $row)
		{
			
			$key = false;
			$j=0;
			foreach($shopIDList as $shopID)
			{
				$result[$i]['shopData'][$j] =array();
				if(isset($shop[$shopID] ['sellData']['p_'.$row['productID']])&&$shop[$shopID] ['sellData']['p_'.$row['productID']]!=0)
				{
					$result[$i]['shopData'][$j]['sellNum'] = $shop[$shopID] ['sellData']['p_'.$row['productID']];
					$key =true;
				}
				if(isset($shop[$shopID] ['amountData']['a_'.$row['productID']])&&$shop[$shopID] ['amountData']['a_'.$row['productID']]!=0)
				{
					$result[$i]['shopData'][$j]['nowNum']  = $shop[$shopID] ['amountData']['a_'.$row['productID']];
					$key =true;
				}
				if(isset($shop[$shopID] ['purchaseData']['p_'.$row['productID']])&&$shop[$shopID] ['purchaseData']['p_'.$row['productID']]!=0)
				{
					$result[$i]['shopData'][$j]['purchaseNum']  = $shop[$shopID] ['purchaseData']['p_'.$row['productID']];
					$key =true;
				}
				if(isset($shop[$shopID] ['adjustData']['p_'.$row['productID']])&&$shop[$shopID] ['adjustData']['p_'.$row['productID']]!=0)
				{
					$result[$i]['shopData'][$j]['adjustNum']  = $shop[$shopID] ['adjustData']['p_'.$row['productID']];
					$key =true;
				}
				if(isset($shop[$shopID] ['backData']['p_'.$row['productID']])&&$shop[$shopID] ['backData']['p_'.$row['productID']]!=0)
				{
					$result[$i]['shopData'][$j]['backNum']  = $shop[$shopID] ['backData']['p_'.$row['productID']];
					$key =true;
				}
				if(isset($shop[$shopID] ['customerBackData']['p_'.$row['productID']])&&$shop[$shopID] ['customerBackData']['p_'.$row['productID']]!=0)
				{
					$result[$i]['shopData'][$j]['customerBackNum']  = $shop[$shopID] ['customerBackData']['p_'.$row['productID']];
					$key =true;
				}
				if(isset($shop[$shopID] ['lastAmountData']['a_'.$row['productID']])&&$shop[$shopID] ['lastAmountData']['a_'.$row['productID']]!=0)
				{
					
					$result[$i]['shopData'][$j]['lastNum']  = $shop[$shopID] ['lastAmountData']['a_'.$row['productID']];
					$key =true;
				}													
				$j++;
				
			}
			if($key)$result[$i++]['product'] = $row;
			
		}
				
		return $result;
		
	}
	function getAmount($sql)
	{
		$query = $this->db->query($sql);
		$data = $query->result_array();
		foreach($data as $row)
		{
			$result['a_'.$row['productID']] = $row['nowNum']	;
			
		}
		if(isset($result))	return $result;		
		
		
	}
	function getAccountAmountByParam($date,$shopID,$productID = 0)
	{
		
		$d = explode("-",$date);
        $this->db->where('productID',$productID);
        $this->db->where('shopID',$shopID);
        $this->db->where('year',$d[0]);
        $this->db->where('month',$d[1]);
	
		$this->db->select("*");
		$this->db->select("O_amount as num");
	
   		$query = $this->db->get('pos_accounting_table');
		$data =  $query->row_array();
        if(!empty($data)) 	return $data;
        else return array('num'=>0,'avgCost'=>0);

		
		
	}
	
	function getAccountAmountByParamByDay($date,$shopID,$productID = 0)
	{
		
		$d = explode("-",$date);
        $this->db->where('productID',$productID);
        $this->db->where('shopID',$shopID);
        $this->db->where('year',$d[0]);
        $this->db->where('month',$d[1]);
		$this->db->where('day',$d[2]);
	
		$this->db->select("*");
		$this->db->select("amount as num");
	
   		$query = $this->db->get('pos_accounting_io_temp');
		$data =  $query->row_array();
        if(!empty($data)) 	return $data;
        else return array('num'=>0,'avgCost'=>0);

		
		
	}
	
	
	
	
	
    function getAmountByParam($date,$shopID,$productID = 0)
    {
        
        
            
        $d = explode("-",$date);
        
        /*
        $this->db->where('productID',$productID);
        $this->db->where('shopID',$shopID);
        $this->db->where('year',$d[0]);
        $this->db->where('month',$d[1]);
   		$query = $this->db->get('pos_accounting_io');
		$data =  $query->row_array();
        if(!empty($data)) 	return $data;
        else return array('num'=>0,'avgCost'=>0);
        */
		$year = $d[0];
		$month = $d[1];
		$sql = "select * from pos_product_amount
						where ((year= $year  and month<= $month )or year< $year) and shopID = $shopID and productID = $productID
					order by `year` desc,`month` desc limit 0,1";		
   		$query = $this->db->query($sql);
		$data =  $query->row_array();
        if(!empty($data)) 	return $data;
        else return array('num'=>0,'avgCost'=>0);
		
		
        
        
        /*
        
        */
    }
    function getSellproduct($from,$to,$shopID,$productID = 0)
	{
		$sql = "SELECT num as sellNum,sellPrice ,pos_product_sell.productID FROM pos_product_sell 
        left join pos_product on pos_product.productID = pos_product_sell.productID
        WHERE type!=2 and type!=3 and type!=4 and type!=6 and type!=9 and type!=10 and date(time)>='".$from."' and  date(time)<='".$to."'";
        if($shopID!=0) $sql.=" and shopID=$shopID ";    
         if($productID!=0) $sql.=" and productID = $productID ";
         
      //  $sql.= " GROUP by productID";
		$query = $this->db->query($sql);
		
		$data = $query->result_array();
        return $data;
       
		
	}
	
	function getSell($from,$to,$shopID,$productID = 0)
	{
		$sql = "SELECT num as sellNum,sellPrice ,productID FROM pos_product_sell WHERE date(time)>='".$from."' and  date(time)<='".$to."'";
        if($shopID!=0) $sql.=" and shopID=$shopID ";    
         if($productID!=0) $sql.=" and productID = $productID ";
         
      //  $sql.= " GROUP by productID";
		$query = $this->db->query($sql);
		
		$data = $query->result_array();
        return $data;
       
		
	}
	function getRecentSell($productID)
	{
		
		$this->db->where('shopID !=',100);
		$this->db->where('productID',$productID);
		$this->db->order_by('time','DESC');
		$this->db->limit(300);
		$query = $this->db->get('pos_product_sell');
		
		$r =  $query->result_array();
        
        
        $month = substr($r[count($r)-1]['time'],0,7);//抓年月
        
        $time = getdate();
        $a = date('Y-m-d H:i:s',mktime(0,0,0,$time['mon']-12,1,$time['year'])); 
            

        $b =  $month.'-01 00:00:00';
        if(strtotime($a)>strtotime($b)) $min = $b;
        else $min = $a;
      
        $this->db->where('time >=',$min);
      
        
        $this->db->where('shopID !=',100);
		$this->db->where('productID',$productID);
       
        
		$this->db->order_by('time','DESC');
        $query = $this->db->get('pos_product_sell');
        return $query->result_array();;
		
	}


	function getPurchase($from,$to,$shopID,$productID = 0 )
	{
		$sql = "SELECT sum(num) as purchaseNum,purchasePrice,productID FROM pos_product_purchase WHERE date(time)>='".$from."' and  date(time)<='".$to."' and shopID=$shopID ";
        if($productID!=0) $sql.=" and productID = $productID ";
        $sql.=" GROUP by productID";
		$query = $this->db->query($sql);
	
		$data = $query->result_array();
       
         if($productID!=0)
         {
          
             if(isset($data[0]['purchaseNum']))  return $data[0];
              else return array('purchaseNum'=>0,'purchasePrice'=>0);;
             
         }
        
		foreach($data as $row)
		{
			$result['p_'.$row['productID']] = $row['purchaseNum']	;
			
		}
		if(isset($result))	return $result;	
	}	
    

    
	function getAccountPurchase($from,$to,$shopID,$productID = 0 )
	{
		
		$sql = "SELECT sum(num) as purchaseNum,purchasePrice,productID,purchaseID FROM pos_product_purchase WHERE
		purchaseID !=0 and accountTime>='".$from."' and  accountTime<='".$to."' and shopID=$shopID ";
        if($productID!=0) $sql.=" and productID = $productID ";
        $sql.=" GROUP by productID";
		$query = $this->db->query($sql);
	
		$data = $query->result_array();
    
        
         if($productID!=0)
         {
          
             if(isset($data[0]['purchaseNum']))  return $data[0];
              else return array('purchaseNum'=>0,'purchasePrice'=>0);;
             
         }
        $purchaseList = array();
		foreach($data as $row)
		{
			
			if(!isset($purchaseList['p_'.$row['purchaseID']]))
			{
				$purchaseList['p_'.$row['purchaseID']] = $this->checkPurchaseInf($row['purchaseID']);
					
			}
			if($purchaseList['p_'.$row['purchaseID']] )	$result['p_'.$row['productID']] = $row['purchaseNum']	;
			
		}
		if(isset($result))	return $result;	
		
		
		
	}
	
	function checkPurchaseInf($purchaseID)
	{
		$this->db->where('status',3);
		$this->db->where('purchaseID',$purchaseID);
		$query = $this->db->get('pos_product_purchase_inf');
		if($query->num_rows()>0) return true;
		else return false;
	}
	
	
	
    function getShipmentOut($from,$to,$shopID,$productID = 0,$type = -1)
       
    {
		
		
		
            $this->db->select('sproductID productID');
            $this->db->select('sellNum');
            $this->db->select('shopID');
            $this->db->select('sellPrice',false);
            $this->db->select('pos_order_shipment.type');
        if($productID!==0)$this->db->where('sproductID',$productID);
        $this->db->join('pos_order_shipment_detail','pos_order_shipment.id=pos_order_shipment_detail.shipmentID');
        if($shopID!=0)$this->db->where('shopID',$shopID);
       
		if($type!=-1 && $type!=-2)$this->db->where('pos_order_shipment.type',$type);
		
		//含調貨跟月結品
		if($type==-2)$this->db->where('pos_order_shipment.type !=',1);
		
		
        $this->db->where('status <=',4);//定單已完成
         $this->db->where('status >=',2);//已到貨
      
        $this->db->where('shippingTime >=',$from);
        $this->db->where('shippingTime <=',$to);
        
        $query = $this->db->get('pos_order_shipment');
        
        $data = $query->result_array();
        
         if($productID==0)  return $data;
       
         if($productID!=0) 
         {
             $result = array('sellNum'=>0,'sellPrice'=>0);
             if(count($data)>0)
             {
                        
                 foreach($data as $row)
                 {
                   
                     $result['sellNum'] += $row['sellNum'];
                     $result['sellPrice'] += $row['sellNum'] * $row['sellPrice'];
                 }
                 
                 
             }
             return $result;
            
         }
		foreach($data as $row)
		{
			$result['p_'.$row['productID']] = $row['totalNum']	;
			
		}
		if(isset($result))	return $data; $result;	
        
        
        
    }
    
	function getBack($from,$to,$shopID=0,$productID = 0,$isConsignment = -1)
	{
		$sql = "SELECT num as backNum, purchasePrice,productID ,isConsignment,purchasePrice
				FROM pos_order_back LEFT JOIN
				pos_order_back_detail ON pos_order_back_detail.backID = pos_order_back.id				
				WHERE  status=4 and  date(backTime)>='".$from."' and  date(backTime)<='".$to."'";
        if($shopID!=0)  $sql.= "and shopID=$shopID "; 
         if($productID!=0) $sql.=" and productID = $productID ";
		if($isConsignment!=-1) $sql.=" and isConsignment = $isConsignment ";
		
	
     
		$query = $this->db->query($sql);
	
		$data = $query->result_array();
        	return $data;	
	}	
	function getAdjust($from,$to,$shopID=0,$productID = 0,$toShopID = 0,$isConsignment = -1)
	{
       
		$sql = "SELECT num as adjustNum,purchasePrice,productID,isConsignment,purchasePrice
				FROM pos_order_adjust LEFT JOIN
				pos_order_adjust_detail ON pos_order_adjust_detail.adjustID = pos_order_adjust.id
			 WHERE date(time)>='".$from."' and  date(time)<='".$to."'";
        if($shopID!=0) $sql.=" and fromShopID=$shopID "; 
		 if($toShopID!=0) $sql.=" and destinationShopID=$toShopID "; 
         if($productID!=0) $sql.=" and productID = $productID ";
		 if($isConsignment!=-1) $sql.=" and isConsignment = $isConsignment ";
 
		$query = $this->db->query($sql);
	
		$data = $query->result_array();
        if($productID==0) return $data;	
        
        $result = array('adjustNum'=>0,'purchasePrice'=>0);
        foreach($data as $row)
        {
            
            $result['adjustNum'] += $row['adjustNum'];
            $result['purchasePrice'] += $row['purchasePrice']*$row['adjustNum'];
            
        }
        return $result;
        	
          
	}	

	function getCustomerBack($from,$to,$shopID,$productID=0)
	{
		
		$sql = "SELECT num as customerBackNum, sellPrice,productID FROM pos_product_back WHERE date(backTime)>='".$from."' and  date(backTime)<='".$to."' and shopID=".$shopID ; 
		if($productID!=0) $sql.=" and productID = $productID ";
     
		$query = $this->db->query($sql);
	
		$data = $query->result_array();
		return $data;	
	}	
	function getProductFlow($shopID,$from,$to)
	{
	
		$time = getdate();
		$year = $time['year'];
		$month  = $time['mon'];
		$sql = "SELECT a.*,ZHName,ENGName,language,type,price,buyPrice,pos_product_amount.num as  nowNum,wait
				FROM
				(
				SELECT productID,sellPrice,num as sellNum,0 as purchaseNum,time,shopID FROM pos_product_sell WHERE  date(time)>='".$from."' and  date(time)<='".$to."' and  shopID = $shopID
				UNION
				SELECT productID,0 as sellPrice,0 as sellNum ,num as purchaseNum, time,shopID FROM pos_product_purchase WHERE  date(time)>='".$from."' and  date(time)<='".$to."' and shopID = $shopID
				ORDER BY time
				) as a
				LEFT JOIN pos_product ON a.productID = pos_product.productID
				LEFT JOIN pos_product_amount ON a.productID = pos_product_amount.productID and pos_product_amount.shopID = a.shopID and year=$year and month = $month
				
				
				";
		$query = $this->db->query($sql);
	
		return $query->result_array();
		
	}	
	function getComNum()
	{
	
				$time = getdate();
		$year = $time['year'];
		$month  = $time['mon'];
		$this->db->select('productID,sum(num) as num',false);
		$this->db->group_by('productID');
		$query = $this->db->get(
		"(select * from
			(
					 select * from pos_product_amount
						where ((year= $year  and month<= $month )or year< $year) 
					order by year desc ,month desc
			) as myamount
			 group by productID,shopID ) as a",false
		
		
		
		);
		$data =  $query->result_array();
		foreach($data as $row)
		{
			$index = 'p_'.$row['productID'];
			$result[$index] = $row;	
		}
		return $result;
		
	}
	function getProductRate()
	{
		$this->db->where('type',1);
//		$this->db->where('pos_product_amount.num >',1);
		$this->db->where('flowNum >',1);
	
		$this->db->order_by('flowRate','ASC');
		$this->db->join('pos_product_amount','pos_product.productID =  pos_product_amount.productID and shopID=0');
		//$this->db->limit(120);
		$query = $this->db->get('pos_product');
	
	
		return $query->result_array();
	
		
		
	}
	function getSupplierProduct($supplierID)
	{
		$this->db->select('productID');
		$this->db->where('suppliers',$supplierID);
		$query = $this->db->get('pos_product');
		return $query->result_array();
	}
	function getStockSubTotal ($productID,$to)
	{
		$ymd = explode('-',$to);
		$year = $ymd[0];
		$month = $ymd[1];
		$this->db->select('avgCost');
		$this->db->select('num');
		$this->db->select('month');
		$this->db->select('year');
		$this->db->where('productID',$productID);
		$this->db->where('shopID',"0");
		$this->db->order_by('year','DESC');
		$this->db->order_by('month','DESC');
		$query = $this->db->get('pos_product_amount');
		if($query->num_rows()!=0){
			$data = $query->result_array();
			foreach ($data as $stock) {
				if($stock['year']==$year){
					if($stock['month']<=$month) return $stock['avgCost']*$stock['num'];
				}
				else if($stock['year']<$year){
					return $stock['avgCost']*$stock['num'];
				}
			}
			return 0;
		}
		else return 0;
	}
	function getStockTotal($supplierID, $to)
	{
		$total=0;
		$query = $this->getSupplierProduct($supplierID);
		foreach ($query as $data) {
			$toStr = substr($to,0,10);
			$subtotal = $this->getStockSubTotal($data['productID'],$toStr);
			$total +=$subtotal;
		}
		return $total;
	}
    
    function getShipTotal($year, $shipTotal)
	{
		
		$this->db->select('id');
		$this->db->from('pos_order_shipment');
		$this->db->where('year(shippingTime)',$year);
		$this->db->where('status','3');
		$query =  $this->db->get();
		$query = $query->result_array();
		foreach ($query as $data) {
			$shipTotal = $this->getShipDetail($data['id'],$shipTotal);
		}
		return $shipTotal;
	}
	function getShipDetail($shipmentID, $shipTotal)
	{
		$this->db->select('sellPrice');
		$this->db->select('sellNum');
		$this->db->where('shipmentID',$shipmentID);
		$query =  $this->db->get('pos_order_shipment_detail');
		$query = $query->result_array();
		foreach ($query as $data) {
			$shipTotal['price']+= $data['sellPrice'] * $data['sellNum'];
			$shipTotal['num']+= $data['sellNum'];
		}
        return $shipTotal;
	}
	function getBackTotal($year, $backTotal)
	{
		$this->db->select('id');
		$this->db->from('pos_order_back');
		$this->db->where('year(backTime)',$year);
		$this->db->where('status','4');
		$query =  $this->db->get();
		$query = $query->result_array();
		foreach ($query as $data) {
			 $backTotal = $this->getBackDetail($data['id'],$backTotal);
		}
		return $backTotal;
	}
	function getBackDetail($backID, $backTotal)
	{
		$this->db->select('purchasePrice');
        $this->db->select('reason');
		$this->db->select('num');
		$this->db->where('backID',$backID);
		$query =  $this->db->get('pos_order_back_detail');
		$query = $query->result_array();
		foreach ($query as $data) {
			$backTotal['price']+= $data['purchasePrice'] * $data['num'];
			$backTotal['num']+= $data['num'];
            if(!isset($backTotal['reason'][$data['reason']]))
               {
                   $backTotal['reason'][$data['reason']]['num'] = 0 ;
                   $backTotal['reason'][$data['reason']]['price'] = 0;
                   
                   
               }
                $backTotal['reason'][$data['reason']]['num']+=$data['num'];
                $backTotal['reason'][$data['reason']]['price']+=$data['purchasePrice'] * $data['num'];
		}
        return $backTotal;
	}
    function getAllBackData($year)
    {
        $this->db->select('id');
		$this->db->from('pos_order_back');
		$this->db->where('year(backTime)',$year);
		$this->db->where('status','4');
		$query =  $this->db->get();
		$query = $query->result_array();
        $r = array();
		foreach ($query as $data) {
			 $ret = $this->getBackDetailData($data['id']);
            foreach($ret as $row) $r[] = $row;
		}
		return $r;
        
        
        
    }
    function getBackDetailData($backID)
    {
        
	   $this->db->where('reason',0);
		$this->db->where('backID',$backID);
		$query =  $this->db->get('pos_order_back_detail');
		$data = $query->result_array();
		
        return $data;
        
        
    }
	
	function  getAccountingData($productID,$year,$month)
	{
		
		 $this->db->where('productID',$productID);
		 $this->db->where('year',$year);
		 $this->db->where('month',$month);
		
		$query =  $this->db->get('pos_accounting_io_data');
		$data = $query->row_array();
		if(isset($data['content']))   return $data['content'];
		else return '';
		
	}
    function getAllPackage()
    {
        
         $q = $this->db->get('pos_package');
        return  $q->result_array();
  
    }
    
    
    function checkPackage($productID,$box=true)
    {
        
        if($productID =='8881675') return false; //會員卡50不做打散處理 by taitin
        if($box)
        $this->db->where('boxProductID',$productID);
        else $this->db->where('unitProductID',$productID);
        $q = $this->db->get('pos_package');
        $result['data'] = $q->row_array();
        if($q->num_rows() > 0 )$result['result'] = true;
        else  $result['result'] = false;
        return $result;
        
    }
    
    function deleteAccountData($productID,$year,$month)
    {
        
        $this->db->where('productID',$productID);
            $this->db->where('year',$year);
            $this->db->where('month',$month);
            
			$this->db->delete('pos_accounting_io_data');
        
    }
    
	function insertAccountData($productID,$year,$month,$dataString,$lockKey = 0)
	{
		$datain = array(
			'productID'=>$productID,
			'year'     =>$year,
			'month'    =>$month,
			'content'  =>$dataString,
            'lockKey'  =>$lockKey
		);
		
		
		$st =  $this->getAccountingData($productID,$year,$month);
		if($st!='')
		{
			
			$this->db->where('productID',$productID);
            $this->db->where('year',$year);
            $this->db->where('month',$month);
             $this->db->where('lockKey !=',1);
			$this->db->update('pos_accounting_io_data',$datain);
			
		}
		else
		{
			
			$this->db->insert('pos_accounting_io_data',$datain);
			
			
		}
		
		
		
	}
	
    function productIOCollect($from,$to,$shopID,$productID)
    {
        
        $from = $from.'-01 00:00:00';
        $to = $to.'-31 23:59:59';
        
               
        $data['from'] = $from;
        $data['to']   = $to;
        
        //進
        
        $data['p'] = $this->getPurchase($from,$to,0,$productID);
       
       // $data['b'] = $this->Product_flow_model->getBack($from,$to,0,$productID);
   	   	$data['b'] = array('backNum'=>0,'purchasePrice'=>0);
		//2018 0104 公司退貨已經全部算入 盒損區 因此在這邊不計算 總帳在計算	
        $data['a'] = $this->getAdjust($from,$to,0,$productID);

        //銷
        $data['s'] = $this->getShipmentOut($from,$to,$shopID,$productID);
        
        
        
        //存
        $fromDate = strtotime($from);

        $fromlast =  date('Y-m-d H:i:s', strtotime('-1 sec', $fromDate));

       
        
        $data['o'] = $this-> getAmountByParam($fromlast,$shopID,$productID);//期初
        $data['e'] = $this->getAmountByParam($to,$shopID,$productID);//期末
        return $data;
      
        
        
    }
    
    function getAllShipOut($from,$to)
    {
          $this->db->where('pos_order_shipment_detail.accountTime >=',$from); 
       $this->db->where('pos_product_purchase.accountTime <=',$to); 
        
        
        
        
    }
     function getAllPurchase($from,$to)
    {;
       $this->db->where('pos_product_purchase_inf.accountTime >=',$from); 
       $this->db->where('pos_product_purchase_inf.accountTime <=',$to); 
     $this->db->join('pos_product_purchase_inf','pos_product_purchase_inf.purchaseID = pos_product_purchase.purchaseID');
     $this->db->join('pos_suppliers','pos_product_purchase_inf.supplierID = pos_suppliers.supplierID');
  
       $query = $this->db->get('pos_product_purchase');
       
		$data = $query->result_array();
       return $data;
        
        
    }
    function getAvgCost($year,$month,$productID)
    {
        
      $d =   $this->get_io($year,$month,$productID);
        $totalNum = 0 ; 
        $totalCost = 0 ;
       foreach($d as $row)
       {
           
           $totalNum += $row['O_amount'] + $row['P_amount'];
           $totalCost+= $row['O_totalCost'] + $row['P_totalCost'];
            
           
       }
        if($totalNum!=0) return $totalCost/$totalNum;
        else return 0 ;
    
    }
    
    
    
    function get_io($year,$month,$productID=0, $shopID=-1,$offset=0,$num = 0)
    {
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        if($shopID!=-1) $this->db->where('shopID',$shopID);
        if($productID !=0)$this->db->where('productID',$productID); 
        if($num!=0) $this->db->limit($num,$offset);
        
        $this->db->order_by('productID','ASC');
        $this->db->order_by('shopID','ASC');
        $q = $this->db->get('pos_accounting_table');
        
        
		$data = $q->result_array();
       return $data;
        
        
        
    }
    function insert_o_io($year,$month,$productID,$amount,$totalCost,$shopID= 0)
        {
            $this->db->where('shopID',$shopID);
            $this->db->where('productID',$productID);
            $this->db->where('year',$year);
                $this->db->where('month',$month);
                $datain =array(
                    'year' =>$year,
                    'month' =>$month,
                    'productID' =>$productID,
                    'O_amount'   =>$amount,
                    'O_totalCost'=>$totalCost,
                    'shopID'      =>$shopID
                );
         $q = $this->db->get('pos_accounting_table');
        $t = $q->row_array();
        if(empty($t)) 
        {
            $this->db->insert('pos_accounting_table',$datain);
            
        }
       else
        {
           //print_r($datain);
            $this->db->where('id',$t['id']);
            $this->db->update('pos_accounting_table',$datain);
            
        }
         
                
            
            
            
            
        }
    
    
    function insert_e_io($year,$month,$productID,$amount,$avgCost,$totalCost,$shopID= 0 )
    {
        
        $this->db->where('shopID',$shopID);
        $this->db->where('productID',$productID);
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $datain =array(
            'year' =>$year,
            'month' =>$month,
            'productID' =>$productID,
            'E_amount'   =>$amount,
            'E_totalCost'=>$totalCost,
            'S_avgPrice' =>$avgCost,
            'shopID'      =>$shopID,
            'close'       =>1
        );
         $q = $this->db->get('pos_accounting_table');
        $t = $q->row_array();
        if(empty($t)) 
        {
            $this->db->insert('pos_accounting_table',$datain);
            
        }
       else
        {
            $datain['E_amount'] += $t['E_amount'];
            $datain['E_totalCost'] =  $datain['E_amount'] * $avgCost;
           
           //print_r($datain);
            $this->db->where('id',$t['id']);
            $this->db->update('pos_accounting_table',$datain);
            
        }
       
        /*
        $this->db->where('productID',$productID);
        $this->db->update('pos_product',array('buyPrice'=>$avgCost));
        $sql = 'update pos_product set buyDiscount = buyPrice*100/price where productID='.$productID;;
        $this->db->query($sql);
        */
        
        $nextM = $month+1;
        $nextY = $year;
        if($nextM >12)
        {
            $nextY++;
            $nextM = 1;
            
            
            
        }
        
        
        
        $this->insert_o_io($nextY,$nextM,$datain['productID'],$datain['E_amount'], $datain['E_totalCost'], $datain['shopID']);
        
        
        
        
    }
    function getProductConsignment($year,$month)
    {
        
        $this->db->where('year(startConsignment)',$year);
        $this->db->where('month(startConsignment)',$month);
        $q = $this->db->get('pos_product_consignment');
        return $q->result_array();
        
    }
    function insert_p_io($year,$month,$productID,$amount,$totalCost,$shopID= 0 )
    {
         $this->db->where('consignment !=',1);
        $this->db->where('shopID',$shopID);
        $this->db->where('productID',$productID);
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $datain =array(
            'year' =>$year,
            'month' =>$month,
            'productID' =>$productID,
            'P_amount'   =>$amount,
            'P_totalCost'=>$totalCost
            
        
        
        );
        $q = $this->db->get('pos_accounting_table');
        $t = $q->row_array();
        if(empty($t)) 
        {
            $this->db->insert('pos_accounting_table',$datain);
            
        }
        else
        {
            $datain['P_amount'] += $t['P_amount'];
            $datain['P_totalCost'] += $t['P_totalCost'];
            $this->db->where('id',$t['id']);
            $this->db->update('pos_accounting_table',$datain);
            
        }
            
            
     }
    function moveOut($year,$month,$productID,$amount,$shopID= 0)
    {
        
        
             
       
        //總公司
        
        $this->db->where('shopID',0);
        $this->db->where('productID',$productID);
        $this->db->where('year',$year);
        $this->db->where('month',$month);
         
       // echo $productID.' '. $amount.' '.$totalCost.' '.$shopID.'<br/>';
         $datain =array(
            'year' =>$year,
            'month' =>$month,
            'productID' =>$productID,
            'move'   => -$amount,
             'shopID'=> 0 
            
            
        
        
        );
        $q = $this->db->get('pos_accounting_table');
        $t = $q->row_array();
        if(empty($t)) 
        {
            $this->db->insert('pos_accounting_table',$datain);
        
        }
        else
        {
          
            $datain['move'] += $t['move'];
  
            $this->db->where('id',$t['id']);
            $this->db->update('pos_accounting_table',$datain);
            
        }
         //to 
        
        $this->db->where('shopID',$shopID);
        $this->db->where('productID',$productID);
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $datain['move'] =  $amount;
        $datain['shopID'] =  $shopID;
        $q = $this->db->get('pos_accounting_table');
        $t = $q->row_array();
        if(empty($t)) 
        {
            $this->db->insert('pos_accounting_table',$datain);
        
        }
        else
        {
         //  print_r($t);
           $datain['move'] += $t['move'];   
            $this->db->where('id',$t['id']);
            $this->db->update('pos_accounting_table',$datain);
            
        }
         //to 
        
    }
     function insert_s_io($year,$month,$productID,$amount,$totalPrice,$shopID= 0)      
     {
         $this->db->where('consignment !=',1);   
        $this->db->where('shopID',$shopID);
        $this->db->where('productID',$productID);
        $this->db->where('year',$year);
        $this->db->where('month',$month);
         
       // echo $productID.' '. $amount.' '.$totalCost.' '.$shopID.'<br/>';
         $datain =array(
            'year' =>$year,
            'month' =>$month,
            'productID' =>$productID,
            'S_amount'   =>$amount,
            'S_totalSellPrice'=>$totalPrice,
             'shopID' => $shopID
            
        
        
        );
        $q = $this->db->get('pos_accounting_table');
        $t = $q->row_array();
        if(empty($t)) 
        {
            $this->db->insert('pos_accounting_table',$datain);
        
        }
        else
        {
            $datain['S_amount'] += $t['S_amount'];
            $datain['S_totalSellPrice'] += $t['S_totalSellPrice'];
            $this->db->where('id',$t['id']);
            $this->db->update('pos_accounting_table',$datain);
            
        }
         
     }
            
   
        
}

?>