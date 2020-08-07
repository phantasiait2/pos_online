<?php 
require_once("gallerydb.php");

class gallery extends model
{
	public $list_view;
	public $topic_view;
	public $post_view;
	public $album_edit;
	public $album_filepath;
	public $album_filepath_big;
	public $permission = true;
	public $segment_index = 4;
	private $gallery_url;
	private $my_pid;
	private $parent_id;
	private $parent_type;
	function gallery($parent_id,$parent_type,$my_pid,$gallery_url)
	{
		$this->gallery_url = $gallery_url;
		$this->my_pid = $my_pid;
		$this->parent_id   = $parent_id;
		$this->parent_type = $parent_type;
		$this->segment = explode('/',$_SERVER['REQUEST_URI']);
		$this->gallery_id = getGalleryId($parent_id,$parent_type);
		$this->gallery_view = '/gallery/gallery_view';
		$this->album_view = "/gallery/album_view";
		$this->photo_view = '/gallery/photo_view';
		$this->album_edit = '/gallery/album_edit';
		$this->pw_view   = '/album/album_pw';
		$this->post_view = '/forum/forum_post';
		$this->album_filepath = '/uploads/album/s/'; //預設小圖檔路徑
		$this->album_filepath_big = '/uploads/album/'; //預設大圖檔路徑

	}

	
	function getData()
	{
		if(isset($this->segment[$this->segment_index]))$way = $this->segment[$this->segment_index];
		else $way = 'galleryList' ;

		switch($way)
		{
			case 'album':
				 return $this->albumView();	
			break;
			case 'photo':	
			return $this->photoView();
			break;
			case 'album_edit':
			return $this->album_edit();
			case 'photo_edit':
			return $this->photo_edit();	
			break;
			default:
			if($this->parent_type==1)return $this->albumView();		
			return $this->galleryList();
			
		}

	}


	function category()
	{		
		return getBgPicCategory();
		//$bgForumCategoryList = getBgForumCategory();			
	}
	
	
	function galleryList()
	{ 
	
		getAlbumByGalleryID($this->gallery_id,$this->gallery_url,$this->segment_index,&$data['albumList'],&$data['viewList']);
		$data['display'] = $this->gallery_view;
		$data['gallery_url'] = $this->gallery_url;
		$data['gallery_id'] = $this->gallery_id;
				return	$data;
	}

	function albumView()
	{
		if(isset($this->segment[$this->segment_index+1]))$data['album_id'] = $this->segment[$this->segment_index+1];
		else $data['album_id'] = getAlbumId($this->gallery_id);
		 //圖片搜尋參數設定
		if(isset($_GET['date']))$con['date']= $_GET['date'];//day,year,month,week
		else $con['date']='';
		if(isset($_GET['category']))$con['category']= $_GET['category'];//分類
		else $con['category'] = '';
		if(isset($_GET['order']))$con['order'] = $_GET['order'];//date or good
		if(empty($con['order'])||$con['order']==0)$con['order']='date';
		$con['album_id'] = $data['album_id'];
		
		$con['gallery_url'] = $this->gallery_url;
		$data['gallery_url'] = $this->gallery_url;
		$data['gallery_id'] = $this->gallery_id;
		$data['albumData'] = getAlbumDetail($data['album_id']);
		if($this->parent_type==1)$con['bid'] = $this->parent_id;//指定bid
		getPhotoList($con,&$data['photoList'],&$data['viewList']);
		$data['con'] = $con;	
		$data['bgGalleryCategory'] = $this->category();
		$data['display']=  $this->album_view;
   	    return $data;
	}

	function photoView()
	{
		if(isset($this->segment[$this->segment_index+1]))$data['album_id'] = $this->segment[$this->segment_index+1];
		else redirect($this->gallery_url);
		if(isset($this->segment[$this->segment_index+2])&&$this->segment[$this->segment_index+2]!=''&&$this->segment[$this->segment_index+2]!=0)
		{
			$photo_id = $this->segment[$this->segment_index+2];
		}
		else redirect($this->gallery_url.'/album/'.$data['album_id']);
		$data['photoData'] = getBgPic($photo_id);
		
		if(empty($data['photoData']))redirect($this->gallery_url.'/album/'.$data['album_id']);
		$data['photoReplyList']=getPicReply($photo_id); 
		$data['albumData'] = getAlbumDetail($data['album_id']);

		$data['photo_id'] = $photo_id;
		$nearPic = showNearPic($photo_id,$data['album_id'],$this->parent_id,$this->parent_type);
		$data = array_merge_recursive($data ,$nearPic);
	
		$data['bgGalleryCategory'] = $this->category();
		$data['display']=$this->photo_view;		
		$data['gallery_url'] = $this->gallery_url;
		$data['gallery_id'] = $this->gallery_id;
		return	$data;
		
	}
	
	function album_edit()
	{
		if(isset($this->segment[$this->segment_index+1]))$data['album_id'] = $this->segment[$this->segment_index+1];
		else $data['album_id'] = getAlbumId($this->gallery_id);
		$data['gallery_url'] = $this->gallery_url;
		$data['gallery_id'] = $this->gallery_id;
				$data['albumData'] = getAlbumDetail($data['album_id']);
		$data['bgGalleryCategory'] = $this->category();
		$data['display']=  $this->album_edit;
   	    return $data;
	}

	
	
	function photo_edit()
	{
		$data['gallery_url'] = $this->gallery_url;
		if(isset($this->segment[$this->segment_index+1]))$data['album_id'] = $this->segment[$this->segment_index+1];
		else redirect($this->gallery_url);
		if(isset($this->segment[$this->segment_index+2])&&$this->segment[$this->segment_index+2]!=''&&$this->segment[$this->segment_index+2]!=0)
		{
			$photo_id = $this->segment[$this->segment_index+2];
		}
		else redirect($this->gallery_url.'/album/'.$data['album_id']);
		$data['photoData'] = getBgPic($photo_id);
		if($data['photoData']['pid'] != $this->my_pid) redirect($this->gallery_url);
		if(empty($data['photoData']))redirect($this->gallery_url);
		$data['bgGalleryCategory'] = $this->category();	
		$data['photo_id'] = $photo_id;
		$data['display']= "/gallery/photo_edit";
  		return $data;
		
	}	
	function reply_edit()
	{
		if(empty($this->my_pid))my_msg('請先登入','/welcome/login');
		if(isset($this->segment[$this->segment_index +3]))$reply_id=$this->segment[$this->segment_index +3];
		if(empty($reply_id))  redirect("/err/message/1");		
		$data['topic']=getReplyById($reply_id);
		if($data['topic']['pid']!=$this->my_pid){redirect("/welcome/login");}
		$data['bgForumCategoryList'] = $this->category();
		$data['display'] = $this->post_view;
		$data['forum_id'] = $this->forum_id;
		$data['album_url'] = $this->album_url;
		$data['readonly'] = true;
		$data['type'] = 'edit';
		$data['forum_action'] = '編輯文章';
		return $data;
		
		
	}
	
}
?>
