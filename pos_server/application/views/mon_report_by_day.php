<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<body  style="-webkit-print-color-adjust:exact; ">

<div class="accounting" style="width:1200px">

<h1><?=$shopInf['name']?> <?=$date?>月份 財務報表</h1>
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
<tr>
	
	
</tr>
</table>


<div style="clear:both"></div>

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


<h1>逐日報表</h1>
<?php if(!empty($record)):?>    
    <table border="1" width="1200px;" style="font-size:14pt; text-align:center">
  

<?php $i=0;foreach($class as $key=>$everyDay):
		if($i++%100==0):?>
           <tr>
           	<td>日</td>
		<?php foreach($item as $row):?>
            <td ><?=$row['name']?></td>
        <?php endforeach;?>
            <td >毛利</td>
            <td style="width:200px">總營業額</td>
            <td>支出</td>
            <td></td>
            <td>刷卡</td>
            <td>悠遊卡</td>
            <td>Line Pay</td>
            <td>街口支付</td>
                
        </tr>
		<?php endif;?>
        <?php if($key!=$i)
		{
			if($i%2==0)	echo '<tr style="background-color:#EEE">';
			else "<tr>";
			echo "<td>".str_pad($i,2,0,STR_PAD_LEFT )."</td><td colspan='9'></td><td></td><td></td><td></td></tr>";
			$i++;		
		}
		
		?>
<tr <?=($i%2==1)?'':'style="background-color:#EEE"'?> >
	<td><?=$key?></td>
<?php foreach($everyDay['data'] as $row):?>
	<td><?=$row['count']?></td>
<?php endforeach;?>
	<td><?=isset($everyDay['total'])?$everyDay['total']:''?></td>
    <td><?=isset($everyDay['expenses'])?$everyDay['expenses']:''?></td>
     <td><?=isset($everyDay['comment'])? $everyDay['comment']:''?></td>
     <td><?=isset($everyDay['multiPay'][1])? $everyDay['multiPay'][1]:''?></td>
     <td><?=isset($everyDay['multiPay'][2])? $everyDay['multiPay'][2]:''?></td>
     <td><?=isset($everyDay['multiPay'][3])? $everyDay['multiPay'][3]:''?></td>
     <td><?=isset($everyDay['multiPay'][4])? $everyDay['multiPay'][4]:''?></td>
 
     
</tr>
<? endforeach;?>
<tr>
	<td>總計</td>
    <?php foreach($item as $row):?>
	<td><?=$row['count']?></td>
<?php endforeach;?>
	<td><?=$profit?></td>
    <td><?=$total?></td>
    <td><?=$monExpensesTotal?></td>
    <td></td>
    <td><?=isset($multiPayTotal[1])? $multiPayTotal[1]:''?></td>
    <td><?=isset($multiPayTotal[2])? $multiPayTotal[2]:''?></td>
    <td><?=isset($multiPayTotal[3])? $multiPayTotal[3]:''?></td>
    <td><?=isset($multiPayTotal[4])? $multiPayTotal[4]:''?></td>
    

</tr>

</table>
<?php endif;?>
</div>
</body>