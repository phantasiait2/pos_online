<?php

class Csorder extends POS_Controller {

	function Csorder()
	{
		parent::POS_Controller();
		$this->data['css'] = $this->preload->getcss('shipment');
		/*
		if(!$this->System_model->chkShop($this->input->post('shopID'),$this->input->post('licence')))
		{
			$data['result']	 = false;
			echo json_encode($data);
			exit(1);
			
		}
			*/
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
        else 
        {
            if($this->data['shopID']!=0) return;
            else  redirect('/');
        }
            
		return;
		
	}
	function index()
	{
		$this->data['css'] = $this->preload->getcss('shipment');
		$this->data['display'] = 'order';
		$this->load->view('template',$this->data);	
	}
    
	function mart_save_num()
	{
		$this->load->model('Order_model');
		$this->load->model('Product_model');
		$this->load->model('cs_order_model');
		
		
		$postData = $this->input->post('postData');
		$productData = json_decode($postData,true);
		
		foreach($productData as $row)
		{
			$productList[] = $row['productID'];
			$datain['productID'] = $row['productID'];
            $p = $this->Product_model->chkProductByProductID($datain['productID']);
            if($p['flowNum']>3)	$datain['num'] = 3;//電商安全量
            else if($p['flowNum']>0) 	$datain['num'] = $p['flowNum'];
            else $datain['num'] = 0;
            
			$datain['timestamp'] = date('Y-m-d H:i:s');
			
            
			$this->db->where('productID',$row['productID']);
			$query = $this->db->get('pos_mart_save_amount');
			$r = $query->row_array();
			if(!empty($r))
			{
				$this->db->where('productID',$row['productID']);
				$this->db->update('pos_mart_save_amount',$datain);
			}
			else $this->db->insert('pos_mart_save_amount',$datain);

		}
			
		$this->cs_order_model->examRemainNum($productList);
		
		$data['result'] = true;
		echo json_encode($data);
	}

    function webview()
    {
        
      
        $this->data['display'] = 'csorder_webview';
        $this->load->view('template',$this->data);	
      
    }
    
    function orderlist()
    {
        $this->iframeConfirm();
     
       
           $finish = $this->uri->segment(3);
        $this->data['css'] = $this->preload->getcss('shipment');
        $this->load->model('cs_order_model');
        $this->data['csOrderList'] = $this->cs_order_model->getCsOrderList($this->data['shopID'],$finish); 
       
		$this->load->view('cs_order_list',$this->data);	
        
        
    
    }
    function orderlist_ready()
    {
        
         $this->iframeConfirm();
     
       
  
        $this->data['css'] = $this->preload->getcss('shipment');
        $this->load->model('cs_order_model');
        $this->data['csOrderList'] = $this->cs_order_model->getReadyCsorderList($this->data['shopID']); 
       
		$this->load->view('cs_ready_order_list',$this->data);	
        
        
        
        
    }
     function orderlist_send_ready()
    {
        
         $this->iframeConfirm();
     
       
  
        $this->data['css'] = $this->preload->getcss('shipment');
        $this->load->model('cs_order_model');
        $this->data['csOrderList'] = $this->cs_order_model->getSendoutCsorderList($this->data['shopID']); 
       
		$this->load->view('cs_ready_send_order_list',$this->data);	
        
        
        
        
    }
    
    
    function orderlist_send_email()
    {
         $shopID = $this->data['shopID'];
        $data['shop'] = $this->System_model->getShopByID( $shopID);
       
        $content = $this->paser->post($this->input->post('url'),array(),false);
        $content = '<h1>親愛的店家您好：</h1>';
        $title = '客戶預備取貨清單'.date('Y-m-d H:i:s');;
        $data['mail'] = $data['shop']['email'];
		$data['title'] = $title;
		$data['content'] = $content;

		$data['code'] = md5('phaMail');
		$this->paser->post('http://shipment.phantasia.tw/welcome/mailapi',$data,true);	
        echo 'ok';
        
        
    }
    
    function get_com_num()
	{
		$productID  = $this->input->post('productID');
		$this->load->model('Order_model');
		$data['comNum'] = $this->Order_model->getProductNumExceptOrder($productID,0);
		    $data['result'] = true;
        echo json_encode($data);
		
	}
    
    
    
    function order_view()   
    {
        $csOrderID = $this->uri->segment(3);
       
         $this->load->model('cs_order_model');
        $this->data['csOrder'] = $this->cs_order_model-> getCsOrder($csOrderID);
       // $this->data['csOrderData'] = $this->cs_order_model-> getCsOrderDetail($csOrderID,   $this->data['shopID'] );
        //print_r($this->data['csOrderData'] );
        $this->load->view('cs_order_view',$this->data);	
    
    }
    function cs_order_data()   
    {
        $csOrderID = $this->input->post('csOrderID');
       
         $this->load->model('cs_order_model');
    
        $data['csOrderData'] = $this->cs_order_model-> getCsOrderDetail($csOrderID,   $this->data['shopID'] );
        $data['result'] = true;
        echo json_encode($data);
    
    }
        
	function order_update(){
  
        
		$this->load->model('cs_order_model');
		$this->input->post();

        
		$data = $_POST;
       
     $this->db->insert('pos_test',array('content'=>'in order_update'.json_encode($_POST)));
                
		$csOrderID = $data['csOrderID'];
		//$shopID = $data['shopID'];
		$newdata['csOrderID'] = $csOrderID;
         $csOrder = $this->cs_order_model-> getCsOrder($csOrderID);
		$count = count($data['cancel'])-1;
		for ($i=0; $i <= $count ; $i++) { 
			$cancelNum = $data['cancel'][$i];
			$productID = $data['productID'][$i];
			$num = $data['num'][$i];
			$newdata['num'] = $num; 
			$newdata['sellPrice'] = $data['sellPrice'][$i];
			$newdata['productID'] = $data['productID'][$i];
           
          
            if($csOrder['shopID']==666) 
            {
                $IsExist = false;
                                

            }
			else $IsExist = $this->cs_order_model->checkExist($productID,$csOrderID);
			if($IsExist) $this->cs_order_model->updateCsOrderDetail($cancelNum, $csOrderID, $productID, $newdata);
            //echo 'r:'.$IsExist;
			else $this->cs_order_model->insertCsOrderDetail($cancelNum,$newdata);
			
		}
		$total = $this->cs_order_model->calcuteTotal($csOrderID);
		if(isset($data['shipStatus'])) $newOrderData['cargoStatus'] = $data['shipStatus'];
		$newOrderData['usage'] = $data['usage'];
		$newOrderData['total'] = $total;
		$newOrderData['comment'] = $data['comment'];
		$newOrderData['memberID'] = $data['memberID'];
		$newOrderData['email'] = $data['email'];
		$newOrderData['phone'] = $data['phone'];
		$newOrderData['name'] = $data['name'];
        $newOrderData['title'] = $data['title'];
        $newOrderData['IDNumber'] = $data['IDNumber'];
        $newOrderData['discount'] = $data['discount'];
        $newOrderData['outDate'] = $data['outDate'];
        $newOrderData['deleteToken'] = 0 ;
		$this->cs_order_model->updateCsOrder($newOrderData,$csOrderID);
		$update['result']=true;
		echo json_encode($update);
	}
	function create(){
        $this->load->model('cs_order_model');
        
        $shopID = $this->data['shopID'];
        $source = $this->input->post('source');
        $data['csOrderID'] = $this->cs_order_model->createCsOrder($shopID,$source);
		$data['result'] = true;
		echo json_encode($data);
	}
	function get_csorder_list(){
		$this->load->model('cs_order_model');
        $this->input->post();
       
        $shopID = $_POST['shopID'];
        $cashStatus = $_POST['cashStatus'];
        $cargoStatus = $_POST['cargoStatus'];
        $source = $_POST['source'];
        $start = $_POST['start'];
        $num = $_POST['num'];
        $keyword = $this->input->post('keyWord');
        $data['csOrderList'] = $this->cs_order_model->getConditionCsOrderList($shopID, $cashStatus, $cargoStatus,$source,$start,$num,$keyword);
		$data['result'] = true;
      
		echo json_encode($data);
	}
	function get_csorder_detail(){
		$this->load->model('cs_order_model');
      
        $csOrderID =   $this->input->post('csOrderID');

        $data['csOrder'] = $this->cs_order_model-> getCsOrder($csOrderID);
    
        $query = $this->cs_order_model-> getShopID($csOrderID);
        $data['csOrderData'] = $this->cs_order_model-> getCsOrderDetail($csOrderID,$query['shopID']);
        $data['result'] = true;
        echo json_encode($data);
    }
    function cash_out(){
    	$this->load->model('cs_order_model');
        $this->input->post();
        $csOrderID = $_POST['csOrderID'];
        $cargoStatus = $_POST['cargoStatus'];
        $this->cs_order_model->updateCsOrderStatus($csOrderID, $cargoStatus);	
        $data['result'] = true;
        echo json_encode($data);
    }
    function estimate(){
    
    	$this->load->model('cs_order_model');
    	if(isset($_GET['csOrderID'])) $csOrderID = $_GET['csOrderID'];
    	if(isset($_GET['shopID'])) $shopID = $_GET['shopID'];
    	$query = $this->cs_order_model->getCsOrder($csOrderID);
    	$data['csOrderData'] = $this->cs_order_model-> getCsOrderDetail($csOrderID, $shopID);
        
       
    	$data['person'] = $query;
    	$data['date'] = date('Y-m-d H:i:s');
    	$this->load->view('showestimate',$data);	
    }
     function getproduct(){
    
    	$this->load->model('cs_order_model');
    	if(isset($_GET['csOrderID'])) $csOrderID = $_GET['csOrderID'];
    	if(isset($_GET['shopID'])) $shopID = $_GET['shopID'];
    	$query = $this->cs_order_model->getCsOrder($csOrderID);
    	$data['csOrderData'] = $this->cs_order_model-> getCsOrderDetail($csOrderID, $shopID);
    	$data['person'] = $query;
    	$data['date'] = date('Y-m-d H:i:s');
    	$this->load->view('csorder_get_product',$data);	
    }
    
    function weborder()
    {
        
      
         $this->load->model('cs_order_model');
       $this->data['announceID'] = $this->uri->segment(4);
        $csOrderID = $this->uri->segment(3);
    	 $shopID = $this->data['shopID'];
    	 $this->data['csOrder'] = $this->cs_order_model-> getCsOrder($csOrderID);
        $this->data['csOrderData'] = $this->cs_order_model-> getCsOrderDetail($csOrderID,   $shopID ,true);
     
        $this->load->view('web_order_view',$this->data);	
        
    
    
    }
    function cs_order_ready_all()
    {
         $this->load->model('system_model');
         $this->load->model('cs_order_model');
        $s = $this->system_model->getShop(true);
        
        foreach($s as $row)
        {
            
             $this->cs_order_model->csOrderReadyNotify($row['shopID']);
            
        }
        
        echo 'finish';
    }
        
    
    function cs_order_ready()
    {
         $this->load->model('cs_order_model');
        $shopID = $this->uri->segment(3);
        if($shopID!=666) $this->cs_order_model->csOrderReadyNotify(  $shopID );
         echo 'finish';
        
    }
    
    function get_new_member_data()
    {
         $this->load->model('cs_order_model');
         $csOrderID = $this->input->post('csOrderID');
         $orderID = $this->cs_order_model->getOrderIDOnMart($csOrderID);
        
        if($orderID!=0)
        {
         echo $this->paser->post($this->data['martadmDomain'].'receipt/get_new_member_data',array('orderID'=>$orderID),false);
        }
        else
        {
          $data['result'] = false;
       
            echo json_encode($data);
            
            
        }
        
        
        
    }
    
        
    function web_order_feedback()
    {
        
       /*
		$_POST['csOrderID']	 = 3904;
		$_POST['shopID']	 = 100;
		$_POST['price'][] = 600;
		$_POST['productID'][] = 8885221;
        $_POST['ship_8885221']	 = 1;
		$_POST['num_8885221']	 = 1;	
        */
        $csOrderID = $this->input->post('csOrderID');
        $announceID = $this->input->post('announceID');
        
  		$this->load->model('Order_model');
        $this->load->model('Product_model');
        $this->load->model('cs_order_model');
        $shipWay = 0;
        foreach($_POST['productID'] as $row)
        {
            $productID = $row;
            $r['shipWay'] = $this->input->post('ship_'.$productID );
            $r['backNum'] = $this->input->post('num_'.$productID );
            $shipWay +=$r['shipWay'];
            $this->db->where('productID',$productID);
            $this->db->where('csOrderID',$csOrderID);
            $this->db->update('pos_cs_order_detail',$r);
            
            
            $productData[] =$r ; 
                        
        }
		//出貨單設定
        	$destinationShopID = $this->cs_order_model->csorderToOrder($csOrderID);
     
        //=====出貨單設定完成

        //---以下為商商城訂單紀錄
       
        
         $orderID = $this->cs_order_model->getOrderIDOnMart($csOrderID);
        
        if($shipWay==0)
        {
            //寄信通知客人取貨囉！
            
           
          
            $postData =  $this->web_order_ready($csOrderID);
          
            
        }
        else
        {
            //寄信通知客人，您的貨品因為物流因素，必須等待
            
             $this->db->where('shopID',$destinationShopID);
           $this->db->update('pos_sub_branch',array('shipmentStatus'=>3));
            
            
             $data['shipWay'] = 1;
                 $postData = array(
                    'code'=>53180059,
                'orderID'=>$orderID,
                'type'   =>'wait'
            );
          $data['response']  =$this->paser->post($this->data['martDomain'].'order/send_pos_shop_email',$postData,false);
           
                $this->paser->post($this->data['martadmDomain'].'receipt/modifydeliver',array('mstatus'=>2,'modify'=>true,'orderid'=>$orderID,'type'=>'mt'),false);
        
        }
        $data['postData'] = $postData;
       /*關閉announce url:\'system/confirm_announce\',announceID */
        $this->load->model('system_model');
        $systemInf = $this->system_model->getShopByID($this->data['shopID']);
        $postData['shopID'] = $this->data['shopID'];
        $postData['licenceCode'] =  $systemInf['licenceCode'];
        $postData['announceID'] = $announceID;
        $r = $this->paser->post($this->data['posDomain'].'system/confirm_announce',$postData,true);
        $data['result'] = $r['result'];
       
        echo json_encode($data);
        
        
        
    }
    function web_order_ready($csOrderID = 0)
    {
        $key = false;
        if($csOrderID==0)
        {
            $key = true; //表post
            $csOrderID = $this->input->post('csOrderID');
           
        }
        $this->load->model('cs_order_model');
          $orderID = $this->cs_order_model->getOrderIDOnMart($csOrderID);
        //寄信通知客人取貨囉！
            
            $data['shipWay'] = 0;

                 $postData = array(
                    'code'=>53180059,
                'orderID'=>$orderID,
                'type'   =>'arrived'
            );
         $data['response']  =$this->paser->post($this->data['martDomain'].'order/send_pos_shop_email',$postData,false);
              $data['time'] = date("Y-m-d H:i:s");
        $this->db->where('csOrderID',$csOrderID);
        $this->db->update('pos_cs_order',array('emailNotify'=>  $data['time'] ));
        
        
            
            $this->paser->post($this->data['martadmDomain'].'receipt/modifydeliver',array('mstatus'=>3,'modify'=>true,'orderid'=>$orderID,'type'=>'mt'),false);
            
        if($key==true)
        {
            
           
             $data['result']  = true;
                echo json_encode($data);
            
        }
        else return $postData;
        
    }
    
    
    function phone_notify()
    {
        
        
        $csOrderID = $this->input->post('csOrderID');
         $data['time'] = date("Y-m-d H:i:s");
        $this->db->where('csOrderID',$csOrderID);
        $this->db->update('pos_cs_order',array('phoneNotify'=>  $data['time'] ));
         $data['result']  = true;
        echo json_encode($data);
        
    }
    
    function testmail()
    {
  $sql = "SELECT * FROM `pos_online_order_result` left join pos_weborder_link on pos_weborder_link.csorderID = pos_online_order_result.csOrderID  
where pos_online_order_result.time >'2017-07-20'  
ORDER BY `pos_online_order_result`.`csOrderID` ASC";
      $q = $this->db->query($sql);
        $r = $q->result_array();
                  
        
        print_r($r);
        
        foreach($r as $row)
        {
        $orderID = $row['orderID'];
  
         
            $r = $this->paser->post($this->data['martadmDomain'].'receipt/modifydeliver',array('mstatus'=>5,'modify'=>true,'orderid'=>$orderID,'type'=>'mt'),false);
  
        }
        /*
      
        $data['mail'] = 'lintaitin@gmail.com';
		$data['title'] = '測試抬頭';
		$data['content'] = '測試內容';

		$data['code'] = md5('phaMail');
		$this->paser->post('http://shipment.phantasia.tw/welcome/mailapi',$data,true);	
        
        */
        
    }
    function delete_csorder()
    {
        
        
        $csOrderID = $this->input->post('csOrderID');
        $shopID = $this->data['shopID'];
        
        $this->db->where('shopID',$shopID);
        $this->db->where('csOrderID',$csOrderID);
        
          $this->db->update('pos_cs_order',array('deleteToken'=>1,'deleteName'=>$this->data['account']));
         $data['result']  = true;
        
        echo json_encode($data);
    }
    
    function fetch_web_order()
    {
       $this->load->model('cs_order_model');
         $key = true;
         $data['mail'] = 'lintaitin@gmail.com';
		$data['title'] = 'fetch_web_order';
		$data['content'] = '運行';

		$data['code'] = md5('phaMail');
		$this->paser->post('http://shipment.phantasia.tw/welcome/mailapi',$data,true);	
        
        for($page=1;$key;$page++)
        {
			
			$url =  $this->data['martadmDomain'].'receipt?page='.$page.'tid=1&tstatus=1&select_sumbit=1&date=&date2=&oid=&search_order=&search_price_select=&search_price_order=&returnWay=json';
			
        $r = $this->paser->post($url
           ,array(),true);
            
echo $url;
   
        $key = false;
     
        foreach($r['orderlist'] as $row)
        {
            
           foreach($row as $each)
           {
               
               $ret = $this->cs_order_model->newWebOrder( $each['orderid'],$each['orderdate']);
               if($ret==true) 
               {
                   $key=true;
                   echo $each['orderid'].'<br/>';
               }
           }
               
            
        }
            
        }
        echo 'done';
        
    }
    
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */