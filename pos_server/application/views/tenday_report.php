<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>tenDay</title>
</head>
<style>
td{ 
min-width:20px;

	
}
</style>
<body>
<h2>單月十日報 截至<?=$date?></h2>
<table border="1">


<?php
 
    
$j = 0; $i=1;foreach($tenDay['class']  as  $each):$j++;?>

<tr>
<td style=" <?=($j%2==0)?'background-color:#FFCCCC':''?>" rowspan="2"><?=$tenDay[$each][0]['type']?>：</td>
<?php $i=1;;foreach($tenDay[$each] as $row):?>
<td style="text-align:right; <?=($i++==1)?'color:red;':''?>  <?=($j%2==0)?'background-color:#FFCCCC':''?>"><?=shopNameCut($row['shopName'])?>:</td>
<?php endforeach?>
</tr>
<tr>

<?php $i=1;$j;foreach($tenDay[$each] as $row):?>
<td style="text-align:right; <?=($i++==1)?'color:red;':''?>  <?=($j%2==0)?'background-color:#FFCCCC':''?>"><?=number_format($row['val'])?></td>
<?php endforeach?>
</tr>


<?php endforeach?>



<?php /*
 $i=1;foreach($tenDay['newGoldMember']  as $key=>$row):?>
<tr>
	<?php $j= 0;foreach($tenDay['class'] as $each):?>
	<td style="text-align:right; <?=($i==1)?'color:red;':''?>  <?=($j%2==0)?'background-color:#FFCCCC':''?>"><?=shopNameCut($tenDay[$each][$key]['shopName'])?>:</td>
	<td style="text-align:right;<?=($j++%2==0)?'background-color:#FFCCCC"':''?>"><?=number_format($tenDay[$each][$key]['val'])?></td>
    <?php endforeach;$i++;?>

</tr>


<?php endforeach?>

*/?>
</table>

<h2>銷售排行榜</h2>
<table border="1">
      
 <?php 
   $j=0;foreach($tenDay['pt'] as $pt):?>    
    	 <tr >
         	<td><?=$pt['name']?></td>
          <?php $i=1; $j++;foreach($pt['shopList'] as $key=>$row):?>
		    <td style="text-align:right; <?=($i++==1)?'color:red;':''?> <?=($j%2==0)?'background-color:#FFCCCC':''?>"><?=shopNameCut($pt['shopList'][$key]['shopName'])?>:<br/><?=number_format($pt['shopList'][$key]['val'])?></td>
    	<?php endforeach;$i++?>
        </tr>        
    <?php endforeach;?>
	
	
    
</table>

</html>