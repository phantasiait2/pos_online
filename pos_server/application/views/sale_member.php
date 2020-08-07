<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<link rel="stylesheet" type="text/css" href="/style/sale.css" />
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/swfobject.js"></script>
<script type="text/javascript">
 
 $(document).ready(function()
 {	
  $('.item').click(function()
  {

	 $('.s'+this.id).toggle();
	if($(this).html()=='隱藏')  $(this).html('開啟')
	else $(this).html('隱藏')
	  
	})
 })
</script>
<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "memberPile", "380", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/member_pile/<?=$memberID?>/"} );
</script>
<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "memberProduct", "380", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/member_product/<?=$memberID?>/"} );
</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>會員銷售紀錄</title> 
</head>

<body>
<h1>會員銷售紀錄系統</h1>



 <div id="memberPile"></div>
  <div id="memberProduct"></div>
 <div style="clear:both"></div>
<span class="btn">銷售<span class="item" id="ale1">隱藏</span></span>
<span class="btn">場地<span class="item" id="ale2">隱藏</span></span>
<span class="btn">租賃<span class="item" id="ale3">隱藏</span></span>
<span class="btn">餐飲<span class="item" id="ale4">隱藏</span></span>
<span class="btn">其他<span class="item" id="ale5">隱藏</span></span>
<span class="btn">會員費用<span class="item" id="ale6">隱藏</span></span>
<span class="btn">魔獸世界<span class="item" id="ale7">隱藏</span></span>
<span class="btn">魔法風雲會<span class="item" id="ale8">隱藏</span></span>
<table bgcolor="#FFFFFF" border="1"  style="margin:auto">
	<tr>
    	<td>銷售時間</td>
        <td>品名</td>
        <td>語言</td>
        <td>數量</td>
        <td>金額</td>
        <td>地點</td>
    </tr>
    <?php foreach($saleData as $row):?>
    <tr 
    	class="sale<?=$row['type']?>">
    	<td><?=$row['time']?></td>
        <td><?=$row['ZHName']?><?=!empty($row['rent'])?'('.$row['rent']['ZHName'].')':''?></td>
        <td><?=$row['language']?></td>
        <td><?=$row['num']?></td>
        <td><?=$row['sellPrice']?></td>
        <td><?=$row['shopName']?></td>
    </tr>
    <?php endforeach;?>
</table>
</body>
</html>