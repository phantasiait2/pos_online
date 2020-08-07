<?php 
	function getVideosId($parent_id, $parent_type){
		$sqlstr = "SELECT id 'videos_id' FROM parent WHERE parent_id = '$parent_id' AND parent_type = '$parent_type'";
		$result = mysql_query($sqlstr);
		$row = mysql_fetch_array($result);
	    return $row['videos_id'];
	}

	function getVideoList($video_url, $videos_id, $data, $viewlist){
		$sqlstr = "SELECT * FROM video v JOIN account a ON v.pid = a.pid WHERE videos_id = $videos_id";
		
		//===分頁
		$videoList=new SplitPage;
		$videoList->sqlstr = $sqlstr;
		$videoList->url = $video_url;
		$videoList->segment_index=4;
		//$topicList->url_con="category=".$cat_id;//分頁條件
		$videoList->num_of_a_page=20;
		
		$data = $videoList->splitAndGetData();
		$viewlist = $videoList->viewList();
	}
	
	function getVideoDetail($video_id){
		$sqlstr = "SELECT *, c.zh_name 'category' 
		           FROM video v 
				   JOIN account a ON v.pid = a.pid 
				   JOIN video_category c ON v.cat_id = c.cat_id 
				   WHERE video_id = $video_id";
		$result = mysql_query($sqlstr);
		if( $val = mysql_fetch_array($result) ){ $data = $val; }
		mysql_free_result($result);
		return $data;
	}
	
	function getVideoReplyList($video_id){
		
		$sqlstr = "SELECT * FROM video_reply JOIN account ON account.pid=video_reply.pid WHERE video_id = $video_id";
		$result = mysql_query($sqlstr);
		$i = 0;
		$data = array();
		while( $val = mysql_fetch_array($result)){ $data[$i++] = $val; }
		mysql_free_result($result);
		return $data;
	}
	
	function getVideoCategory(){
		$sqlstr = "SELECT * FROM video_category";
		$result = mysql_query($sqlstr);
		$i = 0;
		$data = array();
		while( $val = mysql_fetch_array($result)){ $data[$i++] = $val; }
		mysql_free_result($result);
		return $data;
	}
	

?>