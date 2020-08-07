<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>對帳單</title>
</head>

<body style="width:900px">
<h1  style="text-align:center">幻遊天下股份有限公司</h1>
<h2  style="text-align:center">客戶對帳單</h2>

<table style="width:900px">
	<tr><td>客戶名稱:</td><td><?=$shopInf['name']?>[<?=$shopInf['shopID']?>]</td><td>帳款區間:</td><td><?=$fromDate?>~<?=$toDate?></td></tr>
    <tr><td>通訊地址:</td><td><?=$shopInf['address']?></td><td>電話號碼:</td><td><?=$shopInf['phone']?></td></tr>
	<tr><td>聯絡人員:</td><td><?=$shopInf['contactPerson']?></td><td>傳真號碼:</td><td><?=$shopInf['fax']?></td></tr>
    <tr><td>發票類型:</td><td><?=$shopInf['invoiceType']?>聯式</td><td>統一編號:</td><td><?=$shopInf['comID']?></td></tr>


</table>


<table style="width:900px;text-align:right">
	<tr >
    	<th style="border-bottom:solid; width:150px">日期</th>
        <th style="border-bottom:solid; width:150px">出貨單號</th>
        <th style="border-bottom:solid;width:150px">類型</th>
        <th style="border-bottom:solid;width:150px">發票號碼</th>
        <th  style="border-bottom:solid;" >出貨小計</th>
    </tr>
    <?php $total = 0;foreach($shipmentList as $row):$total+=$row['total']?>
    <tr>
        <td style="width:100px"><?=$row['shippingTime']?></td>
        <td style="width:100px;text-align:right">s<?=$row['shippingNum']?></td>
        <td style="width:100px">
			<?php switch($row['type'])
				{
				case 1:
					echo '寄賣';
				break;
				case 2:
					echo '買斷(調)';
				break;
				case 0:
					echo '買斷';
				break;		
				}
			?>
       </td>
        <td><?php if(!empty($row['invoice']))foreach($row['invoice'] as $each):?>
        		<?=$each['invoice']?>
              <?php endforeach;?>
        </td>
        <td  style="text-align:right">$ <?=number_format($row['total'])?></td>
    </tr>
    <?php endforeach;?>
     <tr>
     	<td style="border-top:solid"></td>
        <td style="border-top:solid"></td>
         <td style="border-top:solid"></td>
        <td style="border-top:solid">合計</td>
        <td style="border-top:solid">$ <?=number_format($total)?></td>
    </tr>
    
    
</table>

<h3>幻遊天下股份有限公司</br>
瘋桌遊益智遊戲專賣店</br>
新北市板橋區南雅南路二段11-26號 B1</br>
電話：02-2263-0120 傳真：02-2263-0110</br>
中國信託(822)城東分行</br>
幻遊天下股份有限公司 071540245257</h3>




</body>
</html>