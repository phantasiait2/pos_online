<?php

class Order extends POS_Controller {

	function Order()
	{
		parent::POS_Controller();
		$this->data['js'] = $this->preload->getjs('pos_allocate');
		$this->data['js'] = $this->preload->getjs('jquery-ui-1.8.16.custom.min');
	
	
		//錯誤檢測代碼
//		$this->PO_model->errDetect();
	}
	private function iframeConfirm()
	{
		$this->load->model('System_model');
   
     
      
     
        
		$shopID = $this->input->get('shopID');
        $licence = $this->input->get('licence');
        $account = $this->input->get('account');
      

 
		if(empty($shopID))$shopID =  $this->uri->segment(3);
		if(empty($licence))$licence =  $this->uri->segment(4);
		if(empty($account))$account =  $this->uri->segment(5);
       
		if($this->System_model->chkShop($shopID,$licence,$account))
		{
            
              
      
			$this->session->set_userdata('account',  $account);
			$this->session->set_userdata('aid', 999);
			$this->session->set_userdata('shopID',  $shopID);
			$this->data['shopID'] = $shopID;
		
	  }
        
		return;
		
	}
	function index()
	{
		$this->iframeConfirm();
    	$this->data['js'] = $this->preload->getjs('jquery.progressbar');
		$this->load->model('Order_model');
		$data['js'] = $this->preload->getjs('date_format');
			$this->data['js'] = $this->preload->getjs('pos_product_query');
			$this->data['css'] = $this->preload->getcss('jquery-ui-1.8.16.custom');
		
		$this->load->model('System_model');
		$data['shopData'] = $this->System_model->getShopByID($this->data['shopID']);	
        $data['distribute'] = $this->Order_model->getShopDiscount($this->data['shopID']);
		$data['shopID'] = $this->data['shopID'];
		$this->load->view('order',$data);
	}
	
    
	function  online()
	{
		$year = $this->input->post('year');	
		$mon = $this->input->post('mon');	
		$mday = $this->input->post('mday');	
		$this->data['css'] = $this->preload->getcss('jquery-ui-1.8.16.custom');
		
	
		$this->data['js'] = $this->preload->getjs('jquery.progressbar');
		$this->data['js'] = $this->preload->getjs('pos_product_query');
		$this->data['js'] = $this->preload->getjs('date_format');
		$data['date'] =$year.'-'.$mon.'-'.$mday;
		if($this->data['shopID']==0)$shopID =  $this->input->post('shopID');
		else $shopID  = $this->data['shopID'];
		$this->data['shopData'] = $this->System_model->getShopByID($shopID);	
		$this->load->model('Order_model');
		$this->data['distribute'] = $this->Order_model->getShopDiscount($shopID);
		$this->data['remote'] = 1;

		
		$this->data['display'] = 'order';
		$this->load->view('template',$this->data);				
		
	}
    function copy_order()
    {
         $this->load->model('Product_model');
        $this->load->model('Order_model');
        $create_type = $this->input->post('create_type');
         $orderNum = $this->input->post('orderNum');
   
        $order = $this->Order_model->getOrderInf(0,$orderNum);
	

		$p['product'] = $this->Order_model->getOrderDetailByID($order['id']);
     
        foreach($_POST['shop'] as $index=>$row)
		{
			if($row==1) 
            {
                $destinationShopID = $index;

              if(isset($p['product']) && count($p['product'])>0)
                {
                    $addressID = $this->Order_model->getAddress($destinationShopID);
                    $maxNum = $this->Order_model->getMaxOrderNum();
                            $orderDatain = array(
                                'status' =>1,//下好訂單
                                'shopID' =>   $destinationShopID,
                                'orderTime' =>date('Y-m-d H:i:s'),
                                'orderNum' =>$maxNum+1,
                                'type'  =>$create_type,//
                                'orderComment' => '',
                                'addressID' => $addressID
                                );
                            $this->db->insert('pos_order',$orderDatain);
                            $newOrderID = $this->db->insert_id();			

                    $total = 0 ;

                    foreach( $p['product'] as $row)
                    {
                        $comment = '電腦自動下單';      
                      
                        //分配貨品
                            $orderRemainNum = $this->Order_model->getProductNumExceptOrder($row['productID'],0);
                            if($orderRemainNum>$row['OSBANum']) $sellNum = $row['OSBANum'];
                            else if ($orderRemainNum>0)$sellNum=$orderRemainNum;
                            else $sellNum = 0;


                        //出貨品項
                         //導入order

                            $orderDetaildatain = array(
                                'orderID'=>$newOrderID,
                                'sellPrice' =>$row['sellPrice'] ,
                                'buyNum' => $row['OSBANum'],
                                'sellNum' => $sellNum,
                                'productID' => $row['productID'],
                                'comment' => $row['orderComment']
                            );
                            $this->db->insert('pos_order_detail',$orderDetaildatain); 
                            $total += $row['sellPrice']  *$row['OSBANum'];

                         //===========
                    }    

                    $this->db->where('id',$newOrderID)	;
                    $this->db->update('pos_order',array('total'=>$total));



                //=====出貨單設定完成
                }
            }
        }
        $data['result'] = true;
		echo json_encode($data);
		exit(1);
	
    }
    function add_to_order()
    {
         $this->load->model('Product_model');
        $this->load->model('Order_model');
        
		$orderID = $this->input->post('orderID');
		 $data['result'] = false;
        //orderID=108378&productID%5B%5D=38&OSBANum%5B%5D=1
      $this->db->insert('pos_test',array('content'=>json_encode($_POST)));
        
        $productID = $this->input->post('productID');
        $OSBANum = $this->input->post('OSBANum');
        
        
        $i = 0 ; $j=0;
        foreach($productID  as $key=>$row)
		{
			
			         $productList[] = $row;
					$product[$j]['productID'] = $row;
					$product[$j++]['OSBANum'] = $OSBANum[$key];
                        
			
		}
        
		if(isset($product)){
            
			
		$order = $this->Order_model->getOrderInf($orderID);
        $shopID = $order['shopID'];
        $total = $order['total'];
        	$shopData = $this->System_model->getShopByID($shopID);
            $magicStatus = $this->Order_model->magicDiscountTest($product,$shopID);
			
          foreach( $product as $row)
                    {
                        //$comment = '電腦自動下單';      
                      
                        //分配貨品
                            $orderRemainNum = $this->Order_model->getProductNumExceptOrder($row['productID'],0);
                            if($orderRemainNum>$row['OSBANum']) $sellNum = $row['OSBANum'];
                            else if ($orderRemainNum>0)$sellNum=$orderRemainNum;
                            else $sellNum = 0;

                        $sellPrice = $this->Order_model->concessionPrice($shopID,$row['productID'],$shopData['discount'],$row['OSBANum'],$magicStatus);
                        //出貨品項
                         //導入orde

                            $orderDetaildatain = array(
                                'orderID'=>$orderID,
                                'sellPrice' =>$sellPrice ,
                                'buyNum' => $row['OSBANum'],
                                'sellNum' => $sellNum,
                                'productID' => $row['productID'],
                                'comment' => ''
                            );
                            $this->db->insert('pos_order_detail',$orderDetaildatain); 
                            $total += $sellPrice  *$row['OSBANum'];

                         //===========
                    }    

                    $this->db->where('id',$orderID)	;
                    $this->db->update('pos_order',array('total'=>$total));
                   $this->cs_order_model->examRemainNum($productList);
            
          $data['result'] = true;
        }
         $data['orderID'] = $orderID;
		echo json_encode($data);
		exit(1);
        
        
    }
    function card_sleeve_exp()
    {
        $this->load->model('Order_model');
         $this->load->model('Product_model');
        $orderID = $this->input->post('orderID');
      
       
        $order = $this->Order_model->getOrderInf($orderID);

        $product = $this->Order_model->getOrderDetailByID($orderID);
      $this->Product_model->cardSleeveInf($product ,true);
        
		foreach($product as $row)
        {
            
           
            if(isset($row['cardSleeveArray']))
            {
                foreach($row['cardSleeveArray'] as $each)
                {
                    if(!isset( $data['card'][$each['name']] ))
                    {
                        $data['card'][$each['name']] = array(
                            'name' =>$each['name'],
                            'need' =>0,
                            'productID'=>$each['productID'],
                            'pack' =>$each['pack']            


                        );

                    }
                    
                   
                    $data['card'][$each['name']]['need'] += $each['need']*$row['buyNum'];
                
                    
                    
                }
                
                
                
                
            }
       
            
            
            
        }
        
       $data['product'] =  $product;
        $data['result'] = true;
		echo json_encode($data);
		exit(1);
        
    }
    
	function get_rumi()
	{
		$shopID = $this->input->post('shopID');
		if($shopID==0) $shopID  = $this->data['shopID'];
		$data['shopInf'] = $this->System_model->getShopByID($shopID);
		$this->load->model('Order_model');
		$rumkub[] = array('name'=>'拉密：XXL版','productID'=>8881727);
		$rumkub[] = array('name'=>'拉密：XP六人家庭版','productID'=>8882583);
		$rumkub[] = array('name'=>'拉密：6人攜帶版','productID'=>8882372);
		$rumkub[] = array('name'=>'拉密：鐵盒旅行版','productID'=>8881728);
		$rumkub[] = array('name'=>'拉密變臉版(柱形盒)','productID'=>8883934
);
  		$rumkub[] = array('name'=>'拉密變臉版(扁盒)','productID'=>8883581
);
		$rumkub[] = array('name'=>'拉密袋裝版(大袋Max Pouch)','productID'=>8882453);
		$rumkub[] = array('name'=>'拉密：旅行版','productID'=>268);
		$rumkub[] = array('name'=>'拉密：單字版','productID'=>271);
		$rumkub[] = array('name'=>'拉密：袋裝攜帶版 (小)','productID'=>8882452);
		$rumkub[] = array('name'=>'拉密：英文簡易紙牌版','productID'=>8883260);
		$rumkub[] = array('name'=>'拉密：標準版','productID'=>8883182);
        $rumkub[] = array('name'=>'拉密：專業版','productID'=>269);
		
		foreach($rumkub as $row)
		{
			$row['orderingNum'] = $this->Order_model->getOrderingNum($shopID,$row['productID']);
			$row['sellnum'] = $this->Order_model->productSell($shopID,$row['productID'],'2019-06-01');
			
			$data['rumkub'][] = $row;
				
		}
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	
		
		
		
	}
	
	function view()
	{
			$this->data['js'] = $this->preload->getjs('jquery.progressbar');
			$this->data['css'] = $this->preload->getcss('jquery-ui-1.8.16.custom');
		$this->data['display'] = "order_view";
		$this->data['js'] = $this->preload->getjs('pos_order');
		$this->data['js'] = $this->preload->getjs('date_format');
		$this->data['js'] = $this->preload->getjs('pos_product_query');
		$this->data['js'] = $this->preload->getjs('jquery.tablesorter');
		$this->data['js'] = $this->preload->getjs('pos_discount');
		$this->load->model('System_model');
		$this->data['shop'] = $this->System_model->getShop();	
		$this->load->view('template',$this->data);
		
	}
	
	function print_out()
	{
		if(!$this->data['logined'] )redirect('/welcome');
		$this->data['display'] = "order_print";
		$this->data['js'] = $this->preload->getjs('pos_order');
		$this->data['js'] = $this->preload->getjs('pos_product_query');
		$this->data['js'] = $this->preload->getjs('jquery.tablesorter');
		$this->data['type'] = $this->uri->segment(3);
		$this->data['orderID'] = $this->uri->segment(4);
		$this->data['showing'] = $this->uri->segment(5);
		$this->data['price'] = $this->uri->segment(6);
	
		$this->load->view('order_print',$this->data);
		
	}

    function box_print_out()
	{
		
		$this->data['display'] = "order_print";
		$this->data['js'] = $this->preload->getjs('pos_order');
		$this->data['js'] = $this->preload->getjs('pos_product_query');
		$this->data['js'] = $this->preload->getjs('jquery.tablesorter');
		$this->data['type'] = $this->uri->segment(3);
		$this->data['orderID'] = $this->uri->segment(4);
		$this->data['showing'] = $this->uri->segment(5);
		$this->data['price'] = $this->uri->segment(6);
		
		$this->load->view('box_print',$this->data);
		
	}

	function today_print_out()
	{
		$this->data['type'] = $this->uri->segment(3);
		$this->data['date'] = $this->uri->segment(4);
		if($this->data['date']==0)$this->data['date'] =  date("Y-m-d");
		$this->load->view('today_order_print',$this->data);
	}
    
    
    
    
    
	function select_print_out()
	{
        $this->data['IDList'] = '';
       if(isset($_POST['selectID']) &&  count($_POST['selectID'])>0)
       {
            $this->data['IDList'] = implode("-",$_POST['selectID']);
       }
        else
        {
            $this->load->model('Order_model');
		$data['shipmentList'] = $this->Order_model->getShipmentList(0,0,100,4,2,date("Y-m-d",mktime(0, 0, 0, date("m")-1, date("d"),date("Y"))),date("Y-m-d"));
            foreach(	$data['shipmentList'] as $row)                 
            {
                 $shipmentID[] = $row['id'];
              }
                                                          
            if(!empty($shipmentID))     $this->data['IDList'] = implode("-",$shipmentID) ;              
            
        }
     
         $this->data['showType'] = $this->input->post('showType');
        
;
		
		$this->load->view('select_order_print',$this->data);
	}
    function select_print()
    {
            
        
        
        
    
    }
    
    
    
    
	function back_print_out()
	{
		
		$this->data['display'] = "order_print";
		$this->data['js'] = $this->preload->getjs('pos_order');
		$this->data['js'] = $this->preload->getjs('pos_product_query');
		$this->data['js'] = $this->preload->getjs('jquery.tablesorter');
		$this->data['orderID'] = $this->uri->segment(3);
		$this->load->view('order_back_print',$this->data);
		
	}
	
	function get_order_list()
	{
		$this->load->model('Order_model');
		$offset = $this->input->post('offect');
		if($this->input->post('offset')!=0)$offset = $this->input->post('offset');
		$num = $this->input->post('num');
		$arive = $this->input->post('arive');
		$shopID = $this->input->post('shopID');
		if(isset($_POST['orderType'])) $orderType = $_POST['orderType'];
		else $orderType = 2;
		
		if($this->data['shopID']!=0) $shopID  = $this->data['shopID'];
	

		$data['orderList'] = $this->Order_model->getOrderList($shopID,$offset,$num,$arive,$orderType);
		$i = 0;
		foreach($data['orderList'] as $row)
		{
			$data['orderList'][$i]['orderTime']	 = substr($row['orderTime'],0,16);
			$data['orderList'][$i]['shippingTime']	 = substr($row['shippingTime'],0,10);
			$data['orderList'][$i]['status'] = $this->Order_model->changeOrderStatus($row['status']);
			if($row['status']>=2) $data['orderList'][$i]['orderStatus']= '完成出貨';
			else   $data['orderList'][$i]['orderStatus']= '<span style="color:red">貨品等候中</span>';
			
			$i++;
			
		}
		$data['result'] = false;
		if($i!=0)$data['result'] = true;
		echo json_encode($data);
		exit(1);		
				
	}

    
    
	function edit_shipment()
	{
		$id = $this->input->post('id')	;
		$datain['shipmentCode'] = $this->input->post('shipmentCode')	;
		$datain['shipmentFee'] = $this->input->post('shipmentFee')	;
		$datain['charge'] = $this->input->post('charge')	;
		$datain['note'] = $this->input->post('note')	;
		$this->db->where('id',$id);
		$this->db->update('pos_order_shipment',$datain);
	$data['result'] = true;
		echo json_encode($data);
		exit(1);		
				
		
	}
    function edit_ec_order()
	{
		$id = $this->input->post('id')	;
		$datain['trackingNumber'] = $this->input->post('shipmentCode')	;
		
		$datain['charge'] = $this->input->post('charge')	;
		$datain['note'] = $this->input->post('note')	;
		$this->db->where('ECID',$id);
		$this->db->update('pos_ec_order',$datain);
	$data['result'] = true;
		echo json_encode($data);
		exit(1);		
				
		
	}
	function get_invoice_list()
	{
		$toMonth= $this->uri->segment(4);
	   	$fromMonth  = $this->uri->segment(3);
	
		$this->db->where('date >=',$fromMonth);
		$this->db->where('date <=',$toMonth);
		$this->db->order_by('invoice');
		$this->db->join('pos_order_shipment','pos_order_shipment.id=pos_order_invoice.shipmentID','left');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID=pos_order_shipment.shopID','left');
		$query = $this->db->get('pos_order_invoice');
		$data['invoice'] = $query->result_array();
		
		
		$this->load->view('/order_invoice',$data);
		
	}
	function get_pre_allocate()
	{
		$this->load->model('Order_model');
		$shopID = $this->data['shopID'];
		$productID = $this->input->post('productID'); 
		$product = $this->Order_model->getPreTime($productID);
		if(!isset($product['num']))$product['num'] = 0;
		$allocate = $this->Order_model->allocateOrder($productID,$product['num']);
		$data['preAllocateNum'] = 0;
		foreach($allocate as $row)
		{
			if($row['shopID'] ==$shopID) $data['preAllocateNum']+=$row['sellNum'];
		}
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	function prepayorder()
	{
		$this->load->model('Order_model');
		$data['shopID'] = $this->data['shopID'];
		
		$data['product'] = $this->Order_model->getPrePayOrder($data['shopID'] );
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
	}
	
	
	function prepay2order()
	{
		$this->load->model('System_model');
		$this->load->model('Order_model');
		if( $this->uri->segment(3)==0)
		{
			$shops = $this->System_model->getShop(true);
			foreach($shops as $sh)
			{
				$p = $this->Order_model->getPrePayOrder($sh['shopID'] );
				$data = '';
				$shopID = $sh['shopID'] ; 
		
				$data[] = $shopID;
				foreach($p as $row)
				{
						if($row['orderNum'] >0) 
						{
							$data[] = $row['productID'];
							$data[] = $row['orderNum'];
							
						}
										
				}
				
				
				$ret = $this->paser->post('http://shipment.phantasia.com.tw/order/order_confirm',$data,true);
				
			
				$address = $this->Order_model->getOrderAddress($shopID);
				$postData['orderID'] = $ret['orderID'];
				$postData['order_comment'] ='冬季預付';
				$postData['commentStr'] = '';
				$postData['receiver'] = $address[0]['receiver'];
				$postData['address'] = $address[0]['address'];
				$postData['phone'] = $address[0]['phone'];
				$postData['shopID'] = $shopID;
				
				$result = $this->paser->post('http://shipment.phantasia.com.tw/order/order_send',$postData,false);
				
				//回算價錢
				$total  = 0;
				foreach($p as $each)
				{
						if($each['orderNum'] >=$each['packingNum']) 
						{
							$sellPrice = round($each['price']*0.6);
							$this->db->where('orderID',$postData['orderID']);
							$this->db->where('productID',$each['productID']);
							$this->db->update('pos_order_detail',array('sellPrice'=>$sellPrice,'sellNum'=>0));//comment
							
						}
						else $sellPrice = round($each['price']*0.65);
						$total+= $sellPrice*$each['orderNum'] ;
										
				}
				
				
				$this->db->where('id',$postData['orderID']);
				$this->db->update('pos_order',array('total'=>$total));//
				
				$this->db->where('orderID',$postData['orderID']);
				$this->db->update('pos_order_detail',array('comment'=>$postData['order_comment']));//comment

				echo 'finish'.$sh['shopID'].'<br>' ;
				
			}
			
			
		}
	
		
	}
	
	
	
	function new_prepay()
	{
		$datain['productID'] = $this->input->post('productID');
		$this->db->insert('pos_prepay_order',$datain);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	function edit_prepay()
	{
		$productID = $this->input->post('productID');
		$datain['packingNum'] = $this->input->post('packingNum');
		$datain['comment'] = $this->input->post('comment');
		$this->db->where('productID',$productID);
		$this->db->update('pos_prepay_order',$datain);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	function delete_prepay()
	{
		$productID = $this->input->post('productID');
		$this->db->where('productID',$productID);
		$this->db->delete('pos_prepay_order');
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
		
	}
	
	function preorder()
	{
	
		$shopID = $this->data['shopID'];
		$year = $this->input->post('year')	;
		$month = $this->input->post('month')	;
		$this->load->model('Order_model');
		$data['shopID'] = $shopID;
		$data['product'] = $this->Order_model->getPreOrder($shopID,$year,$month);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
	}
	
	function preorder_product()
	{
		$this->load->model('Order_model');
		$productID = $this->input->post('productID')	;
		$date = getdate (mktime(0, 0, 0, date("m")+1, date("d")));
		$year = $date['year'];
		$month = $date['mon']	;
		$shopID = $this->data['shopID'];
		$data['num'] = $this->Order_model->getPreOrderNum($shopID,$year,$month,$productID);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	
		
		
	}
	function save_prepay()
	{
		$shopID = $this->data['shopID'];
		$productID = $this->input->post('productID');
		$datain['orderNum'] = $this->input->post('orderNum');
		$this->db->where('shopID',$shopID);
		$this->db->where('productID',$productID);
		$query = $this->db->get('pos_prepay_order_list');
		if($query->num_rows()==0)
		{
			
			$datain['shopID'] = $shopID;
			$datain['productID'] = $productID;
			$this->db->insert('pos_prepay_order_list',$datain);
		}
		else
		{
			$this->db->where('shopID',$shopID);
		$this->db->where('productID',$productID);
				 $this->db->update('pos_prepay_order_list',$datain);
		}
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	function get_preorder($year,$month)
	{
		
		$this->load->model('Order_model');
		;
		$this->data['product'] = $this->Order_model->getPreOrderSum($year,$month);
		$this->data['shopList'] = $this->System_model->getShop(true);
		$this->data['display'] = "preorder";

		$this->load->view('template',$this->data);
		
		
	}
	
	
	
	
	function check_invoice()
	{
		
			
        $this->load->model('Order_model');
		$id = $this->input->post('id')	;	
		$total = $this->input->post('total');
		
        $shopID = $this->input->post('shopID');	
        $selectOrder = $this->input->post('selectOrder');
        
        //電子商務
        if($selectOrder==-3)
        {
                $this->db->where('ECID',$id);
                $query = $this->db->get('pos_ec_invoice');
                $data['invoice'] = $query ->result_array();

                foreach($data['invoice'] as $row)
                {

                    $total-=$row['price'];

                }

                if($total!=0) $data['result']=false;
                else $data['result'] = true;
            
            
            
            
        }
        else
        {
            $order = $this->Order_model->getShipmentInf($id);
            if($shopID==0)
            {

                $shopID = $order['shopID'];
            }


            if($shopID<700 || $order['type']==1)$data['result'] = true;
            else
            {

                $this->db->where('shipmentID',$id);
                $query = $this->db->get('pos_order_invoice');
                $data['invoice'] = $query ->result_array();

                foreach($data['invoice'] as $row)
                {

                    $total-=$row['price'];

                }

                if($total!=0) $data['result']=false;
                else $data['result'] = true;
            }
            $data['order'] =$order;

            
        }
		
		echo json_encode($data);
		exit(1);
	}
	
	
	function get_invoice()
	{
		$this->load->model('Order_model');
		$id = $this->input->post('id')	;	
		$data['invoice'] = $this->Order_model->getInvoice($id);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
    function get_ec_invoice()
	{
		$this->load->model('Order_model');
		$id = $this->input->post('id')	;	
		$data['invoice'] = $this->Order_model->getECInvoice($id);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	function edit_invoice()
	{
		$id = $this->input->post('id');
        $type = $this->input->post('type')	;
        if($type=='EC')
        {
            $db = 'pos_ec_invoice';
            $mainID = 'ECID';
            
        }
        else
        {
            $db = 'pos_order_invoice';
            $mainID = 'shipmentID'; 
            
        }
        
        
		if($this->input->post('appand')!=1)
		   {
			   	$this->db->where($mainID,$id);
		$this->db->delete($db);
			   
		   }
	
		for($i=1;$i<=10;$i++)
		{
			
			if($this->input->post('invoice_'.$i)!='')
			{
				$datain[$mainID] = $id;
				$datain['invoice'] = $this->input->post('invoice_'.$i);
				$datain['price'] = $this->input->post('iPrice_'.$i);
				$datain['date'] = $this->input->post('iDate_'.$i);
				$this->db->insert($db,$datain);
			}
			
		}
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
		
	}
	
	function delete_shipment()
	{
	
		$this->load->model('Order_model');
		$this->load->model('Product_model');
		$shipmentID = $this->input->post('shipmentID');
		$product = $this->Order_model->getShipmentDetailByID($shipmentID);
		$order = $this->Order_model->getShipmentInf($shipmentID);
	
		$orderStatus = $order['status'];
		$data['result'] = false;
        $time = getdate();
		if($orderStatus==4)
		{
			foreach($product as $row)
			{
				
				//$ret = $this->Product_model->getProductByProductID($row['productID']);
				if($row['sellNum']!=-1&&isset($row['productID']))$this->Product_model->updateNum($row['productID'],$row['sellNum'],0,round($row['eachCost']));
				$this->db->where('id',$row['rowID']);
				$this->db->update('pos_order_detail',array('status'=>0));
				$this->db->where('id',$row['rowID']);
				$query = $this->db->get('pos_order_detail');
				$ret = $query->row_array();
				$this->db->where('id',$ret['orderID']);
				$this->db->update('pos_order',array('status'=>1));
                 if($order['type']==1)
                 {
                     $this->Order_model->updateConsignment($order['shopID'] ,$row['productID'],-$row['sellNum'],$row['sellPrice'],$time['year'],$time['mon'],true,$row['eachCost']);
                
                $this->db->insert('pos_test',array('content'=> json_encode($row)));
                     
                 }
                
            }   
      
            
        
                
                
                
			
			$this->db->where('id',$shipmentID);
			$this->db->delete('pos_order_shipment');
		
				
		$this->db->where('shipmentID',$shipmentID);
		$this->db->delete('pos_order_shipment_detail');
		$data['result'] = true;
		}
		
		
		echo json_encode($data);
		exit(1);				
		
	}
	function delete_order()
	{
		$this->load->model('Order_model');
		$this->load->model('Product_model');
		$orderID = $this->input->post('orderID');
		$product = $this->Order_model->getOrderDetailByID($orderID);
		$order = $this->Order_model->getOrderInf($orderID);
	
		/*
		$orderStatus = $order['status'];
		if($orderStatus>=2)
		{
			foreach($product as $row)
			{
				if($row['sellNum']!=-1&&isset($row['productID']))$this->Product_model->updateNum($row['productID'],$row['sellNum'],0,$row['purchasePrice']);
				
			}
		}
		
		*/
		$this->db->where('id',$orderID);
		$this->db->delete('pos_order');
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
	}
	function delete_back_order()
	{
		$this->load->model('Order_model');
		$this->load->model('Product_model');
		$backID = $this->input->post('backID');
		$order = $this->Order_model->getOrderBackInf($backID);
	
		$orderStatus = $order['status'];
		
			$data['result'] = false;
		if($orderStatus<=3)
		{
			/*
			foreach($product as $row)
			{
				if($row['sellNum']!=-1&&isset($row['productID']))$this->Product_model->updateNum($row['productID'],$row['sellNum'],0);
				
			}
			*/
			$this->db->where('id',$backID);
			$this->db->delete('pos_order_back');
			$data['result'] = true;
		}
		
		
	
		echo json_encode($data);
		exit(1);		
	}
	function preorder_check()
	{
		$this->load->model('Order_model');
		$year = $this->input->post('year')	;
		$month = $this->input->post('month')	;
		$data['result'] = $this->Order_model->preOrderCheck($this->data['shopID'],$year,$month);
		echo json_encode($data);
		exit(1);
	}
	
	
		
	function preorder_save()
	{
		
		$date = getdate (mktime(0, 0, 0, date("m")+2, date("d")-3));

		$year = $date['year'];
		$month = $date['mon']	;
		$i = 0;$j=0;
		foreach($_POST as $row)
		{
			if($i==0) $shopID = $row;
			else
			{
				switch($i%2)
				{
					case 1:
						$product[$j]['productID'] = $row;
					break;
					case 0:
						$product[$j++]['num'] = $row;
					break;	
					
					
				}	
				
				
			}
			
			
			
			$i++;
		}
		$this->load->model('Product_model');
		$this->load->model('Order_model');
	
		$i = 0 ;
		if($j>0)
		{
			foreach($product as $row)
			{
				$this->Order_model->preOrderUpdate($this->data['shopID'],$year,$month,$row['productID'],$row['num']);
			}
			
		}
		
		$data['result'] = true;
		
		
		echo json_encode($data);
		exit(1);
		
		
	}
	function pre_order_turn_order()
	{
		$this->load->model('Order_model');
		$this->load->model('System_model');
		$date = getdate (mktime(0, 0, 0, date("m"), 1));

		$year = $date['year'];
		$month = $date['mon'];
		
		$shopList = $this->System_model->getShop(true);
		
		foreach($shopList as $eachshop)
		{
			$data = '';
			$shopID = $eachshop['shopID']; 
			$result = $this->Order_model->getPreOrder($shopID,$year,$month);
		
			$data[] = $shopID;
			foreach($result as $row)
			{
					if($row['orderNum'] >0) 
					{
						$data[] = $row['productID'];
						$data[] = $row['orderNum'];
						
					}
				
				
				
				
			}
			
			
			$ret = $this->paser->post('http://shipment.phantasia.com.tw/order/order_confirm',$data,true);
			
		
			$address = $this->Order_model->getOrderAddress($shopID);
			$postData['orderID'] = $ret['orderID'];
			$postData['order_comment'] =$year.'年'.$month.'月份預定';
			$postData['commentStr'] = '';
			$postData['receiver'] = $address[0]['receiver'];
			$postData['address'] = $address[0]['address'];
			$postData['phone'] = $address[0]['phone'];
			$postData['shopID'] = $shopID;
			
			$result = $this->paser->post('http://shipment.phantasia.com.tw/order/order_send',$postData,false);
			
			$this->db->where('orderID',$postData['orderID']);
			$this->db->update('pos_order_detail',array('comment'=>$postData['order_comment']));//comment
			
			echo $result;
		
		}
		
		
	
		
		
	}
	
	function open_suggest()
	{
		$shopID = $this->input->post('shopID');
	
		$this->load->model('Product_model');
		$this->load->model('Order_model');
		$result = $this->Product_model->getRentTimesPure(0);		
			$data[] = $shopID;
			$i = 0;
			foreach($result as $row)
			{
				$i++;
					if($i>500) break;
					$data[] = $row['productID'];
						$data[] = 1;
					
				
				
				
				
			}
			
			
			$ret = $this->paser->post('http://shipment.phantasia.com.tw/order/order_confirm',$data,true);
			
		
			$address = $this->Order_model->getOrderAddress($shopID);
			$postData['orderID'] = $ret['orderID'];
			$postData['order_comment'] ='開盒清單';
			$postData['commentStr'] = '';
			$postData['receiver'] ='';
			$postData['address'] = '';
			$postData['phone'] = '';
			$postData['shopID'] = $shopID;
			
			$result = $this->paser->post('http://shipment.phantasia.com.tw/order/order_send',$postData,false);
			
			$this->db->where('orderID',$postData['orderID']);
			$this->db->update('pos_order_detail',array('sellPrice'=>0,'comment'=>$postData['order_comment']));//comment
			
			echo $result;	
		
		
		
	}
	
	function order_confirm()
	{
		
		
		$i = 0;$j=0;
;
		
		
		foreach($_POST as $row)
		{
			if($i==0) $shopID = $row;
			else
			{
				switch($i%2)
				{
					case 1:
						$product[$j]['productID'] = $row;
                        $productList[] = $row;
					break;
					case 0:
						$product[$j++]['num'] = $row;
					break;	
					
					
				}	
				
				
			}
			
			
			
			$i++;
		}
		$this->load->model('Product_model');
		$this->load->model('Order_model');
	    $this->load->model('cs_order_model');
		$shopData = $this->System_model->getShopByID($shopID);
		$i = 0 ;
		if($j>0)
		{
			$this->db->insert('pos_order',array('shopID'=>$shopID,'status'=>0));
			$orderID = $this->db->insert_id();
			$total = 0;
			$magicStatus = $this->Order_model->magicDiscountTest($product,$shopID);
			
			// $magicStatus = 3 or 4 表可以直接下單
			if($magicStatus==-1)
			{
				
				$data['result'] = false; //直接取消訂單;
				echo json_encode($data);
				exit(1);
			}
			foreach($product as $row)
			{
				
				//check suit and allocate in function
				if($this->Order_model->suitAllocate($row,$shopData,$orderID,$total,$magicStatus))
				{
					
					// do nothing
					
				}
				else
				{
					$sellPrice = $this->Order_model->concessionPrice($shopID,$row['productID'],$shopData['discount'],$row['num'],$magicStatus);
				
				
					if($magicStatus==3||$magicStatus==4)$sellNum = 0;
					else
					{
						//分配貨品
						$orderRemainNum = $this->Order_model->getProductNumExceptOrder($row['productID'],0);
						if($orderRemainNum>$row['num']) $sellNum = $row['num'];
						else if ($orderRemainNum>0)$sellNum=$orderRemainNum;
						else $sellNum = 0;
                        
					}
					//==
					$this->db->insert('pos_order_detail',array('orderID'=>$orderID,'productID'=>$row['productID'],'buyNum'=> $row['num'],'sellNum' =>$sellNum,
					'sellPrice'=>$sellPrice));
					$total += $row['num']*$sellPrice;

					
					
				}
				
				
			}
            
            $this->cs_order_model->examRemainNum($productList);
			$data['product'] = $this->Order_model->getOrderDetailByID($orderID);
			$data['address'] = $this->Order_model->getOrderAddress($shopID);
			
			
				// $magicStatus = 3 or 4 表可以直接下單
		if($magicStatus==3 ||$magicStatus==4 )$data['orderComment'] = $shopData['name'].'魔風直送訂單';
		else $data['orderComment']  = '';
		
				
			
			$this->db->where('id',$orderID);
			$this->db->update('pos_order',array('total'=>$total,'orderComment'=>$data['orderComment']));
			
		}
		
		if(!isset($total))$data['result'] = false;
		else
		{
			$data['total'] = $total;
			$data['orderID'] = $orderID;
			$data['result'] = true;
		}

		echo json_encode($data);
		exit(1);
		
		
		
	}
    
   
    function pokemon_confirm()
    {
        	$i = 0;$j=0;
;       $shopID = $this->data['shopID'];
		$this->load->model('Product_model');
		$this->load->model('Order_model');
	    $this->load->model('cs_order_model');

       
       
		if(count($_POST['id'])>0)
		{
            $PID = $_POST['id'];
			$this->db->insert('pos_order',array('shopID'=>$shopID,'status'=>0));
			$orderID = $this->db->insert_id();
			$total = 0;
			
			foreach($PID as $key=>$row)
			{
				 $totalNum = $_POST['num'][$key];
                if( $totalNum <=0)continue;
				
					$p = $this->Order_model->getPokemon($row);
				   
			
                    foreach($p['detail'] as $detail)
                    {
                        
                        
                        
                        $productID = $detail[0];
                        $orderNum = $detail[1] * $totalNum;
                        $sellPrice = $detail[2];
                    
                        //分配貨品
                        $orderRemainNum = $this->Order_model->getProductNumExceptOrder($productID,0);
                            if($orderRemainNum>$orderNum) $sellNum = $orderNum;
                            else if ($orderRemainNum>0)$sellNum=$orderRemainNum;
                            else $sellNum = 0;

                        
                        //==
                        $this->db->insert('pos_order_detail',array('orderID'=>$orderID,'productID'=>$productID,'buyNum'=> $orderNum,'sellNum' =>$sellNum,
                        'sellPrice'=>$sellPrice));
                        $total += $orderNum*$sellPrice;
                    }
                
					
                        
            }
					
				
				
				
		
            
         
			$data['product'] = $this->Order_model->getOrderDetailByID($orderID);
			$data['address'] = $this->Order_model->getOrderAddress($shopID);
			
            $data['orderComment']  = '寶可夢訂單';
			
			$this->db->where('id',$orderID);
			$this->db->update('pos_order',array('total'=>$total,'orderComment'=>$data['orderComment']));
            $this->db->insert('pos_ptcg_order',array('orderID'=>$orderID));
        }
		
		
		if(!isset($total))$data['result'] = false;
		else
		{
			$data['total'] = $total;
			$data['orderID'] = $orderID;
			$data['result'] = true;
		}

		echo json_encode($data);
		exit(1);
		
        
        
    }
	
	
	function delete_magic_shop()
	{
		$shopID = $this->input->post('shopID');
		$this->db->where('shopID',$shopID);
		$this->db->delete('pos_magic_status');
			$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	
	
	function order_cancel()
	{
		$orderID = $this->input->post('orderID');
		
		$this->db->where('id',$orderID);
		$this->db->delete('pos_order');
		
		$this->db->where('orderID',$orderID);
		$this->db->delete('pos_order_detail');		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
			
	}
	function order_create()
	{
		$this->load->model('Order_model');
		$shopID = $this->input->post('shopID');
		$type = $this->input->post('create_type');
		$maxNum = $this->Order_model->getMaxOrderNum();
	
	
		$this->db->insert('pos_order',array('shopID'=>$shopID,'status'=>0,'orderNum'=>$maxNum+1,'orderTime'=> date("Y-m-d H:i:s"),'type'=>$type));
		$data['orderID'] = $this->db->insert_id();
        
        if($shopID==666)
        {
            $this->db->insert('pos_ec_order',array('orderID'=>$data['orderID'],'ECStatus'=>1,'updateTime'=> date("Y-m-d H:i:s")));
            
            
        }
        
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
		
	}
	
	
	
	function order_send()
	{
		$this->load->model('Order_model');
		$orderID = $this->input->post('orderID');
		$comment = $this->input->post('order_comment');
		$commentStr = $this->input->post('commentStr');
	
	
		$receiver = $this->input->post('receiver');
		$address = $this->input->post('address');
		$phone = $this->input->post('phone');
        $comID = $this->input->post('comID');
        $CarrierType = $this->input->post('CarrierType');
        $CarrierId1 = $this->input->post('CarrierId1 ');
        $NPOBAN = $this->input->post('NPOBAN');
        $email = $this->input->post('email');
		$shopID = $this->input->post('shopID');
		if($shopID==0) $shopID = $this->data['shopID'];
        
 
        
        
		$addressID = $this->Order_model->orderAddress($receiver,$address,$phone,$shopID,$comID,$CarrierType,$CarrierId1,$NPOBAN,$email);
		$maxNum = $this->Order_model->getMaxOrderNum();
		
		
		if($shopID==1066 && $this->data['shopID']==1066)
        {
      
            $type=1;//敦琦自動寄賣。
            //更改出貨狀態為綠燈
            $this->db->where('shopID',$shopID);
	  	    $this->db->update('pos_sub_branch',array('shipmentStatus'=>1));
            
            
        }
		else $type= 0 ;
		
		$this->db->where('id',$orderID);
		$this->db->update('pos_order',array('status'=>1,'orderNum'=>$maxNum+1,'orderComment'=>$comment,'orderTime'=> date("Y-m-d H:i:s"),'addressID'=>$addressID,'type'=>$type));//status for send out
		$this->db->where('orderID',$orderID);
		$this->db->update('pos_order_detail',array('status'=>0));//status for send out

		$commentList = explode('[#]',$commentStr);
		foreach($commentList as $row)
		{
			$each = explode(',',$row)	;
			//print_r($each);
			if(isset($each[0])&&isset($each[1]))
			{
			$this->db->where('id',$each[0]);
			$this->db->update('pos_order_detail',array('comment'=>$each[1]));//comment
			}
			
		}

		$data['addressID'] = $addressID;
		$data['orderID'] = $orderID;
		$data['orderNum'] = $maxNum+1;
		$data['result'] = true;
		$order = $this->Order_model->getOrderInf($orderID);
		$orderDetail = $this->Order_model->getOrderDetailByID($orderID);
		
		
	


		//魔法風雲會測試
		$magicStatus = $this->Order_model->magicDiscountTest($orderDetail,$shopID,'OSBANum');
		
		//有買魔風的狀態
        if($magicStatus>0 )$this->magic_order_receipt($shopID,$orderDetail,$order);//寄出對帳單。

        if($comment=='寶可夢訂單') {
            $this->magic_order_receipt($shopID,$orderDetail,$order,'phantasia.pm@gmail.com','寶可夢');//寄出對帳單。
          
        }
        
        // $magicStatus = 3 or 4 表可以直接下單
		if($magicStatus==3 ||$magicStatus==4 )
		{
			$this->magic_order_mail($shopID,$orderDetail,$order);//寄出通知信件
            
			$this->db->insert('pos_magic_order',array('orderID'=>$orderID));
			
				

		}


		echo json_encode($data);
		exit(1);		
		
	}
	function magic_order_receipt($shopID,$data,$order,$pushEmail ='',$type="魔法風雲會")
    {
        $title ='O'.$order['orderNum'].' '.date("Y-m-d").'瘋桌遊'.$order['name'].'的'.$type.'訂單';
		
		
		
		$content ='<h3>親愛的店家您好：</h3>';
		$content .='<h3>本次'.$type.'新訂單已下單完成</h3>';
		$content .='<h3>下單店家：瘋桌遊-'.$order['name'].'</h3>';
		$content .='<h3>寄送地址：'.$order['address'].'</h3>';
		$content .='<h3>收件人：'.$order['receiver'].'</h3>';
		$content .='<h3>聯絡電話：'.$order['phone'].'</h3>';
		$content .='<h3>訂單內容</h3>';
		
        $total = 0;
		foreach($data as $row)
		{
			$content .=$row['ZHName'].' '.$row['sellPrice'].' X '. $row['OSBANum'].'<br/>';
            $total +=$row['OSBANum'] *$row['sellPrice'];
		}
		
		
		
		$content .='<h3>***===請立刻完成匯款，以利商品能準時送達===***</h3>';
        $content .='<h1>總金額：'.$total.'</h1>';
        $content .='感謝您的支持以及配合<br/>'.
			'預祝您業績蒸蒸日上<br/>'.
			'匯款帳號：<br/>'.
			'兆豐國際商業銀行(017)板橋分行 '.
			'幻遊天下股份有限公司 20609013372<br/>'.
			'<br/>';
     
		$content .='<h3>公司名稱：幻遊天下股份有限公司</h3>';
		$content .='<h3>統一編號：53180059</h3>';
		$content .='<h3>帳單地址：新北市板橋區南雅南路二段11-26號1f</h3>';
		$content .='<h3>連絡電話：02-86719616</h3>';
		
		
		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: product@phantasia.tw\nReply-To:product@phantasia.tw\n";
		mb_internal_encoding('UTF-8');
		if($pushEmail !='')$pushEmail  = ','.$pushEmail ;
        $this->Mail_model->myEmail('phantasia.ac@gmail.com,'.$order['email'].$pushEmail ,$title,$content,$headers,0,100);
	
		return;
        
        
        
        
        
    }
	
	function magic_order_mail($shopID,$data,$order)
	{
		
		if($shopID==100)return;
		$title ='O'.$order['orderNum'].' '.date("Y-m-d").'瘋桌遊'.$order['name'].'的新訂單，(此單請將貨與發票帳單分開寄送)';
		
		
		
		$content ='<h3>您好：</h3>';
		$content .='<h3>跟貴司下定新訂單<span style="color:red">(此單請將貨與發票帳單分開寄送)</span></h3>';
		$content .='<h3>下單店家：瘋桌遊-'.$order['name'].'</h3>';
		$content .='<h3>寄送地址：'.$order['address'].'</h3>';
		$content .='<h3>收件人：'.$order['receiver'].'</h3>';
		$content .='<h3>聯絡電話：'.$order['phone'].'</h3>';
		$content .='<h3>訂單內容</h3>';
		
		foreach($data as $row)
		{
			$content .=$row['ZHName'].' X '. $row['OSBANum'].'<br/>';
		}
		
		
		
		$content .='<h3>***===請協助將發票及帳單寄往以下資訊===***</h3>';
		$content .='<h3>公司名稱：幻遊天下股份有限公司</h3>';
		$content .='<h3>統一編號：53180059</h3>';
		$content .='<h3>帳單地址：新北市板橋區南雅南路二段11-26號1F</h3>';
		$content .='<h3>連絡電話：02-86719616</h3>';
		
		
		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: product@phantasia.tw\nReply-To:product@phantasia.tw\n";
		mb_internal_encoding('UTF-8');
		
		$this->Mail_model->myEmail('taiwan@wizards.com,phantasia.pm@gmail.com,phantasia.ac@gmail.com',$title ,$content,$headers,0,100);
		
		
		
		return;
		
		
		
	}
	
	
	
	
	
	function change_shipping_date()
	{
		$id = $this->input->post('id')	;
		$date = $this->input->post('date')	;
		$this->db->where('id',$id);
		$this->db->update('pos_order_detail',array('shippingDate'=>$date));
		$data['result']=true;
		echo json_encode($data);
		exit(1);	
	}
	function attach_consignment()
	{
		$this->load->model('Order_model');	
		$this->load->model('System_model');	
		$this->load->model('Product_model');	
        $this->load->model('Cs_order_model');	
        
		ini_set('max_execution_time', 0);
		$shopList = $this->System_model->getShop(true);
		$time  = getdate();
  	    $this->consignment_num_check($time['year'],$time['mon']);
		foreach($shopList as $shop)
		{
			//加盟商 only
			$shopID = $shop['shopID'];
			 $this->db->insert('pos_test',array('content'=> $shopID));
			
			$consigment = $this->Order_model->getAttachConsignment($shopID,$time['year'],$time['mon']);
		      $this->db->insert('pos_test',array('content'=> $shopID.json_encode($consigment)));
            
		
			$orderDetail = array();
            $productList = array();
			$orderTotal = 0;
			
			foreach($consigment as $row)
			{
				if($row['type']==7) continue;//LC FOW 不補貨
				$find['productID'] = $row['productID'];
				$ret = $this->Product_model->getProductStock($find,0);
			
				$product = $ret['product'][0];
				//$estDistribute = $this->Order_model->getProductInOrder($product['productID'],2);//預計要被分配掉，訂貨優先
				$ret = $this->Order_model->getProductInOrder($row['productID'],2,$shopID);//只要不在訂單內
				//if(($row['stock']-$estDistribute)>= $row['sellNum'] && $ret ==0)
       
                
				if($ret==0)
				{
					$sellNum = 0;
					$remainNum = $this->Order_model->getProductNumExceptOrder($row['productID'],0);
					if($remainNum>0) $sellNum = 1;
					//未在其他訂單中
					$orderDetail[] = array(
					
							'sellNum' => $sellNum,
							'buyNum'  => 1,
							'sellPrice' =>$row['sellPrice'],
							'productID' => $row['productID']
					);
					$orderTotal+=$row['sellPrice'];
                    	$productList[] = $row['productID'];	
				}
		          $this->Cs_order_model->examRemainNum($productList);
				
				
			}  
            
        
          
        
            
            //非瘋桌遊體系不補貨
			if(count($orderDetail)>0 && $shopID<600)
			{		
			
				$maxNum = $this->Order_model->getMaxOrderNum();
				$type= 1 ; //for consignment
				$addressID = $this->Order_model->getAddress($shopID);
				$this->db->insert('pos_order',array('shopID'=>$shopID,'status'=>1,'orderNum'=>$maxNum+1,'orderTime'=> date("Y-m-d H:i:s"),'total'=>$orderTotal,'type'=>$type,'orderComment'=>'寄賣補貨','addressID'=>$addressID));
				$orderID = $this->db->insert_id();
				
				foreach($orderDetail as $row)
				{
					$row['orderID'] = $orderID;
					$this->db->insert('pos_order_detail',$row);		
				}
			}
		
		}
		
		
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
	}
	
	function order_recomment()
	{
	
		$this->load->model('Order_model');	
		$this->load->model('System_model');	
		$this->load->model('Product_model');	
		$this->Order_model->cleanProductPreTime();
			
			$allProduct = $this->Order_model->getAllProductOnOrder();
		
			foreach($allProduct as $row)
			{
				$find['productID'] = $row['productID'];
				//echo '=='.$row['productID'].'==';
				$ret = $this->Product_model->getProductStock($find,0);
				$product = $ret['product'][0];
				$estDistribute = $this->Order_model->getProductInOrder($row['productID'],2);//預計要被分配掉，訂貨優先
				//echo $product['productID'].'('.$estDistribute.')'.'<br/>';
				$num = $product ['nowNum']-$estDistribute;
				if($num< 0  )
				{
					//不足夠配貨應該叫貨
					$product['recommendNum'] = -$num;
					$product['supplierName'] = $row['supplierName'];
					$product['shopList'] = $row['shopList'];
					$product['orderNumList'] = $row['orderNumList'];
					$product['preTime'] = $row['preTime'];
					
					$data['product'][] = $product;
				}
				
				
				
			}
		
		
		
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
		
		
	}
	
	function shipment_create()
	{
		$this->load->model('Order_model');
		$shopID = $this->input->post('shopID');
        $this->load->model('System_model');
        
		$type = $this->input->post('create_type');
		$addressID = $this->input->post('addressID');
		$this->load->model('Product_model');	
		$data['result'] = true;
		$i = 0 ; $j = 0 ;
		
		
		foreach ($_POST as $row)
		{
			if($i!=0 && $i!=1 && $i!=2)
			switch($i%5)
			{
				case  3: 
				
					$order[$j]['sellPrice'] = $row;
				break;
				case  4: 
					$order[$j]['check'] = $row;
				
				case 0 :
				
					$order[$j]['rowID'] = $row;
				break;
				
				case 1 : 
					$order[$j]['sellNum'] = $row;
		
				break;
				
				case  2: 
				
					$order[$j++]['comment'] = $row;
				break;
				
			}			
			$i++;
		}

//print_r($order);

		$total = 0;
		$profit  = 0;
		if($j<=0)$data['result'] = false;
		else
		{
			//更新出貨狀態
			$this->db->where('shopID',$shopID);
			$this->db->update('pos_sub_branch',array('shipmentStatus'=>0));
			
		$maxNum = $this->Order_model->getMaxShippingNum();
		$this->db->insert('pos_order_shipment',array('shopID'=>$shopID,'status'=>0,'shippingNum'=>$maxNum+1,'type'=>$type,'addressID'=>$addressID,'createTime'=>date("Y-m-d H:i:s")));
		$data['shipmentID'] = $this->db->insert_id();			
			foreach($order as $row)
			{
				if($row['check']==1)
				{
					$this->db->where('id',$row['rowID']);
					$query = $this->db->get('pos_order_detail');
					$ret = $query->row_array();
					
					$avgCost = $this->Product_model->getAvgCost($ret['productID']);
					
				$datain =array(
					'shipmentID' =>$data['shipmentID'] ,
					'rowID'      => $row['rowID'] ,
					'sellPrice'  => $row['sellPrice'] , 
					'sellNum'    => $row['sellNum'] ,
					'comment'   => $row['comment'],
					'eachCost'  =>$avgCost,
                    'sProductID' =>$ret['productID']
				);
				$total += $row['sellPrice'] *$row['sellNum'] ;
				$profit+=  ($row['sellPrice']-$avgCost)   *$row['sellNum'] ;
				$this->db->insert('pos_order_shipment_detail',$datain);
				}
		}
            
        $s = $this->System_model->getShopByID($shopID);
            $distributeType = $s['distributeType'];
        if($distributeType==5||$distributeType==9||$distributeType== 10||$distributeType==11||$distributeType==17||$distributeType==21 )
        {
            
            $invoiceDetail = 1;
            
            
        }
        else $invoiceDetail =0;
            
            
		$this->db->where('id',$data['shipmentID']);
		$this->db->update('pos_order_shipment',array('total'=>$total,'profit'=>$profit,'shippingTime'=>date("Y-m-d H:i:s"),'invoiceDetail'=>$invoiceDetail));
		}

		echo json_encode($data);
		exit(1);	
	}
	function comment_change()
	{
		$this->input->post('rowID');
		$this->input->post('comment');
		
		
		$this->db->where('id',$this->input->post('rowID'));
		$this->db->update('pos_order_detail',array('comment'=>$this->input->post('comment')));
		$data['result'] = true;
		
		echo json_encode($data);
		exit(1);
		
	}
	function update_shipment_profit()
	{
		$this->load->model('Order_model');
			$this->load->model('Product_model');
		$this->db->where('shippingTime >','2012-11-30');
		$this->db->where('status >=',2);
		$query = $this->db->get('pos_order_shipment');
		$orderList = $query->result_array();
		foreach($orderList as $order)
		{
			$data = $this->Order_model->getShipmentDetailByID($order['id'],1);
			$profit = 0;
			foreach($data as $row)
			{
				$avgCost = $this->Product_model->getAvgCost($row['productID']);
				//$this->db->where('rowID',$row['rowID']);
				//$this->db->update('pos_order_shipment_detail',array('eachCost'=>$avgCost));
				$profit+= ($row['sellPrice']-$avgCost)  *$row['sellNum'] ;
				
			}
			$this->db->where('id',$order['id']);
			$this->db->update('pos_order_shipment',array('profit'=>$profit));
			echo "shipingNum".$order['shippingNum'].'updated';
		}
	

		
	}
	
	function get_today_shipment()
	{
		$type = $this->input->post('type');


		$date =  $this->input->post('date');
	
		$this->load->model('Order_model');
		$data['product'] = $this->Order_model->getShipmentTodayDetail($type,$date);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
    
    function get_select_shipment()
    {
        $idList = $this->input->post('idList');
       
        $this->load->model('Order_model');
       
        $data['product'] = $this->Order_model->getShipmentDetailByIDList($idList);
        
         $idArray = explode('-',$idList);
        foreach( $idArray as $row)
            $data['orderList'][] = $this->Order_model->getShipmentInf($row);
        
        //print_r($data);
       $data['result'] = true;
		echo json_encode($data);
		exit(1);
        
    
    }
	
	
	function get_shipment()
	{
           
		$this->load->model('Order_model');
		$shipmentID = $this->input->post('shipmentID');
        $shippingNum = $this->input->post('shippingNum');
		$showing = $this->input->post('showing');
		$order = $this->Order_model->getShipmentInf($shipmentID,$shippingNum);
        if(empty($order))    
        {
            
           $data['result'] = false;
		echo json_encode($data);
		exit(1); 
            
        }
        
        if($shipmentID ==0)   $shipmentID =  $order['id'];
		$data['order'] = $order;
        
		$data['order'] ['statusID'] = $order['status'];
		$data['order'] ['status'] = $this->Order_model->changeOrderStatus($order['status']);
		$data['total'] = $order['total'];
		$data['product'] = $this->Order_model->getShipmentDetailByID($shipmentID,$showing);
		$data['address'] =  $this->Order_model->getOrderAddress($order['shopID']);
        $data['shopData']= $this->System_model->getShopByID($order['shopID']); 
        for($i=1;$i<=7;$i++)
        {
            $data['arriveDate'][] =  $arriveDate = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")+$i,date("Y")));
        }
		$data['invoice'] = $this->Order_model->getInvoice($shipmentID);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
	}
    function get_ec_order()
    {
        $this->load->model('Order_model');
        $orderID = $this->input->post('orderID');
        $ECID = $this->input->post('shipmentID');
        $data['order'] = $this->Order_model->getECOrderInf($orderID,$ECID);
        
        
        
        $data['result'] = true;
		echo json_encode($data);
		exit(1);
        
        
        
        
    }
   
    function get_order_in_shipment()
    { $this->load->model('Order_model');
        
         $orderID = $this->input->post('orderID');
   
        $data['order'] = $this->Order_model->getECOrderInf($orderID);
        $shipmentID = $this->input->post('shipmentID');
     
        $data['order']['shipmentComment'] = $data['order']['orderComment'];
        $data['product'] = $this->Order_model->getShipmentDetailByID($shipmentID,0,-1,$orderID);
        
        $data['total'] = $data['order']['total'];
         $data['result'] = true;
		echo json_encode($data);
		exit(1);
        
        
        
    }
    
    
    
    
    function shipment_invoice_generate()
    {
        
        $this->load->model('Accounting_model');
        $this->load->model('Order_model');
        //$this->db->insert('pos_test',array('content'=>json_encode($_POST)));
        
    
        $data['CarrierType'] =  $this->input->post('CarrierType');
        $data['CarrierId1'] =  $this->input->post('CarrierId1');
        $data['BuyerIdentifier'] =  $this->input->post('invoiceCode');
        $data['NPOBAN'] =  $this->input->post('NPOBAN');
		$data['title'] =  $this->input->post('title');
        $data['comment'] =  $this->input->post('comment');
        $data['shopName'] = $this->input->post('shopName');
        $data['orderNum'] = $this->input->post('orderNum');;
        $shipmentID = $this->input->post('shipmentID');
        $invoice_type = $this->input->post('invoice_type');
        $data['email'] = $this->input->post('email');;
        $data['shipment_invoice'] =  $this->input->post('shipment_invoice');;
        
        
        
        if($invoice_type=='EC')
        {
            $ECOrder = $this->Order_model->getECOrderInf(0,$shipmentID);
         
            $product = $this->Order_model->getOrderDetailByID($ECOrder['orderID']);
            
            
            
             $i = 1;
            $total = 0;
            foreach($product as $key=>$row)
            {

                    $record[] = 
                    array(
                        'Description'=> $row['ZHName'],
                        'Quantity'=> $row['buyNum'],
                        'UnitPrice'=> $row['sellPrice'],
                        'Amount'=>$row['buyNum']*$row['sellPrice'],
                        'SequenceNumber' => str_pad($i++,3,0,STR_PAD_LEFT)
                    );
                    $total+= $row['buyNum']*$row['sellPrice'];






            }
       
            $data['total'] = $total;
            $data['record'] = $record;
            $result['invoiceNumber']  = $this->Accounting_model->generateInvoice($data,3);

                $datain['ECID'] = $shipmentID;
                    $datain['invoice'] =  $result['invoiceNumber'];
                    $datain['price'] = $total;
                    $datain['date'] = date("Y-m-d");;
                    $this->db->insert('pos_ec_invoice',$datain);
            $result['result'] = true;
            $result['shipmentID'] = $shipmentID;
            $result['shopID'] =666;
            $result['total'] = $total;
        
             if($result['invoiceNumber']!='')
                 {
                        $orderID = substr($ECOrder['ECOrderNum'],2);
                          $t =  $this->paser->post($this->data['martadmDomain'].'receipt/modify_invoice_num',array('receiptNum'=>$result['invoiceNumber'],'receiptdate'=>$datain['date'],'orderid'=>$orderID),false);
   
                 
                }      

         }
        else
        {
                 $order = $this->Order_model->getShipmentInf($shipmentID);
        
        
        if($data['shipment_invoice']==0)
        {
            $data['product'][0] = array(
                'ZHName'  => '桌遊一批',
                'OSBANum' =>  1,
                'purchasePrice' =>$order['total']
            
            );
            
        }
        else $data['product'] = $this->Order_model->getShipmentDetailByID($shipmentID,1);
        
		
	;
        $i = 1;
        $total = 0;
		foreach($data['product'] as $key=>$row)
		{
			
				$record[] = 
				array(
					'Description'=> $row['ZHName'],
                    'Quantity'=> $row['OSBANum'],
					'UnitPrice'=> $row['purchasePrice'],
					'Amount'=>$row['OSBANum']*$row['purchasePrice'],
                    'SequenceNumber' => str_pad($i++,3,0,STR_PAD_LEFT)
				);
				$total+= $row['OSBANum']*$row['purchasePrice'];
					
				
				
				
			
			
		}
       
        $data['total'] = $total;
		$data['record'] = $record;
        $result['invoiceNumber']  = $this->Accounting_model->generateInvoice($data,1);
        
        	$datain['shipmentID'] = $shipmentID;
				$datain['invoice'] =  $result['invoiceNumber'];
				$datain['price'] = $total;
				$datain['date'] = date("Y-m-d");;
				$this->db->insert('pos_order_invoice',$datain);
        $result['result'] = true;
        $result['shipmentID'] = $shipmentID;
        $result['shopID'] = $order['shopID'];
        $result['total'] = $total;
        
         
        
        
        }
        
   
        
        
        
        echo json_encode($result,true);
        
    }
    
	function show_shipment_invoice()
    {
        $this->load->model('Order_model');
        $shipmentID = $this->uri->segment(3);
        $code = $this->uri->segment(4);
        
        if(md5('IlovePhantasia'.$shipmentID)==$code)
        {
            $data['order'] = $this->Order_model->getShipmentInf($shipmentID);
            $data['invoice'] = $this->Order_model->getInvoice($shipmentID);
            
		

            $this->data['js'] = $this->preload->getjs('pos_order');
            $this->data['js'] = $this->preload->getjs('pos_product_query');
            $this->data['js'] = $this->preload->getjs('jquery.tablesorter');

            $this->data['orderID'] = $shipmentID;

	
		
		  $this->load->view('order_invoice_print',$this->data);
        }
        else redirect('https://www.phantasia.tw'); 
        
        
        
    }
	
	function shipment_to_out($datain = array(),$order = array(),$json=true)
	{
		$this->load->model('Order_model');
		$shipmentID = $this->input->post('shipmentID');
		//	$json = false;
		if(empty($order))
		{
			
			$order = $this->Order_model->getShipmentInf($shipmentID);
			
			$shipmentCode = $order['shipmentCode'];
			$json = $json;
		}
		else 
        {
            $shipmentCode = $datain['shipmentCode'];
            if($shipmentID==0) $shipmentID = $order['shipmentID'];
        }
		$shopID = $order['shopID'] ;
		
        if($order['status']==4)
        {
           $datain['status'] = 2;
		$datain['shippingTime'] = date("Y-m-d H:i:s");
		if( $order['shopID']>1000)$datain['status'] = 3;
		
		$this->db->where('id',$shipmentID);
		$this->db->update('pos_order_shipment',$datain);
		$data['shop'] = $this->System_model->getShopByID( $order['shopID']);
		$product = $this->Order_model->getShipmentDetailByID($shipmentID,1);
		
        if($order['shopID']==666) $this->ec_status_update($product);  
            
		if($data['shop']['email']!='')
		{
			$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			mb_internal_encoding('UTF-8');
			if($order['type']==0)$term = '買斷商品';
			elseif($order['type']==1)$term = '寄賣商品';
			else $term = '調貨商品';
			
			$title = mb_encode_mimeheader($term.'出貨單號 s'.$order['shippingNum'].'已送達物流'  ,'UTF-8');
			$content = '親愛的'.$data['shop']['name'].':<br/>';
			$content .= '您的貨品：<br/>';
			$content .= '=========================<br/>';
			foreach($product as $row)
			{
				$content.=$row['ZHName'].'('.$row['ENGName'].') X '.$row['OSBANum']	.'<br/>';
				
			}
			$content .= '=========================<br/>';

			$content .='已於今日送達物流，物流單號為：'.$shipmentCode.'<a href="https://www.hct.com.tw/Search/SearchGoods_n.aspx" target="_new">查詢物流</a><br/>';
			$content .="請於未來兩日準備收件，<br/>謝謝。<b/r>";
			$content .='幻遊天下股份有限公司(此信為系統自動發出)';
			
			
			$this->Mail_model->myEmail($data['shop']['email'],$title,$content,$headers);
		} 
            
        }
        
		
		if($json)
		{
			$data['result'] = true;
			echo json_encode($data);
			exit(1);
			
		}
	}
    
    
    function ec_status_update($product)
    {
        $this->load->model('Order_model');
        if(!empty($product))
        foreach($product as $row)
        {
            $data['ECOrder'] = $this->Order_model->getECOrderInf($row['orderID']);
             
            
                $this->db->where('ECID',$data['ECOrder']['ECID']);
                $this->db->update('pos_ec_order',array('ECstatus'=>2,'updateTime'=>date('Y-m-d H:i:s')));
            
                         $orderid = strstr($data['ECOrder']['ECOrderNum'], 'EC');
                        $orderid = substr($orderid,2);
                          $datain = array(
                            'orderid'=>$orderid,
                            'type'   =>'ts',
                            'mstatus'=>2//貨物運送中
                        )  ;
                            
                          $this->paser->post($this->data['martadmDomain'].'receipt/modifydeliver',$datain,true);;
            
            
            
        }
        
        
        
    }
	
	function shipment_update()
	{
		$this->load->model('Order_model');
		$this->load->model('Product_model');
		
		$shipmentID = $this->input->post('shipmentID');
		$shipment_type = $this->input->post('shipment_type');
		$shipment_status = $this->input->post('shipment_status');
		$shipmentComment = $this->input->post('shipmentComment');
        $assignDate = $this->input->post('shipment_assignDate');
        $assignT = $this->input->post('shipment_assignT');
		$shipmentCode= $this->input->post('shipmentCode');
		$shipmentFee= $this->input->post('shipmentFee');
		$receiver= $this->input->post('receiver');
		$address= $this->input->post('address');
		$phone= $this->input->post('phone');
        $CarrierType= $this->input->post('CarrierType');
        $CarrierId1= $this->input->post('CarrierId1');
        $NPOBAN= $this->input->post('NPOBAN');
        
        
		$addressID= $this->input->post('addressID');
        $shipment_payway = $this->input->post('shipment_payway');
        $invoiceDetail = $this->input->post('shipment_invoice');
        $comID= $this->input->post('comID');
        $email= $this->input->post('email');
		$order = $this->Order_model->getShipmentInf($shipmentID);
		
		
		if($addressID==0)	$addressID = $this->Order_model->orderAddress($receiver,$address,$phone,$order['shopID'],$comID,$CarrierType,$CarrierId1,$NPOBAN,$email);

		if($order['status']!=2&&$order['status']!=3)
		{
			
			$datain = array(
				'type'    => $shipment_type ,
                'payway'    => $shipment_payway ,
				'status'  =>$shipment_status ,
				'shipmentComment'  =>$shipmentComment ,
				'shipmentCode'  =>$shipmentCode,
				'shipmentFee'   =>$shipmentFee,
				'addressID'      =>$addressID,
                'invoiceDetail'=>$invoiceDetail,
                'assignDate'   => $assignDate,
                'assignT'     => $assignT
			);
			
			if($shipment_status==2) 
			{
				$this->shipment_to_out($datain,$order);
				
			}
			else
			{
				$this->db->where('id',$shipmentID);
				$this->db->update('pos_order_shipment',$datain);
			}
			
		}
		$product = $this->Order_model->getShipmentDetailByID($shipmentID);
		
		if($order['status']==0)
		{
			$orderID = 0;
			foreach($product as $row)
			{
				$datain = array();
				if($row['buyNum']>$row['sellNum'])	
				{
					$inserData = array(
						'orderID'  => $row['orderID'],
						'productID'=> $row['productID'],
						'buyNum'   => $row['buyNum'] - $row['sellNum'],
						'sellNum'  =>0,
						'sellPrice'=> $row['sellPrice'],
						'comment'  =>$row['comment'].'(貨量不足)',
						'status'   => 0
					
					);
					$this->db->insert('pos_order_detail',$inserData);
					$datain['buyNum'] = $row['sellNum'];
				}
				$datain['status'] = 1;
				$this->db->where('id',$row['rowID']);
				$this->db->update('pos_order_detail',$datain);
				$this->Product_model->updateNum($row['productID'], -$row['sellNum'],0);
			    $time  = getdate();
			
				
			    if($shipment_type==1)
				{
					$avgCost = $this->Product_model->getAvgCost($row['productID']);
					$this->Order_model->updateConsignment($order['shopID'] ,$row['productID'],$row['sellNum'],$row['sellPrice'],$time['year'],$time['mon'],true,$avgCost);
				
				}
				//送出為寄賣
				

				if($orderID==0) $orderID = $row['orderID'];
				else if($orderID!=$row['orderID'])
				{
					//do update thing
					$this->Order_model->checkAllProductStatus($orderID);
					$orderID = $row['orderID'];
				}
				
			}
			//for the last
			$this->Order_model->checkAllProductStatus($orderID);
			
			
		}
		$data['result'] = true;
		echo json_encode($data);
		exit(1);			
		
		
		
	}
	function shipment_delete()
	{
		$shipmentID = $this->input->post('shipmentID');
		$order = $this->Order_model->getShipmentInf($shipmentID);
         
        $time = getdate();
        if($order['type']==1)
        {
            $ret = $this->Order_model->getShipmentDetailByID($shipmentID);
            foreach($ret as $row)
            {
					$this->Order_model->updateConsignment($order['shopID'] ,$row['productID'],-$row['sellNum'],$row['sellPrice'],$time['year'],$time['mon'],true,$row['eachCost']);
                
                //$this->db->insert('pos_test',array('content'=> json_encode($row)));
            }
                
            
            
            
            
        }
		if($order['status'] == 0 )
		{
				
			$this->db->where('id',$shipmentID);
			$this->db->delete('pos_order_shipment');
							
			$this->db->where('shipment',$shipmentID);
			$this->db->delete('pos_order_shipment_detail');
            
		}
     
	}
	
	function get_shipment_list()
	{
		$this->load->model('Order_model');
		$shopID = $this->input->post('shopID');
		$arive = $this->input->post('arive');
		$fromDate = $this->input->post('fromDate');
		$toDate = $this->input->post('toDate');
		
		$offset = $this->input->post('offset');
		$num = $this->input->post('num');
		if(isset($_POST['orderType'])) $orderType = $_POST['orderType'];
		else $orderType = 2;

		if($this->data['shopID']!=0) $shopID  = $this->data['shopID'];
	
		$data['shipmentList'] = $this->Order_model->shipmentListHandle($shopID,$offset,$num,$arive,$orderType,$fromDate,$toDate);
		$data['result'] = false;
		if(!empty($data['shipmentList']))$data['result'] = true;
		echo json_encode($data);
		exit(1);	
		
		
	}
	function consignment_delete()
	{
		$shopID = $this->input->post('shopID');
		$productID = $this->input->post('productID');
		$year = $this->input->post('year');
		$mon = $this->input->post('mon');
		$sql = "SELECT * FROM pos_consignment_amount WHERE shopID = $shopID and productID = $productID and year(time)= '$year'  and month(time) = $mon";
		$query = $this->db->query($sql);
		$ret = $query->row_array();
		if($ret['num']>0) $result['result'] = false;
		else
		{
			$sql = "DELETE FROM pos_consignment_amount WHERE shopID = $shopID and productID = $productID and year(time)= '$year'  and month(time) = $mon";
			$query = $this->db->query($sql);
			$result['result'] = true;
		}
        $this->db->where('productID',$productID);
        $this->db->where('shopID',$shopID);
        $this->db->where('deleteToken','0000-00-00');
        $this->db->update('pos_consignment',array('deleteToken'=>date("Y-m-d") ));
		echo json_encode($result);
		exit(1);			
	}
	
	function dump_finish()
	{
		$orderID = $this->input->post('orderID');
		$this->db->where('id',$orderID);
		$this->db->update('pos_order_shipment',array('status'=>3,'arriveTime'=> date("Y-m-d H:i:s")));//status for get product
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
		
	}
	function combine()
	{
		$this->load->model('Order_model');
		$orderIDList = $this->input->post('orderIDList');
		$order_type = $this->input->post('order_type');
		$orderIDArray  = explode(',',$orderIDList);
		sort($orderIDArray);
		$i = 0;
		$orderID = $orderIDArray[0];

		$orderMaster = $this->Order_model->getOrderInf($orderID);
		$comment=$orderMaster ['orderComment'];
		foreach($orderIDArray as $row)
		{
			$i++;
			if($i==1)	continue;
			$rowOrder = $this->Order_model->getOrderInf($row);
			if($rowOrder['shopID']==$orderMaster['shopID'])
			{
				$rowOrderDetail = $this->Order_model->getOrderDetailByID($orderID);
				foreach($rowOrderDetail as $col)
				{
					$ret = $this->Order_model->orderProductDetail($row,$col['productID']);					
					if($ret!=false) 
					{
						
						$this->db->where('orderID',$orderID);
						$this->db->where('productID',$ret['productID']);
						$this->db->set('sellNum','sellNum+'.$ret['sellNum'],false);
						$this->db->set('buyNum','buyNum+'.$ret['buyNum'],false);
						$this->db->update('pos_order_detail');
						
						$this->db->where('orderID',$row);
						$this->db->where('productID',$ret['productID']);
						$this->db->delete('pos_order_detail');					
					
												
					}
					
				}
				$this->db->where('orderID',$row);
				$this->db->update('pos_order_detail',array('orderID'=>$orderID));



				$this->db->where('id',$row);
				$this->db->delete('pos_order');
				$comment.='<br/>'.$rowOrder['orderComment'];
			}
				
		}
		

		$product = $this->Order_model->getOrderDetailByID($orderID);
		$total = 0 ;
		foreach($product as $row)
		{
				
			$total += $row['sellPrice']*$row['sellNum'];
			
		}
		
		$datain = array(

			'total'   =>$total,
			'type'    =>$order_type,
			'orderComment'=>$comment
		
		);		
		$this->db->where('id',$orderID);
		$this->db->update('pos_order',$datain);
		
		$data['result'] = true;
		$data['orderID'] = $orderID;
		echo json_encode($data);
		exit(1);	
	}
	
	function get_address()
	{
		$id = $this->input->post('id');
		$this->db->where('id',$id);
		$query = $this->db->get('pos_order_address');
		$data['record'] = $query->row_array();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
	}
	
	function get_order()
	{	
		$this->load->model('Order_model');
		$orderID = $this->input->post('orderID');
		$orderNum = $this->input->post('orderNum');
		
		$order = $this->Order_model->getOrderInf($orderID,$orderNum);
	
		$data['order'] =$order;
        
        $data['ECOrder'] = $this->Order_model->getECOrderInf($orderID);
		//$data['product'] = $this->Order_model->getAttachConsignment($order['shopID']);
		
		$data['order'] ['statusID'] = $order['status'];
		$data['order'] ['status'] = $this->Order_model->changeOrderStatus($order['status']);
		$data['total'] = $order['total'];
		$data['product'] = $this->Order_model->getOrderDetailByID($order['id']);
		$data['address'] =  $this->Order_model->getOrderAddress($order['shopID']);
		//$data['product'] = $this->Order_model->upDiscountTest($data['product'],$order['shopID'],'each',&$data['total']);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
	}
    
    
    
    
	function product_delete_apply()
	{
		$this->load->model('System_model');
		$shopInf = $this->System_model->getShopByID($this->data['shopID']);
		if($shopInf['cashType']==1)
		{
			$this->load->model('Product_model')	;
			$this->load->model('Order_model');
			$orderID = $this->input->post('orderID');
			$id = $this->input->post('id');
			$this->db->where('orderID',$orderID);
			$this->db->where('id',$id);
			$this->db->update('pos_order_detail',array('applyDelete'=>1));
			$data['result'] = true;
		}
		else $data['result'] = false;
		
		echo json_encode($data);
		exit(1);	
		
	}
	function product_delete()
	{
		$this->load->model('Product_model')	;
		$this->load->model('Order_model')	;
		$orderID = $this->input->post('orderID');
		$id = $this->input->post('id');
		$order = $this->Order_model->getOrderInf($orderID);
		if($order['magic']>0)$data['result'] = false;
		else
		{
			$rowProduct = $this->Order_model->orderProductDetailByID($orderID,$id);
		//if(isset($rowProduct['sellNum'])&&$rowProduct['sellNum']!=-1&&$order['status']>=2)$this->Product_model->updateNum($rowProduct['productID'],$rowProduct['sellNum'],0,0);
			$this->db->where('orderID',$orderID);
			$this->db->where('id',$id);
			//$this->db->delete('pos_order_detail');
			$this->db->update('pos_order_detail',array('status'=>-1));
			
			
			$orderdata = $this->Order_model->getOrderDetailByID($orderID);
			$this->Order_model->checkAllProductStatus($orderID);
			$this->Order_model->countTotal($orderID);
			if(count($orderdata )==0)
			{
				$this->db->where('id',$orderID);
				$this->db->delete('pos_order');
				
			}
			$data['result'] = true;
		}
		echo json_encode($data);
		exit(1);	
		
	}
	function order_address_update()
    {
        $this->load->model('Order_model')	;
        $orderID = $this->input->post('orderID') ;
        
        $address = $this->input->post('address') ;
        $phone = $this->input->post('phone') ;
        $receiver = $this->input->post('receiver') ;
        $comID = $this->input->post('comID') ;
        $CarrierType = $this->input->post('CarrierType') ;
        $CarrierId1 = $this->input->post('CarrierId1') ;
        $NPOBAN = $this->input->post('NPOBAN') ;
        $email = $this->input->post('email') ;
        
        if($email==0) $email = '';
        $order = $this->Order_model->getOrderInf($orderID);    
        $this->db->insert('pos_test',array('content'=>json_encode($_POST)));
     $shopID = $order['shopID'] ;
        $addressID = $this->Order_model->orderAddress($receiver,$address,$phone,$shopID,$comID,$CarrierType,$CarrierId1,$NPOBAN,$email);
        $this->db->where('id',$orderID);		
		$datain = array('addressID'=>$addressID);
        $this->db->update('pos_order',$datain);
        $data['result'] = true;
        echo json_encode($data);
		exit(1);
    }
    
    
    
	function order_update()
	{
		$i = 0;$j=0;
		
		$num  = count($_POST);
		$product = array();
	
		
		foreach($_POST as $row)
		{
			
			if($i==0) $orderID = $row;
             else if($i==($num-1)) $NPOBAN = (string)$row;
             else if($i==($num-2)) $CarrierId1 = (string)$row;
            else if($i==($num-3)) $CarrierType = (string)$row;
             else if($i==($num-4)) $email = (string)$row;
            else if($i==($num-5)) $comID = (string)$row;
			else if($i==($num-6)) $phone = (string)$row;
			else if($i==($num-7)) $address = (string)$row;
			else if($i==($num-8)) $receiver = $row;
			else if($i==($num-9)) $receiverID = $row;
			else if($i==($num-10)) $status = $row;
			else if($i==($num-11)) $type = $row;
			else if($i==($num-12)) $orderComment = $row;
			else
			{
				switch($i%4)
				{
					case 1:
						$product[$j]['sellPrice'] = $row;
					break;
					case 2:
						$product[$j]['productID'] = $row;
					break;
					case 3:
						$product[$j]['num'] = $row;
					break;	
					case 0:
						$product[$j++]['comment'] = $row;
					break;					
					
				}	
				
				
			}
			
			
			
			$i++;
		}
		

		$this->load->model('Product_model')	;
		$this->load->model('Order_model')	;
        $this->load->model('Cs_order_model');	
		$order = $this->Order_model->getOrderInf($orderID);

		$addressID = $this->Order_model->orderAddress($receiver,$address,$phone,$order['shopID'],$comID,$CarrierType,$CarrierId1,$NPOBAN,$email);
		
     
        
        
		$orderStatus = $order['status'];
		if($orderStatus==2)
		{//表送達物流
			$data['result'] = true;
			echo json_encode($data);
			exit(1);		
		}
		$total =  0 ;
	
		$orderDetail = array();
		$shopData = $this->System_model->getShopByID($order['shopID']);
        $productList = array();
		foreach($product as $row)
		{
			//$rowProduct = $this->Order_model->orderProductDetail($orderID,$row['productID']);
			$productList[] = $row['productID'];
			$rowProduct = false;//電商允許重複
            
			//$sellPrice = $this->Order_model->concessionPrice($order['shopID'],$row['productID'],0,$row['num']);
			
			if($rowProduct!=false)
			{
					
					if($rowProduct['sellNum']==-1) $rowProduct['sellNum'] = 0; //表初始狀態
					$this->db->where('orderID',$orderID);
					$this->db->where('productID',$row['productID']);
					$this->db->update('pos_order_detail',array('sellNum'=>$row['num'],'sellPrice'=>$row['sellPrice'] ,'comment'=>$row['comment']));
					$total += $row['num'] * $row['sellPrice'];
			}
			else
			{
				
				//check suit and allocate in function
				if($this->Order_model->suitAllocate($row,$shopData,$orderID,$total,0))
				{
					
					// do nothing
					
				}
				else
				{
					
				
				
				
				$product = $this->Product_model->chkProductByProductID($row['productID']);
				//分配貨品
				$orderRemainNum = $this->Order_model->getProductNumExceptOrder($row['productID'],0);
				if($orderRemainNum>$row['num']) $sellNum = $row['num'];
				else if ($orderRemainNum>0)$sellNum=$orderRemainNum;
				else $sellNum = 0;
				//==							
					$datain = array(
						'buyNum' => $row['num'],
						'sellNum'  => $sellNum,
						'sellPrice' =>$row['sellPrice'],
						'orderID'  => $orderID,
						'productID' => $row['productID'],
						'comment' => $row['comment'],
						
						
					);
					$this->db->insert('pos_order_detail',$datain);
					$orderDetail[] = $datain;
			
					$total += $row['num'] * $datain['sellPrice'];
                    
                  
					}
			}
			 $time  = getdate();
			//change status from 1( order_send_out )to  4(order_finish) or 2
		
		}
           $this->Cs_order_model->examRemainNum($productList);
	          
      //魔法風雲會測試
		$magicStatus = $this->Order_model->magicDiscountTest($orderDetail,$order['shopID'],'buyNum');
   
        $orderDetail = $this->Order_model->getOrderDetailByID($orderID);
       $ptcgTest = $this->Order_model->ptcgTest($orderDetail);
       
        
        $str = json_encode($product);
	
		$this->db->where('id',$orderID);		
		$datain = array('status'=>$status,'total'=>$total,'type'=>$type,'orderComment'=>$orderComment,'addressID'=>$addressID);
         if($ptcgTest) $datain['orderComment'].='寶可夢訂單';
        
		if($status==2) 
		{
			if($order['shopID']>1000)$datain['status'] = 3;
			$datain['shippingTime'] = date("Y-m-d H:i:s");
		}
		else $datain['shippingTime'] =0;
		$this->db->update('pos_order',$datain);		
		
		if($status==2||$status==4)
		{
			//$this->Order_model->orderToShipment($orderID);	
			;
		}
		
        
 
		
		//有買魔風的狀態
        if($magicStatus>0 )
        {
           
            $this->magic_order_receipt($order['shopID'],$orderDetail,$order,'phantasia.pm@gmail.com');//寄出對帳單。。
        }
        
        // $magicStatus = 3 or 4 表可以直接下單到貨
		if($magicStatus==3 ||$magicStatus==4 || ($magicStatus>0&&$this->data['shopID']==0))
		{  
			$this->db->insert('pos_magic_order',array('orderID'=>$orderID));	

		}
        
        if($ptcgTest) {
        
            
            $this->magic_order_receipt($order['shopID'],$orderDetail,$order,'phantasia.pm@gmail.com','寶可夢');//寄出對帳單。
             $this->db->insert('pos_ptcg_order',array('orderID'=>$orderID));
        }
        
		$data['orderID'] = $orderID;
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	function consignmentDelete()
	{
			
		
		
	}
	
	function consignmentt_test()
	{
		$this->load->model('Order_model');
		$shopID = 17; $year = 2015; $mon = 2;
 		$productID = 8882584;
 		$r =  $this->Order_model->getConsignment($shopID,$year,$mon,$productID,$query=0);	
		//print_r($r);
	}
	
	function get_consignment()
	{
	
		$this->load->model('Order_model');
		$this->load->model('System_model');
		$shopID = $this->data['shopID'];
		if($shopID ==0)$shopID = $this->input->post('shopID');
		if($shopID ==0)$shopID = $this->input->post('consignmentShopID');
		
			$year = $this->input->post('year');
		$mon = $this->input->post('mon');
		$query = $this->input->post('query');
		//$year = 2019;
        //$mon = 5;
        //$shopID = 100;
        $con = $this->Order_model->getAllConsignment($shopID);
        /*
    $nextMon = $mon+1;
        if($nextMon>12)
        {
            $nextMon = 1;
            $nextYear = $year+1;
        }
        else $nextYear =  $year;
        */
        foreach($con as $row)
        {
            
            $this->Order_model->consignmentMonData($shopID,$row['productID'],$year,$mon,$row['timeStamp']);
            //資料補上
            
        }
        
       
        
        
        
        
		if($query !='')$data['consignment'] = $this->Order_model->getConsignment($shopID,$year,$mon,0,$query);
		else $data['consignment'] = $this->Order_model->getConsignment($shopID,$year,$mon);
		$data['system'] = $this->System_model->getShopByID($shopID);
		if($shopID==0)$data['status'] = 1;
		else $data['status'] = $data['system']['consignmentStatus'];
		$data['shopID'] = $shopID ;
		$data['result'] = true;
		echo json_encode($data);
		exit(1);

		
		
		
	}
	function consignment_back()
	{
		$this->load->model('Order_model');
		$mon = $this->input->post('mon');
		$shopID = $this->input->post('shopID');
		$year = $this->input->post('year');
		
		$consignment = $this->Order_model->getConsignment($shopID,$year,$mon);	
		
		
		$maxNum = $this->Order_model->getMaxOrderNum();
		$addressID = $this->Order_model->getAddress($shopID);
		$type= 1 ; //for 寄賣退回
		$this->db->insert('pos_order',array('shopID'=>$shopID,'status'=>1,'orderNum'=>$maxNum+1,'orderTime'=> date("Y-m-d H:i:s"),'total'=>0,'type'=>$type,'orderComment'=>'寄賣品結算退回','addressID'=>$addressID));
		$orderID = $this->db->insert_id();
				$total = 0 ; 
				foreach($consignment as $row)
				{
					
					
					$orderDetaildatain = array(
						'orderID'=>$orderID,
						'sellPrice' =>$row['purchasePrice'] ,
						'buyNum' => -$row['num'],
						'sellNum' => -$row['num'],
						'productID' => $row['productID'],
						'comment' => '寄賣結算'
					);
					$total+=-$row['num']*$row['purchasePrice'];
					$this->db->insert('pos_order_detail',$orderDetaildatain);		
				}
				$this->db->where('id',$orderID);
				$this->db->update('pos_order',array('total'=>$total));
		$data['orderID'] = $orderID;
		$data['result'] = true;
		echo json_encode($data);
		exit(1);

	}
	
	
	
	function get_week_check()
	{
		$this->load->model('Order_model');

		$shopID = $this->input->post('shopID');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		$week = $this->input->post('week');
		
		$data= $this->week_check_data($shopID,$year,$month,$week);
		$data['checkRecord'] = $this->Order_model->getCheckRecord($shopID,$year,$month);
	
		$printToken = $this->input->post('printToken');
		$data['printToken'] =$printToken;
		$this->load->view('order_week_check',$data);
	
	}
	
	
	
	function get_month_check()
	{
			$this->load->model('Order_model');

		$shopID = $this->input->post('shopID');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		$monthFromDate = $this->input->post('monthFromDate');
		$monthToDate = $this->input->post('monthToDate');
		$check = $this->input->post('check');
		
		if($check)
		{
			$list =  $this->Order_model->shipmentListHandle($shopID,0,0,3,0,$monthFromDate,$monthToDate);
			
			$data['product'] = array();
			foreach($list as $row)
			{
				$ret = $this->Order_model->getShipmentDetailByID($row['id'],1);
				
				$data['product'] = array_merge($data['product'],$ret);
			}
		}
		else $data= $this->month_check_data($shopID,$year,$month);
			$data['checkRecord'] = $this->Order_model->getCheckRecord($shopID,$year,$month);
	
		$printToken = $this->input->post('printToken');
		$data['printToken'] =$printToken;
		$this->load->view('order_month_check',$data);
	
	}

    
	function get_consignment_record()
	{
			set_time_limit(0);
		ini_set('max_execution_time', 0);;
         ini_set("memory_limit","20480000M");
        $this->load->model('Order_model');
		$shopID = $this->input->post('shopID');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		$monthFromDate = $this->input->post('monthFromDate');
       // $monthFromDate ='2011-1-1';
		$monthToDate = $this->input->post('monthToDate');
       // $monthToDate = '2019-5-10';
       // $shopID =4;
		$check = $this->input->post('check');
        
		$data['shopID'] = $shopID;       
        $from = explode('-',$monthFromDate);
        $to = explode('-',$monthToDate);    
        $productList = array();
        $data['shipmentList'] = $this->Order_model->shipmentListHandle($shopID,0,10000,3,1,$monthFromDate,$monthToDate);
        foreach(  $data['shipmentList'] as $row)
        {
            
          $ret = $this->Order_model->getShipmentDetailByID($row['id'],1);
				
				foreach($ret as $p)
                {
                    if(isset($p['productID']))
                     {
                         if(!isset($productList[$p['productID']]))
                    {
                        
                        $productList[$p['productID']]['inf'] = $p;
                      $productList[$p['productID']]['nowNum']  = $this->PO_model-> getProductNum($shopID,$p['productID'],$monthToDate);
                         $productList[$p['productID']]['sellNum'] = 0;
                        $productList[$p['productID']]['consignmentNum'] = 0;
                    }
                    $productList[$p['productID']]['consignmentNum']+= $p['sellNum'];
                              
                                  
                                  
                                  
                      }
                   
                }
			     
            
            
            
            
        }
        
        $key = true;
        $year = $from[0];
        $month = $from[1];
        $data['from'] = $year.'-'.$month;
        $data['to'] = $to[0].'-'.$to[1];
        
      
        while($key)
        {
             
           $data['consignment'][$year.'-'.$month]= $this->month_check_data($shopID,$year,$month); 
           $month++;
           if($month>12) 
           {
               $month=1;
               $year++;
           }
            $key = false;
            if($year == $to[0])
            {
                if($month<=$to[1]) $key = true;
                
                
            }
            else if ($year < $to[0])$key = true;
                
        }
          
		//	$data['checkRecord'] = $this->Order_model->getCheckRecord($shopID,$year,$month);
	
		$printToken = $this->input->post('printToken');
		$data['printToken'] =$printToken;
        $data['productList'] = $productList;
        $data['shopID'] = $shopID;
		$this->load->view('order_consignment_record',$data);
	
	}
	function consignment_patch()
    {
        $productID = $this->input->post('productID');
        $num = $this->input->post('num');
        $shopID = $this->input->post('shopID');
     
        $this->load->model('Product_model');
        $this->load->model('Order_model');
        $datain['purchasePrice'] = $this->Order_model->concessionPrice($shopID,$productID,65,$num);
        $avgCost = $this->Product_model->getAvgCost($productID);
        $year =  date("Y");
        $month =  date("m");
        
        $this->Order_model->updateConsignment($shopID,$productID,$num,$datain['purchasePrice'],$year,$month, false,$avgCost,'依庫存記錄');
        $data['result'] = true;
        
       
		echo json_encode($data);
		exit(1);
    }
	
	function check_month_profit()
	{
			set_time_limit(0);
		ini_set('max_execution_time', 0);;
         ini_set("memory_limit","20480000M");
		$shopList = $this->System_model->getShop(false,false);
		$this->load->model('Order_model');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
	
		$i = 0 ;
		foreach($shopList as $row)
		{	
			
			$data['shopData'][$i]['sell']= $this->month_check_data($row['shopID'],$year,$month);
			$data['shopData'][$i]['checkRecord'] = $this->Order_model->getCheckRecord($row['shopID'],$year,$month);
	        $data['shopData'][$i]['shopInf'] = $row;
			$i++;
			
		}
		
		$data['year'] = $year;
		$data['month'] = $month;
		$this->load->view('order_month_profit',$data);
		
	}
	
	function check_week_profit()
	{
		$shopList = $this->System_model->getShop();
		$this->load->model('Order_model');
		$year = $this->input->post('year');
		$month = $this->input->post('month');

		$i = 0 ;
		$d = getdate(strtotime($year.'-'.$month.'-01'));
		$n = $d['wday'];
	
		
		$toDay = 0;
		$last =false;
		for($t=0;$last==false;$t++)
		{
				$fromDay = $toDay+1;
				$toDay = 1+(6-$n)+7*$t;
				if($month==1||$month==3||$month==5||$month==7||$month==8||$month==10||$month==12)
				{
					if ($toDay>=31)
					{
						 $toDay = 31;
						 $last =true;
					}
				}
				else if($month==2)
				{
					if($year%4==0)
					{
						if ($toDay>=29) $toDay=29;
									$last =true;
					}
					else if ($toDay>=28) 
					{
								$toDay=28;
								$last =true;
					}
				}
				else 
					if ($toDay>=30)
					{
						$toDay=30;
						$last =true;
					}
						
					$weekArray[] = 	$fromDay.'-'.$toDay;
				
				}
					
		
		
		foreach($shopList as $row)
		{	
			foreach($weekArray as $week)$data['shopData'][$i]['sell'][$week]= $this->week_check_data($row['shopID'],$year,$month,$week);
			$data['shopData'][$i]['checkRecord'] = $this->Order_model->getCheckRecord($row['shopID'],$year,$month);
	        $data['shopData'][$i]['shopInf'] = $row;
			$i++;
			
		}
		$data['weekArray'] = $weekArray;
		$data['year'] = $year;
		$data['month'] = $month;
		$this->load->view('order_week_profit',$data);
		
	}
	
	
	function back_price()
	{
		$this->load->model('Product_model');
		$this->load->model('Order_model');
		$this->db->join('pos_order_back','pos_order_back.id=pos_order_back_detail.backID','left');
		$query = $this->db->get('pos_order_back_detail');
		$data = $query->result_array();
		foreach($data as $row)
		{
			
			$datain['purchasePrice'] = $this->Order_model->concessionPrice($row['shopID'],$row['productID'],65,$row['num']);	
			$this->db->where('productID',$row['productID']);
			$this->db->update('pos_order_back_detail',$datain);
		}
		
	}
	function week_check_data($shopID,$year,$month,$week)
	{
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
        $this->load->model('Accounting_model');
		$data['system'] = $this->System_model->getShopByID($shopID);
		$data['product'] = $this->Order_model->getWeekCheck($shopID,$year,$month,$week);	
		//$data['product'] = $this->Order_model->upDiscountTest($data['product'],$shopID,'month');
		$data['result'] = true;
		$data['shopID'] =$shopID;
		$data['year'] =$year;
		$data['month'] =$month;
		$data['week'] =$week;
		return $data;
	
		
	}
	
	
	function month_check_data($shopID,$year,$month)
	{
		
		
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
        $this->load->model('cs_order_model');
		
		$time = getdate();
	
		if($year<$time['year']||($year==$time['year']&&$month<$time['mon']))
		{
				$dir = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/order/'.$year;
			if(!file_exists($dir) )mkdir($dir);
			 $file = $dir.'/report_'.$year.'_'.$month.'_'.$shopID.'.txt';
          
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
			else 
            {
                $creareFile = true;
                 $dir = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/order/'.$year.'-'.$month;
            
                
                if (file_exists($dir)) 
                {
                foreach (scandir($dir) as $item) 
                {  
                    if ($item == '.' || $item == '..') continue; 
                    unlink($dir . "/" . $item);  
                     
                }  
                 rmdir($dir);  
                }
                
                
                
            }
			
				
		}
		else 
		{
            $dir = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/order/'.$year.'-'.$month;
			if(!file_exists($dir) )mkdir($dir);
            
            
			 $file = $dir.'/report_'.$year.'_'.$month.'_'.$time['mday'].'_'.$shopID.'.txt';
				
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
			
			
		}
		
	
		
		$data['system'] = $this->System_model->getShopByID($shopID);
		
		
		$data['product'] = $this->Order_model->getMonthCheck($shopID,$year,$month,0);	
	//	$data['nonInvoiceProduct'] = $this->Order_model->getMonthCheck($shopID,$year,$month,0,1);//noninvoice	
		
		
		//$data['product'] = $this->Order_model->upDiscountTest($data['product'],$shopID,'month');
			
		
			
			//$data['product']  =  $this->Order_model->upDiscountTest($data['product'],'month');
			

		
		$data['consigmentProduct'] = $this->Order_model->getConsignmentMonthCheck($shopID,$year,$month,0);	
       
        if(!empty($data['consigmentProduct']))
				foreach ($data['consigmentProduct']  as $col)
				{
				
					
					$this->Order_model->updateConsignment($shopID,$col['productID'],$col['remainNum'],round($col['price']*$col['purchaseCount']/100),$time['year'],$time['mon'] ,false,$col['avgCost']);
                   
					
				}
        
        
        
		$data['consigmentErrProduct'] = $this->Order_model->getErrConsignment($shopID,$year,$month);
		
		$data['backProduct'] = $this->Order_model->getBackOrderMonthCheck($shopID,$year,$month);
		$data['adjustProduct'] = $this->Order_model->getAdjustOrderMonthCheck($shopID,$year,$month);
		$data['otherMoney'] = $this->Order_model->getOtherMoneyMonthCheck($shopID,$year,$month);
		
		
		$data['outBonus'] =  $this->Accounting_model->getOutBonus($month,$year,$shopID);
		$data['inBonus'] =  $this->Accounting_model->getInBonus($month,$year,$shopID);
		$data['inBonus'] =  $this->Accounting_model->getInBonus($month,$year,$shopID);

        
        
        	$date = $year.'-'.$month.'-0';
		$data['web'] =   $this->cs_order_model->getFinishWebShopOrder($shopID,$date);
    
        
		$data['result'] = true;
		$data['shopID'] =$shopID;
		$data['year'] =$year;
		$data['month'] =$month;
		if(isset($creareFile) &&$creareFile==true) 
		{
			$output = json_encode($data);
				$f = fopen($file,'w');
			fprintf($f,"%s",$output);
					fclose($f);		
			
		}
		
		return $data;
	
		
	}
    /*特定廠商賣出 點數回饋 20200511
	function suku()
    {
        	$this->load->model('Order_model');
		$this->load->model('Accounting_model');
        $this->load->model('cs_order_model');
        
        $year = 2020; $month = 4;$shopID = 6;
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        for($shopID = 2;$shopID<=42;$shopID++)
        {
        $data['product'] = $this->Order_model->getMonthCheck($shopID,$year,$month,0);	
        $t = 0; $r = '';
        foreach($data['product'] as $row)
        {
            
            if($row['suppliers']==21)
            {
                
               $back = 0.1;
            }
            else if($row['suppliers']==57)
            {
            
                 $back = 0.05;
            }
            else $back = 0;
            
            if($row['sellPrice']>0 &&$back!=0 &&$row['category']!='0')
            {
                 $s =$back*$row['price']*$row['sellNum'];
             $r.=$row['ZHName'].' 出貨'.$row['sellNum'].'套，定價：'.$row['price'].' '.'小計'.$row['price']*$row['sellNum'].' 回饋：'.$s.'<br/>';
                $t+=$s;
            }
                
            
        }
            
        if($t>0)
             $this->System_model->addPoint($shopID,round($t), '4月紓困專案回饋：<br/>'.$r);
        echo $t ;   
        }
    }
    */
    
    
    
	function get_suit_product()
	{
		
		$this->load->model('Order_model');
		//print_r($find);
		$find = $this->search_condition();
		$product = $this->Order_model->getSuit($find,$shopID,'get');
		
		
		
	}
	
	
	
	
	function consignment_num_check($year,$month)
	{
	   return ;
		$shopList = $this->System_model->getShop(true);
		$this->load->model('Order_model');
		foreach($shopList as $eachShop)
		{
			if($eachShop['shopID']>700) continue;
			$consigmentData = $this->Order_model->getConsignment($eachShop['shopID'],$year,$month);
			
			if(isset($consigmentData))
			foreach($consigmentData as $row)
			{
					
				if($row['consignmentNum'] > $row['nowNum']+$row['sellNum'])
				{
					
					$errRet = $this->Order_model->getErrConsignment($eachShop['shopID'],$year,$month,$row['productID']);
					
					$datain = array(
						'shopID'    	 => $eachShop['shopID'],
						'productID'		 => $row['productID'],
						'consignmentNum' => $row['consignmentNum'],
						'sellNum'        => $row['sellNum'],
						'nowNum'         => $row['nowNum'],
						'time'           =>  $year.'-'.$month.'-28 0000:00:00'
					
					);
					if(count($errRet)==0)$this->db->insert('pos_consignment_err',$datain);
					else
					{
						$this->db->where('id',$errRet[0]['id']);
						$this->db->update('pos_consignment_err',$datain);
					}
					
				}
				
				
			}
		
			$data['consigmentErrProduct'] = $this->Order_model->getErrConsignment($eachShop['shopID'],$year,$month,0,0);
			
			//自動產生錯誤捕貨清單
			$orderDetail = array();$orderTotal= 0;
			foreach ($data['consigmentErrProduct'] as $each)
			{
					$ret = $this->Order_model->getProductInOrder($each['productID'],2,$eachShop['shopID']);
					//未在其他訂單中
					if($ret==0)
					{
						$remainNum = $this->Order_model->getProductNumExceptOrder($each['productID'],0);
						$errNum = $each['consignmentNum']-$each['nowNum']-$each['sellNum'];
						if($remainNum>$errNum) $sellNum = $errNum;
						else $sellNum = $remainNum;
						
						$orderDetail[] = array(
						
								'buyNum' => $errNum,
								'sellNum'  => $sellNum,
								'sellPrice' =>round($each['sellPrice']*$each['purchaseCount']/100),
								'productID' => $each['productID']
						);
						$orderTotal+=$errNum*round($each['sellPrice']*$each['purchaseCount']/100);
					}
			}
			if(count($orderDetail)>0)
			{		
								$maxNum = $this->Order_model->getMaxOrderNum();
				$addressID = $this->Order_model->getAddress($eachShop['shopID']);
				$type= 0 ; //for monthcheck
				$this->db->insert('pos_order',array('shopID'=>$eachShop['shopID'],'status'=>1,'orderNum'=>$maxNum+1,'orderTime'=> date("Y-m-d H:i:s"),'total'=>$orderTotal,'type'=>$type,'orderComment'=>'寄賣數量錯誤，可能為店內開盒，因此以買斷形式補貨','addressID'=>$addressID));
				$orderID = $this->db->insert_id();
				
				foreach($orderDetail as $detailEach)
				{
					$detailEach['orderID'] = $orderID;
					$this->db->insert('pos_order_detail',$detailEach);		
				}
				$this->db->where('shopID',$eachShop['shopID']);
				$this->db->update('pos_consignment_err',array('ok'=>1));
				
			}
			
					
		
		
		
		}
			
		
	}
	function get_month_check_shop()
	{
		$shopList = $this->System_model->getShop();	
		foreach($shopList as $row)
		{
			if($row['cashType']!=1) continue;
			$r[] = $row;
			
		}
		$data['shopList'] = $r;
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	function month_check_prepare_imm()
	{
		$shopID = $this->input->post('shopID');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		$this->month_check_data($shopID,$year,$month);	
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	
	
	function month_check_prepare()
	{
		$p = $this->uri->segment(3);
			if(empty($p))$p = 0;
		$shopList = $this->System_model->getShop();	
		$time = getdate();
        	set_time_limit(0);
		ini_set('max_execution_time', 0);;
		$year = $time['year'];
		$month= $time['mon']-1;
        
        
        
		if($month==0)
		{
			 $year--;
			$month=12;
		}
		$i=0;
		foreach($shopList as $row)
		{
			if($row['cashType']!=1) 
            {
               
                continue;
            }
			if($i==$p)
			{
                /*
				$this->month_check_data($row['shopID'],$year,$month);	
			
                /*
				$data['consigmentAllProduct'] = $this->Order_model->getConsignment($row['shopID'],$year,$month);
				if(!empty($data['consigmentAllProduct'] ))	
				foreach ($data['consigmentAllProduct']  as $col)
				{
					
					$this->Order_model->updateConsignment($row['shopID'],$col['productID'],$col['consignmentNum'],round($col['price']*$col['purchaseCount']/100),$time['year'],$time['mon'],false,$col['avgCost']);
					
				}
              
				$data['consigmentProduct'] = $this->Order_model->getConsignment($row['shopID'],$year,$month,0);	
			
	
				if(!empty($data['consigmentProduct']))
				foreach ($data['consigmentProduct']  as $col)
				{
				
					
					$this->Order_model->updateConsignment($row['shopID'],$col['productID'],$col['remainNum'],round($col['price']*$col['purchaseCount']/100),$time['year'],$time['mon'] ,false,$col['avgCost']);
					
				}
				
				
				*/
				
				
				$p++;
				if($p%=20)redirect('/order/month_check_prepare/'.$p); 
				
			}
			$i++;		
			
		}
		echo 'done';
	}
    function consignment_num_update()
     {
        
        $time = getdate();
        
        
        	$year = $time['year'];
		    $month= $time['mon'];
            $shopID = $this->input->post('shopID');
            $productID = $this->input->post('productID');
            $num = $this->input->post('num');
    
           $this->load->model('Order_model');
            $r  = $this->Order_model->getConsignmentCost($productID,$shopID,$year,$month);
      
        	$this->Order_model->updateConsignment($shopID,$productID,$num,$r['purchasePrice'],$year,$month, false,$r['avgCost']);
            $data['result'] = true;
		echo json_encode($data);
		exit(1);
        
        
        
    }
    function consignment_repair()
	{
        $this->load->model('Order_model');
		$p = $this->uri->segment(3);
			if(empty($p))$p = 0;
		$shopList = $this->System_model->getShop();	
		$time = getdate();
        $time['year'] = $this->uri->segment(4);;
        $time['mon'] = $this->uri->segment(5);;
		$year = $time['year'];
		$month= $time['mon']-1;
		if($month==0)
		{
			 $year--;
			$month=12;
		}
		$i=0;
        
		foreach($shopList as $row)
		{
               
			if($row['cashType']!=1) 
            {
                
                continue;
            }
			if($i==$p)
			{
               // echo $row['shopID'];
             
				//$this->month_check_data($row['shopID'],$year,$month);	
				
				$data['consigmentAllProduct'] = $this->Order_model->getConsignment($row['shopID'],$year,$month);
              
				if(!empty($data['consigmentAllProduct'] ))	
				foreach ($data['consigmentAllProduct']  as $col)
				{
					
					$this->Order_model->updateConsignment($row['shopID'],$col['productID'],$col['consignmentNum'],round($col['price']*$col['purchaseCount']/100),$time['year'],$time['mon'],false,$col['avgCost']);
					
				}
				$data['consigmentProduct'] = $this->Order_model->getConsignmentMonthCheck($row['shopID'],$year,$month,0);	
			
	
				if(!empty($data['consigmentProduct']))
				foreach ($data['consigmentProduct']  as $col)
				{
				
					
					$this->Order_model->updateConsignment($row['shopID'],$col['productID'],$col['remainNum'],round($col['price']*$col['purchaseCount']/100),$time['year'],$time['mon'] ,false,$col['avgCost']);
                   
					
				}
				
				
				
				
				
				$p++;
               // exit(1);
				if($p%10==0)redirect('/order/consignment_repair/'.$p.'/'.$time['year'] .'/'.$time['mon']); 
				
			}
			$i++;		
			
		}
		echo 'done';
	}
	 function consignment_repair_by_month()
	{
        $this->load->model('Order_model');
		$shopID = $this->uri->segment(3);
			
		$shopList = $this->System_model->getShop();	
		$now = getdate();
        $time['year'] = $this->uri->segment(4);;
        $time['mon'] = $this->uri->segment(5);;
		 
		 $key = true;
		while($key) 
		{
			$year = $time['year'];
			$month= $time['mon']-1;
		if($month==0)
		{
			 $year--;
			$month=12;
		}
				$i=0;
        
 			
               $row['shopID'] = $shopID;
             
				//$this->month_check_data($row['shopID'],$year,$month);	
				
				$data['consigmentAllProduct'] = $this->Order_model->getConsignment($row['shopID'],$year,$month);
              
				if(!empty($data['consigmentAllProduct'] ))	
				foreach ($data['consigmentAllProduct']  as $col)
				{
					
					$this->Order_model->updateConsignment($row['shopID'],$col['productID'],$col['consignmentNum'],round($col['price']*$col['purchaseCount']/100),$time['year'],$time['mon'],false,$col['avgCost']);
					
				}
				$data['consigmentProduct'] = $this->Order_model->getConsignmentMonthCheck($row['shopID'],$year,$month,0);	
			
	
				if(!empty($data['consigmentProduct']))
				foreach ($data['consigmentProduct']  as $col)
				{
				
					
					$this->Order_model->updateConsignment($row['shopID'],$col['productID'],$col['remainNum'],round($col['price']*$col['purchaseCount']/100),$time['year'],$time['mon'] ,false,$col['avgCost']);
                   
					
				}
			$time['mon'] =  $time['mon']+1;
			if($time['mon']==13){ $time['year']++;$time['mon']=1;};
			if($time['year']==$now['year'] && $time['mon']>$now['mon'] ) $key = false;
			
		}
		
				
				
				
				
				
			//	$p++;
               // exit(1);
		//if($p%10==0)redirect('/order/consignment_repair/'.$p.'/'.$time['year'] .'/'.$time['mon']); 1298
				

		echo 'done';
	}
    
	function consignment_back_view()
	{
		
		$this->data['display'] = 'consignment_back';
		$this->load->view('template',$this->data);	
	}
	
	function consignment_amount_back()
	{
		
			$shopID = $this->input->post('shopID');
			$year = $this->input->post('year');
			$month = $this->input->post('month');
			$time = getdate(mktime(0,0,0,$month-1,1,$year));
			
				$this->month_check_data($shopID,$year,$month);	
			
				
				$data['consigmentAllProduct'] = $this->Order_model->getConsignment($shopID,$time['year'],$time['mon']);
				if(!empty($data['consigmentAllProduct'] ))	
				foreach ($data['consigmentAllProduct']  as $col)
				{
					
					$this->Order_model->updateConsignment($shopID,$col['productID'],$col['consignmentNum'],round($col['price']*$col['purchaseCount']/100),$year,$month,false,$col['avgCost']);
					
				}
			
				$data['consigmentProduct'] = $this->Order_model->getConsignmentMonthCheck($shopID,$time['year'],$time['mon'],0);	
			
	
				if(!empty($data['consigmentProduct']))
				foreach ($data['consigmentProduct']  as $col)
				{
				
					
					$this->Order_model->updateConsignment($shopID,$col['productID'],$col['remainNum'],round($col['price']*$col['purchaseCount']/100),$year,$month ,false,$col['avgCost']);
					
				}
			$data['time']	= getdate(mktime(0,0,0,$month+1,1,$year));
			$data['result'] = true;
		echo json_encode($data);
		exit(1);	
				
			
		
		
		
		
		
	}
	
	
	
	
	
	
	function week_check_auto_send()
	{
		$p = $this->uri->segment(3);
			if(empty($p))$p = 0;
		set_time_limit(0);
		ini_set('max_execution_time', 0);;
		$shopList = $this->System_model->getShop();
		$this->load->model('Order_model');
		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
		
		$time = getdate();
		$year = $time['year'];
		$month= $time['mon'];
		$day = $time['mday'];
		$w = $time['wday'];
		$w = 6;
		$toDay = 6-$w+ $day ;
		$fromDay = $day -$w;
		if($fromDay<1)$fromDay = 1;
		if($month==1||$month==3||$month==5||$month==7||$month==8||$month==10||$month==12)if ($toDay>=31)$toDay = 31;
		else if($month==2)
		{
			if($year%4==0)if ($toDay>=29) $toDay=29;
			else if ($toDay>=28) $toDay=28;
		}
		else if ($toDay>=30)$toDay=30;

		$week = 	$fromDay.'-'.$toDay;	
		
		
		$totalResult = '';
		$sendingResult = '';
		$token = false;$i = 0;
		foreach($shopList as $row)
		{
			if($row['cashType']!=1) continue;
				$i++;
			if($i>2*$p && $i<=($p+1)*2)$token = true;
			else continue; 
			
			
			$data = array(
				'shopID' =>$row['shopID'],
				'year'   =>$year,
				'month'  =>$month,
				'week'   =>$week,
				'printToken' => 1	
			
			);
			$result = $this->paser->post('http://shipment.phantasia.com.tw/order/get_week_check',$data,false);
			
			$content =
			'<h2>親愛的'.$row['name'].'您好:</h2>'.
			'以下是本週('.$week.')的結報表'.
			'請於三日內完成匯款<br/>'.
			'感謝您的支持以及配合<br/>'.
			'預祝您業績蒸蒸日上<br/>'.$result.
			'匯款帳號：<br/>'.
			'兆豐國際商業銀行(017)板橋分行 '.
			'幻遊天下股份有限公司 20609013372<br/>'.
			'<br/>'.
			'幻遊天下股份有限公司';
		
			
		
			$sendingResult.=$row['name'].$row['email'].',';
		//	$totalResult .=$result;
			//echo $totalResult;
			mb_internal_encoding('UTF-8');
			$title = $row['name'].' '.$year.'-'.$month.'週結報表('.$week.')' ;
			if(!empty($row['email']))
			{
				//;
				$this->Mail_model->groupEmail($row['email'].','.'phantasia.ac@gmail.com'.','.'lintaitin@gmail.com',$title,$content,$headers);
				
			}
		
		}
		$p++;
		if($token)
		{
				if($p%19==0)  $this->paser->post_ignore('http://shipment.phantasia.com.tw/order/week_check_auto_send/'.$p,array(),false);
				else  redirect('/order/week_check_auto_send/'.$p);

		}
		else $this->week_profit_send();
	}
	function 月結()//for phantasia.tw 
	{
		
		$data = $_POST;
		$result = $this->paser->post('http://shipment.phantasia.com.tw/order/get_month_check',$data,false);
			//echo $result;
			$content =
			'<h2>親愛的'.$data['name'].'您好:</h2>'.
			'以下是本月的結報表'.
			'請於5日以前完成匯款<br/>'.
			'感謝您的支持以及配合<br/>'.
			'預祝您業績蒸蒸日上<br/>'.$result.
			'匯款帳號：<br/>'.
			'兆豐國際商業銀行(017)板橋分行 '.
			'幻遊天下股份有限公司 20609013372<br/>'.
			'<br/>'.
			'幻遊天下股份有限公司';
		

		//	$sendingResult.=$data['name'].$data['email'].',';
		//	$totalResult .=$result;
			//echo $totalResult;
			mb_internal_encoding('UTF-8');
			$title = $data['name'].' '.$data['year'].'-'.$data['month'].'月結報表'.date("Y-m-d H:i:s") ;
			$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			if(!empty($data['email']))
			{
				$this->Mail_model->myEmail($data['email'].','.'phantasia.ac@gmail.com'.','.'lintaitin@gmail.com',$title,$content,$headers);
				
			}	
		
		
	}
	

	function month_check_auto_send()
	{
        
       
		$p = $this->uri->segment(3);
			if(empty($p))$p = 0;
		set_time_limit(0);
		ini_set('max_execution_time', 0);;
		$shopList = $this->System_model->getShop(false,false);
		$this->load->model('Order_model');
		
		
		$time = getdate();
		$year = $time['year'];
		$month= $time['mon']-1;
		if($month==0)
		{
			 $year--;
			$month=12;
		}
		$this->consignment_num_check($year,$month);
		$totalResult = '';
		$sendingResult = '';
		$token = false;$i = 0;
		foreach($shopList as $row)
		{
				$i++;
			if($row['cashType']!=1 && $row['joinType']!=1 ) continue;
			
			if($i>$p*5&&$i<=($p+1)*5)$token = true;
			else continue;
			
			
			$data = array(
				'shopID' =>$row['shopID'],
				'name'   =>$row['name'],
				'email'  =>$row['email'],
				'year'   =>$year,
				'month'  =>$month,
				'printToken' => 1	
			
			);
			
			//email
	
			 $this->paser->post('http://shipment.phantasia.tw/order/month_check_email/',$data,false);
			// for use in phantasia.tw   sending long email
	
		}
		$p++;
		if($token)
		{
				 redirect('/order/month_check_auto_send/'.$p);
		}
		else $this->profit_send();
		
		
		/*
	
		$emailList = array('lintaitin@gmail.com','phoenickimo@hotmail.com','phantasia.ac@gmail.com');
	//	$emailList = array('lintaitin@gmail.com');
			$title = mb_encode_mimeheader($year.'-'.$month.'月各店家結報表明細-'.$time['mday'] ,'UTF-8');
			foreach($emailList as $row)
			{
				echo $row.','.$title.'<br/>';
				$this->Mail_model->myEmail($row,$title,	$totalResult,$headers);		
			}
			
			*/
			
			
			
			
	}
	function week_profit_send()
	{
			
		$time = getdate();
		$year = $time['year'];
		$month= $time['mon'];
		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			mb_internal_encoding('UTF-8');
	
			
			$emailList = array('lintaitin@gmail.com','phoenickimo@hotmail.com','phantasia.ac@gmail.com');
		$result = $this->paser->post('http://shipment.phantasia.com.tw/order/check_week_profit',array('year'=>$year,'month'=>$month),false);
			$title = $year.'-'.$month.'月各店家週結報表簡表-'.$time['mday'] ;
	
			foreach($emailList as $row)
			{
				echo $row.','.$title.'<br/>';
				$this->Mail_model->myEmail($row,$title,	$result,$headers);		
			}			

		
	}
	
	function shop_month_check_send()
	{
		$shopID  = $this->input->post('shopID');
		$year  = $this->input->post('year');
		$month  = $this->input->post('month');
		$row = $this->System_model->getShopByID($shopID);
			
					$data = array(
				'shopID' =>$shopID,
				'name'   =>$row['name'],
				'email'  =>$row['email'],
				'year'   =>$year,
				'month'  =>$month,
				'printToken' => 1	
			
			);
			
			//email
	
			 $this->paser->post('http://shipment.phantasia.tw/order/month_check_email/',$data,false);	
		
			$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	
	
	function profit_send()
	{
			
		$time = getdate();
		$year = $time['year'];
		$month= $time['mon']-1;
		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			mb_internal_encoding('UTF-8');
		if($month==0)
		{
			 $year--;
			$month=12;
		}
			
			$emailList = array('lintaitin@gmail.com','phoenickimo@hotmail.com','phantasia.ac@gmail.com');
		$result = $this->paser->post('http://shipment.phantasia.com.tw/order/check_month_profit',array('year'=>$year,'month'=>$month),false);
			$title = mb_encode_mimeheader($year.'-'.$month.'月各店家結報表簡表-'.$time['mday'] ,'UTF-8');
	
			foreach($emailList as $row)
			{
				echo $row.','.$title.'<br/>';
				$this->Mail_model->myEmail($row,$title,	$result,$headers);		
			}			

		
	}
	
	
	
	
	
   function order_brief_list()
   {
	   $month = $this->uri->segment(4);
	   	$year = $this->uri->segment(3);
	  	echo $this->paser->post('http://shipment.phantasia.com.tw/order/check_month_profit',array('year'=>$year,'month'=>$month),false); 
	   
	  }
	
	
	
	function consignment_send()
	{
		$this->load->model('Order_model');
		$j = 0 ;$i = 0 ;
		$product = array();
		$num = count($_POST);
		foreach($_POST as $row)
		{
			if($i==0) $shopID  = $row;
			else if($i==1) $status = $row;
			else if($num-1) $deleteStr = $row;
			else
			{
				$product[$j++]['productID'] = $row;
				
				
				
				
			}
			
			
			
			$i++;
		}
		$time = getdate();
		
		$deleteList = explode('_',$deleteStr);
		foreach( $deleteList as $row)
		{
		
			$this->db->where('shopID',$shopID);
			$this->db->where('productID',$row);
			$this->db->where('month(time)',$time['mon'],false);
			$this->db->where('year(time)',$time['year'],false);
			$this->db->delete('pos_consignment_amount');
		}
		
		foreach($product as $row)
		{
			
			$avgCost = $this->Product_model->getAvgCost($row['productID']);
			$this->Order_model->updateConsignment($shopID,$row['productID'],0,$time['year'],$time['mon'],false,$avgCost);
			
			
		}
			
		if($this->data['shopID']==0)$this->db->update('pos_sub_branch',array('consignmentStatus'=>$status));
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
		
	}
	
	function new_order_distribute()
	{
		$name = $this->input->post('name');
		$discount = $this->input->post('discount');
		$this->db->insert('pos_order_distribute',array('distributeName'=>$name,'discount'=>$discount));
		$data['result'] = true;
		echo json_encode($data);
		exit(1);			
		
	}
	
	function delete_distribute()
	{
			$this->load->model('Order_model');
		$distributeType = $this->input->post('distributeType');	
		$ret = $this->Order_model->getShopDistribute($distributeType );
		if(count($ret)>0) $data['result'] = false;
		else
		{
			$this->db->where('id',$distributeType);
			$this->db->delete('pos_order_distribute');
			$data['result'] = true;
		}
		echo json_encode($data);
		exit(1);
	}
	function update_distribute()
	{
		$this->load->model('Order_model');
		$distributeType = $this->input->post('distributeType');	
		$datain['distributeName'] = $this->input->post('distributeName');	
		$datain['discount'] = $this->input->post('distributeDiscount');	
		$datain['upDiscount'] = $this->input->post('upDiscount');	
		$datain['shippingFee'] = $this->input->post('shippingFee');	

			$this->db->where('id',$distributeType);
			$this->db->update('pos_order_distribute',$datain);
			$data['result'] = true;
		
		echo json_encode($data);
		exit(1);		
		
	}
	
	
	function get_distribute_product_list()
	{
			$this->load->model('Order_model');
		$distributeType = $this->input->post('distributeType');	
		$data['product'] = $this->Order_model->getDistributeProduct($distributeType);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	function update_distribute_product()
	{
		
		$productID = $this->input->post('productID');
		$distributeType = $this->input->post('distributeType');
		$distributeNum = $this->input->post('distributeNum');
		$distributeDiscount = $this->input->post('distributeDiscount');
		
		$this->db->where('distributeType',$distributeType);
		$this->db->where('productID',$productID);
		$this->db->delete('pos_order_rule');
		
			
			
			
				$discountList  = explode(',',$distributeDiscount);
				$numList  = explode(',',$distributeNum);
				$count = count($numList);
				for($j = 0 ; $j<$count;$j++)
				{
					if($numList[$j]!=''&&$discountList[$j]!=0)
					{
						 $this->db->where('distributeType',$distributeType);
						 $this->db->where('productID',$productID);
						 $this->db->where('num',$num);
						 $query =  $this->db->get('pos_order_rule')	;
						 if($query->row_nums()==0)$this->db->insert('pos_order_rule',array('productID'=>$productID,'distributeType'=>$distributeType,'num'=>$numList[$j],'discount'=>$discountList[$j]));
						
						
					}
				}
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
	}
	
	
	
	
	function get_order_distribute()
	{
		$this->load->model('Order_model');
		$data['distribute'] = $this->Order_model->getOrderDistribute();
		$i = 0;
		foreach($data['distribute'] as $row)
		{
			if(!empty($row['upDiscount']))
			{
				$discount = explode('-',$row['upDiscount']);
				
			
				foreach($discount as $each)
				{
				
					if(!empty($each))
					{
						$upDiscount = explode(',',$each);
				
						$data['distribute'][$i]['upDiscountList'][] = $upDiscount;
					}
				}
			}
			$i++;
		}
		
		
		$data['result']	 =true;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	function get_other_shop()
	{
		$this->load->model('Order_model');
		$data['otherShop'] = $this->Order_model->getOtherShop($this->input->post('shopID'));
		$data['result']	 =true;
		echo json_encode($data);
		exit(1);			
	}
	function new_other_shop()
	{
		$this->load->model('Order_model');
		if($this->data['shopID']!=0) $data['result'] = false;
		else
		{
			$maxID = $this->Order_model->getMaxShopID();
			$name = $this->input->post('name');
			$shopID = $maxID+1;
			$this->db->insert('pos_sub_branch',array('shopID'=>$shopID,'name'=>$name));
		
			$data['result'] = true;;
			
			
		}
		echo json_encode($data);
		exit(1);			
	}
	
	function edit_shop_name()
	{
		$this->load->model('Order_model');
		
		
		if($this->data['shopID']!=0) $data['result'] = false;
		else
		{
			$shopID = $this->input->post('shopID');
			$name = $this->input->post('name');
			$discount = $this->input->post('discount');
			$distributeType = $this->input->post('distributeType');
			$shipOut = $this->input->post('shipOut');
            $showPrice = $this->input->post('showPrice');
            if($shopID<1000)$jointype = $this->input->post('jointype');
            else $jointype = 0; 
			$this->db->where('shopID',$shopID);
			$this->db->update('pos_sub_branch',array('name'=>$name,'discount'=>$discount,'distributeType'=>$distributeType,'shipOut'=>$shipOut,'jointype'=>$jointype,'showPrice'=>$showPrice));
			$datain['address'] = $this->input->post('address');
			$datain['contactPerson'] = $this->input->post('contactPerson');
			$datain['phone'] =  $this->Order_model->phoneForm($this->input->post('phone'));
			$datain['fax'] =$this->Order_model->phoneForm($this->input->post('fax'));
			$datain['invoiceType'] = $this->input->post('invoiceType');
			$datain['invoiceByShip'] = $this->input->post('invoiceByShip');
            
			
            $datain['shipComment'] = $this->input->post('shipComment');
            $datain['assignTime'] = $this->input->post('assignTime');
          
            $datain['shipInterval'] = $this->input->post('shipInterval');
			$datain['address'] = $this->input->post('address');
			$datain['shopID'] = $this->input->post('shopID');
			$datain['comID'] = $this->input->post('comID');
				$data['email'] = $this->input->post('email');
		
			$this->db->where('shopID',$shopID);
			$query = $this->db->get('pos_sub_branch_inf');
			if($query->num_rows()>0)
			{
				$this->db->update('pos_sub_branch_inf',$datain,array('shopID'=>$shopID));
			}
			else $this->db->insert('pos_sub_branch_inf',$datain);
		
		
				$this->db->where('shopID',$shopID);		
		$this->db->update('pos_sub_branch',$data,array('shopID'=>$shopID));
			$data['result'] = true;;
			
			
		}
		echo json_encode($data);
		exit(1);					
	}
	function get_recent_order()
	{
		$this->load->model('Order_model');
		if($this->data['shopID']!=0) $data['result'] = false;
		else
		{
			$shopID = $this->input->post('shopID');

 			$order = $this->Order_model->getOrderList($shopID,0,1,-2,0);
	 		if(isset($order[0]))$data['order'] = $order[0] ;
			else $data['order'] = array('orderTime'=>'');
			$data['result'] = true;;
			
			
		}
		echo json_encode($data);
		exit(1);			
		
	}
	
	
	function change_change_type()
	{
		$this->load->model('Order_model');
		if($this->data['shopID']!=0) $data['result'] = false;
		else
		{
			$shopID = $this->input->post('shopID');
			$cashType = $this->input->post('cashType');
			$this->db->where('shopID',$shopID);
			$this->db->update('pos_sub_branch',array('cashType'=>$cashType));
		
			$data['result'] = true;;
			
			
		}
		echo json_encode($data);
		exit(1);			
		
	}
	
	
	function show_shop()
	{
		$this->load->model('Order_model');
		if($this->data['shopID']!=0) $data['result'] = false;
		else
		{
			$shopID = $this->input->post('shopID');
			$status = $this->input->post('status');
			$this->db->where('shopID',$shopID);
			$this->db->update('pos_sub_branch',array('show'=>$status));
		
			$data['result'] = true;;
			
			
		}
		echo json_encode($data);
		exit(1);			
		
	}
	function delete_shop()
	{
		$this->load->model('Order_model');
		if($this->data['shopID']!=0) $data['result'] = false;
		else
		{
			$shopID = $this->input->post('shopID');
			$this->db->where('shopID',$shopID);
			$this->db->delete('pos_sub_branch');
		
			$data['result'] = true;;
			
			
		}
		echo json_encode($data);
		exit(1);				
	}
	function order_split()
	{
		$this->load->model('Order_model');
		$splitIDList = $this->input->post('splitIDList');
	
		$orderID = $this->input->post('orderID');
		$order_type = $this->input->post('type');
		$order = $this->Order_model->getOrderInf($orderID);
		
		//create new
		$maxNum = $this->Order_model->getMaxOrderNum();
		$this->db->insert('pos_order',array('shopID'=>$order['shopID'],'status'=>1,'orderNum'=>$maxNum+1,'orderTime'=> date("Y-m-d H:i:s"),'type'=>$order_type));
		$newOrderID = $this->db->insert_id();		
		
		//update to new
		$data['splitData'] = explode(',',$splitIDList);

		foreach($data['splitData']  as $row)
		{
			$orderRow = $this->Order_model->orderProductDetailByID($orderID,$row);
		
			$this->db->where('productID',$orderRow['productID']);
			$this->db->where('orderID',$orderID );
			$this->db->update('pos_order_detail',array('orderID'=>$newOrderID));
				
		}
		

		$product = $this->Order_model->getOrderDetailByID($newOrderID);
		$total = 0 ;
		foreach($product as $row)
		{
			if($row['sellNum']!=-1)	$total += $row['sellPrice']*$row['sellNum'];
			
			
		}
		
		$datain = array(

			'total'   =>$total,
			'type'    =>$order_type
		
		);		
		$this->db->where('id',$newOrderID);
		$this->db->update('pos_order',$datain);
		
		$data['result'] = true;
		$data['orderID'] = $orderID;
		echo json_encode($data);
		exit(1);	
		
		
		
	}
	function change_pretime()
	{
		$this->load->model('PR_track_model');
		$this->load->model('Order_model');
		$productID = $this->input->post('productID');
		$preTime = $this->input->post('preTime');
		$num = $this->input->post('num');
		$this->Order_model->changePreTime($productID,$preTime,$num);	
		$data['phantri'] = $this->PR_track_model->loadByProductID($productID);
		$data['preTime'] = 	$preTime ;
		
		$data['result'] = true;

		echo json_encode($data);
		exit(1);
		
		
	}
	function daan_pass()
	{
		
		
		$this->load->model('Order_model');
		$shopID = 2; 
		$addressID = 323;
		$data['order'] = $this->Order_model->getShipment($shopID,$addressID);
		$data['distribute'] = $this->Order_model->getShopDiscount($shopID);
		$data['order']['month'] = $this->Order_model->upDiscountTest($data['order']['month'],$shopID,'each');
		
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		foreach($data['order']['month'] as $row)
		{
			echo $row['ZHName'].','.$row['productID'].','. $row['buyNum'].','. round($row['sellPrice']/1.05,0).'<br/>';
			$postData[] = $row['productID'];
			$postData[] = round($row['sellPrice']/1.05,0);
 			$postData[]	= $row['buyNum'];
			
		
		}
//		print_r($postData);
		echo $this->paser->post('http://shipment.phantasia.com.tw/product/purchase_send',$postData,false);
		echo 'ss';
	//	productID,num,purchasePrice;
		

		
		
	}
	
	
	
	function shipment_out()
	{
		$this->load->model('System_model');
		
		if($this->data['shopID']==0)
		{
			
				$data['result'] = false;
			
		}
		else
		{
		$shopInf = $this->System_model->getShopByID($this->data['shopID']);
		
		$money = $this->input->post('money');
		$content ='您好：<br/>'.
					 '收到來自於'.$shopInf['name'].'的匯款，金額一共'.$money.'<br/>'.
					 '此訊息來自於客戶發出，請產品部向會計確認款項入帳後，安排出貨<br/>'
					 ;
				
					  
		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";


		  mb_internal_encoding('UTF-8');
		  $this->Mail_model->myEmail('phantasia.pm@gmail.com,lintaitin@gmail.com,phantasia.ac@gmail.com',mb_encode_mimeheader("已經收到來自".$shopInf['name'].'的匯款，金額一共'.$money,'UTF-8'),$content,$headers);
	$data['result'] = true;
		
		}
		

		echo json_encode($data);
		exit(1);
		
		
		
		
	}
	
	
	function shipment_view()
	{
			$this->load->model('Product_model');
		$this->load->model('Order_model');

		$shopID = $this->input->post('shopID');
		$addressID = $this->input->post('addressID');
	
		$data['addressList'] = $this->Order_model->getShipmentAddressList($shopID);
		if($addressID==0 && isset($data['addressList'][0]['addressID'])) $addressID = $data['addressList'][0]['addressID'];
		$data['order'] = $this->Order_model->getShipment($shopID,$addressID);
		
		$data['distribute'] = $this->Order_model->getShopDiscount($shopID);
		$data['order']['month'] = $this->Order_model->upDiscountTest($data['order']['month'],$shopID,'each');
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	function get_product_in_order()
	{
		$this->load->model('Order_model');
		$this->load->model('Product_model');
		$productID = $this->input->post('productID');
	
		$data['product'] = $this->Order_model->getProductInorderList($productID);
			
		$data['productInf'] = $this->Product_model->getProductStock(array('productID'=>$productID,'hide'=>1,'xclass'=>2),0);
	  
		$data['productInf']['product'][0]['martNum'] = $this->Order_model->getMartSaveNum($productID);
		$data['productInf']['product'][0]['remainNum']  = $data['productInf']['product'][0]['nowNum'] -$data['productInf']['product'][0]['martNum'];
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
	}
	
	function get_available_shop_order()
	{
		$shopID = $this->input->post('shopID');
		$this->load->model('Order_model');
		$data['availableShipment'] = $this->Order_model->getAvailableOrder($shopID);
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);			
		
	}
	
	function change_shipment_status()
	{
		$shopID = $this->input->post('shopID');
		$status = $this->input->post('status');
		$this->db->where('shopID',$shopID);
		$this->db->update('pos_sub_branch',array('shipmentStatus'=>$status));
		$data['result'] = true;
		echo json_encode($data);
		exit(1);			
				
	}
	
	/*
	order back status 
	status 0  //send  out
	status 1  //aggree back
	status 2  //reject back
	status 3  //sending back
	status 4  //get product 
	*/
	
	
	function back_or_adjust()
	{

		$i = 0;$j=0;
		foreach($_POST as $row)
		{
			if($i==0) $type= $row;
			else if($i==1) $destinationShopID = $row;
			else
			{
				switch(($i-2)%4)
				{
					case 0:
						$product[$j]['productID'] = $row;
					break;
					case 1:
						$product[$j]['num'] = $row;
					break;	
					case 2:
						 $product[$j]['reason'] = $row;
					break;	
                        
					case 3:
						$product[$j++]['comment'] = $row;
					break;						
					
					
				}	
			}
			$i++;
		}
		
	
		$this->load->model('Product_model');
		$this->load->model('Order_model');
		
		$i = 0 ;
		if($j>0)
		{
			if($type=='backProduct')$data['backID'] = $this->insertOrderBack($product,$this->data['shopID']);
			else $data['adjustID'] = $this->insertOrderAdjust($product,$this->data['shopID'],$destinationShopID);
		}

		$data['result'] = true;

		echo json_encode($data);
		exit(1);
		
		
	}
	
	private function insertOrderBack($product,$shopID)
	{
		
		$this->load->model('Product_model');
		$this->load->model('Order_model');
		$this->load->model('System_model');
			$datain = array(
			'shopID'=>$shopID,
			'status'=>0,
			'requestTime' => date("Y-m-d H:i:s")
			
			);
			$this->db->insert('pos_order_back',$datain);
			$backID = $this->db->insert_id();
			$total = 0;
			foreach($product as $row)
			{
				$purchasePrice = $this->Order_model->getBackPrice($shopID,$row['productID'],$row['num']);
				
				
				$this->db->insert('pos_order_back_detail',array('backID'=>$backID,'productID'=>$row['productID'],'num'=> $row['num'],'reason'=>$row['reason'],'comment'=>$row['comment'],'purchasePrice'=>$purchasePrice));
			}		
				$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
		$shopInf = $this->System_model->getShopByID($shopID);
		$content ='來自'.$shopInf['name'].'的退貨單，請上線上進銷存處理退貨單<br/>'.
				  '<a href="http://shipment.phantasia.com.tw/order/view">http://shipment.phantasia.com.tw/order/view</a>';
		  mb_internal_encoding('UTF-8');
		$this->Mail_model->myEmail('lintaitin@gmail.com',"來自".$shopInf['name']."的退貨單，退單編號為",$content,$headers);
		
		
		
		
		
		
		
		
		return $backID;
		
	}
	function after_back_send()
	{
		
		$backID = $this->input->post('backID');
		
		//進入問題解決中心
		$postData=
		array
		(
			'productID' => $backID,
			'shopID' => $this->data['shopID'],
			'account' =>$this->data['account'],
			'type' =>  '退貨問題',
			'w' => '我在退貨區申請了一張退貨單，請協助我審核退貨，謝謝。',
			'file' =>$this->input->post('file')
		);
		$result = $this->paser->post('http://shipment.phantasia.com.tw/phantri/problem_ask_send',$postData,false);
	}
	
        function adjustToCom($product,$fromShopID,$destinationShopID)
        {
            
                



        }
	
	
	
	 function insertOrderAdjust($product,$fromShopID,$destinationShopID)
	{
		
		$adjustWay = $this->uri->segment(3);
	
		$this->load->model('Product_model');
		$this->load->model('Order_model');
		$this->load->model('System_model');
			$datain = array(
			'fromShopID'=>$fromShopID,
			'destinationShopID' => $destinationShopID,
			'time' => date("Y-m-d H:i:s"),
			'adjustWay'	=>$adjustWay	
		
			
			);
            $adjustID =  0;
            if($destinationShopID!=0)
            {
                $this->db->insert('pos_order_adjust',$datain);
			    $adjustID = $this->db->insert_id();
            }
			$total = 0;
			$time = getdate();
			//出貨單
			
            //導入order
			$fromShopData = $this->System_model->getShopByID($fromShopID);
         
             if($destinationShopID==0) 
             {
                 $toShopID = $fromShopID;
                 $type = 0;
                 
             }
             else
             {
                 $toShopID = $destinationShopID;
                 $type = 2;
             }
         
			$addressID = $this->Order_model->getAddress($toShopID);
			$maxNum = $this->Order_model->getMaxOrderNum();
				 	$orderDatain = array(
						'status' =>2,//完成出貨
						'shopID' =>$toShopID,
						'orderTime' =>$datain['time'],
						'shippingTime' =>$datain['time'],
						'orderNum' =>$maxNum+1,
						'type'  =>$type,//調貨
						'orderComment' => '來自'.$fromShopData['name'].'的調貨',
						'addressID' => $addressID
						);
         
				 	$this->db->insert('pos_order',$orderDatain);
					$orderID = $this->db->insert_id();			
			
			
			
			
			
			
			
			$total = 0 ;
			foreach($product as $row)
			{
                 $rowProduct = $this->Product_model->getProductByProductID($row['productID'],$destinationShopID);
                $sellPrice = round($rowProduct['purchaseCount']*$rowProduct['price']/100);
				if($adjustID!=0)
                {
                   
                     
                    $this->db->insert('pos_order_adjust_detail',
                    array('adjustID'=>$adjustID,'productID'=>$row['productID'],'num'=> $row['num'],'comment'=>$row['comment'],'purchasePrice'=>$sellPrice));
                }
				//出貨品項
				 //導入order			

				
					$orderDetaildatain = array(
						'orderID'=>$orderID,
						'sellPrice' =>$sellPrice ,
						'buyNum' => $row['num'],
						'sellNum' => $row['num'],
						'productID' => $row['productID'],
						'comment' => $row['comment']
					);
                    if($adjustID==0) 
                    {
                         $orderDetaildatain['sellNum'] = -$orderDetaildatain['sellNum'];
                         $orderDetaildatain['buyNum'] = -$orderDetaildatain['buyNum'];
                    }
                        
					$this->db->insert('pos_order_detail',$orderDetaildatain); 
					$total += $sellPrice  *$row['num'];
                
                if($adjustID!=0)
				$this->Product_model->updateNum($row['productID'],-$row['num'],$fromShopID,$sellPrice);
				
				
				 //===========
				 
				//寄賣調貨問題
 				 $consignmentData = $this->Order_model->getConsignment($fromShopID,$time['year'],$time['mon'],$row['productID']);
			   if(!empty($consignmentData))
			   {
				    	
						$consignmentNowNum = $consignmentData[0]['consignmentNum']-$consignmentData[0]['sellNum'];
						//寄賣量比銷售量多時
					  	if($consignmentNowNum >0)
						{
							if($consignmentNowNum > $row['num'])
							{
								 $minus_num = $row['num'];
								 $consignmentNowNum = $row['num'];
							}
							else
							{
								 $minus_num = $consignmentNowNum ;
								
								 
								 
							}
								$this->db->where('year(time)',$time['year']);
								$this->db->where('month(time)',$time['mon']);
								$this->db->where('shopID',$fromShopID);
								$this->db->where('productID',$row['productID']);
								$this->db->set('num','num-'.$minus_num,false);
								$this->db->update('pos_consignment_amount');								 
								 if($adjustID!=0)
                                 {
                                     $this->db->where('adjustID',$adjustID);
                                     $this->db->where('productID',$row['productID']);
                                     $this->db->update('pos_order_adjust_detail',array('num'=>$consignmentNowNum ,'isConsignment'=>1,'comment'=>'此調貨品項為寄賣品'));

                                    //分拆品項
                                    if($consignmentNowNum <  $row['num'] )
                                    {	
                                                $datain = array(
                                                    'adjustID' => $adjustID,
                                                    'num'    => $row['num']-$consignmentNowNum,
                                                    'productID'=>$row['productID'],
                                                    'isConsignment'=>0,
                                                    'purchasePrice'=>$sellPrice/1.05

                                                );	
                                                $this->db->insert('pos_order_adjust_detail',$datain);								
                                    }
                                 }
							     else
                                 {
                                      $this->db->where('orderID',$orderID);
                                     $this->db->where('productID',$row['productID']);
                                     $this->db->update('pos_order_detail',array('num'=>$consignmentNowNum ,'comment'=>'此調貨品項為寄賣品'));

                                    //分拆品項
                                    if($consignmentNowNum <  $row['num'] )
                                    {	
                                                   //導入order
			                             $fromShopData = $this->System_model->getShopByID($fromShopID);
                                        $addressID = $this->Order_model->getAddress($destinationShopID);
                                        $maxNum = $this->Order_model->getMaxOrderNum();
                                                $orderDatain = array(
                                                    'status' =>2,//完成出貨
                                                    'shopID' =>$destinationShopID,
                                                    'orderTime' =>$datain['time'],
                                                    'shippingTime' =>$datain['time'],
                                                    'orderNum' =>$maxNum+1,
                                                    'type'  =>1,//寄賣
                                                    'orderComment' => '來自'.$fromShopData['name'].'的調貨',
                                                    'addressID' => $addressID
                                                    );
                                        $this->db->insert('pos_order',$orderDatain);
                                         $CorderID = $this->db->insert_id();			
			
                                        
                                        
                                        $orderDetaildatain = array(
                                            'orderID'=>$CorderID,
                                            'sellPrice' =>$sellPrice ,
                                            'buyNum' => $row['num']-$consignmentNowNum,
                                            'sellNum' => $row['num']-$consignmentNowNum,
                                            'productID' => $row['productID'],
                                            'comment' => $row['comment']
                                        );
                                        $this->db->insert('pos_order_adjust_detail',$datain);								
                                    }
                                     
                                     
                                     
                                     
                                     
                                     
                                 }
							
							
							
						}
					   
					   
				   
				} 					
				
				
			}	
			$this->db->where('id',$orderID)	;
			$this->db->update('pos_order',array('total'=>$total));
			
			$shipmentID =$this->Order_model->orderToShipment($orderID);//轉為出貨單
			
			if($adjustWay==1 ||$destinationShopID==0)
			{
				
				
				$shipmentInf = $this->Order_model->getShipmentInf($shipmentID);
				
				
					$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
				$desShopData = $this->System_model->getShopByID($destinationShopID);
				$content ='有來自'.$fromShopData['name'].'的調貨單，目標店家~:'.$desShopData['name'].'<br/>'.
				  '請協助處理調貨事宜，出貨單號s'.$shipmentInf['shippingNum'] ;
		  mb_internal_encoding('UTF-8');
			$this->Mail_model->myEmail('phantasia.pm@gmai.com,phantasia0000@gmail.com,lintaitin@gmail.com',mb_encode_mimeheader('有來自'.$fromShopData['name'].'的調貨單，目標店家~:'.$desShopData['name']."出貨單號s".$shipmentInf['shippingNum'] ,'UTF-8'),$content,$headers);	
		
				
				
			}
			
			
			
			
			
		return $adjustID;
		
	}	
	function get_boa_list()
	{
		$this->load->model('Order_model');
		$offset = $this->input->post('offset');
		$num = $this->input->post('num');
		$shopID = $this->input->post('shopID');
		$type = $this->input->post('type');
		if($this->data['shopID']!=0) $shopID  = $this->data['shopID'];
		
		$data['result'] = false;
		if($type=='Back')
		{
			$data['BOAList'] = $this->Order_model->getOrderBackList($shopID,$offset,$num);
			$i = 0;
			foreach($data['BOAList'] as $row)
			{
				$data['BOAList'][$i]['requestTime']	 = substr($row['requestTime'],0,10);
				$data['BOAList'][$i]['backTime']	 = substr($row['backTime'],0,10);
				$data['BOAList'][$i]['status'] = $this->Order_model->changeOrderBackStatus($row['status']);
				$i++;
				
			}
			if($i!=0)$data['result'] = true;
		}
		else
		{
			$data['BOAList'] = $this->Order_model->getOrderAdjustList($shopID,$offset,$num);
			$data['result'] = true;
		}
		
		
		echo json_encode($data);
		exit(1);						
		
	}
	function boa_ok()
	{
		$shopID = $this->input->post('shopID');
		$type = $this->input->post('type');
		$productID = $this->input->post('productID');
		$shutdown = $this->input->post('shutdown');
		$id = $this->input->post('id');
		$result['result'] = false;
		if($type=='back') 
		{
				
				if($productID==0)
				{			
					$this->db->where('id',$id);
					$query = $this->db->get('pos_order_back');
					$data = $query->row_array();
				}
				else
				{
							
					$this->db->where('backID',$id);
							
					$this->db->where('productID',$productID);
					$query = $this->db->get('pos_order_back_detail');
					$data = $query->row_array();
					
					
				}
				
               
            
                
				if($data['backToken']=='0000-00-00 00:00:00')
				{
					if($shutdown == 1 )
					{
						$this->db->where('id',$data['id']);
						if($productID==0) $this->db->update('pos_order_back',array('backToken'=>date("Y-m-d H:i:s")));
						else $this->db->update('pos_order_back_detail',array('backToken'=>date("Y-m-d H:i:s")));
                        $result['result'] = true;
					}
                    else
                    {
                        //critical section
                        if(strtotime(date("Y-m-d H:i:s")) - strtotime($data['backFlag'] )< 60*3)
                           {
                               
                                $result['result'] = false;
                               
                           }
                        else
                           {
                               
                               $this->db->where('id',$data['id']);
                               $this->db->update('pos_order_back_detail',array('backFlag'=>date("Y-m-d H:i:s")));
                                $result['result'] = true;
                               
                           }
                        
                        
                        
                    }
					
					
				}
				
				
				
		}
		else
		{
				if($productID==0)
				{	
					$this->db->where('id',$id);
					$query = $this->db->get('pos_order_adjust');
					$data = $query->row_array();
				}
				
				else
				{
					$this->db->where('adjustID',$id);
					$this->db->where('productID',$productID);
					$query = $this->db->get('pos_order_adjust_detail');
					$data = $query->row_array();
					
				}
				
				if($data['adjustToken']=='0000-00-00 00:00:00')
				{
					if($shutdown == 1 )
					{
						$this->db->where('id',$id);
						if($productID==0) $this->db->update('pos_order_adjust',array('adjustToken'=>date("Y-m-d H:i:s")));
						else $this->db->update('pos_order_adjust_detail',array('adjustToken'=>date("Y-m-d H:i:s")));
						
					}
                    else
                    {
                         //critical section
                       if(strtotime(date("Y-m-d H:i:s")) - strtotime($data['adjustFlag'] )< 60*3)
                           {
                               
                                $result['result'] = false;
                               
                           }
                        else
                           {
                               
                               $this->db->where('id',$data['id']);
                               $this->db->update('pos_order_adjust_detail',array('adjustFlag'=>date("Y-m-d H:i:s")));
                                $result['result'] = true;
                               
                           }
                        
                        
                        
                    }
					
				}
			
		}
			
		echo json_encode($result);
		exit(1);	
		
	}
	
	
	function get_order_back()
	{	
		$this->load->model('Order_model');
		$this->load->model('Product_model');
		$orderID = $this->input->post('orderID');
		
		$order = $this->Order_model->getOrderBackInf($orderID);
		$data['order'] =$order;
		//$data['product'] = $this->Order_model->getAttachConsignment($order['shopID']);
		
		$data['order'] ['statusID'] = $order['status'];
		$data['order'] ['status'] = $this->Order_model->changeOrderBackStatus($order['status']);
		$data['product'] = $this->Order_model->getOrderBackDetailByID($orderID);
		$i=0;
		/*
		foreach($data['product'] as $row)
		{
				 $product = $this->Product_model->getProductByProductID($row['productID'],$data['order']['shopID']);
				 
				 $data['product'][$i++]['purchasePrice'] = round($product['purchaseCount'] * $product['price']/100);
				 
		}
		*/
		
		if(isset($order['parentID']))
		while(file_exists($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/problem/'.$order['parentID'].'_'.$i.'.jpg')) $data['order']['img'][]='/pos_server/upload/problem/'.$order['parentID'].'_'.$i++.'.jpg';

		$data['result'] = true;

		echo json_encode($data);
		exit(1);		
		
	}
	function get_order_adjust()
	{
		$this->load->model('Order_model');
		$this->load->model('Product_model');
		
		
		$adjustID = $this->input->post('adjustID');
		$adjust = $this->Order_model->getOrderAdjustInf($adjustID);
		$data['adjust'] =$adjust;
		//$data['product'] = $this->Order_model->getAttachConsignment($order['shopID']);
		$data['product'] = $this->Order_model->getOrderAdjustDetailByID($adjustID);
		$i=0;
		foreach($data['product'] as $row)
		{
				 $product = $this->Product_model->getProductByProductID($row['productID'],$adjust['fromShopID']);
				 
				 $data['product'][$i++]['purchasePrice'] = round($product['purchaseCount'] * $product['price']/100);
				 
		}
		$data['result'] = true;
	
		echo json_encode($data);
		exit(1);			
	}
	
	
			
	function order_back_product_delete()
	{
		$this->load->model('Product_model')	;
		$this->load->model('Order_model')	;
		$backID = $this->input->post('backID');
		$productID = $this->input->post('productID');
		//$rowProduct = $this->Order_model->orderProductDetail($orderID,$productID);
		//if(isset($rowProduct['sellNum'])&&$rowProduct['sellNum']!=-1)$this->Product_model->updateNum($productID,$rowProduct['sellNum'],0);
		
		$this->db->where('backID',$backID);
		$this->db->where('productID',$productID);
		$this->db->delete('pos_order_back_detail');
		
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
		
	}
	function get_product_num_except_order()
	{
		$this->load->model('Order_model');
		$productID = $this->input->post('productID');
		$data['orderRemainNum'] =  $this->Order_model->getProductNumExceptOrder($productID,0);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	
		
	function order_back_update()
	{
		
		$i = 0;$j=0;
		$orderID = $this->input->post('orderID');
		$status = $this->input->post('order_status');
			
		$back_comment = $this->input->post('back_comment');		
		
		$this->load->model('Product_model')	;
		$this->load->model('Order_model')	;
		$this->load->model('System_model')	;
		$order = $this->Order_model->getOrderBackInf($orderID);
	
		//同意寄回
		if($status==1&&$this->data['shopID']==0)
		{
				$shopInf = $this->System_model->getShopByID($order['shopID'] );
			$content ='親愛的'.$shopInf['name'].'您好'.
					  '您的退貨單，編號：'.$order['id'].'已通過退貨審核，請您將產品寄回<br/>'.
					  '地址： 新北市三峽區添福里添福13-31號 幻遊天下股份有限公司收'.
					  '幻遊天下 產品部 敬上';
					  
					$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";


		  mb_internal_encoding('UTF-8');
		  $this->Mail_model->myEmail($shopInf['email'],mb_encode_mimeheader("您的退單編號為".$order['id'].'已通過審核','UTF-8'),$content,$headers);
			
		
	
		$word = '產品部已經同意您的退貨囉，請您將產品寄回，並在退貨單點選「產品已寄回」';
	
			
		}
		
		//退貨完成
		$time = getdate();
		if($status==4&&$order['status']!=4&&$this->data['shopID']==0)
		{		
		
			$word = '我們已經收到您的退貨囉！若沒有其他問題請點選結案喔!謝謝。';
			$data['product'] = $this->Order_model->getOrderBackDetailByID($orderID);
		
			$this->load->model('System_model');
					$datain = array(
					'shopID'=>994,
					'status'=>0,
					'requestTime' => date("Y-m-d H:i:s"),
					'backTime'	=>date("Y-m-d H:i:s")

					);
					$this->db->insert('pos_order_back',$datain);
					$backID = $this->db->insert_id();
					$total = 0;
			
            $datain['receiveTime'] = date("Y-m-d H:i:s");
			foreach($data['product'] as $row)
			{
				
				$purchasePrice = $row['purchasePrice'];	
				
				
				//20180103 退貨進改進994
				//$this->Product_model->updateNum($row['productID'],$row['OSBANum'],0,$purchasePrice);
				
				$this->db->insert('pos_order_back_detail',
								  array('backID'=>$backID,'productID'=>$row['productID'],'num'=> -$row['OSBANum'],'reason'=>$row['reason'],'comment'=>$row['comment'],'purchasePrice'=>0));
	
				//
				
				
			   $consignmentData = $this->Order_model->getConsignment($order['shopID'],$time['year'],$time['mon'],$row['productID']);
			   if(!empty($consignmentData))
			   {
				    	
						$consignmentNowNum = $consignmentData[0]['consignmentNum']-$consignmentData[0]['sellNum'];
						//寄賣量比銷售量多時
					  	if($consignmentNowNum >0)
						{
							if($consignmentNowNum > $row['OSBANum'])
							{
								 $minus_num = $row['OSBANum'];
								 
							}
							else
							{
								 $minus_num = $consignmentNowNum ;
								
								 
								 
							}
								$this->db->where('year(time)',$time['year']);
								$this->db->where('month(time)',$time['mon']);
								$this->db->where('shopID',$order['shopID']);
								$this->db->where('productID',$row['productID']);
								$this->db->set('num','num-'.$minus_num,false);
								$this->db->update('pos_consignment_amount');								 
								 
								 $this->db->where('backID',$orderID);
								 $this->db->where('productID',$row['productID']);
								 $this->db->update('pos_order_back_detail',array('num'=>$minus_num ,'isConsignment'=>1));
								//分拆品項
								if($consignmentNowNum <  $row['OSBANum'])
								{	
											$datain = array(
												'backID' => $orderID,
												'num'    => $row['OSBANum']-$minus_num,
												'productID' => $row['productID'],
												
												'isConsignment'=>0
											);									
											 $this->db->insert('pos_order_back_detail',$datain);
								}

							
							
							
							
						}
					   
					   
				   
				} 	
			  
			}
		}
		
		
		$datain = array('status'=>$status);
		if($status==2)$word = '此項退貨沒有審通過，請打開退單查看原因。若沒有其他問題請點選結案喔!謝謝。';
		
		
		if($status==3)
		{
			$word = '我已經將產品寄回囉，請查核。';

			$data['product'] = $this->Order_model->getOrderBackDetailByID($orderID);
		
	
			foreach($data['product'] as $row)
			{
				
				$purchasePrice = $row['purchasePrice'];	
				$this->Product_model->updateNum($row['productID'],-$row['OSBANum'],$order['shopID'],$purchasePrice);
			}
			$datain['backTime'] = date("Y-m-d H:i:s");
		}
		
		
		if($this->data['shopID']==0)$datain['comment'] = $back_comment;
		$this->db->where('id',$orderID);
		$this->db->update('pos_order_back',$datain);
		
		
		
		
			//進入問題解決中心
			
			$this->db->where('productID',$order['id']);//事實上為order backID;
			$this->db->where('type',9);
			$query = $this->db->get('pos_problem_track');
			$r = $query->row_array();
			
			if(count($r)>1)
			{
			$postData=
			array
			(
				'shopID' => $this->data['shopID'],
				'account' =>$this->data['account'],
				'type' => '回覆',
				'msg' => $word,
				'parentID' => $r['parentID']
			);
			
			
			$result = $this->paser->post('http://shipment.phantasia.com.tw/phantri/reply',$postData,false);
			}
		
				
		$data['orderID'] = $orderID;
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	function order_to_shipment()
	{
		$this->load->model('Order_model');
		$query = $this->db->get('pos_order');
		$orderList = $query->result_array();
		foreach($orderList as $row)
		{
			$this->Order_model->orderToShipment($row['id']);

			
		}
		$this->re_allocate_order();
		echo 'done！';
		
	}
	
	function re_allocate_send()
	{
        // $this->db->insert('pos_test',array('content'=> date("Y-m-d H:i:s").'a'));
		$this->load->model('Order_model');
        $this->load->model('Product_model');
        $this->load->model('Cs_order_model');
		$productID = $this->input->post('productID');
		$i = 0 ;
		$j = 0;
		$num =  count($_POST);
		foreach($_POST as $row)
		{
			
			if($i!=0)
			{
				switch($i%2)
				{
					case 1:
					$data[$j]['orderDetailID'] = $row;
					break;
					case 0:
					$data[$j++]['sellNum'] = $row;
					break;			
				}
			}
			$i++;
		}
        //$this->db->insert('pos_test',array('content'=> date("Y-m-d H:i:s").'b'));
		foreach($data as $row)
		{
		//	 $this->db->insert('pos_test',array('content'=> date("Y-m-d H:i:s").'b'.$row['orderDetailID']));
			$this->db->where('id',$row['orderDetailID']);
		//	$this->db->where('productID',$productID);  2019423 add orderDetailID to faster by taitin
			$this->db->update('pos_order_detail',array('sellNum'=>$row['sellNum']));
		}
		//	$this->db->insert('pos_test',array('content'=> date("Y-m-d H:i:s").'c'));
		$this->Cs_order_model->examRemainNum(array($productID));
       //$this->db->insert('pos_test',array('content'=> date("Y-m-d H:i:s").'d'));
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
	}
	
	
	
	function re_allocate_order()
	{
		
		$this->load->model('Order_model')	;
	
		$this->db->distinct('productID');
		$this->db->where('status',0);

		$query = $this->db->get('pos_order_detail');
		$data = $query->result_array();
		foreach($data as $row)
		{
			$this->Order_model->reAllocateOrder($row['productID']);	
			
		}
		
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	}
	
	function chk_consignment()
	{
		$this->load->model('product_model');
		$productID = $this->input->post('productID');
		$data['result']  = $this->product_model->chkConsignment($productID,$this->data['shopID']);
		echo json_encode($data);
		exit(1);	

	}
	
	function get_all_magic_status()
	{
	
		$this->db->select('*,pos_sub_branch.name as shopName');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_magic_status.shopID','left');
		$query = $this->db->get('pos_magic_status');
		$data['magicShop'] = $query->result_array();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
	}
	function magic_status()
	{
			
		$this->load->model('Order_model')	;
		$magic = $this->Order_model->magicStatus($this->data['shopID']);
		
		if(isset($magic['core'])&&$magic['core']==false)
		{
			//非core
				$six = 	77.46;
				$nosix = 8;
				$type = '非核心店家';
			
		}
		else 
		{
				$six = 	7.295;
				$nosix = 7.5;
				$type = '核心店家';
		}
		$content ='你的店家目前設定為 '.$type.'，<br/>未滿6盒則彈性出貨，補充包價格為'.$nosix.'折。滿6盒出貨直送，補充包價格為'.$six.'折，折扣將在點選確認訂單送出後調整(還可再確認一次)。';
		$content.='<br/>彈性進貨是指進貨沒有最小量。由公司總倉集單發貨，每週三中午12:00 前結單，於次週各店家出貨日出貨(所有貨品[含桌遊]須滿五千才主動出貨)';
		$content.='<br/>直送是指進貨最小為6盒補充包。由孩之寶總倉出貨，下單後約3個工作天可以到貨';

		
		/*
		if(!isset($magic['six'])||$magic['six']==false)
		{
			if(isset($magic['core'])&&$magic['core']==true) $content ='你的店家目前設定為  核心店家，彈性出貨，補充包價格為75折,折扣將在點選確認訂單送出後調整(還可再確認一次)';
			else $content ='你的店家目前設定為 非核心店家，彈性出貨，補充包價格為8折';
			$content.='<br/>彈性進貨是指進貨沒有最小量。由公司總倉集單發貨，每週三中午12:00 前結單，於次週各店家出貨日出貨(所有貨品[含桌遊]須滿五千才主動出貨)';
			
		}
		else
		{
			if(isset($magic['core'])&&$magic['core']==false) $content ='你的店家目前設定為 非核心店家，滿6盒出貨直送，補充包價格為7.746折,折扣將在點選確認訂單送出後調整(還可再確認一次)，請注意未滿6盒不予出貨';
			else $content ='你的店家目前設定為 核心店家，滿6盒出貨直送，補充包價格為7.295折,折扣將在點選確認訂單送出後調整(還可再確認一次)，請注意未滿6盒不予出貨';
			
			
		}
		*/
		
	
		$data['result'] = true;
		$data['magicStatusStr'] = $content;
		
		echo json_encode($data);
		exit(1);
	}
	function magic_paser()
	{
		$this->db->like('ZHName','魔法風雲會');
		$this->db->like('ZHName','補充包');	
		$this->db->like('ZHName','盒');
		
		
		$query = $this->db->get('pos_product');
		
		$data = $query->result_array();
		
		foreach($data as $row)
		{
			$this->db->insert('pos_magic_product',array('productID'=>$row['productID']));
		}
	}
	
	
	function get_magic_order()
	{
		
		$this->db->where('pos_magic_order.status !=',5);
		$this->db->join('pos_order','pos_magic_order.orderID = pos_order.id','left');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_order.shopID','left');
		$this->db->order_by('pos_order.orderTime');
		$query = $this->db->get('pos_magic_order');
		
		$data['magicOrder'] = $query->result_array();
		
			$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	function change_magic_type()
	{
		$datain['shopID'] = $this->input->post('shopID');
		$datain['six'] = $this->input->post('six');
		$datain['core'] = $this->input->post('core');
		$this->db->where('shopID',$datain['shopID']);
		$query = $this->db->get('pos_magic_status');
		
		if($query->num_rows()>=1)
		{
			$this->db->where('shopID',$datain['shopID']);
			$this->db->update('pos_magic_status',$datain);
		}
		else $this->db->insert('pos_magic_status',$datain);
		
		$data['result'] = true;
		
		echo json_encode($data);
		exit(1);
		
	}
	
	function search_condition()
	{
		$find['queryString'] = $this->input->post('queryString');
		$find['productNum'] = $this->input->post('productNum');
		$find['ENGName'] = $this->input->post('ENGName');
		$find['ZHName'] = $this->input->post('ZHName');
		$find['suppliers'] = $this->input->post('suppliers');
		$find['start'] = $this->input->post('start');
		$find['num'] = $this->input->post('num');
		$find['openStatus'] = $this->input->post('openStatus');
		$find['order1'] = $this->input->post('order1');
		$find['order2'] = $this->input->post('order2');
		$find['sequence1'] = $this->input->post('sequence1');
		$find['sequence2'] = $this->input->post('sequence2');
		$find['placeID'] = $this->input->post('placeID');
		$find['top150'] = $this->input->post('top150');
		$find['category'] = $this->input->post('orderCategory');
		$find['orderCondition'] = $this->input->post('orderCondition');
		$find['recommand'] = $this->input->post('recommand');
		if($this->input->post('orderType')!=-1)
		$find['type'] =  $this->input->post('orderType');;
		
		
		if($this->input->post('magicToken')==1)
		{
			$find['category']  = '';
			$find['type'] = 8 ;
		}
		
		return $find;	
	}
	function count_search()
	{
		$this->load->model('Order_model');
		//print_r($find);
		$find = $this->search_condition();
		$data['totalNum'] = $this->Order_model->countProduct($find,$this->data['shopID']);
		$data['result']  = true;
		echo json_encode($data);
		exit(1);		
		
	}
	function get_product_info()
	{
		$this->load->model('Product_model');
		$productID = $this->input->post('productID');
		
		$product = $this->Product_model->chkProductByProductID($productID);
		if($product['phaBid']!=0)
		{
			$postData = array(
			'bid'    =>$product['phaBid'],
			'productID'    =>$product['productID'],
			'licence'=>'3d38db21d2ef4d23d10c38bb0ff308cf'
			);
			echo $result = $this->paser->post('https://www.phantasia.tw/bg/pos_get_data',$postData,false);
		}
		else
		{
			$result['result'] = false;
			echo json_encode($result);
		}
		exit(1);
	}
	
	function get_product_discount()
	{
		$this->load->model('Product_model');
		$this->load->model('Order_model');
		
		$shopID = $this->input->post('shopID');
		$productID = $this->input->post('productID');
		
		$find = $this->search_condition();
		$product = $this->Order_model->getProduct(array('productID'=>$productID),$shopID,'get');
		$data['product'] = $product[0];
		$data['product']['sellPrice'] = round($data['product']['price'] *$data['product']['purchaseCount']/100);
		//$result['sellPrice'] = $this->Order_model->concessionPrice($shopID,$productID,0,0);
		$data['result'] = true;
		echo json_encode($data);
		
		
	}
	
	function get_available_num()
	{
	
		$this->load->model('Order_model');
		$productID = $this->input->post('productID');		

		$data['num'] = $this->Order_model->getAvailableNum($productID);
		$data['result'] = true;
		echo json_encode($data);
		
	}
	function get_pre_paynum()
	{
		$productID = $this->input->post('productID');	
		$this->load->model('Order_model');
		$data['preList'] = $this->Order_model->getProductInorderList($productID);
		$data['prePayList'] = $this->Order_model->getPrePayOrderByProductID($productID);
		$preNum = 0;
		foreach($data['preList'] as $row)
		{
			$preNum +=$row['buyNum'];	
			
		}
		$prePayNum = 0;
		foreach($data['prePayList'] as $row)
		{
			$prePayNum +=$row['orderNum'];	
			
		}
		$data['preNum'] = $preNum;
		$data['prePayNum'] = $prePayNum;
		
		$data['result'] = true;
	
		echo json_encode($data);
		exit(1);
	}
	
	function search_product()
	{
		$this->load->model('Order_model');
		$this->load->model('Product_model');
		//print_r($find);
		
		$find = $this->search_condition();
	
		$product = $this->Order_model->getProduct($find,$this->data['shopID'],'get');
		
        
        $this->Product_model->cardSleeveInf($product ,true);
  
		$date = getdate (mktime(0, 0, 0, date("m")+1, date("d")));
		$year = $date['year'];
		$month = $date['mon']	;
		$shopID = $this->data['shopID'];

		
		$i= 0;
		
		
		foreach($product as $row)
		{
			$comNum = $this->Order_model->getProductNumExceptOrder($row['productID'],0);
            
                
            
			if(isset($find['recommand'])&&$find['recommand']==1 &&($row['category']=='0')) continue;
			
			$data['product'][$i] =$row;
            
            
             $sameProduct = $this->Product_model->findSameProduct($row['productID']);
            if(!empty($sameProduct))
            {
                
                foreach($sameProduct as $each)
                {
                $r =  $this->Product_model->getProductInfByProductID($each['productID']);
                   
              $r['num'] = $this->PO_model->getProductNum($this->data['shopID'],$each['productID'],'2017-11-21');
                if($r['num']>0)
                $data['product'][$i]['sameProduct'][] =$r;
                } 
             
            }
            
            
		//	$data['product'][$i]['preOrderNum'] = $this->Order_model->getPreOrderNum($shopID,$year,$month,$row['productID']);

			if($comNum<=0)
			{
				$pre = $this->Order_model->getPreTime($row['productID']);
				if(!empty($pre))
				{
					
					$data['product'][$i]['preTime'] = $pre['preTime'];
					
				}
				
			}
            
            
            
			$data['product'][$i]['comNum']=$comNum;
			if($row['phaBid']!=0) $data['product'][$i]['img'] =  'https://www.phantasia.tw/phantasia/upload/bg/home/b/'.$row['phaBid'].'.jpg';
			else $data['product'][$i]['img'] = 'http://shipment.phantasia.com.tw/pos_server/upload/product/img/'.$row['productID'].'.jpg';
			 $data['product'][$i]['isConsignment']= $this->Product_model->chkConsignment($row['productID'],$this->data['shopID']);
			 
			  $data['product'][$i]['myFlow']= $this->Product_model->getMyFlow($row['productID'],$this->data['shopID']);
				$i++;		
		}
		$data['result']  = true;
		echo json_encode($data);
		exit(1);
	}
	
	function test()
	{
        $this->load->model('Order_model');
        
		
         $ret = $this->Order_model->getBackPrice(28,8882583);	
        echo $ret;
        
        echo 'OK';
	}
	function turn_discount()
	{
		
		$query = $this->db->get('pos_product_discount');
		$data = $query->result_array();
		
		//print_r($data);
		foreach($data as $row)
		{
			
			
			$datain = array(
			'discount'=>$row['discount'],
			'productID'=>$row['productID'],
			'distributeType' => 1,
			'num' => $row['num']
			)	;
			$this->db->insert('pos_order_rule',$datain);
			$datain['distributeType'] = 2;
			$this->db->insert('pos_order_rule',$datain);
			
		
		}
		
		
	}
    
    function order_check()
    {
        
        	$this->load->view('order_check',$this->data);
        
        
    }
    function order_address_list()
    {
        $this->data['IDList'] = '';
         $this->load->model('Order_model');
       if(isset($_POST['selectID']) &&  count($_POST['selectID'])>0)
       {
            
           $idArray = $_POST['selectID'];
       }
        else
        {
           
		$data['shipmentList'] = $this->Order_model->getShipmentList(0,0,100,4,2,date("Y-m-d",mktime(0, 0, 0, date("m")-1, date("d"),date("Y"))),date("Y-m-d"));
            foreach(	$data['shipmentList'] as $row)                 
            {
                
                $idArray[]=$row['id'];
                 
              }
                                                          
        
        }
     
         $this->data['showType'] = $this->input->post('showType');
          $excelData[0]['sheet'][]=array('序號(無用途)','訂單號長度限制: 20碼 請勿使用中文','收件人姓名(必填)長度限制: 20碼','收件人地址(必填) 中文限制: 50字','收件人電話長度限制: 15碼','託運備註中文限制: 50字','(商品別編號)勿填','商品數量(必填)(限數字)<100','才積重量限數字','代收貨款限數字','指定配送日期YYYYMMDD範例: 20140220    ->2月20號','指定配送時間範例:   1   (上午 -> 09~13)<br/>2   (下午 -> 13~17)3   (晚上 -> 17~20)');

   
         
        $i = 0;
        foreach( $idArray as $row)
           
        {
            $order = $this->Order_model->getShipmentInf($row);
            
            
            
        if($order['boxNum']>0)     
        {
           
            if($order['assignDate']!='0000-00-00')$arriveDate = str_replace('-','',$order['assignDate']);
            else
            {
                if($order['shipInterval']>1) $day = $order['shipInterval'];
                else $day = 1;
                $arriveDate = date("Ymd",mktime(0, 0, 0, date("m"), date("d")+$day,date("Y")));
            }
            $fee = '';
            if($order['payway']==1) $fee = $order['total'];
             $excelData[0]['sheet'][]=array(++$i,'S'.$order['shippingNum'],$order['receiver'],$order['address'],$order['phone'],$order['shipComment'],'',$order['boxNum'],$order['boxNum']*20,$fee,$arriveDate,$order['assignTime']);
            
            
        }
            
               
        }
        
        //電商宅配單  
        $key = true;

        $ec = $this->Order_model->getECShippingList();
       
    
       if(empty($ec)) $key = false;
        else foreach ($ec as $order)
            {
                
                if($order['payway']==1) $payTotal = $order['total'];
                else $payTotal = '';
             $excelData[0]['sheet'][]=array(++$i,$order['ECOrderNum'],$order['receiverName'],$order['receiverAddress'],$order['receiverPhone'],'','',$order['boxNum'],$order['boxNum']*20,$payTotal,'','');
                
              
            
            }
            
               
        
 
         
               
        
        
        
        include_once($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/PHPExcel/IOFactory.php'); 
		$objPHPExcel = new PHPExcel();
		$rowArray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
		
		$i = 0; $j=0;$k=0;
		foreach($excelData as $row)
		{
			
			if($i==0)
			{
				$objWorksheet1 = $objPHPExcel->setActiveSheetIndex($i);
			}
			else $objWorksheet1 = $objPHPExcel->createSheet();
			
			//$objWorksheet1->setTitle($row['name']);

			$objPHPExcel->setActiveSheetIndex($i);
			$i++;
			$j = 0 ;
			//$objPHPExcel->fromArray($row['sheet']);
			
			
			foreach($row['sheet'] as $col)
			{
				
				$k=0;$j++;
				foreach($col as $item) 
				{
					if(substr($item,0,1)=='$') 
					{
						$item = substr($item,1);
						$objPHPExcel->getActiveSheet()->getStyle($rowArray[$k].$j)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
					}
					$objPHPExcel->getActiveSheet()->getCell($rowArray[$k].$j)->setValue(str_replace("<br/>","\n",$item));
					
					
					$objPHPExcel->getActiveSheet()->getStyle($rowArray[$k].$j)->getAlignment()->setWrapText(true);
					$k++;
					

					
				}
				
				
			}
			for($l=0;$l<$k;$l++)$objPHPExcel->getActiveSheet()->getColumnDimension($rowArray[$l])->setAutoSize(true);
		}
        $fileName = date("Y-m-d").'shipingList';
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
		$objWriter->save($_SERVER['DOCUMENT_ROOT']."/pos_server/upload/".$fileName.".xls"); 
		
        $this->data['excelData'] = $excelData;
        $this->data['download'] = 'http://shipment.phantasia.com.tw/pos_server/upload/'.$fileName.'.xls';
        $this->load->view('order_address_list',$this->data);
        
        
    }
    
    function ship_code_back()
    {
        $this->load->model('Order_model');
        if ($_FILES["file"]["error"] > 0)
        {
            echo "Error: " . $_FILES["file"]["error"];
         }
        else{
            move_uploaded_file($_FILES["file"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/temp.csv');
 
      
            $handle = fopen($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/temp.csv','r');
            $this->load->model('Product_model');
            while(!feof($handle))
            {
                $contents = fgets($handle,1000);

                $contents_array = explode(',',$contents);
            
                if(isset($contents_array[20]))    
                {
                       /* */ 
                if(isset($contents_array[2])) 
                {
                      //訂單號碼
                    $shippingNum = strstr($contents_array[2], 'S');
                     ;
                    if($shippingNum!='')
                    {
                         $shippingNum = substr($shippingNum,1);
                        
                         $order = $this->Order_model->getShipmentInf(0,$shippingNum);
                        $order['shipmentID'] =$order['id'];
                        $datain = array();
                        $datain['shipmentCode'] = $contents_array[15];  //貨運單號 
                        //print_r($order);
                        $this->shipment_to_out($datain,$order,false);
                        
                    }
                    else
                    {
                        if($contents_array[2]!='')
                        {
                        $this->db->where('ECOrderNum',$contents_array[2]);
                        $this->db->update('pos_ec_order',array('ECstatus'=>2,'trackingNumber'=>$contents_array[15],'updateTime'=>date('Y-m-d H:i:s')));
                        }
                        
                        
                        $orderid = strstr($contents_array[2], 'EC');
                        $orderid = substr($orderid,2);
                      
                          $datain = array(
                            'orderid'=>$orderid,
                            'type'   =>'tp',
                            'transportRemarks'=>1,//新竹物流
                            'packageNumber'=>$contents_array[15],
                            'mstatus'=>2//貨物運送中
                        ) ;
                            
                          $this->paser->post($this->data['martadmDomain'].'receipt/modifydeliver',$datain,true);;
                    }
                   
                }
                      /**/
                    
                }
                else
                {
                     
                    if(isset($contents_array[12]))
                    {
                        $orderNum = str_replace('"','',substr($contents_array[12],1));
                        $arrive =  str_replace('"','',substr($contents_array[5],1));                      
                       $packageNumber =  str_replace('"','',substr($contents_array[2],1)); 
                        
                    
                         if($orderNum!='' && $arrive!='')
                            {
                                $orderid = strstr($orderNum, 'EC');
                                if($orderid!='')
                                {
                                    
                                     $orderid = substr($orderid,2);

                                      $datain = array(
                                        'orderid'=>$orderid,
                                        'type'   =>'tp',
                                        'transportRemarks'=>1,//新竹物流
                                        'packageNumber'=>$packageNumber,
                                        'mstatus'=>5//已完成取貨
                                    ) ;
                                   // echo $orderNum;
                                    $this->paser->post($this->data['martadmDomain'].'receipt/modifydeliver',$datain,true);;
                                    
                                }
                               
                            }


                        /**/
                    }
                    
                }
             
              

            }
            fclose($handle);
		}  
		$result['result'] = true;
	
       echo '<script> window.close();</script>ok';
        
        
        
    }
	
	function time_chk()
	{
		$data['result'] = true;
		if($this->data['shopID']!=0 && $this->data['shopID']!=1040) 
		{
			
			$t = getdate();	
		
			if($t['wday']>=1&&$t['wday']<=5)
			{
				if($t['hours']>=9 && $t['hours']<=11)	$data['result'] = false;
			
			}
			
		}
		echo json_encode($data);
		exit(1);
	}
	
	
	
	function check_sheet()
	{
		
		$this->load->model('Order_model');
		$this->load->model('System_model');
		$shopID = $this->uri->segment(3);
		$arive = $this->uri->segment(4);
		$fromDate = $this->uri->segment(5);
		$toDate = $this->uri->segment(6);
		$orderType = $this->uri->segment(7);
		
		$offset = 0;
		$num = 0;//take all

		if($this->data['shopID']!=0) $shopID  = $this->data['shopID'];
	
		$data['fromDate'] = $fromDate;
		$data['toDate'] = $toDate;
		
		$data['shopInf'] = $this->System_model->getShopByID($shopID);
	
		if($arive==5) $arive = 15;
		$data['shipmentList'] = $this->Order_model->shipmentListHandle($shopID,$offset,$num,$arive,$orderType,$fromDate,$toDate,true);
	
		
		$this->load->view('check_sheet',$data);
		
		
	}

	function new_other_money()
	{
		$shopID = $this->input->post('shopID');
		$money = $this->input->post('money');
		$reason = $this->input->post('reason');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
	
		
		$this->db->insert('pos_other_money',array('shopID'=>$shopID,'money'=>$money,'reason'=>$reason,'year'=>$year,'month'=>$month));
		
		$data['result']  = true;
		echo json_encode($data);
		exit(1);
		
		
	}
	

	
	function update_other_money()
	{
		$id = $this->input->post('id');
		$money = $this->input->post('money');
		$reason = $this->input->post('reason');
	
		$this->db->where('id',$id);
		$this->db->update('pos_other_money',array('money'=>$money,'reason'=>$reason));
		
		$data['result']  = true;
		echo json_encode($data);
		exit(1);
		
		
	}
	function delete_other_money()
	{
		$id = $this->input->post('id');
		
		$this->db->where('id',$id);
		$this->db->delete('pos_other_money');
		
		$data['result']  = true;
		echo json_encode($data);
		exit(1);
		
		
	}
	
	function get_other_money()
	{	
		
		$this->load->model('Order_model');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		$shopID = $this->input->post('shopID');
		
		$data['otherMoney'] = $this->Order_model->getOtherMoney($year,$month,$shopID);
		$data['result']  = true;
		echo json_encode($data);
		exit(1);
	
	
	}
	function new_have_money()
	{
		$shopID = $this->input->post('shopID');
		$amount = $this->input->post('money');
		$date = $this->input->post('date');
	
		
		$this->db->insert('pos_check_record',array('shopID'=>$shopID,'amount'=>$amount,'date'=>$date));
		
		$data['result']  = true;
		echo json_encode($data);
		exit(1);
		
		
	}
	

	
	function update_have_money()
	{
		$id = $this->input->post('id');
		$amount = $this->input->post('money');
		$date = $this->input->post('date');
	
		$this->db->where('id',$id);
		$this->db->update('pos_check_record',array('amount'=>$amount,'date'=>$date));
		
		$data['result']  = true;
		echo json_encode($data);
		exit(1);
		
		
	}
	function delete_have_money()
	{
		$id = $this->input->post('id');
		
		$this->db->where('id',$id);
		$this->db->delete('pos_check_record');
		
		$data['result']  = true;
		echo json_encode($data);
		exit(1);
		
		
	}
	function get_have_money()
	{	
		
		$this->load->model('Order_model');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		$shopID = $this->input->post('shopID');
		
	
		$data['otherMoney'] = $this->Order_model->getCheckRecord($shopID,$year,$month);
		$data['result']  = true;
		echo json_encode($data);
		exit(1);
	
	
	}
	
	function estimate()
	{
        	set_time_limit(0);
		ini_set('max_execution_time', 0);;
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		$this->load->model('Order_model');
		$this->load->model('Product_model');
		$type = $this->uri->segment(3);
		$percent = $this->uri->segment(4);
		$safeCon = $this->uri->segment(5);
		$safeCon = $safeCon/10;
		$stockCon   = $this->uri->segment(6);
		
		$lowerNum   = $this->uri->segment(7);
        $shopID   = $this->uri->segment(8);
        if($shopID==0)$shopID = $this->data['shopID'];
		$time = getdate();
		$shopList = array
		(
			's_1'=>1,
			's_21'=>1,
			's_6'=>1,
			's_24'=>1,		
            's_16'=>1
		);
		$date = getdate (mktime(0, 0, 0, date("m")+1, date("d")));
		$year = $date['year'];
		$month = $date['mon']	;
		
		$allShopNum = count($shopList);
		$monthTime =3;
		$stock = $this->Order_model->getOrderingNum($shopID);	
		 
		for($i=0;$i<$monthTime;$i++)
		{
          
			$time =  getdate(mktime(0, 0, 0, $time['mon'] , -1, $time['year'] ));
			 $ret = $this->Accounting_model->getMonReport($time['mon'],$time['year'],0);	
            
			if(!empty($ret))
			foreach($ret as $row)
			{
				
				if($row['type']==1||$row['type']==5)
				{
					if(isset($shopList['s_'.$row['shopID']]))	
					{
						if(!isset($product['p_'.$row['productID']]))
						{
							
						
							if(isset($stock['p_'.$row['productID']]))
							{
								$product['p_'.$row['productID']]['orderingNum'] = $stock['p_'.$row['productID']]['orderingNum'];
							 ;
								}
							else $product['p_'.$row['productID']]['orderingNum'] = 0;
							$product['p_'.$row['productID']]['nowNum'] =$this->PO_model->getProductNum($shopID,$row['productID'],$year.'-'.$month);
                           
							$product['p_'.$row['productID']]['allNum'] = 0;
							$product['p_'.$row['productID']]['shopNum'] = 0;
							$product['p_'.$row['productID']]['productID'] = $row['productID'];
						
							
						}
						
						$product['p_'.$row['productID']]['allNum'] += $row['sellNum'];
					}
				}
				
			}
			
			
			$ret = $this->Accounting_model->getMonReport($time['mon'],$time['year'],$shopID);
				
			foreach($ret as $row)
			{
				
				if($row['type']==1||$row['type']==5)
				{
					if(!isset($product['p_'.$row['productID']]))
					{

							if(isset($stock['p_'.$row['productID']]))$product['p_'.$row['productID']]['orderingNum'] = $stock['p_'.$row['productID']]['orderingNum'];
							else $product['p_'.$row['productID']]['orderingNum'] = 0;
                        $product['p_'.$row['productID']]['nowNum'] =$this->PO_model->getProductNum($shopID,$row['productID'],$year.'-'.$month);
                           
							$product['p_'.$row['productID']]['allNum'] = 0;
							$product['p_'.$row['productID']]['shopNum'] = 0;
							$product['p_'.$row['productID']]['productID'] = $row['productID'];
						
						
					}
					
					$product['p_'.$row['productID']]['shopNum'] += $row['sellNum'];
				
				}
				
			}
            
		}
		
		$data['allShopNum'] = $allShopNum;
		$data['monthTime'] = $monthTime;
		$data['safeCon'] = $safeCon;
		$data['stockCon'] = $stockCon;
		$data['percent'] = $percent;
		$data['lowerNum'] = $lowerNum;
		
		 foreach($product as $row)
		 {
						
						
						$row['allNum'] = round(($row['allNum']/$allShopNum/$monthTime),2);
						$row['shopNum'] = round(($row['shopNum']/$monthTime),2);
						$row['safeStock'] = round( $safeCon*$row['shopNum']*$percent/100 + $row['allNum']*(1-$percent/100));
						if(!isset($row['nowNum']))$row['nowNum'] = 0;
						$row['orderNum'] = $row['safeStock'] - $stockCon *($row['nowNum']+$row['orderingNum']) ;
						
						
						if($row['orderNum']>$lowerNum)
						{
							$ret = $this->Order_model->getProduct(array('productID'=>$row['productID']),$this->data['shopID'],'get');
							$row = array_merge($ret[0],$row);
							
							if($row['openStatus']==1)
							{
							 $row['comNum'] = $this->Order_model->getProductNumExceptOrder( $row['productID'],0);
							 $row['preOrderNum'] = $this->Order_model->getPreOrderNum($shopID,$year,$month, $row['productID']);
							 $data['product'][] = $row;
							}
						}
				
		 }
            
		if($type=='html')  $this->load->view('order_estimate',$data);
         else 
		 {
			 $data['result']  =true;
			echo json_encode($data)	;
			exit(1);
			 
			 
		}   
        
        
		
		
	}
	
	function adjust_amount()
	{
		$this->load->model('Product_model');
		$productID = $this->input->post('productID');
		$data['shopList'] =  $this->Product_model->getShopAmountByBid(0,$productID,$this->data['shopID']);
		
		 $data['result']  =true;
			echo json_encode($data)	;
			exit(1);
		
	}
	
	
	function get_shop_address()
	{
		$this->load->model('Order_model');
		$shopID = $this->input->post('shopID');
		
		$data['address'] = $this->Order_model->getOrderAddress($shopID);
		 $data['result']  =true;
			echo json_encode($data)	;
			exit(1);
		
	}
	
	function change_row_price()
	{
	
		$id = $this->input->post('rowID')	;
        $shipmentID = $this->input->post('shipmentID')	;
		$purchasePrice = $this->input->post('purchasePrice');
		$this->db->where('rowID',$id);
        $this->db->where('shipmentID',$shipmentID);
		$this->db->update('pos_order_shipment_detail',array('sellPrice'=>$purchasePrice));
		$data['result']  =true;
		echo json_encode($data)	;
		exit(1);
		
		
		
		
	}
	function change_total()
	{
		$this->load->model('Order_model');
			$shipmentID = $this->input->post('shipmentID')	;
			$product = $this->Order_model->getShipmentDetailByID($shipmentID);
			$total = 0 ; 
			foreach($product as $row) 
			{
				$total +=$row['sellPrice']*$row['sellNum']	;
				
			}
			$data['total'] = $total;
			$this->db->where('id',$shipmentID);
			$this->db->update('pos_order_shipment',array('total'=>$total));
		$data['result']  =true;
		echo json_encode($data)	;
		exit(1);	

			
			
			
		
	}
	function update_address()
	{
		$shopID = $this->input->post('shopID');
		
		$id = $this->input->post('id');
		$datain['phone'] = $this->input->post('phone');
		$datain['receiver'] = $this->input->post('receiver');
		$datain['address']= $this->input->post('address');
		$datain['defaultToken']= $this->input->post('defaultToken');
		
		if($datain['defaultToken']==1)
		{
			$this->db->where('shopID',$shopID);
			$this->db->update('pos_order_address',array('defaultToken'=>0));
		}
		
		$this->db->where('id',$id);
		$this->db->update('pos_order_address',$datain);
		$data['result']  =true;
		echo json_encode($data)	;
		exit(1);
		
	}
	function delete_address()
	{
		$id = $this->input->post('id');
		$this->db->where('id',$id);
		$this->db->update('pos_order_address',array('deleteToken'=>1));
		$data['result']  =true;
		echo json_encode($data)	;
		exit(1);
		
		
	}
    function get_order_limit()
    {
        
         $this->load->model('Order_model');
         $data['shopStatus'] = $this->Order_model->autoOrderStatus($this->data['shopID']);
        
        $date = getdate();
		$year = $date['year'];
		$month = $date['mon'];
        $orderData  = $this->Order_model->getOrderList($this->data['shopID'],0,9999,-2,3,$year,$month);
  
        if(isset($data['shopStatus']['limitAmount']))
        foreach($orderData as $row)
        {
           
            if(isset($row['total']))
            {
               
            if($row['orderComment']!='電腦自動下單' && strpos($row['orderComment'],'商城產品補貨')===false && strpos($row['orderComment'],'魔法風雲會')===false && strpos($row['orderComment'],'寶可夢')===false)    $data['shopStatus']['limitAmount'] -= $row['total'] ;
            }
            
        }
        
        
        
        $data['result']  =true;
		echo json_encode($data)	;
		exit(1);
        
        
    }
    
    
    function auto_order_sender()
    {
   
        $this->db->where('autoOrder',1);
        $query = $this->db->get('pos_sub_branch_order');
        $data = $query->result_array();
        
        foreach($data as $row)
        {
            $this->paser->post_ignore('http://shipment.phantasia.com.tw/order/auto_order/'.$row['shopID'],array(),true);
            echo $row['shopID'].'<br/>';
        }
    }
    
    
    
    
    
    
    function auto_order()
    {
        
  
          //產生訂單
          //導入order
           $this->load->model('Product_model');
           $this->load->model('Order_model');
       
	    
        $destinationShopID = $this->uri->segment(3);
        
       
        $shopStatus = $this->Order_model->autoOrderStatus($destinationShopID);
        
        $p = $this->paser->get('http://shipment.phantasia.com.tw/order/estimate/json/'.
                          $shopStatus['percent'].'/'. $shopStatus['safeCon'].'/'. $shopStatus['stockCon'].'/'. $shopStatus['lowerNum'].'/'. $shopStatus['shopID'],array(),true);
        
        if(isset($p['product']) && count($p['product'])>0)
        {
			$addressID = $this->Order_model->getAddress($destinationShopID);
			$maxNum = $this->Order_model->getMaxOrderNum();
				 	$orderDatain = array(
						'status' =>1,//下好訂單
						'shopID' =>   $destinationShopID,
						'orderTime' =>date('Y-m-d H:i:s'),
						'orderNum' =>$maxNum+1,
						'type'  =>0,//
						'orderComment' => '電腦自動下單',
						'addressID' => $addressID
						);
				 	$this->db->insert('pos_order',$orderDatain);
					$newOrderID = $this->db->insert_id();			
			
			$total = 0 ;
        
			foreach( $p['product'] as $row)
			{
				$comment = '電腦自動下單';      
               $sellPrice =  $this->Order_model->concessionPrice($destinationShopID,$row['productID'],0, $row['orderNum']);
                
                //分配貨品
					$orderRemainNum = $this->Order_model->getProductNumExceptOrder($row['productID'],0);
					if($orderRemainNum>$row['orderNum']) $sellNum = $row['orderNum'];
					else if ($orderRemainNum>0)$sellNum=$orderRemainNum;
					else $sellNum = 0;
                
                
				//出貨品項
				 //導入order
                 
					$orderDetaildatain = array(
						'orderID'=>$newOrderID,
						'sellPrice' =>$sellPrice ,
						'buyNum' => $row['orderNum'],
						'sellNum' => $sellNum,
						'productID' => $row['productID'],
						'comment' => '電腦自動下單'
					);
					$this->db->insert('pos_order_detail',$orderDetaildatain); 
					$total += $sellPrice  *$row['orderNum'];
			
				 //===========
            }    
    
            $this->db->where('id',$newOrderID)	;
			$this->db->update('pos_order',array('total'=>$total));
             
        
          
        //=====出貨單設定完成
        }
        
    }
	function budget()
    {
        
        
        
        
    }
	function malldata()
    {
        
        $orderID = $this->input->post('orderID');
        $domain = $this->input->post('url');

        if(strpos('a'.$domain,'mart.phantasia.tw'))
        {
            
            $this->db->insert('pos_weborder_link',array('orderID'=>$orderID,'time'=>date("Y-m-d H:i:s")));
        }
    	$data['result']  =true;
		echo json_encode($data)	;
		exit(1);
        
        
        
    }
    function malldatatest()
    {
        $this->load->model('cs_order_model');
       // $this->cs_order_model->mallDataTransfer();
        
    }
    
    function mallProductMerge()
    {
        $this->data['js'] = $this->preload->getjs('pos_order');
       $this->data['js'] = $this->preload->getjs('pos_order');
		$this->data['js'] = $this->preload->getjs('date_format');
		$this->data['js'] = $this->preload->getjs('pos_product_query');

      
        $this->data['product'] = $this->paser->get($this->data['martadmDomain'].'product/get_unlink_product',array(),true);
        $this->data['display'] = 'product_merge';
        $this->load->view('template',$this->data);
                                  
        
    }
    
    function product_merge()
    {
   
       echo $this->paser->post($this->data['martadmDomain'].'product/make_product_link',
                          array('pID'=>$this->input->post('pID'),
                               'productID'=>$this->input->post('productID')
                               
                               
                               ),false);
                               
                             
    }
    function boxprepare()
	{
		$shop =  $this->System_model->getShop(true);
		foreach($shop as $row)
		{
			if($row['shopID']<600)
			{
					$this->db->insert('pos_announce_check',array('shopID'=>$row['shopID'],'announceID'=>3511,'confirm'=>0));	
					 $this->paser->post('http://possvr.phantasia.com.tw/system/delete_notifaction_temp',array('shopID'=>$row['shopID']),true) ;
			}
			
		}
		
	}
    
    
    function quick_purchase()
    {
        
      $this->load->model('order_model');
        
      $r = $this->order_model->getOrderBySupplier($this->input->post('supplierID'));
        $time = getdate();
		$year = $time['year'];
		$month = $time['mon'];
      foreach($r as $row)    
      {
          
          if(!isset($result['product']['p_'.$row['productID']]))
          {
			  if(empty($row['martSaveNum']))$row['martSaveNum'] = 0;
              $result['product']['p_'.$row['productID']] = $row;
                $result['product']['p_'.$row['productID']]['comNum']  = 
              $this->PO_model->getProductNum(0,$row['productID'],$year.'-'.$month);
          }
          else  $result['product']['p_'.$row['productID']]['buyNum']+=$row['buyNum'];
      }
        $result['result']  =true;
		echo json_encode($result)	;
		

        
    }
	
	function collect_order_update()
	{
        if($this->data['shopID']==0)
        {
           $this->db->where('id',$this->input->post('id'));
		$this->db->update('pos_collect_order',$_POST);
		 $result['result']  =true; 
                
        }
		else  $result['result']  =false; 
		echo json_encode($result);
		
		
		
		
	}
	
	function collect_order_transfer()
	{
		$this->load->model('Product_model');
		  $this->load->model('Order_model');
		$id= $this->input->post('id');

		$result['collectOrderList']  = $this->Order_model->getCollectProgress($id);
		
		
			foreach($result['collectOrderList']  as $row)
			{
					$product = '';
					if($row['num'] >0) 
					{
						$destinationShopID =  $row['shopID'];
						$product[] = array('productID'=>$row['productID'],'num'=>$row['num']);
		
						$addressID = $this->Order_model->getAddress($destinationShopID);
						$maxNum = $this->Order_model->getMaxOrderNum();
						$orderDatain = array(
							'status' =>1,//下好訂單
							'shopID' =>   $destinationShopID,
							'orderTime' =>date('Y-m-d H:i:s'),
							'orderNum' =>$maxNum+1,
							'type'  =>0,//
							'orderComment' => '集單訂單',
							'addressID' => $addressID
							);
						$this->db->insert('pos_order',$orderDatain);
						$newOrderID = $this->db->insert_id();			
			$result['orderList'][] = $orderDatain['orderNum'];
					$total = 0 ;
        
					foreach( $product as $row)
					{
					   $comment = '集單訂單';      
					   $sellPrice =  $this->Order_model->concessionPrice($destinationShopID,$row['productID'],0, $row['num']);

						//分配貨品
							$orderRemainNum = $this->Order_model->getProductNumExceptOrder($row['productID'],0);
							if($orderRemainNum>$row['num']) $sellNum = $row['num'];
							else if ($orderRemainNum>0)$sellNum=$orderRemainNum;
							else $sellNum = 0;


						//出貨品項
						 //導入order

							$orderDetaildatain = array(
								'orderID'=>$newOrderID,
								'sellPrice' =>$sellPrice ,
								'buyNum' => $row['num'],
								'sellNum' => $sellNum,
								'productID' => $row['productID'],
								'comment' => '電腦自動下單'
							);
							$this->db->insert('pos_order_detail',$orderDetaildatain); 
							$total += $sellPrice  *$row['num'];

						 //===========
					}    

					$this->db->where('id',$newOrderID)	;
					$this->db->update('pos_order',array('total'=>$total));
						 
        
          
        //=====出貨單設定完成
						
						
						
					}
				
				
				
				
			}
			
			
		 $result['result']  =true;
		echo json_encode($result);
			
		
		
		
		
		
	}
	
	
	
    function get_collect()
    {
            $this->load->model('order_model');
        
		  $r = $this->order_model->getCollect($this->input->post('offset'),$this->input->post('type'));
		if($this->input->post('type')=='edit')
		{
			
			$result['collectOrder'] = $r;
		}
		else
        foreach($r as $row)
		{
			if(strtotime(date("Y-m-d H:i:s"))>strtotime($row['deadline'].' '.$row['deadTime']))
			{
				$row['orderToken'] = false;
				
			}
			else $row['orderToken'] = true;
			
			$row['comment'] = $this->order_model->urlToLink($row['comment']);
			$row['joinComment'] = $this->order_model->urlToLink($row['joinComment']);
			$row['outComment'] = $this->order_model->urlToLink($row['outComment']);
			$result['collectOrder'][] = $row;
			
		}
         $result['result']  =true;
		
		echo json_encode($result);
        
        
        
        
    }
	
	function create_order_collect()
	{
		 $this->load->model('order_model');
		$this->db->insert('pos_collect_order',array('status'=>-1));
		$data['id'] = $this->db->insert_id();
		$data['collect'] = $this->order_model->getCollectByID($data['id'] );
		$data['result']  =true;
		echo json_encode($data);

	}
	function get_collect_progress()
	{
		
		   $this->load->model('order_model');
        
		$result['collectOrderList']  = $this->order_model->getCollectProgress($this->input->post('id'));
		$result['total'] = 0;
		foreach($result['collectOrderList'] as $row)
		{
			
			$result['total'] +=$row['num'];
			
		}
        
         $result['result']  =true;
		echo json_encode($result)	;
		
		
	}
	function update_collect_num()
	{
		
		$collectID = $this->input->post('id');
		$num = $this->input->post('num');
		$this->db->where('collectID',$collectID);
		$this->db->where('shopID',$this->data['shopID']);
		$query = $this->db->get('pos_collect_order_list');
		$datain = array(
			'num'=>$num,
			'timeStamp'=>date("Y-m-d H:i:s")
			
		);
		if($query->num_rows()>0)
		{
			$this->db->where('collectID',$collectID);
			$this->db->where('shopID',$this->data['shopID']);
			$this->db->update('pos_collect_order_list',$datain);
			
		}
		else
		{
			$datain['shopID'] = $this->data['shopID'];
			$datain['collectID'] = $collectID;
			$this->db->insert('pos_collect_order_list',$datain);
			
			
		}
		 
         $result['result']  =true;
		echo json_encode($result)	;
		
		
		
	}
	function update_collect()
	{
		$datain['comment']     = $this->input->post('comment');
		$datain['target']      = $this->input->post('target');
	 	$datain['deadline']    = $this->input->post('deadline');
		$datain['deadTime']    = $this->input->post('deadTime');
		$datain['joinComment'] = $this->input->post('joinComment');
		$datain['outComment']  = $this->input->post('outComment');
		$datain['status'] = 0;

		$this->db->where('id',$this->input->post('id'));
		$this->db->update('pos_collect_order',$datain);
		 $result['result']  =true;
		echo json_encode($result)	;
        
        
		
		
	}
    
	function get_point_inf()
	{
		$shopID = $this->input->post('shopID');
		if(empty($shopID) ||$shopID==0) $shopID = $this->data['shopID'];
		
		
		$this->load->model('System_model');
		$data['shopInf'] = $this->System_model->getShopByID($shopID);
		
		
	 	$data['result']  =true;
		echo json_encode($data);
		
		
		
	}
    
    function get_point_detail()
	{
		$shopID = $this->input->post('shopID');
		if(empty($shopID) ||$shopID==0) $shopID = $this->data['shopID'];
        $offset = $this->input->post('offset');
        $num = $this->input->post('num');
		$this->load->model('Order_model');
		$data['detail'] = $this->Order_model->getPointDetail($shopID,$offset,$num);
		$data['result']  =true;
		echo json_encode($data);
		
	}
	
	
	function get_point_change()
	{
			$this->load->model('Order_model');
		
		$data['product'] = $this->Order_model->getPointChange();
		$data['result']  =true;
		echo json_encode($data);
		
		
		
	}
    
    function point_batch()
    {
         $shopList= $this->System_model->getShop(true,true);	
        print_r($shopList);
    
        foreach($shopList as $row)
        {
            if($row['shopID']<=50)
             $this->System_model->addPoint($row['shopID'],5000, '親子天下行銷廣告回饋點數');
        }
       
    }
    
    
    function change_point_send()
    {
       $this->load->model('Order_model');
        $this->load->model('System_model');
        for($i=0;isset($_POST['productID'][$i]);$i++)
        {
            
            if($_POST['num'][$i]>0)
            $p['product'][] = array(
                'productID' =>$_POST['productID'][$i],
                'orderNum'       =>$_POST['num'][$i],
                'point'     =>$_POST['point'][$i],
            );
            
            
            
        }
        
        

        
        $destinationShopID = $this->data['shopID'] ;
         if(isset($p['product']) && count($p['product'])>0)
        {
			$addressID = $this->Order_model->getAddress($destinationShopID);
			$maxNum = $this->Order_model->getMaxOrderNum();
				 	$orderDatain = array(
						'status' =>1,//下好訂單
						'shopID' =>   $destinationShopID,
						'orderTime' =>date('Y-m-d H:i:s'),
						'orderNum' =>$maxNum+1,
						'type'  =>0,//
						'orderComment' => '點數兌換訂單',
						'addressID' => $addressID
						);
				 	$this->db->insert('pos_order',$orderDatain);
					$newOrderID = $this->db->insert_id();			
			
			$total = 0 ;
            $totalPoint  = 0;
			foreach( $p['product'] as $row)
			{
                $totalPoint += $row['point'] *$row['orderNum'];
				$comment = '點數兌換訂單';      
               $sellPrice = 0;
                
                //分配貨品
					$orderRemainNum = $this->Order_model->getProductNumExceptOrder($row['productID'],0);
					if($orderRemainNum>$row['orderNum']) $sellNum = $row['orderNum'];
					else if ($orderRemainNum>0)$sellNum=$orderRemainNum;
					else $sellNum = 0;
                
                
				//出貨品項
				 //導入order
                 
					$orderDetaildatain = array(
						'orderID'=>$newOrderID,
						'sellPrice' =>$sellPrice ,
						'buyNum' => $row['orderNum'],
						'sellNum' => $sellNum,
						'productID' => $row['productID'],
						'comment' => '點數兌換小計：'.$row['point']* $row['orderNum']
					);
					$this->db->insert('pos_order_detail',$orderDetaildatain); 
					$total += $sellPrice  *$row['orderNum'];
			
				 //===========
            }    
    
            $this->db->where('id',$newOrderID)	;
			$this->db->update('pos_order',array('total'=>$total,'orderComment'=>'點數兌換訂單 共：'.$totalPoint));
             
        
          
        //=====出貨單設定完成
        }
              
       
        
        
       $data['output'] = $this->System_model->addPoint($destinationShopID, -$totalPoint,'點數兌換',$newOrderID);
       
        
 
       $data['result']  =true;
       echo json_encode($data); 
        
        
    }
    function point_product_update()
    {
       $datain['productID'] = $this->input->post('productID');
       $datain['point'] = $this->input->post('point');
        
       $this->db->where('productID',$datain['productID']);
       $q =  $this->db->get('pos_point_change_table');
       if($q->num_rows()>0)
       {
            $this->db->where('productID',$datain['productID']);
            $this->db->update('pos_point_change_table',array('point'=>$datain['point']));
           
       }
        else $this->db->insert('pos_point_change_table',$datain);
        
         
       $data['result']  =true;
       echo json_encode($data); 
        
    }
        
    function point_add()
    {
          $this->load->model('System_model');
        $datain['shopID'] = $this->input->post('shopID');
        $datain['comment'] = $this->input->post('comment');
        $datain['point'] = $this->input->post('point');
        $data['output'] = $this->System_model->addPoint($datain['shopID'],$datain['point'], $datain['comment']);
           $data['result']  =true;
        echo json_encode($data); 
    }
    function point_product_delete()
    {
        
         $datain['productID'] = $this->input->post('productID');
         $this->db->where('productID',$datain['productID']);
            $this->db->delete('pos_point_change_table');
         $data['result']  =true;
        echo json_encode($data);
    }
    
    function shipment_report()
    {
          $this->load->model('System_model');
      	$this->data['shop'] = $this->System_model->getShop(true);
        $year = $this->uri->segment(3);
    
        foreach($this->data['shop'] as $row)
        {
            $shopID = $row['shopID'];
            $r[$shopID]['name'] = $row['name'];
            $this->db->where('shopID',$shopID);
            $this->db->where("year(shippingTime)",$year,false);
            $this->db->join('pos_order_shipment_detail','pos_order_shipment_detail.shipmentID = pos_order_shipment.id','left');
            $this->db->join('pos_product','pos_order_shipment_detail.sProductID = pos_product.productID','left');
            $query = $this->db->get('pos_order_shipment');
            $data = $query->result_array();
             for($i=1;$i<=12;$i++)
             {
                $month =   str_pad($i, 2, "0", STR_PAD_LEFT);
             $r[$shopID]['data'][$month]['own']['total']= 0;
             $r[$shopID]['data'][$month]['own']['amount']= 0;
             $r[$shopID]['data'][$month]['other']['total']= 0;
             $r[$shopID]['data'][$month]['other']['amount']= 0;
             }
            
            foreach($data as $each)
            {
                $month = substr($each['shippingTime'],5,2);
                if($each['suppliers']==21)
                {
                    if(!isset( $r[$shopID]['data'][$month]['own'])) 
                    {
                        $r[$shopID]['data'][$month]['own']['total']= 0;
                        $r[$shopID]['data'][$month]['own']['amount']= 0;
                    }
                    $r[$shopID]['data'][$month]['own']['amount'] += $each['sellNum'];
                    $r[$shopID]['data'][$month]['own']['total']+= $each['sellNum'] * $each['sellPrice'];
                   
                }
                else
                {
                     if(!isset($r[$shopID]['data'][$month]['other'])) 
                    {
                        $r[$shopID]['data'][$month]['other']['total']= 0;
                        $r[$shopID]['data'][$month]['other']['amount']= 0;
                    }
                    $r[$shopID]['data'][$month]['other']['amount'] += $each['sellNum'];
                    $r[$shopID]['data'][$month]['other']['total']  += $each['sellNum'] * $each['sellPrice'];
                    
                    
                }
                
                
            }
                    
            
            
        }
        echo '	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        echo '<table border="1"><tr><td></td>';
        for($i=1;$i<=12;$i++)
        {
            
             //echo '<td colspan="4">'.$i.'</td>';
            echo '<td colspan="2">'.$i.'</td>';
        }
            
          echo   '</tr>'  ;
         echo '<tr><td></td>';
         for($i=1;$i<=12;$i++)
        {
            
            // echo '<td colspan="2">自有</td><td colspan="2">其他</td>';
              echo '<td colspan="1">自有</td><td colspan="1">其他</td>';
        }
        echo '</tr>';
        
        /*
        echo '<tr><td></td>';
         for($i=1;$i<=12;$i++)
        {
            
             echo '<td>數量</td><td>業績</td><td>數量</td><td>業績</td>';
        }
        echo '</tr>';
        */
        foreach($r as $key=>$s)
        {
            $s['name'];
            echo '<tr><td>'.$key.$s['name'].'</td>';
            foreach($s['data'] as $d)
            {
                if(!isset($d['own']['total'])) $d['own']['total'] = 0;
                 if(!isset($d['other']['total'])) $d['other']['total'] = 0;
                //echo '<td>'.$d['own']['amount'].'</td>';
                echo '<td>'.$d['own']['total'].'</td>';
                //echo '<td>'.$d['other']['amount'].'</td>';
                echo '<td>'.$d['other']['total'].'</td>';
            }
      
                echo '</tr>';
            
            
            
        }
        
          echo '</table>';
    }
    
     function member_quick_chk()
    {
         $this->load->model('Member_model');
        $memberID = $this->input->post('memberID');
        $memberChk = $this->Member_model->memberIDRuleChk($memberID);
        $data['result'] = $memberChk;
        
		echo json_encode($data);
		exit(1);
        
    }
    
    
	/*
	$data['result']:
	1 : insert successs
	0 : memberID repeat or type error
	-1: connect error into buffer;
	*/
	function quick_new_member()
	{

        $this->load->model('Member_model');
        $this->load->model('Order_model');
        /*
        $_POST['memberID'] = 230857;
        $_POST['level'] = 4;
        $_POST['orderID'] = 110196;
        */
		$data = array(
		'memberID' => $this->input->post('memberID'),
		'levelID'    => $this->input->post('level'),
		);
		$data['result'] = 0;
		
        $orderID =  $this->input->post('orderID');
         
        $data['wrong'] ='會員編號重複';
        //memberID 格式確認
        
        $memberChk = $this->Member_model->memberIDRuleChk($data['memberID']);
         
	   if($memberChk==false) $result['wrong'] ='會員編號格式錯誤';
    
        if($memberChk==1&&$data['memberID']!='')
		{	
           
            
				if($this->Member_model->getMemberByID($data['memberID'],0)===false)
				{
					$i = 0;
					$pattern = "/^(0+)(\d+)/i";
					$replacement = "\$2";
					//$qid = substr($id,-10);
					$data['memberID']= preg_replace($pattern,$replacement,$data['memberID']);
					//去零
					$ecorder = $this->Order_model->getECOrderInf($orderID);
                    $orderNum = $ecorder['ECOrderNum'];
               
                     if($orderNum!==0)
                    {
                         $ECOrderID = substr($orderNum,2);
                         $memberData = $this->paser->post($this->data['martadmDomain'].'receipt/get_new_member_data',array('orderID'=>$ECOrderID),true);
                      
                         $datain = array(
                             'shopID'  =>666,
                             'licence'=>'150e4e2633d2d5aa712b6a41fcd6ba01',
                             'levelID'  =>$data['levelID'],
							'memberID' =>$data['memberID'],
							'name' =>$memberData['memberData']['name'],
                             'phone' =>$memberData['memberData']['cellPhone'],
							'pw' =>md5($memberData['memberData']['pw'])
							
						);
               
						$data = $this->paser->ECPost('member/quick_new_member',$datain,true);
                         
                        if($data['result']==true)
                        {
                            
                            $this->db->where('orderID',$orderID);
                            $this->db->update('pos_ec_order',array('memberID'=>$data['memberID']));
                            
                             
                        
                            
                             $postData['memberID'] = $datain['memberID'];
                            $postData['name'] = $memberData['memberData']['name'];
                            $postData['phone'] = $memberData['memberData']['cellPhone'];
                            $postData['email'] = $memberData['memberData']['email'];
                            $postData['birth'] = $memberData['memberData']['birthday'];
                            $postData['address'] = $memberData['memberData']['address'];
                            $postData['sex'] = $memberData['memberData']['sex'];
                            $postData['note'] = '線上申辦';
                            $postData['shopID']  =666;
                            $postData['licence']  = '150e4e2633d2d5aa712b6a41fcd6ba01';
                            $data = $this->paser->ECPost('member/update_member',$postData,true);
                            
             $r = $this->paser->post($this->data['martadmDomain'].'receipt/member_level_change',array('orderID'=>$ECOrderID,'memberID'=>$datain['memberID']),true);
                               
                            
                        }
                         
                    }
                    else $data['result'] = false;

              
				
					
			}
		}
		else $data['result'] = 0;
		
		echo json_encode($data);
		exit(1);		
	}
	
    
    function order_box_finish()
    {
       $shipmentID =  $this->input->post('shipmentID');
        $boxTime =  $this->input->post('boxTime');
        $boxNum =  $this->input->post('num');
        $type =  $this->input->post('type');
        $finishTime =  date("Y-m-d H:i:s");
    
        if($type==1) 
        {
            $db = 'pos_ec_order';
            $this->db->where('orderID',$shipmentID);
        }
        else
        {
            $db = 'pos_order_shipment';
            $this->db->where('id',$shipmentID);
            
            
        }
        $this->db->update($db,array('boxTime'=>$boxTime,'finishTime'=>$finishTime,'boxNum'=>$boxNum));
        $data['result']  =true;
        echo json_encode($data);
        
        
        
    }
    //EC
    
    function ec_order_update()
    {
        $orderID = $this->input->post('orderID');
  // $this->db->insert('pos_test',array('content'=>$orderID));
         $datain = array(
                        'platformID' =>$this->input->post('platformID'),
                        'ECOrderNum' =>	$this->input->post('ECOrderNum'),
                        'ECstatus'	    =>1,
                        'updateTime'	=>date('Y-m-d H:i:s'),
                        'transportID'	=>$this->input->post('transportID'),				
                        'receiverName'	=>$this->input->post('receiverName'),
                        'receiverPhone'	=> $this->input->post('receiverPhone'),
                        'receiverAddress'=>$this->input->post('receiverAddress'),
                        'email'           =>$this->input->post('receiverEmail'),
                        'payway'          =>$this->input->post('payway'),
                        'remark'          =>$this->input->post('remark'),
                        'memberID'       =>$this->input->post('memberID'),
                        'comID'         =>$this->input->post('comID')
                    
                    );
       $this->db->where('orderID',$orderID);
        $this->db->update('pos_ec_order',$datain);
        
        $result['result'] = true;		
        echo json_encode($result);
		exit(1);
        
        
    }
    function get_ec_platform()
	{
		$this->load->model('Order_model');

	    $result['platform'] = $this->Order_model->getEcPlatform();
	
		$result['result'] = true;		
        echo json_encode($result);
		exit(1);
	}
	
    
    function get_ec_platform_order()
    {
     
        $status = $this->input->post('status');
        $platFormID = $this->input->post('id');
         $fromDate = $this->input->post('fromDate');
         $toDate = $this->input->post('toDate');
        $this->load->model('Order_model');
         
        
        $r= $this->Order_model->getEcPlatformOrder($platFormID,$status,$fromDate,$toDate);
         foreach($r as $row)
         {
             
             $row['orderTime']	 = substr($row['orderTime'],0,16);
			$row['shippingTime']	 = substr($row['shippingTime'],0,10);
			switch($row['transportID'])
            {
                case 2:
                    $row['transportName'] ='宅配';
                break;    
                 case 3:
                    $row['transportName'] ='7-11';
                break;  
               case 4:
                    $row['transportName'] ='三大超';
                break;
               case 5:
                    $row['transportName'] ='海外';
                break;
                    
                default:$row['transportName']='其他';    
                    
            }
			if($row['status']>=2)$row['orderStatus']= '完成出貨';
			else   $row['orderStatus']= '<span style="color:red">貨品等候中</span>';
			  $result['platformOrder'][] =$row;
			;
             
             
             
         }
	
		$result['result'] = true;		
        echo json_encode($result);
		exit(1);
        
        
        
        
    }
    
    function get_pokemon()
    {
        $this->load->model('Order_model');
        $result['product']= $this->Order_model-> getAllPokemon(0);
    	$result['result'] = true;		
        echo json_encode($result);
		exit(1);
        
    
    
    }
    
    
    
        
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */