<?php 
require_once('maildb.php');
class PhaMail
{
	public $type;
	private $nowmid;
	public $my_pid;
	public $mailList;
	public $pageList;
	public $mailData;
	function PhaMail($my_pid)
	{
		$this->my_pid=$my_pid;
		if(isset($_GET['type']))$this->type = $_GET['type'];
		if(isset($_GET['mail']))$this->nowmid = $_GET['mail'];
	}
		
	function listPage()
	{
			
			if(empty($this->type))$this->type='inbox';
			getMailByidAndType($this->my_pid,$this->type,&$this->mailList,&$this->pageList);
			
		
	}	
		
	function viewPage()
	{

  		$this->mailData=getMailDetail($this->nowmid,$this->my_pid,$this->type);
		hasRead($this->nowmid,$this->my_pid);	
		if($this->nowmid=="")my_header("/user/mailbox");
		
	}		
	function sendPage()
	{
		if(isset($this->type)) $this->mailData=getMailDetail($this->nowmid,$this->my_pid,$this->type);
		
	}		
	
	function readOrNot($status)
		{
			switch ($this->type){
			case 'inbox':	
				if($status==0)return '+';
				else return '-' ;
				break;
			case 'sent':
				return '+';
				break;
			case 'save':
				return '-';
				break;
			}
			
		} 
	function mailTitle($title){
			if($this->type=='reply') return 'RE:'.$title;
			else if ($this->type=='fw') return 'FW:'.$title;
			else return $title;
		}
	function mailReceiverName($receiver){
		if($this->type=='reply'||$this->type=="assign"||$this->type=="edit")	return $receiver;
		return;		
	}
	function mailReceiver($receiver){
		if($this->type=='reply'||$this->type=="assign")	return $receiver['send_id'];
		else if ($this->type=="edit" && isset($receiver['reciive_id'])) return $receiver['reciive_id'];
		return;		
	}

	function mailText($text){
		if($this->type=='reply'||$this->type=='fw'){
				$result= '<p></p><p>';
				for($i=0;$i<100;$i++)$result.='-';
				$result.= '</P>';
				$result.="<div>原信件由 ".$this->mailData['user']." 於 ".$this->mailData['date']."寄出</div>";
				$result.="<div>原標題：".$this->mailData['title']."</div>";
				$result.=$text;
		}
		else if($this->type=='edit') $result= $text;
		return $result;		
	}
	
	function viewOrEdit()
	{
		if($this->type=='save') return '/user/mailsend?type=edit&';
		else return '/user/mailview?type='.$this->type."&";
		
	}
	
	function save()
	{
		if($this->type=='edit') return 'edit_save';
		else return 'save';
	}
	function send()
	{
		if($this->type=='edit') return 'edit_send';
		else return 'send';
	}
	
	function sendOrReceive()
	{
		if($this->type=="inbox") return '寄件人';
		else return '收件人';
		
	}
	function where(){
			switch($this->type) 
			{
				case "inbox" :return '的訊息信件箱';	
				case "sent" :return '的寄件備份箱';
				case "save" :return '的寄件儲存箱';
				
				
			}
		
		}
	function replyOrNot(){
			$str="";
			if($this->type== "inbox" ) 
				$str='<a href="/user/mailsend?type=reply&mail='.$this->mailData['mid'].'">回應這篇訊息</a> ｜ ';
			
			return $str.'<a href="/user/mailsend?type=fw&mail='.$this->mailData['mid'].'">轉寄這封訊息</a>';
		
		}	
	
	
}
?>
