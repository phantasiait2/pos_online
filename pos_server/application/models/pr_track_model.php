<?php 
class PR_track_model extends Model {
	function PR_track_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function getEmail($type)
	{
		
		$r = array(
			0=>'',
			1=>'',
			2=>'',
			3=>'phantasia.mk@gmail.com,phantasia.ed@gmail.com',
			4=>'phantasia.mk@gmail.com,phantasia.art@gmail.com,phantasia.mkart@gmail.com',
			5=>'phantasia.ec@gmail.com,lintaitin@gmail.com',
			6=>'',
			7=>'lintaitin@gmail.com',
			8=>'phantasia.ac@gmail.com',
			9=>''
		
		);
		if(isset($r[$type])) $email = $r[$type];
		else $email = '';
		
		
		return $email;
        /*
      1 到貨詢問
/6缺件回報
/2產品相關問題
/3活動執行
/4海報DM名片等印刷品
/7系統問題
/5經營相關
/8會計問題
/9退貨問題
        
        */
	}
	
	function urlToLink($content)	
	{
		$content = nl2br($content);
		$maxpos = strlen($content);
		$data = array();
		for($pos=0;$pos<$maxpos;$pos++)
		{
			
			$http = strpos($content,'http',$pos);
			//echo $http;
			if($http===false)$pos =$maxpos;
			else
			{
				$a = strpos($content,'<br />',$http);
				if(!$a) $a = 99999999999;
			
				$b = strpos($content,' ',$http);
				if(!$b) $b = 99999999999;
				$end =	min($a,$b);
			
				
				$pos = $end;
			
				$data[] = array('start'=>$http,'end'=>$end);
			}
		
		}
	//	print_r($data);
		$pos = 0;$result = '<div style="font-size:12pt; text-align:left">';
		foreach($data as $row)
		{
			$result .= substr($content,$pos,$row['start'] - $pos);
			$result .='<a target="_blank" href="'.substr($content,$row['start'],$row['end'] - $row['start']).'">';
			$result .=substr($content,$row['start'],$row['end'] - $row['start']);
			$result .='</a>';
			$pos = $row['end'];
		
			
		}
		$result.=substr($content,$pos);
	
		return $result.'</div>';
		
	}
	
	function typeToNum($in,$to = 'to')
	{

		$r = array(
			-1=>'全部',
			0=>'回覆',
			1=>'到貨詢問',
			2=>'產品相關問題',
			3=>'活動執行',
			4=>'海報DM名片等印刷品',
			5=>'經營相關',
			6=>'缺件回報',
			7=>'系統問題',
			8=>'會計問題',
			9=>'退貨問題'
		);
		
		if($to=='to')
			foreach($r as $index=>$row)
				if($row==$in)return  $index;
				
		return $r[$in];
				
		
		
	}
	function changeStatus($parentID,$status)
	{
		 $this->db->where('parentID',$parentID);
			 $this->db->update('pos_problem_track',array('status'=>$status));
		
	}
	function status($s)
	{
		
		$r = array(
			0=>'待回覆',
			1=>'已收到，處理中',
			2=>'已回覆',
			-1=>'已結案'
		
		);
		return $r[$s];
	}
	function loadReply($id)
	{
		$this->db->select('pos_problem_track.*,pos_sub_branch.name');
		$this->db->where('pos_problem_track.id',$id);
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_problem_track.shopID','left');
		
		$query = $this->db->get('pos_problem_track');	
		$row=  $query->row_array();
		$result = array();

			
		$result =$this->dataForm($row);
		
		return $result;

		
		
	}
	
	function loadFeedback($parentID)
	{
		$this->db->where('parentID',$parentID);
		$query = $this->db->get('pos_problem_track_response');
		return $query->row_array();
		
	}
	
	function loadByProductID($productID)
	{
		
		$this->db->where('pos_problem_track.productID',$productID);
		$this->db->where('pos_problem_track.status != ',-1);
		$this->db->where('pos_problem_track.type = ',1);
		$query = $this->db->get('pos_problem_track');	
		return $query->result_array();
		
	}
	
	
	
	function loadByParentID($parentID)
	{
		$this->db->select('pos_problem_track.*,pos_sub_branch.name as shopName');
		$this->db->where('pos_problem_track.parentID',$parentID);
		$this->db->order_by('pos_problem_track.id','ASC');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_problem_track.shopID','left');
		$query = $this->db->get('pos_problem_track');	
		return $query->result_array();
		
	}
	
	
	function loadReplyByParentID($parentID)
	{
		
		
		$this->db->select('pos_problem_track.*,pos_sub_branch.name');
		$this->db->where('pos_problem_track.parentID',$parentID);
		$this->db->where('pos_problem_track.id !=',$parentID);
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_problem_track.shopID','left');
		$this->db->order_by('pos_problem_track.id','ASC');
		$query = $this->db->get('pos_problem_track');	
		return $query->result_array();
		
		
	}
	function dataForm($row)
	{
		$row['type'] = $this->typeToNum($row['type'], 'back');
			$row['status'] = $this->status($row['status']);
			$row['content'] = $this->urlToLink($row['content']);
			
			$i = 0;
			
		while(file_exists($_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/problem/'.$row['id'].'_'.$i.'.jpg')) $row['img'][]='/pos_server/upload/problem/'.$row['id'].'_'.$i++.'.jpg';

		return $row;
	}
	
	function loadProblemList($shopID,$status = 'open',$offset= 0,$num=30,$date = '0')
	{
	
		$this->db->select('pos_problem_track.*,pos_sub_branch.name');
		if($shopID!=0)$this->db->where('pos_problem_track.shopID',$shopID);
		if($status=='open')$this->db->where('pos_problem_track.status != ',-1);
		else $this->db->where('pos_problem_track.status',-1); 
		if($date!='0')$this->db->where('date(pos_problem_track.updateTime)',$date); 
		$this->db->where('pos_problem_track.id = pos_problem_track.parentID');
		$this->db->join('pos_sub_branch','pos_sub_branch.shopID = pos_problem_track.shopID','left');
		$this->db->order_by('updateTime','DESC');
		$this->db->order_by('parentID','DESC');
		$this->db->order_by('pos_problem_track.id','ASC');
		$this->db->limit($num,$offset);
		$query = $this->db->get('pos_problem_track');	
		$r =  $query->result_array();
		
        $result = array();
        
		foreach($r as $row) 
		{
			
			$result[] = $this->dataForm($row);;
			$reply = $this->loadReplyByParentID($row['id']);
			if(!empty($reply))
			foreach($reply as $each) $result[] = $this->dataForm($each);
			
		}
		return $result;
	}
	
	function getUserMail($parentID)
	{
		
		
		$this->db->where('parentID',$parentID);
		$this->db->where('pos_problem_track.shopID !=',0);
		$this->db->join('pos_account','pos_account.account = pos_problem_track.account','left');
		$q = $this->db->get('pos_problem_track')	;
		$r = $q->row_array();
		if(isset( $r['email']))return $r['email'];
		else return '';
		
	}
	
	function insertPR($data,$file='')
	{
		 $this->db->where('shopID',$data['shopID']);
		 $q = $this->db->get('pos_sub_branch');
		$shop = $q->row_array();
		
		$data['time'] = date("Y-m-d H:i:s");
		$data['updateTime'] = date("Y-m-d H:i:s");
		$data['type'] =  $this->typeToNum(	$data['type'] , 'to');
		if(!isset($data['status']))	$data['status']  = 0;
		$this->db->insert('pos_problem_track',$data);
		 $id = $this->db->insert_id();
		if(!isset($data['parentID']))
		{
			
			 $this->db->where('id',$id);
			 $this->db->update('pos_problem_track',array('parentID'=>$id));
			 $parentID = $id;
		}
		else 
		{
			$parentID = $data['parentID'];
			 $this->db->where('id',$parentID);
			 $this->db->update('pos_problem_track',array('updateTime'=>$data['updateTime']));
		
			
		}
	
		$data['id'] = $id;
		
		
	
		if($file!='')
		{
		
			$fileArray = explode(',',$file);
			$i = 0;
			foreach($fileArray as $row)
			{
					$filename =$_SERVER{'DOCUMENT_ROOT'}.$row;
					$b=$_SERVER['DOCUMENT_ROOT'].'/pos_server/upload/problem/'.$id.'_'.$i++.'.jpg';
					ImageResize($filename, $b,600, 1000, 100);
					unlink($filename);
			}
		}
		
		
		
		
		
		$data = $this->dataForm($data);
		
		$content ="您好：<br/>";
		$content .= $shop['name'].' '.$data['account'].'的留言:<br/>';
		$content .=  $data['type'].'<br/>';
		$content .= $data['time'].'<br/>';
		$content .= $data['content'];
		if(isset($data['img']))
		foreach($data['img'] as $each):
			$content .='<img src="http://shipment.phantasia.com.tw'.$each.'"   style="max-width:100px; max-height:100px" >';
		endforeach; 
		
		
		$content .= '<div style=" border-bottom:1px"></div>';
		$content .= '請上  瘋桌遊問題解決中心觀看';
		
		$emailLst = array();
		$r = $this->loadByParentID($parentID);
		foreach($r as $row)
		{
			if($row['shopID']<1000&&$row['shopID']>0)	
			{
				if($data['shopID']!=$row['shopID'])
				{
					$e = 'phantasia'.str_pad($row['shopID'],4,0,STR_PAD_LEFT ).'@gmail.com';
					$emailLst[] = $e;
				}
			}
			else 
			{
				
				$emailLst[] = $this->getUserMail($parentID);
				
				
			}
			
				$e = $this->getEmail($row['type']);
				$emailLst[] = $e;
			
				
			
		}
		
		if($id!=$parentID)$content.='<div><h3>原始問題['.$this->typeToNum($r[0]['type'],'back').']為：</h3>'.$r[0]['content'].'</div>';
		
		if($shop['shopID']!= $r[0]['shopID']) $name=' 問題解決小組回覆';
		else $name=' ';
		
		if(!empty($emailLst))
		foreach($emailLst as $row)$this->Mail_model->myEmail( $row ,'Q:'.$parentID.$name.$r[0]['shopName']." 在Phant-ri上的新留言",$content);
		
		return   $id;
		
	}
	

}

?>