<?php ob_start();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Phantasia</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="shortcut icon" href="/images/favicon.ico"/>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/jquery.innerfade.js"></script>
<script type="text/javascript">
	   $(document).ready(
				function(){
					$('#news').innerfade({
						animationtype: 'slide',
						speed: 'slow',
						timeout: 4000,
						type: 'sequence',
						containerheight: '12px'
					});
			});
</script>

</head>
<body>
	<div class="webFrame">
		<?php include("header.php"); ?>
		<!-- main code -->
        	
            <div class="main_content_top_big"></div>
        	<div class="mainPadding">
            	<div class="boxCententTopBig"></div>
                <div class="boxBig">
	            	<a href="/bg" >
                    	<img src="/images/index_picture.png" width="940px" />
	            	</a>
	            	<div style="margin:10px 10px 5px 10px; padding:2px 7px; border-top:1px solid #BBB; background:#EEE; ">
		            	<ul id="news" style="margin-left:0px; text-align:left; ">					
							<?php foreach($bgNewsList as $bgNews): ?>
                            	<li>
								最新消息：<a href="/bg/news_view/<?=$bgNews['nid']?>"><?=$bgNews['title']?></a>
                                </li>
							<?php endforeach; ?>			
						</ul>
                    </div>
	            	
	            	<div style="width:225px; height:140px; margin-left:22px; margin-right:5px; border:0px solid #BBB; float:left">
	            		<div class="paddingFive">
	            			<a href="/bg/dictionary">
	            				<img src="/images/index/dic.png" width="215" height="130" />
	            			</a>
	            		</div>
	            	</div>
	            	<div style="width:225px; height:140px; margin-right:5px; border:0px solid #BBB; float:left;">
	            		<div class="paddingFive">
	            			<a href="/bg/news">
		            			<img src="/images/index/news.png" width="215" height="130" />
		            		</a>
	            		</div>
	            	</div>
	            	<div style="width:225px; height:140px; margin-right:5px; border:0px solid #BBB; float:left;">
	            		<div class="paddingFive">
	            			<a href="/joins">
		            			<img src="/images/index/join.png" width="215" height="130" />
		            		</a>
	            		</div>
	            	</div>
	            	<div style="width:225px; height:140px; border:0px solid #BBB; float:left;">
	            		<div class="paddingFive">
	            			<a href="/forum">
		            			<img src="/images/index/forum.png" width="215" height="130" />
		            		</a>
	            		</div>
	            	</div>
                    <div class="clearfix"></div>
                    
            	</div>
            	
            	
                <div class="boxCententBottomBig"></div>
        	</div>
            
            <div class="main_content_bottom_big"></div>

	</div>
	<?php include("footer.php");
		ob_end_flush(); ?>
</body>
</html>