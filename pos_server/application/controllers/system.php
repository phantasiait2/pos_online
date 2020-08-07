<?php

class System extends POS_Controller {

	function System()
	{
		parent::POS_Controller();
		
	}
	
	function get_shop()
	{
		
		$token = $this->input->post('token');
        $show = $this->input->post('show');
		
		if($token==0) $token = false;
		else 
		{
			$token = true;
			
		}
	   if($show==0) $show = false;
		else 
		{
			$show = true;
			
		}
	
        
		$data['shopData'] = $this->System_model->getShop($token,$show);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	

	function version_chk()
	{
	
		$client_version = $this->input->post('version');
		$shopID = $this->input->post('shopID');
		$licence = $this->input->post('licence');
		if($client_version==$this->data['version']) $data['needUpdate'] = false;
		else $data['needUpdate'] = true;;
		if($this->System_model->chkShop($shopID,$licence))$data['licence'] =true;
		else $data['licence'] =false;
		$data['result'] = true;
		$data['msg'] = array("商品介面修正，現在可直接用中英文查詢，若無法使用請重新整理","瘋桌遊使命：讓全人類得到真正的快樂","假日不插電，一起瘋桌遊","創造豐富人生，分享快樂時光");
		echo json_encode($data);
		exit(1);
	}
	function update_data()
	{
		/*
		if(!$this->System_model->chkShop($this->input->post('shopID'),$this->input->post('licence')))
		{
			$data['result']	 = false;
			echo json_encode($data);
			exit(1);
			
		}
		*/		
		$this->load->model('Member_model');
		$this->Member_model->updateMemberLevel();
		$timeStamp = $this->input->post('timeStamp');
		
		$data['memberData'] = $this->System_model->getMemberData($timeStamp);
		$data['shopMember'] = $this->System_model->getShopMemberData($timeStamp);
		$data['productData'] = $this->System_model->getProductData($timeStamp,$offset,$shopID);
		
		$data['productType'] = $this->System_model->getProductType($timeStamp);
		$data['result'] =true;
		echo json_encode($data);
		exit(1);		
		
	}

	function garbage_clean()
	{
		$this->load->model('System_model');
		$data['result'] =false;
		if(!$this->System_model->isClearning($this->input->post('shopID')))
		{		
			$this->db->where('shopID',$this->input->post('shopID'));
			$this->db->update('pos_sub_branch',array('dirtyBit'=>1));
			$data['memberData'] = $this->System_model->getAllMemberData();
			$data['productData'] = $this->System_model->getAllProductData();
	
			$data['result'] =true;
		}
		echo json_encode($data);
		exit(1);
		
		
		
	}
	function remote()
	{
		$this->data['shopData'] = $this->System_model->getShop();
		$this->data['shopID'] =  $this->uri->segment(3);
		if($this->data['shopID']==0) $this->data['shopID'] =1;
		$this->data['display'] = 'remote';
		$this->load->view('template',$this->data);	
		
		
	}
	function result_chk()
	{
		$shopID = $this->input->post('shopID');
		$this->db->where('shopID',$shopID);
		$this->db->where('ok',1);
		$query = $this->db->get('pos_remote');
		if($query->num_rows()>0) $data['result'] = true;
		else $data['result'] = false;
		echo json_encode($data);
		exit(1);
	}
	
	function get_direct_shop()
	{
		
		$query = $this->db->get('pos_direct_branch');
		$data['shop']= $query->result_array();
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	function get_shop_status()
	{
		$shopID = $this->input->post('shopID');

		$this->data['shopData']= $this->System_model->getShopByID($shopID);
		if(isset($this->data['shopData']['handShake']))
		{
				$data['handShake'] =$this->data['shopData']['handShake'];
			$data['handShakeSign'] =$this->data['shopData']['handShakeSign'];
			
		}
		$data['shopData'] = $this->data['shopData'];
		$data['result'] =true;
	
		echo json_encode($data);
		exit(1);
		
		
	}
	function set_shop_status()
	{
		$shopID = $this->input->post('shopID');
		$handShake = $this->input->post('status');
		$this->db->where('shopID',$shopID);
		
		$this->db->update('pos_sub_branch',array('handShake'=>$handShake,'handShakeSign'=>0));
		$data['result'] =true;
	
		echo json_encode($data);
		exit(1);
		
	}
	function sql_send()
	{
		$shopID = $this->input->post('shopID');
		$sql = $this->input->post('sql');
		$this->db->where('shopID',$shopID);
		$query = $this->db->get('pos_remote');
		$datain =array('sql'=>$sql,'data'=>'','ok'=>0);
		if($query->num_rows()>0)
		{
			$this->db->where('shopID',$shopID);
			$this->db->update('pos_remote',$datain);	
		}
		else
		{
			$datain['shopID'] =$shopID;
			$this->db->insert('pos_remote',$datain);
		}
		$data['result'] =true;
		echo json_encode($data);
		exit(1);
	}	
	
	
	function get_data()	
	{
		$shopID = $this->input->post('shopID');
		$this->db->where('ok',1);
		$this->db->where('shopID',$shopID);
		$query = $this->db->get('pos_remote');
		$data = $query->row_array();

		$result['result'] = json_decode($data['data'],true);
		
		$result['err'] =$data['err'];
		$this->load->view('remote_result',$result);
	}
	
	
	
	
	function get_data_pass()	
	{
		$shopID = $this->input->post('shopID');
		$this->db->where('ok',1);
		$this->db->where('shopID',$shopID);
		$query = $this->db->get('pos_remote');
		$data = $query->row_array();
		
		$ret = json_decode($data['data'],true);
		foreach($ret as $row)
		{
			$this->db->where('productID',$row['productID']);
			$this->db->select('productNum,ZHName,ENGName,language');
			$query = $this->db->get('pos_product');
			$result['result'][] = $query->row_array();
			
		}

		
	
		
		$result['err'] =$data['err'];
		$this->load->view('remote_result',$result);
	}
	
	
	function garbage_clean_fun()
	{
		$this->load->model('System_model');
		$this->db->where('shopID',$this->input->post('shopID'));
		$this->db->update('pos_sub_branch',array('dirtyBit'=>0));
		$data['result'] =true;
		echo json_encode($data);
		exit(1);
		
		
		
	}	
	
	function dump()
	{
		//if($this->data['my_pid']!=1) redirect('/err');
	
		include_once($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/PHPExcel/IOFactory.php');  
		  echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$reader = PHPExcel_IOFactory::createReader('Excel5'); // 讀取舊版 excel 檔案  
		$PHPExcel = $reader->load($_SERVER['DOCUMENT_ROOT']."/pos_server/upload/dump1.xls"); // 檔案名稱  
		$sheet = $PHPExcel->getSheet(0); // 讀取第一個工作表(編號從 0 開始)  
		$highestRow = $sheet->getHighestRow(); // 取得總列數  
	
		// 一次讀取一列  
		for ($row = 2; $row <= $highestRow; $row++) 
		{  
		  //product sump
		  
			if($sheet->getCellByColumnAndRow(19, $row)->getValue()=='天鵝堡') $suppliers =1;
			else $suppliers =0;
			//======================================= 
			$barcode = (string)$sheet->getCellByColumnAndRow(22, $row)->getCalculatedValue();
			if($barcode=="") $barcode = 0 ;
			else $barcode = substr($barcode,1);
			$datain=array(
			'id' => $sheet->getCellByColumnAndRow(1, $row)->getValue(),
			'barcode'  =>$barcode ,
			'ZHName'   => (string)$sheet->getCellByColumnAndRow(2, $row)->getValue(),
			'ENGName'  => (string)$sheet->getCellByColumnAndRow(3, $row)->getValue(),
			'language' => $sheet->getCellByColumnAndRow(5, $row)->getValue(),
			'price'    => $sheet->getCellByColumnAndRow(7, $row)->getValue(),
			'minDiscount'=> 65,
			'type' =>1,
			'timeStamp' => date("Y-m-d H:i:s"),
			'suppliers' => $suppliers,
			'category'  => $sheet->getCellByColumnAndRow(1, $row)->getValue()
			);
			if($datain['category']=='') $datain['category'] = 0;
			$this->db->insert('pos_product',$datain);
			if(!empty($barcode))
			{
			$product_purchase = array(
				'barcode'  => $barcode,
				'time'       => date("Y-m-d H:i:s"),
				'num'        => $sheet->getCellByColumnAndRow(8, $row)->getValue(),
				'shopID'     =>  1
				);
				
			$this->db->insert('pos_product_purchase',$product_purchase); 			
			}
			if($sheet->getCellByColumnAndRow(4, $row)->getValue()!="")
			{
			$sql = "update product set num = '".$sheet->getCellByColumnAndRow(8, $row)->getValue()."' where barcode='".$barcode."'";
			echo $sql.';<br/>';
			}
			
		
			if($sheet->getCellByColumnAndRow(3, $row)->getValue()=='男')$sex=1;
			else $sex =2;
			if($sheet->getCellByColumnAndRow(4, $row)->getValue()=='無')
			{
				$phone = $sheet->getCellByColumnAndRow(5, $row)->getValue();
			}
			else
			{
				$phone = '0'.$sheet->getCellByColumnAndRow(4, $row)->getValue();	
				
			}
			/*
			$data = array(
		'memberID' => (string)$sheet->getCellByColumnAndRow(0, $row)->getValue(),
		'name'     => $sheet->getCellByColumnAndRow(1, $row)->getValue(),
		'phone'    => $phone,
		'email'    => $sheet->getCellByColumnAndRow(7, $row)->getValue(),
		'birthday' => $this->excelTime($sheet->getCellByColumnAndRow(6, $row)->getValue()),
		'sex'    => $sex,
		'address'    =>$sheet->getCellByColumnAndRow(8, $row)->getValue(),
		'joinTime'     => $this->excelTime($sheet->getCellByColumnAndRow(11, $row)->getValue()),
		'timeStamp'     => date("Y-m-d H:i:s")  
		);	

		
	
			$level = $sheet->getCellByColumnAndRow(9, $row)->getValue();
			switch($level)
			{
				case '一般':	
				$levelID = 1;
				break;
				case '無限玩':	
				$levelID = 2;
				break;
				case '無限租':
				$levelID = 3;	
				break;								
				default:
				$levelID = 1;
				break;
				
			}
			$levelData = array();
			$levelData['levelID'] = $levelID;
			$levelData['shopID']= 1;
			$levelData['memberID']= $data['memberID'];	
			$levelData['timeStamp']=date("Y-m-d H:i:s");
			if($levelID>1) 
			{
				
				$levelData['reNew'] = 1;
				$levelData['dueTime'] = $this->excelTime($sheet->getCellByColumnAndRow(10, $row)->getValue());

			}
			$this->db->insert('pos_shop_member',$levelData);	
			$this->db->insert('pos_pha_members',$data);			
		*/
		}
		
		
		
		
		
	}
	
	function excelTime($date, $time = false) {
	if(function_exists('GregorianToJD')){
		if (is_numeric( $date )) {
			$jd = GregorianToJD( 1, 1, 1970 );
			$gregorian = JDToGregorian( $jd + intval ( $date ) - 25569 );
			$date = explode( '/', $gregorian );
			$date_str = str_pad( $date [2], 4, '0', STR_PAD_LEFT )
						."-". str_pad( $date [0], 2, '0', STR_PAD_LEFT )
						."-". str_pad( $date [1], 2, '0', STR_PAD_LEFT )
						. ($time ? " 00:00:00" : '');
			return $date_str;
		}
	}else{
		$date=$date>25568?$date+1:25569;
		/*There was a bug if Converting date before 1-1-1970 (tstamp 0)*/
		$ofs=(70 * 365 + 17+2) * 86400;
		$date =  date("Y-m-d",($date * 86400) - $ofs).($time ? " 00:00:00" : '');
	}
	return $date;
}

	function coorect()
	{
		$this->load->model('Product_model');
		$shopID = 5;
		$this->db->distinct('productID');
		$this->db->where('shopID',$shopID);
		$query = $this->db->get('pos_product_amount');
		$data  = $query->result_array();
	
		
		foreach($data as $row)
		{
	$productID = 		$row['productID'];
		$ret = $this->Product_model->getProductByProductID($row['productID'],$shopID);
		$purchasePrice = round($ret['purchaseCount'] *$ret['price']/100);
		
		$sql = "update product_amount set totalCost = ".$purchasePrice."*num where month>=3 and year =2012 and productID = ".	$productID  ;
		$datain =array('sql'=>$sql,'data'=>'','ok'=>0);
			$datain['shopID'] =$shopID;
			$this->db->insert('pos_remote',$datain);
		
		//$sql = "update pos_product_amount set totalCost = ".$purchasePrice."*num where month>=3 and year =2012 and shopID = 5 and productID =".$productID ;
		//$this->db->query($sql);
		}
					
		
		
		
	}


	function stock_dump()
	{
		$shopID= $this->uri->segment(3);
		$month= $this->uri->segment(4);
		echo "$shopID,$month";
		if($shopID==''||$shopID==0)$shopID = $this->input->post('shopID');
		if($month==''||$month==0)$month = $this->input->post('month');
		
		$handle = fopen($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/stockdump'.$shopID.'-'.$month.'.csv','r');
		$this->load->model('Product_model');
		while(!feof($handle))
		{
			$contents = fgets($handle,100);
			
			$contents_array = explode(',',$contents);
			$productNum = $contents_array[0];
			$num = $contents_array[1];
			$month = $contents_array[2];
			$year = $contents_array[3];
	
	
			//print_r($contents_array);
			$data = $this->Product_model->getProductByProductNum($productNum);	
			
			if(!isset($data['productID'])) echo '<br/>error'.$productNum.'<br/>';
			else
			{
				$productID = $data['productID'];
					if(isset( $contents_array[4])&& $contents_array[4]!='')$purchasePrice = round($contents_array[4]);
					else
					{
						 $ret = $this->Product_model->getProductByProductID($productID,$shopID);
						 $purchasePrice = round($ret['purchaseCount'] *$ret['price']/100);
						 
					}
					$totalCost =$purchasePrice*$num;
				
					$datain =array(
						'productID'=>$productID,
						'month'=>$month,
						'year'=>$year,
						'num' =>$num,
						'totalCost'=>$totalCost  
				);
				echo $totalCost .',';
				echo 
				//insert
				$sql = "INSERT INTO product_amount (productID, month, year,num,totalCost)".
					   "VALUES (".$datain['productID'].", ".$datain['month'].",".$datain['year'].",".$datain['num'].",".$datain['totalCost'].")";
				echo $sql.'<br/>';
				/*//update
				$sql = "update product_amount  set num = ".$datain['num']." and totalCost =$".$datain['totalCost']." where productID = $productID and month = $month, year = $year";
				 echo $sql;
	*/
	
				$datain['shopID'] =$shopID;
				$this->db->insert('pos_product_amount',$datain);
				//植入各分點電腦
				$datain =array('sql'=>$sql,'data'=>'','ok'=>0);
				$datain['shopID'] =$shopID;
			
				$this->db->insert('pos_remote',$datain);
				
			}
		}
		
		echo 'ok';
	}

	
function product_amount_transfer()
	{
		
		
		$this->load->view('product_transfer',$this->data);	
		
	}
	function product_amount_purchased()
	{
		$this->load->model('Product_model');
		$data['product'] = $this->Product_model->getProductPuchasedNum();	
		$data['result']	= true;
		echo json_encode($data);
		exit(1);	
		
	}
	function product_amount_update()
	{
		
			$this->load->model('Product_model');
		$productID = $this->input->post('productID');

		for($shopID=0; $shopID<22;$shopID++)
		{
			 $result = $this->PO_model->getProductAmountInf($shopID,$productID,'2015-5');
			//$result = $this->Product_model->getProductStock(array('productID'=>$productID),0);
		//	print_r($result);
			if(!empty($result))
			{
			$this->Product_model->currentNumUpdate($productID,$result['num'],$result['totalCost'],$result['avgCost'],$shopID);	
			
			$data['product'] = $result;
			}
		}
				$data['result']	= true;
		echo json_encode($data);
		exit(1);	
		
	}

	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */