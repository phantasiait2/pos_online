<?php
function shopNameCut($shopName)
{
	if(strpos($shopName,'店',4)>0)
	return substr($shopName,0,strpos($shopName,'店',4)) ;
	else return mb_substr ($shopName,0,8,'UTF-8') ;
}

function tableShowing($data)
{
		$r =  '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$r.='<table border="1">';
		$index = array();
	
			
			$i = 0;
			foreach($data as $row)
			{
				
				if($i==0)
				{
						$r.='<tr>';
					foreach($row as $key=> $c)
					{
						
							$r.='<td>'.$key.'</td>';
						
						$index[] = $key;
					}
					$r.='</tr>';
					
				}
				$i++;
				
				
				
				$r.='<tr>';
					foreach($index as $col)	$r.='<td>'.$row[$col].'</td>';
				$r.='</tr>';
			}
			
		$r.='</table>';
		
		return $r;
	
	
}

function deldir($path){
        //如果是目錄則繼續
        if(is_dir($path)){
        //掃描一個資料夾內的所有資料夾和檔案並返回陣列
        $p = scandir($path);
        foreach($p as $val){
        //排除目錄中的.和..
        if($val !="." && $val !=".."){
        //如果是目錄則遞迴子目錄，繼續操作
        if(is_dir($path.$val)){
        //子目錄中操作刪除資料夾和檔案
        deldir($path.$val.'/');
        //目錄清空後刪除空資料夾
        @rmdir($path.$val.'/');
        }else{
        //如果是檔案直接刪除
        unlink($path.$val);
        }
        }
}
}
}



function weekDayColor($day)
{
	switch($day)	
	{
		case 0:
			return 'background:#FF1C19';
		break;
		case 6:
			return 'background:#C7FF91';
		break;
		case 2:
			return 'background:#CCC';
		break;
			
		
		
	}
	
}

function cmpValue($a,$b)
{
	if ($a['val'] == $b['val']) {
        return 0;
    }
    return ($a['val'] < $b['val']) ? 1 : -1;	
	
	
}

function fillZero($ret)
{
	return str_pad($ret,3,0,STR_PAD_LEFT );
	if($ret=='') $ret=0;
	if ($ret<10) return '000'.(string)$ret;
	else if ($ret<100) return '00'.(string)$ret;
	else if ($ret<1000) return '0'.(string)$ret;
	else return (string)$ret;
	
	
}

	
function printSex($sex){
	if($sex == 'M'){
		return '男';
	}else{
		return '女';
	}
}



//擷取部分文字
function getTextBrief($text, $length){
		$text=str_replace('&nbsp;',  '',  $text);
		$result = mb_substr(exclude($text,$len),0 ,$length,'utf-8'); 
		if(mb_strlen($text,'UTF-8') > $length){
			$result.='...';
		}
		
		$farr = array( 
					"/\s /", //過濾多餘的空白 
					"/<(\/?)(script|i?frame|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU", //過濾 <script 等可能引入惡意內容或惡意改變顯示佈局的代碼,如果不需要插入flash等,還可以加入<object的過濾 
					"/(<[^>]*)on[a-zA-Z] \s*=([^>]*>)/isU", //過濾javascript的on事件 
					); 
		$tarr = array(" ", "＜\\1\\2\\3＞","\\1\\2",); 

		$result = preg_replace($farr,$tarr,$result);
		return $result;
}


function select($nowOp,$checkOp){
		if ($nowOp==$checkOp) return ' selected="selected"';
}

function chklogin($logined){
		if (!$logined){my_msg("請先登入","/welcome/login");}
		
	}

function printGalleryAlbumPhoto($cover_id){
	$filename =$_SERVER{'DOCUMENT_ROOT'}.'/phantasia/upload/gallery/s/'.$cover_id.'.jpg';
	$exist = file_exists($filename);
	
	if($exist && isset($cover_id)){
		return '/phantasia/upload/gallery/s/'.$cover_id.'.jpg';
	}
	else{
		return '/phantasia/upload/gallery/s/no_image.jpg';
	}
}

function printGalleryPhoto($photo_id){
	$filename =$_SERVER{'DOCUMENT_ROOT'}.'/phantasia/upload/gallery/s/'.$photo_id.'.jpg';
	$exist = file_exists($filename);
	
	if($exist){
		return '/phantasia/upload/gallery/s/'.$photo_id.'.jpg';
	}
	else{
		return '/phantasia/upload/gallery/s/no_image.jpg';
	}
}

function printGroupPhoto($photo_id){
	$filename =$_SERVER{'DOCUMENT_ROOT'}.'/phantasia/upload/group/photo/s/'.$photo_id.'.jpg';
	$exist = file_exists($filename);
	
	if($exist){
		return '/phantasia/upload/group/photo/s/'.$photo_id.'.jpg';
	}
	else{
		return '/phantasia/upload/group/photo/s/no_image.jpg';
	}
}

function printGroupPhotoBig($photo_id){
	$filename =$_SERVER{'DOCUMENT_ROOT'}.'/phantasia/upload/group/photo/b/'.$photo_id.'.jpg';
	$exist = file_exists($filename);
	
	if($exist){
		return '/phantasia/upload/group/photo/b/'.$photo_id.'.jpg';
	}
	else{
		return '/phantasia/upload/group/photo/b/no_image.jpg';
	}
}

function printGroupHomePhoto($group_id){
	$filename =$_SERVER{'DOCUMENT_ROOT'}.'/phantasia/upload/group/home/b/'.$group_id.'.jpg';
	$exist = file_exists($filename);

	if($exist){
		return '/phantasia/upload/group/home/b/'.$group_id.'.jpg';
	}
	else{
		return '/phantasia/upload/group/home/b/no_image.jpg';
	}
}

function printGroupHomeTinyPhoto($group_id){
	$filename =$_SERVER{'DOCUMENT_ROOT'}.'/phantasia/upload/group/home/s/'.$group_id.'.jpg';
	$exist = file_exists($filename);

	if($exist){
		return '/phantasia/upload/group/home/s/'.$group_id.'.jpg';
	}
	else{
		return '/phantasia/upload/group/home/s/no_image.jpg';
	}
}


function printBgImg($pic_id){
	
	$filename =$_SERVER{'DOCUMENT_ROOT'}.'/phantasia/upload/bg/gallery/s/'.$pic_id.'.jpg';

	$exist = file_exists($filename);

	if($exist){
		return '/phantasia/upload/bg/gallery/s/'.$pic_id.'.jpg';
	}
	else{
		return '/phantasia/upload/bg/gallery/s/no_image.jpg';
	}
}



function printBgImgBig($pic_id){
	
	$filename =$_SERVER{'DOCUMENT_ROOT'}.'/phantasia/upload/bg/gallery/b/'.$pic_id.'.jpg';

	$exist = file_exists($filename);

	if($exist){
		return '/phantasia/upload/bg/gallery/b/'.$pic_id.'.jpg';
	}
	else{
		return '/phantasia/upload/bg/gallery/b/no_image.jpg';
	}
}

function printBgHomeImg($pic_id){
	
	$filename =$_SERVER{'DOCUMENT_ROOT'}.'/phantasia/upload/bg/home/b/'.$pic_id.'.jpg';

	$exist = file_exists($filename);

	if($exist){
		return '/phantasia/upload/bg/home/b/'.$pic_id.'.jpg';
	}
	else{
		return '/phantasia/upload/bg/home/b/no_image.jpg';
	}
}

function printBgHomeTinyImg($pic_id){
	
	$filename =$_SERVER{'DOCUMENT_ROOT'}.'/phantasia/upload/bg/home/s/'.$pic_id.'.jpg';

	$exist = file_exists($filename);

	if($exist){
		return '/phantasia/upload/bg/home/s/'.$pic_id.'.jpg';
	}
	else{
		return '/phantasia/upload/bg/home/s/no_image.jpg';
	}
}



function printUserImg($pid){
	
	$filename =$_SERVER{'DOCUMENT_ROOT'}.'/phantasia/upload/user/s/'.$pid.'.jpg';
	$exist = file_exists($filename);

	if($exist){
		return '/phantasia/upload/user/s/'.$pid.'.jpg';
	}
	else{
		return '/phantasia/upload/user/s/no_image.jpg';
	}
}

function printUserBigImg($pid){

	$filename =$_SERVER{'DOCUMENT_ROOT'}.'/phantasia/upload/user/b/'.$pid.'.jpg';
	$exist = file_exists($filename);
	if($exist){
		return '/phantasia/upload/user/b/'.$pid.'.jpg';
	}
	else{
		return '/phantasia/upload/user/b/no_image.jpg';
	}
}

function odd_bgcolor($i){
 if(!($i&1)) echo 'style="background:#EEE;"';
}

function redirectUnlogin(){
	redirect('welcome/please_login');
}

/*重新導入*/
function my_header($redirect)
{	
	header("Location:".$redirect);
	return;
}
/*丟訊息並重新導入*/
function my_msg($msg,$redirect)
{
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	echo "<SCRIPT Language=javascript>";
	echo "window.alert('".$msg."')";
	echo "</SCRIPT>";
	echo "<script language=\"javascript\">";
	echo "location.href='".$redirect."'";
	echo "</script>";
	exit(1);
	return;	
}
/*找出id的帳號名*/
function fname($id)
{
		$sqlstr="select user from account where pid =$id";
			  	 $result=mysql_query($sqlstr);	
	  		while( $value=mysql_fetch_row($result)){
				$name=$value[0];}
	     mysql_free_result($result);
		 return $name;
}	 

/*找出名字id*/
function find_user_inf($name)
{
	$sqlstr="select pid from account where user='$name'";
			  	 $result=mysql_query($sqlstr);	
	  		if( $value=mysql_fetch_row($result)){
				$pid=$value[0];
				}
			
	     mysql_free_result($result);   
		 return $pid;	
}


function compare_people($min, $max){
		if($min == $max)
		{
			return $min.' 人';
		}
		else
		{
			return $min.' - '.$max.' 人';
		}
	}

//trans datetime saving format "YYYY-MM-DD HH:mm:ss" to date, hour, min
function transToPart($dateTime){
		$date = explode(' ', $dateTime);
		$transDateTime['date'] = $date[0]; //YYYY-MM-DD
		$hour_min = explode(':', $date[1]);
		$transDateTime['hour'] = (int) $hour_min[0]; //HH
		$transDateTime['min'] = (int) $hour_min[1]; //mm
		
		return $transDateTime;
	}

function homePicExist($home_pic,$album_id){
			if (empty($home_pic)||$home_pic==0) return;
			return '/photo/'.$album_id.'/'.$home_pic;
		
		}

function translate_runtime_format($runtime){
		switch($runtime){
			case "under30":
				return "少於 30 分鐘";
				break;
			case "30to60":
				return "30 至 60 分鐘";
				break;
			case "60to90":
				return "60 至 90分鐘";
				break;
			case "90to120":
				return "90 至 120 分鐘";
				break;
			case "over120":
				return "超過 120 分鐘";
				break;
		}
	}


function show_bg_score($score){
		
		for($star = 0; $star < $score; $star++) { echo '<img src="/images/star.png" />'; } 
		for($star = 0; $star <= 5-$score-1; $star++) { echo '<img src="/images/star_dark.png" />';}
	}


/* trpg
/*找出某id的所有團id及角色
	function game_play_id($pid,$link,$gid,$cha_name,$role_name,$i) 
	{
		$sqlstr="select `group`.`gname`,`group`.`cha_name`,`pgroup`.`role_name` 
		         from `pgroup` join `group` on `group`.gid=`pgroup`.`gid`
				 where `pgroup`.`pid`=$pid";	
			  	 $result=mysql_query($sqlstr,$link);	
			$i=0;
	  		while( $value=mysql_fetch_row($result)){
				$gid[$i]=$value[0];
				$cha_name[$i]=$value[1];
				$role_name[$i++]=$value[2];}
	     mysql_free_result($result);
		 return;	
}
/*找出gid的團名
function fgname($id,$link)
{
		$sqlstr="SELECT  `gname` FROM  `group` WHERE  `gid`  =$id";
			  	 $result=mysql_query($sqlstr,$link);	
	  		while( $value=mysql_fetch_row($result)){
				$name=$value[0];}
	     mysql_free_result($result);
		 return $name;
}	 

/*由英文找出中文團名
function find_cha_name($gname,$link)
{
		$sqlstr="SELECT  `cha_name` FROM  `group` WHERE  `gname`  ='$gname'";
			  	 $result=mysql_query($sqlstr,$link);	
	  		while( $value=mysql_fetch_row($result)){
				$cha_name=$value[0];}
	     mysql_free_result($result);
		 return $cha_name;
}	 


/*找出團的gid
function fgid($gname,$link)
  {
 		$sqlstr="SELECT  `gid` FROM  `group` WHERE  `gname`='$gname'";
			  	 $result=mysql_query($sqlstr,$link);	
	  		while( $value=mysql_fetch_row($result)){
				$gid=$value[0];}
				
	     mysql_free_result($result);
		 return $gid;
	}	 


	
/*查詢holding的遊戲
function game_hold($id,$link,$gname_hold,$cha_name_hold,$i)
	{
		$sqlstr="SELECT  `gname`,`cha_name` FROM  `group` WHERE  `dmid`=$id";
			  	 $result=mysql_query($sqlstr,$link);	
			
	  		$i=0;
			while( $value=mysql_fetch_row($result)){
				$gname_hold[$i]=$value[0];
				$cha_name_hold[$i++]=$value[1];}
	     mysql_free_result($result);
		 return ;
	

/*系統最新消息
function system_inf($link,$title,$i)
{
	$sqlstr='SELECT `title` FROM `system_inf` ORDER BY `system_inf`.`date` DESC';
			  	 $result=mysql_query($sqlstr,$link);	
			$i=0;
	  		while( $value=mysql_fetch_row($result)){
				$title[$i++]=$value[0];
				if ($i==5) break;
				}
	     mysql_free_result($result);
		 return;	
}
/*找出遊戲中所有參予玩家的名字
function show_pcname($gid,$link)
{
	  $sqlstr="select pid from pgroup where gid = $gid";
	  $result=mysql_query($sqlstr,$link);
	  $i=0;
		while( $value=mysql_fetch_row($result)){
			$pc[$i++]=$value[0];
			}	
	mysql_free_result($result);
	for ($j=0;$j<$i;$j++)
	 { 	
	 	echo ' '.show_single_link('user.php',$name=fname($pc[$j],$link),'name',$name); }
	 return;
}
/*找出該團發表文章，依照更新順序排序
function show_text_title($gname,$gid,$link)
{
$sqlstr= "SELECT  `blog_id`,`pid`  ,  `time` , `title` 
FROM  `blog` WHERE  `gid` =$gid ORDER BY  `time` DESC";
$result=mysql_query($sqlstr,$link);
	  $i=0;
		while( $value=mysql_fetch_row($result)){
			  $id[$i]=$value[0];
			  $pid[$i]=$value[1];
			  $time[$i]=$value[2];
			  $title[$i++]=$value[3];
			}	
	mysql_free_result($result);
	if ($i==0) {echo "沒有新日誌";}
	else
	{	for ($j=0;$j<min($i,3);$j++)
		{	
			echo '<li><a href="trpg_dia.php?trpggroup='.$gname.'&id='.$id[$j].'">';
			echo $title[$j];
			echo "</a>";
			echo " " ;
			echo show_single_link("user.php",$name=fname($pid[$j],$link),"name",$name) ;;
			echo " 於 ".$time[$j]." 發佈</li>";
				
		}
	}
	return;	
}

function group_state($public,$method,$status,$searchformember)
{
	if ($public) {$public='公開';}
	else{$public='隱密';}
	switch($method)
	{
		case 'real':
			$method='現場';
			break;
		case 'web':
			$method='網路';
			break;
	}
	switch($status)
	{
		case 0:
			$status='已經結束了!';
			break;
		case 1:
			$status='正在進行中!';
			break;
		case 3:
			$status='暫停中...';
		break;		
	}
	
	if ($searchformember) {$searchformember='正在招募玩家!!';}
	else{$searchformember='';}		

}




/*找出該團世界觀，依照更新順序排序
function show_text_world($link,$gname,$gid)
{
$sqlstr= "SELECT  `world_id`, `title` , `time` 
FROM  `worldview` WHERE  `gid` =$gid ORDER BY  `time` DESC";
$result=mysql_query($sqlstr,$link);
	  $i=0;
		while( $value=mysql_fetch_row($result)){
			   $world_id[$i]=$value[0];
			  $title[$i]=$value[1];
			  $time[$i++]=$value[2];
			  
			}	
	mysql_free_result($result);
	if ($i==0) {return 1;}
	else
	{	for ($j=0;$j<$i;$j++)
		{	
			echo '<li><a href="trpg_world.php?trpggroup='.$gname.'&id='.$world_id[$j].'">';
			echo $title[$j];
			echo "</a></li>";
				
		}
	}
	return;	
}





/*團的細節*

function game_detail($link,$assign,$by,$gid,$gname,$cha_name,$type,$dmid,$story,$gpic,$public,$method,$status,$searchformember){
	$sqlstr="select `gid`,`gname`,`cha_name`,`type`,`dmid`,`story`,`gpic`,`public`,`method`,`status`,`searchformember` from `group` where `".$assign."`='$by'";
	 $result=mysql_query($sqlstr,$link);
	  if ($value=mysql_fetch_row($result)){
   			$gid=$value[0];
			$gname=$value[1];	
			$cha_name=$value[2];
			$type=$value[3];
			$dmid=$value[4];
			$story=$value[5];
			$gpic=$value[6];
			$public=$value[7];
			$method=$value[8];
			$status=$value[9];
			$searchformember=$value[10];
		} 
       else {
	   echo "no such group";	   
	   }
	   return;	
}
/*團的中文名*
function game_cha_name($link,$assign,$by){
	$sqlstr="select `cha_name` from `group` where `".$assign."`='$by'";
	 $result=mysql_query($sqlstr,$link);
	  if ($value=mysql_fetch_row($result)){		
			$cha_name=$value[0];
		} 
       else {
	   echo "no such group";	   
	   }
	   return $cha_name ;	
}
		
	
/*show出某團的所有玩家	*
function find_pcname($link,$gid)
	{
		$sqlstr="select `account`.`user` from `pgroup` join `account` where  `pgroup`.`pid`=`account`.`pid` and `pgroup`.`gid`=$gid";
			  	 $result=mysql_query($sqlstr,$link);	
	  		while( $value=mysql_fetch_row($result)){
				echo "<li>".show_single_link("user.php",$value[0],'name',$value[0])."</li>";
				}
	     mysql_free_result($result);
	return;	
	}

	/*玩家是否參與此團及權限*
	function ingame($link,$gname,$my_pid)
	{
		$sqlstr="select `gid`,`dmid` from `group` where `gname`='$gname'";
		 $result=mysql_query($sqlstr,$link);
		  if ($value=mysql_fetch_row($result)){
				$gid=$value[0];
				$dmid=$value[1];} 
		  if ($dmid==$my_pid){return 100;}
		$sqlstr="select `pid` from `pgroup` where `pid`=$my_pid and `gid`=$gid";
		 $result=mysql_query($sqlstr,$link);
		 $row=mysql_fetch_array($result,MYSQL_BOTH);
		if(mysql_num_rows($result)==1){return 50;}
		if ($logined) return 0;
		return -1;
		  mysql_free_result($result);
	}

/*查詢是否有此玩家*
function registed($link,$user,$pid)
{
	$sqlstr="select `pid` from `account` where `user`='$user'";
	 $result=mysql_query($sqlstr,$link);
	if($value=mysql_fetch_row($result)){
	$pid=$value[0];
	 mysql_free_result($result);
	return true;}
	else {
	 mysql_free_result($result);
	return false;}
 
}

/*將玩家加入某團
function joingroup($link,$gid,$pid)
{
	$sqlstr="select `pid` from `pgroup` where `gid`=$gid and `pid`=$pid";
	 $result=mysql_query($sqlstr,$link);
	 $row=mysql_fetch_array($result,MYSQL_BOTH);
	if(mysql_num_rows($result)==1){
	 mysql_free_result($result);
	return false;}
    else{
		$sqlstr="INSERT INTO `pgroup` 
					VALUES ($pid,$gid,'','','','','','','')";
		mysql_query($sqlstr,$link);
		
		return true;
	
	}
}

function subscribe($gid,$pid,$link)
{
	$sqlstr="select * from `subscribe` where `pid`=$pid and `gid`=$gid";
	$result=mysql_query($sqlstr,$link);
	if($value=mysql_fetch_row($result)){
	$pid=$value[0];
	mysql_free_result($result);
	return true;}
	return false;
}



function world_index($gid,$link)
{
	$sqlstr="select world_index from `group` where `gid`=$gid";
	$result=mysql_query($sqlstr,$link);
	if($value=mysql_fetch_row($result)){
	$world_id=$value[0];
	}
	mysql_free_result($result);
	return $world_id;
}


*/



//陣列連結(limit指上限，若less不足上限則只po less數目)
function show_array_link($hyperlink,$content,$asssign,$limit,$less)
{	
	for($j=0;$j<$limit;$j++)
	{
		if($j==$less) break;
		echo '<div><a href="'.$hyperlink.'?'.$asssign.'=';
		echo $content[$j];
		echo '">';
		echo $content[$j];
		echo '</a></div>';
	}		
	return;				

}					

/*單一名稱連結*/
function show_single_link($hyperlink,$content,$sign_name,$sign_value)
{	
		echo '<a href="'.$hyperlink.'?'.$sign_name.'=';
		echo $sign_value;
		echo '">';
		echo $content;
		echo '</a>';
		return;	
		
}					
/*排名*/
function rank($link,$gname,$cha_name,$dmname,$gpic)
{	 
$sqlstr="select `group`.`gname`, `group`.`cha_name`,`group`.`dmid`,`group`.`gpic` from  `top10_dnd` join `group` where `top10_dnd`.`gid`=`group`.`gid`";
	  $result=mysql_query($sqlstr,$link);
		$i=0;
		 while ($value=mysql_fetch_row($result)){
   			$gname[$i]=$value[0];
			$cha_name[$i]=$value[1];
			$dmname[$i]=fname($value[2]); 
			$gpic[$i++]=$value[3];
			}
		mysql_free_result($result);	
		return;	
}	


/*字串標籤排除*/	
function exclude($str,&$length)
{	 
	/*
		$i=0;$length=0;$result="";
		while(isset($str[$i]))
		  {
			  while ($str[$i]=="<"){while ($str[$i++]!=">"){}}
				$result=$result.$str[$i++];
				$length++;
		  }
	return $result;	  
	*/
	return strip_tags($str);
}	
/*參數下傳*/	
function pass($assign_name,$name,$assign_id,$id)
{
	if ($name==""){echo "?".$assign_id."=$id";}
	else {echo "?".$assign_name."=$name";}
	return;	
}

//分頁函數
class SplitPage{
	public $data_num;//資料總共筆數
	public $num_of_a_page=20;//一頁顯示多少筆
	public $pre_text="上一頁";//上一頁顯示文字
	public $next_text="下一頁";//下一頁顯示文字
	public $first_text="首頁";//首頁顯示文字
	public $last_text="末頁";//最後一頁顯示文字
	public $page;//目前為第幾頁
	public $view_num=2;//顯示前後多少數字
	public $sqlstr;//要query的字串
	public $url;//分頁網址
	public $url_con;//分頁條件
	private  $link;
	public  $pages;
	public $segment_index=3;
	public $url_type='segment';//使用segment或是get
	function Splitpage(){}//construct
	function splitAndGetData(){
		if($this->url_type=='segment')
		{
			$segment=explode('/',$_SERVER['PHP_SELF']);
			
			if(isset($segment[$this->segment_index+1]))$this->data_num = $segment[$this->segment_index+1];
			if(isset($segment[$this->segment_index+2])) $this->page = $segment[$this->segment_index+2];

		}
		else
		{
			if(isset($_REQUEST['num']))$this->data_num = $_REQUEST['num'];
			if(isset($_REQUEST['page'])) $this->page = $_REQUEST['page'];

		}
		if (empty($this->sqlstr)) {
			echo '<h1 style="color:red">err:請先設定query字串</h1>';
			return;
			
			}
		if(empty($this->data_num)||empty($this->page))
		{
			$result = mysql_query($this->sqlstr);
			if($result)
			{
				$this->data_num = mysql_num_rows ($result);//計算資料筆數
				mysql_free_result($result);
			}
		    $this->page=1;
		}
		if($this->url_type=='segment')
		{
			$this->link=$this->url.'/'.$this->data_num.'/';
		}
		else
		{
			if(isset($this->url_con)) $this->link=$this->url.'?'.$this->url_con.'&num='.$this->data_num.'&page=';
			else $this->link=$this->url.'?'.'num='.$this->data_num;       //網址處理
			
		}
		$this->pages=floor(($this->data_num-1)/$this->num_of_a_page)+1;      //計算共有幾頁
		if($this->pages==0) $this->pages=1;	
		$start=($this->page-1)*$this->num_of_a_page;
		$this->sqlstr.=" limit $start,$this->num_of_a_page";
		$result = mysql_query($this->sqlstr);
		if($result)
		{
			$i = 0;
			while($temp = mysql_fetch_array($result)){$data[$i++]=$temp;}//抓取檔案
			mysql_free_result($result);
			if(isset($data))return $data;
		}
			
		}
	
	function viewList(){
		if (empty($this->url)) {
			echo '<h1 style="color:red">err:請先設定導向網址</h1>';
			return;
			
			}
			$result = "";
		if($this->page!=1)
			{
				$result.='<a href="'.$this->link.'1">'.$this->first_text."</a>｜";     //是否顯示首頁標籤
				$result.='<a href="'.$this->link.($this->page-1).'">'.$this->pre_text."</a>｜";//上一頁標籤
			}
			for($j=$this->view_num;$j>0;$j--)       
				if (($this->page-$j)>0) $result.='<a href="'.$this->link.($this->page-$j).'">'.($this->page-$j)."</a> "; //計算前幾頁
			$result.='<span style="font-weight:bold;margin-right:3px">'.$this->page."</span>";                             //現在頁面
			for($j=1;$j<=$this->view_num;$j++)
				if (($this->page+$j)<=$this->pages) $result.='<a href="'.$this->link.($this->page+$j).'">'.($this->page+$j)."</a> "; //計算後面幾頁
			if($this->page!=$this->pages)
				{
				$result.="｜".'<a href="'.$this->link.($this->page+1).'">'.$this->next_text."</a>";//下一頁標籤
				$result.="｜".'<a href="'.$this->link.$this->pages.'">'.$this->last_text."</a>";  //顯示尾頁
				}
			$result.="（共 ".$this->pages." 頁）";	
			return $result;
		}
	
	
	}


//forget pw 
function checkLicence($license)
{
	$sql="SELECT * FROM user_forget_pw WHERE license='$license'";
	 $result=mysql_query($sql);

	 return mysql_fetch_array($result);

		
}


function nullHandel($data)
{
	
	if(is_array($data))
	{
		$index=array_keys($data,""); 	
		if($index) foreach($index as $row)
		{
			if(empty($data[$row])) $data[$row]='不詳';
		}
		return $data;
	}
	return;
}

//圖片處理
	function ImageResize($from_filename, $save_filename, $in_width, $in_height, $quality)
	{
			
		$allow_format = array('jpeg', 'png', 'gif');
		$sub_name = $t = '';
	
		// Get new dimensions
		$img_info = getimagesize($from_filename);
		$width    = $img_info['0'];
		$height   = $img_info['1'];
		$imgtype  = $img_info['2'];
		$imgtag   = $img_info['3'];
		$bits     = $img_info['bits'];
		$channels = $img_info['channels'];
		$mime     = $img_info['mime'];
	
		list($t, $sub_name) = explode('/', $mime);
		if ($sub_name == 'jpg') {
			$sub_name = 'jpeg';
		}
	
		if (!in_array($sub_name, $allow_format)) {
			return false;
		}
	
		// 取得縮在此範圍內的比例
		$percent = getResizePercent($width, $height, $in_width, $in_height);
		$new_width  = $width * $percent;
		$new_height = $height * $percent;
	
		// Resample
		$image_new = imagecreatetruecolor($new_width, $new_height);
			
		// $function_name: set function name
		//   => imagecreatefromjpeg, imagecreatefrompng, imagecreatefromgif
		/*
		// $sub_name = jpeg, png, gif
		$function_name = 'imagecreatefrom'.$sub_name;
		$image = $function_name($filename); //$image = imagecreatefromjpeg($filename);
		*/
		$image = imagecreatefromjpeg($from_filename);
	
		imagecopyresampled($image_new, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	
		return imagejpeg($image_new, $save_filename, $quality);
	}
	
	/**
	 * 抓取要縮圖的比例
	 * $source_w : 來源圖片寬度
	 * $source_h : 來源圖片高度
	 * $inside_w : 縮圖預定寬度
	 * $inside_h : 縮圖預定高度
	 *
	 * Test:
	 *   $v = (getResizePercent(1024, 768, 400, 300));
	 *   echo 1024 * $v . "\n";
	 *   echo  768 * $v . "\n";
	 */
	function getResizePercent($source_w, $source_h, $inside_w, $inside_h)
	{
		if ($source_w < $inside_w && $source_h < $inside_h) {
			return 1; // Percent = 1, 如果都比預計縮圖的小就不用縮
		}
	
		$w_percent = $inside_w / $source_w;
		$h_percent = $inside_h / $source_h;
	
		return ($w_percent > $h_percent) ? $h_percent : $w_percent;
	}	


?>