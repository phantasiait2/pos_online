<?php 

		
	//===============相簿=======================
	function getGalleryId($parent_id,$parent_type)
	{
		$sqlstr = "SELECT id FROM  parent WHERE parent_id= '$parent_id' AND parent_type = '$parent_type'";
		$result = mysql_query($sqlstr);
		$row = mysql_fetch_array($result);
	    return  $row['id'];
		
		
	}
	
	function getAlbumId($gallery_id) //bg專用
	{
		$sqlstr = "SELECT album_id FROM  gallery_album WHERE gallery_id= $gallery_id";
		$result = mysql_query($sqlstr);
		$row = mysql_fetch_array($result);
	    return  $row['album_id'];
		
	}

	
	function getBgPicCategory()
	{
		$sqlstr="SELECT * FROM `gallery_photo_category`";
		$result = mysql_query($sqlstr);
		$i=0;
		while($temp = mysql_fetch_array($result)){$data[$i++]=$temp;}
		mysql_free_result($result);
		return $data;
		
		
	}
	function getAlbumByGalleryID($gallery_id,$gallery_url,$segment_index,$data,$viewlist)
	{
		$sqlstr = "SELECT album_id,photo_num as photoNum,user,title,cover_id FROM gallery_album
				  LEFT JOIN account ON gallery_album.pid = account.pid 
				  WHERE gallery_id = $gallery_id";
		//===分頁
		$albumList=new SplitPage;
		$albumList->sqlstr=$sqlstr;
		$albumList->url= $gallery_url;
		$albumList->segment_index = $segment_index+1;
		$albumList->num_of_a_page=20;
		$data=$albumList->splitAndGetData();
		$viewlist=$albumList->viewList();

		
		
	}
	
	function getAlbumDetail($album_id)
	{
		$sqlstr="SELECT * FROM `gallery_album` where album_id =  $album_id";
		$result = mysql_query($sqlstr);
		while($temp = mysql_fetch_array($result)){$data=$temp;}
		mysql_free_result($result);
		if(isset($data))return $data;
		
		
	}
	
	

		function getPhotoList($con,$data,$viewlist){
		/*
		//圖片搜尋參數設定
		$con['bid'];//隸屬遊戲ID
		$con['album_id'];//隸屬相簿ID
		$con['date'];//mday,year,mon
		$con['category'];//分類
		$con['order'];//date or good
		*/
		$sqlstr = "SELECT photo_id,title,user,gallery_photo_category.zh_name as category FROM gallery_photo 
				  LEFT JOIN account ON gallery_photo.pid = account.pid 
				  LEFT JOIN gallery_photo_category ON gallery_photo.category=gallery_photo_category.cat_id 
				  WHERE";
		if(!empty($con['bid']))	$sqlstr.=" bid=".$con['bid']." AND";	  
		elseif(!empty($con['album_id']))$sqlstr.=" album_id=".$con['album_id']." AND";
		if(!empty($con['date'])&&$con['date']!='alltime')
		switch($con['date'])
		{
			case 'yearly':
				$sqlstr.=" date between now()-INTERVAL 1 YEAR   AND  now() AND ";
				break;
			case 'monthly':
				$sqlstr.=" date between now()-INTERVAL 1 MONTH   AND  now() AND";
				break;
			case 'weekly':
				$sqlstr.=" date between now()-INTERVAL 7 DAY   AND  now() AND";
				break;
			case 'today':
				$sqlstr.=" date between now()-INTERVAL 1 DAY  AND  now() AND";	
				break;
		}
	
		if(!empty($con['category'])&&$con['category']!='all')
			$sqlstr.=" category='".$con['category']."' AND";
		
		
		$sqlstr.=" photo_id is not null";	
		if(!empty($con['order']))$sqlstr.=" ORDER by ".$con['order']." DESC";
		//===分頁
		$piclist=new SplitPage;
		$piclist->sqlstr=$sqlstr;
		$piclist->url= $con['gallery_url'].'/album/'.$con['album_id'];
		$piclist->url_type='get';
		$piclist->url_con="&order=".$con['order']."&date=".$con['date']."&category=".$con['category'];//分頁條件
		$piclist->num_of_a_page=20;
		$data=$piclist->splitAndGetData();
		$viewlist=$piclist->viewList();
		
	}

	
	
	function getBgPic($photo_id){
		
		$sqlstr = "
		SELECT 
				user,gallery_photo.pid,
				gallery_photo.photo_id,
				gallery_photo.date,
				gallery_photo.title,
				gallery_photo.des,
				gallery_photo.good,
				gallery_photo.bad,
				gallery_photo.category as cat_id,
				gallery_photo.reply_num,
				date,
				gallery_photo_category.zh_name as category
		FROM  gallery_photo 
		LEFT JOIN account ON gallery_photo.pid=account.pid 
		LEFT JOIN gallery_photo_category ON gallery_photo.category=gallery_photo_category.cat_id 
		WHERE photo_id=$photo_id";
		$result = mysql_query($sqlstr);
		while($temp = mysql_fetch_array($result)){$data=$temp;}
		mysql_free_result($result);
		if(isset($data))return $data;
		
		
		}
		
	function showNearPic($photo_id,$album_id,$bid,$parent_type){
		if($parent_type==1)$sqlstr="SELECT photo_id FROM gallery_photo WHERE bid=$bid";
		else $sqlstr="SELECT photo_id FROM gallery_photo WHERE album_id=$album_id";
		$result = mysql_query($sqlstr);
		$i=0;
		while($temp = mysql_fetch_array($result)){$data[$i++]=$temp;}
		mysql_free_result($result);
		
		$num=$i-1;
		$i=0;
		while ($data[$i]['photo_id']!=$photo_id) $i++;
	
		$result = array();
		if ($i!=0) $result['prePhoto']=$data[$i-1]['photo_id'];
		if ($i!=$num) $result['nextPhoto']=$data[$i+1]['photo_id'];
		return  $result;
		
		}
	
	function getPicReply($photo_id){
		$sqlstr="SELECT reply_id,user,account.pid,text,time FROM gallery_photo_reply  JOIN account ON account.pid=gallery_photo_reply.pid WHERE photo_id=$photo_id
		ORDER by time ASC";
		$result = mysql_query($sqlstr);
		$i=0;
		while($temp = mysql_fetch_array($result)){$data[]=$temp;}
		mysql_free_result($result);
		if(isset($data))return  $data;
		
	}
?>