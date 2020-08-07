<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<div class="accounting">
<div style="float:right"><?=$shopData['name']?></div>
<h1><?=$date?> 財務報表</h1>

<table border="1" width="200px;" style="font-size:14pt; text-align:right; float:left;  ">
<tr>
	<th>銷售種類</th>
    <th>總消費額</th>
</tr>
<?php foreach($item as $row):?>
<tr>
	<td><?=$row['name']?></td>
	<td><?=$row['count']?></td>
</tr>
<?php endforeach;?>
<tr>
	<td>店面總收</td>
	<td><?=$total  ?></td>
</tr>
<?php if($backAmount>0): ?>
<tr>
	<td>退貨金額</td>
	<td><?=$backAmount?></td>
</tr>
<?php endif;?>
</table>
<table style="margin-left:15px; float:left ">
<tr>
   <td>
    <table border="1" width="180px;" style="font-size:14pt; text-align:right; float:left;   ">
        <tr>
            <th>項目</th>
            <th>金額</th>
        </tr>
        <tr>
            <td>昨現金餘額</td>
            <td><?=$lastRegisterRemain?></td>
            
        </tr>
        <tr>
            <td>今日置入＋</td>
            <td><?=$into?></td>
            
        </tr>                   
        <tr style="color:red">
            <td>今日提領－</td>
            <td><?=$withdraw?></td>
            
        </tr>              
           <tr>
            <td>總現金收＋</td>
            <td><?=$sales?></td>
        </tr>        
        
        <tr style="color:red">
            <td>今日支出－</td>
            <td><?=$registerOUT?></td>
            
        </tr>        
        <tr style="color:red">
             <td>退貨金額－</td>
            <td><?=$backAmount?></td>  
        </tr>
           
           
            <tr style="font-weight:bold"    >
            <td>今現金餘額</td>
            <td><?=$registerRemain?></td>
            
        </tr>        
           
        
    </table>
   <table border="1" width="180px;" style="font-size:14pt; text-align:right; margin-left:15px; float:left;  ">
        <tr>
            <th>項目</th>
            <th>金額</th>
        </tr>
        <tr>
            <td>店銷現收＋</td>
            <td><?=$sales+ $web['wbCash']+(-$web['wbFee'])-$web['wbTotal']?></td>
            
        </tr>
          <tr>
            <td style="font-weight:bold">店銷刷收＋</td>
            <td><?=isset($credit[0]['total'])?$credit[0]['total']:''?></td>
           </tr>   
            <?php foreach($credit as $key=>$row):
                if($key==0) continue;    
             ?>
            <tr>
                     <td><?=$row['name']?>＋</td>
                    <td><?=$row['total']?></td>    
           </tr>        
            <?php endforeach;?>
        <tr style="font-weight:bold">
            <td>店面營業額</td>
            <td><?=$shopTotal = $sales+ $web['wbCash']+(-$web['wbFee'])-$web['wbTotal']+$creditTotal?>    </td>
            
        </tr>              
        
           
        
    </table>
   <table border="1" width="200px;" style="font-size:14pt; text-align:right; margin-left:15px; float:left;  ">
        <tr>
            <th>項目</th>
            <th>金額</th>
        </tr>
        <tr>
            <td style="font-weight:bold">商城店取營收</td>
            <td><?=$web['wbTotal']?></td>
            
        </tr>
        
         </tr>              
           <tr>
            <td>網銷店收＋</td>
            <td><?=$web['wbTotal'] +$item[10]['count']?></td>
        </tr>        
        
        <tr style="color:red">
            <td>網路付款－</td>
            <td><?=-$item[10]['count']?></td>
        <tr style="color:red">
            <td>商城手續－</td>
            <td><?=-$web['wbFee']?></td>
            
        </tr>
         <tr style="font-weight:bold">
            <td>商城店取收小計</td>
            <td><?=$web['wbCash']?></td>
            
        </tr>
              
       
        
    </table>
    </td>
    </tr>
    <tr>
    
        <td>
            <div class="account_item">店面營業額：<?=$shopTotal-$web['wbTotal']?></div>
            <div class="account_item"> 商城店取營業額：<?=$web['wbTotal']?></div>
              <div class="account_item"> 宅配分潤：<?=$web['wbOrderHomeProfit']?></div>
           </td>
    
    </tr>
    <tr>
        <td> 
        <div class="account_item">今日總營收：<?=$shopTotal+$item[10]['count']+$web['wbOrderHomeProfit']?></div>
        
        <div class="account_item">本月總營收：<?=$monTotal?></div>
        </td>
    </tr>
    
    <tr>
        <td>
            
            <?php if($verifyKey):?>
<div class="account_item" style=" color:#F00"><?=($diff>0)?'距離今日目標尚有：'.$diff.'元':'超越目標：'.(-$diff).'元'?></div>
<div class="account_item" style=" color:#F00"><?=($monDiff>0)?'距離本月目標尚有：'.$monDiff.'元':'超越目標：'.(-$monDiff).'元'?></div>
<div class="account_item" style=" color:#F00"><?='今日毛利：'.$verify.'元'?></div>
<?php endif;?>
</div>
            
            
        </td>
    </tr>
</table>    
 
 
<div class="clearfix"></div>
店面總收 = 總現金收 + 店銷刷收 。<br/>
    總現金收 = 店銷現收 + 網銷店收 。
<div style="clear:both"></div>
<?php if(!empty($accountSplit)):?>
<h1>現金收支明細</h1>

<table border="1" width="900px;" style="font-size:14pt">
<tr>
	<th>員工帳號</th>
    <th>現金收入</th>
	<th style="color:red">現金支出</th>
    <th style="color:red">現金提領</th>
    <th>現金小計</th>
     <?php foreach($credit as $key=>$row):
                if($key==0) continue;   ?>
             <th><?=$row['name']?></th>
 <?php endforeach;?>

</tr>
<?php foreach($accountSplit as $row):?>
<tr>
	<td style="text-align:center"><?=$row['account']?></td>
	<td style="text-align:right"><?=$row['MIN']?></td>    
	<td style="color:red;text-align:right"><?=$row['MOUT']?></td>
    <td style="color:red;text-align:right"><?=$row['withdraw']?></td>
    <td style="text-align:right"><?=$row['MIN']-$row['MOUT']-$row['withdraw']?></td>
  
     <?php foreach($credit as $key=>$each):
                if($key==0) continue;   ?>
             <td style="text-align:right"><?=$row['credit'][$key]?></td>
 <?php endforeach;?>
</tr>
<?php endforeach;?>        
</table>
<?php endif;?>


<div style="clear:both"></div>

<?php if(!empty($withdrawRecord)):?>
<h1>提領明細</h1>

<table border="1" width="900px;" style="font-size:14pt">
<tr>
	<th colspan=""style="text-align:center">員工帳號</th>
    <th style="text-align:center">時間</th>
	<th style="text-align:center">提領原因</th>
    <th style="text-align:center">金額</th>
</tr>
<?php foreach($withdrawRecord as $row):?>
<tr>
	<td style="text-align:center"><?=$row['account']?></td>
	<td style="text-align:center"><?=substr($row['time'],0,16)?></td>    
	<td style="text-align:center"><?=$row['note']?></td>
    <td style="text-align:right"><?=$row['price']?></td>
</tr>
<?php endforeach;?>
</table>
<?php endif;?>


<h1>支出明細</h1>
<?php if(!empty($OUTrecord)):?>
<table border="1" width="900px;" style="font-size:14pt">
<tr>
	<th style="text-align:center">員工帳號</th>
    <th style="text-align:center">時間</th>
    <th style="text-align:center">項目</th>
	<th style="text-align:center">支出原因</th>
    <th style="text-align:right">金額</th>
</tr>
<?php foreach($OUTrecord as $row):?>
<tr>
	<td style="text-align:center"><?=$row['account']?></td>
	<td style="text-align:center"><?=substr($row['time'],0,16)?></td>    
    <td style="text-align:center"><?=$row['item']?></td>
	<td style="text-align:center"><?=$row['note']?></td>
    <td style="text-align:right"><?=$row['price']?></td>
</tr>
<?php endforeach;
else: echo'今日無支出';
endif;
?>
</table>


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
    <table border="1" width="1000px;" style="font-size:14pt; text-align:center">
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
          <td style="text-align:right"></td>
    </tr>
    <?php endforeach;
else: echo'今日無消費';
endif;
?>
</table>

<?php if(!empty($secondHandRecord)):?>
<h1>二手品明細</h1>
<table border="1" width="900px;" style="font-size:14pt">
<tr>
        <th style="text-align:center">時間</th>
        <th style="text-align:center">商品名稱</th>
        <th style="text-align:center">銷售價格</th>
   </tr>
<?php foreach($secondHandRecord as $row):?>
<tr>
	<td><?=substr($row['time'],0,16)?></td>    
    <td><?=$row['ZHName']?></td>
    <td><?=$row['sellPrice']?></td>
</tr>
<?php endforeach;?>
</table>
<?php endif;?>

<?php if(!empty($backRecord)):?>
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
<?php foreach($backRecord as $row):?>
<tr>
    <td><? /* $row['memberID']=='999999'?'非會員':$row['memberID'] */?></td>
	<td><?=substr($row['backTime'],0,16)?></td>    
    <td><?=$row['ZHName']?></td>
    <td><?=$row['sellNum']?></td>
    <td><?=$row['sellPrice']?></td>
	<td><?=$row['backComment']?></td>
    <td><?=$row['sellPrice']*$row['sellNum']?></td>
</tr>
<?php endforeach;?>
</table>
<?php endif;?>
<?php if(!empty($consumeRecord)):?>
<h1>消耗品品明細</h1>
<table border="1" width="900px;" style="font-size:14pt">
<tr>
        <th style="text-align:center">時間</th>
        <th style="text-align:center">商品名稱</th>
   </tr>
<?php foreach($consumeRecord as $row):?>
<tr>
	<td><?=substr($row['time'],0,16)?></td>    
    <td><?=$row['ZHName']?></td>
</tr>
<?php endforeach;?>
</table>
<?php endif;?>
<?php if(!empty($web['wbOrder'])):?>
<h1>網路商城明細</h1>
<table border="1" width="900px;" style="font-size:14pt">
<tr>
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
<table border="1" width="900px;" style="font-size:14pt">
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
</div>