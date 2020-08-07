<?php 
class Order_model extends Model {
	function Order_model()
	{
		
	  // 呼叫模型(Model)的建構函數''
        parent::Model();
	}
	function getOrderAddress($shopID)
	{
		$this->db->where('deleteToken',0);
		$this->db->where('shopID',$shopID);
			$this->db->order_by('defaultToken','desc');
		$query = $this->db->get('pos_order_address');
	
		return $query->result_array();
		
	}
	
	function getCheckRecord($shopID,$year,$month)
	{
	
		$this->db->select('pos_check_record.*,name');
		$this->db->where('year(date)',$year,false);
		$this->db->where('month(date)',$month,false);
		if($shopID!=0)$this->db->where('pos_check_record.shopID',$shopID);
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_check_record.shopID','left');
		$this->db->order_by('date');
				$query = $this->db->get('pos_check_record');
	
		return $query->result_array();
		
	}
	function orderAddress($receiver,$address,$phone,$shopID,$comID='',$CarrierType='',$CarrierId1='',$NPOBAN='',$email='')
	{
		$this->db->where('receiver',(string)$receiver);
		$this->db->where('address',(string)$address);
		$this->db->where('phone',(string)$phone);
		$this->db->where('deleteToken',0);
		$query = $this->db->get('pos_order_address');
		if($query->num_rows()>0)
		{
			
			$data = $query->row_array();	
			$addressID =  $data['id'];
             $this->db->where('id',$addressID);
        $this->db->update('pos_order_address',array('comID'=>$comID,'CarrierType'=>$CarrierType,'CarrierId1'=>$CarrierId1,'NPOBAN'=>$NPOBAN,'email'=>$email));
            return $addressID;
		}
		else
		{
		
			$datain =array(
			 'receiver'=>$receiver,
			 'address'=>$address,
			 'phone'=>$phone,
             'comID'=>$comID,
             'CarrierType'=>$CarrierType,
             'CarrierId1'=>$CarrierId1,
             'NPOBAN'=>$NPOBAN,
            'email' =>$email,
			 'shopID'=>$shopID
			
			);
			$this->db->insert('pos_order_address',$datain)	;
			return $this->db->insert_id();
		}
		
	}
	function getOtherMoney($year,$month,$shopID=0)
	{
		$this->db->select('pos_other_money.*,name');
			if($shopID!=0)	$this->db->where('shopID',$shopID);
		$this->db->where('year',$year);
		$this->db->where('month',$month);
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_other_money.shopID','left');
		$query = $this->db->get('pos_other_money');
		return $query->result_array();	
		
	}
	
	
	function preOrderUpdate($shopID,$year,$month,$productID,$num)
	{
	
			$this->db->where('pos_order_pre.shopID',$shopID);
			$this->db->where('pos_order_pre.year',$year);
			$this->db->where('pos_order_pre.month',$month);
			$this->db->where('pos_order_pre.productID',$productID);
			$query = $this->db->get('pos_order_pre');
			if($query->num_rows()>0)
			{
					$this->db->where('pos_order_pre.shopID',$shopID);
					$this->db->where('pos_order_pre.year',$year);
					$this->db->where('pos_order_pre.month',$month);
					$this->db->where('pos_order_pre.productID',$productID);
					$this->db->update('pos_order_pre',array('num'=>$num));
			}
			else
			{
				$datain = array(
					
					'shopID'=>$shopID,
					'year'=>$year,
					'month'=>$month,
					'productID'=>$productID,
					'num'=>$num,
				)	;
				$this->db->insert('pos_order_pre',$datain);
				
			}
				

		
	}
	function preOrderCheck($shopID,$year,$month)
	{
			$this->db->where('pos_order_pre.shopID',$shopID);
			$this->db->where('pos_order_pre.year',$year);
			$this->db->where('pos_order_pre.month',$month);
			$query = $this->db->get('pos_order_pre');
		if($query->num_rows()>0) return true;
		else return false;
	}
	function getPreOrderNum($shopID,$year,$month,$productID)
	{
			$this->db->where('pos_order_pre.shopID',$shopID);
			$this->db->where('pos_order_pre.year',$year);
			$this->db->where('pos_order_pre.month',$month);
			$this->db->where('pos_order_pre.productID',$productID);
			$query = $this->db->get('pos_order_pre');
			if($query->num_rows()>0) 
			{
				$data = $query->row_array();
				return $data['num'];
			}
			else return 0;
		
		
		
	}
	function getPreOrderSum($year,$month)
	{
		
			$this->db->select('pos_order_pre.*,pos_product.ZHName');
			$this->db->join('pos_product','pos_product.productID = pos_order_pre.productID','left');
			$this->db->where('pos_order_pre.year',$year);
			$this->db->where('pos_order_pre.month',$month);
			$this->db->order_by('pos_product.suppliers','DESC');
			$this->db->order_by('pos_product.productID','DESC');
			$this->db->order_by('shopID','ASC');
			$query = $this->db->get('pos_order_pre');
			return $query->result_array();	
		
		
		
		
	}
	
	
	
	function getPreOrder($shopID,$year,$month)
	{
		$find['top150'] = 10;//
		$data = $this->getProduct($find,$shopID,'get');
		
		$i = 0 ;
		foreach($data as $row)
		{
			$this->db->join('pos_order_pre','pos_top_product.productID = pos_order_pre.productID','left');
			$this->db->where('pos_order_pre.shopID',$shopID);
			$this->db->where('pos_order_pre.year',$year);
			$this->db->where('pos_order_pre.month',$month);
			$this->db->where('pos_top_product.top10',1);
			$this->db->where('pos_order_pre.productID',$row['productID']);
			$query = $this->db->get('pos_top_product');
			$ret = $query->row_array();	
			$result[$i] = $row;
			if(isset($ret['num']))$result[$i]['orderNum'] = $ret['num'];
			else $result[$i]['orderNum'] = 0;
			$result[$i]['comNum'] = '?';
			
			$i++;	
		}
		return $result;
		
	}
	function getPrePayOrder($shopID)
	{
		$this->db->select('pos_product.*,pos_prepay_order.*,IFNULL(pos_prepay_order_list.orderNum,0) as orderNum',false);
		$this->db->join('pos_product','pos_product.productID = pos_prepay_order.productID ','left');
		$this->db->join('pos_prepay_order_list','pos_prepay_order_list.productID = pos_prepay_order.productID and pos_prepay_order_list.shopID ='.$shopID,'left');
		
		$this->db->order_by('suppliers','DESC');
		$this->db->order_by('ZHName','DESC');
		$query = $this->db->get('pos_prepay_order');
		
		return $query->result_array();	
	}
	
	
	function getPrePayOrderByProductID($productID)
	{
		
		$this->db->where('productID',$productID);
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_prepay_order_list.shopID ','left');
		$query = $this->db->get('pos_prepay_order_list');
			return $query->result_array();	
		
	}
	
	
	function getOrderDetailByID($id)
	{
		$time = getdate();
		$year = $time['year'];
		
		$month = $time['mon'];
        if($id<66466) $db = 'pos_old_order_detail';
        else $db = 'pos_order_detail';
		
		$sql ="SELECT ".$db.".id as rowID,".$db.".*,".$db.".buyNum as OSBANum,pos_product.*,".$db.".comment as orderComment,concessions,concessionsNum
				FROM ".$db."
				LEFT JOIN pos_product ON ".$db.".productID = pos_product.productID
				LEFT JOIN
				(SELECT productID,group_concat(pos_product_discount.discount ORDER BY discount DESC SEPARATOR ',') as concessions,
						group_concat(pos_product_discount.num ORDER BY discount DESC SEPARATOR ',') as concessionsNum
				FROM pos_product_discount GROUP BY productID) as c
				ON c.productID = pos_product.productID
	
				 WHERE ".$db.".orderID =$id ORDER BY pos_product.productNum ASC
				";
	
		$query = $this->db->query($sql);	
		$product =  $query->result_array();
		
		$i = 0;
		foreach($product as $row)
		{
			$product[$i]['num'] = $this->PO_model->getProductNum(0,$row['productID'],$year.'-'.$month);
			
			$i++;
			
		}		
		
		return $product;

		
	}
	function getShipmentDetailByID($id,$showing=0,$type =-1 ,$orderID=0)
	{
		
		$sql ="
		SELECT pos_order_shipment_detail.id as srowID,pos_order_shipment_detail.*,pos_order_shipment_detail.sellPrice as purchasePrice,pos_order_shipment_detail.sellNum as OSBANum,pos_order_detail.buyNum,pos_order_detail.orderID
		       ,pos_product.*,pos_order_shipment_detail.comment as orderComment,pos_order.orderNum,pos_order_detail.comment,pos_ec_order.transportID
				FROM pos_order_shipment_detail
				LEFT JOIN pos_order_detail ON pos_order_detail.id = pos_order_shipment_detail.rowID
				LEFT JOIN pos_order ON pos_order_detail.orderID = pos_order.id
                LEFT JOIN pos_ec_order ON pos_ec_order.orderID = pos_order.id
				LEFT JOIN pos_product ON pos_order_detail.productID = pos_product.productID
                LEFT JOIN pos_order_shipment ON pos_order_shipment_detail.shipmentID = pos_order_shipment.id
				 WHERE pos_order_shipment_detail.shipmentID =$id";
        if($type!=-1)	$sql.=" and pos_order_shipment.type  = $type  "	 ;
        if($orderID!=0)	$sql.=" and pos_order.id  = $orderID  "	 ;
        $sql.= " ORDER BY ";
		if($showing==1)	$sql.=" pos_order.orderNum ASC,  "	 ;
		$sql.=" pos_product.cabinet ASC,pos_product.productNum ASC
				";

		
		$query = $this->db->query($sql);	
		return $query->result_array();

		
	}
	function getShipmentTodayDetail($type,$date=0)
	{
		
		$sql ="
		SELECT pos_order_shipment_detail.id as srowID,pos_order_shipment_detail.*,pos_order_shipment_detail.sellPrice as purchasePrice,sum(pos_order_shipment_detail.sellNum) as OSBANum,pos_order_detail.buyNum,pos_order_detail.orderID
		       ,pos_product.*,GROUP_CONCAT(pos_order_shipment_detail.comment) as orderComment,pos_order.orderNum,pos_order_detail.comment
				FROM pos_order_shipment
				LEFT JOIN pos_order_shipment_detail ON pos_order_shipment.id = pos_order_shipment_detail.shipmentID
				LEFT JOIN pos_order_detail ON pos_order_detail.id = pos_order_shipment_detail.rowID
				LEFT JOIN pos_order ON pos_order_detail.orderID = pos_order.id
				LEFT JOIN pos_product ON pos_order_detail.productID = pos_product.productID
				 WHERE pos_order_shipment.status = ".$type." and date(pos_order_shipment.shippingTime) = '".$date."'
				group by pos_product.productID 
				  ORDER BY ";
		$sql.=" pos_product.cabinet ASC,pos_product.productNum ASC
				";


		
		$query = $this->db->query($sql);	
		return $query->result_array();

		
	}
    function getShipmentDetailByIDList($idList)
	{
		
       
       
        $idArray = explode('-',$idList);
        
		$sql ="
		SELECT pos_order_shipment_detail.id as srowID,pos_order_shipment_detail.*,pos_order_shipment_detail.sellPrice as purchasePrice,sum(pos_order_shipment_detail.sellNum) as OSBANum,pos_order_detail.buyNum,pos_order_detail.orderID
		       ,pos_product.*,GROUP_CONCAT(pos_order_shipment_detail.comment) as orderComment,pos_order.orderNum,pos_order_detail.comment
				FROM pos_order_shipment
				LEFT JOIN pos_order_shipment_detail ON pos_order_shipment.id = pos_order_shipment_detail.shipmentID
				LEFT JOIN pos_order_detail ON pos_order_detail.id = pos_order_shipment_detail.rowID
				LEFT JOIN pos_order ON pos_order_detail.orderID = pos_order.id
				LEFT JOIN pos_product ON pos_order_detail.productID = pos_product.productID";
        $sql .=" WHERE pos_order_shipment.id = 0 ";
      
        foreach( $idArray as $row)
        {    
            if(!empty($row) )  $sql .= " or  pos_order_shipment.id = ".$row;
        
        }
       
				
        $sql.="	group by pos_product.productID 
				  ORDER BY ";
		$sql.=" pos_product.cabinet ASC,pos_product.productNum ASC
				";

		//echo $sql;
		$query = $this->db->query($sql);	
		return $query->result_array();

		
	}
		
		
	//symbol must be > >= = <= < or false for not define	
	function getAttachConsignment($shopID,$year,$month)
	{
	
		$distributeType = $this->getDistributeType($shopID);
		$sql = "
			SELECT pos_product.*,round((IFNULL(d.productDiscount,IFNULL(e.discount,f.discount))*Price/100),0) as sellPrice FROM
			(
			SELECT pos_consignment.shopID,pos_consignment.productID
			FROM (`pos_consignment`) 
			WHERE `pos_consignment`.`shopID` = $shopID AND deleteToken='0000-00-00'
			) as a
			left join pos_product ON pos_product.productID = a.productID
			 
			LEFT JOIN pos_order_rule as e ON e.productID = pos_product.productID and e.distributeType= 0  and e.num = 0
			LEFT JOIN
			(
			SELECT productID,pos_order_rule.discount as productDiscount,pos_order_distribute.discount as shopDiscount FROM
			 pos_order_rule 
			LEFT JOIN pos_order_distribute ON pos_order_distribute.id = pos_order_rule.distributeType
			 where pos_order_rule.distributeType =$distributeType and pos_order_rule.num = 0
		
			) AS d
			ON pos_product.productID = d.productID 
			LEFT JOIN pos_order_distribute as f ON  f.id = $distributeType";
		
	
			
		$query = $this->db->query($sql);
		$data = $query->result_array();	
        $result = array();
		foreach($data as $row)
		{
			$row['num'] = $this->PO_model->getProductNum($shopID,$row['productID'],$year.'-'.$month);
			$row['sellNum'] = $row['num'];
			if($row['num'] <1) $result[] = $row;
			
		}		

		
		return $result;
		
		
		
	}
	function getAllProductOnOrder()
	{
		$orderList = $this->getOrderList(0,0,0,-1);
	
		foreach($orderList as $row)
		{
			$orderIDList[] = $row['id'];
			
		}
		
		$this->db->select('pos_order_detail.productID,suppliers,pos_suppliers.name as supplierName,orderID,group_concat(pos_sub_branch.name) as shopList,pos_product_preTime.preTime,
		group_concat(pos_order.orderNum) as orderNumList');
		$this->db->where_in('orderID',$orderIDList);
		$this->db->where('pos_order_detail.status',0);
		$this->db->where('pos_order.status !=',0);
		$this->db->where('pos_order.status !=',4);
		$this->db->where('pos_order.status !=',3);
		$this->db->group_by('pos_order_detail.productID');
		
		$this->db->join('pos_order','pos_order.id = pos_order_detail.orderID','left');
		$this->db->join('pos_sub_branch','pos_order.shopID = pos_sub_branch.shopID','left');


		$this->db->join('pos_product','pos_product.productID = pos_order_detail.productID','left');
		$this->db->join('pos_product_preTime','pos_product.productID = pos_product_preTime.productID','left');
		$this->db->join('pos_suppliers','pos_product.suppliers = pos_suppliers.supplierID','left');

		$this->db->order_by('pos_product.suppliers,pos_product.productNum ASC');
		$query = $this->db->get('pos_order_detail');
		
	
		return $query->result_array();
		
	}
	
	function getWeekMagicOrder()
	{
		$this->db->where('pos_product.type',8);
		$this->db->where('openStatus',1);
		$query = $this->db->get('pos_product');
		$product = $query->result_array();
		$result = array();
		foreach($product as $row)
		{
			
			$r = $this->getAvailableNum($row['productID'],true);
		
			$directOrderNum = $this->magicOrderNum($row['productID']);
		
			$r['nowNum'] +=  $directOrderNum;
			 $r['buyNum']-= $directOrderNum;
			if($r['nowNum']<0)
			{
				$row['nowNum'] = $r['nowNum'];
				$row['buyNum'] = $r['buyNum'];
				$result[] = $row;
			}
			
		}
		
		return $result;
		
		
	}
    
    function getProductShipment($productID,$from,$to,$orderType)
    {
        $this->db->select('pos_order_shipment.*,pos_order_shipment_detail.*,pos_direct_branch.id as dirShop');
        $this->db->where('sProductID',$productID);
        $this->db->join('pos_order_shipment','pos_order_shipment.id = pos_order_shipment_detail.shipmentID','left');
         $this->db->join('pos_direct_branch','pos_direct_branch.shopID = pos_order_shipment.shopID','left');
        $this->db->where('shippingTime >=',$from);
        $this->db->where('shippingTime <=',$to);
        $this->db->where('status >=',2);
        $this->db->where('status <=',3);
        $this->db->where('type',$orderType);
        $query = $this->db->get('pos_order_shipment_detail');
        return $query->result_array();
        
    }
    
    
    
    
	function magicOrderNum($productID)
	{
			
		$this->db->where('productID',$productID);
		$this->db->where('pos_magic_order.status !=',5);
		$this->db->where('pos_order_detail.status',0);
		$this->db->join('pos_magic_order','pos_magic_order.orderID = pos_order_detail.orderID','left');
		$query = $this->db->get('pos_order_detail');
		$product = $query->result_array();
		$num = 0;
		foreach($product as $row)
		{
			
			$num +=$row['buyNum'];
		}
		return $num;
	}
	
		
	function getProductInOrder($productID,$status,$shopID=0)
	{
	
		$orderList = $this->getOrderList($shopID,0,0,-1);
		foreach($orderList as $row)
		{
			$orderIDList[] = $row['id'];
			
		}
		
		if(isset($orderIDList))
		{
		$this->db->where('productID',$productID);
		$this->db->where('status',0);
		$this->db->where_in('orderID',$orderIDList);
		$this->db->select('buyNum,sellNum');
		$query = $this->db->get('pos_order_detail');
		$data = $query->result_array();
		}
		else $data = array();
		
		$total = 0;
		foreach($data as $row)
		{
			
			//if($row['sellNum']<0||$row['buyNum']<0) echo $productID.'('.$row['sellNum'].')('.$row['buyNum'].')<br/>';
			if($row['buyNum'] == 0 )	$total+=$row['sellNum'];  //表示沒訂貨但有單，因此看出貨
			else if ($row['sellNum'] ==0||$row['sellNum'] ==-1)  $total+=$row['buyNum'];//有訂貨，1.可能沒貨2.還沒出貨()(算為預計出貨)
			else $total+=$row['sellNum'];//有訂貨，也有出貨，就算最後出貨量而定
			
		}
		return $total;
	}
	
	
	function getProductInorderList($productID)
	{

			$this->db->select('pos_sub_branch.name as shopName,pos_order_detail.id as odID,pos_order_detail.id as rowID,pos_order_detail.id as orderDetailID,pos_order_detail.*,pos_order.*,pos_magic_order.id as magic');
			$this->db->where('pos_order.status !=',0);//尚未出貨，或是尚未完全出貨
			$this->db->where('pos_order_detail.status',0);//尚未出貨，或是尚未完全出貨。
			$this->db->where('pos_order_detail.productID',$productID);
			$this->db->join('pos_order','pos_order.id = pos_order_detail.orderID');
			$this->db->join('pos_magic_order','pos_order.id = pos_magic_order.orderID','left');
			$this->db->join('pos_sub_branch','pos_order.shopID = pos_sub_branch.shopID','left');
			$this->db->order_by('pos_order.orderTime');
			$query = $this->db->get('pos_order_detail');
			return  $query->result_array();
	}
	function getProductNumExceptOrder($productID,$shopID)
	{
	
		$this->db->where('productID',$productID);
		$this->db->where('shopID',$shopID);

		$query = $this->db->get('pos_current_product_amount');
		$data = $query->row_array();

		
	
		
		
		$order = $this->getProductInorderList($productID);
	
		if(isset($data['num']))
		{
			// martget savity num
			$saveNum = $this->getMartSaveNum($productID);
            if($shopID==0) $saveNum = 0 ; 
            //$saveNum = 0; //20190423 暫時取消電商扣貨機制 by 小南
            //20200608 開啟電商安全庫存
            
            
			$data['num'] -= $saveNum;
			foreach($order as $row)
			{
				$data['num'] -= $row['sellNum'];
			}
			
            
            
            
            
            
            return $data['num'];
            
		}
		else return 0 ;
		
		
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
	function allocateOrder($productID,$nowNum = 0)
	{
		$order = $this->getProductInorderList($productID);
		
		$stockNum = $nowNum;
		$totalBuyNum = 0;	
		$shopBuyNum = 0;	
		
		//計算總訂貨量
			$result =array();
		foreach($order as $row)
		{
			if($row['magic']!='') continue;
			
			$totalBuyNum+=$row['buyNum'];
			if($row['shopID']<=1000)$shopBuyNum+=$row['buyNum'];
			$result[] = array(
					'id'  =>$row['odID'],
					'rowID'  =>$row['rowID'],
					'orderID' => $row['orderID'],
					'shopID' => $row['shopID'],
					'sellNum' =>0
				
				)	;
		}
		
		//產品部先補齊
		$i = 0 ;
	
		foreach($order as $row)
		{
			if($row['magic']!='') continue;
			if($nowNum>0 &&$row['shopID']==1040)
			{
				
				
				  $NUM = $row['buyNum'];
				if($nowNum<$NUM) $NUM = $nowNum;
				$result[$i]['sellNum'] = $NUM;
		
				$nowNum = $nowNum - $NUM;
			}			
			
			
			$i++;
		}
		
		
		
		
		
		//每店先分配1個
		$i = 0 ;
		foreach($order as $row)
		{
			if($row['magic']!='') continue;
			if($nowNum>0 &&isset($row['shopID'])&&!isset($shop[$row['shopID']]) && $row['shopID']<1000)
			{
				$shop[$row['shopID']]=true;
				$result[$i]['sellNum'] ++;
				$nowNum--;
				$stockNum--;
			}
			$i ++;
		}
		
		
		
		
		
		
		
		//按瘋桌遊店家比例分配
		$i = 0 ;
	
		foreach($order as $row)
		{
			if($row['magic']!='') continue;
			if($nowNum>0 && $totalBuyNum>0 &&$row['shopID']<=1000 && $shopBuyNum>0)
			{
				
				
				$NUM = round($row['buyNum']*$stockNum/ $shopBuyNum);
				if($nowNum<$NUM) $NUM = $nowNum;
				if(($row['buyNum']-$result[$i]['sellNum'])<$NUM) $NUM = $row['buyNum'] - $result[$i]['sellNum'];
				$result[$i]['sellNum'] += $NUM;
		
				$nowNum = $nowNum - $NUM;
			}			
			
			
			$i++;
		}
		
		//按店家比例分配
		$i = 0 ;
	
		foreach($order as $row)
		{
			if($row['magic']!='') continue;
			if($nowNum>0 && $totalBuyNum>0 && $totalBuyNum-$shopBuyNum>0)
			{
				
				$NUM = round($row['buyNum']*$stockNum/ ($totalBuyNum-$shopBuyNum));
				if($nowNum<$NUM) $NUM = $nowNum;
				if(($row['buyNum']-$result[$i]['sellNum'])<$NUM) $NUM = $row['buyNum'] - $result[$i]['sellNum'];
				$result[$i]['sellNum'] += $NUM;
		
				$nowNum = $nowNum - $NUM;
			}			
			
			
			$i++;
		}
		
		//剩餘分配
		//按店家比例分配
		$i = 0 ;
	
		foreach($order as $row)
		{
			if($row['magic']!='') continue;
			if($nowNum>0 && $row['buyNum'] - $result[$i]['sellNum'])
			{
				
				$NUM = $row['buyNum'] - $result[$i]['sellNum'];
				if($nowNum<$NUM) $NUM = $nowNum;
				 
				$result[$i]['sellNum'] += $NUM;
		
				$nowNum = $nowNum - $NUM;
			}			
			
			
			$i++;
		}
		
		
		
		return $result;
		
		
	}
	
	function reAllocateOrder($productID)
	{
		$this->db->where('productID',$productID);
		$this->db->where('shopID',0);
		$this->db->order_by('year','DESC');
		$this->db->order_by('month','DESC');
		$query = $this->db->get('pos_product_amount');
		$product = $query->row_array();
			if(!isset($product['num'])) $nowNum = 0;
		else $nowNum = $product['num'];
		$result  =  $this->allocateOrder($productID,$nowNum);
		if(isset($result))
		{
			foreach($result as $row)
			{
				$this->db->where('id',$row['id']);
				$this->db->update('pos_order_detail',array('sellNum'=>$row['sellNum']));
				
				
			}
		}
		
		return $nowNum;
	}
	
	function getMaxShippingNum()
	{
		$this->db->select_max('shippingNum');
		$query=$this->db->get('pos_order_shipment');
		$data = $query->row_array();
		return $data['shippingNum'];
		
	}	
	
	
	function getMaxOrderNum()
	{
		$this->db->select_max('orderNum');
		$query=$this->db->get('pos_order');
		$data = $query->row_array();
		return $data['orderNum'];
		
	}
	
	function getOrderList($shopID,$offset,$num,$arive,$orderType = 2,$year=0,$month = 0)
	{

        
		if($shopID==-1)
		{
			 $this->db->where('pos_sub_branch.jointype !=',1);
		}
		elseif($shopID==-2)
		{
			 $this->db->where('pos_sub_branch.jointype',1);
		}
		elseif($shopID!=0)$this->db->where('pos_order.shopID',$shopID);
        //買斷+調貨
        if($orderType==3)$this->db->where('pos_order.type != ',1);
        else if($orderType!=2)$this->db->where('pos_order.type',$orderType);
        
        
		
		$this->db->where('status >',0);
		if($arive=='true'&&$arive!=-1)  
		{
		
			$this->db->where('status >=',2);
			 $this->db->where('status !=',4);	
			$this->db->order_by('shippingTime','DESC');
		}
		else if($arive=='false') 
		{
			
			 $this->db->where('status >=',2);
			  $this->db->where('status <=',3);	
			  $this->db->order_by('shippingTime','DESC');		
		}

		else if($arive==0) 
		{
			
			$this->db->where('status !=',3);	
			
		
		}
		else if($arive==1)
		{
			$this->db->where('status ',1);
			
		
		}
		else if($arive== - 1)
		{
			
			 $this->db->where('status ',1);

		}
		else if($arive== 4)
		{
			 $this->db->where('status ',4);
			 //$this->db->or_where('status ',3);
		}
		else if($arive== 3)
		{
			 $this->db->where('status',3);
			 //$this->db->or_where('status ',3);
		}	
		else if($arive== 2)
		{
		
			 $this->db->where('status',2);
			 //$this->db->or_where('status ',3);
		}						
		else if($arive== -2)
		{
			  $this->db->where('status !=',0);
			 //不 sort
		}
        else if($arive== 6) //寶可夢訂單
		{
			$this->db->join('pos_ptcg_order','pos_ptcg_order.orderID = pos_order.id','left');
             $this->db->where('pos_ptcg_order.id >',0);
			 //不 sort
		}	
		else
		{
			 $this->db->where('status >=',2);
			  $this->db->where('status <=',3);
			 //$this->db->or_where('status ',3);
		}
		
        
        if($year!=0 && $month!=0)
        {
            
            $this->db->where('orderTime >=',$year.'-'.$month.'-01');
            $this->db->where('orderTime <=',$year.'-'.$month.'-31');
            
            
            
        }
		
		$this->db->select('pos_order.*,pos_sub_branch.name as shopName,pos_order_address.receiver');
		$this->db->join('pos_sub_branch','pos_order.shopID = pos_sub_branch.shopID','left');
		$this->db->join('pos_order_address','pos_order_address.id = pos_order.addressID','left');
		if($num>0)$this->db->limit($num,$offset);
		$this->db->order_by('orderNum','DESC');
		$query = $this->db->get('pos_order');
		$this->db->insert('pos_test',array('content'=>$this->db->last_query()));
		return $query->result_array();
		
	}
	
	function checkAllProductStatus($orderID)
	{
		$this->db->where('orderID',$orderID);
		$query = $this->db->get('pos_order_detail');
		$data = $query->result_array();
		foreach($data as $row)
		{
			if($row['status']==0)return false;
			
		}
		 $this->db->where('id',$orderID);
		 $this->db->update('pos_order',array('status'=>2));
		return true;
				
	}
	
	function countTotal($orderID)
	{
		$this->db->where('orderID',$orderID);
		$query = $this->db->get('pos_order_detail');
		$data = $query->result_array();
		$total = 0 ;
		foreach($data as $row)
		{
			$total+=$row['buyNum'] *$row['sellPrice'];
			
		}
		 $this->db->where('id',$orderID);
		 $this->db->update('pos_order',array('total'=>$total));
		return true;
				
	}	
	function getShipmentList($shopID,$offset,$num,$arive,$orderType = 2,$fromDate=0,$toDate=0)
	{
		
		if($shopID==-1)
		{
			 $this->db->where('pos_sub_branch.jointype !=',1);
		}
		elseif($shopID==-2)
		{
			 $this->db->where('pos_sub_branch.jointype ',1);
		}
		elseif($shopID!=0)$this->db->where('pos_order_shipment.shopID',$shopID);
		if($orderType!=2)$this->db->where('pos_order_shipment.type',$orderType);
		$this->db->where('status >',0);

         if($arive== 4)//訂單完成
		{
			
			 $this->db->where('status',4);
			 //$this->db->or_where('status ',3);
		}
		else if($arive== 3)//到貨
		{
			 $this->db->where('status',3);
			 //$this->db->or_where('status ',3);
		}	
		else if($arive== 2)//送物流
		{
		
			 $this->db->where('status',2);
			 //$this->db->or_where('status ',3);
		}		
		else if($arive== 15)//送物流或已到貨
		{
		
			 $this->db->where('status >=',2);
			 $this->db->where('status <=',3);
			 //$this->db->or_where('status ',3);
		}						
		if($fromDate!=0&&$toDate!=0)
		{
			
			$this->db->where('pos_order_shipment.shippingTime >=',$fromDate.' 00:00:00');
			$this->db->where('pos_order_shipment.shippingTime <=',$toDate.' 23:59:59');
		}
		
		
		$this->db->select('pos_order_shipment.id as shipmentID,pos_order_shipment.*,pos_sub_branch.name as shopName,pos_order_address.receiver,pos_sub_branch.distributeType');
		$this->db->join('pos_sub_branch','pos_order_shipment.shopID = pos_sub_branch.shopID','left');
		$this->db->join('pos_order_address','pos_order_address.id = pos_order_shipment.addressID','left');
		if($num>0)$this->db->limit($num,$offset);
       
		$this->db->order_by('shippingTime','DESC');
		$query = $this->db->get('pos_order_shipment');
		
		return $query->result_array();
			
		
		
	}
	function getInvoice($id)
	{
		$this->db->where('shipmentID',$id);
        $this->db->join('pos_einvoice','pos_einvoice.InvoiceNumber=pos_order_invoice.invoice','left');
		$query = $this->db->get('pos_order_invoice');
		$r= $query ->result_array();
        $result = array();
        foreach($r as $row)
        {
            
            $row['code'] =  md5('IlovePhantasia'.$row['InvoiceNumber']);
            $result[] = $row;
            
        }
		return $result;
	}
    
    function getECInvoice($id)
    {
        
       $this->db->where('ECID',$id);
        $this->db->join('pos_einvoice','pos_einvoice.InvoiceNumber=pos_ec_invoice.invoice','left');
		$query = $this->db->get('pos_ec_invoice');
		$r= $query ->result_array();
        $result = array();
        foreach($r as $row)
        {
            
            $row['code'] =  md5('IlovePhantasia'.$row['InvoiceNumber']);
            $result[] = $row;
            
        }
		return $result; 
        
    }
	
	function shipmentListHandle($shopID,$offset,$num,$arive,$orderType,$fromDate,$toDate,$invoice = false)
	{
		
		$data['shipmentList'] = $this->getShipmentList($shopID,$offset,$num,$arive,$orderType,$fromDate,$toDate);
		$i = 0;
        if(!empty($data['shipmentList']))
		foreach($data['shipmentList'] as $row)
		{
			$data['shipmentList'][$i]['shippingTime']	 = substr($row['shippingTime'],0,10);
			$data['shipmentList'][$i]['status'] = $this->changeOrderStatus($row['status']);
			$data['shipmentList'][$i]['colorStatus']= $this->changeColorOrderStatus($row['status']);
			if($invoice) $data['shipmentList'][$i]['invoice']= $this->getInvoice($row['id']);
			$i++;
		}
		return $data['shipmentList'];
	}
	function getWeekCheck($shopID,$year,$month,$week)
	{
		$d = explode('-',$week);
		//目前 僅限 魔風  補充包。
		$sql ="SELECT pos_order_shipment.*,pos_product.*,pos_order_shipment_detail.* FROM
				( select * from  pos_order_shipment 
					WHERE shopID= $shopID  and 
					 year(shippingtime)=$year and month(shippingtime) =$month and day(shippingtime)>=".$d[0]." and day(shippingtime)<=".$d[1]."  and pos_order_shipment.status>1 
					 and pos_order_shipment.status<4 and pos_order_shipment.type =0
				
				
				) as pos_order_shipment
				LEFT JOIN pos_order_shipment_detail ON pos_order_shipment.id = pos_order_shipment_detail.shipmentID
				LEFT JOIN pos_order_detail ON pos_order_detail.id = pos_order_shipment_detail.rowID
				LEFT JOIN pos_product ON pos_order_detail.productID = pos_product.productID
				order by pos_order_shipment.shippingNum
					 
					 ";

		 $query= $this->db->query($sql);
        $data =  $query->result_array();
		$result = array();
		foreach($data as $row)
		{
			if($row['type']==8)
			{
				 $result[] = $row;	
			}	
			
		}
		return $result;
		
	}
	
	function getMonthCheck($shopID,$year,$month)
	{

		$sql ="SELECT pos_order_shipment.*,pos_product.*,pos_order_shipment_detail.* FROM pos_order_shipment 
				LEFT JOIN pos_order_shipment_detail ON pos_order_shipment.id = pos_order_shipment_detail.shipmentID
				LEFT JOIN pos_order_detail ON pos_order_detail.id = pos_order_shipment_detail.rowID
				LEFT JOIN pos_product ON pos_order_detail.productID = pos_product.productID
				WHERE pos_order_shipment.status>1 and pos_order_shipment.status<4 and pos_order_shipment.type!=1 
				     and shopID= $shopID and 
					 year(shippingtime)=$year and month(shippingtime) =$month
				order by pos_order_shipment.shippingNum,cabinet,productNum	 
					 
					 ";

		 $query= $this->db->query($sql);
		return  $query->result_array();
		
		
	}
	
	function getMonthTotal($shopID,$year,$month)
	{
		$sql ="SELECT sum(pos_order_shipment_detail.sellPrice *pos_order_shipment_detail.sellNum) as total FROM pos_order_shipment 
				LEFT JOIN pos_order_shipment_detail ON pos_order_shipment.id = pos_order_shipment_detail.shipmentID
				WHERE pos_order_shipment.status>1 and pos_order_shipment.status<4 and pos_order_shipment.type!=1
				 and shopID= $shopID and year(shippingtime)=$year and month(shippingtime) =$month
				 group by shopID";
				 

		 $query= $this->db->query($sql);
		 $data = $query->row_array();
		return $data['total'];
		
	}
	
	function getMartSaveNum($productID)
	{
			$this->db->where('productID',$productID);
						$q = $this->db->get('pos_mart_save_amount');
						$ret = $q->row_array();
						
						if(isset($ret['num']))	$saveNum = $ret['num'];
						else $saveNum = 0;
		return $saveNum;
	}
	
	function getShipmentAddressList($shopID)
	{
		
		$time = getdate();
		$year = $time['year'];	
		$month = $time['mon'];	
		$this->db->select('*,pos_order_detail.id as PID,pos_order.type as orderType,pos_order_detail.id as rowID');
		$this->db->where('pos_order.shopID',$shopID);
		$this->db->where('pos_order.status !=', 0);
		$this->db->where('pos_order_detail.status ', 0);
		$this->db->join('pos_order_detail','pos_order_detail.orderID = pos_order.id','left');
		$this->db->join('pos_order_address','pos_order_address.id = pos_order.addressID','left');
		$this->db->order_by('pos_order_address.defaultToken','desc');
		$this->db->group_by('pos_order.addressID');
		
		
		$query = $this->db->get('pos_order');
		$data = $query->result_array();
		
		return $data;
		
	}

	function getPreTime($productID)
	{
		$this->db->where('productID',$productID);
		$query = $this->db->get('pos_product_preTime');	
		return $query->row_array();
		
	}

	function cleanProductPreTime()
	{
		$this->db->where('preTime <',date("Y-m-d"))	;
		$this->db->delete('pos_product_preTime');
		
		
	}
	function changePreTime($productID,$preTime,$num=0)
	{
		
			
			$datain = array('preTime'=>$preTime);
			if($num!=0)$datain['num'] = $num;
		$this->db->where('productID',$productID);
		$query = $this->db->get('pos_product_preTime');
		if($query->num_rows()>=1)
		{
			$this->db->where('productID',$productID);
			$this->db->update('pos_product_preTime',$datain);
			
		}
		else 
		{
			
			$datain['productID'] = $productID;
			$this->db->insert('pos_product_preTime',$datain);
			
		}
		
		

		
	}

		
	function suitAllocate($row,$shopData,$orderID,&$total,$magicStatus)
	{
			$shopID = $shopData['shopID'];
			//check suit
				$suit = $this->getSuitProduct($row['productID']);
				//$this->db->insert('pos_test',array('content'=>json_encode($suit)));
				if(!empty($suit['list']))
				{
					
					$suitinf = $this->getSuitProduct($row['productID']);
					$sellPrice = $this->concessionPrice($shopID,$row['productID'],$shopData['discount'],$row['num'],$magicStatus);
						
						$discount = $sellPrice / $suitinf['inf']['orgTotal'];
						
					foreach($suit['list'] as $suitList)
					{
						
						
						$sellPrice = round($discount * $suitList['price']);
						
						//分配貨品
						
					$orderRemainNum = $this->getProductNumExceptOrder($suitList['productID'],0);
					if($orderRemainNum>$row['num']) $sellNum = $row['num'];
					else if ($orderRemainNum>0)$sellNum=$orderRemainNum;
					else $sellNum = 0;
						
						
							$this->db->insert('pos_order_detail',array('orderID'=>$orderID,'productID'=>$suitList['productID'],'buyNum'=> $row['num'],'sellNum' =>$sellNum,'comment'=>'套裝組合',
				'sellPrice'=>$sellPrice));
				$total += $row['num']*$sellPrice;
						
					}
					
				
					return true;
					
				}
				return false;
		
		
		
	}
	
	
	
	
	function getShipment($shopID,$addressID)
	{


		$time = getdate();
		$year = $time['year'];
		
		$month = $time['mon'];	
		$this->db->select('pos_order_detail.*,pos_product.*,pos_order.*,pos_order_address.*,pos_order_address.comID as thisComID,,pos_order_detail.id as PID,pos_order.type as orderType,pos_order_detail.id as rowID,pos_order_detail.comment as productComment,pos_order.orderComment as orderComment,pos_sub_branch.shipmentStatus,pos_sub_branch.cashType,pos_product_preTime.preTime,pos_magic_order.id as magic');
		$this->db->where('pos_order.shopID',$shopID);
		$this->db->where('pos_order.status !=', 0);
		$this->db->where('pos_order_detail.status ', 0);
		$this->db->where('pos_order.addressID', $addressID);
		$this->db->join('pos_order_detail','pos_order_detail.orderID = pos_order.id','left');

		$this->db->join('pos_magic_order','pos_magic_order.orderID = pos_order.id','left');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_order.shopID','left');
		$this->db->join('pos_product','pos_product.productID = pos_order_detail.productID','left');
		$this->db->join('pos_product_preTime','pos_product.productID = pos_product_preTime.productID','left');	
 		$this->db->join('pos_order_address','pos_order_address.id = pos_order.addressID','left');
		$this->db->order_by('pos_order.addressID','ASC');
		$this->db->order_by('pos_order.orderNum','ASC');
		$query = $this->db->get('pos_order');
		$data = $query->result_array();
		$result['month'] = array();
		$result['consignment'] = array();
		
		foreach($data as $row)
		{
		
			if(isset($row['productID']))	
			{
				$ret = $this->PO_model->getProductAmountInf(0,$row['productID'],$year.'-'.$month);
				if(isset($ret['num']))
				{
				$row['num']  = $ret['num'];
				$row['avgCost']  = $ret['avgCost'];
				}
				else
				{
					$row['num']  = 0;
				$row['avgCost']  =0;	
					
					
				}
				
			}
			else
			{
				$row['num']  = 0;
				$row['avgCost']  = 0;
				
				
			}
			if($row['orderType']==0)
			{
				$result['month'][] = $row;
			}	
			else
			{
				$result['consignment'][] = $row;
			}
			
			
		}
		
		return $result;
		
	}
	function getAvailableOrder($shopID)
	{
		if($shopID!=0) 
		{
			$this->db->where('pos_sub_branch.shopID',$shopID);
			$this->db->where('pos_sub_branch.shipmentStatus',0);
		}
		$this->db->select('*');
		$this->db->select_sum('pos_order_detail.sellNum * pos_order_detail.sellPrice','totalPrice');
		$this->db->where('pos_order_detail.status',0);
		$this->db->where('pos_order.status',1);
		$this->db->join('pos_order_detail','pos_order_detail.orderID = pos_order.id','left');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_order.shopID','left');
		$this->db->join('pos_order_distribute','pos_sub_branch.distributeType = pos_order_distribute.id','left');
		$this->db->order_by('shipOut','DESC');
		$this->db->group_by(array("pos_order.shopID", "pos_order.type"));
		$query= $this->db->get('pos_order');
		return $query->result_array();
	}
	
	function getConsignmentMonthCheck($shopID,$year,$month,$isConsignment,$productID = 0,$day = 0)
	{
		
	$distributeType = $this->getDistributeType($shopID);

$this->db->select("pos_product.*,pos_consignment_amount.avgCost,pos_consignment_amount.purchasePrice,IFNULL(d.productDiscount,IFNULL(e.discount,f.discount)) as purchaseCount,IFNULL(a.consignmentNum,0) as consignmentNum,(sum(pos_product_sell.num)) as sellNum,group_concat(DISTINCT pos_product_sell.time  order by pos_product_sell.time SEPARATOR '<br/>') as timeStr",false);
		$this->db->where('pos_product_sell.shopID',$shopID);
		$this->db->where('year(pos_product_sell.time)',$year,false);
		$this->db->where('month(pos_product_sell.time)',$month,false);	
if($day!=0)	$this->db->where('month(pos_product_sell.time)',$month,false);	
	//	$this->db->where('pos_product_sell.num',0);		
        $this->db->where('a.productID !=','');		
		
		$this->db->join('(SELECT productID,sum(num) as consignmentNum FROM pos_consignment_amount '.
						' WHERE pos_consignment_amount.shopID='.$shopID.' AND year(time)='.$year.' AND month(time)='.$month.
						' GROUP BY productID) as a', 'a.productID=pos_product_sell.productID','left');

		
		$this->db->join('pos_consignment_amount','pos_product_sell.productID = pos_consignment_amount.productID AND pos_product_sell.shopID = pos_consignment_amount.shopID AND month(pos_consignment_amount.time) = '.$month.' and year(pos_consignment_amount.time) = '.$year,'left');
		$this->db->join('pos_product','pos_product_sell.productID = pos_product.productID','left');
		
		$this->db->join('pos_order_rule AS e','e.productID = pos_product.productID and e.distributeType= 0 and e.num = 0','left');

		$this->db->join("
				(SELECT productID,pos_order_rule.discount as productDiscount,pos_order_distribute.discount as shopDiscount FROM
				 pos_order_rule 
				LEFT JOIN pos_order_distribute ON pos_order_distribute.id = pos_order_rule.distributeType
				 where pos_order_rule.distributeType =$distributeType and pos_order_rule.num = 0
			
		        ) AS d",'pos_product.productID = d.productID ','left');

		$this->db->join('pos_order_distribute AS f'," f.id = $distributeType",'left');

		
		
		
		if($productID!=0) $this->db->where('pos_product_sell.productID',$productID);
		$this->db->group_by('pos_product_sell.productID');
	//	$this->db->order_by('orderNum','asc');
		$query = $this->db->get('pos_product_sell');
		$data = $query->result_array();
	

		return $this->turnToConsignmentRemainNum($data,$year,$month,$shopID);
		
		
	}	
	function getBackOrderMonthCheck($shopID,$year,$month)
	{
		$distributeType = $this->getDistributeType($shopID);
		$this->db->select('*,pos_order_back_detail.comment as orderComment,sum(pos_order_back_detail.num) as totalNum',false);
		$this->db->where('shopID',$shopID);
		$this->db->where('year(pos_order_back.backToken)',$year,false);
		$this->db->where('month(pos_order_back.backToken)',$month,false);	
		$this->db->where('pos_order_back.status >=',3);//有寄回就算數　20200108 Taitin
		$this->db->join('pos_order_back_detail','pos_order_back.id = pos_order_back_detail.backID','left');
		$this->db->join('pos_product','pos_order_back_detail.productID = pos_product.productID','left');
			
		
		
		$this->db->group_by('pos_order_back_detail.productID,pos_order_back_detail.isConsignment,backTime');
		$this->db->order_by('backTime','asc');
		$query = $this->db->get('pos_order_back');
		return $query->result_array();
			
		
		
	}
	function getConsignmentCost($productID,$shopID,$year,$month)
    {
        
        $this->db->where('time',$year.'-'.$month.'-1');
        $this->db->where('productID',$productID);
         $this->db->where('shopID',$shopID);
        $query = $this->db->get('pos_consignment_amount');
        return $query->row_array();
        
    }
	function orderTurnToShipment($orderID)
	{
		/* 須修正
		
		//直接送達訂單已完成
		$row = $this->getOrderInf($orderID);
		//導入shipment
			$maxNum = $this->getMaxShippingNum();
				 	$datain = array(
						'status' =>4,
						'shopID' =>$row['shopID'],
						'shippingTime' =>date("Y-m-d H:i:s"),
						'shippingNum' =>$maxNum+1,
						'total' =>$row['total'],
						'type'  =>$row['type'],
						'shipmentComment' => $row['orderComment']
					
					);
				 	$this->db->insert('pos_order_shipment',$datain);
					$shipmentID = $this->db->insert_id();
				 //導入shipmentDetail				
				 $this->db->where('orderID',$row['id']);
				 $query = $this->db->get('pos_order_detail');
				 $product = $query->result_array();
				 $profit = 0 ;
				 foreach($product as $col)
				 {
					
					$datain = array(
						'shipmentID'=>$shipmentID,
						'rowID' =>    $col['id'],
						'sellPrice' =>$col['sellPrice'],
						'sellNum' => $col['sellNum'],
						'comment' => $col['comment'],
						'eachCost' => $col['sellPrice']/1.05,
					);
					$profit += ($datain['sellPrice'] -$datain['eachCost']) * $datain['sellNum'];
					$this->db->insert('pos_order_shipment_detail',$datain); 	
		
			//=================
			$this->db->where('orderID',$row['id']);
			$this->db->update('pos_order_detail',array('status'=>$status));
			
			$this->db->where('id',$shipmentID);
			$this->db->update('pos_order_shipment',array('profit'=>$profit));
		return $shipmentID;
		*/
	}
	


	function orderToShipment($orderID,$realCost=false,$stock=false)
	{
			$row = $this->getOrderInf($orderID);
				
			if($row['status'] == 0 ) $status = -1; //未送出訂單
			else 
			{
				 $status = 1;//已經完成裝箱 訂單已完成
					if($stock) $row['status'] = 4;
                    elseif($row['shopID']>1000) $row['status'] = 3;//送達物流
			     //導入shipment
				 	$datain = array(
						'status' =>$row['status'],
						'shopID' =>$row['shopID'],
						'shippingTime' =>$row['shippingTime'],
						'shippingNum' =>$this->getMaxShippingNum()+1,
						'total' =>$row['total'],
						'type'  =>$row['type'],
						'shipmentComment' => $row['orderComment']
					
					);
				 	$this->db->insert('pos_order_shipment',$datain);
					$shipmentID = $this->db->insert_id();
				 //導入shipmentDetail				
				 $this->db->where('orderID',$row['id']);
				 $query = $this->db->get('pos_order_detail');
				 $product = $query->result_array();
				 $profit = 0 ;
				 foreach($product as $col)
				 {
					
					if($realCost)$avgCost = $this->Product_model->getAvgCost($col['productID']);
					else $avgCost  = $col['sellPrice']/1.05;
					$datain = array(
						'shipmentID'=>$shipmentID,
						'rowID' =>    $col['id'],
						'sellPrice' =>$col['sellPrice'],
						'sellNum' => $col['sellNum'],
						'comment' => $col['comment'],
						'eachCost' => $avgCost,
                        'sProductID'=>$col['productID']
					);
					$profit += ($datain['sellPrice'] -$datain['eachCost']) * $datain['sellNum'];
					$this->db->insert('pos_order_shipment_detail',$datain); 
					
					
					if($stock) 	$this->Product_model->updateNum($col['productID'], -$datain['sellNum'],0);

				}
				 
				 
				//
				 
				 
			}
			$this->db->where('orderID',$row['id']);
			$this->db->update('pos_order_detail',array('status'=>$status));
			
			$this->db->where('id',$shipmentID);
			$this->db->update('pos_order_shipment',array('profit'=>$profit));

			return $shipmentID;
		
	}
	
	
	
	
	
		
	function getAdjustOrderMonthCheck($shopID,$year,$month)
	{
	
		$this->db->select('*,pos_sub_branch.name as destinationName,pos_order_adjust_detail.comment as orderComment,(pos_order_adjust_detail.num) as totalNum');
		$this->db->where('fromshopID',$shopID);
		$this->db->where('year(time)',$year,false);
		$this->db->where('month(time)',$month,false);	
		$this->db->join('pos_order_adjust_detail','pos_order_adjust.id = pos_order_adjust_detail.adjustID','left');
		$this->db->join('pos_product','pos_order_adjust_detail.productID = pos_product.productID','left');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_order_adjust.destinationShopID','left');
				
	

	
		$this->db->order_by('time','asc');
		$query = $this->db->get('pos_order_adjust');
		return $query->result_array();
			
		
		
	}	
    
    function autoOrderStatus($shopID)
    {
        
        $this->db->where('shopID',$shopID);
        $query = $this->db->get('pos_sub_branch_order');
        return $query->row_array();
        
    }
    
    function isOwn($productID)
    {
        $this->db->where('productID',$productID) ;
        $query = $this->db->get('pos_own');
        
        if($query->num_rows()>0) return true;
        else return false;
        
    }
    
    
	function concessionPrice($shopID,$productID,$shopDiscount,$sellNum,$magicStatus = 0)
	{
				$rowProduct = $this->Product_model->getProductByProductID($productID,$shopID);
			
                //直營店shopID
                $Ds = array(
                    '1'  => true
            
                );
        
                if(isset($Ds[$shopID]) && $Ds[$shopID])
                {
                 
                    if($this->isOwn($productID)) $sellNum = 2000; //最大折數
                }
        
                			
				$concessions = explode(',',$rowProduct['concessions']);
				$concessionsNum = explode(',',$rowProduct['concessionsNum']);
			
				$num = count($concessionsNum);
				
		
				$purchaseCount =  $rowProduct['purchaseCount'];
				
				for($k=0;$k<$num;$k++)
				{
					
					if($sellNum>=$concessionsNum[$k]&&$concessions[$k]!='')	$purchaseCount = $concessions[$k];
                    
				
				}	
				$magicCount = 80 ;
				if($magicStatus==0) $magicCount = 80 ;// nocore no6
				else if($magicStatus ==1) $magicCount = 75; // core no6
				else if($magicStatus ==3) $magicCount = 77.46; // noncore 6
				else if($magicStatus ==4) $magicCount = 72.95; // core 6	
				if($magicStatus>0 && $this->checkMagicProductPack($productID)) $purchaseCount = $magicCount;
				return round(($purchaseCount)*$rowProduct['price']/100);	
	}
	

	function magicStatus($shopID)
	{
		$this->db->where('shopID',$shopID);
		$query = $this->db->get('pos_magic_status');
		if($query->num_rows()>0)	return $query->row_array();
		else 
		{
			$magic['shopID'] = $shopID;
			$magic['core'] = 0 ;
			return $magic;
		}
	}
	

	

	function checkMagicProductPack($productID)
	{
		//魔法風雲會  補充包  盒裝
		
		$this->db->where('productID',$productID);
        
        // 2017 06 01 delta 說 只要是魔風品項都可以
        $this->db->where('type',8);// 2017 06 01 delta 說 只要是魔風品項都可以
		//$query = $this->db->get('pos_magic_product');
        $query = $this->db->get('pos_product');
        // 2017 06 01 delta 說 只要是魔風品項都可以
        
        
        
		if($query->num_rows()>0) return true;
		return false;	
	}
	
	
	
	function getAddress($shopID) 
	{
		$this->db->where('shopID',$shopID);
        $this->db->where('deleteToken',0);
        $this->db->order_by('defaultToken','DESC');
        $query = $this->db->get('pos_order_address');
		$data = $query->row_array();
		if(isset( $data['id']))	return $data['id'];
		else return 0;
		
	}
	function getOrderInf($orderID,$orderNum =0)
	{
		
		$this->db->select('pos_order.*,pos_sub_branch.name,pos_sub_branch.email,pos_sub_branch.discount,pos_order_address.address,pos_order_address.receiver,pos_order_address.phone,pos_magic_order.id as magic');
		
		if($orderID!=0)$this->db->where('pos_order.id',$orderID);
		if($orderNum!=0)$this->db->where('pos_order.orderNum',$orderNum);		
		$this->db->join('pos_sub_branch','pos_order.shopID = pos_sub_branch.shopID','left');
		$this->db->join('pos_magic_order','pos_order.id = pos_magic_order.orderID','left');
		$this->db->join('pos_order_address','pos_order_address.id = pos_order.addressID','left');
		$query = $this->db->get('pos_order');
		return $query->row_array(); 
	}
	
	function getShipmentInf($shipmentID,$shippingNum=0)
	{
	
        if($shipmentID==0 && $shippingNum==0) return;
      
		$this->db->select('pos_order_shipment.*,pos_sub_branch.name,pos_sub_branch.discount,pos_sub_branch_inf.comID,pos_sub_branch.showPrice,pos_sub_branch_inf.invoiceByShip,pos_order_address.receiver,pos_order_address.email,pos_order_address.address,pos_order_address.phone,pos_order_address.comID as thisComID,pos_order_address.CarrierType,pos_order_address.CarrierId1,pos_order_address.NPOBAN,pos_sub_branch_inf.shipComment,pos_sub_branch_inf.assignTime,pos_sub_branch_inf.shipInterval');
		if($shipmentID!=0)$this->db->where('pos_order_shipment.id',$shipmentID);
        if($shippingNum!=0)$this->db->where('pos_order_shipment.shippingNum',$shippingNum);
		$this->db->join('pos_sub_branch','pos_order_shipment.shopID = pos_sub_branch.shopID','left');
		$this->db->join('pos_sub_branch_inf','pos_sub_branch_inf.shopID = pos_sub_branch.shopID','left');
		$this->db->join('pos_order_address','pos_order_address.id = pos_order_shipment.addressID','left');
		$query = $this->db->get('pos_order_shipment');
		$order = $query->row_array(); 
        if( $order['hidePrice']==1 )$order['showPrice'] = 0;
        $code = md5('IlovePhantasia'.$shipmentID);
        $order['urlCode'] ='https://mart.phantasia.tw/qrcode?d='.urlencode('http://shipment.phantasia.com.tw/order/show_shipment_invoice/'.$shipmentID.'/'.$code);
        
        
        return $order;
	}	
	function updateConsignment($shopID,$productID,$num,$purchasePrice,$year,$mon,$cumulative,$avgCost,$comment='')
	{
       
		//pos_consignment table  2019/5/10
		$this->db->where('shopID',$shopID);
		$this->db->where('productID',$productID);
		$this->db->where('deleteToken','0000-00-00');
		
		$query = $this->db->get('pos_consignment');
		if($query->num_rows()==0)
		{
			$this->db->insert('pos_consignment',array('productID'=>$productID,'lowNum'=>$num,'shopID'=>$shopID,'timeStamp'=>date("Y-m-d")));
			 $firstDate = '0000-00-00';
		}
        else
        {
            $r = $query->row_array();
            $firstDate = $r['timeStamp'];        
            $firstTime = true;;
        }
		
	   $r = $this->consignmentMonData($shopID,$productID,$year,$mon,$firstDate);
	$this->db->insert('pos_test',array('content'=>$year.$mon.json_encode($r)));
		//echo '<br>'.$num.'<br/>';
		if(count($r)==0)
		{
            
			
            //echo 'insert'
			$this->db->insert('pos_consignment_amount',
			array('productID'=>$productID,'num'=>$num,'shopID'=>$shopID,'time'=>date("Y-m-d H:i:s",mktime(0, 0, 0, $mon, 1, $year)),'purchasePrice'=>$purchasePrice,'avgCost'=>$avgCost,'comment'=>$comment));
		}
		else 
		{
			if($cumulative==true)
			{
				 $num = $r['num'] +$num ; 
                 
                if($num>0)
                {
                      $purchasePrice = ($r['purchasePrice']*$r['num']+$purchasePrice*$num)/$num;
                    $avgCost = ($r['avgCost']*$r['num']+$avgCost*$num)/$num;
                     $this->db->where('id',$r['id']);
                    $this->db->update('pos_consignment_amount',
				 array('productID'=>$productID,'num'=>$num,'shopID'=>$shopID,'time'=>date("Y-m-d H:i:s",mktime(0, 0, 0, $mon, 1, $year)),'purchasePrice'=>$purchasePrice,'avgCost'=>$avgCost,'comment'=>$comment));	
                }
                else
                {
                    $purchasePrice = 0;
                    $avgCost = 0;
                    
                }
              
				
			}
			else
			{
				
				 $this->db->where('id',$r['id']);
				 $this->db->update('pos_consignment_amount',
				 array('productID'=>$productID,'num'=>$num,'shopID'=>$shopID,'time'=>date("Y-m-d H:i:s",mktime(0, 0, 0, $mon, 1, $year)),'purchasePrice'=>$purchasePrice,'avgCost'=>$avgCost,'comment'=>$comment));	
				
				}
			
		}
      
	
		return $this->consignmentMonData($shopID,$productID,$year,$mon,$firstDate);	
		
		
	}
	
	function consignmentMonData($shopID,$productID,$year,$mon,$firstDate = '0000-00-00')
    {
        	$sql = "SELECT * FROM pos_consignment_amount WHERE shopID = $shopID and productID = $productID and year(`time`) =$year and  month(`time`) =$mon";
        	$query = $this->db->query($sql);
            $ret = $query->row_array();
	
        if($firstDate!='0000-00-00' &&$query->num_rows()==0)
            {
                        $lastMon = $mon - 1;
                    if($lastMon==0) 
                    {
                           $lastMon =   12;     
                        $lastYear= $year-1;
                    }
                    else $lastYear= $year;
               if(strtotime($firstDate) -  strtotime($lastYear.'-'.$lastMon.'-31')<0)    
               {                                            //數量0只為繼承  
                   $r = $this->updateConsignment($shopID,$productID,0,0,$lastYear,$lastMon,true,0,'autoPatch');
                   	$this->db->insert('pos_consignment_amount',
			array('productID'=>$productID,'num'=>$r['num'],'shopID'=>$shopID,'time'=>date("Y-m-d H:i:s",mktime(0, 0, 0, $mon, 1, $year)),'purchasePrice'=>$r['purchasePrice'],'avgCost'=>$r['avgCost'],'comment'=>'autoPatch')); 
                   
                   
                   
                   
               }
                else return array();
            }
        else return $query->row_array();
        return $this->consignmentMonData($shopID,$productID,$year,$mon,$firstDate);
        
    }
	
	function orderProductDetail($orderID,$productID)
	{
	
		$this->db->where('orderID',$orderID);
		$this->db->where('productID',$productID);
		$query = $this->db->get('pos_order_detail');
		if($query->num_rows()>0)return $query->row_array();
		else return false;
	}
	
	function orderProductDetailByID($orderID,$id)
	{
		
		$this->db->where('orderID',$orderID);
		$this->db->where('id',$id);
		$query = $this->db->get('pos_order_detail');
		if($query->num_rows()>0)return $query->row_array();
		else return false;
	}
	
	function changeColorOrderStatus($status)
		{
				switch($status)
				{
					//send out
					case 0:
						$result= "採購中";
					break;	

					case 1:
						$result= "<span style='color:red'>訂單已送達</span>";
					break;	
					case 4:
						$result = "<span style='color:#FA8000'>訂單處理完成</span>";
					break;					
					case 2:
						$result = "<span style='color:green'>已送達物流</span>";
					break;	
					case 3:
						$result = "已到貨";
					break;									
					
				}		
				return $result;
		}
	function changeOrderStatus($status)
		{
				switch($status)
				{
					//send out
					case 0:
						$result= "採購中";
					break;	

					case 1:
						$result= "訂單已送達";
					break;	
					case 4:
						$result = "訂單處理完成";
					break;					
					case 2:
						$result = "已送達物流";
					break;	
					case 3:
						$result = "已到貨";
					break;									
					
				}		
				return $result;
		}		
		
		
	function getErrConsignment($shopID,$year,$mon,$productID=0,$ok = 1)
	{
		$distributeType = $this->getDistributeType($shopID);
		$sql=
		"SELECT pos_product.*,IFNULL(d.productDiscount,IFNULL(e.discount,f.discount)) as purchaseCount,pos_product.price as sellPrice,pos_consignment_err.* 
		FROM pos_consignment_err
		LEFT JOIN pos_product ON pos_product.productID = pos_consignment_err.productID  
		LEFT JOIN (SELECT productID,sum(pos_product_sell.num) as sellNum FROM pos_product_sell  
				WHERE shopID=$shopID  and year(pos_product_sell.time)=$year and month(pos_product_sell.time) = $mon GROUP BY productID) as a
				 ON a.productID = pos_consignment_err.productID
		LEFT JOIN pos_order_rule as e ON e.productID = pos_product.productID and e.distributeType= 0 and e.num = 0
			LEFT JOIN
			(
			SELECT productID,pos_order_rule.discount as productDiscount,pos_order_distribute.discount as shopDiscount FROM
			 pos_order_rule 
			LEFT JOIN pos_order_distribute ON pos_order_distribute.id = pos_order_rule.distributeType
			 where pos_order_rule.distributeType =$distributeType and pos_order_rule.num = 0
		
			) AS d
			ON pos_product.productID = d.productID 
			LEFT JOIN pos_order_distribute as f ON  f.id = $distributeType 		 
				 
		WHERE pos_consignment_err.shopID =$shopID and year(pos_consignment_err.time)=$year and month(pos_consignment_err.time) = $mon";
		if($productID!=0)$sql .= " and pos_product.productID = $productID";
		if($ok!=1)$sql .= " and pos_consignment_err.ok = 0";
		$sql.= " ORDER BY pos_consignment_err.id ASC";
	$query = $this->db->query($sql);
		return $query->result_array();		
	}	
	
	function getConsignmentRecord($productID,$year,$month,$shopID)
	{
		$sql = "SELECT productID ,num as sellNum ,0 as consignmentNum ,`time` as `time` FROM pos_product_sell WHERE year(time) = $year and month(time)=$month and shopID =$shopID and productID = $productID
				UNION
				SELECT productID ,0 as sellNum ,pos_order_shipment_detail.sellNum as consignmentNum ,`shippingtime` as `time`  FROM pos_order_shipment 
				LEFT JOIN pos_order_shipment_detail
				 ON pos_order_shipment.id= pos_order_shipment_detail.shipmentID 
				LEFT JOIN pos_order_detail
				 ON pos_order_shipment_detail.rowID= pos_order_detail.id
				WHERE pos_order_shipment.status>1 and pos_order_shipment.status <4 and  year(shippingtime) = $year and month(shippingtime)=$month and shopID =$shopID and productID = $productID and type =1
				UNION
				SELECT productID ,0 as sellNum ,- pos_order_back_detail.num as consignmentNum ,`backTime` as `time`  FROM pos_order_back 
				LEFT JOIN pos_order_back_detail
				 ON pos_order_back.id= pos_order_back_detail.backID 
			
				WHERE pos_order_back_detail.isConsignment=1 and  year(backTime) = $year and month(backTime)=$month and shopID =$shopID and productID = $productID		
				order by time";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	function turnToConsignmentRemainNum($data,$year,$month,$shopID)
	{
		$i= 0;
		foreach($data as $row)
		{
			$result	[$i] = $row;
			
			if($row['consignmentNum']>0)
			{
				$consignmentData = $this->getConsignmentRecord($row['productID'],$year,$month,$shopID);
				
				$consignmentSellNum = 0;
				$consignmentNumCheck = $row['consignmentNum'];
				
				foreach($consignmentData as $each)
				{	
					if($each['consignmentNum']!=0)$consignmentNumCheck -=$each['consignmentNum']; //減去這個月補的貨
				}
				$consignmentNum = $consignmentNumCheck;//得到上個月剩下的寄賣貨
				foreach($consignmentData as $each)
				{	
					if($each['consignmentNum']!=0)
					{
						$consignmentNum+=$each['consignmentNum'];
						
					}
					else 
					{
						if(($consignmentNum - $each['sellNum']) > 0 )
						{
								$consignmentSellNum+=$each['sellNum'];
								$consignmentNum-=$each['sellNum'];
								//賣出去
						}
						else 
						{
								
							$consignmentSellNum += $consignmentNum;
							$consignmentNum = 0;
						}
						
					}
					
				}
				
            	 $result[$i]['remainNum'] = $row['consignmentNum']- $consignmentSellNum;			
			
			}
			else $result[$i]['remainNum'] = 0;
			
			$i++;
		}
		if(isset($result))return $result;
	}
		
			
	function getConsignment($shopID,$year,$mon,$productID=0,$query=0)
	{
		$month = $mon;
		$distributeType = $this->getDistributeType($shopID);
		$sql = "SELECT IFNULL(d.productDiscount,IFNULL(e.discount,f.discount)) as purchaseCount,pos_product.*,pos_product.price as sellPrice,pos_consignment_amount.*,IFNULL(pos_consignment_amount.num,0) as consignmentNum,IFNULL(a.sellNum,0) as sellNum
		FROM  pos_consignment_amount 
		LEFT JOIN pos_product ON pos_product.productID = pos_consignment_amount.productID  
			LEFT JOIN (SELECT productID,sum(pos_product_sell.num) as sellNum FROM pos_product_sell  
				WHERE shopID=$shopID  and year(pos_product_sell.time)=$year and month(pos_product_sell.time) = $mon GROUP BY productID) as a
				 ON a.productID = pos_consignment_amount.productID	
		LEFT JOIN pos_order_rule as e ON e.productID = pos_product.productID and e.distributeType= 0 and e.num = 0
				LEFT JOIN
				(
				SELECT productID,pos_order_rule.discount as productDiscount,pos_order_distribute.discount as shopDiscount FROM
				 pos_order_rule 
				LEFT JOIN pos_order_distribute ON pos_order_distribute.id = pos_order_rule.distributeType
				 where pos_order_rule.distributeType =$distributeType and pos_order_rule.num = 0
			
		        ) AS d
				ON pos_product.productID = d.productID 
				LEFT JOIN pos_order_distribute as f ON  f.id = $distributeType 		 
		WHERE pos_consignment_amount.shopID =$shopID and year(pos_consignment_amount.time) = $year and month(pos_consignment_amount.time) = $mon";
       // $sql.=' and a.sellNum is null ';
		if($productID!=0) $sql.= " and pos_product.productID = $productID";
		if($query!='') $sql.= " and (pos_product.ZHName like '%$query%' or pos_product.ENGName like '%$query%' )";
		$sql.=" ORDER BY pos_product.productNum ASC";
	
		$query = $this->db->query($sql);
		$data = $query->result_array();
		$i = 0;
		foreach($data as $row)
		{
			
			$ret = $this->PO_model->getProductAmountInf($shopID,$row['productID'],$year.'-'.$month);
			if(isset($ret['num']))$data[$i]['nowNum'] = $ret['num'];
			else $data[$i]['nowNum'] = 0;
			if(isset($ret['totalCost']))$data[$i]['totalCost'] = $ret['totalCost'];
			else $data[$i]['totalCost'] = 0;
			$i++;
			
		}	
		
		return  $this->turnToConsignmentRemainNum($data,$year,$mon,$shopID);
		
	}
	
	function getOtherShop($shopID = 0)
	{
        
        
		$this->db->select('pos_sub_branch.*,pos_order_distribute.distributeName,pos_sub_branch_inf.*,pos_sub_branch.shopID as shopID');	
        
        if($shopID==-2) $this->db->where('joinType',1);
        else if($shopID==-1) $this->db->where('joinType',0);
        else if($shopID!=0) $this->db->where('pos_sub_branch.shopID',$shopID);
		$this->db->join('pos_order_distribute','pos_order_distribute.id= pos_sub_branch.distributeType','left');
		$this->db->join('pos_sub_branch_inf','pos_sub_branch_inf.shopID= pos_sub_branch.shopID','left');
		$this->db->order_by('cashType','DESC');
		$this->db->order_by('pos_sub_branch.shopID','ASC');
		$query = $this->db->get('pos_sub_branch');
		return $query->result_array();
	}
	function getOrderDistribute()
	{
		
		$query = $this->db->get('pos_order_distribute');
		return $query->result_array();	
		
	}
	function getShopDistribute($distributeType)
	{
		$this->db->where('distributeType',$distributeType);
		$query = $this->db->get('pos_sub_branch');
		return $query->result_array();	
		
		
	}
	
	
	function getMaxShopID()
	{
		$this->db->order_by('shopID','desc');
		$this->db->limit(1);
		$query = $this->db->get('pos_sub_branch');
		$data = $query->row_array();
		return $data['shopID'];
	}


//=====back order
	function getOrderBackList($shopID,$offset,$num)
	{
		
		if($shopID!=0)$this->db->where('pos_order_back.shopID',$shopID);
		$this->db->select('pos_order_back.*,pos_sub_branch.name as shopName');
		$this->db->join('pos_sub_branch','pos_order_back.shopID = pos_sub_branch.shopID','left');
		$this->db->where('pos_order_back.shopID !=',994);//盒損區
		$this->db->limit($num,$offset);
		$this->db->order_by('id','DESC');
		$query = $this->db->get('pos_order_back');
		
		return $query->result_array();
		
	}
	
	function getOrderAdjustList($shopID,$offset,$num)
	{

		if($shopID!=0)$this->db->where('pos_order_adjust.fromShopID',$shopID);
		$this->db->select('pos_order_adjust.*,a.name as fromShopName,b.name as destinationShopName');
		$this->db->join('pos_sub_branch a','pos_order_adjust.fromShopID = a.shopID','left');
		$this->db->join('pos_sub_branch b','pos_order_adjust.destinationShopID = b.shopID','left');
		$this->db->limit($num,$offset);
		$this->db->order_by('id','DESC');
		$query = $this->db->get('pos_order_adjust');
		
		return $query->result_array();	
	}
	
	
	function changeOrderBackStatus($status)
		{
				switch($status)
				{
					//send out
					case 0:
						$result= "退單審核中";
					break;	

					case 1:
						$result= "同意寄回";
					break;	
				
					case 2:
						$result = "不同意寄回";
					break;	
					case 3:
						$result = "寄回中";
					break;	
					case 4:
						$result = "退貨完成";
					break;														
					
				}		
				return $result;
		}

	
	function getOrderBackInf($id)
	{
		$this->db->select('pos_order_back.*,pos_sub_branch.name,parentID');
		$this->db->where('pos_order_back.id',$id);
		$this->db->join('pos_sub_branch','pos_order_back.shopID = pos_sub_branch.shopID','left');
		$this->db->join('pos_problem_track','pos_order_back.id = pos_problem_track.productID and pos_problem_track.type = 9','left');
	
		$query = $this->db->get('pos_order_back');
		return $query->row_array();
	}
	function getOrderAdjustInf($id)
	{
		$this->db->select('pos_order_adjust.*,a.name as fromShopName,b.name as destinationShopName');
		$this->db->where('pos_order_adjust.id',$id);
		$this->db->join('pos_sub_branch a','pos_order_adjust.fromShopID = a.shopID','left');
		$this->db->join('pos_sub_branch b','pos_order_adjust.destinationShopID = b.shopID','left');
		$query = $this->db->get('pos_order_adjust');
		return $query->row_array();	
		
	}
	
	function getOrderBackDetailByID($backID)
	{

		$this->db->select('*,pos_order_back_detail.id as rowID,pos_order_back_detail.num as OSBANum,CONCAT(pos_order_back_reason.reason,pos_order_back_detail.comment) as orderComment',false);
		$this->db->join('pos_product','pos_order_back_detail.productID = pos_product.productID','left');
        $this->db->join('pos_order_back_reason','pos_order_back_reason.id = pos_order_back_detail.reason','left');
		$this->db->where('backID', $backID);
		$this->db->order_by("productNum", 'ASC');
		$query = $this->db->get('pos_order_back_detail');
        $data = $query->result_array();
        //$this->db->insert('pos_test',array('content'=>json_encode($data)));
		return $query->result_array();

		
	}
	
	function getOrderAdjustDetailByID($id)
	{
		$this->db->select('*,pos_order_adjust_detail.num as OSBANum,pos_order_adjust_detail.comment as orderComment,');
		$this->db->from('pos_order_adjust_detail');
		$this->db->join('pos_product','pos_order_adjust_detail.productID = pos_product.productID','left');
		$this->db->where("pos_order_adjust_detail.adjustID", $id);
		$this->db->order_by("productNum", 'ASC');
		$query = $this->db->get();	
		return $query->result_array();		
	}
	
	

	function getDistributeType($shopID)
	{
	
		$this->db->where('shopID',$shopID);
		$query = $this->db->get('pos_sub_branch');
		$data = $query->row_array();
		$distributeType = $data['distributeType'];	
		return $distributeType;
		
	}	
	
	function getProductqueryString($find,$shopID= 999)
	{
		
		$time = getdate();
		$year = $time['year'];
	//IF(b.num>0,1,0) as stock
		$month = $time['mon'];	
		$sql = "SELECT pos_product.* ,IFNULL(pos_top_product.top10,0) as top,IF(category='0',0,1) as productType FROM pos_product 
				LEFT JOIN pos_top_product ON pos_top_product.productID = pos_product.productID
				LEFT JOIN pos_own ON pos_product.productID = pos_own.productID 
				";
			if(isset($find['orderCondition'])&&$find['orderCondition'] == 4)
				$sql.="	LEFT JOIN pos_current_product_amount as g ON  g.productID =  pos_product.productID and g.shopID = 0 ";
		
			$sql.=" LEFT JOIN pos_cheap_product ON pos_cheap_product.productID = pos_product.productID
				 where pos_product.productID!=0  ";
		if(isset($find['queryString'])&&$find['queryString']!='0')
		{
			$sql.= ' and (ZHName like "%'.$find['queryString'].'%" or ENGName like "%'.$find['queryString'].'%")';
			
		}	

		if(isset($find['openStatus'])&&$find['openStatus']!='all')
		{
			$sql.= ' and openStatus = '.$find['openStatus'];
			
		}
		if(isset($find['top150'])&&$find['top150']!='all')
		{
			if($find['top150']==1)$sql.= ' and pos_top_product.productID is not null';
			else if($find['top150']==10)$sql.= ' and pos_top_product.top10 =1';
			else if($find['top150']==30)$sql.= ' and pos_own.best30 =1';
			else if($find['top150']==-1)$sql.= ' and pos_cheap_product.productID is not null';
			else $sql.= ' and pos_top_product.productID is null';
			
			
		}
		if(isset($find['category'])&&$find['category']!=''&&$find['category']!=-1)
		{
			 $sql.= " and pos_product.category like'".$find['category']."'";
			
			
		}
		if(isset($find['suppliers'])&&$find['suppliers']!='0')
		{
			$sql.= ' and suppliers = '.$find['suppliers'];
			
		} 
		if(isset($find['type'])&&$find['type']!='0')
		{
			$sql.= ' and pos_product.type = '.$find['type'];
            
			
		} 
        else
        {
            
            $sql.= ' and pos_product.type != 8'; //魔風排除
            
        }
		if($find['distributeType']==1||$find['distributeType']==15 ||$find['distributeType']==20||$this->data['shopID']<=1000);
		else 
		{
            $sql.= ' and suppliers!= 18 ';//新天鵝堡 貨品非加盟合作店家無法購買
			$sql.= ' and suppliers!= 2 ';//kanga 貨品非加盟合作店家無法購買
            
			$sql.= ' and suppliers!= 87 ';//勃根地 貨品非加盟合作店家無法購買
           
            
			
			$sql.= ' and pos_product.productID != 8885826'; //砂之國度
			
			
		}
        /* 20181214 taitin
        if($find['distributeType']==2)
        {
            
             $sql.= ' and suppliers!= 57 ';//gokids 貨品桌遊同業無法購買
            
            
        }
        */
        
        if(isset($find['recommand'])&&$find['recommand'] == 1  )$sql.=" and pos_product.id >6400 ";
		if($shopID!=1 && $shopID!=3 && $shopID!=4 && $shopID!=18 && $shopID!=36) $sql.= ' and pos_product.productID != 8881348';//會員卡250pic品項只有前四家加盟店才看的到
			
	


		return $sql;
		
	}
	function getSuit($find,$shopID,$type)
	{
		
		$distributeType = $this->getDistributeType($shopID);
		$this->db->like('name',$find['queryString']);
		$query = $this->db->get('pos_suit');
		$s = $query->result_array();
		
		foreach($s as $row)
		{
			
			$row['productList'] = $this->getSuitProduct($row['suitID']);
			$row['discount'] = $this->getSuitDiscount($row['suitID'],$distributeType);
			$this->getShopDiscount($shopID);
		}
		
	}
	
	function getSuitProduct($mainProduct)
	{
		
		$this->db->where('productID',$mainProduct);
		$query = $this->db->get('pos_product');
		$suit['inf'] = $query->row_array();
		
		$this->db->where('mainProductID',$mainProduct);
		$this->db->join('pos_product','pos_product.productID = pos_suit_list.productID','left');
		$query = $this->db->get('pos_suit_list');
		$suit['list'] =  $query->result_array();
		$suit['inf']['orgTotal'] = 0 ;
		if(!empty($suit['list']))
		{
			
			foreach($suit['list'] as $row)
			{
				
				$suit['inf']['orgTotal'] += $row['price'] * $row['snum'] ;
				
			}
			
		}
		return $suit;
		
	}
	
	function getSuitDiscount($suitID,$distributeType)
	{
		$this->db->where('suitID',$suitID);
		$this->db->where('distributeType',$distributeType);
		$query = $this->db->get('pos_suit_order');
		return $query->result_array();
		
	}
	
	
	function getProduct($find,$shopID,$type)
	{
		$time = getdate();
        $rc = 0;
		$distributeType = $this->getDistributeType($shopID);
		
		$find['distributeType'] = $distributeType;
		$year = $time['year'];
		
		$month = $time['mon'];	
		if(isset($find['productID'])&&$find['productID'])
		{
			$data[0]['productID'] = $find['productID'];
			
		}
		else
		{
			
			
			
			$sql = $this->getProductqueryString($find,$shopID);
		
         
            
			$sql.=" order by ";
            if(isset($find['recommand'])&&$find['recommand'] == 1  )$sql.=" pos_product.id DESC,";
			 elseif(isset($find['orderCondition'])&&$find['orderCondition'] == 0)$sql.=" recent DESC,";
			elseif(isset($find['orderCondition'])&&$find['orderCondition'] == 1)$sql.=" flowNum DESC,";
			elseif(isset($find['orderCondition'])&&$find['orderCondition'] == 2)$sql.=" pos_product.price ASC,";
			elseif(isset($find['orderCondition'])&&$find['orderCondition'] == 3)$sql.=" pos_product.price DESC,";
			elseif(isset($find['orderCondition'])&&$find['orderCondition'] == 4)$sql.=" g.num DESC,";
     		$sql.=" pos_product.ZHName DESC,";
            $sql.=" top DESC ";
			if($type=='get'&&isset($find['start'])&&isset($find['num']))$sql.= " limit ".$find['start'].','.$find['num'];
	//if($shopID==100)   echo $sql;
			$query = $this->db->query($sql);
			$data =  $query->result_array();		
		
		}
		$result  = array();
		$shopNum = 5;
		foreach($data as $row)
		{
			;
		$sql = "SELECT 
				d.productDiscount as prodctDiscount,
				e.discount as defaultDiscount,
				f.discount as distributeDiscount,
		pos_product.*,pos_suppliers.name as suppliersName,IFNULL(orderingNum,0) as orderingNum,concessions,concessionsNum,pos_own.best30
		        FROM pos_product 
				LEFT JOIN pos_own ON pos_product.productID = pos_own.productID
				LEFT JOIN 
				(SELECT productID,group_concat(pos_order_rule.discount ORDER BY discount DESC SEPARATOR ',') as concessions,
						group_concat(pos_order_rule.num ORDER BY discount DESC SEPARATOR ',') as concessionsNum
				FROM pos_order_rule  WHERE distributeType = $distributeType GROUP BY productID ) as discount
				ON discount.productID = pos_product.productID	
				LEFT JOIN pos_suppliers	ON pos_product.suppliers = pos_suppliers.supplierID
				LEFT JOIN
				(
				SELECT productID,sum(num) as orderingNum FROM
				
				(
					SELECT pos_order_detail.id,productID,(pos_order_detail.buyNum) AS num FROM
					pos_order  
					LEFT JOIN pos_order_detail ON pos_order.id = pos_order_detail.orderID
					 where pos_order_detail.status =0 and pos_order.status != 3 and pos_order.status != 0 and pos_order.shopID =$shopID 
				 	UNION 
					SELECT  pos_order_detail.id,productID,pos_order_shipment_detail.sellNum as num FROM
					pos_order_shipment
					LEFT JOIN pos_order_shipment_detail ON pos_order_shipment.id = pos_order_shipment_detail.shipmentID
					LEFT JOIN pos_order_detail ON pos_order_detail.id = pos_order_shipment_detail.rowID
					where pos_order_shipment.status != 3 and pos_order_shipment.status != 0 and pos_order_shipment.shopID =$shopID 
				 	
				 
				 ) as t
				 GROUP BY productID 
		        ) AS c
				ON pos_product.productID = c.productID 
				LEFT JOIN pos_order_rule as e ON e.productID = pos_product.productID and e.distributeType= 0 and e.num = 0
				LEFT JOIN
				(
				SELECT productID,pos_order_rule.discount as productDiscount,pos_order_distribute.discount as shopDiscount FROM
				 pos_order_rule 
				LEFT JOIN pos_order_distribute ON pos_order_distribute.id = pos_order_rule.distributeType
				 where pos_order_rule.distributeType =$distributeType and pos_order_rule.num = 0
			
		        ) AS d
				ON pos_product.productID = d.productID ";
				
		
				
				$sql.=" LEFT JOIN pos_order_distribute as f ON  f.id = $distributeType 
				 where pos_product.productID!=0  and pos_product.productID = ".$row['productID'];
				 
		
				$query = $this->db->query($sql);	
				$ret = $query->row_array();
			
				$ret['nowNum'] =  $this->PO_model->getProductNum($shopID,$row['productID'],$year.'-'.$month);
				if(isset($ret))
				{
					if(empty($ret['prodctDiscount']))
					{
						//預設商商品設定值空時
						if(empty($ret['defaultDiscount'])) $ret['purchaseCount'] = $ret['distributeDiscount'];
						else 
						{
							//比較商品預設跟經銷商預設大小，
							$ret['purchaseCount'] = max( $ret['defaultDiscount'], $ret['distributeDiscount'])	;	
							
						}
						
					}
					else $ret['purchaseCount'] = $ret['prodctDiscount'];//經銷商商品設定值最優先。
					
				
				}
            	if(isset($find['recommand'])&&$find['recommand'] == 1 ) 
					{
             
						if($rc<21)
                        {
                            $result[] = $ret;	//新品推薦
                            $rc++;
                        }
					}
					else $result[] = $ret;
		}
 
		return $result;
		
	}


function getOrderingNum($shopID,$productID = 0)
{
	$sql = "	SELECT productID,sum(num) as orderingNum,sum(sellTotal) as sellTotal FROM
				
				(
					SELECT pos_order_detail.id,productID,(pos_order_detail.buyNum) AS num,pos_order_detail.buyNum*sellPrice as sellTotal FROM
					pos_order  
					LEFT JOIN pos_order_detail ON pos_order.id = pos_order_detail.orderID
					 where pos_order_detail.status =0 and pos_order.status != 3 and pos_order.status != 0 and pos_order.shopID =$shopID 
				 	UNION 
					SELECT  pos_order_detail.id,productID,pos_order_shipment_detail.sellNum as num ,pos_order_shipment_detail.sellNum*pos_order_shipment_detail.sellPrice as sellTotal FROM
					pos_order_shipment
					LEFT JOIN pos_order_shipment_detail ON pos_order_shipment.id = pos_order_shipment_detail.shipmentID
					LEFT JOIN pos_order_detail ON pos_order_detail.id = pos_order_shipment_detail.rowID
					where pos_order_shipment.status != 3 and pos_order_shipment.status != 0 and pos_order_shipment.shopID =$shopID 
				 	
				 
				 ) as t
				 "	;
	if($productID!=0) 
	{
		$sql.=" where productID = ".$productID." GROUP BY productID ";
		$query = $this->db->query($sql);
		$data =  $query->row_array();
		if(empty($data)) return array('productID'=>$productID,'orderingNum'=>0,'sellTotal'=>0);
		else return $data;
	}
		$sql.=" GROUP BY productID ";
		$query = $this->db->query($sql);
		foreach ($query->result_array() as $row)
			{
				$result['p_'.$row['productID']] = $row;



			}
		return $result;

}

	
function productSell($shopID,$productID,$time)	
{
	
	$sql="SELECT  pos_order_detail.id,productID,sum(pos_order_shipment_detail.sellNum) as orderingNum,sum(pos_order_shipment_detail.sellNum*pos_order_shipment_detail.sellPrice) as sellTotal FROM
					pos_order_shipment
					LEFT JOIN pos_order_shipment_detail ON pos_order_shipment.id = pos_order_shipment_detail.shipmentID
					LEFT JOIN pos_order_detail ON pos_order_detail.id = pos_order_shipment_detail.rowID
					where pos_order_shipment.status=3 and pos_order_shipment.shopID =$shopID and pos_order_shipment.shippingTime >= '$time' ";
	$sql.=" and productID = ".$productID." GROUP BY productID ";
	$query = $this->db->query($sql);
		$data =  $query->row_array();
		if(empty($data)) return array('productID'=>$productID,'orderingNum'=>0,'sellTotal'=>0);
		else return $data;
	
	
}


function countProduct($find,$shopID)
	{
			$distributeType = $this->getDistributeType($shopID);
		
		$find['distributeType'] = $distributeType;

		$query = $this->db->query($this->getProductqueryString($find,$shopID));	
		
		return $query->num_rows();	
	}
	
	function getShopDiscount($shopID)
	{
		$this->db->where('shopID',$shopID);
		$this->db->join('pos_order_distribute','pos_order_distribute.id = pos_sub_branch.distributeType','left');
		$query = $this->db->get('pos_sub_branch');
		return $query->row_array();	
			
		
	}
    
    function ptcgTest($data)
    {
       foreach($data as $row)
       {
           
           if(strpos($row['productNum'],'PTCG')!==false) return true;
           
       }
        return false;
        
        
        
    }
	
	function magicDiscountTest($data,$shopID,$text = 'num')
	{
	
		$magic = $this->magicStatus($shopID);
	
		/*	改為一律彈性~		
		if(!isset($magic['six'])) return 0 ;
		
		if($magic['six']==0)
		{
			if($magic['core']==1) return 1; //core no6
			else	return 0;// nocore no6
		}*/

		$magicNum = 0 ; 
		//1. 確認魔風 數量超過6
	
		foreach($data as $row)
		{
					
			//$total+= $row['sellPrice'] *$row['sellNum'];
			if($this->checkMagicProductPack($row['productID']))
			{
				$magicNum +=$row[$text];
			
			}
						
		}
	
		if($magicNum < 6)
		{
		
			if($magicNum > 0)
			{
				//return -1; //有6盒承諾，但未滿6盒，須取消訂單。20160225已取消固定
			
				if($magic['core']==1) return 1; //core no6
				else	return 2;// nocore no6
			
				
			}
			else return 0 ;//一般訂單
		
		}
		
		//2.確認是不是core
		if($magic['core']==1)  return 4;  // core6
		else 	return 3 ; // nocore6 
				
	}
	

	
	
	function upDiscountTest($data,$shopID,$type,$newTotal = 0)
	{
		
		$shopDiscount = $this->getShopDiscount($shopID);
		$upDiscount = explode('-',$shopDiscount['upDiscount']);
		$total = 0;
	
	
		//print_r($data);
		if(!empty($upDiscount))
		{
		
			foreach($upDiscount as $each)
			{
				$upDiscountList = explode(',',$each);
		
				if(isset($upDiscountList[2]))
				if(($upDiscountList[0]==1&&$type=='month') ||($upDiscountList[0]==0&&$type=='each'))
				{
					foreach($data as $row)
					{
					
						$total+= $row['sellPrice'] *$row['sellNum'];
						
					}
					$newTotal  = $total;
					if($total>$upDiscountList[1])	
					{
					
						$newTotal = 0 ;
						foreach($data as $row)
						{	
							$distributeType = $this->getDistributeType($shopID);
							
							
							$sellPrice = $this->specialPrice($row,$distributeType, round($row['price'] * $upDiscountList[2]/100));
							//需確認特殊商品定價
							$sellPriceList[] =min($sellPrice,$row['sellPrice']);
							$newTotal+=min($sellPrice,$row['sellPrice'])*$row['sellNum'];
							
						}
			
						
						if($newTotal>$upDiscountList[1])
						{
							$i = 0;
							//echo count($data);
							foreach($data as $row)
							{
							
								$data[$i]['sellPrice'] = $sellPriceList[$i] ;
								$i++;
							}
							
							
						}
					
					}
				}
			}
		}
		
		return $data;
		
	}
	
	
	function specialPrice($data,$distributeType,$upDiscountPrice)
	{
		//折數判斷，可以打的最大值
		//最大折數 進入經銷商特別折扣，確認是否有特殊品折扣跟經銷商折扣牴觸，若有则取高
		
		
		$defaultPrice =  0 ;
		$this->db->where('distributeType',0);
		$this->db->where('productID',$data['productID']);
		
		$query = $this->db->get('pos_order_rule');
		if($query->num_rows()>0)
		{
			//找max
			$discount = $query->result_array();
			$num = count($discount);
			foreach($discount as $row)
			{
				if($data['buyNum']>=$row['num'] ) $defaultPrice = round($data['price'] *$row['discount']/100) ;
				
			}
           
		}
		
		$this->db->where('distributeType',$distributeType);
		$this->db->where('productID',$data['productID']);
		$query = $this->db->get('pos_order_rule');
		if($query->num_rows()>0)
		{
			//找max
			$discount = $query->result_array();
			$num = count($discount);
			foreach($discount as $row)
			{
				if($data['buyNum']>=$row['num'] ) $defaultPrice = round($data['price'] *$row['discount']/100) ;
				
			}
			
		}	
        
      

		if($defaultPrice!=0) return $defaultPrice;
		else return	$upDiscountPrice;
	}
	function getDistributeProduct($distributeType)
	{
		
		$sql ="SELECT pos_product.*,group_concat(pos_order_rule.discount ORDER BY discount DESC SEPARATOR ',') as concessionsDiscount,
						group_concat(pos_order_rule.num ORDER BY discount DESC SEPARATOR ',') as concessionsNum
				FROM pos_order_rule 
				LEFT JOIN pos_product ON pos_product.productID = pos_order_rule.productID
				 WHERE distributeType = $distributeType GROUP BY productID";
		$query = $this->db->query($sql);	
				
		 $data = $query->result_array();		
		 
		 $j = 0;
		 foreach($data as $row)
		{
			$concessionsList = explode(',',$row['concessionsDiscount'])	;
			$concessionsNumList = explode(',',$row['concessionsNum'])	;
		
			$i = 0;
			$num = count($concessionsList);
			for($i=0;$i<$num;$i++)
			{
				
				$data[$j]['concessions'][$i] = array('num'=>$concessionsNumList[$i],'discount'=>$concessionsList[$i]);
				
			}
			$j++;
		}
		 
		 return $data;
	}

	function getOtherProduct($year,$month,$shopID)
	{
		
		$distributeType = $this->getDistributeType($shopID);
		$sql ="SELECT pos_product_purchase.*,pos_product_purchase.num as purchaseNum,pos_product.*,IFNULL(d.productDiscount,IFNULL(e.discount,f.discount)) as purchaseCount
				 FROM pos_product_purchase 
				LEFT JOIN pos_product ON pos_product_purchase.productID = pos_product.productID
		LEFT JOIN pos_order_rule as e ON e.productID = pos_product.productID and e.distributeType= 0 and e.num = 0
			LEFT JOIN
			(
			SELECT productID,pos_order_rule.discount as productDiscount,pos_order_distribute.discount as shopDiscount FROM
			 pos_order_rule 
			LEFT JOIN pos_order_distribute ON pos_order_distribute.id = pos_order_rule.distributeType
			 where pos_order_rule.distributeType =$distributeType and pos_order_rule.num = 0
		
			) AS d
			ON pos_product.productID = d.productID 
			LEFT JOIN pos_order_distribute as f ON  f.id = $distributeType 		
				
				WHERE pos_product.type = 4 and
				 shopID= $shopID and year(time)=$year and month(time) =$month";
		$query = $this->db->query($sql);
		$data =  $query->result_array();	
		return $data;
		
	}

	function getOtherMoneyMonthCheck($shopID,$year,$month)
	{
		$this->db->where('shopID',$shopID);
		$this->db->where('year',$year);
		$this->db->where('month',$month);
		$query = $this->db->get('pos_other_money')	;
		return  $query->result_array();	
	}
	
	
	/*20190901
	function getLowerSafeStock()
	{
		
		$time = getdate();
		$year = $time['year'];
		$month = $time['mon'];
		$day =  $time['mday'];
		$stockMonth = $month-3;
		if($stockMonth<=0)
		{
			$stockYear = $year-1;
			$stockMonth+=12;	
			
		}
		else 	$stockYear = $year;
		
		$sql = "SELECT pos_product_amount.num as nowNum,safeNum,pos_product.ZHName,ENGName,language,pos_suppliers.name as supplier,price,pos_product.buyPrice,pos_suppliers.day
		FROM
		(
			SELECT productID,sum(pos_order_shipment_detail.sellNum)/3 as safeNum
			FROM pos_order_shipment_detail  
			LEFT JOIN pos_order_shipment 
			ON pos_order_shipment.id=  pos_order_shipment_detail.shipmentID 
			
			LEFT JOIN pos_order_detail
			ON pos_order_detail.id = pos_order_shipment_detail.rowID
			
			WHERE `shippingTime`>'$stockYear-$stockMonth-$day 00:00:00' and productID is Not Null and pos_order_detail.status = 1  and pos_order_shipment.status>=2 
			GROUP BY productID
		) as safe

		LEFT JOIN pos_product_amount ON 
		pos_product_amount.productID = safe.productID
		
		LEFT JOIN pos_product ON
		pos_product.productID = safe.productID
		
		LEFT JOIN pos_suppliers ON
		pos_product.suppliers= pos_suppliers .supplierID

		where safeNum>pos_product_amount.num*1.2 and year=$year and month = $month and shopID=0
		order by supplierID ";
	

		$query = $this->db->query($sql);
		return $query->result_array();
		
	}
	
	*/
    
    
    
	function getBackPrice($shopID,$productID)
	{
		$this->db->select('pos_order_shipment_detail.*');
		$this->db->where('productID',$productID);
		$this->db->where('shopID',$shopID);
		$this->db->join('pos_order_shipment_detail','pos_order_shipment.id = pos_order_shipment_detail.shipmentID','left');
		$this->db->join('pos_order_detail','pos_order_detail.id = pos_order_shipment_detail.rowID','left');
		$this->db->order_by('pos_order_shipment_detail.shipmentID','DESC');
		$query = $this->db->get('pos_order_shipment');
		
		$ret = $query->result_array();
		
			if(!empty($ret))
			{
				foreach($ret as $row) if($row['sellPrice']!=0) return $row['sellPrice'];
		
			
				 
			}
			 return 0 ;
			
	}
	
	function getAllConsignment($shopID)
    {
        
        $this->db->where('shopID',$shopID);
        $this->db->where('deleteToken','0000-00-00');
        	
		$query = $this->db->get('pos_consignment');
        
        return $query->result_array();        
    }
	
	function getAllStock($productID)
	{
			//shop
			$this->db->where('shopID <',1000);
			$this->db->where('shopID !=',2);
			$this->db->where('shopID !=',100);
			$this->db->where('shopID !=',703);
			$query = $this->db->get('pos_sub_branch');
			$shopList = $query->result_array();
		
				//抓庫存量 
					//公司
					$ret = $this->Product_model->getProductStock(array('productID'=>$productID),0);
					$stock = $ret['product'][0]['nowNum'];
					//板橋店
					$ret = $this->Product_model->getProductStock(array('productID'=>$productID),1);
					$stock+= $ret['product'][0]['nowNum'];
					
				$remain = 0;
				//以及各店寄賣中數量
				foreach($shopList as $shop)
				{
					$ret = $this->getConsignment($shop['shopID'],$year,$month,$row['productID']);
					$remain+= $ret[0]['remainNum'];
					//觀看每間店寄賣量可以從這裡加入
					//$cc[] = array('shopID'=>$shop['shopID'],'num'=>$ret[0]['remainNum']);
					
				}	
			$result['stock'] = $stock;
			$result['remain'] = $remain;
			return $result;
		
	}
	
	
	function getProductConsignmentNum($supplierID,$year,$month,$monthShift = 0)
	{
		
		//前月的月底
		$year = $year;
		$month = $month+$monthShift;
		while($month <= 0)
		{
			echo $month.'w<br/>';
			$year = $year-1;
			$month = 12-($month+$monthShift);
		}
		
		$this->db->select('*,pos_product_consignment.num as stockNum')	;
		$this->db->where('year',$year);
		$this->db->where('month',$month);
		$this->db->where('suppliers',$supplierID);
		$this->db->join('pos_product','pos_product.productID = pos_product_consignment.productID','left');
		$this->db->order_by('pos_product.productID');
		 $query = $this->db->get('pos_product_consignment');
		if($query->num_rows()>=1)return $query->result_array();
		else
		{
			if($monthShift<0) return; //前月也無資料
		
			$product = $this->getProductConsignmentNum($supplierID,$year,$month,-1);
			
			
				
			foreach($product as $row)
			{	
			
				$ret = $this->getAllStock($productID);
				
				$result[] = array('productID'=>$row['productID'],'stockNum'=>$ret['stock']+$ret['remain']);
				
			}
			
			
			return $result;
			
		}
	}
	
	
	
	function getProductConsignmentByFactory($supplierID,$year,$month)
	{
	
		
		$lastTime  = $this->getProductConsignmentNum($supplierID,$year,$month,-1);//期初庫存
  	    $thisTime  = $this->getProductConsignmentNum($supplierID,$year,$month);//期末庫存
   	     
	
		 
		 $newIn[] = array('productID'=>0,'num'=>0)   ;
		
		$i = 0;$j = 0; $k = 0;
		$lastNum =  count($lastTime);
		$thisNum =  count($thisTime);
		$newNum =  count($newIn);
		for($i=0;$i<$lastNum;$i++)
		{
			
			while($j<$thisNum && isset($thisTime[$j]) && $lastTime[$i]['productID']!=$thisTime[$j]['productID']) $j++;
			while($k<$newNum && isset($newIn[$k]) && $lastTime[$i]['productID']!=$newIn[$k]['productID']) $k++;
			
			
			$product[$i] = $lastTime[$i];
			$product[$i]['lastConsignmentNum'] = $lastTime[$i]['stockNum'];
			if($j<$thisNum) $product[$i]['thisConsignmentNum'] = $thisTime[$j]['stockNum'];
			 else $product[$i]['thisConsignmentNum'] = 0;
			if($k<$newNum)	$product[$i]['newNum'] = $newIn[$k]['num'];
			else $product[$i]['newNum'] = 0;
			
		
			
			
			
			
		}
		return $product;
		
		
		
		
	}
	
	
	
	function getConsignmentFactory($year,$month)
	{
		$monthShift = -1;
		
			//前月的月底
		$year = $year;
		$month = $month+$monthShift;
		while($month <= 0)
		{
			
			$year = $year-1;
			$month = 12-($month+$monthShift);
		}
		
		
		$this->db->select('suppliers as supplierID');
		$this->db->where('year',$year);
		$this->db->where('month',$month);
		$this->db->join('pos_product','pos_product.productID = pos_product_consignment.productID','left');
		$this->db->group_by('suppliers');
		$query = $this->db->get('pos_product_consignment');
		return $query->result_array();
		
	}
	function phoneForm($phone)
	{
		if($phone==0) return 0;
		$phone = str_replace('-','',trim($phone));	
		if($phone!='')
		if($phone[0]==0)
		{
			switch($phone[1])
			{
 				case '9': //手機
					$token = 4;
				break;	
				case '3':
					if($phone[2] =='7')$token = 3;//苗栗
					else $token = 2;//新竹 桃園 花蓮 宜蘭
				break;	
				case '4':
					if($phone[2] =='9')$token = 3;//南投
					else $token = 2;//台中
				break;	
				case '8':
					if($phone[2] =='9')$token = 3;//台東
					else if($phone[2]=='3')$token = 4;//馬祖 0836
					else if($phone[2]=='2')$token = 3;//馬祖 082
					else $token = 2;//屏東
				break;					
				default :
				   $token = 2;
				break;
				
			}
			$phone = substr($phone,0,$token).'-'.substr($phone,$token);

		}
		
		return $phone;
		
		
	}
    function getLastSellPrice($productID,$shopID)
    {
        
        $this->db->where('productID',$productID);
        $this->db->where('shopID',$shopID);
        $this->db->where('status',2);
        
        $this->db->join('pos_order','pos_order.id = pos_order_detail.orderID','left');
        $this->db->order_by('orderTime','desc');
        $query = $this->db->get('pos_order_detail');
            
        $r = $query->row_array();
        return $r['sellPrice'];
        
        
    }
    
    
    function getOrderBySupplier($supplierID)
    {
        $this->db->select('pos_product.*,pos_order_detail.buyNum,pos_top_product.id as top,pos_mart_save_amount.num as martSaveNum');
        $this->db->where('suppliers',$supplierID);
        $this->db->where('pos_order_detail.status',0);
        $this->db->where('pos_order.status !=',0);
        $this->db->where('pos_product.openStatus',1);
        
        $this->db->join('pos_order_detail','pos_order_detail.productID = pos_product.productID');
         $this->db->join('pos_top_product','pos_top_product.productID = pos_product.productID','left');
		  $this->db->join('pos_mart_save_amount','pos_mart_save_amount.productID = pos_product.productID','left');
        $this->db->join('pos_order','pos_order_detail.orderID = pos_order.id');      
        $query = $this->db->get('pos_product');
        return $query->result_array();
        
         
    }
        
    function getOrderLackByProductID($productID)
    {
     
        $this->db->select('pos_product.*,sum(pos_order_detail.buyNum) as buyNum,pos_top_product.id as top');
        $this->db->where('pos_product.productID',$productID);
        $this->db->where('pos_order_detail.status',0);
        $this->db->where('pos_order.status !=',0);
       
        
        $this->db->join('pos_order_detail','pos_order_detail.productID = pos_product.productID');
         $this->db->join('pos_top_product','pos_top_product.productID = pos_product.productID','left');
        $this->db->join('pos_order','pos_order_detail.orderID = pos_order.id');      
        $this->db->group_by('pos_product.productID');
        $query = $this->db->get('pos_product');
        return $query->row_array();
        
         
    }
	
	function urlToLink($content)	
	{
		$content = nl2br($content);
		$maxpos = strlen($content);
		$data = array();
		for($pos=0;$pos<$maxpos;$pos++)
		{
			$http = strpos($content,'https',$pos);
			if($http<=0)$http = strpos($content,'http',$pos);
			//echo $http;
			if($http===false)$pos =$maxpos;
			else
			{
				$a = strpos($content,'<br />',$http);
				if(!$a) $a = 99999999999;
		
				$b = strpos($content,' ',$http);
				if(!$b) $b = 99999999999;
				$end =	min($a,$b);
			
				$pos = $end;
			
				
				$data[] = array('start'=>$http,'end'=>$end);
			}
		
		}
	//	print_r($data);
		$pos = 0;$result = '<div style="font-size:12pt; text-align:left">';
		foreach($data as $row)
		{
			$result .= substr($content,$pos,$row['start'] - $pos);
			
			$url =substr($content,$row['start'],$row['end'] - $row['start']);
			
			if(strpos($url,'.jpg')>0||strpos($url,'.png')>0||strpos($url,'.gif')>0)
            {
                $result .='<img style="width:90%" src="'.$url.'"/>';
               
                
            }
            else
            {
                $result .='<a target="_blank" href="'.$url.'">';
                $len = strlen($url);
                if($len>45) $len = 45;

                $result .=substr($content,$row['start'],$len).'...';
                $result .='</a>';
                
            }
            
            
			
			$pos = $row['end'];
		
			
		}
		$result.=substr($content,$pos);
	
		return $result.'</div>';
		
	}
  
    function getCollect($offset,$type)
    {
        $this->db->limit(20,$offset*20);
		$this->db->select('pos_collect_order.*,ZHName,ENGName,productNum');
		if($type=='edit')$this->db->where('status >=',0);
		else $this->db->where('status',0);
		
		$this->db->order_by('pos_collect_order.id','DESC');
		$this->db->join('pos_product','pos_product.productID = pos_collect_order.productID','left');
        $query = $this->db->get('pos_collect_order');
		return $query->result_array();
    }
	function getCollectByID($id)
    {
     
		$this->db->where('id',$id);
	
        $query = $this->db->get('pos_collect_order');
		return $query->row_array();
    }
	function getCollectProgress($id)
	{
		$this->db->select('pos_collect_order_list.*,pos_sub_branch.name as shopName,pos_collect_order.productID');
		$this->db->where('pos_collect_order_list.collectID',$id);
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_collect_order_list.shopID');
		$this->db->join('pos_collect_order','pos_collect_order.id= pos_collect_order_list.collectID');
        $query = $this->db->get('pos_collect_order_list');
		return $query->result_array();
		
		
		
	} 
	
	function getPointDetail($shopID,$offset,$num)
	{
		$this->db->where('shopID',$shopID);
        $this->db->limit($num,$offset);
        $this->db->order_by('time','DESC');
		$q = $this->db->get('pos_feedback_point');
		return $q->result_array();
		
		
	}
	function getPointChange()
	{
		

		$this->db->join('pos_product','pos_product.productID = pos_point_change_table.productID','left');
			
		$q = $this->db->get('pos_point_change_table');
		return $q->result_array();
	}
    
    //EC
    
    function getEcPlatform($id = 0 )
    {
        if($id!=0) $this->db->where('platFormID',$id);
        $q = $this->db->get('pos_ec_platform');
        if($id!=0) return $q->row_array();
        else return $q->result_array();
        
        
        
    }
    function getEcPlatformOrder($platFormID,$status,$fromDate,$toDate,$timeTable='pos_order.orderTime')
    {
        if($platFormID!=0)$this->db->where('pos_ec_platform.platFormID',$platFormID);
        if($status!=0) $this->db->where('ECstatus',$status); 
        $this->db->join('pos_order','pos_order.id = pos_ec_order.orderID');
        $this->db->join('pos_ec_platform','pos_ec_platform.platFormID = pos_ec_order.platformID','left');
       $this->db->where('pos_order.status !=',0); 
        $this->db->where($timeTable.' >=',$fromDate); 
        $this->db->where($timeTable.' <=',$toDate.' 23:59:59'); 
        $this->db->order_by('pos_order.id','DESC');
        $q = $this->db->get('pos_ec_order');
     
         return $q->result_array();
        
        
        
    }
    
    
    
    
    
    function getECOrderInf($orderID,$ECID=0)
    {
          if($ECID!=0) $this->db->where('ECID',$ECID);
       if($orderID!=0) $this->db->where('orderID',$orderID);
        $this->db->join('pos_ec_platform','pos_ec_platform.platFormID = pos_ec_order.platformID','left');
           $this->db->join('pos_order','pos_order.id = pos_ec_order.orderID');
         $q = $this->db->get('pos_ec_order');
     
         return $q->row_array();
        
        
        
        
    }
    
   function getECShippingList()
   {
       
       $this->db->where('ECstatus',1);
       $this->db->where('boxNum >',0);
        $this->db->where('transportID',2);
       $this->db->where('platformID !=',2);
       
       
       $this->db->join('pos_order','pos_order.id = pos_ec_order.orderID');
         $q = $this->db->get('pos_ec_order');
     
         return $q->result_array();
       
       
       
       
   }
    function getPokemon($id)
    {
         $this->db->where('id',$id);
         $q = $this->db->get('pos_pokemon_sell');
        
        $data = $q->row_array();
         $p = explode("-",$data['productStr']);
            
  $data['detail'] = array();
        $ds = array();
            foreach($p as $each)
            {
                
                $d = explode(",",$each);
           
                $ds[]= $d;
            }
        $data['detail'] = $ds;
         
          return $data;
    }
    
    
    
    function getAllPokemon($status)
    {
        $this->db->where('status >=',$status);
         $this->db->order_by('name','ASC');
        $q = $this->db->get('pos_pokemon_sell');
       
        $data = $q->result_array();
        $result = array();
        if(count($data)>0)
        foreach($data as $row)
        {
            
            $p = explode("-",$row['productStr']);
            
            foreach($p as $each)
            {
                
                $d = explode(",",$each);
                $row['detail'][] =$d;
            }
            $result[] = $row;
        }
        return $result;
        
    }
    
}




?>
