<?php

class Product extends POS_Controller {

	function Product()
	{
		parent::POS_Controller();
		$this->data['css'] = $this->preload->getcss('pos');
		$this->data['js'] = $this->preload->getjs('barcode');
		$this->load->model('Product_model');
			
	}
	
	
	function index()
	{
		
		$this->data['css'] = $this->preload->getcss('jquery-ui-1.8.16.custom');
		$this->data['js'] = $this->preload->getjs('jquery-ui-1.8.16.custom.min');
		$this->data['js'] = $this->preload->getjs('jquery.tablesorter');
		$this->data['js'] = $this->preload->getjs('jquery.fixedheadertable');
		$this->data['js'] = $this->preload->getjs('pos_product');
		$this->data['js'] = $this->preload->getjs('pos_product_query');
		if($this->data['shopID']!=0)
		{
			if($this->data['level']==5)	redirect('/order/online');
			 redirect('/accounting');
		}
		$this->data['display'] = 'product';
		$this->load->view('template',$this->data);	
	}
	function get_product()
	{
		$barcode = $this->input->post('barcode');
		$productID = $this->input->post('productID');
		if($barcode!=0)	 $data['product']= $this->Product_model->getProductByBarcode($barcode);
		if(empty($data['product']))$data['product']= $this->Product_model->getProductByProductID($productID);

		if($data['product']==false)$data['result'] = false;
		else $data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	
	
	
	
	function get_product_stock()
	{
		
		$find['barcode'] = $this->input->post('barcode');
		$find['productNum'] = $this->input->post('productNum');
		$find['ENGName'] = $this->input->post('ENGName');
		$find['ZHName'] = $this->input->post('ZHName');
		$find['suppliers'] = $this->input->post('suppliers');
		$find['start'] = $this->input->post('start');
		$find['num'] = $this->input->post('num');
		$find['status'] = $this->input->post('status');
		$find['start'] = $this->input->post('start');
		$find['order1'] = $this->input->post('order1');
		$find['order2'] = $this->input->post('order2');
		$find['sequence1'] = $this->input->post('sequence1');
		$find['sequence2'] = $this->input->post('sequence2');
		$find['placeID'] = $this->input->post('placeID');
		$find['topProduct'] = $this->input->post('topProduct');
	
		$result = $this->Product_model->getProductStock($find,$this->data['shopID']);
		$data['product'] = $result['product'];
		$data['totalNum'] = $result['totalNum'];
		if($data['product']==false)$data['result'] = false;
		else $data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
		
	}
	function get_top_product()
	{
		$find['topProduct'] = $this->input->post('topProduct');
		
		$data['product'] = $this->Product_model->getTopProduct();
	
		$data['notProduct'] = $this->Product_model->getNotTopProduct();
		if($data['product']==false)$data['result'] = false;
		else $data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
	}
	function delete_top_product()
	{
		$productID = $this->input->post('productID');
		$this->db->where('productID',$productID);
		$this->db->delete('pos_top_product');
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	function get_sell_info()
	{
			$bid = $this->input->post('bid');
	

		$shopList =  $this->Product_model->getShopAmountByBid($bid);
		
		
		$openToken = false;
		$i = 0 ;
		$data['productStatus'] = false;
		foreach($shopList as $row)
		{
			if($row['shopID']==0)	
			{
				if($row['num']>0)	
				{
					$openToken = true;
					
				} 
				else if($row['openStatus']==1)$openToken = true;
				else 
				{
					$data['productStatus'] = false;
					
				}
			}
			else
			{
				if($row['num']>0) 
				{
					$status ='現貨供應中';
					$data['productStatus'] = true;
				}
				else if($openToken) $status = '暫時缺貨中';
				else  $status = '缺貨中';
				$data['shopList'][$i] = $row;
				$data['shopList'][$i]['status'] = $status;
				$i++;
			}
			
			
		}
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	
	
	
	function get_product_type()
	{
		$data['productType']= $this->Product_model->getProductType();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}	
	function pay()
	{
		$i = 0;$j=0;$key = false;
		$data = array(); 
		$postNum = count($_POST);
		foreach($_POST as $row)
		{
			
			if($i+2==$postNum)
			{
				$payType = $row;	
			
			}
			else if($i+1==$postNum)
			{
				$memberID = $row;
				
			}
			else
			{
				switch($i%5)
				{
					case 0:
						$data[$j]['productID'] = $row;
					break;
					case 1:
						$data[$j]['price'] = $row;
					case 2:
						//jump
					break;				
					case 3:
						$data[$j]['num'] = $row;
					break;
					case 4:
						$data[$j++]['count'] = $row;
					break;								
				}
			}
			$i++;
			
		}
		$this->load->model('Member_model');
		$this->load->model('Accounting_model');
		if($this->Member_model->getMemberByID($memberID,$this->data['shopID'])==false) $memberID = 999999;
		$result['total'] = 0;
		$i = 0 ;
		foreach($data as $row)
		{
				$datain[$i] = array(
				'productID'  => $row['productID'],
				'memberID'  => $memberID,
				'payType'  => $payType,
				'num'       => $row['num'],
				'sellPrice' => round($row['price']*$row['count']/100),
				'time'      => date("Y-m-d H:i:s")	
				);
			
			$result['total'] +=	$datain[$i]['sellPrice']*$row['num'];
			$this->db->insert('product_sell',$datain[$i]);
			$this->Product_model->updateNum($row['productID'],-$row['num']);
			$i++;
				
		}
		$this->load->model('Accounting_model');
		$cash_data['remain'] = $this->Accounting_model->registerIO($result['total'],0,$this->data['aid'],'sales',$this->data['account']);		
		$postData['postStr'] = 	json_encode($datain);
		//$getData = $this->paser->post($this->data['serverDomain'].'product/pay',$postData,true);
		//direct in buffer	do not pase to server this time
		$this->load->model('Buffer_model');
		$this->Buffer_model->addToBuf('product/pay',$postData);
		$result['result'] = 1;
		echo json_encode($result);
		exit(1);	
	}
	
	function edit_send()
	{
		$result['result'] = false;
		if($this->data['level']>=50)
		{
			$productID = $this->input->post('productID');
			$productNum = $this->input->post('productNum');
			$barcode = $this->input->post('barcode');
			$price = $this->input->post('price');
			$minDiscount = $this->input->post('minDiscount');
			$ZHName = $this->input->post('ZHName');
			$ENGName = $this->input->post('ENGName');
			$type = $this->input->post('productType');
			$category = $this->input->post('category');
			$buyDiscount = $this->input->post('buyDiscount');
			$buyPrice = $this->input->post('buyPrice');
			$purchaceDiscount = $this->input->post('purchaceDiscount');
			$nonJoinPurchaceDiscount = $this->input->post('nonJoinPurchaceDiscount');
			$suppliers = $this->input->post('suppliers');
			$language = $this->input->post('language');
			$openStatus = $this->input->post('openStatus');
			$publisher = $this->input->post('publisher');
			$comment = $this->input->post('comment');
			$phaBid = $this->input->post('phaBid');
			$concessionsNum = $this->input->post('concessionsNum');
			$concessions = $this->input->post('concessions');
			$cardSleeve = $this->input->post('cardSleeve');	
			$rule = $this->input->post('rule');	
			$wait = $this->input->post('wait');	
			$limitNum = $this->input->post('limitNum');		
			
			if($cardSleeve=='0')$cardSleeve ='';
	
			$bidExist = 1;
			$ret = $this->paser->post('http://www.phantasia.tw/bg/chk_bg',array('bid'=>$phaBid),true);
				if($ret['result']==false)
				{
					$bidExist = 0 ; 
			
					
				}
				else
				{ 
				$data = $this->paser->post('http://www.phantasia.tw/bg_controller/update_price',array('bid'=>$phaBid,'price'=>$price,'category'=>$category),false);	
					
				}
				
			$datain = array(
					'productNum' =>$productNum,
					'barcode' => $barcode,
					'price' => $price,
					'minDiscount' => $minDiscount,
					'ZHName'     => $ZHName,
					'ENGName'  => $ENGName,
					'type'     => $type,
					'category' =>$category,
					'buyDiscount' => $buyDiscount,
					'buyPrice'   => $buyPrice,
					'purchaseCount' =>$purchaceDiscount,
					'nonJoinPurchaceDiscount' => $nonJoinPurchaceDiscount,
					'suppliers'   =>$suppliers,
					'language'    => $language,
					'openStatus'  => $openStatus,
					'publisher '  => $publisher,
					'comment'     => $comment,
					'phaBid'      => $phaBid,
					'timeStamp'   => date("Y-m-d H:i:s"),
					'bidExist'	  => $bidExist,
					'cardSleeve'  => substr($cardSleeve,0,strlen($cardSleeve)-1),
					'rule'        => $rule,
					'wait'        => $wait,
					'limitNum'        => $limitNum
				
					);
					
			$this->db->where('productID',$productID);
			$this->db->update('pos_product',$datain);
			
			//concessions
			//clear order date
			$this->db->where('productID',$productID);
			$this->db->delete('pos_product_discount');
			$concessionsList = split(',',$concessions);
			$concessionsNumList = split(',',$concessionsNum);
			$num = count($concessionsList);
			for($i=0;$i<$num;$i++)
			{
				if($concessionsList[$i]!='')
				{
					$this->db->insert('pos_product_discount',array('productID'=>$productID,'num'=>$concessionsNumList[$i],'discount'=>$concessionsList[$i]))	;
					
					
				}
				
			}
			
			
			//
			
			
			
			$datain['productID'] = $productID;	 
			$product= $this->Product_model->getProductStock(array('productID'=>$productID),$this->data['shopID']);
			$result['product'] = $product['product'][0];
		}
		$result['result'] = true;
		echo json_encode($result);
		exit(1);	

	}
	function get_inshop_without_cs()
	{
		$product= $this->Product_model->getInshopProductWithoutCS($this->data['shopID'])	;
		$num = count($product);
		if($num>0)
		{
			$result['product'] = $product[rand(0,$num-1)];
			$result['result'] = true;
		}
		else $result['result'] = false;
		echo json_encode($result);
		exit(1);
	}
	
	
	function edit_product_card_sleeve()
	{
			$productID = $this->input->post('productID');	
			$cardSleeve = $this->input->post('cardSleeve');			
			//if($cardSleeve==0)$cardSleeve ='';		
			if($cardSleeve!='*')substr($cardSleeve,0,strlen($cardSleeve)-1);
			$datain = array(
					'timeStamp'   => date("Y-m-d H:i:s"),
					'cardSleeve'  => $cardSleeve
					);
					
			$this->db->where('productID',$productID);
			$this->db->update('pos_product',$datain);	
		$result['result'] = true;
		echo json_encode($result);
		exit(1);	
			
	}
	
	
	function get_card_sleeve()
	{
		
		$result['cardSleeve'] = $this->Product_model->getCardSleeve();
		$result['result'] = true;
		echo json_encode($result);
		exit(1);		
		
	}
	
	
	
	function product_open()
	{
		$productID = $this->input->post('productID');
		$openStatus = $this->input->post('openStatus');
		$this->db->where('productID',$productID);
		$this->db->update('pos_product',array('openStatus'=>$openStatus,'timeStamp'=>date("Y-m-d H:i:s")));
		$product= $this->Product_model->getProductStock(array('productID'=>$productID),$this->data['shopID']);
		$result['product'] = $product['product'][0];
		$result['result'] = true;
		echo json_encode($result);
		exit(1);	

	}
	function get_product_list()
	{
		$time = $this->input->post('time');
		$this->load->model('Product_model');
		$data['product'] = $this->Product_model->getProductList($time,$this->data['shopID']);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
	
	}
	function get_suppliers()
	{
		$this->load->model('Product_model');
		if($this->data['shopID']<1000)	$data['suppliers'] = $this->Product_model->getSuppliers();
		else $data['suppliers']  = array();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	function get_publisher()
	{
		$this->load->model('Product_model');
		$data['publisher'] = $this->Product_model->getPublisher();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	
	function new_product()
	{
		$barcode = $this->input->post('barcode');
		$price = $this->input->post('price');
		$minDiscount = $this->input->post('minDiscount');
	    $ZHName = $this->input->post('ZHName');
		$ENGName = $this->input->post('ENGName');
		$type = $this->input->post('productType');
		$category = $this->input->post('category');
		$productNum = $this->input->post('productNum');
		$buyDiscount = $this->input->post('buyDiscount');
		$buyPrice = $this->input->post('buyPrice');
		$purchaceDiscount = $this->input->post('purchaceDiscount');
		
		$suppliers = $this->input->post('suppliers');
		$language = $this->input->post('language');
		$openStatus = $this->input->post('openStatus');
		$publisher = $this->input->post('publisher');
		$comment = $this->input->post('comment');
		
		
		$datain = array(
				'productNum' =>$productNum ,
				'barcode'  => $barcode,
				'price' => $price,
				'minDiscount' => $minDiscount,
				'ZHName'     => $ZHName,
				'ENGName'  => $ENGName,
				'type'  => $type,
				'category' => $category,
				'buyDiscount' => $buyDiscount,
				'buyPrice'   => $buyPrice,
				'purchaseCount' =>$purchaceDiscount,
				'suppliers'   =>$suppliers,
				'language'    => $language,
				'openStatus'  => $openStatus,
				'publisher '  => $publisher,
				'comment'     => $comment, 
				'timeStamp'=>date("Y-m-d H:i:s")	
				);
				
				
		 $data['result'] = false;	
		if($this->Product_model->getProductStock(array('productNum' =>$productNum),$this->data['shopID'])==false&&$this->Product_model->getProductByBarcode($barcode)==false&&$this->data['level']>=50){		

						$data['result'] = true;	
						$maxID = $this->Product_model->getMaxID();
						$datain['productID'] = $maxID+1;
						$this->db->insert('pos_product',$datain); 
						$this->Product_model->updateNum($datain['productID'],0,$this->data['shopID']);
				

		}
		else $data['result'] = false;
		
	 

		echo json_encode($data);
		exit(1);	
        
	}
	function test()
	{
		
		$this->load->view('test')	;
	}
	function purchase_send()
	{
		$this->load->model('Order_model');
		$i = 0;$j = 0 ;
		foreach($_POST as $row)
		{
			switch ($i%2)
			{
				case 0:
					$datain[$j]['productID'] = $row;
				break;
				case 1;
					if($row<0&&$this->data['level']<50)$datain[$j++]['num'] = 0;
					else $datain[$j++]['num'] = $row;
				break;
					
			}	
			$i++;
			
		}
		$this->load->model('Product_model');
		
		if($i>0&&isset($datain))
		{
			foreach($datain as $row)
			{
				$this->Product_model->refreshProductNum($row['productID'] , $row['num'],$this->data['shopID']);
				$this->Order_model->reAllocateOrder($row['productID'] );
			}
			
		}
		$result['result'] = true;

		echo json_encode($result);
		exit(1);
		
	}
	
	
	function shipping_send()
	{
		
		$result['result'] = false;
		$i = 0;$j = 0 ;
		$shopID = $this->input->post('shopID');
		$shopType = $this->input->post('shopType');
		foreach($_POST as $row)
		{
			if($i>=2)
			switch (($i-2)%4)
			{
				case 0:
					$datain[$j]['productID'] = $row;
				break;
				case 1;
					$datain[$j]['price'] = $row;
				break;
				case 2;
					$datain[$j]['count'] = $row;
				break;				
				case 3;
					$datain[$j++]['num'] = $row;
				break;
					
			}	
			$i++;
			
		}
		if($shopID==(int)$this->data['systemInf']['shopID'].'000000') $result['result'] = false;
		if(isset($datain))
		{
			$postData['shipment'] = array(
				'shopID'=>$shopID,
				'shopType'=>$shopType,
				'time'=>date("Y-m-d H:i:s")	
			);
			$this->db->insert('shipment',$postData['shipment']);
			$postData['shipment']['shipmentID'] = $this->db->insert_id();
			
			$i=0;
			foreach($datain as $row)
			{
				$shipmentDetail[$i] = array(
					'shipmentID' =>$postData['shipment']['shipmentID'],
					'productID'	 =>$row['productID'],
					'num' 		 =>$row['num'],
					'sellPrice'  =>$row['count']*$row['price']/100
				);
				$this->Product_model->refreshProductNum($row['productID'] , -$row['num']);	
				$this->db->insert('shipment_detail',$shipmentDetail[$i]);
				$i++;
			}			
			$postData['postStr'] = json_encode($shipmentDetail);
			$getData = $this->paser->post($this->data['serverDomain'].'product/purchase_send',$postData,true);
			if(!isset($getData['result'])){
				//direct in buffer	
					$this->load->model('Buffer_model');
					$this->Buffer_model->addToBuf('product/purchase_send',$postData);
				
			}
			 
			$result['result'] = true;

			
			
		}
		echo json_encode($result);
		exit(1);
	}
	function delete()
	{
		$productID = $this->input->post('productID');
		$this->db->where('productID',$productID);
		$this->db->delete('pos_product'); 
		
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
	}
	function get_shipment_shop()
	{
		$this->load->model('Product_model');
		$type = $this->input->post('shop_type');
		if($type==0) $data= $getData =  $this->paser->post($this->data['serverDomain'].'system/get_shop',array(),true);
		else  $data['shopData'] = $this->Product_model->getShipmentShop();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);


	}
	function new_shipment_shop()
	{
		$name = $this->input->post('name');
		if($this->Product_model->getShipmentShopByName($name )) $data['result'] = false;
		else 
		{
			$this->db->insert('shipment_shop',array('name'=>$name));
			$data['shopID'] = $this->db->insert_id();
			$data['result'] = true;
		}
		echo json_encode($data);
		exit(1);		
		
	}
	function delete_shipment_shop()
	{
		$shopID = $this->input->post('shopID');
		$this->db->where('id',$shopID);
		$this->db->delete('shipment_shop');
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
	
		
	}
	
	function card_sleeve_edit()
	{
		
		$CSID = $this->input->post('id');
		$CSsize = $this->input->post('name');

		
		if($this->Product_model->getCardSleeveByID($CSID)==false)
		{
			$this->db->insert('pos_card_sleeve',array('CSsize'=>$CSsize,'timeStamp'=>date("Y-m-d H:i:s")));	
			
		}
		else
		{
			$this->db->where('CSID',$CSID);
			$this->db->update('pos_card_sleeve',array('CSsize'=>$CSsize,'timeStamp'=>date("Y-m-d H:i:s")));
		}
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
	}
	
	
	 
	function supplier_edit()
	{
		$supplierID = $this->input->post('id');
		$name = $this->input->post('name');
		$order = $this->input->post('order');
		if($order=='') $order = 99;
		
		if($this->Product_model->getSupplierByID($supplierID)==false)
		{
			$this->db->insert('pos_suppliers',array('name'=>$name,'order'=>$order,'timeStamp'=>date("Y-m-d H:i:s")));	
			
		}
		else
		{
			$this->db->where('supplierID',$supplierID);
			$this->db->update('pos_suppliers',array('name'=>$name,'order'=>$order,'timeStamp'=>date("Y-m-d H:i:s")));
		}
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
		
		
	}
	
	

	
	function batch()
	{
		$handle = fopen('D:\text.txt','r');
		$this->load->model('Product_model');
		while(!feof($handle))
		{
			$contents = fgets($handle,10);
			
			$contents_array = explode(" ",$contents);
			$barcode = $contents_array[0];
			$num = $contents_array[1];

			$final_num = $this->Product_model->refreshProductNum($barcode , $num);
		}
		fclose($handle);
		$result['result'] = true;
		$result['num'] = $final_num;
		echo json_encode($result);
		exit(1);
	}
	function pha_bg_create()
	{
		
			$url = 'http://www.phantasia.tw/bg_controller/pos_bg_create';
			$result = $this->paser->post($url,$_POST,false);
			echo $result;	
		
		
	}
	function pha_search()
	{
		$url = 'http://www.phantasia.tw/bg_controller/search';
			$result = $this->paser->post($url,$_POST,false);
		echo $result;
		
	}
	function bid_chk()
	{
		ini_set('max_execution_time',360000);
		$this->load->model('Product_model');
		$product = $this->Product_model->getAllProduct();
		$i = 0 ; 
		foreach($product as $row)
		{
				sleep(1);
				$data = $this->paser->post('http://www.phantasia.tw/bg/chk_bg',array('bid'=>$row['phaBid']),true);
				$this->db->where('productID',$row['productID']);
				
				
				
				if($data['result']==true&&$row['phaBid']!=0)
				{
					//echo 'true|'.$row['phaBid'].'<br/>';
					
					$this->db->update('pos_product',array('bidExist'=>1))	;
					
				}
				else {
					echo 'false|'.$row['phaBid'].'<br/>';
					//print_r($data);
					$this->db->update('pos_product',array('bidExist'=>0));
				}
				$i++;
		}
		echo 'finish';
		
		
	}
	
	function update_price()
	{
		ini_set('max_execution_time',360000);
		$this->load->model('Product_model');
		$product = $this->Product_model->getAllProduct();
		$i = 0 ; 
		foreach($product as $row)
		{
				
				if($row['phaBid']!=0) 
				{
					sleep(1);
					$data = $this->paser->post('http://www.phantasia.tw/bg_controller/update_price',array('bid'=>$row['phaBid'],'price'=>$row['price'],'category'=>$row['category']),true);
					if($data['result']==false) echo 'wrong';
				}
				
		
		}		
		echo 'finish';
	}
	
	
	

	function announce()
	{
	   $result = '<h1>新增商品通告</h1>';
	   $result .='<div>新增：</div>';
	
		  foreach($_POST as $row)
		  {
			
			  $product= $this->Product_model->chkProductByProductID($row);
			  $result.=$product['ZHName'].'('.$product['ENGName'].')['.$product['language'].']<br/>';
			  $result.=$row['url'].'<br/>';
			  $result.=$row['comment'].'<br/>';
			  $result.='=====================<br/>';
		  }	
		
		$result .='<div>請上訂購商品選購喔！</div>';
		$result .='<div>幻遊天下　產品部</div>';
		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
		$mailTo = array('lintaitin@gmail.com','phoenickimo@hotmail.com','phantasia0000@gmail.com');
		$time = getdate();
		$shopList = $this->System_model->getShop();
		foreach($shopList as $row)
		{
			$mailTo[] = $row['email'];
		}
		foreach($mailTo as $row)
		{
			mail($row,$time['year'].'/'.$time['mon'].'/'.$time['mday'].'新增商品通告',$result,$headers);
		}
		$data['result'] = true;

		echo json_encode($data);
		exit(1);				
	}
	function turn_zero()
	{
		$data['result'] = false;
		if($this->data['shopID']==0)	
		{
				$this->db->where('shopID',0);
				$this->db->update('pos_product_amount',array('num'=>0));
				$data['result'] = true;
			
		}
		echo json_encode($data);
		exit(1);		
		
	}
	function flow_rate()
	{

		
		
		
	}
	function get_product_IO()
	{
		$this->load->model('Product_model');
		$offset = $this->input->post('offset');
		$num = $this->input->post('num');
		$shopID = $this->input->post('shopID');
		$query = $this->input->post('query');
		$from = $this->input->post('from');
		$to = $this->input->post('to').' 23:59:59';
		$data['product'] = $this->Product_model->getProductIO($shopID,$offset,$num,$query,$from,$to);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
	}	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */