<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<h1 id="headerTitleBig"  style=" text-align:left">瘋桌遊庫存</h1>
<form>
<?php 

foreach($product as $class):?>
<h2 style="color:#FFF"><?=$class[0]['class']?></h2>
<?php if(empty($class)) :echo '<h3>查無資料</h3>'; 
	else :?>
<table  border="3" bordercolor="#000000" id="poduct_list">
  <tr class="shipment_tr">
	<td  class="shipment_medium">英文名稱</td>
    <td class="shipment_long">中文名稱</td>
    <td class="shipment_short">數量</td>
<?php foreach($branch as $row): ?>
	<td class="shipment_medium"><?=$row['branchName']?></td>
<?php endforeach;?>
  </tr>
  <?php endif;?>
  <?php foreach($class as $row):?>
  <tr class="shipment_tr">
 
	<td  class="shipment_short"><?=$row['ENGName']?></td>
     <td  class="shipment_short"><?=$row['ZHName']?></td>
    <td class="shipment_short">
    <input  type="text" value="<?=$row['num']?>"  style="width:30px"/>
	
	</td>
	<?php foreach($branch as $column): ?>
        <td class="shipment_medium"></td>
    <?php endforeach;?>    
  </tr>  
  <?php endforeach;?>
</table>
  <?php endforeach;?>
<input type="submit"  class="shipment_btn"  value="確認並送出"/ >
</form>

<div  class="clearfix divider_light"></div>
