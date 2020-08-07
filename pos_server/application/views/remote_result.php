
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?=$err?>
<table border="1">
<?php $i=0; foreach($result as $row):?>
		<?php if($i==0):?>
		<tr>
		<?php foreach($row as $key=>$col):?>
        	<td><?=$key?></td>
		<?php endforeach;?>
		</tr>
		<?php endif;?>
		<tr>
		<?php foreach($row as $key=>$col):?>
        	<td><?=$col?></td>
		<?php endforeach;?>
		</tr>
<?php $i++;endforeach;?>




</table>