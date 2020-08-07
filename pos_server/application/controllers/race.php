<?php

class Race extends POS_Controller {

	function Race()
	{
		parent::POS_Controller();
		
		
	}
	function index()
	{

		$query = $this->db->get('pos_race');
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		$this->load->model('Race_model');
		$ret = $query->result_array();
		$data['shopID'] = $this->data['shopID'];
		foreach($ret as $row)
		{
			$data['raceDatail'][$row['key']] = $row['val'];
			
			
		}
		
		$shop_num = 6;
	
		//粉絲團成長比例
		$max = 0;
		for($i=1;$i<=$shop_num;$i++)
		{
			$data['runOut']['facebook'][$i] = 0;
			$t = round(($data['raceDatail']['f_1_'.$i]-$data['raceDatail']['f_0_'.$i])*100/$data['raceDatail']['f_0_'.$i]);
			if($t>$max)$max = $t;
			$data['runOut']['fans_base'][] =$t;
			
		}
		$i=0;
		foreach($data['runOut']['fans_base'] as $row)	
		{
				$i++;
			if($max==0)$data['runOut']['fans_grade'][]=0;
			else 
			{
				$data['runOut']['fans_grade'][] =round($row*10/$max);
				$data['runOut']['facebook'][$i]+=round($row*10/$max);
			}
		
		}
		
		//每日是否po文
		$max = 0;
		for($i=1;$i<=$shop_num;$i++)
		{
			
			$t = $data['raceDatail']['f_2_'.$i];
			if($t>$max)$max = $t;
			$data['runOut']['post_base'][] =$t;
			
		}
		$i = 0;
		foreach($data['runOut']['post_base'] as $row)
		{
			$i++;
			if($max==0)$data['runOut']['post_grade'][]=0;
			else 	
			{
				$data['runOut']['post_grade'][] =round($row*10/$max);
				$data['runOut']['facebook'][$i]+=round($row*10/$max);
			}
			
		
		}
		//和粉絲互動性
		$max = 0;
		for($i=1;$i<=$shop_num;$i++)
		{
			
			$t = $data['raceDatail']['f_3_'.$i];
			if($t>$max)$max = $t;
			$data['runOut']['inter_base'][] =$t;
			
		}
		$i=0;
		foreach($data['runOut']['inter_base'] as $row)	
		{
			$i++;
			if($max==0)$data['runOut']['inter_grade'][]=0;
			else
			{
				 $data['runOut']['inter_grade'][] =round($row*10/$max);
				 $data['runOut']['facebook'][$i]+=round($row*10/$max);
			}
			
		}
		
		//有效問卷填寫份數
		$max = 0;
		for($i=1;$i<=$shop_num;$i++)
		{
			 $data['runOut']['ask'][$i]=  0;
			$t = $data['raceDatail']['q_'.$i];
			if($t>$max)$max = $t;
			$data['runOut']['ask_base'][] =$t;
			
		}
		
		$i= 0;
		foreach($data['runOut']['ask_base'] as $row)
		{
			$i++;
			if($max==0)$data['runOut']['ask_grade'][]=0;
			else 	
			{
				$data['runOut']['ask_grade'][] =round($row*15/$max);
				$data['runOut']['ask'][$i]+=round($row*15/$max);
			}
		
		}
		//消費者評價
		$max = 0;
		for($i=1;$i<=$shop_num;$i++)
		{
			
			$t = $data['raceDatail']['q_2_'.$i];
			if($t>$max)$max = $t;
			$data['runOut']['askR_base'][] =$t;
			
		}
		$i= 0;
		foreach($data['runOut']['askR_base'] as $row)
		{
			$i++;
			if($max==0)$data['runOut']['askR_grade'][]=0;
			else 	
			{
				$data['runOut']['askR_grade'][] =round($row*15/$max);
					$data['runOut']['ask'][$i]+=round($row*15/$max);
			}
		}


		//活動宣傳
		$max = 0;
		for($i=1;$i<=$shop_num;$i++)
		{
			
			$t = $data['raceDatail']['a_1_'.$i]+$data['raceDatail']['a_2_'.$i]+$data['raceDatail']['a_3_'.$i];
			if($t>$max)$max = $t;
			$data['runOut']['active_base'][] =$t;
			
		}
		foreach($data['runOut']['active_base'] as $row)	
		{
			if($max==0)$data['runOut']['active_grade'][]=0;
			else $data['runOut']['active_grade'][] =round($row*30/$max);
		
		}
								
					
						
						
						
						
						
						

		//產品
		$productList = array
		(
			array('productID'=>8881878  ,'val'=>3,'name'=>'聖城尋寶(x3)'),		
			array('productID'=>8881696  ,'val'=>2,'name'=>'三國殺(x2)'),
			array('productID'=>8881795  ,'val'=>2,'name'=>'祕寶快手(x2)'),
			array('productID'=>8882046  ,'val'=>2,'name'=>'喵喵快手(x2)'),
			array('productID'=>8881740  ,'val'=>2,'name'=>'龜兔賽跑(x2)'),
			array('productID'=>8881871  ,'val'=>2,'name'=>'夢幻女僕餐廳(x2)'),
			array('productID'=>8881915  ,'val'=>1,'name'=>'三國殺軍爭擴充(x1)'),
			array('productID'=>8881906  ,'val'=>1,'name'=>'三國殺風擴充(x1)'),
			array('productID'=>8882021  ,'val'=>1,'name'=>'三國殺火擴充(x1)'),
			array('productID'=>8882321  ,'val'=>1,'name'=>'聖誕折扣)')
		
		
		
		);
		$data['productList'] = $productList;
		$date = date("Y-m-d");
		$date = '2013-09-30';
		$data['product']= $this->Race_model->getRaceProduct($date,$productList);
		$max = 0;
		echo $date;
		
		for($i=1;$i<=$shop_num;$i++)
		{
			$t = 0;
			foreach($productList as $each)
			{
				if(isset($data['product'][$i][$each['productID']]))		 $t +=$data['product'][$i][$each['productID']]*$each['val'];
			}
			if($t>$max)$max = $t;
			$data['runOut']['product_base'][] =$t;
			
		}
		foreach($data['runOut']['product_base'] as $row)	
		{
			if($max==0)$data['runOut']['product_grade'][]=0;
			else $data['runOut']['product_grade'][] =round($row*20/$max);
		
		}
		
		for($i=0;$i<$shop_num;$i++)
		{			
			$data['runOut']['result_grade'][] = 
				$data['runOut']['product_grade'][$i]+
				$data['runOut']['active_grade'][$i]+
				$data['runOut']['ask'][$i+1]+
				$data['runOut']['facebook'][$i+1];
				
			
			
							
			
		}
		
		$data['shop_num'] = $shop_num;
		
		//$this->load->view('race_index');	
					
		$this->load->view('race',$data);
	}
	function best30()
	{
		$this->load->model('Race_model');
		$data['shopID'] = $this->uri->segment(3);
		$data['account'] = $this->uri->segment(4);
		$data['pw'] = $this->uri->segment(5);
		
		 $year = $this->input->get('year');
		 $month = $this->input->get('month');
		
		if(empty($year)||$year==0)
		{
			$t = getdate();
			$year = $t['year'];
			$month = $t['mon'];
			
			
		}
		//echo time().'a<br/>';
		
			$best30Major =  $this->Race_model->getBest30MajorAllSell($year,$month);
			$data['best30Major'] = 	$best30Major['majorInf'];		
		$data['info'] =  $this->Race_model->getAccountInfo($data['shopID'],$data['account']);
		
		 $best= $this->Race_model->getBest30(0,$year,$month);
	
;
		foreach($best as $row) 
		{ 
			//if(!isset($major[$row['shopID']] ))
			//$major[$row['shopID']] = $this->Race_model->getBest30MajorSell($row['shopID'],$year,$month);
		
			
			if(isset($best30Major['major'][$row['shopID']]))
			{
				
				$t = 0 ;
				foreach($best30Major['major'][$row['shopID']] as $each)
				{
					$num = $this->Race_model->getSellNum($row['shopID'],$year,$month,$row['account'],$each['productID']);
					
					$t += $each['bonusDiff'] *$num;
					
				}

				$row['bonusTotal'] += $t;
			}
			$row['val'] = $row['bonusTotal'];
			$data['r'][] = $row;
		}
		//echo time().'c<br/>';
		if(isset($data['r']))
		 usort($data['r'], 'cmpValue');
		//echo time().'d<br/>';
		$data['year'] = $year;
		$data['month'] = $month;
		
		$this->load->view('best30',$data);
	}
	
	function best30_online()
	{
		 $t = getdate();
        $this->data['url']	=$this->data['serverDomain'].'race/best30/'.$this->data['shopID'].'/';	
		$this->data['display'] = 'empty_page';
		$this->load->view('template',$this->data);
        
	}
	function best30_detail()
	{
	
		$this->load->model('Race_model');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		$account = $this->input->post('account');
		$shopID = $this->input->post('shopID');
		
		$ret = $this->Race_model->getBest30Detail($shopID,$year,$month,$account);
		$data['record'] = $ret['data'];
		$data['sell'] = $ret['sell'];
		//$data['mart'] = $this->Race_model->getMartProduct($shopID,$month);
		$data['result'] = true;
		$this->load->view('best30_detail',$data);
		
	}
	function best30_inf_send()
	{
		$this->load->model('Race_model');
		$datain['shopID'] = $this->input->post('shopID');
		$datain['account'] = $this->input->post('account');
		$datain['name'] = $this->input->post('name');
			$datain['phone'] = $this->input->post('phone');
		$datain['address'] = $this->input->post('address');
		$datain['IDNumber'] = $this->input->post('IDNumber');
		$datain['bankCode'] = $this->input->post('bankCode');
		$datain['bankAccount'] = $this->input->post('bankAccount');
		$pwCode = $this->input->post('pwCode');
		$data['info'] =  $this->Race_model->getAccountInfo($datain['shopID'],$datain['account']);
		if(empty($data['info'] ))
		{
			$this->db->insert('pos_best30_inf',$datain);
			
		}
		else
		{
			$this->db->where('id',$data['info']['id']);
			$this->db->update('pos_best30_inf',$datain);
		
		}
		redirect('/race/best30/'.$datain['shopID'].'/'.$datain['account'].'/'.$pwCode);
		
		
		
		
	}
	
	
	function best30_report()
	{
		$this->load->model('Race_model');
		$t = getdate();
      	$this->data['product'] = $this->Race_model->getbest30Inf();
		
		$this->data['year'] = $this->input->post('year');
		$this->data['mon'] = $this->input->post('mon');
		
		
		//$this->data['display'] = 'best30_report';
		$this->load->view('best30_report',$this->data);
		
		
	}
	function online()
	{
		$this->data['shop'] = $this->System_model->getShop(true);
		//$this->data['cashInRegister'] = $data['remain'];
	
		$this->data['display'] = 'race_index';
		$this->load->view('template',$this->data);
		
		
	}
	
	function edit_send()
	{
		$key = $this->input->post('key');
		$val = $this->input->post('val');
		$this->db->where('key',$key);
		$query = $this->db->get('pos_race');
		
		
		$datain	 = array('val'=>$val);
	
		if($query->num_rows()>0)
		{ 
			$this->db->where('key',$key);
			$this->db->update('pos_race',$datain);
			
		}
		else
		{
		;
			if($key!='')
			{
			$datain['key'] = $key;
			$this->db->insert('pos_race',$datain);	
			}
			
		}
	
			$data['result']  = true;
		echo json_encode($data);
		exit(1);
	}
	
	
	function divide()
	{
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		$shopID= $this->data['shopID'];
	
		if($shopID==0)  $shopID =  $this->uri->segment(3);
		$year =  $this->uri->segment(4);	
		$mon =  $this->uri->segment(5);
		$data['monRecord'] = $this->Accounting_model->getMonReport($mon,$year,$shopID);
			$data['monSecondHand'] = $this->Accounting_model->monSecondHand($mon,$year,$shopID);
			$data['monBack'] = $this->Accounting_model->monBack($mon,$year,$shopID);
			$profit = $this->Accounting_model->caculateProfit($data['monRecord'],$data['monSecondHand'],$data['monBack']);
	
			$data['monVerify'] = $profit['monVerify'];
			$data['monTotal'] = $profit['monTotal'];
	
		 $data['monExpenses'] = $this->Accounting_model->getMonExpenses($mon,$year,$shopID);
		$this->load->view('divide',$data);
	}
	
	function edit_info_get()
	{
		$this->load->model('Race_model');
		$datain['shopID'] = $this->input->post('shopID');
		$datain['account'] = $this->input->post('account');
		if(md5($this->input->post('pw'))==$this->input->post('pwCode'))	
		{
			
			$data['info'] =  $this->Race_model->getAccountInfo($datain['shopID'],$datain['account']);
			$data['result'] = true;
			
		}
		else $data['result'] = false;
		
			echo json_encode($data);
		exit(1);
		
	}
	

	function race_content()
	{
		$this->load->model('Accounting_model');
		$time = getdate();
		$data['shopID'] = $this->uri->segment(3);
	
		$this->load->model('System_model');
		$data['shopList'] = $this->System_model->getShop(true);			
		// threshold
		$data['rentThreshold'] =array(1=>6000,2=>6000,3=>7500);// rent Threshold  aword 1
		$data['sellThreshold'] = array(1=>132000,2=>40000,3=>60000);//sell Threshold aword 2
		$data['memberThreshold'] = array(1=>32500,2=>20000,3=>52000);//member Threshold aword 3
		$data['totalThreshold']= array(1=>220000,2=>120000,3=>160000); //total Threshold aword 4	
		$data['selectaThreshold']= array(1=>30000,2=>30000,3=>30000); //selecta Threshold aword 5	
		
	
		foreach($data['shopList'] as $col)
		{ 
			$data['groupOn']['s_'.$col['shopID']] = 0 ;
			$record = $this->Accounting_model->getMonReport($time['mon'],$time['year'].'-'.$time['mon'].'-'.$time['mday'],$col['shopID']);
			
			$data['name']['s_'.$col['shopID']] = $col['name'];
			//initial
			$data['rent']['s_'.$col['shopID']] = 0;// rent  aword 1
			$data['sell']['s_'.$col['shopID']] = 0;//sell aword 2
			$data['member']['s_'.$col['shopID']] = 0;//member aword 3
			$data['total']['s_'.$col['shopID']]= 0; //total aword 4
			$data['selecta']['s_'.$col['shopID']]= 0;//selecta aword 5
			foreach($record as $row)
			{
	
					$data['total']['s_'.$col['shopID']] += $row['sellPrice']*$row['sellNum'];
					//game rent 
					if($row['type']==3)
					{
						
						$data['rent']['s_'.$col['shopID']] +=$row['sellPrice']*$row['sellNum'];
						
					}
					//game sell
					else if($row['type']==1||$row['type']==7||$row['type']==8)				
					{
						$data['sell']['s_'.$col['shopID']] += $row['sellPrice']*$row['sellNum'];
					}
					//member
					else if($row['type'] == 6) 
					{
						$data['member']['s_'.$col['shopID']] +=$row['sellPrice']*$row['sellNum'];
					}
					if($row['productID']==8881365) $data['groupOn']['s_'.$col['shopID']]+=$row['sellNum'];
					
					
					if($row['publisher']=='Selecta'&&(int)substr($row['time'],8,2)>12)
					{
						$data['selecta']['s_'.$col['shopID']]+=	$row['sellPrice']*$row['sellNum'];
						
					}
					
			}
		}
		asort($data['rent']);
		asort($data['sell']);
		asort($data['member']);
		asort($data['total']);
		asort($data['selecta']);
	
		$data['rent'] = array_reverse($data['rent']);
		$data['sell'] = array_reverse($data['sell']);
		$data['member']= array_reverse($data['member']);
		$data['total'] = array_reverse($data['total']);
		$data['selecta'] = array_reverse($data['selecta']);
		$data['num'] = 3 ; //show lead num
			
	
		return $data;	
	}
	
	function ted_day_mail()
	{
		$this->load->model('System_model');
		$this->data['shop'] = $this->System_model->getShop(true);
		
		$year = $this->uri->segment(3);
		$month = $this->uri->segment(4);
		$day = $this->uri->segment(5);
		if(empty($year))
		{
			$time = getdate();	
			$year = $time['year'];
			$month = $time['mon'];
			$day = $time['mday'];
			
		}
		
		$result = $this->paser->post('http://shipment.phantasia.com.tw/race/ten_day_report/'.$year.'/'.$month.'/'.$day,array(),false);
		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
		echo $result;
		$content =$result;
		mb_internal_encoding('UTF-8');
		
		$email = '';
		foreach($this->data['shop'] as $row) 
			if($row['shopID']<=600&&!empty($row['email']))	
            {
                
                if($day==1)
                {
                $bonusResult = $this->paser->post('http://shipment.phantasia.com.tw/accounting/member_bonus_notify/',array('shopID'=>$row['shopID']),false);
		          $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
		
		         
                   
                    if($bonusResult!='')
                    $this->Mail_model->myEmail($row['email'].',lintaitin@gmail.com',date("Y-m",mktime(0,0,0,$month,0,$year))."紅利兌換明細" ,$bonusResult,$headers);
                   
                   
                     }
                $email[] = $row['email'];
                
              
            }
//		echo $email;
		$email[] = 'phoenickimo@gmail.com';
		$email[] = 'lintaitin@gmail.com';
		$email[] = 'phantasia.odp@gmail.com';
			
		//$this->db->reconnect();
		$this->Mail_model->groupEmail($email,mb_encode_mimeheader( date("Y-m-d",mktime(0,0,0,$month,$day-1,$year))."十日報" ,'UTF-8'),$content,$headers);

		echo 'ok';
	}
    
   
    
    
	
	function ten_day_report()
	{
        set_time_limit(3000000) ;
		ini_set('memory_limit','204800M');
		$year = $this->uri->segment(3);
		$month = $this->uri->segment(4);
		$day = $this->uri->segment(5);
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		  
		if(empty($year))
		{
			$time = getdate();	
			$year = $time['year'];
			$month = $time['mon'];
			$day = $time['mday'];
			
		}
		
	
		$date = date("Y-m-d",mktime(0,0,0,$month,$day-1,$year));
		$data['date'] = $date;
		$data['tenDay'] = $this->Accounting_model->getTenDay($date);
		;
		$this->load->view('tenday_report',$data);
	
		
		
	}
	
	
	function get_sell_record()
	{
		$this->load->model('Product_flow_model');
		$this->load->model('Product_model');
		$from  = $this->input->post('from');
		$to = $this->input->post('to');
		$productID = $this->input->post('productID');
		
		 $data['a'] = $this->Product_flow_model->getAdjust($from,$to,0,$productID);

        //銷
        $data['s'] = $this->Product_flow_model->getShipmentOut($from,$to,0,$productID);
        
		$data['sr'] = $this->Product_model->getProductSellRecord($productID,$from, 0,$to);
		$data['result'] = true;
		
		echo json_encode($data);
		exit(1);
	}
	
    function get_end_sell_record()
    {
        $this->load->model('Product_flow_model');
		$this->load->model('Product_model');
		$from  = $this->input->post('from');
		$to = $this->input->post('to');
		$productID = $this->input->post('productID');
		
		
        
		$data['sr'] = $this->Product_model->getProductSellRecord($productID,$from, 0,$to);
		$data['result'] = true;
		
		echo json_encode($data);
		exit(1);
        
        
    }
    
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */