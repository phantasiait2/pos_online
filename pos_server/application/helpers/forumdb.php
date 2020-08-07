<?php 

	//==========論壇=====================
	function getForumId($parent_id,$parent_type)
	{
		$sqlstr = "SELECT id FROM  parent WHERE parent_id= '$parent_id' AND parent_type = '$parent_type'";
		$result = mysql_query($sqlstr);
		$row = mysql_fetch_array($result);
	    return  $row['id'];
		
		
	}

	function getBgForumCategory()
	{
		$sqlstr = "SELECT cat_id, en_name, zh_name FROM forum_category";
		$result = mysql_query($sqlstr);
		
		$i = 0;
		while($val = mysql_fetch_array($result)){
			$bgForumCategoryList[$i++] = $val;
		}
		
		mysql_free_result($result);
		return $bgForumCategoryList;
	
	}


	function getTopic($topic_id){
		$sqlstr = "SELECT topic_id,title,user,account.pid,text,time,forum_category.zh_name 'category' 
				   FROM forum_topic 
				   LEFT JOIN account ON forum_topic.pid=account.pid 
				   LEFT JOIN forum_category ON forum_topic.category = forum_category.cat_id 
				   WHERE topic_id=$topic_id";
				   
			$result = mysql_query($sqlstr);
			while($temp = mysql_fetch_array($result)){$data=$temp;}
			mysql_free_result($result);
			if(isset($data))return $data;
		}
	function getReplyList($url,$topic_id,$data,$viewlist){
		$sqlstr = "SELECT text,user,time,account.pid,reply_id FROM forum_reply left JOIN account ON forum_reply.pid=account.pid
				WHERE topic_id=$topic_id ORDER by time ASC";
				
		//===分頁
		$replyList=new SplitPage;
		$replyList->sqlstr=$sqlstr;
		$replyList->url=$url.'/view/'.$topic_id;
		$replyList->segment_index=6;
		//$replyList->url_con="id=".$bid."&a=".$forum_id;//分頁條件
		$replyList->num_of_a_page=20;
		$data=$replyList->splitAndGetData();
		
		$viewlist=$replyList->viewList();
		
		}
	function getReplyById($reply_id){
		$sqlstr = "SELECT forum_topic.title,forum_topic.forum_id,forum_reply.text,user,forum_reply.time,account.pid,reply_id,forum_topic.topic_id
					FROM forum_reply left JOIN account ON forum_reply.pid=account.pid
				   left JOIN forum_topic ON forum_reply.topic_id=forum_topic.topic_id
					WHERE reply_id=$reply_id ";
			$result = mysql_query($sqlstr);
			while($temp = mysql_fetch_array($result)){$data=$temp;}
			mysql_free_result($result);
			return $data;
		
		}
	
	
	
	function getTopicList($url,$forum_id,$cat,$data,$viewlist){
		$sqlstr = "SELECT title, user, update_time, forum_topic.topic_id, IFNULL(temp.reply_num,0)  AS reply_num 
				   FROM forum_topic
				   LEFT JOIN (SELECT topic_id,COALESCE(COUNT(reply_id),0) AS reply_num FROM forum_reply GROUP BY topic_id ) AS temp
					 ON forum_topic.topic_id=temp.topic_id
					LEFT JOIN account ON account.pid=forum_topic.pid
					  WHERE forum_id=$forum_id";
		if($cat!='all') $sqlstr.=" AND category='$cat'";		  
		$sqlstr.=" ORDER by update_time DESC";
		//===分頁
		$topicList=new SplitPage;
		$topicList->sqlstr=$sqlstr;
		$topicList->url=$url.'/'.$cat;
		$topicList->segment_index=5;
		//$topicList->url_con="id=".$bid;//分頁條件
		$topicList->num_of_a_page=30;
		$data=$topicList->splitAndGetData();
		$viewlist=$topicList->viewList();
		}
	
?>