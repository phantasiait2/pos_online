<html>
<meta charset="UTF-8">
<head>
	<title>報價單</title>
	 <style media=print type="text/css">  
         .noprint{
            visibility:hidden
         }  
        </style>  
</head>
<body>
	<div id="print">
	<div style="margin-top: 20px;text-align: center;" align="center">
	<font style="font-size: 20px;font-weight: bold;"><?=$this->input->get('shopName')?></font><br>
	
	<font style="font-size: 20px;font-weight: bold;"><?=$this->input->get('address')?></font><br>
	<font style="font-size: 20px;font-weight: bold;">TEL：<font style="font-size: 20px;font-weight: bold;"><?=$this->input->get('phone')?></font><br>
	
	<font style="font-size: 20px;font-weight: bold;"><?=$this->input->get('name')?>　　統一編號：<font style="font-size: 20px;font-weight: bold;"><?=$this->input->get('comID')?><br>
	</div>

	<div style="font-size: 20px;">
	<div style="margin: 0px auto; width: 600px;">
	<div style="float: right;">報價日期  ：<?=$person['outDate']?></div>
	<div>客戶名稱  ：<?=$person['name']?></div>
	</div>
	<div style="margin: 0px auto; width: 600px;">連絡電話   ：<?=$person['phone']?></div>
	<div style="margin: 0px auto; width: 600px;">抬頭   ：<?=$person['title']?><div style="float: right;">統一編號   ：<?=$person['IDNumber']?>  </div></div>
	<div style="margin: 0px auto; width: 600px;"></div>
	
	<div align="center" style="text-align: center;">
	報價單
	<table align="center" border="2" style="min-width:800px;text-align: center;font-size: 20px;">
		<tr>
			<th>品名</th>
			<th>語言</th>
			<th>數量</th>
			<th>單價</th>
			<th>總價</th>
			<th>備註</th>
		</tr>
		<?php $total = 0;?>
		<?foreach ($csOrderData as $data):?>
		
		<tr>
		<td><?=$data['ZHName']?>(<?=$data['ENGName']?>)</td>
		<td><?=$data['language']?></td>
		<td><?=$data['num']?></td>
		<td><?=$data['sellPrice']?></td>
		<?php $price = $data['num']*$data['sellPrice']; $total+=$price;?>
		<td><?=$price?></td>
		<td></td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td colspan="4">合計</td>
		<td>$<?=$total?></td>
		<td></td>
		</tr>
       <tr><td colspan="6"></td></tr>
        <tr><td colspan="6"><?=$person['comment']?></td></tr>
	</table>
	</div>
	</div>
	</div>
	<div align="center" class="noprint">
	<br>
	<input  type="button" style="font-size: 30px;font-weight: bold;" value="列印" onClick="javascript:print();">
	</div>
</body>
</html>