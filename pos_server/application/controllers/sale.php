<?php
class Sale extends POS_Controller {

	function Sale()
	{
		parent::POS_Controller();
      
               
		
	}
	private function iframeConfirm()
	{
		$this->load->model('System_model');
		$shopID =  $this->uri->segment(3);
		$licence =  $this->uri->segment(4);
      
        
        	
        if($this->System_model->chkShop($shopID,$licence))
		{
			$this->session->set_userdata('aid', 999);
            
			$this->session->set_userdata('shopID',  $this->uri->segment(3));
			$this->data['shopID'] = $this->uri->segment(3);
            
          
		
		}
		return;
		
	}
	function index()
	{
      
		$this->iframeConfirm();
		$this->load->model('System_model');
		
		
		$this->data['js'] = $this->preload->getjs('pos_accounting');
		$this->data['shop'] = $this->System_model->getShop(true);
		//$this->data['cashInRegister'] = $data['remain'];
        
		$this->data['display'] = 'sale';
      //print_r($_SESSION);
		$this->load->view('sale',$this->data);	
	}
	function online()
	{
		
		$this->data['js'] = $this->preload->getjs('geo');
		$this->load->model('System_model');
		$this->data['js'] = $this->preload->getjs('pos_accounting');
		//$this->data['cashInRegister'] = $data['remain'];
		$this->data['shop'] = $this->System_model->getShop(true);

		$this->data['display'] = 'sale';
		$this->load->view('template',$this->data);	
	}	
	function map_insert()
	{
		$this->load->model('Member_model');
		$members = $this->Member_model->getAllMember(0);
		foreach( $members as $row)
		{
			
			$data =  $this->paser->post('http://maps.google.com/maps/api/geocode/json?address='.$row['address'].'&sensor=true',array(),true);
			$datain = array(
				'latitude' => $data['results'][0]['geometry']['location']['lat'],
				'longitude'=> $data['results'][0]['geometry']['location']['lng']
			
			);
			$this->db->where('memberID',$row['memberID']);
			$this->db->update('pos_pha_members',$datain);
			echo $row['memberID'];
			 sleep(1) ;
		}
	}
	
	function rfm()
	{
		
		
		$this->load->model('Sale_model');
		$shopID = $this->input->post('shopID');	
		if($shopID==0)$shopID =  $this->uri->segment(3);	
		$from =  $this->uri->segment(4);	
		$to =  $this->uri->segment(5);	
		$start =  $this->uri->segment(6);	
		$data['rfm'] = $this->Sale_model->getRfm($shopID,$from,$to,$start);
		$data['from'] = $from;
		$data['to'] = $to;
		$this->load->view('sale_rfm',$data);
		
		
		
		
	}
	
	function member_sell()
	{
		
		
		$this->load->model('Sale_model');
		$time =  $this->uri->segment(3);	
		$data['member'] = $this->Sale_model->getMemberSale($time);
		$this->load->view('sale_member_sell',$data);
		
		
		
		
	}
	
	
	function map()
	{
		$this->load->model('Member_model');
		$data['coord'] = json_encode($this->Member_model->getCoord());
		$address = array(
			'新北市板橋區南雅南路二段 11-26 號',
			'新北市板橋區南雅南路二段 11-26 號',
			'台北市大安區通化街28巷2號',
			'新竹市建美路33號',
			'新北市新店區寶安街60-1號',
			'桃園縣桃園市復興路320號',
			'台中市西區中港路一段360巷42號',
			'桃園縣中壢市興仁路二段67巷43號',
			'新北市中和區自立路2巷7號',
			'台中市南屯區豐富路310號',
			'高雄市岡山區文賢路43號',
			'桃園縣蘆竹鄉忠孝西路175巷14號1F',
			'台北市北投區光明路2巷9號',
			'台中市后里區墩南里南村路508號',
			'台北市松山區三民路102巷20號',
			'新北市汐止區仁愛路160號',
			'新北市新莊區中安街39號',
			'彰化縣彰化市景宗街16號',
			'新竹縣竹北市嘉豐二街二段8號',
			'新北市土城區裕民路259巷24號',
			'新北市三峽區大義路221號',
			'台中市西屯區文華路162巷32號',
			'台中市霧峰區振興街118巷3號',
			'新北市永和區永貞路401號',
			'高雄市左營區修明街122號',
			'台北市士林區忠義街161號',
			'桃園市平鎮區文化街278號',
			'南投縣南投市光明南路60巷1號',
			'台中市東區忠孝路242號',
			'苗栗縣竹南鎮民治街3號',
			'新北市蘆洲區中山一路8號',
			'高雄市左營區高鐵路115號B2彩虹廊',
			'高雄市苓雅區苓雅一路25號',
            '高雄市鳳山區光復路79號',
            '新竹市關東路143號',
            '桃園市龍潭區中興路402號',
            '新北市板橋區懷德街174巷18號',
            '苗栗縣頭份市忠孝二路69號',
            '台中市北屯區崇德路二段439號'
			
			
			
		);
		$address[100] ='新北市板橋區南雅南路二段 11-26 號';
        if(isset($address[$this->data['shopID']]))
        {
            $data['address'] = $address[$this->data['shopID']];
        }
        else $data['address'] = 	$address[100];
		
		$this->load->view('sale_map',$data);
	}
	function analysis()
	{
		$this->load->model('System_model');
		$year = $this->input->post('year');	
		$mon = $this->input->post('mon');
		$day = $this->input->post('day');	
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
		
		$data['date'] =$year.'-'.$mon;
		$date = $year.'-'.$mon.'-'.$data['mday'];
		if($this->data['shopID']==0)$shopID =  $this->input->post('shopID');
		else $shopID = $this->data['shopID'];		
		$data['shopData'] = $this->System_model->getShopByID($shopID);
		$data['assignShopID'] = $shopID;
		$this->data['display'] = 'sale_view';
		$this->load->view('sale_view',$data);	
	
		
	}	
	
	
	function get_year_chart()
	{
		
		$this->load->helper('chart');

					
		$date = $this->uri->segment(3);
		$dateList = explode('-',$date);
		$year = $dateList[0];
		$month = $dateList[1];
		$shopID =  $this->uri->segment(4);
		$creareFile =false;
		$time = getdate();
		if($year<$time['year']||($year==$time['year']&&$month<$time['mon']))
		{
			
				$file = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/sale/month_sale_'.$date.'_'.$shopID.'.txt';
			if(file_exists($file))
			{
					$handle = fopen($file,'r');
					$contents = '';
				while(!feof($handle))
				{$contents .= fgets($handle);}
				fclose($handle);	
				echo $contents;
				
				exit(1);
				
			}
			else $creareFile = true;
			
			
			
				
	
					
				
		}
		
	
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		$consumption = $this->Accounting_model->getConsumption();
		
		$this->load->model('System_model');
		
		if($shopID!=0)	$shop = $this->System_model->getShopByID($shopID);
		else $shop['name'] = '全省平均';
		$chart = new open_flash_chart();
		$title = new title( $shop['name'].'逐月營業走勢圖' );
		$color = array('FF37FD','FF9D37','37FF9D','DDDD37','99FF37','37FF39','37FDFF','9D37FF','3799FF','3937FF','FF3937','FF3799');		
		$title->set_style( "{font-size: 20px; color: #A2ACBA; text-align: center;}" );
		$chart->set_title( $title );		
		$data['total']  = 0 ;
		$data['verifyKey'] = true;
		ini_set('memory_limit','3048M');

		//$date = $date.'-'.$data['mday'];
		//$data['record'] = $this->Accounting_model->getDayReport($data['date'],$shopID);
		$data['record'] = $this->Accounting_model->getYearReport($year,$shopID,'<=',$month-1);
	
		$data['record2'] = $this->Accounting_model->getYearReport($year-1,$shopID,'>=',$month);
	
		
		$data['verify'] = 0 ;
		$data['monVerify'] = 0 ;
		$data['place'] =0;
		for($i=1;$i<=10;$i++)
		{
			for($j=0;$j<=12;$j++)
			{
				$dayList[] = $j;
				$data['chartData'][$i][$j] = 0;
			}	
			
		}
		
		$maxValue = 0;
		
	
		foreach($data['record'] as $row)
		{
				$thisMon = (int)substr($row['time'],5,2);
				$thisYear = (int)substr($row['time'],0,4);
				$mon = (12 - $month)+$thisMon;
				if($mon>12) continue;
				
				$shopNum = $this->shopNumDetect($thisYear,$thisMon);
				
					
				if($row['type']=='') $row['type']= 8;
				if($shopID!=0)	$data['chartData'][$row['type']][(int)$mon] +=$row['sellPrice']*$row['sellNum'];
				else $data['chartData'][$row['type']][(int)$mon] +=$row['sellPrice']*$row['sellNum']/$shopNum;
				
				//營業額
				if($shopID!=0)	$data['chartData'][9][(int)$mon] +=$row['sellPrice']*$row['sellNum'];
				else  $data['chartData'][9][(int)$mon] +=$row['sellPrice']*$row['sellNum']/$shopNum;
				
				//毛利
				if($shopID!=0) $data['chartData'][10][(int)$mon] +=($row['sellPrice']-$row['purchasePrice'])*$row['sellNum'];
				else $data['chartData'][10][(int)$mon] +=($row['sellPrice']-$row['purchasePrice'])*$row['sellNum']/$shopNum;
				
				if($data['chartData'][9][(int)$mon]>$maxValue) $maxValue = $data['chartData'][9][(int)$mon];
			
		}
		foreach($data['record2'] as $row)
		{
				$thisMon = (int)substr($row['time'],5,2);
				$thisYear = (int)substr($row['time'],0,4);
				//$mon = substr($row['time'],5,2)+12;
				
				if($thisMon<=$month) continue;
				
	
				if(($thisMon+12)%12==0)$mon = 12- $month;
				else $mon = ($thisMon+12)%12- $month;
				$shopNum = $this->shopNumDetect($thisYear,$thisMon);
				
				if($mon==0) $mon=12;
				if($row['type']=='') $row['type']= 8;
				if($shopID!=0)	$data['chartData'][$row['type']][(int)$mon] +=$row['sellPrice']*$row['sellNum'];
				else $data['chartData'][$row['type']][(int)$mon] +=$row['sellPrice']*$row['sellNum']/$shopNum;
				
				//營業額
				if($shopID!=0)	$data['chartData'][9][(int)$mon] +=$row['sellPrice']*$row['sellNum'];
				else  $data['chartData'][9][(int)$mon] +=$row['sellPrice']*$row['sellNum']/$shopNum;
				
				//毛利
				if($shopID!=0) $data['chartData'][10][(int)$mon] +=($row['sellPrice']-$row['purchasePrice'])*$row['sellNum'];
				else $data['chartData'][10][(int)$mon] +=($row['sellPrice']-$row['purchasePrice'])*$row['sellNum']/$shopNum;
				
				if($data['chartData'][9][(int)$mon]>$maxValue) $maxValue = $data['chartData'][9][(int)$mon];
			
		}		
		$i=0;
        $out[0][$i] = '月份';//(1月,直1,直2...)
       
        
        $j = 1; 
           for($j=1;$j<=12;$j++)
			{
             $out[$j][$i] = $j;
            }
       
      //  print_r($out);
        $i++;
		foreach($consumption as $row)
		{
            $out[0][$i] = $row['name'];
         //    echo  $out[0][$i].','.$i.'<br/>';
            
            foreach($data['chartData'][ $row['typeID']] as $key=>$each)
            {
                if($key != 0)
                {
                    $out[$key][$i] = $key;
                    $out[$key][$i] = $each;
                }
            }
            
            $i++;
            
				
	
			
		}	
	
        $dataout['out'] = $out;

        $dataout['result'] = true;
        echo json_encode($dataout);
        /*
		//總營業額
				$line = new line();
				$line->set_colour( '#'.$color[$i%12] );
				//$line->set_values( array(9,8,7,6,5,4,3,2,1) );
				//print_r($data['chartData'][ $i]);
				$line->set_values( $data['chartData'][9]);
				$line->set_key( '營業額', 12 );
				$chart->add_element( $line);
				$i++;
		//總營業額
				$line = new line();
				$line->set_colour( '#'.$color[$i%12] );
				//$line->set_values( array(9,8,7,6,5,4,3,2,1) );
				//print_r($data['chartData'][ $i]);
				$line->set_values( $data['chartData'][10]);
				$line->set_key( '毛利', 12 );
				$chart->add_element( $line);		
		
		
		
		
			$x_labels = new x_axis_labels();
			$x_labels->set_steps( 1 );
			$x_labels->set_vertical();
			$x_labels->set_colour( '#A2ACBA' );
			
			//$x_labels->set_labels( array(1,2,3,4,5,6,7,8,9,10,11,12,1,2,3,4,5,7,8,9,10,11,12));
			
		
			$label[0]='0';
			for($k=1;$k<=12;$k++)
			{
				if(($k+$month)%12==0)$label[]=strval((string)12);
				else $label[] = strval(($k+$month)%12);
			}
			
			//print_r($label);
			$x = new x_axis();
			$x->set_colour( '#A2ACBA' );
			$x->set_grid_colour( '#D7E4A3' );
			$x->set_offset( false );
			$x->set_labels_from_array($label);
			//array('0','1','2','3','4','5','6','7','8','9','10','11','12');
			$x->set_steps(1);
			
			// Add the X Axis Labels to the X Axis
			//$x->set_labels( $x_labels );
			
			$chart->set_x_axis( $x );
			
			//
			// LOOK:
			//
			$x_legend = new x_legend( $year );
			$x_legend->set_style( '{font-size: 20px; color: #778877}' );
			$chart->set_x_legend( $x_legend );
			
			//
			// remove this when the Y Axis is smarter
			//
			$y = new y_axis();
			
			if($maxValue>100) $offset = round($maxValue/8,-2);
			else $offset = round($maxValue/8,-1);
			$y->set_range( 0, $maxValue, $offset);
			$chart->add_y_axis( $y );		
		$output = $chart->toPrettyString();
		if($creareFile)	
		{
				
			$f = fopen($file,'w');
			fprintf($f,"%s",$output);
					fclose($f);	
			
		}
		echo $output;	

		/**/
	}
	function shopNumDetect($year,$month)	
	{
		
		$num =1;
		$shopNum = array(
			array('year'=>2011,'month'=>4,'num'=>3),
			array('year'=>2011,'month'=>10,'num'=>4),
			array('year'=>2012,'month'=>4,'num'=>5),
			array('year'=>2013,'month'=>6,'num'=>6),
			array('year'=>2013,'month'=>9,'num'=>7),
			array('year'=>2013,'month'=>10,'num'=>8),
			array('year'=>2013,'month'=>11,'num'=>10)
		);
			
		foreach($shopNum as $row)
		{
			if($year==$row['year'] )	
			{
				
				if($month>=$row['month']) $num = $row['num'];
			}
			else if($year>$row['year'] )
			{
				$num = $row['num'];
			}
			
			
			
		}
		//echo $year.','.$month.','.$num.'<br/>';
		return $num;
		
	}
	
	
	function get_chart()
	{
		
		$this->load->helper('chart');

					
		$date = $this->uri->segment(3);
		$shopID =  $this->uri->segment(4);
		
		$dateList = explode('-',$date);
		$year = $dateList[0];
		$mon = $dateList[1];
		
		$creareFile =false;
		$time = getdate();
		if($year<$time['year']||($year==$time['year']&&$mon<$time['mon']))
		{
			
				$file = $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/sale/sale_'.$date.'_'.$shopID.'.txt';
			if(file_exists($file))
			{
					$handle = fopen($file,'r');
					$contents = '';
				while(!feof($handle))
				{$contents .= fgets($handle);}
				fclose($handle);	
				echo $contents;
				
				exit(1);
				
			}
			else $creareFile = true;
			
			
			
				
	
					
				
		}

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

		
		
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		$this->load->model('System_model');
		$consumption = $this->Accounting_model->getConsumption();
		
		foreach($consumption as $row)
		{
			
				$item[$row['typeID']]['name'] =$row['name'];
				$item[$row['typeID']]['count'] = 0;
			
		}
		if($shopID!=0)	$shop = $this->System_model->getShopByID($shopID);
		else $shop['name'] = '全省累積';		
		$chart = new open_flash_chart();
		$title = new title( $shop['name'].'逐日營業走勢圖' );
		$color = array('FF37FD','FF9D37','37FF9D','DDDD37','99FF37','37FF39','37FDFF','9D37FF','3799FF','3937FF','FF3937','FF3799');		
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
		for($i=1;$i<=10;$i++)
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
				//營業額
				$data['chartData'][9][(int)$date] +=$row['sellPrice']*$row['sellNum'];
				
				//毛利
				$data['chartData'][10][(int)$date] +=($row['sellPrice']-$row['purchasePrice'])*$row['sellNum'];
				if($data['chartData'][9][(int)$date]>$maxValue) $maxValue = $data['chartData'][9][(int)$date];
			
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
		//總營業額
				$line = new line();
				$line->set_colour( '#'.$color[$i%12] );
				//$line->set_values( array(9,8,7,6,5,4,3,2,1) );
				//print_r($data['chartData'][ $i]);
				$line->set_values( $data['chartData'][9]);
				$line->set_key( '營業額', 12 );
				$chart->add_element( $line);
				$i++;
		//總營業額
				$line = new line();
				$line->set_colour( '#'.$color[$i%12] );
				//$line->set_values( array(9,8,7,6,5,4,3,2,1) );
				//print_r($data['chartData'][ $i]);
				$line->set_values( $data['chartData'][10]);
				$line->set_key( '毛利', 12 );
				$chart->add_element( $line);		
		
		
		
		
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
			
			if($maxValue>100) $offset = round($maxValue/8,-2);
			else $offset = round($maxValue/8,-1);
			//$maxValue = 10000;
			$y->set_range( 0, $maxValue, $offset);
			
			$chart->add_y_axis( $y );		
		
		$output = $chart->toPrettyString();
		if($creareFile)	
		{
				
			$f = fopen($file,'w');
			fprintf($f,"%s",$output);
					fclose($f);	
			
		}
		echo $output;
		/**/
	}
	function get_bar()
	{
		$this->load->helper('chart');

					
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
		$shopID =  $this->uri->segment(4);
			$this->load->model('Order_model');
			$this->load->model('Accounting_model');
		$this->load->model('System_model');
		$consumption = $this->Accounting_model->getConsumption();
		
		foreach($consumption as $row)
		{
				$item[$row['typeID']]['name'] =$row['name'];
				$item[$row['typeID']]['count'] = 0;
			
		}
		if($shopID!=0)	$shop = $this->System_model->getShopByID($shopID);
		else $shop['name'] = '全省';		
		$chart = new open_flash_chart();

		$chart = new open_flash_chart();
		$title = new title( $shop['name'].'消費模式分布圖' );
		$color = array('FF37FD','FF9D37','37FF9D','DDDD37','99FF37','37FF39','37FDFF','FF3937','3799FF','3937FF','9D37FF','FF3799');		
		$title->set_style( "{font-size: 20px; color: #A2ACBA; text-align: center;}" );
		$chart->set_title( $title );		
		$data['total']  = 0 ;
		$data['verifyKey'] = true;
		
		//$data['record'] = $this->Accounting_model->getDayReport($data['date'],$shopID);
		$data['record'] = $this->Accounting_model->getMonReport($mon,$year,$shopID);
		$data['verify'] = 0 ;
		$data['monVerify'] = 0 ;
		$data['place'] =0;
		for($i=1;$i<=9;$i++)
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
				if($row['type']=='') $row['type']= 9;//其他
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
			
			if($maxValue>100) $offset = round($maxValue/8,-2);
			else $offset = round($maxValue/8,-1);
			$y->set_range( 0, $maxValue, $offset);
			$chart->add_y_axis( $y );			
			
			
	
	
	$bar = new bar_glass();
	$bar->set_values( array(8,2,3,4,5,6,7) );
	$bar->colour( '#'.$color[$i%12] );
	$chart->add_element( $bar );
							
	echo $chart->toString();		
		
		
	}
	
	function get_product_rank()
	{
		
		set_time_limit (0);
	
		$this->load->helper('chart');

					
		$date = $this->uri->segment(3);
		
		if($this->data['shopID']!=0)$shopID = $this->data['shopID'];
		 else $shopID = $this->input->post('shopID');
		 $date = $this->input->post('date');

	   	 $dateList = explode('-',$date);
  		 $year = $dateList[0];
			$mon = $dateList[1];
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');

		$this->load->model('Sale_model');
		$id = $this->input->post('id');
	
		
	
		if($id == 'productRankTable_self')$data['ret'] = $this->Sale_model->getProductRank($shopID,$year,$mon);	
		if($id == 'productRankTable_all')$data['ret'] = $this->Sale_model->getProductRank(0,$year,$mon);	
		if($id == 'productRankTable_full_self')$data['ret'] = $this->Sale_model->getProductRank($shopID,0,0);	
		if($id == 'productRankTable_full_all')
		{
			
			$data['ret'] = $this->Sale_model->getProductRank(0,0,0);	
		}

	
		
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		/**/
	}
	
	function  test()
	{
		$this->load->model('Order_model');
		$this->load->model('Accounting_model');

		$this->load->model('Sale_model');
		$this->Sale_model->getProductRank(0,0,0);	
		
	}
	function get_customer_bar()
	{
		$this->load->helper('chart');

					
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
		$shopID =  $this->uri->segment(4);
			$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		$this->load->model('Sale_model');
		$this->load->model('System_model');
		
		if($shopID!=0)	$shop = $this->System_model->getShopByID($shopID);
		else $shop['name'] = '全省';
		$chart = new open_flash_chart();
		if($mon!=0)	$title = new title( $shop['name'].'單月消費型態分布圖' );
		else $title = new title( $shop['name'].'年度消費型態分布圖' );
		$color = array('FF37FD','FF9D37','37FF9D','DDDD37','99FF37','37FF39','37FDFF','FF3937','3799FF','3937FF','9D37FF','FF3799');		
		$title->set_style( "{font-size: 20px; color: #A2ACBA; text-align: center;}" );
		$chart->set_title( $title );		
		$data['total']  = 0 ;
		$data['verifyKey'] = true;
		
		//$data['record'] = $this->Accounting_model->getDayReport($data['date'],$shopID);
		$data['record'] = $this->Accounting_model->getMonReport($mon,$year,$shopID);
		$data['verify'] = 0 ;
		$data['monVerify'] = 0 ;
		$data['place'] =0;

		$maxValue = 0;
		
		$switchArray = array('06:00','10:00','13:00','16:00','19:00','23:59');
		$timeNum = count($switchArray)			;

		$categoryArray = array('A','B','C','D','E','F');
		$categoryName = array('A.親子益智','B.派對歡樂','C.輕度策略','D.重度策略','E.團隊合作','F.兩人對戰');
		for($i= 0 ; $i< $timeNum ; $i++)
		{
			for($j= 0 ; $j< 6 ; $j++) $data['chartData'][$categoryArray[$j]][$i] = 0;
			
			if($i==$timeNum-1)$timePeriod[] = '全天';
			else $timePeriod[] = $switchArray[$i].'~'.$switchArray[$i+1];
		}
		
		
		foreach($data['record'] as $row)
		{
			$ret = $this->Sale_model->timeSwitch(substr($row['time'],10),$switchArray);
			//$ret for the peirod durring switchArray ex 0 for  06:00:00~13:00:00

			//only for the purpose of product sell
			if($row['type']==1&&$row['category']!='0')
			{
				
				if(!isset($data['chartData'][$row['category']][$ret]))$data['chartData'][$row['category']][$ret] = 0;
				$data['chartData'][$row['category']][$ret] += $row['sellNum'];
                if(!isset($data['chartData'][$row['category']][$timeNum -1]))$data['chartData'][$row['category']][$timeNum -1]  = 0; 
				$data['chartData'][$row['category']][$timeNum -1] += $row['sellNum'];
				if($data['chartData'][$row['category']][$timeNum -1]>$maxValue) $maxValue = $data['chartData'][$row['category']][$timeNum -1];
			}
		}
			
		$i=1;
		$chart = new open_flash_chart();
		$chart->set_title( $title );
		for($i= 0 ; $i< 6 ; $i++)
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
			$bar->set_values( $data['chartData'][$categoryArray[$i]]);
			$bar->set_key( $categoryName[$i], 12 );
			$bar->colour( '#'.$color[$i%12] );
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
			$x->set_labels_from_array($timePeriod);
			$x->set_steps(1);
			// Add the X Axis Labels to the X Axis
			//$x->set_labels( $x_labels );
			
			$chart->set_x_axis( $x );
			
			//
			// LOOK:
			//
			if($mon!=0)	$x_legend = new x_legend( $year.' / '.$mon );
			else $x_legend = new x_legend( $year );
			$x_legend->set_style( '{font-size: 20px; color: #778877}' );
			$chart->set_x_legend( $x_legend );
			
			//
			// remove this when the Y Axis is smarter
			//
			$y = new y_axis();
			
			if($maxValue>100) $offset = round($maxValue/10,-1);
			else if($maxValue>50)$offset = 10;
			else if($maxValue>5)$offset = 5;
			else $offset = 1;
			$y->set_range( 0, $maxValue, $offset);
			$chart->add_y_axis( $y );			
			
			
	
	

							
	echo $chart->toString();		
		
		
	}
	function mon_predict()
	{
		$time = getdate();
		$mon = $time['mon'];
		$year = $time['year'];
		
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
		
		$data['date'] =$year.'-'.$mon;
		$date = $year.'-'.$mon.'-'.$data['mday'];
		$firstDay = getdate(mktime(0,0,0,$mon,1,$year));
		$data['firstWeekDay'] = $firstDay['wday'];
		
		if($this->data['shopID']==0)$shopID =  $this->input->post('shopID');
		else $shopID = $this->data['shopID'];
			$this->load->model('Order_model');
		$this->load->model('Accounting_model');
		$consumption = $this->Accounting_model->getConsumption();
		
		foreach($consumption as $row)
		{
			
				$item[$row['typeID']]['name'] =$row['name'];
				$item[$row['typeID']]['count'] = 0;
			
		}
		
		$saleTotal  = 0 ;
		$total = 0;
		$data['verifyKey'] = true;
		
		//$data['record'] = $this->Accounting_model->getDayReport($data['date'],$shopID);
		$data['record'] = $this->Accounting_model->getMonReport($mon,$date,$shopID);
		$data['verify'] = 0 ;
		$data['monVerify'] = 0 ;
		$data['place'] =0;

		
		for($i=0;$i<7;$i++)
		{
			$saleRecord[$i] = 0;
			$record[$i] = 0;
			$future[$i] = 0;
			$weekDayTimes[$i] = 0;
		}
		
		
		$weekDay = $data['firstWeekDay'] ; 
		
		//caculate past
		foreach($data['record'] as $row)
		{			
		
			$date = substr($row['time'],8,2);
			if($time['mday']!=$date)//pass today
			{
				$wday =($data['firstWeekDay']+((int)$date - 1)%7)%7;
				$saleTotal += $row['sellPrice']*$row['sellNum'];		
				$saleRecord[$wday]	+= $row['sellPrice']*$row['sellNum'];	
				if($row['productID']==8881365)$record[$wday]  += $row['sellNum']*74;//團購券
				else $record[$wday]  += round($row['sellNum']*($row['sellPrice']-$row['purchasePrice']));
				$total +=round($row['sellNum']*($row['sellPrice']-$row['purchasePrice']));
			}
			;
		}
		
		//cacule how many days in th future
		for($i=1;$i<=$data['mday'];$i++)
		{

			$wday =($data['firstWeekDay']+($i- 1)%7)%7;
			if($i<$time['mday'])$weekDayTimes[$wday]++;//已過的天數
			else $future[$wday]++;//未過的天數
		}
		
		//predict
		$salePredict = $saleTotal;
		$predict = $total;
		
		for($i=0;$i<7;$i++)
		{
			if($weekDayTimes[$i]!=0)
			{
				$salePredict += ($saleRecord[$i]/$weekDayTimes[$i])*$future[$i];

				$predict += ($record[$i]/$weekDayTimes[$i])*$future[$i];
			}
			
		}
		$predictArray= array(
			array(),
			array(39000,65000,90000),
			array(35000,65000,80000),
			array(35000,65000,80000),
			array(40000,65000,80000),
			array(25000,50000,70000),
			array(25000,60000,75000),
			array(25000,60000,75000),
			array(35000,60000,75000),
			array(25000,60000,75000),
			array(25000,60000,75000),
			array(25000,60000,75000),
			array(25000,60000,75000),
			array(25000,60000,75000),
			array(25000,60000,75000),
			array(25000,60000,75000)
		
		);
		
		$predictArray['999'] = array(20000,56000,70000);
		 $comment ='測試';
		if($shopID!=100)
		{
			if(!isset($predictArray[$shopID]))$predictArray[$shopID] = array(25000,60000,75000);
			if((int)$date<=6) $comment='<span style="color:red;font-size:14pt">預測資訊尚不足~</span>';
			else if($predict<$predictArray[$shopID][0]) $comment='<span style="color:red;font-size:14pt">是不是出了什麼問題，快請求總部協助</span>';
			else if($predict<$predictArray[$shopID][1])$comment='<span style="color:#D9DC00;font-size:14pt">還要再加油喔！請再多多努力</span>';	
			else if ($predict<$predictArray[$shopID][2])   $comment='<span style="color:green;font-size:14pt">非常不錯喔！請繼續保持!</span>' ;
			else $comment='<span style="color:red;font-size:14pt">實在太棒了！瘋桌遊以你為榮</span>';
		}
		$result['saleTotal'] = round($saleTotal);
		$result['salePredict'] = round($salePredict);
		$result['salePredict'] = round($salePredict);
		$result['predict'] = round($predict);
		$result['comment'] = $comment;
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		
		
	}
	
	
	
	
	
	
	function send_mail()
	{
		$mailTo = array('lintaitin@gmail.com','phoenickimo@hotmail.com');
		$url = 'http://shipment.phantasia.com.tw/accounting/get_day_report';
		$time = getdate();
		$this->load->model('System_model');
		$data['shopList'] = $this->System_model->getShop(true);	
		$eachResult = array();
		$result = '<link rel="stylesheet" type="text/css" href="http://shipment.phantasia.com.tw/style/pos.css">';
		foreach ($data['shopList'] as $row)
		{
			$eachResult[$row['shopID']] ='<h1>'.$row['name'].'報表</h1>'.
										 $this->paser->post($url,array('year'=>$time['year'],'mon'=>$time['mon'],'mday'=>$time['mday'],'shopID'=>$row['shopID']),false);
			$result .=	$eachResult[$row['shopID']]	;				
		}
	
		$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";
		
		foreach($mailTo as $row)
		{
			$this->Mail_model->myEmail($row,$time['year'].'/'.$time['mon'].'/'.$time['mday'].'營業報表',$result,$headers);
		}
		$this->Mail_model->myEmail('st855168@hotmail.com',$time['year'].'/'.$time['mon'].'/'.$time['mday'].'營業報表',$eachResult[4]	,$headers);
		
		
	}
	
	function member()
	{
		$this->iframeConfirm();
		$memberID = $this->uri->segment(5);
		$type = $this->uri->segment(6);
     
		$this->load->model('Sale_model');
        $this->load->model('Accounting_model');
      
        $m = substr($memberID,5);
         if($memberID == substr(md5('p'.$m),0,5).$m)$this->data['memberID'] = $m;
        
		
		 $this->data['saleData'] = $this->Sale_model->getSaleData($this->data['memberID']);
        $i = 0;
        foreach( $this->data['saleData'] as $row)
        {
               $this->data['saleData'][$i]['rent']  =  $this->Accounting_model->getRentData($row['productID'],$row['sellID'],$row['shopID']);
            
            $i++;
            
        }
  
		if($type==0)$this->load->view('sale_member',$this->data);	
		else $this->load->view('sale_member_index',$this->data);	
	}	
	
	function member_pile()
	{
		$memberID = $this->uri->segment(3);
			$this->load->model('Sale_model');
		$this->load->helper('chart');
		
		$title = new title( '消費習慣紀錄' );
		$pie = new pie();
		$pie->set_alpha(0.6);
		$pie->set_start_angle( 35 );
		$pie->add_animation( new pie_fade() );
		$pie->set_tooltip( '#val# of #total#<br>#percent# of 100%' );
		$pie->set_colours( array('FF37FD','FF9D37','37FF9D','DDDD37','99FF37','37FF39','37FDFF','9D37FF','3799FF','3937FF','FF3937','FF3799'));
		
		$this->data['saleData'] = $this->Sale_model->getSaleData($memberID);
		
		$result[1] = array('total'=>0,'name'  => '銷售');
		$result[2] = array('total'=>0,'name'  => '場地');
		$result[3] = array('total'=>0,'name'  => '租賃');
		$result[4] = array('total'=>0,'name'  => '餐飲');
		$result[5] = array('total'=>0,'name'  => '其他');
		$result[6] = array('total'=>0,'name'  => '會員費用');
		$result[7] = array('total'=>0,'name'  => '魔獸世界');
		$result[8] = array('total'=>0,'name'  => '魔法風雲會');														
	
	
		foreach($this->data['saleData'] as $row)
		{
			if($row['type']!=NULL)	
			{
				$result[$row['type']]['total'] +=$row['sellPrice']*$row['num'];
			
			}
		}
		
		foreach($result as $row)
		{
			$data[] = new pie_value($row['total'], $row['name']);
		}
		$pie->set_values( $data );
		
		$chart = new open_flash_chart();
		$chart->set_title( $title );
		$chart->add_element( $pie );
		
		
		$chart->x_axis = null;
		
		echo $chart->toPrettyString();
	
	}
	function member_product()
	{
			$memberID = $this->uri->segment(3);

			$this->load->model('Sale_model');
		$this->load->helper('chart');
		
		$title = new title( '銷售類型紀錄' );
		$pie = new pie();
		$pie->set_alpha(0.6);
		$pie->set_start_angle( 35 );
		$pie->add_animation( new pie_fade() );
		$pie->set_tooltip( '#val# of #total#<br>#percent# of 100%' );
		$pie->set_colours( array('FF37FD','FF9D37','37FF9D','DDDD37','99FF37','37FF39','37FDFF','9D37FF','3799FF','3937FF','FF3937','FF3799'));
		
		$this->data['saleData'] = $this->Sale_model->getSaleData($memberID);
		
		$result['A'] = array('total'=>0,'name'  => '親子益智');
		$result['B'] = array('total'=>0,'name'  => '派對歡樂');
		$result['C'] = array('total'=>0,'name'  => '輕度策略');
		$result['D'] = array('total'=>0,'name'  => '重度策略');
		$result['E'] = array('total'=>0,'name'  => '團隊合作');
		$result['F'] = array('total'=>0,'name'  => '兩人對戰');
	
	

		foreach($this->data['saleData'] as $row)
		{
			if($row['category']!=NULL)	
			{
				
				if($row['type']==1&&$row['category']!='0')
				{
				
						$result[$row['category']]['total'] +=$row['sellPrice']*$row['num'];
				}
			
			}
		}
		
		
		foreach($result as $row)
		{
			$data[] = new pie_value($row['total'], $row['name']);
		}
		$pie->set_values( $data );
		
		$chart = new open_flash_chart();
		$chart->set_title( $title );
		$chart->add_element( $pie );
		
		
		$chart->x_axis = null;
		
		echo $chart->toPrettyString();
			
		
		
	}

	function get_member_present()
	{
		$this->iframeConfirm();
		$this->load->model('Sale_model');
			$shopID = $this->data['shopID'] ; 
		$this->data['presentData'] = $this->Sale_model->getAllMemberPresent($shopID);
		if($shopID==0)
		{
			$this->data['display'] = 'sale_present';
			$this->load->view('template',$this->data);	
			
		}
		else $this->load->view('sale_present',$this->data);	
		
	}
	function present_delete()
	{
		$id= $this->input->post('id');
		$shopID = $this->data['shopID'] ; 
//		$this->db->where('shopID',$shopID);
		$this->db->where('id',$id);
		$this->db->delete('pos_birthday_present');
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		
		
	}
	
	function  sanguosha()
	{
		$this->iframeConfirm();
		$this->load->model('Sale_model');
		
		
		
		$this->data['isIframe'] = true;
		$this->data['sanguosha'] = $this->Sale_model->getSanguosha($this->data['shopID']);
		$this->data['display'] = 'sale';
		$this->load->view('sanguosha',$this->data);	
	
	}
	function  share()
	{
		$this->iframeConfirm();
		$this->load->model('Sale_model');
		
		
		
		$this->data['isIframe'] = true;
        $t = time();
        $this->data['code'] = md5('phantasia+pos'.$t);
        $this->data['t'] = $t;
        $this->data['shopID']  = 1;
		$this->load->view('sale_share',$this->data);	
	
	}
	
	function  sanguosha_online()
	{
		//$this->iframeConfirm();
		$this->load->model('Sale_model');
		
		
		
		$this->data['isIframe'] = false;
		$this->data['sanguosha'] = $this->Sale_model->getSanguosha($this->data['shopID']);
		$this->data['display'] = 'sanguosha';
		$this->load->view('template',$this->data);	
	
	}

	function get_sanguosha_online()
	{
		echo $this->paser->post('http://www.sanguoshatw.com/buying/get_all_data',array('token'=>'phantasia_sanguosha'),false);	
		
		exit(1);
		
	}
	
	function remit_check()
	{
		echo $this->paser->post('http://www.sanguoshatw.com/buying/update',$_POST,false);
		
	}
	
	function sanguosha_shipment()
	{
		$LogisticsCode = $this->input->post('LogisticsCode');
		$id = $this->input->post('id');
		$this->load->model('Order_model');
		
		 $ret = $this->paser->post('http://www.sanguoshatw.com/buying/update',array('id'=>$id,'LogisticsCode'=>$LogisticsCode),true);
		
		
				if($ret['result']== true)
				{
				
				$maxNum = $this->Order_model->getMaxOrderNum();
				
				$shopID = 1035;
				$this->db->insert('pos_order',array('shopID'=>$shopID,'status'=>0,'orderNum'=>$maxNum+1,'orderTime'=> date("Y-m-d H:i:s"),'type'=>0));
				$orderID= $this->db->insert_id();
				$this->db->insert('pos_order_detail',array('orderID'=>$orderID,'productID'=>8881696,'buyNum'=> 1,'sellNum' =>1,'sellPrice'=>600));
				$total=  600;

				$this->db->where('id',$orderID);
				$this->db->update('pos_order',array('total'=>$total));
			
				$row = $this->Order_model->getOrderInf($orderID);
				$maxNum = $this->Order_model->getMaxShippingNum();
			     //導入shipment
				 	$datain = array(
						'shopID' =>$row['shopID'],
						'status' =>0,
						'shippingTime' =>$row['shippingTime'],
						'shippingNum' =>$maxNum+1,
						'total' =>$row['total'],
						'type'  =>$row['type'],
						'shipmentComment' => $row['orderComment']
					
					);
				 	$this->db->insert('pos_order_shipment',$datain);
					$shipmentID = $this->db->insert_id();
				 //導入shipmentDetail				
				 $this->db->where('orderID',$row['id']);
				 $query = $this->db->get('pos_order_detail');
				 $product = $query->result_array();
				 foreach($product as $col)
				 {
					$datain = array(
						'shipmentID'=>$shipmentID,
						'rowID' =>    $col['id'],
						'sellPrice' =>$col['sellPrice'],
						'sellNum' => $col['sellNum'],
						'comment' => $col['comment']
					);
					$this->db->insert('pos_order_shipment_detail',$datain); 
				}
					$data['shipmentID'] = $shipmentID;			
					$data['result'] = true;
					 
				}
				else  $data['result'] = false;
			
	
	echo json_encode($data);
		exit(1);			
			
	}
	
	
	function pile($shopID=0,$outID=0)
	{
		$this->db->select('sellPrice,memberID,type');
		$this->db->where('year(time)',2015,false);
		$this->db->where('shopID !=',100);
		if($shopID!=0)$this->db->where('shopID ',$shopID);
		$this->db->where('shopID !=',$outID);
		
		$this->db->where('memberID !=',999999);
		$this->db->join('pos_product','pos_product_sell.productID = pos_product.productID','left');
		$query = $this->db->get('pos_product_sell');
		$data = $query->result_array();
		
		
		foreach($data as $row)
		{
			if(!isset($result[$row['memberID']]))
			{
				$result[$row['memberID']] = array(
					'memberID' =>$row['memberID'],
					'rent' => 0,
					'sell' => 0,
					'place' => 0
				)	;
				
				
			}
			//遊戲
			if($row['type']==1)
			{
				 $result[$row['memberID']]['sell'] +=(int)$row['sellPrice'];
			}
			elseif($row['type']==2)$result[$row['memberID']]['place'] += (int)$row['sellPrice'];
			elseif($row['type']==3)
			{
				$result[$row['memberID']]['rent'] += (int)$row['sellPrice'];
			}
			
			
			
		}
		
		echo '<table border="1">';
			echo '<tr>'	;
				echo '<td>memberID</td>'	;
				echo '<td>rent</td>'	;
				echo '<td>sell</td>'	;
				echo '<td>place</td>'	;
				echo '<td>0  Consumption</td>'	;
				echo '<td>rent</td>'	;
				echo '<td>sell</td>'	;
				echo '<td>place</td>'	;
				echo '<td>rent,sell</td>'	;
				echo '<td>rent,place</td>'	;
				echo '<td>place,sell</td>'	;
				echo '<td>rent,place,sell</td>'	;								
			echo '</tr>'	;
				
		foreach($result as $row)
		{
			echo '<tr>'	;
				echo '<td>'.$row['memberID'].'</td>'	;
				echo '<td>'.$row['rent'].'</td>'	;
				echo '<td>'.$row['sell'].'</td>'	;
				echo '<td>'.$row['place'].'</td>'	;
			if($row['rent']==0&&$row['sell']==0&&$row['place']==0) echo '<td>1</td>'	;
			else echo '<td>0</td>';

			if($row['rent']!=0&&$row['sell']==0&&$row['place']==0) echo '<td>1</td>'	;
			else echo '<td>0</td>';
			
			if($row['sell']!=0&&$row['rent']==0&&$row['place']==0) echo '<td>1</td>'	;
			else echo '<td>0</td>'	;
			
			
			if($row['place']!=0&&$row['sell']==0&&$row['rent']==0) echo '<td>1</td>'	;
			else echo '<td>0</td>'	;
			
			
			if($row['rent']!=0&&$row['sell']!=0&&$row['place']==0) echo '<td>1</td>'	;
			else echo '<td>0</td>';
			
			if($row['rent']!=0&&$row['place']!=0&&$row['sell']==0) echo '<td>1</td>'	;
			else echo '<td>0</td>'	;
			
			
			if($row['place']!=0&&$row['sell']!=0&&$row['rent']==0) echo '<td>1</td>'	;
			else echo '<td>0</td>'	;

			if($row['place']!=0&&$row['sell']!=0&&$row['rent']!=0) echo '<td>1</td>'	;
			else echo '<td>0</td>'	;
			
			echo '</tr>'	;
			
			
			
		}
		echo '</table>';
	}
			
		
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */