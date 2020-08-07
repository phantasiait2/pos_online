<?php

class Accounting extends POS_Controller {

	function Accounting()
	{
		parent::POS_Controller();
		
		
	}
	function index()
	{
          if($this->data['level']==-1)	redirect('/supplier/product');
		if($this->data['shopID']==0&&$this->data['level']<80) redirect('/');
		$this->load->model('System_model');
		$this->data['js'] = $this->preload->getjs('pos_accounting');
		$this->data['js'] = $this->preload->getjs('jquery.tablesorter');
		$this->data['js'] = $this->preload->getjs('pos_product_query');
		$this->data['js'] = $this->preload->getjs('pos_product');
        $this->data['js'] = $this->preload->getjs('date_format');
        
        $this->data['js'] = $this->preload->getjs('jquery-ui-1.8.16.custom.min');
        $this->data['js'] = $this->preload->getjs('jquery-ui-timepicker-addon');
        $this->data['css'] = $this->preload->getcss('jquery-ui-timepicker-addon');
       
        $this->data['css'] = $this->preload->getcss('jquery-ui-1.8.16.custom');
		$this->data['shop'] = $this->System_model->getShop(true);
		//$this->data['cashInRegister'] = $data['remain'];
		$this->data['display'] = 'accounting';
		$this->load->view('template',$this->data);	
	}
	
	
	function register_io()
	{	
		/*
		if(!$this->System_model->chkShop($this->input->post('shopID'),$this->input->post('licence')))
		{
			$data['result']	 = false;
			echo json_encode($data);
			exit(1);
			exit(1);
			
		}
		*/
		$datain['outputItem'] = $this->input->post('outputItem');			
		$datain['time'] = $this->input->post('time');
		$datain['MIN'] = $this->input->post('MIN');
		$datain['MOUT'] =  $this->input->post('MOUT');
		$datain['aid'] = $this->input->post('aid');
		$datain['note'] =  $this->input->post('note');
		$datain['cashType'] =  $this->input->post('cashType');
		$datain['remain'] = $this->input->post('remain');	
		$datain['shopID'] = $this->input->post('shopID');	
		$this->db->insert('pos_cash_register',$datain);
		$data['result'] = true;
		echo json_encode($data);
	}
	function get_day_report($return=false,$input='',$saveResult = false)
	{
		
		$testKey = false;
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		$this->load->model('Member_model');
		$saveResult = false;
		if($return)
		{
				$year = $input['year'];	
				$mon =  $input['mon'];		
				$mday =  $input['mday'];		
				$data['date'] =$year.'-'.$mon.'-'.$mday;
				$shopID =  $input['shopID'];	
		
		}
		else
		{
			$year = $this->input->post('year');	
			$mon = $this->input->post('mon');	
			$mday = $this->input->post('mday');	
			if($testKey) $mday = 25;
			$data['date'] =$year.'-'.$mon.'-'.$mday;
			if($this->data['shopID']==0)$shopID =  $this->input->post('shopID');
            else $shopID = $this->data['shopID'];
           
            $saveResult = $this->input->post('saveResult');
         
			if($saveResult==0) $saveResult=false;
			if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'$saveResult'.$saveResult));
		}
		if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'1'));
	
		$createFile  = false;
		if($saveResult)
		{
            
            
			$dir = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/report/'.$year.'_'.$mon;
			if(!file_exists($dir) )mkdir($dir);
			 $file = $dir.'/report'.$year.'_'.$mon.'_'.$mday.'_'.$shopID.'.txt';
          
			
			
				if(file_exists($file))
				{
						$handle = fopen($file,'r');
						$contents = '';
					while(!feof($handle))
					{$contents .= fgets($handle);}
					fclose($handle);	
                   
					$data = json_decode($contents,true);
					if($return)return $data;
					else 
                    {
                        $this->load->view('report',$data);	
                      
				                return    ;
                    }
				}
				else 
                {
                        if(strtotime(date("Y-m-d H:i:s")) >= strtotime($year.'-'.$mon.'-'.$mday.' 23:00:00'))  $createFile = true;

                  	if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'createOK'));
                    
                  
                    
                    
                }
			
			
			
		}
		
		$_POST['date'] = $data['date'];
        if(!isset($_POST['shopID'])) $_POST['shopID']  = $this->data['shopID'];
		$data['web'] =$this->web_shop_order(1);
      	if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'2'));
      
		$cashRegister = $this->Accounting_model->getRegisterByDay($data['date'],$shopID);
			$outputItems = $this->Accounting_model->getOutputItems();
		
		$data['withdraw'] = 0;
		$data['into'] = 0;
		$data['sales'] = 0;
		$data['registerOUT'] = 0 ;
		
		if(isset( $cashRegister[0]))
		$data['lastRegisterRemain'] = $cashRegister[0]['remain']-$cashRegister[0]['MIN']+$cashRegister[0]['MOUT'];
		else $data['lastRegisterRemain']  = 0;
		if($data['lastRegisterRemain']  == 0) $data['lastRegisterRemain'] = $this->Accounting_model->getLastRegisterRemain($data['date'],$shopID); 
		$data['registerRemain'] = $data['lastRegisterRemain'];
		
		$data['cashTotal'] = 0; 
		$data['verifyKey'] = true;
        $data['creditTotal'] = 0;
        
        for($i=1;$i<=4;$i++)
        {
              $data['credit'][$i]['total'] =  0 ;
              $data['credit'][$i]['name'] = $this->Accounting_model->getCreditName($i);
             $credit[$i] = 0;
        }
  
        
        
        
        $checkID = 0;
		foreach( $cashRegister as $row)
		{
            if($row['checkID']==$checkID)
            {
                $this->db->where('id',$row['id']);
                $this->db->delete('pos_cash_register');
                continue;
                
            }
            else $checkID = $row['checkID'];
            //重覆資料 校正
            
			$data['registerRemain'] = $row['remain'];
            $row['account'] = $row['aid'];
              if(!isset($data['accountSplit'][$row['account']]))
            {
                    $data['accountSplit'][$row['account']] =
                      array(
						'account' => $row['account'],
						'MIN'    => 0,
						'MOUT'    =>0,
						'withdraw'   => 0,
                        'credit'  => $credit    
					);  
                
                
            }
			switch($row['cashType'])
			{
				case 2	:
					$data['withdraw'] += $row['MOUT'];
					$data['withdrawRecord'][] =array(
						'account' => $row['aid'],
						'time'    => $row['time'],
						'note'    => $row['note'],
						'price'   => $row['MOUT']
					);
                      $data['accountSplit'][$row['account']]['withdraw']+= $row['MOUT'];     
				break;		
				case 4:
					$data['into'] += $row['MIN'] ;
					$data['cashTotal'] += $row['MIN']; 
                    $data['accountSplit'][$row['account']]['MIN']+= $row['MIN'];
				break;				
				case 1:
					$data['sales'] += $row['MIN'] ;
					$data['cashTotal'] += $row['MIN'];
                  
					
                    $data['creditTotal'] += $row['credit'] ;
                    $data['accountSplit'][$row['account']]['MIN']+= $row['MIN'];
                      if($row['creditType']!=0)              
                    $data['accountSplit'][$row['account']]['credit'][$row['creditType']]+= $row['credit'];
                    if($row['creditType']!=0)  $data['credit'][$row['creditType']]['total']+=   $row['credit'] ; 
                    else 
                    {
                        if(!isset($data['credit'][0]['total']))$data['credit'][0]['total'] = 0; 
                       $data['credit'][0]['total']+=   $row['credit'];
                    }
                    
                    
                    
				break;	
				default:
					if($row['outputItem']>0)
					{
						foreach($outputItems as $col)
						{
							if($row['outputItem']==$col['id'])	$itemName = $col['name'];
							
						}	
						
					}
					else $itemName = '';
                    $data['accountSplit'][$row['account']]['MOUT']+= $row['MOUT'];
					$data['registerOUT'] += $row['MOUT'];
					$data['OUTrecord'][] =array(
						'account' => $row['aid'],
						'time'    => $row['time'],
						'note'    => $row['note'],
						'price'   => $row['MOUT'],
						'item'    => $itemName
					);
				break;					
				
			}
				 
				
		}
		
		
		
		$consumption = $this->Accounting_model->getConsumption();
		
		foreach($consumption as $row)
		{
				$item[$row['typeID']]['name'] =$row['name'];
				$item[$row['typeID']]['count'] = 0;
			
		}
		
		$data['total']  = 0 ;
		$data['verify'] = 0;
		$data['monVerify'] = 0;
		
		$data['bonusChangeData'] = $this->Member_model->bonusChangeRecord(0,$data['date'],$data['date'] ,$shopID,true);
		if($testKey) $this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'3'));
		$data['record'] = $this->Accounting_model->getDayReport($data['date'],$shopID);
   		if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'3.1'));
       
		$data['backRecord'] = $this->Accounting_model->getBackProduct($data['date'],$shopID);
		$data['secondHandRecord'] = $this->Accounting_model->getSecondHandSell($data['date'],$shopID);
		$data['consumeRecord'] = $this->Accounting_model->getConsumeRecord($data['date'],$shopID);
		$data['backAmount']  = 0 ;
		foreach($data['backRecord'] as $row)
		{
				$data['backAmount'] += $row['sellPrice']*$row['sellNum'];
		}
		
		
		$i=0;
		if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'3.2'));
        $sellID = 0;
		foreach($data['record'] as $row)
		{
           
             if($row['sellID']==$sellID)
            {
                $this->db->where('id',$row['posSellID']);
                $this->db->delete('pos_product_sell');
                  unset( $data['record'][$i]);
                 $i++;
                 continue;
                
            }
            else $sellID = $row['sellID'];
            //重覆資料 校正
            
            /*三國殺閃卡判定*/
            
            if($saveResult &&$row['productID']>=8886974 && $row['productID']<=8886978) $p['product'][] = $row; 
            
            /*=====*/
            
			$data['total'] += $row['sellPrice']*$row['sellNum'];
           // $data['record'][$i]['rank'] = $this->Accounting_model->getRank($row['checkID'] ,$shopID);
			if(isset($item[$row['type']]))	$item[$row['type']]['count']+=$row['sellPrice']*$row['sellNum'];
			else $item[5]['count']+=$row['sellPrice']*$row['sellNum'];			
			if($data['verifyKey'])
			{
				$data['verify'] += round($row['sellNum']*($row['sellPrice']-round($row['purchasePrice'])));
				
			}
			
			//租借檢查
		
            $data['record'][$i]['rent']  =  $this->Accounting_model->getRentData($row['productID'],$row['sellID'],$shopID);
			$i++;
			
		}
   		if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'3.3'));
		$data['secondAmount']  = 0 ;
		foreach($data['secondHandRecord'] as $row)
		{
				$data['secondAmount'] += $row['sellPrice'];
				if($data['verifyKey'])
				{
					$data['verify'] += ($row['sellPrice']-$row['cost']);
				}
				
		}
		if($data['secondAmount']>0)
		{
			$item[] = array('name'=>'二手商品','count'=>$data['secondAmount']);
			
		
			
		}
	
		$data['total'] +=$data['secondAmount'];
     	if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'4'));
			$data['monRecord'] = $this->Accounting_model->getMonReport($mon,$year,$shopID);
       if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'5'));
			$data['monSecondHand'] = $this->Accounting_model->monSecondHand($mon,$year,$shopID);
			$data['monBack'] = $this->Accounting_model->monBack($mon,$year,$shopID);
			$profit = $this->Accounting_model->caculateProfit($data['monRecord'],$data['monSecondHand'],$data['monBack']);
        
        $data['monVerify'] = $profit['monVerify'];
         $webData = $this->cs_order_model->getFinishWebShopOrder($shopID,$year.'-'.$mon.'-0');
    
       $data['monVerify']+= $webData['wbFee']+$webData['wbOrderHomeProfit'];
        //
        
        ///三國殺閃卡自動下單
        
         if(isset($p['product']) && count($p['product'])>0 && $shopID!=666)
        {
             $destinationShopID = $shopID;
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
               $sellPrice =  0;
                $sellNum = $row['sellNum'];

                
				//出貨品項
				 //導入order
                 
					$orderDetaildatain = array(
						'orderID'=>$newOrderID,
						'sellPrice' =>$sellPrice ,
						'buyNum' => $row['sellNum'],
						'sellNum' => $sellNum,
						'productID' => 8882647,
						'comment' => '閃卡:'.$row['ENGName']
					);
					$this->db->insert('pos_order_detail',$orderDetaildatain); 
					$total += $sellPrice  *$row['sellNum'];
			
				 //===========
            }    
    
            $this->db->where('id',$newOrderID)	;
			$this->db->update('pos_order',array('total'=>$total));
             
        
          
        //=====出貨單設定完成
        }
        
        
        
			$data['monTotal'] = $profit['monTotal'];
	
				$data['shopID'] =$this->data['shopID'];
        //商城收入補上 直接加入紅利 因為成本已經計算過了
        $data['verify'] +=   $data['web']['wbFee'] +$item[10]['count']+ $data['web']['wbOrderHomeProfit'] ;
        //
       
		$data['shopData'] = $this->System_model->getShopByID($shopID);
		$data['diff'] = 3846-$data['verify'];
		$data['monDiff'] = 100000-$data['monVerify'];
		$data['level']  = $this->data['level'];
		//$data['registerRemain'] += $data['total'] ;
		$data['item'] = $item;
		
		if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'$createFile'.$createFile));
		
		if($createFile==true) 
		{
			$output = json_encode($data);
				$f = fopen($file,'w');
			fprintf($f,"%s",$output);
					fclose($f);		
			
			if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'filesave'.$file));
			
		}
		
	if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'6'));
		if($return)return $data;
		else $this->load->view('report',$data);	
		
		
	}
	function get_each_day_report()
	{
		$testKey = false;
		$this->load->model('System_model');
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		$data['shopList'] = $this->System_model->getShop(true);	
		$input = $_POST;
			if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'dr1'));
		foreach($data['shopList'] as $row)
		{
			$input['shopID'] = $row['shopID'];
			$row['report'] = $this->get_day_report(true,$input,true)	;
			$result[] = $row;
			
		}
		
		if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'dr2'));
		
		$consumption = $this->Accounting_model->getConsumption();
		$itemIndex = 1;
		foreach($consumption as $row)
		{
			
				$item[$row['typeID']]['name'] =$row['name'];
				$item[$row['typeID']]['count'] = 0;
			$itemIndex++;
		}
			if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'dr3'));
		$item[$itemIndex++] =array('name' =>'二手商品','count' => 0);
		$item[$itemIndex++] =array('name' =>'退貨商品','count' => 0);
		$outPut['result'] = $result;
		$outPut['item'] = $item;
		$this->load->view('each_day_report',$outPut);	
	}
	function mon_report_data()
	{
	   $year = $this->input->post('year');
		$mon = $this->input->post('mon');
		$day = $this->input->post('day');	
		if($mon==1||$mon==3||$mon==5||$mon==7||$mon==8||$mon==10||$mon==12)
		{
			$data['mday'] = 31;		
		}
		else if($mon==2)
		{
			if($year%4==0)	$data['mday'] = 29;	
			else $data['mday'] = 28;	
			
		}
		else 	$data['mday'] = 30;		
		
		$data['date'] =$year.'-'.$mon;
		$date = $year.'-'.$mon.'-'.$data['mday'];
		$firstDay = getdate(mktime(0,0,0,$mon,1,$year));
		$data['firstWeekDay'] = $firstDay['wday'];
		
		if($this->data['shopID']==0)$shopID =  $this->input->post('shopID');
		else $shopID = $this->data['shopID'];
         
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		$this->load->model('Member_model');
		$data['shopInf'] = $this->System_model->getShopByID($shopID);
    
		if(empty($data['shopInf']))
        {
            $data['exist'] =false;
          
            return $data;
        }
        else $data['exist'] =true;
          
        
        
		$consumption = $this->Accounting_model->getConsumption();
		$itemIndex = 1 ; 
		foreach($consumption as $row)
		{
			
				$item[$row['typeID']]['name'] =$row['name'];
				$item[$row['typeID']]['count'] = 0;
                $item[$row['typeID']]['profit'] = 0;
			 $itemIndex++;
		}
		$item[$itemIndex] =array('name' =>'二手商品','count' => 0);
		$item[$itemIndex+1] =array('name' =>'退貨商品','count' => 0);

		
		$data['total']  = 0 ;
		$data['verifyKey'] = true;
		
        
        	$_POST['date'] = $data['date'].'-0';
		$data['web'] =$this->web_shop_order(1);
        
        
		//$data['record'] = $this->Accounting_model->getDayReport($data['date'],$shopID);
		$data['record'] = $this->Accounting_model->getMonReport($mon,$year,$shopID);
		$data['monSecondHand'] = $this->Accounting_model->monSecondHand($mon,$year,$shopID);
		$data['bonusChangeData'] = $this->Member_model->bonusChangeRecord(0,$year.'-'.$mon.'-1',$date ,$shopID,true);


		$data['monBack'] = $this->Accounting_model->monBack($mon,$year,$shopID);
		$data['monWithdraw'] =  $this->Accounting_model->getMonWithdraw($mon,$year,$shopID);
		$data['monExpenses'] =  $this->Accounting_model->getMonExpenses($mon,$year,$shopID);
		
		
		$data['verify'] = 0 ;
		$data['monVerify'] = 0 ;
		$data['place'] =0;

		
		$i = 0 ;$checkID = 0; $t = 0;
		foreach($data['record'] as $row)
		{
            if($row['checkID']!=$checkID)
            {
                $t++;
                $checkID = $row['checkID'];
                
            }
			if(!isset( $data['record'][$i]['rank'] ))             $data['record'][$i++]['rank'] ='';
			$data['total'] += $row['sellPrice']*$row['sellNum'];
			if(isset($item[$row['type']]))
            {
                $item[$row['type']]['count']+=$row['sellPrice']*$row['sellNum'];
                $item[$row['type']]['profit']+=round($row['sellNum']*($row['sellPrice']-$row['purchasePrice']));
                
            }
			else
            {
                $item[5]['count']+=$row['sellPrice']*$row['sellNum'];
                $item[5]['profit']+=round($row['sellNum']*($row['sellPrice']-$row['purchasePrice']));
            }
			
			$date = substr($row['time'],8,2);
			$data['recordOut']['p_'.$row['productID']]['ZHName'] = $row['ZHName'];
			if(isset($row['best30']))
			$data['recordOut']['p_'.$row['productID']]['best30'] = $row['best30'];
			else 	$data['recordOut']['p_'.$row['productID']]['best30']  = 0;
			$data['recordOut']['p_'.$row['productID']]['ENGName'] = $row['ENGName'];
			$data['recordOut']['p_'.$row['productID']]['productID'] = $row['productID'];

			if(!isset($data['recordOut']['p_'.$row['productID']][(int)$date]))$data['recordOut']['p_'.$row['productID']][(int)$date] = 0;
			$data['recordOut']['p_'.$row['productID']][(int)$date] +=$row['sellNum'];			
			if(!isset($data['recordOut']['p_'.$row['productID']]['totalNum']))$data['recordOut']['p_'.$row['productID']]['totalNum'] =0;
			$data['recordOut']['p_'.$row['productID']]['totalNum']+=  $row['sellNum'];
			
			
			if($data['verifyKey'])
			{
				$data['monVerify'] +=  round($row['sellNum']*($row['sellPrice']-$row['purchasePrice']));
				if($row['type']==3)
				{
					$data['verify'] +=$row['sellPrice']*$row['sellNum'];
				
					
										
				}
				
				else if($row['type'] == 6) 
				{
					if($row['barcode']!='8880012'&&$row['barcode']!='8880013')	;
					else $data['place']+=$row['sellPrice']*$row['sellNum'];round($row['sellNum']*($row['sellPrice']-$row['purchasePrice']));
				}
				else if($row['type'] == 2||$row['type'] == 4)
				{
					$data['place']+=round($row['sellNum']*($row['sellPrice']-$row['purchasePrice']));
					
				}	
			
				
			}

	
			
		}
        $data['cashNum'] =  $t ;
		if(!empty($data['monSecondHand']))
		{
		
			//$item[9] =array('name' =>'二手商品','count' => 0);
		
             $item[$itemIndex]['profit']  = 0;
			foreach($data['monSecondHand'] as $row)
			{
					if(isset($row))
					{
						$row['num'] = 1;
					$item[$itemIndex]['count'] +=$row['num']*$row['sellPrice'];
                    $item[$itemIndex]['profit'] -=round($row['num']*$row['sellPrice']-$row['cost']);    
					$data['monVerify']+=round($row['num']*($row['sellPrice']-$row['cost']));
					$data['total'] += $row['num']*$row['sellPrice'];
				
					}
			
			}
		}
		
		
		if(!empty($data['monBack'] ))
		{
//				$item[10] =array('name' =>'退貨商品','count' => 0);
              $item[$itemIndex+1]['profit']  = 0;
				foreach($data['monBack'] as $row)
			{
					if(isset($row['num']))
					{
					$item[$itemIndex+1]['count'] -=$row['num']*$row['sellPrice'];
                    $item[$itemIndex+1]['profit'] -=round($row['num']*$row['sellPrice']-$row['cost']);
					$data['monVerify'] -=round($row['num']*$row['sellPrice']-$row['cost']);
					$data['total'] -= $row['num']*$row['sellPrice'];
					}
			}
		}
	
         $webData = $this->cs_order_model->getFinishWebShopOrder($shopID,$year.'-'.$mon.'-0');
    
       $data['monVerify']+= $webData['wbFee']+$webData['wbOrderHomeProfit'];
        $item[1]['profit']+= $webData['wbFee']+$webData['wbOrderHomeProfit'];
        //
        
		$i=0;
		$data['shopID'] =$this->data['shopID'];
		$data['assignShopID'] = $shopID;
		$data['diff'] = 3846-$data['verify'];
		$data['monDiff'] = 100000-$data['monVerify'];
		$data['credit'] = $this->Accounting_model->getCredit($mon,$year,$shopID);
		//$data['registerRemain'] += $data['total'] ;
		$data['item'] = $item;
		return $data;	
		
		
	}
	
	
    function member_bonus_notify()
    {
    
        $this->load->model('Member_model');
        $shopID = $this->input->post('shopID');
       
     
        $time = getdate();	
		$year = $time['year'];
		$month = $time['mon'];
		$day = $time['mday'];
        $data['date'] =$year.'-'.date("m",mktime(0,0,0,$month,0,$year));
        $data['bonusChangeData'] = $this->Member_model->bonusChangeRecord(0, date("Y-m-d",mktime(0,0,0,$month-1,1,$year)),date("Y-m-d",mktime(0,0,0,$month,0,$year)) ,$shopID,true);
        if(empty($data['bonusChangeData']))exit(1);
        $this->load->view('mon_bonus_report',$data);
       
    }
	
	function get_mon_report()
	{
		
		$this->load->view('mon_report',$this->mon_report_data());	
	}
	
		
	function get_mon_report_data()
	{
	
		$data = $this->mon_report_data();
        if($data['exist'])	$data['shopItem'] = $data['item'];
		
        $_POST['year']--;
        $data['lastyear'] =  $this->mon_report_data();
        
        
        
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	function get_mon_report_by_day()
	{
	
		$year = $this->input->post('year');
		$mon = $this->input->post('mon');
		$day = $this->input->post('day');	
		if($mon==1||$mon==3||$mon==5||$mon==7||$mon==8||$mon==10||$mon==12)
		{
			$data['mday'] = 31;		
		}
		else if($mon==2)
		{
			if($year%4==0)	$data['mday'] = 29;	
			else $data['mday'] = 28;	
			
		}
		else 	$data['mday'] = 30;		
		
		$data['date'] =$year.'-'.$mon;
		$date = $year.'-'.$mon.'-'.$data['mday'];
		$firstDay = getdate(mktime(0,0,0,$mon,1,$year));
		$data['firstWeekDay'] = $firstDay['wday'];
			
		if($this->data['shopID']==0)$shopID =  $this->input->post('shopID');
		else $shopID = $this->data['shopID'];
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
			$data['shopInf'] = $this->System_model->getShopByID($shopID);
		$consumption = $this->Accounting_model->getConsumption();
		
        $itemIndex = 1 ;
        foreach($consumption as $row)
		{
		
				$item[$row['typeID']]['name'] =$row['name'];
				$item[$row['typeID']]['count'] = 0 ;
				 $itemIndex++; 
			
		}
		
		$data['total']  = 0 ;
		$data['verifyKey'] = true;
		
		//$data['record'] = $this->Accounting_model->getDayReport($data['date'],$shopID);
		$data['record'] = $this->Accounting_model->getMonReport($mon,$year,$shopID);
		$data['monSecondHand'] = $this->Accounting_model->monSecondHand($mon,$year,$shopID);
		$data['monBack'] = $this->Accounting_model->monBack($mon,$year,$shopID);
        $data['monCashFlow'] = $this->Accounting_model->getMonCashFlow($mon,$year,$shopID);
		$data['monExpenses'] = $this->Accounting_model->getMonExpenses($mon,$year,$shopID);
		
		$data['verify'] = 0 ;
		$data['monVerify'] = 0 ;
		$data['place'] =0;
		$data['profit'] = 0;
		$secondToken = false;
		$backToken = false;
		if(!empty($data['monSecondHand'] ))	$secondToken = true;
		if(!empty($data['monBack'] ))	$backToken =true;
		
		for($i=1;$i<=31;$i++)
		{
			if(!isset($class[$i]['data']))
			{
				$sellDay = str_pad($i,2,0,STR_PAD_LEFT);
				foreach($consumption as $cos) $class[$sellDay]['data'][$cos['typeID']]['count'] = 0 ;
				if($secondToken)$class[$sellDay]['data'][$itemIndex]['count'] = 0 ;
				if($backToken)$class[$sellDay]['data'][$itemIndex+1]['count'] = 0 ;
				if(!isset($class[$sellDay]['data']['profit']['count'])) $class[$sellDay]['data']['profit']['count'] = 0;
				$class[$sellDay]['total'] = 0;
			}	
			
			
		}
		
        $checkID = 0 ; $t = 0 ;
		foreach($data['record'] as $row)
		{
		
			$data['total'] += $row['sellPrice']*$row['sellNum'];

			$sellDay = substr($row['time'],8,2);
			
			
			
			
			$class[$sellDay]['total'] +=$row['sellPrice']*$row['sellNum'];
			if(isset($item[$row['type']]))	
			{
				if(!isset($class[$sellDay]['data'][$row['type']]['count'])) $class[$sellDay]['data'][$row['type']]['count'] = 0;
				$class[$sellDay]['data'][$row['type']]['count']+=$row['sellPrice']*$row['sellNum'];
				$item[$row['type']]['count']+=$row['sellPrice']*$row['sellNum'];
				
			}
			else 
			{
			
					$class[$sellDay]['data'][5]['count']+=$row['sellPrice']*$row['sellNum'];
					$item[5]['count']+=$row['sellPrice']*$row['sellNum'];
				
			}
			
           
            
            
			
			$class[$sellDay]['data']['profit']['count'] +=  round($row['sellNum']*($row['sellPrice']-$row['purchasePrice']));
			$data['profit'] += round($row['sellNum']*($row['sellPrice']-$row['purchasePrice']));
				
		}
		$data['cashNum'] = $t  ;
		if(!empty($data['monSecondHand'] ))
		{
			$item[ $itemIndex] =array('name' =>'二手商品',
			               'count' => 0);
	
			foreach($data['monSecondHand'] as $row)
			{
					$sellDay = substr($row['time'],8,2);
					if(!isset($class[$sellDay]['data'][ $itemIndex]['count'])) $class[$sellDay]['data'][$itemIndex]['count']=0;
					$class[$sellDay]['data'][ $itemIndex]['count'] +=$row['sellPrice'];
					$item[$itemIndex]['count']+=$row['sellPrice'];
					$data['monVerify']+=round(($row['sellPrice']-$row['cost']));
					$data['total'] += $row['sellPrice'];
					$class[$sellDay]['total'] +=$row['sellPrice'];
					$class[$sellDay]['data']['profit']['count'] +=round(($row['sellPrice']-$row['cost']));
					$data['profit'] += round(($row['sellPrice']-$row['cost']));

			}
             
		}
		
		
		if(!empty($data['monBack'] ))
		{
			$item[$itemIndex+1] =array('name' =>'退貨商品',
			               'count' => 0);
			foreach($data['monBack'] as $row)
			{
					$sellDay = substr($row['backTime'],8,2);
					if(!isset($class[$sellDay]['data'][$itemIndex+1]['count'])) $class[$sellDay]['data'][$itemIndex+1]['count']=0;
					$class[$sellDay]['data'][$itemIndex+1]['count'] -=$row['sellPrice']*$row['num'];
					$item[$itemIndex+1]['count'] -=$row['sellPrice']*$row['num'];
					$data['monVerify'] -=round($row['sellPrice']-$row['cost'])*$row['num'];
					$data['total'] -= $row['sellPrice']*$row['num'];
					$class[$sellDay]['total'] -=$row['sellPrice']*$row['num'];
					$class[$sellDay]['data']['profit']['count'] -=round(($row['sellPrice']-$row['cost'])*$row['num']);
					$data['profit'] -=round(($row['sellPrice']-$row['cost'])*$row['num']);
		
			}
		}
		
        
 
        
        foreach($data['monCashFlow'] as $row)
		{
		 	 $sellDay = substr($row['time'],8,2);
			if(!isset($class[$sellDay]['multiPay'][$row['creditType']]))
				$class[$sellDay]['multiPay'][$row['creditType']] = 0 ; 

            if(!isset( $data['multiPayTotal'][$row['creditType']]))
                     $data['multiPayTotal'][$row['creditType']] = 0;
            
			$class[$sellDay]['multiPay'][$row['creditType']]  += $row['credit'];
			 $data['multiPayTotal'][$row['creditType']] += $row['credit'];
				
            
		
			
			
		}
        
		$data['monExpensesTotal'] = 0;
		
		
		foreach($data['monExpenses'] as $row)
		{
		 	 			$sellDay = substr($row['time'],8,2);
			if(!isset($class[$sellDay]['expenses']))
			{
				$class[$sellDay]['expenses']=$row['MOUT'];
				$class[$sellDay]['comment']=$row['note'];
				$data['monExpensesTotal'] +=$row['MOUT'];
			}
			else 
			{
				$class[$sellDay]['comment'].=','.$row['note'];
				$class[$sellDay]['expenses']+=$row['MOUT'];
				$data['monExpensesTotal'] +=$row['MOUT'];
			}
			
		}
		$data['monWithdraw'] =  $this->Accounting_model->getMonWithdraw($mon,$year,$shopID);
		$i=0;
		$data['shopID'] =$this->data['shopID'];
		$data['assignShopID'] = $shopID;
		$data['item'] = $item;
		$data['class'] = $class;
		$this->load->view('mon_report_by_day',$data);	
	}
		
	function get_month_verify()
	{
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
        $this->load->model('cs_order_model');
		$shopID = $this->input->post('shopID');
		$mon = $this->input->post('mon');
		$year = $this->input->post('year');
	
		$data['monRecord'] = $this->Accounting_model->getMonReport($mon,$year ,$shopID);
		$data['monVerify'] = 0 ;
		$data['monTotal'] = 0;
            $checkID = 0 ;$t = 0 ;
		foreach($data['monRecord'] as $row)
		{
            if($checkID!=$row['checkID']) 
            {
                $checkID = $row['checkID'];
                $t++;
                
            }
            
                $data['monTotal'] += round($row['sellNum']*($row['sellPrice']));
				$data['monVerify']+=round($row['sellNum']*($row['sellPrice']-$row['purchasePrice']));
		}
        $data['cashNum'] = $t;
        $webData = $this->cs_order_model->getFinishWebShopOrder($shopID,$year.'-'.$mon.'-0');
    
       $data['monVerify']+= $webData['wbFee']+$webData['wbOrderHomeProfit'];
        //
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	function get_detail_report()
	{
		
		
		$year = $this->input->post('year');
		$month = $this->input->post('mon');
		$day = $this->input->post('day');
		
		if($this->data['shopID']==0)$shopID =  $this->input->post('shopID');
		else $shopID = $this->data['shopID'];
	
	
	
		$data = $this->detail_data($year,$month,$shopID);
        $data['shopID'] = $shopID; 
		$data['excel']= $this->input->post('excel');
		//$this->load->view('detail_report',$data);
		$this->load->view('detail_report_excel',$data);
		
		
	}
	function detail_data($year,$month,$shopID)
	{
			
		$date = getdate(); 
		$creareFile  = false;
		if($year<$date['year']||($year==$date['year']&&$month<$date['mon']))
		{
		 $file = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/detail_report/detail_report_'.$year.'_'.$month.'_'.$shopID.'.txt';
				
			if(file_exists($file))
			{
					$handle = fopen($file,'r');
					$contents = '';
				while(!feof($handle))
				{$contents .= fgets($handle);}
				fclose($handle);	
//		
			$data = json_decode($contents,true);
				
				return $data;
				
			}
			else $creareFile = true;
		}
		
		
		
		$this->load->model('Order_model');
		$this->load->model('Product_model');
		$this->load->model('Accounting_model');	
		$data['product'] = $this->Order_model->getMonthCheck($shopID,$year,$month,0);	
		 $monthTotal = 0;
		 foreach($data['product'] as $row)
		{
			  $subtotal = $row['sellNum']*$row['sellPrice'];
              $monthTotal += $subtotal;	 
		}
		$data['monthTotal']  = $monthTotal;
	
		
		
	
		
		$data['consigmentProduct'] = $this->Order_model->getConsignmentMonthCheck($shopID,$year,$month,0);
		$consigmentTotal = 0;	
		if(!empty($data['consigmentProduct']))
		foreach($data['consigmentProduct'] as $row)
		{
				
				$row['totalNum'] = $row['consignmentNum'] - $row['remainNum'] ;
				$subtotal = $row['totalNum']*$row['purchasePrice'];
				$consigmentTotal += $subtotal;	
		}
		$data['consigmentTotal'] = $consigmentTotal;
		
		
		 $data['otherProduct'] =  $this->Order_model->getOtherProduct($year,$month,$shopID);
		$otherTotal = 0;	
		$i=0;
		if(!empty($data['otherProduct']))
		foreach($data['otherProduct'] as $row)
		{
				
				
				if(empty($row['purchasePrice'])||$row['purchasePrice']==0)
				{
					$row['purchasePrice']= round($row['purchaseCount']*$row['price']/100);
					$data['otherProduct'][$i]['purchasePrice'] = $row['purchasePrice'];
				}
				$subtotal = $row['purchaseNum']*$row['purchasePrice'];
				$otherTotal += $subtotal;	
				$i++;
		}
		$data['otherTotal'] = $otherTotal;
		
		
		$data['backProduct'] = $this->Order_model->getBackOrderMonthCheck($shopID,$year,$month);
		$backTotal = 0;
		   foreach($data['backProduct']  as $row)
			{
					 if($row['isConsignment'])$subtotal = 0;
					 else
					 {
						$subtotal = $row['totalNum']*$row['purchasePrice'];
					 }
				  $backTotal += $subtotal;	
			}
		$data['backTotal'] = $backTotal;
	
		$data['adjustProduct'] = $this->Order_model->getAdjustOrderMonthCheck($shopID,$year,$month);
		$adjustTotal = 0 ;
		
		 foreach($data['adjustProduct'] as $row)
                {
                         if($row['isConsignment'])$subtotal = 0;
                         else
                         {
							 if(isset($row['purchaseCount']))$subtotal = $row['totalNum']*round($row['price']*$row['purchaseCount']/100);
							 else $subtotal = $row['totalNum']*round($row['purchasePrice']);
                         }
                      $adjustTotal += $subtotal;	
                }
		
		$data['adjustTotal'] = $adjustTotal;
		
		
		
		$data['record'] = $this->Accounting_model->getMonReport($month,$year,$shopID);
		$data['monSecondHand'] = $this->Accounting_model->monSecondHand($month,$year,$shopID);
		$data['monBack'] = $this->Accounting_model->monBack($month,$year,$shopID);

		$data['sellTotal'] =0;
		foreach($data['record'] as $row)
		{
			$data['sellTotal'] += $row['sellPrice']*$row['sellNum'];
					
			$date = substr($row['time'],8,2);
			$data['recordOut']['p_'.$row['productID']]['ZHName'] = $row['ZHName'];
			$data['recordOut']['p_'.$row['productID']]['productNum'] = $row['productNum'];
			$data['recordOut']['p_'.$row['productID']]['ENGName'] = $row['ENGName'];
			$data['recordOut']['p_'.$row['productID']]['productID'] = $row['productID'];

			if(!isset($data['recordOut']['p_'.$row['productID']]['subtotal']))$data['recordOut']['p_'.$row['productID']]['subtotal'] = 0;
			$data['recordOut']['p_'.$row['productID']]['subtotal'] +=$row['sellNum']*$row['sellPrice'];			
			
			if(!isset($data['recordOut']['p_'.$row['productID']]['totalPurchase']))$data['recordOut']['p_'.$row['productID']]['totalPurchase'] =0;
			$data['recordOut']['p_'.$row['productID']]['totalPurchase']+=  $row['purchasePrice'] * $row['sellNum'];
			
			if(!isset($data['recordOut']['p_'.$row['productID']]['totalNum']))$data['recordOut']['p_'.$row['productID']]['totalNum'] =0;
			$data['recordOut']['p_'.$row['productID']]['totalNum']+=  $row['sellNum'];
			
			if(!isset($data['recordOut']['p_'.$row['productID']]['profit']))$data['recordOut']['p_'.$row['productID']]['profit'] = 0;
			$data['recordOut']['p_'.$row['productID']]['profit'] +=$row['sellNum'] *($row['sellPrice']-$row['purchasePrice']);			
			
		}
		$data['secondTotal']  = 0;
		if(!empty($data['monSecondHand'] ))
		{
			
				
			foreach($data['monSecondHand'] as $row)
			{
					
					
					$data['secondTotal'] += $row['sellPrice'];
			
			}
		}
	$data['sellBackTotal'] = 0;
		if(!empty($data['monBack'] ))
		{
			
		   
			foreach($data['monBack'] as $row)
			{
					
					$data['sellBackTotal'] -= $row['sellPrice'];
		
			}
		}
	
		if($month>1)
		{
			 $lastMonth = $month-1;
			 $lastyear = $year;
		}
		else
		{
			 $lastyear = $year-1;
			 $lastMonth= 12;
		}
		
		$data['last']= $lastyear.'-'.$lastMonth;
		$data['lastStock'] = $this->Product_model->getProductStock(array('time'=>$data['last'],'inStock'=>1,'withOutConsignment'=>true),$shopID);
		
		$data['date']= $year.'-'.$month;
		$data['stock'] = $this->Product_model->getProductStock(array('time'=>$data['date'],'inStock'=>1,'withOutConsignment'=>true),$shopID);
		
		$data['lastConsignment'] = $this->Order_model->getConsignment($shopID,$lastyear,$lastMonth);
		$data['consignment'] = $this->Order_model->getConsignment($shopID,$year,$month);

		$data['newInshop'] = $this->Product_model->getNewInshop($shopID,$year,$month);
		$data['inShopData'] = $this->Product_model->getInshopData($shopID,$year,$month);
		
				
		
		$data['verify'] = 0 ;

		$data['monExpenses'] = $this->Accounting_model->getMonExpenses($month,$year,$shopID);
		$data['year'] = $year;
		$data['month'] = $month;
		
		
		$data['lastProduct'] = $this->Order_model->getMonthCheck($shopID,$lastyear,$lastMonth,0);	
		$monthTotal = 0;
		
		 foreach($data['lastProduct'] as $row)
		{
			
			 if(substr($row['shippingTime'],5,2)!=substr($row['arriveTime'],5,2))  $data['lastUnArriveData'] []=$row;

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
	
	function stock_preload()
	{
		set_time_limit(0);
		ini_set('max_execution_time', 0);;
		$month = $this->input->post('mon');
		$year = $this->input->post('year');
		$shopID =  $this->input->post('shopID');
		$this->load->model('Order_model');
		$this->load->model('Product_model');
		$this->load->model('Accounting_model');
		
		if($month==0)
		{
			 $year = $year-1;
			 $month= 12;
		}
		
		$shopID = 22;
		$month =1;
		$year=2015;
		
		$stock= $this->Product_model->getProductStock(array('time'=>$year.'-'.$month,'inStock'=>1,'withOutConsignment'=>true),$shopID);
		$data['result']	 = true;
			echo json_encode($data);
			exit(1);
		
	}
	
	
	
	function get_chart()
	{
		
		$this->load->helper('chart');
	set_time_limit(0);
		ini_set('max_execution_time', 0);;
					
		$date = $this->uri->segment(3);
		
		$dateList = explode('-',$date);
		$year = $dateList[0];
		$mon = $dateList[1];

		if($mon==1||$mon==3||$mon==5||$mon==7||$mon==8||$mon==10||$mon==12)
		{
			$data['mday'] = 31;		
		}
		else if($mon==2)
		{
			if($year%4==0)	$data['mday'] = 28;	
			else $data['mday'] = 29;	
			
		}
		else 	$data['mday'] = 30;		
		$firstDay = getdate(mktime(0,0,0,$mon,1,$year));
		$data['firstWeekDay'] = $firstDay['wday'];

		
		if($this->data['shopID']==0)$shopID =  $this->uri->segment(4);
		else $shopID = $this->data['shopID'];
		$this->load->model('Accounting_model');
		$consumption = $this->Accounting_model->getConsumption();
		
		foreach($consumption as $row)
		{
			
				$item[$row['typeID']]['name'] =$row['name'];
				$item[$row['typeID']]['count'] = 0;
			
		}
		$chart = new open_flash_chart();
		$title = new title( '消費模式分布圖' );
		$color = array('FF37FD','FF9D37','37FF9D','FDFF37','99FF37','37FF39','37FDFF','FF3937','3799FF','3937FF','9D37FF','FF3799');		
		$title->set_style( "{font-size: 20px; color: #A2ACBA; text-align: center;}" );
		$chart->set_title( $title );		
		$data['total']  = 0 ;
		$data['verifyKey'] = true;
		
		$date = $date.'-'.$data['mday'];
		//$data['record'] = $this->Accounting_model->getDayReport($data['date'],$shopID);
		$data['record'] = $this->Accounting_model->getMonReport($mon,$year,$shopID);
		$data['verify'] = 0 ;
		$data['monVerify'] = 0 ;
		$data['place'] =0;
		for($i=1;$i<=8;$i++)
		{
			for($j=0;$j<=31;$j++)
			{
				$dayList[] = $j;
				$data['chartData'][$i][$j] = 0;
			}	
			
		}
		
		$maxValue = 0;
		foreach($data['record'] as $row)
		{
				$date = substr($row['time'],8,2);
				if($row['type']=='') $row['type']= 8;
				$data['chartData'][$row['type']][(int)$date] +=$row['sellPrice']*$row['sellNum'];
				if($data['chartData'][$row['type']][(int)$date]>$maxValue) $maxValue = $data['chartData'][$row['type']][(int)$date];
			
		}
		$i=1;
		
		foreach($consumption as $row)
		{
				$line = new line();
				$line->set_colour( '#'.$color[$i%12] );
				//$line->set_values( array(9,8,7,6,5,4,3,2,1) );
				//print_r($data['chartData'][ $i]);
				$line->set_values( $data['chartData'][ $row['typeID']]);
				$line->set_key( $row['name'], 12 );
				$chart->add_element( $line);
				$i++;
	
			
		}	
		
$x_labels = new x_axis_labels();
			$x_labels->set_steps( 1 );
			$x_labels->set_vertical();
			$x_labels->set_colour( '#A2ACBA' );
			$x_labels->set_labels( $dayList);
			
			$x = new x_axis();
			$x->set_colour( '#A2ACBA' );
			$x->set_grid_colour( '#D7E4A3' );
			$x->set_offset( false );
			$x->set_steps(1);
			// Add the X Axis Labels to the X Axis
			//$x->set_labels( $x_labels );
			
			$chart->set_x_axis( $x );
			
			//
			// LOOK:
			//
			$x_legend = new x_legend( $year.' / '.$mon );
			$x_legend->set_style( '{font-size: 20px; color: #778877}' );
			$chart->set_x_legend( $x_legend );
			
			//
			// remove this when the Y Axis is smarter
			//
			$y = new y_axis();
			
			if($maxValue>100) $offset = 20;
			else $offset = 10;
			$y->set_range( 0, $maxValue, $offset);
			$chart->add_y_axis( $y );		
			
		echo $chart->toPrettyString();
		/**/
	}
	function get_bar()
	{
		$this->load->helper('chart');
			set_time_limit(0);
		ini_set('max_execution_time', 0);;
					
		$date = $this->uri->segment(3);
		
		$dateList = explode('-',$date);
		$year = $dateList[0];
		$mon = $dateList[1];

		if($mon==1||$mon==3||$mon==5||$mon==7||$mon==8||$mon==10||$mon==12)
		{
			$data['mday'] = 31;		
		}
		else if($mon==2)
		{
			if($year%4==0)	$data['mday'] = 28;	
			else $data['mday'] = 29;	
			
		}
		else 	$data['mday'] = 30;		
		$firstDay = getdate(mktime(0,0,0,$mon,1,$year));
		$data['firstWeekDay'] = $firstDay['wday'];

		$date = $date.'-'.$data['mday'];
		if($this->data['shopID']==0)$shopID =  $this->uri->segment(4);
		else $shopID = $this->data['shopID'];
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		$consumption = $this->Accounting_model->getConsumption();
		
		foreach($consumption as $row)
		{
				$item[$row['typeID']]['name'] =$row['name'];
				$item[$row['typeID']]['count'] = 0;
			
		}
		$chart = new open_flash_chart();
		$title = new title( '消費模式分布圖' );
		$color = array('FF37FD','FF9D37','37FF9D','FDFF37','99FF37','37FF39','37FDFF','FF3937','3799FF','3937FF','9D37FF','FF3799');		
		$title->set_style( "{font-size: 20px; color: #A2ACBA; text-align: center;}" );
		$chart->set_title( $title );		
		$data['total']  = 0 ;
		$data['verifyKey'] = true;
		
		//$data['record'] = $this->Accounting_model->getDayReport($data['date'],$shopID);
		$data['record'] = $this->Accounting_model->getMonReport($mon,$year,$shopID);
		$data['verify'] = 0 ;
		$data['monVerify'] = 0 ;
		$data['place'] =0;
		for($i=1;$i<=8;$i++)
		{
			for($j=0;$j<=7;$j++)
			{
				$dayList[] = $j;
				$data['chartData'][$i][$j] = 0;
			}	
			
		}
		
		$maxValue = 0;
		foreach($data['record'] as $row)
		{
				$date = substr($row['time'],8,2);
				$wday =($data['firstWeekDay']+((int)$date - 1)%7)%7;
				if($row['type']=='') $row['type']= 8;
				$data['chartData'][$row['type']][$wday] +=$row['sellPrice']*$row['sellNum'];
				
				if($data['chartData'][$row['type']][$wday]>$maxValue) $maxValue = $data['chartData'][$row['type']][$wday];
		}
			
		$i=1;
		$chart = new open_flash_chart();
		$chart->set_title( $title );

		foreach($consumption as $row)
		{
			/*
				$line = new line();
				$line->set_colour( '#'.$color[$i%12] );
				//$line->set_values( array(9,8,7,6,5,4,3,2,1) );
				//print_r($data['chartData'][$i]);
				$line->set_values( $data['chartData'][ $i ]);
				$line->set_key( $row['name'], 12 );
				$chart->add_element( $line);
				$i++;
			*/
			$bar = new bar_glass();
			$data['chartData'][$row['typeID']][7] = $data['chartData'][$row['typeID']][0]; 
			$data['chartData'][$row['typeID']][0] = 0;
			$bar->set_values( $data['chartData'][ $row['typeID']]);
			$bar->set_key( $row['name'], 12 );
			$bar->colour( '#'.$color[$i++%12] );
			$chart->add_element( $bar );

		}	
		
			$x_labels = new x_axis_labels();
			$x_labels->set_steps( 1 );
			$x_labels->set_vertical();
			$x_labels->set_colour( '#A2ACBA' );
			
			
			$x = new x_axis();
			$x->set_colour( '#A2ACBA' );
			$x->set_grid_colour( '#D7E4A3' );
			$x->set_offset( false );
			$x->set_labels_from_array( array('','一','二','三','四','五','六','日'));
			$x->set_steps(1);
			// Add the X Axis Labels to the X Axis
			//$x->set_labels( $x_labels );
			
			$chart->set_x_axis( $x );
			
			//
			// LOOK:
			//
			$x_legend = new x_legend( $year.' / '.$mon );
			$x_legend->set_style( '{font-size: 20px; color: #778877}' );
			$chart->set_x_legend( $x_legend );
			
			//
			// remove this when the Y Axis is smarter
			//
			$y = new y_axis();
			
			if($maxValue>100) $offset = 20;
			else $offset = 10;
			$y->set_range( 0, $maxValue, $offset);
			$chart->add_y_axis( $y );			
			
			
	
	
	$bar = new bar_glass();
	$bar->set_values( array(8,2,3,4,5,6,7) );
	$bar->colour( '#'.$color[$i%12] );
	$chart->add_element( $bar );
							
	echo $chart->toString();		
		
		
	}
	
	function each_shop_report_send_mail()
	{
		set_time_limit(0);
		ini_set('max_execution_time', 0);;
	
		$mailTo = array('lintaitin@gmail.com','phoenickimo@gmail.com','phantasia.store@gmail.com');

		$url = 'http://shipment.phantasia.com.tw/accounting/get_each_day_report';
		$time = getdate();
		$result = '<html><link rel="stylesheet" type="text/css" href="http://shipment.phantasia.com.tw/style/pos.css">';
		
		$result =  $this->paser->post($url,array('year'=>$time['year'],'mon'=>$time['mon'],'mday'=>$time['mday']),false);
		
		$result.='</html>';
		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
		//$this->db->reconnect();  
	
		
		
		$postData = array(
			'code' => md5('phaMail'),
			'mail' =>json_encode($mailTo,true),
			'title'=>$time['year'].'/'.$time['mon'].'/'.$time['mday'].'營業報表(各店總表)',
			'content'=>$result,
			'headers'=>$headers
			
		);
		
		
		;
		$this->paser->post('http://shipment.phantasia.com.tw/welcome/g_mailapi/',$postData,true);	
		
		
		
		
	}
	
	function send_mail_prepare()
    {
        
        
        $p = $this->uri->segment(3);
		if(empty($p))$p = 0;
		$mailTo = array('lintaitin@gmail.com','phoenickimo@gmail.com');

		
		
		$time = getdate();

		$this->load->model('System_model');
		$data['shopList'] = $this->System_model->getShop(true);	
		$eachResult = array();
		$result = '<link rel="stylesheet" type="text/css" href="http://shipment.phantasia.com.tw/style/pos.css">';
		$url = 'http://shipment.phantasia.com.tw/accounting/get_day_report';
		$i = 0;$token = false;$num = 6;
		foreach ($data['shopList'] as $row)
		{
			if($i>=$num*$p && $i<($p+1)*$num)
			{	
				$eachResult[$row['shopID']] ='<h1>'.$row['name'].'報表</h1>'.
										 $this->paser->post($url,array('year'=>$time['year'],'mon'=>$time['mon'],'mday'=>$time['mday'],'shopID'=>$row['shopID'],'saveResult'=>1),false);
				$result .=	$eachResult[$row['shopID']]	;		
			
				$token = true;		
			}
			
			$i++;
		}
		
	;
		
		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
		$p++;
		if($token) redirect('/accounting/send_mail_prepare/'.$p);
		else 	 redirect('/accounting/send_mail/');
		
        
        
        
        
        
        
    }
		
	function send_mail()
	{
		$p = $this->uri->segment(3);
		if(empty($p))$p = 0;
		$mailTo = array('lintaitin@gmail.com');

		
		
		$time = getdate();
       
		$this->load->model('System_model');
		$data['shopList'] = $this->System_model->getShop(true);	
		$eachResult = array();
		$result = '<link rel="stylesheet" type="text/css" href="http://shipment.phantasia.com.tw/style/pos.css">';
		$url = 'http://shipment.phantasia.com.tw/accounting/get_day_report';
		$i = 0;$token = false;$num = 6;
		foreach ($data['shopList'] as $row)
		{
			if($i>=$num*$p && $i<($p+1)*$num)
			{	
				$eachResult[$row['shopID']] ='<h1>'.$row['name'].'報表</h1>'.
										 $this->paser->post($url,array('year'=>$time['year'],'mon'=>$time['mon'],'mday'=>$time['mday'],'shopID'=>$row['shopID'],'saveResult'=>1),false);
				$result .=	$eachResult[$row['shopID']]	;		
			
                 $this->paser->post($url,array('year'=>$time['year'],'mon'=>$time['mon'],'mday'=>$time['mday'],'shopID'=>$row['shopID'],'saveResult'=>true),false);
				$token = true;		
			}
			
			$i++;
		}
		
	;
		
        
       
		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
		$p++;
		if($token)
		{
			
			$this->db->reconnect();
			//	$this->Mail_model->groupEmail($mailTo,$time['year'].'/'.$time['mon'].'/'.$time['mday'].'營業報表'.$p ,$result,$headers);
			
			if($p==1)$this->Mail_model->myEmail('st855168@hotmail.com',$time['year'].'/'.$time['mon'].'/'.$time['mday'].'營業報表',$eachResult[4]	,$headers);
			
			 redirect('/accounting/send_mail/'.$p);
			 
		}
	}
	
	
	
	function updatePurchasePrice()
	{
		$this->load->model('Product_model');
		$i=18;
		do{
			
			$this->db->limit(1000,1000*$i);
			$query = $this->db->get('pos_product_sell');
			$data = $query->result_array();
			foreach($data as $row)
			{
				if($row['productID']=='8880014' &&$row['shopID']>=5) $purchasePrice= 50;
				else
				{
					$product = $this->Product_model->getProductByProductID($row['productID'],$row['shopID']);
					$purchasePrice= round($product['price'] * $product['purchaseCount']/100) ;
				}
				$this->db->where('id',$row['id']);
				$this->db->update('pos_product_sell',array('purchasePrice'=>$purchasePrice));
				
			}
			$i++;
			echo $i;
		}
		while(!empty($data ));
		
		
	}
	function updateAllTotalCost()
	{
		$this->load->model('Product_model');
		$query = $this->db->get('pos_product_amount');
		$data = $query->result_array();
		foreach($data as $row)
		{
			
			$product = $this->Product_model->getProductByProductID($row['productID'],$row['shopID']);
			if(!empty($product))
			{
			$datain = array('totalCost'=>($product['price']*$product['purchaseCount']*$row['num']/100));
			$this->db->where('id',$row['id']);
			$this->db->update('pos_product_amount',$datain);
			}
			
			
		}
		
		
	}
	function updateInshopAllTotalCost()
	{
		$this->load->model('Product_model');
		$query = $this->db->get('pos_inshop_amount');
		$data = $query->result_array();
		foreach($data as $row)
		{
			
			$product = $this->Product_model->getProductByProductID($row['productID'],$row['shopID']);
			if(!empty($product))
			{
			$datain = array('purchasePrice'=>(round($product['price']*$product['purchaseCount']/100)));
			$this->db->where('id',$row['id']);
			$this->db->update('pos_inshop_amount',$datain);
			}
			
			
		}
		
		
	}	

	function updateConsignmentAllTotalCost()
	{
		$this->load->model('Product_model');
		$query = $this->db->get('pos_consignment_amount');
		$data = $query->result_array();
		foreach($data as $row)
		{
			
			$product = $this->Product_model->getProductByProductID($row['productID'],$row['shopID']);
			if(!empty($product))
			{
			$datain = array('purchasePrice'=>(round($product['price']*$product['purchaseCount']/100)));
			$this->db->where('id',$row['id']);
			$this->db->update('pos_consignment_amount',$datain);
			}
			
			
		}
		
		
	}	


	function updateAlltype()
	{
		$this->db->update('pos_cash_register',array('cashType'=>3));//支出
		
		
		$this->db->where('note','sales');
		$this->db->update('pos_cash_register',array('cashType'=>1));//銷售
		
		$this->db->where('note','withdraw');
		$this->db->update('pos_cash_register',array('cashType'=>2));//提領
		
		$this->db->where('note','into');
		$this->db->update('pos_cash_register',array('cashType'=>4));//置入
		
	}
	
	function web_shop_order($ret = 0)
    {
        $this->load->model('cs_order_model');
    
        $shopID = $this->input->post('shopID');
        $date = $this->input->post('date');
      
        

        $data = $this->cs_order_model->getFinishWebShopOrder($shopID,$date);
        $data['date'] = $date;
        $data['result'] = true;
        if($ret==1)return $data;
        else echo json_encode($data);
    }
    
    
    
	function excel_test()
	{
			include_once($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/PHPExcel/IOFactory.php'); 
			$objPHPExcel = new PHPExcel();

		
		
		$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Firstname:');
$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Lastname:');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Maarten');
$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Balliauw');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
$objWriter->save($_SERVER['DOCUMENT_ROOT']."/pos_server/upload/test.xls"); 

	}
    function people_view()
    {
        
        $data['date'] =$this->uri->segment(3);
        $data['shopID'] = $this->uri->segment(4);
        $data['people'] = $this->people($data['date'],$data['shopID'],true);
        $this->load->view('people_view',$data);
        
        
    }
    
    function people($date,$shopID,$return = false)
    {
        
       	
		$this->load->helper('chart');

					
		$date = $this->uri->segment(3);
        $t = explode('-',$date) ;
        $year = $t[0];
        $mon = $t[1];
		$shopID =  $this->uri->segment(4);
		
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		$people = $this->Accounting_model->get_vistors($year,$mon,$shopID);
		
		$this->load->model('System_model');
		
	
		$chart = new open_flash_chart();
		$title = new title( '本月客戶流量圖' );
		$color = array('FF37FD','FF9D37','37FF9D','DDDD37','99FF37','37FF39','37FDFF','9D37FF','3799FF','3937FF','FF3937','FF3799');			
		$title->set_style( "{font-size: 20px; color: #A2ACBA; text-align: center;}" );
		$chart->set_title( $title );		
		
		ini_set('memory_limit','3048M');

		//$date = $date.'-'.$data['mday'];
		//$data['record'] = $this->Accounting_model->getDayReport($data['date'],$shopID);
	
		 for($i=1;$i<=1;$i++)
		{
			for($j=0;$j<=31;$j++)
			{
				$dayList[] = $j;
				$data['chartData'][$i][(int)$j] = 0;
			}	
			
		}
      
        	$maxValue = 0;$i = 1;
        foreach($people as $row)
            
        {
            
           
            $data['chartData'][$i][(int)$row['day']] += 1;
            if($maxValue <  $data['chartData'][$i][$row['day']]) $maxValue - $data['chartData'][$i][$row['day']];
        }
    
        if($return==true) return  $data['chartData'][$i];
       
		$i = 1;
	
		        $line = new line();
				$line->set_colour( '#'.$color[0] );
				//$line->set_values( array(9,8,7,6,5,4,3,2,1) );
				//print_r($data['chartData'][ $i]);
				$line->set_values( $data['chartData'][$i]);
				$line->set_key('來店人次', 12 );
				$chart->add_element( $line);
				$i++;
	
		
		
		$x_labels = new x_axis_labels();
			$x_labels->set_steps( 1 );
			$x_labels->set_vertical();
			$x_labels->set_colour( '#A2ACBA' );
			$x_labels->set_labels( $dayList);
			
			$x = new x_axis();
			$x->set_colour( '#A2ACBA' );
			$x->set_grid_colour( '#D7E4A3' );
			$x->set_offset( false );
			$x->set_steps(1);
			// Add the X Axis Labels to the X Axis
			//$x->set_labels( $x_labels );
			
			$chart->set_x_axis( $x );
			
			//
			// LOOK:
			//
			$x_legend = new x_legend( $year.' / '.$mon );
			$x_legend->set_style( '{font-size: 20px; color: #778877}' );
			$chart->set_x_legend( $x_legend );
			
			//
			// remove this when the Y Axis is smarter
			//
			$y = new y_axis();
			
			//if($maxValue>100) $offset = round($maxValue/8,-2);
		//	else $offset = round($maxValue/8,-1);
			$maxValue = 100;
			$y->set_range( 0, $maxValue, 5);
			
			$chart->add_y_axis( $y );		
		
		$output = $chart->toPrettyString();
		

        echo $output;	
         
		/**/
	
        
    }
	
    
    function goodthing()
    {
        
        
        $year = $this->input->post('year');	
		$mon = $this->input->post('mon');	

		$mday = $this->input->post('mday');	
            
        
        $this->db->where('year(time)',$year);
        $this->db->where('month(time)',$mon);
        $this->db->where('day(time)',$mday);
        $query = $this->db->get('pos_flow_msg');
        $data['msg'] = $query->result_array();
        
         $this->load->view('goodthing',$data);
        
        
    }
    
    function get_own_product()
    {
        
        $year = $this->input->post('year');	
		$mon = $this->input->post('mon');	
	if($mon==1||$mon==3||$mon==5||$mon==7||$mon==8||$mon==10||$mon==12)
		{
			$data['mday'] = 31;		
		}
		else if($mon==2)
		{
			if($year%4==0)	$data['mday'] = 29;	
			else $data['mday'] = 28;	
			
		}
		else 	$data['mday'] = 30;		
		
		$data['date'] =$year.'-'.$mon;
		$date = $year.'-'.$mon.'-'.$data['mday'];
		$firstDay = getdate(mktime(0,0,0,$mon,1,$year));
		$data['firstWeekDay'] = $firstDay['wday'];
        $this->load->model('Accounting_model');
        $data['product'] = $this->Accounting_model->getOwnProduct($year,$mon);
      
        $this->load->view('own_product',$data);
        
    }
    
    function get_invoice_total()
    {
        
        $this->load->model('Accounting_model');
        $year = $this->input->post('year');	
		$mon = $this->input->post('mon');
        $day = $this->input->post('mday');
        $shopID= $this->input->post('shopID');
        $dep= $this->Accounting_model-> getAllDep($shopID);
       
         $data['invoicetotal'] = 0; 
         foreach($dep as $row)
            {
                $invoiceList = $this->Accounting_model->getInvoiceList($year,$mon,$row['depID'],$day) ;
             
             foreach($invoiceList as $each)
             {
                  $data['invoicetotal'] += $each['total'];
                 
             }
                 
            }
        
        
        
        $data['result'] = true;   
          $data['result'] = true;
		echo json_encode($data);
    }
    function winlist_send()
    {
        
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        if($month%2==1) $month++;
        
        $file = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/einvoice/winlist/'.$year.'-'.$month.'txt';
         if(!file_exists($file))
         {
          $tmp_name = $_FILES["file"]["tmp_name"];
        // basename() may prevent filesystem traversal attacks;
        // further validation/sanitation of the filename may be appropriate
  
        move_uploaded_file($tmp_name,  $file);
         $data['result'] = true;
       if(file_exists($file))
			{
					$handle = fopen($file,'r');
					$contents = '';
				while(!feof($handle))
				{
                    $line = fgets($handle);
                   $a = explode(" ",$line);
               $testKey = true;
         
                    if(isset($a[2]))
                    {
                        $m = substr($a[2],3,2);

                      
                        if(($m+1)==($month+1))
                        {
                            $datain['year'] = $year;
                            $datain['month'] = $month;
                            $datain['amount'] = substr($a[296],5,10);
                            $datain['InvoiceNumber'] = substr($a[2],5,10);
                            $this->db->insert('pos_einvoice_winlist',$datain);

                        }
                        else
                        {
                             $data['result'] = false;
                            unlink($file);
                            break;

                        }
                    }
                
                }
				fclose($handle);	
//		        
                
				
				
				
				
			}
            else $data['result'] == false;
              if($data['result'] == true)
              {
                  $msg ='OK';
                  $title = date('Y').'-'.$month.'期 中獎發票清冊未寄發客戶資料已上傳';
                
                $content =$title.'<br/> 請速通知會計完成中獎發票列印寄發';
                $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			
                $this->Mail_model->myEmail('lintaitin@gmail.com,phantasia.ac@gmail.com',$title,$content,$headers,0,100,1);    
                  
                  
              }
             else $msg ='Error: wrong Time';
         }
          else $msg ='Error: File exist';
  
        my_msg($msg,'http://shipment.phantasia.com.tw/accounting/index/invoice');
        
    }
    
    function send_win_invoice()
    {
        $InvoiceNumber = $this->input->post('InvoiceNumber'); 
        
        $this->db->where('InvoiceNumber',$InvoiceNumber);
        $this->db->update('pos_einvoice_winlist',array('sending'=>date('Y-m-d H:i:s')));
        $result['result'] = true;
        echo json_encode($result);
        
    }
    function send_win_memo()
    {
         $InvoiceNumber = $this->input->post('InvoiceNumber'); 
        $memo = $this->input->post('memo'); 
        $this->db->where('InvoiceNumber',$InvoiceNumber);
        $this->db->update('pos_einvoice_winlist',array('memo'=>$memo ));
        $result['result'] = true;
        echo json_encode($result);
        
        
        
    }
    
    
    
    function get_win_list()
    {
         $this->load->model('Accounting_model');
         $year = $this->input->post('year');
        $month = $this->input->post('month');
   
         if($month%2==1) $month++;
           $r = $this->Accounting_model->getWinList($year,$month);
        if($r===false )$result['result'] = false;
        else $result['result'] = true;
        $result['winlist']  = $r;
        	echo json_encode($result);
        
    }
    
    
    function get_einvoice_inf()
    {
        $this->load->model('Accounting_model');
        $year = $this->input->post('year');	
		$mon = $this->input->post('mon');
        $shopID= $this->input->post('shopID');
        if($year==0)
        {
         $t = getdate();
            $year = $t['year'];
            $month = $t['mon'];
        }
           
        if($mon%2==1) $mon++;
        $YearMonth = ($year-1911).str_pad($mon,2,0,STR_PAD_LEFT);
        $dep= $this->Accounting_model-> getAllDep($shopID);
            foreach($dep as $row)
            {
                $row['invoice'] = $this->Accounting_model->getInvoiceInf($YearMonth,$row['depID']) ;
                  $data['dep'][] = $row; 
            }
        $data['result'] = true;
		echo json_encode($data);
        
        
    }
    
    function invoice_set()
    {
    
        $tmp_name = $_FILES["file"]["tmp_name"];
        // basename() may prevent filesystem traversal attacks;
        // further validation/sanitation of the filename may be appropriate
     
      
   
        $f = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/tempInvoice.csv';
        
        move_uploaded_file($tmp_name,  $f);
        $result['result'] = false;
        if(file_exists($f))
            
        {
        
        $handle = fopen( $f,'r');
           $i = 0;
            while(!feof($handle))
            {
              
                
                $contents = fgets($handle,1000);
                $i++;
                if($i==1) continue;
                $contents_array = explode(',',$contents);
   
                if(isset($contents_array[3])) 
                {
                    if($contents_array[0] =='53180059');
                    
                   $t = explode(' ~ ', $contents_array[3]);
                      $str =  str_replace('/',"",$t[1]);
                    $datain['YearMonth'] = $str;
                    $datain['InvoiceTrack'] = trim($contents_array[4]);
                    $datain['InvoiceBeginNo'] = trim($contents_array[5]);
                    $datain['InvoiceEndNo'] = trim($contents_array[6]);
                    $datain['InvoiceNext'] =  $datain['InvoiceBeginNo'];
                    $datain['depID']  = $this->input->post('depID');
                    
                    
                     $tStr =  explode('/',$t[0]);
                    
                    $datain['updateTime'] = ($tStr[0]+1911).'-'.$tStr[1].'-1';
                    
                    
                    $this->db->where('InvoiceTrack', $datain['InvoiceTrack']);
                     $this->db->where('YearMonth', $datain['YearMonth']);
                     $this->db->where('InvoiceBeginNo', $datain['InvoiceBeginNo']);
                    $q = $this->db->get('pos_einvoice_use');
                    if($q->num_rows()<=0)
                    {
                        
                        $this->db->insert('pos_einvoice_use',$datain);
                        
                        $result['result'] = true;
                    }
                    
                    
                  
                }
              

            }
            fclose($handle);
        }
          
        if($result['result'] == true)$msg ='OK';
        else $msg ='Error: repeat number';
        my_msg($msg,'http://shipment.phantasia.com.tw/accounting/index/invoice');
		
        
    }
    
    function invoice_generate()
    {
        
        $this->load->model('Accounting_model');
		
        $data['CarrierType'] =  $this->input->post('CarrierType');
        $data['CarrierId1'] =  $this->input->post('CarrierId1');
        $data['BuyerIdentifier'] =  $this->input->post('invoiceCode');
        $data['NPOBAN'] =  $this->input->post('NPOBAN');
		$data['title'] =  $this->input->post('title');
        $data['comment'] =  $this->input->post('comment');
        $data['shopName'] = $this->input->post('shopName');
        $data['orderNum'] = $this->input->post('orderNum');;
        $data['email'] = $this->input->post('email');;
        
        $data['invoiceTime'] = $this->input->post('invoiceTime');;
      
        $depID = $this->input->post('depID');;
         if($depID==0)
        {
             $shopID = $this->input->post('shopID');;
             $dep= $this->Accounting_model-> getAllDep($shopID);
            $depID = $dep[0]['depID']; 
        }

        $handIN = $this->input->post('handIN');;$repeat = false; 
        if($handIN!=1)
        {
            
            $repeat = false;
            $r = $this->Accounting_model->getEInvoiceByOrderNum($data['orderNum'],$depID);
            if(!empty($r))
            {
                 $result['invoiceNumber']  = $r['InvoiceNumber'];
                 $result['code'] =  md5('IlovePhantasia'.$result['invoiceNumber'] );
                 $result['result'] = true;
                 $repeat = true;        
                 
                
            }
           
            
        }
      
       
        
                        
        if( !$repeat)
        {
           $api = $this->input->post('api');;
	   if($api==1)
       {
           
           $r = json_decode($this->input->post('dataStr'),true);
        
           $total = $r['total'];
           $record = $r['record'];
       }
       else
        {   
           
		$num = $_POST['num'];
		$uniPrice = $_POST['uniPrice'];
		$item = $_POST['item'];
        $i = 1;
        $total = 0;
		foreach($num as $key=>$row)
		{
			if($row !='' && $row>=0)
			{
				$record[] = 
				array(
					'Description'=> $item[$key],
                    'Quantity'=> $num[$key],
					'UnitPrice'=> $uniPrice[$key],
					'Amount'=>$num[$key]*$uniPrice[$key],
                    'SequenceNumber' => str_pad($i++,3,0,STR_PAD_LEFT)
				);
				$total+=$num[$key]*$uniPrice[$key];
					
				
				
			}	
			
			
		}
           
       }
       
        $data['total'] = $total;
		$data['record'] = $record;
        if($total==0)  $result['result'] = false;
        else
        {
            $result['invoiceNumber']  = $this->Accounting_model->generateInvoice($data,$depID);
            $result['code'] =  md5('IlovePhantasia'.$result['invoiceNumber'] );
            $result['result'] = true;
            if($result['invoiceNumber']===false) $result['result']=false;
        }
        
        
        
        
        }
        
       
        
        
        
        echo json_encode($result,true);
    }
    
    function get_invoice_detail()
    {
        $this->load->model('Accounting_model');
        $year = $this->input->post('year');
        $month = $this->input->post('month');;
        $depID = $this->input->post('depID');
        
        
         $shopID = $this->input->post('shopID');;
        $key = false;
        if($this->data['logined'] && $this->data['shopID']==0) $key = true;
        if($this->System_model->chkShop($this->input->post('shopID'),$this->input->post('licence'))) $key=true;
		    
         if($depID==0)
        {
             $shopID = $this->input->post('shopID');
             $dep = $this->Accounting_model-> getAllDep($shopID);
             if(!empty($dep[0]['depID']))        $depID = $dep[0]['depID']; 
             
        }
        
       
        if($key)
        {
           $data['invoiceDetail'] = $this->Accounting_model->getInvoiceList($year,$month,$depID);
         $data['result'] = true;   
            
        }
        else  $data['result'] = false;  
        echo json_encode($data);
        
        
        
    }
    function show_invoice_detail()
    {
        $this->load->model('Accounting_model');
       $invoiceNumber = $this->input->post('InvoiceNumber');
        $total = $this->input->post('total');
      if(empty($total))$total = 0;
        
            $incoiceCode = substr($invoiceNumber,0,2);
            $invoiceNum = substr($invoiceNumber,2,8);
            $data['invoiceInf'] = $this->Accounting_model->getEInvoiceData($invoiceNum,$incoiceCode);
            
           if(isset($data['invoiceInf']['total']) && (($this->data['logined']&&$this->data['shopID']==0)||$total==$data['invoiceInf']['total'] ))
           {
            $data['code'] =  md5('IlovePhantasia'.$invoiceNumber);
        
            $data['result'] = true;
           }
            else
            {
                
                $data['invoiceInf'] = array();  
                   $data['result'] = false;
            }
        echo json_encode($data);
        
    }
    function show_invoice()
    {
        
        $invoiceNumber = $this->uri->segment(3);
        $code = $this->uri->segment(4);
        $json = $this->uri->segment(5);
        $this->data['print'] = $this->uri->segment(7);
    
        if( md5('IlovePhantasia'.$invoiceNumber)==$code)
        {
            
            
            $this->load->model('accounting_model');
        
       
            $incoiceCode = substr($invoiceNumber,0,2);
            $invoiceNum = substr($invoiceNumber,2,8);
            $this->data['invoiceInf'] = $this->accounting_model->getEInvoiceData($invoiceNum,$incoiceCode);
        ;
                $t = explode(' ', $this->data['invoiceInf']['InoviceDateTime']);     
                $date = explode('-',$t[0]);
                $year = $date[0]-1911;
                $month = str_pad( $date[1], 2, "0" ,STR_PAD_LEFT);  
                $day = str_pad( $date[2], 2, "0" ,STR_PAD_LEFT);  ;
                $total  =   $this->data['invoiceInf']['total'];
                $InvoiceDate = $year.$month.$day;
                $InoviceTime = str_replace(':','',$t);
                $InvoiceNumber = $this->data['invoiceInf']['InvoiceCode'].$this->data['invoiceInf']['InvoiceNum'];
                $RandomNumber = $this->data['invoiceInf']['RandomNumber'];
                $SalesAmount = 0;
                $sellTotalBtax = str_pad($SalesAmount, 8, "0" ,STR_PAD_LEFT); 
                $TaxAmount = 0;
                $TotalAmount = dechex($total);
                $sellTotalAtax = str_pad($TotalAmount, 8, "0" ,STR_PAD_LEFT);  

                $BuyerIdentifier = $this->data['invoiceInf']['BuyerIdentifier'];
                $RepresentIdentifier ='00000000';//電子發票證明聯二維條碼規格已不使用代表店，請填入00000000 8碼字串。
                $SellerIdentifier = $this->data['invoiceInf']['SellerIdentifier'];
              $BusinessIdentifier = '53180059';

                $invoicStr = $InvoiceNumber.$RandomNumber;


                $AESKey  =  '6729BBE168E033597EF04CBFADC4CBC4' ;//'以字串方式記載加密金鑰之 HEX 值。'           
                $r = $this->accounting_model->getEncrypt($invoicStr, $AESKey);

                $qrStr = $InvoiceNumber.    
                         $InvoiceDate.
                         $RandomNumber.
                         $sellTotalBtax.
                         $sellTotalAtax.
                         $BuyerIdentifier.
                         $SellerIdentifier.
                         $r ;
                /*
                12. ProductArrays : 單項商品資訊
        ProductArrays 中包含產品的陣列 (ProductArray)，此產品陣列應包含 :
        i. Product Code : 以字串方式記載透過條碼槍所掃出之條碼資訊。
        ii. Product Name : 以字串方式記載商品名稱。
        iii. ProductQty : 以字串方式記載商品數量。
        iv. ProductSaleAmount : 以字串方式載入商品銷售額 (整數未稅)，若無法分離稅項
        則記載為字串0。
        v. ProductTaxAmount : 以字串方式載入商品稅額(整數)，若無法分離稅項則記載為
        字串0。
        vi. ProductAmount : 以字串方式載入商品金額(整數含稅)。
        */      
                $ProductArrays = json_decode( $this->data['invoiceInf']['productDetail'],true);





                $num = count($ProductArrays);
                $totalNum = 0; $productStr ='';$productStr2 = '**'; $productStr2Key = false;
                foreach($ProductArrays as $row)
                {

                   $totalNum+= (int)$row['Quantity'];


                    $str=':'.str_replace(':','-',$row['Description']).':'.$row['Quantity'].':'.$row['Amount'];

                    if($productStr2Key||(strlen($productStr)+strlen($str))>(154-94))
                    {

                         $productStr2Key = true;
                        if((strlen($productStr2)+strlen($str))<154) $productStr2.= $str;
                    }
                    else $productStr.= $str;

                }

                $code = 2; //UTF8編碼

                $qrStr.=':**********:'.$num.':'.$totalNum.':'.$code.$productStr;

                $name = $year.$month.$InvoiceNumber.$RandomNumber;



        
        $this->data['code'] = $name;
       $this->data['bcT']='https://mart.phantasia.tw/barcode?t='.$name;
        // set BarcodeQR object 
    $l= $this->qrCodeVersion(strlen($qrStr)) ; 
            $s=2;
           
           
            
            
            
            
            
        $this->data['qrT']='https://mart.phantasia.tw/qrcode?d='.$qrStr.'&e=L&s='.$s.'&v='.$l;;
      $l= $this->qrCodeVersion(strlen($productStr2)) ; 
            $s=2;
           
        
        $this->data['qrT2']= 'https://mart.phantasia.tw/qrcode?d='.$productStr2.'&e=L&s='.$s.'&v='.$l;;;
      
      
        $this->data['ProductArrays'] =$ProductArrays;
        $this->data['reprint'] = $this->uri->segment(6);;
        $this->data['result'] = true;
        $this->data['commentText'] = '';
            
        if($this->data['invoiceInf']['CarrierId1']!='')$this->data['commentText'] .= '此發票已經存入載具：' .$this->data['invoiceInf']['CarrierId1'].'<br/>';
                
         if($this->data['invoiceInf']['NPOBAN']!='')$this->data['commentText'] .= '此發票已經設定愛心捐贈碼：'.$this->data['invoiceInf']['NPOBAN'].'<br/>'; 
            
            
        }
        else $this->data['result'] = false;
        
         if($json=='json') echo json_encode($this->data);
         else
         {
             if($this->data['result'] ==true) 
             {
                $this->load->view('invoice_print_bc',$this->data);	
             }
             else redirect('https://www.phantasia.tw');
        
             
         }
       
        
    }
    
    function qrCodeVersion($strlen)
    {
       if($strlen<134)return 6;
       else if($strlen<154) return 7;
       else if($strlen<192) return 8;
        else if($strlen<230) return 9;
        else if($strlen<271) return 10;
      
        else return 11;
        
            
        
    }
     
    
    function invalid_invoice()
    {
        $key = false;
        if($this->data['logined'] && $this->data['shopID']==0) $key = true;
        if($this->System_model->chkShop($this->input->post('shopID'),$this->input->post('licence'))) $key=true;
		    
          $result['result'] = false;
        if($key)
        {
        $this->load->model('accounting_model');
        $invoiceNumber = $this->input->post('invoiceNumber');
            $invoiceCode = substr($invoiceNumber,0,2);
            $invoiceNum = substr($invoiceNumber,2,8);
            $this->data['invoiceInf'] = $this->accounting_model->getEInvoiceData($invoiceNum,$invoiceCode);
        $result['invoiceNum'] =$invoiceNum;
        
        
        $datain = array(
            'year'           =>$this->data['invoiceInf']['year'],
            'period'         =>$this->data['invoiceInf']['period'],
            'InvoiceDateTime'=>$this->data['invoiceInf']['InoviceDateTime'], 
            'BuyerIdentifier'=>$this->data['invoiceInf']['BuyerIdentifier'], 
            'SellerIdentifier'=>$this->data['invoiceInf']['SellerIdentifier'], 
            'InvoiceNum'    =>$invoiceNum,
            'InvoiceCode'  =>$invoiceCode,
            'CancelDateTime'=>date("Y-m-d H:i:s"),
            'CancelReason'=> $this->input->post('CancelReason'),
            'Remark'      => ''
            
        
        
        
        );
        $this->db->insert('pos_einvoice_cancel',$datain);
        $invalidID = $this->db->insert_id();
        $this->db->where('id',$this->data['invoiceInf']['id']);
        $this->db->update('pos_einvoice',array('invalid'=>$invalidID));
        $result['result'] = true;
        }
        echo json_encode($result);
        
        
        
    }
   function void_invoice()
     {
             $this->load->model('accounting_model');
        $invoiceNumber = $this->input->post('invoiceNumber');
   //    $invoiceNumber  = 'VD47068924';
            $invoiceCode = substr($invoiceNumber,0,2);
            $invoiceNum = substr($invoiceNumber,2,8);
            $this->data['invoiceInf'] = $this->accounting_model->getEInvoiceData($invoiceNum,$invoiceCode);
        
        
        
        $datain = array(
            'year'           =>$this->data['invoiceInf']['year'],
            'period'         =>$this->data['invoiceInf']['period'],
            'InvoiceDateTime'=>$this->data['invoiceInf']['InoviceDateTime'], 
            'BuyerIdentifier'=>$this->data['invoiceInf']['BuyerIdentifier'], 
            'SellerIdentifier'=>$this->data['invoiceInf']['SellerIdentifier'], 
            'InvoiceNum'    =>$invoiceNum,
            'InvoiceCode'  =>$invoiceCode,
            'VoidDateTime'=>date("Y-m-d H:i:s"),
            'VoidReason'=> $this->input->post('VoidReason'),
            'Remark'      => ''

        );
        $this->db->insert('pos_einvoice_void',$datain);
        $invalidID = $this->db->insert_id();
        $this->db->where('id',$this->data['invoiceInf']['id']);
        $this->db->update('pos_einvoice',array('void'=>$invalidID));
        
        
        
        $result['result'] = true;
        
        echo json_encode($result);                   
                              
                              
     }
                          
   
    
  
   
        
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */