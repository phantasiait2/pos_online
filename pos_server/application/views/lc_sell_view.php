<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" >
	function changeUrl()
	{
		
		location.href = '/product/get_lc/'+$('#year').val()+'/'+$('#month').val()
		
		
	}


</script>
<title>LC 銷售狀況</title>
</head>

<body>
	<?php if(isset($year) && $year!=''):?>
	<select name="year" id="year" onchange="changeUrl()">
    	<?php	$t = getdate();
		
		 for($y=2015;$y<=$t['year'];$y++):?>
         	<option value="<?=$y?>"  <?=($y==$year)?'selected="selected"':''?>><?=$y?></option>
         <?php endfor;?>
    </select>
    	<select name="month" id="month" onchange="changeUrl()">
    	<?php	
		
		 for($m=1;$m<=12;$m++):?>
         	<option value="<?=$m?>" <?=($m==$month)?'selected="selected"':''?>><?=$m?></option>
         <?php endfor;?>
    </select>
    	<a href="/product/get_lc" target="_new">點此查看累積總出貨量及銷售量</a>
     <?php else:?>
     	<h1>開賣至今累積銷售狀況</h1>
        
    <?php endif;?>
    <?php foreach($output as $sellView):?>
	<h1><?=$sellView['title']?></h1>
	<table border="1"  style="text-align:right">
				<tr>
                	<td>店家\品名</td>
		<?php foreach($sellView['product'] as  $p): $total[$p['productID']] = 0;?>      		
                	<td><?=$p['ZHName']?></td>
        <?php endforeach;?>
				 </tr>
                
                 <?php $i= 0;foreach($sellView['sell'] as $shopID=>$row):?>
                 	 <tr <?=($i++%2==1)?'bgcolor="#CCFFCC"':''?>>
                		<td><?=$row['shopName']?></td>
                        	<?php foreach($sellView['product'] as $p):  ?>
                            	<?php if(!isset($row[$p['productID']])) $row[$p['productID']] = 0;?>
                                		 <td><?=$row[$p['productID']]?></td>
                                         
                                <?php $total[$p['productID']]+= $row[$p['productID']] ;
									endforeach;?>
                 	</tr>
				<?php endforeach; ?>

				<tr bgcolor="#FF6600">
                	<td>總量</td>
                    	<?php foreach($total as $row):?>
		                    <td><?=$row?></td>
                   		<?php endforeach;?>
                
                </tr>
	</table>

	<?php endforeach;?>



</body>
</html>