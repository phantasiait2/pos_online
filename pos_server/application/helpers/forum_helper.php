<?php 
require_once('forumdb.php');
class Forum
{
	public $list_view;
	public $topic_view;
	public $post_view;
	function Forum($parent_id,$parent_type,$my_pid,$forum_url)
	{
		$this->forum_url = $forum_url;
		$this->my_pid = $my_pid;
		$this->parent_id   = $parent_id;
		$this->parent_type = $parent_type;
		$this->segment = explode('/',$_SERVER['PHP_SELF']);
		$this->forum_id = getForumId($parent_id,$parent_type);
		$this->list_view = '/forum/forum_list';
		$this->topic_view = '/forum/forum_view';
		$this->post_view = '/forum/forum_post';
		
	}
	
	function getData()
	{
		if(isset($this->segment[5]))$way = $this->segment[5];
		else $way = 'all' ;
	
		switch($way)
		{
			case 'view':
			return $this->viewTopic();	
			break;
			case 'post':	
			return $this->post();
			break;
			case 'reply_edit':	
			return $this->reply_edit();
			break;
			case 'edit':
			return $this->edit();	
			break;
			default:
			
			return $this->forumList();
			
		}

	}
	

	
	function category()
	{		
		return getBgForumCategory();
		//$bgForumCategoryList = getBgForumCategory();			
	}
	
	
	function forumList()
	{
		if(isset($this->segment[5]))$category = $this->segment[5];
		if (empty ($category))$category='all';
		$data['bgForumCategoryList'] = $this->category();
	
		getTopicList($this->forum_url,$this->forum_id,$category,&$data['listData'],&$data['pageList']);
		$data['display'] = $this->list_view;
		$data['forum_url'] = $this->forum_url;
		return	$data;
	}
	function viewTopic()
	{
		$topic_id = $this->segment[6];
		if($topic_id == '')redirect($this->forum_url);
		$data['topic'] = getTopic($topic_id);
		if(empty($data['topic']))redirect($this->forum_url);
		getReplyList($this->forum_url,$topic_id ,&$data['reply'],&$data['pageList']);
		$data['display'] = $this->topic_view;
		$data['forum_url'] = $this->forum_url;
		return	$data;
		
	}
	function post()
	{
		
		if(empty($this->my_pid))my_msg('請先登入','/welcome/login');
		if(isset($this->segment[6]))$topic_id=$this->segment[6];
		if(isset($topic_id))
		{
			 $data['topic']=getTopic($topic_id);	
			 $data['readonly'] = true;
			 $data['forum_action'] = '回應文章';
		}
		else 
		{
			$data['readonly'] = false;
			$data['forum_action'] = '發表文章';
		}
		$data['bgForumCategoryList'] = $this->category();
		$data['display'] = $this->post_view;
		$data['forum_id'] = $this->forum_id;
		$data['forum_url'] = $this->forum_url;
		
		$data['type'] = 'post';
		return $data;
	}
	function edit()
	{
		if(empty($this->my_pid))my_msg('請先登入','/welcome/login');
		if(isset($this->segment[6]))$topic_id=$this->segment[6];
		if(empty($topic_id))  redirect("/err/message/1");
		$data['topic']=getTopic($topic_id);
		if($data['topic']['pid']!=$this->my_pid){redirect("/welcome/login");}
		$data['bgForumCategoryList'] = $this->category();
		$data['display'] = $this->post_view;
		$data['forum_id'] = $this->forum_id;
		$data['forum_url'] = $this->forum_url;
		$data['readonly'] = false;
		$data['type'] = 'edit';
		$data['forum_action'] = '編輯文章';
		return $data;
		
	}	
	function reply_edit()
	{
		if(empty($this->my_pid))my_msg('請先登入','/welcome/login');
		if(isset($this->segment[6]))$reply_id=$this->segment[6];
		if(empty($reply_id))  redirect("/err/message/1");		
		$data['topic']=getReplyById($reply_id);
		if($data['topic']['pid']!=$this->my_pid){redirect("/welcome/login");}
		$data['bgForumCategoryList'] = $this->category();
		$data['display'] = $this->post_view;
		$data['forum_id'] = $this->forum_id;
		$data['forum_url'] = $this->forum_url;
		$data['readonly'] = true;
		$data['type'] = 'edit';
		$data['forum_action'] = '編輯文章';
		return $data;
		
		
	}
	
}
?>
