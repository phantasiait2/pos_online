<?php 
class Accounting_model extends Model {
	function Accounting_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function getDayReport($date,$shopID)
	{
        $t = explode('-',$date);
          if($t[1]<=3)$onYear = $t[0]-1;
            else $onYear = $t[0];
        
        
    $distributeType = $this->Order_model->getDistributeType($shopID);
		$sql = "SELECT pos_product.*,pos_product_sell.*,pos_product_sell.num as sellNum,pos_own.best30,pos_product_sell.id as posSellID 
		 		FROM pos_product_sell
				LEFT JOIN pos_product
				ON pos_product_sell.productID = pos_product.productID
				LEFT JOIN pos_own
				ON pos_own.productID = pos_product.productID and onYear = $onYear 
				LEFT JOIN pos_order_rule as e ON e.productID = pos_product.productID and e.distributeType= 0  and e.num = 0
				LEFT JOIN
				(
				SELECT productID,pos_order_rule.discount as productDiscount,pos_order_distribute.discount as shopDiscount FROM
				 pos_order_rule 
				LEFT JOIN pos_order_distribute ON pos_order_distribute.id = pos_order_rule.distributeType
				 where pos_order_rule.distributeType =$distributeType and pos_order_rule.num = 0
		        ) AS d
				ON pos_product.productID = d.productID 
				LEFT JOIN pos_order_distribute as f ON  f.id = $distributeType 
				where date(pos_product_sell.time)='".$date."' and pos_product_sell.shopID =$shopID
            
				order by pos_product_sell.time,sellID";

		$query = $this->db->query($sql);	
		return $query->result_array();		
		
	}
    function fillINRank($data,$shopID)
    {
        foreach($data as $row)
        {
            
            
            $row['rank'] = $this->getRank($row['checkID'],$shopID);
            $result[] = $row;
        }
       
        return $result;
        
        
        
    }
    
    function getRank($checkID,$shopID)
    {
         $this->db->where('shopID',$shopID);
         $this->db->where('checkID',$checkID);
         $query = $this->db->get('pos_cash_register');
            
            $r = $query->row_array();
        if(isset($r['rank']))return $r['rank'];
        return '';
        
        
    }
    
    
	//某某會員memberID 在 某某店shopID 換了某商品productID  成本為purchasePrice =>pos_bonus_change
	//某店shopID index 依照當時比例為必須負擔成本cost 多少元 =>pos_bonus_change_allocate
	
	function getOutBonus($mon,$year,$shopID)
	{
		$this->db->select('pos_sub_branch.name as shopName,time,pos_product.*,pos_bonus_change.purchasePrice,pos_bonus_change.memberID,pos_bonus_change_allocate.cost');
		$this->db->where('year(time)',$year);
		$this->db->where('month(time)',$mon);
		$this->db->where('pos_bonus_change_allocate.shopID',$shopID);
		$this->db->where('pos_bonus_change.shopID !=',$shopID);
		$this->db->where('pos_bonus_change_allocate.shopID !=','');
		$this->db->join('pos_bonus_change_allocate','pos_bonus_change.id = pos_bonus_change_allocate.changeID','left');
		$this->db->join('pos_product','pos_product.productID = pos_bonus_change.productID','left');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_bonus_change.shopID','left');
		$query = $this->db->get('pos_bonus_change');
		return $query->result_array();	
		
		
		
	}
	function getInBonus($mon,$year,$shopID)
	{
		$this->db->select('pos_sub_branch.name as shopName,time,pos_product.*,pos_bonus_change.purchasePrice,pos_bonus_change.memberID,pos_bonus_change_allocate.cost');
		$this->db->where('year(time)',$year);
		$this->db->where('month(time)',$mon);
		$this->db->where('pos_bonus_change.shopID',$shopID);
		$this->db->where('pos_bonus_change_allocate.shopID !=',$shopID);
		$this->db->where('pos_bonus_change_allocate.shopID !=','');
		$this->db->join('pos_bonus_change_allocate','pos_bonus_change.id = pos_bonus_change_allocate.changeID','left');
		$this->db->join('pos_product','pos_product.productID = pos_bonus_change.productID','left');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_bonus_change_allocate.shopID','left');

		$query = $this->db->get('pos_bonus_change');
		return $query->result_array();	
		
		
		
	}
	function getSecondHandSell($date,$shopID)
	{
		
		$sql = "SELECT pos_product.*,pos_inshop_sell.*
		 		FROM pos_inshop_sell
				LEFT JOIN pos_inshop_amount
				ON pos_inshop_amount.rentID = pos_inshop_sell.rentID and pos_inshop_sell.shopID = pos_inshop_amount.shopID
				LEFT JOIN pos_product
				ON pos_inshop_amount.productID = pos_product.productID
				where date(pos_inshop_sell.time)='".$date."' and pos_inshop_amount.shopID =$shopID
				order by pos_inshop_sell.time";
		$query = $this->db->query($sql);	
		return $query->result_array();	
		
	}
	function getBackProduct($date,$shopID)
	{
		$sql = "SELECT *,pos_product_back.num as sellNum,pos_product_back.comment as backComment
		 		FROM pos_product_back
				LEFT JOIN pos_product
				ON pos_product_back.productID = pos_product.productID
				where date(pos_product_back.backTime)='".$date."'
				and shopID = $shopID
				order by pos_product_back.backTime";
		$query = $this->db->query($sql);	
		return $query->result_array();	
		
		
	}
	
	
	function getLastRegisterRemain($date,$shopID)
	{
        
        $db = $this->cashRegisterDB(0,$date);
        
		$sql = "SELECT ".$db.".* FROM ".$db."
				where date(time)<'".$date."'  and shopID = $shopID ORDER BY time DESC limit 1";
		$query =$this->db->query($sql);
	 	$data = $query->row_array();	
		if(isset($data['remain']))return $data['remain'];
		else return 0;
		
	}
	function monSecondHand($mon,$year,$shopID,$date='')
	{
		$sql = "SELECT productNum,category,barcode,pos_product.ZHName,pos_product.ENGName,pos_product.price,pos_product.type,pos_inshop_sell.*
		 		FROM pos_inshop_sell
				LEFT JOIN pos_inshop_amount
				ON pos_inshop_amount.rentID = pos_inshop_sell.rentID and pos_inshop_sell.shopID = pos_inshop_amount.shopID
				LEFT JOIN  pos_product
				ON  pos_inshop_amount.productID = pos_product.productID";
		if($date!=''&&$year==0)$sql.= " where (pos_inshop_sell.time)<='".$date."' and month(pos_inshop_sell.time)=month('".$date."') and  year(pos_inshop_sell.time)=year('".$date."')";
		else	$sql.=" where year(pos_inshop_sell.time)='".$year."'";
		if($mon!=0)	$sql.=" and month(pos_inshop_sell.time)='".$mon."'";			
					
		if($shopID!=0)$sql.= " and pos_inshop_sell.shopID =$shopID";  
		if($shopID!=100)$sql.= " and pos_inshop_sell.shopID !=100";  
		if($shopID!=999)$sql.= " and pos_inshop_sell.shopID !=999";  		 
		$sql.=	" order by  pos_inshop_sell.time";
			
		
		$query = $this->db->query($sql);
		$result =  $query->result_array();		
		
		
		return $result;	
	}
	function monBack($mon,$year,$shopID,$date='')
	{
		$sql = "SELECT productNum,category,barcode,pos_product.ZHName,pos_product.ENGName,pos_product.price,pos_product.type,pos_product_back.*
		 		FROM pos_product_back
				LEFT JOIN  pos_product
				ON  pos_product_back.productID = pos_product.productID";
		if($date!=''&&$year==0)$sql.= " where (pos_product_back.backTime)<='".$date."' and month(pos_product_back.backTime)=year('".$date."') and  year(pos_product_back.backTime)=month('".$date."')";
		else	$sql.=" where year(pos_product_back.backTime)='".$year."'";
		if($mon!=0)	$sql.=" and month(pos_product_back.backTime)='".$mon."'";				
		if($shopID!=0)$sql.= " and shopID =$shopID";  
		if($shopID!=100)$sql.= " and shopID !=100";  
		if($shopID!=999)$sql.= " and shopID !=999";  		 
		$sql.=	" order by  pos_product_back.backTime";
			
		
		$query = $this->db->query($sql);	
		$result =  $query->result_array();		
		
		
		return $result;	
	}
	function getYearReport($year,$shopID,$sign,$boundMonth)
	{
		$result = array();
		$start = 1;
		$end = 12;
		
		if($sign=='>=')$start = $boundMonth;
		else $end = $boundMonth;
	if($boundMonth==0) $start=1;
	
		for($i=1;$i<=12;$i++)
		{
			$data = $this->getMonReport($i,$year,$shopID);
			foreach($data as $row) $result[] = $row;
		}
		
		return $result;
		
	}
	function getTenDay($date)
	{
		
		
		 $file = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/race/report_'.$date.'.txt';
				
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
		$this->db->where('shopID <=',600);
        $this->db->where('jointype',1);
		$this->db->where('shopID !=',100);
		$this->db->where('shopID !=',0);
		$query = $this->db->get('pos_sub_branch');
		$shopList = $query->result_array();
		
		 $pt = $this->getOwnProductList();
	
		
		$ptNum = count($pt);
		foreach($shopList as $row)
		{
			$shopID = $row['shopID'];
            $sale = array();
			$sale = $this->getMonReport(0,0,$shopID,$date);
			$data[$shopID]['back'] = $this->monBack(0,0,$shopID,$date);
           
			$data[$shopID]['second'] = $this->monSecondHand(0,0,$shopID,$date);
         
			$data[$shopID]['month'] = $this->caculateProfit($sale,$data[$shopID]['second'],$data[$shopID]['back'],true);
			$data[$shopID]['newGoldMember'] = $this->getMember($date,$shopID,'8881935');
			$data[$shopID]['newWhiteMember'] = $this->getMember($date,$shopID,'8881936');
			$data[$shopID]['goGoldMember'] = $this->getMember($date,$shopID,'8882448');
			$data[$shopID]['goWhiteMember'] = $this->getMember($date,$shopID,'8882449');
			$data[$shopID]['upgradeGoldMember'] = $this->getMember($date,$shopID,'8881942');
			$data[$shopID]['upgradeWhiteMember'] = $this->getMember($date,$shopID,'8881943');
			

			//會員
			$result['newGoldMember'][] = array('type'=>'新辦黃金','shopName'=>$row['name'],'val'=>count($data[$shopID]['newGoldMember']));
			$result['newWhiteMember'][] = array('type'=>'新辦白金','shopName'=>$row['name'],'val'=>count($data[$shopID]['newWhiteMember']));
			$result['goGoldMember'][] = array('type'=>'續辦黃金','shopName'=>$row['name'],'val'=>count($data[$shopID]['goGoldMember']));
			
			$result['goWhiteMember'][] = array('type'=>'續辦白金','shopName'=>$row['name'],'val'=>count($data[$shopID]['goWhiteMember']));
			$result['upgradeGoldMember'][] = array('type'=>'升級黃金','shopName'=>$row['name'],'val'=>count($data[$shopID]['upgradeGoldMember']));
			$result['upgradeWhiteMember'][] = array('type'=>'升級白金','shopName'=>$row['name'],'val'=>count($data[$shopID]['upgradeWhiteMember']));
			//場地
			$result['place'][] = array('type'=>'場地','shopName'=>$row['name'],'val'=>$data[$shopID]['month']['item'][2]['count']);
			//銷售
			$result['sale'][] = array('type'=>'銷售','shopName'=>$row['name'],'val'=>$data[$shopID]['month']['item'][1]['count']);
			//租借
			$result['rent'][] = array('type'=>'租借','shopName'=>$row['name'],'val'=>$data[$shopID]['month']['item'][3]['count']);
		
			//餐飲
			$result['food'][] = array('type'=>'餐飲','shopName'=>$row['name'],'val'=>$data[$shopID]['month']['item'][4]['count']);
	
		
			//魔風
			$result['magic'][] = array('type'=>'魔風','shopName'=>$row['name'],'val'=>$data[$shopID]['month']['item'][8]['count']);
			//LC
			$result['LC'][] = array('type'=>'LC,FOW','shopName'=>$row['name'],'val'=>$data[$shopID]['month']['item'][7]['count']);
            
            //課程
			$result['school'][] = array('type'=>'課程','shopName'=>$row['name'],'val'=>$data[$shopID]['month']['item'][9]['count']);
            
			//其他
			$result['other'][] = array('type'=>'其他','shopName'=>$row['name'],'val'=>$data[$shopID]['month']['item'][5]['count']);
			//總營業額
			$result['total'][]= array('type'=>'總營業額','shopName'=>$row['name'],'val'=>$data[$shopID]['month'] ['monTotal']);
			//毛利率
			if($data[$shopID]['month']['monTotal']!=0)$result['profitRatio'][] = array('type'=>'毛利率','shopName'=>$row['name'],'val'=>round($data[$shopID]['month']['monVerify']*100/$data[$shopID]['month']['monTotal'],0));
			else $result['profitRatio'][] = array('type'=>'毛利率','shopName'=>$row['name'],'val'=>0,0);
			
			
		
			
			for($i = 0;$i<$ptNum;$i++) $count[$i] = 0 ; ;
				
			
			foreach ($sale as $each)
			{
								
				for($i = 0;$i<$ptNum;$i++)if($each['productID']==$pt[$i]['productID']) $count[$i]+=$each['sellNum'];
				
				
					
			}
			
			for($i=0;$i<$ptNum;$i++)$pt[$i]['shopList'][]  = array('shopName'=>$row['name'],'val'=>$count[$i]);
			
			
		}
		
		foreach($result as $key=>$row) usort($result[$key],'cmpValue');;
		
		foreach($result as $key=>$row)$result['class'][] = $key;

/*
		usort($result['newGoldMember'],'cmpValue');
		usort($result['newWhiteMember'],'cmpValue');
		usort($result['goGoldMember'],'cmpValue');
		usort($result['goWhiteMember'],'cmpValue');
		usort($result['upgradeGoldMember'],'cmpValue');
		usort($result['upgradeWhiteMember'],'cmpValue');
		usort($result['place'],'cmpValue');
		usort($result['sale'],'cmpValue');
		usort($result['rent'],'cmpValue');
		usort($result['food'],'cmpValue');
		usort($result['magic'],'cmpValue');
		usort($result['total'],'cmpValue');
		usort($result['profitRatio'],'cmpValue');
		*/
		for($i=0;$i<$ptNum;$i++)usort($pt[$i]['shopList'],'cmpValue');
		
		$result['pt'] = $pt;
		
		if(isset($creareFile) &&$creareFile==true) 
		{
			$output = json_encode($result);
				$f = fopen($file,'w');
			fprintf($f,"%s",$output);
					fclose($f);		
			
		}
		
		return $result;	
		
		
	}
	
	function getMember($date,$shopID,$productID)
	{
		$time = explode('-',$date);
	
		$this->db->where('time <=',$date.' 23:59:59');
		$this->db->where('time >=',$time[0].'-'.$time[1].'-01'.' 00:00:00');
		$this->db->where('productID',$productID);
		$this->db->where('shopID',$shopID,true);
		$query = $this->db->get('pos_product_sell');
		$data = $query->result_array();

		return $data;
		
	}
	
	
	
	
	function getMonReport($mon,$year,$shopID,$date='')
	{

	
		$creareFile =false;
		$time = getdate();
		if($date!=''&&$year==0) 
		{
			$nowTime = explode('-',$date);
			$year = $nowTime[0];
			$mon = $nowTime[1];
		}
	
		if($year<$time['year']||($year==$time['year']&&$mon<$time['mon']))
		{
		
			if($year==0&&$date!='') 
            {
                $dir = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/sale/monthreport/temp/';
                $file = $dir.'monreport_'.substr($date,0,7).'_'.$shopID.'.txt';
            }
			else
            {
                $dir = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/sale/monthreport/'.$year.'_'.$mon.'/';
                $file=$dir.'monreport_'.$year.'_'.$mon.'_'.$shopID.'.txt';
            }
      
            if (!file_exists($dir) && !is_dir($dir)) {
                mkdir($dir);         
            } 

            
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
		$result = array();
		
		if($shopID!=0) 
        {
            
            if($mon<=3)$onYear = $year-1;
            else $onYear = $year;
        
            $distributeType = $this->Order_model->getDistributeType($shopID);
             $sql = "SELECT productNum,category,barcode,minDiscount,language,pos_product.ZHName,pos_product.ENGName,pos_product.price,pos_product.type,pos_product_sell.*,pos_product_sell.num as sellNum,pos_own.best30  
                    FROM pos_product_sell
                    
                    LEFT JOIN  pos_product
                    ON  pos_product_sell.productID = pos_product.productID
                   LEFT JOIN pos_own
				ON pos_own.productID = pos_product.productID and onYear = $onYear
                    
                    ";
            if($date!=''&&$year==0)$sql.= " where (pos_product_sell.time)<='".$date." 23:59:59' and month(pos_product_sell.time)=month('".$date."') and  year(pos_product_sell.time)=year('".$date."')";
            else	$sql.=" where year(pos_product_sell.time)='".$year."'";
            if($mon!=0)	$sql.=" and month(pos_product_sell.time)='".$mon."'";
            
            if($shopID!=0)$sql.= " and pos_product_sell.shopID =$shopID";  
            if($shopID!=100)$sql.= " and pos_product_sell.shopID !=100";  
            if($shopID!=999)$sql.= " and pos_product_sell.shopID !=999";  		 
            $sql.=	" order by  pos_product_sell.time";
            


            $query = $this->db->query($sql);	

            $result =  $query->result_array();		
           
               
            
        }
		else 
        {
        
            
            
            
            $sql = "SELECT productNum,category,barcode,pos_product.ZHName,pos_product.ENGName,pos_product.price,pos_product.type,pos_product_sell.*,sum(num) as sellNum 
                    FROM pos_product_sell
                    LEFT JOIN  pos_product
                    ON  pos_product_sell.productID = pos_product.productID";
            if($date!=''&&$year==0)$sql.= " where (pos_product_sell.time)<='".$date." 23:59:59' and month(pos_product_sell.time)=month('".$date."') and  year(pos_product_sell.time)=year('".$date."')";
            else	$sql.=" where year(pos_product_sell.time)='".$year."'";
            if($mon!=0)	$sql.=" and month(pos_product_sell.time)='".$mon."'";				
            if($shopID!=0)$sql.= " and shopID =$shopID";  
            if($shopID!=100)$sql.= " and shopID !=100";  
            if($shopID!=999)$sql.= " and shopID !=999";  		 
            $sql.=	" group by productID ";
            
            

            $query = $this->db->query($sql);	

            $result =  $query->result_array();		
            
            
         
             
        }
        
    
           
            
        
		if($creareFile) 
		{
			$output = json_encode($result);
				$f = fopen($file,'w');
			fprintf($f,"%s",$output);
					fclose($f);		
			
		}
		
		return $result;
		
	}
	function getBriefMonReport($mon,$year,$date='')
    {
        
         $this->db->where('shopID !=',100);
             $this->db->where('shopID !=',666);
             $this->db->where('shopID !=',999);
             $this->db->where('shopID <=',1000);
            $query = $this->db->get('pos_sub_branch');
            $shopList = $query->result_array();
                    $r = array();
            foreach($shopList as $each)
            {
        
                $data = $this->getMonReport($mon,$year,$each['shopID'],$date);
        
        

            
                foreach ($data as $row)
                {

                    if(!isset($r['p_'.$row['productID']])) $r['p_'.$row['productID']] =  $row;
                    else  $r['p_'.$row['productID']]['sellNum'] +=$row['sellNum'];



                }
            }
        return $r;
        
        
    }
	function caculateProfit(&$data,$secondHand,$back,$item=false)
	{
		
		$result['monVerify'] = 0 ;
		$result['monTotal'] = 0 ;
		if($item)
		{
			$consumption = $this->getConsumption();
			foreach($consumption as $row)
			{
				
					$result['item'][$row['typeID']]['name'] =$row['name'];
					$result['item'][$row['typeID']]['count'] = 0;
				
			}
		}
		
		
		foreach($data as $row)
		{		
				if($item)	
				{
					if(isset($result['item'][$row['type']]))	$result['item'][$row['type']]['count']+=$row['sellPrice']*$row['sellNum'];
					else $result['item'][5]['count']+=$row['sellPrice']*$row['sellNum'];
				}
		
				$result['monVerify']+=round($row['sellNum']*($row['sellPrice']-$row['purchasePrice']));
				$result['monTotal'] += $row['sellPrice']*$row['sellNum'];
            
		}
		if(!empty($secondHand ))
		foreach($secondHand as $row)
		{
		
		
				$result['monVerify']+=1*round(($row['sellPrice']-$row['cost']));
				$result['monTotal'] += 1*$row['sellPrice'];
               
			
		
		}
		
		
		if(!empty($back ))
		foreach($back as $row)
		{
				
				$result['monVerify']-=$row['num']*round($row['sellPrice']-$row['cost']);
				$result['monTotal'] -= $row['num']*$row['sellPrice'];
	
		}
	
		return $result;
		
	}	
	function getConsumption()
	{
		$this->db->order_by('typeOrder','asc');
		$query = $this->db->get('pos_product_type');
		return $query->result_array();
		
	}
	function getRegister($shopID)
	{
		$this->db->where('shopID',$shopID);
		$this->db->order_by('id','DESC');
		$this->db->limit(1);
		$query=$this->db->get('pos_cash_register');
		
		return $query->row_array();	
		
	}
	function getRegisterByDay($date,$shopID)
	{
        $db = $this->cashRegisterDB(0,$date);
    
		$sql = "SELECT * FROM ".$db."
				where date(time)='".$date."' and shopID =$shopID ORDER BY time ASC";
		$query =$this->db->query($sql);
		return $query->result_array();	
		
	}
	
	function getMonExpenses($mon,$year,$shopID)
	{
        $db = $this->cashRegisterDB($year);
		$this->db->select($db.'.*,pos_output_items.name as item');
		$this->db->join('pos_output_items','pos_output_items.id = '.$db.'.outPutItem','left');
		$this->db->where('shopID',$shopID);
		$this->db->where('cashType',3);
		$this->db->where('month(time)',$mon,false);
		$this->db->where('year(time)',$year,false);
		
		$query  = $this->db->get($db);
		$data = $query->result_array();
	
		return $data;
		
		
		
	}
    function getMonCashFlow($mon,$year,$shopID)
	{
        $db = $this->cashRegisterDB($year);
		$this->db->select($db.'.*');
		//$this->db->join('pos_output_items','pos_output_items.id = '.$db.'.outPutItem','left');
		$this->db->where('shopID',$shopID);
		$this->db->where('cashType',1);
		$this->db->where('month(time)',$mon,false);
		$this->db->where('year(time)',$year,false);
		
		$query  = $this->db->get($db);
		$data = $query->result_array();
	
		return $data;
		
		
		
	}
	
	function getCredit($mon,$year,$shopID)
	{
        $db = $this->cashRegisterDB($year);
		$this->db->select($db.'.*,sum(credit) as credit');
		$this->db->where('shopID',$shopID);
		$this->db->where('cashType',1);
		$this->db->where('month(time)',$mon,false);
		$this->db->where('year(time)',$year,false);
		
		$query  = $this->db->get($db);
		$data = $query->row_array();
	
		return $data['credit'];
		
		
		
	}
	
    function cashRegisterDB($year=0,$time='')
    {
        if($year==0) 
        {
            
            $t = explode('-',$time);
            $year = $t[0];
        }
        
        
       if($year<2018)
        {
            $db = 'pos_old_cash_register';
            
            
        }
       else $db ='pos_cash_register';
        
        return $db;
    }
    
    
	
	function getMonWithdraw($mon,$year,$shopID)
	{
        
		$db = $this->cashRegisterDB($year,$time='');
        $this->db->select($db.'.*');
		$this->db->where('shopID',$shopID);
		$this->db->where('cashType',2);
		$this->db->where('month(time)',$mon,false);
		$this->db->where('year(time)',$year,false);
		$this->db->order_by('time');
		$query  = $this->db->get($db);
		$data = $query->result_array();
	
		return $data;
		
		
		
	}
    
    function getRentData($productID,$sellID,$shopID)
    {
       //租借檢查
			if($productID=='8880004' || $productID=='8880005' ||$productID=='8880006' )
			{
				
				
				return  $this->getRentProductBySellID($sellID,$shopID);
				
				
			}
			else return array(); 
        
        
    }
    
    
    
	function getRentProductBySellID($sellID,$shopID)
	{
		if($sellID==0) return;
		$this->db->where('sellID',$sellID);
		$this->db->where('pos_rent.shopID',$shopID);
		$this->db->join('pos_inshop_amount','pos_inshop_amount.rentID = pos_rent.rentID and pos_rent.shopID = pos_inshop_amount.shopID','left');
		$this->db->join('pos_product','pos_product.productID = pos_inshop_amount.productID','left');
	
		$query = $this->db->get('pos_rent');
		return $query->row_array();
		
	}
	function getOutputItems()
	{
		$query = $this->db->get('pos_output_items');
		return $query->result_array();
		
	}
	
	function getConsumeRecord($date,$shopID)
	{
		$sql = "SELECT *
		 		FROM pos_inshop_amount
				LEFT JOIN pos_product
				ON pos_inshop_amount.productID = pos_product.productID
				where shopID = ".$shopID." and status = -3 and date(pos_inshop_amount.time)='".$date."'
				order by pos_inshop_amount.time";
		$query = $this->db->query($sql);	
		return $query->result_array();	
		
		
	}
	function get_vistors($year,$month,$shopID)
    {
        
        $this->db->select('*,day(time) as day');
          $this->db->where('year(time)',$year);
        $this->db->where('month(time)',$month);
        $this->db->where('shopID',$shopID);
        $this->db->order_by('day','ASC');
        $query = $this->db->get('pos_vistors');
        
        return $query->result_array();
        
        
        
        
    }
    
    
    function getOwnProductList()
    {
         $query = $this->db->get('pos_own');
        return $query->result_array();
        
        
    }
	function getOwnProduct($year,$month)
    {
        
       
        $product = $this->getOwnProductList();
        $shop = $this->System_model->getShop(true);
        foreach($shop as $row)
        {
            
            $shopList[$row['shopID']] = $row;
            
            
        }
        foreach($product as $row)
        {
            
            
            $this->db->where('productID',$row['productID']);
            $this->db->where('year(time)',$year);
            $this->db->where('month(time)',$month);
            
            $query = $this->db->get('pos_product_sell');
            $p[$row['productID']]['sell'] = $query->result_array();
            $p[$row['productID']]['product'] = $row; 
            $p[$row['productID']]['shop'] =  $shopList;
            
            
            foreach( $p[$row['productID']]['sell']  as $each)
            {
                
                
                  $date = substr($each['time'],8,2);
                if(!isset($p[$row['productID']]['shop'][$each['shopID']]['m_'.(int)$date] ))$p[$row['productID']]['shop'][$each['shopID']]['m_'.(int)$date]  = 0;
                $p[$row['productID']]['shop'][$each['shopID']]['m_'.(int)$date] +=$each['num'];
                
                
                if(!isset( $p[$row['productID']]['shop'][$each['shopID']]['totalNum'])) $p[$row['productID']]['shop'][$each['shopID']]['totalNum'] =0;
            
                $p[$row['productID']]['shop'][$each['shopID']]['totalNum'] += $each['num'];
                
            }
        }
        
         return $p ;
        
        
        
        
    }
       function generateRandomNumber($invoiceNum)
    {
       $a = ord(substr($invoiceNum,0,1));
       $b = ord(substr($invoiceNum,1,1));
       
        $r = $a* (substr($invoiceNum,2,4))* $b* (substr($invoiceNum,6,4))+53180059;
        while($r>=10000)
        {
            $r = round($r/10000 +$r%10000);
        
            
            
        }
        return str_pad( $r, 4, "0" ,STR_PAD_LEFT); 
    }  
  public function getEncrypt($sStr, $sKey)
  {
     $sKey = hex2bin($sKey); 
      $iv = base64_decode("Dt8lyToo17X/XkXaQvihuA==");
        $aes_data = $sStr; //發票號碼10碼+隨機碼4碼
$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $sKey, $this->pkcs5_pad($aes_data, 16), MCRYPT_MODE_CBC, $iv);
        return base64_encode($encrypted);
    
 }

// ==== 副程式，主要是把資料長度補足 ===== //
function pkcs5_pad($text, $blocksize) {
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
}

  public function getDecrypt($sStr, $sKey) {
        $sKey = hex2bin($sKey); 
     $iv = base64_decode("Dt8lyToo17X/XkXaQvihuA==");
     return mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$sKey,base64_decode($sStr),MCRYPT_MODE_ECB, $iv);
 }

function generateInvoice($data,$depID = 1)    
{
    if(!isset($data['invoiceTime'])||empty($data['invoiceTime'])||$data['invoiceTime']==''||$data['invoiceTime']==0)
        {
            
            
            
            $data['invoiceTime']  =date('Y-m-d H:i:s');
        }
        
    $t = getdate();
    $year = substr( $data['invoiceTime'],0,4);
    $Cy = $year-1911;
    $m = substr( $data['invoiceTime'],5,2);
    if($m%2==1) $m++;
    $YearMonth = $Cy.str_pad($m,2,0,STR_PAD_LEFT);
    $data['invoice'] = $this->getInvoiceInf($YearMonth,$depID,true) ;
   if(empty($data['invoice'] )) return false;

    if($data['CarrierType']==''||$data['CarrierId1']==''||$data['CarrierId1']===0)
    {
            
        $data['CarrierType'] = '';
        $data['CarrierId1'] = '';
        $PrintMark = 'Y';
        
    }
    else 
    {
     
        $data['CarrierId1'] = str_replace(' ','+',$data['CarrierId1']);
        
        
        
        $PrintMark = 'N';
    }
    
    
    if($data['NPOBAN']!='' &&$data['NPOBAN']!=0)
    {
        $DonateMark = 1;
         $PrintMark = 'N';
    }
    else 
    {
        $DonateMark = 0;
        $data['NPOBAN'] ='';
    }
    
    
     
    
    
     $data['CarrierId2']  =$data['CarrierId1'];
     $data['BuyerIdentifier'] = trim($data['BuyerIdentifier']);
    if(strlen($data['BuyerIdentifier'])!=8 ) $data['BuyerIdentifier']='0000000000';
      $datain = array(
             'year'           => $Cy,
             'period'         => str_pad($m-1,2,0,STR_PAD_LEFT).'-'.str_pad($m,2,0,STR_PAD_LEFT),
            'InoviceDateTime' => $data['invoiceTime'] ,
             'InvoiceCode'    => $data['invoice'][0]['InvoiceTrack'],
            'InvoiceNum'      => $data['invoice'][0]['InvoiceNext'],
             'InvoiceNumber'  =>$data['invoice'][0]['InvoiceTrack']. $data['invoice'][0]['InvoiceNext'],
            'RandomNumber'    => $this->generateRandomNumber($data['invoice'][0]['InvoiceNext']),
             'total'          =>  $data['total'],
             'BuyerIdentifier'=>  $data['BuyerIdentifier'],
             'SellerIdentifier'=>'53180059',
             'shopName'       =>$data['shopName'],
             'orderNum'       =>$data['orderNum'],
            'DonateMark'      =>$DonateMark,//0：非捐贈發票 1：捐贈發票
            'CarrierType'     =>trim($data['CarrierType']),//1. 手機條碼為 3J0002  2. 自然人憑證條碼為 CQ0001
            'CarrierId1'      => trim($data['CarrierId1']),
            'CarrierId2'   => trim($data['CarrierId2']),
            'PrintMark'    =>$PrintMark,// N OR Y
            'NPOBAN'          =>trim($data['NPOBAN']), //發票捐贈對象
            'depID'           =>$depID,
            'productDetail'   =>json_encode($data['record'],true),
            'invoiceType'   =>0,
            'email'         =>$data['email']
         );
  
    

     $r =  $this->newInvoice($datain);
   
        if($r>0)
        {
              
            $update['InvoiceNext'] = intval($data['invoice'][0]['InvoiceNext'])+1;
            $update['updateTime'] = $datain['InoviceDateTime'];
            $this->db->where('id',$data['invoice'][0]['id']);
            $this->db->update('pos_einvoice_use',$update);
            
                    
            
        }
    
    
    
    
    
    
    
    
      return $datain['InvoiceNumber'];
    
    
    
    
}
    
function getInvoiceList($year,$month,$depID,$mday=0)
{
    
    $this->db->where('year(InoviceDateTime)',$year);
    $this->db->where('month(InoviceDateTime)',$month);
    if($mday!=0) $this->db->where('day(InoviceDateTime)',$mday);
    
    $this->db->where('depID',$depID);
    $this->db->order_by('invoiceNumber','DESC');
     $q = $this->db->get('pos_einvoice');
    return $q->result_array();
    
    
}
    
function newInvoice($data)    
{
  $this->db->where('InvoiceNum',$data['InvoiceNum']);
  $this->db->where('InvoiceCode',$data['InvoiceCode']);  
  $this->db->where('period',$data['period']); 
  $this->db->where('year',$data['year']);   
  $q = $this->db->get('pos_einvoice');
  if($q->num_rows()>0)
  {
      
      return false;
      
      
      
  }
  $this->db->insert('pos_einvoice',$data);
    return $this->db->insert_id();
    
}
function getEInvoiceByOrderNum($orderNum,$depID)
{
     $this->db->where('invalid',0);
    $this->db->where('void',0);
     $this->db->where('orderNum',$orderNum);
    $this->db->where('depID',$depID);
     $q = $this->db->get('pos_einvoice');
    return $q->row_array();
     
}

function  getXmloutNum($date)
{
    
    $this->db->where('date(xmlOutTime)',$date);
      $q = $this->db->get('pos_einvoice');
    $t = $q->num_rows();
    $this->db->where('date(xmlOutTime)',$date);
      $q = $this->db->get('pos_einvoice_cancel');
    $t += $q->num_rows();
    
    return $t;
    
    
}
    
function getWinList($year,$month)   
{
    if($month%2==1) $month++;
   
    $this->db->where('year',$year);
    $this->db->where('month',$month);
    $q = $this->db->get('pos_einvoice_winlist');
    $d = $q->result_array();
    if(empty($d)) return false;
    $sending = true;
    foreach($d as $row)
    {
        if($row['sending']=='0000-00-00 00:00:00'|| $row['sending']=='') $sending = false;
        
       $row['invoiceData'] =  $this->getEInvoiceData(substr($row['InvoiceNumber'],2,8),substr($row['InvoiceNumber'],0,2));
            
        $r['data'][] = $row;
        
        
    }
    $r['sending'] = $sending;
  
    return $r;

    
}
    
    
    
    
    
function getEInvoiceData($invoiceNum = 0,$invoiceCode=0,$xmlout=-1)
{
    
    $this->db->where('invalid',0);
    $this->db->where('void',0);
    if($invoiceNum!=0)
    {
        
        $this->db->where('invoiceNum',$invoiceNum);
        $this->db->where('invoiceCode',$invoiceCode);
    }
    if($xmlout!=-1)
    {
        
        $this->db->where('xmlout',$xmlout);
      
    }
    $q = $this->db->get('pos_einvoice');
    
     if($invoiceNum!=0) return $q->row_array();
     return $q->result_array();
    
    
}
    
function getCancelEInvoiceData($invoiceNum = 0,$invoiceCode=0,$xmlout=-1)
{
    
    if($invoiceNum!=0)
    {
        
        $this->db->where('invoiceNum',$invoiceNum);
        $this->db->where('invoiceCode',$invoiceCode);
    }
     if($xmlout!=-1)
    {
        
        $this->db->where('xmlout',$xmlout);
      
    }
    $q = $this->db->get('pos_einvoice_cancel');
    
     if($invoiceNum!=0) return $q->row_array();
     return $q->result_array();
    
    
}

function getConfirmEInvoiceData($invoiceNumber = 0)
{
    
    if($invoiceNumber!==0)
    {
        
        $this->db->where('invoiceNumber',$invoiceNumber);
        
    }
    $q = $this->db->get('pos_einvoice_confirm');
    
     if($invoiceNumber!==0) return $q->row_array();
     return $q->result_array();
    
    
}    
function getVoidEInvoiceData($invoiceNum = 0,$invoiceCode=0,$xmlout=-1)
{
    
    if($invoiceNum!=0)
    {
        
        $this->db->where('invoiceNum',$invoiceNum);
        $this->db->where('invoiceCode',$invoiceCode);
    }
   
      if($xmlout!=-1)
    {
        
        $this->db->where('xmlout',$xmlout);
      
    }
     $q = $this->db->get('pos_einvoice_void');
     if($invoiceNum!=0) return $q->row_array();
     return $q->result_array();
    
    
}
function getAllowanceData($allowanceNum,$xmlout=-1)    
{
    
      
    if($allowanceNum!=0)
    {
        
        $this->db->where('AllowanceNumber',$allowanceNum);
       
    }
       if($xmlout!=-1)
    {
        
        $this->db->where('xmlout',$xmlout);
      
    }
    $q = $this->db->get('pos_einvoice_allowance');
    
     if($allowanceNum!=0) return $q->row_array();
     return $q->result_array();
    
}
    
function getCancelAllowanceData($allowanceNum,$xmlout=-1)    
{
    
      
    if($allowanceNum!=0)
    {
        
        $this->db->where('AllowanceNumber',$allowanceNum);
       
    }
       if($xmlout!=-1)
    {
        
        $this->db->where('xmlout',$xmlout);
      
    }
    $q = $this->db->get('pos_einvoice_allowance_cancel');
    
     if($allowanceNum!=0) return $q->row_array();
     return $q->result_array();
    
}
    
function getRejectData($RejectInvoiceNumber)    
{
    
      
    if($RejectInvoiceNumber!==0)
    {
        
        $this->db->where('RejectInvoiceNumber',$RejectInvoiceNumber);
       
    }
    $q = $this->db->get('pos_einvoice_reject');
    
     if($RejectInvoiceNumber!==0) return $q->row_array();
     return $q->result_array();
    
}    
   
    
    
    //A0301
function B0601xml($invoiceInf,$type="B0601")
{
    $t = explode(' ',$invoiceInf['InvoiceDateTime']);
   $c = explode(' ',$invoiceInf['RejectDateTime']);
    $users_array= array(
                'RejectInvoiceNumber'=>$invoiceInf['RejectInvoiceNumber'],
                'InvoiceDate'=> str_replace('-','',$t[0]),
                'BuyerId'=> $invoiceInf['BuyerIdentifier'], 
                'SellerId'=>$invoiceInf['SellerIdentifier'],
                'RejectDate'=>str_replace('-','',$c[0]),
                'RejectTime'=>$c[1],
                'RejectReason'=>$invoiceInf['RejectReason'],
                'Remark'    =>$invoiceInf['Remark']
        );

   $fileName =$_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice/reject_test.xml';  
        //creating object of SimpleXMLElement
        $xml_user_info = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><RejectInvoice xmlns="urn:GEINV:eInvoiceMessage:'.$type.':3.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:GEINV:eInvoiceMessage:'.$type.':3.2 '.$type.'.xsd"></RejectInvoice>');

        //function call to convert array to xml
        $this->array_to_xml($users_array,$xml_user_info);

        //saving generated xml file
        $xml_file = $xml_user_info->asXML($fileName);

        //success and error message based on xml creation
        if($xml_file){
            echo 'XML file have been generated successfully.';
        }else{
            echo 'XML file generation error.';
        }       
    
    
}        
    
    
function D0501xml($invoiceInf,$type="D0501")
{
    $t = explode(' ',$invoiceInf['AllowanceDateTime']);
   $c = explode(' ',$invoiceInf['CancelDateTime']);
    
    $dirMain = $_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice_xml/'.date('Y-m-d');
        if(!is_dir($dirMain)) mkdir($dirMain);
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
    $users_array= array(
                'CancelAllowanceNumber'=>$invoiceInf['AllowanceNumber'],
                'AllowanceDate'=> str_replace('-','',$t[0]),
                'BuyerId'=> $invoiceInf['BuyerIdentifier'], 
                'SellerId'=>$invoiceInf['SellerIdentifier'],
                'CancelDate'=>str_replace('-','',$c[0]),
                'CancelTime'=>$c[1],
                'CancelReason'=>$invoiceInf['CancelReason'],
                'Remark'    =>$invoiceInf['Remark']
        );

    
        $fileName =$dirsub.'/'.$invoiceInf['id'].'.xml';   
    
        //creating object of SimpleXMLElement
        $xml_user_info = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><CancelAllowance xmlns="urn:GEINV:eInvoiceMessage:'.$type.':3.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:GEINV:eInvoiceMessage:'.$type.':3.2 '.$type.'.xsd"></CancelAllowance>');

        //function call to convert array to xml
        $this->array_to_xml($users_array,$xml_user_info);

        //saving generated xml file
        $xml_file = $xml_user_info->asXML($fileName);

        //success and error message based on xml creation
        if($xml_file){
            $this->db->where('id',$invoiceInf['id']);
            $this->db->update('pos_einvoice_allowance_cancel',array('xmlOut'=>1,'xmlOutTime'=>date('Y-m-d H:i:s')));
            
        }else{
            
            mb_internal_encoding('UTF-8');
            $title = '發票XML file generation error'.date('Y-m-d H:i:s');
            $textResult= json_encode($users_array);
            $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			
            $this->Mail_model->myEmail('lintaitin@gmail.com',$title,$textResult,$headers);  
        }       
    
}    
    
    
function D0401xml($invoiceInf,$type='D0401')
{
    $t = explode(' ',$invoiceInf['AllowanceDateTime']);
             $dirMain = $_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice_xml/'.date('Y-m-d');
        if(!is_dir($dirMain)) mkdir($dirMain);
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
    
    $users_array['Main']= array(
                'AllowanceNumber'=>$invoiceInf['AllowanceNumber'],
                'AllowanceDate'=> str_replace('-','',$t[0]),
                'Seller'=> array(
                    'Identifier'      =>$invoiceInf['SellerIdentifier'],
                    'Name'            =>$invoiceInf['SellerName'],
                    'Address'         =>$invoiceInf['SellerAddress'],
                    'PersonInCharge'  =>$invoiceInf['SellerPersonInCharge'],
                    'TelephoneNumber' =>$invoiceInf['SellerTelephoneNumber'],
                    'FacsimileNumber' =>$invoiceInf['SellerFacsimileNumber'],
                    'EmailAddress'    =>$invoiceInf['SellerEmailAddress'],
                    'CustomerNumber'  =>$invoiceInf['SellerCustomerNumber'],
                    'RoleRemark'      =>$invoiceInf['SellerRoleRemark'],
                ),
                'Buyer'=>array(
                    'Identifier'      =>$invoiceInf['BuyerIdentifier'],
                    'Name'            =>$invoiceInf['BuyerName'],
                    'Address'         =>$invoiceInf['BuyerAddress'],
                    'PersonInCharge'  =>$invoiceInf['BuyerPersonInCharge'],
                    'TelephoneNumber' =>$invoiceInf['BuyerTelephoneNumber'],
                    'FacsimileNumber' =>$invoiceInf['BuyerFacsimileNumber'],
                    'EmailAddress'    =>$invoiceInf['BuyerEmailAddress'],
                    'CustomerNumber'  =>$invoiceInf['BuyerCustomerNumber'],
                    'RoleRemark'      =>$invoiceInf['BuyerRoleRemark'],
                
                ),
                'AllowanceType' =>2 ,  //1:買方開立折讓證明單;2:賣方折讓證明單通知
                'Attachment'    =>$invoiceInf['Attachment']
        );
   
        $users_array['Details'] = json_decode($invoiceInf['productDetail'],true);
       
        $users_array['Amount']=
            array(
            'TaxAmount' =>$invoiceInf['TaxAmount'],
            'TotalAmount'=>$invoiceInf['TotalAmount']
        
        
        
        );
    
    
       

    
        $fileName =$dirsub.'/'.$invoiceInf['id'].'.xml';   
        //creating object of SimpleXMLElement
        $xml_user_info = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Allowance xmlns="urn:GEINV:eInvoiceMessage:'.$type.':3.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:GEINV:eInvoiceMessage:'.$type.':3.2 '.$type.'.xsd"></Allowance>');

        //function call to convert array to xml
        $this->array_to_xml($users_array,$xml_user_info);

        //saving generated xml file
        $xml_file = $xml_user_info->asXML($fileName);

          //success and error message based on xml creation
        if($xml_file){
            $this->db->where('id',$invoiceInf['id']);
            $this->db->update('pos_einvoice_allowance',array('xmlOut'=>1,'xmlOutTime'=>date('Y-m-d H:i:s')));
            
        }else{
            
            mb_internal_encoding('UTF-8');
            $title = '發票XML file generation error'.date('Y-m-d H:i:s');
            $textResult= json_encode($users_array);
            $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			
            $this->Mail_model->myEmail('lintaitin@gmail.com',$title,$textResult,$headers);  
        }       
    
    
}    
    
    
function C0701xml($invoiceInf)
{ $type ='C0701';
    $t = explode(' ',$invoiceInf['InvoiceDateTime']);
    $c = explode(' ',$invoiceInf['VoidDateTime']);
        $dirMain = $_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice_xml/'.date('Y-m-d');
        if(!is_dir($dirMain)) mkdir($dirMain);
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
    $users_array= array(
                'VoidInvoiceNumber'=>$invoiceInf['InvoiceCode'].$invoiceInf['InvoiceNum'],
                'InvoiceDate'=> str_replace('-','',$t[0]),
                'BuyerId'=> $invoiceInf['BuyerIdentifier'], 
                'SellerId'=>$invoiceInf['SellerIdentifier'],
                'VoidDate'=>str_replace('-','',$c[0]),
                'VoidTime'=>$c[1],
                'VoidReason'=>$invoiceInf['VoidReason'],
                'Remark'    =>$invoiceInf['Remark']
        );


    
        $fileName =$dirsub.'/'.$invoiceInf['id'].'.xml';   
    
        //creating object of SimpleXMLElement
        $xml_user_info = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><VoidInvoice xmlns="urn:GEINV:eInvoiceMessage:C0701:3.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:GEINV:eInvoiceMessage:C0701:3.2 C0701.xsd"></VoidInvoice>');

        //function call to convert array to xml
        $this->array_to_xml($users_array,$xml_user_info);

        //saving generated xml file
        $xml_file = $xml_user_info->asXML($fileName);

        //success and error message based on xml creation
        if($xml_file){
            $this->db->where('id',$invoiceInf['id']);
            $this->db->update('pos_einvoice_void',array('xmlOut'=>1,'xmlOutTime'=>date('Y-m-d H:i:s')));
            
        }else{
            
            mb_internal_encoding('UTF-8');
            $title = '發票XML file generation error'.date('Y-m-d H:i:s');
            $textResult= json_encode($users_array);
            $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			
            $this->Mail_model->myEmail('lintaitin@gmail.com',$title,$textResult,$headers);  
        }       
    
    
}    

    
function A0102xml($invoiceInf,$type="A0102")   
{
     $t = explode(' ',$invoiceInf['InvoiceDateTime']);
    $c = explode(' ',$invoiceInf['ReceiveDateTime']);
    
    
  
    $users_array= array(
                'InvoiceNumber'=>$invoiceInf['InvoiceNumber'],
                'InvoiceDate'=> str_replace('-','',$t[0]),
                'BuyerId'=> $invoiceInf['BuyerIdentifier'], 
                'SellerId'=>$invoiceInf['SellerIdentifier'],
                'ReceiveDate'=>str_replace('-','',$c[0]),
                'ReceiveTime'=>$c[1],
                'BuyerRemark'=>$invoiceInf['BuyerRemark'],
                'Remark'    =>$invoiceInf['Remark']
        );
    
  
    
    $users_array['Remark'] = $invoiceInf['Remark'];
   $fileName =$_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice/confirm_test.xml';  
        //creating object of SimpleXMLElement
        $xml_user_info = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><InvoiceConfirm  xmlns="urn:GEINV:eInvoiceMessage:'.$type.':3.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:GEINV:eInvoiceMessage:'.$type.':3.2 '.$type.'.xsd"></InvoiceConfirm>');

        //function call to convert array to xml
        $this->array_to_xml($users_array,$xml_user_info);

        //saving generated xml file
        $xml_file = $xml_user_info->asXML($fileName);

        //success and error message based on xml creation
        if($xml_file){
            echo 'XML file have been generated successfully.';
        }else{
            echo 'XML file generation error.';
        }       
    
    
    
}
    
    
function C0501xml($invoiceInf,$type="C0501")
{
    $t = explode(' ',$invoiceInf['InvoiceDateTime']);
    $c = explode(' ',$invoiceInf['CancelDateTime']);
    
    
  
    $users_array= array(
                'CancelInvoiceNumber'=>$invoiceInf['InvoiceCode'].$invoiceInf['InvoiceNum'],
                'InvoiceDate'=> str_replace('-','',$t[0]),
                'BuyerId'=> $invoiceInf['BuyerIdentifier'], 
                'SellerId'=>$invoiceInf['SellerIdentifier'],
                'CancelDate'=>str_replace('-','',$c[0]),
                'CancelTime'=>$c[1],
                'CancelReason'=>$invoiceInf['CancelReason'],
                'Remark'    =>$invoiceInf['Remark']
        );
    
    if($type=='A0201'||$type=='A0501')$users_array['ReturnTaxDocumentNumber'] = $invoiceInf['ReturnTaxDocumentNumber'];
    
    $users_array['Remark'] = $invoiceInf['Remark'];
       
        $dirMain = $_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice_xml/'.date('Y-m-d');
        if(!is_dir($dirMain)) mkdir($dirMain);
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
    
        $fileName =$dirsub.'/'.$invoiceInf['id'].'.xml';   
    
    
        //creating object of SimpleXMLElement
        $xml_user_info = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><CancelInvoice xmlns="urn:GEINV:eInvoiceMessage:'.$type.':3.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:GEINV:eInvoiceMessage:'.$type.':3.2 '.$type.'.xsd"></CancelInvoice>');

        //function call to convert array to xml
        $this->array_to_xml($users_array,$xml_user_info);

        //saving generated xml file
        $xml_file = $xml_user_info->asXML($fileName);

          //success and error message based on xml creation
        if($xml_file){
            $this->db->where('id',$invoiceInf['id']);
            $this->db->update('pos_einvoice_cancel',array('xmlOut'=>1,'xmlOutTime'=>date('Y-m-d H:i:s')));
            
        }else{
            
            mb_internal_encoding('UTF-8');
            $title = '發票XML file generation error'.date('Y-m-d H:i:s');
            $textResult= json_encode($users_array);
            $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			
            $this->Mail_model->myEmail('lintaitin@gmail.com',$title,$textResult,$headers);  
        }         
    
    
}
    
function C0401xml($invoiceInf,$type= 'C0401')
{   
  
    deldir($_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice_xml/'.date('Y-m-d',mktime(0, 0, 0, date("m")-3, date("d"),   date("Y"))));
    
    $dirMain = $_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice_xml/'.date('Y-m-d');
        if(!is_dir($dirMain)) mkdir($dirMain);
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
    
        
        if($invoiceInf['BuyerIdentifier']=="00000000")$BuyerIdentifier = "0000000000";
        else $BuyerIdentifier=$invoiceInf['BuyerIdentifier'];
            $t = explode(' ',$invoiceInf['InoviceDateTime']);
 
    $invoiceType = '07';
    
        $users_array['Main'] = array(
                'InvoiceNumber'=>$invoiceInf['InvoiceCode'].$invoiceInf['InvoiceNum'],
                'InvoiceDate'=> str_replace('-','',$t[0]),
                'InvoiceTime'=> $t[1],
                'Seller'=> array(
                    'Identifier'=>$invoiceInf['SellerIdentifier'],
                    'Name'      =>'幻遊天下股份有限公司',
                    'Address'   =>'新北市板橋區華東里南雅南路２段１１―２６號',       
                    'PersonInCharge'=>'黃家樺',
                    'TelephoneNumber'=>'0286719616',
                    'FacsimileNumber'=>'0286719006',
                    'EmailAddress'   =>'service@phantasia.tw',
                    'CustomerNumber' =>'',
                    'RoleRemark'=>'',
                ) ,   
                'Buyer'=> array(
                    'Identifier'=>$BuyerIdentifier,
                    'Name'      =>$BuyerIdentifier,
                    
                    'Address'   =>'',       
                    'PersonInCharge'=>'',
                    'TelephoneNumber'=>'',
                    'FacsimileNumber'=>'',
                    'EmailAddress'   =>'',
                    'CustomerNumber' =>'',
                    'RoleRemark'=>'',
                ),    
                'CheckNumber'=>'',
                'BuyerRemark'=>1,
              
               'MainRemark'=>'',
             'CustomsClearanceMark'=>'1',
               'Category'=>'St',
               'RelateNumber'=>'',
            
               'InvoiceType'=>$invoiceType,/// 發票類別  詳細定義請參考InvoiceTypeEnum 資料元規格
        ///  01：三聯式
        /// 02：二聯式
        /// 03：二聯式收銀機
        /// 04：特種稅額
        /// 05：電子計算機
        /// 06：三聯式收銀機
        /// 07：一般稅額計算之電子發票
        /// 08：特種稅額計算之電子發票
               'DonateMark'=>$invoiceInf['DonateMark'] 
               
        
        );
     if($type=='C0401')
    { 
         
        $users_array['Main']['CarrierType']=$invoiceInf['CarrierType'] ; //存入載具或是捐贈的 不可印出 PrintMark 必為N
        $users_array['Main']['CarrierId1']=$invoiceInf['CarrierId1'] ;
        $users_array['Main']['CarrierId2']=$invoiceInf['CarrierId2'] ;
        $users_array['Main']['PrintMark']=$invoiceInf['PrintMark'] ;
        $users_array['Main']['NPOBAN']=$invoiceInf['NPOBAN'] ;
        $users_array['Main']['RandomNumber']=$invoiceInf['RandomNumber'] ;
     
             
             
     }
     
    
    
    $users_array['Details'] = json_decode($invoiceInf['productDetail'],true);
    
    $SalesAmount = $invoiceInf['total'];
    $TaxAmount = 0;
    if( $users_array['Main']['Buyer']['Identifier']!='0000000000')
    {
        $SalesAmount = round($invoiceInf['total']/1.05);
    
        $TaxAmount = $invoiceInf['total']-$SalesAmount;
    }
    
    
    if($type=='A0401'||$type == 'A0101')
    {
         $users_array['Amount'] =array(
            'SalesAmount'    =>$SalesAmount,
            'TaxType' =>1  ,
            'TaxRate' =>0.05,
            'TaxAmount'=> $TaxAmount,
            'TotalAmount'=>$invoiceInf['total'],
            'DiscountAmount' =>0,
            'OriginalCurrencyAmount'=>0,
            'ExchangeRate'=>0
                );
        
        
    }
    else
    {
    
    
    
        $users_array['Amount'] =array(
            'SalesAmount'    =>$SalesAmount,
        'FreeTaxSalesAmount' => 0,
        'ZeroTaxSalesAmount' => 0 ,
            'TaxType' =>1   ,
            'TaxRate' =>0.05,
            'TaxAmount'=> $TaxAmount,
            'TotalAmount'=>$invoiceInf['total']);
    }
    
    
     
      
       
    
    
        $fileName =$dirsub.'/'. $users_array['Main']['InvoiceNumber'].'_'.$invoiceInf['id'].'.xml';  
        //creating object of SimpleXMLElement
        $xml_user_info = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Invoice  xmlns="urn:GEINV:eInvoiceMessage:'.$type.':3.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:GEINV:eInvoiceMessage:'.$type.':3.2 '.$type.'.xsd"></Invoice>');

        //function call to convert array to xml
        $this->array_to_xml($users_array,$xml_user_info);

        //saving generated xml file
        $xml_file = $xml_user_info->asXML($fileName);

  //success and error message based on xml creation
        if($xml_file){
            $this->db->where('id',$invoiceInf['id']);
            $this->db->update('pos_einvoice',array('xmlOut'=>1,'xmlOutTime'=>date('Y-m-d H:i:s')));
            
        }else{
            
            mb_internal_encoding('UTF-8');
            $title = '發票XML file generation error'.date('Y-m-d H:i:s');
            $textResult= json_encode($users_array);
            $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			
            $this->Mail_model->myEmail('lintaitin@gmail.com',$title,$textResult,$headers);  
        }       
          
                 
                    if($invoiceInf['depID']==3)
                    {
                        $title = '幻遊天下股份有限公司(瘋桌遊購物商城)電子發票開立通知【'.$invoiceInf['InvoiceNumber'].'】';
                         $content = '<img src="https://mart.phantasia.tw/images/logo.png" width="200px"><h1 style="line-height:2;">'.$title.'</h1><p style="width:100%; margin-top:20px;">親愛的顧客您好!</p>'.
                             '<h2>感謝您訂購瘋桌遊購物的產品！</h2>'.
                            '<h3>訂單編號：'.$invoiceInf['orderNum'].'發票已經開立，發票號碼：'. $invoiceInf['InvoiceNumber'] .'，<br/>電子發票不直接寄送，請至網站<a target="_blank" href="http://mart.phantasia.tw/member">「帳號專區-訂單查詢」</a>查看發票內容或索取正本。(財政部核准文號：PF00170431號)<br/>

                            若您有索取正本或是有發票中獎的情形，我們會寄發紙本至訂購人地址。</h3>


                            <p>若想了解詳細資訊,<br/>請到帳號專區/<a target="_blank" href="http://mart.phantasia.tw/member">訂單查詢</a></p><br/>
                            <p>如有問題或意見，請聯繫我們</p>
                            <p>感謝您的訂購!</p>
                            <h4>若需索取紙本發票請來信至 service@phantasia.tw 將有專人為您服務</h4>    

                            <p style="width:100%; margin-top:10px;">
                            [ 防詐騙提醒 ]<br/>
                            ＊不要依指示到ATM前操作<br/>
                            ＊接獲+字號開頭的電話勿理會<br/>
                            ＊客服人員不會主動打電話聯絡誤設多期扣款<br/><br/>  

                            在您完成繳費程序之後，瘋桌遊購物商城不會以電話要求購買人操作ATM進行轉帳或<br/>
                            重新設定、變更付款條件及程序、要求您設定成分期付款或是購買點數，<br/>
                            若有接到類似電話，請立即來電(02)8671-9616向客服詢問或<br/>
                            透過官方網頁查詢商品購買進度。<br/><br/>

                            如有任何疑問，可撥打客服專線 0909-103913<br/><br/>

                            服務時間:<br/>
                            周一 ~ 周五 上午10:00 ~ 17:00<br/> <br/>

                            國定假日及假日非服務時間，可先透過客服信箱反應，將於服務時間內盡快回覆。</p >';
                         
                            
                    }
                    else
                    {
                        $code =  md5('IlovePhantasia'.$invoiceInf['InvoiceNumber']);
                        $url='https://mart.phantasia.tw/order/show_invoice/'.$invoiceInf['InvoiceNumber'].'/'.$code;
                        $title = '幻遊天下股份有限公司(瘋桌遊)電子發票開立通知【'.$invoiceInf['InvoiceNumber'].'】';
                        $content='<h3>親愛的客戶您好：</h3><h4>您的電子發票已開立，請上此網址<a href="'.$url.'">'.$url.'</a>下載電子發票證明聯</h4>';
                        $content .='幻遊天下股份有限公司(此信為系統自動發出，請勿回覆此信)<br/>';
                        $content .='若您對發票有任何疑問，歡迎來信至service@phantasia.tw 洽詢<br/>';
                        $content .='或於上班時間來電客服專線 0909-103913<br/>';
                    }
            mb_internal_encoding('UTF-8');
			
			$headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
            $eStr = '';
			if(!empty($invoiceInf['email']) && strpos($invoiceInf['email'],'@')>0)
			{
				$this->Mail_model->myEmail($invoiceInf['email'],$title,$content,$headers);
                //$this->Mail_model->myEmail('phantasia.ac@gmail.com,lintaitin@gmail.com',$title,$content,$headers);
				//$this->Mail_model->myEmail(,$title,$content,$headers);
                $eStr = ' 已發送客戶信箱:'.$invoiceInf['email'];
			}	
                  
            return $invoiceInf['shopName'].'發票【<a href="'.$url.'">'.$invoiceInf['InvoiceNumber'].'</a>】已開立'.$eStr;
                 
                 
                    
                 
               
    
    
    
}
    
function getAllDep($shopID = 0)    
{
    if($shopID!=0) $this->db->where('shopID',$shopID);
    $q = $this->db->get('pos_einvoice_dep');
    return $q->result_array();
}
    
    

function getInvoiceInf($YearMonth,$depID=0,$thisMon = false)    
{
    
    if($thisMon==false) $this->db->where('YearMonth >=',$YearMonth);
     else $this->db->where('YearMonth',$YearMonth);
    if($depID!=0) $this->db->where('depID',$depID);
    $this->db->where('InvoiceNext <= InvoiceEndNo');
    $this->db->order_by('id','ASC');
      $q = $this->db->get('pos_einvoice_use');
      return  $q->result_array();
    
    
    
}
    
function getAllTrack($YearMonth)    
{
    
    $this->db->where('YearMonth',$YearMonth);
    $this->db->group_by('InvoiceTrack');
    $q = $this->db->get('pos_einvoice_use');
    
    return  $q->result_array();
}
    
function E0402xml($YearMonth,$InvoiceTrack) 
{
    $type ='E0402';
    $dirMain = $_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/invoice_xml/'.date('Y-m-d');
        if(!is_dir($dirMain)) mkdir($dirMain);
        $dirsub = $dirMain.'/'.$type;
        if(!is_dir($dirsub)) mkdir($dirsub);
    
        $this->db->where('YearMonth',$YearMonth);
        $this->db->where('InvoiceTrack',$InvoiceTrack);
       
        $q = $this->db->get('pos_einvoice_use');
        $invoiceInf = $q->result_array();
        if(!empty($invoiceInf))
        {
            $users_array['Main'] = array(
                    'HeadBan'  =>53180059,
                    'BranchBan'=>53180059,
                    'InvoiceType'=>'07',
                    'YearMonth'   =>$invoiceInf[0]['YearMonth'],
                    'InvoiceTrack'=>$invoiceInf[0]['InvoiceTrack']
                    );
   
    
            foreach($invoiceInf as $row)
            {
                //沒用完
                if($row['InvoiceNext']<$row['InvoiceEndNo'])
                $users_array['Details'][] = array(
                    'InvoiceBeginNo'=> $row['InvoiceNext'],
                    'InvoiceEndNo'  =>$row['InvoiceEndNo']
                
                );
                
            }
            

       


            $fileName =$dirsub.'/'.$YearMonth.$InvoiceTrack.'.xml';  
            //creating object of SimpleXMLElement
            $xml_user_info = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><BranchTrackBlank   xmlns="urn:GEINV:eInvoiceMessage:'.$type.':3.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:GEINV:eInvoiceMessage:'.$type.':3.2 '.$type.'.xsd"></BranchTrackBlank >');

            //function call to convert array to xml
            $this->array_to_xml($users_array,$xml_user_info,'BranchTrackBlankItem');

            //saving generated xml file
            $xml_file = $xml_user_info->asXML($fileName);
            }
  //success and error message based on xml creation
        if($xml_file){
             foreach($invoiceInf as $row)
             {
                $this->db->where('id',$row['id']);
                $this->db->update('pos_einvoice_use',array('xmlOut'=>1,'xmlOutTime'=>date('Y-m-d H:i:s')));
             }
        }else{
            
            mb_internal_encoding('UTF-8');
            $title = '發票XML file generation error'.date('Y-m-d H:i:s');
            $textResult= json_encode($users_array);
            $headers = "Content-type: TEXT/HTML;CHARSET=utf-8\r\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
			
            $this->Mail_model->myEmail('lintaitin@gmail.com',$title,$textResult,$headers);  
        } 
    
        
    
    
}
    
  function registerIO($in,$out,$credit,$aid,$note,$account,$cashType,$checkID,$shopID,$outputItem=0)
	{
		
		
		$datain['time'] =  date("Y-m-d H:i:s");
		$datain['cashType'] =  $cashType;
		$datain['outputItem'] =  $outputItem;
		$datain['MIN'] =  $in;
		$datain['MOUT'] =  $out;
		$datain['credit'] =  $credit;
		$datain['aid'] =  $aid;
		$datain['note'] =  $note;
      $datain['checkID'] =$checkID;
		if(!isset($datain['remain']))$datain['remain']=0;
		
		
		$data = $this->getRegister($shopID);
		//銷售，或非全部相同
		if(
			$datain['note'] == 'sales' ||
			!(	$data['cashType'] ==$datain['cashType'] &&
				$data['outputItem'] ==$datain['outputItem'] &&
				$data['MIN'] ==$datain['MIN'] &&
				$data['MOUT'] ==$datain['MOUT'] &&
				$data['credit'] ==$datain['credit'] &&
				$data['note'] ==$datain['note'] &&
              $data['checkID'] ==$datain['checkID'] &&
				(strtotime($datain['time'])-strtotime($data['time']))<5
		  	  )
			
		 )
		{
		
			$this->db->where($datain);
			$query = $this->db->get('pos_cash_register');
			$buffer = false;
			if($query->num_rows()==0)
			{
				$data = $this->getRegister($shopID);
                
                $len = mb_strlen($note);
                $resultStr = '';
                for($i=0;$i<$len;$i++)
                {
                    $tempStr  = mb_substr($note,$i,1,'UTF-8');
                    if( preg_match("/[\x7f-\xff]/", $tempStr))
                        $resultStr.='\\'.$tempStr;
                    else $resultStr.= $tempStr;
                }
                $datain['note'] = $resultStr;
			     $datain['aid'] = $account;
                $datain['shopID'] = $shopID ;               
                
				$datain['remain'] =  $data['remain']+ $in - $out;	
				$this->db->insert('pos_cash_register',$datain);
				$result['id'] = $this->db->insert_id();
                $datain['checkID'] = $result['id'];
				$result['remain'] = $datain['remain'];	
				$buffer = true;
			}
			else
			{
				
				$result['id'] = $data['id'];
				$result['remain'] = $data['remain'];
			}
			
			//if($buffer)echo $this->paser->ECPost('accounting/register_io',$datain);
		}
		else  $result['remain'] = $data['remain'];
		 
		return $result;
	}
   function getCreditName($t)
  {
     switch ($t)
     {
      case 1: return '信用卡'; break;
       case 2: return '悠遊卡'; break;
        case 3: return 'Line Pay'; break;
       case 4: return '街口支付'; break;
     }
      
  } 

  //function defination to convert array to xml
function array_to_xml($array, &$xml_user_info,$loopItem = 'ProductItem' ) {
    foreach($array as $key => $value) {
        if(is_array($value)) {
            if(!is_numeric($key)){
                $subnode = $xml_user_info->addChild("$key");
                $this->array_to_xml($value, $subnode,$loopItem);
            }else{
            
                $subnode = $xml_user_info->addChild($loopItem);
                $this->array_to_xml($value, $subnode,$loopItem);
            }
        }else {
            $xml_user_info->addChild("$key",htmlspecialchars("$value"));
        }
    }
}

        
}

?>
