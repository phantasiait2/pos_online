<?php 
class Product_model extends Model {
	function Product_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function getProductSql($time,$shopID,$where='')
	{
		//mysql_query("SET optimizer_switch='index_condition_pushdown=off'");
		$timeStr = explode('-',$time);
	
		$year = $timeStr[0];
		$month = $timeStr[1];
	
		$sql = "
		SELECT  pos_product.*,concessions,concessionsNum";
		
		if($shopID<1000) $sql.=",pos_suppliers.name as suppliersName";
		$sql.=" FROM ";
		if($where!='')$sql.="(select * from pos_product ".$where.") as 	  ";
		$sql.= " pos_product 
		LEFT JOIN pos_suppliers
		ON pos_product.suppliers = pos_suppliers.supplierID 
		
		LEFT JOIN
		(SELECT productID,group_concat(pos_product_discount.discount ORDER BY discount DESC SEPARATOR ',') as concessions,
		        group_concat(pos_product_discount.num ORDER BY discount DESC SEPARATOR ',') as concessionsNum
		FROM pos_product_discount GROUP BY productID) as c
		ON c.productID = pos_product.productID";
	    
	
		
		return $sql;
		
		
	}
	
	function magicProductCheck($productID)
	{
		$this->db->like('ZHName','魔法風雲會');
		$this->db->like('ZHName','補充包');	
		$this->db->like('ZHName','盒');
		$this->db->where('productID',$productID);
		$query = $this->db->get('pos_product');
		if($query->num_rows() > 0)
		{
			$this->db->where('productID',$productID);
			$query = $this->db->get('pos_magic_product');
			
			if($query->num_rows() ==0)	$this->db->insert('pos_magic_product',array('productID'=>$productID));
			
		}
	}
	function getShopAmountByBid($bid=0,$productID = 0,$shopID = -1)
	{
		$time = getdate();
		$year = $time['year'];
		$month = $time['mon'];
		
		if($shopID!=0)$this->db->where('shopID <=',600);
		else $this->db->where('shopID <=',1000);
		$this->db->where('show',1);
        $this->db->where('jointype',1);
		$this->db->where('shopID !=',100);
		 $this->db->order_by('shopID','ASC');
		$query = $this->db->get('pos_sub_branch');
		$shopList = $query->result_array();
		
		if($bid!=0)	$this->db->where('phaBid',$bid);
		else $this->db->where('productID',$productID);
		$query = $this->db->get('pos_product');
		$productList = $query->result_array();
	
		$row['num'] = 0;
		$row['shopID'] = 0;
		foreach($productList as $col)
		{
				$row['num'] += $this->PO_model->getProductNum($row['shopID'],$col['productID'],$year.'-'.$month);
             
				$row['ZHName'] = $col['ZHName'];
		}
		$result[] = $row;
		
		
		$i = 0;
		foreach($shopList as $row)
		{
			$row['num']  = 0 ; 
			
			foreach($productList as $col)
			{
				$row['num'] += $this->PO_model->getProductNum($row['shopID'],$col['productID'],$year.'-'.$month);
                   //扣掉預定商品
                $row['num'] -= $this->PO_model->getCsorderNum($row['shopID'],$col['productID']);
			}
			$row['name'] =shopNameCut($row['name']);
			
			$result[] = $row;
			
		}		
		
		
		return $result;
		
		
	}
	function magicOrderInf($orderID)
	{
		$this->db->where('orderID',$orderID);
		$query = $this->db->get('pos_magic_order');
		return $query->row_array();	
		
	}
	
	

	
	
	
	function saveChkProduct($checkID,$productID,$nowNum,$realNum,$shopID=0)
	{
			$time = getdate();
		$ret = $this->PO_model->getProductAmountInf($shopID,$productID,$time['year'].'-'.$time['mon']);
		if(empty($ret))$ret['avgCost'] = 0;
		$this->db->where('checkID',$checkID);
		$this->db->where('productID',$productID);
		$query = $this->db->get('pos_product_check');
		$datain = array(
			'checkID' =>$checkID,
			'productID' =>$productID,
			'nowNum' =>$nowNum,
			'realNum' =>$realNum,
			'cost' =>$ret['avgCost']
		);
		
	
		
		
		if($query->num_rows()>0)
		{
			$this->db->where('checkID',$checkID);
			$this->db->where('productID',$productID);
			$this->db->update('pos_product_check',$datain);
			
			
		}
		else
		{
			$this->db->insert('pos_product_check',$datain);
			
			
		}			
	$this->db->where('checkID',$checkID);
		$this->db->where('productID',$productID);
		$query = $this->db->get('pos_product_check');
		return $query->row_array();
		
		
	}
	function getAllChkProductDamage($checkID)
	{	$time = getdate();
		$year = $time['year'];
		$month = $time['mon'];

		$sql = 
		
		"	
		select pos_product.*,IFNULL(pos_product_check.realNum, 0) as realNum, pos_product_check.nowNum as nowNum ,IFNULL(pos_product_check.status,0) as status,com.avgCost FROM
		(
			select * from
			(
			select * from pos_product_amount
			where shopID = 0 and num >=0 
			) as a
			 group by productID
		
		) as com
	 	LEFT JOIN 
		pos_product_check ON pos_product_check.productID = com.productID and checkID = $checkID
		LEFT JOIN
		pos_product	ON  pos_product.productID = com.productID

		";
		
		$query = $this->db->query($sql);
		$product =  $query->result_array();
		$i = 0;
		$result =array();
		foreach($product as $row)
		{
			if(isset($row['productID'])&&empty($row['nowNum']))
			$row['nowNum'] = $this->PO_model->getProductNum(0,$row['productID'],$year.'-'.$month);
			
			if($row['nowNum']>$row['realNum']) $result[] = $row;
			$i++;
			
		}		
		
		
		return $result;
		
	}
	
	function getChkList($shopID = 0)
	{
		$this->db->where('shopID',$shopID);
		$this->db->order_by('checkID','desc');
		$query = $this->db->get('pos_product_check_list');
		return $query->result_array();
		
		
	}
	
	
	
	
	
	function getChkProduct($checkID,$status='all')
	{
	
		if($status=='overage')	$this->db->where('realNum > nowNum');
		elseif($status=='damage')	$this->db->where('realNum < nowNum');
		
		$this->db->where('checkID',$checkID);

		$this->db->join('pos_product','pos_product_check.productID = pos_product.productID','left');
		$query = $this->db->get('pos_product_check');
		
		return $query->result_array();
		
		
	}
	
	function getConsignmentSql($time,$shopID,$isConsignment)
	{
	
		$date = explode('-',$time);
		$year = $date[0];
		$month = $date[1];
		
		
	
		if($isConsignment==false)	$sql = $this->getProductSql($time,$shopID);
		else $sql= " select *, pos_consignment_amount.num as nowNum from pos_consignment_amount where  month(time)=$month and year(time)= $year and shopID =$shopID";

		return $sql ;	
		
		
		
	}
	function getIndexCardSleeve()
	{
		$query = $this->db->get('pos_card_sleeve');
		$data = $query->result_array();
		foreach($data as $row)
		{
			$result[$row['CSID']] =$row;
		}
       
      
        
		return $result;
		
	}
	
	function getCardSleeve()
	{
		$query  = $this->db->get('pos_card_sleeve');
		return $query->result_array();
	
		
	}
	function getCardSleeveByID($CSID)
	{
		
		$this->db->where('CSID',$CSID);
		$query = $this->db->get('pos_card_sleeve');
		if($query->num_rows()>0) return $query->row_array();
		else return false;
		
	}
	
	function getProductInfByProductID($productID)
	{
		$this->db->where('productID',$productID);
		$query = $this->db->get('pos_product');
		return $query->row_array();
		
	}

	
	function getProductByProductNum($productNum)
	{
		$this->db->where('productNum',$productNum);
		$query = $this->db->get('pos_product');
		return $query->row_array();
		
	}
	function getProductByBid($bid)
	{
		$this->db->where('phaBid',$bid);
		$this->db->order_by('openStatus','desc');
		$query = $this->db->get('pos_product');
		return $query->row_array();
		
	}
	
    function getAllProductByBid($bid)
    {
        
        $this->db->where('phaBid',$bid);
		$this->db->order_by('openStatus','desc');
		$query = $this->db->get('pos_product');
		return $query->result_array();
        
        
    }
    
    
    
	function getConsignmentStock($year,$month,$shopID)
	{
		$this->db->select('pos_product.*,pos_consignment_amount.avgCost');
		$this->db->select_sum('pos_consignment_amount.num');
		$this->db->join('pos_product','pos_consignment_amount.productID = pos_product.productID','left');
		$this->db->group_by('pos_consignment_amount.productID');
		if($shopID!=0) $this->db->where('shopID',$shopID);
		 $this->db->where('shopID !=',100);
         $this->db->where('shopID !=',997);
         $this->db->where('shopID !=',998);
		  $this->db->where('year(time)',$year,false);
		   $this->db->where('month(time)',$month,false);
		$query = $this->db->get('pos_consignment_amount');
		return $query->result_array();
		
	}
	function productHStock($year,$mon,$shopID)
	{
		$date = getdate(); 
		
		if($year==0&&$date!='') $file = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/stock/temp/stock_'.substr($date,0,7).'_'.$shopID.'.txt';
			else
            {
             $dir = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/stock/'.$year;
                if(!is_dir($dir)) mkdir($dir);
                
                $file = $dir.'/stock_'.$year.'_'.$mon.'_'.$shopID.'.txt';
            }

			if(file_exists($file))
			{
					$handle = fopen($file,'r');
					$contents = '';
				while(!feof($handle))
				{$contents .= fgets($handle);}
				fclose($handle);	
//		
				return json_decode($contents,true);
				
				
				
			}
			else $creareFile = true;
		
		$sql = " select *,amount.num as nowNum from (select * from pos_product_amount  where shopID =$shopID and year = $year and month = $mon) amount
			LEFT JOIN pos_product ON pos_product.productID = amount.productID	where pos_product.productID != 0 order by amount.productID ASC;
		
		";
		$query = $this->db->query($sql);
		$data['totalNum'] = $query->num_rows();
		
		if($query->num_rows==0)return false;	
		$data['product'] = $query->result_array();	
		
		$sql = " select distinct productID from pos_product_amount where shopID =$shopID  order by productID ASC";
		$query = $this->db->query($sql);
		$r = $query->result_array();
		$num = count($r);
		$j = 0;
		$totalNum = $data['totalNum'];
		for($i=0;$i<$num;$i++)
		{
		//	echo $r[$i]['productID'].','.$data['product'][$j]['productID'] .'<br/>';
			if( $j<$totalNum && $r[$i]['productID'] ==$data['product'][$j]['productID']  ) $j++	;
			else 
			{
					$data['totalNum']++;
					
					$ret = $this->PO_model->getProductAmountInf($shopID,$r[$i]['productID'],$year.'-'.$mon);
					if(isset($ret)&&!empty($ret))
					{
						 $p = $this->chkProductByProductID($r[$i]['productID']);
						 $p['nowNum']  = $ret['num'];;
						 $p['id'] = $ret['id'];
						 $p['totalCost'] = $ret['totalCost'];
						 $p['avgCost'] = $ret['avgCost'];
					
						$data['product'][]  = $p;
					}
			}
			
		}
		
		if($creareFile) 
		{
			$output = json_encode($data);
				$f = fopen($file,'w');
			fprintf($f,"%s",$output);
					fclose($f);		
					
					
			
		}
		return $data;
		
	}
			
	function getProductStock($find,$shopID)
	{	
	
	$creareFile =false;
		$date = getdate(); 
		if(!isset($find['time'])) 
		{
			$time = $date['year'].'-'.$date['mon'].'-'.$date['mday'];
		}
		else $time = $find['time'];;
	
	
		$nowTime = explode('-',$time);
		$year = $nowTime[0];
		$mon = $nowTime[1];
	

		if($year<$date['year']||($year==$date['year']&&$mon<$date['mon']))
		{
			
       
			return $this->productHStock($year,$mon,$shopID);
		}
		
		
		
		$where=" where pos_product.id!=0 ";
		$flag= false;
		if(isset($find['productID'])&&$find['productID']!='')
		{
			$where .=" and pos_product.productID = '".$find['productID']."'";
			$flag =true;
		}
		if(isset($find['barcode'])&&$find['barcode']!='')
		{
			$num = strlen(strval($find['barcode']));
		
			if($num==13) $EanBarcode = substr($find['barcode'],1);//去首位
			else if($num==12)$EanBarcode = '0'.$find['barcode'];//首位補0
			
			if(!isset($EanBarcode))$where .=" and pos_product.barcode = '".$find['barcode']."'";
			else $where .=" and (pos_product.barcode = '".$find['barcode']."' or pos_product.barcode ='".$EanBarcode."' )";
			$flag =true;
		}
		if(isset($find['productNum'])&&$find['productNum']!='')
		{ 	
			
			
			$where .=" and pos_product.productNum  like  '".str_replace('*','_',$find['productNum']."'");
			$flag =true;
			
				
		}
        if(isset($find['hide'])&&$find['hide']==1);
        else
		{ 	
			
			
			$where .=" and pos_product.hide = 0 ";  
			$flag =true;
			
				
		}
         if(isset($find['xclass'])&&$find['xclass']==2);
         else if(isset($find['xclass'])&&$find['xclass']==1)
         {
             
			$where .=" and pos_product.category = 'X' ";  
			$flag =true;
			
             
         }
        else 
		{ 	
			
			
			$where .=" and pos_product.category != 'X' ";  
			$flag =true;
			
				
		}
		if(isset($find['query'])&&$find['query']!='')
		{
			$where .=" and ((pos_product.ZHName  like '%".$find['query']."%') or (pos_product.ENGName  like '%".$find['query']."%'))";
			$flag =true;	
			
		}
		if(isset($find['ZHName'])&&$find['ZHName']!='')
		{
			$find['ZHName'] = str_replace("'","''",$find['ZHName']);
			$where .=" and pos_product.ZHName  like '%".$find['ZHName']."%'";
			$flag =true;
		}
		if(isset($find['ENGName'])&&$find['ENGName']!='')
		{
			$find['ENGName'] = str_replace("'","''",$find['ENGName']);
			
			$where .=" and pos_product.ENGName  like '%".$find['ENGName']."%'";
			$flag =true;
		}
		if(isset($find['publisher'])&&$find['publisher']!='')
		{
			$find['publisher'] = str_replace("'","''",$find['publisher']);
			
			$where .=" and pos_product.publisher  like '%".$find['publisher']."%'";
			$flag =true;
		}
	
		
		if(isset($find['suppliers'])&&$find['suppliers']!='')
		{
			
			if($find['suppliers']!=0)
			$where .=" and pos_product.suppliers  = ".$find['suppliers']."";
			$flag =true;
		}
		if(isset($find['status'])&&$find['status']!='')
		{
			
			if($find['status']!=2)
			$where .=" and pos_product.openStatus  = ".$find['status']."";
		
		}
		if(isset($find['topProduct'])&&$find['topProduct']!='')
		{
			
			if($find['topProduct']==1)	$where .=" and pos_product.productID in (select productID from pos_top_product)";
			else if($find['topProduct']==3)	$where .=" and pos_product.productID in (select productID from pos_top_product where top10 = 1)";
			else if($find['topProduct']==0)	$where .=" and pos_product.productID not in (select productID from pos_top_product)";
			$flag =true;
		}
		
		
	
		$sql = $this->getProductSql($time,$shopID,$where);
		// $this->db->insert('pos_test',array('content'=>'sqlour'.date("Y-m-d H:i:s")));
		$sql .=" where pos_product.id!=0 ";
		/*
		if(isset($find['inStock'])&&$find['inStock']!='')
		{
			
			if($find['inStock']==1)	$sql .=" and pos_product_amount.id is not null";
			$flag =true;
		}
		*/
		if(isset($find['withOutConsignment'])&&$find['withOutConsignment']==true)
		{
			
			$sql .=" and pos_consignment_amount.id is  null";
			$flag =true;
		}
		if(isset($find['offset']) && isset($find['num']))
        {
            $sql .=" limit ".$find['offset'].','.$find['num'];
            
            
        }

		$query = $this->db->query($sql);
      //   $this->db->insert('pos_test',array('content'=>'queryok'.date("Y-m-d H:i:s")));
        //  $this->db->insert('pos_test',array('content'=>$sql));
		$data['totalNum'] = $query->num_rows();
		

		if($query->num_rows==0)return false;	
		$data['product'] = $query->result_array();		
		$this->cardSleeveInf($data['product']);
		$this->productStockNum($data['product'],$shopID,$time);
		 //$this->db->insert('pos_test',array('content'=>'getnum'.date("Y-m-d H:i:s")));
		$orderflag = false;
		$orderNowToken = false;
		if(isset($find['order2'])&&$find['order2']!='')
		{
			if($find['order2']!='0')
			{
				 $this->PO_model->arraySort($data['product'],$find['order2'],$find['sequence2']);
				$orderflag =true;
			}

			
		}
		if(isset($find['order1'])&&$find['order1']!='')
		{
			if($find['order1']!='0')
			{
				 $this->PO_model->arraySort($data['product'],$find['order1'],$find['sequence1']);
				$orderflag =true;
			}

			
		}
		if(!$orderflag)  
		{
			
			$this->PO_model->arraySort($data['product'],'nowNum','DESC');
		}

 //$this->db->insert('pos_test',array('content'=>'sortfinidh'.date("Y-m-d H:i:s")));
		if(isset($find['num'])&&$find['num']!='')
		{
			
			if($find['num']!=0)
			{
				$result['totalNum'] = $data['totalNum'];
				$result['product'] =  array_slice($data['product'] ,$find['start'],$find['num']);
				return $result;
			}
		}
		
	
		
		return $data;
		
		
	}
	function productStockNum(&$data,$shopID,$time)
	{
		$i = 0;
		foreach($data as $row)
		{
			
			$ret = $this->PO_model->getProductAmountInf($shopID,$row['productID'],$time);
			
			if(!empty($ret))
			{
				$data[$i]['comNum'] =$this->PO_model->getProductNum(0,$row['productID'],$time);
				$data[$i]['nowNum']  = $ret['num'];;
				$data[$i]['id'] = $ret['id'];
				$data[$i]['totalCost'] = $ret['totalCost'];
				$data[$i]['avgCost'] = $ret['avgCost'];
			
			}
			else
			{
				$data[$i]['nowNum'] = 0;
				$data[$i]['comNum'] = 0;
				$data[$i]['id'] = 0;
				$data[$i]['totalCost'] = 0;
				$data[$i]['avgCost'] = 0;				
			}
			$i++;	
		}
	}
	
	
	
	function cardSleeveInf(&$data,$multi=true)
	{
		
		//card sleeve
		$cardSleeve = $this->getIndexCardSleeve();
		$i= 0 ; 
		
		if($multi==false)
		{
			$data['cardSleeveInf'] = '';
			if(isset($data['cardSleeve'])&&$data['cardSleeve']!=0)
			{
				$productCs = explode('-',$data['cardSleeve']);
				
				foreach($productCs as $each)
				{
					$card = explode(',',$each);
					if(isset($cardSleeve[$card[0]]))
                    {
                        
                        $data['cardSleeveInf'].=$cardSleeve[$card[0]]['CSsize'].'('.$card[1].')<br/>';
                    $data['cardSleeveArray'][] = array(
                        'name' =>$cardSleeve[$card[0]]['CSsize'],
                        'productID' =>$cardSleeve[$card[0]]['productID'],
                        'need'  =>$card[1],
                        'pack'  =>$cardSleeve[$card[0]]['num']
                    
                    
                    );
                        
                    }
				}
			}
			
			
			
			
		}
		else
		{
			foreach($data as $row)
			{
				$data[$i]['cardSleeveInf'] = '';
				if($row['cardSleeve']!=0)
				{
					$productCs = explode('-',$row['cardSleeve']);
					
					foreach($productCs as $each)
					{
						$card = explode(',',$each);
						if(isset($cardSleeve[$card[0]]))
                        {
                              $data[$i]['cardSleeveInf'].=$cardSleeve[$card[0]]['CSsize'].'('.$card[1].')<br/>';
                        
                         $data[$i]['cardSleeveArray'][] = array(
                        'name' =>$cardSleeve[$card[0]]['CSsize'],
                        'productID' =>$cardSleeve[$card[0]]['productID'],
                        'need'  =>$card[1],
                        'pack'  =>$cardSleeve[$card[0]]['num']
                    
                    
                    );
                        
                        
                        
                        }
                          
					}
				}
				
				$i++;		
			}
            return $data;
		}
		// call by reference
		
		
		
	}
	
	
	
	function getShopProductNum($productID,$date)
	{
		echo 'wrong';
		/*
		$sql = "select sum(cTemp.num)as shopNum,productID  from
			(
			select * from
			(
					 select * from pos_product_amount
						where ((year= $date[year]  and month<= $date[mon] )or year< $date[year])  and productID = $productID and shopID<=1000 and shopID!=0 and  shopID!=100
					order by year desc ,month desc
			) as my_amount
			 group by productID	,shopID
			 ) as cTemp
			  "	;
		
		$query = $this->db->query($sql);
		$ret =  $query->row_array();	
		if($ret['shopNum']=='')return 0;
		return $ret['shopNum'];
		*/
	}
	function getOrderNum($productID,$time)
	{
		$sql ="SELECT sum(pos_order_shipment_detail.sellNum) as orderNum 
		from pos_order_shipment
		LEFT JOIN pos_order_shipment_detail ON pos_order_shipment_detail.shipmentID = pos_order_shipment .id
		LEFT JOIN pos_order_detail ON pos_order_shipment_detail.rowID= pos_order_detail .id
		WHERE `pos_order_detail`.`productID` =$productID and`pos_order_shipment`.`shippingTime` >= '$time' and pos_order_shipment.status >0 and pos_order_shipment.shopID>1000
		GROUP BY `pos_order_detail`.`productID`";
		$query = $this->db->query($sql);
		$r = $query->row_array();
		if(!isset($r['orderNum'])) $r['orderNum'] = 0;
		return $r['orderNum'];
		
			
		
	}
	function getProductSellRecord($productID,$time,$shopID = 0,$to='')
	{
		$sql = "SELECT sum(pos_product_sell.num) as sellNum,count(DISTINCT  shopID) as shopNum
		from `pos_product_sell` 
		WHERE `pos_product_sell`.`time` >= '$time'  and productID = $productID  and shopID !=100";
		if($shopID!=0) $sql.=" and shopID = ".$shopID;
		if($to!='') $sql.=" and `pos_product_sell`.`time` <= '$to'";
		$sql .=" GROUP BY `pos_product_sell`.`productID`";
   
		$query = $this->db->query($sql);
		$r = $query->row_array();
		if(!isset($r['sellNum'])) $r['sellNum'] = 0;
		return $r;
		
		
	}
	
	function getMyFlow($productID,$shopID)
	{
		$date = getdate(); 
		$month = ($date['mon']) - 3;
		if($month<=0) 
		{
			$month+=12;
			$year = $date['year']-1;
		}
		else $year = $date['year'];
		
		$time = $year.'-'.$month.'-'.$date['mday'];
		 $r = $this->getProductSellRecord($productID,$time,$shopID);
		 $data['result'] = true;
		
		 $data['flowNum'] = round($r['sellNum']/3,2);	
		 return $data['flowNum'];
		
	}
	
	function chkTopProduct($productID)
	{
		$this->db->where('productID',$productID);
		$query = $this->db->get('pos_top_product');
		if($query->num_rows()> 0 )return true;
		else return false;
	
		
	}
	function flowUpdate()
    {
        $period = 3;//month
        $date = getdate(); 
		$month = ($date['mon']) - $period;
        if($month<=0) 
		{
			$month+=12;
			$year = $date['year']-1;
		}
		else $year = $date['year'];
		
		$time = $year.'-'.$month.'-'.$date['mday'];
        $this->db->where('openStatus',1);
        $this->db->where('hide !=',1);
        $query = $this->db->get('pos_product');

        $product = $query->result_array();
$i=0;
        foreach($product as $row)
        {
            $r = $this->getProductSellRecord($row['productID'],$time);
			
			$this->db->where('productID',$row['productID']);
			if(isset($r['shopNum']) && $r['shopNum']> 0) $flow = round($r['sellNum']/($r['shopNum']*$period),0);
			else $flow = 0;
			$this->db->update('pos_product',array('flowNum'=>$flow,'sellAmount'=>$r['sellNum']));
        }

       
        
        
        
    }
    
    function topProductUpdate()
    {
        $this->db->where('type',1);
        $this->db->order_by('sellAmount','DESC');
            
        $query = $this->db->get('pos_product');
        $r = $query->result_array();
        $i = 0;
        $this->db->where('id !=',0);
        $this->db->delete('pos_top_product');
        foreach($r as $row)
        {
            if($i>100) break;
            if($row['suppliers']!=21) $i++;
            
            $this->db->insert('pos_top_product',array('productID'=>$row['productID']));
            
            
            
        }
        
        
        
        
    }
    
    
    
	function getTopProduct($token = 'top')
	{
		$date = getdate(); 
		$month = ($date['mon']) - 3;
		if($month<=0) 
		{
			$month+=12;
			$year = $date['year']-1;
		}
		else $year = $date['year'];
		
		$time = $year.'-'.$month.'-'.$date['mday'];
		//echo 'top'. time() ;
		
		if($token=='top')$sql ="SELECT `pos_product`.*,pos_top_product.top10,pos_suppliers.name as supplierName,pos_suppliers.day
		FROM (`pos_top_product`) 
		LEFT JOIN `pos_product` ON `pos_product`.`productID` = `pos_top_product`.`productID` 
		LEFT JOIN `pos_suppliers` ON `pos_suppliers`.`supplierID` = `pos_product`.`suppliers` 
		where pos_product.openStatus = 1 order by `pos_suppliers`.`supplierID`
		 ";
		 else $sql ="SELECT `pos_product`.*,pos_suppliers.name as supplierName,pos_suppliers.day
		FROM  pos_product
		LEFT JOIN `pos_top_product` ON `pos_product`.`productID` = `pos_top_product`.`productID` 
		LEFT JOIN `pos_suppliers` ON `pos_suppliers`.`supplierID` = `pos_product`.`suppliers` 
		where pos_top_product.productID is NULL and pos_product.openStatus = 1 order by `pos_suppliers`.`supplierID`
		 ";
		 
	    $path = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/stock_agent/';
		$file = $path.'report_'.$token.'_'.$date['year'].'_'.$date['month'].'_'.$date['mday'].'.txt';
			
		if(file_exists($file))
		{
				$handle = fopen($file,'r');
				$contents = '';
			while(!feof($handle))
			{$contents .= fgets($handle);}
			fclose($handle);	
//		
			return json_decode($contents,true);
			
			
			
		}
		else $creareFile = true;
			
	
		 
		 

		$query = $this->db->query($sql);
		$product =  $query->result_array();		
		$i = 0 ;
		$shopList = $this->PO_model->getShopList();
		//echo 'each'. time() ;
		 foreach($product as $row)
		{
 $r = $this->getProductSellRecord($row['productID'],$time);
			 $product[$i]['sellNum']  =$r['sellNum'];
             
			$comNum =  $this->Order_model->getAvailableNum($row['productID']);;
			if($comNum<=0 && $token!='top') 
			{
				unset($product[$i]);
				$i++;
				continue;
			}
			
			$product[$i]['comNum'] = $comNum;
			
			
		
			
			
			
			$product[$i]['orderNum'] = $this->getOrderNum($row['productID'],$time);
			$product[$i]['nowNum'] = $this->PO_model->getProductNum(0,$row['productID'],$date['year'].'-'.$date['mon']);
			$product[$i]['pre']  = $this->Order_model->getPreTime($row['productID']);
			
			$product[$i]['purchasePrice'] = $this->getLastPrice($row['productID'],0);	
			$product[$i]['shopNum'] = 0;
			
			
			$day = $row['day'];
			$comNum =  $product[$i]['comNum'];
			$orderNum = $product[$i]['orderNum'];
			$sellNum = $product[$i]['sellNum'];
			$boxcase = $row['case'];
			$shouldOrder =0;
			$num = round((($sellNum*1.5+$orderNum) - $comNum )  *$day/90 ,0);
			if($num<0) $num = 0;
			//if((num)) $num = 0;
			if(($num+$comNum)<0) $num = $num-$comNum;
			if($boxcase==0)
			{
				$result = $num;
			}
			else
			{
				if(($num%$boxcase)/$boxcase>0.33)
				{
						 $result = floor($num / $boxcase)+1;
				}
				else  $result = floor($num / $boxcase);
		
				
			}
			
			$product[$i]['shouldOrder'] = $result ;
			
			
			
			
			
			
			
			
			
			
			
			$i++;
			
		}	
		//	echo 'return'. time() ;	
		
		if(isset($creareFile) &&$creareFile==true) 
		{
            deldir($path);
           
			$output = json_encode($product);
				$f = fopen($file,'w');
			fprintf($f,"%s",$output);
					fclose($f);		
			
		}
		
		
		
		return $product;
	}
	function getNotTopProduct()
	{
	
		
		return 	$this->getTopProduct('nottop');
	}
    
    
    
	function getRentTimesPure()
	{
		$date = getdate(); 
		 $file = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/stock_agent/rent_'.$date['year'].'_'.$date['month'].'_'.$date['mday'].'.txt';
			
		if(file_exists($file))
		{
				$handle = fopen($file,'r');
				$contents = '';
			while(!feof($handle))
			{$contents .= fgets($handle);}
			fclose($handle);	
//		
echo 'sss';
			return json_decode($contents,true);
			
			
			
		}
		else $creareFile = true;
		
		
		
		
		$sql = "select * from
		(
		SELECT productID,count(pos_rent.id) as times FROM `pos_inshop_amount`
		left join pos_rent on pos_rent.shopID = pos_inshop_amount.shopID and pos_rent.rentID = 
		pos_inshop_amount.rentID 
		 group by productID
			) as b  
			
			ORDER BY times DESC limit 0 ,500";
			$query = $this->db->query($sql);

	;	
		$product =  $query->result_array();	
		if(isset($creareFile) &&$creareFile==true) 
		{
			$output = json_encode($product);
				$f = fopen($file,'w');
			fprintf($f,"%s",$output);
					fclose($f);		
			
		}
		
		
	
	
	
		return $product;
		
	}
	
	
	function getRentTimes($shopID=0)
	{
		$sql = "select * from
		(
		select * from
		(
		SELECT productID,count(pos_rent.id) as times FROM `pos_inshop_amount`
		left join pos_rent on pos_rent.shopID = pos_inshop_amount.shopID and pos_rent.rentID = 
		pos_inshop_amount.rentID ";
		  if($shopID!=0)$sql.= " where shopID .=".$shopID;
		 $sql.=" group by productID
			) as b  
			
			ORDER BY times DESC limit 0 ,500
			) as a 
		left join pos_product on pos_product.productID = a.productID"
		;	

		$query = $this->db->query($sql);

	;	
		return $query->result_array();	
		
	}
	
	function getEliminateProduct($shopID = 0)
	{
		$date = getdate(); 
		$month = ($date['mon']) - 6;
		if($month<=0) 
		{
			$month+=12;
			$year = $date['year']-1;
		}
		else $year = $date['year'];
		
		$time = $year.'-'.$month.'-'.$date['mday'];
		 

		$sql ="SELECT `pos_product`.*, IFNULL(sellNum,0) as sellNum ,IFNULL(orderNum,0) as orderNum 
		from `pos_product` 
		
		LEFT JOIN
		(SELECT sum(pos_product_sell.num) as sellNum,productID 
		from `pos_product_sell` 
		WHERE `pos_product_sell`.`time` >= '$time' and `pos_product_sell`.`shopID` !=100 GROUP BY `pos_product_sell`.`productID`
		) as a
		ON a.productID = `pos_product`.`productID` 
		LEFT JOIN
		(SELECT sum(pos_order_shipment_detail.sellNum) as orderNum,productID 
		from pos_order_shipment
		LEFT JOIN pos_order_shipment_detail ON pos_order_shipment_detail.shipmentID = pos_order_shipment .id
		LEFT JOIN pos_order_detail ON pos_order_shipment_detail.rowID= pos_order_detail .id
		
		WHERE `pos_order_shipment`.`shippingTime` >= '$time' and pos_order_shipment.status >0  
		GROUP BY `pos_order_detail`.`productID`
		
		) as b
		ON b.productID = `pos_product`.`productID`
		LEFT JOIN
		(
			select max(time) as purchaseTime,pos_product_purchase.productID from pos_product_purchase where shopID = 0 group by pos_product_purchase.productID 
		 ) as purchase
		ON purchase.productID  = `pos_product`.`productID` 
		
		where (type =1 or type=5) and pos_product.productID not  in(select productID from pos_top_product) and (purchaseTime <='$time' or purchaseTime = '')
		 ";

		$query = $this->db->query($sql);
		$product  = $query->result_array();	
		$num =count($product);
		$i = 0 ;
		$shopList = $this->PO_model->getShopList();
		 foreach($product as $row)
		{
			$product[$i]['nowNum'] = $this->PO_model->getProductNum(0,$row['productID'],$year.'-'.$month);
			$product[$i]['shopNum'] = 0;
			foreach($shopList as $each)$product[$i]['shopNum'] += $this->PO_model->getProductNum($each['shopID'],$row['productID'],$year.'-'.$month);
			$i++;
			
		}		
		return 	$product;
	}	
	
	
	
	
		
	function getProductByBarcode($barcode)
	{
		$sql = "SELECT pos_product.*,group_concat(pos_product_discount.discount ORDER BY pos_product_discount.discount DESC SEPARATOR ',') as concessions,
		        group_concat(pos_product_discount.num ORDER BY pos_product_discount.discount DESC SEPARATOR ',') as concessionsNum
				FROM  pos_product
				LEFT JOIN pos_product_discount
				ON pos_product_discount.productID = pos_product.productID
				 WHERE pos_product.barcode = '".$barcode."'
				 GROUP BY pos_product_discount.productID";
		$query = $this->db->query($sql);

		if($query->num_rows==0)return false;	
		return $query->row_array();		
		
	}

	function timeStr($timeArray)
	{
		$year = $timeArray[0];
		$month = $timeArray[1];
		if($month==0) $str = ($year-1).'-'.$month.'-31';
		else if($month ==2) $str = $year.'-'.$month.'28';
		else if($month==1||$month==3||$month==5||$month==7||$month==8||$month==10||$month==12)
		{
			$str = $year.'-'.$month.'-31';
		}
		else $str = $year.'-'.$month.'-30';
		return $str;
		
	}
	
	
	
	function getProductList($timeArray,$shopID)
	{
		
		$sql = $this->getProductSql($timeArray,$shopID);
		$query = $this->db->query($sql);
		return $query->result_array();
		
	}
	function getSuppliers()
	{
		
		$this->db->order_by('order','asc');
		$query = $this->db->get('pos_suppliers');
		return $query->result_array();
		
	}
	function getSupplierByID($supplierID)
	{
		
		$this->db->where('supplierID',$supplierID);
		$query = $this->db->get('pos_suppliers');
		if($query->num_rows()>0) return $query->row_array();
		else return false;
		
	}
	
	function getPublisher()
	{
		$this->db->distinct('publisher');
		$this->db->select('publisher');
		$this->db->where('publisher !=','');
		$this->db->order_by('publisher','asc');
		$query = $this->db->get('pos_product');
		return $query->result_array();
		
	}
	function getCabinetProduct($cabinet,$all=false)
	{
		if($all) $this->db->like('cabinet',$cabinet);
		else $this->db->where('cabinet',$cabinet);
        $this->db->order_by('cabinet','DESC');
		$this->db->order_by('productNum','asc');
		$query = $this->db->get('pos_product');
		return $query->result_array();
		
		
	}
	
	
	
	
	function getCabinet()
	{
		$this->db->distinct('cabinet');
		$this->db->select('cabinet');
		$this->db->where('cabinet !=','');
		$this->db->order_by('cabinet','asc');
		$query = $this->db->get('pos_product');
		return $query->result_array();
		
	}
	function currentNumUpdate($productID,$num,$totalCost,$avgCost,$shopID,$key=false)
	{
		$time = getdate();
		
		$this->db->where('productID',$productID);
		$this->db->where('shopID',$shopID);
		$query = $this->db->get('pos_current_product_amount');
		$datain['timeStamp'] = date("Y-m-d H:i:s");
		if(empty($num)) $num = 0 ;;
		if(empty($avgCost)) $avgCost = 0 ;;
		if(empty($totalCost)) $totalCost = 0 ;;
		$datain['num'] = $num;
		$datain['totalCost'] = $totalCost;
		$datain['avgCost'] = $avgCost;
		
		
		if($query->num_rows()>=1)
		{
			
			$this->db->where('shopID',$shopID);
			$this->db->where('productID',$productID);
			$this->db->update('pos_current_product_amount',$datain); 
		}
		else
		{
			if($num==0) return;
			$datain['shopID'] = $shopID;
			$datain['productID'] = $productID;
			$this->db->insert('pos_current_product_amount',$datain);
			
		}
			$estDistribute = $this->PO_model->getAvailableNum($productID);//預計要被分配掉，訂貨優先
				$remain = $num-$estDistribute;

		
		
			if($remain>0 && $shopID==0 && $key)
			{
				$this->db->where('productID',$productID);
		 		$this->db->update('pos_product',array('timeStamp'=>date("Y-m-d H:i:s"),'openStatus'=>1,'hide'=>0,'ig'=>0));
			}
            else 
            {
                $this->db->where('productID',$productID);
		 		$this->db->update('pos_product',array('ig'=>0,'igBreak'=>date("Y-m-d") ));
                
                
            }
		
		return;			
		
	}
	
    function md5pw($str,$code='phantasia')
    {
        return md5($str.$code);
        
    }
	
	
	function updateNum($productID,$num,$shopID,$purchacePrice=-1)
	{
		$time = getdate();
		$year = $time['year'];
		$month = $time['mon'];
		$this->db->where('shopID',$shopID);
		$this->db->where('productID',$productID);
		$this->db->where('year',$year);
		$this->db->where('month',$month);

		$query = $this->db->get('pos_product_amount');
		if($query->num_rows()>=1)
		{
			$data = $query->row_array();
			if($purchacePrice==-1)$purchacePrice = round($data['avgCost']);
			$datain['num'] = $data['num'] + $num;
			$datain['totalCost'] = $data['totalCost']+ $purchacePrice*$num;
			$datain['avgCost'] = $purchacePrice;
			if($num>0)
			{
				if($datain['num']!=0)	$datain['avgCost'] = round(($data['num']*$data['avgCost']+$num*$purchacePrice)/$datain['num']);
				
			}
			$this->db->where('id',$data['id']);
			$this->db->update('pos_product_amount',$datain); 
		
			
			
		}
		else
		{
			if($purchacePrice==-1)$purchacePrice = $this->getAvgCost($productID);
			$this->db->where('shopID',$shopID);
			$this->db->where('productID',$productID);
			$this->db->order_by('year','DESC');
			$this->db->order_by('month','DESC');
			$query = $this->db->get('pos_product_amount');
			$datain =array(
			'shopID'=>$shopID,
				'productID'=>$productID,
				'month'=>$month,
				'year'=>$year,
				'totalCost'=>$purchacePrice*$num,
				'avgCost'=>$purchacePrice
			);
			
			
			if($query->num_rows()>=1)
			{
				$data = $query->row_array();
				$datain['num'] = $data['num'] + $num;
				$datain['totalCost'] = $data['totalCost']+ $purchacePrice*$num;
				if($num>0)
				{
                    if($datain['num']==0) $datain['avgCost']= 0 ;
				else $datain['avgCost'] = round(($data['num']*$data['avgCost']+$num*$purchacePrice)/$datain['num']);
				
				}
			}
			else $datain['num'] = $num;
			$this->db->insert('pos_product_amount',$datain);
			
		
		}
		/*
		$this->db->where('productID',$productID);
		$this->db->update('pos_product',array('num'=>$datain['num']));
		*/
		if($num>0) $key=true;
		else $key = false;
		$this->currentNumUpdate($productID,$datain['num'],$datain['totalCost'],$datain['avgCost'],$shopID,$key);

		
		
		$datain = array(
			'productID' =>$productID,
			'shopID'    => $shopID,
			'num'       =>$num,
			'time'      =>date("Y-m-d H:i:s"),
			'uri'       =>$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3),
			'post'      =>json_encode($_POST),
			'account'   =>$this->data['account']
		);
	   	$this->db->insert('pos_product_IO',$datain);
	
		
		
		return;		
		
		
	}
	
	function getMaxID()
	{
		$this->db->select("productID");
		$this->db->limit(1);
		$this->db->order_by('productID','DESC');
		$query = $this->db->get('pos_product');
		$data = $query->row_array();	
		return $data['productID'];
		
		
		
	}
	function retStatus($data,$type)
	{
		
		if($type=='purchase')
		{	
			if(isset($data['status']))$data['statusID']  = $data['status'];
			else $data['statusID'] = 0;
				$data['status'] =  '已入庫';
				
		}
		else
		{
			if(!isset($data['status'])) return array();
			
			$data['statusID']  = $data['status'];
			switch($data['status'])
			{
				case 5:	
				$data['lastStatus'] = '';
				$data['status'] =  '達預算上限，待主管審核';
				$data['nextStatus'] = '主管審核通過';
				$data['nextStatusID'] = '3';
				$data['nextLevel'] = '150';
				break;
				case 3:	
				
				$data['lastStatus'] = '待主管審核';
				$data['lastStatusID'] = '5';
				$data['status'] =  '主管審核通過';
				$data['nextStatus'] = '會計審核通過';
				$data['nextStatusID'] = '4';
				$data['nextLevel'] = '80';
				break;
				case 4:	
				
				$data['lastStatus'] = '主管審核通過';
				$data['lastStatusID'] = '3';
				$data['status'] =  '會計審核通過';
				$data['nextStatus'] = '採購單送出';
				$data['nextStatusID'] = '1';
				$data['nextLevel'] = '0';
				break;
				case 1:	
				
				$data['lastStatus'] = '會計審核通過';
				$data['lastStatusID'] = '4';
				$data['status'] =  '採購單送出';
				$data['nextStatus'] = '採購單成立';
				$data['nextStatusID'] = '2';
				$data['nextLevel'] = '0';
				break;
				case 2:
				$data['lastStatus'] = '採購單送出';
				$data['lastStatusID'] = '1';	
				$data['status'] =  '採購單成立';
				$data['nextStatus'] = '';
				$data['nextLevel'] = '0';
				break;
				
				case -1:	
				$data['status'] =  '已到貨';
				break;				
				
			}
			
			
			
			
		}
		
		return $data;
	}
	
	
	
	function statusChange($data,$type)
	{
		$result = array();
	
			foreach($data as $row)
			{
				
				$result[] = $this-> retStatus($row,$type);
			}
		
	
		return $result;
		
	}
	
	function purchaseInLimit($supplierID,$inComing,$year,$month)
    {
        $r = $this->getPurchaseBudget($supplierID,$year,$month);
        if($r['limitAmount']>=$r['used']+$inComing) return true;
        else return false;
  
        
        
    }
    

    
    
    function getPurchaseBudget($supplierID,$year,$month)
    {
        
        
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        if($supplierID!=0) $this->db->where('supplierID',$supplierID);
        $query = $this->db->get('pos_purchase_budget');
        $r =   $query->row_array();
        if(empty($r))
        {
            
            $r['supplierID'] = $supplierID;
            $r['limitAmount'] = 0;
              $r['used'] = 0;
            $r['insert'] = 1;
            
        }
        else
        {
            
            $r['used']  =   $this->getUsedBudget($supplierID,$year,$month);
            
            
        }
        
        return $r;
        
        
        
    }
    
    function getUsedBudget($supplierID,$year,$month)
    {
        
        if($supplierID!=0) $this->db->where('supplierID',$supplierID);
         $this->db->where('status !=',0);
         $this->db->where('year(pos_product_purchase_order_inf.timeStamp)',$year,false);
        $this->db->where('month(pos_product_purchase_order_inf.timeStamp)',$month,false);
        $query = $this->db->get('pos_product_purchase_order_inf');
         $r =   $query->result_array();
        $total = 0 ;
        foreach($r as $row)
        {
            
            $total+=$row['total'];
            
        }
        return $total;
        
        
    }
     function getAllPurchaseBudget($year,$month)
    {
        
        $this->db->select('pos_purchase_budget.*,pos_suppliers.name as supplierName');
        $this->db->where('year',$year);
        $this->db->where('month',$month);
         $this->db->join('pos_suppliers','pos_suppliers.supplierID = pos_purchase_budget.supplierID','left');
        $query = $this->db->get('pos_purchase_budget');
        $r =  $query->result_array();
         $result = array();
         foreach($r as $row )
         {
             $row['used']  =   $this->getUsedBudget($row['supplierID'],$year,$month);
             $result[] = $row  ;
         }
         
        return $result;
        
    }
	
	function getPurchaseList($offset,$num,$type,$purchaseID = 0,$status = 0,$supplierID = 0,$from = '',$to = '')
	{
		
		if($type=='purchase') $db = 'pos_product_purchase_inf';
		else if ($type=='purchaseOrder') $db = 'pos_product_purchase_order_inf';
		if($status==100) $this->db->where('status !=',0);
        else if($status==50) 
        {
            $this->db->where('status !=',-1);
            $this->db->where('status !=',0);
        }
		else if($status!=0)$this->db->where('status',$status);
		
        if($from!='')$this->db->where($db.'.timeStamp >=',$from);
         if($to!='')$this->db->where($db.'.timeStamp <=',$to);

		if($supplierID!=0)$this->db->where('pos_suppliers.supplierID',$supplierID);
		$this->db->select($db.'.*,pos_suppliers.name as supplier,pos_suppliers.invoiceWay','left');
		$this->db->join('pos_suppliers',$db.'.supplierID = pos_suppliers.supplierID');
		$this->db->order_by('purchaseID','DESC');
		$this->db->limit($num,$offset);
		if($purchaseID!=0) $this->db->where('purchaseID',$purchaseID);
		$query = $this->db->get($db);
		
		if($purchaseID!=0)return $this->retStatus($query->row_array(),$type);
		else return $this->statusChange($query->result_array(),$type);
		
	}
	function getPurchaseListByInvoice($supplierID = 0,$from = '',$to = '',$consignmentType = 0)
    {
        $type='purchase';
        $db = 'pos_product_purchase_inf';
        
        $this->db->where('pos_product_purchase_inf.type',$consignmentType);
        
      if($supplierID!=0)$this->db->where('pos_suppliers.supplierID',$supplierID);
        if($from!='')$this->db->where($db.'.accountTime >=',$from);
         if($to!='')$this->db->where($db.'.accountTime <=',$to);  
        $this->db->select($db.'.*,pos_suppliers.name as supplier,pos_suppliers.invoiceWay','left');
		$this->db->join('pos_suppliers',$db.'.supplierID = pos_suppliers.supplierID');
		$this->db->order_by('purchaseID','DESC');
        $query = $this->db->get($db);
		 return $this->statusChange($query->result_array(),$type);
    }
        
        
        
	function getPurchaseDetailByID($purchaseID,$type,$productID = 0,$rowID = 0)
	{
		
       
		if($type=='purchase') 
        {
            $db = 'pos_product_purchase';
             $this->db->select('*,'.$db.'.id as rowID');
        }
		else if ($type=='purchaseOrder') $db = 'pos_product_purchase_order';
        
		if($productID!=0)$this->db->where($db.'.productID',$productID);
        if($rowID!=0)$this->db->where($db.'.id',$rowID);
		$this->db->where('purchaseID',$purchaseID);
		$this->db->join('pos_product','pos_product.productID = '.$db.'.productID','left');
        $this->db->order_by('cabinet','ASC');
		$query = $this->db->get($db);
		
		if($productID!=0)return $query->row_array();
		else return $query->result_array();
	
	

		
	}
	
    function getPurchaseByProductID($productID,$from,$to,$type,$supplier = 0)
    {
        $this->db->where('timeStamp >=',$from);
        $this->db->where('timeStamp <=',$to);
        $this->db->where('type',$type);
        if($supplier!=0)  $this->db->where('supplierID',$supplier);
        $this->db->join('pos_product_purchase','pos_product_purchase.purchaseID = pos_product_purchase_inf.purchaseID','left');
        $this->db->where('productID',$productID);
        $query = $this->db->get('pos_product_purchase_inf');
        return $query->result_array();
        
         
        
        
    }
    
    
    function getConsignmentAmount($year,$month,$productID)
    {
        
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->where('productID',$productID);
        $query = $this->db->get('pos_product_consignment_amount');
        return $query->row_array();
        
    }
    
    
    
	
	function changePurchasePrice($productID,$purchaseID,$orgPurchaseTotal,$newPurchaseTotal,$changeNum,$rowID = 0)
	{
			$err = false;
		
			if($err)echo 'test'.$orgPurchaseTotal.'   '.$newPurchaseTotal;
			if($orgPurchaseTotal == $newPurchaseTotal) 
			{
				return false; //nothing change	
				
			}
			else
			{
				
				$changeTotal = ($newPurchaseTotal - $orgPurchaseTotal);
				$chagePrice = $changeTotal/$changeNum;
				//update purchase Table
				$datain['purchaseTotal'] = $newPurchaseTotal;
 				$datain['purchasePrice'] = $newPurchaseTotal/ $changeNum;
				if($err)echo $datain['purchaseTotal'];
				$this->db->where('purchaseID',$purchaseID);
				$this->db->where('productID',$productID);
                if($rowID!=0)$this->db->where('id',$rowID);
				$this->db->update('pos_product_purchase',$datain); 
				
				
				//update product cost
				
				$product = $this->getAvgCost($productID,true);
				$nowNum = $product['num'];
				
				
				if($nowNum >= $changeNum) 
				{
					$saveNum  = $changeNum;
					$traceNum  = 0;//免追回
					//現在庫存 >= 要改變的庫存
				  	//直接攤入
					if($err)echo '//現在庫存 >= 要改變的庫';
				}
				else 
				{
					$saveNum = $nowNum;
					$traceNum = $changeNum - $saveNum;
				}
				$datain = array();
				$datain['totalCost'] = $product['totalCost'] + $chagePrice * $saveNum ; //攤提到現有庫存或 最多到所有庫存(已售出的狀態)
                if($nowNum==0)$datain['avgCost'] = 0;
				else $datain['avgCost'] = $datain['totalCost']  /$nowNum;
				$saveNum = $nowNum - $changeNum ;
				if($err)echo 'saveNum'.$saveNum;;
				$this->currentNumUpdate($productID,$nowNum ,$datain['totalCost'] ,$datain['avgCost'],0);
					$this->db->where('id',$product['id']);
					$this->db->update('pos_product_amount',$datain); 
				  
				
				
				
				if($traceNum >0)
				{
					//反之  將改變單價 加到庫存
					//然後追回賣出品 直到 庫存加賣出品 = 改變庫存
					if($err)echo '//然後追回賣出';
					$this->db->select('pos_order_shipment_detail.*');
					$this->db->where('productID',$productID);
					$this->db->where('pos_order_shipment.status >=',2); //到貨 物流  完成
					$this->db->join('pos_order_detail','pos_order_detail.id = pos_order_shipment_detail.rowID');
					$this->db->join('pos_order_shipment','pos_order_shipment.id = pos_order_shipment_detail.shipmentID');
					$this->db->order_by('pos_order_shipment.shippingTime','DESC');
					$query = $this->db->get('pos_order_shipment_detail');
					$data = $query->result_array();
					
					foreach($data as $row)
					{
						$datain = array();
						if($row['sellNum']	>$traceNum ) 
						{
							$datain['eachCost'] = $row['eachCost'] +  ($traceNum * $chagePrice)/$row['sellNum'];
							
							$traceNum = 0;
														
						}
						else 
						{
							$datain['eachCost'] = $row['eachCost'] +   $chagePrice; //每個都加

							
							$traceNum = $traceNum-$row['sellNum'];
						}
						if($datain['eachCost']<0) $datain['eachCost'] = 0;
						$this->db->where('id',$row['id']);
						$this->db->update('pos_order_shipment_detail',$datain); 
						 if($traceNum <=0) break;
						
						
					}
					
				
				
				
				}
				
				return true;
				
			}
		
		
	}
	
	
	
    function refreshProductNum($productID,$num,$shopID,$purchasePrice,$purchaseID,$purchaseTotal = 0 ,$tax = 0)
	{
		if($purchaseTotal==0) $purchaseTotal = $purchasePrice * $num;
		
		$product_purchase = array(
				'productID'  => $productID,
				'time'       => date("Y-m-d H:i:s"),
				'accountTime'=> date("Y-m-d H:i:s"),
				'num'        => $num,
				'shopID'     => $shopID,
				'purchasePrice'     => $purchasePrice,
				'purchaseTotal'     => $purchaseTotal,
				'purchaseID' =>$purchaseID
				);			
		$this->db->insert('pos_product_purchase',$product_purchase); 
		
		if($tax==1)$purchasePrice = $purchasePrice/1.05;
		
		$this->updateNum($productID,$num,$shopID,$purchasePrice);
		return ;
		
	}
	function getProductType()
	{
		$query = $this->db->get('pos_product_type');
		return $query->result_array();
	}
	function chkProductByBarcode($barcode)
	{
		$this->db->where('barcode',$barcode);
		$query = $this->db->get('pos_product');
		if($query->num_rows==0)return false;	
		return $query->row_array();		
		
	}
	function chkProductByProductID($productID)
	{
		$this->db->where('productID',$productID);
		$query = $this->db->get('pos_product');
		if($query->num_rows==0)return false;	
		return $query->row_array();		
		
	}
	
	
	
	function getProductByProductID($productID,$shopID=0)
	{
		if($shopID==0)	$distributeType=0	;
		else
		{
			$this->db->where('shopID',$shopID);
			$query = $this->db->get('pos_sub_branch');
			$data = $query->row_array();
			if(!isset($data['distributeType']))$distributeType=0;	
			else $distributeType = $data['distributeType'];	
				
		}
	
		$sql = "SELECT 
				d.productDiscount as prodctDiscount,
				e.discount as defaultDiscount,
				f.discount as distributeDiscount,
				pos_product.*,group_concat(pos_order_rule.discount ORDER BY pos_order_rule.discount DESC SEPARATOR ',') as concessions,
		        group_concat(pos_order_rule.num ORDER BY pos_order_rule.discount DESC SEPARATOR ',') as concessionsNum,pos_product_amount.num as nowNum
				FROM  pos_product
				LEFT JOIN pos_order_rule
				ON pos_order_rule.productID = pos_product.productID and pos_order_rule.distributeType=$distributeType 
				
				LEFT JOIN pos_order_rule as e ON e.productID = pos_product.productID and e.distributeType= 0
				LEFT JOIN
				(
				SELECT productID,pos_order_rule.discount as productDiscount,pos_order_distribute.discount as shopDiscount FROM
				 pos_order_rule 
				LEFT JOIN pos_order_distribute ON pos_order_distribute.id = pos_order_rule.distributeType
				 where pos_order_rule.distributeType =$distributeType and pos_order_rule.num = 0
			
		        ) AS d
				ON pos_product.productID = d.productID 
				LEFT JOIN pos_order_distribute as f ON  f.id = $distributeType 
				LEFT JOIN pos_product_amount
				ON pos_product_amount.productID = pos_product.productID and pos_product_amount.shopID= 0		 		
				 WHERE  pos_product.productID = ".$productID.
				" GROUP BY pos_order_rule.productID";
				
		
		
		$query = $this->db->query($sql);
		if($query->num_rows==0)return false;	
		else
		{
			$ret = $query->row_array();		
			if(!isset($ret['prodctDiscount']))
				{
					//預設商商品設定值空時
					if(!isset($ret['defaultDiscount'])) $ret['purchaseCount'] = $ret['distributeDiscount'];
					else 
					{
						//比較商品預設跟經銷商預設大小，
						$ret['purchaseCount'] = max( $ret['defaultDiscount'], $ret['distributeDiscount'])	;	
						
					}
					
				}
				else $ret['purchaseCount'] = $ret['prodctDiscount'];//經銷商商品設定值最優先。
				
			
				return $ret;		
		}
		
	}	
	function getProductOrderRule($productID)
	{
		
		$this->db->select('pos_order_rule.*,pos_order_distribute.distributeName');	
		$this->db->where('productID',$productID);
		$this->db->join('pos_order_distribute','pos_order_distribute.id = pos_order_rule.distributeType','left');
		$this->db->order_by('pos_order_rule.distributeType','ASC');
		$this->db->order_by('pos_order_rule.num','ASC');
		$query = $this->db->get('pos_order_rule');
		$data = $query->result_array();
		$distributeType = -1;
		$result = array();
		foreach($data as $row)
		{
			
			if($distributeType!=$row['distributeType'])
			{
				$distributeType  = $row['distributeType'];
				$result[$distributeType] =array('distributeName'=>$row['distributeName'],'distributeType'=>$row['distributeType'],'concessions'=>array())	;
				
			}
			$result[$distributeType]['concessions'][] = array('num'=>$row['num'],'discount'=>$row['discount']);
			
		}
		
		
		return $result;
	}
	
	
	
	
	
	function chkProductType($typeID)
	{
		$this->db->where('typeID',$typeID);
		$query = $this->db->get('pos_product_type');
		if($query->num_rows==0)return false;	
		return $query->row_array();		
		
	}	
	
	function getShipmentShop()
	{
		$this->db->select('id as shopID,name');
		$query = $this->db->get('shipment_shop');
		return $query->result_array();
		
	}
	function getShipmentShopByName($name)
	{
		$this->db->where('name',$name);
		$query = $this->db->get('shipment_shop');
		if($query->num_rows==0)return false;	
		return $query->row_array();	
		
	}
	function getAllProduct()
	{
		$query = $this->db->get('pos_product');
		return $query->result_array();
	}
	function getInshopProductWithoutCS($shopID)
	{
		$this->db->where('shopID',$shopID);
		$this->db->join('pos_product','pos_product.productID = pos_inshop_amount.productID','left');
		$this->db->where('cardSleeve','0');
		$query = $this->db->get('pos_inshop_amount');
		$data1 =$query->result_array();
		
		$this->db->where('shopID',$shopID);
		$this->db->join('pos_product','pos_product.productID = pos_inshop_amount.productID','left');
		$this->db->where('cardSleeve','');	
		$query = $this->db->get('pos_inshop_amount');
		$data2 = $query->result_array();
		
		
		return array_merge_recursive($data1,$data2);
		
	}
	function chkConsignment($productID,$shopID)
	{
			$time = getdate();
			
			$this->db->where('shopID',$shopID);
			$this->db->where('productID',$productID);
			$this->db->order_by('id','desc');
			//$this->db->where('month(time)',$time['mon']);
			//$this->db->where('year(time)',$time['year']);
			$query = $this->db->get('pos_consignment_amount');
			if($query->num_rows>=1)
			{
				 $r = $query->row_array();
				// echo 's'.substr($r['time'],0,8).','.$time['year'].'-'.str_pad($time['mon'],2,'0',STR_PAD_LEFT);
				 if(substr($r['time'],0,7)==(string)$time['year'].'-'.str_pad($time['mon'],2,'0',STR_PAD_LEFT))
				 {
					//echo 'OK';
					 return true;
				 }
				 
			}
			 return false;
			
	}
    
    function updateProductConsignmentNum($productID,$purchasePrice,$supplierID)
	{
		
		$time = getdate();
		
		$this->db->where('productID',$productID);
	
		$query = $this->db->get('pos_product_consignment');
		if($query->num_rows()>=1)
		{
			$data = $query->row_array();
			$datain['purchasePrice'] = $data['purchasePrice'] ;
			$this->db->where('id',$data['id']);
			$this->db->update('pos_product_consignment',$datain); 
            
          
		}
		else
		{
				$datain =array(
				'productID'=>$productID,
				'purchasePrice'=>$purchasePrice,
				'startConsignment'=>date('Y-m-d'),
                'supplierID'   =>$supplierID
				
			);
			
			
			$this->db->insert('pos_product_consignment',$datain);	
			
		}
        $this->db->where('productID',$productID);
        $this->db->update('pos_product',array('Sconsignment'=>1)); 
	
	}
	function getInshopData($shopID,$year,$month)
	{
		
		$month = ($month+1)%12;
		if($month==0) $month=12;
		if($month==1) $year++;
		$time  = $year.'-'.$month.'-01';
	
		$sql = "SELECT * FROM (pos_inshop_amount) 
		LEFT JOIN pos_product ON pos_product.productID = pos_inshop_amount.productID 
		LEFT JOIN pos_inshop_sell ON pos_inshop_sell.rentID = pos_inshop_amount.rentID  and pos_inshop_amount.shopID=pos_inshop_sell.shopID 
		WHERE (`pos_inshop_amount`.`shopID` = '".$shopID."' AND `pos_inshop_amount`.`time` <= '".$time."' )
		AND (`pos_inshop_sell`.`time` > '".$time."'  or `pos_inshop_sell`.`time` is NULL )ORDER BY pos_product.productNum ASC";
		
		
		$query = $this->db->query($sql);
		return $query->result_array();

	}
    
        
        
	function getNewInshop($shopID,$year,$month)
	{
		
		$this->db->where('shopID',$shopID);
		$this->db->where('year(time)',$year,false);
		$this->db->where('month(time)',$month,false);
		$this->db->join('pos_product','pos_product.productID = pos_inshop_amount.productID','left');
		$query = $this->db->get('pos_inshop_amount');
		return $query->result_array();
		
		
	}
	function getProductOut($shopID,$from,$to)
	{
		$sql = "SELECT * FROM pos_order_shipment
				WHERE  pos_order_shipment.shippingTime >='$from' and pos_order_shipment.shippingTime<='$to' 
				and pos_order_shipment.type=0 and (pos_order_shipment.status=2 || pos_order_shipment.status=3)";
		$query = $this->db->query($sql);	
		return $query->result_array();	
		
	}
	function getProductIN($shopID,$from,$to)
	{
		$sql = "SELECT  pos_product.productNum,pos_product.ZHName,pos_product.ENGName,pos_product.language,pos_product_purchase.*
				FROM pos_product_purchase
				LEFT JOIN pos_product on pos_product_purchase.productID = pos_product.productID
				WHERE shopID = $shopID and pos_product_purchase.time >='$from' and pos_product_purchase.time<='$to'";
		$query = $this->db->query($sql);	
		return $query->result_array();	
	}
	function getProductBACK($shopID,$from,$to)
	{
		$sql = "SELECT  pos_sub_branch.name, pos_product.productNum,pos_product.ZHName,pos_product.ENGName,pos_product.language,pos_order_back.*,pos_order_back_detail.*
				FROM pos_order_back
				LEFT JOIN pos_order_back_detail ON pos_order_back_detail.backID = pos_order_back.id
				LEFT JOIN pos_sub_branch ON pos_sub_branch.shopID = pos_order_back.shopID
				LEFT JOIN pos_product on pos_order_back_detail.productID = pos_product.productID
				WHERE pos_order_back.status >=4 and backTime >='$from' and backTime<='$to'";			
		$query = $this->db->query($sql);	
		return $query->result_array();	
	}
	function getProductAdjust($shopID,$from,$to)
	{
		$sql = "SELECT pos_sub_branch.name, pos_product.productNum,pos_product.ZHName,pos_product.ENGName,pos_product.language,pos_order_adjust.*,pos_order_adjust_detail.*
				FROM pos_order_adjust
				LEFT JOIN pos_order_adjust_detail ON pos_order_adjust_detail.adjustID = pos_order_adjust.id
				LEFT JOIN pos_sub_branch ON pos_sub_branch.shopID = pos_order_adjust.fromShopID
				LEFT JOIN pos_product on pos_order_adjust_detail.productID = pos_product.productID
				WHERE time >='$from' and time<='$to'";			
		$query = $this->db->query($sql);	
		return $query->result_array();	
	}
	
    function getSupplierPurchase($supplierID,$from,$to)
    {
        $this->db->where('supplierID',$supplierID);
        $this->db->where('timeStamp >=',$from);
        $this->db->where('timeStamp <=',$to);
        
        
        $query = $this->db->get('pos_product_purchase_inf');
        
        $data = $query->result_array();
        $total = 0;
        foreach($data as $row)
        {
            if($row['tax']!=0) $r = 1.05;
            else $r = 1;
                $total += $row['total'] * $r ;
                
            
            
        }
        return $total;
        
        
    }
    function getAllSConsignment($supplierID)
    {
       if($supplierID!=0) $this->db->where('pos_product_consignment.supplierID',$supplierID);
      
        $this->db->join('pos_product','pos_product_consignment.productID = pos_product.productID','left');
        $this->db->where('Sconsignment',1);
        $query = $this->db->get('pos_product_consignment');
        $product = $query->result_array();
       
        return  $product;
            
        
    }
    
	
    function setConsignment($productID,$year,$month,$num,$purchasePrice)
    {
        
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->where('productID',$productID);
        
        $query = $this->db->get('pos_product_consignment_amount');
        $datain = array('productID' => $productID,
                        'year' =>$year,
                        'month'=>$month,
                        'num' => $num,
                        'purchasePrice'=>$purchasePrice,
                        'timeStamp' =>date('Y-m-d H:i:s')
                       
                       
                       
                       );
        
         if( $query->num_rows()>0)
         {
                $this->db->where('year',$year);
                $this->db->where('month',$month);
                $this->db->where('productID',$productID);
                $this->db->update('pos_product_consignment_amount',$datain);

         }
        else
        {
             $this->db->insert('pos_product_consignment_amount',$datain);
      



        }
    
    }
    
    
	function getProductIO($shopID,$offset,$num,$query,$from,$to,$shopQuery,$shopGroup)
	{
		//,pos_product.ZHName,pos_product.ENGName,pos_product.language
	   if(strpos($query,'_')>0)
       {
           $p = $this->getProductByProductNum($query);
            $query = $p['productID'];
       }
        
        
        
		$sql = "
				SELECT pos_product.productNum,pos_product.ZHName,pos_product.ENGName,pos_product.suppliers,pos_product.language,pos_product.buyPrice,a.shippingNum,a.shipmentID,purchasePrice ";
		if($shopGroup==1)$sql .= ",sum(inNum) as inNum,sum(outNum) as outNum,sum(backNum) as backNum,toWhere,shopID,a.productID,a.time,a.type,avg(a.sellPrice) as sellPrice ,a.receiver"	;	
		else $sql .=" ,a.* "		;
		$sql.=	" from (
				SELECT 0 as shippingNum,0 as shipmentID,pos_product_purchase.id,pos_product_purchase.num as inNum ,0 as outNum,0 as backNum,'' as  toWhere,shopID,pos_product_purchase.productID,pos_product_purchase.time,-1 as type,0 as sellPrice,'' as  receiver,purchasePrice
				FROM pos_product_purchase
				WHERE shopID = $shopID and pos_product_purchase.time >='$from' and pos_product_purchase.time<='$to'";
                if(is_numeric($query)) $sql.=" and productID = ".$query;
        
                
                $sql.=" UNION 
				SELECT pos_order_shipment.shippingNum,pos_order_shipment.id as shipmentID,pos_order_shipment_detail.id,0 as inNum , pos_order_shipment_detail.sellNum as outNum,0 as backNum,pos_sub_branch.name as toWhere,pos_sub_branch.shopID,pos_order_shipment_detail.sproductID as productID,pos_order_shipment.shippingTime as time,pos_order_shipment.type as type,pos_order_shipment_detail.sellPrice as sellPrice,pos_order_address.receiver,0 as purchasePrice
				FROM pos_order_shipment
				LEFT JOIN pos_order_shipment_detail ON pos_order_shipment_detail.shipmentID = pos_order_shipment.id
				
				LEFT JOIN pos_sub_branch ON pos_sub_branch.shopID = pos_order_shipment.shopID
				LEFT JOIN pos_order_address ON pos_order_address.id = pos_order_shipment.addressID
				WHERE pos_order_shipment.status>=2  and pos_order_shipment.type <2 and shippingtime >='$from' and shippingtime<='$to'";
                if(is_numeric($query)) $sql.=" and sproductID = ".$query;
        
        
        
        
                $sql.=" UNION 
				SELECT 0 as shippingNum,0 as shipmentID,pos_order_back_detail.id,0 as inNum , 0 as outNum,pos_order_back_detail.num as backNum,pos_sub_branch.name as toWhere,pos_sub_branch.shopID,pos_order_back_detail.productID,pos_order_back.backTime as time,-1 as type,0 as sellPrice,'' as  receiver,0 as purchasePrice
				FROM pos_order_back
				LEFT JOIN pos_order_back_detail ON pos_order_back_detail.backID = pos_order_back.id
				LEFT JOIN pos_sub_branch ON pos_sub_branch.shopID = pos_order_back.shopID
				WHERE pos_order_back.status >=4 and backTime >='$from' and backTime<='$to'";
                if(is_numeric($query)) $sql.=" and productID = ".$query;
                $sql.="	) as a
				LEFT JOIN pos_product on a.productID = pos_product.productID";
                if(is_numeric($query)) $sql.=" where pos_product.productID = ".$query; 
				else $sql.=" WHERE ZHName like '$query'  ";
				if(is_numeric($shopQuery))$sql.=" and  shopID = ".$shopQuery ;
                else if ( $shopQuery!='')$sql.=" and toWhere like '%$shopQuery%'" ;
				if($shopGroup==1) $sql.=" group by toWhere , a.productID , a.type ";
			
				$sql.=" ORDER BY time DESC limit $offset,$num";
	            $this->db->insert('pos_test',array('content'=>$sql));
$this->db->insert('pos_test',array('content'=>$sql));
		$query = $this->db->query($sql);	
		return $query->result_array();
	}

	function getSanguosha($userID,$sanguoshaID)
	{
		$this->db->where('userID',$userID);	
		$this->db->or_where('sanguoshaID',$sanguoshaID);	
		$this->db->order_by('shopID','ASC');
		$query = $this->db->get('pos_sanguosha');
		if($query->num_rows()>0) return $query->result_array();
		else return false;	
		
	}
	function getPurchaseToday()
	{
		$time = getdate();
		$date = date("Y-m-d H:i:s", mktime($time['hours'], $time['minutes'], $time['seconds'], $time['mon'], $time['mday']-1, $time['year']));	
		$this->db->where('pos_product_purchase.time >=',$date);
		$this->db->where('shopID ',0);
        
        $this->db->join('pos_product_num_check','pos_product_num_check.purchaseID=pos_product_purchase.purchaseID and pos_product_num_check.productID=pos_product_purchase.productID','left');
        
		$query = $this->db->get('pos_product_purchase');
		return $query->result_array();
	}
	
	function getAvgCost($productID,$detail = false)
	{
		$this->db->where('productID',$productID);
        
		$this->db->where('shopID',0);
		$this->db->order_by('year','DESC');
		$this->db->order_by('month','DESC');
		$this->db->limit(1,0);
		$query = $this->db->get('pos_product_amount');
		$data =  $query->row_array();
		if($detail==false) 	return $data['avgCost'];
		else return $data;
		
	}
	function chkPackage($boxProductID)
	{
		$this->db->where('boxProductID',$boxProductID);
			$query = $this->db->get('pos_package');
		if($query->num_rows()>0) return $query->result_array();
		else return false;	
	
	}
	function chkConsume($productID)
	{
		$this->db->where('productID',$productID);
			$query = $this->db->get('pos_consumables');
		if($query->num_rows()>0) return $query->result_array();
		else return false;	
	
	}
	function getConsume()
	{

		$this->db->select('pos_product.*');

		$this->db->join('pos_product','pos_product.productID = pos_consumables.productID','left');
		$query = $this->db->get('pos_consumables');
		if($query->num_rows==0)return false;	
		else
		{
			return $data = $query->result_array();	
		
		}	
		
		
	}
	function getProductPuchasedNum()
	{
		$this->db->where('productID !=','');
		$this->db->group_by('productID');
		$q = $this->db->get('pos_product_amount');	
		return  $q->result_array();
	}
	
	
	function getPackage()
	{

		$this->db->select('a.ZHName as boxZHName,a.ENGName as boxENGName,a.language as boxLanguage,b.ZHName as unitZHName,b.ENGName as unitENGName,b.language as unitLanguage,pos_package.*');

		$this->db->join('pos_product as a','pos_package.boxProductID = a.productID','left');
		$this->db->join('pos_product as b','pos_package.unitProductID = b.productID','left');
		$query = $this->db->get('pos_package');
		if($query->num_rows==0)return false;	
		else
		{
			return $data = $query->result_array();	
		
		}	
		
		
	}
	
	function getLastPrice($productID,$shopID)
	{
		$this->db->where('productID',$productID);
		$this->db->where('shopID',$shopID);
		$this->db->order_by('id','desc');
		$this->db->limit(1);
		$query = $this->db->get('pos_product_purchase');
		$r = $query->row_array();
		if(isset($r['purchasePrice']))	return $r['purchasePrice'];
		else return 0 ;
	}
	
	function getSupplierInf($supplierID)
	{
		
		$this->db->where('supplierID',$supplierID);
		
		$query = $this->db->get('pos_suppliers');
		return $query->row_array();
		
	}
	
	
	function sameProductTrans($productID)
	{
		$this->db->select('b.*');
		$this->db->where('a.productID',$productID);
		$this->db->where('b.productID !=',$productID);
		$this->db->join('pos_same_product as b ','a.index = b.index','left');
		$query = $this->db->get('pos_same_product as a');
		$data = $query->result_array();
		if(empty($data)) return;
		foreach($data as $row)
		{
			$this->db->where('productID',$row['productID']);
			$this->db->where('sellNum',0);
			$this->db->where('status',0);
			$this->db->update('pos_order_detail',array('productID'=>$productID));
			
			
		}
		
	}
    
    function findSameProduct($productID)
    {
        $this->db->where('productID',$productID);
        $query = $this->db->get('pos_same_product');
        $data = $query->row_array();
         if(empty($data)) return;
        else
        {
            $this->db->where('productID !=',$productID); 
             $this->db->where('index',$data['index']);
            $query = $this->db->get('pos_same_product');
             $data = $query->result_array();
            return $data;
        }
        
        
    }
    
    
	
	function getProductIDBycheckID($checkID,$shopID)
	{
		$this->db->select('*,pos_product_sell.num as sellNum');
		$this->db->where('checkID',$checkID);
		$this->db->where('shopID ',$shopID);
		
		$this->db->join('pos_product','pos_product.productID = pos_product_sell.productID','left');

			$query = $this->db->get('pos_product_sell');
		return $query->result_array();
		
	}
	function getSellTogetherCheckID($productID,$shopID=0)
	{
		
		$this->db->where('productID',$productID);
		$this->db->where('checkID !=',0);
		if($shopID!=0)$this->db->where('shopID',$shopID);
		$this->db->group_by('checkID');
		$query = $this->db->get('pos_product_sell');
		return $query->result_array();
		
	}

	
	//**************
// 身份證檢查
//**************
function checkNick($id){

		//建立字母分數陣列
		$head = array('A'=>1,'I'=>39,'O'=>48,'B'=>10,'C'=>19,'D'=>28,
					  'E'=>37,'F'=>46,'G'=>55,'H'=>64,'J'=>73,'K'=>82,
					  'L'=>2,'M'=>11,'N'=>20,'P'=>29,'Q'=>38,'R'=>47,
					  'S'=>56,'T'=>65,'U'=>74,'V'=>83,'W'=>21,'X'=>3,
					  'Y'=>12,'Z'=>30);
		//建立加權基數陣列
		$multiply = array(8,7,6,5,4,3,2,1);
		
		//檢查身份字格式是否正確
		if (ereg("^[a-zA-Z][1-2][0-9]+$",$id) && strlen($id) == 10){
			//切開字串
			$len = strlen($id);
			for($i=0; $i<$len; $i++){
				$stringArray[$i] = substr($id,$i,1);
			}        
			//取得字母分數
			$total = $head[array_shift($stringArray)];
			//取得比對碼
			$point = array_pop($stringArray);
			//取得數字分數
			$len = count($stringArray);
			for($j=0; $j<$len; $j++){
				$total += $stringArray[$j]*$multiply[$j];
			}
			//檢查比對碼
			if (($total%10 == 0 )?0:10-$total%10 != $point) {
				return false;
			} else {
				return true;
			} 
		}  else {
		   return false;
    }
}
	
	
	function getPhaLCSell($year,$month)
	{
		$this->db->like('productNum','LC');
		$query  = $this->db->get('pos_product');
		$product = $query->result_array();
		
		$sell =      array();
		foreach($product as $row)
		{
			$this->db->where('productID',$row['productID']);
			if($year!=0 &&$year!='')$this->db->where('year(time)',$year,false);
			if($month!=0 &&$month!='')$this->db->where('month(time)',$month,false);
			$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_product_sell.shopID','left');
			$query = $this->db->get('pos_product_sell');
			$shopData = $query->result_array();
			
           
			foreach($shopData as $each)
			{
				if(!isset($sell[$each['shopID']][$row['productID']]))	
				{
					$sell[$each['shopID']][$row['productID']] = 0;
					$sell[$each['shopID']]['shopName'] = $each['name'];
				}
				$sell[$each['shopID']][$row['productID']] += $each['num'];
				
			}
		}
		//[shopID][productID]
		$result['product'] = $product;
 
		$result['sell'] = $sell;
		
		return $result;
		
	}
	
	function getLCShip($year,$month)
	{
		$this->db->like('productNum','LC');
		$query  = $this->db->get('pos_product');
		$product = $query->result_array();
		
		$sell = array();
		foreach($product as $row)
		{
			$this->db->where('productID',$row['productID']);
			if($year!=0 &&$year!='')$this->db->where('year(shippingTime)',$year,false);
			if($month!=0 &&$month!='')$this->db->where('month(shippingTime)',$month,false);
			$this->db->join('pos_order_detail','pos_order_detail.id = pos_order_shipment_detail.rowID','left');
			$this->db->join('pos_order_shipment','pos_order_shipment.id = pos_order_shipment_detail.shipmentID','left');
			$this->db->where('pos_order_shipment.status >=',2);
			$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_order_shipment.shopID','left');
			$query = $this->db->get('pos_order_shipment_detail');
			$shopData = $query->result_array();
			
			foreach($shopData as $each)
			{
				if(!isset($sell[$each['shopID']][$row['productID']]))	
				{
					$sell[$each['shopID']][$row['productID']] = 0;
					$sell[$each['shopID']]['shopName'] = $each['name'];
				}
				$sell[$each['shopID']][$row['productID']] += $each['sellNum'];
				
			}
		}
		//[shopID][productID]
		$result['product'] = $product;
		$result['sell'] = $sell;
		
		return $result;
		
	}
	
	function getProductByTime($d)
	{
		$this->db->where('preTime',$d);
		$this->db->join('pos_product','pos_product.productID = pos_product_preTime.productID','left');
		$this->db->join('pos_suppliers','pos_suppliers.supplierID = pos_product.suppliers','left');
		$this->db->order_by('pos_suppliers.supplierID');
		$query = $this->db->get('pos_product_preTime');
		return $query->result_array();
		
		
		
		
	}
    
    function getCurrentAllProductStock($shopID,$offset,$num)
    {
        
        
        $this->db->where('shopID',$shopID);
        $this->db->join('pos_product','pos_product.productID = pos_current_product_amount.productID');
        $this->db->limit($num,$offset);
        $query=$this->db->get('pos_current_product_amount');
        
        return $query->result_array();
        
        
    }
    
    function makeBidLink($phaBid,$productID)
    {
        $data['product']= $this->getProductByProductID($productID);
        	$ret = $this->paser->post('http://www.phantasia.tw/bg/chk_bg',array('bid'=>$phaBid),true);
			
			if($ret['result']==false)
				{
					$bidExist = 0 ; 
			
					
				}
				else
				{ 
					$this->db->where('productID',$productID);
					$this->db->update('pos_product',array('phaBid'=>$phaBid,'bidExist'=>1));
					$data = $this->paser->post('http://www.phantasia.tw/bg_controller/update_price',array('bid'=>$phaBid,'price'=>$data['product']['price'],'category'=>$data['category']),false);	
					
				}
        
        return $data['product'];
        
    }
    function same_product_check($productID)
    {
        
            $p = array(8881843=>8882780,
                8882633=>8882781,
                8882457=>8882782,
                8883177=>317,
                8883691=>8881436,
                53=> 8880199);
        if(isset($p[$productID])) return $p[$productID];
        else return $productID;
        
        
        
        
    }
    
    function getProductCheck($purchaseID)
    {
        
        $this->db->where('purchaseID',$purchaseID);
        $this->db->join('pos_product','pos_product.productID = pos_product_num_check.productID','left');
        $this->db->order_by('cabinet','ASC');
        $query = $this->db->get('pos_product_num_check');
        return $query->result_array();
                 
    }
    
}

?>