<?php

class Product extends POS_Controller {

	function Product()
	{
		parent::POS_Controller();
		$this->data['css'] = $this->preload->getcss('pos');
		$this->data['js'] = $this->preload->getjs('barcode');
		$this->data['js'] = $this->preload->getjs('pos_allocate');
		
		if($this->uri->segment(2)!='magic_order_to_shipment')
		{
			$this->load->model('Order_model');
			$this->load->model('Product_model');
		}
			
	}
	
	
	function index()
	{
		
		$this->data['css'] = $this->preload->getcss('jquery-ui-1.8.16.custom');
		$this->data['js'] = $this->preload->getjs('jquery-ui-1.8.16.custom.min');
		$this->data['js'] = $this->preload->getjs('jquery.tablesorter');
		$this->data['js'] = $this->preload->getjs('jquery.fixedheadertable');
		$this->data['js'] = $this->preload->getjs('pos_product');
		$this->data['js'] = $this->preload->getjs('pos_product_query');
		$this->data['js'] = $this->preload->getjs('pos_discount');
		if($this->data['shopID']!=0)
		{
			if($this->data['level']==5)	redirect('/order/online');
			 redirect('/accounting');
		}
        if($this->data['level']==-1)	redirect('/supplier/product');
		$this->data['display'] = 'product';
		$this->load->view('template',$this->data);	
	}
	function get_product()
	{
		$barcode = $this->input->post('barcode');
		$productID = $this->input->post('productID');
		if($barcode!=0)	 $data['product']= $this->Product_model->getProductByBarcode($barcode);
		if(empty($data['product']))$data['product']= $this->Product_model->getProductByProductID($productID);
		if($data['product']['phaBid']!=0) $data['product']['img'] =  'https://www.phantasia.tw/phantasia/upload/bg/home/b/'.$data['product']['phaBid'].'.jpg';
		else $data['product']['img'] = 'http://shipment.phantasia.com.tw/pos_server/upload/product/img/'.$data['product']['productID'].'.jpg';
			

		$data['productOrderRule']= $this->Product_model->getProductOrderRule($data['product']['productID']);
		
	
	
		if($data['product']==false)$data['result'] = false;
		else $data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	function chk_top_product()
	{
		
		$productID = $this->input->post('productID');
		$data['result'] = $this->Product_model->chkTopProduct($productID);
		echo json_encode($data);
		exit(1);
	}
	
	function get_last_product()
	{
		$data['maxID'] = $this->Product_model->getMaxID();	
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
	}
	
	function img_upload()
	{
		$data['productID'] = $this->uri->segment(3);
		$data['product'] = $this->Product_model->getProductByProductID($data['productID']);	
		/*
		echo $data['productID'].'<br/>';
		echo '<form name="gallery_photo_form" enctype="multipart/form-data"  action="/product/photo_upload" method="post">';
		echo '<input type="hidden" name="productID" value="'.$data['productID'].'">';
		echo '<input type="file" name="file_name">';
		echo '<input type="submit"  class="PHAButton" style="margin-right:10px" value="upload" />';
		echo '</form>';
		*/
		$this->load->view('img_upload',$data);
		
	}
	function war_games_stock()
	{
		$shopID = $this->uri->segment(3);
			$this->load->model('Order_model');
			$this->load->model('Product_model');
		$data = $this->Order_model->getConsignment($shopID,2013,2);
	
	
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		foreach($data as $row)
		{
			
			if($row['publisher']=='戰棋會')	echo $row['ZHName'].',',$row['remainNum'].','.$row['totalCost'].','.round($row['price']*0.74).'<br/>';
			
		}
		
	}
	
	function get_last_price()
	{
		$productID = $this->input->post('productID');	
     
		$data['purchasePrice'] = $this->Product_model->getLastPrice($productID,$this->data['shopID']);	
		 $data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	
	
	
	
	function photo_upload()
	{
			
	
		$productID = $this->input->post('productID');
		$filename=$_FILES['file_name']['tmp_name'];
		if(empty($filename))redirect('/err/message/1');
		$img_info = getimagesize($filename);
		$width    = $img_info['0'];
		$height   = $img_info['1'];
		$img_type = $img_info['2'];
		if  (filesize($filename)>4000000) my_msg('圖檔太大，請壓縮後再上傳',$_SERVER['HTTP_REFERER']);
		else
		{
			
			switch ($img_type)
			{
				case 1: 
					$im = imagecreatefromgif($filename);
					break;
				case 2: 
					$im = imagecreatefromjpeg($filename);  
					break;
				case 3: 
					$im = imagecreatefrompng($filename); 
					break;
				default: 
					return 'Image Type Error!';  
					break;
			}
			/* 先建立一個 新的空白圖檔 */
			$newim = imagecreatetruecolor($width, $height);
			imagecopy($newim, $im, 0,0,0,0, $width, $height);
			imagejpeg($newim, $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/product/img/temp/'.$productID.'.jpg', 75);
			
			$filename =$_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/product/img/temp/'.$productID.'.jpg';
			$b=$_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/product/img/'.$productID.'.jpg';
			ImageResize($filename, $b,600, 1000, 100);
			unlink($filename);
		
			echo 'OK';
		}
		
	}
	
	function get_product_stock()
	{
		
		$find['barcode'] = $this->input->post('barcode');
		$find['productNum'] = $this->input->post('productNum');
		$find['query'] = $this->input->post('query');
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
        $find['hide'] = $this->input->post('hide');
        $find['xclass'] = $this->input->post('xclass');
	
		$result = $this->Product_model->getProductStock($find,$this->data['shopID']);
		$data['product'] = $result['product'];
		$data['totalNum'] = $result['totalNum'];
		if($data['product']==false)$data['result'] = false;
		else $data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
		
		
	}
	function get_eliminate_product()
	{
		
		$shopID = $this->input->post('shopID');
		$data['notProduct'] = $this->Product_model->getEliminateProduct($shopID);
		if($data['notProduct']==false)$data['result'] = false;
		else $data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
		
		
		
	}
	
	
	function stock_agent_mail()
	{
		ini_set('max_execution_time',360000);
		$url = 'http://shipment.phantasia.com.tw/product/stock_agent';
		$result = $this->paser->post($url,array(),false);
		//$this->Mail_model->myEmail('lintaitin@gmail.com,phoenickimo@gmail.com,phantasia.pm@gmail.com,phantasia.pa@gmail.com','應訂商品通知'.date("Y-m-d H:i:s"),$result);
        echo 'OK';
	}
	function my_flow()
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
		 $r = $this->Product_model->getProductSellRecord($productID,$time,$this->data['shopID']);
		 $data['result'] = true;
		
		 $data['flowNum'] = $r['sellNum']/3;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	function stock_agent()
	{
		ini_set('max_execution_time',360000);
		$data['product'] = $this->Product_model->getTopProduct();
		
		$data['notProduct'] = array();
		$data['notProduct'] = $this->Product_model->getNotTopProduct();
		$this->load->view('stock_agent',$data);
		
	}
	
	function stock_health()
	{
		ini_set('max_execution_time',360000);
		$data['product'] = $this->Product_model->getTopProduct();
		$data['notProduct'] = $this->Product_model->getNotTopProduct();
		
		$result['comNum'] =0;
		$result['sellNum'] =0;
		foreach($data['product'] as $row)
		{
			$result['comNum'] += $row['comNum'];
			$result['sellNum']+=$row['sellNum'];
			$row['val'] = $row['sellNum'];
			$result['product'][] = $row;
			
		}
		foreach($data['notProduct'] as $row)
		{
			$result['comNum'] += $row['comNum'];
			$result['sellNum']+=$row['sellNum'];
			$row['val'] = $row['sellNum'];
			$result['product'][] = $row;
		}
			usort($result['product'],'cmpValue');
		$this->load->view('stock_health',$result);
		
		
	}

	function a()
	{
			
			$query = $this->db->get('pos_cheap_product');
			$r = $query->result_array();
			foreach($r as $row)
			{
				$this->db->where('productID',$row['productID']);
				$this->db->delete('pos_order_rule');
				
				$this->db->insert('pos_order_rule',array('productID'=>$row['productID'],'distributeType'=>0,'discount'=>50,'num'=>0));
				$this->db->insert('pos_order_rule',array('productID'=>$row['productID'],'distributeType'=>1,'discount'=>50,'num'=>0));
				$this->db->insert('pos_order_rule',array('productID'=>$row['productID'],'distributeType'=>2,'discount'=>50,'num'=>0));
				$this->db->insert('pos_order_rule',array('productID'=>$row['productID'],'distributeType'=>15,'discount'=>50,'num'=>0));
				
			}
			
			

	
	}
	



	function get_not_top_product()
	{
 		ini_set('max_execution_time',360000);
		$data['notProduct'] = $this->Product_model->getNotTopProduct();
		if($data['notProduct']==false)$data['result'] = false;
		else $data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
	}
	function   product_flow_update()
    {
        	ini_set('max_execution_time',360000);
        $this->Product_model->flowUpdate();
        $this->Product_model->topProductUpdate();
        
        echo 'ok';
        
        
    }
	
	function rent()
	{
		$data['rent'] = $this->Product_model->getRentTimes(0);
		$this->load->view('rent_times',$data);
		
	}
	
	function get_top_product()
	{
		ini_set('max_execution_time',360000);
		$find['topProduct'] = $this->input->post('topProduct');
;
		$data['product'] = $this->Product_model->getTopProduct();
	
		//$data['notProduct'] = $this->Product_model->getNotTopProduct();
		if($data['product']==false)$data['result'] = false;
		else $data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
	}
	
	function top10_product()
	{
		$productID = $this->input->post('productID');
		$top10 = $this->input->post('top10');
		$this->db->where('productID',$productID);
		$this->db->update('pos_top_product',array('top10'=>$top10));
		 $data['result'] = true;
		echo json_encode($data);
		exit(1);	
		
	}
	function add_top_product()
	{
		
		$datain['productID'] = $this->input->post('productID');
		if( $this->Product_model->chkTopProduct($datain['productID'])==false)	$this->db->insert('pos_top_product',$datain);
		$data['result'] = true;
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
	
		if($bid==0) $bid = $this->uri->segment(3);
		$shopList =  $this->Product_model->getShopAmountByBid($bid);
		
		$data['product'] = $this->Product_model-> getProductByBid($bid);
		 $this->Product_model->cardSleeveInf($data['product'] ,false);
	
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
					$data['productStatus'] = true;
				} 
				else if(isset($data['product']['openStatus'])&&$data['product']['openStatus']==1)
				{
					$openToken = true;
					$data['productStatus'] = true;
					
				}
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
	
	function already_order()
	{
		$productID = $this->input->post('productID');
		$this->load->model('Order_model');
		$data['pre'] = $this->Order_model->getPreTime($productID);
		$data['comNum'] = $this->Order_model->getAvailableNum($productID);

		if(empty($data['pre']))$data['result'] = false;
		else $data['result'] = true;
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
		/*
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
		*/
	}
	function get_cabinet()
	{
		$data['cabinetList'] = $this->Product_model->getCabinet();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
		
		
		
	}
	
	function get_cabinet_product()
	{
		$cabinet = $this->input->post('cabinet');
		$data['product'] = $this->Product_model->getCabinetProduct($cabinet);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
		
	}
	
	
	
	
	function cabinet_send()
	{
	
	$i = 0;
		foreach($_POST as $row)
		{
			if($i==0)$datain['cabinet'] = $row	;
			else
			{
					$this->db->where('productID',$row);	
				$this->db->update('pos_product',$datain);
		
				
			} 
			$i++;
			
			
		}
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		
		
		
	}
	
	
	
	function cabinet_remove()
	{
		$productID = $this->input->post('productID');
		$cabinet = $this->input->post('cabinet');
		$this->db->where('cabinet',$cabinet);	
		$this->db->where('productID',$productID);	
		$this->db->update('pos_product',array('cabinet'=>''));
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		
		
	}
	
	
	
	
	
	function edit_send()
	{
		
		
		
		$result['result'] = false;
		$result['errMsg'] = '錯誤，請重新輸入';
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
			$purchaseDiscount = $this->input->post('purchaseDiscount');
			$nonJoinPurchaceDiscount = $this->input->post('nonJoinPurchaceDiscount');
			$suppliers = $this->input->post('suppliers');
			$language = $this->input->post('language');
			$openStatus = $this->input->post('openStatus');
			$publisher = $this->input->post('publisher');
			$nonBonus = $this->input->post('nonBonus');
			$comment = $this->input->post('comment');
            $patch = $this->input->post('patch');
              
            if($patch=='0')$patch = '';
         
			$phaBid = $this->input->post('phaBid');
			$concessionsNum = $this->input->post('concessionsNum');
			$distributeType = $this->input->post('distributeType');
			$cabinet = $this->input->post('cabinet');
			
			$case = $this->input->post('case');
			
			$concessions = $this->input->post('concessions');
			$cardSleeve = $this->input->post('cardSleeve');	
			$rule = $this->input->post('rule');	
			$wait = $this->input->post('wait');	
			$limitNum = $this->input->post('limitNum');		
			
			if($cardSleeve=='0')$cardSleeve ='';
			
				$new = explode('-',$productNum);
			if(isset($new[1])&&$new[1]!= '') $myproductNum = $new[1];
			else $myproductNum =$productNum; 
			$this->db->like('productNum',$myproductNum);
			$this->db->where('productID !=',$productID);
			
			$query = $this->db->get('pos_product');
			if($query->num_rows()>0)
			{
				$sql = "select SUBSTRING_INDEX(productNum, '-', -1) as productNum from pos_product where productNum like '%".substr($myproductNum,0,3)."%' order by productNum DESC ";
				
				
				
				$query = $this->db->query($sql);
				$ret = $query->row_array();
				
				$result['result'] = false;
				$result['errMsg'] = '編號重複,最大編號為:'.$ret['productNum'];
				
			}
			else
			{
	
			$bidExist = 1;
			$ret = $this->paser->post('https://www.phantasia.tw/bg/chk_bg',array('bid'=>$phaBid),true);
				if($ret['result']==false)
				{
					$bidExist = 0 ; 
			
					
				}
				else
				{ 
				$data = $this->paser->post('https://www.phantasia.tw/bg_controller/update_price',array('bid'=>$phaBid,'price'=>$price,'category'=>$category),false);	
					
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
					
					'patch'   =>$patch,
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
					'nonBonus'    => $nonBonus,
					'wait'        => $wait,
					'limitNum'        => $limitNum,
					'case'       =>$case,
					'cabinet'	 =>$cabinet
					);
					
			$this->db->where('productID',$productID);
			$this->db->update('pos_product',$datain);
			
			//concessions
			//clear order date
			
			$this->db->where('productID',$productID);
			$this->db->delete('pos_order_rule');
		
			
			
			
			$distributeList =explode(',',$distributeType);
			$concessionsList = explode('-',$concessions);
			$concessionsNumList = explode('-',$concessionsNum);
			$num = count($distributeList);
			for($i=0;$i<$num;$i++)
			{
				$discountList  = explode(',',$concessionsList[$i]);
				$numList  = explode(',',$concessionsNumList[$i]);
				
				$count = count($numList);
				for($j = 0 ; $j<$count;$j++)
				{
					if($numList[$j]!='')
					{
						
						
						$distributeList[$i].','.$numList[$j].','.$discountList[$j].'<br/>';
						$this->db->where('productID',$productID);
						$this->db->where('distributeType',$distributeList[$i]);
						$this->db->where('num',$numList[$j]);
						$query = $this->db->get('pos_order_rule');
						
						$datain = array('productID'=>$productID,'distributeType'=>$distributeList[$i],'num'=>$numList[$j],'discount'=>$discountList[$j]);
						if($query->num_rows()>0)
						{
							$this->db->where('productID',$productID);
							$this->db->where('distributeType',$distributeList[$i]);
							$this->db->where('num',$numList[$j]);	
							$this->db->update('pos_order_rule',array('discount'=>$discountList[$j]));
						
						}
						else $this->db->insert('pos_order_rule',$datain);
						
						
					}
				}
				$result['result'] = true;
			}
			
			//
			
			
			}
			$datain['productID'] = $productID;	 
			$this->Product_model->magicProductCheck($productID);
			$product= $this->Product_model->getProductStock(array('productID'=>$productID),$this->data['shopID']);
			$result['product'] = $product['product'][0];
			
		}
		
		echo json_encode($result);
		exit(1);	

	}
	function change_case()
	{
		$productID = $this->input->post('productID');
		$datain['case'] = $this->input->post('case');
		$this->db->where('productID',$productID);
		$this->db->update('pos_product',$datain);
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
	
    function product_update()
    {
        $handle = fopen($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/product2.csv','r');
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        
        while(!feof($handle))
		{
			$contents = fgets($handle,1000);
			
			$contents_array = explode(',',$contents);
            $productID = $contents_array[5];
           $datain = array(
                'ZHName'       =>$contents_array[0],
                'ENGName'      =>$contents_array[1],
                'language'     =>$contents_array[2],
                'type'         =>$contents_array[3],
                'suppliers'	   =>$contents_array[4],
      
                'openStatus'   =>$contents_array[6],
                'publisher'    =>$contents_array[7],
                'cabinet'      =>$contents_array[8],
                'hide'	       =>$contents_array[9],
                'productNum'   =>$contents_array[10]

           
           );
			if($datain['openStatus']==1)
            {
                $datain['timeStamp'] = '2017-09-07 14:55:00';
                
                
                
                
            }
            $this->db->where('productID',$productID);
            $this->db->update('pos_product',$datain);
            echo $productID.'<br/>';
		}
		fclose($handle);
		
        
        
    }
    
	function MTG_game_function()
	{
		//$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/wargame.csv','r');
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

		$this->db->like('ZHName','魔獸世界');
		$query = $this->db->get('pos_product');
		$data = $query->result_array();
		foreach($data as $row)
		{	
			
			$this->db->where('productID',$row['productID']);
			$this->db->update('pos_product',array(
			'nonBonus'=>1,
			'timeStamp'=> date("Y-m-d H:i:s")
			));
			
			
			
		
			echo $row['ZHName'].'<br/>';
		}
	
		
		
		
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

	function edit_productNum()
	{
		$productID = $this->input->post('productID');
		$productNum = $this->input->post('productNum');
		
		$new = strstr('-',$productNum);
		if($new!= '') $productNum = $new;
		$this->db->like('productNum',$productNum);
		$query = $this->db->get('pos_product');
		if($query->num_rows()>0)
		{
			$result['result'] = false;
			$result['errMsg'] = '編號重複';
		}
		else
		{
		
			$this->db->where('productID',$productID);
			$this->db->update('pos_product',array('productNum'=>$productNum,'timeStamp'=>date("Y-m-d H:i:s")));
			//$product= $this->Product_model->getProductStock(array('productID'=>$productID),$this->data['shopID']);
			//$result['product'] = $product['product'][0];
			$result['result'] = true;
		}
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
	function edit_prodct_category()
	{

		$productID = $this->input->post('productID');
		$category = $this->input->post('c');
		$this->db->where('productID',$productID);
		$this->db->update('pos_product',array('category'=>$category,'timeStamp'=>date("Y-m-d H:i:s")));
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
        if($this->data['level']==-1)
        {
            
           $s =  $this->Product_model-> getSupplierByID($this->data['shopID']);
            $data['suppliers'][]=$s;
        }
        else
        { 
            if($this->data['shopID']<1000)	$data['suppliers'] = $this->Product_model->getSuppliers();
            else $data['suppliers']  = array();
            $data['suppliers']=  array_merge(array(array('name'=>'全部','supplierID'=>0)),$data['suppliers']);
        }
        
        
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	function get_supplier_inf()
	{
		$this->load->model('Product_model');
		$supplierID = $this->input->post('supplierID');
		$data['suppliers'] = $this->Product_model->getSupplierInf($supplierID);
        
        $time = getdate();
        $data['budget'] = $this->Product_model->getPurchaseBudget(0,$time['year'],$time['mon']);
		
        
        
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
        if($minDiscount==0) $minDiscount =85;
        
	    $ZHName = $this->input->post('ZHName');
		$ENGName = $this->input->post('ENGName');
		$type = $this->input->post('productType');
         if($type!=1) $minDiscount =100;
		$category = $this->input->post('category');
		$productNum = $this->input->post('productNum');
		$buyDiscount = $this->input->post('buyDiscount');
		$buyPrice = $this->input->post('buyPrice');
		//$purchaseDiscount = $this->input->post('purchaseDiscount');
		
		$suppliers = $this->input->post('suppliers');
		$language = $this->input->post('language');
		$openStatus = $this->input->post('openStatus');
		$publisher = $this->input->post('publisher');
		$comment = $this->input->post('comment');
		
		if($barcode=='') $barcode = 9999999;
       
        
        
        
        
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
				
				'suppliers'   =>$suppliers,
				'language'    => $language,
				'openStatus'  => $openStatus,
				'publisher '  => $publisher,
				'comment'     => $comment, 
				'timeStamp'=>date("Y-m-d H:i:s")	
				);
				
				
		 $data['result'] = false;	
		if($this->Product_model->getProductStock(array('productNum' =>$productNum),$this->data['shopID'])==false&&($this->Product_model->getProductByBarcode($barcode)==false||$barcode='9999999')&&$this->data['level']>=50||$this->data['level']==-1){		

                    if(strpos($productNum,'MTG')!==false || strpos($productNum,'PTCG')!==false ) $datain['nonBonus'] = 1;
						$data['result'] = true;	
						$maxID = $this->Product_model->getMaxID();
						$datain['productID'] = $maxID+1;
            
                     if($this->data['level']==-1)
                    {
                        $datain['openStatus']= 0;
                        $datain['productNum'] = 'NTS'.$datain['productID'];
                    }

            
            
						$this->db->insert('pos_product',$datain); 
						$this->Product_model->updateNum($datain['productID'],0,$this->data['shopID'],$buyPrice);
						$data['productID'] = $datain['productID'];
						$this->Product_model->magicProductCheck($data['productID'] );
            
                 $this->db->where('mainID',$datain['productID']);
                 $this->db->where('type',1);
                 $this->db->delete('pos_grave');
             if($this->data['level']!=-1)
             {
            $content='<h1>本次後台新增商品：</h1>';
            $content .='<div>'.$datain['ZHName'].'('.$datain['ENGName'].')<br/>請協助補充商品資訊</div>';
			
			$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
			$mailToList = 'phantasia.ec@gmail.com';
			$time = getdate();
		
			$this->Mail_model->myEmail($mailToList,date("Y-m-d H:i:s").'新增商品'.$datain['ZHName'],$content,$headers);
             }
            
            

		}
		else $data['result'] = false;
		
       
		echo json_encode($data);
		exit(1);	
        
	}
	function edit_single_item_product()
	{
		$this->load->model('Product_model');
		$productID = $this->input->post('productID');
		$itemName = $this->input->post('itemName');
		$value = $this->input->post('value');
		$this->db->where('productID',$productID);
		$this->db->update('pos_product',array($itemName =>$value));
		if($itemName=='suppliers')	
		{
			$sup = $this->Product_model->getSupplierByID($value);
			$data['ret'] = $sup['name'];
		}
		else $data['ret'] = $value;
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
        
		
		
		
	}
	
	function test()
	{
		
       $r =  $this->Order_model->getOrderLackByProductID(8882194);
        print_r($r);
		$this->load->view('test')	;
	}
	function purchase_create()
	{
		
	
		$datain['supplierID'] = $this->input->post('suppliers');
		$datain['tax'] = $this->input->post('taxType');
		if($datain['tax']%2==0) $type = 'bTax';	
		else $type = 'aTax';
		
		$datain['accountTime'] = $this->input->post('accountTime');
		$datain['shareFee'] = $this->input->post('purchase_total_'.$type);
		$datain['shippingFee'] = $this->input->post('freight_'.$type);
		$datain['timeStamp'] = date("Y-m-d H:i:s");
		$datain['total'] = $this->input->post('orderTotal');
        $datain['type'] = $this->input->post('buyType');
		if( $this->input->post('comment')!=0 && $this->input->post('comment')!='' )$datain['comment'] = $this->input->post('comment');

		$datain['status']=3;

		$this->db->insert('pos_product_purchase_inf',$datain);
		$data['purchaseID'] = $this->db->insert_id();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
		
	}
	
	function purchase_inf_edit()
	{
		$this->load->model('Product_model');
	
		$datain['supplierID'] = $this->input->post('suppliers');
		$datain['tax'] = $this->input->post('taxType');
		$datain['accountTime'] = $this->input->post('accountTime');
        $datain['type'] = $this->input->post('buyType');
		if($datain['tax']%2==0) $type = 'bTax';	
		else $type = 'aTax';
		
		
		$datain['shareFee'] = $this->input->post('purchase_total_'.$type);
		$datain['shippingFee'] = $this->input->post('freight_'.$type);
        $purchaseID =  $this->input->post('purchaseID');
        $purchase = $this->Product_model->getPurchaseList(0,1,'purchase',$purchaseID);
		if($purchase['statusID']!=3)
        {
            
            	$datain['timeStamp'] = date("Y-m-d H:i:s");
        }
        
        
	
		$datain['total'] = $this->input->post('orderTotal');
		$purchaseID =  $this->input->post('purchaseID');
		$datain['comment'] = $this->input->post('comment');

		$datain['status']=3;

        //$this->db->where('status !=',3);
		$this->db->where('purchaseID',$purchaseID);
		$this->db->update('pos_product_purchase_inf',$datain);
		$data['purchaseID'] = $this->db->insert_id();
		
		$this->db->where('purchaseID',$purchaseID);
		$this->db->update('pos_product_purchase',array('accountTime'=>$datain['accountTime']));
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
		
	}
	function edit_purchase_accounting()
	{
		  $purchaseID =  $this->input->post('purchaseID');
		$datain['accountTime'] = $this->input->post('accountTime');
		$this->db->where('purchaseID',$purchaseID);
		$this->db->update('pos_product_purchase_inf',$datain);
		$this->db->where('purchaseID',$purchaseID);
		$this->db->update('pos_product_purchase',array('accountTime'=>$datain['accountTime']));
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
	}
	
	function change_purchase_price()
	{
		$rowID = $this->input->post('id')	;
		$productID = $this->input->post('productID');
		$purchasePrice = $this->input->post('purchasePrice');
		$num = $this->input->post('num');
		$purchase_preTime = $this->input->post('purchase_preTime');
	
        if($this->input->post('deleteToken'))
        {
            $this->db->where('rowID',$rowID);
              $this->db->delete('pos_product_purchase_order');
            
        }
        else
        {
            $this->db->where('rowID',$rowID);
            $this->db->update('pos_product_purchase_order',array('purchasePrice'=>$purchasePrice,'num'=>$num));
        }
		$getData = $this->paser->post($this->data['serverDomain'].'order/change_pretime',array('productID'=>$productID,'preTime'=>$purchase_preTime,'num'=>$num),true);

		$data['result']  =true;
		echo json_encode($data)	;
		exit(1);
	}
	
	
	function purchase_order_append()
	{
		$productID = $this->input->post('productID');
		$purchaseID = $this->input->post('purchaseID');
							$product_purchase = array(
							'productID'  => $productID ,
							'time'       => date("Y-m-d H:i:s"),
							'num'        =>  0,
							'shopID'     => $this->data['shopID'],
							'purchasePrice' =>0,
							'purchaseID' =>$purchaseID
							);			
					$this->db->insert('pos_product_purchase_order',$product_purchase); 

		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
	}
	
	
	function get_purchase_list()
	{
		$this->load->model('Order_model');
		$offset = $this->input->post('offect');
		if($this->input->post('offset')!=0)$offset = $this->input->post('offset');
		$num = $this->input->post('num');


		$data['purchaseList'] = $this->Product_model->getPurchaseList($offset,$num,$this->input->post('type'),0,$this->input->post('status'),$this->input->post('suppliers'));
		$i = 0;
		/*
		foreach($data['orderList'] as $row)
		{
			
			$data['orderList'][$i]['orderTime']	 = substr($row['orderTime'],0,16);
			$data['orderList'][$i]['shippingTime']	 = substr($row['shippingTime'],0,10);
			$data['orderList'][$i]['status'] = $this->Order_model->changeOrderStatus($row['status']);
			if($row['status']>=2) $data['orderList'][$i]['orderStatus']= '完成出貨';
			else   $data['orderList'][$i]['orderStatus']= '<span style="color:red">貨品等候中</span>';
			
			$i++;
			
		}
		*/
//		print_r($data);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
				
		
	}
	function print_out()
	{
		
		
		$this->data['display'] = "purchase_print";
		$this->data['js'] = $this->preload->getjs('pos_order');
		$this->data['js'] = $this->preload->getjs('pos_product_query');
		$this->data['js'] = $this->preload->getjs('jquery.tablesorter');
		$this->data['type'] = $this->uri->segment(3);
		$this->data['purchaseID'] = $this->uri->segment(4);
		$this->data['status'] = $this->uri->segment(5);
		
		$this->load->view('purchase_print',$this->data);
		
	}
	
	function purchase_order_dump_finish()
	{
		
		$purchaseID = $this->input->post('purchaseID');
		$datain['status'] = -1;
		$this->db->where('purchaseID',$purchaseID);
		$this->db->update('pos_product_purchase_order_inf',$datain);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	
	function purchance_change_inf()
	{
		$purchaseID = $this->input->post('purchaseID');
		$datain['preTime'] = $this->input->post('preTime');
		$datain['status'] = $this->input->post('status');
		
		$d =  $this->Product_model->getPurchaseDetailByID($purchaseID,'purchaseOrder');
		$total = 0 ; 
		$purchaseIn = '<h1>採購： 本次採購purchaseOrder'.$purchaseID.'</h1>';	
		foreach($d as $row)
		{
			$purchaseIn .='<div>'.$row['ZHName'].'('.$row['ENGName'].')['.$row['num'].']</div>';
	
			$this->Order_model->changePreTime($row['productID'],$datain['preTime'],$row['num']);	
			//$getData = $this->paser->post($this->data['serverDomain'].'order/change_pretime',array('productID'=>$row['productID'],'preTime'=>$datain['preTime'],'num'=>$row['num']),true);
			$total +=  $row['purchasePrice'] * $row['num'];
				
		}
        $time = getdate();
        
        $purchase = $this->Product_model->getPurchaseList(0,1,'purchaseOrder',$purchaseID);
        
        if($datain['status']==3)$datain['status']=2; //20200303 edit by taitin jump to finish
        
        $inLimit =   $this->Product_model->purchaseInLimit($purchase['supplierID'],$total,$time['year'],$time['mon']);
		if($inLimit  && $datain['status']==5) $datain['status']=3;
        
		
		
		$this->db->where('purchaseID',$purchaseID);
		$this->db->update('pos_product_purchase_order_inf',$datain);


			
			$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
			$mailToList = 'phantasia0000@gmail.com,product@phantasia.tw,phantasia.ac@gmail.com,phoenickimo@gmail.com';
			$time = getdate();
		
			
		 	$r = $this->Product_model->retStatus(array('status'=>$datain['status']),'');
			if($r['nextStatus']!='') $purchaseIn .= '<h1>下一階段：'.$r['nextStatus'].'</h1>';
			$this->Mail_model->myEmail($mailToList,'purchaseOrder'.$purchaseID.' 採購狀態改變為'.$r['status'].date("Y-m-d H:i:s"),$purchaseIn,$headers,0,99,1);






		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
	}
	function update_purchase_total()
	{
		$purchaseID = $this->input->post('purchaseID');
			$d =  $this->Product_model->getPurchaseDetailByID($purchaseID,'purchaseOrder');
			$total = 0 ;
			foreach($d as $c)
			{
				$total +=$c['num'] * $c['purchasePrice'];
				
			}
			$this->db->where('purchaseID',$purchaseID);
			$this->db->update('pos_product_purchase_order_inf',array('total'=>$total));
			$data['total'] = $total;
		
			$data['result'] = true;
		echo json_encode($data);
		exit(1);	
	}
	
	function get_purchase()
	{	
        $time = getdate();
		$year = $time['year'];
		$month = $time['mon'];
		$this->load->model('Product_model');
        $this->load->model('Order_model');
		$purchaseID = $this->input->post('purchaseID');
		$type = $this->input->post('type');

		$purchase = $this->Product_model->getPurchaseList(0,1,$type,$purchaseID);
		$data['purchase'] =$purchase;
		
		if(isset($purchase['total'] ))$data['total'] = $purchase['total'];
		if(isset($purchase['purchaseID'] ))$data['product'] = $this->Product_model->getPurchaseDetailByID($purchase['purchaseID'],$type);
		
        $productAll = array();
		if(isset($data['product']) && !empty($data['product']))
		foreach($data['product'] as $product)
		{
				
			if($product['rule']==1) $result['rule'][]=array('ZHName'=>$product['ZHName'],'ENGName'=>$product['ENGName'],'num'=>$product['num']);
            $comNum =  $this->PO_model->getProductNum(0,$product['productID'],$year.'-'.$month);
            $lack = $this->Order_model->getOrderLackByProductID($product['productID']);
            if(!isset($lack['buyNum']))$lack['buyNum'] = 0;
            $product['lackNum'] = $lack['buyNum']-$comNum;
            if(isset($lack['top']))$product['top100'] = $lack['top'];
            else $product['top100'] = '';
					$cabinet[$product['cabinet']][] = $product;
            $productAll[] = $product;
			
		}
        $data['product'] = $productAll;
		$content = '<div style="margin:0 auto; float:left">';
		
		if(!empty($cabinet))
		{
		$content.='<h1>商品上架指引</h1><div style=" text-align:left">';
		foreach($cabinet as $index=>$row)
		{
			foreach($row as $each)
			{
				$content.=$index.'=>'.$each['ZHName'].'('.$each['ENGName'].')<br/>';
			}
			
		}
		
			
		$content.='=======</div>';
		}
		if(isset($result['rule']))
		{
			$content.='<h1>本次需列印手冊的清單</h1>';
			foreach($result['rule'] as $row)
			{
				$content .='<div>'.$row['ZHName'].'('.$row['ENGName'].')['.$row['num'].']</div>';
			}
			$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
			$mailToList = 'phantasia0000@gmail.com,product@phantasia.tw';
			$time = getdate();
		
			$this->Mail_model->myEmail($mailToList,date("Y-m-d H:i:s").'本次需列印的手冊清單',$content,$headers);
		}		
		$content .='</div><div style=" clear:both"></div>';
		$data['content'] = $content;
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
	}
	function purchase_order_create()
	{
	
       
		$datain['supplierID'] = $this->input->post('suppliers');
		$datain['total'] = $this->input->post('orderTotal');
		$datain['tax'] = $this->input->post('taxType');
        $datain['type'] = $this->input->post('buyType');
		if( $this->input->post('comment')!=0 && $this->input->post('comment')!='' );	$datain['comment'] =  $this->input->post('comment');
		
		
		if($datain['tax']%2==0) $type = 'bTax';	
		else $type = 'aTax';
		
		
		$datain['shareFee'] = $this->input->post('purchase_total_'.$type);
		$datain['shippingFee'] = $this->input->post('freight_'.$type);
		$datain['preTime'] = $this->input->post('purchase_preTime');
		
		$datain['timeStamp'] = date("Y-m-d H:i:s");
		
		
		$this->db->insert('pos_product_purchase_order_inf',$datain);
		$data['purchaseID'] = $this->db->insert_id();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
		
	}
	
	
	function trans()
	{
		$row['productID'] = 8881844;
		$this->Product_model->sameProductTrans($row['productID']);
		
	}
	
	function auto_allocate()
	{
		
		$productID = $this->input->post('productID');
		$nowNum = $this->input->post('nowNum');
		$data['allocate']  =  $this->Order_model->allocateOrder($productID,$nowNum);	
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	
	function purchase_edit_send()
	{
	
		$purchaseID = $this->uri->segment(3);
		$NotAllocate = $this->uri->segment(4);
		$this->load->model('Order_model');
		$i = 0;$j = 0 ;
		   //$this->db->insert('pos_test',array('content'=>json_encode($_POST)));
		foreach($_POST as $row)
		{
			switch ($i%5)
			{
                case 0:
					$datain[$j]['rowID'] = $row;
				break;    
                    
                    
				case 1:
					$datain[$j]['productID'] = $row;
				break;
				
				case 2:
					$datain[$j]['purchasePrice'] = $row;
				break;
				case 3;
					if($row<0&&$this->data['level']<50)$datain[$j]['num'] = 0;
					else $datain[$j]['num'] = $row;
				break;
				case 4:
					$datain[$j++]['purchaseTotal'] = $row;	
				
				break;
					
			}	
			$i++;
			
		}
		

		$this->load->model('Product_model');
	
		$k = 0 ;
		$total = 0 ;
      //  $this->db->insert('pos_test',array('content'=>json_encode($datain)));
		if($i>0&&isset($datain))
		{
			$purchase = $this->Product_model->getPurchaseList(0,1,'purchase',$purchaseID);
			$purchaseIn = '';	
			foreach($datain as $row)
			{
				if(isset($row['num']) && $row['num']!=0)	
				{
					$row['purchasePrice'] = $row['purchaseTotal'] / $row['num'];
				
					/*
					$this->Product_model->refreshProductNum($row['productID'] , $row['num'],$this->data['shopID'],$row['purchasePrice'],$purchaseID,$row['purchaseTotal'],$purchase['tax']);
					$this->Product_model->sameProductTrans($row['productID']);
					if($NotAllocate==1) $num = $row['num'];
					else $num = $this->Order_model->reAllocateOrder($row['productID']);
					*/
					
					
					$result['product'][$k]['productID'] = $row['productID'] ;
					$result['product'][$k++]['num'] =  $row['num'];
				
					$product= $this->Product_model->getPurchaseDetailByID($purchaseID,'purchase',$row['productID'],$row['rowID']);
				
					$r = $this->Product_model->changePurchasePrice($row['productID'],$purchaseID,$product['purchaseTotal'], $row['purchaseTotal'] ,$row['num'],$row['rowID']);
					if($r)$purchaseIn .='<div>'.$product['ZHName'].'('.$product['ENGName'].')['.$row['num'].']修改價錢從 $'.$product['purchasePrice'].'改為 $'.$row['purchasePrice'].']</div>';
				
				
			
				}
			}
			if($purchaseIn!='')
			{
			
				$purchaseIn = '<h1>入庫金額變動'.$purchase['supplier'].' purchase'.$purchaseID.'</h1>'.$purchaseIn;	
			
			$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
			$mailToList = 'phantasia0000@gmail.com,product@phantasia.tw,phantasia.ac@gmail.com,phoenickimo@gmail.com';
			$time = getdate();
		
			$this->Mail_model->myEmail($mailToList,date("Y-m-d H:i:s").'purchase'.$purchaseID.' '.$purchase['supplier'].' 入庫金額變動',$purchaseIn,$headers);

			}
		}
		
		if($purchaseIn!='' && isset($content))	$result['content'] = $purchaseIn;
		else $result['content'] ='';
		$result['result'] = true;

		echo json_encode($result);
		exit(1);
	
	}

	
	
	
	function purchase_send()
	{
		$purchaseID = $this->uri->segment(3);
		$NotAllocate = $this->uri->segment(4);
		$this->load->model('Order_model');
		$i = 0;$j = 0 ;
		
		foreach($_POST as $row)
		{
			switch ($i%4)
			{
				case 0:
					$datain[$j]['productID'] = $row;
				break;
				
				case 1:
					$datain[$j]['purchasePrice'] = $row;
				break;
				case 2;
					if($row<0&&$this->data['level']<50)$datain[$j]['num'] = 0;
					else $datain[$j]['num'] = $row;
				break;
				case 3:
					$datain[$j++]['purchaseTotal'] = $row;	
				
				break;
					
			}	
			$i++;
			
		}
		

		$this->load->model('Product_model');
        $this->load->model('Cs_order_model');
	
		$k = 0 ;
		$total = 0 ; 
		if($i>0&&isset($datain))
		{
			$purchase = $this->Product_model->getPurchaseList(0,1,'purchase',$purchaseID);
			$purchaseIn = '<h1>到貨：'.$purchase['supplier'].' 本次入庫purchase'.$purchaseID.'</h1>';	
			foreach($datain as $row)
			{
				if(isset($row['num']) && $row['num']!=0)	
				{
					$row['purchasePrice'] = $row['purchaseTotal'] / $row['num'];
				
					$this->Product_model->refreshProductNum($row['productID'] , $row['num'],$this->data['shopID'],$row['purchasePrice'],$purchaseID,$row['purchaseTotal'],$purchase['tax']);
					$this->Product_model->sameProductTrans($row['productID']);
					if($NotAllocate==1) $num = $row['num'];
					else $num = $this->Order_model->reAllocateOrder($row['productID']);
                    
                    
                    $this->Cs_order_model->examRemainNum(array($row['productID']));
                    
                    
					$result['product'][$k]['productID'] = $row['productID'] ;
					$result['product'][$k++]['num'] = $num;
					$product= $this->Product_model->chkProductByProductID($row['productID']);
					if($product['rule']==1) $result['rule'][]=array('ZHName'=>$product['ZHName'],'ENGName'=>$product['ENGName'],'num'=>$row['num']);
					$cabinet[$product['cabinet']][] = $product;
					$purchaseIn .='<div>'.$product['ZHName'].'('.$product['ENGName'].')['.$row['num'].']</div>';
					$this->db->where('productID',$row['productID']);
					$this->db->update('pos_product',array('recent'=>date("Y-m-d"),'buyPrice'=>$row['purchasePrice'],'buyDiscount'=>round($row['purchasePrice']*100/$product['price']),'hide'=>0));
					$this->db->where('productID',$row['productID']);
					$this->db->delete('pos_product_preTime');
					$total +=$row['num'] * $row['purchasePrice'];
				
                    //寄賣品
			         if($purchase['type']==1)
                     {
                         
                        $this->Product_model-> updateProductConsignmentNum($row['productID'],$row['purchasePrice'],$purchase['supplierID']);
                     }
                    
				}
			}
			
			if($purchase['accountTime']=='0000-00-00')
				$purchase['accountTime'] = date("Y-m-d");
			
			
			$this->db->where('purchaseID',$purchaseID);
			$this->db->update('pos_product_purchase',array('accountTime'=>$purchase['accountTime']));
			
			$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
			$mailToList = 'phantasia0000@gmail.com,product@phantasia.tw,phantasia.ac@gmail.com,phoenickimo@gmail.com';
			$time = getdate();
			$this->Mail_model->myEmail($mailToList,date("Y-m-d H:i:s").'purchase'.$purchaseID.' 到貨：'.$purchase['supplier'].' 本次入庫清單',$purchaseIn,$headers,0,99,1);

			
		}
		
			
		$content='<h1>商品上架指引</h1>';
		
		foreach($cabinet as $index=>$row)
		{
			foreach($row as $each)
			{
				$content.=$index.'=>'.$each['ZHName'].'('.$each['ENGName'].')<br/>';
			}
			
		}
		
			
		$content.='=======<br/>';
		if(isset($result['rule']))
		{
			$content.='<h1>本次需列印手冊的清單</h1>';
			foreach($result['rule'] as $row)
			{
				$content .='<div>'.$row['ZHName'].'('.$row['ENGName'].')['.$row['num'].']</div>';
			}
			$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
			$mailToList = 'phantasia0000@gmail.com,product@phantasia.tw';
			$time = getdate();
		
			$this->Mail_model->myEmail($mailToList,date("Y-m-d H:i:s").'本次需列印的手冊清單',$content,$headers,0,99,1);
		}
		
		
		
		
		
		
		if(isset($content))	$result['content'] = $content;
		$result['result'] = true;

		echo json_encode($result);
		exit(1);
	
	}
	function delete_purchase_order()
	{
	
		$purchaseID = $this->input->post('purchaseID');
		$this->db->where('purchaseID',$purchaseID);
		$this->db->delete('pos_product_purchase_inf');
		$this->db->where('purchaseID',$purchaseID);
		$this->db->delete('pos_product_purchase_order_inf');
		$this->db->where('purchaseID',$purchaseID);
		$this->db->delete('pos_product_purchase_order');
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);			
		
		
	}
	
	function get_budget_by_time()
    {
        
          $timeStr = $this->input->post('dateString');
         $ta = explode('-',$timeStr);
        $year = $ta[0];
        $month = $ta[1];
        
        
         $data['budgetList'] = $this->Product_model->getAllPurchaseBudget($year,$month);
        $data['result'] = true;
		echo json_encode($data);
		exit(1);			
		
        
        
    }
	function budget_update()
    {
  
    
        
        $timeStr = $this->input->post('budgetTime');
       
        $ta = explode('-',$timeStr);
        $year = $ta[0];
        $month = $ta[1];
        
        
        $supplierIDList = $this->input->post('supplierID');
        $budgetLimit = $this->input->post('budgetLimit');
        foreach($supplierIDList as $key=>$row)
        {
           
            
            $r = $this->Product_model->getPurchaseBudget($row,$year,$month);
            if(isset($r['insert']) && $r['insert']==1 )
            {
                $datain = array(
                    'supplierID' =>$row,
                    'limitAmount' =>$budgetLimit[$key],
                    'year' =>$year,
                    'month'  =>$month
                
                
                );
                $this->db->insert('pos_purchase_budget',$datain);
                
                
                
            }
            else
            {
                
                 $datain = array(
                        'limitAmount' =>$budgetLimit[$key]
                                
                );
                $this->db->where('year',$year) ;
                $this->db->where('month',$month) ;
                $this->db->where('supplierID',$row) ;
                $this->db->update('pos_purchase_budget',$datain);
                
            
            
            
            }
                
            
        
            
            
            
            
        }
        
        $result['result'] = true;

		echo json_encode($result);
		exit(1);
        
        
    }
	
	
	
	function purchase_order_send()
	{
		
     

		$purchaseID = $this->uri->segment(3);
		$this->load->model('Order_model');
		$i = 0;$j = 0 ;
		
		foreach($_POST as $row)
		{
			switch ($i%5)
			{
				case 0:
					$datain[$j]['productID'] = $row;
				break;
				case 1:
					$datain[$j]['purchasePrice'] = $row;
				break;
				case 2;
					if($row<0&&$this->data['level']<50)$datain[$j]['num'] = 0;
					else $datain[$j]['num'] = $row;
				break;
				case 3:
					$datain[$j]['purchaseTotal'] = $row;
				break;
                case 4:
					$datain[$j++]['comment'] = $row;
				break;
                    
				
					
			}	
			$i++;
			
		}
		

		$this->load->model('Product_model');
	
		$k = 0 ;$total = 0;
		if($i>0&&isset($datain))
		{
			$purchase = $this->Product_model->getPurchaseList(0,1,'purchaseOrder',$purchaseID);
			
			$purchaseIn = '<h1>採購：'.$purchase['supplier'].' 本次採購purchaseOrder'.$purchaseID.'</h1>';	
			$result['preTime'] = $purchase['preTime'];
			$result['phantri'] =array();
			foreach($datain as $row)
			{
				if(isset($row['num']) && $row['num']!=0)	
				{
					//$this->Product_model->refreshProductNum($row['productID'] , $row['num'],$this->data['shopID'],$row['purchasePrice'],$purchaseID);
					//$this->Product_model->sameProductTrans($row['productID']);
					//$num = $this->Order_model->reAllocateOrder($row['productID']);
					$product_purchase = array(
							'productID'  => $row['productID'],
							'time'       => date("Y-m-d H:i:s"),
							'num'        =>  $row['num'],
							'shopID'     => $this->data['shopID'],
							'purchasePrice' => $row['purchasePrice'],
							'purchaseTotal' => $row['purchaseTotal'],
							'purchaseID' =>$purchaseID,
                            'orderComment' => $row['comment']
							);			
					$this->db->insert('pos_product_purchase_order',$product_purchase); 

					
					
					
					$result['product'][$k]['productID'] = $row['productID'] ;
					$product= $this->Product_model->chkProductByProductID($row['productID']);
					if($product['rule']==1) $result['rule'][]=array('ZHName'=>$product['ZHName'],'ENGName'=>$product['ENGName'],'num'=>$row['num']);
										$purchaseIn .='<div>'.$product['ZHName'].'('.$product['ENGName'].')['.$row['num'].']</div>';
					$total +=$row['num'] * $row['purchasePrice'];
					
					if($purchase['preTime']!='0000-00-00 00:00:00')
					{
						$this->load->model('PR_track_model');
						$this->Order_model->changePreTime($row['productID'],$purchase['preTime'],$row['num']);	
						$getData['phantri'] = $this->PR_track_model->loadByProductID($row['productID']);
						
						//$getData = $this->paser->post($this->data['serverDomain'].'order/change_pretime',array('productID'=>$row['productID'],'preTime'=>$purchase['preTime'],'num'=>$row['num']),true);
	
						
						
						if(!empty($getData['phantri']))	$result['phantri'][] = $getData['phantri'] ;
					}
					
					//preTime
					/*
					$this->db->where('productID',$row['productID']);
					$this->db->delete('pos_product_preTime');
					*/
				}
			}
            
            
             $time = getdate();
        
        
        $inLimit =   $this->Product_model->purchaseInLimit($purchase['supplierID'],$total,$time['year'],$time['mon']);
            
 
				if($inLimit)
				{
					 $status=3 ; 
					 $purchaseIn.="<h1>待會計審核通過</h1>";
				}
				else 
				{
					$status = 5 ;
					 $purchaseIn.="<h1>已達預算上限，待主管審核通過</h1>";
						
					
				}
            if($status==3)
            {
                
                $status = 2; //自動通過 2020 0316 資by taitin;
                
            }
			$this->db->where('purchaseID',$purchaseID);
			$this->db->update('pos_product_purchase_order_inf',array('total'=>$total,'status'=>$status));
		
		
		
			$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
			$mailToList = 'phantasia.pa@gmail.com,phantasia.pm@gmail.com,phantasia.ac@gmail.com,phoenickimo@gmail.com';
			$time = getdate();
		
			$this->Mail_model->myEmail($mailToList,'purchaseOrder'.$purchaseID.' 採購：'.$purchase['supplier'].date("Y-m-d H:i:s"),$purchaseIn,$headers,0,99,1);

			
		}
		if(isset($result['rule']))
		{
			$content='<h1>本次採購單預計需列印手冊的清單</h1>';
			foreach($result['rule'] as $row)
			{
				$content .='<div>'.$row['ZHName'].'('.$row['ENGName'].')['.$row['num'].']</div>';
			}
			$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
			$mailToList = 'phantasia0000@gmail.com,product@phantasia.tw';
			$time = getdate();
		
			$this->Mail_model->myEmail($mailToList,date("Y-m-d H:i:s").'本次採購單預計需列印手冊的清單',$content,$headers,0,99,1);
		}
		if(isset($content))	$result['content'] = $content;
		$result['result'] = true;

		echo json_encode($result);
		exit(1);
	
	}
	
	function get_purchase_today()
	{
		$this->load->model('Order_model');
		$result['product']  = $this->Product_model->getPurchaseToday();	
		
		
	}
	
	
	
	function purchase_msg()
	{
		$this->load->model('Order_model');
		$result['product']  = $this->Product_model->getPurchaseToday();	
		
	  $content = '<h1>今日商品到貨通知</h1>';
	   $title='商品到貨通知';
	   $token = false;
        $tokena = false;$tokenb = false;
	   $i=0;
        $a = '';
        $b = '';
      $err = '<h1>入庫檢核表</h1>';
        
		  foreach($result['product'] as $row)
		  {
              
             
              
                $product= $this->Product_model->chkProductByProductID($row['productID']);
		
              if($row['stockNum']!=$row['checkNum']) $color ='style="color:red"';
              else $color = "";
          
                   $err.=$product['ZHName'].'('.$product['ENGName'].')['.$product['language'].'] <span '.$color.'>[電腦]'.$row['stockNum'].'[實際]'.$row['checkNum'].'</span><br/>';
                
            
            
              
            $num = $this->Order_model->getAvailableNum($row['productID']);
			if($num>0)
			{
				$token = true;
			
			
                $t ='';
			  if(!empty($product))
			  {
			 	
                    
                    $t.=$product['ZHName'].'('.$product['ENGName'].')['.$product['language'].'] '.$num;
				if($product['phaBid']!=0) $t.='<a target="_blank" href="https://www.phantasia.tw/bg/home/'.$product['phaBid'].'">網址</a>';
				$t.='<br/>';
			 	if($i++%3==2)$t.='<br/>';
                  if($product['suppliers']==18   ||$product['suppliers']==2||$product['suppliers']==94||$product['suppliers']==87||$product['productID']==8883633)
                  {
                      $b .= $t;
                       $tokenb = true; 
                  }
                  else 
                  {
                    $tokena = true; 
                      $a.=$t;
                  }
             
			  }
			}
		  }	
	
		$content.='　請上訂購商品選購喔！';
		$content .='<div>幻遊天下　產品部</div>';
		$content .='<div>(此信件由系統發送)</div>';
		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
		$mailToListA =array( 'lintaitin@gmail.com','phoenickimo@hotmail.com','phantasia0000@gmail.com');
        
        $mailToListB[] = 'lintaitin@gmail.com';
		$time = getdate();
		$shopList = $this->System_model->getShop(false);
		foreach($shopList as $row)
		{
			
            
           if($row['email']!='')
           {
               if($row['distributeType']==1||$row['distributeType']==15 ||$row['distributeType']==20||$row['shopID']<=1000)
            $mailToListA[]=$row['email']; 
            else $mailToListB[]=$row['email']; 
               
               
               
           } 
		}
        if($tokena||$tokenb)
        {
              $this->Mail_model->groupEmail($mailToListA,$time['year'].'/'.$time['mon'].'/'.$time['mday'].'商品到貨通知',$content.$a.$b,$headers);
            
        }
		if($tokena)
        { 
           /*
            echo 'A:'.$mailToListA.'<br/>';
             echo 'A:'.$content.$a.$b.'<br/>';
             echo 'B:'.$mailToListB.'<br/>';
             echo 'B:'.$content.$a.'<br/>';
             */   
            
          
             $this->Mail_model->groupEmail($mailToListB,$time['year'].'/'.$time['mon'].'/'.$time['mday'].'商品到貨通知',$content.$a,$headers);
             
        }
		
        
        if($err!='<h1>入庫檢核表</h1>')
        {
		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
		$mailToListErr =array( 'lintaitin@gmail.com','phantasia.stock@gmail.com');
         $this->Mail_model->groupEmail($mailToListErr,$time['year'].'/'.$time['mon'].'/'.$time['mday'].'入庫檢核通知',$err,$headers);
        }
		echo 'done';
		
	}
	
	function  save_product()
	{
		$_POST['shopID'] = 0;
		$_POST['licenceCode'] = '150e4e2633d2d5aa712b6a41fcd6ba01';
		
		echo $this->paser->post($this->data['posDomain'].'product/save_product', $_POST,false);
				/*
		
		$checkID = $this->input->post('checkID');
		$productID = $this->input->post('productID');
		$nowNum = $this->input->post('nowNum');
		$realNum = $this->input->post('realNum');
		
		$this->load->model('Product_model');
		$this->Product_model->saveChkProduct($checkID,$productID,$nowNum,$realNum);
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		*/
	}
	

	function get_damage()
	{
		$this->load->model('Product_model');
		
		$checkID = $this->input->post('checkID');
	
		$this->load->model('Product_model');
		$ret = $this->paser->post($this->data['posDomain'].'product/get_chk_product',array('checkID'=>$checkID,'order'=>1,'shopID'=>0,'licenceCode'=>'150e4e2633d2d5aa712b6a41fcd6ba01'),true);
		$countStock = $ret['product'];
	
		$countSize = count($countStock);
		$ret = $this->Product_model->getProductStock(array('purchased'=>1,'order1'=>'productID','sequence1'=>'ASC'),0);
		$recordStock = $ret['product'];
		$recordSize = count($recordStock);
	
		$flag = true;
		$i = 0 ; $j = 0 ;
		if($i>=$countSize||$j>=$recordSize)$flag = false;
		while($flag)
		{
			
			
			if($countStock[$i]['productID']>$recordStock[$j]['productID'])	
			{
				
				if($recordStock[$j]['nowNum']>0 &&isset($countStock[$i-1])&& $recordStock[$j]['productID']!=$countStock[$i-1]['productID'])
				{
					$r = $recordStock[$j];
					$r['realNum'] = 0;
					$r['status'] = 0;
					$data['product'][] = $r;
				
				
				}
				$j++;
			}
			else 
			{
				//count productID較小  
					if($countStock[$i]['status']==1 )
					{
						if($countStock[$i]['nowNum']>$countStock[$i]['realNum'])$data['product'][] = $countStock[$i];
			
						$i++;
						if($i>=$countSize||$j>=$recordSize)$flag = false;
						continue;
					}
					
			
			
					
					
						if($countStock[$i]['nowNum']>$countStock[$i]['realNum'])
						{
							$r = $countStock[$i];
							$r['realNum'] = $countStock[$i]['realNum'];
							$r['status'] = 0;
							$data['product'][] = $r;
						
						
						}
					if ($countStock[$i]['productID'] == $recordStock[$j]['productID'])
					{
					
							$j++;
							$i++;						
					}
					else $i++;
			}
			if($i>=$countSize||$j>=$recordSize)$flag = false;
			
		}
		//清空所有J
		for($j;$j<$recordSize;$j++)
		{
			
			if($recordStock[$j]['nowNum']>0 &&  ($j>0&&$recordStock[$j]['productID']!=$recordStock[$j-1]['productID']))
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
		$_POST['shopID'] = 0;
		$_POST['licenceCode'] = '150e4e2633d2d5aa712b6a41fcd6ba01';
		echo	$this->paser->post($this->data['posDomain'].'product/get_overage',$_POST,false);
		
		
	}
	
	
	function stock_recover()
	{
		$checkID = $this->input->post('checkID');
		$productID = $this->input->post('productID');
		$nowNum = $this->input->post('nowNum');
		$realNum = $this->input->post('realNum');
		$status = $this->input->post('status');
		$data =  $this->paser->post($this->data['posDomain'].'product/stock_recover',$_POST,true);
	
		
		if($data['product']['status']==0)
		{
			
			
			$this->Product_model->refreshProductNum($productID , ($realNum-$nowNum)*$status,0,0,0);
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
			$this->paser->post($this->data['posDomain'].'product/save_chk_product',array('checkID'=>$checkID,'datainStr' => json_encode($datain),'shopID'=>0,'licenceCode'=>'150e4e2633d2d5aa712b6a41fcd6ba01'),false);
			
			//foreach($datain as $row)$this->Product_model->saveChkProduct($checkID,$row['productID'],$row['nowNum'],$row['realNum']);
			
			
		}
		
		$result['result'] = true;

		echo json_encode($result);
		exit(1);
	
		
	
	
	
	}
	function chk_product_delete()
	{

		$_POST['shopID'] = 0;
		$_POST['licenceCode'] = '150e4e2633d2d5aa712b6a41fcd6ba01';
		echo	$this->paser->post($this->data['posDomain'].'product/chk_product_delete',$_POST,false);
		
	}
	
	function new_chk_product()
	{
		$_POST['shopID'] = 0;
		$_POST['licenceCode'] = '150e4e2633d2d5aa712b6a41fcd6ba01';
		echo $this->paser->post($this->data['posDomain'].'product/new_chk_product',array(),false);

	}
	function get_chk_product_list()
	{
	
		$_POST['shopID'] = 0;
		$_POST['licenceCode'] = '150e4e2633d2d5aa712b6a41fcd6ba01';
		echo $this->paser->post($this->data['posDomain'].'product/get_chk_product_list',array(),false);
		
	}
	 
	
	
	function get_chk_product()
	{	
		$_POST['shopID'] = 0;
		$_POST['licenceCode'] = '150e4e2633d2d5aa712b6a41fcd6ba01';
		echo $this->paser->post($this->data['posDomain'].'product/get_chk_product',$_POST,false);
		
		
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
		$query = $this->db->get('pos_product'); 
		$data = $query->result_array();
		foreach($data as $row)
		{
			$this->System_model->grave(1,$row['productID'],$row);	
			
			
		}
		
		
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
	function supplier_delete()
	{
		$supplierID = $this->input->post('id');
		$this->db->where('supplierID',$supplierID);
		
		$this->db->delete('pos_suppliers');
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	
	 
	function supplier_edit()
	{
		$supplierID = $this->input->post('id');
		$datain['name']  = $this->input->post('name');
		$datain['order']  = $this->input->post('order');
		$datain['day']  = $this->input->post('day');
		$datain['IDNumber']  = $this->input->post('IDNumber');
		$datain['phone']  = $this->input->post('phone');
		$datain['address']  = $this->input->post('address');
		$datain['email']  = $this->input->post('email');
		$datain['invoice']  = $this->input->post('invoice');
        $datain['invoiceWay']  = $this->input->post('invoiceWay');
    
		if($datain['order']=='') $datain['order']  = 99;
		$datain['timeStamp'] = date("Y-m-d H:i:s");
		if($this->Product_model->getSupplierByID($supplierID)==false)
		{
			$this->db->insert('pos_suppliers',$datain);	
			
		}
		else
		{
			$this->db->where('supplierID',$supplierID);
			$this->db->update('pos_suppliers',$datain);
		}
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
		
		
	}
	
    function ug_upload()
    {
      $handle = fopen($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/ug.csv','r');
        $i = 0;
        while(!feof($handle))
		{
            $contents = fgets($handle,1000);
            
      $contents_array = explode(',',$contents);
            print_r($contents_array);
	  $productNum = $contents_array[0];
      $data = $this->Product_model->getProductByProductNum($productNum);
			if(isset($contents_array[5]))
			{
			$purchasePrice = $contents_array[5];
            }
                if($purchasePrice/$contents_array[4]>0.65)
                {
                   $discount = round($purchasePrice*100/$contents_array[4]+5);
                    
                    $datain = array('productID'=>$data['productID'],'distributeType'=>0,'num'=>0,'discount'=>$discount);
				    echo $productNum.'-'.$discount.'<br/>';
                    $this->db->insert('pos_order_rule',$datain);
                    
                    
                    
                }
            
            
           $url ="http://www.kktcg.com/index.php?route=product/search&search=".$productNum  ;
              $result =  $this->paser->post($url,array(''),false);
		  
        $a = $this->cut('<div class="image">','</div>',$result);
    
        $link = $this->cut('<img src="','" alt',$a);
        echo '<img src="'.$link.'">';
            $productID = $data['productID'];
            
            
            $result =  $this->paser->post($link,array(''),false);
            $b=$_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/product/img/'.$productID.'.jpg';
            $newfile = $b;
            $write = @fopen($newfile,"w");
            fwrite($write,$result);
                 fclose($write);  
                /*
            	$filename=$link;
		print_r($data);
		$img_info = getimagesize($filename);
		$width    = $img_info['0'];
		$height   = $img_info['1'];
		$img_type = $img_info['2'];
              	$newim = imagecreatetruecolor($width, $height);
			imagecopy($newim, $im, 0,0,0,0, $width, $height);
			imagejpeg($newim, $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/product/img/temp/'.$productID.'.jpg', 75);
			
			$filename =$_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/product/img/temp/'.$productID.'.jpg';
			;
			ImageResize($filename, $b,600, 1000, 100); 
            */
           
        }
    }
    
     function cut($begin,$end,$str){
        $b = mb_strlen($begin);
         $str = strstr($str,$begin);
        
    $e = mb_strpos($str,$end) - $b ;

    return trim(mb_substr($str, $b ,$e));
} 
    
    
	function stock()
	{
		for($i=1;$i<=2;$i++)
		{
		$handle = fopen($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/stockdump'.$i.'.csv','r');
		$this->load->model('Product_model');
		while(!feof($handle))
		{
			$contents = fgets($handle,100);
			
			$contents_array = explode(',',$contents);
						$productNum = $contents_array[0];
			$data = $this->Product_model->getProductByProductNum($productNum);
			if(isset($contents_array[1]))
			{
			$purchasePrice = $contents_array[1];
			$num = $contents_array[2];
			$datain =array(
				'shopID'=> 5,
				'productID'=>$data ['productID'],
				'month'=>4,
				'year'=>2012,
				'num' =>$num,
				'totalCost'=>$purchasePrice*$num
			);
			
	
			$this->db->insert('pos_product_amount',$datain);
			}
		}
		fclose($handle);
		}
		$result['result'] = true;
	
		echo json_encode($result);
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
		
			$url = 'https://www.phantasia.tw/bg_controller/pos_bg_create';
   
			$result = $this->paser->post($url,$_POST,false);
   
			echo $result;	
		
		
	}
	function pha_search()
	{
		$url = 'https://www.phantasia.tw/bg_controller/search';
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
				$data = $this->paser->post('https://www.phantasia.tw/bg/chk_bg',array('bid'=>$row['phaBid']),true);
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
					$data = $this->paser->post('https://www.phantasia.tw/bg_controller/update_price',array('bid'=>$row['phaBid'],'price'=>$row['price'],'category'=>$row['category']),true);
					if($data['result']==false) echo 'wrong';
				}
				
		
		}		
		echo 'finish';
	}
	
	
	

	function announce()
	{
		$this->load->model('Msg_model');
	   $result = '<h1>新增商品通告</h1>';
	   $title='新增商品通告';
	   $result .='<div>新增：</div>';
	$content = '';
		  foreach($_POST as $row)
		  {
			
			  $product= $this->Product_model->chkProductByProductID($row);
			  if(!empty($product))
			  {
			  $result.=$product['ZHName'].'('.$product['ENGName'].')['.$product['language'].']<br/>';
			  $result.=$_POST['url_'.$product['productID']].'<br/>';
			  $result.=$_POST['comment_'.$product['productID']].'<br/>';
			  $result.='=====================<br/>';
			  	$content.=" ".$product['ZHName'].'('.$product['ENGName'].')['.$product['language'].']<br/>';
			  
			  }
		  }	
		
		$result .='<div>請上訂購商品選購喔！</div>';
		
		$content.='　請上訂購商品選購喔！';
		$this->Msg_model->insert($title,$content,1);
		$result .='<div>幻遊天下　產品部</div>';
		$result .='<div>(此信件由系統發送)</div>';
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
			$this->Mail_model->myEmail($row,$time['year'].'/'.$time['mon'].'/'.$time['mday'].'新增商品通告',$result,$headers);
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
    function get_month_purchase()
    {
        $this->load->model('Product_model');
		$supplierID = $this->input->post('supplierID');
		$from = $this->input->post('from');
		$to = $this->input->post('to');


		$data['purchaseList'] = $this->Product_model->getPurchaseListByInvoice($supplierID ,$from,$to );
		
        foreach($data['purchaseList'] as $row)
        {
            $r['inf'] = $row;
            $r['detail'] = $this->Product_model->getPurchaseDetailByID($row['purchaseID'],'purchase');
            $data['purchase'][]=$r;
            
            
        }
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
				
        
        
        
        
        
    }
    
    
    
    function get_all_consignment_report()
    {
        
        $t = getdate();
        $year = $t['year'];
        $month = $t['mon'];
     
            
            $m = $month-1;
            $y = $year;
            if($m==0)
            {
                $m =12;
                $y=$year-1;
                
            }
          
            
            
        //$this->db->where('year',$y);
        $this->db->where('end',0);
        
        $this->db->join('pos_product','pos_product_consignment.productID = pos_product.productID','left');
        
        $this->db->group_by('pos_product_consignment.supplierID');
        $q = $this->db->get('pos_product_consignment');
        $data = $q->result_array();
        
        
        foreach($data as $row)
        {
            $supplierID = $row['supplierID'];
             $pw = $this->Product_model->md5pw($supplierID.$year.$month);
            $data['url']= 'product/get_consignment?year='.$year.'&month='.$month.'&supplierID='.$supplierID.'&pw='.$pw;
           
            //echo $data['url'];
            $r = $this->get_consignment($year,$month,$supplierID,1);
            $title = $r['product'][0]['inf']['supplierName'].$r['from'].'-'.$r['to'].'寄售狀況';
            $content ='<h3>親愛的協力廠商您好：</h3>';
		$content .='<h3>本月份('.$r['from'].'-'.$r['to'].')銷售紀錄：</h3>';
		$content .='<h3>明細請點此網址：<a href="'.$this->data['serverDomain'].$data['url'].'">'.$this->data['serverDomain'].$data['url'].'</h3>';
            if($r['cTotal']==0)
            {
                		$content .='<h3>本期未售出喔</h3>';
		                    
                
            }
                
            else
            {
                $content .='<h3>本月金額總計：'.$r['cTotal'].'</h3>';
                $content .='<h3>請協助開立發票</h3>';
                $content .='<h3>統一編號：53180059 抬頭：幻遊天下股份有限公司</h3>';
                	$content .='<h3>寄送地址：新北市板橋區南雅南路二段11-26號1F</h3>';
		            $content .='<h3>收件人：幻遊天下會計部</h3>';
                    $content .='<h3>聯絡電話：02-86719616</h3>';
                
                
            }

		  $content .='<h3></h3>';
		$content .='<h3>謝謝(此為系統自動發出)</h3>' ; 
            
            $content .='Product Department<br/>';
 $content .='+886 2 86719616<br/>';
 $content .='幻遊天下股份有限公司<br/>';
 $content .='Phantasia Co., Ltd.<br/>';
 $content .='22060 新北市板橋區南雅南路二段11-26號<br/>';
 $content .='Address: No. 11-26, Sec. 2, Nanya S. Rd., Banqiao Dist., New Taipei City 220, Taiwan (R.O.C.)<br/>';
 $content .='Website: http://www.phantasia.tw/';
            
            $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: product@phantasia.tw\nReply-To:product@phantasia.tw\n";
		mb_internal_encoding('UTF-8');
            
             $s =  $this->Product_model->getSupplierInf($supplierID);
            $r['inf']['supplierName'] = $s['name'];
            
		//if($pushEmail !='')$pushEmail  = ','.$pushEmail ;
        $this->Mail_model->myEmail($s['email'].',phantasia.pm@gmail.com,phantasia.ac@gmail.com,lintaitin@gmail.com' ,$title,$content,$headers,0,100);
                
		
           
        }
        
        
    }
    
    
    
    
    
    function get_consignment_link()
    {
        
         $year  = $this->input->post('year');
         $month = $this->input->post('month');
         $supplierID = $this->input->post('supplierID');
        if(empty($supplierID))
        {
            
            $supplierID = $this->session->userdata('csupplierID');
            
        }
         $pw = $this->Product_model->md5pw($supplierID.$year.$month);
        $data['url'] = '/product/get_consignment?year='.$year.'&month='.$month.'&supplierID='.$supplierID.'&pw='.$pw;
          if(empty($supplierID))$data['result'] = false;
        else $data['result'] = true;
        echo json_encode($data);
		exit(1);
        
    }
    function get_consignment($year=0,$month=0,$supplierID=0,$databack=0)
    {
        $this->load->model('Product_flow_model'); 
       $this->load->model('Product_model'); 
        $this->load->model('Order_model'); 
        
        if($databack==0)
        {
       $supplierID = $this->input->get('supplierID');
        
         $year  = $this->input->get('year');
         $month = $this->input->get('month');
        }
     
        if($databack==0)
        {
            
            $pw = $this->input->get('pw');
            if($pw!=$this->Product_model->md5pw($supplierID.$year.$month)) redirect('/welcome/login');
            
           
            
        }
        $this->session->set_userdata('csupplierID',  $supplierID);
        if($year!=0) 
        {
            $to = $year.'-'.$month.'-25';
            
            $m = $month-1;
            $y = $year;
            if($m==0)
            {
                $m =12;
                $y=$year-1;
                
            }
            $from = $y.'-'.$m.'-26';
            
            
        }
        else
        {
            
            
         $from = $this->input->get('from');
         $to = $this->input->get('to');
        }
    
            $today =date('Y-m-d');
             if(strtotime($today) >  strtotime($to))  $fileto = $today;
                else $fileto = $to; 
                 
                 
        	$file = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/consignment/cs_report_'.$supplierID.'_'.$fileto.'.txt';
            if(file_exists($file) && strtotime($to)<strtotime(date('Y-m-d')))
				{
						$handle = fopen($file,'r');
						$contents = '';
					while(!feof($handle))
					{$contents .= fgets($handle);}
					fclose($handle);	
	//		
                    $data = json_decode($contents,true);
					
					$creareFile = false;
					
				}
				else $creareFile = true;
        
        if($creareFile)
        {
        $df = explode("-",$from);
        $dt = explode("-",$to);
        $dirShop = $this->System_model->getDirectShop();
       $product = $this->Product_model->getAllSConsignment($supplierID);
        
        $data = array();
        $data['cTotal'] = 0;
        $data['from'] = $from;
        $data['to'] = $to;
         foreach($product as $row)
        {
             
             if(strtotime($row['startConsignment'])>strtotime($data['from'])) $from=$row['startConsignment'];
             else $from = $data['from'];
            $pr = $this->Order_model->getProductShipment($row['productID'],$from,$to,0);
       
             $sellNum = 0;
             foreach($pr as $each)
             {
                 
                 if($each['dirShop']=='')   
                 {
             
                    
                     if(strpos($each['comment'],'展示品') ===false)
                     {
                       
                         $sellNum += $each['sellNum'];
                     }
                 }
            
             }
   
             $r['sellNum'] = $sellNum;
   
        // 直營店賣出
             
            $dirSell = 0;
             foreach($dirShop as $each)
             {
                  $ret =  $this->Product_flow_model->getSell($from,$to,$each['shopID'],$row['productID']);  
               if(!empty($ret))
               {
                    $num = 0 ;
                    foreach($ret as $sell)
                    {
                            $dirSell+=$sell['sellNum'];

                    }
                   
                   
               }
               
                 
             }
         
             $r['dirSell'] = $dirSell;
             
             
             
          //  $r = $this->Product_flow_model->productIOCollect($from,$to,0,$row['productID']);
             
            $consignmentProduct =  $this->Order_model->getProductShipment($row['productID'],'2018-05-01',$to,1);
             $consignmentShop = array();
             $consignmentSellNum = 0;
          
             foreach($consignmentProduct as $each)
             {
                 
                 if(!isset($consignmentShop['s_'.$each['shopID']]))
                 {
                     $y = $df[0];
                     $m = $df[1];
                     $m = $m-1;//算上個月的
                     if($m==0)
                     {
                         $m=12;
                             $y--;
                     }
                     $consignmentShop['s_'.$each['shopID']] = 1;
                    
                    $ret =  $this->Order_model->getConsignmentMonthCheck($each['shopID'],$y,$m,0,$row['productID']);//寄賣賣出
                    if(!empty($ret))
                        foreach($ret as $eachConsignment)
                        {
                             $consignmentSellNum += $eachConsignment['consignmentNum'] - $eachConsignment['remainNum'] ;
                            
                        }
                    
                     
                     
                 }
                 
                 
             }
             
            $r['consignmentSellNum'] = $consignmentSellNum;
             
             
             
             
            $r['inf'] = $this->Product_model->getProductInfByProductID($row['productID']);
             $s =  $this->Product_model->getSupplierInf($row['suppliers']);
            $r['inf']['supplierName'] = $s['name'];
            
            $p = $this->Product_model->getPurchaseByProductID($row['productID'],$from,$to,1,$supplierID);
     
             $purchaseNum = 0;
             $purchasePrice = 0;
            foreach($p as $row)
            {
                
                $purchaseNum +=$row['num'];
                $purchasePrice += $row['num']*$row['purchasePrice'];
            }
             
               $r['purchase']['num'] = $purchaseNum;
             if($purchaseNum==0) $r['purchase']['purchasePrice'] = 0 ;
              else $r['purchase']['purchasePrice'] = round($purchasePrice/$purchaseNum);
             
             
             
             $o = $this->Product_model->getConsignmentAmount($df[0],$df[1],$row['productID']);
             if(empty($o))
             {
                 $r['open']['num'] = 0;
                 $r['open']['purchasePrice'] = 0;
             }
             else 
             {
               
                 $r['open']['num'] = $o['num'];;
                 $r['open']['purchasePrice'] = $o['purchasePrice'];;
             }
           
             
             
             
              

            $sell = $r['sellNum']+$r['consignmentSellNum']+$r['dirSell'];
                $r['sell'] = $sell;
                $finalNum = $r['open']['num']-$sell+$r['purchase']['num'];
                $r['finalNum']     = $finalNum;
             
           
                if($r['open']['num']+$r['purchase']['num'] >0)
                $purchasePrice =  ($r['open']['purchasePrice'] * $r['open']['num'] +$r['purchase']['purchasePrice'] *$r['purchase']['num'])/($r['open']['num']+$r['purchase']['num']);
                 else $purchasePrice = 0;
                $r['purchasePrice'] = $purchasePrice;
            
             $data['cTotal'] += $r['purchasePrice'] * $r['sell'];
                $data['product'][]=$r;
             
        }
     
       // print_r($data['product']);

            $output = json_encode($data);
					$f = fopen($file,'w');
				fprintf($f,"%s",$output);
						fclose($f);		
        }
       
         if(strtotime($today) >  strtotime($to))
            { 
            foreach($data['product'] as $row)
            {



                    if(isset($year) && isset($month) && $year !=0 && $month!=0)    
                    $this->Product_model->setConsignment($row['inf']['productID'],$year,$month,$row['finalNum'],$row['purchasePrice']);

                            //echo $year.'-'.$month.'-'.$finalNum.','.$row['productID'].'<br/>';    


            }
        
        }
        
        
        
       $data['year'] = $year;
        $data['month'] = $month;
        
        if($databack) return $data;
        $this->load->view('consignment_list',$data);
        
        
        
        
    }
    function get_product_inf()
    {
        $productID = $this->input->post('productID');
        $this->load->model('Product_model');
        $data['inf'] = $this->Product_model->getProductInfByProductID($productID);
         $data['result'] = true;
           
        echo json_encode($data);
		exit(1);
    }
    function get_product_num()
    {
        
        $q = $this->input->post('q');
        $this->db->like('productNum',$q);
        $this->db->order_by('productNum','DESC');
        $query =$this->db->get('pos_product');
        $data= $query->row_array();
        $data['result'] = true;
           
        echo json_encode($data);
		exit(1);
        
    }
    
	function consignment_product_send()
	{
        
		
		$i = 0 ; $j = 0;
        
		foreach($_POST as $key=>$row)
		{
            if($key=='orderSupplier')
            {
                $supplierID =$row;
                continue;
            }
            
			if($i%2==0)$data[$j]['productID'] = $row;
			else $data[$j++]['purchasePrice'] = $row;
			
			
			$i++;
		}
		
		$this->load->model('Product_model');
		foreach($data as $row)
		{
			$this->Product_model->updateProductConsignmentNum($row['productID'],$row['purchasePrice'],$supplierID);
		}
		$data['result']  = true;
		echo json_encode($data);
		exit(1);
		
		
	}

	
	
	
    
	function product_num_update()
	{
		$this->load->model('Product_model');
		$offset = 0;
		$num = 10000;
		$shopID = 0;
		$query = '';
		$from = '2012-06-01';
		$to = '2012-06-30'.' 23:59:59';
		$data['product'] = $this->Product_model->getProductIO($shopID,$offset,$num,$query,$from,$to);
	
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		echo '<table>';
		foreach($data['product'] as $row)
		{
			if($row['inNum']<0)
			{
				echo	'<tr>'.
						'<td style=" text-align:right">'.$row['ZHName'].'</td>'.
						'<td style=" text-align:right">'.$row['ENGName'].'</td>'.
						'<td style=" text-align:right">'.$row['productID'].'</td>'.
						'<td style=" text-align:right">'.$row['inNum'].'</td>'.
						'<td style=" text-align:right">'.$row['backNum'].'</td>'.
						'<td style=" text-align:right">'.$row['outNum'].'</td>'.
						'</tr>'	;
			
			}
			else  
			{
				$num= $row['inNum'] +	$row['backNum'] -$row['outNum'];
				$this->Product_model->updateNum($row['productID'],$num,0,$row['buyPrice']);
			}
			
		}
		echo '</table>';
		
	}
	
	
	function get_product_IO()
	{
		$this->load->model('Product_model');
		$offset = $this->input->post('offset');
		$num = $this->input->post('num');
		$shopID = $this->input->post('shopID');
		$query = $this->input->post('query');
		$shopQuery = $this->input->post('shopQuery');
		$shopGroup = $this->input->post('shopGroup');

		$from = $this->input->post('from');
		$to = $this->input->post('to').' 23:59:59';
		$data['product'] = $this->Product_model->getProductIO($shopID,$offset,$num,$query,$from,$to,$shopQuery,$shopGroup);
	
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
	}	
	
	function sanguosha_check_id()
	{
		$userID = $this->input->post('userID');
		$sanguoshaID = $this->input->post('sanguoshaID');
		$shopID = $this->input->post('shopID');
		$data = $this->sanguosha_check($userID,$sanguoshaID,$shopID);
		echo json_encode($data);
		exit(1);
	}
	
	function sanguosha_check($userID,$sanguoshaID,$shopID)
	{
		if( $this->Product_model->checkNick($userID)==false)
		{
				$data['result'] = false;	
				$data['errCode'] =4;
			
		}
		else
		{
			$sanguosha = $this->Product_model->getSanguosha($userID,$sanguoshaID);
			$data['result'] =true;	
			if($sanguosha==false) $data['result'] =true;
			else 
			{
				foreach($sanguosha as $row)
				{
					if($row['sanguoshaID']==$sanguoshaID && $row['delete']!=1)
					{
						$data['result'] =false;	
						$data['errCode'] =1;	
						
					}
					else if($row['userID']==$userID && $row['delete']!=1)
					{
						$data['result'] =false;	
						if($row['shopID']==0)
						{
							$data['errCode'] =2;	
						}
						else $data['errCode'] =3;	
					}
					
				}
			}
		}
		return  $data;	
	}
	
	function sanguosha_send()
	{
		$userID = $this->input->post('userID');
		$sanguoshaID = $this->input->post('sanguoshaID');
		$shopID = $this->input->post('shopID');
		
		$data = $this->sanguosha_check($userID,$sanguoshaID,$shopID);
		if($data['result']==true ||$data['errCode']==2)
		{
			$datain = array(
				'userID'=> $userID,
				'sanguoshaID' => $sanguoshaID,
				'time' =>date("Y-m-d H:i:s"),
				'shopID' => $shopID
			)	;
			$data['result']=true ;
			$this->db->insert('pos_sanguosha',$datain);
		}
		else if($data['errCode']==1)$data['errMsg'] = '保證卡號碼重複';
		else if($data['errCode']==3)$data['errMsg'] = '身分證字號重複';
	
		
		
		
		echo json_encode($data);
		exit(1);		
	}
	
	
	function caulate_cost()
	{
		//$find['order1'] = 'nowNum';
		//$find['sequence1'] = 'DESC';
		//$result = $this->Product_model->getProductStock($find,0);
		$this->db->where('shopID',0);
		$query = $this->db->get('pos_product_amount');
		$data = $query->result_array();
		foreach($data as $row)
		{
			 $product = $this->Product_model->getProductByProductID($row['productID']);	
			$this->db->where('id',$row['id']);
			 $this->db->update('pos_product_amount',array('avgCost'=>$product['buyPrice']));
		}
		
		
	}
	function caulate_adjust_cost()
	{
		$this->db->select('pos_order_adjust.fromShopID,pos_order_adjust_detail.*');
		$this->db->join('pos_order_adjust','pos_order_adjust.id=pos_order_adjust_detail.adjustID','left');
		$query = $this->db->get('pos_order_adjust_detail');
		$data = $query->result_array();

		
		foreach($data as $row)
		{
			
			 $product = $this->Product_model->getProductByProductID($row['productID'],$row['fromShopID']);	
			$this->db->where('id',$row['id']);
			 $this->db->update('pos_order_adjust_detail',array('purchasePrice'=>round($product['purchaseCount']*$product['price']/100,0)));
		}
		
		
		
		
		
	}
	
	
	function caulate_consignment_cost()
	{
		//$find['order1'] = 'nowNum';
		//$find['sequence1'] = 'DESC';
		//$result = $this->Product_model->getProductStock($find,0);
	//	$this->db->where('shopID',0);
		$query = $this->db->get('pos_consignment_amount');
		$data = $query->result_array();
		foreach($data as $row)
		{
			 $product = $this->Product_model->getProductByProductID($row['productID']);	
			$this->db->where('id',$row['id']);
			 $this->db->update('pos_consignment_amount',array('avgCost'=>$product['buyPrice']));
		}
		
		
	}
	
	function sanguosha_delete()
	{
		
		$sanguoshaID = $this->input->post('sanguoshaID');
		$shopID = $this->input->post('shopID');
		$data['result'] = false;
		if($shopID==$this->data['shopID']||$this->data['shopID']==0)
		{
			
			$this->db->where('sanguoshaID',$sanguoshaID);
			$this->db->where('shopID',$shopID);
			$this->db->update('pos_sanguosha',array('delete'=>1));
			$data['result'] = true;
			
		}
		else $data['errMsg'] = '權限不足';

		echo json_encode($data);
		exit(1);			
	}
	function get_product_amount()
	{
        $page = $this->input->post('page');
        $num = $this->input->post('num');
        
        $offset = $page*$num;
		$result['product'] = $this->Product_model->getCurrentAllProductStock(0,$offset,$num);
        
        
        
        
        
		echo json_encode($result);
		exit(1);
		
	}
	
	function get_package()
	{
		$data['package'] = $this->Product_model->getPackage();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	
	function delete_package()
	{	
		$boxProductID = $this->input->post('boxProductID');
		$this->db->where('boxProductID',$boxProductID);
		$this->db->delete('pos_package');
		$data['result'] = true;
		echo json_encode($data);
		exit(1);			

		
		
		
	}

	function sell_ogether()
	{
			
			echo 'ss';
		$productID = $this->uri->segment(3);
		$shopID = $this->uri->segment(4);
		
		
		$checkIDList = $this->Product_model->getSellTogetherCheckID($productID,$shopID);
		
		$data['productList'] = array();
		foreach($checkIDList as $row)
		{
			
			$data['productList'][] = $this->Product_model->getProductIDBycheckID($row['checkID'],$row['shopID']);
			
			
		}
		$this->load->view('sell_together',$data);
	}

    function new_pokemon_send()
    {
        
        $i = 0; $a = '';
		foreach($_POST as $row)
		{
		
                if($a=='')$a = $row.',0,0';
                else $a = $a.'-'. $row.',0,0';
                    
           
			$i++;
			
			
		}
        $this->db->insert('pos_pokemon_sell',array('name'=>'未設定名稱','productStr'=>$a));
        
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
			
        
    }
    function delete_pokemon()
    {
        
        $id = $this->input->post('id');
            $this->db->where('id',$id);
        $this->db->delete('pos_pokemon_sell');
          
		$result['result'] = true;
     
		echo json_encode($result);
       
		exit(1);
    }
    function pokemon_set()
    {
       $id = $this->input->post('id');
       $datain['name'] = $this->input->post('name');
       $datain['sellPrice'] = $this->input->post('sellPrice');
       $a = '';
 
        if(isset($_POST['productID']))
        foreach($_POST['productID'] as $key=>$row)
        {
            
                if($a=='')$a = $row.','.$_POST['num'][$key].','.$_POST['eachSellPrice'][$key];
                else $a = $a.'-'.$row.','.$_POST['num'][$key].','.$_POST['eachSellPrice'][$key];
            
            
        }
        $datain['productStr'] = $a;
  
        $this->db->where('id',$id);
        $this->db->update('pos_pokemon_sell',$datain);
          
		$result['result'] = true;
     
		echo json_encode($result);
       
		exit(1);
        
    }

	
	
	function new_package_send()
	{
		$i = 0;
		foreach($_POST as $row)
		{
			
			if($i%2==0) $boxProductID = $row;
			else
			{
				if(!$this->Product_model->chkPackage($boxProductID))
				{
				
				 $unitProductID = $row;
				$this->db->insert('pos_package',array('boxProductID'=>$boxProductID,'unitProductID'=>$unitProductID));
		
				}
			} 
			$i++;
			
			
		}
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
			
		
	}
		
	function get_consume()
	{
		$data['consume'] = $this->Product_model->getConsume();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	function new_consume()
	{
		$productID = $this->input->post('productID')	;
		if($this->Product_model->chkConsume($productID)) $result['result'] = false;
		else
		{
				$this->db->insert('pos_consumables',array('productID'=>$productID,'timeStamp'=>date("Y-m-d H:i:s")));
				$result['result'] = true;
		}
			
		
		echo json_encode($result);
		exit(1);
			
		
	}
	
	function delete_consume()
	{	
		$productID = $this->input->post('productID');
		$this->db->where('productID',$productID);
		$this->db->delete('pos_consumables');
		$data['result'] = true;
		echo json_encode($data);
		exit(1);			

		
		
		
	}
	
	
	function package_edit()
	{
		$num = $this->input->post('num');
		$boxProductID = $this->input->post('boxProductID');
		$this->db->where('boxProductID',$boxProductID);
		$this->db->update('pos_package',array('unitToBox'=>$num,'timeStamp'=>date("Y-m-d H:i:s")));
		$data['result'] = true;
		echo json_encode($data);
		exit(1);			
	}
		
    function auto_shut_down()
	{
		$this->load->model('PO_model');
		$shift = time() - (6 * 30 * 24 * 60 * 60); //9個月前
		$sql="SELECT * FROM (`pos_product`) LEFT JOIN (SELECT sproductID as productID,max(pos_order_shipment.shippingTime)as near from pos_order_shipment_detail left join pos_order_shipment on pos_order_shipment.id = pos_order_shipment_detail.shipmentID GROUP BY `pos_order_shipment_detail`.`sproductID` ) as `pos_order_shipment_detail` ON `pos_order_shipment_detail`.`productID`=`pos_product`.`productID` WHERE `pos_product`.`openStatus` = 1 AND near <=  '".date('Y-m-d', $shift)."'";
		
        echo $sql;
		$query = $this->db->query($sql);	
		$content =  '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$data = $query->result_array();
		//print_r($data);
		$i =  0;
		foreach($data as $row)
		{
			
			$r = $this->PO_model->getProductNum(0,$row['productID'],date('Y-m-d'));	
			if($r<=0)
			{
				$content.= $row['ZHName'].'('.$row['ENGName'].')'.$row['language'].'-lastOut:'.$row['near'].'<br/>';
				$this->db->where('productID',$row['productID']);
		 		$this->db->update('pos_product',array('openStatus'=>0,'timeStamp'=>date("Y-m-d H:i:s")));
				$i++;
				
			}
		}
		
			$mailToList = 'lintaitin@gmail.com,phantasia.pa@gmail.com,phantasia.pm@gmail.com';
			$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
			$time = getdate();
		echo $content;
		if($i>0)$this->Mail_model->myEmail($mailToList,date("Y-m-d H:i:s").'本次系統自動關閉品項',$content,$headers);
	
		
		
	}		
	function print_cabinet()
    {
        
        
        $data['cabinet'] = $this->uri->segment(3);
		$data['product'] = $this->Product_model->getCabinetProduct($data['cabinet'],true);
        
        $i = -1; $keyword  = 'z';
        foreach($data['product'] as $row)
        {
           
            $all = substr(strstr($row['cabinet'], '['),1);
    
             $body =  stristr($all, ']',true);
             if($keyword!=$body) 
             {
                 $keyword = $body;
                 $i++;
                $data['line'][$i]['num'] = 1;    
                $data['line'][$i]['name'] = $body; 

             }
            else $data['line'][$i]['num']++;   
         
            
        }
		$data['result'] = true;
    
		$this->load->view('cabinet_print',$data);
        
        
        
            
            
    }
	
    function barcode_generate()
    {
        
               $this->load->model('Product_model');
        $productID = $this->input->post('productID');
    
         $inf = $this->Product_model->getProductInfByProductID($productID);
     
         $b1 = '8888' ; //瘋桌遊內碼一開頭
         $b2 = '00';  //00薄套 10 厚套 20有色厚套 30卡盒 40卡冊 41卡冊內頁 42卡夾
        
         $b3 = '065090'; //各品類編碼 共6碼
         //以上共12碼
        if(strpos ($inf['ZHName'],'卡套')!==false) $b2= '00';
        if(strpos ($inf['ZHName'],'厚')!==false)   $b2= '10';
        if(strpos ($inf['ZHName'],'磨砂')!==false) $b2= '20';
        if(strpos ($inf['ZHName'],'卡盒')!==false) $b2= '30';
        if(strpos ($inf['ZHName'],'卡夾')!==false) $b2= '40';
        if(strpos ($inf['ZHName'],'卡頁')!==false) $b2= '41';
        if(strpos ($inf['ZHName'],'卡夾')!==false) $b2= '42';
        $t = explode('_',$inf['productNum']);
        $b3 = $t[1];
         
         $code = $b1.$b2.$b3;
         
         $m = ($code[1]+$code[3]+$code[5]+$code[7]+$code[9]+$code[11])*3;
         /*   1)	將偶位數值相加乘3 。	7+0+2+1+5+6=21 , 21*3=63*/
         
                 
             
        /*   (2)	將奇位數值相加。4+1+0+1+0+2=8*/
         $n = ($code[0]+$code[2]+$code[4]+$code[6]+$code[8]+$code[10])*3;
         
        /*(3)	將步驟1.2中所求得的值相加，取其個位數之值。63+8=71*/
         $o = ($m+$n)%10;
 
        /*(4)	以10減去步驟3中所求得的值，即為該EAN條碼之檢查碼。*/
             
        $chkNum = 10-$o;
         $code = $code.$chkNum; 
        $data['code'] = $code;
        $data['result'] = true;
    	echo json_encode($data);
		exit(1);			
	
        
    }
    
    
	function auto_set_fin()
	{
					
		$productID = $this->input->post('productID');
		$phaBid = $this->input->post('phaBid');			
		
		
		
		$data['product']= $this->Product_model->makeBidLink($phaBid,$productID);
	
		$data['result'] = true;
		echo json_encode($data);
		exit(1);			
	
		
		
	}
	function get_lc()
	{
		$data['year'] = $this->uri->segment(3);
		$data['month'] =  $this->uri->segment(4);
		
		//瘋桌遊末端銷售
			
		$data['output']['sellView'] = $this->Product_model->getPhaLCSell($data['year'],$data['month']);
		$data['output']['sellView']['title'] = '瘋桌遊末端銷售';	
		
		//各家出貨狀況
		$data['output']['shipView'] = $this->Product_model->getLCShip($data['year'],$data['month']);
		$data['output']['shipView']['title'] = '各家出貨狀況';
		
		$this->load->view('lc_sell_view',$data);	
		
        
		
	}
	
	
	
	
	function fax()
	{
		
		$filename=$_FILES['file_name']['tmp_name'];
		$allname = $_FILES['file_name']['name'];
		$nameArray = explode('.',$allname);
		$name = $nameArray[0];
		
		//if(empty($filename))redirect('/err/message/1');
		$img_info = getimagesize($filename);
		$width    = $img_info['0'];
		$height   = $img_info['1'];
		$img_type = $img_info['2'];
		if  (filesize($filename)>4000000) echo '圖檔太大，請壓縮後再上傳';
		else
		{
		
			switch ($img_type)
			{
				case 1: 
					$im = imagecreatefromgif($filename);
					break;
				case 2: 
					$im = imagecreatefromjpeg($filename);  
					break;
				case 3: 
					$im = imagecreatefrompng($filename); 
					break;
				default: 
					return 'Image Type Error!';  
					break;
			}
			/* 先建立一個 新的空白圖檔 */
			
			$filename =$_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/fax/'.$name.'.jpg';

			$newim = imagecreatetruecolor($width, $height);
			imagecopy($newim, $im, 0,0,0,0, $width, $height);
			imagejpeg($newim, $filename , 100);
		//	$b=$_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/product/fax/'.$name.'.jpg';
	//		ImageResize($filename, $b,600, 1000, 100);
			//unlink($filename);
			
		}
		
	
		
		$content = $width.','.$height.'<img src="http://shipment.phantasia.com.tw/pos_server/upload/fax/'.$name.'.jpg" >';
		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phantasia0000@gmail.com\nReply-To:phant@phantasia.tw\n";
		$mailToList = 'lintaitin@gmail.com';
		
		$this->Mail_model->myEmail($mailToList, date("Y-m-d H:i:s").'有傳真',$content,$headers);
		echo 'ok';
		
		
	}
	
	function magic_allocate()
	{
		
		$orderID = $this->input->post('orderID');
		$sql = 'update pos_order_detail set sellNum = buyNum where orderID = '.$orderID;
		$this->db->query($sql);
		
		
		
		$this->db->where('orderID',$orderID);
		$this->db->update('pos_magic_order',array('status'=>1));
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);		
		
		
	}
	function magic_purchase_order()
	{
		$orderID = $this->input->post('orderID');
		
		$this->load->model('Order_model')	;
		$order = $this->Order_model->getOrderInf($orderID);
		$datain['shopID'] = 0;
		$datain['licence'] = '150e4e2633d2d5aa712b6a41fcd6ba01';
		$datain['suppliers']  = 6; //孩之寶
		$datain['comment']  = $order['name'].'魔風直送訂單'; 
		$datain['taxType'] = 'bTax';
		$shift = time() + (3 * 24 * 60 * 60); //9個月前
		$datain['purchase_preTime'] =date("Y-m-d",$shift);
		$url = 'http://shipment.phantasia.com.tw/product/purchase_order_create';
		$result = $this->paser->post($url,$datain,true);
	
		if($result['result']==false)
		{
				
			$data['result'] = false;
			echo json_encode($data);
			exit(1);
				
		}
		$purchaseID = $result['purchaseID'];
		
		
		$url = 'http://shipment.phantasia.com.tw/product/purchase_order_send';
		$url .= '/'.$purchaseID;

		$product = $this->Order_model->getOrderDetailByID($orderID);
		
		foreach($product as $row)
		{
			$productData[]=  $row['productID'];
			$productData[] = $row['buyPrice'];
			$productData[]= $row['buyNum'];
			$productData[] = $row['buyNum'] * $row['buyPrice'];
            $productData[] = '';
			
		}
		$result = $this->paser->post($url,$productData,true);

		if($result['result']==false)
		{
			
				
			$data['result'] = false;
			echo json_encode($data);
			exit(1);
				
		}
		
	
		$purchaseInf['purchaseID'] = $purchaseID;
		$purchaseInf['preTime'] = date("Y-m-d",$shift);
		$purchaseInf['status'] =  2 ;//採購單成立
		$url = 'http://shipment.phantasia.com.tw/product/purchance_change_inf';

		$result = $this->paser->post($url,$purchaseInf,true);
       
		if($result['result']==false)
		{
				
			$data['result'] = false;
			echo json_encode($data);
			exit(1);
				
		}
		else
		{
			$this->db->where('orderID',$orderID);
			$this->db->update('pos_magic_order',array('status'=>2,'purchaseID'=>$purchaseID));
			$data['result'] = true;
			$data['purchaseID'] = $purchaseID;
			echo json_encode($data);
			exit(1);
			
		}
		
		
	}
	
	
	
	function magic_purchase_in()
	{
		$orderID = $this->input->post('orderID');
		$this->load->model('Order_model')	;
		$order = $this->Order_model->getOrderInf($orderID);
		$purchaseOrderID = $this->input->post('purchaseID');
		$this->load->model('Product_model')	;
		$purchase = $this->Product_model->getPurchaseList(0,1,'purchaseOrder',$purchaseOrderID);
		
		$datain['shopID'] = 0;
		$datain['licence'] = '150e4e2633d2d5aa712b6a41fcd6ba01';
		$datain['orderTotal'] = $purchase['total'];
		$datain['comment']  = $order['name'].'魔風直送訂單'; 
		$datain['suppliers']  = 6; //孩之寶
		$datain['taxType'] = 'bTax';
		$shift = time() + (3 * 24 * 60 * 60); //
		$datain['purchase_preTime'] =date("Y-m-d",$shift);
		$url = 'http://shipment.phantasia.com.tw/product/purchase_create';
		$result = $this->paser->post($url,$datain,true);
	
		if($result['result']==false)
		{
				
			$data['result'] = false;
			echo json_encode($data);
			exit(1);
				
		}
		$purchaseID = $result['purchaseID'];
		
		
		$url = 'http://shipment.phantasia.com.tw/product/purchase_send';
		$url .= '/'.$purchaseID.'/1';
		$product = $this->Product_model->getPurchaseDetailByID($purchaseOrderID,'purchaseOrder');

		foreach($product as $row)
		{
			$productData[]=  $row['productID'];
			$productData[] = $row['purchasePrice'];
			$productData[]= $row['num'];
			$productData[] = $row['purchaseTotal'] ;
						
			
		}
		$result = $this->paser->post($url,$productData,true);
			
		if($result['result']==false)
		{
				
			$data['result'] = false;
			echo json_encode($data);
			exit(1);
				
		}
		$url = 'http://shipment.phantasia.com.tw/product/purchase_order_dump_finish';
		$result = $this->paser->post($url,array('purchaseID'=>$purchaseOrderID),true);
		
		
		
		$this->db->where('orderID',$orderID);
		$this->db->update('pos_magic_order',array('status'=>3));
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	
	
	function magic_order_to_shipment()
	{
		
		
		$this->load->model('Product_model');
		$this->load->model('Order_model');
		$orderID = $this->input->post('orderID');
		$shipmentID =$this->Order_model->orderToShipment($orderID,true,true);//轉為出貨單
		$data['shipmentID'] = $shipmentID;
		$data['result'] = true;
		$this->db->where('orderID',$orderID);
		$this->db->update('pos_magic_order',array('status'=>4,'shipmentID'=>$shipmentID));
		echo json_encode($data);
		exit(1);
	}
	
	function magic_ship_out()
	{
		
		
		$url = 'http://shipment.phantasia.com.tw/order/shipment_to_out';
		$shipmentID = $this->input->post('shipmentID');
		$orderID = $this->input->post('orderID');
		$result = $this->paser->post($url,array('shipmentID'=>$shipmentID),true);
		$data['result'] = true;
		$this->db->where('orderID',$orderID);
		$this->db->update('pos_magic_order',array('status'=>5));//完成送達物流
		echo json_encode($data);
		exit(1);
		
	}
	
	function get_magic_status()
	{
		$this->load->model('Product_model')	;
		$orderID = $this->input->post('orderID');
		$data = $this->Product_model->magicOrderInf($orderID);
		$data['result'] =true;
		echo json_encode($data);
		exit(1);
	}
	
	
	function get_week_magic_order()
	{
		$this->load->model('Order_model')	;
		$data['product'] = $this->Order_model->getWeekMagicOrder();
		
		$data['result'] =true;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	function delete_magic_order()
	{
		$orderID = $this->input->post('orderID');
		$this->db->where('orderID',$orderID);
		$this->db->delete('pos_magic_order');
		$data['result'] =true;
		$url = 'http://shipment.phantasia.com.tw/order/delete_order';
		$result = $this->paser->post($url,array('orderID'=>$orderID),true);

		
		echo json_encode($result);
		exit(1);
	}
	
	
	function predit_notify()
	{
		$today = date("Y-m-d");
		$d = date("Y-m-d", strtotime($today."+3 day"));
		$data = $this->Product_model->getProductByTime($d);
		$title=$d.'預計到貨商品';
		$content='<h1>'.$title.'</h1>';
		$content.='您好：預計到貨商品如下，請確認到貨時間是否正確，<br/>若到貨時間有更動，請上系統調整，以利店家回覆客人<br/>';
		
			$content.='<table>';
		foreach($data as $row)
		{
			$content.='<tr><td>'.$row['name'].'</td><td>'.$row['ZHName'].'</td><td>'.$row['num'].'</td>.</tr>';
			
			
		}
			$content.='</table>';
		
		if(count($data)>0) $this->Mail_model->myEmail('lintaitin@gmail.com,phantasia.pa@gmail.com,phantasia.pm@gmail.com' ,$title,$content);
		else echo 'empty';
		
		$this->db->where('preTime <',$today);
		$this->db->delete('pos_product_preTime');
		
	}
	
	function inshop_flow()
	{
		$this->db->select('ZHName,sum(times) as total');
		$this->db->join('pos_product','pos_inshop_amount.productID = pos_product.productID','left');
		$this->db->group_by('pos_product.productID');
		$this->db->order_by('total','desc');
		$query = $this->db->get('pos_inshop_amount');
		$d = $query->result_array();
		
	
		
		echo  tableShowing($d);
		
	}
	
	
	
	
	function inshop_detect()
	{
		$shopID = $this->uri->segment(3);
		$this->db->where('pos_order_shipment.shopID',$shopID);
		$this->db->where('pos_order_shipment.status >=',2);
		$this->db->order_by('pos_order_shipment_detail.comment');
		$this->db->select('ZHName,pos_order_shipment.shippingNum,pos_order_shipment_detail.sellPrice,pos_order_shipment_detail.sellNum,pos_order_shipment_detail.comment,pos_order_shipment.arriveTime');
		$this->db->where('pos_order_shipment_detail.comment !=','');
		$this->db->join('pos_order_detail','pos_order_detail.id = pos_order_shipment_detail.rowID','left');
		$this->db->join('pos_product','pos_order_detail.productID = pos_product.productID','left');
		$this->db->join('pos_order_shipment','pos_order_shipment.id = pos_order_shipment_detail.shipmentID','left');
		
		$query = $this->db->get('pos_order_shipment_detail');
		$d = $query->result_array();
		echo  tableShowing($d);
		
	}
    
    function three_way_merage()
    {
        $phaBid = $this->input->get('phaBid');
        $productID = $this->input->get('productID');
        $pID = $this->input->get('pID');
        $result['connect'][0] = false;//系統連網站
        $result['connect'][1] = false;//網站連商城
        $result['connect'][2] = false;//商城連系統
        
        $t= 0;
        while(($result['connect'][0]&&$result['connect'][1]&&$result['connect'][2])==false && $t<=2)
        {
   
        if($productID!=0)
        {
        
             $product = $this->Product_model->getProductByProductID($productID,0);
            $result['product'] = $product;
          
            if(!empty($product))
            {
                if($product['phaBid']!=0)
                {
                    if($phaBid!=0 && $phaBid !=$product['phaBid'])
                    {
                        $result['err'] = 'phaBid does not match on pos';


                    }
                    else 
                    {

                         $result['connect'][0] = true;
                        $phaBid = $product['phaBid'];
                    }
                }
                 else if($phaBid!=0)
                {

                    // make link from pos to web

                         $result['connect'][0] = true;
                    $result['product']= $this->Product_model->makeBidLink($phaBid,$productID);

                }
            
                
                
            }
           
        }
        
        if(($result['connect'][0]&&$result['connect'][1]&&$result['connect'][2])==true)break;
        
        
        
        if($phaBid!=0)
        {
            
           
            
            
            //看看網站是否連上商城
            $r = $this->paser->post($this->data['website'].'/bg/chk_mart',array('bid'=>$phaBid),true);
        
            $website =  $r['bg'];
            $result['website'] = $website;
			
            if(!empty($website))
            {
                if($website['pID']!=0)
                {
                     if($pID!=0 && $website['pID']!= $pID)
                    {

                         $result['err'] = 'pID does not match on website';

                    }

                     else 
                     {
                    
                         $result['connect'][1] = true;
                         $pID = $website['pID'];
               
                    
                     }
                    
                }               
                else if($pID != 0)
                {
                   //make link from web to mart
                  $this->paser->post($this->data['website'].'/bg_controller/make_mart_link',array('pID'=>$pID,'phaBid'=>$phaBid),true);
         
                  
                 $result['connect'][1] = true; 
                    
                    
                }
                
                
            }
           
            if($productID==0)
            {
                //逆向回測商城
               $product = $this->Product_model->getAllProductByBid($phaBid);
                if(count($product)==1) 
                {
                    $result['product'] = $product[0];
                     $result['connect'][0] = true;
                     $productID = $result['product']['productID'];
                }
          
                
                
                
            }
                
                
            
        }
        if(($result['connect'][0]&&$result['connect'][1]&&$result['connect'][2])==true)break;
            
        if($pID!=0)
        {
             //看看商城是否連上系統
            
            //$mart =  somefunction();
              $r = $this->paser->post($this->data['martadmDomain'].'/product/chk_product_link',array('pID'=>$pID),true);
        
            $mart =  $r['product'];
            $result['mart'] = $mart;
            if(!empty($mart))
            {
                if($mart['productID']!=0)
                {
                    if($productID!=0 && $mart['productID']!= $productID)
                    {

                         $result['err'] = 'productID does not match on mart';

                    }
                    else 
                    {

                         $result['connect'][2] = true;
                        $productID = $mart['productID'];


                    }
                }
                else if($productID!=0)
                {

                    //make link from mart to pos
                    $this->paser->post($this->data['martadmDomain'].'/product/make_product_link',array('pID'=>$pID,'productID'=>$productID),true);

                     $result['connect'][2] = true;

                }

                
            }
            
                
            
            
            
            
        }
            $t++;
        }

        
        if($result['connect'][1]==false)
        {
            //make link from web to mart
            if($phaBid!=null && $pID!=null)
            {
                 $this->paser->post($this->data['website'].'/bg_controller/make_mart_link',array('pID'=>$pID,'phaBid'=>$phaBid),true);
            }
                 
            else
            {
                 $r =  $this->paser->post($this->data['martadmDomain'].'/product/get_all_product',array('pID'=>$pID,'productID'=>$productID),true);
         
            $result['allMartProduct'] = $r['allproduct'];
            
            }
            
           
        }
        
        $result['productID'] = $productID;
         $result['pID'] = $pID;
         $result['phaBid'] = $phaBid;
        if( $this->input->get('type')=='json')
        {
            $result['result'] = true;
            echo json_encode($result);
            
            
        }
        else
        {
            $this->load->view('three_way_merage',$result);
        }
    }
    
    
    
    
    
    function skb_change()
    {
        $this->db->where('suppliers',124);
        $query= $this->db->get('pos_product');
        $data  = $query->result_array();
        foreach($data as $row)
        {
         
            
            $this->db->where('barcode',$row['barcode']);
            $this->db->where('productID !=',$row['productID']);
            $query= $this->db->get('pos_product');
            
            $r = $query->row_array();
            if(!empty($r))
            {
                echo $r['productID'];
                 echo $row['productID'];
                $this->db->where('productID',$r['productID']);
                $this->db->update('pos_product_sell',array('productID'=>$row['productID']));
                
                  $this->db->where('productID',$r['productID']);
                $this->db->update('pos_order_adjust_detail',array('productID'=>$row['productID']));
                    
                $this->db->where('productID',$r['productID']);
                $this->db->update('pos_product_purchase',array('productID'=>$row['productID']));
                    
                    
                 $this->db->where('productID',$r['productID']);
                $this->db->update('pos_product_amount',array('productID'=>$row['productID']));
                
                   $this->db->where('productID',$r['productID']);
                $this->db->update('pos_product_back',array('productID'=>$row['productID']));
                
                
                $this->db->where('productID',$r['productID']);
                $query = $this->db->get('pos_current_product_amount');
                
                $c = $query->result_array();
                
                foreach($c as $each)
                {    
                
                       $this->db->where('shopID',$each['shopID']);
                        $this->db->where('productID',$row['productID']);
                
                         $this->db->delete('pos_current_product_amount'); 
                    
                    
                     
                    $this->db->where('shopID !=',0);
                   $this->db->where('id',$each['id']);
                
                $this->db->update('pos_current_product_amount',array('productID'=>$row['productID']))    ;
                
                
                
                
                }
                
                $this->db->where('productID',$r['productID']);
                 $this->db->delete('pos_product');  
            }
           
        }
        
         echo 'SKB finish close this page';
        
        
        
        
        
    }
    
    function sell_record_page()
    {
        
        
        
        $this->data['display'] = 'sell_record_page';
		$this->load->view('template',$this->data);	
        
        
    }
    
    
    
    function sell_record()
    {
        $page = $this->input->post('page');

        $num = 1;
        
        $this->db->order_by('id','asc');
        $this->db->limit($num,$num*$page);
        $this->db->where('time >=','2016-04-01 00:00:00');
        $query = $this->db->get('pos_product_sell');
        
        
        $data = $query->result_array();
        
       
        if(empty($data)) $data['result'] = false;
        else
        {
            foreach($data as $row)
            {
                if($row['shopID']>90||$row['productID']==0) continue;
              //  echo $row['productID'].','.$row['num'].'<br/>';
                $this->db->where('productID',$row['productID']);
                $query = $this->db->get('pos_product_rank');
                $r = $query->row_array();
                if(empty($r))
                {
                    $this->db->insert('pos_product_rank',array('productID'=>$row['productID'],'num'=>$row['num']));


                }
                else 
                {
                    $this->db->where('productID',$row['productID']);
                    $this->db->update('pos_product_rank',array('num'=>$r['num']+$row['num']));


                }

            }

           $data['result'] = true;
        }
           echo json_encode($data);
           
        
    }
    function get_product_check()
    {
        	$this->load->model('Product_model')	;
       $purchaseID = $this->input->post('purchaseID');
        
       $data['product'] = $this->Product_model->getProductCheck($purchaseID);
      if(empty($data['product']))   $data['result']  = false;
        else $data['result'] =true;
          echo json_encode($data);
        
        
    }
    
    function product_num_check_send()
    {
        $purchaseID = $this->uri->segment(3);
       /*
        $_POST['productID'][] = 8885408;
         $_POST['productID'][] = 8885407;
        $_POST['checkNum'][] = 3;
         $_POST['checkNum'][] = 3;
         */
        $product = $_POST['productID'];
        $checknum = $_POST['checkNum'];
  
        $i = 0;
        foreach($product as $row)
        {
            
            $num = $checknum[$i];
            $stockNum = $this->PO_model->getProductNum(0,$row,'2018-1');
            $datain = array(
                'purchaseID'=>$purchaseID,
                'productID' =>$row,
                'stockNum'  =>$stockNum,
                'checkNum'  =>$num,
                'time'      =>date("Y-m-d H:i:s")
            
            );
            $this->db->insert('pos_product_num_check',$datain);
                
            
            
            $i++;
        }
        $data['result'] = true;
        echo json_encode($data);
    }
    
    
    
    function product_amount_upload()
    {
        
        $data = array(
        array(	8883489	,	0	),
array(	8881695	,	0	),
array(	8882658	,	0	),
array(	8882647	,	0	),
array(	8883490	,	0	),
array(	8883768	,	3	),
array(	8883769	,	4	),
array(	8882021	,	25	),
array(	8882797	,	48	),
array(	8881906	,	25	),
array(	8884352	,	67	),
array(	8882780	,	147	),
array(	8882937	,	28	),
array(	8882609	,	26	),
array(	8882320	,	0	),
array(	8881664	,	11	),
array(	8884262	,	420	),
array(	8883309	,	433	),
array(	8883529	,	433	),
array(	8883994	,	433	),
array(	8883389	,	433	),
array(	8881915	,	50	),
array(	8883602	,	433	),
array(	8883217	,	433	),
array(	8884234	,	26	),
array(	8884259	,	384	),
array(	8881617	,	0	),
array(	8880024	,	16	),
array(	8884156	,	160	),
array(	8881771	,	16	),
array(	8883704	,	419	),
array(	8880019	,	20	),
array(	8881913	,	4	),
array(	8883542	,	89	),
array(	8881696	,	93	),
array(	8882603	,	12	),
array(	8880018	,	22	),
array(	8882507	,	12	),
array(	8880021	,	22	),
array(	8882975	,	20	),
array(	8883637	,	0	),
array(	8881437	,	82	),
array(	8883109	,	95	),
array(	8882612	,	25	),
array(	8884211	,	11	),
array(	8884212	,	11	),
array(	8882263	,	311	),
array(	8881537	,	32	),
array(	8880025	,	20	),
array(	8884263	,	150	),
array(	8883390	,	102.4	),
array(	8883391	,	102.4	),
array(	8884323	,	554	),
array(	8883218	,	102.4	),
array(	8884003	,	140	),
array(	8881871	,	120	),
array(	8881336	,	28	),
array(	8883219	,	102.4	),
array(	8883381	,	283	),
array(	8883388	,	15	),
array(	8881352	,	16	),
array(	8883295	,	294	),
array(	8881327	,	21	),
array(	8883177	,	275	),
array(	8883067	,	16	),
array(	8881920	,	81	),
array(	8883354	,	549	),
array(	8883584	,	190	),
array(	8882571	,	385	),
array(	8881908	,	69	),
array(	8881308	,	21	),
array(	8882207	,	1	),
array(	8883691	,	184	),
array(	8883894	,	379	),
array(	8882372	,	410	),
array(	8881309	,	19	),
array(	8882530	,	495	),
array(	8881412	,	17	),
array(	8883604	,	440	),
array(	8882577	,	242	),
array(	8881413	,	16	),
array(	39	,	253.5	),
array(	8883605	,	21	),
array(	8883801	,	188	),
array(	8882686	,	86	),
array(	8884246	,	35	),
array(	8882188	,	200	),
array(	8882036	,	0	),
array(	8883180	,	77	),
array(	8883380	,	202	),
array(	268	,	330	),
array(	8881670	,	21	),
array(	8883179	,	-41	),
array(	8883807	,	357	),
array(	8881539	,	15	),
array(	8882770	,	455	),
array(	8883942	,	163	),
array(	8884349	,	188	),
array(	8881410	,	618	),
array(	8882453	,	476	),
array(	8883844	,	275	),
array(	8882092	,	791	),
array(	8883033	,	47	),
array(	8881675	,	25	),
array(	8883311	,	140	),
array(	8883540	,	77	),
array(	8882529	,	20	),
array(	8883943	,	162	),
array(	8881922	,	0	),
array(	219	,	347	),
array(	8882087	,	475	),
array(	192	,	258	),
array(	8883080	,	105	),
array(	8883244	,	68	),
array(	8883478	,	41	),
array(	8884109	,	486	),
array(	8884159	,	550	),
array(	8883940	,	198	),
array(	8882978	,	20	),
array(	8883933	,	7	),
array(	8882264	,	49	),
array(	138	,	375	),
array(	8882010	,	215	),
array(	8883941	,	161	),
array(	8884134	,	428	),
array(	8884153	,	660	),
array(	8884196	,	11	),
array(	8881529	,	119	),
array(	8884388	,	295	),
array(	8883531	,	175	),
array(	8882220	,	42	),
array(	8884319	,	446	),
array(	8883900	,	5	),
array(	8882939	,	345	),
array(	8883045	,	571	),
array(	8883939	,	167	),
array(	8884017	,	160	),
array(	8884127	,	686	),
array(	8883601	,	220	),
array(	8884231	,	400	),
array(	8884093	,	278	),
array(	8884260	,	430	),
array(	8883254	,	251	),
array(	8882285	,	574	),
array(	8883582	,	30	),
array(	8884113	,	858	),
array(	8884241	,	495	),
array(	8884116	,	638	),
array(	8881704	,	149	),
array(	8881025	,	590	),
array(	8883778	,	384	),
array(	8883881	,	317	),
array(	8882199	,	121	),
array(	8882198	,	115	),
array(	8882200	,	121	),
array(	8882636	,	149	),
array(	8883387	,	660	),
array(	8881485	,	56	),
array(	8882049	,	131	),
array(	8883809	,	200	),
array(	8883813	,	200	),
array(	8884230	,	534	),
array(	8882767	,	271	),
array(	8884094	,	356	),
array(	8882438	,	105	),
array(	8883808	,	200	),
array(	8883812	,	200	),
array(	8883871	,	200	),
array(	8883882	,	348	),
array(	8883350	,	535	),
array(	8883471	,	593	),
array(	8884002	,	247	),
array(	8884122	,	270	),
array(	8882201	,	121	),
array(	42	,	437	),
array(	8881399	,	18	),
array(	8882578	,	716	),
array(	8883474	,	499	),
array(	8881718	,	149	),
array(	8883316	,	856	),
array(	8883783	,	233	),
array(	8884115	,	432	),
array(	8884309	,	497	),
array(	8884400	,	81	),
array(	8883202	,	446	),
array(	8883644	,	215	),
array(	8884126	,	644	),
array(	8882439	,	109	),
array(	8883267	,	295	),
array(	8883782	,	233	),
array(	8884197	,	969	),
array(	8884384	,	270	),
array(	8883181	,	200	),
array(	8883032	,	185	),
array(	8881973	,	250	),
array(	8883880	,	215	),
array(	8883997	,	495	),
array(	8883205	,	465	),
array(	8883901	,	7	),
array(	8884314	,	424	),
array(	43	,	211	),
array(	8882377	,	42	),
array(	8883304	,	153	),
array(	8883559	,	275	),
array(	8884092	,	325	),
array(	8883066	,	300	),
array(	8881319	,	307	),
array(	8881435	,	429	),
array(	8881765	,	85	),
array(	8882370	,	131	),
array(	8882959	,	193.5	),
array(	8883395	,	334	),
array(	8884141	,	210	),
array(	8884199	,	754	),
array(	8884248	,	325	),
array(	8883117	,	200	),
array(	8883771	,	1400	),
array(	8880077	,	126	),
array(	8884177	,	473	),
array(	8884198	,	449	),
array(	8883879	,	225	),
array(	63	,	237	),
array(	8881665	,	30	),
array(	8881764	,	179	),
array(	8882218	,	202	),
array(	8883487	,	510	),
array(	8883781	,	655	),
array(	8884200	,	579	),
array(	8883530	,	28.87	),
array(	8882633	,	472	),
array(	8883022	,	442	),
array(	8883171	,	215	),
array(	8884018	,	377	),
array(	8882032	,	240	),
array(	8883288	,	1728	),
array(	8883784	,	247	),
array(	8883062	,	754	),
array(	8883491	,	255	),
array(	8884016	,	716	),
array(	8883115	,	87	),
array(	8883287	,	1728	),
array(	8883329	,	249	),
array(	11109	,	311	),
array(	8882064	,	413	),
array(	8883008	,	264	),
array(	8883351	,	915	),
array(	8884326	,	600	),
array(	8882242	,	125	),
array(	8882371	,	93	),
array(	8882992	,	149	),
array(	8883043	,	149	),
array(	8883056	,	149	),
array(	8883142	,	149	),
array(	8883615	,	0	),
array(	8881483	,	119	),
array(	8881879	,	371	),
array(	8882091	,	420	),
array(	8882202	,	215	),
array(	8882982	,	540	),
array(	8883541	,	510	),
array(	8883564	,	325	),
array(	8884154	,	770	),
array(	8881703	,	149	),
array(	8882065	,	177	),
array(	8882369	,	131	),
array(	8882517	,	45	),
array(	8882795	,	30	),
array(	8882958	,	480	),
array(	8883291	,	1728	),
array(	8883999	,	28.87	),
array(	8882458	,	227.428	),
array(	8882574	,	275	),
array(	8882660	,	425	),
array(	8883141	,	720	),
array(	8883168	,	196	),
array(	8883630	,	391	),
array(	8883681	,	474	),
array(	8883982	,	444	),
array(	8884244	,	359	),
array(	8884283	,	308	),
array(	8884380	,	525	),
array(	8884395	,	384	),
array(	8881538	,	58	),
array(	8881962	,	124	),
array(	8882794	,	87	),
array(	8882974	,	87	),
array(	8882251	,	215	),
array(	8882261	,	221.523	),
array(	8882759	,	50	),
array(	8882981	,	201	),
array(	8883131	,	275	),
array(	8883185	,	0	),
array(	8883299	,	358	),
array(	8883633	,	194	),
array(	8884104	,	119	),
array(	8884233	,	551	),
array(	8884315	,	233	),
array(	8884316	,	310	),
array(	8881963	,	124	),
array(	8881965	,	124	),
array(	8882368	,	131	),
array(	8882634	,	0	),
array(	8882635	,	0	),
array(	8882654	,	52	),
array(	8883392	,	28.87	),
array(	8883705	,	28.87	),
array(	8881635	,	19	),
array(	8881976	,	525	),
array(	8882455	,	229	),
array(	8883010	,	484	),
array(	8883184	,	374	),
array(	8883302	,	601	),
array(	8883578	,	380	),
array(	8883679	,	398	),
array(	8884317	,	425	),
array(	8881592	,	143	),
array(	8882638	,	0	),
array(	8883209	,	80	),
array(	8883343	,	45	),
array(	8883459	,	23	),
array(	8883780	,	416	),
array(	8884014	,	264	),
array(	37	,	648	),
array(	8882001	,	212	),
array(	8882364	,	159	),
array(	8882402	,	297	),
array(	8883326	,	270	),
array(	8883477	,	191	),
array(	8883560	,	248	),
array(	8883793	,	496	),
array(	8884355	,	289	),
array(	8884356	,	912	),
array(	8880983	,	59	),
array(	8881966	,	60	),
array(	8882391	,	0	),
array(	8882823	,	14	),
array(	8882824	,	14	),
array(	8882825	,	14	),
array(	8882826	,	14	),
array(	8882827	,	14	),
array(	8882828	,	14	),
array(	8882829	,	14	),
array(	8882830	,	14	),
array(	8882985	,	150	),
array(	8883241	,	28.87	),
array(	8883292	,	1728	),
array(	8883310	,	28.87	),
array(	8881987	,	348	),
array(	8882002	,	239	),
array(	8882086	,	144	),
array(	8882378	,	476	),
array(	8882379	,	579	),
array(	8882768	,	284	),
array(	8883207	,	216	),
array(	8883223	,	437	),
array(	8883473	,	537	),
array(	8883492	,	236	),
array(	8884013	,	726	),
array(	8884095	,	420	),
array(	8881702	,	198	),
array(	8882187	,	53	),
array(	8882189	,	140	),
array(	8883041	,	200	),
array(	8883247	,	80	),
array(	8884125	,	449	),
array(	8884313	,	214	),
array(	134	,	226	),
array(	8880877	,	115	),
array(	8881497	,	119	),
array(	8881770	,	450	),
array(	8881809	,	265	),
array(	8882088	,	529	),
array(	8882238	,	696	),
array(	8882516	,	630	),
array(	8882941	,	223	),
array(	8883012	,	215	),
array(	8883030	,	704	),
array(	8883273	,	286	),
array(	8883384	,	468	),
array(	8883683	,	470	),
array(	8883803	,	313	),
array(	8884022	,	377	),
array(	8881348	,	525	),
array(	8881743	,	129	),
array(	8881928	,	52	),
array(	8882030	,	168	),
array(	8882269	,	112	),
array(	8882548	,	117	),
array(	8882590	,	89	),
array(	8882637	,	0	),
array(	8882674	,	683	),
array(	8883243	,	433	),
array(	8884155	,	265	),
array(	8884285	,	605	),
array(	53	,	630	),
array(	8882194	,	503	),
array(	8882275	,	290	),
array(	8882430	,	194	),
array(	8882616	,	876	),
array(	8883895	,	152	),
array(	8883934	,	488	),
array(	8884124	,	579	),
array(	8880092	,	162	),
array(	8881641	,	0	),
array(	8881687	,	199	),
array(	8881971	,	73	),
array(	8882031	,	200	),
array(	8882478	,	113	),
array(	8882591	,	238	),
array(	8882624	,	104	),
array(	8882690	,	104	),
array(	8882714	,	107	),
array(	8882715	,	107	),
array(	8883079	,	548	),
array(	8884106	,	191	),
array(	128	,	237	),
array(	8880091	,	131	),
array(	8881408	,	84	),
array(	8882090	,	480	),
array(	8882160	,	215	),
array(	8882543	,	274	),
array(	8882762	,	734	),
array(	8883011	,	240	),
array(	8883111	,	759	),
array(	8883220	,	215	),
array(	8883270	,	655	),
array(	8883271	,	259	),
array(	8883773	,	613	),
array(	8883776	,	813	),
array(	8883786	,	313	),
array(	8884102	,	774	),
array(	8884117	,	182	),
array(	8884252	,	349	),
array(	8884389	,	540	),
array(	8880093	,	108	),
array(	8880123	,	536	),
array(	8881361	,	102	),
array(	8881719	,	385	),
array(	8881731	,	65	),
array(	8881880	,	39	),
array(	8882055	,	45	),
array(	8882056	,	45	),
array(	8882068	,	129	),
array(	8882250	,	75	),
array(	8882454	,	105	),
array(	8882475	,	113	),
array(	8882518	,	110	),
array(	8882716	,	107	),
array(	8882717	,	107	),
array(	8882718	,	107	),
array(	8882719	,	107	),
array(	8882807	,	41	),
array(	8882808	,	41	),
array(	8882809	,	41	),
array(	8882810	,	41	),
array(	8882811	,	41	),
array(	8882812	,	41	),
array(	8882813	,	41	),
array(	8882814	,	41	),
array(	8882815	,	39	),
array(	8882816	,	39	),
array(	8882817	,	39	),
array(	8882818	,	39	),
array(	8882819	,	39	),
array(	8882820	,	39	),
array(	8882821	,	39	),
array(	8882822	,	39	),
array(	8882831	,	18	),
array(	8882832	,	18	),
array(	8882833	,	18	),
array(	8882834	,	18	),
array(	8882835	,	18	),
array(	8882836	,	18	),
array(	8882837	,	18	),
array(	8882838	,	18	),
array(	8882839	,	21	),
array(	8882840	,	21	),
array(	8882841	,	21	),
array(	8882842	,	21	),
array(	8882843	,	21	),
array(	8882844	,	21	),
array(	8882845	,	21	),
array(	8882846	,	21	),
array(	8882847	,	23	),
array(	8882848	,	23	),
array(	8882849	,	23	),
array(	8882850	,	23	),
array(	8882851	,	23	),
array(	8882852	,	23	),
array(	8882853	,	23	),
array(	8882854	,	23	),
array(	8882855	,	25	),
array(	8882856	,	25	),
array(	8882857	,	25	),
array(	8882858	,	25	),
array(	8882859	,	25	),
array(	8882860	,	25	),
array(	8882861	,	25	),
array(	8882862	,	25	),
array(	8882863	,	28	),
array(	8882864	,	28	),
array(	8882865	,	28	),
array(	8882866	,	28	),
array(	8882867	,	28	),
array(	8882868	,	28	),
array(	8882869	,	28	),
array(	8882870	,	28	),
array(	8882871	,	25	),
array(	8882872	,	25	),
array(	8882873	,	25	),
array(	8882875	,	25	),
array(	8882876	,	25	),
array(	8882877	,	25	),
array(	8882878	,	25	),
array(	8882879	,	25	),
array(	8882880	,	25	),
array(	8882881	,	25	),
array(	8882882	,	25	),
array(	8882883	,	25	),
array(	8882884	,	25	),
array(	8882885	,	25	),
array(	8882886	,	25	),
array(	8882887	,	32	),
array(	8882888	,	32	),
array(	8882889	,	32	),
array(	8882890	,	32	),
array(	8882891	,	32	),
array(	8882892	,	32	),
array(	8882893	,	32	),
array(	8882894	,	32	),
array(	8882895	,	39	),
array(	8882896	,	39	),
array(	8882897	,	39	),
array(	8882898	,	39	),
array(	8882899	,	39	),
array(	8882900	,	39	),
array(	8882901	,	39	),
array(	8882902	,	39	),
array(	8882903	,	41	),
array(	8882904	,	41	),
array(	8882905	,	41	),
array(	8882906	,	41	),
array(	8882907	,	41	),
array(	8882908	,	41	),
array(	8882909	,	41	),
array(	8882910	,	41	),
array(	8882911	,	32	),
array(	8882912	,	32	),
array(	8882913	,	32	),
array(	8882914	,	32	),
array(	8882915	,	32	),
array(	8882916	,	32	),
array(	8882918	,	32	),
array(	8882919	,	39	),
array(	8882920	,	39	),
array(	8882921	,	39	),
array(	8882922	,	39	),
array(	8882923	,	39	),
array(	8883101	,	70	),
array(	8883324	,	1728	),
array(	8883338	,	52	),
array(	8883348	,	68	),
array(	8883603	,	28.87	),
array(	8883845	,	119	),
array(	8880078	,	345	),
array(	8880903	,	130	),
array(	8881353	,	15	),
array(	8881521	,	416	),
array(	8881636	,	225	),
array(	8882093	,	551	),
array(	8882254	,	234	),
array(	8882376	,	226	),
array(	8882583	,	585	),
array(	8882761	,	510	),
array(	8882989	,	385	),
array(	8883013	,	435	),
array(	8883130	,	275	),
array(	8883143	,	493	),
array(	8883305	,	1109	),
array(	8883330	,	510	),
array(	8883884	,	226	),
array(	8883885	,	411	),
array(	8883983	,	286	),
array(	8884019	,	663	),
array(	8884243	,	574	),
array(	8884279	,	114	),
array(	8884280	,	114	),
array(	8884350	,	720	),
array(	8881401	,	30	),
array(	8881744	,	276	),
array(	8881745	,	276	),
array(	8881909	,	138	),
array(	8881926	,	60	),
array(	8882027	,	107	),
array(	8882053	,	416	),
array(	8882323	,	100	),
array(	8882365	,	107	),
array(	8882425	,	110	),
array(	8882553	,	68	),
array(	8882745	,	340	),
array(	8882766	,	271	),
array(	8882779	,	220	),
array(	8883327	,	80	),
array(	8883347	,	68	),
array(	8884286	,	605	),
array(	8884343	,	846	),
array(	19	,	940	),
array(	74	,	374	),
array(	258	,	570	),
array(	8880094	,	108	),
array(	8881585	,	275	),
array(	8881727	,	540	),
array(	8881931	,	341	),
array(	8882089	,	540	),
array(	8882227	,	360	),
array(	8882230	,	144	),
array(	8882367	,	135	),
array(	8882572	,	380	),
array(	8882620	,	265	),
array(	8882729	,	345	),
array(	8882765	,	425	),
array(	8882801	,	286	),
array(	8883009	,	301	),
array(	8883029	,	325	),
array(	8883139	,	440	),
array(	8883222	,	342	),
array(	8883284	,	550	),
array(	8883297	,	638	),
array(	8883300	,	231	),
array(	8883632	,	60	),
array(	8883779	,	520	),
array(	8883945	,	574	),
array(	8884101	,	657	),
array(	8884142	,	648	),
array(	8884264	,	38	),
array(	8884267	,	38	),
array(	8884281	,	114	),
array(	8884383	,	894	),
array(	8884422	,	114	),
array(	8884423	,	114	),
array(	8880122	,	367	),
array(	8881715	,	70	),
array(	8881716	,	47	),
array(	8881717	,	47	),
array(	8881730	,	200	),
array(	8881733	,	70	),
array(	8881967	,	60	),
array(	8881997	,	375	),
array(	8882028	,	107	),
array(	8882044	,	132	),
array(	8882120	,	90	),
array(	8882161	,	91	),
array(	8882186	,	65	),
array(	8882214	,	72	),
array(	8882286	,	464	),
array(	8882479	,	113	),
array(	8882480	,	34	),
array(	8882481	,	34	),
array(	8882542	,	575	),
array(	8882566	,	702	),
array(	8882607	,	240	),
array(	8882753	,	87	),
array(	8882782	,	462	),
array(	8882924	,	39	),
array(	8882925	,	39	),
array(	8882926	,	39	),
array(	8882960	,	256	),
array(	8883014	,	464	),
array(	8883081	,	293	),
array(	8883095	,	78	),
array(	8883231	,	220	),
array(	8883268	,	358	),
array(	8883396	,	210	),
array(	8883486	,	249	),
array(	8883562	,	484	),
array(	8883563	,	805	),
array(	8883684	,	99	),
array(	33	,	342	),
array(	151	,	577	),
array(	11152	,	709	),
array(	8880037	,	639	),
array(	8880038	,	1444	),
array(	8880047	,	548	),
array(	8880053	,	203	),
array(	8880055	,	247	),
array(	8880056	,	203	),
array(	8880076	,	312	),
array(	8880083	,	403	),
array(	8880087	,	503	),
array(	8880686	,	450	),
array(	8881394	,	378	),
array(	8881404	,	150	),
array(	8881639	,	248	),
array(	8881763	,	299	),
array(	8881805	,	950	),
array(	8881884	,	415	),
array(	8881907	,	374	),
array(	8881972	,	851	),
array(	8882005	,	558	),
array(	8882048	,	131	),
array(	8882133	,	275	),
array(	8882229	,	720	),
array(	8882432	,	726	),
array(	8882539	,	578	),
array(	8882579	,	163	),
array(	8882643	,	275	),
array(	8882720	,	160	),
array(	8883015	,	484	),
array(	8883021	,	285	),
array(	8883057	,	567	),
array(	8883121	,	1268	),
array(	8883144	,	870	),
array(	8883167	,	290	),
array(	8883224	,	574	),
array(	8883253	,	0	),
array(	8883260	,	189	),
array(	8883261	,	189	),
array(	8883296	,	548	),
array(	8883331	,	510	),
array(	8883472	,	644	),
array(	8883774	,	813	),
array(	8883883	,	204	),
array(	8883888	,	493	),
array(	8883944	,	553	),
array(	8884232	,	1450	),
array(	8884255	,	730	),
array(	8884266	,	38	),
array(	8884268	,	38	),
array(	8884318	,	773	),
array(	8884381	,	387	),
array(	8884385	,	180	),
array(	8884387	,	895	),
array(	8884418	,	38	),
array(	8884420	,	38	),
array(	8884421	,	38	),
array(	8884424	,	95	),
array(	8884425	,	333	),
array(	8884426	,	305	),
array(	8884427	,	238	),
array(	8884428	,	57	),
array(	156	,	358	),
array(	220	,	234	),
array(	11082	,	293	),
array(	11153	,	319	),
array(	11177	,	319	),
array(	8880043	,	234	),
array(	8880086	,	325	),
array(	8880191	,	574	),
array(	8880408	,	514	),
array(	8880566	,	498	),
array(	8881101	,	505	),
array(	8881226	,	405	),
array(	8881254	,	728	),
array(	8881256	,	780	),
array(	8881307	,	1105	),
array(	8881392	,	325	),
array(	8881732	,	132	),
array(	8881736	,	124	),
array(	8881773	,	286	),
array(	8881798	,	198	),
array(	8881835	,	338	),
array(	8881867	,	555	),
array(	8881869	,	238	),
array(	8881870	,	1235	),
array(	8881925	,	60	),
array(	8881961	,	107	),
array(	8882051	,	325	),
array(	8882076	,	173	),
array(	8882181	,	683	),
array(	8882190	,	47	),
array(	8882221	,	56	),
array(	8882255	,	94	),
array(	8882354	,	377	),
array(	8882355	,	377	),
array(	8882375	,	1044	),
array(	8882384	,	156	),
array(	8882389	,	102	),
array(	8882450	,	469	),
array(	8882477	,	113	),
array(	8882515	,	390	),
array(	8882540	,	579	),
array(	8882613	,	351	),
array(	8882651	,	400	),
array(	8882760	,	631	),
array(	8882769	,	304	),
array(	8882776	,	1225	),
array(	8882798	,	618	),
array(	8882805	,	735	),
array(	8882990	,	390	),
array(	8882997	,	133	),
array(	8883039	,	571	),
array(	8883040	,	499	),
array(	8883055	,	526	),
array(	8883059	,	1612	),
array(	8883076	,	423	),
array(	8883175	,	90	),
array(	8883228	,	220	),
array(	8883239	,	220	),
array(	8883272	,	262	),
array(	8883285	,	1172	),
array(	8883294	,	385	),
array(	8883345	,	45	),
array(	8883352	,	331	),
array(	8883353	,	75	),
array(	8883500	,	280	),
array(	8883565	,	434	),
array(	8883640	,	0	),
array(	8883802	,	200	),
array(	8883814	,	500	),
array(	8883815	,	820	),
array(	8883985	,	336	),
array(	8880108	,	154	),
array(	8881904	,	99	),
array(	8882132	,	193	),
array(	8882615	,	143	),
array(	8883169	,	670	),
array(	8883193	,	975	),
array(	129	,	213	),
array(	157	,	894	),
array(	178	,	200	),
array(	180	,	582	),
array(	218	,	370	),
array(	11088	,	551	),
array(	8880036	,	670	),
array(	8880079	,	972	),
array(	8880975	,	368	),
array(	8881293	,	545	),
array(	8881294	,	790	),
array(	8881334	,	220	),
array(	8881499	,	810	),
array(	8881504	,	890	),
array(	8881713	,	580	),
array(	8881783	,	314	),
array(	8881787	,	507	),
array(	8881788	,	284	),
array(	8881790	,	284	),
array(	8882039	,	2808	),
array(	8882124	,	347	),
array(	8882223	,	737	),
array(	8882403	,	20	),
array(	8882544	,	1286	),
array(	8882558	,	2340	),
array(	8882598	,	2225	),
array(	8882656	,	0	),
array(	8882680	,	2344	),
array(	8882802	,	394	),
array(	8882996	,	969	),
array(	8883006	,	322	),
array(	8883173	,	381	),
array(	8881376	,	878	),
array(	8881780	,	331	),
array(	8881781	,	371	),
array(	8881782	,	314	),
array(	8881785	,	325	),
array(	8881789	,	284	),
array(	8881791	,	284	),
array(	8881951	,	0	),
array(	8882130	,	2674	),
array(	8882266	,	230	),
array(	8883639	,	600	),
array(	11121	,	181	),
array(	8881778	,	91	),
array(	8881784	,	325	),
array(	8881786	,	507	),
array(	8882084	,	0	),
array(	8881775	,	103	),
array(	8881776	,	105	),
array(	8881779	,	257	),
array(	8882366	,	822	),
array(	8882406	,	0	),
array(	8883110	,	-368	),
array(	8882601	,	2157	),
array(	8881692	,	3865	),
array(	8882037	,	2829	),
array(	8881850	,	9	),
array(	8882592	,	587	),
array(	8882267	,	275	),
array(	8882644	,	0	),
array(	8882185	,	0	),
array(	8884334	,	0	),
array(	8884329	,	0	),
array(	8884333	,	0	),
array(	8884335	,	0	),
array(	8884332	,	0	),
array(	8884330	,	0	),
array(	8884331	,	0	),
array(	8884327	,	0	),
array(	8884328	,	0	),
array(	8884161	,	0	),
array(	8884160	,	0	)

        
        );
        
        
            foreach($data as $row)
            {
                echo $row[0].','.$row[1].'<br/>';
                $this->db->where('productID',$row[0]);
                $this->db->where('shopID',0);
                $this->db->order_by('year','desc');
                $this->db->order_by('month','desc');
                
                    
                $query =  $this->db->get('pos_product_amount');
                $r = $query->result_array();
                $i = 0;
                foreach($r as $each)
                {
                    
                    if( $each['year']==2017)  
                    {
                        
                       print_r($each);
                        $this->db->where('id',$each['id']);
                        $this->db->update('pos_product_amount',array('avgCost'=>$row[1],'totalCost'=>$row[1] * $each['num']));
                        
                    }
                    else
                    {
                        if($i<1)
                        {
                        print_r($each);
                            $this->db->where('id',$each['id']);
                        $this->db->update('pos_product_amount',array('avgCost'=>$row[1],'totalCost'=>$row[1] * $each['num']));
                        $i++;
                        }
                        
                        
                    }
                    
                    
                    
                    
                }
            }
        
        
    }
    
    function check_mart()
    {
        
        $productID = $this->input->post('productID');
        
    echo  $this->paser->post('https://martadm.phantasia.tw/product/chk_product_pos_link',array('productID'=>$productID),false);
     
        
    }
    function upload_to_mart()
    {
        
        $productID = $this->input->post('productID');
     
        $product = $this->Product_model->chkProductByProductID($productID);
        $insert['name'] = $product['ZHName'];
        $insert['simpleName']= $product['ZHName'];;
        $insert['ENGName'] = $product['ENGName'];;
        $insert['language'] = $product['language'];
        $insert['price'] = $product['price'];
        $insert['inventory'] = 3;
        $insert['maxOneSalse'] = 12;
        $insert['productID'] = $product['productID'];
        $insert['onlyBuy'] = 0;
        if($product['minDiscount'] <=90)    $insert['discType'] = 0;
        else $insert['discType'] = 1;
        $insert['pre_order'] = 0;
        
		if($product['phaBid']!=0)
		{
			$postData = array(
			'bid'    =>$product['phaBid'],
			'productID'    =>$product['productID'],
			'licence'=>'3d38db21d2ef4d23d10c38bb0ff308cf',
            'json'=>1
			);
			 $result = $this->paser->post('https://www.phantasia.tw/bg/pos_get_data',$postData,true);
          
         ;
           $insert['minpeople'] =  $result['bg']['min_people'];
           $insert['maxpeople'] =  $result['bg']['max_people'];
            if(is_numeric($result['bg']['age']))
           $insert['age'] =  $result['bg']['age'];
           $insert['year']   =  $result['bg']['year'];
            
            switch($result['bg']['runtimeOrg'])
            {
                case "under30":
				 $insert['runtime'] = "30分鐘";
				break;
			case "30to60":
				$insert['runtime'] = "45分鐘";
				break;
			case "60to90":
				$insert['runtime'] = "75分鐘";
				break;
			case "90to120":
				$insert['runtime'] = "90分鐘";
				break;
			case "over120":
				$insert['runtime'] = "120分鐘";
				break;     
                    
            }
         ;
           $insert['summary'] = $result['bgdes']['des'];
           $insert['introduction'] = $result['bgrule']['rule'];
            if(isset($result['videoData']['videoList'][0]['host_id']))
           $insert['video'] = $result['videoData']['videoList'][0]['host_id']  ;
        
		}
		else
		{
			$insert['result'] = false;
           
			
		}
        	$data['result'] =true;
	
     
		
         $insert['submit'] = true;
          $result = $this->paser->post('https://martadm.phantasia.tw/product/insertproduct',$insert,false);
          $photo = array('photoLink'=>'http://www.phantasia.tw/upload/bg/home/G/'.$product['phaBid'].'.jpg','productID'=>$product['productID']);
         $result = $this->paser->post('https://martadm.phantasia.tw/product/photo_upload_api',$photo,false);
          $photo =
         $data['result'] = true;
		echo json_encode($data);
		exit(1);
        
        
    }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */