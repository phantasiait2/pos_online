<?php

class Product extends POS_Controller {

	function Product()
	{
		
		
		parent::POS_Controller();
		$this->data['css'] = $this->preload->getcss('pos');
		$this->data['js'] = $this->preload->getjs('barcode');
		$this->load->model('Product_model');
			
	}
	
	function barcode()
	{
		$this->data['js'] = $this->preload->getjs('barcode');
		$this->load->helper('barcode');
		$this->data['display'] = 'barcode_view';
		$this->load->view('template',$this->data);
		
	}
	
	
	function index()
	{

		
		$this->data['js'] = $this->preload->getjs('jquery.tablesorter');
		$this->data['js'] = $this->preload->getjs('pos_product');
		$this->data['js'] = $this->preload->getjs('pos_product_query');
		$this->data['display'] = 'product';
		$this->load->view('template',$this->data);	
	}
	function get_product()
	{
		$barcode = $this->input->post('barcode');
		$productID = $this->input->post('productID');
		if($barcode!=0)	 $data['product']= $this->Product_model->getProductByBarcode($barcode);
		if(empty($data['product']))$data['product']= $this->Product_model->chkProductByProductID($productID);
		if($data['product']==false)$data['result'] = false;
		else $data['result'] = true;
		
		echo json_encode($data);
		exit(1);
	}
	function get_product_info()
	{
		$barcode = $this->input->post('barcode');
		$r =  $this->paser->post($this->data['serverDomain'].'product/get_product',array('barcode'=>$barcode),true);
		if(isset($r['result'])&&$r['result']==true);
		else $r['result'] = false;
		echo json_encode($r);
		exit(1);
	}
	
	
	function get_suppliers()
	{
		$this->load->model('Product_model');
		$data['suppliers'] = $this->Product_model->getSuppliers();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}	
	
	function get_product_stock()
	{
		
	//	$data['totalNum'] =	count($this->Product_model->getProductStock($_POST));
	//	$find['start'] = $this->input->post('start');
	//	$find['num'] = $this->input->post('num');
		$result = $this->Product_model->getProductStock($_POST);
		$data['product'] = $result['product'];
		$data['totalNum'] = $result['totalNum'];
		if($data['product']==false)$data['result'] = false;
		else $data['result'] = true;
		 
		echo json_encode($data);
		exit(1);		
		
		
	}
	
	function sell_record()
	{
		
		$productID= $this->input->post('productID');
		$start= $this->input->post('start');
		
		$data['sellRecord'] = $this->Product_model->getSellRecord($productID,$start,5);
		
		if(count($data['sellRecord'])>0) $data['result'] = true;
		else $data['result'] = false;

	 	echo json_encode($data);
		exit(1);		
		
	}
	
	function check_back()
	{
		
		
		$i = 0;$j=0;
		foreach($_POST as $row)
		{
			
				switch(($i)%5)
				{
					case 0:
						$product[$j]['productID'] = $row;
					break;
					case 1:
						$product[$j]['sellID'] = $row;
					break;
					case 2:
						$product[$j]['sellPrice'] = $row;
					break;	
					case 3:
						$product[$j]['comment'] = $row;
					break;	
					
					case 4:
						$product[$j++]['num'] = $row;
					break;						
					
					
				}	
		
			$i++;
		}
		
		if($i>0)
		{
		$total = 0 ;$i=0;
		foreach($product as $row)
		{
			$cost = $this->Product_model->getProductSellCost($row['sellID']);
			$datain[$i] = array(
				'productID' =>$row['productID'],
				'cost' =>$cost,
				'backTime'=>date("Y-m-d H:i:s"),
				'sellID' =>$row['sellID'],
				'sellPrice' =>$row['sellPrice'],
				'num' =>$row['num'],
				'comment'=>$row['comment']
			)	;
			$this->db->insert('product_back',$datain[$i]);
			$this->Product_model->updateNum($row['productID'],$row['num'],$cost);
			$total+=$row['sellPrice']*$row['num'];
			$i++;
			
		}
				$this->load->model('Accounting_model');
		$cash_data = $this->Accounting_model->registerIO(0,$total,0,$this->data['aid'],'back',$this->data['account'],1);		
		$result['checkID'] =$cash_data['id'] ;
		$postData['postStr'] = 	json_encode($datain);
		$this->load->model('Buffer_model');
		$this->Buffer_model->addToBuf('product/check_back',$postData);
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
		
		$this->load->model('Buffer_model');
		$i = 0;$j=0;$key = false;
		$data = array(); 

		$postNum = count($_POST);
		$result['total'] = 0;
		$result['result'] = 0;
		foreach($_POST as $row)
		{
			
			if($i+2==$postNum)
			{
				$credit = $row;	//for credit card
			
			}
			else if($i+1==$postNum)
			{
				$memberID = $row;
				
			}
			else
			{
				switch($i%6)
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
						$data[$j]['count'] = $row;
						$data[$j]['sellPrice'] = round($data[$j]['price']*$data[$j]['count']/100);
						$result['total'] +=	$data[$j]['sellPrice']*$data[$j]['num'];
					break;		
					case 5:
						$data[$j]['bonus'] = $row;
						$j++;
					break;								
				}
			}
			$i++;
			
		}
			
		$this->load->model('Member_model');
		$this->load->model('Accounting_model');
		if($this->Member_model->getMemberByID($memberID,$this->data['shopID'])==false &&  $memberID!='')
		{
			
			$postData['memberID'] = $memberID;
			$getData = $this->paser->post($this->data['serverDomain'].'member/get_member',$postData,true);	
			if($getData['result']==false) $memberID = 999999;
			
		}
		if($memberID=='')$memberID = 999999;
		$this->load->model('Accounting_model');
		$cash_data = $this->Accounting_model->registerIO($result['total']-$credit,0,$credit,$this->data['aid'],'sales',$this->data['account'],1);		
		$result['checkID'] =$cash_data['id'] ;
		$i = 0 ;
		$totalBonus = 0 ;
		$result['rentString'] = '';
		foreach($data as $row)
		{
				$ret = $this->Product_model->getPurchasePrice($row['productID'],$row['num']);	
				if( $row['num']==0)$eachBonus = 0;
				else 
				{
					if($row['num']!=0)$eachBonus  = $row['bonus'] / $row['num'];
				}
				
				foreach($ret as $col)
				{
					if($row['productID']=='8880014' )
					{
						if($this->data['shopID']>4)$col['purchasePrice'] = 50;
						else $col['purchasePrice'] = 5;
					}
					$datain[$i] = array(
					'productID'  => $row['productID'],
					'memberID'  => $memberID,
					'num'       => $col['num'],
					'sellPrice' => round($row['price']*$row['count']/100),
					'time'      => date("Y-m-d H:i:s"),
					'checkID'	=> $cash_data['id'],
					'purchasePrice' =>$col['purchasePrice'],
					'purchaseID' =>$col['purchaseID'],
					'bonus' =>$eachBonus * $col['num'] 
					);
					$totalBonus += $row['bonus'];
					$this->db->insert('product_sell',$datain[$i]);
					$datain[$i]['sellID'] = $this->db->insert_id();
					
					//租借回傳sellID
					
					if($row['productID']=='8880004' || $row['productID']=='8880005' ||$row['productID']=='8880006' )	
					{
						$result['rentString'].= $datain[$i]['sellID'].',';
					}
					
					$this->Product_model->updateNum($row['productID'],-$col['num'],$col['purchasePrice']);
					$i++;
				}
			
			
			
			
			if ($row['productID'] =='8880016') $data['speicalMember'][1] = $row['num'];
			else $data['speicalMember'][1]  = 0;
			if ($row['productID'] =='8880017') $data['speicalMember'][1] = $row['num'];
			else $data['speicalMember'][2]  = 0;
			
			
			
			
			$this->Product_model->updatePruchaseSellNum($row['productID'],$row['num']);
			
			
				
		}
		$result['myBonus'] = $this->Member_model->updateMemberBouns($memberID,$totalBonus);
		if($i!=0)
		{
			$postData['postStr'] = 	json_encode($datain);
			//$getData = $this->paser->post($this->data['serverDomain'].'product/pay',$postData,true);
			//direct in buffer	do not pase to server this time
			
			$this->Buffer_model->addToBuf('product/pay',$postData);
			$result['result'] = 1;
		}
//		$result['invoice'] = $this->data['systemInf']['invoice'];
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
			$datain = array(
					'productNum' =>$productNum,
					'barcode' => $barcode,
					'price' => $price,
					'minDiscount' => $minDiscount,
					'ZHName'     => $ZHName,
					'ENGName'  => $ENGName,
					'type'     => $type,
					'category' =>$category
					);
			$this->db->where('productID',$productID);
			$this->db->update('product',$datain);
			$datain['productID'] = $productID;
			$getData = $this->paser->post($this->data['serverDomain'].'product/edit_send',$datain,true);
			if(!isset($getData['result'])){
				//in buffer	
				$this->load->model('Buffer_model');
				$this->Buffer_model->addToBuf('product/edit_send',$datain);
				
				$result['result'] = -1;
				
			}		 
			$product= $this->Product_model->getProductStock(array('productID'=>$productID));
			$result['product'] = $product[0];
		}
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
	function get_product_list()
	{
		$time = $this->input->post('time');
		$this->load->model('Product_model');
		$data['product'] = $this->Product_model->getProductList($time);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
	
	}
	function test()
	{
		
		$this->load->view('test')	;
	}
	function purchase_send()
	{
		
		$i = 0;$j = 0 ;
	
		foreach($_POST as $row)
		{
			switch ($i%3)
			{
				case 0:
					$datain[$j]['productID'] = $row;
				break;
				case 2;
					if($row<0&&$this->data['level']<50)$datain[$j++]['num'] = 0;
					else $datain[$j++]['num'] = $row;
					break;	
				case 1;
					$datain[$j]['purchasePrice'] = $row;
	
				break;
					
			}
				
			$i++;
			
		}
	
		$this->load->model('Product_model');
		$j = 0;$result['package'] = array();
		if($i>0&&isset($datain))
		{
			
			foreach($datain as $row)
			{
				$datain[$j++]['time'] = $this->Product_model->refreshProductNum($row['productID'] , $row['num'],$row['purchasePrice']);
				
				$ret =  $this->Product_model->chkPackage($row['productID']);
				
				if($ret!=false) $result['package'][] = $ret;
			}
			$postData['postStr'] = json_encode($datain);
				if(!isset($getData['result'])){
					//direct in buffer	
						$this->load->model('Buffer_model');
						$this->Buffer_model->addToBuf('product/purchase_send',$postData);
					
				}
		}
		$result['result'] = true;

		echo json_encode($result);
		exit(1);
		
	}
	function chk_package()
	{
		$productID = $this->input->post('productID');
		$result['result'] = true;
		$ret =  $this->Product_model->chkPackage($productID);
		if($ret!=false) $result['package'][] = $ret;
		else $result['result'] = false;
		echo json_encode($result);
		exit(1);
	}
	function package_send()
	{
			
		$i = 0;$j = 0 ;
	
		foreach($_POST as $row)
		{
			switch ($i%2)
			{
				case 0:
					$datain[$j]['productID'] = $row;
				break;
				case 1;
					$datain[$j++]['num'] = $row;
				break;
					
			}
				
			$i++;
			
		}
		$this->load->model('Product_model');
		foreach($datain as $row)
			{
				$package =  $this->Product_model->chkPackage($row['productID'],false);			
			
				if(isset($row['num']))
				{
					$ret = $this->Product_model->getPurchasePrice($row['productID'],$row['num']);	
					foreach($ret as $col)
					{
						if($package['unitToBox']>0)
						{
						//盒出
						$this->Product_model->refreshProductNum($package['boxProductID'] , -$col['num'],$col['purchasePrice']);
						//包進
						$this->Product_model->refreshProductNum($package['unitProductID'] , $col['num']*$package['unitToBox'],round($col['purchasePrice']/$package['unitToBox'],0));
						}
					}
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
		$barcode = $this->input->post('barcode');
		$this->db->where('barcode',$barcode);
		$this->db->delete('product'); 
		
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
	}
	function get_shipment_shop()
	{
		$this->load->model('Product_model');
		$type = $this->input->post('shop_type');
		if($type==0) $data= $getData =  $this->paser->jumpPost($this->data['serverDomain'].'system/get_shop',array(),true);
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
	function get_abstract_product()
	{
	 	$data['abstract']=$this->Product_model->getAbstractProduct();			
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
	}

	function get_all_dining_product()
	{
		
		$data['dining']=$this->Product_model->getAllDiningProduct();	
		$data['diningHide']=$this->Product_model->getAllDiningProduct(false);			
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}	
	
	function edit_dining_product()
	{
	
		$productID = $this->input->post('productID');
		$datain['barcode'] = $this->input->post('barcode');
		$datain['ZHName'] = $this->input->post('ZHName');
		$datain['ENGName'] = $this->input->post('ENGName');
		$datain['price'] = $this->input->post('price');
		$datain['suppliers'] = $this->input->post('suppliers');
		;
		if($datain['price']!=0)	$datain['purchaseCount'] = round($this->input->post('purchase')/$datain['price'],2)*100;
		else 	$datain['purchaseCount'] = 0 ;
		//$datain['suppliersName_'] = $this->input->post('suppliersName_');
		$datain['minDiscount'] = $this->input->post('minDiscount');
		$this->db->where('productID',$productID);
		$this->db->update('product',$datain);
		$datain['productID'] = $productID;
		  $this->paser->post($this->data['serverDomain'].'product/edit_dining',$datain,false);
		
		
		$data['datain'] = $this->Product_model->getAllDiningProduct(true,$productID);		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	function dining_status()
	{
		
		$productID = $this->input->post('productID');
		$this->db->where('productID',$productID);
		$query = $this->db->get('dining');
		if($query->num_rows()>0) 
		{
			$this->db->where('productID',$productID);
			 $this->db->delete('dining');
		}
		else
		{
			$this->db->insert('dining',array('productID'=>$productID));
		}
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	function get_dining_product()
	{
		$data['dining']=$this->Product_model->getDiningProduct();			
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	function edit_product_card_sleeve()
	{
		echo $this->paser->post($this->data['shipmentDomain'].'product/edit_product_card_sleeve',$_POST,false);
		
		
	}

	
	function sanguosha_check_id()
	{
		$_POST['shopID'] = $this->data['shopID'];
		echo $this->paser->post($this->data['shipmentDomain'].'product/sanguosha_check_id',$_POST,false);
		
	}
	function sanguosha_send()
	{
		$_POST['shopID'] = $this->data['shopID'];
		echo $this->paser->post($this->data['shipmentDomain'].'product/sanguosha_send',$_POST,false);
		
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
	function get_dining_max_productNum()
	{
		$this->load->model('Product_model');
		$productNum = $this->Product_model->getDiningMaxProductNum($this->data['shopID']);
		$result['productNum'] = 'DN'.str_pad($this->data['shopID'],3,0,STR_PAD_LEFT ).str_pad(((int)substr($productNum,5)+1),4,0,STR_PAD_LEFT);
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
	}
	
	function new_product()
	{
		$barcode = $this->input->post('barcode');
		$productNum = $this->input->post('productNum');
		$price = $this->input->post('price');
		$minDiscount = $this->input->post('minDiscount');
	    $ZHName = $this->input->post('ZHName');
		$ENGName = $this->input->post('ENGName');
		$suppliers = $this->input->post('suppliers');
		$purchasePrice = $this->input->post('purchasePrice');
		$type = 4; //餐飲
		$purchaseCount = $this->input->post('purchaseCount');
		$suppliers = $this->input->post('suppliers');
		$openStatus = 0;//關閉
		
		
		$datain = array(
				'productNum'  => $productNum ,
				'barcode'     => $barcode,
				'price'       => $price,
				'purchaseCount' => $purchaseCount,
				'minDiscount' => $minDiscount,
				'ZHName'      => $ZHName,
				'ENGName'     => $ENGName,
				'type' 		  => $type,				
				'suppliers'   => $suppliers,
				'openStatus'  => $openStatus,
				
				);
				
			
		 $data['result'] = false;	
 		if($this->Product_model->getProductStock(array('productNum' =>$productNum))==true)$data['reason'] = '編號重覆';
		else if($this->Product_model->getProductByBarcode($barcode)==true)$data['reason'] = '條碼重覆';
		else if($this->data['level']< 50)$data['reason'] = '權限不足';
		else 	 $data['result'] = true;		
	
		 
				if($data['result'] )
				{		
				
						$data['result'] = true;	
						//insert to remote
						$ret = $this->paser->post($this->data['serverDomain'].'product/new_product',$datain,true);
						
						if($ret['result']==true) 
						{
							
					
							$datain['productID'] = $ret['productID'];
							$this->db->insert('product',$datain); 
							//$this->Product_model->updateNum($datain['productID'],0,$this->data['shopID'],$buyPrice);
							$data['productID'] = $datain['productID'];
							
							
						}
						else 
						{
							$data['result'] = false;
							$data['reason'] = '與遠端連線失敗，請恢復網路後再新增';
						}

			}
		
	 

		echo json_encode($data);
		exit(1);	
		
		
		
		
	}
	
	
	function updateAllTotalCost()
	{
		$this->load->model('Product_model');
		$this->Product_model->updateAllTotalCost();	
	}
	
	function updateInshopAllTotalCost()
	{
		$this->load->model('Product_model');
		$this->Product_model->updateInshopAllTotalCost();
	}
	
	function updateAlltype()
	{
		$this->load->model('Accounting_model');	
		$this->Accounting_model->updateAlltype();		
		
	}	
	
	function updateAllSellTotalCost()
	{
		$this->load->model('Product_model');	
		$this->Product_model->updateAllSellTotalCost();		
		
	}	

	function  save_product()
	{
		
		echo $this->paser->post($this->data['serverDomain'].'product/save_product',$_POST,false);
		
	}
	

	function get_damage()
	{
		$this->load->model('Product_model');
		
		$checkID = $this->input->post('checkID');
	
		$this->load->model('Product_model');
		$ret = $this->paser->post($this->data['serverDomain'].'product/get_chk_product',array('checkID'=>$checkID,'order'=>1),true);
		$countStock = $ret['product'];
	
		$countSize = count($countStock);
		$ret = $this->Product_model->getProductStock(array('purchased'=>1,'order1'=>'productID','sequence1'=>'ASC'));
		$recordStock = $ret['product'];
		$recordSize = count($recordStock);
	
		$flag = true;
		$i = 0 ; $j = 0 ;
		if($i>=$countSize||$j>=$recordSize)$flag = false;
		while($flag)
		{
			
			if($countStock[$i]['status']==1 )
			{
				if($countStock[$i]['nowNum']>$countStock[$i]['realNum'])$data['product'][] = $countStock[$i];
	
				$i++;
				if($i>=$countSize||$j>=$recordSize)$flag = false;
				continue;
			}
			
			if($countStock[$i]['productID']>$recordStock[$j]['productID'])	
			{
				
				if($recordStock[$j]['nowNum']>0 && $recordStock[$j]['productID']!=$countStock[$i-1]['productID'])
				{
					$r = $recordStock[$j];
					$r['realNum'] = 0;
					$r['status'] = 0;
					$data['product'][] = $r;
				
				
				}
					$j++;
			}
			else if ($countStock[$i]['productID'] == $recordStock[$j]['productID'])
			{
				if($recordStock[$j]['nowNum']>$countStock[$i]['realNum'])
				{
					$r = $recordStock[$j];
					$r['realNum'] = $countStock[$i]['realNum'];
					$r['status'] = 0;
					$data['product'][] = $r;
				
				
				}
					$j++;
					$i++;
				
			}
			else $i++;
			if($i>=$countSize||$j>=$recordSize)$flag = false;
			
		}
		for($j;$j<$recordSize;$j++)
		{
			if($recordStock[$j]['nowNum']>0 &&  $recordStock[$j]['productID']!=$recordStock[$j-1]['productID'])
				{
					$r = $recordStock[$j];
					$r['realNum'] =0;
					$r['status'] = 0;
					$data['product'][] = $r;
				
				
				}
			
		}
		
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
		
		
	}
	
	
	
	function get_overage()
	{
		echo	$this->paser->post($this->data['serverDomain'].'product/get_overage',$_POST,false);
		
		
		
		
	}
	
	function stock_recover()
	{
		$checkID = $this->input->post('checkID');
		$productID = $this->input->post('productID');
		$nowNum = $this->input->post('nowNum');
		$realNum = $this->input->post('realNum');
		$status = $this->input->post('status');
		$data =  $this->paser->post($this->data['serverDomain'].'product/stock_recover',$_POST,true);
	
		
		if($data['product']['status']==0)
		{
			
			
			$this->Product_model->refreshProductNum($productID , ($realNum-$nowNum)*$status,0);
		}
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	function save_chk_product()
	{
		$i = 0;$j = 0 ;
		
		foreach($_POST as $row)
		{
			if($i==0) $checkID = $row;
			else
			switch ($i%3)
			{
				case 1:
					$datain[$j]['productID'] = $row;
				break;
				case 2;
					$datain[$j]['nowNum'] = $row;
				break;
				case 0:
					$datain[$j++]['realNum'] = $row;
				break;
				
					
			}	
			$i++;
			
		}
		$this->load->model('Product_model');
	
		
		$k = 0 ;
		if($i>0&&isset($datain))
		{
			$this->paser->post($this->data['serverDomain'].'product/save_chk_product',array('checkID'=>$checkID,'datainStr' => json_encode($datain)),false);
			
			
		}
		
		$result['result'] = true;

		echo json_encode($result);
		exit(1);
	
		
	
	
	
	}
	function chk_product_delete()
	{

		echo	$this->paser->post($this->data['serverDomain'].'product/chk_product_delete',$_POST,false);
		
	}
	
	function new_chk_product()
	{
		echo $this->paser->post($this->data['serverDomain'].'product/new_chk_product',array(),false);

	}
	function get_chk_product_list()
	{
		
		echo $this->paser->post($this->data['serverDomain'].'product/get_chk_product_list',array(),false);
		
	}
	 
	
	
	function get_chk_product()
	{
		echo $this->paser->post($this->data['serverDomain'].'product/get_chk_product',$_POST,false);
		
		
	}
	
	
	 
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */