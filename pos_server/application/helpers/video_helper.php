<?php 
require_once('video_db.php');

class Video
{
	public $video_url;
	public $video_list;
	public $video_view;	
	public $video_edit;
	
	function Video($parent_id,$parent_type,$my_pid,$video_url){
		
		$this->my_pid = $my_pid;
		$this->parent_id   = $parent_id;
		$this->parent_type = $parent_type;
		$this->segment = explode('/',$_SERVER['PHP_SELF']);
		$this->videos_id = getVideosId($parent_id, $parent_type);
		
		$this->video_list = '/video/videos';
		$this->video_view = '/video/video';
		$this->video_edit = '/video/video_edit';
		
		$this->video_url = $video_url;
	}
	
	function getData(){

		if(isset($this->segment[5]))$way = $this->segment[5];
		else $way = 'all' ;
	
		switch($way){
			
			case 'view':
			return $this->video_view();	
			break;
			
			case 'edit':
			return $this->video_edit();
			break;
			
			default:
			return $this->video_list();
		}
	}

	function video_list(){
		getVideoList($this->video_url, $this->videos_id, &$data['videoList'], &$data['viewlist']);
		$data['videos_id'] = $this->videos_id;
		$data['videoCategory'] = getVideoCategory();
		$data['display'] = $this->video_list;
		$data['url'] = $this->video_url;
		
		return	$data;
	}
	
	function video_view(){
		$video_id = $this->segment[6];
		$data['video'] = getVideoDetail($video_id);
		if(empty($data['video'])) redirect($this->video_url);
		$data['replyList'] = getVideoReplyList($video_id);
		$data['videos_id'] = $this->videos_id;
		
		$data['display'] = $this->video_view;
		$data['url'] = $this->video_url;
		return $data;
	}
	
	function video_edit(){
		$video_id = $this->segment[6];
		$data['video'] = getVideoDetail($video_id);
		$data['videoCategory'] = getVideoCategory();
		$data['display'] = $this->video_edit;
		$data['url'] = $this->video_url;
		return $data;
	}

}
?>
