<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<h1 id="headerTitleBig"  style=" text-align:left">歷史訂購記錄</h1>
<h2 style="color:#FFF">未確認的定單</h2>
<?php if(empty($unCheck)) :echo '<h3>查無資料</h3>'; 
	else :?>
<table width="980" border="3" bordercolor="#000000" id="poduct_list">
  <tr class="shipment_tr">
	<td  class="shipment_long">時間</td>
    <td class="shipment_long">總價</td>
    <td class="shipment_short"></td>
     <td class="shipment_short"></td>
  </tr>
  <?php endif;?>
  <?php foreach($unCheck as $row):?>
  <tr class="shipment_tr">
	<td  class="shipment_long"><?=$row['time']?></td>
    <td class="shipment_long"><?=$row['total']?></td>
 <td class="shipment_short"><a href="/order/check/<?=$row['tID']?>">前往觀看</a></td>
  <td class="shipment_short"><a  onclick="(confirm('確定要刪除'))? location.href='/order/delete/<?=$row['tID']?>':''">刪除</a></td>
  </tr>  
  <?php endforeach;?>
</table>
<h2 style="color:#FFF">已確認的定單</h2>
<?php if(empty($check)) :echo '<h3>查無資料</h3>'; 
	else :?>
<table width="980" border="3" bordercolor="#000000" id="poduct_list">
  <tr class="shipment_tr">
	<td  class="shipment_long">時間</td>
    <td class="shipment_long">總價</td>
    <td class="shipment_short"></td>
  </tr>
 <?php endif;?>
  <?php foreach($check as $row):?>
  <tr class="shipment_tr">
	<td  class="shipment_long"><?=$row['time']?></td>
    <td class="shipment_long"><?=$row['total']?></td>
 <td class="shipment_short"><a href="/order/finish/<?=$row['tID']?>">前往觀看</a></td>
  </tr>  
  <?php endforeach;?>
</table>

<h2 style="color:#FFF">貨物已經送出</h2>
<?php if(empty($send)) :echo '<h3>查無資料</h3>'; 
	else :?>
<table width="980" border="3" bordercolor="#000000" id="poduct_list">
  <tr class="shipment_tr">
	<td  class="shipment_long">時間</td>
    <td class="shipment_long">總價</td>
    <td class="shipment_short">數量</td>
  </tr>
<?php endif;?>  
  <?php foreach($send as $row):?>
  <tr class="shipment_tr">
	<td  class="shipment_long"><?=$row['time']?></td>
    <td class="shipment_long"><?=$row['total']?></td>
 <td class="shipment_short"><a href="/order/finish/<?=$row['tID']?>">前往觀看</a></td>
  </tr>   
  <?php endforeach;?>
</table>


<div  class="clearfix divider_light"></div>
