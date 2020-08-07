<?php 
class Report_model extends Model {
	function Report_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}

	function getReportList($offset,$limit,$shopID,$fix)
	{

		if($shopID!=0) $this->db->where('shopID',$shopID);
		if($fix) $this->db->where('status',2);
		else $this->db->where('status !=',3);
		$this->db->limit($limit,$offset);
		$query = $this->db->get('pos_bug_report');
		$data = $query->result_array();
		$i = 0;
		foreach($data as $row)
		{
			$data[$i++]['status'] = $this->status($row['status']);
			
		}	
		return $data;
	}
	function getReport($id)
	{
		
		$this->db->where('id',$id);
		$query = $this->db->get('pos_bug_report');
		$data = $query->row_array();
	
		return $data;
	}	
	function status($key)
	{
		switch($key)
		{
			case 0:
				$ret = '回報';
			break;
			case 1:
				$ret = '已排入修復時程';
			break;
			case 2:
				$ret = "完成待回報者確認";
			break;
				
			case 3:
				$ret = "已修復完成";
			break;	
			
			
		}
		return $ret;
	}
	function sendMail($data,$fixtime)
	{
			$headers = "Content-type: TEXT/HTML; CHARSET=utf-8\nFrom: phant@phantasia.tw\nReply-To:phant@phantasia.tw\n";

			$result = "<h1>親愛的".$data['reporter']."您好：</h1>".
			"<div>您所回報的問題：</div>".
			"<div>操作：".$data['operation']."</div>".
			"<div>描述：".$data['description']."</div>".
			"<div>預期：".$data['expected'] ."</div>".
			"<h2>你所回報的問題已經修正，請於試用後無誤將清單勾選完成</h2>".
			"<div>謝謝您的回報，感謝您的配合。</div>".
			"<div>瘋桌遊系統開發團隊將竭盡最大心力為您服務</div>".
			"<div>若問題持續存在，請再點選「錯誤仍存在」按鈕，我們將盡快修復</div>".
			"<div>瘋桌遊開發團隊敬上</div>";
			

			 mb_internal_encoding('UTF-8');
				$this->Mail_model->myEmail($data['email'],mb_encode_mimeheader('[瘋桌遊進銷存系統]錯誤已修正，'.$fixtime,'UTF-8') ,$result,$headers);		
		
		
	}
	
	function getErrList()
	{
		$query = $this->db->get('pos_err_report');
		return $query->result_array();
		
	}
	
}

?>
