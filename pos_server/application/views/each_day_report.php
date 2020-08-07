<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<body  style="-webkit-print-color-adjust:exact; ">

<div class="accounting" style="width:1200px">

<h1>各店今日 <?=$result[0]['report']['date']?> 財務報表</h1>

    <table border="1" width="1200px;" style="font-size:14pt; text-align:center">
<?php $p = 0 ;$monthAllTotal = 0; foreach($result as $row):?>
	<?php if($p%10==0):?>
        <tr style="font-weight:bold">
    	<td>店名</td>
        <?php foreach($item as $each):?>
        <td><?=$each['name']?></td>
        <?php endforeach;?>
        <td>營業額</td>
        <td>總營業額</td>

    </tr>
	<?php endif;?>



	<tr  style="background-color:<?=($p++%2==1)?'#FCF':'none'?>" >
    	<td><?=$row['name']?></td>
			<?php $total = 0 ;  foreach($item as $i=>$each):
			if(!isset($row['report']['item'][$i]['count']))$row['report']['item'][$i]['count']=0;
			 $total+=$row['report']['item'][$i]['count'];
			 if(!isset( $subtotal[$i])) $subtotal[$i] = 0;
			 $subtotal[$i]+=$row['report']['item'][$i]['count'];
			 ?>
            <td><?=$row['report']['item'][$i]['count']?></td>
            <?php endforeach;?>
            <td><?=$total?></td>
            <td><?php $monthAllTotal += $row['report']['monTotal']; ?><?=$row['report']['monTotal']?></td>
    </tr>	  

	
<?php endforeach;?>
	<tr  style="background-color:#F9F" >
    	<td>小計</td>
			<?php $total = 0 ;   foreach($item as $i=>$each):?>
            		<td><?php 
					 $total+= $subtotal[$i];
					echo  $subtotal[$i];
					?></td>
            <?php endforeach;?>
            <td><?=$total?></td>
            <td><?=$monthAllTotal?></td>
    </tr>	  


</table>

</div>
</body>