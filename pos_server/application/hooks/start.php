<?php 






class RunTimeTest{
    var $CI;

    function __construct(){
        $this->CI =& get_instance();
		
    }

    function start($t) {
          // This function will run after the constructor for the controller is ran
          // Set any initial values here
		//  echo 's'.$t[0];
		
	         	$_SESSION['t'.$t[0]] = microtime(true);
          
    }
	function endT($t)
	{
	   if(isset($_SESSION['t'.$t[0]]))
      {
             $rt =  microtime(true) - $_SESSION['t'.$t[0]] ;
          if($rt>5) 
          {
             $title  = '長時間運時function shipment '.$this->CI->uri->segment(1).'/'.$this->CI->uri->segment(2);;
            $content=$title .date("Y-m-d H:i:s").'<br/>時間為'.$rt.'秒';
                //$this->CI->Mail_model->myEmail('lintaitin@gmail.com',$title,$content,'',0,99,1);
                $this->CI->db->insert('pos_longer',array(
                'url'=>$this->CI->uri->segment(1).'/'.$this->CI->uri->segment(2),
                'runTime'=>$rt,
                'time'=>date("Y-m-d H:i:s"),
                'shopID'=>$this->CI->data['shopID'],
                'type'=>'shipment'
                )


                );


             }
            //$_SESSION['t'.$t] = microtime(true);
            unset($_SESSION['t'.$t[0]]);  
                              
                              
                              
       }
       
	}
}

?>