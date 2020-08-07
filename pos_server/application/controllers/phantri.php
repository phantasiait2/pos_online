<?php

class Phantri extends POS_Controller {

	function Phantri()
	{
		parent::POS_Controller();
		$this->load->model('PR_track_model');
			
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
		return;
		
	}
	
	function index()
	{
		
		$this->iframeConfirm();
		//$this->data['display'] = 'phantri';
		//$this->data['problemList'] = $this->PR_track_model->loadProblemList($this->data['shopID'],'open');
		$this->load->view('/phantri/phantri_template',$this->data);	
	}
	
	function view()
	{
			
	//	$this->data['problemList'] = $this->PR_track_model->loadProblemList($this->data['shopID'],'open');
		$this->data['display']= '/phantri/phantri_template';
		$this->load->view('template',$this->data);	
	}
	function tracking()
	{
		
		$this->data['type'] =  $this->PR_track_model->typeToNum($this->uri->segment(4));
		$this->data['status'] =  $this->uri->segment(3);
        $shopID =  $this->uri->segment(5);
      
		$this->data['problemList'] = $this->PR_track_model->loadProblemList($shopID,$this->uri->segment(3));
       $this->load->model('System_model');
          $this->data['shop'] = $this->System_model->getShop();	  
	
		$this->load->view('/phantri/problem_list',$this->data);	
	
		
		
	}
	
	
	function autoload()
	{


        $shopID = $this->input->post('shopID');
		$type =  $this->PR_track_model->typeToNum($this->input->post('type'));
		$problemList = $this->PR_track_model->loadProblemList($shopID,$this->input->post('status'),$this->input->post('offset'));

		
		$data['shopID']	 = $shopID;

		
			if(!empty($problemList))
			{
	
			 $parentID = $problemList[0]['parentID'];
					$status = $problemList[0]['status'];
				
				foreach($problemList as $row){
					if($parentID==$row['parentID']) 
					{
						$data['list'][]  =$row;
						
					}
					else
					{
					
						if(!empty($data))$this->load->view('/phantri/topic_view',$data);
						$data['shopID']	 = $shopID;
						$parentID = $row['parentID'];
						$data = array();
						$data['list'][]  =$row;
					
					}
					if($row['shopID']!=0 &&$row['status']=='待回覆') $data['key'] = true;
						else 
						{
							$data['key'] =false;
							
					
						} 
				};
				if(!empty($data))$this->load->view('/phantri/topic_view',$data);
			}
			else echo '<h1>查無問題</h1>';	
		
	}
	
	
	function holding()
	{
		$this->data['problemList'] = $this->PR_track_model->loadProblemList(0,'open',0,1000);
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$today =  date("Y-m-d");
		$yesterday = date("Y-m-d", strtotime($today."-1 day"));
		$wait = array();
		$hold = array();
		$newTask = 0;
		foreach($this->data['problemList'] as $row)
		{
			if(!isset($wait[$row['type']])) $wait[$row['type']] = 0;
			if(!isset($hold[$row['type']])) $hold[$row['type']] = 0;

			if($row['parentID']==$row['id'])
			{
					$parent['type']	 = $row['type'];
					if ($row['status'] !=-1 ) $hold[$parent['type']]++;
			}
			if($row['status'] =='待回覆' && $row['shopID']!=0 ) 
			{
				$wait[$parent['type']]++;
			}
		
			if($row['id']==$row['parentID'] && strtotime($today)-strtotime($row['time'])<24*60*60)$newTask++;
			
		}
		$email = 'phantasia.mk@gmail.com,phantasia.ep@gmail.com,phantasia.art@gmail.com,product@phantasia.tw,phantasia.ac@gmail.com,phantasia.ec@gmail.com,phantasia.it@gmail.com,';
		if($this->uri->segment(3)==1) $email = 'lntaitin@gmail.com,phoenickimo@gmail.com,phantasia.se@gmail.com';
		
		
		;
		$this->data['closeList'] =  $this->PR_track_model->loadProblemList(0,'close',0,1000, $yesterday);
		$closeTask  = 0;
		foreach($this->data['closeList']  as $row)
		{
			if($row['id']==$row['parentID'])	$closeTask ++;
			
		}
		
		
		$content="<h1>問題解決中心</h1>";
		$content.='請相關人員至問題解決中心回覆問題喔<a href="http://shipment.phantasia.com.tw/phantri">http://shipment.phantasia.com.tw/phantri</a>';
		$content.='<h2>昨日新增問題數量：'.$newTask.'<h2>';
		$content.='<h2>昨日總結案問題數量：'.$closeTask.'<h2>';
		
		$content.="<table><tr><th>類型</th><th>未結案的問題</th><th>待回復的問題</th></tr>";
		foreach($wait as $key=>$row)
		{
			$content.="<tr>";
			$content.="<td>".$key."</td>";
			$content.="<td>".$hold[$key]."</td>";
			$content.="<td>".$wait[$key]."</td>";
			$content.="</tr>";
	
			
		}
		$content.="</tr>";
		echo $content;
		$this->tracking_update();
		$this->Mail_model->myEmail( $email ,"問題解決中心".date("Y-m-d"),$content,'',0,99,1);	
	}
	
	
	
	function arrive_track()
	{
		$shopID = $this->data['shopID'];
		$productID = $this->input->post('productID');
		//need to insert
		$this->load->model('Product_model');
		$p = $this->Product_model->chkProductByProductID($productID);
		
		
		
		
		$data=
		array
		(
			'productID' => $this->input->post('productID'),
			'shopID' => $this->data['shopID'],
			'account' =>$this->data['account'],
			'type' => '到貨詢問',
			'content' => '請問 ['.$p['ZHName'].'('.$p['ENGName'].')] 到貨時間為何？<br/>'.$this->input->post('w')
			
		);
		
		
	
		$this->PR_track_model->insertPR($data);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
	}
	
	function get_short_product_status()
	{
		
		
		$parentID = $this->input->post('parentID');
		$this->db->where('id',$parentID);
		$query = $this->db->get('pos_problem_track_short');
		$data = $query->row_array();
		$data['statusArray'] = explode('-',$data['status']);
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
	}
	
	
	function short_product_status()
	{
		$parentID = $this->input->post('parentID');
		$status = $this->input->post('status_'.$parentID);
		
		$a = array();
		foreach($status as $row)
		{
			$a[$row] = true;
			
		}
		$str = '' ;
		$token = true;
		for($i=1;$i<=4;$i++ )
		{
			if(isset($a[$i])) $str.=$i.'-';
			else
			{
				$token = false;
				 $str.='0-';
			}
			
		}
		
	    $this->db->where('id',$parentID);
		$query = $this->db->get('pos_problem_track_short');
		if($query->num_rows()>0)
		{
			  $this->db->where('id',$parentID);
			  $this->db->update('pos_problem_track_short',array('status'=>$str));
		}
		else  $this->db->insert('pos_problem_track_short',array('id'=>$parentID,'status'=>$str));
		$data['result'] = $token;
		echo json_encode($data);
		exit(1);
	}
	
	
	function short_product_send()
	{
		
		$short = $_POST['short'];
		$shopID = $this->data['shopID'];
			$this->load->model('Product_model');
		
		foreach($short as $productID=>$row)
		{
			//need to insert
	
			$p = $this->Product_model->chkProductByProductID($productID);
			$data=
			array
			(
				'productID' => $productID,
				'shopID' => $this->data['shopID'],
				'account' =>$this->data['account'],
				'type' => '缺件回報',
				'content' => '['.$p['ZHName'].'('.$p['ENGName'].')] 缺件<br/>['.$row[0].']<br/>'.$row[1]
				
			);
			$this->PR_track_model->insertPR($data);
		}
		$data['result'] = true;
		echo json_encode($data);
		exit(1);	
	}
    function problem_ask_api()
    {
        $this->data['shopID'] =  $this->input->post('shopID');
        
        if( empty( $this->data['shopID']) || $this->data['shopID']==0)  $this->data['shopID'] =  666;	
        
        $this->data['account'] = $this->input->post('account');
       $data=
		array
		(
			
			'shopID' => $this->data['shopID'],
			'account' =>$this->data['account'],
			'type' =>  $this->input->post('type'),
			'content' => $this->input->post('w')
			
		);
            
         	$file = $this->input->post('file');
      
        $fileArray = explode(',',$file);
        //file move
        
        $resultArray = array();
        if(!empty($fileArray))
        foreach($fileArray as $url)
        {
            $name = md5(time());        
            $newFile='/pos_server/upload/problem/temp/'.$name.'.jpg';
            copy($url,$_SERVER['DOCUMENT_ROOT'].$newFile);
            $resultArray[] = $newFile;
        }
        
        
        
        $id = $this->PR_track_model->insertPR($data,implode(',',$resultArray));
		$data['result'] = true;
		echo json_encode($data);
		exit(1);   
            
    }
    
	function problem_ask_send()
	{
		if($this->data['account']=='sys')
		{
			$this->data['shopID'] =  $this->input->post('shopID');	
			$this->data['account'] =  $this->input->post('account');
		}
		
		$data=
		array
		(
			'productID' => $this->input->post('productID'),
			'shopID' => $this->data['shopID'],
			'account' =>$this->data['account'],
			'type' =>  $this->input->post('type'),
			'content' => $this->input->post('w')
			
		);
		
		
		
		$id = $this->PR_track_model->insertPR($data,$this->input->post('file'));
		$data['result'] = true;
		echo json_encode($data);
		exit(1);
		
		
	}
    
    
    
	function close_task()
	{
		$this->PR_track_model->changeStatus($this->input->post('parentID'),-1);
		if($this->data['shopID']!=0)$result['feedback'] = 1;
		else $result['feedback'] = 0;
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
	}
	function reply()
	{
		if($this->data['account']=='sys')
		{
			$this->data['shopID'] =  $this->input->post('shopID');	
			$this->data['account'] =  $this->input->post('account');
		}
		$data=
		array
		(
			'productID' => $this->input->post('productID'),
			'shopID' => $this->data['shopID'],
			'account' =>$this->data['account'],
			'type' => '回覆',
			'content' => $this->input->post('msg'),
			'parentID' => $this->input->post('parentID')
		);
		
		$this->PR_track_model->changeStatus($data['parentID'],2);
		
		$result['id'] = $this->PR_track_model->insertPR($data,$this->input->post('file'));
		
		
		
		
		
		$result['reply'] = $this->PR_track_model->loadReply($result['id']);
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		
	}
	
	function trans_part()
	{
		$parentID = $this->input->post('parentID');
		$part = $this->input->post('part');
		
		$data['type'] =  $this->PR_track_model->typeToNum(	$part , 'to');
		
		$this->db->where('parentID',$parentID);
		$this->db->update('pos_problem_track',$data);
		$r = $this->PR_track_model->loadByParentID($parentID);
		$data=
		array
		(
			'productID' => 0,
			'shopID' => $r[0]['shopID'],
			'account' =>$this->data['account'],
			'type' => '回覆',
			'content' => '已轉介至'.$part.'進行處裡',
			'parentID' => $parentID
		);
		
		
		$this->PR_track_model->changeStatus($data['parentID'],2);
		$result['id'] = $this->PR_track_model->insertPR($data,$this->input->post('file'));
		$result['result'] = true;
		echo json_encode($result);
		exit(1);
		
	}
	
	
	function photo_upload()
	{
		$filename=$_FILES['myfile']['tmp_name'];
		if(empty($filename))$r['err'] = '沒有檔案';
		else
		{
			$img_info = getimagesize($filename);
			$width    = $img_info['0'];
			$height   = $img_info['1'];
			$img_type = $img_info['2'];
			if  (filesize($filename)>4000000) $r['err']= '圖檔太大，請壓縮後再上傳';
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
				$name = md5(time());
				
				$newim = imagecreatetruecolor($width, $height);
				imagecopy($newim, $im, 0,0,0,0, $width, $height);
				imagejpeg($newim, $_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/problem/temp/'.$name.'.jpg', 75);
				/*
				$filename =$_SERVER{'DOCUMENT_ROOT'}.'/pos_server/upload/product/img/temp/'.$name.'.jpg';
				$b=$_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/problem/'.$name.'.jpg';
				ImageResize($filename, $b,600, 1000, 100);
				unlink($filename);
				*/
				$r['success'] = '/pos_server/upload/problem/temp/'.$name.'.jpg';
			}
		
		}	
		echo json_encode($r);
		exit(1);
	}
	
	
	function tracking_update()
	{
		
		$shift = time() - (7 * 1 * 24 * 60 * 60); //7天前
		$data['time'] = date("Y-m-d H:i:s",$shift);
		$this->db->where('status >=',0);
		$this->db->where('updateTime <=',$data['time']);
		$this->db->update('pos_problem_track',array('updateTime'=>date("Y-m-d H:i:s")) );
		
	}
	
	function  problem_solve()
	{
		$datain = array(
		'parentID'=>$this->input->post('parentID'),
		'rank'=>$this->input->post('rank'),
		'comment'=>$this->input->post('comment')
		
		
		)	;
		$this->db->insert('pos_problem_track_response',$datain);
		$r['result'] = true;
		$r['parentID'] = $this->input->post('parentID');
		echo json_encode($r);
		exit(1);
		
	}
	
	
	
	function problem_conclusion()
	{
		
		$parentID = $this->input->post('parentID');
		$p =  $this->PR_track_model->loadByParentID($parentID);
		 
		 $f = $this->PR_track_model->loadFeedback($parentID);
	
		$title= 'Q'.$parentID.$p[0]['shopName'].'本問題結案' ;
		
		
		$content="此次客戶給的評價為".$f['rank'].'分<br/>';
		
		if($f['comment']!='')$content+='客戶的評語：'.$f['comment'].'<br/>';
		
		$c ='';
		foreach($p as $data)
		{
		
				$r = $data['shopName'].' '.$data['account'].'的留言:<br/>';
				$r .=  $data['time'].'<br/>';
				$r .= $data['content'];
				if(isset($data['img']))
				foreach($data['img'] as $each):
					$r .='<img src="http://shipment.phantasia.com.tw'.$each.'"   style="max-width:100px; max-height:100px" >';
				endforeach; 
			$c.= '<br/>'.$r 	;
		}
		$content .=$c;
		
		$this->Mail_model->myEmail('lintaitin@gmail.com' ,$title,$content);
		if($f['rank']<=3)$this->Mail_model->myEmail('phantasia.ec@gmail.com' ,$title,$content);
		if($f['rank']<=2)
		{
			 $_POST['type'] = '經營相關';
			  $_POST['w'] = $content;
			  $_POST['shopID'] = 0;
			  $_POST['account'] = 'sys';
			 
			
			$this->problem_ask_send();	
		}
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */