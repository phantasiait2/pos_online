<?php
//動態預先載入css或js
class  preload extends Model {
public $cssStr="";
public $jsStr="";
	function preload()
	{
		parent::Model();	
		
	
	}
	function getCss()
	{
		
		$css=func_get_args();
		for($i=0;$i<count($css);$i++)
			$this->cssStr.='<link rel="stylesheet" type="text/css" href="/style/'.$css[$i].'.css?data=20180709" />';
		
		return $this->cssStr;
	}
	

	
	function getJs()
	{
		$js=func_get_args(); 
		for($i=0;$i<count($js);$i++)
			$this->jsStr.='<script type="text/javascript" src="/javascript/'.$js[$i].'.js?data=79" ></script>';
		return $this->jsStr;
	}
    
    function bug($text)
    {
        if($text===null)$text = 'null';
        $this->db->insert('pos_test',array('content'=>$text));
        
        
    }
}
	
?>
