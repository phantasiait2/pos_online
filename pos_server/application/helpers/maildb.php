<?php
function getMailByidAndType($pid,$type,$data,$viewlist)
{
	$sqlstr="SELECT mail.*,user FROM mail LEFT JOIN account ";	
	switch($type){
		
		case 'inbox':
			$sqlstr.=" ON account.pid=send_id WHERE receive_id=$pid AND send_status!='0' ";
			break;
		case 'sent'	:
			$sqlstr.=" ON account.pid=receive_id WHERE send_id=$pid AND send_status='1' ";
			break;
		case 'save'	:
			$sqlstr.=" ON account.pid=receive_id WHERE send_id=$pid AND send_status='0' ";
			break;
		
		}
	$sqlstr.=" ORDER BY date DESC";
	//====================分頁部份==================

	$mail=new SplitPage; 
	//==參數設定
	$mail->sqlstr=$sqlstr;
	$mail->url_type='get';
	$mail->url='/user/mailbox';
	$mail->url_con="type=".$type;//分頁條件
	//檔案擷取
	$data=$mail->splitAndGetData();//使用call by refrence傳回
	$viewlist=$mail->viewList();    //同上		


}

function getMailDetail($mid,$pid,$type){
	
		$sqlstr="SELECT mail.*,user FROM mail LEFT JOIN account";
	switch($type){	
		case 'inbox':
			$sqlstr.=" ON account.pid=send_id WHERE receive_id=$pid AND send_status!='0' ";//收件者
			break;
		case 'reply':
			$sqlstr.=" ON account.pid=send_id WHERE receive_id=$pid AND send_status!='0' ";//收件者
			break;
		case 'fw'	:
			$sqlstr.=" ON account.pid=receive_id  OR account.pid=send_id  WHERE (receive_id=$pid OR send_id=$pid) AND send_status!='0' "; //收件者轉寄郵件讀取的內容與上者相同
			break;
		case 'sent'	:
			$sqlstr.=" ON account.pid=receive_id WHERE send_id=$pid AND send_status='1' ";//寄件者寄件備份
			break;
		case 'edit'	:
			$sqlstr.=" ON account.pid=receive_id WHERE send_id=$pid AND send_status='0' "; //寄件者編輯未送出之郵件
			break;	
		}
			$sqlstr.=" AND mid=$mid";
		$result = mysql_query($sqlstr);
		while($temp = mysql_fetch_array($result)) $data=$temp;
		mysql_free_result($result);
		return $data;		
	
	}

function hasRead($mid,$pid){
		$sqlstr="UPDATE mail SET receive_status='1' WHERE mid=$mid and receive_id=$pid";
		$result = mysql_query($sqlstr);
	}


function countMail($pid)
{
	$sqlstr="SELECT mid FROM mail WHERE receive_id=$pid AND send_status!='0' AND  receive_status='0'";
	$result = mysql_query($sqlstr);
	$num=mysql_num_rows($result);
	mysql_free_result($result);
	return $num;	
	
}





?>