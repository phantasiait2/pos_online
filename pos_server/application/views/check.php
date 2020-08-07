<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<form action="/order/check_send" method="post">
<input type="hidden" name="id" value="<?=$id?>"/>
<h1 id="headerTitleBig"  style=" text-align:left">STEP.3 確認商品訂單</h1>
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
<div  class="clearfix divider_light"></div>
<input type="submit"  class="shipment_btn"  value="確認並送出"/ >
<input type="button"  class="shipment_btn"  value="刪除此訂單" onclick="(confirm('確定要刪除'))? location.href='/order/delete/<?=$id?>':''"/ >
</form>
