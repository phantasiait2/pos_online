<script src="javascript/switch_header_menu.js" type="text/javascript" charset="utf-8"></script>

<div class="header">
	<div class="logoFrame">
    	<div class="logo"><a href="index.php">FantasyWorld</a></div>
    </div>
        <div id="mupic">
            <ul >
                <li class="intro"><a href="intro.php">導覽</a></li>
                <li class="boardgame"><a href="boardgame.php">桌上遊戲</a></li>
                <li class="trpg"><a href="trpg.php">TRPG</a></li>
                <li class="tool"><a href="tool.php">工具</a></li>
                <li class="forum"><a href="forum.php">論壇</a></li>
            </ul>
        </div>
        <div class="searchBox">
        <form style="display:inline;">
                <input type="text" value="Search..." class="searchBar" />
                <input type="submit" style="border:0px; background:url(images/button_search.png); width:20px; height:20px;"/>
        </form>
  		</div>

    <div class="clearfix"></div>
</div>

<?php include_once("php/default.php");
session_start();
ob_end_flush();
/*ÀË¬d¬O§_µn¤J*/
global $logined;
		if($_SESSION['login_chk']!="logined")
		{
			 $logined=false;
		}
		else{$logined=true;}
$myid=$_SESSION['login_id'];
find_user_inf($myid,$link,&$my_pid,&$my_pic);
?>
