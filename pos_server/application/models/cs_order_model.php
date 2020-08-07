<?php 
class Cs_order_model extends Model {
	function Cs_order_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function getCsOrderList($shopID,$finish = 0)
	{
        
        
		if($finish==0) 
        {
            $where = "shopID=".$shopID." AND( cargoStatus=0 OR cashStatus=0 )";
                
        }
        else   $where = "shopID=".$shopID." AND( cargoStatus!=0 AND cashStatus!=0 )";
          $this->db->where('deleteToken',0);
		$this->db->where($where,NULL,false);
		$query = $this->db->get('pos_cs_order');
		return $query->result_array();
	}
    function getCsOrder($csOrderID)
	{
		
		$this->db->where('csOrderID',$csOrderID);
		$query = $this->db->get('pos_cs_order');
		return $query->row_array();
	}
     function getCsOrderDetail($csOrderID,$shopID,$stockCount = false)
	{
		
		$this->db->where('csOrderID',$csOrderID);
         $this->db->join('pos_product','pos_cs_order_detail.productID = pos_product.productID','left');
		$query = $this->db->get('pos_cs_order_detail');
		$data =  $query->result_array();
         $r = array();

         if(count($data)>0)
         foreach($data as $row)
         {
             //優惠券
             if($row['productID'] ==8884457)  $row['stockNum'] =100;
             else $row['stockNum'] = $this->getCurrentNum($shopID,$row['productID']);
             if($stockCount==true)
             {
                 
                      //扣掉預定商品
                $row['stockNum'] -= $this->PO_model->getCsorderNum($shopID,$row['productID']);
                 
             }
             $row['ENGName'] = str_replace("'","",$row['ENGName']);
             
             $r[] = $row;
         }
         return $r;
        
         
	}
	function getReadyCsorderList($shopID)
    {
        
        
        $orderList = $this->getCsOrderList($shopID, 0);
        $result = array();
              
            foreach($orderList as $row)
            {
                $p = $this->getCsOrderDetail($row['csOrderID'],$shopID);
                $row['ready'] = 1;       
                foreach($p as $each)
                {
                    
                
                    if(!isset( $product[$each['productID']] ))
                    {
                      $product[$each['productID']] =  $each['stockNum'];
                    }
                    
                        if($each['num']-$product[$each['productID']]<=0)
                        {
                           $product[$each['productID']]-=$each['num'];     
                        }
                        else $row['ready'] = 0;
                    
                 
                }
                  
                   if($row['ready'] == 1)  
                   {
                        $row['detail']  = $p;
                       $result[] = $row;
                   }
                      
                    
            
            }
        
        return $result;
            
        
        
    }
    function getSendoutCsorderList($shopID)
    {
        
        
        $orderList = $this->getCsOrderList($shopID, 0);
        $result = array();
              
            foreach($orderList as $row)
            {
                
                
                if($row['emailNotify'] !='0000-00-00 00:00:00' || $row['phoneNotify'] !='0000-00-00 00:00:00')
                   {
                       
                         $p = $this->getCsOrderDetail($row['csOrderID'],$shopID);
                       
                         $row['detail']  = $p;
                        $result[] = $row;
                       
                   }            
                           
            }
        
        return $result;
            
        
        
    }
    
    function csOrderReadyNotify($shopID)
    {
      $r =  $this->getReadyCsorderList($shopID);
        if(count($r)>0)
        {
            
            
            $this->db->insert('pos_announce',array('autoBtn'=>1,'time'=>date('Y-m-d H:i:s'),'title'=>'貨齊通知'));
            $id = $this->db->insert_id();
            
            
            
            $content = '<div style="width:900px;height:500px"><iframe border="1" width="900" height="500" src="/system/iframe_connect?a=b%20&url=http://shipment.phantasia.com.tw/csorder/orderlist_ready/"></iframe></div>';
            
            $this->db->where('announceID',$id);
            $this->db->update('pos_announce',array('content'=>$content));
            
            $this->db->insert('pos_announce_check',array('shopID'=>$shopID,'announceID'=>$id,'announceTime'=>date('Y-m-d H:i:s')));
            
            
            
            
            
        }
        
        
        
        
        
    }
    
    
    
    
    function getCurrentNum($shopID,$productID)
    {
        
        $this->db->where('shopID',$shopID);
        $this->db->where('productID',$productID);
        $this->db->order_by('timeStamp','desc');
     
        
        $query = $this->db->get('pos_current_product_amount');
		$d = $query->row_array();
		if(!isset($d['num'])) return 0;
        return $d['num'];
        
    }
    
    function updateCsOrderDetail($cancelNum, $csOrderID, $productID, $newdata)
    {
	    $this->db->where('csOrderID',$csOrderID);
		$this->db->where('productID',$productID);
	   	if($cancelNum == 0){
		    $this->db->update('pos_cs_order_detail', $newdata); 
	    }
	    else{
	    	$this->db->delete('pos_cs_order_detail'); 
	    }
    }
    function checkExist($productID,$csOrderID){
    	$this->db->where('csOrderID',$csOrderID);
		$this->db->where('productID',$productID);
		$query = $this->db->get('pos_cs_order_detail');
		$num = $query->num_rows();
		if($num>0)return true;
		else return false;
    }
    function insertCsOrderDetail($cancelNum,$newdata)
    {
    	if($cancelNum==0) $this->db->insert('pos_cs_order_detail', $newdata); 
    }
    function calcuteTotal($csOrderID)
    {
    	$total = 0;
    	$this->db->where('csOrderID',$csOrderID);
    	$query = $this->db->get('pos_cs_order_detail');
    	foreach ($query->result_array() as $data) {
    		$total+=$data['num']*$data['sellPrice'];
    	}
    	return $total;
    }
    function updateCsOrder($newdata,$csOrderID)
    {
    	$this->db->where('csOrderID',$csOrderID);
    	$this->db->update('pos_cs_order', $newdata); 
    }
    function createCsOrder($shopID,$source){
    
    	for($i=1;;$i++){
    		$csOrderNumString = $shopID.date('Ymd').sprintf("%03d",$i);
    		$this->db->where('csOrderNum',$csOrderNumString);
			$query = $this->db->get('pos_cs_order');
			if($query->num_rows()==0) break;
    	}
    	$newdata['csOrderNum'] = $csOrderNumString;
    	$newdata['source'] = $source;
    	$newdata['orderTime'] = date('Y-m-d H:i:s');
        $newdata['outDate'] = date('Y-m-d');
    	$newdata['shopID'] = $shopID;
     	$this->db->insert('pos_cs_order', $newdata); 
     	return $this->db->insert_id();
    }
    function getConditionCsOrderList($shopID, $cashStatus, $cargoStatus,$source,$start = 0,$num = 10,$keyword = '0'){
    	
        $sql = "select * from pos_cs_order";
        
        $sql .= " where shopID =".$shopID;
      
    	if($source!=-1) 
        {
            if($source==-2)  $sql .= " and source !=1"; //非商城
            else $sql .= " and source =".$source; 
        }
        $sql .= " and deleteToken =0";
       
    	if($cargoStatus!=-1) $sql .= " and cargoStatus =".$cargoStatus; 
        if($cashStatus!=-1) $sql .= " and cashStatus =".$cashStatus;
        if($keyword!='0' && !empty($keyword))
        {
         $sql.=" and (name like '%".$keyword."%' ".
               " or comment like '%".$keyword."%' ".
               " or phone like '%".$keyword."%' ".
               " or email like '%".$keyword."%' ".    
               " or csOrderNum like '%".$keyword."%')";
          
        }
        $sql.=" order by orderTime DESC";
        $sql.=" limit ".$start.','.$num;
    
         
    
    	$query = $this->db->query($sql);
    	return $query->result_array();
    }
    function getShopID($csOrderID){
    	$this->db->select('shopID');
    	$this->db->where('csOrderID',$csOrderID);
		$query = $this->db->get('pos_cs_order');
		return $query->row_array();
    }
    function updateCsOrderStatus($csOrderID, $cargoStatus)
    {
    	$newdata['cashStatus'] = 1;
    	$newdata['cashInTime'] = date('Y-m-d H:i:s');
    	if($cargoStatus==1){
    		$newdata['cargoStatus'] = $cargoStatus;
    		$newdata['cargoTime'] = date('Y-m-d H:i:s');
    	}
    	$this->db->where('csOrderID',$csOrderID);
    	$this->db->update('pos_cs_order', $newdata); 

    	$this->db->where('csOrderID',$csOrderID);
		$query = $this->db->get('pos_cs_order');
		return $query->result_array();
    }
    
    function getCheckDetail($checkID,$shopID)
    {
        
        $this->db->where('checkID',$checkID);
        $this->db->where('shopID',$shopID);
        $this->db->join('pos_product','pos_product.productID = pos_product_sell.productID');
        $query = $this->db->get('pos_product_sell');
        
        return $query->result_array();
        
    }
    
    function getOrderIDOnMart($csOrderID)
    {
        
        $this->db->where('csOrderID',$csOrderID);
        $query = $this->db->get('pos_weborder_link');
        $data = $query->row_array();
        if(!empty($data)) return $data['orderID'];
        else return 0;
        
        
        
    }
    function newWebOrder($orderID,$date)
    {
        
        $this->db->where('orderID',$orderID);
        $query = $this->db->get('pos_weborder_link');
        if($query->num_rows()<=0)
        {
            
            $this->db->insert('pos_weborder_link',array('orderID'=>$orderID,'time'=>$date));
            return true;
        }
        return false;
    }
    
    
    function mallDataTransfer()
    {
        $this->db->where('datatransfer',0);
        $this->db->limit(10);
        $query = $this->db->get('pos_weborder_link');
        $data = $query->result_array();
      

        foreach($data as $row)
        {
            
            
            $r = $this->paser->post($this->data['martadmDomain'].'receipt/detail?orderid='.$row['orderID'].'&type=json',array() ,true);
   
        
          
            if($r['personlist']['transportID']!=1)$shopID = 666;
            else $shopID = $r['personlist']['shopnumber'];
           
            $source = 1 ; 
            
            
            
            
            $postData['shopID'] = $shopID;
            $emptyProductID = false;
            $errproduct = '';
            $num = count($r['personlist']['detail']['gname'] );
            $product =array();
            $postData = array();
            for($i=0;$i<$num;$i++)
            {
                
                if($r['personlist']['detail']['productID'][$i]==-1)
                {
                    
                    $boundle = $this->getBoundle($r['personlist']['detail']['pID'][$i]);
                    $t = $r['personlist']['detail']['price'][$i] ;
                  //  $this->db->insert('pos_test',array('content'=>json_encode($boundle)));
                    foreach($boundle as $p)
                    {
                        $postData['cancel'][] = 0 ;
                        $postData['productID'][] = $p['productID'];
                        $postData['num'][] = $p['num']*$r['personlist']['detail']['num'][$i] ;
                       
                        $op = round($t/$p['num']);
                        $postData['sellPrice'][] = $op;
                         $t-=$op*$p['num'];
                        
                        if($t<0)
                        {
                              $postData['productID'][] = 8884457;//csorder 334行也有用到
                              $postData['cancel'][] = 0 ;
                              $postData['num'][] = 1;
                              $postData['sellPrice'][] =$t;
                              $t = 0 ;
                            
                        }
                        
                    }
                     // $this->db->insert('pos_test',array('content'=>json_encode($postData)));
                     
                    
                }
                else
                {
                    
                
                if( $r['personlist']['detail']['productID'][$i]==0 ||empty( $r['personlist']['detail']['productID'][$i]))
                {
                    $emptyProductID = true;
                    $errproduct .=$r['personlist']['detail']['gname'][$i];
                }
                
                $postData['cancel'][] = 0 ;          
                $postData['productID'][] = $r['personlist']['detail']['productID'][$i];
                $postData['num'][] = $r['personlist']['detail']['num'][$i];
                $postData['sellPrice'][] = $r['personlist']['detail']['price'][$i];
                    
                    
                    
                }


                $product[] = array(
                    'num'=>$r['personlist']['detail']['num'][$i],
                    'pID'=> $r['personlist']['detail']['productID'][$i],
                    'ZHName'=>$r['personlist']['detail']['gname'][$i],
                    'price'=>$r['personlist']['detail']['price'][$i],
                    'totalPrice'=>$r['personlist']['detail']['price'][$i] * $r['personlist']['detail']['num'][$i]
                );
                
            }
        
            //有無運費
            if($r['personlist']['fare'] > 0)
             {
                     $product[] = array(
                    'num'=>1,
                    'pID'=> 8881437,// 商城優惠折扣
                    'ZHName'=>'運費',
                    'price'=>$r['personlist']['fare'],
                    'totalPrice'=>$r['personlist']['fare'] 
                );
                
                 $postData['productID'][] = 8881437;
                $postData['cancel'][] = 0 ;
               
                $postData['num'][] = 1;
                $postData['sellPrice'][] =$r['personlist']['fare'] ;
                   
                   
                   
                   
                   
            }
            
            
            
            
            
            //有無優惠券
            if($r['personlist']['discount'] > 0)
            {
                
                 $product[] = array(
                    'num'=>1,
                    'pID'=> 8884457,// 商城優惠折扣
                    'ZHName'=>$r['personlist']['cname'],
                    'price'=>-$r['personlist']['discount'] ,
                    'totalPrice'=>-$r['personlist']['discount']
                );
                
                 $postData['productID'][] = 8884457;//csorder 334行也有用到
                  
                $postData['cancel'][] = 0 ;
               
                $postData['num'][] = 1;
                $postData['sellPrice'][] =-$r['personlist']['discount'];
            }
            
            
            
            
            
            
            
            
            if($emptyProductID ==true)
            {
                
                //wrong product
                $shop['email'].='lintaitin@gmail.com,phantasia.ec@gmail.com,phantasia.it@gmail.com';
                $title = '［重大錯誤］商城購物訂單錯誤！！'.date('Y-m-d H:i:s');

                $content='<h2>您好：</h2>'.
                          '<p>瘋桌遊商城產品與系統連結失敗。<br/>'.					
                          '以下產品錯誤，請立刻修復<br>'.
                          $errproduct.'<br/>'.	
                            '<a href="http://shipment.phantasia.com.tw/order/mallProductMerge">http://shipment.phantasia.com.tw/order/mallProductMerge</a>'.
                            '祝您 事事順心。<br/>'.
                            '(此信由電腦系統直接發出，請上系統查詢)';

                $this->Mail_model->myEmail( $shop['email'],$title,$content); 
                
            }
            else
            {
            $postData['csOrderID']  = $this->cs_order_model->createCsOrder($shopID,$source);
       //     $postData['cargoStatus'] = $data['shipStatus'];
		    $postData['usage'] =1;
	
            $postData['comment'] = '商城訂單編號'.$row['orderID'].$r['personlist']['remark'];
            $postData['memberID'] =  $r['personlist']['memberID'];
            $postData['email'] = $r['personlist']['email'];
            $postData['phone'] = $r['personlist']['phone'];
            $postData['name'] = $r['personlist']['name'];
            $postData['name'] = $r['personlist']['name'];
            $postData['title'] = '';
            $postData['IDNumber'] = $r['personlist']['vatnum'];
            $postData['discount'] = 100;
            $postData['outDate'] = '';

             $this->paser->post($this->data['serverDomain'].'csorder/order_update',$postData,true);


    // $this->db->insert('pos_test',array('content'=>$this->data['serverDomain'].'csorder/order_update send :csorder/order_update :'.$r));
                

                
                if($shopID !=666)//非電子商務出貨電腦
                {
					$this->db->insert('pos_announce',array('autoBtn'=>0,'time'=>date('Y-m-d H:i:s'),'title'=>'商城訂單'.$row['orderID']));
					$id = $this->db->insert_id();

					
                    $content = '<div style="width:900px;height:500px"><iframe border="1" width="900" height="500" src="/system/iframe_connect?a=b%20&url=http://shipment.phantasia.com.tw/csorder/weborder/'.$postData['csOrderID'] .'/'.$id.'"></iframe></div>';

                    $this->db->where('announceID',$id);
                    $this->db->update('pos_announce',array('content'=>$content));

                    $this->db->insert('pos_announce_check',array('shopID'=>$shopID,'announceID'=>$id,'announceTime'=>date('Y-m-d H:i:s')));
					
					
                    //更新notifiaction
                    $this->paser->post($this->data['posDomain'].'system/delete_notifaction',array('shopID'=>$shopID),false);
                  }
				else
				{
                    
                    //ec order
                    $payway =  0 ;
                    if($r['personlist']['pay']=='貨到付款')$payway=1;
                           
                    if($r['personlist']['transportID']==2) $address =$r['personlist']['address'];
                    else $address =$r['personlist']['shopnumber'];
                    
                    $datain = array(
                        'platformID' =>1,
                        'ECOrderNum' =>	'EC'.$r['personlist']['orderid'],
                        'ECstatus'	    =>1,
                        'updateTime'	=>date('Y-m-d H:i:s'),
                        'transportID'	=>$r['personlist']['transportID'],				
                        'receiverName'	=>$r['personlist']['name'],
                        'receiverPhone'	=> $r['personlist']['phone'],
                        'receiverAddress'=>$address,
                        'email'           =>$r['personlist']['email'],
                        'payway'          =>$payway,
                        'remark'          =>$postData['comment'],
                        'memberID'       =>$r['personlist']['memberID'],
                        'comID'          =>$r['personlist']['vatnum'],
                        'CarrierType'    =>$r['personlist']['CarrierType'],
                        'CarrierId1'     =>$r['personlist']['CarrierId1'],
                        'NPOBAN'         =>$r['personlist']['NPOBAN']
                    
                    );
                    $this->db->insert('pos_ec_order',$datain);
                    $ECID = $this->db->insert_id();
					//商城出貨定單
					$this->csorderToOrder( $postData['csOrderID'],true,$ECID);//自動下定商品 分配
					//print_r($postData['productID']);
					//檢驗剩餘庫存
					$r = $this->examRemainNum($postData['productID']);
					
				}
				
				
                    //更新 csorderID
                    $this->db->where('id',$row['id']);
                    $this->db->update('pos_weborder_link',array('csOrderID'=>  $postData['csOrderID'],'datatransfer'=>1));


                 

                //寄信給店家
                $this->db->where('shopID',$shopID);
                $query = $this->db->get('pos_sub_branch');
                $shop = $query->row_array();

                 $shop['email'].=',lintaitin@gmail.com';
                $title = '［重要］來自瘋桌遊商城的購物訂單！！'.date('Y-m-d H:i:s');

                $content='<h2>親愛的店家 您好：</h2>'.
                          '<p>有客人在瘋桌遊商城上購物，店面取貨。<br/>'.					
                          '訂單內容請上系統查詢！！<br>'.
                          '<br/>'.	
                            '祝您 事事順心。<br/>'.
                            '(此信由電腦系統直接發出，，直接回覆客人將收不到訊息，請上系統查詢)';
         
        $textTable = '';
        $textTableCont = '';
        $textResult = ''; 
        $orderID = $row['orderID'];
               
        if(!empty($product))
        {
            $textTableTitle = '<table max-width="100%" rules="all" cellpadding="10" style="border:3px #c7c5c5  dashed;padding:3px;">
            <tr>
                <td style="padding:5px; background-color: #efd7da;" colspan="5">*訂單編號:'.$orderID.'_商品清單</td>
            </tr>
            <tr style="background-color: #c7c5c5;">
                <td>商品編號</td>
                <td>商品名稱</td>
                <td style="padding:5px;">數量</td>
                <td style="padding:5px;">單價</td>
                <td style="padding:5px;">小計</td>
            </tr>';
            
            $num =0;$orderPrice = 0; 
            foreach($product as $row)
               
            {
                $num += $row['num'];               
                $textTableCont = $textTableCont.'<tr><td style="padding:8px;">'.$row['pID'].'</td><td style="padding:8px;">'.$row['ZHName'].'</td><td style="padding:8px;">'.$row['num'] .'</td><td style="padding:8px;">'.$row['price'].'元</td><td>'.$row['totalPrice'].'元</td></tr>';
                $orderPrice +=  $row['totalPrice'];
            }
                   
            $textTableEnd = $textTableTitle.$textTableCont.'</table>';
            
            $textResult = $textTableEnd.'<div style="font-size:150%; padding:10px 15px;">訂單一共'.$num.'件商品, <br/>總額NT$'. $orderPrice .' 元</div>';
            $content.= $textResult;
        }
        
      
                
                
                
                
                
                
                
                
                
                $this->Mail_model->myEmail( $shop['email'],$title,$content); 

            
            }
            
        }
        
        
        
    }
    function getBoundle($pID)
    {
        
        $this->db->where('pID',$pID);
        $q = $this->db->get('pos_product_boundle');
        return $q->result_array();
        
    }
	function examRemainNum($productList)
	{
		foreach($productList as $eachProductID)
			{
						//未含 savenum 20190423
						$orderRemainNumAndSaveNum = $this->Order_model->getProductNumExceptOrder($eachProductID,0);
						
						
			
						// get savity num
						$saveNum = $this->Order_model->getMartSaveNum($eachProductID);
                        
            
            
						$orderRemainNum = $orderRemainNumAndSaveNum + 0;//$saveNum = 0 // 20190423 暫時將saveNUm移除 

                        if($orderRemainNumAndSaveNum >100) $num = 20;
                        else if ($orderRemainNumAndSaveNum >5) $num = 5;
                        else $num = $orderRemainNumAndSaveNum;
                        /*
						if($orderRemainNum<0) $num = 0;
						else if($orderRemainNum >=10*$saveNum) $num = 3*$saveNum;
						else if($orderRemainNum < $saveNum) $num = $orderRemainNum;
						else  $num = $saveNum;
						if($saveNum==0) $num = 0;
                        */
					   $d[] = array('productID'=>$eachProductID,'num'=>$num,'safeNum'=>$saveNum);
					}
				;
					//回報庫存
					if(!empty($d))
					{
						$postData['dataList'] = json_encode($d);
                 
					$r = $this->paser->post($this->data['martadmDomain'].'product/product_inventory_sync',$postData,true);   
$this->db->insert('pos_test',array('content'=> 
$this->data['martadmDomain'].'product/product_inventory_sync'.json_encode($postData).json_encode($r)));
		$this->load->model('Order_model');	
					}
				  
			return;
		
		
	}
	
	function csorderToOrder($csOrderID,$autoBackNum = false,$ECID=0)
	{
		
		
        $this->data['csOrder'] = $this-> getCsOrder($csOrderID);
        $destinationShopID = $this->data['csOrder']['shopID'];
		
        $this->data['csOrderData'] = $this->getCsOrderDetail($csOrderID,   $destinationShopID );
        //產生訂單
        //導入order
        //$this->load->model('Order_model');
        //$this->load->model('Product_model');
	    
		$addressID = $this->Order_model->getAddress($destinationShopID);
		$maxNum = $this->Order_model->getMaxOrderNum();
				 	$orderDatain = array(
						'status' =>1,//下好訂單
						'shopID' =>   $destinationShopID,
						'orderTime' =>date('Y-m-d H:i:s'),
						'orderNum' =>$maxNum+1,
						'type'  =>0,//調貨
						'orderComment' => $this->data['csOrder']['comment'].'補貨_'.$this->data['csOrder']['csOrderNum'],
						'addressID' => $addressID
						);
				 	$this->db->insert('pos_order',$orderDatain);
					$newOrderID = $this->db->insert_id();			
		   if($ECID !=0)
           {
                $this->db->where('ECID',$ECID);
                $this->db->update('pos_ec_order',array('orderID'=>$newOrderID));
           }
        
        
        
        
        
			$total = 0 ;$sanTotal = 0;
			foreach( $this->data['csOrderData'] as $row)
			{
				$comment = '';
				if($autoBackNum) $row['backNum'] = $row['num'];
                if($row['backNum']<=0 ) continue;
                if($destinationShopID==666)
                {
                    $sellPrice = $row['sellPrice'];
                    if(strpos($row['ZHName'],'三國')!==false)$sanTotal+=$sellPrice;
                    
                }
                    
                else if($row['productID'] ==8884457  ) //csordermodel 375,33行也有用到
                {
                    //優惠券補貨
                    
                    $sellPrice = $row['sellPrice'];
                    
                    
                    
                    
                }
                else
                {

                    $rowProduct = $this->Product_model->getProductByProductID($row['productID'],$destinationShopID);
                    //獲取這家店上次出貨金額
                    $lastSellPrice = $this->Product_model->getLastPrice($row['productID'],$destinationShopID);
                    if($lastSellPrice==0)    $lastSellPrice =round($rowProduct['purchaseCount']*$rowProduct['price']/100);
                    $sellPrice = min(round($rowProduct['purchaseCount']*$rowProduct['price']/100),$lastSellPrice);
                //
                }
				//出貨品項
				 //導入order
                 //會員資格不補貨
                if($destinationShopID==666||($row['productID']!= 8881935 && $row['productID']!= 8881936))
                {
                
					$orderDetaildatain = array(
						'orderID'=>$newOrderID,
						'sellPrice' =>$sellPrice ,
						'buyNum' => $row['backNum'],
						'sellNum' => $row['backNum'],
						'productID' => $row['productID'],
						'comment' => $this->data['csOrder']['comment'].' '.$comment
					);
					$this->db->insert('pos_order_detail',$orderDetaildatain); 
					$total += $sellPrice  *$row['backNum'];
                }
				 //===========
            }
            
        
        
    
            $this->db->where('id',$newOrderID)	;
			$this->db->update('pos_order',array('total'=>$total));
          return $destinationShopID;   
		
		
	}
    
    function getFinishWebShopOrder($shopID,$date)
    {
        $t = explode('-',$date);
        if($t[2]=='0')$monthKey = true;
        else $monthKey = false
            ;
        $this->db->select('*,pos_online_order_result.memberID as newMemberID');
      
        
        $this->db->join('pos_cs_order','pos_cs_order.csOrderID = pos_online_order_result.csOrderID','left');
        $this->db->where('shopID',$shopID);
        if($monthKey)
        {
             $this->db->where('year(time)',$t[0]);
             $this->db->where('month(time)',$t[1]);
            
        }
        else   $this->db->where('date(time)',$date);
        $query = $this->db->get('pos_online_order_result');
        $data = $query->result_array();
        $webTotal =  0 ;
        $webCash  = 0 ; 
        $webTotal = 0;
        $webFee = 0;
        $csorder =array();
        foreach($data as $row)
        {
            
               $diff = 0 ; 
            if($row['finalTotal']==0 && $row['fee']==0)
            {
                //現場辦會員
                if($row['isNew']==1 || $row['newMemberID']!=0 && $row['cashType']==0)
                {    

                $orderDetail = $this->getCsOrderDetail($row['csOrderID'],$shopID);

                $orderCheckDetail = $this->getCheckDetail($row['checkID'],$shopID);


                    foreach($orderDetail as $a)
                    {



                        foreach($orderCheckDetail as $b)
                        {

                            ;
                            //相同商品，價差在一定範圍內
                            if($a['productID'] == $b['productID'] && round($a['sellPrice'])>= $b['sellPrice'] )
                                $diff+= ($a['sellPrice'] - $b['sellPrice']) * $a['num'];
                        }



                    }


                }







                    $datain['diff'] = $row['diff']  = $diff;  
                    $datain['finalTotal'] =  $row['finalTotal'] = $row['total'] - $row['diff'];
                    //**商城行銷費用設
                    $ratio = 0.15;//抽成比



                    $datain['fee'] =  $row['fee'] = -round($row['finalTotal']* $ratio);
                    $datain['webPay'] = $row['webPay']  = round( $row['total'] * $row['cashType']);


                    $datain['creditFee'] = $row['creditFee'] = -round($row['total']*0.02)*$row['creditType'];
                    $datain['subTotal'] = $row['subTotal'] = $row['total']*$row['cashType'] + $row['fee'] + $row['creditFee'];

                    $this->db->where('csOrderID',$row['csOrderID']);
                    $this->db->update('pos_online_order_result',$datain);
                
                
            }
            $csorder[] = $row  ; 
            $webCash +=  $row['subTotal'] -  $row['webPay'] ;
            $webTotal += $row['finalTotal'];
            $webFee += $row['fee'] + $row['creditFee'];
          
        }
        $result['wbOrder'] = $csorder;
        
        $result['wbCash'] = $webTotal + $webCash;
        $result['wbTotal'] = $webTotal;
        $result['wbFee'] = $webFee;
        
      
       
        
      //  $this->db->join('pos_cs_order','pos_cs_order.csOrderID = pos_online_order_result.csOrderID','left');
        $this->db->where('shopID',$shopID);
           if($monthKey)
        {
             $this->db->where('year(time)',$t[0]);
             $this->db->where('month(time)',$t[1]);
            
        }
        else $this->db->where('date(time)',$date);
        $query = $this->db->get('pos_online_order_home_result');
        $result['wbOrderHome'] = $query->result_array();
        $result['wbOrderHomeProfit'] = 0;
        foreach($result['wbOrderHome']  as $row)
        {
            
            
             $result['wbOrderHomeProfit'] += $row['profit'];
            
        }
        
        
        return $result;
        
    }
    
}

?>
