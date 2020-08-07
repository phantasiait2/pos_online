<?php

class Product_flow extends POS_Controller {

	function Product_flow()
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
        $this->data['js'] = $this->preload->getjs('pos_product_query');
		$this->data['js'] = $this->preload->getjs('pos_product_flow');
		if($this->data['shopID']!=0) redirect('/accounting');
		$time = getdate();
		if($time['mon']==1||3||5||7||8||10||12) $maxDay=31;
		else if($time['mon']==4||6||9||11||12) $maxDay=30;
		else
		{
			if($time['mon']%4==0)	$maxDay=29;
			else $maxDay = 20;
			
			
		}
		$this->data['maxDay'] =$maxDay;
		$this->data['time'] = $time;
		
		$this->load->model('System_model');
		$this->data['shopList'] = $this->System_model->getShop(true);
		
		
		
		$this->data['display'] = 'product_flow';
		$this->load->view('template',$this->data);	
	}
	function flow_report()
	{		

		$this->load->model('Product_flow_model');
		$data['from'] = $this->input->post('from_year').'-'.$this->input->post('from_mon').'-'.$this->input->post('from_day');
		$data['to'] =  $this->input->post('to_year').'-'.$this->input->post('to_mon').'-'.$this->input->post('to_day');
		$data['purchase'] = $this->input->post('purchase');
		$data['customerBack'] = $this->input->post('customerBack');
		$data['adjust'] = $this->input->post('adjust');
		$data['back'] = $this->input->post('back');
		$data['sell'] = $this->input->post('sell');
		$data['amount'] = $this->input->post('amount');
		$this->load->model('System_model');
		$shopList = $this->System_model->getShop();
		$i= 0 ;
		$time =$data['to'];	
				if($this->input->post('from_mon')==1)$lastTime = ($this->input->post('from_year')-1).'-01-'.$this->input->post('from_day');
		else 	$lastTime= $this->input->post('from_year').'-'.($this->input->post('from_mon')-1).'-'.$this->input->post('from_day');

		if($this->input->post('shop_0')==1)
		{
			$data['shopData'][] =  array('shopID'=>0,'name'=>'幻遊天下');
			$data['shopIDList'][] = 0;
			$data['shopSql'][] 
				=array(
				 'lastAmount'=>$this->Product_model->getProductSql($lastTime,0)." WHERE pos_product_amount.productID is not NULL",
				 'amount' =>$this->Product_model->getProductSql($time,0)." WHERE pos_product_amount.productID is not NULL"
				 );
		}
		

		$this->load->model('Product_model');	
		foreach($shopList as $row)
		{
			if($this->input->post('shop_'.$row['shopID'])==1)
			{
				$data['shopData'] []=  $row;
				$data['shopIDList'][] = $row['shopID']; 
				$data['shopSql'][] 
				=array(
				 'lastAmount'=>$this->Product_model->getProductSql($lastTime,$row['shopID'])." WHERE a.productID is not NULL",
				 'amount' =>$this->Product_model->getProductSql($time,$row['shopID'])." WHERE a.productID is not NULL"
				 );
				
			}
		}
		

		$data['flowList']= $this->Product_flow_model->getFlow($data['from'],$data['to'],$data['shopIDList'],$data['shopSql'],$data);
		$this->load->view('flow_report',$data);	
		
	}
	function get_inventory()
	{
		$this->load->model('Product_flow_model');
		$data['from'] = $this->input->post('from_year').'-'.$this->input->post('from_mon').'-'.$this->input->post('from_day');
		$data['to'] =  $this->input->post('to_year').'-'.$this->input->post('to_mon').'-'.$this->input->post('to_day');
		$data['purchase'] = false;
		$data['sell'] =false;
		$data['amount'] = $this->input->post('amount');
		$this->load->model('System_model');
		$shopList = $this->System_model->getShop();
		$i= 0 ;
		$time =$data['to'];	
				if($this->input->post('from_mon')==1)$lastTime = ($this->input->post('from_year')-1).'-01-'.$this->input->post('from_day');
		else 	$lastTime= $this->input->post('from_year').'-'.($this->input->post('from_mon')-1).'-'.$this->input->post('from_day');

		if($this->input->post('shop_0')==1)
		{
			$data['shopData'][] =  array('shopID'=>0,'name'=>'幻遊天下');
			$data['shopIDList'][] = 0;
			$data['shopSql'][] 
				=array(
				 'lastAmount'=>$this->Product_model->getProductSql($lastTime,0)." WHERE pos_product_amount.productID is not NULL",
				 'amount' =>$this->Product_model->getProductSql($time,0)." WHERE pos_product_amount.productID is not NULL"
				 );			//echo $this->Product_model->getConsignmentSql($time,0,false); 
		}
		
		$this->load->model('Product_model');	
		foreach($shopList as $row)
		{
			if($this->input->post('shop_'.$row['shopID'])==1)
			{
				$data['shopData'] []=  $row;
				$data['shopIDList'][] = $row['shopID']; 
				if($row['shopID']<=2)$data['shopSql'][]['amount'] = $this->Product_model->getConsignmentSql($time,$row['shopID'],false); 
				else $data['shopSql'][]['amount'] = $this->Product_model->getConsignmentSql($time,$row['shopID'],true); 
				
			}
		}
		
		

		$data['flowList']= $this->Product_flow_model->getFlow($data['from'],$data['to'],$data['shopIDList'],$data['shopSql'],$data);
		
		$this->load->view('consignment_report',$data);	
		
		
	}
	
	function product_flow_rate()
	{
		$data['from'] = $this->input->post('from_year').'-'.$this->input->post('from_mon').'-'.$this->input->post('from_day');
		$data['to'] =  $this->input->post('to_year').'-'.$this->input->post('to_mon').'-'.$this->input->post('to_day');
	
		$this->load->model('product_flow_model');
		$shopList = $this->System_model->getShop();
		foreach($shopList as $row)
		{
			if($this->input->post('shop_'.$row['shopID'])==1)
			{
				$data['shopData'] []=  $row;
				$data['shopIDList'][] = $row['shopID']; 
				
			}
		}
		$data['shopNum'] =count($data['shopIDList']);
		foreach($data['shopIDList'] as $eachShop)
		{
			
			$product_flow = $this->product_flow_model->getProductFlow($eachShop,$data['from'],$data['to']);
		
		
			foreach($product_flow as $row)
			{
				$index = 'p_'.$row['productID'];
				if(!isset($product[$index]))
				{
					$product[$index] = $row;
					
					$product[$index]['productID'] = $row['productID'];
					$product[$index]['num'] = 0;
					$product[$index]['sellNum'] = 0;
					$product[$index]['purchaseNum'] = 0;
					$product[$index]['firstDay']=0;
					$product[$index]['days'] = 1;
					
					
				}
				if($product[$index]['firstDay']==0 )
				{
					if($row['purchaseNum']>0)
					{
						 $product[$index]['firstDay']  = $row['time'] ; 
						
						 $lastDay  = $row['time'] ; 
					}
					else 
					{
						$product[$index]['firstDay']= $data['from'];
						 $lastDay  = $data['from']; 
					}
					
				}
				
				$product[$index]['purchaseNum'] += $row['purchaseNum'];
				if($row['sellPrice']==0)
				{
					$product[$index]['purchaseNum'] -= $row['sellNum'];//贈品不計
					
				}
				else
				{
					$lastDay = $row['time'] ; 
				    $product[$index]['sellNum'] += $row['sellNum'];
				}
		
				if($product[$index]['purchaseNum'] == $product[$index]['sellNum'])
				{
					
					$product[$index]['num']+=	$product[$index]['sellNum'];
					$product[$index]['days'] += round((strtotime($row['time'])-strtotime($product[$index]['firstDay']))/(3600*24));
					
					$product[$index]['firstDay']=0;
					$product[$index]['sellNum'] = 0;
					$product[$index]['purchaseNum'] = 0;
					
				}
				$lastTime = $row['time'];
			}
			
			foreach($product as $row)
			{
				$index = 'p_'.$row['productID'] ; 
				if($product[$index]['firstDay']!=0)
				{
					$product[$index]['num']+=	$product[$index]['sellNum'];
					$product[$index]['days'] += round((strtotime($lastTime)-strtotime($product[$index]['firstDay']))/(3600*24));
					
					
					$product[$index]['firstDay']=0;
						$product[$index]['sellNum'] = 0;
					$product[$index]['purchaseNum'] = 0;
				}
			}
		}
		
		$product_comNum= $this->product_flow_model->getComNum();
		$i = 0;
		foreach($product as $row)
		{
			
			
				if($row['num']!=0)
				{
					$data['product'][$i] = $row;
					if(isset($product_comNum['p_'.$row['productID']]['num']))$data['product'][$i]['comNum'] =$product_comNum['p_'.$row['productID']]['num'];
					else $data['product'][$i]['comNum'] = 0;
					$data['product'][$i]['flowRate'] = $row['days']/$row['num'];
					$data['product'][$i++]['flowNum'] = $row['num'];
			
				
				}
			
			
			
		}	
		
		$this->load->view('flow_rate',$data);	
		
	}
	function product_sale_flow()
	{
            $data['productID'] = $this->uri->segment(3);
        $this->load->model('product_flow_model');
            $sell = $this->product_flow_model->getRecentSell($data['productID']);
        $this->load->model('system_model');
            foreach($sell as $row)
            {                
             
                if(!isset($r[$row['shopID']]))
                     {
                          $shopData = $this->System_model->getShopByID($row['shopID']);
                           $r[$row['shopID']]['name'] = $shopData['name'] ;  
                         $r[$row['shopID']]['num'] = 0;
                         
                    }
       
                $r[$row['shopID']]['num'] += $row['num'];
                
                
            
            }
                             
			$data['sell'] = $r;
        
			$this->load->view('product_sale_flow',$data);
		
	}
	function latest_product_sell()
	{
		$this->load->model('product_flow_model');
		$this->load->model('product_model');
		$productID = $this->input->post('productID');
		
		$sell = $this->product_flow_model->getRecentSell($productID);
		$product =$this->product_model->chkProductByProductID($productID);
		
		$i = 0;
		$j = 0;
		$monthIndex = array();
		$maxValue = 0;
		foreach($sell as $row)
		{
				$month = substr($row['time'],0,7);//抓年月
				if(!isset($monthIndex[$month]))
				{
					$data['chartData'][0][$j] = 0;
					$dayList[] = $month;	
					$monthIndex[$month] =$j;
					$j++;
					
				}
        
				$data['chartData'][0][$monthIndex[$month]] +=$row['num'];
				if($data['chartData'][0][$monthIndex[$month]]>$maxValue)$maxValue   = $data['chartData'][0][$monthIndex[$month]];
			
		}
             $out[] = array('Month','Sales') ;
        foreach($data['chartData'][0] as $key=>$row)
        {
            
            $out[]=array($dayList[$key],$row);
            
            
            
            
        }
        $result['title'] = $product['ZHName'].'最近銷售資訊';
        
        $result['out'] = $out;
        $result['result'] = true;
      
		echo json_encode($result);
		exit(1);	          
		
	}
	
	
	
	function product_sale_update()
	{
		
			$this->db->update('pos_product',array('flowRate'=>0,'flowNum'=>0));
		$now =getdate();
		$to =  date("Y-m-d H:i:s");
		$from = date("Y-m-d H:i:s",mktime(00,00,00,$now['mon']-3,$now['mday'],$now['year']));	
		
		$this->db->select('pos_product.ENGName,pos_order_detail.productID,sum(pos_order_shipment_detail.sellPrice * pos_order_shipment_detail.sellNum) as sum,sum(pos_order_shipment_detail.sellNum) as num');
		$this->db->where('pos_order_detail.status',1);
		$this->db->where('pos_order_shipment.shippingTime >=',$from);
		$this->db->where('pos_order_shipment.shippingTime <=',$to);
		$this->db->where('pos_order_shipment.type',0);
		$this->db->where('pos_order_shipment.status >=',2);
		$this->db->where('pos_order_shipment.status <=',3);
		$this->db->join('pos_order_detail','pos_order_detail.id=pos_order_shipment_detail.rowID');
		$this->db->join('pos_product','pos_product.productID=pos_order_detail.productID');
		$this->db->join('pos_order_shipment','pos_order_shipment.id=pos_order_shipment_detail.shipmentID');
		
		$this->db->group_by('pos_order_detail.productID');
		$query = $this->db->get('pos_order_shipment_detail');
		//print_r($query->result_array());
		$data = $query->result_array();
		foreach($data as $row)
		{
			$this->db->where('productID',$row['productID'])	;
			$this->db->update('pos_product',array('flowRate'=>$row['sum'],'flowNum'=>$row['num']));
			
			
		}
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
		
	}
	function test()
	{
		date_default_timezone_set("Asia/Taipei");
		$this->load->model('Product_flow_model');
		$from = '2018-01-01 00:00:00';
        $to = '2018-01-31 23:59:59';
		$productID = 8881871;
		$row['shopID'] = 0;
	$data['consignment_s'] = 
				//銷 對於直營而言 ，此處昰公司出貨單
		$isCosignment= -1;
				if($isCosignment==0)$type= -2;
				else $type= $isCosignment;
				$data['s'][$row['shopID']] = $this->Product_flow_model->getShipmentOut($from,$to,$row['shopID'],$productID,$type);;
		
		print_r(	$data['s']);
		
	}
	function ship_out()
	{
		$year = $this->uri->segment(3);
		$month = $this->uri->segment(4);
       
        $this->load->model('Product_flow_model');
        $data['year'] = $year;
        $data['month'] = $month;
        $data['date'] = $year.'-'.$month;
        $file = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/shipout/ship_out_report_'.$data['date'].'.txt';
				
			if(file_exists($file))
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
        
     // $creareFile = true;
            $thisMonth = $month;
          if($creareFile) 
		{
              
             $total = 0;
            $purchaseTotal = 0;      
              
           for($thisMonth = $month;$thisMonth<$month+6;$thisMonth++)
            {
                $thisMon = array();
               $from = date("Y-m-d H:i:s",mktime(00,00,00,$thisMonth,1,$year));
                $to = date("Y-m-d H:i:s",mktime(23,59,59,$thisMonth+1,0,$year));
          
                $thisMonFile = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/shipout/month_ship_out_report_'.$from.'.txt';
                if(file_exists($thisMonFile))
                {
                        $handle = fopen($thisMonFile,'r');
                        $contents = '';
                    while(!feof($handle))
                    {$contents .= fgets($handle);}
                    fclose($handle);	
    //		
                 $thisMon = json_decode($contents,true);

                    $monthCreareFile = false;

                }
                else $monthCreareFile = true;

                if($monthCreareFile)
                {
                        
                      $data['outStock'] = $this->Product_model->getProductIO(0,0,999999,'%%',$from,$to,'',0);	
                
                        foreach($data['outStock'] as $row )
                        {
                            if(!isset($thisMon['s'][$row['suppliers']]))
                            {

                                $myTotal = round($this->Product_model->getSupplierPurchase($row['suppliers'],$from,$to),0);
                                $thisMon['s'][$row['suppliers']]['進貨額度'] =  $myTotal;
                           
                                $thisMon['s'][$row['suppliers']]['進貨百分比'] = 0;
                                $thisMon['s'][$row['suppliers']]['出貨額度'] = 0;
                                //$thisMon['s'][$row['suppliers']][substr($to,0,10).'庫存總額'] = $this->Product_flow_model->getStockTotal($supplierID, substr($to,0,10));
                            }
                            if($row['type']==0)	
                            {
                                $thisMon['s'][$row['suppliers']]['出貨額度'] += $row['sellPrice']* $row['outNum'];
                             
                            }

                        }

                        $today = date('Y-m-d') ;
                        if(strtotime($today)>strtotime($to) )   
                           {
                               
                               $output = json_encode($thisMon);
                        $f = fopen($thisMonFile,'w');
                        fprintf($f,"%s",$output);
                        fclose($f); 
                               
                           }
                       	
                }
               
                    if(isset($thisMon['s'] ))
                    foreach($thisMon['s'] as $key=>$each)
                    {
                        
                        
              
                        if(!isset( $data['s'][$key]['name']))
                        {
                            $s =  $this->Product_model->getSupplierInf($key);
                            if(isset($s['name']))$data['s'][$key]['name'] = $s['name'];
                            else $data['s'][$key]['name'] = '';
                        
                            
                        }
                        
                        
                        if(!isset( $data['s'][$key]['進貨額度'])) $data['s'][$key]['進貨額度'] = 0;
                        $data['s'][$key]['進貨額度']+= $each['進貨額度'];
                        
                        if(!isset( $data['s'][$key]['出貨額度'])) $data['s'][$key]['出貨額度'] = 0;
                        $data['s'][$key]['出貨額度']+= $each['出貨額度'];
                       
                        //$data['s'][$key][substr($to,0,10).'庫存總額'] = $each[substr($to,0,10).'庫存總額'];
                        $total+=$each['出貨額度'];
                        $purchaseTotal +=  $each['進貨額度'];
                    }
                    
              
                
              
           }
            
              
          
            $result = array();
              $this->db->update('pos_suppliers',array('order'=>100));   
              
		if(isset($data['s']))
		foreach($data['s'] as $supplierID => $row)
		{
            
			$row['出貨百分比'] = round($row['出貨額度'] / $total,2);
            $row['進貨百分比'] = round($row['進貨額度'] / $purchaseTotal,2);
            if($row['進貨額度'] !=0)  $ratio = round(($row['出貨額度'] - $row['進貨額度'])*100/$row['進貨額度'] ,0);
            else $ratio = 0 ;
            
            
            if($ratio>40) $comment='庫存減少中';
            else if($ratio>10) $comment = '合理利潤銷';
            else if($ratio>=0)$comment='庫存增或利潤低';
            else $comment = '嚴重庫存增加，或賠錢出售';
            
            if($ratio >0)$row['買賣超'] = '<span style="color:green">'.$ratio.'%</span>';
            else $row['買賣超'] = '<span style="color:red">+'.$ratio.'%</span>';
             $row['買賣超'] .=$comment;
		//	$row[substr($to,0,10).'庫存總額'] = $this->Product_flow_model->getStockTotal($supplierID, substr($to,0,10)); ;//庫存 總額  (時間，suppliers);$supplierID ,$to

			$result[] = $row;
            $this->db->where('supplierID',$supplierID);
            $this->db->update('pos_suppliers',array('order'=>(1-$row['出貨百分比'])*100));
		}
		$data['s'] = $result;
		//$this->PO_model->arraySort($data['s'],'出貨額度','DESC');
			$today = date('Y-m-d') ;
        if(strtotime($today)>strtotime($to) ) 
        {
			$output = json_encode($data);
				$f = fopen($file,'w');
			fprintf($f,"%s",$output);
					fclose($f);		
        }
		}
        
        
        
        
        
        $this->load->view('product_stock_view',$data);
		
		
	}
	function product_accounting()
	{
		//期初庫存
		//期末庫存
		ini_set('max_execution_time', 0);
		$this->load->model('Order_model');
		$shopID = 0;
		$data['excel']= $this->input->post('excel');
		$year = $this->input->post('from_year');
		$month = $this->input->post('from_mon');
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
		//	echo $data['last'];
		$data['lastStock'] = $this->Product_model->getProductStock(array('time'=>$data['last'],'inStock'=>1,'withOutConsignment'=>true,'order1'=>'productNum','sequence1'=>'DESC'),$shopID);
		
		$data['date']= $year.'-'.$month;
		$data['stock'] = $this->Product_model->getProductStock(array('time'=>$data['date'],'inStock'=>1,'withOutConsignment'=>true,'order1'=>'productNum','sequence1'=>'DESC'),$shopID);
		
		$data['lastConsignmentStock'] =$this->Product_model->getConsignmentStock($lastyear,$lastMonth,$shopID);
		$data['consignmentStock'] =$this->Product_model->getConsignmentStock($year,$month,$shopID);
		//進貨
		$from = $data['date'].'-01 00:00:00';
	
		$to = date("Y-m-d H:i:s",mktime(23,59,59,$month+1,0,$year));
		echo $to;
		$query ='';
		$shopQuery ='';
		$data['inStock'] = $this->Product_model->getProductIN(0,$from,$to);
		//出貨
		$data['outStock'] = $this->Product_model->getProductOut(0,$from,$to);
		
	
			$data['outConsignment'] = array();
		$shopList = $this->System_model->getShop();
	
		foreach($shopList as $row)
		{
			$total = 0 ;
			$product = $this->Order_model->getConsignmentMonthCheck($row['shopID'],$year,$month,0);
			if(!empty($product))
			foreach($product as $col)
			{
					
				 $col['totalNum'] = $col['consignmentNum'] - $col['remainNum'] ;
				$subtotal = $col['totalNum']*round($col['price']*$col['purchaseCount']/100);
					$total += $subtotal;	
				
			}
			$data['outConsignment'][] =array( 'name'=>$row['name'],'total'=>$total);
			
		}
		
		//退貨
		$data['backStock'] = $this->Product_model->getProductBack(0,$from,$to);
		
		//調貨
		$data['adjustStock'] = $this->Product_model->getProductAdjust(0,$from,$to);
		
		//期初庫存-期末庫存+退貨+進貨-出貨 -稅額 =>毛利
		
		//寄賣的期初庫存-寄賣的期末庫存+寄賣賣出。
		$this->load->view('product_report_excel',$data);
		
		
		
	}
    function get_all_stock_back()
    {
        	$this->load->model('Product_flow_model');
        $year = $this->input->post('year');
  
        $data['backData'] = $this->Product_flow_model->getAllBackData($year);
            $data['result'] = true;
		echo json_encode($data);
        
        
        
    }
    
    function clear_ig()
    {
        $year =  $this->input->get('year');
         $mon =  $this->input->get('mon');
        
        $this->db->where('shopID',0);
         $this->db->where('year',$year);
         $this->db->where('month',$mon);
        $q = $this->db->get('pos_accounting_io');
        $data = $q->result_array();
       foreach($data as $row)
       {
           echo $row['productID'];
            $this->db->where('productID',$row['productID']);
           $this->db->update('pos_product',array('ig'=>0));
           
           
       }
            
        
    }
    
    
    
    
    function back_reason()
    {
        
          $id = $this->input->post('id');
          $reason = $this->input->post('reason');
        
          $this->db->where('id',$id);
         
         $this->db->update('pos_order_back_detail',array('reason'=>$reason));
           $data['result'] = true;
		echo json_encode($data);
    
    }
    
    
	function stock_ratio()
    {
        
        $year = $this->uri->segment(3);
		if($year!=date('Y')){
			$file = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/ship/ship_report'.$year.'.txt';
				if(file_exists($file))
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
				$shipTotal=array(
					'price' => 0, 
					'num' => 0
					);
				$backTotal=array(
					'price' => 0, 
					'num' => 0
					);
				$this->load->model('Product_flow_model');
				$shipTotal = $this->Product_flow_model->getShipTotal($year,$shipTotal);
				$backTotal = $this->Product_flow_model->getBackTotal($year,$backTotal);
               // print_r($backTotal);
				$data['shipTotal']['num'] =$shipTotal['num'];
				$data['shipTotal']['price'] =$shipTotal['price'];
				 $data['backTotal'] = $backTotal;
				$output = json_encode($data);
					$f = fopen($file,'w');
				fprintf($f,"%s",$output);
						fclose($f);		
			}
		}
		else{
			$shipTotal=array(
					'price' => 0, 
					'num' => 0
					);
			$backTotal=array(
					'price' => 0, 
					'num' => 0
					);
				$this->load->model('Product_flow_model');
				$shipTotal = $this->Product_flow_model->getShipTotal($year,$shipTotal);
				$backTotal = $this->Product_flow_model->getBackTotal($year,$backTotal);
				$data['shipTotal']['num'] =$shipTotal['num'];
				$data['shipTotal']['price'] =$shipTotal['price'];
				
                $data['backTotal'] = $backTotal;
        
		}
        if($data['shipTotal']['price']!=0)
        $data['totalPercent'] = round($data['backTotal']['price']/$data['shipTotal']['price']*100,2);
        else  $data['totalPercent']  = 0;
		$data['numPercent'] = round($data['backTotal']['num']/$data['shipTotal']['num']*100,2);
        $data['result'] = true;
		echo json_encode($data);
		
        
        
    }
    
	
	
    
    function pos_io()
    {
        $this->load->model('Product_flow_model');
        $this->load->model('Product_model');
  /*
        $_POST['from'] = '2017-08';
        $_POST['to'] = '2017-08';
         $_POST['shopID'] = 0;
         $_POST['productID'] = '8881906';
 */
         
    
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        $shopID = $this->input->post('shopID');
        if(empty($shopID))$shopID = 0;
        $productID = $this->input->post('productID');
        $data = $this->Product_flow_model-> productIOCollect($from,$to,$shopID,$productID);
        $data['inf'] = $this->Product_model->getProductInfByProductID($productID);
      
        $data['result'] = true;
          echo json_encode($data);
    }
    function  io_test()
    {
       $this->do_pos_account_io_update( $this->input->get('from'),$this->input->get('productID'));
        
            
    }
    
    
    function pos_account_io_update()
    {
        
       
        
      $this->do_pos_account_io_update( $this->input->post('from'),$this->input->post('productID'));
    }
    
    function box_product_update()
    {
        $this->load->model('Product_flow_model');
        $from = $this->input->post('from');
      	$t = explode('-',$from);
		
		$year = $t[0];
		$month = $t[1];
        $package = $this->Product_flow_model->getAllPackage();
        foreach($package as $row)
        {
            
           $this->do_pos_account_io_update($from,$row['boxProductID']);
            
           $this->Product_flow_model->deleteAccountData($row['boxProductID'],$year,$month);
           $this->do_pos_account_io_update($from,$row['unitProductID']); 
           $this->Product_flow_model->deleteAccountData($row['unitProductID'],$year,$month); 
            
        }
        
        
        
    }
    
    
    function do_pos_account_io_update($from,$productID,$echo=true)
	{
  	 $testKey = false;;
		
    

        
		$this->load->model('Product_flow_model');
        $this->load->model('Product_model');
		$this->load->model('Order_model');
		$t = explode('-',$from);
		
		$year = $t[0];
		$month = $t[1];
		$thisYear = $year;
		$thisMonth = $month;
		$day = 0;
		$from = $from.'-01 00:00:00';
        $to = $from.'-31 23:59:59';
		$isCosignment = -1;
		
	
		$dataString = $this->Product_flow_model-> getAccountingData($productID,$thisYear,$thisMonth);
		$data = json_decode($dataString,true);
		
		$thisMonth++; //下個月
		if($thisMonth>12)
		{
			$thisMonth = 1;
			$thisYear++;
		}
		
		foreach($data['e_each'] as $index =>$row)
		{
			$shopID = $index;
			
			

			$datain = array(
				'shopID'    =>$shopID,
				'productID' =>$productID,
				'month'     =>$month,
				'year'      =>$year,
				'amount'    =>$row['num'],
				'avgCost'   =>$row['avgCost'],
				'totalCost' =>$row['totalCost'],
				'type'      =>2,
				'mainID'    =>0


			);
			$this->db->where('productID',$productID);
			$this->db->where('shopID',$shopID);
			$this->db->where('year',$year);
			$this->db->where('month',$month);
			$query = $this->db->get('pos_accounting_io');
			$r =  $query->row_array();
			if(empty($r)) 	$this->db->insert('pos_accounting_io',$datain);
			else
			{
					
					$this->db->where('id',$r['id']);

					$this->db->update('pos_accounting_io',$datain);

			}
			
			
		}
		/*
        if(!isset($data['o_all']) ||($data['o_all']['num']==0&&$data['p_all']['num']==0&&$data['s_all']['num']==0&&$data['e_all']['num']==0))
        {
            
                $this->db->where('productID',$productID);
                $this->db->where('igBreak <=',$year.'-'.$month.'-01');
				$this->db->update('pos_product',array('ig'=>1));
        }
        */
	
				if($data['inf']['price']!=0)
				$purchasePrice = round(($data['e_all']['avgCost']*100/$data['inf']['price']),2);
				else $purchasePrice =0;
            if($purchasePrice!=0)
            {
                $this->db->where('productID',$productID);
				$this->db->update('pos_product',array('buyPrice'=>$data['e_all']['avgCost'],'buyDiscount'=>$purchasePrice));
                
            }
				
				if($testKey)$this->db->insert('pos_test',array('content'=>date("Y-m-d H:i:s").'$saveResult'.$purchasePrice.','.$row['avgCost'].','.$productID));
			
		
		  $data['result'] = true;
	
         if($echo) echo json_encode($data);
       
	}
    
    function send_order_IO()
    {
        
             $this->load->model('Mail_model');
             $r =  $this->paser->post('http://shipment.phantasia.com.tw/product_flow/get_order_IO/',array(),false);
        
		$time = getdate();
        	$mailTo = array('lintaitin@gmail.com,phantasia.odp@gmail.com');
		$this->db->reconnect();
        $headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
	echo $r;
        $this->Mail_model->groupEmail($mailTo,$time['year'].'/'.$time['mon'].'/'.$time['mday'].'營業匯報' ,$r,$headers);
			
        
        
    }
    
    function get_order_IO()
    {
           $this->load->model('Product_flow_model');
        $this->load->model('Product_model');
		 $this->load->model('Order_model');
      //   $_POST['from'] = '2019-01-01';
      //   $_POST['to'] = '2019-01-31';
        $from = $this->input->post('from');
        if($from=='') $from = date('Y-m').'-1';
        $toDate = $this->input->post('to');
        if($toDate=='') $toDate = date('Y-m').'-31';    
  
        $data['shipmentList'] = $this->Order_model->getShipmentList(0,0,99999,15,0 ,$from,$toDate);
        $total = 0 ;
       $joinTotal = 0;
        $nonJointotal = 0;
       $directShop = $this->System_model-> getDirectShop();
        foreach( $data['shipmentList'] as $row)
        {
            $countKey  = true;
            foreach($directShop as $each)
            {
                
                if($each['shopID']==$row['shopID'])
                {
                    $countKey  = false;
                    
                }
                
            }
            
            if($countKey)
            {
                if($row['distributeType']==1|| $row['distributeType']==20 || $row['distributeType']==15) $joinTotal+=$row['total'];
                else $nonJointotal +=$row['total'];
                
                $total+=$row['total'];
            }
            
        }
        $result['orderTotal'] = $total;
        $result['joinTotal'] = $joinTotal;
        $result['nonJointotal'] = $nonJointotal;
        
        $total = 0;
        foreach($directShop as $row)
        {
          if($row['shopID']==0) continue;
            $t = explode('-',$from);
           $datain['shopID'] = $row['shopID'];
            $datain['year'] = $t[0];
            $datain['mon'] =$t[1];
       ;
           
            $r =  $this->paser->post('http://shipment.phantasia.com.tw/accounting/get_month_verify/',$datain,true);
           $total +=$r['monTotal'];
           $result['directShop'][] = array('shopID'=>$row['shopID'],'monTotal' =>$r['monTotal']);
        }
            
          $result['directShopTotal'] =  $total;   
        
            $data['purchaseList'] = $this->Product_model->getPurchaseList(0,99999,'purchase',0,0,0,$from.' 00:00:00',$toDate." 23:59:59");
            $total = 0 ;
            foreach($data['purchaseList'] as $row )
            {

                $total+=$row['total'];

            }

             $result['purchaseTotal'] = $total;
        
        
        
          $data['purchaseOrderList'] = $this->Product_model->getPurchaseList(0,99999,'purchaseOrder',0,50,0,$from.' 00:00:00',$toDate." 23:59:59");
            $total = 0 ;
            foreach($data['purchaseOrderList'] as $row )
            {

                $total+=$row['total'];

            }
         $result['purchaseOrderTotal'] = $total;
        
        $income = $result['orderTotal']+$result['directShopTotal'];
        $outcome = $result['purchaseTotal']+$result['purchaseOrderTotal'];
        echo '　joinOrderTotal:'. $result['joinTotal'].'<br/>';
        echo '　nonjoinOrderTotal:'. $result['nonJointotal'].'<br/>';
        echo 'orderTotal:'.$result['orderTotal'].'<br/>';
        echo '-------------------<br/>';
        foreach( $result['directShop'] as $row)
        {
            
            echo '　['.$row['shopID'].']'.$row['monTotal'].'<br/>';
            
        }
        
        
        
        echo 'directShopTotal:'.$result['directShopTotal'].'<br/>';
        echo '_________________<br/>';
        echo 'incomeTotal:'.$income.'<br/>';
        echo  '<br/>';
        echo '<br/>';
        echo 'purchaseTotal:'.$result['purchaseTotal'].'<br/>';
        echo 'purchaseOrderTotal:'.$result['purchaseOrderTotal'].'<br/>';
        echo '_________________<br/>';
        echo 'outcomeTotal:'.$outcome.'<br/>';
        echo  '<br/>';
        echo '<br/>';
        
     
        
    }
    
    
	function pos_account_io()
    {
        $this->load->model('Product_flow_model');
        $this->load->model('Product_model');
		 $this->load->model('Order_model');
		$testToken = false;
        if($this->input->get('key'))
        {
          $_POST['from'] = $this->input->get('from');
          $_POST['to']   = $this->input->get('to');
   
            $_POST['shopID'] = $this->input->get('shopID');
   
           $_POST['productID'] = $this->input->get('productID');
    $testToken = $this->input->get('testToken');;
        }
		/* */
 /*
		 $_POST['date'] = '2018-1-2';
		 $_POST['byDay'] = '1';
		
      */
        
        $productID = $this->input->post('productID');
    	
       
        
        
		if($this->input->post('byDay')==1)
		{
			
			$from = $this->input->post('date').' 00:00:00';;
			 $to = $this->input->post('date').' 23:59:59';
			
			$t = explode('-',$this->input->post('date'));
			$year = $t[0];
			$month = $t[1];
		
			$day = $t[2];
			$isCosignment = 0;
			
		}
		else
		{
			$t = explode('-',$this->input->post('from'));
			$year = $t[0];
			$month = $t[1];
			$thisYear = $year;
			$thisMonth = $month;
			$day = 0;
			    $from = $this->input->post('from').'-01 00:00:00';
        		$to = $this->input->post('from').'-31 23:59:59';
			$isCosignment = -1;
		}
    
       
        
        $shopID = $this->input->post('shopID');
        if(empty($shopID))$shopID = 0;
        
    	
		$dataString = '';
		if($shopID==0)
		{
			
			
			
				$dataString = $this->Product_flow_model-> getAccountingData($productID,$thisYear,$thisMonth);
				
			
			
			
			
		}
		
          $this->db->where('productID',$productID);
	
		$query = $this->db->get('pos_product_consignment');
        $c = $query->row_array();
        if(!empty($c))
        {
            
           if(strtotime($c['startConsignment'])<= strtotime($from)) $dataString = json_encode(array('result'=>true)); //jump
           else if(strtotime($c['startConsignment']) <strtotime($to)) $to = date('Y-m-d H:i:s',strtotime($c['startConsignment'])-1);      
            
        }
        
		if($dataString == '')
		{
         
            
            
            
            $data = $this->productIOCaculate($productID,$from,$to,$day,$isCosignment);
         
			$data['result'] = true;	
			$dataString =  json_encode($data);
			 
			if($shopID==0)
			{
			
			 if($testToken)$this->db->insert('pos_test',array('content'=>'f'.date('Y-m-d H:i:s')));
			
				if(isset($data['mulity'])) 
                {
                    foreach($data['product'] as $row)
                    {
                        
                        $row['result'] = true;
                        $ds = json_encode($row);
                      
                        $this->Product_flow_model-> insertAccountData($row['inf']['productID'],$thisYear,$thisMonth,$ds,1);
                       $this->do_pos_account_io_update($thisYear.'-'.$thisMonth,$row['inf']['productID'],false);
                    }
                    
                }
                else   
                {
                    $this->Product_flow_model-> insertAccountData($productID,$thisYear,$thisMonth,$dataString);
                     $this->do_pos_account_io_update($thisYear.'-'.$thisMonth,$productID,false);
                }
				
			
			
			
			
			}
		}
        
          
          echo $dataString;
    }
    
    
    function  productIOCaculate($productID,$from,$to,$day,$isCosignment,$inNum = 0,$inCost =0,$package =false,$packageArray=array())
    {
         $printTest = $this->input->get('testToken');;;
     
              if($printTest) echo $productID.',$inNum:'.$inNum.',$inCost:'.$inCost.',$package:'. $package;
            if($printTest)    print_r($packageArray);
             if($printTest)  echo '<br>';;
        
        
        $testToken = false;
        
        $GiveUpToken = false;
            
            
        if($testToken)$this->db->insert('pos_test',array('content'=>'a'.date('Y-m-d H:i:s')));
            
			$data['from'] = $from;
			$data['to']   = $to;
                
        if($from=='2019-10-01 00:00:00')
        {
            $GiveUpToken = true;
            $GiveUpShopID = 36;
        }
			$this->db->order_by('shopID','asc');
			$query = $this->db->get('pos_direct_branch');
			$data['shop']= $query->result_array();
			$fromDate = strtotime($from);
			$fromlast =  date('Y-m-d H:i:s', strtotime('-1 sec', $fromDate));

			$data['o_all'] = array('num'=>0,'avgCost'=>0) ; 
			$data['s_all'] = array('num'=>0,'sellPrice'=>0) ; 

            if($testToken) $this->db->insert('pos_test',array('content'=>'b'.date('Y-m-d H:i:s')));

			  //進 只需要看公司
			$data['p'][0] = $this->Product_flow_model->getAccountPurchase($from,$to,0,$productID);
             if($printTest) print_r($data['p'][0] );
            // 頂讓進貨  量  價
            
        
           if($GiveUpToken)
           {
            $this->db->where('productID',$productID);
            $q = $this->db->get('pos_migrate');
            $migrate = $q->row_array();
            
            if(!empty($migrate) && $migrate['sellNum']>0)
            {
                if(($data['p'][0]['purchaseNum']+$migrate['sellNum'])!=0)
                 $data['p'][0]['purchasePrice'] =   
                     ($data['p'][0]['purchasePrice']*$data['p'][0]['purchaseNum']+$migrate['sellPrice']) /
                     ($data['p'][0]['purchaseNum']+$migrate['sellNum']);
                else $data['p'][0]['purchasePrice'] = 0;
                $data['p'][0]['purchaseNum'] += $migrate['sellNum'];
                     
            }
           }
           
            
            
            
			$totalCost  = 0;
			$i = 0;
	
			foreach($data['shop'] as  $row)
			{
				if($row['shopID']==993 && $isCosignment == 0) 
				{
					unset($data['shop'][$i++]);

						continue;
				}
				$i++;
					//存
				if($day<=1)
				{
					 $data['o'][$row['shopID']] = $this->Product_flow_model->getAccountAmountByParam($fromlast,$row['shopID'],$productID);


				}
				else
				{

					 $data['o'][$row['shopID']] = $this->Product_flow_model->getAccountAmountByParamByDay($fromlast,$row['shopID'],$productID);


				}
				$cost = $data['o'][$row['shopID']]['num'] * $data['o'][$row['shopID']]['avgCost'];;
				$totalCost += $cost;
				$data['o_all']['num'] +=$data['o'][$row['shopID']]['num'];
				
			}
			
			if( $data['o_all']['num']!=0)
			$data['o_all']['avgCost'] =$totalCost / $data['o_all']['num'] ;
			else $data['o_all']['avgCost'] = 0;
			 if($testToken)$this->db->insert('pos_test',array('content'=>'c'.date('Y-m-d H:i:s')));
			$totalCost = 0;
			foreach($data['shop'] as $row)
			{

                    		 if($testToken)	$this->db->insert('pos_test',array('content'=>$row['shopID'].'c'.date('Y-m-d H:i:s')));

				if($row['shopID']==993 && $isCosignment == 0) 
				{
					unset($data['shop'][$i++]);

						continue;
				}
				$i++;
				//對直營而言 所有退回公司的退貨 都不會記帳
				  $data['b'][$row['shopID']] = $this->Product_flow_model->getBack($from,$to,$row['shopID'],$productID,$isCosignment);

				//對直營而言 這裡是所有調出的貨單 視為出貨
				 $data['a'][$row['shopID']] = $this->Product_flow_model->getAdjust($from,$to,$row['shopID'],$productID,0,$isCosignment);

				//銷 對於直營而言 ，此處昰公司出貨單
				if($isCosignment==0)$type= -2;
				else $type= $isCosignment;
				
                $data['s'][$row['shopID']] = $this->Product_flow_model->getShipmentOut($from,$to,$row['shopID'],$productID,$type);
               
                
                // 頂讓出貨處裡
                
              if($GiveUpToken)
           {
                if($row['shopID']==$GiveUpShopID)
                {
                     
                     if(!empty($migrate) && $migrate['sellNum']>0)
                    {
                       
                         $data['s'][$row['shopID']]['sellPrice'] += $migrate['sellPrice'];    
                         $data['s'][$row['shopID']]['sellNum'] +=  $migrate['sellNum'];
                         
                         //echo $row['shopID'].','.$data['s'][$row['shopID']]['sellNum'].'<br/>';
                       
                         
                        // $data['s_all']['num']+=	$migrate['sellNum']; 
			             //$data['s_all']['sellPrice']	 += $migrate['sellPrice']; 
                         ///======////
                    }
                      
                }
                  if($row['shopID']==0)
                  {
                        if(!empty($migrate) && $migrate['sellNum']>0)
                        {
                        //頂讓視同總公司出貨
                         $data['s'][0]['sellPrice'] += $migrate['sellPrice'];    //總公司出貨
                         $data['s'][0]['sellNum'] +=  $migrate['sellNum'];       //總公司出貨
                         // $data['s_each'][0]['sellPrice'] += $migrate['sellPrice'];    //總公司出貨
                         //$data['s_each'][0]['num'] += $migrate['sellNum']; 
                        }
                      
                      
                  }
                
              }

				$data['cs'][$row['shopID']] = $this->Product_flow_model->getSell($from,$to,$row['shopID'],$productID);

				//店面客戶退貨
				$data['cb'][$row['shopID']] = $this->Product_flow_model->getCustomerBack($from,$to,$row['shopID'],$productID);





			//存
			if($day<=1)
			{
				 $data['o'][$row['shopID']] = $this->Product_flow_model->getAccountAmountByParam($fromlast,$row['shopID'],$productID);


			}
			else
			{

				 $data['o'][$row['shopID']] = $this->Product_flow_model->getAccountAmountByParamByDay($fromlast,$row['shopID'],$productID);


			}
		   //期初
		   // $data['e'][$row['shopID']] = $this->Product_flow_model->getAccountAmountByParam($to,$row['shopID'],$productID);//期末

			 /*
			期初
			1.總存(公司+直營+盒損)


			進貨
			1. 公司進貨


			銷貨
			1.公司所有銷 - 直營所有進(含直營調入) - 所有調貨 + 直營所有調出 + 直營所有銷 - 直營店面所有退
			2.公司所有退 - 直營所有退 
			1-2


			期末存貨
			1.總存(公司+直營+盒損)

			*/
			  /*
			期初
			1.總存(公司+直營+盒損)
			*/
			
			$data['o_each'][$row['shopID']]['num'] =$data['o'][$row['shopID']]['num'];
			$data['o_each'][$row['shopID']]['avgCost'] =$data['o'][$row['shopID']]['avgCost'];

			/*
			進貨
			1. 公司進貨
			*/
			if($row['shopID']==0)
			{
				$data['p_all']['num'] =$data['p'][$row['shopID']]['purchaseNum'];
				$data['p_all']['purchasePrice'] =$data['p'][$row['shopID']]['purchasePrice'];
                
				$data['p_each'][$row['shopID']]['num'] =$data['p_all']['num'];
				$data['p_each'][$row['shopID']]['purchasePrice'] =$data['p_all']['purchasePrice'];	
                
                
                if($printTest)echo $productID.'purchaseNum:'. $data['p'][$row['shopID']]['purchaseNum'].'<br/>';
                
    


			}
			else if($row['shopID']==993 ) //寄賣區
			{
				//取得所有寄賣出貨單 即是進貨單
				$data['consignment_s'] = $this->Product_flow_model->getShipmentOut($from,$to,0,$productID,1);//consignment;

				//print_r($data['consignment_s'] );
				$data['p_each'][$row['shopID']]['num']  = $data['consignment_s']['sellNum'] ;
				if($data['consignment_s']['sellNum']!=0)	$data['p_each'][$row['shopID']]['purchasePrice'] =$data['o_all']['avgCost'];
				else $data['p_each'][$row['shopID']]['purchasePrice'] = 0;

				// -寄賣退貨
				$consignment_back = $this->Product_flow_model->getBack($from,$to,0,$productID,1);

				$data['p_each'][$row['shopID']]['num'] -=	$consignment_back['backNum'];
				$data['p_each'][$row['shopID']]['purchasePrice'] -=$consignment_back['purchasePrice'];


			}
			else	
			{
                
             
            
                
				//直營所有進(含直營調入)-直營所有退
				$data['p_each'][$row['shopID']]['num'] =$data['s'][$row['shopID']]['sellNum']-$data['b'][$row['shopID']]['backNum'];



				if($data['p_each'][$row['shopID']]['num']>0)
				{
					
						$data['p_each'][$row['shopID']]['purchasePrice'] = 
							($data['s'][$row['shopID']]['sellPrice']-$data['b'][$row['shopID']]['purchasePrice'])/$data['p_each'][$row['shopID']]['num'];
							
					

				//	$data['p_each'][$row['shopID']]['purchasePrice'] = $data['o_all']['avgCost'];

				}
				else if($data['p_each'][$row['shopID']]['num']<0)
				{
					$data['p_each'][$row['shopID']]['purchasePrice'] = 
							($data['s'][$row['shopID']]['sellPrice']-$data['b'][$row['shopID']]['purchasePrice'])/$data['p_each'][$row['shopID']]['num'];


				}

				else
				{
					$data['p_each'][$row['shopID']]['purchasePrice'] = 0;


				}

				//
				//


			}


			/*
			銷貨
			1.公司所有銷 - 直營所有進(含直營調入) - 所有調貨 + 直營所有調出 + 直營所有銷 - 直營店面所有退
			2.公司所有退 - 直營所有退 
			1-2
			公司所有銷 - 所有調貨 - 公司所有退
			-直營所有進(含直營調入)+直營所有調出+ 直營所有銷 - 直營店面所有退
			+直營所有退	

			*/

			if($row['shopID']==0)
			{
				//公司所有銷 - 所有調貨 
				$data['s_each'][$row['shopID']]['num']=  $data['s'][$row['shopID']]['sellNum']
										-$data['a'][$row['shopID']]['adjustNum'];

				$data['s_each'][$row['shopID']]['sellPrice']=  $data['s'][$row['shopID']]['sellPrice']
											   -$data['a'][$row['shopID']]['purchasePrice'];






			}
			else if($row['shopID']==993)	
			{


				$consignment_adjust =  $this->Product_flow_model->getAdjust($from,$to,0,$productID, 0,1);//consignment

				//+寄賣調出
				$data['s_each'][$row['shopID']]['num'] = $consignment_adjust['adjustNum'];
				$data['s_each'][$row['shopID']]['sellPrice'] = $consignment_adjust['purchasePrice'];

				//+寄賣賣出

				$this->db->where('time >=',$from);
				$this->db->group_by('shopID');
				$query = $this->db->get('pos_consignment_amount');
				$cosignment_shop = $query->result_array();


				$t1 = getdate(strtotime($from));
				$t2 = getdate(strtotime($to));



				foreach($cosignment_shop as $eachShop)
				{

					$year = $t1['year'];
					$month = $t1['mon'];
					$key = true;

					while($key)	
					{

						$r= $this->Order_model->getConsignmentMonthCheck($eachShop['shopID'],$year,$month,1,$productID,0);
						if(!empty($r))
						foreach($r as $eachConsignment)
						{
							$data['s_each'][$row['shopID']]['num'] += $eachConsignment['consignmentNum'] -$eachConsignment['remainNum'];
							$data['s_each'][$row['shopID']]['sellPrice'] +=round($eachConsignment['purchasePrice']);



						}
						$month++;
						if($month>12)
						{
							$month=1;
							$year++;

						}
						if($year>$t2['year'] || ($year==$t2['year'] && $month >$t2['mon'] ) )
						{

							$key = false;
						}


					}

				}





				$data['s_all']['num'] -=$data['p_each'][$row['shopID']]['num'];
				
				$data['s_all']['sellPrice']-=$data['p_each'][$row['shopID']]['purchasePrice']*$data['p_each'][$row['shopID']]['num'];


			}
			else
			{
				//+直營所有調出+ 直營所有銷 - 直營店面所有退	

				$data['s_each'][$row['shopID']]['num']= 
														+$data['a'][$row['shopID']]['adjustNum']
														+$data['cs'][$row['shopID']]['sellNum']
														-$data['cb'][$row['shopID']]['customerBackNum'];


			   $data['s_each'][$row['shopID']]['sellPrice']=
														+$data['a'][$row['shopID']]['purchasePrice']
														+$data['cs'][$row['shopID']]['sellPrice']
														-$data['cb'][$row['shopID']]['sellPrice'];



                    //電商歸0
                if($row['shopID']==666)
                {
                    
                     $data['s_each'][$row['shopID']]['sellPrice'] += 0;    
                    $data['s_each'][$row['shopID']]['num'] +=  $data['o_each'][$row['shopID']]['num'];
                    
                    
                }


				$data['s_all']['num'] -=$data['p_each'][$row['shopID']]['num'];
				$data['s_all']['sellPrice']-=$data['p_each'][$row['shopID']]['purchasePrice']*$data['p_each'][$row['shopID']]['num'];

                
               
                



			}	
                
               if($testToken)  $this->db->insert('pos_test',array('content'=>$row['shopID'].'c1'.date('Y-m-d H:i:s')));

			//退貨記在盒損區( - 公司所有退 )
			if($row['shopID']==994)
			{
					$data['s_each'][994]['num'] = -$data['b'][0]['backNum'];
					$data['s_each'][994]['sellPrice'] = -$data['b'][0]['purchasePrice'];

			}




			$data['s_all']['num']+=	$data['s_each'][$row['shopID']]['num'];
			$data['s_all']['sellPrice']	+=$data['s_each'][$row['shopID']]['sellPrice'];

				if(($data['o_each'][$row['shopID']]['num'] +$data['p_each'][$row['shopID']]['num'])==0)$data['s_each'][$row['shopID']]['avgCost'] = 0;
				else
			$data['s_each'][$row['shopID']]['avgCost'] = 
				round(($data['o_each'][$row['shopID']]['num'] *$data['o_each'][$row['shopID']]['avgCost']
				+$data['p_each'][$row['shopID']]['num'] *$data['p_each'][$row['shopID']]['purchasePrice'])/($data['o_each'][$row['shopID']]['num'] +$data['p_each'][$row['shopID']]['num']),2 );


					if($data['s_each'][$row['shopID']]['num']!=0)
					{
						$data['s_each'][$row['shopID']]['profit'] = ($data['s_each'][$row['shopID']]['sellPrice']/$data['s_each'][$row['shopID']]['num']) -$data['s_each'][$row['shopID']]['avgCost'];
						if($data['s_each'][$row['shopID']]['num']>0 && $data['s_each'][$row['shopID']]['sellPrice']>0)
						$data['s_each'][$row['shopID']]['profitRatio'] = 100* round($data['s_each'][$row['shopID']]['profit']/($data['s_each'][$row['shopID']]['sellPrice']/$data['s_each'][$row['shopID']]['num']) ,2)	;
						else $data['s_each'][$row['shopID']]['profitRatio'] = 0;

						$data['s_each'][$row['shopID']]['eachSellPrice'] = $data['s_each'][$row['shopID']]['sellPrice']/$data['s_each'][$row['shopID']]['num'];
					}
				else
				{
					$data['s_each'][$row['shopID']]['eachSellPrice'] = 0;
					$data['s_each'][$row['shopID']]['profit'] = 0;
					$data['s_each'][$row['shopID']]['num'] = 0;	

				}

                

			$data['e_each'][$row['shopID']]['num'] = $data['o_each'][$row['shopID']]['num'] 
													+ $data['p_each'][$row['shopID']]['num'] 
													- $data['s_each'][$row['shopID']]['num'] ;
                 if($testToken&&$row['shopID']==0)$this->db->insert('pos_test',array('content'=>'o'.$data['o_each'][$row['shopID']]['num'].',p'.$data['p_each'][$row['shopID']]['num'].',s'.$data['s_each'][$row['shopID']]['num']));
                
                
				if($data['e_each'][$row['shopID']]['num']!=0)	
			$data['e_each'][$row['shopID']]['avgCost'] =
					($data['o_each'][$row['shopID']]['num']*$data['o_each'][$row['shopID']]['avgCost'] + $data['p_each'][$row['shopID']]['num']*$data['p_each'][$row['shopID']]['purchasePrice'] - $data['s_each'][$row['shopID']]['num'] *$data['s_each'][$row['shopID']]['avgCost'] )/$data['e_each'][$row['shopID']]['num'];

				else $data['e_each'][$row['shopID']]['avgCost'] = 0;





			$data['e_each'][$row['shopID']]['totalCost']  =$data['e_each'][$row['shopID']]['avgCost']*$data['e_each'][$row['shopID']]['num'] ;

				$cost = $data['o'][$row['shopID']]['num'] * $data['o_each'][$row['shopID']]['avgCost'] ;
				$totalCost += $cost;

				$e_cost = $data['e_each'][$row['shopID']]['num'] * $data['e_each'][$row['shopID']]['avgCost'];





			}






             if($testToken)         $this->db->insert('pos_test',array('content'=>$row['shopID'].'c2'.date('Y-m-d H:i:s')));

			if(($data['o_all']['num'] +$data['p_all']['num'])==0)$data['s_all']['avgCost']=0;
			else	
			$data['s_all']['avgCost'] = 
	round(($data['o_all']['num'] *$data['o_all']['avgCost']
				+$data['p_all']['num'] *$data['p_all']['purchasePrice'])/($data['o_all']['num'] +$data['p_all']['num']),2 );


			if($data['s_all']['num']!=0&& $data['s_all']['sellPrice']!=0)	
			{

				$data['s_all']['eachSellPrice'] = $data['s_all']['sellPrice'] /$data['s_all']['num'];
				$data['s_all']['profit'] = ($data['s_all']['sellPrice']/$data['s_all']['num']) -$data['s_all']['avgCost'];
				$data['s_all']['profitRatio'] =  100*round($data['s_all']['profit']/($data['s_all']['sellPrice']/$data['s_all']['num']) ,2)	;

			}

			else
			{

				$data['s_all']['profit'] = 0;
				$data['s_all']['profitRatio'] = 0;
				$data['s_all']['eachSellPrice'] = 0;
			}






			/*
			期末
			1.總存(公司+直營+盒損)
			*/
        //
		      
          $data['e_all']['num'] = $data['o_all']['num'] + $data['p_all']['num'] - $data['s_all']['num'] ;
           if(($data['o_all']['num']+$data['p_all']['num'])!=0)
        $data['e_all']['avgCost'] = ($data['o_all']['num']*$data['o_all']['avgCost']+$data['p_all']['num']*$data['p_all']['purchasePrice'])/($data['o_all']['num']+$data['p_all']['num']);
           else $data['e_all']['avgCost'] = 0;  
             
             
         $data['e_all']['totalCost'] = $data['e_all']['num'] * $data['e_all']['avgCost'] ;
		
        
                //盒進包出調整
            if(  $inNum!=0)
            {
                $key = false;
                if($package!=false)
                {
                    $t = 0;    
                 foreach($packageArray as $p)
                 {
                     $t+=$p;
                     
                 }
                    if($t>0)   $key = true;
                }
                //else if( $data['e_all']['num']<0)$key = true;

                    
                while($key)
                {
                    if(($inNum+$data['p_all']['num'])!=0)
                    $data['p_all']['purchasePrice'] = ($data['p_all']['purchasePrice']* $data['p_all']['num']+ $inNum*$inCost)/($inNum+$data['p_all']['num']);
                     else $data['p_all']['purchasePrice'] = 0;
                   
                    
                    

                    $data['p_all']['num'] += $inNum;            
                    if($printTest && $package==true)echo $productID.'p_all:'. $data['p_all']['num'].'t:'.$t.'<br/>';
                    $data['e_all']['num'] += $inNum;
                    $data['e_all']['totalCost'] += $inNum * $inCost;
                    if($data['e_all']['num']!=0)
                    $data['e_all']['avgCost'] =  $data['e_all']['totalCost'] /$data['e_all']['num'];
                    else $data['e_all']['avgCost'] = 0;    
                    if($package==true) 
                    {
                        $t--;
                        if($t>0)   $key = true;
                        else $key =false;
                        
                    }
                    else if( $data['e_all']['num']>=0 )$key = false;//跑到存貨為正

                }
                if(($data['o_all']['num']+$data['p_all']['num'])==0) $data['s_all']['avgCost'] = 0;
                else
                {
                 $data['s_all']['avgCost'] = ($data['o_all']['avgCost']* $data['o_all']['num']+  $data['p_all']['purchasePrice']* $data['p_all']['num'] )/( $data['o_all']['num']+$data['p_all']['num']);
                }
            }
        
       
        
     
       
         
                 if($testToken)$this->db->insert('pos_test',array('content'=>'d'.date('Y-m-d H:i:s')));
			foreach($data['shop'] as $row)
			{
               
				if($data['e_each'][$row['shopID']]['num']!=0)
				{
				$data['e_each'][$row['shopID']]['avgCost'] = $data['e_all']['avgCost'];
				$data['e_each'][$row['shopID']]['totalCost'] = $data['e_all']['avgCost'] *$data['e_each'][$row['shopID']]['num'];
                 
                    
                     //盒進包出調整
                    if(  $inNum!=0)
                    { 
                         if($printTest)echo 'shopID:'.$row['shopID'].'e_each:'.$data['e_each'][$row['shopID']]['num'].'<br/>';
                        $key = false;
                        if($package!=false)
                        {
                            $key = true;
                            if(!isset($packageArray[$row['shopID']])) $key = false;
                        }
                        else if($data['e_each'][$row['shopID']]['num']<0)$key = true;
                        
                       
                        $t= 0;
                        while($key)
                        {
                            
                            if($inNum+$data['p_each'][$row['shopID']]['num']!=0)
                            {
                            $data['p_each'][$row['shopID']]['purchasePrice'] = ($data['p_each'][$row['shopID']]['purchasePrice']* $data['p_each'][$row['shopID']]['num']+ $inNum*$inCost)/($inNum+$data['p_each'][$row['shopID']]['num']);
                            }
                            else  $data['p_each'][$row['shopID']]['purchasePrice'] = 0;
                             
                            $data['p_each'][$row['shopID']]['num'] += $inNum;
                                
                            
                            if($printTest)echo $productID.',shopID:'.$row['shopID'].',num:'.$data['e_each'][$row['shopID']]['num'].'peach:'.$data['p_each'][$row['shopID']]['num'].'purchasePrice'.$data['p_each'][$row['shopID']]['purchasePrice'].'<br>';
                            
                          
                            
                            $data['e_each'][$row['shopID']]['num'] += $inNum;
                            $data['e_each'][$row['shopID']]['totalCost'] += $inNum * $inCost;
                            if($data['e_each'][$row['shopID']]['num']!=0)
                            $data['e_each'][$row['shopID']]['avgCost'] =   $data['e_each'][$row['shopID']]['totalCost'] / $data['e_each'][$row['shopID']]['num'];
                            else 
                            { 
                                $data['e_each'][$row['shopID']]['avgCost'] = 0;
                                $data['e_each'][$row['shopID']]['totalCost'] = 0;
                            }
                             if($printTest)echo  'avgCost'.$data['e_each'][$row['shopID']]['avgCost'].'totalCost'. $data['e_each'][$row['shopID']]['totalCost'].'</br>';
                            
                            
                             if($package!=true) 
                            {
                                  if($printTest)echo 'package false<br/>';
                                $data['p_all']['purchasePrice'] = ($data['p_all']['purchasePrice']* $data['p_all']['num']+ $inNum*$inCost)/($inNum+$data['p_all']['num']);

                                if($printTest)echo $productID.'p_all:'. $data['p_all']['num'].'t:'.$t.'<br/>';
                

                                $data['p_all']['num'] += $inNum; 
                                 
                               
                             }
                            
                            
                            $t++;
                            if($printTest && $package!=false) echo '$packageArray'.$packageArray[$row['shopID']].'t:'.$t.'<br/>';
                            if($package==true)
                            { 
                                if( $packageArray[$row['shopID']]<=$t) $key = false;//根據店家換盒數來跑
                            }
                            else if($data['e_each'][$row['shopID']]['num']>=0)$key = false;//跑到存貨為正
                            

                        }
                        $data['package'][$row['shopID']] = $t;
                        
                        
                        
                        
                 $data['e_all']['num'] =$data['o_all']['num']+$data['p_all']['num']-$data['s_all']['num'];
                        if($data['o_all']['num']+$data['p_all']['num']==0) $data['s_all']['avgCost'] = 0;
                         else
                           {
                 $data['s_all']['avgCost'] = ($data['o_all']['num']*$data['o_all']['avgCost']+
                                                        $data['p_all']['num']*$data['p_all']['purchasePrice'])   /($data['o_all']['num']+$data['p_all']['num']) ;
                           }
                 $data['e_all']['avgCost'] =  $data['s_all']['avgCost'];
                            if($printTest)echo  'allavgCost'.$data['e_all']['avgCost'].'totalCost'. $data['e_all']['totalCost'].'</br>';
                            
                    
                                  if($printTest)echo $productID.'totalCost:'. $data['e_all']['totalCost'].'<br/>';
                        
                      
                        
                    }
                    
                    $data['e_all']['totalCost']  = $data['e_all']['avgCost']*$data['e_all']['num'] ;
                    
                    
				}
				else 
				{
					$data['e_each'][$row['shopID']]['avgCost'] = 0;
					$data['e_each'][$row['shopID']]['totalCost'] = 0;
				}
				if($data['s_each'][$row['shopID']]['num']>0)
				{
					$data['s_each'][$row['shopID']]['avgCost'] = $data['e_all']['avgCost'];
					$data['s_each'][$row['shopID']]['totalCost'] = $data['e_all']['avgCost'] * $data['s_each'][$row['shopID']]['num'];
				
					
				}
				else
				{
					$data['s_each'][$row['shopID']]['avgCost'] = 0;
					$data['s_each'][$row['shopID']]['totalCost'] = 0;
					
				}
				  $data['e_each'][$row['shopID']]['num'] =  
                                $data['o_each'][$row['shopID']]['num']+$data['p_each'][$row['shopID']]['num']-$data['s_each'][$row['shopID']]['num'];
                
                           if($data['o_each'][$row['shopID']]['num']+$data['p_each'][$row['shopID']]['num']==0)$data['s_each'][$row['shopID']]['avgCost'] = 0;
                           else
                           {
                 $data['s_each'][$row['shopID']]['avgCost'] = ($data['o_each'][$row['shopID']]['num']*$data['o_each'][$row['shopID']]['avgCost']+ $data['p_each'][$row['shopID']]['num']*$data['p_each'][$row['shopID']]['purchasePrice'])   /($data['o_each'][$row['shopID']]['num']+$data['p_each'][$row['shopID']]['num']) ;
                           }
                  if($printTest)echo  'savgCost'.$data['s_each'][$row['shopID']]['avgCost'].'totalCost'. $data['s_each'][$row['shopID']]['totalCost'].'</br>';
                
              
                 $data['e_each'][$row['shopID']]['totalCost']  = $data['e_each'][$row['shopID']]['avgCost']*$data['e_each'][$row['shopID']]['num'] ;
			}
       
          
        
                
        
			if($day>=1)
			{
                  if($testToken)$this->db->insert('pos_test',array('content'=>'e'.date('Y-m-d H:i:s')));
			  foreach($data['shop'] as $row)
				{

							   $datain = array(
						'productID' =>$productID,
						'year'      =>$year,
						'month'     =>$month,
						'amount'    =>$data['e_each'][$row['shopID']]['num'],
						'avgCost'   =>$data['e_all']['avgCost'],
						'totalCost' =>$data['e_each'][$row['shopID']]['num'] * $data['e_all']['avgCost'],
						'type'      =>2 , //1 for sale 2 for purchase
						'mainID'    =>0 , // orderID or purchaseID
						'shopID'    =>$row['shopID']

						);



						$datain['day'] = $day;
						$r = $this->Product_flow_model->getAccountAmountByParamByDay($year.'-'.$month.'-'.$day,$row['shopID'],$productID);
						if(isset($r['id']))	
						{
							$this->db->where('id',$r['id']);
							$this->db->update('pos_accounting_io_temp',$datain);
						}
						else $this->db->insert('pos_accounting_io_temp',$datain);






					}
			}
            $data['inf'] = $this->Product_model->getProductInfByProductID($productID);
             
            if($package==false)
            {
                //盒進包出確認
                  $ret = $this->Product_flow_model->checkPackage($productID);
               
                if($ret['result']==true)
                {
                    //有box
                    //包出
                    $unitData = $this->productIOCaculate($ret['data']['unitProductID'],$from,$to,$day,$isCosignment,$ret['data']['unitToBox'],$data['e_all']['avgCost']/$ret['data']['unitToBox']);
                      //  print_r($unitData);
                    if(isset($unitData['mulity']) && $unitData['mulity']==1)
                    {
                        foreach($unitData['product'] as $unit)
                        {
                            $result['product'][] = $unit;
                            $package = $unit;
                        }
                            
                        
                    }
                    else
                    {
                        $result['product'][] = $unitData;
                        $package = $unitData;
                    }
                    //盒進
                    if(!empty($package['package']))
                    {
                        $unitData =  $this->productIOCaculate($ret['data']['boxProductID'],$from,$to,$isCosignment,$day,-1,$data['e_all']['avgCost'],true,$package['package']);
                    
                    
                        if(isset($unitData['mulity']) && $unitData['mulity']==1)
                        {
                            foreach($unitData['product'] as $unit)
                            {
                                $result['product'][] = $unit;
                                $package = $unit;
                            }
                            
                        
                        }
                        else
                        {
                            $result['product'][] = $unitData;
                            $package = $unitData;
                        }
                        
                    }
                    else $result['product'][] = $data;
                    $result['mulity'] = 1;
                }
                else $result = $data;
                return $result;
            }
        
           return $data; 
			
        
    }
    
    
    function account_io_trans()
    {
        
        
        
        //find O
    }
    function purchase_in_table()
    {
              $this->load->model('Product_flow_model');
      
        $t = explode('-',$this->input->post('from'));
			$year = $t[0];
			$month = $t[1];
        $from = $this->input->post('from').'-01 00:00:00';
        $to = $this->input->post('from').'-31 23:59:59';
    	$p = $this->Product_flow_model->getAllPurchase($from,$to);
        
        $total = 0 ;
        
        $this->db->where('year',$year);
         $this->db->where('month',$month);
        $this->db->update('pos_accounting_table',array('P_amount'=>0,'P_totalCost'=>0));
        $c = $this->Product_flow_model->getProductConsignment($year,$month);
        foreach($c as $row)
        {
            //寄賣標記
            $this->db->where('productID',$row['productID']);
            $this->db->where('year',$year);
            $this->db->where('month',$month);
            $this->db->update('pos_accounting_table',array('consignment'=>1));
            
            
            
            
        }
        
        
        
        
        foreach($p as $row)
        {
            
         
            if(!isset($ps[$row['supplierID']]))
            {
                $ps[$row['supplierID']]['total'] =  0 ;
                $ps[$row['supplierID']]['name'] = $row['name'];
            }
            $stotal = $row['purchasePrice'] * $row['num'];
            if($row['tax']==0)   $stotal = round($stotal*1.05);
            $total += $stotal ; 
            $ps[$row['supplierID']]['total'] +=$stotal;  
            
            
            
            $this->Product_flow_model->insert_p_io($year,$month,$row['productID'],$row['num'],$stotal);
            
        }
        /*
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        print_r($ps);
        echo $total;
        */
              $result['result'] = true;
         echo json_encode($result);
        
        
        
    }
    function sell_in_table()
    {
              $this->load->model('Product_flow_model');
      $this->load->model('Order_model');
      
        $t = explode('-',$this->input->post('from'));
			$year = $t[0];
			$month = $t[1];
        $from = $this->input->post('from').'-01 00:00:00';
        $to = $this->input->post('from').'-31 23:59:59';
    	
        $directShop = $this->System_model-> getDirectShop();
                
        $this->db->where('year',$year);
         $this->db->where('month',$month);
        $this->db->update('pos_accounting_table',array('S_amount'=>0,'S_totalSellPrice'=>0,'move'=>0,'E_amount'=>0,'E_totalCost'=>0));
        
        $total = 0;
        // 全部出貨含調貨(出貨直營店視同調出公司)
        $s = $this->Product_flow_model->getShipmentOut($from,$to,0,0,-1);
        foreach($s as $row)
        {
            $key = true;
            foreach($directShop as $dirs)
            {
              
                if($dirs['shopID']==$row['shopID'])
                {
                    
                    $this->Product_flow_model->moveOut($year,$month,$row['productID'],$row['sellNum'],$row['shopID']);
                    $key = false;
                }
            }
            
            if($key) 
            {
                //寄賣
                if($row['type']==1) $this->Product_flow_model->moveOut($year,$month,$row['productID'],$row['sellNum'],993);
                else
                {
                    $this->Product_flow_model->insert_s_io($year,$month,$row['productID'],$row['sellNum'],$row['sellNum']*$row['sellPrice']);
                      $total += $row['sellNum']*$row['sellPrice'];
                }
              
            }
            
            
        }
      
          foreach($directShop as $row)
          {
              if($row['shopID']==0) continue;
              if($row['shopID']==993)
              {
                  
                   //寄賣賣出
          
				$this->db->where('time >=',$from);
				$this->db->group_by('shopID');
				$query = $this->db->get('pos_consignment_amount');
				$cosignment_shop = $query->result_array();

				$t1 = getdate(strtotime($from));
				$t2 = getdate(strtotime($to));
              $sellNum = 0 ; $sellTotal = 0;
				foreach($cosignment_shop as $eachShop)
				{

					$year = $t1['year'];
					$month = $t1['mon'];
					$key = true;
						$r= $this->Order_model->getConsignmentMonthCheck($eachShop['shopID'],$year,$month,1,0,0);
						if(!empty($r))
						foreach($r as $eachConsignment)
						{
							$sellNum += $eachConsignment['consignmentNum'] -$eachConsignment['remainNum'];
                            
							$sellTotal +=round($eachConsignment['purchasePrice']);



						}
			

				}

                  
              }
              else
              {
              
            // 直營店銷貨-店面退貨。
            
              $cs = $this->Product_flow_model->getSellproduct($from,$to,$row['shopID']);
              foreach($cs as $each)
              {
                 $this->Product_flow_model->insert_s_io($year,$month,$each['productID'],$each['sellNum'],$each['sellNum']*$each['sellPrice'],$row['shopID']);
                   $total += $each['sellNum']*$each['sellPrice'];
                  
              }
          
            //店面客戶退貨
				$cb = $this->Product_flow_model->getCustomerBack($from,$to,$row['shopID']);
             foreach($cb as $each)
              {
                 $this->Product_flow_model->insert_s_io($year,$month,$each['productID'],-$each['customerBackNum'],-$each['customerBackNum']*$each['sellPrice'],$row['shopID']);
                  //退貨時 數量寄負 總價也要計負
                  $total += -$each['customerBackNum']*$each['sellPrice'];
                  
              }
              }

    
              
          }
        // -全部調出 (直營店調出視同轉回公司)
        $a = $this->Product_flow_model->getAdjust($from,$to);
        foreach($a as $row)
        {
            //忽略寄賣品的調貨
             //寄賣
                if($row['isConsignment']==1) $this->Product_flow_model->moveOut($year,$month,$row['productID'],-$row['adjustNum'],993);
                else
                {
                    $this->Product_flow_model->insert_s_io($year,$month,$row['productID'],-$row['adjustNum'],-$row['adjustNum']*$row['purchasePrice']);
                     $total += -$row['adjustNum']*$row['purchasePrice'];
                }
             
            
            
            
        }
        
          
        
        
        // 退貨視為轉給 盒損區。
        
        //對直營而言 所有退回公司的退貨 都不會記帳
        $b = $this->Product_flow_model->getBack($from,$to);
        foreach($b as $row)
        {
            //忽略寄賣品的調貨
             //寄賣
                if($row['isConsignment']==1) $this->Product_flow_model->moveOut($year,$month,$row['productID'],-$row['backNum'],993);
                else
                {
                    $this->Product_flow_model->insert_s_io($year,$month,$row['productID'],-$row['backNum'],-$row['backNum']*$row['purchasePrice']);
                     $total += -$row['backNum']*$row['purchasePrice'];
                }
            
            
            $this->Product_flow_model->moveOut($year,$month,$row['productID'],$row['backNum'],994);
            
        }
        
     
        /*
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        print_r($ps);
        echo $total;
        */
       $result['result'] = true;
         echo json_encode($result);
        
        
        
    }
    function test_avg()
    {
        $this->load->model('Product_flow_model');
        $_POST['from'] = '2020-1';
        $t = explode('-',$this->input->post('from'));

        $year = $t[0];
        $month = $t[1];
        $from = $this->input->post('from').'-01 00:00:00';
        $to = $this->input->post('from').'-31 23:59:59';
        /*
    	$p = $this->Product_flow_model->getAllPurchase($from,$to);
        $total = 0;$X =array();
          foreach($p as $row)
            {
                $stotal = $row['purchasePrice'] * $row['num'];
                if($row['tax']==0)   $stotal = round($stotal*1.05);
                $total += $stotal ;
              if($row['supplierID']==193)
              {
              if(!isset($X[$row['purchaseID']]['total'] ))$X[$row['purchaseID']]['total'] =0;
               $X[$row['purchaseID']]['total'] +=$stotal;
              }

            } 
         foreach($X as $row) echo $row['total'].'<br>';
        echo $total;
          */  
        //print_r($p);

            
        
         $from = $this->input->post('from').'-01 00:00:00';
        $to = $this->input->post('from').'-31 23:59:59';
        $fString = $from;
        $b = $this->Product_flow_model->get_io($year,$month,0,-1);
            foreach($b as $row)
            {
                ;
               //;$r = $this->stockCorrect($fString,$row['productID'],0);// 必要時再開啟
                 $this->Product_flow_model->insert_o_io(2020,2, $row['productID'],$row['E_amount'], $row['E_totalCost'],$row['shopID']);
                
                
                
            }
   
   
        
        
        
    }
    
    function product_update()
    {
         $this->load->model('Product_flow_model');
        $year = $this->input->get('year');
        $month = $this->input->get('month');;
        $fString = $year.'-'.$month;
          $g = $this->Product_flow_model->get_io($year,$month,0,0);
        foreach($g as $row)
        {
            
             $this->db->where('productID',$row['productID']);
             $this->db->update('pos_product',array('buyPrice'=>$row['S_avgPrice']));
        
            $r = $this->stockCorrect($fString,$row['productID'],0);// 必要時再開啟
            
        }
         $sql = 'update pos_product set buyDiscount = buyPrice*100/price';
        $this->db->query($sql);
    }
    
    
    function result_in_table()
    {
          $this->load->model('Product_flow_model');
    
   
        $t = explode('-',$this->input->post('from'));
			$year = $t[0];
			$month = $t[1];
        
         $fString = $this->input->post('from');
        
        $offset = $this->input->post('offset');
      
         $g = $this->Product_flow_model->get_io($year,$month,0,-1,$offset,1000);
       
        $productID = 0;
        foreach($g as $row)
        {
          
            if($productID != $row['productID'])
            {
                 $productID = $row['productID'];
                $avg = $this->Product_flow_model->getAvgCost($year,$month,$productID);
                //$this->db->where('productID',$productID);
                
                //$this->db->update('pos_product',array('buyPrice'=>$avg));
        
               

            }
            $num = $row['O_amount']+$row['P_amount']+$row['move'] - $row['S_amount'];
            
           $this->Product_flow_model->insert_e_io($year,$month,$row['productID'],$num,$avg,$avg*$num,$row['shopID']);
           
       
           // $r = $this->stockCorrect($fString,$row['productID'],0);// 必要時再開啟
            
            if($num<0) $error[] = array('num'=>$num,'productID'=>$row['productID'],'shopID'=>$row['shopID']);
        }
        

        if(!empty($error))
        foreach($error as $row)
        {
            $package =  $this->Product_flow_model->checkPackage($row['productID'],false);
               
                
             if(!empty($package['data']))
            {
             
                 $u =ceil( (-$row['num'])/$package['data']['unitToBox'] );
                  
               
                $b = $this->Product_flow_model->get_io($year,$month,$package['data']['boxProductID'],$row['shopID']);    
                
                 
                 if(empty($b[0]['O_amount'])) $b[0]['O_amount'] = 0;
                 if(empty($b[0]['P_amount'])) $b[0]['P_amount'] = 0;
                 $base =  $b[0]['O_amount'] + $b[0]['P_amount']; 
                $avg = $b[0]['S_avgPrice'];
                 
                $this->Product_flow_model->insert_p_io($year,$month,$package['data']['boxProductID'],-$u ,-$u*$avg,$row['shopID']);
                $this->Product_flow_model->insert_e_io($year,$month,$package['data']['boxProductID'],-$u,$avg,-$avg*$u,$row['shopID']);
                 
                 $this->Product_flow_model->insert_p_io($year,$month,$row['productID'],$u*$package['data']['unitToBox'] ,$u*$avg,$row['shopID']);
                $this->Product_flow_model->insert_e_io($year,$month,$row['productID'],$u*$package['data']['unitToBox'],$avg/$package['data']['unitToBox'],$avg*$u,$row['shopID']);
                 
          }
        }
         $result['result'] = true;
         echo json_encode($result);
    }
    
    
   function show_all()
   {
         $this->load->model('Product_flow_model');
     $shopID = $this->input->post('shopID');
      
        $t = explode('-',$this->input->post('from'));
			$year = $t[0];
			$month = $t[1];
        $b = $this->Product_flow_model->get_io($year,$month,0,$shopID);    
              
       $productID = 0;
       foreach($b as $row)
       {
           if($row['productID']!=$productID)
           {
               $productID = $row['productID']; 
               $inf = $this->Product_model->getProductInfByProductID($productID);
            
               $all = array(
                   'O_amount' => 0 ,
                   'O_totalCost' => 0 ,
                   'P_amount'=>0,
                   'P_totalCost'=>0,
                   'move'=>0,
                   'S_amount'=>0,
                   'S_totalSellPrice'=>0,
                   'S_avgPrice'=>0,
                   'E_amount'=>0,
                   'E_totalCost'=>0
                   		
               );
               $result['product'][$productID]= array('inf'=>$inf,'all'=>$all);
               
                 
               
               
           }
           $result['product'][$productID]['all']['O_amount'] +=$row['O_amount'];
            
           $result['product'][$productID]['all']['O_totalCost'] +=$row['O_totalCost'];
           $result['product'][$productID]['all']['P_amount'] +=$row['P_amount'];
           $result['product'][$productID]['all']['P_totalCost'] +=$row['P_totalCost'];
           $result['product'][$productID]['all']['S_amount'] +=$row['S_amount'];
           $result['product'][$productID]['all']['S_totalSellPrice'] +=$row['S_totalSellPrice'];
           if($row['S_avgPrice'] !=0)
           $result['product'][$productID]['all']['S_avgPrice'] =$row['S_avgPrice'];
           $result['product'][$productID]['all']['E_amount'] +=$row['E_amount'];
           $result['product'][$productID]['all']['E_totalCost'] +=$row['E_totalCost'];
            $result['product'][$productID]['all']['move'] ='-';
           $result['product'][$productID]['each'][$row['shopID']] = $row;
           
           
       }
       
  
       $result['shop'] = $this->System_model-> getDirectShop();
        $result['result'] = true;
      echo json_encode($result);
       
       
       
   }
    
    function parse_product()
    {
        
        $this->db->where('sProductID',0);
         $this->db->where('productID !=','');
       $this->db->join('pos_order_detail','pos_order_detail.id = pos_order_shipment_detail.rowID');
        $this->db->order_by('pos_order_shipment_detail.id','desc');
        $this->db->limit(1000);
       // $this->db->order_by('pos_order_shipment_detail.id','DESC');
        $query = $this->db->get('pos_order_shipment_detail');
        $data = $query->result_array();
        $p = $this->input->get('p');
        $p++;
     
        $key = false; $result['result'] = false;
        foreach($data as $row)
        {
            
           // $this->db->where('id',$row['rowID']);
           // $query = $this->db->get('pos_order_detail');
           // $r = $query->row_array();
           // print_r($r);
            $this->db->where('rowID',$row['rowID']);
            $this->db->update('pos_order_shipment_detail',array('sProductID'=>$row['productID']));
            $key =true;
            $result['result'] = true;
            
        }
        
        
        
        
       echo json_encode($result);
        
    }
 
    
    
    
    function get_all_product()
    {
          $this->load->model('Product_flow_model');
       $t = explode('-',$this->input->post('from'));
			$year = $t[0];
			$month = $t[1];
       $g = $this->Product_flow_model->get_io($year,$month);
       $data['num'] =  count($g);
        $data['result'] = true;
        echo json_encode($data);
        
    }
    
    function sell_total_num_parse()
    {
        $sql="SELECT productID,sum(num) as num  FROM `pos_product_sell` group by productID ";
        $query = $this->db->query($sql);
        $data = $query->result_array();
	
		$i = 0;
        foreach($data  as $row)
        {
			if($row['productID']==0) continue;
			
            $row['timeStamp'] = date('Y-m-d H:i:s');
            $this->db->where('productID',$row['productID']);
            $query = $this->db->get('pos_product_sell_num');
            if($query->num_rows()>0)
            {
                $this->db->where('productID',$row['productID']);
                $this->db->update('pos_product_sell_num',$row);
            }
            else  $this->db->insert('pos_product_sell_num',$row);
            
           
        }
        echo 'done';
    }
    
  function get_sell_num()
  {
     
      
     $productIDList =  $_POST;
   
      
      foreach($productIDList as $row)
      {
          
          $this->db->where('productID',$row);
          $query = $this->db->get('pos_product_sell_num');
          $data = $query->row_array();
          if(isset($data['num']))
          {
              
              $output['product'][]=array('productID'=>$row,'num'=>$data['num']);
              
          }
          
          
      }
        $output['result'] = true;
        echo json_encode($output);
      
  }
    function get_all_list()
	{
		$shopID = $this->input->post('shopID');
		  $this->db->where('shopID',$shopID);
        $query = $this->db->get('pos_current_product_amount');
		 $data['product']= $query->result_array();
		$data['result'] = true;
		    echo json_encode($data);
      
	}
	
	
	function set_consigment_num()
	{
		$shopEach['shopID'] = 993;
		$sql="SELECT sum(num) as num,productID  FROM `pos_consignment_amount` WHERE `num` >= 1 AND `time` >= '2018-01-01 00:00:00'  AND `time` < '2018-02-01 00:00:00'  group by productID ORDER BY `pos_consignment_amount`.`productID`  DESC";
		$query = $this->db->query($sql);
		$ret =  $query->result_array();
		
		foreach($ret as $row)
		{
			 $this->db->where('shopID',0);
             $this->db->where('year(time)',2017);
             $this->db->where('productID',$row['productID']);
             $this->db->join('pos_product_purchase_inf','pos_product_purchase_inf.purchaseID=pos_product_purchase.purchaseID');
             $this->db->where('pos_product_purchase_inf.status = 3');
            $query = $this->db->get('pos_product_purchase');
            $purchaseData  = $query->result_array();
            $num = 0; $total = 0;
            foreach($purchaseData as $each)
            {

               $num += $each['num'];
               $total+=$each['purchaseTotal']; 

            }
     
            if($num!=0)   $avgCost = round($total/$num,2);
            else $avgCost = 0;
		
			$amount = $row['num'];
			if($amount>0 && $avgCost==0)
			{
			$productID = $row['productID'];
			$this->db->where('productID',$productID);
			$this->db->where('shopID',0);
			$this->db->where('purchasePrice >',0);
			$this->db->order_by('id','desc');
			$this->db->limit(1);
			$query = $this->db->get('pos_product_purchase');
			$r = $query->row_array();
			if(isset($r['purchasePrice']))	$avgCost =  $r['purchasePrice'];
			else $avgCost=  0 ;
			
		}
			
          
            $datain = array(
                'productID' =>$row['productID'],
                'year'      =>2017,
                'month'     =>12,
                'amount'    =>$amount,
                'avgCost'   =>$avgCost,
                'totalCost' =>$amount*$avgCost,
                'type'      =>2 , //1 for sale 2 for purchase
                'mainID'    =>0 , // orderID or purchaseID
                'shopID'    =>$shopEach['shopID']
            
            );
            $this->db->insert('pos_accounting_io',$datain);
			
			echo $productID;
		}
		
		
	}
    
    function   set_avarage_num() 
    {
		$shopEach['shopID'] = $this->input->post('shopID');;
		$row['productID'] = $this->input->post('productID');;
             $this->db->where('shopID',0);
             $this->db->where('year(time)',2017);
             $this->db->where('productID',$row['productID']);
             $this->db->join('pos_product_purchase_inf','pos_product_purchase_inf.purchaseID=pos_product_purchase.purchaseID');
             $this->db->where('pos_product_purchase_inf.status = 3');
            $query = $this->db->get('pos_product_purchase');
            $purchaseData  = $query->result_array();
            $num = 0; $total = 0;
            foreach($purchaseData as $each)
            {

               $num += $each['num'];
               $total+=$each['purchaseTotal']; 

            }
     
            if($num!=0)   $avgCost = round($total/$num,2);
            else $avgCost = 0;
			$year = 2017;
			$month = 12;
			$shopID = $shopEach['shopID'];
			$productID = $row['productID'];
			$sql = "select * from pos_product_amount
						where ((year= $year  and month<= $month )or year< $year) and shopID = $shopID and productID = $productID
					order by `year` desc,`month` desc limit 0,1";			 
		
			$query = $this->db->query($sql);
			$ret =  $query->row_array();	
		
		if(empty($ret)||empty($ret['num'])||$ret['num']=='')$amount =  0;
		if(empty($ret)||empty($ret['num'])||$ret['num']=='')$amount =  0;
		else  $amount= $ret['num'];
			
		
		
		if($amount>0 && $avgCost==0)
		{
			
			$this->db->where('productID',$productID);
			$this->db->where('shopID',0);
			$this->db->where('purchasePrice >',0);
			$this->db->order_by('id','desc');
			$this->db->limit(1);
			$query = $this->db->get('pos_product_purchase');
			$r = $query->row_array();
			if(isset($r['purchasePrice']))	$avgCost =  $r['purchasePrice'];
			else $avgCost=  0 ;
			
		}
			
          
            $datain = array(
                'productID' =>$row['productID'],
                'year'      =>2017,
                'month'     =>12,
                'amount'    =>$amount,
                'avgCost'   =>$avgCost,
                'totalCost' =>$amount*$avgCost,
                'type'      =>2 , //1 for sale 2 for purchase
                'mainID'    =>0 , // orderID or purchaseID
                'shopID'    =>$shopEach['shopID']
            );
            $this->db->insert('pos_accounting_io',$datain);
       
          $data['result'] = true;
		  echo json_encode($data);  
        
			
		
      
         
            
        
    }
    function product_io_correction()
	{
		/*
		$content = '年終盤點關閉中 年初請打開';
		$this->Mail_model->myEmail('lintaitin@gmail.com',"庫存校正通知-".date('Y-m-d H:i:s'),$content,$headers,0,99,1);
		/*年終盤點關閉
		*/
		$t = getdate();
        $this->db->distinct();

		$this->db->select('productID');
		$this->db->where('shopID',0);
		$this->db->where('time >=',date("Y-m-d H:i:s", mktime(0, 0, 0, $t['mon'], $t['mday']-1, $t['year'])));
		
		$query = $this->db->get('pos_product_IO');

		$pro = $query->result_array();
	
 		$this->load->model('Product_flow_model');
        $this->load->model('Product_model');
  
		
		
		$content = '';
		foreach($pro as $row)
		{
			
			
		  $f = getdate( mktime(0, 0, 0, $t['mon'], $t['mday']-1, $t['year']));
          $fString = $f['year'].'-'.$f['mon'];
    
         
            
        $shopID = 0;
        $productID = $row['productID'];
       
        $r = $this->stockCorrect($fString,$productID,0);
		$content.=$r['content'];
			
		}
		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
        mb_internal_encoding('UTF-8');
		
		echo $content;
		$this->Mail_model->myEmail('lintaitin@gmail.com',"庫存校正通知".date('Y-m-d H:i:s'),$content,$headers,0,99,1);
		
		
	}
    function corect()
    { 
        	
 		$this->load->model('Product_flow_model');
        $this->load->model('Product_model');
        $p = $this->uri->segment(4);
        if(!empty($p)) $pro[] = array('productID'=>$p);
        else
        {
        $this->db->distinct();

		$this->db->select('productID');
       
        $this->db->where('year',2019);
        $this->db->where('month >',8);
        $this->db->where('shopID',0);
        $query = $this->db->get('pos_product_amount');

		$pro = $query->result_array();
        
        }
   
        $content= '';
	   foreach($pro as $row)
		{
			
			

         
            $fString = $this->uri->segment(3);
        $shopID = 0;
        $productID = $row['productID'];
       
        $r = $this->stockCorrect($fString,$productID,0);
		$content.=$r['content'];
			
		}
		$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
        mb_internal_encoding('UTF-8');
		echo 'ss';
		echo $content;
		$this->Mail_model->myEmail('lintaitin@gmail.com',"庫存校正通知".date('Y-m-d H:i:s'),$content,$headers,0,99,1);
        
        
        
        
    }
    
    function stockCorrect($fString,$productID,$t)
    {
        	
 		$this->load->model('Product_flow_model');
       $this->load->model('Product_model');
        $this->load->model('PO_model');
        
  
         $from = $fString.'-01 00:00:00';
          $to = $fString.'-31 23:59:59';
 
            
        $shopID = 0;
     
       
        $data['from'] = $from;
        $data['to']   = $to;
        
        
                
        
        $x = explode('-',$from);
			$year = $x[0];
			$month = $x[1];
        
        $mon = date('m', strtotime($from));
        $year = date('Y', strtotime($from));
        $data['content'] = '';
        
        $lastmonth = $mon -1;
        $lastyear = $year;
        if($lastmonth==0)
        {
            $lastmonth =12;
            $lastyear--;
        }
        $r = $this->Product_model->getConsignmentAmount($lastyear,$lastmonth,$productID);
        if(empty($r)) 
        {
             $consignment = 0;
            $b = $this->Product_flow_model->get_io($year,$month,$productID,0);   //期初
            if($b[0]['close']==1)
            {
            $o =  $b[0]['O_amount'];
            $en = $b[0]['E_amount'];
            }
            else 
            {
                
                $or = $this->Product_flow_model->getAmountByParam($lastyear.'-'.$lastmonth,0,$productID);//期初
                $o = $or['num'];
                echo 'notOK';
            }
            echo 'open:'.$o.'<br>';
        }
        else
        {
            $consignment = 1;
            echo 'consignment';
            $lastfrom = $lastyear.'-'.$lastmonth.'-31 23:59:59';
            
            $or = $this->Product_flow_model->getAmountByParam($lastfrom,0,$productID);//期初
            //$er = $this->Product_flow_model->getAmountByParam($to,0,$productID);//期末
         
          
          $to = $fString.'-31 23:59:59';
            $o = $or['num'];
          
        }
        
        //當月份
        if($consignment||$mon==date('m') && $year ==date('Y') || (isset($b[0]['S_avgPrice']) &&$b[0]['S_avgPrice']==0)||$b[0]['close']==0)
        {
             $data['p'] = $this->Product_flow_model->getPurchase($from,$to,0,$productID);
       
            //$data['b'] = $this->Product_flow_model->getBack($from,$to,0,$productID);
             $data['b'] = array('backNum'=>0,'purchasePrice'=>0);
			//2018 0104 公司退貨已經全部算入 盒損區 因此在這邊不計算 總帳在計算	
			
			$data['a'] = $this->Product_flow_model->getAdjust($from,$to,0,$productID);
            //銷
             $data['s'] = $this->Product_flow_model->getShipmentOut($from,$to,0,$productID,-1);
      
            //存
            $fromDate = strtotime($from);
            
echo 'sss';
            $data['e'] = $this->Product_flow_model->getAmountByParam($to,0,$productID);//期末
        echo $data['e']['num'];
		      $totalIn = $data['p']['purchaseNum'] + $data['b']['backNum']+$data['a']['adjustNum'];
            if(( $o +$totalIn) == ($data['s']['sellNum']+ $data['e']['num']))
            {
                  $data['e'] =  $data['e']['num'];
                 $nowNum = $this->PO_model->getProductAmountInf(0,$productID,$from);
                  echo $from.'now'.$nowNum['num'] .'<br/>';
               $this->Product_model->updateNum($productID,  $data['e'] - $nowNum['num'],0); 
            }
            else
            {
                 
            //
                $e =  $o +$totalIn-$data['s']['sellNum'];
            echo 'correct'.$e .'<br/>';
                if($mon==date('m') && $year ==date('Y'))
                {
                    
                    $nowNum = $this->PO_model->getProductAmountInf(0,$productID,$from);
                  echo $from.'now'.$nowNum['num'] .'<br/>';
               $this->Product_model->updateNum($productID,$e - $nowNum['num'],0); 
                $j = 'now';
                }
                else
                {
                      $this->db->where('year',$year);
                $this->db->where('month',$mon);
                $this->db->where('shopID',0);
                $this->db->where('productID',$productID);
                $this->db->update('pos_product_amount',array('num'=>$e));
                 $j = 'old'; 
                    
                }
                
                
               
                
                
                $inf = $this->Product_model->getProductInfByProductID($productID);
                $data['content'] = $j.' '.$year.$mon.' '.$inf['ZHName'].'數量已經調整為'.$e.'<br/>';
            }
            
        }
        
        else
        {
           
           $data['e'] = $this->Product_flow_model->getAmountByParam($to,0,$productID);
            
            
            if($data['e'] ==  $en) $e = $en;
            else
            {
                
                $e = $en;
                $this->db->where('year',$year);
                $this->db->where('month',$mon);
                $this->db->where('shopID',0);
                $this->db->where('productID',$productID);
                $this->db->update('pos_product_amount',array('num'=>$e));
                 $j = 'old';

                $inf = $this->Product_model->getProductInfByProductID($productID);
                $data['e'] = $e;
                $data['content'] = $j.' '.$year.$mon.' '.$inf['ZHName'].'數量已經調整為'.$e.'<br/>';
               
                
            }
            
          
            
            
            
        }
         return $data;
        //進
 
        
    }
   
    
    
}


/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */