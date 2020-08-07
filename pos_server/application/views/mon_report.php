<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!--
<script type="text/javascript" src="/javascript/swfobject.js"></script>
<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "chart", "550", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"http://shipment.phantasia.com.tw/accounting/get_chart/<?=$date?>/<?=$assignShopID?>"} );
</script>
<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "bar", "550", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"http://shipment.phantasia.com.tw/accounting/get_bar/<?=$date?>/<?=$assignShopID?>"} );
</script>	
<link rel="stylesheet" type="text/css" href="/style/pos.css" />
-->
<script>
    function getDayPeople()
    {
        $.post('/accounting/people_view/')
        
        
        
        
    }
    
    
</script>    



<div class="accounting" style="width:1200px">

<h1><?=$date?>月份 財務報表</h1>
<table border="1" width="1200px;" style="font-size:14pt; text-align:center; float:left;">
<tr>
<?php foreach($item as $row):?>
	<td ><?=$row['name']?></td>
<?php endforeach;?>
	<td style="width:300px">總營業額</td>
</tr>
<tr>
<?php foreach($item as $row):?>
	<td><?=$row['count']?></td>
<?php endforeach;?>
	<td><?=$total?></td>
</tr>
<?php if($verifyKey):?>
<tr>
	<?php foreach($item as $row):?>
	<td style="color:red"><?=(isset($row['profit']))?$row['profit']:'無資料'?></td>
<?php endforeach;?>
	<td  style="color:red"><?=$monVerify?></td>
	
</tr>
<?php endif;?>
</table>

<div style=" margin-top:10px; margin-bottom:10px; float:left; width:1200px; padding-left:10px;">
<div class="clearfix"></div>
<div class="divider"></div>
<div class="account_item">總營業額：<?=$total?></div>

<div class="clearfix"></div>
<?php if($verifyKey):?>
<div class="account_item" style=" width:400px;color:#F00"><?=($monDiff>0)?'距離本月目標尚有：'.$monDiff.'元':'超越目標：'.(-$monDiff).'元'?></div>
<div class="account_item" style=" width:400px;color:#F00"><?='本月場地消費：'.$place.'元'?></div>
<div class="account_item" style=" width:400px;color:#F00"><?='毛利：'.($monVerify).'元'?></div>
<?php if($total!=0):?>
<div class="account_item" style=" width:400px;color:#F00"><?='毛利率：'.round($monVerify/$total,2)?></div>
<?php endif;?>
<div class="account_item" style=" width:400px;color:#F00"><?='多元支付總額：'.$credit.'元'?></div>
<div class="account_item" style=" width:400px;color:#F00"><?='成交筆數：'.$cashNum.'筆,客單價：'.round($total/$cashNum).'元'?></div>
<?php endif;?>
</div>

<div style="clear:both"></div>

<iframe style="width:700px;height:350px" src="http://shipment.phantasia.com.tw/accounting/people_view/<?=$date?>/<?=$assignShopID?>">
</iframe>
<div id="chart"></div>
<div id="bar"></div>

<?php if(!empty($monWithdraw)):?>    
  <h1>提領明細</h1>
    <table border="1" width="1200px;" style="font-size:14pt; text-align:center">
    <tr>
    	<td>時間</td>
       	<td>金額</td>
        <td>提領人</td>
       	<td>原因</td>
       	
    <tr>
   <?php $withDraw=0;
   foreach($monWithdraw as $row):$withDraw+=$row['MOUT']?>
    <tr>
    	<td><?=$row['time']?></td>
       	<td><?=$row['MOUT']?></td>
       	<td><?=$row['aid']?></td>
        <td><?=$row['note']?></td>
    <tr>
   <?php endforeach;?>
    <tr>
    	<td colspan="3"></td>
        <td>總額：<?=$withDraw?></td>
    </tr>
    
    
    </table>
<?php endif;?>

<?php if(!empty($monExpenses)):?>    
  <h1>支出明細</h1>
    <table border="1" width="1200px;" style="font-size:14pt; text-align:center">
    <tr>
    	<td>時間</td>
       	<td>金額</td>
        <td>提領人</td>
        <td>類別</td>
       	<td>原因</td>
       	
    <tr>
   <?php $expenses=0;
   foreach($monExpenses as $row):$expenses+=$row['MOUT']?>
    <tr>
    	<td><?=$row['time']?></td>
       	<td><?=$row['MOUT']?></td>
       	<td><?=$row['aid']?></td>
        <td><?=$row['item']?></td>
        
        <td><?=$row['note']?></td>
    <tr>
   <?php endforeach;?>
    <tr>
    	<td colspan="4"></td>
        <td>總額：<?=$expenses?></td>
    </tr>
    
    
    </table>
<?php endif;?>


<?php if(!empty($bonusChangeData)):?>    
  <h1>紅利兌換明細</h1>
    <table border="1" width="1200px;" style="font-size:14pt; text-align:center">
    <tr>
    	<td>會員</td>
    	<td>時間</td>
        <td>品名</td>
        <td>語言</td>
       	<td>金額</td>
        <td>使用紅利</td>
 	       	
  
    <tr>
   <?php $withDraw=0;
   foreach($bonusChangeData as $row):?>
    <tr>
    	<td><?=$row['memberID'].$row['memberName']?></td>
    	<td><?=$row['time']?></td>
        <td><?=$row['ZHName']?></td>
        <td><?=$row['language']?></td>
        <td><?=$row['price']?></td>
        <td><?=$row['useBonus']?></td>
    <tr>
   <?php endforeach;?>
    	
    
    
    </table>
<?php endif;?>

    <h1>消費明細</h1>
<?php if(!empty($record)):?>    
    <table border="1" width="1200px;" style="font-size:14pt; text-align:center">
    <tr>
        <th style="text-align:center; width:300px">商品名稱</th>
        <th style="text-align:center; width:100px">總數</th>
         <?php for($i=1;$i<=$mday;$i++):?>
		<th style="text-align:right; width:100px; <?=weekDayColor($firstWeekDay++%7)?>">
			<?=$i?>
 		</th>
        <?php endfor;?>
    </tr>
    <?php $j= 0; foreach($recordOut as $row):?>
    <tr  <?=($j++%2==0)? 'style="background-color:#F0F0F6"':''?>>
    	 <td><?=(isset($row['best30'])&&$row['best30']==1)?'<img src="http://www.phantasia.tw/images/best30.png" style="width:20px;">':''?><?=$row['ZHName']?>(<?=$row['ENGName']?>)</td>
         <td><?=$row['totalNum']?></td>
         <?php for($i=1;$i<=$mday;$i++):?>
		<td id="td_<?=$row['productID']?>_<?=$i?>" style="text-align:right;<?=(isset($row[$i]))? 'background-color:yellow':''?>" 
        onmouseover="showDate(<?=$i?>,'td_<?=$row['productID']?>_<?=$i?>')" onmouseout="" >
			<?=(isset($row[$i]))?$row[$i]:''?>
 		</td>
        <?php endfor;?>
        </td>
    </tr>
    <?php endforeach;
else: echo'今日無消費';
endif;
?>
</table>

<?php if(!empty($monBack)):?>
<h1>退貨明細</h1>
<table border="1" width="900px;" style="font-size:14pt">
<tr>
        <th style="text-align:center">會員編號</th>
        <th style="text-align:center">時間</th>
        <th style="text-align:center">商品名稱</th>
        <th style="text-align:center">商品數量</th>
        <th style="text-align:center">退貨價格</th>
        <th style="text-align:center">退貨原因</th>
        <th style="text-align:center">小計</th>
   </tr>
<?php foreach($monBack as $row):?>
<tr>
    <td><? /* $row['memberID']=='999999'?'非會員':$row['memberID'] */?></td>
	<td><?=substr($row['backTime'],0,16)?></td>    
    <td><?=$row['ZHName']?></td>
    <td><?=$row['num']?></td>
    <td><?=$row['sellPrice']?></td>
	<td><?=$row['comment']?></td>
    <td><?=$row['sellPrice']*$row['num']?></td>
</tr>
<?php endforeach;?>
</table>
<?php endif;?>
<?php if(!empty($web['wbOrder'])):?>
<h1>網路商城明細</h1>
<table border="1" width="1200px;" style="font-size:14pt">
<tr>
       <th style="text-align:center">取貨時間</th>
        <th style="text-align:center">訂單編號</th>
    <th style="text-align:center">姓名</th>
    <th style="text-align:center">會員編號</th>
        <th style="text-align:center">訂單金額
(A)</th>
    <th style="text-align:center">現場辦會員退差額
(B)</th>
    <th style="text-align:center">修正後訂單金額
(C)</th>
    <th style="text-align:center">商城行銷費用
(D)=(C)* 15%</th>
    <th style="text-align:center">網路付款金額
(E)
</th>
    <th style="text-align:center">刷卡費用
(F)
</th>
    <th style="text-align:center">款項合計
D+E+F
</th>
   </tr>
<?php foreach($web['wbOrder'] as $row):?>
<tr  style="text-align:right">
<td><?=$row['time']?></td>  
	<td><?=$row['csOrderNum']?></td>    
  
    <td><?=$row['name']?></td>  
    <td><?=($row['newMemberID']==0)?'未加入會員':$row['newMemberID']?></td>
     <td><?=$row['total']?></td>
     <td><?=$row['diff']?></td>
     <td><?=$row['finalTotal']?></td>
     <td><?=$row['fee']?></td> 
    <td><?=$row['webPay']?></td>
     <td><?=$row['creditFee']?></td>
     <td><?=$row['subTotal']?></td>
    
    
    
    
</tr>
<?php endforeach;?>
</table>
<?php endif;?>
    <?php  if(!empty($web['wbOrderHome'])):?>
<h1>會員宅配到府分潤</h1>
<table border="1" width="1200px;" style="font-size:14pt">
<tr>
       
        <th style="text-align:center">訂單編號</th>
    <th style="text-align:center">姓名</th>
    <th style="text-align:center">會員編號</th>
        <th style="text-align:center">訂單總額
(A)</th>
    <th style="text-align:center">店家分潤
(B)</th>
   </tr>
<?php foreach($web['wbOrderHome'] as $row):?>
<tr  style="text-align:right">
	<td><?=$row['csOrderNum']?></td>    
  
    <td><?=$row['name']?></td>  
    <td><?=($row['memberID']==0)?'未加入會員':$row['memberID']?></td>
     <td><?=$row['total']?></td>
     <td><?=$row['profit']?></td>
  
    
    
</tr>
<?php endforeach;?>
</table>
<?php endif;?>


  <h1>消費明細</h1>
<?php if(!empty($record)):?>    
    <table border="1" width="" style="font-size:14pt; text-align:center">
    <tr>
        <th style="text-align:center">會員編號</th>
        <th style="text-align:center">時間</th>
        <th style="text-align:center">商品名稱</th>
         <th style="text-align:center">語言</th>
        <th style="text-align:center">商品數量</th>
        <th style="text-align:center">商品成本</th>
        <th style="text-align:center">銷售價格</th>
        <th style="text-align:center">小計</th>
        <th style="text-align:center">備註</th>
        <th style="text-align:center">客評</th>
    </tr>
    <?php foreach($record as $row):?>
    <tr
    
    	<?php
			
				 $token=false;
            if($row['memberID']=='999999'&&$row['sellPrice']!=$row['price']) $token=true;
            if(($row['memberID']!='999999'&&$row['sellPrice']<round($row['price']*0.9))||
                ( $row['memberID']!='999999'&&$row['sellPrice']<round($row['price']*$row['minDiscount']/100)))$token=true;
	  
		?>
        style=" color:<?=$token?'red':'black'?>"
     >
        <td><?=$row['memberID']=='999999'?'非會員':$row['memberID']?></td>
        <td><?=substr($row['time'],0,16)?></td>    
        <td><?=(isset($row['best30'])&&$row['best30']==1)?'<img src="http://www.phantasia.tw/images/best30.png" style="width:20px;">':''?><?=$row['ZHName']?><?=!empty($row['rent'])?'('.$row['rent']['ZHName'].')':''?></td>
        <td><?=$row['language']?></td>
        <td><?=$row['sellNum']?></td>
        <td style="text-align:right; color:#900"><?=$row['purchasePrice']?></td>
        <td style="text-align:right"><?=$row['sellPrice']?></td>
        <td style="text-align:right"><?=$row['sellPrice']*$row['sellNum']?></td>
        <td style="text-align:right"><?=$row['comment']?></td>
        <td style="text-align:right"><?=(isset($row['rank']))?$row['rank']:''?></td>
    </tr>
    <?php endforeach;
else: echo'今日無消費';
        ?>
          </table>  
<?php endif;?>

</div>