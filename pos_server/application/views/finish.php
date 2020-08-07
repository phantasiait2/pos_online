<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<h1 id="headerTitleBig"  style=" text-align:left">商品訂購完成</h1>
<table width="980" border="3" bordercolor="#000000" id="poduct_list">
  <tr class="shipment_tr">
	<td  class="shipment_long">英文名稱</td>
    <td class="shipment_long">中文名稱</td>
    <td  class="shipment_short">原價</td>
	<td  class="shipment_short">折數</td>
	<td  class="shipment_short">售價</td>

    <td class="shipment_short">數量</td>
  </tr>
  <?php foreach($product as $row):?>
  <tr class="shipment_tr">
	<td  class="shipment_long"><?=$row['ENGName']?></td>
    <td class="shipment_long"><?=$row['ZHName']?></td>
    <td  class="shipment_short"><?=$row['price']?></td>
	<td  class="shipment_short"><?=$row['discount']?></td>
	<td  class="shipment_short"><?=$row['uniPrice']?></td>

    <td class="shipment_short"><?=$row['num']?></td>
  </tr>  
  <?php endforeach;?>
</table>
<div  class="total">總價：<?=$product[0]['total']?></div>
<input type="button"  class="shipment_btn"  value="回歷史訂購記錄" onclick="location.href='/order/profile/'"/ >
<div  class="clearfix divider_light"></div>
