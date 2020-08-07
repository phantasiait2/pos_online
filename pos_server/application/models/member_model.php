<?php 
class Member_model extends Model {
	function Member_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
	function updateMemberLevel()
	{
		$datain =array(
			'dueTime'  => '',
			'levelID'  => 1,
			'reNew'    => 0,
			'timeStamp'=> date("Y-m-d H:i:s")
		);
		$this->db->where('dueTime <',date("Y-m-d"));
		$this->db->update('pos_shop_member',$datain);
		return;
		
	}
	
	function phoneForm($phone)
	{
		$phone = str_replace('-','',trim($phone));	
		if($phone!='')
		if($phone[0]==0)
		{
			switch($phone[1])
			{
 				case '9': //手機
					$token = 4;
				break;	
				case '3':
					if($phone[2] =='7')$token = 3;//苗栗
					else $token = 2;//新竹 桃園 花蓮 宜蘭
				break;	
				case '4':
					if($phone[2] =='9')$token = 3;//南投
					else $token = 2;//台中
				break;	
				case '8':
					if($phone[2] =='9')$token = 3;//台東
					else if($phone[2]=='3')$token = 4;//馬祖 0836
					else if($phone[2]=='2')$token = 3;//馬祖 082
					else $token = 2;//屏東
				break;					
				default :
				   $token = 2;
				break;
				
			}
			$phone = substr($phone,0,$token).'-'.substr($phone,$token);

		}
		
		return $phone;
		
		
	}
    
    function getEC()
    {
        $this->paser->post('http://phantfriend.tw/welcome/g_mailapi_with_eid/',$postData,true);	
        
        
    }
    
    
    
	function isShopMember($memberID,$shopID)
	{
		if($shopID==0) return true;
		$this->db->where('memberID',$memberID);
		$this->db->where('shopID',$shopID);
		$query = $this->db->get('pos_shop_member');
        if($query->num_rows()==0) return false;
		else return true;
				
		//shop has no privilage,so only can get some data
	
		
	}
	
	function chkMemberInf($memberID,$phone)
	{
		$this->db->where('memberID',$memberID);
		$this->db->where('phone',$phone);
		$query = $this->db->get('pos_pha_members');
        if($query->num_rows()==0) return false;
		else return true;		
		
	}
	
	
	function getMemberByID($memberID,$shopID)
	{	
		$this->db->select('pos_pha_members.*,
						   pos_shop_member.levelID,
						   pos_shop_member.shopID,
						   pos_shop_member.dueTime,
						   pos_shop_member.reNew,
						   pos_member_level.levelName');
		$this->db->where('pos_pha_members.memberID',$memberID);
if($shopID!=0)$this->db->join('pos_shop_member','pos_shop_member.memberID=pos_pha_members.memberID and shopID='.$shopID,'left');
		else $this->db->join('pos_shop_member','pos_shop_member.memberID=pos_pha_members.memberID','left');
				$this->db->join('pos_member_level','pos_member_level.levelID=pos_shop_member.levelID','left');
		$query = $this->db->get('pos_pha_members');
		if($query->num_rows()==0) return false;
		else return $query->row_array();
		
		
	}
	function getAllMember($shopID)
	{
		
		$this->db->select('pos_pha_members.*,
						   pos_shop_member.levelID,
						   pos_shop_member.shopID,
						   pos_shop_member.dueTime,
						   pos_shop_member.reNew,
						   pos_member_level.levelName');
		if($shopID!=0)$this->db->where('pos_shop_member.shopID',$shopID);
		$this->db->join('pos_shop_member','pos_shop_member.memberID=pos_pha_members.memberID','left');
		$this->db->join('pos_member_level','pos_member_level.levelID=pos_shop_member.levelID','left');
		$this->db->order_by('pos_pha_members.memberID','ASC');
		$query = $this->db->get('pos_pha_members');
		if($query->num_rows()==0) return false;
		else return $query->result_array();
		
		
	}	
	
	function getCoord()
	{
		$this->db->select('latitude,longitude');
		$query = $this->db->get('pos_pha_members');
		return $query->result_array();
		
	}
    
    
	
	function getMemberByNameOrPhone($name,$phone,$shopID)
	{
		$this->db->select('pos_pha_members.*,
						   pos_shop_member.levelID,
						   pos_shop_member.shopID,
						   pos_shop_member.dueTime,
						   pos_shop_member.reNew,
						   pos_member_level.levelName');
		if($name==''&&$phone=='') return false;
		if($name!='')$this->db->or_like('name',$name);
		else
		{
			if($phone!='')$this->db->or_where('phone',$phone);
		}
		$this->db->from('pos_pha_members');
		if($shopID!=0)$this->db->join('pos_shop_member','pos_shop_member.memberID=pos_pha_members.memberID and shopID='.$shopID,'left');
		else $this->db->join('pos_shop_member','pos_shop_member.memberID=pos_pha_members.memberID','left');
		$this->db->join('pos_member_level','pos_member_level.levelID=pos_shop_member.levelID','left');
		$query = $this->db->get();
		if($query->num_rows()==0) return false;
		else return $query->result_array();		
	}

	function getMemberLevel()
	{
		$query = $this->db->get('pos_member_level');
		return $query->result_array();	
	}
	
	function memberFlag($id)
	{
		$this->db->where('memberID',$id);
		$query = $this->db->get('pos_pha_members');
		$data = $query->row_array();	
		if($data['flag']==true) return true;
		else return false;
		
		
	}
	function getMemberLevelByID($memberID,$shopID)
	{
		$this->db->where('memberID',$memberID);
		$this->db->where('shopID',$shopID);
        $this->db->join('pos_member_level','pos_member_level.levelID = pos_shop_member.levelID','json');
		$query = $this->db->get('pos_shop_member');
       	return $query->row_array();
		
		
	}
    
    
    
    
    
    function memberIDRuleChk($memberID)
    {
        if($memberID<=99999) return true;
            $code = $memberID%10;         //個
            $a = ($memberID/10)%10;       //十
            $b = ($memberID/100)%10;      //百
            $c = ($memberID/1000)%10;     //千
            $d = ($memberID/10000)%10;    //萬
            $e = ($memberID/100000)%10;   //十萬
            $f = -1;
            if($memberID>999999)$f = ($memberID/1000000)%10;   //百萬
         
            $t = 0;
            $t+= $a*$a;
            $t+= $b*6+7*$a;
            $t+= $c*6+9*$b;
            $t+= $d*8+1*$c;
            $t+= $e*9+5*$a;
            if($f>=0) $t+=$f*5;
            
            
            $n = $t%10;

            if($n==$code) return true;
            else return false;
                
        
        
        
        
    }
	function getAlltheMember()
	{
		

		$query = $this->db->get('pos_pha_members');
       	return $query->result_array();
		
		
	}
	
	function getMonthMember($month)
	{
		$this->db->where('month(birthday)',$month,false);

		$query = $this->db->get('pos_pha_members');
       	return $query->result_array();
		
		
	}
	function getSignature($shopID)
	{
		$this->db->where('shopID',$shopID);
        $this->db->where('signature !=','');

		$this->db->order_by('time','DESC');
		$query = $this->db->get('pos_email');
       	$data = $query->row_array();	
		if(isset($data['signature']))return $data['signature'];
		
		
	}
	function getECMemberEmail($offset,$num)
    {
        
       $postData = array(
			'code' => md5('phaMail'),
           'offset'=>$offset,
           'num'=>$num
       
       );
        
        
        $r = $this->paser->post('http://phafriend.phantasia.com.tw/member/get_email_list/',$postData,true);	
     
        if($num == 0) return count($r['email']);
        else return $r['email'];
            
        
        
    }
	
	
	function getMemberEmail($shopID,$sex,$fromAge,$toAge,$offset = 0,$num = 0)
	{
		$time= getdate();
		if($shopID!=0)$this->db->where('shopID',$shopID);
		if($sex!=0)$this->db->where('sex',$sex);
		$this->db->where("year(birthday) <=",$time['year']-$fromAge,false);
		if($toAge!=0)$this->db->where("year(birthday) >=",$time['year']-$toAge,false);
		$this->db->join('pos_my_member','pos_my_member.memberID=pos_pha_members.memberID');
        $this->db->order_by('timeStamp','DESC');
        if($num!=0) $this->db->limit($num,$offset);
		$query = $this->db->get('pos_pha_members');
        
        if($num==0) return $query->num_rows();
		else return  $query->result_array();
		
		
	}
	function getSendingEmailList($shopID,$offset,$num)
	{
		if($shopID!=0)$this->db->where('pos_email.shopID',$shopID);
		else 
		{
            //$this->db->where('pos_email.shopID !=',0);
			$this->db->select('pos_email.*,pos_sub_branch.name');
			$this->db->join('pos_sub_branch','pos_sub_branch.shopID =pos_email.shopID','left');
		}
        $this->db->where('status <',2);
		$this->db->order_by('time','DESC');
		$this->db->limit($num,$offset);	
		$query = $this->db->get('pos_email');
		return  $query->result_array();
	}
	function getEmailPreview($id)
	{
	
		$this->db->where('id',$id);
		$query = $this->db->get('pos_email');
		return  $query->row_array();
	}
	
	function getProgress($id)
	{
		$data['emailData'] = $this->getEmailPreview($id);
		$realMemberNum = $this->getSendingList($id,'all',true);
			
	

		$sendingMemberNum = $this->getSendingList($id,'sending',true);
		
		
        if($realMemberNum ==0) return 100;
		return round(($sendingMemberNum/$realMemberNum)*100,0);
		
		
	}
	
	function getSendingList($id,$status,$getNum = false)
	{
		
		$this->db->where('eID',$id);
		if($status =='sending')$this->db->where('status !=',0);
		
		 $this->db->order_by('memberID');
		$query = $this->db->get('pos_email_list');
        if($getNum) return  $query->num_rows();
		return  $query->result_array();
		
	}
	
	
	function getWrongEmail($id,$status)
	{
		
		$this->db->where('eID',$id);
		if($status!=2)$this->db->where('result',$status);
		 $this->db->order_by('memberID');

		$query = $this->db->get('pos_email_list');
		return  $query->result_array();
		
		
	}
	
	
	function emailChk($newemail)
	{
			
 
		$validator = new email_validation_class;
		$validator->timeout = 1;
 
		if(isset($newemail) && strcmp($newemail,"")) {
		    if(($result = $validator->ValidateEmailBox($newemail))<0) {
        	//echo "不能确定您的信箱是否正确. 您的信箱离这里太远了吧?";
	        return -1;
    		} else {
	        if(!$result) {
    	      //  echo "您输入的信箱地址是不正确的! :)";
				return 0;
;
        	    return;
	        } else {
//	        echo "邮箱合法!";
				return 1;
    
        	}
	    }
		} else {
	//    echo '郵箱地址錯誤';
			return 0;
;	
}		
		
		
	}
	function bonusChangeRecord($memberID = 0 ,$from='',$to='',$shopID = 0,$joinProduct = false)
	{
		if($memberID = 0 )$this->db->where('memberID',$memberID);	
		if($from!='') $this->db->where('time >=',$from);
		if($to!='')$this->db->where('time <=',$to);
		
		$this->db->join('pos_pha_members','pos_pha_members.memberID= pos_bonus_change.memberID','left');	
		if($shopID!=0)$this->db->where('pos_bonus_change.shopID',$shopID);
		if($joinProduct) 
		{
			$this->db->join('pos_product','pos_product.productID= pos_bonus_change.productID','left');
			$this->db->join('pos_sub_branch','pos_sub_branch.shopID= pos_bonus_change.shopID','left');
		}
		$this->db->select('*,pos_pha_members.name as memberName');
		$query = $this->db->get('pos_bonus_change');
		return $query->result_array();
	}
	
}

?>
